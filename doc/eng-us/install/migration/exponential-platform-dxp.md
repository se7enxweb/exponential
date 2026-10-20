---
title: Exponential 6 and Exponential Platform DXP
audience: admin, developer, project-manager
category: install-upgrade
order: 22
summary: Moving an Exponential 6 site to Exponential Platform DXP 1.x (the 4.6 skeleton) or 2.x (the v5 skeleton), and back - what DXP contains and does not, the generation-by-generation route through the shared database, XmlText to RichText, templates, users, URLs, verification, rollback and checklist.
keywords: Exponential Platform DXP, DXP 1.x, DXP 2.x, skeleton, exponential-platform-dxp, v4.6, v5, Symfony 7.4, RichText, XmlText, ezxmltext:convert-to-richtext, ibexa_ tables, Twig, migration
type: install
---
# Exponential 6 and Exponential Platform DXP

**Exponential Platform DXP** is the Symfony-only platform of the family. "DXP 1.x" and "DXP 2.x" are its two project
skeletons:

| Name | Repository | Latest release | Generation | Symfony | PHP |
|---|---|---|---|---|---|
| DXP 1.x | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | `v1.0.0.2` | 4.6 (`exponential-platform-dxp 4.6.x-dev`) | 5.4 | `^7.4` to `^8.5` |
| DXP 2.x | `se7enxweb/exponential-platform-dxp-skeleton` | `v2.0.0.2` | v5 (`exponential-platform-dxp dev-master`) | 7.4 | `>=8.3` |

Both require the metapackage `se7enxweb/exponential-platform-dxp` (releases `0.0.0.1`, `v0.0.0.2`), whose kernel is
`se7enxweb/exponential-platform-dxp-core` (`v5.0.9` the newest release; `v4.6.x` tags for the 4.6 line). A
ready-to-run variant is `se7enxweb/exponential-platform-dxp-starter` (`1.0.0.0`).

[Migration hub](README.md) · [Checklist](checklist.md) · [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md) ·
[Platform package map](../../specifications/6.0/platform-package-map.md)

## Contents

- [1. What DXP contains, and what it does not](#1-what-dxp-contains-and-what-it-does-not)
- [2. From Exponential 6 to DXP](#2-from-exponential-6-to-dxp)
- [3. Content, templates and code](#3-content-templates-and-code)
- [4. From DXP to Exponential 6](#4-from-dxp-to-exponential-6)
- [5. Verify, roll back](#5-verify-roll-back)
- [6. Risks](#6-risks)
- [7. Checklist](#7-checklist)

## 1. What DXP contains, and what it does not

From the `composer.json` of `se7enxweb/exponential-platform-dxp` (`master`, read 2026-10-18):

| Contains | Does not contain |
|---|---|
| the core (`exponential-platform-dxp-core`), admin UI, content forms, REST, GraphQL with GraphiQL, HTTP cache, Solr and core search, RichText, Matrix and Query field types, notifications, messenger, cron, the standard design, Twig components, JWT authentication | the Exponential 6 legacy kernel and the legacy bridge |
| `se7enxweb/layouts-core` | the Layouts editor UI, the standard block set or the Ibexa integration of Layouts (Nexus has them) |
| | a workflow or scheduler package |
| | the legacy features that have no platform counterpart: shop, legacy workflows, collaboration, information collection, newsletters, RSS import/export, PDF export |

So a site that uses any of the right-hand column loses it on DXP, or needs a replacement. Check every feature against
the package list before you promise it.

## 2. From Exponential 6 to DXP

There is no converter that moves an Exponential 6 database straight to a v5 database. The route goes through the
shared content tables, one platform generation at a time, the way the vendor's own update path does:

1. **Exponential 6 to the 2.5 generation**: put the Symfony stack next to the legacy kernel with
   [Exponential Platform Legacy `v2.5.0.x`](exponential-platform-legacy.md#3-from-exponential-6-to-platform-legacy)
   (or [Nexus `1.0.0.x`](exponential-platform-nexus.md)). The content is now readable by the platform kernel.
2. **Convert XmlText to RichText** while the 2.5 generation is in place: `ezxmltext:convert-to-richtext` of
   `se7enxweb/ezplatform-xmltext-fieldtype` (branch `1.8`, `bundle/Command/ConvertXmlTextToRichTextCommand.php`).
   Later generations have no XmlText field type. Test the result on your most complex items (custom tags, embeds,
   tables) and fix the source where the converter reports errors.
3. **Users**: platform generations from 3.0 on accept only password hash types 6 and 7. Users with older types must
   reset their passwords, or sign in once while the 2.5 generation is running (the legacy kernel re-hashes on sign-in)
   ([17.7](../17-migration-reference.md#177-passwords-url-aliases-and-image-storage)).
4. **2.5 to 3.3**: the `upgrade/db/<engine>/` files on the `master` branch of `se7enxweb/exponential-platform`
   ([Platform Legacy, section 4](exponential-platform-legacy.md#4-moving-to-a-newer-line)).
5. **3.3 to 4.6**: the vendor's 3.3 to 4.0 database update and the configuration renames to `ibexa:`
   ([chapter 16.5.6](../16-migrating-from-ez-platform-and-ibexa.md#1656-the-database-generation-by-generation)).
   At this point you can **arrive on DXP 1.x**: create the `v4x` skeleton project, point it at the database without
   running the installer, and remove the legacy half.
6. **4.6 to v5**: the 4.6 to 5.0 update renames every core table to `ibexa_*`
   ([16.5.6, "The 5.0 table renames"](../16-migrating-from-ez-platform-and-ibexa.md#1656-the-database-generation-by-generation)).
   Then **arrive on DXP 2.x**.
7. **Leave the legacy-only tables** (shop, workflow and others) in an archive copy; the platform does not use them.

Each generation step is a project of its own: run it on a copy, verify, and only then take the next one. The vendor's
pages for each step are listed in [chapter 16.3](../16-migrating-from-ez-platform-and-ibexa.md#163-the-vendors-pages-and-where-this-chapter-covers-them).

An alternative for a small site, or for a site whose model you want to redesign anyway: **create a fresh DXP project
and import the content** through the platform's own APIs (a Symfony command that reads an xrowextract export, see
[Exponential's tools](exponential-tools.md#2-export-from-exponential)). It avoids four schema steps at the cost of
writing the importer and losing the version history.

## 3. Content, templates and code

| Exponential 6 | DXP |
|---|---|
| content classes, attributes | content types, fields (the same tables up to 4.6) |
| datatypes | field types; check every datatype in [17.3](../17-migration-reference.md#173-datatypes-and-field-types) |
| `ezxmltext` | `ezrichtext` / `ibexa_richtext` (converted, step 2) |
| TPL templates, overrides | Twig templates, content view configuration in YAML (rewritten by hand, [17.5](../17-migration-reference.md#175-templates-tpl-and-twig)) |
| INI settings | YAML configuration ([17.4](../17-migration-reference.md#174-settings-ini-and-yaml)) |
| extensions, modules | Symfony bundles, controllers |
| Exponential Layouts | none in DXP; rebuild the pages, or use Nexus for Netgen Layouts |
| URL aliases | the same aliases, regenerated by the platform command (`ibexa:urls:regenerate-aliases` upstream, [17.9](../17-migration-reference.md#179-migration-and-upgrade-tooling)) |
| REST v1/v2 of `ezprestapi` | REST v2 of the platform (different routes and media types); clients must be adapted |
| Velocity, SQLite, MongoDB, Oracle | a web server with PHP-FPM; MySQL/MariaDB or PostgreSQL (SQLite in the starters) |

## 4. From DXP to Exponential 6

This is "path B" of [chapter 16.6](../16-migrating-from-ez-platform-and-ibexa.md#166-path-b-down-to-the-exponential-60-legacy-kernel),
which is written for exactly this direction:

1. Inventory the field types in use; each needs a legacy datatype (16.6.3). RichText stays DocBook and is rendered as
   described there, or is converted to XmlText.
2. Database: on a v5 database reverse the `ibexa_*` renames (or use the translator extension), recreate the
   legacy-only tables with `ezsqldiff.php` (reference first), set the version rows, apply the Exponential 6 update
   files (16.6.4).
3. Configuration from YAML to INI (16.6.5), Twig to TPL (16.6.6).
4. Users keep their type 6/7 hashes, which Exponential 6 reads.
5. Serve with Velocity or PHP-FPM; redirect the platform-only routes (REST v2, admin paths).

If editors need the legacy admin while the Symfony site keeps running, use Exponential Platform Legacy of the same
generation instead (16.6.8).

## 5. Verify, roll back

- After each generation step: counts per content type and language, the admin, one edit and publish, an image
  variation, search reindexed (`exponential:reindex` on the Exponential lines), `doctrine:schema:validate`.
- Rich text: compare a sample of the most complex items, rendered, before and after the conversion.
- Rollback: the backup taken before each step. The platform's update files cannot be undone by another SQL file.

## 6. Risks

| Risk | Counter-measure |
|---|---|
| Features the site uses are not in DXP | check the package list ([1](#1-what-dxp-contains-and-what-it-does-not)) before the decision |
| XmlText conversion errors | convert on the 2.5 generation, test the worst items first |
| Old password hashes | resets or a sign-in while the legacy kernel still runs |
| Four schema steps in one go | one generation per project phase, each verified |
| Templates underestimated | Twig rewrite planned as its own work package |

## 7. Checklist

- [ ] Every feature in use checked against DXP's `composer.json`.
- [ ] Route chosen: through the generations, or a fresh project with an import.
- [ ] 2.5 generation in place (Platform Legacy or Nexus); XmlText converted and checked.
- [ ] Password hashes 6/7 for all active users.
- [ ] 2.5 to 3.3 to 4.6 (DXP 1.x) to v5 (DXP 2.x), each verified on a copy.
- [ ] Templates rewritten in Twig; REST clients adapted.
- [ ] Legacy-only tables archived; redirects for addresses that change.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
