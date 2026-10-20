---
title: Exponential 6 and Exponential Platform (Symfony only)
audience: admin, developer, project-manager
category: install-upgrade
order: 23
summary: Moving between Exponential 6 and Exponential Platform 3.2.9, the Symfony-only distribution based on the last free eZ Platform 3.2 - when it is the right target, the route through the 2.5 generation and the 2.5 to 3.0 database files, templates and code, the way back, verification, rollback and checklist.
keywords: Exponential Platform, 3.2.9, Symfony 5, ezplatform-kernel, Symfony only, no legacy, upgrade/db, ezplatform-2.5.latest-to-3.0.0.sql, migration
type: install
---
# Exponential 6 and Exponential Platform (Symfony only)

**Exponential Platform** (`se7enxweb/exponential-platform`, default branch `3.2`) is the Symfony-only distribution
"based on the last free versions of eZ Platform", in its own words, with "No Exponential 6.x Legacy Support". Its one
GitHub release is `v3.2.9` (2025-09-28). Its `composer.json` requires the 3.2 generation's forks
(`se7enxweb/ezplatform-kernel ~1.3.42`, `se7enxweb/ezplatform-admin-ui ~2.3.39`, REST, GraphQL, RichText, Matrix and
Query field types, Solr), Symfony 5 and PHP `^7.3 || ^8.1` to `^8.4`.

[Migration hub](README.md) · [Checklist](checklist.md) · Installation notes in the product's repository: `doc/en/us/INSTALL.md`

## Contents

- [1. Is it the right target?](#1-is-it-the-right-target)
- [2. From Exponential 6 to Exponential Platform 3.2](#2-from-exponential-6-to-exponential-platform-32)
- [3. From Exponential Platform 3.2 onward or back](#3-from-exponential-platform-32-onward-or-back)
- [4. Verify, roll back, checklist](#4-verify-roll-back-checklist)

## 1. Is it the right target?

The 3.2 generation is older than the 3.3 generation that the other Exponential products carry (Platform Legacy
`v3.3.44.x`, Nexus `1.1.0.x`, OSS `v3.3.0.x`). For new work, prefer:

| You want | Better target |
|---|---|
| Symfony only, current PHP and Symfony | [DXP 2.x](exponential-platform-dxp.md) (v5, Symfony 7.4) or DXP 1.x (4.6) |
| Symfony plus the legacy kernel | [Platform Legacy](exponential-platform-legacy.md) |
| A site with a page builder | [Nexus](exponential-platform-nexus.md) |

Choose Exponential Platform 3.2.9 when an existing project already runs on it or on eZ Platform 3.0 to 3.2, and you
want to keep that generation.

## 2. From Exponential 6 to Exponential Platform 3.2

The content tables are shared, but the 3.x schema dropped the legacy-only tables, and the platform reads no XmlText
and no legacy password types. The route:

1. **Arrive on the 2.5 generation** with the legacy kernel next to it:
   [Platform Legacy `v2.5.0.x`, section 3](exponential-platform-legacy.md#3-from-exponential-6-to-platform-legacy).
2. **Convert XmlText to RichText** there (`ezxmltext:convert-to-richtext`, [DXP page, step 2](exponential-platform-dxp.md#2-from-exponential-6-to-dxp)).
3. **Make every active user's hash type 6 or 7** (resets, or a sign-in on the legacy kernel, which re-hashes).
4. **Apply the 2.5 to 3.0 file** of this product: `upgrade/db/<engine>/ezplatform-2.5.latest-to-3.0.0.sql` (MySQL
   and PostgreSQL, branch `3.2`). The Platform Legacy book describes what it does: removes the
   `ezpublish-version` and `ezplatform-release` rows and inserts `ezplatform-release` `3.0.0`, widens
   `ezcontentclass_attribute.data_text1`, adds `is_thumbnail`, adds and fills `ezkeyword_attribute_link.version`.
   There are no files for 3.0 to 3.1 or 3.1 to 3.2. There is no SQLite file.
5. **Create the 3.2.9 project** (`composer create-project se7enxweb/exponential-platform:v3.2.9` or a Git clone of
   the tag), point `.env.local` (`DATABASE_URL`) at the migrated database, and do **not** run the installer on it.
6. **Move the storage** to the platform's `var_dir`/`storage_dir` below the web root, so that the paths stored in the
   image XML and in `ezimagefile` still point at files; the path schemes of both sides and the checks are in
   [17.7](../17-migration-reference.md#177-passwords-url-aliases-and-image-storage) and 17.8. Configure the image
   variations, rebuild the URL aliases and the search index.
7. **Rewrite the presentation**: TPL to Twig, INI to YAML, extensions to bundles
   ([17.4](../17-migration-reference.md#174-settings-ini-and-yaml), [17.5](../17-migration-reference.md#175-templates-tpl-and-twig)).
8. **Archive the legacy-only tables** you no longer use.

The vendor's pages for the 2.5 to 3.x step are listed in
[chapter 16.3](../16-migrating-from-ez-platform-and-ibexa.md#163-the-vendors-pages-and-where-this-chapter-covers-them).

## 3. From Exponential Platform 3.2 onward or back

| Direction | Route |
|---|---|
| 3.2 to 3.3 | `upgrade/db/<engine>/ezplatform-3.2.0-to-3.3.0.sql` (creates `ibexa_setting`) and `ezplatform-3.2.3-to-3.2.4.sql`, on the `master` branch; then Platform Legacy `v3.3.44.x`, Nexus `1.1.0.x` or OSS `v3.3.0.x` |
| 3.2 to DXP | 3.3 first, then the [DXP route](exponential-platform-dxp.md#2-from-exponential-6-to-dxp) from step 5 |
| 3.2 back to Exponential 6 | "path B" of [chapter 16.6](../16-migrating-from-ez-platform-and-ibexa.md#166-path-b-down-to-the-exponential-60-legacy-kernel): `ezsqldiff.php` (reference first) recreates the legacy-only tables, the version rows are set, RichText is rendered or converted (16.6.3), YAML becomes INI and Twig becomes TPL; the "3.0 to 3.3" row of 16.6.9 lists the details, including the default that `ezkeyword_attribute_link.version` needs for the legacy kernel's inserts |
| 3.2 next to the legacy kernel | arrive on 3.3 and use Platform Legacy `v3.3.44.x` |

## 4. Verify, roll back, checklist

Verify with counts per content type and language, the admin, one edit and publish, image variations, search,
`doctrine:schema:validate`. Roll back with the backup taken before each step; keep the old installation until the
new one has run in production.

- [ ] 3.2.9 confirmed as the right target (or a newer generation chosen).
- [ ] 2.5 generation reached through Platform Legacy; XmlText converted; hashes 6/7.
- [ ] `ezplatform-2.5.latest-to-3.0.0.sql` applied on a copy; errors read.
- [ ] Project from `v3.2.9`; installer not run on the data; storage moved; aliases and index rebuilt.
- [ ] Templates, configuration and code rewritten; legacy-only tables archived.
- [ ] Redirects for every address that changed.

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
