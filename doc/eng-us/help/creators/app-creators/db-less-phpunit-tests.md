---
title: DB-less PHPUnit tests
audience: app-creator, developer
category: app-creation
order: 500
summary: Write fast PHPUnit tests for extension code that need no database - the bootstrap, loading your classes, testing services, validators, datatype string round trips and template operators, and running only the tests of a change.
keywords: PHPUnit, unit tests, DB-less, bootstrap, tests/bootstrap.php, test-map.json, services, validators, toString, fromString, mocks, app creator
type: help
---
# DB-less PHPUnit tests

This page is for application creators who write unit tests that run anywhere in seconds. Exponential's `phpunit.xml`
loads `tests/bootstrap.php`, which loads Composer's autoloader and the test toolkit but does not boot the kernel; tests
that need nothing else run without a database ([PHPUnit](../../../standards/testing-and-ci.md#phpunit)).

## Contents

1. [What to test without a database](#what-to-test-without-a-database)
2. [A test class](#a-test-class)
3. [Loading your classes](#loading-your-classes)
4. [Running](#running)
5. [Mapping tests to files](#mapping-tests-to-files)

## What to test without a database

| Code | Example assertions |
|---|---|
| Services with pure logic | a report's totals, an import's parsing |
| Validators | accepted and refused inputs, error texts |
| Datatype string forms | `fromString( toString( x ) )` gives `x` |
| Template operators | `modify()` sets the expected value |
| Settings parsing | defaults when a key is missing |
| Security helpers | escaping, allow lists, redirect checks |

Keep database access out of these classes, or behind an interface you can replace in the test.

## A test class

```php
<?php
// extension/expdemo/tests/expDemoColorValidatorTest.php
class expDemoColorValidatorTest extends PHPUnit\Framework\TestCase
{
    public function testAcceptsHexColors()
    {
        $this->assertTrue( expDemoColorValidator::isValid( '#a1b2c3' ) );
    }

    public function testRefusesOtherText()
    {
        $this->assertFalse( expDemoColorValidator::isValid( 'red; background: url(x)' ) );
    }
}
```

## Loading your classes

Classes in the extension autoload map are found once `php bin/php/ezpgenerateautoloads.php -e` has run. A test that runs
in a separate checkout can `require_once` the class file when it is not autoloadable, as the kernel's own help tests do
(`tests/tests/kernel/classes/help/`).

## Running

```bash
vendor/bin/phpunit extension/expdemo/tests/
vendor/bin/phpunit --filter testRefusesOtherText extension/expdemo/tests/
```

Run it in your extension's CI on every push and pull request ([Continuous integration for extensions](ci-for-extensions.md)).

## Mapping tests to files

The kernel keeps `tests/test-map.json`, which maps source files to the tests that cover them, so a change runs only its
own tests before a push. Do the same in a large extension: a small map file and a script that picks the tests of the
changed files.

## Related pages

- [Testing applications](testing-applications.md)
- [Testing and CI standard](../../../standards/testing-and-ci.md)
- [PHPUnit versions](../../../bc/6.0/phpunitv13.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
