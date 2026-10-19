---
title: "Case study: small PHP 8 fixes across many extensions"
audience: developer, app-creator
category: extension-development
order: 260
summary: How this project brought more than twenty extensions to PHP 8.0 through 8.5 in October 2026 - the same few fixes again and again, one commit and one release per extension, and how the work was checked.
keywords: case study, PHP 8.0, PHP 8.5, many extensions, curl_close, nullable, ezinfo, constructors, static calls, composer.json, release, upgrade
type: help
---
# Case study: small PHP 8 fixes across many extensions

Part 4, chapter 26, the last of this part. The other case studies follow one extension. This one follows one kind of
work across many: in October 2026 the project checked every extension it ships against PHP 8.0, the oldest version
Exponential 6 supports, and against PHP 8.5, the newest. It shows what you find when you upgrade a whole collection,
and how to organize that work.

## What was found

The commits, in the extensions' own repositories under se7enxweb on GitHub, fall into a handful of kinds:

| Kind | Examples | Chapter |
|---|---|---|
| Only the PHP constraint and the version | expchangeclass 32196f0, explayouts a58d5f3, explayouts_core 4834db1, expsite_api cfc9e02, expquery_translator f18e9b4, sevenx_themes_media 848b146 | [Modernizing composer.json](modernizing-composer-json.md) |
| `ezinfo.php` with bare keys | expsite_installer 0c771fe, expsite_data_media 81b88ef | [ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md) |
| Implicitly nullable parameters | expsite_api 0f708f4, expquery_translator 8ddf68d, ezfind 4134cbb | [Nullable parameters](nullable-parameters-and-types.md) |
| Functions deprecated in 8.5 | cjw_tmv 0bfa27b (`curl_close()`, `imagedestroy()`), sevenx_authentication_2fa d36f58a, sevenx_themes_media da7c923 (`curl_close()`), sevenx_dse dbeacb3 (`$http_response_header`) | [PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md) |
| Functions deprecated in 8.2 | sevenx_dse b02ff09 (`utf8_decode()`) | the same |
| Constructors PHP 8 does not call | ezfind 7ff9824 (five classes) | [Old-style constructors](old-constructors-and-static-calls.md) |
| Static calls and missing methods | sevenx_themes_media f7d93c0, explayouts_site_api 031d380 | [PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md) |
| `method_exists()` on `false` | explayouts_ui_api 2a95edc | the same |

Most extensions needed only the first row. A few needed one real fix. One (ezfind) needed five constructors, and
without them search results were empty: the size of a fix says nothing about its importance.

## How the work was organized

### One extension, one commit per kind, one release

Each extension got its own commits, each about one kind of change, followed by a version commit. The commit message
says what changed and why, in a form a changelog can be built from ("Updated: ..."):

```text
Updated: The importer and client call imagedestroy() and curl_close() only on PHP versions before 8.0,
where they still had an effect; PHP 8.5 deprecates both
```

The release commit message says what the release means:

```text
Updated: Version 1.0.6: installs and runs on PHP 8.0, the oldest PHP Exponential 6 supports
```

Commit message conventions are in
[Commit messages the project can build a changelog from](../part-5-extend-fork-contribute/commit-messages.md).

### The same fix, the same words

When the same change goes into many repositories, write it the same way everywhere. Every `curl_close()` fix uses the
same line and the same comment:

```php
if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
```

Someone searching for one finds them all, and a reviewer recognizes the change at a glance.

### Checked the same way everywhere

The commit that widened expchangeclass to PHP 8.0 records the checks, and they are the ones to run for every
extension of a collection:

1. PHPCompatibility for the oldest PHP.
2. `php -l` with that PHP (8.0.30 in the commit) and with the newest.
3. Loading every class with that PHP.
4. Serving the extension's pages with that PHP on a full installation.

The kernel's own PHP 8.0 review used the same four checks on "the kernel and 63 extensions"
([PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)).

### Releases in step with the kernel

The kernel's `composer.json` requires versions of the extensions. When an extension's new release is needed (because
it fixes something the kernel's own tests or CI check), the kernel raises its requirement to that release. The
PHP 8.0 support page lists such releases: "syndication 1.3.5, ezstarrating 6.0.9, swark 1.0.5, birthday 1.3.3 and
enhancedezbinaryfile 4.4.5 were released with fixes for code no PHP 8 could load."

## Doing this for your own collection

If you maintain several extensions, or a site with many:

1. **List them** with their PHP constraint and last release (`composer show` and each `composer.json`).
2. **Run the inventory searches** of [Taking inventory of an old extension](inventory-an-old-extension.md) over all of
   them at once; sort the hits by kind, not by extension.
3. **Fix one kind at a time across all extensions**, with the same wording, then release each extension.
4. **Add a CI workflow** with the lint on the oldest and newest PHP to each repository
   ([Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md)), so the next PHP version
   shows up as a red check, not as a broken site.

## Where to go from here

You have reached the end of Part 4. If the extension you upgraded is someone else's, the next part is about doing
that fairly: forks, credit, licenses and pull requests back to the original.

## Related pages

- Previous: [Case study: two-factor authentication without inline scripts](case-study-two-factor-authentication.md)
- Next: [Part 5: Extending, forking ethically and contributing back](../part-5-extend-fork-contribute/README.md)
- [The upgrade checklist](the-upgrade-checklist.md)
- [PHP 8 support](../../../bc/6.0/php8.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
