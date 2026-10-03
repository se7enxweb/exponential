# ezwebin-ezpackage: the installer packages of ezwebin

This page is for administrators who install a site with the setup wizard and want to know what it imports.
`ezwebin-ezpackage` is the repository of the **setup wizard packages** of the Website Interface: the content classes,
sample content, settings and installer code (`ezwebin_site`, `ezwebin_site_clean` and their installers) that the setup
wizard imports to create a site.

The code of the design itself is [ezwebin](ezwebin.md). This repository is the package source the design was split
from ("mass updates from parent repository ezwebin-ezpackage").

## How it is used

You do not run these packages by hand. The setup wizard (or `php bin/php/kickstarter.php`) imports them, and the
installer class decides which extensions to activate, which siteaccesses to create, and which roles and policies to
add. To see the installed form after an installation:

```bash
ls var/storage/packages/
```

## What is in the repository

| Path | What it is |
|---|---|
| `packages/ezwebin_site` | The full-site package: `package.xml`, `settings/ezwebininstaller.php` (the installer class `eZWebinInstaller`), the INI, role and preference settings the installer applies (`ini-site.php`, `ini-common.php`, `ini-admin.php`, `roles.php`, `preferences.php`, `install-scripts.php`), `documents` and `files` |
| `packages/ezwebin_site_clean` | The same installer without the demo content |
| `packages/ezwebin_classes`, `ezwebin_banners`, `ezwebin_democontent`, `ezwebin_democontent_clean` | Content classes, banners and the demo content |
| `packages/ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray` | The design packages |
| `packages/ezwebin_extension` | The extension itself, packaged (`ezextension/ezwebin`) |
| `ant`, `bin` | Build helpers of the original project |

## What changed (checked against the diffs)

| Date | Change |
|---|---|
| 31 December 2023 | The installer casts the siteaccess port (`access_type_value`) with `(int)` before adding one to it, so a port stored as text no longer raises a warning on PHP 8 when the wizard creates the admin siteaccess. Both `ezwebin_site` and `ezwebin_site_clean`. |
| 8 January 2024 | **SQLite** support, see below. |
| 27 January 2024 | The `eZArchive` autoload file of the packaged extension: the commit message speaks of a PHP 5 constructor and MySQL 8, but the diff only changes the release comment (`1.4-0` to `1.4.0`) and the licence header whitespace. No behaviour changed. |
| 28 January 2024 | The commit message speaks of a MySQL 8 fix for blog templates; the diff changes only the author homepage in the packaged extension's `composer.json` (to the se7enxweb repository). No template changed. |
| 5 March 2024 | Release-testing update of both installers, see below. The same day a trailing comma in the class list of the clean installer was fixed (a parse problem when the wizard ran it). |
| 19 July 2026 | HTML5 markup in the packaged ezwebin templates: XHTML self-closing slashes and obsolete `type` attributes removed from 56 files, the same cleanup as in [ezwebin](ezwebin.md) 6.0.3. |

**8 January 2024, SQLite.** When the extension's database schema is installed, a `sqlite` database uses
`sql/sqlite/sqlite.sql` of the extension, next to the existing `mysql` and `postgresql` choices; the error message says
"schema" instead of the misspelt "shema". This is a code path only: the packaged `ezwebin` extension must ship a
`sql/sqlite` folder for it to find. Check with `ls extension/ezwebin/sql`; the installed copy of ezwebin has no `sql`
folder at all, so the extension-schema step is not used for it.

**5 March 2024, release testing.**

- The installer class version constant is `1.6`; constructors use `__construct` and call `parent::__construct` (PHP 8).
- The packages are versioned `6.0.0-alpha1` (named version 6.0) and point to the se7enx package host instead of the old
  one.
- The list of extensions the installer activates grew: `ezautosave`, `ezodf`, `ezie`, `ezprestapiprovider`, the theme
  extension, `ezpaypal`, `owsimpleoperator`, `swark`, `bcgooglesitemaps`, `bcwebsitestatistics`, `bccie`,
  `xrowextract`, `bcwebshop`, `ezwebin`, `ezmultiupload`.
- The theme extension is `sevenx-themes-simple` (`solutionExtensionName()`).
- The language based siteaccesses are no longer created (the siteaccess list is the user and admin one).
- The var directory is `var/site`.
- The anonymous role received policies for `shop/buy`, creating `comment` objects in the Standard section and the
  star rating functions of `ezjscore` (see [ezstarrating](ezstarrating.md)).
- The `ezpaypal_extension` package is required.

## Related pages

- [ezwebin](ezwebin.md), [ezdemo](ezdemo.md)
- [Setup wizard and editor siteaccess](../setup-wizard-and-editor-siteaccess.md)
- [Chronicle](../../../history/extensions/ezwebin-ezpackage.md) and [release notes](../../../changelogs/extensions/ezwebin-ezpackage.md)
- [Change ledger](../../../history/ledger/ezwebin-ezpackage.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../../history/extensions/months/2023-12.md), [2024-01](../../../history/extensions/months/2024-01.md), [2024-03](../../../history/extensions/months/2024-03.md), [2026-07](../../../history/extensions/months/2026-07.md) (all extensions)
