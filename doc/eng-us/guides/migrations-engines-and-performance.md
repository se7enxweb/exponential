# Migrations: engines and performance

This page says on which database engines every part of the migration engine is tested, how the tests are run in a
project and in CI, and what a run costs: the time and memory of 1,000 migrations, of a large data step and of a long
run with checkpoints, measured on each engine, with how to measure your own.

[Guides](README.md) · [Migrations](migrations.md) · Related: [SQL and settings](migrations-sql-and-settings.md),
[Packages, deploys and long runs](migrations-packages-deploy-and-long-runs.md),
[Rollback and captions](migrations-rollback-and-captions.md)

## The engine matrix

Every family of the migration engine has one set of checks (a PHP trait,
`tests/tests/kernel/classes/migration/expMigration<Family>EngineChecks.php`) that runs unchanged on every engine,
through three test classes: `expMigration<Family>SqliteTest` (SQLite in process, in the normal test suites),
`expMigration<Family>DatabaseTest` (MySQL/MariaDB and PostgreSQL, group `database`) and
`expMigration<Family>MongoDBTest` (MongoDB, testsuite `mongodb-live`). Each class works on a database of its own with
the kernel schema and the clean data and removes it afterwards; no installation's database is opened.

| Family | What its checks cover |
|---|---|
| Runner | PHP migrations in order, the record and the run log, transactions per engine, rollback with `down()`, batches with a checkpoint |
| Record | The tables `exp_migration` and `exp_migration_run`, the state of each migration, runs |
| Caption | A caption before a run, its reuse, the automatic restore after a failure (`mysqldump`, `pg_dump`, the SQLite file, `mongodump`) |
| Command | The `exp:migration:*` commands end to end: status, migrate, dry run, rollback, resume |
| UpgradeSource | The upgrade steps of `exp:upgrade` as migrations on the same record |
| Yaml | YAML migrations: header, steps, references, conditions, the down part |
| ContentSteps | The content and location steps in several languages |
| StructureSteps | Classes, users, roles, sections, languages and object states |
| TagAliasTrashFileSteps | Tags, URL aliases, the trash, files and images |
| CacheSearchAutoloadSteps | The caches, the search index and the autoloads a run touched |
| LayoutHttpMailCommandSteps | Layouts, HTTP requests, mails and allowlisted console commands |
| SqlSettingsSteps | The schema and data DSL, per-engine SQL files, `.mongodb.json`, INI migrations |
| Recording | Changes recorded in the administration and written as the steps of a migration |
| Graphql | The step type `graphql`: a persisted query and a mutation as the run's user, tagged events, a failed migration |
| Performance | The measurements below (PF-1 to PF-3) |

The result of the matrix on 2026-11-02, every cell the number of tests that passed with none failed or skipped ("n/a":
the family has no class for that engine; the record's SQLite checks run without a database in `expMigrationRecordTest`).
PostgreSQL 13 ran here because its `pg_dump` must not be older than the server; CI runs PostgreSQL 15 with a matching
one. MongoDB needs a higher open-files limit than Docker's default: with the default, `mongod` aborted while `mongorestore`
rebuilt the indexes of a caption (`--ulimit nofile=64000:64000`).

| Family | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 |
|---|---|---|---|---|
| CacheSearchAutoloadSteps | pass 4 | pass 4 | pass 4 | pass 4 |
| Caption | pass 2 | pass 2 | pass 2 | pass 2 |
| Command | pass 3 | pass 3 | pass 3 | pass 3 |
| ContentSteps | pass 3 | pass 3 | pass 3 | pass 3 |
| Graphql | pass 2 | pass 2 | pass 2 | pass 2 |
| LayoutHttpMailCommandSteps | pass 2 | pass 2 | pass 2 | pass 2 |
| Performance | pass 3 | pass 3 | pass 3 | pass 3 |
| Record | n/a | pass 1 | pass 1 | pass 1 |
| Recording | pass 3 | pass 3 | pass 3 | pass 3 |
| Runner | pass 3 | pass 3 | pass 3 | pass 3 |
| SqlSettingsSteps | pass 3 | pass 3 | pass 3 | pass 3 |
| StructureSteps | pass 3 | pass 3 | pass 3 | pass 3 |
| TagAliasTrashFileSteps | pass 4 | pass 4 | pass 4 | pass 4 |
| UpgradeSource | pass 2 | pass 2 | pass 2 | pass 2 |
| Yaml | pass 2 | pass 2 | pass 2 | pass 2 |

What differs between engines is intended and checked as such: PostgreSQL and SQLite undo a failed migration
completely, schema changes included; MySQL/MariaDB undo the rows but keep a table a failed migration created (a
schema change commits at once); MongoDB has no transactions, so what a failed migration or batch wrote stays, and
the restore after a failure comes from the caption. Oracle is not part of the matrix: its driver is the extension ezoracle,
which the kernel's test toolkit does not load, so Oracle is checked by hand. The engine treats it like
MySQL/MariaDB for transactions (rows undone, schema changes kept) and takes no automatic restore there: after a
failure the run prints the exact steps to restore the database by hand
([Rollback and captions](migrations-rollback-and-captions.md)).

### Running the matrix

SQLite runs with every test run. For the other engines, give the tests a server; the database names follow the test
naming rule (they contain `test`):

```
# MySQL/MariaDB or PostgreSQL: every migration family, a fresh database per test
php tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test --group database tests/tests/kernel/classes/migration
php tests/runtests.php --dsn "postgresql://postgres@127.0.0.1/testdb" --db-per-test --group database tests/tests/kernel/classes/migration

# MongoDB: each class makes and drops a database exp_test_migration_*
EXP_TEST_MONGO_URI="mongodb://127.0.0.1:27017" vendor/bin/phpunit --testsuite mongodb-live --group mongodb-live --filter '^expMigration'
```

YAML migrations need `symfony/yaml` and the GraphQL family `webonyx/graphql-php` (both in the default
`composer.json`); without them those checks are skipped, on CI they fail. The caption checks need the engine's dump
tool on `PATH`.

### In CI

The workflow `phpunit.yml` runs the SQLite classes in the PHPUnit job, and its database job runs the
`database` group on MySQL 8.0 and PostgreSQL 15 and the `expMigration*` classes of `mongodb-live` on MongoDB 7, each
as a service of the job. A project with migrations of its own wires its CI the same way: `exp:migration:check` on its
files, then `exp:migration:migrate --dry-run` against a throwaway database of each engine it supports.

## Performance

Three checks of the Performance family measure what the engine costs, the same way on every engine. Each asserts what
the run did, keeps a generous upper bound on its time, and writes its measurements as one JSON line to the file
named by `EXP_MIGRATION_PERF_REPORT`:

- **PF-1, 1,000 migrations:** 1,000 YAML migrations of one data step each (every tenth requires the one before):
  discovery, ordering and checksums; loading; the run that applies all of them (a record row and a run log entry
  each); a second run with nothing to do; the status of each.
- **PF-2, a large data step:** one migration inserts 5,000 rows, a second updates all of them, a third deletes them.
- **PF-3, a long run with checkpoints:** a PHP migration sets a value on 5,000 rows in batches of 250 with a
  checkpoint after each batch, is stopped by a failure at row 2,600, and goes on from the last checkpoint in a second
  run.

The numbers of 2026-11-02, PHP 8.5, on a shared development server that was under heavy load from other work at the
time (each engine in a Docker container on the same machine). Read them for their order of magnitude and the
relation between engines, not as a benchmark:

**PF-1, 1,000 migrations**

| Measurement | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 |
|---|---:|---:|---:|---:|
| Discovery, ordering, checksums (s) | 0.041 | 0.067 | 0.065 | 0.092 |
| Loading (s) | 0.48 | 0.32 | 0.38 | 0.29 |
| Run, 1,000 applied (s) | 3.9 | 38.8 | 17.0 | 13.8 |
| per migration (ms) | 3.9 | 38.8 | 17.0 | 13.8 |
| Second run, nothing to do (s) | 0.028 | 0.031 | 0.030 | 0.061 |
| Status of each, one read each (s) | 0.5 | 7.6 | 4.0 | 5.6 |
| Memory (MiB) | 11.3 | 8.2 | 12.0 | 12.3 |

**PF-2, a large data step**

| Measurement | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 |
|---|---:|---:|---:|---:|
| Insert 5,000 rows (s) | 0.9 | 71.7 | 14.3 | 10.4 |
| rows per second | 5,432 | 70 | 351 | 479 |
| Update all (s) | 0.23 | 0.16 | 0.10 | 0.22 |
| Delete all (s) | 0.07 | 0.18 | 0.13 | 0.17 |
| Memory (MiB) | 2.2 | 2.2 | 2.2 | 2.2 |

**PF-3, 5,000 rows in batches of 250 with checkpoints**

| Measurement | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 |
|---|---:|---:|---:|---:|
| Checkpoint at the failure | 2,500 | 2,500 | 2,500 | 2,500 |
| First run, stopped (s) | 0.04 | 2.85 | 2.06 | 6.09 |
| Second run, resumed (s) | 0.06 | 3.00 | 3.01 | 7.22 |
| rows per second | 42,808 | 833 | 829 | 346 |
| Memory (MiB) | 0.8 | 0.8 | 0.8 | 0.8 |

What they say:

- **The engine's own overhead is small.** Discovery, ordering and checksums of 1,000 files take 0.04 to 0.09 s,
  well inside the target of under 1 s (also checked without a database by `expMigrationLocatorTest`, ML-07). A second
  run of 1,000 migrations that has nothing to do costs 0.03 to 0.06 s. Reading the record of each of the 1,000 one by
  one (one query each, as PF-1 does to check them) takes 0.5 to 7.6 s; `exp:migration:status` reads it in one query.
- **A run costs milliseconds per migration plus what the migration does.** Applying 1,000 migrations that do almost
  nothing took 4 s on SQLite, 14 s on MongoDB, 17 s on PostgreSQL and 39 s on MySQL (4 to 39 ms per migration): per
  migration the runner opens and commits a transaction and writes the record row and the run log entry, and each
  commit waits for the disk. A site with a hundred pending migrations is done in a few seconds.
- **Large data steps are bound by the database, not the engine.** PF-2's insert is a migration with
  `transactional: no`, so every row is a commit of its own: 70 rows per second on the loaded MySQL server, 5,400 on
  SQLite. Updating and deleting all rows are single statements and take a fraction of a second everywhere. Keep a large
  insert in the migration's transaction (the default), or write a PHP migration with `batches()`, which commits a batch
  at a time (PF-3: 350 to 830 rows per second on the servers, each row a statement of its own).
- **Long runs resume where they stopped.** PF-3 stops at row 2,600 and the checkpoint holds 2,500, the last batch
  that was committed; the second run handles only the rest. The checkpoint is written after each batch, so the cost
  of an interruption is at most one batch.
- **Memory stays flat.** None of the checks needed more than 12.3 MiB over what was in use before it; 1,000 loaded
  migrations are the largest part.

### Measuring your own

```
EXP_MIGRATION_PERF_REPORT=var/tmp/perf.jsonl vendor/bin/phpunit tests/tests/kernel/classes/migration/expMigrationPerformanceSqliteTest.php
EXP_MIGRATION_PERF_REPORT=var/tmp/perf.jsonl php tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test \
    --group database tests/tests/kernel/classes/migration/expMigrationPerformanceDatabaseTest.php
```

`EXP_MIGRATION_PERF_COUNT` changes the number of migrations of PF-1 (default 1000), `EXP_MIGRATION_PERF_ROWS` the rows
of PF-2 and PF-3 (default 5000). Each line of the report names the engine, the check, the PHP version and the
seconds and MiB of each part. To time a real run of a site, `./console exp:migration:migrate --apply --format=json`
reports the `duration_ms` of every migration.

## Related pages

- [Migrations](migrations.md), [SQL and settings](migrations-sql-and-settings.md),
  [Packages, deploys and long runs](migrations-packages-deploy-and-long-runs.md),
  [Rollback and captions](migrations-rollback-and-captions.md), [Troubleshooting](migrations-troubleshooting.md)
