# Specification: the 6.0.13 security and stability hardening

This page is the map of the hardening pass that the 6.0.13 line received on 21 February 2026: seven security
findings, a larger set of null and undefined-value guards that PHP 8 would turn into fatal errors, PHP 8.4
deprecation fixes, and a test suite that proves them. Read it if you run 6.0.12 or older, or if you maintain
extensions that use the same code patterns. The full findings, diffs, test results and CVSS scores are in
[Security hardening release notes](../../bc/6.0/hardening.md); the test tool-chain is explained in
[PHPUnit 10](../../bc/6.0/phpunitv10.md).

## What you need to do

1. Update to 6.0.13 or later. All fixes are backward compatible. The one visible effect: a sort column name
   with characters other than `[a-zA-Z0-9_.]` is skipped with a debug warning (SEC-02).
2. If you have custom extensions, read [For extension developers](#for-extension-developers). The same patterns
   may be in your code.
3. Run the security tests (see [Verify](#verify)).

## The seven security findings

| ID | Class | CWE | Base score | File | Fix |
|---|---|---|---|---|---|
| SEC-01 | SQL injection | 89 | 7.2 | `kernel/classes/ezrole.php` | Role ids are cast to `(int)` before they reach SQL |
| SEC-02 | SQL injection | 89 | 8.8 | `kernel/classes/ezcontentobjecttreenode.php` (`createSortingSQLStrings`) | Custom sort columns are accepted only if they match `[a-zA-Z0-9_.]` |
| SEC-03 | SQL injection | 89 | 7.5 | same file (`createPermissionCheckingSQL`) | Subtree paths are escaped with `escapeString()` |
| SEC-04 | SQL injection | 89 | 6.5 | same file (`hideSubTree`, `unhideSubTree`) | Node ids are cast, paths escaped |
| SEC-05 | OS command injection | 78 | 9.8 | `lib/ezutils/classes/ezsendmailtransport.php` | The sender address passed to `sendmail -f` goes through `escapeshellarg()` |
| SEC-06 | OS command injection | 78 | 8.1 | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | Gzip file names go through `escapeshellarg()` |
| SEC-07 | Reflected XSS | 79 | 6.1 | `kernel/content/search.php` | The search text in the page title is HTML-encoded |

Commits: SEC-01 to SEC-06 in `221caccf31`; SEC-07 is part of `5a4c83fb30`.

At HEAD, `kernel/content/search.php` is a one-call entry point. The code, including the
`htmlspecialchars( $searchText ... )` title, lives in `kernel/private/classes/views/content/search.php`
(see [Commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md)).

## The stability fixes

| Group | Meaning | Examples | Commit |
|---|---|---|---|
| UND | Undefined offset or variable (a notice in PHP 7, an error in PHP 8) | `ezhttpheader.php`, `eznavigationpart.php`, `ezkeyword.php` | `dbb64b1692` |
| LOG | Return value of `preg_match_all` used without a check | `eznamepatternresolver.php` | `dbb64b1692` |
| NUL | Object or array may be null (PHP 8 `TypeError`) | `ezcontentobjecttrashnode.php`, `ezcontentupload.php`, `ezpackagehandler.php`, `ezimagealiashandler.php` | `dbb64b1692` |
| PRG | `preg_replace()` result may be null | `ezdifftextengine.php` | `dbb64b1692` |
| PHP | PHP 8.4 deprecations | `eztimetype.php` (short time strings such as `10:30`), `ezorder.php` (`fetchList()`: required parameter after optional ones, nullable type) | `2b98050cc4` |
| IMP | Stub classes that silently dropped data | `ezsoapparameter.php` (`setValue()` stored nothing), `ezsoapheader.php` (`addHeader()` stored nothing): SOAP calls sent empty parameters and no headers | `5a4c83fb30` |
| SET | Setup scripts with uninitialised variables | `kernel/setup/cachetoolbar.php`, `datatype.php`, `extensions.php`, `session.php` and two wizard steps | `5a4c83fb30` |
| KNT | Content module views without null or input checks | `kernel/content/node_edit.php`, `removenode.php`, `restore.php`, `upload.php`, `view.php` and others (12 files) | `5a4c83fb30` |

At HEAD the KNT views are entry points that call classes under `kernel/private/classes/views/content/`.

The SOAP fix matters if an extension talks to a payment gateway, a remote feed or an authentication provider
through `lib/ezsoap`: calls that looked successful before may have sent empty values.

## What did not change

The patch set does not raise the minimum PHP version. The PHP version analysis per patched file is in the
hardening notes, section "PHP Version Compatibility".

## For extension developers

- Cast ids to integers before SQL: `(int)$id`, and `array_map( 'intval', $ids )` for lists you implode into
  `IN ( ... )`.
- Escape strings with `eZDB::instance()->escapeString( $text )` before you interpolate them.
- Never interpolate a column or table name from the request; check it against a whitelist.
- Wrap every shell argument in `escapeshellarg()`.
- Encode user text for the place it lands in: `htmlspecialchars()` for HTML, the `wash` operator in templates.
- Guard against `null`: `$value ?? ''`, `$array ?? array()`, `is_array()` before `implode()` or `foreach`.

## Verify

The tests are PHPUnit 10 tests in `tests/tests/kernel/classes/security/`: `eZSecurityHardeningTest.php`
(14 test methods at HEAD) and `eZRSSSecurityTest.php`. They are registered as the `security` suite in
`phpunit.xml`. Run them on a development installation, never against a production database:

```bash
php vendor/bin/phpunit --testsuite security
```

When the change was made, 14 of 14 security tests passed after the patch and none before.

## Related pages

- [Security hardening release notes](../../bc/6.0/hardening.md), [PHP 8 support](../../bc/6.0/php8.md), [PHPUnit 10](../../bc/6.0/phpunitv10.md), [PHPUnit 13](../../bc/6.0/phpunitv13.md), [RAD tools — security](../../bc/6.0/rad-security.md)
- [Specification: the August 2026 security patches](security-hardening-2026-08.md), [Security defaults of September 2026](security-defaults-2026-09.md), [Datatype and input hardening](datatype-input-hardening.md)
- [Changelog 6.0.13](../../changelogs/6.0/6.0.13.md), [Chronicle: February 2026](../../history/2026/2026-02.md)
