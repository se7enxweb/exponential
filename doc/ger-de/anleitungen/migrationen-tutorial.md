# Migrationen: eine erste PHP-Migration, Schritt für Schritt

In diesem Tutorial schreiben Sie zwei PHP-Migrationen für eine Site: Die eine legt eine Tabelle `exp_news_note` mit
einer ersten Zeile an, die andere veröffentlicht einen Ordner „News“ unter der Inhaltswurzel und vermerkt ihn in der
Tabelle. Sie prüfen sie, sehen sich Probelauf und Simulation an, führen sie aus, lesen die Aufzeichnung, nehmen eine
mit ihrem `down()` zurück und sehen, was passiert, wenn eine Migration scheitert. Zum Schluss schreiben Sie eine
YAML-Migration mit Referenzen, einer Schleife und einer Bedingung. Es dauert etwa 25 Minuten.

Die Ausgaben unten stammen aus einem Lauf auf SQLite am 31. Oktober 2026, auf einer Installation mit den Grunddaten;
Lauf-IDs, Zeiten und Objekt-IDs sind bei Ihnen andere. Führen Sie jeden Befehl im Wurzelverzeichnis der Installation
als der Benutzer aus, unter dem der Webserver läuft (als root mit `--allow-root-user`). Die Ausgaben der Befehle sind
Englisch.

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · [Migrationen](migrationen.md) · Weiter: [PHP-Migrationen](migrationen-php.md)

## Bevor Sie beginnen

- Exponential 6.0.15 mit den Aufzeichnungstabellen: `./console exp:upgrade:status` zeigt
  `6.0.15-migration-tables` als `done` (steht dort `pending`, führen Sie zuerst `./console exp:upgrade --apply` aus).
- Eine Entwicklungsinstallation. Ein Lauf legt einen Sicherungspunkt an und committet in das Git-Repository der Site;
  geben Sie beim ersten Versuch wie unten `--no-commit` an, damit nichts ein Remote erreicht.

## 1. Die erste Migration erzeugen

```bash
./console exp:migration:generate --skeleton news_note_table --description="A table for news notes, with a first note"
```

Ohne `--apply` zeigt der Befehl die Datei, die er schreiben würde:

```text
Exponential migrations: generate, dry run (--apply writes the file)
File migrations/20261031120000_news_note_table.php
Migration 20261031120000_news_note_table, class \Site\Migrations\NewsNoteTable20261031120000

    <?php
    // description: A table for news notes, with a first note
    namespace Site\Migrations;
    ...
PASS
```

Schreiben Sie sie:

```bash
./console exp:migration:generate --skeleton news_note_table --description="A table for news notes, with a first note" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261031120000_news_note_table.php
Migration 20261031120000_news_note_table, class \Site\Migrations\NewsNoteTable20261031120000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Der Dateiname beginnt mit der aktuellen Zeit; die Klasse ist nach Name und Zeit benannt, im Namensraum
`Site\Migrations`, sodass keine zwei Migrationen dieselbe Klasse deklarieren. Eine vorhandene Datei wird nie ersetzt.

## 2. Ausfüllen

Ersetzen Sie den Inhalt der Klasse durch die Änderung. `plan()` nennt, was `up()` tun würde, ohne etwas zu ändern
(der Probelauf zeigt es); `up()` tut es und nennt, was es getan hat; `down()` nimmt es zurück.

```php
<?php
// description: A table for news notes, with a first note
// author: Site team
namespace Site\Migrations;

final class NewsNoteTable20261031120000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';

    /** @return array the table in the format of share/db_schema.dba */
    public static function schema()
    {
        return array( self::TABLE => array(
            'name' => self::TABLE,
            'fields' => array(
                'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'title' => array( 'length' => 150, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
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
        $context->db()->query( 'INSERT INTO ' . self::TABLE . " ( id, title ) VALUES ( 1, 'First note' )" );
        $context->setReference( 'news_note_table', self::TABLE );
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

`expKernelTableSchema::install()` legt die Tabelle auf jeder Engine aus einer Definition im Format von
`share/db_schema.dba` an (SQL auf MySQL/MariaDB, PostgreSQL, SQLite und Oracle, eine Collection auf MongoDB). Alles
geht über `$context->db()`, sodass die Transaktion des Laufs verwendet wird.

## 3. Eine zweite Migration, die die erste braucht

Schreiben Sie `migrations/20261031120100_news_folder.php` von Hand oder erzeugen Sie sie wie oben:

```php
<?php
// description: The folder News below the content root
// requires: 20261031120000_news_note_table
namespace Site\Migrations;

final class NewsFolder20261031120100 extends \expMigrationPhp
{
    const REMOTE_ID = 'site-news-folder';

    public function plan( $context )
    {
        return array( 'publish the folder News below node 2' );
    }

    public function up( $context )
    {
        $root = \eZContentObjectTreeNode::fetch( 2 );
        if ( !$root instanceof \eZContentObjectTreeNode )
            throw new \RuntimeException( 'there is no content root (node 2)' );
        $object = \eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 2,
            'language' => $root->attribute( 'object' )->attribute( 'initial_language_code' ),
            'class_identifier' => 'folder',
            'creator_id' => \eZUser::currentUserID(),
            'remote_id' => self::REMOTE_ID,
            'attributes' => array( 'name' => 'News' ),
        ), false );
        if ( !$object instanceof \eZContentObject )
            throw new \RuntimeException( 'the folder News could not be published' );
        $id = (int)$object->attribute( 'id' );
        $context->db()->query( 'INSERT INTO ' . $context->reference( 'news_note_table', 'exp_news_note' )
                               . " ( id, title ) VALUES ( 2, 'folder $id' )" );
        return array( "published the folder News (object $id)" );
    }

    public function down( $context )
    {
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( $object instanceof \eZContentObject )
            $object->purge();
        $context->db()->query( 'DELETE FROM exp_news_note WHERE id = 2' );
        return array( 'removed the folder News and its note' );
    }
}
```

Die Kopfzeile `requires:` lässt sie nach der ersten Migration laufen, was die Zeitstempel auch sagen. Der Ordner wird
als Benutzer des Laufs angelegt (`eZUser::currentUserID()`): der angemeldete Benutzer, auf der Konsole
`[RunSettings] RunAsUser`. Über die Remote-ID findet `down()` den Ordner auf jeder Installation, wo seine Objekt-ID eine
andere ist.

## 4. Prüfen

```bash
./console exp:migration:check
```

```text
Exponential migrations: check
  site                     migrations

  ok    20261031120000_news_note_table               php  migrations/20261031120000_news_note_table.php
  ok    20261031120100_news_folder                   php  migrations/20261031120100_news_folder.php

2 migration(s) checked
PASS
```

Die Prüfung braucht keine Datenbank und führt nichts aus; nehmen Sie sie in CI auf. Ein falscher Schlüssel im Kopf,
eine Anforderung, die es nicht gibt, ein Zyklus, ein PHP-Syntaxfehler oder eine zweite Klasse sind jeweils ein Fehler
mit Exit-Code 1.

## 5. Status und Probelauf

```bash
./console exp:migration:status
```

```text
Exponential migrations: status
Run 20261031-193019-c2dff4, environment development, database sqlite (transactions: full)

  pending     20261031120000_news_note_table               site               A table for news notes, with a first note
  pending     20261031120100_news_folder                   site               The folder News below the content root

Summary: 2 migration(s): 0 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261031-193019-1a60c4, environment development, database sqlite (transactions: full)

  would-apply 20261031120000_news_note_table               site               A table for news notes, with a first note
              would create the table exp_news_note
              would write the first note
  would-apply 20261031120100_news_folder                   site               The folder News below the content root
              would publish the folder News below node 2

Captions: pre would-take, post would-take
Summary: 2 migration(s): 0 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

Der Probelauf prüft auch die Code-Bäume: Haben `settings/`, `extension/` oder `design/` nicht committete Änderungen,
warnt er, dass ein Lauf mit `--apply` abgelehnt würde.

## 6. Simulieren

Auf PostgreSQL und SQLite können die Migrationen wirklich laufen, in einer Transaktion, die am Ende zurückgerollt wird:

```bash
./console exp:migration:dry-run --simulate --diff
```

```text
Exponential migrations: simulation, the database is rolled back afterwards
Run 20261031-193020-599bb5, environment development, database sqlite (transactions: full), as admin

  simulated   20261031120000_news_note_table               site               A table for news notes, with a first note
              would create the table exp_news_note
              would write the first note
              simulation passed in 7 ms
              created exp_news_note
              wrote the note 1
              + table exp_news_note (2 columns, 1 row)
  simulated   20261031120100_news_folder                   site               The folder News below the content root
              would publish the folder News below node 2
              simulation passed in 359 ms
              published the folder News (object 57)
              ~ table exp_news_note: rows 1 -> 2
              ~ table ezcontentobject: rows 18 -> 19
              ~ table ezcontentobject_attribute: rows 85 -> 90
              ~ table ezcontentobject_tree: rows 19 -> 20
              ...

Summary: 2 migration(s): 0 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 simulated
WARN simulated in a transaction that was rolled back: the database is unchanged, but what the migrations wrote outside it (files, caches, the search index, mail) is not undone
PASS
```

(Der Diff nennt jede Tabelle, die sich geändert hat; einige Zeilen sind hier ausgelassen.) Auf MySQL/MariaDB, Oracle
und MongoDB läuft die Simulation nicht: Dort wird eine Schemaänderung sofort committet, also zeigt der Befehl den Plan
mit einer Warnung.

## 7. Ausführen

```bash
./console exp:migration:migrate --apply --no-commit
```

Auf der Installation dieses Tutorials waren auch die Sicherungspunkte abgeschaltet (`--no-caption`), die Ausgabe zeigt
also nur den Lauf:

```text
Exponential migrations: run
Run 20261031-193020-1356d6, environment development, database sqlite (transactions: full), as admin

  applied     20261031120000_news_note_table               site               A table for news notes, with a first note
              created exp_news_note
              wrote the note 1
  applied     20261031120100_news_folder                   site               The folder News below the content root
              published the folder News (object 57)

Repository: no commit, push disabled
Summary: 2 migration(s): 2 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 2 applied
WARN no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically
PASS
```

Mit Sicherungspunkten (die Voreinstellung) sagt der Lauf, welche er angelegt hat:

```text
Captions: pre 20261031-193126, post 20261031-193127
```

Der Lauf hat sein Protokoll geschrieben, `migrations/runs/20261031-193020-1356d6.log`:

```text
2026-11-01T02:30:20Z info run 20261031-193020-1356d6 by admin on sqlite in development: 2 to apply
2026-11-01T02:30:20Z info applied 20261031120000_news_note_table in 3 ms: created exp_news_note | wrote the note 1
2026-11-01T02:30:20Z info applied 20261031120100_news_folder in 21 ms: published the folder News (object 57)
2026-11-01T02:30:20Z info run 20261031-193020-1356d6 done: 2 applied, 0 failed
2026-11-01T02:30:20Z info the record of the run is not committed ([RepositorySettings] Commit=disabled or --no-commit)
```

und die Statusdatei `migrations/status/development.json` (Schema `exp-migration-status/1`) mit beiden Migrationen als
`done`. Ohne `--no-commit` werden beide Dateien committet, als `migrations/development/<run-id>` getaggt und gepusht
([Das Migrations-Repository](migrationen-repositories.md)).

## 8. Die Aufzeichnung

```bash
./console exp:migration:status
```

```text
Exponential migrations: status
Run 20261031-193020-ec6ba1, environment development, database sqlite (transactions: full)

  done        20261031120000_news_note_table               site               A table for news notes, with a first note
              recorded done by run 20261031-193020-1356d6 on 2026-10-31T19:30:20-07:00
  done        20261031120100_news_folder                   site               The folder News below the content root
              recorded done by run 20261031-193020-1356d6 on 2026-10-31T19:30:20-07:00

The latest runs, newest first:
  20261031-193020-1356d6   done        2026-10-31T19:30:20-07:00  cli admin, push disabled
Summary: 2 migration(s): 2 done, 0 pending, 0 not needed, 0 failed, 0 unknown
PASS
```

Ein zweites `exp:migration:migrate` zeigt jetzt beide als `done` und tut nichts.

## 9. Eine geänderte Datei

Bearbeiten Sie `20261031120100_news_folder.php`, nachdem sie gelaufen ist (hier: ein Kommentar am Ende), und führen Sie
den Lauf erneut aus:

```text
  done        20261031120000_news_note_table               site               A table for news notes, with a first note
  refused     20261031120100_news_folder                   site               The folder News below the content root
              CHANGED: the file is not the one applied (checksum 75375541e9e9 -> c82e1066cc76)

Summary: 2 migration(s): 2 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 refused
FAIL: 20261031120100_news_folder changed after it was applied (checksum 75375541e9e9 -> c82e1066cc76); a run with --force --reason=<text> accepts the change
FAIL (exit 1)
```

Eine gelaufene Migration läuft nie wieder, eine geänderte Datei würde also unterschiedliche Installationen bedeuten.
Spielt die Änderung keine Rolle, nehmen Sie sie mit einer Begründung an; sie wird aufgezeichnet und im Audit
festgehalten:

```bash
./console exp:migration:migrate --apply --force --reason="a comment only"
```

```text
  accepted    20261031120100_news_folder                   site               The folder News below the content root
              the change of the file was accepted: a comment only
```

Eine echte Änderung gehört in eine neue Migration.

## 10. Eine Migration zurücknehmen

```bash
./console exp:migration:rollback 20261031120100_news_folder
```

```text
Exponential migrations: rollback with down(), dry run (--apply undoes them)
Run 20261031-193104-4d6423, environment development, database sqlite (transactions: full)

  would-undo  20261031120100_news_folder                   site               The folder News below the content root

Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 would-undo
PASS
```

```bash
./console exp:migration:rollback 20261031120100_news_folder --apply
```

```text
Exponential migrations: rollback with down()
Run 20261031-193104-c8d367, environment development, database sqlite (transactions: full), as admin

  undone      20261031120100_news_folder                   site               The folder News below the content root
              removed the folder News and its note

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 undone
PASS
```

Die Aufzeichnung der Migration wird entfernt, sie steht also wieder aus; das nächste
`exp:migration:migrate --apply` führt sie aus.

## 11. Ein Fehler

Fügen Sie eine Migration hinzu, die eine Zeile schreibt und dann scheitert:

```php
<?php
// description: Writes a note, then fails
namespace Site\Migrations;

final class FailsAfterChange extends \expMigrationPhp
{
    public function up( $context )
    {
        $context->db()->query( "INSERT INTO exp_news_note ( id, title ) VALUES ( 3, 'half done' )" );
        throw new \RuntimeException( 'the import file is missing' );
    }
}
```

und führen Sie den Lauf mit Sicherungspunkten aus (die Voreinstellung):

```text
Exponential migrations: run
Run 20261031-193127-c293f8, environment development, database sqlite (transactions: full), as admin

  done        20261031120000_news_note_table               site               A table for news notes, with a first note
  done        20261031120100_news_folder                   site               The folder News below the content root
  failed      20261031120300_fails_after_change            site               Writes a note, then fails
              the import file is missing

Captions: pre 20261031-193128, failure 20261031-193128-2
Restore: restored (caption 20261031-193128)
Repository: no commit, push disabled
Summary: 3 migration(s): 2 done, 0 pending, 0 not needed, 1 failed, 0 unknown
WARN restored the caption 20261031-193128 taken before the run (database, storage, settings); the failed state is kept in the caption 20261031-193128-2
FAIL (exit 1)
```

Die Transaktion der Migration hat ihre Zeile zurückgenommen; dann wurde der Zustand in einem Sicherungspunkt der Art
`migration-failure` festgehalten und der Sicherungspunkt von vor dem Lauf wiederhergestellt. Beheben Sie die Migration
(oder löschen Sie die Datei, sie ist ja nie gelaufen) und führen Sie den Lauf erneut aus.
[Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md) erklärt jeden Teil, und
[Fehlerbehebung](migrationen-fehlerbehebung.md), was bei jeder Meldung zu tun ist.

## 12. Eine YAML-Migration

Derselbe Lebenszyklus gilt für eine Migration aus YAML-Schritten. Die Ausgabe unten stammt vom 1. November 2026 von
einer Entwicklungsinstallation, auf der sie die einzige Migration war. Erzeugen Sie eine:

```bash
./console exp:migration:generate --skeleton news_languages --type=yml --description="The languages of the news, checked before the import" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101094239_news_languages.yml
Migration 20261101094239_news_languages
Next: write the steps, then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Das Gerüst hat den Kopf und `steps: []`, mit einem Beispiel in Kommentaren. Ersetzen Sie es durch:

```yaml
# description: The languages of the news, checked before the import
# author: Site team
steps:
    - type: reference
      set:
          news_languages: [ eng-US, ger-DE ]
          news_parent: "${idmap.node.news}"
    - foreach: "${ref.news_languages}"
      as: language
      steps:
          - type: assert
            that: { value: "${loop.language}", matches: "/^[a-z]{3}-[A-Z]{2}$/" }
            message: "${loop.language} is not a language code"
    - type: assert
      if: { environment: production }
      that: { value: "${ref.news_parent}", not_equals: 2 }
      message: production names its own news node in its id map
```

`${idmap.node.news}` liest die ID-Zuordnung der Umgebung, hier `migrations/idmap/development.json`:

```json
{
    "schema": "exp-migration-idmap/1",
    "ids": {
        "node": {
            "news": 2
        }
    }
}
```

Die Prüfung liest das YAML und prüft jeden Schritt gegen seinen Schritttyp, ohne Datenbank. Eine Datei mit Fehlern
(ein falsch geschriebener Typ, ein unbekannter Operator, eine Schleifenvariable, die keine Schleife setzt, ein
Referenzname, der mit einer Ziffer beginnt) zeigt jeden mit seiner Stelle:

```text
Exponential migrations: check
  site                     migrations

  ok    20261101094239_news_languages                yml  migrations/20261101094239_news_languages.yml
  FAIL  20261101094500_news_typo                     yml  migrations/20261101094500_news_typo.yml
        steps[0]: the unknown step type refrence (registered: assert, reference; an extension adds one in migration.ini [StepSettings] StepTypeList)
        steps[1] (assert).that: value takes exactly one operator (equals, not_equals, in, not_in, gt, gte, lt, lte, matches, empty), not bigger
        steps[2].steps[0]: ${loop.language} names no variable of a loop around it (item, key, index)
        steps[2].steps[0] (reference): set.1st: a reference name is letters, digits and _, not starting with a digit

2 migration(s) checked
...
FAIL (exit 1)
```

Ohne die fehlerhafte Datei zeigt der Probelauf den Plan, mit aufgelöster Schleife und übersprungenem Schritt für
die Produktion:

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-094515-4b20db, environment development, database sqlite (transactions: full)

  would-apply 20261101094239_news_languages                site               The languages of the news, checked before the import
              would set the reference news_languages to ["eng-US","ger-DE"]
              would set the reference news_parent to 2
              would run for each of 2 item(s), as language:
                would check that {"value":"eng-US","matches":"/^[a-z]{3}-[A-Z]{2}$/"}
                would check that {"value":"ger-DE","matches":"/^[a-z]{3}-[A-Z]{2}$/"}
              would skip steps[2] (assert): the condition is false

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

und der Lauf führt sie aus:

```bash
./console exp:migration:migrate --apply --no-commit --no-caption
```

```text
Exponential migrations: run
Run 20261101-094534-57f2cf, environment development, database sqlite (transactions: full), as admin

  applied     20261101094239_news_languages                site               The languages of the news, checked before the import
              set the reference news_languages
              set the reference news_parent
              checked that {"value":"eng-US","matches":"/^[a-z]{3}-[A-Z]{2}$/"}
              checked that {"value":"ger-DE","matches":"/^[a-z]{3}-[A-Z]{2}$/"}
              skipped steps[2] (assert): the condition is false

Repository: no commit, push disabled
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 1 applied
WARN no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically
PASS
```

Die Aufzeichnung, das Laufprotokoll, die Prüfsummen-Sperre, die Sicherungspunkte und der Commit funktionieren wie
bei den PHP-Migrationen oben. Ein falsches `assert` lässt die Migration scheitern wie eine Ausnahme in `up()`, und
ihre Transaktion wird zurückgerollt. Diese Migration hat eine Schleife und kein `down:`, darum lehnt
`exp:migration:rollback 20261101094239_news_languages` sie ab: Fügen Sie eine `down:`-Liste hinzu, um eine
YAML-Migration selbst zurückzunehmen, oder setzen Sie den Lauf auf seinen Sicherungspunkt zurück.

Neben `reference` und `assert` legen die Schritttypen `content`, `location` und `content_version` des Kernels Objekte,
ihre Übersetzungen, Orte und Versionen an und ändern sie ([Schritte von YAML-Migrationen](migrationen-yaml-schritte.md));
die Schritte für Klassen und Rollen kommen in `migration.ini [StepSettings] StepTypeList` hinzu, sobald es sie gibt,
und eine Erweiterung registriert dort ihre eigenen. [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md#die-yaml-migration) nennt jeden
Schlüssel, Platzhalter, jede Bedingung und Schleife.

## Was Sie getan haben

- eine Migration erzeugt und `plan()`, `up()` und `down()` ausgefüllt;
- zwei Migrationen mit `requires:` geordnet und mit `setReference()` einen Wert von der einen an die nächste gegeben;
- sie ohne Datenbank geprüft, Probelauf und Simulation angesehen;
- sie ausgeführt, die Aufzeichnung und das Laufprotokoll gelesen, die Prüfsummen-Sperre kennengelernt;
- eine mit `down()` zurückgenommen und einen Fehler gesehen, der aus seinem Sicherungspunkt wiederhergestellt wurde;
- eine YAML-Migration mit Referenzen, einer Schleife, einer Bedingung und der ID-Zuordnung geschrieben, geprüft,
  geplant und ausgeführt.

Weiter: [PHP-Migrationen](migrationen-php.md) für alles, was eine Migrationsklasse kann.

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- [Die Anleitung](migrationen.md), [PHP-Migrationen](migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](migrationen-repositories.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
