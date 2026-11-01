---
title: "Lektion 9.5: Migrationen"
audience: trainer, developer
category: training
order: 95
summary: Die Teilnehmenden liefern eine Datenbankänderung als Migration aus - ein Gerüst erzeugen, plan(), up() und down() für eine Tabelle schreiben, die Datei ohne Datenbank prüfen, den Probelauf lesen, auf SQLite simulieren, ohne Commit ausführen und mit down() zurücknehmen - und lernen, warum dann jede Installation dieselbe Änderung in derselben Reihenfolge bekommt. 50 Minuten.
keywords: entwickler, migrationen, migration, probelauf, simulieren, zurücknehmen, sicherungspunkt, schemaänderung, datenbankänderung, auslieferung, developers course, migrations, exp:migration:generate, exp:migration:check, exp:migration:migrate, dry run, simulate, rollback, down, expMigrationPhp, expKernelTableSchema, caption, schema change, database change, deploy
type: help
---
# Lektion 9.5: Migrationen

In den Lektionen 9.3 und 9.4 haben die Teilnehmenden eine Tabelle auf ihrer eigenen Installation von Hand angelegt.
Eine echte Site hat eine Entwicklungskopie, eine Staging-Kopie und die Produktion, und jede braucht diese Tabelle.
Eine Migration ist die Änderung, einmal als Datei in Git geschrieben und auf jede Installation in derselben
Reihenfolge angewendet, mit einer Aufzeichnung, was gelaufen ist, einem Backup (einem Sicherungspunkt) vor dem Lauf
und einem Weg zurück. In dieser Lektion schreiben die Teilnehmenden eine, führen sie aus und nehmen sie zurück.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 9: Die Konsole und persistente Objekte](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 9.4](sicheres-sql-mit-ezdb.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was eine Migration ist und warum eine von Hand angelegte Tabelle für eine Site mit mehreren
  Installationen nicht reicht;
- eine Migration mit `exp:migration:generate --skeleton` erzeugen und ihre Teile benennen: Kopf, Namensraum, Klasse;
- `plan()`, `up()` und `down()` für eine Tabelle mit `expKernelTableSchema::install()` schreiben;
- eine Migration mit `exp:migration:check` prüfen, den Probelauf lesen und sie auf SQLite simulieren;
- sie mit `--apply --no-commit` ausführen, den Stand lesen und sie mit `exp:migration:rollback <id> --apply`
  zurücknehmen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Migrationen: die Anleitung](../../../../anleitungen/migrationen.md) und
[das Tutorial](../../../../anleitungen/migrationen-tutorial.md).

1. **Das Problem** (3 Minuten). Fragen Sie: „Sie haben `mysite_greeting` auf Ihrem Laptop angelegt. Wie kommt die
   Tabelle auf die Produktion?“ Sammeln Sie Antworten (eine SQL-Datei, eine Notiz in einem Ticket, von Hand). Nennen
   Sie dann, was jeder fehlt: eine Reihenfolge, eine Aufzeichnung, was wo gelaufen ist, ein Backup, ein Weg zurück.
2. **Die Datei** (4 Minuten). Zeigen Sie `migrations/20261101140000_mysite_note_table.php`: die ID ist der
   Dateiname ohne `.php`; die Kommentarzeilen oben sind der Kopf (`description`, `requires`, `environments` ...); die
   Klasse steht in `Site\Migrations` und erweitert `\expMigrationPhp`.
3. **Die drei Methoden** (4 Minuten). `plan()` sagt, was sich ändern würde, und schreibt nichts (der Probelauf zeigt
   es); `up()` macht die Änderung und gibt zurück, was es getan hat, oder wirft; `down()` macht sie rückgängig. Alles
   geht über `$context->db()`, die Verbindung des Laufs.
4. **Der Lebenszyklus** (5 Minuten). Schreiben Sie die Befehle an die Tafel: `generate`, `check`, `migrate` (ein
   Probelauf), `dry-run --simulate --diff`, `migrate --apply`, `status`, `rollback <id> --apply`. Jeder Befehl, der
   schreibt, ist ein Probelauf, bis er `--apply` bekommt.
5. **Das Sicherheitsnetz** (4 Minuten). Ein Lauf hält eine Sperre, nimmt zuerst einen Sicherungspunkt, führt jede
   Migration in einer Transaktion aus, wo die Datenbank eine hat (SQLite und PostgreSQL: alles; MySQL: nur Daten),
   hält beim ersten Fehler an und stellt den Sicherungspunkt wieder her. Nach einem Lauf wird die Aufzeichnung
   committet, getaggt und gepusht; im Kurs schalten wir das mit `--no-commit` ab.

### Vorführung

Auf der Installation des Trainers, vor der Übung: führen Sie `./console exp:migration:status` aus (die
Upgrade-Schritte des Kernels erscheinen zuerst, als Migrationen der Quelle `kernel`), erzeugen Sie ein Gerüst ohne
`--apply`, um zu zeigen, was es schreiben würde, und führen Sie `./console exp:migration:check` aus.

### Häufige Fragen

**Ist das dasselbe wie `exp:upgrade`?** Nein, aber beide teilen die Aufzeichnungstabelle, die Sperre und die
Sicherungspunkte. `exp:upgrade` bringt eine Installation auf ein neues Kernel-Release; Migrationen tragen die
eigenen Änderungen einer Site.

**Kann ich eine Migration ändern, nachdem sie gelaufen ist?** Nein. Die Aufzeichnung behält eine Prüfsumme, und der
nächste Lauf lehnt die geänderte Datei ab. Schreiben Sie eine neue Migration.

**Kann eine Migration YAML oder SQL sein?** In 6.0.15 werden Migrationen in PHP geschrieben; andere Typen werden
gefunden und geprüft, aber nicht ausgeführt.

### Stolperfallen

- **Der führende Backslash.** Die Datei hat einen Namensraum: schreiben Sie `\expMigrationPhp`,
  `\expKernelTableSchema`, `\RuntimeException`.
- **Eine zweite Klasse in der Datei.** Eine Klasse je Datei; `exp:migration:check` lehnt zwei ab.
- **Eine eigene Verbindung.** `eZDB::instance()` funktioniert, aber außerhalb der Transaktion des Laufs; nehmen Sie
  `$context->db()`.
- **Als root ausführen.** Führen Sie die Befehle als eigener Benutzer aus, wie in Lektion 9.1.

## Praktische Übung

Auf der Entwicklungsinstallation (SQLite), im Wurzelverzeichnis der Installation.

1. Prüfen Sie, ob es die Aufzeichnungstabellen gibt: `./console exp:upgrade:status` zeigt
   `6.0.15-migration-tables` als `done`; steht der Schritt aus, führen Sie `./console exp:upgrade --apply` aus.
2. Erzeugen Sie die Migration:

   ```bash
   ./console exp:migration:generate --skeleton mysite_note_table --description="A table for notes on greetings, with a first note" --apply
   ```

3. Öffnen Sie die genannte Datei und ersetzen Sie den Rumpf der Klasse so, dass sie die Tabelle `mysite_note` mit
   den Spalten `id` und `text` anlegt, eine Notiz schreibt und die Tabelle in `down()` löscht.
4. Prüfen Sie sie ohne Datenbank: `./console exp:migration:check`.
5. Lesen Sie den Probelauf: `./console exp:migration:migrate`.
6. Simulieren Sie sie: `./console exp:migration:dry-run --simulate --diff`. Finden Sie die Zeile, die sagt, dass die
   Tabelle hinzugekommen ist.
7. Führen Sie sie ohne Commit aus: `./console exp:migration:migrate --apply --no-commit`.
8. Lesen Sie die Aufzeichnung: `./console exp:migration:status`, dann noch einmal `./console exp:migration:migrate`.
9. Nehmen Sie sie zurück: `./console exp:migration:rollback <id>` (ein Probelauf), dann mit `--apply`.
10. Führen Sie sie noch einmal mit `--apply --no-commit` aus und committen Sie die Migrationsdatei in Ihren Branch.

### Lösung

```php
<?php
// description: A table for notes on greetings, with a first note
// author: Course learner
namespace Site\Migrations;

final class MysiteNoteTable20261101140000 extends \expMigrationPhp
{
    const TABLE = 'mysite_note';

    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'text' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
            ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    public function plan( $context )
    {
        return array( 'create the table ' . self::TABLE, 'write the first note' );
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO ' . self::TABLE . " ( id, text ) VALUES ( 1, 'First note' )" );
        return array( 'created ' . self::TABLE, 'wrote the note 1' );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

## Erwartetes Ergebnis

- Schritt 4 listet die Migration als `ok` und endet mit `PASS`.
- Schritt 5 listet sie als `would-apply` mit den zwei Zeilen von `plan()`.
- Schritt 6 listet sie als `simulated` mit `+ table mysite_note (2 columns, 1 row)` und warnt, dass die Datenbank
  zurückgerollt wurde.
- Schritt 7 listet sie als `applied` mit den zwei Zeilen von `up()`; das Protokoll steht in
  `migrations/runs/<run-id>.log`.
- Schritt 8 zeigt sie als `done`; der zweite Probelauf tut nichts.
- Schritt 9 listet sie als `would-undo`, dann `undone`; der Stand zeigt sie wieder als `pending`.
- Der Trainer lässt alle Teilnehmenden sagen, was Probelauf, Simulation und Lauf jeweils an der Datenbank geändert
  haben.

## Quiz

1. Wie lautet die ID der Migration `migrations/20261101140000_mysite_note_table.php`?
2. Welche Methode darf nie in die Datenbank schreiben?
   A. `up()` B. `down()` C. `plan()` D. keine
3. Was tut `exp:migration:migrate` ohne `--apply`?
4. Warum läuft die Simulation nicht auf MySQL?
5. Eine Migration ist auf Staging gelaufen, und Sie finden einen Fehler darin. Was tun Sie?

<details>
<summary>Antworten</summary>

1. `20261101140000_mysite_note_table`.
2. **C.** `plan()`: es läuft bei jedem Probelauf.
3. Einen Probelauf: es listet jede ausstehende Migration mit ihrem Plan und ändert nichts.
4. MySQL committet eine Schemaänderung sofort, eine Transaktion kann sie also nicht zurückrollen.
5. Eine neue Migration schreiben, die ihn korrigiert; die gelaufene Datei bleibt, wie sie ist (die
   Prüfsummen-Sperre lehnt eine geänderte Datei ab).

</details>

## Weiterführende Literatur

- [Migrationen: der Einstieg](../../../migrationen/README.md).
- [Migrationen: eine erste PHP-Migration, Schritt für Schritt](../../../../anleitungen/migrationen-tutorial.md).
- [PHP-Migrationen](../../../../anleitungen/migrationen-php.md).
- [Eine Datenbanktabelle anlegen](../../../migrationen/rezepte/eine-datenbanktabelle-anlegen.md).

## Verwandte Seiten

- [Modul 9: Die Konsole und persistente Objekte](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
