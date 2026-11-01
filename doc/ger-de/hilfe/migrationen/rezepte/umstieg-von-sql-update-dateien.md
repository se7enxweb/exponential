---
title: "Umstieg von SQL-Update-Dateien"
audience: developer, software-engineer, dba, hosting, php-developer
category: migrations
order: 25
summary: Für Teams, die Datenbankänderungen als handgeschriebene SQL-Dateien und Update-Skripte ausliefern - wie eine Änderung zu einer PHP-Migration wird, die jede Engine kennt, aufgezeichnet, mit Sicherungspunkt versehen und committet, mit einem durchgerechneten Beispiel aus ALTER TABLE und UPDATE.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, SQL-Update-Dateien, Umstieg, Begriffe im Vergleich, migration, migrations, schema change, database change, SQL update files, dbupdate, update/database, update/common/scripts, ALTER TABLE, hand-written SQL, db_schema.dba, upgrade check, concept map
type: help
---
# Umstieg von SQL-Update-Dateien

Vor 6.0.15 war eine Änderung an der Datenbank einer Site eine SQL-Datei, von Hand ausgeführt: eine Datei je Engine in
`update/database/<treiber>/`, ein PHP-Skript in `update/common/scripts/` für das, was SQL nicht konnte, und ein Hinweis
in der Upgrade-Anleitung, was auszuführen war. Nichts hielt fest, welche Anweisungen gelaufen waren. Der Kernel liefert
solche Dateien weiterhin für von Hand aktualisierte Installationen
(`update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql` und die Geschwister für PostgreSQL und SQLite), seine eigenen
Änderungen sind aber jetzt Upgrade-Schritte und die eigenen Änderungen einer Site Migrationen. Diese Seite macht aus
einem handgeschriebenen Update eine Migration.

## Was Sie am Ende haben

- eine PHP-Migration anstelle einer SQL-Datei je Engine und eines Hinweises;
- dieselbe Änderung aufgezeichnet, mit einem Sicherungspunkt vor dem Lauf, committet und gepusht;
- das `.dba`-Schema für neue Installationen beibehalten.

## Bevor Sie beginnen

- Die Aufzeichnungstabellen existieren: `./console exp:upgrade:status` zeigt `6.0.15-migration-tables` als `done`.
- Halten Sie die SQL-Datei bereit, die Sie geschrieben hätten.

## Begriffe im Vergleich

| SQL-Update-Dateien | Migrationen |
|---|---|
| `dbupdate-<von>-<nach>.sql` je Treiber | eine `YYYYMMDDHHMMSS_<name>.php`, die die Engines innen unterscheidet |
| „diese Datei einmal, ganz, der Reihe nach ausführen“ in einer Anleitung | `./console exp:migration:migrate --apply` führt jede ausstehende Migration der Reihe nach einmal aus |
| niemand weiß, welche Anweisungen liefen | `exp_migration` zeichnet jede Migration auf; `exp:migration:status` listet sie |
| eine Anweisung scheitert mittendrin | die Transaktion der Migration wird zurückgenommen, wo die Engine eine hat, und der Sicherungspunkt von vor dem Lauf wiederhergestellt |
| eine Sicherung von Hand, vielleicht | ein Sicherungspunkt vor jedem Lauf |
| `update/common/scripts/<x>.php` | dasselbe PHP in `up()` |
| die Upgrade-Prüfung vergleicht Tabellen mit dem Schema | weiterhin: halten Sie `share/db_schema.dba` (oder das der Erweiterung) aktuell |

## Schritte

### 1. Das SQL, das Sie geschrieben hätten

```sql
-- update/database/mysql/news/dbupdate-news-1.1.sql
ALTER TABLE exp_news_note ADD COLUMN archived INT NOT NULL DEFAULT 0;
UPDATE exp_news_note SET archived = 1 WHERE id < 100;
```

dazu eine Datei für PostgreSQL und eine für SQLite mit denselben Anweisungen in ihren Dialekten.

### 2. Die Migration erzeugen

```bash
./console exp:migration:generate --skeleton news_note_archived --description="The column archived of exp_news_note, set on the old notes" --apply
```

```text
Exponential migrations: generate
Wrote migrations/20261101160000_news_note_archived.php
Migration 20261101160000_news_note_archived, class \Site\Migrations\NewsNoteArchived20261101160000
Next: fill in plan() and up(), then ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

### 3. Ausfüllen

Ersetzen Sie die erzeugte Klasse durch die Datei unten.

## Die Migration

```php
<?php
// description: The column archived of exp_news_note, set on the old notes
// engines: mysql, postgresql, sqlite, oracle
// author: Site team
namespace Site\Migrations;

final class NewsNoteArchived20261101160000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.archived', 'mark the notes below id 100 as archived' );
    }

    public function up( $context )
    {
        $db = $context->db();
        $done = array();
        if ( \expUpgradeCatalog::hasColumn( $db, self::TABLE, 'archived' ) !== true )
        {
            $type = $context->engine() === 'oracle' ? 'NUMBER(11)' : 'INTEGER';
            \expUpgradeCatalog::execute( $db, 'ALTER TABLE ' . self::TABLE . " ADD archived $type DEFAULT 0 NOT NULL;\n" );
            \expKernelTableSchema::reset();
            $done[] = 'added the column ' . self::TABLE . '.archived';
        }
        $db->query( 'UPDATE ' . self::TABLE . ' SET archived = 1 WHERE id < 100' );
        $done[] = 'marked the notes below id 100 as archived';
        return $done;
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), 'ALTER TABLE ' . self::TABLE . " DROP COLUMN archived;\n" );
        \expKernelTableSchema::reset();
        return array( 'dropped the column ' . self::TABLE . '.archived' );
    }
}
```

Die Spalte wird nur angelegt, wenn sie fehlt; eine Installation, auf der jemand die alte SQL-Datei schon von Hand
ausgeführt hat, wird also vervollständigt statt zu scheitern. Wo die Engines mehr als einen Typnamen brauchen, lassen
Sie den Schema-Handler das SQL schreiben
([Migrationen auf MySQL, PostgreSQL, SQLite, Oracle und MongoDB](hinweise-zu-den-datenbanken.md)).

## Nach der Migration

### 4. Die Schemadatei behalten

Ergänzen Sie die Spalte auch in der Tabellendefinition in `share/db_schema.dba` der Erweiterung der Site, damit eine
neue Installation sie bekommt und die Upgrade-Prüfung (Setup > Upgrade-Prüfung, **Überprüfe Datenbankkonsistenz**)
sie nicht meldet.

### 5. Prüfen, ansehen, ausführen

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:migrate --apply
```

```text
  applied     20261101160000_news_note_archived             site               The column archived of exp_news_note, set on the old notes
              added the column exp_news_note.archived
              marked the notes below id 100 as archived

Captions: pre 20261101-160105, post 20261101-160106
Repository: commit 9a8b7c6d5e4f, tag migrations/staging/20261101-160105-1f2e3d, push pushed
```

## Prüfen

`./console exp:migration:status` zeigt die Migration auf jeder Installation, die sie erreicht hat, als `done`. Entfernen
Sie die SQL-Dateien aus dem Repository, sobald keine Installation sie mehr braucht; der Verlauf bleibt in Git.

## Zurücknehmen

`./console exp:migration:rollback 20261101160000_news_note_archived --apply` führt `down()` aus. Beachten Sie, dass
`down()` die Spalte löscht und damit die vom `UPDATE` gesetzten Werte.

## Nächster Schritt

[Upgrade-Schritt oder Migration?](upgrade-schritt-oder-migration.md): das eigene Schema einer Erweiterung für jede
Installation ist ein Upgrade-Schritt, keine Migration.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Spalte oder einen Index hinzufügen](eine-spalte-oder-einen-index-hinzufuegen.md), [Migrationen auf jeder Engine](hinweise-zu-den-datenbanken.md)
- [Upgrades und SQL-Update-Dateien](../../datenbankadministratoren/upgrades-und-sql-update-dateien.md),
  [Schemaänderungen und Migrationen](../../softwareingenieure/schemaaenderungen-und-migrationen.md),
  [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
