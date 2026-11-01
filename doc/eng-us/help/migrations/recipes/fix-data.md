---
title: "Fix data"
audience: developer, software-engineer, php-developer, admin, dba
category: migrations
order: 6
summary: "A migration that corrects wrong rows - names with stray spaces, addresses over http - with a pure function that the plan, the run and a test share, escaped SQL through the run's connection, and the old values in the run log."
keywords: migration, migrations, fix data, data correction, data cleanup, update rows, wrong data, escapeString, run log, dry run, plan, idempotent, exp:migration:migrate, exp:migration:dry-run, database change
type: help
---
# Fix data

This recipe is for developers and database administrators who find wrong data on an installation and must correct
it on every installation of the site: values an import mangled, a bug that wrote bad rows for a week. A fix written
as a migration is reviewed like code, shown row by row in the dry run before anything changes, recorded when it
ran, and taken back with the caption if it was wrong. The example corrects the table `exp_news_source`: names
lose their stray spaces, `http://` addresses become `https://`.

## What you'll have at the end

- the migration `20261101140000_fix_news_source_rows.php`;
- a dry run that lists every row it would correct, with the old and the new value;
- the corrected rows, the old values in the run log, and a second run that finds nothing to do.

## Before you start

- Exponential 6.0.15 with `6.0.15-migration-tables` done; the table `exp_news_source`
  ([Add a database table](add-a-database-table.md)).
- A copy of the data that has the problem, so the dry run shows real rows.
- Run as the web server user (as root, add `--allow-root-user`).

## Steps

### 1. Generate and fill in

```bash
./console exp:migration:generate --skeleton fix_news_source_rows --description="News sources: names without surrounding spaces, addresses over https" --apply
```

Add `// requires: 20261101090000_news_source_table` and the methods below.

### 2. Read the dry run carefully

The dry run is the review of a data fix: every line is a row that will change.

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

On PostgreSQL and SQLite, `./console exp:migration:dry-run --simulate --diff` runs the fix in a transaction that is
rolled back and reports the rows that changed.

### 3. Run it

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

The run log keeps the old values:

```text
2026-11-01T21:04:02Z info source 3 was " City  desk" http://example.org/city
2026-11-01T21:04:02Z info source 7 was "Sports" http://example.org/sports
```

The run log is cleaned of secrets and e-mail addresses before it is written and is committed to the migrations
repository. Never log personal data from a fix of user records; log ids only.

## The migration

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

- **One pure function decides.** `fixed()` takes a row and returns the corrected row or `null`. The plan, the run
  and a unit test all use it, so the dry run shows exactly what the run does.
- **Escape every value.** `$db->escapeString()` for strings, `(int)` for numbers; never put a value into SQL
  unescaped, even one read from your own table.
- **Through `$context->db()`.** On PostgreSQL and SQLite the whole fix is one transaction: a failure in row 7 leaves
  row 3 untouched too. On MySQL/MariaDB and Oracle the data changes are transactional as well; only schema changes
  are not.
- **No `down()`.** A data fix has no meaningful reverse; the old values are in the log and in the caption.

A unit test of the decision needs no database:

```php
$this->assertSame( array( 'id' => 3, 'name' => 'City desk', 'url' => 'https://example.org/city' ),
    \Site\Migrations\FixNewsSourceRows20261101140000::fixed( array( 'id' => 3, 'name' => ' City  desk', 'url' => 'http://example.org/city' ) ) );
$this->assertNull( \Site\Migrations\FixNewsSourceRows20261101140000::fixed( array( 'id' => 1, 'name' => 'Newsroom', 'url' => '' ) ) );
```

## Check it

- `./console exp:migration:status`: the migration is `done`.
- Query the table: no name starts or ends with a space, no address starts with `http://`.
- If the bug that wrote the bad rows is not fixed yet, new bad rows appear; the migration does not run again. Fix the
  code first, or write a second migration after the code fix.

## Roll it back

```bash
./console exp:migration:rollback 20261101-140402-5f13aa --apply
```

restores the caption taken before the run. When the fix was right for most rows and wrong for one, correct that row
in a new migration instead of restoring the whole installation.

## Next step

Fix settings rather than rows: [Move or rename INI settings](move-or-rename-ini-settings.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Change content in bulk](change-content-in-bulk.md), [Testing migrations](testing-migrations.md), [Reviewing a migration](reviewing-a-migration.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
