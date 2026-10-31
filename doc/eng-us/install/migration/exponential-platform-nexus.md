---
title: Exponential 6 and Exponential Platform Nexus
audience: admin, developer, project-manager
category: install-upgrade
order: 21
summary: Moving an Exponential 6 site into Exponential Platform Nexus (lines 1.0.0.x and v2.5.0.x on Platform 2.5, 1.1.0.x on 3.3, 1.2.0.x on 4.6, 1.3.0.x on v5) and back - which line, the database, the demo site you do not want, Exponential Layouts on Nexus against the legacy kernel, verification, rollback and checklist.
keywords: Exponential Platform Nexus, Nexus, media site, site bundle, 1.0.0.x, 1.1.0.x, 1.2.0.x, 1.3.0.x, v2.5.0.x, CJW Nexus, Exponential Layouts, nglayouts:export, exp:layouts, migration
type: install
---
# Exponential 6 and Exponential Platform Nexus

**Exponential Platform Nexus** (`se7enxweb/exponential-platform-nexus`) is a complete site distribution: Exponential
Layouts with its visual editor, the Netgen media site, Tags, the Site API and an admin, on one Exponential Platform
generation per release line. Its legacy-capable lines also run the Exponential 6 kernel through the legacy bridge.

[Migration hub](README.md) · [Checklist](checklist.md) · The product's own book:
[Nexus, chapter 12: Migrating into Nexus](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/12-migrating-into.md) and
[chapter 11: Upgrading between lines](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/11-upgrading-between-lines.md)

## Contents

- [1. The release lines](#1-the-release-lines)
- [2. Which line to aim for](#2-which-line-to-aim-for)
- [3. From Exponential 6 to Nexus](#3-from-exponential-6-to-nexus)
- [4. Page building: Exponential Layouts on Nexus and in the legacy kernel](#4-page-building-exponential-layouts-on-nexus-and-in-the-legacy-kernel)
- [5. From Nexus to Exponential 6](#5-from-nexus-to-exponential-6)
- [6. Verify, roll back](#6-verify-roll-back)
- [7. Risks](#7-risks)
- [8. Checklist](#8-checklist)

## 1. The release lines

"Nexus 2.5", "Nexus 4.x" and "Nexus 5.x" are the platform generations inside Nexus, not its version numbers. Read
from the release tags' `composer.json` on 2026-10-18:

| Nexus line | Latest release | Generation | Symfony | PHP | Legacy kernel |
|---|---|---|---|---|---|
| `1.0.0.x` | `1.0.0.11` | eZ Platform 2.5 kernel fork (`~7.5.40`) | 3.4 | `^7.1.3` to `^8.6` | yes: `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| `v2.5.0.x` (branch `master`) | `v2.5.0.7` | 2.5 | 3.4 | as 1.0.0.x | yes |
| `1.1.0.x` | `v1.1.0.8` | 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | yes: `legacy-bridge ^3.0`, `site-legacy-bundle ^2.0` |
| `1.2.0.x` | `v1.2.0.1` | 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | yes: `site-legacy-bundle v2.0.0` with the legacy installer |
| `1.3.0.x` | `1.3.0.6` | v5 (`exponential-platform-dxp dev-master`) | 7.4 | `>=8.4` | **no** |

Related products: **CJW Exponential Platform Nexus** (`se7enxweb/cjw-exponential-platform-nexus`, `1.0.0.12`, the 2.5
generation with Exponential 6 and vendor files included), its starter (`1.0.0.3`), and the **Nexus Starter**
(`se7enxweb/exponential-platform-nexus-starter`, `1.0.0.0`, Platform v5, no legacy kernel in its `composer.json`).

> **Warning**
> Always ask Composer or Git for an explicit Nexus version. The repository keeps tags of the Netgen media site it was
> forked from, so an unversioned `composer create-project` can install one of those instead of Nexus
> ([chapter 16.1](../16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations)).

## 2. Which line to aim for

The product's rule is **same generation first**. An Exponential 6 database is a legacy database, so:

| You have | First target | Then |
|---|---|---|
| Exponential 6 | `1.0.0.x` (2.5): it runs your Exponential 6 kernel next to the Symfony stack on the same database | move up line by line with the product's chapter 11 |
| Exponential Platform Legacy `v2.5.0.x`, `v3.3.44.x`, `v4.6.23.x` | `1.0.0.x`, `1.1.0.x`, `1.2.0.x` | add the Netgen packages, the Layouts tables and the site bundle's configuration |
| Exponential Platform Legacy `v5.0.x` | `1.3.0.x` has no legacy kernel | port what the legacy half did first, or stay on Platform Legacy and add Exponential Layouts yourself |

Lines `1.0.0.x` to `1.2.0.x` keep the legacy admin and your legacy extensions working while you port. `1.3.0.x` is a
Symfony-only application: the product's book lists what disappears (the legacy bundles, `ezpublish_legacy/`, the
legacy cron, the `legacy_admin` and `ngadminui` siteaccesses).

## 3. From Exponential 6 to Nexus

The product's chapter 12.4 ("From eZ Publish 4.x and Exponential 6") is the procedure; in short:

1. **Inventory, freeze, back up** ([checklist](checklist.md)).
2. **Bring the database to the newest Exponential 6 schema** ([chapter 11](../11-upgrading.md)), and check it with
   `ezsqldiff.php`, the reference schema first and your database second.
3. **Add the tables the 2.5 kernel expects** that a legacy-only database lacks: compare with `data/mysql/schema.sql`
   of the installed `se7enxweb/ezpublish-kernel` 7.5 and create the missing ones
   ([chapter 16.6.1](../16-migrating-from-ez-platform-and-ibexa.md#1661-what-the-two-share)).
4. **Create the Nexus `1.0.0.x` project from a release tag** (for example `git clone -b 1.0.0.x ...` and
   `git checkout 1.0.0.11`, then `composer install`). **Do not run the installer** against your database: it builds a
   repository from scratch. The demo data package can stay installed; it is only read by the installer.
5. **Put your legacy extensions and siteaccess settings** where the project keeps them
   (`src/AppBundle/ezpublish_legacy/`, linked into `ezpublish_legacy/` by the project's scripts), copy the storage to
   `ezpublish_legacy/var/<site>/storage`, and point `app/config/parameters.yml` at the database.
6. **Replace the demo host map** in the siteaccess configuration with your own host names.
7. **Create the Exponential Layouts tables** (the Layouts migrations on MySQL, or the schema file the Layouts package ships)
   and set the **site bundle's location parameters** (`ngsite.default.locations.site_info.id`,
   `ngsite.default.locations.tree_root.id` and the others the product's chapter 12.9 lists) to locations in *your*
   tree. A wrong id gives a 404 or an empty block, not an error at deploy time.
8. **Keep the legacy siteaccesses running** (`legacy_mode: true`) while the public siteaccess moves to Twig templates
   and Exponential Layouts one content type at a time.
9. **XmlText stays XmlText on `1.0.0.x`** (`se7enxweb/ezplatform-xmltext-fieldtype v1.8.11` is part of the project).
   Convert to RichText with `ezxmltext:convert-to-richtext` (in that package) only when you need it in the Symfony
   admin, at the latest before moving to `1.1.0.x`.
10. **Passwords**: the 2.5 kernel accepts the legacy hash types; from 3.0 on only types 6 and 7 work. Ask users with
    older types to reset before you move to `1.1.0.x`.
11. **Build** caches, assets and the search index, verify, and rehearse on a fresh copy for the cut-over.

Your own content types render with your own templates: map them in the view configuration instead of renaming them
to the demo `ng_*` types.

## 4. Page building: Exponential Layouts on Nexus and in the legacy kernel

Exponential Layouts (`se7enxweb/explayouts`) is the port of the upstream layouts packages to the Exponential 6 kernel: the same data
shape on `explayouts_*` tables, the editor as a legacy module ([chapter 16.6.7](../16-migrating-from-ez-platform-and-ibexa.md#1667-a-worked-port-exponential-layouts-and-the-media-site)).
They are two implementations; there is no command that copies one into the other.

| Direction | What exists | What you do |
|---|---|---|
| Between Exponential installations | `./console exp:layouts export` / `import` (ZIP transfer file) | the normal tool |
| Between Nexus installations | `php bin/console nglayouts:export layout <uuid>` / `nglayouts:import` (JSON) | the product's chapter 12.11 |
| Legacy kernel (`explayouts_*`) to Nexus (`nglayouts_*`) | no converter | rebuild the layouts in the Nexus editor, or write a script from the `explayouts_*` rows to the `nglayouts_*` tables (the port follows the Netgen data shape, chapter 16.6.7) |
| Nexus (`nglayouts_*`) to legacy kernel (`explayouts_*`) | the explayouts commands `ext:explayouts:updatelinklocationreferences` (block links from `ibexa-location://<id>` to `exp-remote-location://<remote id>`) and `ext:explayouts:updatecomponentblockidentifiers` (`ibexa_component_<type>` to `exp_component_<type>`) convert data that came from the Symfony side | copy the rows (a script of your own: table and column names differ), then run both commands |

Layouts refer to content by id or remote id. Keep remote ids identical when content moves between installations, or
the blocks point at nothing (the product's chapter 12.11).

## 5. From Nexus to Exponential 6

Moving from a Nexus line back to Exponential 6 alone is "path B" of
[chapter 16.6](../16-migrating-from-ez-platform-and-ibexa.md#166-path-b-down-to-the-exponential-60-legacy-kernel):

- **From `1.0.0.x` to `1.2.0.x`**: the legacy kernel is already there. Install Exponential 6, move the legacy settings,
  extensions and storage over, apply the Exponential 6 update files, and serve it with Velocity or PHP-FPM. The
  Platform Legacy page has the same steps ([section 5](exponential-platform-legacy.md#5-from-platform-legacy-back-to-exponential-6-alone)).
- **From `1.3.0.x`**: the database has the v5 `ibexa_*` table names and no legacy-only tables. Follow chapter 16.6.4
  (the renames, or the translator extension) and 16.6.9 ("5.0" row), recreate the legacy-only tables, and convert or
  render RichText ([16.6.3](../16-migrating-from-ez-platform-and-ibexa.md#1663-field-types)).
- **Layouts**: rebuild the Nexus layouts in the legacy kernel, or copy the rows and run the two conversion commands
  of [section 4](#4-page-building-exponential-layouts-on-nexus-and-in-the-legacy-kernel). The media design exists for the legacy
  kernel as `se7enxweb/sevenx_themes_media`.
- **Twig templates** become TPL by hand; a converter gets the syntax, not the semantics (16.6.7).

## 6. Verify, roll back

Verify as the product's book says: counts before and after, the front page and one page per content type, a
layout-managed page, the Layouts editor, an image variation, search, forms, sign-in of an editor and of a user with an
old password hash. Roll back by keeping the source untouched until the cut-over has settled; all work is done on
copies.

## 7. Risks

| Risk | Counter-measure |
|---|---|
| The demo installer run on your data | never run it on a migrated database |
| Demo location ids in configuration | grep the configuration for every location id before go-live |
| An unversioned install picks an upstream media-site tag | always name the version |
| Moving to `1.3.0.x` too early | the legacy kernel and its admin are gone there; port first |
| Layouts pointing at missing content | keep remote ids; run the import with `--mode=skip` first |
| Old password hash types | resets before 3.0 and later lines |

## 8. Checklist

- [ ] Line chosen by generation (`1.0.0.x` from Exponential 6); explicit version.
- [ ] Database on the newest Exponential 6 schema; 2.5 tables added; `ezsqldiff.php` clean.
- [ ] Project from a release tag; installer **not** run on the data; demo host map replaced.
- [ ] Legacy extensions, settings and storage in place; legacy siteaccesses on `legacy_mode`.
- [ ] Layouts tables present; site bundle location parameters point at your tree.
- [ ] Page structures rebuilt as layouts and rules; remote ids kept.
- [ ] XmlText strategy decided; password resets planned before 3.0.
- [ ] Verified on a fresh copy during the freeze; rollback agreed.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
