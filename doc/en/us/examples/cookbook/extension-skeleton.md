---
title: An extension skeleton
audience: developer
category: examples
order: 10
summary: The smallest complete extension with its metadata (ezinfo.php, extension.xml, composer.json), its settings and the commands that switch it on and make its classes known.
keywords: extension, skeleton, ezinfo.php, extension.xml, composer.json, ActiveExtensions, ExtensionRepositories, DesignExtensions, autoload, ezpgenerateautoloads, version, release
type: example
---
# An extension skeleton

You build the empty frame of the example extension **expexample** that the rest of the cookbook fills: the folder,
the two metadata files every extension carries, a `composer.json` for installing it with Composer, and the settings
files that tell the kernel where its modules, templates, operators and translations are. At the end the extension is
switched on and listed on **Setup > Extensions** with its version.

Prerequisites: a running Exponential 6 installation, a shell in its root, and an administrator login. As `root`,
every command needs `--allow-root-user`; better, run them as the site's user.

## 1. The folder

```bash
mkdir -p extension/expexample/settings \
         extension/expexample/classes \
         extension/expexample/design/standard/templates
```

The folder name is the extension's name: lower case letters, digits and underscores. New code in this project starts
with `exp`.

## 2. ezinfo.php

`extension/expexample/ezinfo.php` is read by **Setup > About** and **Setup > Extensions**. The class is named
`<extension>Info`, `info()` is static, and the keys are quoted strings:

```php
<?php
/**
 * The metadata of the expexample extension, read by Setup > About and Setup > Extensions.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expexampleInfo
{
    public static function info()
    {
        return array(
            'Name'      => "Exponential example extension",
            'Version'   => "0.1.0",
            'Copyright' => "Copyright (C) 2026 Example Company",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/example/expexample"
        );
    }
}
```

## 3. extension.xml

`extension/expexample/extension.xml` is read first when it has `<metadata>`, so its version must be the same number:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>Exponential example extension</name>
        <version>0.1.0</version>
        <copyright>Copyright (C) 2026 Example Company</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/example/expexample</info_url>
    </metadata>
    <dependencies>
    </dependencies>
</software>
```

`<dependencies>` names the extensions this one needs (`<requires><extension name="ezjscore" /></requires>`) or
builds on (`<extends>`); with `site.ini [ExtensionSettings] ExtensionOrdering=enabled` they decide the loading order. Never write `//autogentag//` or
`dev-master` as a version: write the release number.

## 4. composer.json

Only needed when the extension is installed with Composer. The package type `ezpublish-legacy-extension` and the
`ezpublish-legacy-extension-name` make the installer put it in `extension/expexample`:

```json
{
    "name": "example/expexample",
    "description": "An example extension for Exponential 6",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": ">=8.0",
        "se7enxweb/exponential": "^6.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "expexample"
    }
}
```

## 5. The settings that say where things are

Each part of an extension is found through one setting, written in the extension's own
`settings/<file>.ini.append.php`. The file is PHP that outputs nothing, with the INI text inside a comment:

`extension/expexample/settings/module.ini.append.php` (modules, [A custom module and view](custom-module-view.md)):

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
# Look for modules/<module>/module.php in this extension
ExtensionRepositories[]=expexample
# The known module names: keeps a URL alias from taking over the module's address
ModuleList[]=expexample

*/ ?>
```

`extension/expexample/settings/design.ini.append.php` (templates, scripts and style sheets):

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
# Look for design/<design>/templates/... in this extension
DesignExtensions[]=expexample

[JavaScriptSettings]
# design/standard/javascript/expexample.js, packed and loaded by the pagelayouts of the site designs
FrontendJavaScriptList[]=expexample.js

*/ ?>
```

`extension/expexample/settings/site.ini.append.php` (template operators and translations):

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
# Look for autoloads/eztemplateautoload.php in this extension (template operators)
ExtensionAutoloadPath[]=expexample

[RegionalSettings]
# Look for translations/<locale>/translation.ts in this extension
TranslationExtensions[]=expexample

*/ ?>
```

Add the other files (`content.ini.append.php`, `workflow.ini.append.php`, `cronjob.ini.append.php`,
`ezjscore.ini.append.php`) when you add the part they switch on; the pages for those parts show them. An extension
never edits a kernel file in `settings/` and never writes to `settings/override/`.

## 6. Switch it on

Add the extension to `site.ini [ExtensionSettings] ActiveExtensions[]` of the installation. The `exp:ini` command
writes the line into `settings/override/site.ini.append.php` and keeps a backup:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' expexample global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' expexample global --allow-root-user
```

Or press **Activate** on its card on **Setup > Extensions**. For one siteaccess only, use the scope
`siteaccess:<name>` and `ActiveAccessExtensions[]`.

Then make the kernel see its classes and settings:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

`ezpgenerateautoloads.php -e` scans the `classes/` (and every other PHP) directory of the active extensions and writes
`var/autoload/ezp_extension.php`; run it again after adding or renaming a class. Under PHP-FPM or Velocity, the
running workers see new classes only after a reload: see [Deploying with Velocity](velocity-deploy.md).

## 7. Check it

```bash
php -l extension/expexample/ezinfo.php
xmllint --noout extension/expexample/extension.xml
./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

Expected: `No syntax errors detected`, no output from `xmllint`, and `expexample` among the active extensions.
**Setup > Extensions** lists *Exponential example extension* with version 0.1.0, license and website.

Checked for this page (2026-10-18): `php -l` on `ezinfo.php` and the four settings files, `xmllint --noout` on
`extension.xml`, `composer.json` parsed as JSON.

## 8. Releasing a version

When you publish the extension, the version in `ezinfo.php` and in `extension.xml` (and `version` in `composer.json`,
if you keep one) is set to the release number in a commit **before** the tag. Choose the number from the tags that
exist (`git tag -l 'v*' --sort=version:refname`), never from memory, and never move a tag that was pushed. The whole
procedure is in [Extensions, section 7](../../guides/extensions.md#7-release-it).

## Common mistakes

| Symptom | Cause |
|---|---|
| The extension list shows a dash as version | `ezinfo.php` or `extension.xml` is missing or does not parse; or the two disagree |
| `Class '...' not found` | the autoload array was not regenerated, or PHP-FPM or Velocity still runs the old one |
| A setting of the extension has no effect | the extension is not active, or another extension loaded earlier, or `settings/override/`, sets the same variable: `./console exp:ini where <file>/<Block>/<Variable>` |
| A new template is not used | the template override cache: `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` |

## Derived from

- `extension/expservices/ezinfo.php`, `extension/expservices/extension.xml` (bundled extension metadata)
- `extension/ezjscore/settings/site.ini.append.php`, `extension/ezjscore/settings/module.ini.append.php`
- `settings/module.ini` (`ExtensionRepositories[]`, `ModuleList[]`), `settings/design.ini` (`[JavaScriptSettings]`)
- `lib/eztemplate/classes/eztemplate.php` (`ExtensionAutoloadPath`), `lib/ezi18n/classes/eztstranslator.php`
  (`TranslationExtensions`)
- `doc/eng-us/guides/extensions.md`, `doc/eng-us/specifications/6.0/extension-metadata.md`

## Related pages

- [Cookbook](README.md) and [Examples](../README.md)
- [Extensions: find, install, configure, build and release](../../guides/extensions.md)
- [Extension metadata](../../specifications/6.0/extension-metadata.md),
  [Extension loading order](../../features/6.0/extension-loading-order.md),
  [Settings override placements](../../specifications/6.0/ini-override-placements.md)
- [RAD tools](../../features/6.0/rad-tools.md): a wizard that writes this skeleton for you
- [INI settings](../ini-settings/README.md) and [extension points](../extension-points/README.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
