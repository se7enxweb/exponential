# PHP 8 support

## PHP 8.0 support

For the first 7x release line (December 2023, tag `v6.0.0` and later),
Exponential received changes all over the code base, switching variable syntax to PHP 8 style, variable return type comparison checking before use errors under PHP 8.2, and more. 

The reason is to achieve full PHP 8.0 support by avoiding the deprecation warnings when using PHP 8 syntax.

Care has been taken to keep around compatibility functions in all known cases to avoid fatal errors
for custom extensions, however to avoid warnings you might need to adapt your code as well.

Common cases are classes extending `eZPersistentObject` or `eZDataType`.

Further reading:
- [www.php.net/manual/en/migration80.incompatible.php](https://www.php.net/manual/en/migration80.incompatible.php)
- [www.php.net/manual/en/migration81.incompatible.php](https://www.php.net/manual/en/migration81.incompatible.php)

## PHP 8.2 support

Starting with the 2023.12 release, issues happening on PHP 8.1 and PHP 8.2 have been fixed, but in your own code (extensions) you'll
also need to handle some of those.

Further reading:
- [www.php.net/manual/en/migration82.incompatible.php](https://www.php.net/manual/en/migration82.incompatible.php)

## PHP 8.3 support

Starting with the 2023.12 release, most issues happening on PHP 8.2 and PHP 8.3 have been fixed, but in your own code (extensions) you'll
also need to handle some of those.

Further reading:
- [www.php.net/manual/en/migration83.incompatible.php](https://www.php.net/manual/en/migration83.incompatible.php)

## PHP 8.1 to 8.5: the sequence of changes (December 2023 to April 2026)

This section lists what each release did, so you can see which of your own
extensions might need the same treatment. Every item is a change in the kernel
or library; the first column is the first release that carries it.

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
| 6.0.13 (Apr 2026) | 8.x | The session handler: `read()` returns an empty string for an unknown session, `gc()` uses `time()` and a start time, and the current siteaccess global is checked before use. |

### What this means for your own code

- Declare the properties your classes use. PHP 8.2 deprecates creating them on
  the fly.
- Test a value with `is_countable()` or `is_array()` before `count()` or
  `foreach`.
- Do not pass `null` to string functions; use `$value ?? ''`.
- Give methods that implement `Iterator`, `ArrayAccess` or `Countable` the
  `#[ReturnTypeWillChange]` attribute, or real return types.
- Raise your own `composer.json` PHP constraint to `^8.1` or newer, as the
  product did in 6.0.8; sites still on PHP 7.4 must stay on 6.0.7.

See also [Security hardening](hardening.md), [PHPUnit 10](phpunitv10.md),
[PHPUnit 13](phpunitv13.md).
