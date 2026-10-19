---
title: What an extension is
audience: user, developer, designer, app-creator
category: extension-development
order: 10
summary: A folder under extension/ that adds settings, templates, modules, datatypes, workflow events and more without touching the kernel, and does nothing until switched on.
keywords: extension, folder, ActiveExtensions, kernel, plugin, add-on, module, datatype, template operator, design, settings, what can I build
type: help
---
# What an extension is

Part 1, chapter 1. This chapter is for anyone who has heard the word "extension" and wants to know exactly what it
means in Exponential: what it is on disk, how it is switched on, and what kinds of things it can add. Everything you
build later in this book is an extension, so this is the foundation.

## In one sentence

An extension is **a folder under `extension/`** that adds settings, templates, modules, content types, commands,
cronjobs and many other things to Exponential **without changing the kernel**, and that does nothing until it is
**switched on**.

## The folder

Look at your own installation:

```bash
ls extension/
```

You see one folder per extension, for example `ezjscore`, `ezoe`, `eztags`, `explayouts`, `sevenx_themes_simple`.
The folder name is the extension's name everywhere: in settings, in Composer's `extra` block, in the extension list
of the administration. The setting that names the folder is `ExtensionDirectory` in the `[ExtensionSettings]` block
of `settings/site.ini`, and its default value is `extension`.

Inside, an extension uses the same directory names the kernel uses, so once you know one you can read all of them:

| Folder or file | What it holds |
|---|---|
| `ezinfo.php`, `extension.xml` | Who made it, which version, which license (see [ezinfo.php](../part-3-extension-developer/ezinfo-php.md)) |
| `settings/` | `.ini.append.php` files that add to or change the kernel's settings |
| `classes/` | PHP classes, found through the autoload array |
| `autoloads/` | Template operator registrations (`eztemplateautoload.php`) |
| `modules/<name>/` | A module with views: pages with their own address |
| `design/<design>/templates/` | Templates, and `stylesheets/`, `javascript/`, `images/` |
| `datatypes/<name>/` | A datatype: a new kind of field for content classes |
| `eventtypes/event/<name>/` | A workflow event type |
| `cronjobs/` | Cronjob scripts |
| `bin/php/` | Command line scripts |
| `translations/<locale>/` | Translations of the extension's texts |
| `share/`, `sql/` | Database schema and data, packages |

The complete map is in [The extension directory layout](../part-3-extension-developer/extension-directory-layout.md).

## The switch

An extension on disk does nothing. It works once it is listed in `ActiveExtensions[]` of `[ExtensionSettings]`
(for the whole installation) or in `ActiveAccessExtensions[]` (for one siteaccess, that is one site or the
administration). See which ones are switched on:

```bash
./console exp:ini list site.ini/ExtensionSettings --allow-root-user
```

You see one `ActiveExtensions[]=<name>` line per active extension, in loading order. The order matters: an
extension loaded earlier wins over a later one for settings and design templates. The browser way is
**Setup > Extensions** (`/setup/extensions`); both are explained in
[Extensions: find, install, configure, build and release](../../../guides/extensions.md).

## Why the kernel stays untouched

The kernel is the code under `kernel/`, `lib/`, `settings/` and `design/` at the root of the installation. It is
replaced at every upgrade. Anything you change there is lost, or worse, merged badly into the next version.
An extension lives beside the kernel and plugs into it through places the kernel was built to look:

- a **setting** that names your folder or your class (for example `ExtensionRepositories[]` for modules,
  `DesignExtensions[]` for templates, `ExtensionDirectories[]` for datatypes),
- a **directory** the kernel scans for a known file name,
- a **design** in the design chain, so your template is found before the kernel's,
- an **event** the kernel announces, that your listener answers.

Because of this, an upgrade of the kernel leaves your extension alone, and an extension can be shared: copy the
folder to another installation, switch it on, and it works there too.

## What an extension can add

The kernel knows more than seventy places where an extension can plug in. **Setup > RAD** (`/setup/rad`) lists every
one of them in seven groups, drawn from the catalogue in `kernel/setup/expradcatalogue.php`. When this page was
written that catalogue held 73 entries:

| Group | Entries | Examples |
|---|---|---|
| Content | 9 | datatype, content class, rich text custom tag, information collection behavior |
| Templates and design | 8 | template operator, fetch function, template function, design extension, override set |
| Modules and views | 11 | module, view of an existing module, policy function, REST provider, ajax function |
| Workflow, events and jobs | 9 | workflow event type, trigger, notification handler, cronjob part, event listener |
| Storage and infrastructure | 12 | database handler, cluster file handler, search engine, session handler, mail transport |
| Packaging and shop | 9 | package handler, payment gateway, VAT and shipping handlers |
| Users, access and language | 15 | login handler, content policy limitation, translation, icon theme, siteaccess settings |

Count the entries on your own installation at `/setup/rad`; the number grows when the kernel learns a new place.
The curated description of every entry is [Extension points](../../../bc/6.0/rad-extension-points.md).

## Small or large, both are extensions

- **Very small**: `extension/str_replace` is two PHP files and one settings file. It adds one template operator.
- **Medium**: `extension/eztags` adds a datatype, modules, templates, fetch functions and its own database tables.
- **Very large**: `extension/explayouts` and its sibling packages add a visual page builder with an editor, an API
  and its own storage.
- **Settings only**: an extension can hold nothing but `.ini.append.php` files, for example the settings of one
  site. That is a perfectly good extension and the safest way to keep a site's configuration in version control.
- **Design only**: a theme is an extension with a `design/` folder (see
  Themes are extensions).

## What an extension is not

| It is not | Because |
|---|---|
| A change to a file in `kernel/` | That is lost at the next upgrade. Use a setting, an override or an event instead. |
| A Composer library | A library under `vendor/` is plain PHP code. An extension can *be* installed by Composer (its package type is `ezpublish-legacy-extension`), but it still lands in `extension/`. |
| Content | Content lives in the database. An extension can *create* content classes or ship sample content in a package, but the content itself belongs to the site. |
| Automatically safe | An extension runs with the same rights as the kernel. Read [Evaluating an old extension](evaluating-an-old-extension.md) before you install one you do not know. |

## Try it

1. List your extensions: `ls extension/ | wc -l` counts the folders on disk.
2. Count the active ones: `./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions --allow-root-user`.
3. Pick one active extension and list its folders: `ls extension/<name>/`. Match each folder to the table above.

The difference between the two counts is normal: installations carry extensions they do not use, so that switching
one on is a settings change rather than an installation.

## Related pages

- Previous: [Part 1: From user to developer](README.md)
- Next: [The ladder: user, builder, extension developer, theme developer](the-ladder-from-user-to-developer.md)
- [Extensions: find, install, configure, build and release](../../../guides/extensions.md)
- [The Extensions page](../../../guides/extensions-page.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
