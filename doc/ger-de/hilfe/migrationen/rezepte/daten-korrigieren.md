---
title: "Daten korrigieren"
audience: developer, software-engineer, php-developer, admin, dba
category: migrations
order: 6
summary: "Eine Migration, die falsche Zeilen korrigiert - Namen mit überzähligen Leerzeichen, Adressen über http - mit einer reinen Funktion, die Plan, Lauf und Test teilen, maskiertem SQL über die Verbindung des Laufs und den alten Werten im Laufprotokoll."
keywords: Migration, Migrationen, Daten korrigieren, Datenkorrektur, Bereinigung, falsche Daten, Zeilen aktualisieren, Laufprotokoll, Probelauf, migration, migrations, fix data, data correction, data cleanup, update rows, escapeString, run log, dry run, idempotent, exp:migration:migrate, exp:migration:dry-run
type: help
---
# Daten korrigieren

Dieses Rezept richtet sich an Entwickler und Datenbankadministratoren, die auf einer Installation falsche Daten
finden und sie auf jeder Installation der Site korrigieren müssen: Werte, die ein Import verdorben hat, ein Fehler,
der eine Woche lang schlechte Zeilen schrieb. Als Migration wird eine Korrektur wie Code geprüft, im Probelauf Zeile
für Zeile gezeigt, bevor sich etwas ändert, beim Lauf aufgezeichnet und mit dem Sicherungspunkt zurückgenommen, wenn
sie falsch war. Das Beispiel korrigiert die Tabelle `exp_news_source`: Namen verlieren ihre überzähligen
Leerzeichen, `http://`-Adressen werden zu `https://`.

## Was Sie am Ende haben

- die Migration `20261101140000_fix_news_source_rows.php`;
- einen Probelauf, der jede zu korrigierende Zeile mit altem und neuem Wert zeigt;
- die korrigierten Zeilen, die alten Werte im Laufprotokoll und einen zweiten Lauf, der nichts mehr zu tun findet.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`; die Tabelle `exp_news_source`
  ([Eine Datenbanktabelle anlegen](eine-datenbanktabelle-anlegen.md)).
- Eine Kopie der Daten mit dem Problem, damit der Probelauf echte Zeilen zeigt.
- Ausführen als Benutzer des Webservers (als root mit `--allow-root-user`).

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton fix_news_source_rows --description="News sources: names without surrounding spaces, addresses over https" --apply
```

Ergänzen Sie `// requires: 20261101090000_news_source_table` und die Methoden unten.

### 2. Den Probelauf genau lesen

Der Probelauf ist die Prüfung einer Datenkorrektur: Jede Zeile ist eine Zeile, die sich ändern wird.

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-140310-d2a6f8, environment staging, database postgresql (transactions: full)

  would-apply 20261101140000_fix_news_source_rows          site               News sources: names without surrounding spaces, addresses over https
              would correct the source 3: " City  desk" -> "City desk", http://example.org/city -> https://example.org/city
              would correct the source 7: "Sports" -> "Sports", http://example.org/sports -> https://example.org/sports

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

Auf PostgreSQL und SQLite führt `./console exp:migration:dry-run --simulate --diff` die Korrektur in einer
Transaktion aus, die zurückgerollt wird, und meldet die geänderten Zeilen.

### 3. Ausführen

```bash
./console exp:migration:migrate --apply
```

```text
Exponential migrations: run
Run 20261101-140402-5f13aa, environment staging, database postgresql (transactions: full), as admin

  applied     20261101140000_fix_news_source_rows          site               News sources: names without surrounding spaces, addresses over https
              corrected the source 3
              corrected the source 7
```

Das Laufprotokoll behält die alten Werte:

```text
2026-11-01T21:04:02Z info source 3 was " City  desk" http://example.org/city
2026-11-01T21:04:02Z info source 7 was "Sports" http://example.org/sports
```

Das Laufprotokoll wird vor dem Schreiben von Geheimnissen und E-Mail-Adressen bereinigt und in das
Migrations-Repository committet. Protokollieren Sie bei einer Korrektur von Benutzerdaten nie personenbezogene Daten,
nur IDs.

## Die Migration

```php
<?php
// description: News sources: names without surrounding spaces, addresses over https
// requires: 20261101090000_news_source_table
// author: Site team
namespace Site\Migrations;

final class FixNewsSourceRows20261101140000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_source';

    /**
     * The corrected row, or null when it is right already. Pure: plan(), up() and the test use it alike.
     *
     * @param array $row id, name, url
     * @return array|null
     */
    public static function fixed( array $row )
    {
        $name = trim( preg_replace( '/\s+/', ' ', (string)$row['name'] ) );
        $url = preg_replace( '#^http://#i', 'https://', trim( (string)$row['url'] ) );
        if ( $name === $row['name'] && $url === $row['url'] )
            return null;
        return array( 'id' => (int)$row['id'], 'name' => $name, 'url' => $url );
    }

    /** @return array[] the rows to correct, each with its old and new values */
    private static function changes( $db )
    {
        $changes = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, name, url FROM ' . self::TABLE . ' ORDER BY id' ) as $row )
        {
            $fixed = self::fixed( $row );
            if ( $fixed !== null )
                $changes[] = array( 'old' => $row, 'new' => $fixed );
        }
        return $changes;
    }

    public function plan( $context )
    {
        $lines = array();
        foreach ( self::changes( $context->db() ) as $change )
            $lines[] = 'correct the source ' . $change['new']['id'] . ': "' . $change['old']['name'] . '" -> "' . $change['new']['name'] . '", ' . $change['old']['url'] . ' -> ' . $change['new']['url'];
        return $lines ? $lines : array( 'nothing: every source is right' );
    }

    public function up( $context )
    {
        $db = $context->db();
        $done = array();
        foreach ( self::changes( $db ) as $change )
        {
            $new = $change['new'];
            $db->query( 'UPDATE ' . self::TABLE . " SET name = '" . $db->escapeString( $new['name'] ) . "', url = '"
                        . $db->escapeString( $new['url'] ) . "' WHERE id = " . (int)$new['id'] );
            // the old values go into the run log, so the change can be traced and undone by hand
            $context->log( 'info', 'source ' . $new['id'] . ' was "' . $change['old']['name'] . '" ' . $change['old']['url'] );
            $done[] = 'corrected the source ' . $new['id'];
        }
        return $done ? $done : array( 'nothing to correct' );
    }
}
```

- **Eine reine Funktion entscheidet.** `fixed()` nimmt eine Zeile und liefert die korrigierte Zeile oder `null`. Plan,
  Lauf und ein Unit-Test nutzen sie alle, daher zeigt der Probelauf genau, was der Lauf tut.
- **Jeden Wert maskieren.** `$db->escapeString()` für Zeichenketten, `(int)` für Zahlen; setzen Sie nie einen Wert
  unmaskiert in SQL ein, auch keinen aus Ihrer eigenen Tabelle.
- **Über `$context->db()`.** Auf PostgreSQL und SQLite ist die ganze Korrektur eine Transaktion: Ein Fehler in Zeile 7
  lässt auch Zeile 3 unberührt. Auf MySQL/MariaDB und Oracle sind Datenänderungen ebenfalls transaktional, nur
  Schemaänderungen nicht.
- **Kein `down()`.** Eine Datenkorrektur hat keine sinnvolle Umkehrung; die alten Werte stehen im Protokoll und im
  Sicherungspunkt.

Ein Unit-Test der Entscheidung braucht keine Datenbank:

```php
$this->assertSame( array( 'id' => 3, 'name' => 'City desk', 'url' => 'https://example.org/city' ),
    \Site\Migrations\FixNewsSourceRows20261101140000::fixed( array( 'id' => 3, 'name' => ' City  desk', 'url' => 'http://example.org/city' ) ) );
$this->assertNull( \Site\Migrations\FixNewsSourceRows20261101140000::fixed( array( 'id' => 1, 'name' => 'Newsroom', 'url' => '' ) ) );
```

## Prüfen

- `./console exp:migration:status`: Die Migration ist `done`.
- Fragen Sie die Tabelle ab: Kein Name beginnt oder endet mit einem Leerzeichen, keine Adresse beginnt mit `http://`.
- Ist der Fehler, der die schlechten Zeilen schrieb, noch nicht behoben, entstehen neue schlechte Zeilen; die
  Migration läuft nicht erneut. Beheben Sie zuerst den Code, oder schreiben Sie nach der Code-Korrektur eine zweite
  Migration.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101-140402-5f13aa --apply
```

stellt den Sicherungspunkt von vor dem Lauf wieder her. War die Korrektur für die meisten Zeilen richtig und für
eine falsch, korrigieren Sie diese Zeile in einer neuen Migration, statt die ganze Installation wiederherzustellen.

## Nächster Schritt

Korrigieren Sie Einstellungen statt Zeilen:
[INI-Einstellungen verschieben oder umbenennen](ini-einstellungen-verschieben-oder-umbenennen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md), [Migrationen testen](migrationen-testen.md), [Eine Migration im Code-Review prüfen](eine-migration-pruefen-im-code-review.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
