---
title: "Migrationen auf MySQL, PostgreSQL, SQLite, Oracle und MongoDB"
audience: developer, software-engineer, dba, hosting
category: migrations
order: 18
summary: Was jede Datenbank-Engine für eine Migration bedeutet - Transaktionen, die Simulation, Sicherungspunkte und die Wiederherstellung, Schemaänderungen, die sofort committet werden - und wie eine Migration auf MySQL/MariaDB, PostgreSQL, SQLite, Oracle und MongoDB funktioniert.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Engines, Transaktionen, Sicherungspunkt, portables SQL, Schema-Handler, migration, migrations, schema change, database change, engines, MySQL, MariaDB, PostgreSQL, SQLite, Oracle, ezoracle, MongoDB, transactions, DDL, engines header, portable SQL
type: help
---
# Migrationen auf MySQL, PostgreSQL, SQLite, Oracle und MongoDB

Exponential läuft auf fünf Datenbank-Engines, und eine Migration läuft auf der, die die Installation nutzt. Die Engine
entscheidet, wie viel einer gescheiterten Migration der Runner selbst zurücknehmen kann, ob ein Lauf simuliert werden
kann und wie ein Sicherungspunkt angelegt und wiederhergestellt wird. Diese Seite zeigt die Unterschiede und eine
Migration, die auf jeder SQL-Engine eine Spalte hinzufügt.

## Was Sie am Ende haben

- eine Tabelle, die Sie prüfen, bevor Sie entscheiden, wo eine Migration zuerst läuft;
- eine Migration, die die Spalte `exp_news_note.priority` auf MySQL/MariaDB, PostgreSQL, SQLite und Oracle hinzufügt
  und auf MongoDB `not-needed` ist;
- die Gewohnheiten, die eine Migration portabel halten.

## Bevor Sie beginnen

- Kennen Sie Ihre Engine: `./console exp:migration:status` nennt sie in der zweiten Zeile
  (`database sqlite (transactions: full)`).
- Lesen Sie [Eine Spalte oder einen Index hinzufügen](eine-spalte-oder-einen-index-hinzufuegen.md), wenn Sie noch keine
  Spalte in einer Migration angelegt haben.

## Die Engines im Überblick

| Engine | Transaktionen (`transactions:` in der Ausgabe) | `--simulate` | Sicherungspunkt | Wiederherstellung nach einem Fehler |
|---|---|---|---|---|
| SQLite | `full`: Schema und Daten werden zurückgenommen | ja | eine konsistente Kopie der Datei | die Kopie wird zurückgeschrieben |
| PostgreSQL | `full` | ja | `pg_dump --clean` | über `psql` |
| MySQL/MariaDB | `data`: eine Schemaänderung (`CREATE`, `ALTER`, `DROP`) wird sofort committet | nein, der Plan wird mit einer Warnung gelistet | `mysqldump` | über `mysql` |
| Oracle (ezoracle) | `data` | nein | keiner eingebaut: exportieren Sie selbst mit `expdp` | von Hand; der Lauf gibt die Schritte aus |
| MongoDB | `none`: jeder Schreibvorgang bleibt | nein | `mongodump` | über die Rücknahme von `exp:upgrade` |

Der nach einem Fehler wiederhergestellte Sicherungspunkt deckt ab, was eine Transaktion nicht kann. Auf MySQL/MariaDB,
Oracle und MongoDB ist er der einzige automatische Weg zurück; lassen Sie die Sicherungspunkte dort eingeschaltet.

## Schritte

### 1. Sagen, für welche Engines eine Migration ist

Der Kopfschlüssel `engines:` beschränkt eine Migration auf einige Engines. Auf jeder anderen ist sie `not-needed` und
läuft nie:

```php
// engines: mysql, postgresql, sqlite, oracle
```

Lassen Sie ihn weg, wenn die Migration überall funktioniert (Inhalte, Rollen und Einstellungen über die Klassen des
Kernels tun das meist). Setzen Sie ihn, wenn die Migration SQL schreibt oder Spalten ändert: MongoDB hat Collections,
keine Spalten und kein SQL.

### 2. Die Engine in der Migration fragen

`$context->engine()` liefert `mysql`, `postgresql`, `sqlite`, `oracle` oder `mongodb`. Verzweigen Sie nur dort, wo
SQL wirklich abweicht; lassen Sie den Schema-Handler der Engine das SQL schreiben, wo immer es geht (die Migration
unten).

### 3. Die Arbeit für Engines ordnen, die Schemaänderungen sofort committen

Auf MySQL/MariaDB und Oracle behält eine gescheiterte Migration ihre Schemaänderungen. Schreiben Sie die Migration so,
dass ein zweiter Lauf nach einem Fehler den halb erledigten Zustand findet und vervollständigt: fragen Sie zuerst den
Katalog (`detect()` oder `\expUpgradeCatalog::hasColumn()` in `up()`), und machen Sie wo möglich eine Schemaänderung je
Migration.

### 4. Den Lauf ansehen

```bash
./console exp:migration:migrate
```

```text
Exponential migrations: dry run, nothing is changed (--apply runs the migrations)
Run 20261101-090102-5d1e7a, environment development, database mysql (transactions: data)

  would-apply 20261101090000_news_note_priority             site               The column priority of exp_news_note
              would add the column exp_news_note.priority on mysql

Captions: pre would-take, post would-take
Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 would-apply
PASS
```

Auf MongoDB zeigt dieselbe Migration `not-needed` und `Summary: ... 1 not needed`.

## Die Migration

```php
<?php
// description: The column priority of exp_news_note
// engines: mysql, postgresql, sqlite, oracle
// author: Site team
namespace Site\Migrations;

final class NewsNotePriority20261101090000 extends \expMigrationPhp
{
    const TABLE = 'exp_news_note';
    const FIELD = 'priority';

    /** @return array die Spalte im Format von share/db_schema.dba */
    private static function field()
    {
        return array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 );
    }

    public function detect( $context )
    {
        return \expUpgradeCatalog::hasColumn( $context->db(), self::TABLE, self::FIELD ) === true ? \expMigration::DONE : null;
    }

    public function plan( $context )
    {
        return array( 'add the column ' . self::TABLE . '.' . self::FIELD . ' on ' . $context->engine() );
    }

    public function up( $context )
    {
        $engine = $context->engine();
        $params = array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                         'table_type' => $engine === 'mysql' ? 'innodb' : '' );
        $schema = array( self::TABLE => array( 'name' => self::TABLE, 'fields' => array( self::FIELD => self::field() ),
                                               'indexes' => array() ) );
        $handler = \eZDbSchema::instance( array( 'type' => $engine, 'instance' => \expKernelTableSchema::nullDB(),
                                                 'schema' => $schema ) );
        if ( !$handler )
            throw new \RuntimeException( "no schema handler for $engine" );
        // PostgreSQL: Vorgabe und NOT NULL in derselben Anweisung, damit vorhandene Zeilen gefüllt werden
        $sql = $engine === 'postgresql'
             ? 'ALTER TABLE ' . self::TABLE . ' ADD COLUMN '
               . $handler->generateFieldDef( self::TABLE, self::FIELD, self::field(), true, $params ) . ";\n"
             : $handler->generateAddFieldSql( self::TABLE, self::FIELD, self::field(), $params );
        $ran = \expUpgradeCatalog::execute( $context->db(), $sql );
        \expKernelTableSchema::reset();
        return array( 'added the column ' . self::TABLE . '.' . self::FIELD . ' (' . count( $ran ) . ' statement(s))' );
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), 'ALTER TABLE ' . self::TABLE . ' DROP COLUMN ' . self::FIELD . ";\n" );
        \expKernelTableSchema::reset();
        return array( 'dropped the column ' . self::TABLE . '.' . self::FIELD );
    }
}
```

`\expUpgradeCatalog::execute()` führt die Anweisungen einzeln aus und wirft bei der ersten, die scheitert, sodass der
Runner den Fehler sieht. `ALTER TABLE ... DROP COLUMN` in `down()` funktioniert auf MySQL/MariaDB, PostgreSQL, Oracle
und SQLite ab 3.35.

## Portable Gewohnheiten

- Gehen Sie über `$context->db()` und die Helfer des Kernels (`\expKernelTableSchema::install()` für Tabellen, den
  Schema-Handler für Spalten und Indizes, `\expUpgradeCatalog` für Fragen an den Katalog). Sie schreiben das SQL jeder
  Engine.
- Bezeichner klein und höchstens 30 Zeichen lang (Oracle); keine reservierten Wörter als Spaltennamen.
- Werte mit `$context->db()->escapeString()` maskieren, Zahlen mit `(int)` umwandeln.
- Kein `LIMIT` in `UPDATE` oder `DELETE`, keine Funktionen nur einer Engine, kein `SET @variable`; wählen Sie zuerst
  die IDs und schreiben Sie in Stapeln.
- Für MongoDB Inhalte, Rollen und Einstellungen über die Klassen des Kernels schreiben, nie mit SQL.
- Testen Sie auf den Engines, die Sie in Produktion einsetzen. SQLite simuliert am schnellsten, akzeptiert aber SQL,
  das MySQL oder PostgreSQL ablehnen.

## Prüfen

```bash
./console exp:migration:status
./console exp:upgrade:verify
```

`exp:migration:status` zeigt die Migration als `done` (oder `not-needed` auf MongoDB); `detect()` hält sie auch auf
einer Installation `done`, auf der die Spalte von Hand angelegt wurde.

## Zurücknehmen

`./console exp:migration:rollback 20261101090000_news_note_priority --apply` führt `down()` aus. Auf Oracle wird ein
Fehler während eines Laufs von Hand wiederhergestellt: der Bericht listet die Schritte (ein `impdp` des Exports, den
Sie vor dem Lauf angelegt haben).

## Nächster Schritt

[Sicherungspunkte und Rücknahme in der Praxis](sicherungspunkte-und-ruecknahme-in-der-praxis.md): was die
Sicherungspunkte jeder Engine enthalten und wie ein Lauf zurückgenommen wird.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Spalte oder einen Index hinzufügen](eine-spalte-oder-einen-index-hinzufuegen.md), [Große Datenmigrationen](grosse-datenmigrationen.md)
- [Portables SQL für Erweiterungen](../../datenbankadministratoren/portables-sql-fuer-erweiterungen.md),
  [Sicherungen je Engine](../../datenbankadministratoren/sicherungen-je-engine.md),
  [Eine Engine wählen](../../datenbankadministratoren/eine-engine-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
