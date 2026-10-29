---
title: "Supported PHP versions: 8.0 to 8.5"
audience: php-developer, developer
category: php-development
order: 50
summary: Which PHP versions Exponential 6 supports and how each is tested - PHP 8.0 as the oldest, 8.5 as the newest, the composer.json constraint, the CI jobs per version, the differences on 8.0, and what to declare in your own extension.
keywords: PHP 8.0, PHP 8.1, PHP 8.2, PHP 8.3, PHP 8.4, PHP 8.5, supported PHP versions, composer.json, php constraint, RHEL 9, CI matrix, PHPUnit versions, Velocity 0.0.4.54, PHP 7.4
type: help
---
# Supported PHP versions: 8.0 to 8.5

This page is for PHP developers who need to know which PHP versions their code must run on. Exponential 6 runs on
PHP 8.0 to 8.5. The oldest version decides what syntax and functions you may use; the newest decides which
deprecations you must avoid.

## Contents

1. [The range](#the-range)
2. [Why PHP 8.0 is the oldest](#why-php-80-is-the-oldest)
3. [How each version is tested](#how-each-version-is-tested)
4. [What differs on PHP 8.0](#what-differs-on-php-80)
5. [The history of the requirement](#the-history-of-the-requirement)
6. [What to declare in your extension](#what-to-declare-in-your-extension)

## The range

`composer.json` declares:

```json
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"
```

The ranges for 8.6 to 8.8 look forward; those versions do not exist yet and are not tested
([PHP, PHP-FIG and Composer standards](../../standards/php-and-composer.md#supported-php-versions)). The required PHP
extensions are `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl` and `simplexml`;
database and image extensions are in `suggest`.

Exponential Velocity, the application server, runs on PHP 8.0 to 8.5 from its release 0.0.4.54 and is required at
`~0.0.4.57`, so the kernel, the shipped extensions and the server share one minimum
([Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md)).

## Why PHP 8.0 is the oldest

PHP 8.0 is the stock PHP of Red Hat Enterprise Linux 9, AlmaLinux 9 and Rocky Linux 9. Supporting it means a site on
those systems needs no third-party PHP packages. The cost for developers is a rule: no syntax and no unguarded
functions newer than 8.0 in the code ([Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)).

## How each version is tested

| Check | PHP | Where |
|---|---|---|
| Syntax of every PHP file, production `composer install --no-dev`, boot smoke test (`tests/bin/bootsmoke.php`) | 8.0 | `quality.yml`, job `php80` |
| PHPCompatibility with `testVersion` `8.0-` | static | `phpcs.xml.dist` (a warning in CI) |
| PHP syntax of changed files | 8.3 | `quality.yml` |
| PHPUnit without a database | 8.1, 8.2, 8.3, 8.4, 8.5 | `phpunit.yml`, with PHPUnit 10.5 on 8.1, 11.5 on 8.2 and 8.3, 13 on 8.4 and 8.5 |
| PHPUnit with MySQL 8.0 and PostgreSQL 15 | 8.3 | `phpunit.yml`, database tests |
| PHPStan (analysis as `phpVersion: 80100`) | 8.3 | `quality.yml` |
| Micro benchmark | 8.5 | `performance.yml` |

PHPUnit does not run on PHP 8.0, because PHPUnit 10 and later need 8.1. On 8.0 the code is covered by syntax, install
and boot only, which is why the rules for 8.0 matter so much in review
([CI and reproducing a red check](ci-and-reproducing-a-red-check.md)).

## What differs on PHP 8.0

The kernel behaves the same on every version; a few features need newer PHP and fall back quietly:

| Feature | On PHP 8.0 |
|---|---|
| `array_is_list()` | defined in `lib/phpcompat.php` |
| `fputcsv()` with `$eol` | written without `$eol`, line endings replaced afterwards |
| `fsync()` | behind `function_exists()`; the data is flushed with `fflush()` |
| `memory_reset_peak_usage()` | behind `function_exists()` |
| `str_increment()`, `str_decrement()` (8.3) | the string operators return their input |
| `hash( 'xxh128' )` | the SQL query cache uses md5 |
| AVIF image output under Velocity | needs 8.1; negotiation stops at WebP |

The full table with the files is in [PHP 8.0 support](../../bc/6.0/php-8.0-support.md).

## The history of the requirement

| Release | Requirement |
|---|---|
| up to 6.0.7 | PHP 7.4 still installable; sites on 7.4 must stay on 6.0.7 |
| 6.0.8 (April 2025) | Composer requires PHP 8.1 or newer; initial 8.4 support |
| 6.0.11 (December 2025) | PHP 8.5 support |
| 6.0.15 | PHP 8.0 supported again as the oldest version, checked in CI |

Every step is listed in [PHP 8 support](../../bc/6.0/php8.md).

## What to declare in your extension

- Declare `"php": "^8.0"` (or a list like the kernel's) in your extension's `composer.json` unless your extension
  really needs more. A higher minimum blocks installation on RHEL 9's stock PHP, and the kernel's CI job `php80`
  fails when a required package raises its minimum.
- If you need a newer PHP feature, guard it as the kernel does instead of raising the minimum.
- Test on the oldest and the newest version you declare.

## Related pages

- [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)
- [PHP 8 migration notes for your code](php-8-migration-notes-for-your-code.md)
- [Modernizing composer.json](../extension-development/part-4-upgrading/modernizing-composer-json.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
