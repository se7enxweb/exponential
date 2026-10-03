# Specification: continuous integration and the test suite (July to August 2026)

This page describes the test suite of the root repository and the GitHub Actions workflow that runs it on PHP 8.1
to 8.5 on every change: what runs, how to run the same on your machine, and what each piece protects. Read it if
you change the kernel or ship extensions and want proof that a change keeps the platform working. Both were
added between 11 July and 18 August 2026.

## In short

- Run everything: `php vendor/bin/phpunit`. Run one suite: `php vendor/bin/phpunit --testsuite security`.
- The workflow is `.github/workflows/phpunit.yml`, with a job per PHP version and a database job.
- Database tests create and drop tables: point them only at a throw-away database.

## What was added

| Date | Commit | What |
|---|---|---|
| 11 July | `8798108324` | PHPUnit 13 and PHP 8.4 clean-up: new `phpunit.xml`, `tests/bootstrap.php`, `tests/xdebug.ini`, 132 files changed under `tests/` and beside it, 72 of them new (cluster file handlers, content classes, roles, RSS export, site access ...; count with `git show --name-status 8798108324`). |
| 18 August | `ea4b1dbbb7`, `ea350a1cd1`, `fd67c78a2f` | The workflow `.github/workflows/phpunit.yml`: PHPUnit on PHP 8.1 to 8.5; path filters; a manual trigger. |
| 18 August | `1ba705708d` and following | A second job runs the database tests on MySQL and PostgreSQL. |
| 18 August | `d07edf3297`, `4de4ebbbe5`, `3cad135744` | MongoDB support in the workflow. |
| 18 August | `8ec58ea189`, `a1effad49a`, `fcd79af6b8` | A PHPUnit compatibility shim so the old test toolkit (written for PHPUnit 3.7) runs on current PHPUnit. |
| 18 August | `d362e2523d`, `f964202536`, `03b6de43d8` | Deprecations of PHPUnit 10 and PHP 8.5 removed (`setAccessible()`, `$backupGlobals`, `imagedestroy()`). |

Many of the 18 August commits are fixes of single workflow failures that were
found by running it; they are merged pull requests with the title "Initial plan"
(an empty first commit) followed by the fix.

## The workflow

File `.github/workflows/phpunit.yml`. Triggers: a push or pull request to
`main` or `master` that changes a `.php` file, `composer.json`, `composer.lock`,
`phpunit.xml` or the workflow itself, and a manual run (`workflow_dispatch`).
The token has read access only (`permissions: contents: read`).

### Job `phpunit`

| Item | Value |
|---|---|
| Matrix | PHP `8.1`, `8.2`, `8.3`, `8.4`, `8.5` (not fail-fast: one failing version does not stop the others) |
| PHPUnit | `^10.5` on PHP 8.1, `^11.5` on 8.2 and 8.3, the version required by `composer.json` on 8.4 and 8.5 |
| PHP extensions | dom, libxml, mbstring, pcre, json, iconv, reflection, session, spl, simplexml, gd, mongodb, mysqli, pdo_mysql, pdo_sqlite, sqlite3, zip |
| Coverage | Xdebug |
| System packages | ImageMagick (image tests), the MongoDB driver libraries |
| Autoloads | `php bin/php/ezpgenerateautoloads.php -s -e` |
| Command | `vendor/bin/phpunit --colors=always` |

The workflow copies `composer.json` to a temporary overlay file
(`composer.ci-phpunit.json`), adjusts `phpunit/phpunit` for the PHP version,
requires `mongodb/mongodb`, resolves the dependencies fresh with
`composer update`, and deletes the overlay when it ends. It cannot use one
committed lock file: the dev requirements differ per PHP version, and a frozen lock
broke on every later repin of `composer.json`.

### Job `phpunit-db`

Runs on PHP 8.3 with MySQL 8.0 and with PostgreSQL 15 (service containers),
creates the database `testdb` (PostgreSQL also gets the `pgcrypto` extension),
sets the time zone to `America/Los_Angeles` for stable results and runs:

```bash
php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test tests extension/ezoe
```

`--db-per-test` gives each test class a fresh schema, as the legacy toolkit does.

## Run the same on your machine

You need a checkout with its `vendor/` directory (a copy of the Exponential
distribution, not a bare git clone; if `vendor/` is missing, run `composer install` once yourself).

```bash
# all suites
php vendor/bin/phpunit

# one suite
php vendor/bin/phpunit --testsuite security
php vendor/bin/phpunit --testsuite kernel-classes

# list tests and files
php vendor/bin/phpunit --list-tests
php vendor/bin/phpunit --list-test-files

# the suites and how many tests each holds (6.0.15 tree, PHPUnit 13.0.0)
php vendor/bin/phpunit --list-suites

# the database tests of the legacy toolkit (use a throw-away database)
php tests/runtests.php --dsn=mysql://YOUR_USER:YOUR_PASSWORD@127.0.0.1/testdb --db-per-test tests
```

The test suites in `phpunit.xml`. Test counts were checked with `--list-suites` on 2 October 2026; they grow with
every release.

| Suite | Directory | Tests | Needs |
|---|---|---|---|
| `security` | `tests/tests/kernel/classes/security` | 51 | nothing |
| `kernel-classes` | `tests/tests/kernel/classes` (without `security`) | 1364 | nothing |
| `kernel-content` | `tests/tests/kernel/content` | 4 | nothing |
| `kernel-datatypes` | `tests/tests/kernel/datatypes` | 38 | nothing |
| `lib` | `tests/tests/lib` (without `ezdb/mongodb`) | 610 | nothing |
| `mongodb` | `expMongoDBAdapterTest.php` | 37 | nothing (no live MongoDB) |
| `mongodb-live` | `expMongoDBIntegrationTest.php` | 18 | running MongoDB and MySQL servers; its group is excluded from the default run |
| `cjw_newsletter` | `tests/tests/extension/cjw_newsletter` (added after August) | 244 | a live database of the installation (throw-away data, mail written to files, never sent) |

Groups `database`, `mail-live`, `mongodb-live` and `network-live` are excluded from the default run. Do not point a test run at a database that holds content you want to keep: the
database tests create and drop tables.

## Coverage with Xdebug

`tests/xdebug.ini` loads Xdebug in coverage mode for isolated test processes.
`tests/bootstrap.php` finds the system's PHP ini scan directory at run time and
appends `tests/`, so the children load it too (11 July; on 18 August the double
`zend_extension` load that broke PHP 8.4 with Xdebug 3.5 was removed, `302134b6e8`).

```bash
php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-text
```

## For extension authors

- Put tests under `tests/` in your extension and add a `testsuite` to a copy of `phpunit.xml`.
- Do not declare the same test class name twice (two fixes in August renamed
  duplicates in the MongoDB tests and the `eZINITest` class).
- Abstract base classes are excluded from the suites; concrete subclasses
  `require_once` their base.
- The test toolkit still offers `ezpTestCase`, `ezpDatabaseTestCase` and
  `ezpTestSuite`; they boot the kernel through `eZScript`, so do not bootstrap
  the kernel yourself in `bootstrap.php`.

## Related pages

- Tool-chain: [PHPUnit 10](../../bc/6.0/phpunitv10.md), [PHPUnit 13](../../bc/6.0/phpunitv13.md), [PHPUnit 13 for PHP 8.4.1](../../bc/6.0/phpunitv13forPHP841.md), [PHP 8 support](../../bc/6.0/php8.md)
- Specifications: [Security patches of August 2026](security-hardening-2026-08.md) (the `security` suite), [Database drivers and installers, September 2026](database-drivers-2026-09.md)
- [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md), [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), month pages [July 2026](../../history/2026/2026-07.md) and [August 2026](../../history/2026/2026-08.md)
