---
title: Exponential 6 and Exponential Platform Legacy
audience: admin, developer, project-manager
category: install-upgrade
order: 20
summary: Moving an Exponential 6 site into Exponential Platform Legacy (v2.5.0.x, v3.3.44.x, v4.6.23.x, v5.0.x), where the legacy kernel and a Symfony stack share one database, and back to Exponential 6 alone - target line, database steps, configuration, verification, rollback and checklist.
keywords: Exponential Platform Legacy, legacy bridge, hybrid, dual kernel, 2.5, 3.3, 4.6, v5, ezpublish_legacy, legacy_mode, Symfony, migration, translator
type: install
---
# Exponential 6 and Exponential Platform Legacy

**Exponential Platform Legacy** (`se7enxweb/exponential-platform-legacy`) runs the Exponential 6 kernel and a Symfony
platform on one database, joined by `se7enxweb/legacy-bridge`. It is the gentlest way to give an Exponential 6 site
the Symfony stack: the content, the legacy admin, the TPL templates and the extensions keep working while Twig
templates, REST v2 and the platform admin are added. And because nothing of the legacy side is removed, the way back
is short.

[Migration hub](README.md) · [Checklist](checklist.md) · The product's own book:
[Exponential Platform Legacy, chapter 11: Migrating into Exponential Platform Legacy](https://github.com/se7enxweb/exponential-platform-legacy/blob/5.x/doc/book/11-migrating-into.md)

## Contents

- [1. The release lines](#1-the-release-lines)
- [2. Is it the right target?](#2-is-it-the-right-target)
- [3. From Exponential 6 to Platform Legacy](#3-from-exponential-6-to-platform-legacy)
- [4. Moving to a newer line](#4-moving-to-a-newer-line)
- [5. From Platform Legacy back to Exponential 6 alone](#5-from-platform-legacy-back-to-exponential-6-alone)
- [6. Verify](#6-verify)
- [7. Rollback](#7-rollback)
- [8. Risks](#8-risks)
- [9. Checklist](#9-checklist)

## 1. The release lines

Read from the `composer.json` of each release tag on 2026-10-18 ([hub, 1.2](README.md#12-the-release-lines-of-nexus-and-platform-legacy)):

| Line | Latest release | Generation | Symfony | PHP | Bridge |
|---|---|---|---|---|---|
| `v2.5.0.x` | `v2.5.0.4` | eZ Platform 2.5 kernel fork (`~7.5.33`) | 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `^2.1`, with `se7enxweb/exponential ^6.0.12` |
| `v3.3.44.x` | `v3.3.44.8` | 3.3 (`se7enxweb/oss ~3.3.0.0`) | 5.4 | `^8.0` | `^3.0.0.35` |
| `v4.6.23.x` | `v4.6.23.3` | 4.6 (`exponential-platform-dxp 4.6.x-LB-dev`) | 5.4 | `^7.4` to `^8.5` | `^4.0.0.0` |
| `v5.0.x` | `v5.0.3.1` | v5 (`exponential-platform-dxp dev-5.x-LB`) | 7.4 | `>=8.3` | `^5.0.0.0` |

> **Warning**
> `v5.0.3` holds the 2.5 line's `composer.json`. For v5 ask for `~5.0.3.1` (the product's README does so:
> `composer create-project se7enxweb/exponential-platform-legacy:~5.0.3.1`), never a bare `^5.0`.

A ready-to-run variant of the v5 line is `se7enxweb/exponential-platform-legacy-starter` (`1.0.0.0`).

## 2. Is it the right target?

| You want | Platform Legacy is right? |
|---|---|
| Symfony controllers, Twig, REST v2, GraphQL **next to** the legacy admin and TPL pages | Yes |
| To port the public site to Twig page by page, without a big-bang cut-over | Yes |
| Only the legacy kernel, served by Velocity or PHP-FPM | No: stay on Exponential 6 alone. The product's book says the hybrid "pays for itself only when you want Symfony controllers, REST v2, GraphQL or the platform admin" |
| A visual page builder on the Symfony side | Look at [Nexus](exponential-platform-nexus.md), which adds Netgen Layouts |
| A Symfony-only project without the legacy kernel | [DXP](exponential-platform-dxp.md) |

Things that change for the site when the Symfony stack serves it:

- **Symfony serves the requests**; the legacy siteaccesses are reached through the bridge (`legacy_mode: true`).
  Velocity, which serves Exponential 6 alone, is not the server of a Platform Legacy project.
- **Database credentials live only in Symfony's configuration**; the bridge injects them into the legacy kernel.
- **Image aliases come from the YAML image variations**; legacy-only aliases in `image.ini` must be declared there.
- **Sign-in on non-legacy siteaccesses is Symfony's.** Both kernels read the legacy password hashes, but platform
  generations from 3.0 on accept only types 6 and 7 ([17.7](../17-migration-reference.md#177-passwords-url-aliases-and-image-storage)).
- **Database engines**: the platform side supports MySQL/MariaDB and PostgreSQL; SQLite is offered for development
  in the v5 quick start. MongoDB and Oracle, which Exponential 6 supports, are not platform engines: move such a site
  to MySQL, MariaDB or PostgreSQL first ([Databases](../09-databases.md)).

## 3. From Exponential 6 to Platform Legacy

An Exponential 6 database is a legacy database that is already past the 5.4 step of the old chain, so you follow the
product's chapter 11.3 without its legacy update chain.

**Arrive on the 2.5 line first.** The product's book: the 2.5 platform schema "is the complete legacy schema", so
nothing the legacy site uses is missing; move up line by line afterwards ([4](#4-moving-to-a-newer-line)).

1. **Freeze and back up** the database and `var/<site>/storage`. Restore the backup once on a scratch machine.
2. **Bring Exponential 6 to its newest release** ([chapter 11.6](../11-upgrading.md#116-from-any-60x-to-today)) and
   confirm the version rows ([11.2](../11-upgrading.md#112-which-version-do-you-run)).
3. **On MySQL, convert the tables to `utf8mb4`.** The 2.5 platform kernel creates `utf8mb4` tables and the
   distribution's legacy override talks `utf8mb4` to the server; the product's chapter 11.3 gives the query that
   prints the `ALTER TABLE ... CONVERT TO CHARACTER SET utf8mb4` statements to review and run.
4. **Apply the 2.5 platform kernel's update files** from `vendor/se7enxweb/ezpublish-kernel/data/update/<engine>/`, in
   the order the product's chapter 11.3 step 3 lists. On an Exponential 6 database most of what they do (the wider
   `password_hash`, `ezcontentobject_trash.trashed`, the PostgreSQL sequence names) is already there; the product's
   chapter 10.7 explains which statements are safe to repeat. Run them on the copy and read every error: a
   "duplicate column" means the change exists, anything else stops the migration.
5. **Add the tables the 2.5 kernel expects** that a legacy-only database lacks (for example
   `ezcontentclass_attribute_ml`, `eznotification`): compare your table list with `data/mysql/schema.sql` of the
   installed `se7enxweb/ezpublish-kernel` and create the missing ones from that file
   ([chapter 16.6.1](../16-migrating-from-ez-platform-and-ibexa.md#1661-what-the-two-share) has the comparison).
6. **Create the 2.5 project** with an explicit version, and point it at the migrated database **without running the
   installer**: the product's book warns that `ezplatform:install` / `exponential:install` drop and recreate the tables
   they know.
7. **Move the storage** to `ezpublish_legacy/var/<site>/storage` (or the directory your `VarDir` names).
8. **Move the legacy configuration**: `settings/override/` into `ezpublish_legacy/settings/override/`, the siteaccess
   directories into `ezpublish_legacy/settings/siteaccess/`, and remove the `[DatabaseSettings]` connection values.
9. **Declare the siteaccesses in YAML** with the same names; the legacy ones get `legacy_mode: true`. Declare every
   image alias the legacy designs use as an image variation.
10. **Move your extensions** into the project and activate them; regenerate the autoloads through the bridge
    (`php bin/console ezpublish:legacy:script bin/php/ezpgenerateautoloads.php`).
11. **Verify the schema** with `ezsqldiff.php`, the reference schema first and your database second (the product's
    chapter 11.3 step 10 and [the Nexus book, 12.4](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/12-migrating-into.md#124-from-ez-publish-4x-and-exponential-6)
    explain the argument order). `DROP` lines for platform-only tables are expected.
12. **Run the legacy cron through the bridge**, clear the caches of both sides, reindex, and verify ([6](#6-verify)).

What does not need to move: the content, the classes, the users, roles, sections, states, URL aliases and the
Exponential Layouts data stay in the shared database and are read by the legacy side as before.

## 4. Moving to a newer line

Move one line at a time, with the product's chapter 10
([Upgrading between release lines](https://github.com/se7enxweb/exponential-platform-legacy/blob/5.x/doc/book/10-upgrading-between-lines.md)):

| Step | Database | Notes from the product's book |
|---|---|---|
| 2.5 to 3.3 | `upgrade/db/<engine>/ezplatform-2.5.latest-to-3.0.0.sql`, `ezplatform-3.2.0-to-3.3.0.sql`, `ezplatform-3.2.3-to-3.2.4.sql` (on the `master` branch of `se7enxweb/exponential-platform`) | none of the files drops a table, so the legacy-only tables survive; the first file deletes the `ezpublish-version` row, which must be put back for the legacy update files. There are no upstream files for SQLite |
| 3.3 to 4.6, 4.6 to v5 | the product's chapter 10.6 | on 4.6 create the legacy-only tables first; on a v5 database the translator extension (`se7enxweb/sevenx_exponential_platform_v5_database_translator`) must be the first entry of `ActiveExtensions[]`, because v5 renames the core tables to `ibexa_*` |

Every line change also changes the project layout and configuration (for example `app/config` to `config/`, `web/` to
`public/` from 2.5 to 3.x); the product's chapter lists them.

## 5. From Platform Legacy back to Exponential 6 alone

The legacy half is a complete Exponential 6 installation; going back means letting it serve the site again.

1. **Back up** the shared database and storage.
2. **Decide what happens to Symfony-only content.** RichText fields, Netgen Layouts or other platform-only data have
   no legacy reading of their own: list the field types in use and check them against
   [chapter 16.6.3](../16-migrating-from-ez-platform-and-ibexa.md#1663-field-types).
3. **On a v5 database**, reverse the `ibexa_*` table renames (or keep the translator extension active)
   ([chapter 16.6.4](../16-migrating-from-ez-platform-and-ibexa.md#1664-the-database-step-by-step) step 2).
4. **Check the schema** against Exponential 6 with `ezsqldiff.php` (reference first) and apply the missing tables and
   columns; then apply the current `update/database/<engine>/6.0/` files as [chapter 11.6](../11-upgrading.md#116-from-any-60x-to-today)
   says.
5. **Install Exponential 6** (a Git clone with history is recommended, [chapter 3](../03-getting-the-code.md)), copy
   `ezpublish_legacy/settings/override/`, `settings/siteaccess/`, the extensions and the storage into it, and put the
   database connection back into `settings/override/site.ini.append.php`.
6. **Serve it** with Velocity or Apache/nginx with PHP-FPM ([chapter 8](../08-serving-the-site.md)); the addresses of
   the legacy siteaccesses stay the same; addresses that Symfony routes served (REST v2, Twig-only pages) need
   redirects or a replacement.
7. **Passwords**: users who signed in on the Symfony side have type 7 hashes, which Exponential 6 reads.

## 6. Verify

- Counts of objects per class and language, users, URL aliases before and after.
- The front page, one page per class, both admins: edit and publish one item from each.
- An image variation, an upload, the search, the legacy cron parts run by hand.
- `var/log` of Symfony and `ezpublish_legacy/var/log/error.log` are clean.
- The product's book has the commands per line (chapter 10.8).

## 7. Rollback

The platform's update files alter rows and add tables but cannot be undone by another SQL file. Rollback is the
backup from step 1, plus the old Exponential 6 installation, kept untouched until the new one has run in production
for a while.

## 8. Risks

| Risk | Counter-measure |
|---|---|
| The installer run against the migrated database | never run it; the product's book repeats this warning in every chapter |
| Two configurations drift apart | database settings only in Symfony; one list of siteaccess names |
| Image aliases missing on the Symfony side | declare them as variations before the first request |
| MongoDB or Oracle site | move to MySQL, MariaDB or PostgreSQL first |
| Old password hash types | ask users to reset before moving to 3.3 or later |
| A v5 database read by a legacy kernel without the translator | translator first in `ActiveExtensions[]` |

## 9. Checklist

- [ ] Line chosen (arrive on 2.5, move up later); version given explicitly.
- [ ] Exponential 6 at its newest release; backup restored once.
- [ ] MySQL tables on `utf8mb4`; 2.5 kernel update files applied and their errors read.
- [ ] Missing 2.5 tables created; `ezsqldiff.php` (reference first) shows no missing table or column.
- [ ] Project created, installer **not** run on the data; storage moved.
- [ ] Legacy settings moved, connection values removed; siteaccesses in YAML with `legacy_mode`.
- [ ] Image aliases declared as variations; extensions moved and autoloads regenerated.
- [ ] Caches cleared on both sides, search reindexed, cron through the bridge.
- [ ] Front end and both admins verified; rollback plan agreed.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
