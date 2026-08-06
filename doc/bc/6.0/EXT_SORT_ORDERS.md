# Proposal: Backend extension sorting, metadata, and direct download

Ticket: `Aufgabe #16328` – Backend Sortingorder of extensins A-Z
Scope: `setup/extensions` and `package/create` (extension export) on `edit.alpha.se7enx.com`
Status: Draft for discussion – no code changes yet

## Current state

- `setup/extensions` lists all available extensions in the order returned by `eZExtension::extensionRootDirectories()` / `eZDir::findSubItems()`. That order is filesystem order, not alphabetical.
- The grid has two columns: a checkbox and the extension name. There is no version, no modification date, and no direct download action.
- `package/create` (creator `ezextension`) uses the same unsorted list in `design/standard/templates/package/creators/ezextension/extension.tpl`.

## Goals

1. Sort the available-extension list A-Z (natural, case-insensitive).
2. Show the last modification date per extension.
3. Show the version per extension when it can be discovered.
4. (Optional per ticket note #2) Offer a direct `tar.gz` or `.zip` download of an extension so an admin does not need the full package wizard.

## Proposed implementation

### 1. Common extension metadata helper

Add a small helper in `lib/ezutils/classes/ezextension.php` so the logic is shared between `setup/extensions` and the package creator:

```php
/**
 * Return metadata for a single extension: name, path, mtime, version.
 * Version is read from the first source that exists:
 *   1. package.xml       <version> or <ezpublish>/<version>
 *   2. composer.json     version
 *   3. ezinfo.php        $Params['Version'] (if present)
 * Falls back to '—' if none are available.
 */
public static function extensionInfo( $name, eZINI|null $siteINI = null )
```

- `mtime`: `filemtime()` of the extension root directory returned by `eZExtension::extensionPath( $name )`. This is cheap and stable. If we later want "newest file under the tree" we can make the source configurable via `site.ini`.
- `version`: parsed once and cached in a static map to avoid re-reading the same files for both `setup/extensions` and `package/create`.

### 2. `setup/extensions` (`kernel/setup/extensions.php`)

Replace the flat `$availableExtensionArray` with a sorted, info-enriched array and expose the details to the template:

```php
$availableExtensionArray = array(); // keeps the sorted names for BC with selected_extensions
$extensionInfo = array();           // keyed by name

foreach ( ... same roots ... ) {
    foreach ( ... sub items ... ) {
        $availableExtensionArray[$extensionName] = $extensionName;
    }
}
natcasesort( $availableExtensionArray );
$availableExtensionArray = array_values( $availableExtensionArray );

foreach ( $availableExtensionArray as $name ) {
    $extensionInfo[$name] = eZExtension::extensionInfo( $name );
}

$tpl->setVariable( 'available_extension_array', $availableExtensionArray );
$tpl->setVariable( 'extension_info',            $extensionInfo );
```

Update `design/admin/templates/setup/extensions.tpl` to a three-column (or four-column if download is included) grid:

| Activate | Name | Version | Modified | Download |
|----------|------|---------|----------|----------|

The checkbox `value` and `contains` logic stay unchanged so the existing `ActivateExtensions` action continues to work.

### 3. `package/create` extension export (`kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php`)

In `loadExtensionName()`:

```php
$extensionList = array();
$extensionInfo  = array();
// collect names then sort natcasesort
// build $extensionInfo via eZExtension::extensionInfo()
$tpl->setVariable( 'extension_list', $extensionList );
$tpl->setVariable( 'extension_info',  $extensionInfo );
```

Update `design/standard/templates/package/creators/ezextension/extension.tpl` to either a sortable table or at least an A-Z `<ul>` with version/mtime annotations. A table is preferable because it matches the `setup/extensions` UX.

### 4. Direct download (optional)

Add a new action to `kernel/setup/extensions.php` (or a dedicated `setup/extensiondownload` view if preferred):

- `DownloadExtension` action: `POST` with `ExtensionName` and `ExtensionFormat` (`tar.gz` or `zip`).
- Reuse `eZPackage` with the existing `ezextension` package handler to build an in-memory package for the single extension, then call `eZPackage::exportToArchive()` for `tar.gz` or a new zip wrapper.
- Stream the generated archive with the right `Content-Type` and `Content-Disposition: attachment; filename="<name>.tar.gz"`.
- Delete the temporary archive after the response is sent, or place it under `var/tmp/` and GC it.

For the `setup/extensions` grid this means adding a small per-row "Download" dropdown/button (format + button) or a single download icon per row. If this is out of scope for the first pass, it can be deferred to a follow-up ticket.

## Files likely to change

- `lib/ezutils/classes/ezextension.php` – new `extensionInfo()` helper
- `kernel/setup/extensions.php` – sort + set `extension_info`
- `design/admin/templates/setup/extensions.tpl` – grid with Version/Modified/Download
- `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php` – sort + set `extension_info`
- `design/standard/templates/package/creators/ezextension/extension.tpl` – sorted, annotated list
- `kernel/setup/module.php` – if a new `DownloadExtension` action or view is added
- `settings/package.ini` or `module.ini` – if new permissions are required for the download action

## Open questions for discussion

1. Is the extension `mtime` the directory mtime, or should it be the newest file under the extension tree? Directory mtime is cheap; newest-file is more useful but heavier.
2. Is the direct-download feature in scope for this ticket, or should it be split into a second, smaller ticket?
3. Where should download links live: `setup/extensions` only, `package/create` only, or both?
4. Which version source is authoritative for Exponential-specific extensions? Most of them appear to have `composer.json`; the existing `package.xml` files are mainly for legacy packages.
5. Should the grid support click-to-sort by Version/Modified, or is A-Z by name sufficient?

## Testing plan

1. Open `https://edit.alpha.se7enx.com/setup/extensions` and confirm:
   - Extensions are A-Z.
   - Version and Modified columns are populated (or show `—` where not available).
   - Activating/deactivating extensions still works and preserves the sorted display.
2. Open `https://edit.alpha.se7enx.com/package/create` → "Extension export" and confirm the list is A-Z and annotated.
3. If download is implemented: download a `tar.gz`, verify it extracts and contains the full extension directory.

## Next step

Await approval/answers to the open questions, then implement and commit in the `sevenx_themes_media` / kernel directories as appropriate.
