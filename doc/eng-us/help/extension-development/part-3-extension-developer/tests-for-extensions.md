---
title: Tests for extensions
audience: developer, app-creator
category: extension-development
order: 340
summary: Tests that ship with an extension and need no database: phpunit.xml.dist and bootstrap, unit and file tests, the kernel's own suite, and the other checks before every commit.
keywords: tests, PHPUnit, phpunit.xml.dist, bootstrap, unit test, DB-less, TestCase, assertSame, template test, styling test, CI, checkclasses, checkcodestyle, php -l
type: help
---
# Tests for extensions

Part 3, chapter 34. A test is the cheapest way to know your extension still works after a kernel upgrade, a PHP upgrade
or your own next change. This chapter sets up PHPUnit for an extension the way `git_manager` and `expui` do it, with
tests that need no database. The configuration and test below were run with the PHPUnit that the kernel's
`vendor/` provides (PHPUnit 13 on the reference installation): `OK (3 tests, 3 assertions)`.

## The layout

```text
extension/mysite_hello/
    phpunit.xml.dist
    tests/
        bootstrap.php
        unit/
            OperatorsTest.php
```

## phpunit.xml.dist

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  The unit tests of mysite_hello: plain PHP, no database, no request.
  Run from extension/mysite_hello: php ../../vendor/bin/phpunit
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         bootstrap="tests/bootstrap.php"
         colors="false"
         cacheDirectory="tests/.phpunit.cache"
         failOnWarning="true"
         failOnRisky="true"
         beStrictAboutOutputDuringTests="true">
    <testsuites>
        <testsuite name="unit">
            <directory suffix="Test.php">tests/unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

This is close to `git_manager`'s own file. `failOnWarning` and `beStrictAboutOutputDuringTests` turn sloppy tests
into failures.

## The bootstrap

```php
<?php
/**
 * PHPUnit bootstrap for mysite_hello: loads only the plain classes under test. Nothing of Exponential is booted.
 */
date_default_timezone_set( 'UTC' );
require_once dirname( __DIR__ ) . '/classes/mysitehellooperators.php';
```

Load only what you test. A test that needs no database, no siteaccess and no session runs in milliseconds, on any
machine, in any CI.

## A test

```php
<?php
/**
 * The template operators of mysite_hello, called directly: no template engine needed.
 */
class OperatorsTest extends PHPUnit\Framework\TestCase
{
    private function runOperator( $name, $value, array $named )
    {
        $operators = new mysiteHelloOperators();
        $operators->modify( null, $name, array(), '', '', $value, $named );
        return $value;
    }

    public function testGreetUsesTheDefaultGreeting()
    {
        $this->assertSame( 'Hello, Ada!', $this->runOperator( 'mysite_greet', null, array( 'name' => 'Ada', 'greeting' => 'Hello' ) ) );
    }

    public function testInitialsOfUtf8Names()
    {
        $this->assertSame( 'ÉZ', $this->runOperator( 'mysite_initials', 'émile zola', array() ) );
    }

    public function testEveryOperatorHasAParameterList()
    {
        $operators = new mysiteHelloOperators();
        $this->assertSame( $operators->operatorList(), array_keys( $operators->namedParameterList() ) );
    }
}
```

Run:

```bash
cd extension/mysite_hello
php ../../vendor/bin/phpunit
```

## What to test

| Kind of code | Test |
|---|---|
| Operators, helpers, services, validation | call them directly with values, assert the result |
| Templates | read the files and check rules: no `<style>`, no inline script, every text through `i18n` |
| Settings | the shipped `.ini.append.php` parses and names classes that exist |
| Stylesheets | only `--a4-*` tokens, only your class prefix (`git_manager`'s `AdminStylingTest` does this) |
| Translations | every source text has a translation in the languages you ship |
| Code that needs the database | keep it thin; test the logic beside it without a database |

`git_manager`'s `tests/unit/AdminStylingTest.php` is a good model of a file-checking test: it reads the extension's
templates and stylesheet and fails on inline CSS, inline scripts, hard-coded colors and missing translations.

## Tests in the kernel's suite

The kernel's repository also tests some extensions in `tests/tests/extension/<name>/` (for example `git_manager`,
`ezjscore`, `ezoe`, `ezupdate`). The kernel's CI runs PHPUnit on PHP 8.1 to 8.5 without a database, plus database
tests on MySQL and PostgreSQL ([Continuous integration](../../../bc/6.0/ci.md)). An extension that ships its own
`phpunit.xml.dist` can run in its own repository's CI the same way.

## Other checks before every commit

```bash
find extension/mysite_hello -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
php bin/php/checkclasses.php --quiet-ok
./console exp:checkcodestyle
```

The first prints nothing when every file parses; the second when every class loads; the third checks changed files
against the kernel's `phpcs.xml.dist`.

## Related pages

- Previous: [Admin pages in the admin4 style](admin-pages-in-admin4-style.md)
- Next: [Debugging extensions](debugging-extensions.md)
- [By hand: a PHPUnit test for your extension](tutorials/by-hand-phpunit-test.md)
- [Testing before a release](../part-7-releasing/testing-before-a-release.md)
- [phpunit.de](https://phpunit.de/)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
