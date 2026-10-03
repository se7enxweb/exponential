# Specification: the 6.0.13 security and stability hardening

On 21 February 2026 the 6.0.13 line received a hardening pass over the kernel
and library: seven security findings, a larger set of null and undefined-value
guards that PHP 8 turns into fatal errors, PHP 8.4 deprecation fixes and a test
suite that proves them. This page is the map; the full findings, diffs,
test results and CVSS scoring are in
[Security hardening release notes](../../bc/6.0/hardening.md) (`doc/bc/6.0/hardening.md`),
and the test tool-chain is explained in [PHPUnit 10](../../bc/6.0/phpunitv10.md).

## Do you need to do anything?

1. Update to 6.0.13 or later. All fixes are backward compatible; the one visible
   effect is that sort column names containing characters other than
   `[a-zA-Z0-9_.]` are skipped with a debug warning (SEC-02).
2. If you have custom extensions, read "For extension developers" below: the same
   patterns may exist in your code.
3. Run the security tests on your installation (see "Verify").

## The seven security findings

| ID | Class | CWE | Base score | File | Fix |
|---|---|---|---|---|---|
| SEC-01 | SQL injection | 89 | 7.2 | `kernel/classes/ezrole.php` | role ids are cast to `(int)` before they reach SQL |
| SEC-02 | SQL injection | 89 | 8.8 | `kernel/classes/ezcontentobjecttreenode.php` (`createSortingSQLStrings`) | custom sort columns accepted only if they match `[a-zA-Z0-9_.]` |
| SEC-03 | SQL injection | 89 | 7.5 | same (`createPermissionCheckingSQL`) | subtree paths escaped with `escapeString()` |
| SEC-04 | SQL injection | 89 | 6.5 | same (`hideSubTree`, `unhideSubTree`) | node ids cast, paths escaped |
| SEC-05 | OS command injection | 78 | 9.8 | `lib/ezutils/classes/ezsendmailtransport.php` | the sender address passed to `sendmail -f` goes through `escapeshellarg()` |
| SEC-06 | OS command injection | 78 | 8.1 | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | gzip file names go through `escapeshellarg()` |
| SEC-07 | reflected XSS | 79 | 6.1 | `kernel/content/search.php` | the search text in the page title is HTML-encoded |

Commits: SEC-01 to SEC-06 `221caccf31`; SEC-07 is part of `5a4c83fb30`.

## The stability fixes

| Group | Meaning | Examples | Commit |
|---|---|---|---|
| UND | undefined offset or variable (PHP 8: notice becomes error) | `ezhttpheader.php`, `eznavigationpart.php`, `ezkeyword.php` | `dbb64b1692` |
| LOG | return value of `preg_match_all` used without checking | `eznamepatternresolver.php` | `dbb64b1692` |
| NUL | object or array may be null (PHP 8 `TypeError`) | `ezcontentobjecttrashnode.php`, `ezcontentupload.php`, `ezpackagehandler.php`, `ezimagealiashandler.php` | `dbb64b1692` |
| PRG | `preg_replace()` result may be null | `ezdifftextengine.php` | `dbb64b1692` |
| PHP | PHP 8.4 deprecations | `eztimetype.php` (short time strings such as `10:30`), `ezorder.php` (`fetchList()` required parameter after optional ones, nullable type) | `2b98050cc4` |
| IMP | stub classes that silently dropped data | `ezsoapparameter.php` (`setValue()` stored nothing), `ezsoapheader.php` (`addHeader()` stored nothing): SOAP calls sent empty parameters and no headers | `5a4c83fb30` |
| SET | setup scripts with uninitialised variables | `kernel/setup/cachetoolbar.php`, `datatype.php`, `extensions.php`, `session.php` and two wizard steps | `5a4c83fb30` |
| KNT | content module views without null or input checks | `kernel/content/node_edit.php`, `removenode.php`, `restore.php`, `upload.php`, `view.php` and others (12 files) | `5a4c83fb30` |

The SOAP fix matters for you if an extension talks to a payment gateway, a
remote feed or an authentication provider through `lib/ezsoap`: calls that
looked successful earlier may have sent empty values.

## What did not change

The patch set does not raise the minimum PHP version; the PHP version analysis
per patched file is in the hardening notes ("PHP Version Compatibility").

## For extension developers

- Cast ids to integers before SQL: `(int)$id`, and `array_map( 'intval', $ids )`
  for lists you implode into `IN ( ... )`.
- Escape strings with `eZDB::instance()->escapeString( $text )` before
  interpolating them.
- Never interpolate a column or table name from the request; whitelist it.
- Wrap every shell argument in `escapeshellarg()`.
- Encode user text for the context it lands in: `htmlspecialchars()` for HTML,
  the `wash` template operator in templates.
- Guard `null`: `$value ?? ''`, `$array ?? array()`, `is_array()` before
  `implode()` or `foreach`.

## Verify

The tests are PHPUnit 10 tests in `tests/tests/kernel/classes/security/`
(`eZSecurityHardeningTest.php`), registered as the `security` suite in
`phpunit.xml`:

```bash
php vendor/bin/phpunit --testsuite security
```

At the time of the change 14 of 14 security tests passed after the patch and
none before. Running PHPUnit is read-only for your content; use a development
installation, never a production database.

## Related

[Chronicle: February 2026](../../history/2026/2026-02.md),
[Changelog 6.0.13](../../changelogs/6.0/6.0.13.md),
[PHP 8 support](../../bc/6.0/php8.md),
[PHPUnit 13](../../bc/6.0/phpunitv13.md).
