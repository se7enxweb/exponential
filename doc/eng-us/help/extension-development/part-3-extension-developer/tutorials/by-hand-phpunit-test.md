---
title: "By hand: a PHPUnit test for your extension"
audience: developer, app-creator
category: extension-development
order: 230
summary: Tests that run without a database: phpunit.xml.dist, a bootstrap, operator tests and a file test against inline CSS and scripts, green on the kernel's PHPUnit.
keywords: tutorial, by hand, PHPUnit, test, phpunit.xml.dist, bootstrap, TestCase, assertSame, DB-less, template test, inline CSS, inline script
type: help
---
# By hand: a PHPUnit test for your extension

Tutorial 22. You add a test suite to `mysite_hello` that needs no database and no request, so it runs anywhere in a
second. The configuration, bootstrap and operator tests below ran with the PHPUnit in the kernel's `vendor/` (PHPUnit 13
on the reference installation) and reported `OK (3 tests, 3 assertions)`.

## 1. phpunit.xml.dist

`extension/mysite_hello/phpunit.xml.dist`:

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

Add `tests/.phpunit.cache/` to the extension's `.gitignore`.

## 2. The bootstrap

`extension/mysite_hello/tests/bootstrap.php`:

```php
<?php
/**
 * PHPUnit bootstrap for mysite_hello: loads only the plain classes under test. Nothing of Exponential is booted.
 */
date_default_timezone_set( 'UTC' );
require_once dirname( __DIR__ ) . '/classes/mysitehellooperators.php';
```

## 3. The operator tests

`extension/mysite_hello/tests/unit/OperatorsTest.php`: the three tests shown in
[Tests for extensions](../tests-for-extensions.md#a-test) (default greeting, UTF-8 initials, every operator has a
parameter list).

## 4. A file test for templates

`extension/mysite_hello/tests/unit/TemplatesTest.php`:

```php
<?php
/**
 * The templates of mysite_hello follow the administration's rules: no <style>, no style="", no inline script or
 * event handler. One test over every template, so it runs the same on every PHPUnit version the kernel supports.
 */
class TemplatesTest extends PHPUnit\Framework\TestCase
{
    public function testNoInlineCssOrScript()
    {
        $checked = 0;
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( dirname( __DIR__, 2 ) . '/design' ) );
        foreach ( $it as $file )
        {
            if ( !$file->isFile() || substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $text = file_get_contents( $file->getPathname() );
            $name = $file->getPathname();
            $this->assertDoesNotMatchRegularExpression( '/<style\b/i', $text, "$name: a <style> block" );
            $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/i', $text, "$name: a style attribute" );
            $this->assertDoesNotMatchRegularExpression( '/<script\b[^>]*>\s*\S/i', $text, "$name: an inline script" );
            $this->assertDoesNotMatchRegularExpression( '/\son[a-z]+\s*=/i', $text, "$name: an event handler attribute" );
            $checked++;
        }
        $this->assertGreaterThan( 0, $checked, 'no template was found' );
    }
}
```

With the templates of the earlier tutorials, the whole suite reported `OK (4 tests, 24 assertions)`.

> **Note**
> A first version of this test used a `@dataProvider` annotation. PHPUnit 13 no longer reads annotations and called
> the test without arguments (`ArgumentCountError`). One test that loops over the files works on every PHPUnit version
> the kernel's `composer.json` accepts (9.6 to 13); if you only support new versions, use the
> `#[DataProvider('templates')]` attribute instead.

## 5. Run

```bash
cd extension/mysite_hello
php ../../vendor/bin/phpunit
```

## 6. In CI

Put the same command into the extension repository's CI (for example a GitHub Actions workflow that checks out the
extension, installs PHP and PHPUnit with Composer, and runs it). The kernel's own CI runs PHPUnit on PHP 8.1 to 8.5
([Continuous integration](../../../../bc/6.0/ci.md)); do the same for the versions your `composer.json` claims.

## Related pages

- Previous: [By hand: help pages for your extension](by-hand-help-pages-for-your-extension.md)
- Next: [Capstone: a complete extension from idea to release](capstone-a-complete-extension.md)
- [Tests for extensions](../tests-for-extensions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
