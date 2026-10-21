---
title: DB-less tests and test suites
audience: software-engineer, developer
category: software-engineering
order: 510
summary: How the PHPUnit configuration of Exponential is organized - the bootstrap, the suites in phpunit.xml, the excluded groups, the PHPUnit version per PHP version - and how to write and run tests that need no database, plus the commands for database and live tests when you do need them.
keywords: PHPUnit, phpunit.xml, test suites, DB-less, bootstrap, groups, database group, runtests.php, live tests, environment variables, ezpTestCase, test toolkit, software engineer
type: help
---
# DB-less tests and test suites

This page is for engineers who write or run tests for the kernel or an extension. Most of Exponential's tests run
without a database, which is what lets CI run them on five PHP versions on every push. The reference is
[Testing and continuous integration, PHPUnit](../../standards/testing-and-ci.md#phpunit); for extension authors,
[DB-less PHPUnit tests](../creators/app-creators/db-less-phpunit-tests.md).

## Contents

1. [The configuration](#the-configuration)
2. [The suites](#the-suites)
3. [Running tests](#running-tests)
4. [Writing a DB-less test](#writing-a-db-less-test)
5. [When you need a database or a live server](#when-you-need-a-database-or-a-live-server)

## The configuration

`phpunit.xml`:

- `bootstrap="tests/bootstrap.php"` loads Composer's autoloader, enables the test class map and loads the toolkit
  classes in `tests/toolkit/` (`ezpTestCase`, `ezpDatabaseTestCase` ...). It does not boot the kernel; tests that need
  it do so themselves.
- The groups `database`, `mail-live`, `mongodb-live` and `network-live` are excluded by default.
- A test run writes its own audit records into a directory under `var/tmp`.

PHPUnit itself depends on the PHP version: CI uses 10.5 on PHP 8.1, 11.5 on 8.2 and 8.3, and 13 on 8.4 and 8.5. Write
tests that work on all three ([PHPUnit 13](../../bc/6.0/phpunitv13.md), [PHPUnit 10](../../bc/6.0/phpunitv10.md)).

## The suites

| Suite | Covers |
|---|---|
| `security` | `tests/tests/kernel/classes/security` |
| `kernel-classes` | kernel classes, the template operators of `kernel/common`, the error module |
| `kernel-content`, `kernel-datatypes`, `kernel-private`, `kernel-search` | content, datatypes, REST and console commands, search |
| `kernel-contentfinder`, `kernel-contentcheck`, `kernel-logviewer`, `kernel-requestlog` | newer kernel subsystems |
| `lib` | the libraries |
| `mongodb`, `mongodb-live` | the MongoDB adapter; the live suite needs servers |
| `cjw_newsletter`, `explayouts_admin`, `ezjscore`, `ezmultiupload`, `expservices`, `ezoe`, `maps`, `admin-extensions` | extensions |
| `quality` | the project's own code style sniffs, the git hooks, the workflow rules |
| `design` | the administration design rules, the Content Security Policy rules, translations |

## Running tests

```bash
php vendor/bin/phpunit                                    # every suite, without a database
php vendor/bin/phpunit --testsuite security               # one suite
php vendor/bin/phpunit tests/tests/kernel/classes/security/eZSecurityHardeningTest.php   # one file
php vendor/bin/phpunit --list-tests                       # what would run
```

A test that passes alone and fails in the whole run shares state with another test; run the two files together to find
the pair. A test that passes on your installation and fails in CI usually reads something from your installation; run
it in a fresh clone with only `vendor/` installed.

## Writing a DB-less test

- Test classes and functions directly; pass values, do not read them from a request.
- Do not read `settings/override` or `var/` caches; when you need a setting, set it in the test and restore it.
- Clean up statics, INI values set in-process and files you created.
- Name the file `*Test.php` and put it under the directory of a suite, or it is never run.
- For a template operator or a view, render with a stand-in object rather than a database row.
- If the change maps to a file, make sure `tests/test-map.json` can find your test (named after the class, or a hand
  entry), so the fast gates run it.

## When you need a database or a live server

```bash
php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test
```

runs the suites against a throwaway database, as CI's database job does. Tag such tests `@group database`. Tests
against live servers read their location from environment variables (`EXP_TEST_MYSQL_*`, `EXP_TEST_MONGO_URI`,
`EXP_TEST_PUBLIC_HOST` ...) and skip with the variable's name when it is not set. Pass passwords through the
environment, not on the command line ([Quality checks, Tests that need a live server](../../features/6.0/quality-checks.md#tests-that-need-a-live-server)).

## Related pages

- [Testing strategy](testing-strategy.md)
- [CI gates](ci-gates.md)
- [Tests and CI for an upgraded extension](../extension-development/part-4-upgrading/tests-and-ci-for-an-upgraded-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
