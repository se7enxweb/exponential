---
title: "Umstieg von Doctrine Migrations"
audience: developer, dxp-developer, php-developer, software-engineer
category: migrations
order: 23
summary: Für Entwickler, die Doctrine Migrations aus Symfony-Projekten kennen - jeder Doctrine-Befehl und jede Klasse mit ihrem Gegenstück in Exponential 6, was Exponential hinzufügt (Sicherungspunkte, Commit, Tag und Push, Umgebungen, requires, die Prüfsummen-Sperre, die Administration) und was es nicht hat.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Umstieg, Begriffe im Vergleich, migration, migrations, schema change, database change, Doctrine, Doctrine Migrations, doctrine:migrations:migrate, doctrine:migrations:generate, doctrine:migrations:status, AbstractMigration, addSql, Symfony, DXP, concept map
featured: true
type: help
---
# Umstieg von Doctrine Migrations

Wenn Sie Doctrine Migrations in einem Symfony-Projekt geschrieben haben, werden Ihnen die Migrationen von Exponential 6
vertraut vorkommen: versionierte PHP-Klassen, `up()` und `down()`, eine Tabelle, die festhält, was lief, ein Probelauf.
Die Unterschiede liegen in dem, was um einen Lauf herum geschieht. Diese Seite ordnet beides einander zu und schreibt
eine typische Doctrine-Migration um.

Die Exponential Platform DXP behält Doctrine: in einem DXP-Projekt arbeiten Sie weiter mit
`doctrine:migrations:migrate` ([Doctrine-Migrationen](../../dxp-entwickler/doctrine-migrationen.md)). Diese Seite gilt
für Installationen von Exponential 6.

## Was Sie am Ende haben

- den Befehl und die Klasse von Exponential für jeden Doctrine-Befehl, den Sie nutzen;
- eine Doctrine-Migration als PHP-Migration von Exponential;
- eine Liste dessen, was es nicht gibt, damit Sie nicht danach suchen.

## Bevor Sie beginnen

Arbeiten Sie [das Tutorial](../../../anleitungen/migrationen-tutorial.md) einmal durch; es dauert etwa 20 Minuten.

## Begriffe im Vergleich

| Doctrine Migrations | Exponential 6 |
|---|---|
| `doctrine:migrations:generate` | `./console exp:migration:generate --skeleton <name> --apply` |
| `migrations/VersionYYYYMMDDHHMMSS.php` | `migrations/YYYYMMDDHHMMSS_<name>.php`; die ID ist der Dateiname |
| `final class Version... extends AbstractMigration` | eine Klasse in einem Namensraum, die `\expMigrationPhp` erweitert (`Site\Migrations\<Name><Zeit>`) |
| `getDescription()` | die Kopfzeile `// description: ...` |
| `up( Schema $schema )` mit `$this->addSql()` | `up( $context )`: die Änderung über `$context->db()` ausführen und zurückgeben, was getan wurde, eine Zeile je Schritt |
| `down( Schema $schema )` | `down( $context )`; weglassen, wenn sich die Änderung nicht zurücknehmen lässt |
| `throwIrreversibleMigrationException()` | kein `down()`: die Rücknahme dieser Migration wird abgelehnt und verweist auf den Sicherungspunkt |
| `isTransactional()` | `transactional()` |
| `skipIf()` / `abortIf()` | `detect()` liefert `NOT_NEEDED` oder `DONE`; zum Abbrechen eine Ausnahme werfen |
| Tabelle `doctrine_migration_versions` | Tabellen `exp_migration` (eine Zeile je Migration) und `exp_migration_run` (eine Zeile je Lauf) |
| `doctrine:migrations:status` | `./console exp:migration:status` |
| `doctrine:migrations:migrate --dry-run` | `./console exp:migration:migrate` (ein Probelauf ist voreingestellt) |
| `doctrine:migrations:migrate` | `./console exp:migration:migrate --apply` |
| `doctrine:migrations:execute <v> --up` | `./console exp:migration:migrate --only=<id> --apply` |
| `doctrine:migrations:execute <v> --down` | `./console exp:migration:rollback <id> --apply` |
| `doctrine:migrations:migrate prev` | `./console exp:migration:rollback <run-id> --apply` stellt den Sicherungspunkt von vor dem Lauf wieder her |
| `doctrine:migrations:version --add` / `--delete` | kein Gegenstück; eine Migration wird aufgezeichnet, indem sie läuft, und eine geänderte Datei wird mit `--force --reason=<text>` akzeptiert |
| `doctrine:migrations:diff` | kein Schema-Diff-Generator in 6.0.15; schreiben Sie die Änderung und sehen Sie sie mit `exp:migration:dry-run --simulate --diff` |
| `doctrine:migrations:list` | `./console exp:migration:status` |
| Abhängigkeiten nur über die Reihenfolge | `// requires: <id>` (auch `extension:<name>` oder die ID eines Upgrade-Schritts) |

## Was Exponential um einen Lauf herum hinzufügt

- **Sicherungspunkte**: einer (Datenbank, `var/`-Speicher, `settings/override`) vor jedem Lauf, voreingestellt
  wiederhergestellt, wenn eine Migration scheitert, und einer nach einem erfolgreichen Lauf.
- **Commit, Tag und Push**: Statusdatei und Laufprotokoll jedes Laufs werden committet, als
  `migrations/<umgebung>/<run-id>` getaggt und gepusht.
- **Umgebungen und Engines**: `// environments: staging, production` und `// engines: mysql, postgresql` im Kopf.
- **Die Prüfsummen-Sperre**: eine Migration, deren Datei sich nach dem Lauf geändert hat, wird abgelehnt.
- **Sperre und Benutzer**: ein Lauf zur Zeit je Installation (geteilt mit `exp:upgrade`), unter einem benannten
  Benutzer.
- **Die Administration**: Setup > Migrationen zeigt Migrationen, führt sie aus und nimmt sie zurück, mit
  „Identität bestätigen“.

## Schritte

### 1. Eine Doctrine-Migration

```php
final class Version20261101090000 extends AbstractMigration
{
    public function getDescription(): string { return 'The column priority of news_note'; }
    public function up( Schema $schema ): void
    {
        $this->addSql( 'ALTER TABLE exp_news_note ADD priority INT DEFAULT 0 NOT NULL' );
        $this->addSql( 'UPDATE exp_news_note SET priority = 1 WHERE title LIKE \'Urgent%\'' );
    }
    public function down( Schema $schema ): void
    {
        $this->addSql( 'ALTER TABLE exp_news_note DROP COLUMN priority' );
    }
}
```

### 2. Dieselbe als Migration von Exponential

```bash
./console exp:migration:generate --skeleton news_note_priority --description="The column priority of exp_news_note" --apply
```

```php
<?php
// description: The column priority of exp_news_note
// engines: mysql, postgresql, sqlite, oracle
namespace Site\Migrations;

final class NewsNotePriority20261101090000 extends \expMigrationPhp
{
    public function plan( $context )
    {
        return array( 'add the column exp_news_note.priority', 'set priority 1 on the urgent notes' );
    }

    public function up( $context )
    {
        $db = $context->db();
        if ( \expUpgradeCatalog::hasColumn( $db, 'exp_news_note', 'priority' ) !== true )
            \expUpgradeCatalog::execute( $db, "ALTER TABLE exp_news_note ADD COLUMN priority INTEGER DEFAULT 0 NOT NULL;\n" );
        $db->query( "UPDATE exp_news_note SET priority = 1 WHERE title LIKE 'Urgent%'" );
        return array( 'added the column exp_news_note.priority', 'set priority 1 on the urgent notes' );
    }

    public function down( $context )
    {
        \expUpgradeCatalog::execute( $context->db(), "ALTER TABLE exp_news_note DROP COLUMN priority;\n" );
        return array( 'dropped the column exp_news_note.priority' );
    }
}
```

`plan()` ist das, was der Probelauf ausgibt, anstelle der SQL-Liste von Doctrine. Für SQL, das der Schema-Handler je
Engine schreibt, siehe [Migrationen auf MySQL, PostgreSQL, SQLite, Oracle und MongoDB](hinweise-zu-den-datenbanken.md).

### 3. Ausführen

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:migrate --apply
```

## Gewohnheiten, die Sie ablegen

- Ändern Sie eine Migration nicht, nachdem sie irgendwo gelaufen ist; die Prüfsummen-Sperre lehnt sie ab. Schreiben
  Sie eine neue.
- Markieren Sie Versionen nicht von Hand; es gibt kein `version --add`.
- Verlassen Sie sich nicht allein auf die Reihenfolge der IDs, wenn eine Migration eine andere braucht; schreiben Sie
  `requires:`.

## Prüfen

`./console exp:migration:status` zeigt die Migration als `done`, der Verlauf den Lauf mit seinen Sicherungspunkten und
seinem Commit.

## Nächster Schritt

[Migrationen testen](migrationen-testen.md), danach [Mit Migrationen ausliefern](mit-migrationen-ausliefern.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Exponential-Migrationen für Doctrine-Nutzer](../../dxp-entwickler/exponential-migrationen-fuer-doctrine-nutzer.md),
  [Doctrine-Migrationen in der DXP](../../dxp-entwickler/doctrine-migrationen.md)
- [Umstieg vom Kaliop-Migrationsbundle](umstieg-vom-kaliop-ezmigrationbundle.md),
  [Umstieg von SQL-Update-Dateien](umstieg-von-sql-update-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
