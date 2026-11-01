---
title: "Ein umkehrbares down() schreiben"
audience: developer, software-engineer, php-developer
category: migrations
order: 12
summary: Wie man ein down() schreibt, das up() auf jeder Installation wirklich rückgängig macht - wiederholbar, über Remote-ID oder Bezeichner gefunden, sicher auf jeder Engine -, wann man es weglässt und wie man es mit up, down und wieder up beweist.
keywords: Migration, Migrationen, Rücknahme, rückgängig machen, umkehrbar, wiederholbar, Sicherungspunkt, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, down, down(), reversible, rollback, undo, idempotent, remote id, detect, schema change, database change, deploy, exp:migration:rollback, caption
type: help
---
# Ein umkehrbares down() schreiben

`exp:migration:rollback <migration-id>` nimmt eine Migration zurück, indem es ihr `down()` ausführt. Ein gutes
`down()` erlaubt einer Entwicklerin oder einem Administrator, eine einzelne Änderung zurückzunehmen, ohne einen
ganzen Sicherungspunkt wiederherzustellen, der auch jede andere Änderung seither zurücknähme. Dieses Rezept zeigt,
was ein `down()` verlässlich macht und wann man es absichtlich weglässt.

## Was Sie am Ende haben

- eine Migration, deren `down()` genau zurücknimmt, was `up()` getan hat, auf jeder Installation und jeder
  unterstützten Engine;
- einen Test, der `up()`, `down()` und wieder `up()` ausführt;
- eine klare Regel für Migrationen, die nicht umkehrbar sind.

## Bevor Sie beginnen

Lesen Sie, wie der Runner `down()` behandelt, in [PHP-Migrationen](../../../anleitungen/migrationen-php.md#down-und-reversible).
Kurz: Eine Klasse, die `down()` deklariert, ist umkehrbar; die Rücknahme lehnt eine Migration ohne `down()` oder
eine, die nicht `done` ist, ab; nach einem erfolgreichen `down()` wird die Aufzeichnung entfernt, und die Migration
ist wieder `pending`.

## Schritte

### 1. Zurücknehmen, was up() getan hat, und nicht mehr

`down()` spiegelt `up()` in umgekehrter Reihenfolge. Legt `up()` eine Tabelle an und schreibt eine Zeile, löscht
`down()` die Tabelle; fügt `up()` eine Spalte hinzu, löscht `down()` diese Spalte, nicht die Tabelle.

### 2. Das Angelegte über etwas Beständiges finden

Objekt-, Knoten- und Attribut-IDs unterscheiden sich zwischen Installationen. Geben Sie dem, was Sie in `up()`
anlegen, eine **Remote-ID** oder einen **Bezeichner** und suchen Sie es in `down()` darüber:
`eZContentObject::fetchByRemoteID()`, `eZContentClass::fetchByIdentifier()`, `eZRole::fetchByName()`.

### 3. Zweimal ausführbar machen

Ein `down()`, das nichts zurückzunehmen findet, gibt eine Meldung zurück, statt zu scheitern. So funktioniert eine
Rücknahme auch dann, wenn ein Administrator etwas von Hand entfernt hat.

### 4. Die Datenbank fragen, nicht die Aufzeichnung

`expUpgradeCatalog::hasColumn()`, `hasIndex()` und `expKernelTableSchema::exists()` sagen, was die Datenbank jetzt
enthält.

## Die Migration

```php
<?php
// description: News notes get a priority (default 0)
// requires: 20261101090000_news_note_table
// engines: mysql, postgresql, sqlite
namespace Site\Migrations;

final class NewsNotePriority20261101110000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';
    const COLUMN = 'priority';

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.' . self::COLUMN . ' (integer, default 0)' );
    }

    public function up( $context )
    {
        $has = \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::COLUMN );
        if ( $has === null )
            throw new \RuntimeException( 'the table ' . self::TABLE . ' does not exist' );
        if ( $has )
            return array( self::TABLE . '.' . self::COLUMN . ' exists already' );
        \expUpgradeCatalog::execute( $context->db(),
            'ALTER TABLE ' . self::TABLE . ' ADD COLUMN ' . self::COLUMN . " INTEGER DEFAULT 0 NOT NULL;\n" );
        return array( 'added ' . self::TABLE . '.' . self::COLUMN );
    }

    public function down( $context )
    {
        if ( \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::COLUMN ) !== true )
            return array( self::TABLE . '.' . self::COLUMN . ' is not there: nothing to undo' );
        \expUpgradeCatalog::execute( $context->db(),
            'ALTER TABLE ' . self::TABLE . ' DROP COLUMN ' . self::COLUMN . ";\n" );
        return array( 'dropped ' . self::TABLE . '.' . self::COLUMN );
    }
}
```

`engines:` beschränkt sie auf die Engines, deren SQL sie schreibt; auf Oracle und MongoDB ist sie `not-needed`.
`DROP COLUMN` braucht SQLite 3.35 oder neuer. `expUpgradeCatalog::execute()` führt jede Anweisung mit Fehlern als
Ausnahmen aus, eine scheiternde Anweisung lässt also die Migration scheitern, statt still durchzulaufen.

## Wann es kein down() geben darf

Lassen Sie `down()` weg, wenn das Zurücknehmen Daten verlöre, die die Migration nicht zurückbringen kann: entfernte
Inhalte, überschriebene Werte, zwei zu einem zusammengeführte Felder. Dann sagt die Rücknahme das:

```text
Exponential migrations: rollback with down()
Run 20261101-111845-0c5d2e, environment development, database sqlite (transactions: full), as admin

  done        20261101113000_merge_news_teasers            site               The teaser text becomes part of the summary

Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown
FAIL: 20261101113000_merge_news_teasers cannot be undone by itself (no down()); restore the caption taken before it
FAIL (exit 1)
```

Der Weg zurück ist der vor ihrem Lauf genommene Sicherungspunkt: `./console exp:migration:rollback <run-id> --apply`
([Sicherungspunkte und Rücknahme in der Praxis](sicherungspunkte-und-ruecknahme-in-der-praxis.md)). Ein leeres
`down()`, das nur eine Meldung zurückgibt, ist schlimmer als keines: Es entfernt die Aufzeichnung und lässt die
Änderung bestehen.

## Prüfen

Führen Sie die drei Schritte auf einer Entwicklungsinstallation aus:

```bash
./console exp:migration:migrate --apply --no-commit
./console exp:migration:rollback 20261101110000_news_note_priority --apply
./console exp:migration:migrate --apply --no-commit
```

```text
Exponential migrations: rollback with down()
Run 20261101-112010-9e3a41, environment development, database sqlite (transactions: full), as admin

  undone      20261101110000_news_note_priority            site               News notes get a priority (default 0)
              dropped exp_news_note.priority

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 undone
PASS
```

Das zweite `migrate` wendet sie wieder an. Legen Sie dieselbe Folge in einen PHPUnit-Test
([Migrationen testen](migrationen-testen.md)).

## Zurücknehmen

Diese Seite handelt vom Zurücknehmen. Achten Sie auf die Reihenfolge: Nehmen Sie zuerst die Migrationen zurück, die
diese verlangen. Die Rücknahme prüft das nicht für Sie; sie lehnt nur eine Migration ab, die nicht `done` ist oder
kein `down()` hat. Der Befehl nimmt eine Migration nach der anderen, nehmen Sie eine Kette also mit einem Befehl je
Migration zurück, die neueste zuerst.

## Nächster Schritt

[Migrationen testen](migrationen-testen.md): die Prüfung, die Simulation und ein PHPUnit-Test.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Zurücknehmen und Sicherungspunkte](../../../anleitungen/migrationen-rueckgaengig-und-captions.md), [exp:migration:rollback](../../../beispiele/befehle/expmigrationrollback.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
