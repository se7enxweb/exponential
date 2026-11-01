---
title: "Exponential-Migrationen für Doctrine-Nutzer"
audience: dxp-developer, developer
category: migrations
order: 52
summary: Für Entwickler, die Doctrine Migrations aus der DXP kennen und jetzt an einer Exponential-6-Site arbeiten - was gleich ist, was sich unterscheidet (Inhalte wie Schema, Sicherungspunkte, Commit, Tag und Push, Umgebungen, die Administration), die Befehle nebeneinander, dieselbe Tabelle in beiden, und wann welcher Stack gilt.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, DXP-Entwickler, Sicherungspunkt, Doctrine-Migrationen, migration, migrations, schema change, database change, deploy, exp:migration:migrate, Doctrine, doctrine:migrations:migrate, doctrine:migration:migrate, AbstractMigration, addSql, Version, dxp developer, Symfony, expMigrationPhp
featured: true
type: help
---
# Exponential-Migrationen für Doctrine-Nutzer

Diese Seite ist für Entwickler, die die Datenbank von DXP-Projekten mit Doctrine Migrations ändern
([Doctrine-Migrationen](doctrine-migrationen.md)) und jetzt an einer Exponential-6-Site arbeiten, dem Kernel ohne
Symfony. Exponential 6.0.15 hat eigene Migrationen. Die Idee ist die bekannte; Umfang und Sicherheitsnetz sind
breiter.

## Was gleich ist

- Eine Datei je Änderung, nach einem Zeitstempel benannt, in Git mit dem Code, der sie braucht.
- In Reihenfolge angewendet, je Datenbank aufgezeichnet, nie zweimal angewendet; eine gelaufene Migration wird nie
  geändert.
- Ein Probelauf vor dem echten Lauf, `up()` zum Anwenden, `down()` zum Zurücknehmen einer Migration.
- Ein Befehl in der Auslieferung, der anwendet, was aussteht.

## Was sich unterscheidet

| Thema | Doctrine Migrations (DXP) | Exponential-6-Migrationen |
|---|---|---|
| Was eine Migration ändert | SQL des Schemas und Ihrer eigenen Tabellen | alles: Tabellen, Inhaltsklassen, Inhalte, Rollen, Einstellungen, über die APIs des Kernels |
| Die Klasse | `final class Version20261101100000 extends AbstractMigration` | `final class NewsNote20261101100000 extends \expMigrationPhp` in `Site\Migrations` |
| Reihenfolge | nach Version | nach ID, dazu `requires:` im Kopf (andere Migrationen, Erweiterungen, Upgrade-Schritte des Kernels) |
| Die Aufzeichnung | `doctrine_migration_versions` | `exp_migration` und `exp_migration_run`, gespiegelt nach `migrations/status/<umgebung>.json` in Git |
| Geänderte Dateien | nicht bemerkt | von einer Prüfsummen-Sperre abgelehnt, außer `--force --reason` akzeptiert sie |
| Vor einem Lauf | Ihr eigenes Backup | ein Sicherungspunkt (Dump, Speicher, Einstellungen), automatisch genommen, wiederverwendet, solange sich nichts geändert hat |
| Nach einem Fehler | die Datenbank, wie der Fehler sie hinterließ (MySQL committet DDL) | die Transaktion zurückgerollt, wo die Engine es kann, dann der Sicherungspunkt wiederhergestellt |
| Nach einem Lauf | nichts | Statusdatei und Laufprotokoll committet, getaggt und gepusht |
| Umgebungen | `--env` wählt die Symfony-Konfiguration | ein Kopf `environments: staging, production` schränkt ein, wo eine Migration läuft |
| Schema-Diff | `doctrine:migrations:diff` erzeugt aus Entitäten | kein Diff-Generator; `exp:migration:generate --skeleton` schreibt eine leere Klasse |
| Engines | MySQL, PostgreSQL, SQLite | MySQL/MariaDB, PostgreSQL, SQLite, Oracle, MongoDB |
| Oberfläche | nur Konsole | Konsole und Setup > Migrationen in der Administration |

## Die Befehle nebeneinander

| Doctrine (DXP) | Exponential 6 |
|---|---|
| `php bin/console doctrine:migrations:status` | `./console exp:migration:status` |
| `doctrine:migrations:migrate --dry-run` | `./console exp:migration:migrate` (ein Probelauf bis `--apply`) |
| `doctrine:migrations:migrate` | `./console exp:migration:migrate --apply` |
| `doctrine:migrations:generate` | `./console exp:migration:generate --skeleton <name> --apply` |
| `doctrine:migrations:execute --down <version>` | `./console exp:migration:rollback <migrations-id> --apply` |
| (keiner) | `./console exp:migration:rollback <run-id> --apply`: der Sicherungspunkt von vor einem Lauf |
| (keiner) | `./console exp:migration:dry-run --simulate --diff`: ausführen und zurückrollen, zeigen, was sich geändert hat |
| (keiner) | `./console exp:migration:check`: die Prüfung für die CI, ohne Datenbank |

Die Seiten der DXP schreiben die Befehle mit dem Alias `doctrine:migration:*`; dort funktionieren beide Schreibweisen.

## Dieselbe Tabelle in beiden

Doctrine:

```php
final class Version20261101100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'A table for news notes';
    }

    public function up( Schema $schema ): void
    {
        $this->addSql( 'CREATE TABLE news_note (id INT NOT NULL, title VARCHAR(150) NOT NULL, PRIMARY KEY(id))' );
    }

    public function down( Schema $schema ): void
    {
        $this->addSql( 'DROP TABLE news_note' );
    }
}
```

Exponential 6: die Tabelle im Format von `share/db_schema.dba`, vom Kernel auf jeder Engine angelegt:

```php
<?php
// description: A table for news notes
namespace Site\Migrations;

final class NewsNote20261101100000 extends \expMigrationPhp
{
    const TABLE = 'news_note';

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
        return array( 'create the table ' . self::TABLE );
    }

    public function detect( $context )
    {
        return \expKernelTableSchema::exists( self::TABLE, $context->db(), true ) ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $messages = array();
        if ( !\expKernelTableSchema::install( array( self::TABLE ), $context->db(), self::schema(), $messages ) )
            throw new \RuntimeException( 'the table could not be created: ' . implode( '; ', $messages ) );
        return array( 'created ' . self::TABLE );
    }

    public function down( $context )
    {
        $context->db()->query( 'DROP TABLE ' . self::TABLE );
        \expKernelTableSchema::reset();
        return array( 'dropped ' . self::TABLE );
    }
}
```

Die Unterschiede im Code: kein SQL je Engine (der Schema-Handler schreibt es), `plan()` statt nur einer
Beschreibung, `detect()`, damit eine Installation, die die Tabelle schon hat, die Migration als erledigt zählt, und
jeder Aufruf geht über `$context->db()`, die Verbindung des Laufs. Kernel-Code bleibt PHP-7.4-kompatibel:
Rückgabetypen sind nicht nötig.

## Wann welcher Stack gilt

| Sie arbeiten an | Nehmen Sie |
|---|---|
| einem DXP-Projekt (Symfony, das Content-Repository) | Doctrine Migrations, wie es das Skeleton der DXP tut |
| einer Exponential-6-Site (der Kernel, `./console`) | `exp:migration:*` |
| einem Projekt, das beides betreibt | das Werkzeug jedes Stacks auf seiner eigenen Datenbank; zeichnen Sie nie die Änderung des einen Stacks in der Tabelle des anderen auf |

[DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md) erklärt die Stacks.
Eine Site von einem Stack auf den anderen umzuziehen, ist ein Umzug der Site, keine Migrationsdatei:
[Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md).

## Wie es weitergeht

- [Umstieg von Doctrine Migrations](../migrationen/rezepte/umstieg-von-doctrine-migrations.md): das Rezept, das eine
  Doctrine-Migration Schritt für Schritt übersetzt
- [Eine Datenbanktabelle anlegen](../migrationen/rezepte/eine-datenbanktabelle-anlegen.md),
  [eine Spalte oder einen Index hinzufügen](../migrationen/rezepte/eine-spalte-oder-einen-index-hinzufuegen.md)
- [Mit Migrationen ausliefern](../migrationen/rezepte/mit-migrationen-ausliefern.md)
- Jetzt beginnen: [das Tutorial](../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](../migrationen/README.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](../migrationen/in-der-administration.md)
- [Die Migrationsbefehle](../migrationen/befehle.md)
- [Die Migrationseinstellungen](../migrationen/einstellungen.md)
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md)
- [DXP-Entwickler: hier beginnen](dxp-entwickler-hier-beginnen.md)
- [Doctrine-Migrationen](doctrine-migrationen.md), [Umstieg von Exponential 6](umstieg-von-exponential-6.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
