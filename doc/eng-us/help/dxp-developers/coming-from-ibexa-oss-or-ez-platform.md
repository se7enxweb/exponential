---
title: Coming from Ibexa OSS or eZ Platform
audience: dxp-developer
category: dxp-development
order: 41
summary: For teams with an eZ Platform or Ibexa OSS project - which Exponential Platform line matches your generation, what stays the same (namespaces, Twig functions, APIs), what changes (package names, command names, branding, table names on 5.0), and where the full migration chapter is.
keywords: migration, Ibexa OSS, eZ Platform, upgrade, generation, package swap, table renames, ibexa_ tables, Twig functions, namespaces, chapter 16, skeleton, Nexus
type: help
---
# Coming from Ibexa OSS or eZ Platform

This page is for developers whose project runs on eZ Platform or Ibexa OSS and who consider moving it to the
Exponential Platform line. It gives the orientation; the step-by-step work is in the installation guide's chapter
[16. Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md).

## Contents

1. [Match your generation](#match-your-generation)
2. [What stays the same](#what-stays-the-same)
3. [What changes](#what-changes)
4. [The work, in phases](#the-work-in-phases)
5. [Things that have no direct counterpart](#things-that-have-no-direct-counterpart)

## Match your generation

The rule of chapter 16: "same generation first". Move to the matching Exponential Platform line, then upgrade.

| Your source | Exponential Platform target |
|---|---|
| Ibexa 5.0 | This DXP: `se7enxweb/exponential-platform-dxp-skeleton` (or Nexus 1.3 and the starter) |
| Ibexa 4.0 to 4.6 | First to 4.6, then the [4.6.x skeleton](the-v4x-skeleton.md) or Nexus 1.2 |
| eZ Platform 3.0 to 3.2, Ibexa 3.3 | First to 3.3, then the 3.3 line (Nexus 1.1, `se7enxweb/oss` 3.3) |
| eZ Platform 2.5 | Nexus 1.0 or Exponential Platform Legacy 2.5 |

Source: [16.5.1 Pick the generation](../../install/16-migrating-from-ez-platform-and-ibexa.md#1651-pick-the-generation).

## What stays the same

- **PHP namespaces** (`Ibexa\Contracts\Core\...`), service ids and the public API: the forks keep them
  ([The se7enxweb forks](the-se7enxweb-forks-and-replace.md#namespaces-stay-the-same)).
- **Twig functions** of the generation: on v5 `ibexa_render_field`, `ibexa_content_name` and the rest; there are no
  `exponential_*` Twig functions ([16.5.7](../../install/16-migrating-from-ez-platform-and-ibexa.md#1657-admin-twig-field-types-rest-and-graphql)).
- **REST and GraphQL**: the same packages (`ibexa/rest`, `ibexa/graphql`).
- **Your bundles** in `src/`, your templates and configuration move across within the same generation.

## What changes

| Area | Change | Page |
|---|---|---|
| Package names | se7enxweb forks replace several upstream packages | [The se7enxweb forks](the-se7enxweb-forks-and-replace.md) |
| Console commands | `exponential:` primary names, the old names as aliases | [Console commands](console-commands-and-renames.md) |
| Branding | The admin and seed data say Exponential Platform DXP | [Platform package forks, Step 5](../../bc/6.0/platform-package-forks-and-command-renames.md#step-5-branding-in-installed-data-and-templates) |
| License field | `(GPL-2.0-or-later or proprietary)` | [Licensing](licensing.md) |
| Databases | SQLite is supported in addition | [SQLite for development](sqlite-for-development.md) |
| From 4.x to 5.0 | Every core table renamed to `ibexa_*`; field type identifiers start with `ibexa_` | [16.5.6](../../install/16-migrating-from-ez-platform-and-ibexa.md#1656-the-database-generation-by-generation), [Field types](the-field-types-it-ships.md#identifiers-changed-in-v5) |

## The work, in phases

1. **Inventory**: `composer.json` and `composer.lock`, custom bundles and templates, field types in use, the size of
   the content tables, the search engine.
2. **Bring the source to the last release of its line.**
3. **Code**: create a project from the matching skeleton, carry over `src/`, `templates/`, `translations/`, `assets/`,
   configuration and storage.
4. **Data**: point it at a copy of your database; for 4.x to 5.0 apply the table renames.
5. **Timed dry run**, then freeze, cut over, verify.

Never run `exponential:install` against a database with content; it creates a new repository
([16.5.1](../../install/16-migrating-from-ez-platform-and-ibexa.md#1651-pick-the-generation)). The hosting view of
the same work is [Migrating customers from eZ Platform and Ibexa](../hosting/migrations/from-ez-platform-and-ibexa.md).

## Things that have no direct counterpart

- **Page Builder landing pages**: no equivalent ships with the DXP; chapter 16 rebuilds them with Exponential Layouts on
  Nexus ([16.5.9](../../install/16-migrating-from-ez-platform-and-ibexa.md#1659-page-building-page-builder-against-exponential-layouts)).
- **Commercial-edition features** of the source: the DXP is built from open source packages only; list what you use
  and check each against [What the DXP ships](what-the-dxp-ships-and-what-it-does-not.md).

## Related pages

- [16. Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md)
- [Platform package forks, command renames and version rules](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
