# Specification: extension metadata (`ezinfo.php` and `extension.xml`)

This page is the reference for how an extension tells the system who it is: its name, version, license and
website, as shown on **Setup > About** and used by the upgrade checks. Read it if you write or release an
extension, or if an extension shows a wrong version or no license on the about page.

Since September 2026 every extension, design and theme that ships with or beside Exponential 6.0.15 carries both
files and keeps them in step with its release.

## In short

- Write both `extension.xml` (with a `<metadata>` block) and `ezinfo.php`. `extension.xml` is read first.
- The version in both files is the release version without a `v` (tag `v6.0.8` gives `6.0.8`).
- Raise both in the commit the release tag points at, then tag.
- Check with `php -l ezinfo.php` and `xmllint --noout extension.xml`.

## The reader: `expInfo`

The class `expInfo` (`lib/ezutils/classes/expinfo.php`) is the stable, read-only API for extension metadata. It
reads both files and normalises them. Its public static methods:

| Method | Returns |
|---|---|
| `activeExtensions()` | Metadata of every active extension, keyed by directory name |
| `availableExtensions()` | Metadata of every extension on disk, active or not |
| `hasActiveExtension( $name )` | Whether the named extension is active |
| `extensionInfo( $name, $activeOnly = false )` | Metadata of one extension, or `false` if not found (or inactive when `$activeOnly` is true) |
| `activeNames()` | The directory names of the active extensions |
| `kernelInfo( $section = false )` | Information about the kernel installation |

The normalised fields are `name`, `description`, `summary`, `version`, `copyright`, `author`, `license` and
`info_url`. A summary becomes the description when no description is given.

### Read order

| Order | Source | Rule |
|---|---|---|
| 1 | `extension.xml` | Read first. Fields come from `<metadata>` when the file has one, otherwise from the top level of the file. Non-empty values win |
| 2 | `ezinfo.php` | Used when `extension.xml` is missing or cannot be parsed. Class `<extensionname>Info` with a method `info()` that returns an array; keys are matched case-insensitively. A key that `extension.xml` already gave is not replaced |
| 3 | `composer.json` and the git origin | Fill what neither file gave (version, description, license, website) |

Parse errors of `extension.xml` are collected and then cleared, so a persistent worker does not accumulate errors
across requests.

## `ezinfo.php`

```php
<?php
class myextensionInfo
{
    static function info()
    {
        return array(
            'Name'      => "My Extension",
            'Version'   => "1.2.3",
            'Copyright' => "Copyright (C) 1998 - 2026 7x & Exponential Foundation",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/se7enxweb/myextension"
        );
    }
}
?>
```

Rules the Exponential releases follow:

| Rule | Why |
|---|---|
| The class name is the directory name plus `Info` | — |
| `Name` is a readable name, not the directory name | `enhancedezbinaryfile`, `xrowmetadata` and `exp_enhanced_link` declared their directory name and showed it on the about page until the release of 30 September 2026 |
| Keys are quoted strings | An unquoted key such as `Name` is a constant lookup; fixed in `explayouts_ui` and `explayouts_ui_api` |
| `info()` is `static` | It is called statically |
| `Version` has no `v` prefix | Tag `v6.0.8` is `'Version' => "6.0.8"` |

## `extension.xml`

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>Exponential Website Interface LS</name>
        <version>6.0.16</version>
        <copyright>Copyright (C) 7x. All rights reserved.</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/se7enxweb/ezwebin</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
        <extends>
            <extension name="ezwt" />
        </extends>
    </dependencies>
</software>
```

- `<requires>` names extensions that must be active; `<extends>` names extensions this one builds on.
- The flat form, `<extension requires="..."><name>..</name><summary>..</summary><version>..</version>...</extension>`
  (used by `syndication`), is read the same way, from the top level.

## Release rule

1. Choose the release version.
2. Write it into **both** files, in the commit the release tag will point at. Never tag first and bump
   afterwards: a published tag cannot be corrected.
3. Move the other version fields of the repository with it: `version` in `composer.json` where present, and the
   version header of `share/filelist.md5` where the extension has one.
4. Set `license` to the full text `GNU General Public License v2.0 (or any later version)`, or exactly what the
   extension's license file says, and `info_url` to the extension's project page.

`//autogentag//` and `dev-master` are not versions.

## File manifest (`share/filelist.md5`)

An extension that ships `share/filelist.md5` (for example `cjw_newsletter`) lists the checksum of each of its
files. **Setup > System Upgrade > File consistency check** lists every line that no longer matches as a modified
file, so refresh the manifest in the same release as any file change. Examples from `cjw_newsletter`:

| Release | What happened |
|---|---|
| 4.1.4 | Changed 21 files without refreshing the manifest; the check listed all of them |
| 4.1.5 | Corrected the manifest |
| 4.1.14 | The manifest left out 33 files under `classes/runnable/` |
| 4.1.15 | The manifest lists all 309 tracked files |

## Check before publishing

Run in the extension directory:

```bash
php -l ezinfo.php
xmllint --noout extension.xml
```

Expected: `No syntax errors detected in ezinfo.php`, and no output from `xmllint`. Then confirm both files show
the new version.

## Related pages

- [Behaviour changes of the extensions: metadata, translations and integrity manifests](../../bc/6.0/extensions-behaviour-changes.md#10-metadata-translations-and-integrity-manifests)
- [Extensions](../../features/6.0/extensions/README.md), [Default extension distribution](../../features/6.0/default-extension-distribution.md), [Extension list: sort, inspect and download](../../features/6.0/extension-list-and-downloads.md), [Setup > Extensions: loading order and safe saving](../../features/6.0/extension-loading-order.md), [File consistency check](../../features/6.0/file-consistency-check.md)
- Month pages: [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [February 2024](../../history/2024/2024-02.md), [March 2024](../../history/2024/2024-03.md), [June 2024](../../history/2024/2024-06.md)
