---
title: "Migrationen testen: Prüfung, Simulation, Diff und CI"
audience: developer, software-engineer, php-developer
category: migrations
order: 13
summary: Vier Ebenen von Tests für Migrationen - die Prüfung ohne Datenbank, der Probelauf, die Simulation mit Diff auf PostgreSQL oder SQLite und ein PHPUnit-Test auf einer Wegwerf-SQLite-Datenbank - und wie man sie in der CI ausführt.
keywords: Migration, Migrationen, Test, Testen, Probelauf, Simulation, Prüfung, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, test, testing, PHPUnit, CI, continuous integration, simulate, simulation, diff, dry run, lint, SQLite, schema change, database change, deploy, exp:migration:check, exp:migration:dry-run, --simulate, --diff, --fail-on-pending
featured: true
type: help
---
# Migrationen testen: Prüfung, Simulation, Diff und CI

Eine Migration läuft einmal je Installation und ist schwer zurückzunehmen, sobald die Produktion sie hat. Testen
Sie sie, bevor sie dort ankommt. Dieses Rezept zeigt vier Ebenen, von der günstigsten bis zur gründlichsten, und wie
Sie sie in die CI einbinden.

## Was Sie am Ende haben

- `exp:migration:check` in jedem Pull Request, ohne Datenbank;
- einen Probelauf und eine Simulation mit Diff auf einer Entwicklungskopie;
- einen PHPUnit-Test, der die Migrationen der Site auf einer Wegwerf-SQLite-Datenbank hin, zurück und wieder hin
  ausführt.

## Bevor Sie beginnen

- Die Migrationen der Rezepte [Tabelle](eine-datenbanktabelle-anlegen.md) und
  [umkehrbares down()](ein-umkehrbares-down-schreiben.md), oder Ihre eigenen.
- Für die Simulation: eine Entwicklungsinstallation auf SQLite oder PostgreSQL. MySQL/MariaDB, Oracle und MongoDB
  können nicht simulieren, weil eine Schemaänderung dort sofort committet wird.

## Schritte

### 1. Die Prüfung: exp:migration:check

```bash
./console exp:migration:check
./console exp:migration:check --path=migrations/20261101110000_news_note_priority.php
```

```text
Exponential migrations: check
  site                     migrations
  extension:expnews        extension/expnews/migrations

  ok    20261101090000_news_note_table               php  migrations/20261101090000_news_note_table.php
  ok    20261101100000_news_source_table             php  extension/expnews/migrations/20261101100000_news_source_table.php
  ok    20261101110000_news_note_priority            php  migrations/20261101110000_news_note_priority.php

3 migration(s) checked
PASS
```

Sie braucht keine Datenbank und führt nichts aus: Dateinamen, Kopfschlüssel, die Reihenfolge für jede Umgebung,
Anforderungen, Zyklen, doppelte IDs, die PHP-Syntax und dass die Datei genau eine Klasse deklariert, die
`\expMigrationPhp` erweitert. Jedes Problem ergibt Exit 1.

### 2. Der Probelauf

```bash
./console exp:migration:migrate
```

listet jede ausstehende Migration mit ihrem `plan()`. Er warnt auch, wenn `settings/`, `extension/` oder `design/`
nicht committete Änderungen haben, die ein Lauf mit `--apply` ablehnen würde.

### 3. Die Simulation mit Diff

```bash
./console exp:migration:dry-run --simulate --diff
```

```text
Exponential migrations: simulation, the database is rolled back afterwards
Run 20261101-113402-5d0b7e, environment development, database sqlite (transactions: full), as admin

  simulated   20261101110000_news_note_priority            site               News notes get a priority (default 0)
              would add the column exp_news_note.priority (integer, default 0)
              simulation passed in 4 ms
              added exp_news_note.priority
              ~ table exp_news_note: columns +priority

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 simulated
WARN simulated in a transaction that was rolled back: the database is unchanged, but what the migrations wrote outside it (files, caches, the search index, mail) is not undone
PASS
```

Die Migrationen laufen wirklich, in einer Transaktion, die zurückgerollt wird. Lesen Sie den Diff: Eine Tabelle,
deren Änderung Sie nicht erwartet haben, ist ein Fehler. Beachten Sie die Warnung: Simulieren Sie auf einer
Entwicklungskopie, nie in der Produktion.

### 4. Ein PHPUnit-Test auf einer Wegwerf-SQLite-Datenbank

Der Kernel testet seinen Runner genauso (`tests/tests/kernel/classes/migration/expMigrationRunnerSqliteTest.php`):
das Kernel-Schema und die sauberen Daten in eine neue SQLite-Datei laden, die Migrationen der Site finden und laden,
sie ausführen.

```php
<?php
/**
 * The site's migrations on a throwaway SQLite database: forward, back and forward again.
 * Run: php vendor/bin/phpunit tests/site/SiteMigrationsTest.php
 */
class SiteMigrationsTest extends PHPUnit\Framework\TestCase
{
    /** @var eZSQLite3DB */
    private $db;

    /** @var string */
    private $file;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->file = sys_get_temp_dir() . '/site-migrations-' . getmypid() . '.sqlite';
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->file,
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( 'kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        $this->db->close();
        if ( is_file( $this->file ) )
            unlink( $this->file );
    }

    private function migrations()
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => 'migrations' ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $migrations;
    }

    private function context( $apply = true )
    {
        return new expMigrationContext( $this->db, $apply, array( 'environment' => 'testing', 'no-mirror' => true,
                                                                  'user' => eZUser::fetchByName( 'admin' ) ) );
    }

    public function testUpDownUp()
    {
        $report = ( new expMigrationRunner( $this->context() ) )->run( $this->migrations() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertTrue( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );

        foreach ( array_reverse( $this->migrations() ) as $migration )
            if ( $migration->reversible() )
                $migration->down( $this->context() );
        $this->assertFalse( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );
    }

    public function testPlanWritesNothing()
    {
        foreach ( $this->migrations() as $migration )
            $this->assertNotEmpty( $migration->plan( $this->context( false ) ) );
        $this->assertNull( expUpgradeCatalog::hasColumn( $this->db, 'exp_news_note', 'priority' ) );
    }
}
```

`'no-mirror' => true` verhindert, dass der Test eine Statusdatei ins Repository schreibt. Führen Sie ihn im
Wurzelverzeichnis der Installation aus. Halten Sie `plan()` rein, damit es sich ganz ohne Datenbank testen lässt.

### 5. In der CI

| Job | Befehl | Braucht |
|---|---|---|
| jeder Pull Request | `./console exp:migration:check` | PHP und den Code, keine Datenbank |
| jeder Pull Request | `php vendor/bin/phpunit tests/site/SiteMigrationsTest.php` | die Erweiterung SQLite3 |
| vor einer Auslieferung | `./console exp:migration:status --fail-on-pending` auf dem Ziel | die Datenbank des Ziels: Exit 1 sagt der Pipeline, dass Migrationen warten |

Eine vollständige Workflow-Datei steht in [Mit Migrationen ausliefern](mit-migrationen-ausliefern.md).

## Prüfen

Alle vier Ebenen bestehen, und der PHPUnit-Test scheitert, wenn Sie `down()` absichtlich kaputt machen (das
`DROP COLUMN` entfernen).

## Nächster Schritt

Lassen Sie ein zweites Paar Augen draufschauen: [Eine Migration im Code-Review prüfen](eine-migration-pruefen-im-code-review.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [exp:migration:check](../../../beispiele/befehle/expmigrationcheck.md), [exp:migration:dry-run](../../../beispiele/befehle/expmigrationdryrun.md),
  [Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
