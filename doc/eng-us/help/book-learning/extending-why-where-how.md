---
title: "Extending: why, where and how"
audience: developer, builder
category: books
order: 520
summary: Why write an extension, how to name and activate one, and where its folders go, with the book's table of subfolders matched to the layout of an Exponential 6 extension.
keywords: extension, why extend, naming, ActiveExtensions, ActiveAccessExtensions, extension directory, modules, datatypes, eventtypes, actions, settings, Learning book
type: help
---
# Extending: why, where and how

Chapter 5 starts from the fact that almost every area of the system is open to extensions: modules, datatypes, template
operators, workflow events, actions and notifications. An **extension** is a folder under `extension/` that adds one or
more of them without touching the kernel.

## Why write one

The book gives reasons; they still stand:

1. The feature you need does not exist.
2. A similar one does, but not exactly: ask for it or [send a pull request](../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md).
3. A feature has a bug and the fix cannot wait: a small extension (or a kernel override) is a temporary solution.
4. The feature belongs to your company alone.
5. It moves data in or out of another system.

Before you start, look at what exists. The book sends you to the forums and a Subversion server of its time; today
[the se7enxweb extension catalogue](../extension-development/part-1-from-user-to-developer/the-se7enxweb-extension-catalogue.md),
[Packagist](../extension-development/part-1-from-user-to-developer/finding-ideas-on-packagist.md) and
[GitHub](../extension-development/part-1-from-user-to-developer/finding-ideas-on-github.md) are the places.

## Naming

Choose a unique name so that it will not collide later. Use letters, digits and an underscore; **no hyphen** (the book says
so and it is still true: the name becomes part of class names and setting values). The book's example: eZ Systems put `ez`
in front of its own. New names in Exponential use `exp` ([Naming the extension](../extension-development/part-3-extension-developer/extension-directory-layout.md#naming-the-extension)).

## Activating it

Make a folder `extension/<name>` and tell the system in a settings file. The book's setting is the one used today, and it
has a second form for one siteaccess:

```ini
# settings/override/site.ini.append.php: for every siteaccess
[ExtensionSettings]
ActiveExtensions[]=newextension

# settings/siteaccess/<site>/site.ini.append.php: for one siteaccess only
[ExtensionSettings]
ActiveAccessExtensions[]=newextension
```

The order of the list matters when two extensions provide the same setting or template ([Extension load order](../extension-development/part-3-extension-developer/extension-load-order.md)).
The administration also shows and switches them ([Extensions page](../../guides/extensions-page.md),
[Activating an extension](../extension-development/part-3-extension-developer/activating-an-extension.md)).

## The folders

| Book | Exponential 6 | What it holds |
|---|---|---|
| `actions/` | (rarely used) | New actions for forms |
| `datatypes/` | `datatypes/<type>/<type>type.php` | New datatypes ([datatypes](../extension-development/part-3-extension-developer/datatypes.md)) |
| `eventtypes/` | `eventtypes/event/<type>/<type>type.php` | New workflow event types ([event types](../extension-development/part-3-extension-developer/workflow-event-types.md)) |
| `modules/` | `modules/<module>/module.php` and view scripts | New modules and views ([modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)) |
| `settings/` | `settings/` | Settings of the extension ([settings in an extension](../extension-development/part-3-extension-developer/settings-in-an-extension.md)) |
| `translations/` | `translations/<locale>/translation.ts` | Translations |
| (none) | `design/<design>/` | Templates, styles, images of a design ([a design in an extension](../extension-development/part-3-extension-developer/design-in-an-extension.md)) |
| (none) | `classes/` | Your PHP classes, found by the autoloads |
| (none) | `autoloads/eztemplateautoload.php` | Template operators and functions |
| (none) | `cronjobs/`, `bin/php/` | Cronjob scripts and command line scripts |
| (none) | `ezinfo.php`, `extension.xml` | Name, version and license ([ezinfo.php](../extension-development/part-3-extension-developer/ezinfo-php.md)) |

The complete tree with what the kernel does with each folder is [The extension directory layout](../extension-development/part-3-extension-developer/extension-directory-layout.md).
After adding a class run `php bin/php/ezpgenerateautoloads.php -e` ([Autoloads and class naming](../extension-development/part-3-extension-developer/autoloads-and-class-naming.md)).

Next: [the building blocks of an extension](extension-building-blocks.md).

## Source and license

Adapted from chapter 5, "Extending eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged as a map to the current developer pages; folder names, settings and constants checked against Exponential 6; SOAP replaced by the REST API.
