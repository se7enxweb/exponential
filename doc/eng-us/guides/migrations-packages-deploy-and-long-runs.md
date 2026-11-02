# Migrations: packages, deploys and long runs

This page is the guide to migrations that run without anybody starting them by hand, and to runs that take long:
the migrations a package ships, which run when it is installed; the run of the kickstarter; the run of a deploy
(`exp:velocity deploy --migrate`); and runs in parts, paced, in the background, and PHP migrations that work through
many items in batches and go on where they stopped.

[Guides](README.md) · [Migrations](migrations.md) · Related: [PHP migrations](migrations-php.md),
[Rollback and captions](migrations-rollback-and-captions.md), [The migrations repository](migrations-repositories.md)

## Migrations in a package

A package ships migrations in a directory `migrations/` at its top, next to its `package.xml` or `package.json`, named
as every migration file is (`YYYYMMDDHHMMSS_name.<type>`). An exported package carries the directory along: the export
copies every directory at the top of the package.

```
mysite_news/
  package.xml
  ezcontentclass/...
  migrations/
    20261101120000_news_folder.yml
    20261101120100_news_index.sql
```

Installing the package (Setup > Packages, or a package the setup wizard or the kickstarter installs item by
item) runs them once its items are in:

```
./console exp:migration:migrate --path=var/storage/packages/<repository>/mysite_news/migrations --apply \
    --include-uncommitted --no-push --format=json
```

in a process of its own, as the user who installs the package (on the console, `[RunSettings] RunAsUser`). Only the
package's migrations run; one that requires a migration not yet applied stops the run with a message naming it. The
run is an ordinary run: it takes the installation lock, its captions and its record, and its caches are cleared as
after any run.

A migration that fails leaves the package **not installed**: the caption taken before the run is restored, and
installing the package again runs what is still pending. A package that is installed again later finds its applied
migrations done and runs nothing.

The migrations of every imported package are found by every run as well (the source `package:<name>`), so
`./console exp:migration:migrate --apply` applies those a package install did not run.

| Setting (`migration.ini [InstallSettings]`) | Default | What it does |
|---|---|---|
| `RunOnPackageInstall` | `enabled` | run a package's migrations when it is installed; `disabled`: leave them to the next run |
| `RunOnKickstart` | `enabled` | the kickstarter runs every pending migration after the sites are created |
| `RunOptions[]` | `include-uncommitted`, `no-push` | the switches of these runs: `no-caption`, `no-restore`, `no-commit`, `no-push`, `no-tag`, `include-uncommitted` |
| `Timeout` | `3600` | seconds a run at installation may take |

`include-uncommitted` is on by default because installing writes settings, design and extension files that a run would
otherwise refuse as changes that are not committed (they are kept in the caption).

## The kickstarter

`php bin/php/kickstarter.php run --apply` runs `./console exp:migration:migrate --apply` once the sites are created and
the help manuals are queued, while maintenance mode is still on. It prints `Migrations: 3 migrations applied (run
<run id>)`, or `Migrations: FAIL ...` with the command that goes on once the cause is fixed:

```
./console exp:migration:status
./console exp:migration:resume --apply
```

A failed run ends the kickstarter with exit 1; the site is installed and the caption taken before the run is restored.

## In a deploy

`exp:velocity deploy --migrate` runs the pending migrations as a step of the deploy: after the autoloads and the
caches of settings and templates (so the new classes and settings are found), and before the engine archive, PHP-FPM
and Velocity run the new code. A migration that fails stops the deploy there, and nothing after the step is done.

```
./console exp:velocity deploy --migrate --dry-run --allow-root-user
./console exp:velocity deploy --migrate --allow-root-user
```

The step is `./console exp:migration:migrate --apply`, with its captions, record, commit and push as on the console.
`velocity.ini [DeploySettings] Migrate=enabled` makes every deploy run it; the default is `disabled`, and
`--no-migrate` skips it for one deploy. The caches a run's steps touched are cleared by the run itself; on another
server, `exp:velocity deploy --caches=<list>` clears the same list (the report of the run names it).

## Long runs

A run of many migrations, or of one that changes many objects, can be split and paced. Every setting below is in
`migration.ini [RunSettings]`, and the option of the same name wins for one run (`exp:migration:migrate`,
`exp:migration:resume`, and the runs started in Setup > Migrations).

| Option | Setting | What it does |
|---|---|---|
| `--max-migrations=<n>` | `MaxMigrations` | apply at most n migrations; the rest are **postponed** |
| `--max-time=<seconds>` | `MaxTime` | start no further migration after this time (the one running is finished); the rest are postponed |
| `--throttle=<seconds>` | `Throttle` | a pause between two migrations, and between the batches of a PHP migration (`0.5` is half a second, at most 60) |
| `--nice=<n>` | `Nice` | a lower priority for the run, 1 to 19 as `nice(1)` (the console and background runs, never a web request) |

A postponed migration stays pending: the run ends with exit 0 and the warning `3 migrations postponed by the limit of
20 migrations per run: the next run (./console exp:migration:migrate --apply) goes on with them`. A nightly cron job
that applies what fits into a window:

```
30 1 * * * cd /path/to/site && ./console exp:migration:migrate --apply --max-time=3000 --throttle=1 --nice=10 --format=json >> var/log/migrations.log
```

The first migration of a run always starts, however long the time is up, so every run makes progress.

### PHP migrations in batches

A PHP migration that changes thousands of objects does it a batch at a time with `batches()`, each batch committed on
its own. After every batch it keeps how many items it handled in its **checkpoint**
(`var/<site>/migration/checkpoints/<migration id>.json`); a run that fails, is stopped or times out goes on from
there the next time instead of doing the first batches again. The runner removes the checkpoint once the migration is
applied.

```php
final class ReindexNewsTeasers extends \expMigrationPhp
{
    public function transactional()
    {
        return false; // batches() commits every batch on its own
    }

    public function up( $context )
    {
        $db = $context->db();
        $count = $this->batches( $context,
            function ( $offset, $limit ) use ( $db ) {
                return $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE contentclass_id = 42 ORDER BY id",
                                        array( 'offset' => $offset, 'limit' => $limit ) );
            },
            function ( $row ) {
                // one object: change it
            }, 200 );
        return array( "updated $count objects" );
    }
}
```

`$fetch` gets the offset (the items handled in every run so far) and the batch size and returns the next items in a
stable order; fewer than the batch size ends the work. A handler that removes the items it handles asks for what is
left instead and ignores the offset. Between batches `batches()` pauses as long as `--throttle`. A dry run handles
nothing. A migration whose `transactional()` is true cannot use it: the batches are its transactions.

The checkpoint itself is open to a migration as `$this->checkpoint( $context )` (`get()`, `set()`, `values()`,
`clear()`), for a cursor of its own.

### In the background

A run started in Setup > Migrations always runs in the background, as a console process of its own, and its page
follows it ([In the administration](migrations.md#in-the-administration)). With expqueue active, the run can go to
its queue instead:

```
; settings/override/migration.ini.append.php
[AdminSettings]
Background=queue

; settings/override/expqueue.ini.append.php
[JobType_migration.run]
Handler=expMigrationQueueHandler
Class=bulk
Timeout=86400
Description=A migration run started in Setup > Migrations
```

The queue's worker then runs the same command line, and the run's page follows it the same way; until the worker
takes it, the run counts as running, so a second one is refused. When expqueue is not active, its tables are not
installed, the job type is missing or no worker runs, the run starts as a process as before. A run that fails fails
the queue job for good: a migration run is never repeated by itself.

## Not in this part

Several migrations at once in parallel workers, the steps that change code and design files, and a site-wide
recording window come with a later release.

## Related pages

- [Migrations](migrations.md), [PHP migrations](migrations-php.md), [Rollback and captions](migrations-rollback-and-captions.md)
- [exp:migration:migrate](../examples/commands/expmigrationmigrate.md), [exp:migration:resume](../examples/commands/expmigrationresume.md)
- [migration.ini](../examples/ini-settings/migration/README.md)
