---
title: Tests and CI for an upgraded extension
audience: developer
category: extension-development
order: 220
summary: Keep an upgraded extension upgraded: a lint on every supported PHP, small tests without a database that forbid inline code, and a GitHub Actions workflow, with the xrowextract workflow as a model.
keywords: tests, CI, GitHub Actions, php -l, PHP versions matrix, PHPCompatibility, PHPStan, PHPUnit, inline script test, inline CSS test, xmllint, upgrade
type: help
---
# Tests and CI for an upgraded extension

Part 4, chapter 22. An upgrade is only finished when something stops it from coming undone. The next contributor
adds an `onclick`, a hosting company moves to PHP 8.5, someone copies an old snippet back in. This chapter adds the
cheapest tests that catch those, and runs them automatically on GitHub. Writing tests in general is covered in
[Tests for extensions](../part-3-extension-developer/tests-for-extensions.md) and the tutorial
[A PHPUnit test, by hand](../part-3-extension-developer/tutorials/by-hand-phpunit-test.md).

## What to test, in order of value

| Test | Catches | Needs |
|---|---|---|
| `php -l` on every file, on the oldest and newest PHP you support, with all error levels | parse errors, removed syntax, compile-time deprecations (implicitly nullable parameters) | PHP only |
| No inline script, no event handler attribute, no `javascript:` link in templates | CSP regressions | PHP only |
| No `<style>` and no `style=""` in templates | inline CSS regressions | PHP only |
| `xmllint --noout` on `extension.xml` and every `translation.ts` | broken XML | libxml2 |
| The version in `ezinfo.php` equals the one in `extension.xml` | half-done releases | PHP only |
| PHPCompatibility (a PHP_CodeSniffer standard) for your oldest PHP | functions and syntax newer than that PHP | Composer |
| PHPStan against the kernel's classes | calls to methods that do not exist, wrong argument counts | the kernel's source |
| Unit tests of your own classes | logic | PHPUnit |

The first five need nothing but PHP and run in seconds. Start there.

## Tests without a database

The extensions this project upgraded in October 2026 each received small tests that read files and need neither
the kernel nor a database. They are plain PHP scripts with PASS and FAIL lines, run with `php tests/<file>.php`.
se7enxweb/expchangeclass has two: `tests/expchangeclassinlinescripttest.php` and
`tests/expchangeclassadmininlinecsstest.php`. The inline script check, reduced to its core:

```php
<?php
/**
 * The templates carry no inline JavaScript: no <script> element with code, no event handler attribute and no
 * javascript: link. Run with: php tests/myoldextinlinescripttest.php
 */
$failures = 0;
function check( $ok, $message )
{
    global $failures;
    echo ( $ok ? 'PASS' : 'FAIL' ), ": $message\n";
    if ( !$ok )
    {
        $failures++;
    }
}

$root = dirname( __DIR__ );
$templates = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/design', FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file )
{
    if ( substr( (string)$file, -4 ) === '.tpl' )
    {
        // template comments may mention <script>; leave them out
        $templates[substr( (string)$file, strlen( $root ) + 1 )] = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( (string)$file ) );
    }
}

$scripts = $handlers = array();
foreach ( $templates as $path => $source )
{
    if ( preg_match( '/<script\b(?![^>]*\bsrc\s*=)(?![^>]*type\s*=\s*["\']application\/(?:ld\+)?json["\'])[^>]*>/i', $source ) )
    {
        $scripts[] = $path;
    }
    if ( preg_match( '/<[a-z][^>]*\son[a-z]+\s*=/i', $source ) || preg_match( '/=\s*["\']?\s*javascript:/i', $source ) )
    {
        $handlers[] = $path;
    }
}
check( count( $templates ) > 0 && !$scripts, 'no inline script in ' . count( $templates ) . ' templates' . ( $scripts ? ': ' . implode( ', ', $scripts ) : '' ) );
check( !$handlers, 'no event handler attribute or javascript: link' . ( $handlers ? ': ' . implode( ', ', $handlers ) : '' ) );
exit( $failures ? 1 : 0 );
```

An inline CSS test is the same with two other patterns: `/<style\b/i` and `/\sstyle\s*=/i`.

Why so simple? Because it runs anywhere: on a laptop without an installation, on GitHub's runners, on PHP 8.0.

## A lint that also catches deprecations

```bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 | grep -v '^No syntax errors'
```

With `error_reporting=-1`, PHP 8.4 and later print the implicitly nullable parameters as deprecations during the
lint. No output means clean.

## A GitHub Actions workflow

se7enxweb/xrowextract runs its checks on every push and pull request, on every PHP version it supports. Its workflow
`.github/workflows/check.yml`, shortened:

```yaml
name: check

on:
  push:
  pull_request:

permissions:
  contents: read

jobs:
  check:
    name: PHP ${{ matrix.php }}
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.1', '8.2', '8.3', '8.4', '8.5']
    steps:
      - name: Check out xrowextract
        uses: actions/checkout@v4
        with:
          path: xrowextract

      - name: Check out Exponential (kernel and library classes for PHPStan)
        uses: actions/checkout@v4
        with:
          repository: se7enxweb/exponential
          path: exponential

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          tools: composer
          coverage: none

      - name: bin/check.sh
        working-directory: xrowextract
        env:
          EXPONENTIAL_ROOT: ${{ github.workspace }}/exponential
        run: bin/check.sh --php="$(command -v php)"
```

Things worth copying:

- **A matrix of PHP versions** with `fail-fast: false`, so one version's failure does not hide another's.
- **The kernel checked out next to the extension**, so PHPStan can check calls to kernel classes without an
  installation.
- **`permissions: contents: read`**: the workflow can read the code and nothing else.
- **One script (`bin/check.sh`) that does the work**, so the same checks run on your machine as on GitHub.

A smaller workflow for an extension with only the database-free tests:

```yaml
name: tests

on:
  push:
  pull_request:

permissions:
  contents: read

jobs:
  tests:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.0', '8.5']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          coverage: none
      - name: Lint
        run: find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 | (! grep -v '^No syntax errors')
      - name: Tests without a database
        run: for t in tests/*test.php; do php "$t" || exit 1; done
```

The `(! grep ...)` turns "grep found a line" into a failure.

## PHPCompatibility

If you want to be sure no function newer than your oldest PHP slipped in, PHPCompatibility checks the code against a
PHP version range. The kernel itself runs it with `testVersion` `8.0-` ([PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)).
Install it as a development dependency of the extension and run it in the workflow like the lint. The commit that
widened expchangeclass to PHP 8.0 (32196f0) names it among the checks that justified the change.

## Before every release

Run the whole set locally before you tag, because a tag cannot be corrected afterwards
([Testing before a release](../part-7-releasing/testing-before-a-release.md)).

## Related pages

- Previous: [Naming and branding during an upgrade](naming-and-branding-during-an-upgrade.md)
- Next: [Case study: from eZChangeclass to expchangeclass](case-study-expchangeclass.md)
- [Tests for extensions](../part-3-extension-developer/tests-for-extensions.md)
- [GitHub Actions documentation](https://docs.github.com/en/actions)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
