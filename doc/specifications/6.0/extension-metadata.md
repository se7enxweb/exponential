# Extension metadata: `ezinfo.php` and `extension.xml`

How an extension tells the system, the **Setup > About** page and the upgrade checks what it is. Since September 2026 every extension, design and theme that ships with or beside
Exponential 6.0.15 carries both files and keeps them in step with its release. The reader is `eZInfo` (`lib/ezutils/classes/expinfo.php`, the "stable, read-only API for
querying extension metadata"), which normalises both files.

## What is read

The normalised fields are `name`, `description`, `summary`, `version`, `copyright`, `author`, `license` and `info_url`. A summary becomes the description when none is given.

| Order | Source | Rule |
|---|---|---|
| 1 | `extension.xml` | Read first. Fields are taken from `<metadata>` when the file has one, else from the top level of the file. Non-empty values win |
| 2 | `ezinfo.php` | Used when `extension.xml` is missing or cannot be parsed. Class `<extensionname>Info` with a method `info()` returning an array; keys are matched case-insensitively. A key that `extension.xml` already gave is not replaced |
| 3 | `composer.json` and the git origin | Fill what neither file gave (version, description, license, website) |

Parsing errors of `extension.xml` are collected and cleared so a persistent worker does not keep collecting errors across requests.

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

Rules the Exponential releases follow: the class name is the directory name plus `Info` (an extension whose `ezinfo.php` declared the directory name as `Name`, such as
`enhancedezbinaryfile`, `xrowmetadata` and `exp_enhanced_link`, showed that on the about page until the release of 30 September 2026); the keys are quoted strings (an unquoted key such as
`Name` without quotes is a constant lookup and was fixed in `explayouts_ui`, `explayouts_ui_api`); `info()` is `static`; the `Version` has no `v` prefix (tag `v6.0.8` is `'Version' => "6.0.8"`).

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

The flat form (`<extension requires="..."><name>..</name><summary>..</summary><version>..</version>...</extension>`, used by `syndication`) is read the same way, from the top level.
`<requires>` names extensions that must be active, `<extends>` extensions this one builds on.

## Release rule

A release of an extension raises the version in **both** files to exactly the release's version, in the commit the release tag points at (never tag first and bump afterwards: a tag cannot be corrected
once published). Other version fields the repository keeps move with it: `composer.json` `version` where present, and the version header of `share/filelist.md5` where the extension has one.
`license` is the full text `GNU General Public License v2.0 (or any later version)` (or exactly what the extension's license file says) and `info_url` is the extension's project page.
`//autogentag//` and `dev-master` are not versions.

## File manifest (`share/filelist.md5`)

An extension that ships `share/filelist.md5` (for example `cjw_newsletter`) lists the checksum of each of its files, and **Setup > System Upgrade > File consistency check** lists every line that no
longer matches as a modified file. The manifest must be refreshed in the same release as any file change; `cjw_newsletter` 4.1.4 changed twenty-one files without refreshing it and the check listed all of
them, 4.1.5 corrected it, and 4.1.14's manifest omitted 33 files under `classes/runnable/` until 4.1.15 listed all 309 tracked files.

## Check before publishing

```bash
php -l ezinfo.php
xmllint --noout extension.xml
```

and confirm that both files show the new number.

## Related

* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md#10-metadata-translations-and-integrity-manifests)
* [Extensions](../../features/6.0/extensions/README.md)
