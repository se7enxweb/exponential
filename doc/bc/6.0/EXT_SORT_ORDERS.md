# Setup > Extensions: sorting, metadata and download

Read this page if you override the **Setup > Extensions** template or the extension list of the package wizard, or
if you want to know how the extension list changed. Since 5 August 2026 the list is sorted, shows each extension's
version and last change, and offers a direct download. The page also keeps the original design proposal and the
answers its open questions received. How to use the page is described in
[Extension list: sort, inspect and download any extension](../../features/6.0/extension-list-and-downloads.md).

## In short

| | |
|---|---|
| What changed | `/setup/extensions` is sortable and shows Version, Modified and a Details card with downloads; `package/create` (Extension) shows the same sorted table without downloads. |
| Who is affected | Installations that override `design/admin/templates/setup/extensions.tpl` or `design/standard/templates/package/creators/ezextension/extension.tpl`. |
| How to check | Open `/setup/extensions`; the columns Order, Name, Version and Modified are links. |
| How to fix | Merge your override with the shipped template; the activation check boxes and the `ActivateExtensions` action are unchanged. |

## What changed

Before, `setup/extensions` listed the available extensions in filesystem order (the order of
`eZExtension::extensionRootDirectories()` and `eZDir::findSubItems()`), in a grid of two columns: a check box and the
name. There was no version, no modification date and no download. `package/create` (creator `ezextension`) used the
same unsorted list in `design/standard/templates/package/creators/ezextension/extension.tpl`.

Now (commits `74bc4ccbd8`, `104a4b487a`, `3d125848e8`):

- **Sorting.** Column headers are links; the choice travels as view parameters,
  `/setup/extensions/(sort)/version/(dir)/desc`. The default is the loading order since 29 September 2026
  (`cb5d523bde`); on 5 August it was A to Z, natural and case insensitive. See
  [Setup > Extensions: loading order](../../features/6.0/extension-loading-order.md).
- **Version** comes from `extension.xml`, `ezinfo.php` or `composer.json`
  ([extension metadata](../../specifications/6.0/extension-metadata.md)); a dash means none is stated.
- **Modified** is the time of the newest file inside the extension, not of its folder.
- **Download** as `tar.gz`, `zip`, `tar.bz2` or `ezpkg`: `/setup/extensions/<extension name>/<format>`. The server
  builds the package with the `ezextension` package handler, streams it and removes the temporary file.
- **Package wizard.** Setup > Packages > Create package > Extension shows the same sorted table with Version,
  Modified and a Details card, without download links.
- **Developer API.** `eZExtension::extensionInfo( $name )` in `lib/ezutils/classes/ezextension.php` returns `name`,
  `version`, `mtime`, `mtime_formatted` and the `meta` array, or `null` for an unknown extension.
  `eZPackage::exportToArchive( $archivePath, $format )` accepts the four formats.

## How to check

1. Open `/setup/extensions` in the admin. The column headers Order, Name, Version and Modified are links, and
   **Details** opens a card with four download links.
2. Open `/setup/extensions/ezflow/zip`. The browser downloads `ezflow.zip`.
3. Open Setup > Packages > Create package > Extension. The list is a sorted table with Version and Modified.

## How to fix an override

If you override one of the two templates, compare it with the shipped one:

```bash
diff design/admin/templates/setup/extensions.tpl <your override>
diff design/standard/templates/package/creators/ezextension/extension.tpl <your override>
```

The check box `value` and the `contains` logic did not change, so an old override keeps activating extensions; it
only lacks the new columns and the download links.

## Background: the original design proposal

The change started as a proposal with four goals: sort the list A to Z (natural, case insensitive); show the last
modification date; show the version where it can be found; and, optionally, offer a direct `tar.gz` or `.zip`
download so an administrator does not need the full package wizard.

It proposed a shared metadata helper in `lib/ezutils/classes/ezextension.php` used by both `setup/extensions`
(`kernel/setup/extensions.php`) and the package creator
(`kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php`), a grid of
Activate, Name, Version, Modified and Download in `design/admin/templates/setup/extensions.tpl`, and a download
action that reuses `eZPackage` with the `ezextension` handler and streams the archive with
`Content-Disposition: attachment`.

How its open questions were answered by the implementation:

| Question | Answer |
|---|---|
| Directory mtime or newest file? | Newest file inside the extension. |
| Download in scope, or a separate change? | In scope (`104a4b487a`), in four formats. |
| Where do download links live? | On `setup/extensions` only; the package wizard shows the table without them. |
| Which version source is authoritative? | `extension.xml`, then `ezinfo.php`, then `composer.json` (see the metadata specification). |
| Click-to-sort or A to Z only? | Click-to-sort on every column header. |

## Related pages

- [Extension list: sort, inspect and download any extension](../../features/6.0/extension-list-and-downloads.md)
- [Setup > Extensions: loading order](../../features/6.0/extension-loading-order.md)
- [Extension metadata](../../specifications/6.0/extension-metadata.md)
- [expInfo class and expinfo operator](expinfo-operator.md)
- [Additional extension directories](AdditionalExtensionDirectories.md)
- [August 2026](../../history/2026/2026-08.md)
