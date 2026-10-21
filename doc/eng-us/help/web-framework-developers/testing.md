---
title: "Testing: PHPUnit, database tests and checks"
audience: web-framework-developer, developer
category: web-frameworks
order: 360
summary: How to test an application on Exponential for developers used to WebTestCase, Laravel's HTTP tests, Django's test client or Rails system tests - PHPUnit without a database for services, tests against a throwaway database with runtests.php, template and settings checks, HTTP and browser checks with a test user, the kernel's own CI as a model, and what has no built-in equivalent.
keywords: testing, PHPUnit, unit tests, integration tests, database tests, runtests.php, phpunit.xml, tests/bootstrap.php, test-map.json, eztemplatecheck, browser tests, CI, GitHub Actions, WebTestCase, Laravel tests, Django test client, web framework developer
type: help
---
# Testing: PHPUnit, database tests and checks

This page is for developers who test with a framework's test kit: Symfony's `WebTestCase`, Laravel's HTTP tests and
factories, Django's test client and fixtures, Rails system tests. Exponential's tests are plain PHPUnit. The kernel runs
most of its suites **without a database**, a few against a throwaway one, and adds checks for templates, settings and
links. Do the same in your extension ([Testing applications](../creators/app-creators/testing-applications.md)).

## Contents

1. [The layers](#the-layers)
2. [Unit tests without a database](#unit-tests-without-a-database)
3. [Tests with a database](#tests-with-a-database)
4. [Templates, settings and pages](#templates-settings-and-pages)
5. [The kernel's CI as a model](#the-kernels-ci-as-a-model)
6. [What has no built-in equivalent](#what-has-no-built-in-equivalent)

## The layers

| Layer | Tool | Catches |
|---|---|---|
| Syntax | `php -l`, `bin/php/eztemplatecheck.php` | parse errors in PHP and templates |
| Unit | PHPUnit, no database | logic of services, validators, handlers, operators |
| Integration | PHPUnit with `tests/runtests.php --dsn ...` | SQL, persistent objects, content operations |
| Rendered pages | HTTP requests, browser automation | templates, permissions, redirects |
| Accessibility and CSP | an accessibility checker, the CSP report log | barriers, inline code |

## Unit tests without a database

`phpunit.xml` loads `tests/bootstrap.php`, which loads Composer's autoloader and the test toolkit but does not boot the
kernel ([DB-less PHPUnit tests](../creators/app-creators/db-less-phpunit-tests.md)):

```php
<?php
// extension/expdemo/tests/expDemoColorValidatorTest.php
class expDemoColorValidatorTest extends PHPUnit\Framework\TestCase
{
    public function testAcceptsHexColors()
    {
        $this->assertTrue( expDemoColorValidator::isValid( '#a1b2c3' ) );
    }
}
```

```bash
vendor/bin/phpunit extension/expdemo/tests/
```

This works when the logic lives in plain classes with their dependencies passed in
([Dependency injection and services](dependency-injection-and-services.md)). Keep database access out of those classes
or behind an interface you can replace in the test.

## Tests with a database

`tests/runtests.php --dsn <dsn> --db-per-test` runs suites against a throwaway database, as the kernel's CI job does on
MySQL and PostgreSQL. Mark such tests `@group database`; `vendor/bin/phpunit` alone excludes that group. Never point
tests at a database that holds real content ([PHPUnit in the testing standard](../../standards/testing-and-ci.md#phpunit)).

The test toolkit in `tests/toolkit/` has the base classes `ezpTestCase` and `ezpDatabaseTestCase`, and
`ezpINIHelper::setINISetting()` / `restoreINISettings()` to change a setting for one test.

There are no model factories: create the content your test needs with `eZContentFunctions::createAndPublishObject()`
and remote IDs, and your own rows with `eZPersistentObject::store()`.

## Templates, settings and pages

```bash
php bin/php/eztemplatecheck.php -s<siteaccess> extension/expdemo/design/   # every template of your extension
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit <siteaccess>  # settings resolve as expected
```

For pages, send HTTP requests to a running installation (PHP's built-in server through Velocity's `php` engine, or a
test host) as anonymous, as a restricted user and as an editor. Use a dedicated test user created for the run, never
a shared administrator account. Check the browser console and `var/log/csp.log` for blocked inline code.

## The kernel's CI as a model

The kernel's `phpunit.xml` has 23 suites; GitHub Actions run them on PHP 8.1 to 8.5 without a database and on MySQL
and PostgreSQL with one, plus syntax, PHPStan, code style and a PHP 8.0 boot test. `tests/test-map.json` maps source
files to their tests, so a change runs only its own tests before a push ([Testing and CI standard](../../standards/testing-and-ci.md),
[Continuous integration for extensions](../creators/app-creators/ci-for-extensions.md)).

## What has no built-in equivalent

| Framework feature | In Exponential |
|---|---|
| A kernel test client that handles a request in-process (`WebTestCase`, `$this->get()`, Django `Client`) | none; test services directly and pages over HTTP |
| Model factories, fixtures loaders | content API calls and `.dba` seed rows |
| Transaction rollback per test | `--db-per-test` with a throwaway database |
| Mail fakes | the `file` mail transport in a test environment variant ([Environments](environments-and-secrets.md)) |

## Related pages

- [Testing applications](../creators/app-creators/testing-applications.md)
- [Quality checks](../../features/6.0/quality-checks.md)
- [PHPUnit versions](../../bc/6.0/phpunitv13.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
