---
title: Anatomy of an extension
audience: app-creator, developer
category: app-creation
order: 70
summary: Every directory and file an extension can have - modules, classes, autoloads, design, settings, datatypes, event types, cronjobs, bin, share, sql, translations, doc - plus extension.xml, ezinfo.php and composer.json, and what each one registers.
keywords: extension anatomy, extension structure, directories, extension.xml, ezinfo.php, composer.json, modules, classes, design, settings, datatypes, eventtypes, cronjobs, translations, app creator
type: help
---
# Anatomy of an extension

This page is for application creators who want to know what goes where in an extension. An extension is a directory
under `extension/` (or an additional extension directory); the kernel finds its parts by directory names and by the
settings the extension carries ([Extensions](../../../guides/extensions.md)).

## Contents

1. [The directory layout](#the-directory-layout)
2. [Metadata files](#metadata-files)
3. [Activation](#activation)
4. [Loading order](#loading-order)
5. [Checklist for a new extension](#checklist-for-a-new-extension)

## The directory layout

| Directory | Holds | Registered by |
|---|---|---|
| `modules/<module>/` | `module.php`, view scripts, `function_definition.php` | `module.ini [ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| `classes/` | PHP classes | `ezpgenerateautoloads.php -e` writes `autoload/ezp_extension.php` |
| `autoloads/` | template operator registration (`eztemplateautoload.php`) | `site.ini [TemplateSettings] ExtensionAutoloadPath[]` |
| `design/<design>/` | templates, override templates, stylesheets, images, scripts | `design.ini [ExtensionSettings] DesignExtensions[]` |
| `settings/` | `*.ini.append.php` for every siteaccess; `siteaccess/<name>/` per siteaccess; `override/` | being active |
| `datatypes/<name>/` | datatype classes | `content.ini [DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| `eventtypes/event/<name>/` | workflow event types | `workflow.ini [EventSettings] ExtensionDirectories[]`, `AvailableEventTypes[]` |
| `cronjobs/` | cronjob scripts | `cronjob.ini [CronjobSettings] ExtensionDirectories[]` and a `[CronjobPart-<name>]` |
| `bin/php/` | command line scripts | found by the console as `ext:<ext>:<name>` |
| `share/`, `sql/<engine>/` | database schema and data | installed by your setup script or the package |
| `translations/<locale>/translation.ts` | translations | `site.ini [RegionalSettings] TranslationExtensions[]` |
| `doc/` | the extension's own documentation | the help can read it when listed in `exphelp.ini` `ExtensionDocDirs[]` |
| `tests/` | PHPUnit tests | your CI ([Testing applications](testing-applications.md)) |

The real catalogue of every extension point, with the INI line that registers it, is **Setup > RAD**
([Extension points](../../../bc/6.0/rad-extension-points.md)).

## Metadata files

| File | Content |
|---|---|
| `extension.xml` | `<metadata>` with `name`, `version`, `copyright`, `license`, `info_url`; `<dependencies>` with `<requires><extension name="..."/></requires>` |
| `ezinfo.php` | class `<extension>Info` with a static `info()` returning `Name`, `Version`, `Copyright`, `License`, `Info_url` |
| `composer.json` | the Composer package, type `ezpublish-legacy-extension`, license, requirements |

The about page (`/ezinfo/about`) reads `extension.xml` first, so both files must state the same version
([About and package pages](../../../features/6.0/about-and-package-pages.md),
[Versions and releases](versions-and-releases.md)).

## Activation

| Setting | Effect |
|---|---|
| `site.ini [ExtensionSettings] ActiveExtensions[]` | active everywhere, loaded before the siteaccess is known |
| `ActiveAccessExtensions[]` (in a siteaccess) | active only in that siteaccess |

Use `ActiveExtensions[]` for anything a command line script or cronjob must see; per-siteaccess activation means every
script needs `-s <siteaccess>`.

## Loading order

Extensions are read in their activation order (with `ExtensionOrdering=enabled`, dependencies from `extension.xml` are
respected). Order decides which extension's template or setting wins. Change it in **Setup > Extensions**
([Extension loading order](../../../features/6.0/extension-loading-order.md)).

## Checklist for a new extension

- [ ] Name in lower case, new names start with `exp` (classes `exp<Name>...`).
- [ ] `extension.xml`, `ezinfo.php` and `composer.json` agree on name, version and license.
- [ ] Settings in `settings/*.ini.append.php`, never in the kernel's `settings/`.
- [ ] Autoloads regenerated after every new class.
- [ ] No secrets in the repository; keys go in `settings/override/` of the installation.

## Related pages

- [Your first extension](your-first-extension.md)
- [Packaging your application as an extension](packaging-as-an-extension.md)
- [Layering and naming](../../../standards/architecture/layering-and-naming.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
