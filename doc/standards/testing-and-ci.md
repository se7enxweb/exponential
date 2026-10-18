---
title: Testing and continuous integration
audience: developer, admin
category: standards
order: 90
summary: How Exponential is tested - PHPUnit suites with and without a database, the test map, the GitHub Actions workflows, the file manifest, git hooks and the gates before a push - with counts and known gaps.
keywords: testing, PHPUnit, test suite, continuous integration, CI, GitHub Actions, workflow, quality, phpunit.yml, quality.yml, performance, benchmark, CI health, test map, file manifest, filelist.md5, checkmanifest, git hooks, pre-commit, commit-msg, pre-push, gates, MySQL, PostgreSQL, SQLite
type: reference
---
# Testing and continuous integration

This page is for developers who change Exponential and want to know which tests and checks will run, and for
administrators who want to judge how a release was verified. It answers: which test framework and suites exist, what
runs on every push and pull request, what runs only locally, and what is not covered. The working details are in
[Quality checks](../features/6.0/quality-checks.md) and [Continuous integration](../bc/6.0/ci.md).

## Summary

| Standard / practice | Version | Status | Evidence (short) |
|---|---|---|---|
| Unit and integration tests with PHPUnit | PHPUnit 10.5, 11.5, 13 (by PHP version) | **Complies** | `phpunit.xml` (23 suites), 874 `*Test.php` files under `tests/tests/` |
| Continuous integration | GitHub Actions | **Complies** | `.github/workflows/quality.yml`, `phpunit.yml`, `performance.yml`, `ci-health.yml` |
| Tests against real databases | MySQL 8.0, PostgreSQL 15 | **Partly complies** | `phpunit.yml` job `phpunit-db`; no SQLite, MariaDB, Oracle or MongoDB server job |
| File manifest | `share/filelist.md5` | **Complies** | `bin/php/checkmanifest.php`, blocking on a push to `main` |
| Git hooks | git `core.hooksPath` | **Complies** (opt-in) | `.githooks/pre-commit`, `commit-msg`, `pre-push`; `make hooks` |
| Commit message check | project policy | **Complies** | `.githooks/check-commit-range`, `quality.yml` step "Commit messages" |
| Test selection for a change | `tests/test-map.json` | **Complies** | [Quality checks](../features/6.0/quality-checks.md), "Run only the tests of a change" |
| Code coverage reporting | - | **Does not comply** | coverage is collected (`coverage: xdebug`) but not published or gated |
| Performance regression check | project benchmark | **Partly complies** | `performance.yml`, warning only |

## PHPUnit

**What it is.** PHPUnit (Sebastian Bergmann) is the standard unit testing framework for PHP.

**Version.** `composer.json` `require-dev` allows `phpunit/phpunit` `^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4`, so
Composer picks the newest one the PHP version allows. CI pins it per leg: PHPUnit 10.5 on PHP 8.1, 11.5 on PHP 8.2
and 8.3, the `composer.json` range (13) on PHP 8.4 and 8.5. `phpunit.xml` references the PHPUnit 13.0 schema.

**Configuration** (`phpunit.xml`):

- `bootstrap="tests/bootstrap.php"`: loads Composer's autoloader, enables the test class map
  (`ezpAutoloader::enableTests()`, `var/autoload/ezp_tests.php`), defines `PHPUNIT_COMPOSER_INSTALL` for isolated
  child processes and loads the toolkit classes in `tests/toolkit/` (`ezpTestCase`, `ezpDatabaseTestCase` ...). It
  does not boot the kernel; tests that need it do so themselves.
- `memory_limit` `1G`, environment `EZP_TEST_ENVIRONMENT=1`, `<source>` = `lib` and `kernel`.
- Groups excluded by default: `database`, `mail-live`, `mongodb-live`, `network-live`.

**Suites** (23, from `phpunit.xml`):

| Suite | What it covers | Needs |
|---|---|---|
| `security` | `tests/tests/kernel/classes/security` | nothing |
| `kernel-classes` | `tests/tests/kernel/classes` (without security), `kernel/common` (template operators), `kernel/error` | nothing |
| `kernel-content`, `kernel-datatypes`, `kernel-private` | content, datatypes, REST, console commands, helpers | some tests a database |
| `kernel-search` | the built-in search engine | database tests roll back |
| `kernel-contentfinder`, `kernel-contentcheck` | content finder, content checks | database tests only in a throwaway sandbox (`EXP_CONTENTFINDER_SANDBOX`, `EXP_CONTENTCHECK_SANDBOX`) |
| `kernel-logviewer`, `kernel-requestlog` | log viewer, request logs and classifier | nothing |
| `lib` | `tests/tests/lib` (without MongoDB) | nothing |
| `mongodb` | MongoDB adapter units | nothing |
| `mongodb-live` | MongoDB and MySQL integration | running servers |
| `cjw_newsletter`, `explayouts_admin`, `expservices`, `ezoe` | extension behavior on a live installation | an installed site; skipped without one (`ezpLiveInstallation::requireOrSkip()`) |
| `ezjscore`, `ezmultiupload`, `maps` | extension code with stand-ins | nothing |
| `admin-extensions` | admin templates of `git_manager`, `ezupdate`, `sevenx_dse`, ezflow's edit script | files only |
| `quality` | the project's phpcs sniff, the git hooks, the workflow rules (`tests/tests/bin`) | development tools |
| `design` | the admin design rules, CSP, translations (`tests/tests/design`) | files only, Node.js for some |

**Counts** (made for this page with `find`):

| Directory | `*Test.php` files |
|---|---|
| `tests/tests/kernel` | 508 (of which `kernel/classes` 388) |
| `tests/tests/extension` | 148 |
| `tests/tests/lib` | 123 |
| `tests/tests/design` | 91 |
| `tests/tests/bin` | 4 |
| **Total** | **874** (1,135 PHP files in all, with helpers and fixtures) |

43 test files are tagged `@group database`. [Quality checks](../features/6.0/quality-checks.md) speaks of more than
10,000 test methods in the whole run; that figure was not recounted here.

### Tests with and without a database

- `vendor/bin/phpunit` runs every suite without a database; the `database` group is excluded.
- `tests/runtests.php --dsn <dsn> --db-per-test` runs every suite of `phpunit.xml`, with its excludes, against a
  throwaway database, as CI's `phpunit-db` job does. It exits with PHPUnit's code (since 2026-10-15; before, it always
  exited with 0).
- Live-installation suites skip each class with a reason when no installed site is present (CI has none).

## GitHub Actions workflows

**What it is.** GitHub Actions runs the workflows in `.github/workflows/` on GitHub-hosted Ubuntu runners.

| Workflow | Trigger | Jobs (PHP) | Blocks |
|---|---|---|---|
| `quality.yml` | push and pull request to `main`/`master`, by hand | `checks` (8.3, Node.js 22): PHP syntax of changed files, JavaScript syntax of changed files, file manifest, commit messages, code style (warning only); `phpstan` (8.3); `sniffs` (8.4): `phpunit --testsuite quality`; `php80` (8.0): `make lint80-all`, `composer install --no-dev`, boot smoke test `tests/bin/bootsmoke.php` | yes, except code style; the manifest is a warning on a pull request |
| `phpunit.yml` | push and pull request to `main`/`master`, by hand; no paths filter | `phpunit` matrix 8.1, 8.2, 8.3, 8.4, 8.5 (Node.js 22, ImageMagick, xdebug); `phpunit-db` on MySQL 8.0 and PostgreSQL 15 with PHP 8.3, `date.timezone=America/Los_Angeles` | yes |
| `performance.yml` | push and pull request changing PHP, templates, `settings/`, the German translation or `tests/benchmark/` | `micro` (8.5): `bin/php/benchmark.php micro` against `tests/benchmark/baseline.json`, one remeasure | no (`continue-on-error`) |
| `ci-health.yml` | Mondays 06:17 UTC, by hand | `report`: `.github/scripts/ci_health_report.py` (pass rates, failing steps and tests, flaky runs) | no, reports only |

Common properties, all read from the workflow files:

- `permissions: contents: read` (`actions: read` for CI health); no `pull_request_target`.
  `tests/tests/bin/CiWorkflowPullRequestTest.php` keeps it that way.
- `COMPOSER_ROOT_VERSION: dev-main` so packages requiring `se7enxweb/exponential` resolve on a pull request.
- One concurrency group per pull request (a new push cancels the old run); each push to `main` keeps its own run.
- Every job has a timeout (20 or 30 minutes); Composer steps retry three times.
- The PHPUnit jobs resolve dependencies fresh on every run (`composer.ci-phpunit.json`, not committed), so a new
  dependency release can change a result without a change in the repository.
- `phpunit` checks that the suites `security`, `kernel-classes`, `lib`, `expservices` and `ezoe` are configured.

## The file manifest

**What it is.** `share/filelist.md5` lists `md5  path` for every file of the distribution (9,551 lines). The
administration's upgrade check compares the installed files against it; a stale line shows up there as a modified
file. See [File consistency check](../features/6.0/file-consistency-check.md).

- `bin/php/checkmanifest.php --all` checks every entry, `--staged` the staged files, `--fix` rewrites stale
  checksums and adds missing files. It needs no kernel and no database, and uses the same class as the upgrade check
  (`kernel/private/classes/expfileconsistencyreport.php`).
- `make manifest-check` and `make manifest-fix` wrap it.
- Pre-commit hook: `--staged`. CI: blocking on a push to `main`, a warning with the exact patch on a pull request.

**Status: Complies.**

## Git hooks

`make hooks` sets `git config core.hooksPath .githooks`. The hooks are opt-in per clone.

| Hook | What it runs |
|---|---|
| `pre-commit` | `php -l` of each staged PHP file (also with PHP 8.0 if found), `node --check` of staged scripts (if Node.js is installed), `checkmanifest.php --staged`, `checkcodestyle.php --staged` |
| `commit-msg` | subject must start with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: ` (git's `Merge`, `Revert`, `fixup!`, `squash!`, `amend!` pass) |
| `pre-push` | PHPStan, when the development tools are installed |

`.githooks/check-commit-range [--github] <base> <head>` applies the commit message rule to a range, as CI does. A
commit by a project author (an address listed in `.githooks/commit-msg-authors`) fails on a bad subject; a
contributor's commit only gets a warning, because pull requests are merged with their commits as written.
`EXP_COMMIT_MSG_STRICT_ALL=1` makes every author strict. Tested by `tests/tests/bin/githooks/CheckCommitRangeTest.php`.

## The gates before a push

The project's push gate runs the CI checks on a committed revision in a clean worktree
([Continuous integration](../bc/6.0/ci.md), "The local gates and CI"):

| Gate | Check |
|---|---|
| lint | `php -l` of changed PHP files (PHP 8.3, as CI) |
| lint80 | the same with PHP 8.0 |
| js | `node --check` of changed scripts (Node.js 22) |
| commits | `.githooks/check-commit-range` |
| packagist | every changed `se7enxweb/*` constraint in `composer.json` matches a release Packagist already lists |
| manifest | `bin/php/checkmanifest.php --all` |
| phpcs | `bin/php/checkcodestyle.php` (warning only, as in CI) |
| phpstan | PHPStan against the baseline |
| phpunit | the tests of the change (`--fast`) or the whole DB-less run; `--ci` adds PHP 8.0 lint of every file and the PHP 8.0 install with the boot smoke test |

The gate script itself is a maintainer tool and is not part of the repository; the commands it runs are the ones in
the table, all of which are in the repository. For contributors, [Quality checks](../features/6.0/quality-checks.md),
"Before you push", lists the same checks as plain commands.

## Running only the tests of a change: `tests/test-map.json`

`tests/test-map.json` maps source files to the test files that cover them. It has three sections: `map` (hand-made
entries, a path or a pattern where `*` also matches `/`), `generated` (source paths that test files name literally)
and `ignore` (files no PHPUnit test covers on purpose: `doc/*`, `*.md`, `share/filelist.md5`, translations,
`autoload/*`, `.github/*` ...). Combined with tests named after the changed classes, it selects the test files of a
change; each is run in its own PHPUnit process. The commands are in
[Quality checks](../features/6.0/quality-checks.md), "Run only the tests of a change".

## Known gaps

1. **Six test files are in no suite** and therefore never run by `phpunit` or CI:
   `tests/tests/extension/ezupdate/ezUpdateComposerLocationTest.php`, `ezUpdateComposerVersionCacheTest.php`,
   `ezUpdateInstallabilityTest.php`, `ezUpdateJobOutcomeTest.php`, `tests/tests/extension/bccie/ezBccieTest.php` and
   `tests/tests/extension/syndication/ezSyndicationTest.php` (checked against the suites of `phpunit.xml`).
2. The live-installation suites (`expservices`, `ezoe`, `cjw_newsletter`, `explayouts_admin`, `mongodb-live`) are
   skipped in CI.
3. Database jobs cover MySQL 8.0 and PostgreSQL 15 only; SQLite 3 (the default database), MariaDB, Oracle and
   MongoDB servers have no CI job.
4. PHPUnit does not run on PHP 8.0.
5. Coverage is collected on the DB-less legs but neither published nor enforced.
6. Code style and performance are warnings only.
7. Git hooks are opt-in; a clone without `make hooks` relies on CI.
8. Branch protection: `main` has no branch protection rule or ruleset (GitHub API, 2026-10-18), so a red check does
   not technically block a merge or push; it is a policy.

## Related pages

- [Quality checks](../features/6.0/quality-checks.md)
- [Continuous integration](../bc/6.0/ci.md)
- [Coding standards](coding-standards.md)
- [Source control and releases](source-control-and-releases.md)
- [PHP, PHP-FIG and Composer standards](php-and-composer.md)
