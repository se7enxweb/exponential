# PHP 8 support

Read this page if you upgrade from a release before 6.0.0, or if you maintain your own extensions. The kernel and
libraries were changed step by step for PHP 8.0 up to 8.5 between December 2023 and April 2026. Compatibility
functions were kept wherever they were known to be needed, so custom extensions do not end in fatal errors. To get
rid of deprecation warnings, you may still have to adapt your own code, most often classes that extend
`eZPersistentObject` or `eZDataType`.

## In short

| | |
|---|---|
| What changed | PHP 8 syntax and checks throughout the kernel; since 6.0.8, Composer requires PHP 8.1 or newer. Since 6.0.15 the code runs on PHP 8.0 again, the oldest supported version ([PHP 8.0 support](php-8.0-support.md)). |
| Who is affected | Sites still on PHP 7.4 (must stay on 6.0.7); authors of extensions with dynamic properties, unchecked `count()` or `null` passed to string functions. |
| How to check | Enable the debug output on a development copy and look for "Deprecated" notices from your extension files. |
| How to fix | Follow "What this means for your own code" below. |

## The sequence of changes (December 2023 to April 2026)

The first column is the first release that carries the change. Use it to see which of your extensions may need the
same treatment.

| Release | PHP topic | What changed |
|---|---|---|
| 6.0.0 (Dec 2023) | 8.3 | Fixes for `eZTemplateForFunction` (the `{for}` template function) and further PHP 8.3 incompatibilities; the setup wizard no longer prints PHP 8 warnings during installation; the installation autoload code was rewritten so no error text appears. |
| 6.0.1 (Jan 2024) | 8.0 | `count()` is called only after `is_countable()` in places that received `null` or scalars; inconsistent testing of data before use fixed. Found while testing the [SQLite driver](../../features/6.0/sqlite-database.md). |
| 6.0.4 (Aug 2024) | 8.2 | Dynamic properties of `eZPersistentObject` classes no longer cause warnings in the debug output. |
| 6.0.11 (commit of Dec 2024, first in 6.0.11) | 8.0 | The `ezmatrix` datatype no longer ends in a fatal error (`f433a08cf9`; the commit sits on a branch that reached a release only with 6.0.11). A smaller PHP 8 notice fix of December 2024 (`21d2c6b4f5`) is in 6.0.6. |
| 6.0.7 (Jan 2025) | 8.0 and 8.2 | The template `{for}` loop warning, the star rating extension and PostgreSQL library warnings fixed; unlinking an already removed file no longer warns. |
| 6.0.8 (Apr 2025) | 8.4 | The removed `E_STRICT` constant is no longer referenced; initial 8.4 support in ten kernel and library files (`eZSession`, `eZINI`, `eZExtension`, `eZSys`, `eZOrder`, `eZDebug`, `eZContentObject`, the autoload generator and others, `86abc11907`). **Composer now requires PHP 8.1 or newer** (`^8.1 \|\| ^8.2`); PHP 7.4 is no longer installable. |
| 6.0.11 (Sep 2025) | 8.1 and 8.2 | `#[ReturnTypeWillChange]` on methods that implement SPL interfaces (no behaviour change); `utf8_decode()` in `ezldapuser` replaced (deprecated in 8.2); public properties declared on `eZURL` and other classes instead of dynamic ones; `null` is no longer passed to `mysqli_real_escape_string()` and `preg_match()`. |
| 6.0.11 (Dec 2025) | 8.5 | Nullable types where PHP 8.5 requires them; Zeta Components raised to releases that support PHP 8.5; the README states PHP 8.5 support. |
| 6.0.13 (Feb 2026) | 8.4 | Time datatype and `eZOrder::fetchList()` deprecations fixed, see [the hardening specification](../../specifications/6.0/security-hardening-6.0.13.md). |
| 6.0.15 (Oct 2026) | 8.0 | PHP 8.0 is the oldest supported version (RHEL 9): `array_is_list()` defined when missing (`lib/phpcompat.php`), `fputcsv()` without `$eol` on 8.0, the HTML escaping functions get the PHP 8.1 default flags explicitly so `wash` escapes single quotes on 8.0 too, a PHP 8.0 syntax check and boot test in CI. See [PHP 8.0 support](php-8.0-support.md). |
| 6.0.13 (Apr 2026) | 8.x | The session handler: `read()` returns an empty string for an unknown session, `gc()` uses `time()` and a start time, and the current siteaccess global is checked before use. |

## How to check your installation

```bash
php -v
grep -n '"php"' composer.json
```

The first line of `php -v` must show 8.0 or newer (8.1 or newer if the site runs on Exponential Velocity, see
[PHP 8.0 support](php-8.0-support.md)). The `composer.json` line shows the PHP versions the current release accepts.

## What this means for your own code

- **Declare the properties your classes use.** PHP 8.2 deprecates creating them on the fly.
- **Test before you count.** Use `is_countable()` or `is_array()` before `count()` or `foreach`.
- **Do not pass `null` to string functions.** Use `$value ?? ''`.
- **Mark SPL methods.** Give methods that implement `Iterator`, `ArrayAccess` or `Countable` the
  `#[ReturnTypeWillChange]` attribute, or real return types.
- **Raise your PHP constraint.** Set your own `composer.json` PHP constraint to `^8.0` or newer, the oldest version
  the product supports again since 6.0.15 (6.0.8 to 6.0.14 required `^8.1`). Use `^8.1` if the site runs on
  Exponential Velocity. Sites still on PHP 7.4 must stay on 6.0.7.

The PHP manual lists every incompatible change per version:

- [PHP 8.0 incompatible changes](https://www.php.net/manual/en/migration80.incompatible.php)
- [PHP 8.1 incompatible changes](https://www.php.net/manual/en/migration81.incompatible.php)
- [PHP 8.2 incompatible changes](https://www.php.net/manual/en/migration82.incompatible.php)
- [PHP 8.3 incompatible changes](https://www.php.net/manual/en/migration83.incompatible.php)

## Related pages

- [Security hardening](hardening.md)
- [PHPUnit 10](phpunitv10.md), [PHPUnit 13](phpunitv13.md) and [PHPUnit 13 on PHP 8.4](phpunitv13forPHP841.md)
- [`ezpSessionHandlerDB` PHP 8 compatibility bugfixes and tests](ezpsessionhandlerdb-php8-bugfix-and-tests.md)
- [PHP 8.4 and 8.5 for the older Symfony stack: the framework forks](../../features/6.0/platform-php85-framework-forks.md)
- History: [April 2025](../../history/2025/2025-04.md), [September 2025](../../history/2025/2025-09.md),
  [December 2025](../../history/2025/2025-12.md), [February 2026](../../history/2026/2026-02.md),
  [March 2026](../../history/2026/2026-03.md)
