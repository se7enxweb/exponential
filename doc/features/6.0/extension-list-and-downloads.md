# Extension list: sort, inspect and download any extension

**Setup > Extensions** (`/setup/extensions`) lists the extensions that are on disk
and lets you activate them. Since 5 August 2026 (commits `74bc4ccbd8`,
`104a4b487a`, `3d125848e8`) the list answers the questions an administrator
actually asks: which extension is this, which version, when did it change, and
can I take a copy of it?

## What you see

- **The loading order by default** (since 29 September 2026 at HEAD, commit `cb5d523bde`): active extensions
  in the order of `ActiveExtensions`, then the ones active only for a siteaccess, then the rest by name.
  When this page was first written (5 August, `74bc4ccbd8`) the default was A to Z, natural and case
  insensitive (`Ext2` before `Ext10`); sort by **Name** to get that back. See
  [Setup > Extensions: loading order](extension-loading-order.md).
- Column headers (**Order**, **Name**, **Version**, **Modified** and others) are links: click one to
  sort by it, click again to reverse. The choice travels as view parameters so the pager keeps it:
  `/setup/extensions/(sort)/version/(dir)/desc`. `sort` is one of `order`, `name`, `info_name`,
  `license`, `version`, `mtime`; `dir` is `asc` or `desc`. Any other value falls back to `order` and
  `asc`. The August form `/setup/extensions?SortBy=version&SortOrder=desc` is still read, so bookmarks work
  (`kernel/private/classes/views/setup/extensions.php`).
- **Version** is the version the extension states, read from `extension.xml`,
  `ezinfo.php` or `composer.json` (see [extension metadata](../../specifications/6.0/extension-metadata.md)).
  A dash means none is stated.
- **Modified** is the time of the newest file inside the extension, not of its
  folder, so editing a template deep inside moves the date.
- **Details** (or a click on the name) opens an info card under the row: description, author, license,
  info URL, and four download links. Only one card is open at a time.

The activation check boxes and the Activate button work as before.

## Download an extension

The card offers `tar.gz`, `.zip`, `tar.bz2` and `.ezpkg`. A download is one URL:

```
/setup/extensions/<extension name>/<format>
/setup/extensions/ezflow/zip
```

`<format>` is one of `tar.gz`, `tar.bz2`, `zip`, `ezpkg`. The server builds a
package of the extension with the `ezextension` package handler, streams it as an
attachment named `<name>.<format>` and removes the temporary file afterwards
(`104a4b487a`). Use it to take a copy before an upgrade, to hand an extension to
a colleague, or to move it to another installation.

Only extensions that exist under one of the extension roots
(`extension/` or an [additional extension directory](additional-extension-directories.md))
can be downloaded; any other name does nothing.

## The package wizard list

**Setup > Packages > Create package > Extension** (`package/create`, creator
`ezextension`) shows the same A to Z list as a table with Version, Modified and a
Details card, without the download links (`3d125848e8`).

## For developers

`eZExtension::extensionInfo( $name )` (`lib/ezutils/classes/ezextension.php`)
returns the data the page uses: `name`, `version`, `mtime`, `mtime_formatted`
and the `meta` array (description, author, license, `info_url`). It returns
`null` for an unknown extension. `eZPackage::exportToArchive( $archivePath, $format )`
accepts the four formats.

The design proposal that led to it, with the open questions, is kept in
[EXT_SORT_ORDERS](../../bc/6.0/EXT_SORT_ORDERS.md).

## Limits

- Large extensions take a moment to scan for the newest file; the list is not cached.
- A download packs the extension as it is on disk, including files you may not
  want to hand on (configuration, keys). Look inside before you share it.

## Related

- [Extension metadata](../../specifications/6.0/extension-metadata.md)
- [Additional extension directories](additional-extension-directories.md)
- [Setup > Extensions: loading order](extension-loading-order.md)
- Month page: [August 2026](../../history/2026/2026-08.md); [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [About and Copyright pages](about-and-package-pages.md)
- [Default extension distribution](default-extension-distribution.md)
- [Package compare and import](package-compare-and-import.md)

## Related pages

- [Extensions, themes and packages](extensions/README.md)
- [January 2024, first half (1 to 15 January)](../../history/2024/2024-01a.md)
- [January 2024, second half (16 to 31 January)](../../history/2024/2024-01b.md)
- [February 2024](../../history/2024/2024-02.md)
- [March 2024](../../history/2024/2024-03.md)
- [June 2024](../../history/2024/2024-06.md)
