# 16. Migrating from eZ Platform and Ibexa

This chapter moves a site that runs on eZ Platform 1.x, 2.x or 3.x, or on Ibexa DXP or Ibexa OSS 3.3, 4.x or 5.0, to
one of the Exponential platforms. There are two destinations. **Exponential Platform Nexus** (and the other
Exponential Platform distributions) is the like-for-like path: the same Symfony stack, the same database schema
generation and the same PHP namespaces, with the packages swapped for the se7enxweb forks. **Exponential 6.0**, the
legacy kernel this repository contains, is the path down: the content moves to the INI and TPL world, either on its
own or inside **Exponential Platform Legacy**, where the legacy kernel and a Symfony stack share one database. The
chapter starts with what each destination is and a decision table, then gives each path step by step: packages,
namespaces, configuration, database per version (with the SQL), field types, templates, search, caches, users,
images and URL aliases, the porting of custom bundles, and a real port from the Symfony stack to the legacy kernel. It
ends with the common issues, verification, rollback, a plan and a checklist. It covers every point of the vendor's
own migration and update pages and says where the Exponential code differs from them.

[Contents](README.md) · Previous: [15. Migrating from the 5.x legacy stack](15-migrating-from-5x-legacy.md) · Next: [17. Migration reference](17-migration-reference.md)

## Contents

- [16.1 The two destinations](#161-the-two-destinations)
- [16.2 Choosing a path](#162-choosing-a-path)
- [16.3 The vendor's pages and where this chapter covers them](#163-the-vendors-pages-and-where-this-chapter-covers-them)
- [16.4 Before you start: inventory, freeze and backups](#164-before-you-start-inventory-freeze-and-backups)
- [16.5 Path A: to Exponential Platform Nexus](#165-path-a-to-exponential-platform-nexus)
- [16.6 Path B: down to the Exponential 6.0 legacy kernel](#166-path-b-down-to-the-exponential-60-legacy-kernel)
- [16.7 Common issues](#167-common-issues)
- [16.8 Verify the migration](#168-verify-the-migration)
- [16.9 Rollback](#169-rollback)
- [16.10 A plan and its phases](#1610-a-plan-and-its-phases)
- [16.11 Checklist](#1611-checklist)
- [References](#references)

Conventions: commands for a Symfony project run from its project root (`php bin/console ...`); commands for the
legacy kernel run from the legacy root (`php bin/php/...`), which is the installation root of Exponential 6.0 or the
`ezpublish_legacy/` directory of a Symfony project. `USER`, `DATABASE` and the like are placeholders. Old product
names appear only where they name the system you migrate from.

## 16.1 The two destinations

### What you migrate from

| Source | Symfony | Kernel package | Main config key | PHP namespace of the API |
|---|---|---|---|---|
| eZ Platform 1.x (1.7 LTS, 1.13 LTS) | 2.8 / 3.4 | `ezsystems/ezpublish-kernel` 6.x | `ezpublish:` | `eZ\Publish\API\...` |
| eZ Platform 2.x (2.5 LTS) | 3.4 | `ezsystems/ezpublish-kernel` 7.5 | `ezpublish:` | `eZ\Publish\API\...` |
| eZ Platform 3.0 to 3.2, Ibexa DXP / OSS 3.3 LTS | 5.x (5.4 from 3.3.13) | `ezsystems/ezplatform-kernel` 1.x | `ezplatform:` | `eZ\Publish\API\...` |
| Ibexa DXP / OSS 4.0 to 4.6 LTS | 5.4 | `ibexa/core` 4.x | `ibexa:` | `Ibexa\Contracts\Core\...` |
| Ibexa DXP / OSS 5.0 | 7.3, later 7.4 | `ibexa/core` 5.x | `ibexa:` | `Ibexa\Contracts\Core\...` |

The database schema changed three times along that line: eZ Platform 3.0 dropped about 80 tables of the legacy
schema, 4.0 kept the `ez*` table names and added `ibexa_*` tables, and 5.0 renamed every core table to `ibexa_*`
(sections [16.5.6](#1656-the-database-generation-by-generation) and [16.6.1](#1661-what-the-two-share)).

### Target A: Exponential Platform Nexus and the Exponential Platform distributions

**Exponential Platform Nexus** (repository and package `se7enxweb/exponential-platform-nexus`) is a ready site
distribution maintained by 7x: Netgen Layouts, the Netgen media site design, Netgen Tags, the Netgen Site API and an
administration interface on top of an Ibexa-compatible Symfony platform whose packages are se7enxweb forks. Its GitHub
description calls it "Platform v2/v3/v4/v5 + Exponential v6.x (Legacy) + Netgen Media Site + NG Layouts + Symfony
7.4/5.4/3.4 PHP 8.0-8.5". It is not one stack but one line per platform generation, each on its own branch and tag
series; read from the `composer.json` of each release tag:

| Nexus line (tags) | Platform generation | Symfony | PHP (`composer.json`) | Legacy kernel included |
|---|---|---|---|---|
| `1.0.0.x` (`v1.0.0.0.1` to `1.0.0.10`) | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`) | 3.4 (`se7enxweb/symfony v3.4.55`) | `^7.1.3 \|\| ... \|\| ^8.6` | yes: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1` |
| `1.1.0.x` (`v1.1.0.0` to `v1.1.0.7`) | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | yes: `se7enxweb/legacy-bridge ^3.0`, `se7enxweb/site-legacy-bundle ^2.0` |
| `1.2.0.x` (`v1.2.0.0`, "Ibexa 4.6 / v4 first release") | Ibexa OSS 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | yes: `se7enxweb/site-legacy-bundle v2.0.0` pulls in `se7enxweb/ibexa-legacy-bridge` 4.x and `se7enxweb/exponential` (`dev-main` in the lock) |
| `1.3.0.x` (`1.3.0.0.0`, `1.3.0.1` to `1.3.0.5`, the newest release) | Platform v5 (`se7enxweb/exponential-platform-dxp`, core `v5.0.7` in the lock) | 7.4 | `>=8.4` | no |
| `v2.5.0.x` (Nexus repository tags `v2.5.0.0` to `v2.5.0.6`; `v2.5.0.0` and `v2.5.0.1` still name the package `se7enxweb/exponential-platform-legacy`) | the 2.5 line of Exponential Platform Legacy (whose own 2.5 tags are `v2.5.0.0` to `v2.5.0.3`), packaged as Nexus | 3.4 | as 1.0.0.x | yes |

Two traps when you install a Nexus line with Composer. **Five-part tags are not on Packagist**: `v1.0.0.0.1` to
`v1.0.0.0.3` and `1.3.0.0.0` exist only in git, so ask for `1.0.0.10`, `v1.1.0.7`, `v1.2.0.0` or `1.3.0.5`. And
**always name a version**: the repository is a fork of the Netgen media site and keeps its tags, which Packagist lists
under the Nexus name, so `composer create-project se7enxweb/exponential-platform-nexus` without one installs `3.1.6`,
the upstream `netgen/media-site`. The default branch `master` is the 2.5 line; clone with `--branch 1.3.0.x` (or
the line you want).

The Nexus 5 line also exists as a separate starter, `se7enxweb/exponential-platform-nexus-starter`, which is what the
reference installation behind this book runs (SQLite, Symfony 7.4, `se7enxweb/exponential-platform-dxp-core v5.0.7`).
Next to Nexus there are plain distributions without the demo site: `se7enxweb/exponential-platform` (3.2.9, "runs the
Exponential Platform kernel on Symfony 5.1/5.4 LTS (full-stack, single kernel — no LegacyBridge)"),
`se7enxweb/oss` (the 3.3 metapackage, `replace: ibexa/oss`), `se7enxweb/exponential-platform-v4x-dxp-skeleton`
(4.6.x, PHP 7.4 to 8.5, Symfony 5.4), and `se7enxweb/exponential-platform-dxp-skeleton` with the metapackage
`se7enxweb/exponential-platform-dxp` (Platform v5, PHP 8.3 or newer, Symfony 7.4). The complete package list is in the
[Platform package map](../specifications/6.0/platform-package-map.md); the Nexus lines are introduced in
[Exponential Platform Nexus](../features/6.0/platform-nexus-starter.md) and the skeletons in the
[DXP skeleton](../features/6.0/platform-dxp-skeleton.md) page.

Three facts decide how a migration to these targets works:

1. **The forks replace the upstream names.** Every fork declares the upstream package under `replace` (with `*`
   since 2026-04-12), so Composer installs only the fork, and third-party packages that require `ibexa/*` or
   `ezsystems/*` names keep resolving. The kernel of Platform v5, `se7enxweb/exponential-platform-dxp-core`, declares
   `"replace": {"ibexa/core": "*"}`; the v5 admin `se7enxweb/admin-ui` replaces `ibexa/admin-ui` and
   `ezsystems/ezplatform-admin-ui`.
2. **The PHP namespaces stay upstream.** The forks keep `Ibexa\...` (4.x, 5.x) and `eZ\...` / `EzSystems\...` (2.5,
   3.3) namespaces; `se7enxweb/admin-ui` autoloads `Ibexa\AdminUi\`, the v5 bundles are registered as
   `Ibexa\Bundle\Core\IbexaCoreBundle` and so on. Custom code written for the same generation runs unchanged. What
   changed are console command names, branding, licence wording and the database installers
   ([Package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md)).
3. **Nexus is built on the open source edition.** Its `config/bundles.php` (Nexus 5) registers the Ibexa core,
   legacy search engine, Solr, HTTP cache, REST, GraphQL, admin UI, RichText, Matrix, Query field type, cron,
   messenger and design engine bundles, Netgen Layouts and about 25 other Netgen bundles; there is no Page Builder,
   Form Builder, editorial workflow, Elasticsearch, personalisation or commerce bundle. Content of the commercial
   field types needs a decision before it can move ([16.5.9](#1659-page-building-page-builder-against-netgen-layouts)).

### Target B: Exponential 6.0, alone or as Exponential Platform Legacy

**Exponential 6.0** is the legacy kernel in this repository: Composer package `se7enxweb/exponential`, type
`ezpublish-legacy`, `lib/version.php` 6.0.15, PHP 8.0 to 8.5, five database engines, INI settings, TPL templates,
the legacy administration interface and Exponential Velocity as its own web server ([chapter 1](01-introduction.md)).

**Exponential Platform Legacy** (`se7enxweb/exponential-platform-legacy`) is the hybrid: the README of its 2.5 line
calls it "a hybrid-kernel ... DXP/CMS built on the Exponential (Legacy) 6.x kernel bridged to Symfony 3.4 LTS via
LegacyBridge 2.x". The legacy kernel is a Composer dependency (`se7enxweb/exponential ^6.0.12`), installed into
`ezpublish_legacy/` by `se7enxweb/exponential-legacy-installer`; the Symfony stack and the legacy kernel share one
database through `se7enxweb/legacy-bridge`. It has one line per platform generation:

| Release line | Platform | PHP (`composer.json`) | Bridge |
|---|---|---|---|
| `v2.5.0.x` (`v2.5.0.0` to `v2.5.0.3`) | eZ Platform 2.5, `se7enxweb/ezpublish-kernel ~7.5.33`, Symfony 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `se7enxweb/legacy-bridge ^2.1` |
| `v3.0.0.x` (`v3.0.0.0` to `v3.0.0.14`), then `v3.3.44.x` (`v3.3.44.0` to `v3.3.44.7`), branch `3.x` | eZ Platform 3.3, `se7enxweb/ezplatform-kernel ~1.3` (`~1.3.43` in `v3.3.44.7`), Symfony 5.4 | `^8.0` | `^3.0`, from `v3.3.44.3` `^3.0.0.34`, from `v3.3.44.4` `^3.0.0.35` |
| `v4.6.23.x` (`v4.6.23.0` to `v4.6.23.2`), branch `4.6.x` | Ibexa 4.6 compatible, `se7enxweb/exponential-platform-dxp 4.6.x-LB-dev` | `^7.4` to `^8.5` | `^4.0.0.0` |
| `v5.0.x` (`v5.0.0` to `v5.0.2`), branch `5.x` | Platform v5, `exponential-platform-dxp dev-5.x-LB` | `>=8.3` | `^4.0.0.0` (`v5.0.0`), `^5.0.0.0` (`v5.0.1`, `v5.0.2`) |

**`v5.0.3` is not a v5 release.** The tag that GitHub marks "Latest" (8 July 2026) was cut from `master`, the 2.5
line: its `composer.json` requires `se7enxweb/ezpublish-kernel ~7.5.33` and `se7enxweb/legacy-bridge ^2.1`, PHP
`^7.1.3 || ^8.1 || ^8.2`, and Packagist serves it with that content. Tags are permanent, so it stays. For the v5
line require `v5.0.2` or the `5.x` branch; never a bare `^5.0` constraint, which resolves to `v5.0.3`.

Exponential 6.0 is therefore both a destination of its own and the legacy half of every Exponential Platform Legacy
release. Moving content "down to the legacy kernel" means moving it to a database the 6.0 kernel can read; whether a
Symfony stack keeps running next to it is a second decision ([16.6.8](#1668-keeping-a-symfony-stack-next-to-the-legacy-kernel)).

## 16.2 Choosing a path

| You have, or want | Path A: Exponential Platform Nexus | Path B: Exponential 6.0 (or Platform Legacy) |
|---|---|---|
| Custom Symfony bundles, Twig templates, REST or GraphQL clients | Keep them; same generation means same APIs | Rewrite as legacy extensions, TPL templates and modules; REST through the legacy REST layer |
| RichText (`ezrichtext`) content | Stays as is | Convert or adapt ([16.6.3](#1663-field-types)) |
| Page Builder landing pages (`ezlandingpage`, commercial) | No Page Builder; rebuild with Netgen Layouts | No equivalent; rebuild with Exponential Layouts |
| Editors who know the Symfony admin | Same admin UI generation | The legacy admin (`admin3` design), a different tool |
| A team that knows INI, TPL and legacy extensions | Has to learn the Symfony stack | Works with what it knows |
| PHP 8.0 or 8.1 servers | Nexus 1.0.0.x / 1.1.0.x (or Platform 3.x forks) | All of 6.0 |
| PHP 8.4 or 8.5 | Nexus 1.3.0.x (needs 8.4), or older lines with the [framework forks](../features/6.0/platform-php85-framework-forks.md) | All of 6.0 |
| One server without a separate web server, SQLite under load | Nexus 5 can run on SQLite (development default) | Velocity and the SQLite driver are production features ([chapter 8](08-serving-the-site.md), [chapter 9](09-databases.md)) |
| Lowest risk for a live site | Highest: same generation, a package swap | Highest effort: a data and template migration |
| Both worlds for a transition | Nexus 1.0.0.x, 1.1.0.x and 1.2.0.x or Platform Legacy run both kernels on one database | Same products, seen from the other side |

Rules of thumb:

- **Same generation first.** If your site is on Ibexa 4.6, the shortest safe step is Nexus 1.2.0.x (or the 4.6
  skeleton), not Nexus 5. Change the generation afterwards, with the vendor's update steps, on the Exponential forks.
- **Path B is a rewrite of the presentation and of custom code**, not of the content. The content tables are shared
  ([16.6.1](#1661-what-the-two-share)); templates, configuration and custom PHP are not.
- **Do not mix the paths in one cut-over.** Move to the matching Exponential Platform line first (path A), let it
  settle, and only then decide whether to go down to the legacy kernel.

## 16.3 The vendor's pages and where this chapter covers them

The vendor documents migration and updates under "Update and migration" in its documentation. Each page below was
read in full; the right-hand column says where this chapter covers its points and where Exponential differs.

| Vendor page | Covered in | Where Exponential differs |
|---|---|---|
| [Migrating from the 4.x and 5.x legacy stack (vendor page)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/) (the 4.x/5.x legacy stack to the Symfony stack) | [16.5.11](#16511-notes-per-source-version), [16.6](#166-path-b-down-to-the-exponential-60-legacy-kernel) | The vendor says running the Symfony stack side by side with the legacy kernel is "practically impossible" because of XmlText, the Page field and the schema. The se7enxweb legacy bridge branches keep the legacy schema in place for 2.5, 3.3, 4.6 and v5 ([legacy bridge](../features/6.0/legacy-bridge.md)), and Exponential 6.0 is itself a maintained destination, so the direction can be reversed |
| [Migrating from the 5.x platform stack (vendor page)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/) (5.4 / 2014.11 to 1.7, then 1.13, then 2.x) | [16.5.11](#16511-notes-per-source-version), [16.6.5](#1665-configuration-siteaccess-yaml-to-ini) (image aliases), [16.7](#167-common-issues) | The legacy kernel accepts sort fields 6 and 7, XmlText, the ezflow Page and Star Rating, which the vendor lists as unsupported; on the legacy side nothing has to be converted for them |
| [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/) | [16.7](#167-common-issues) | Command names on Exponential Platform v5 carry the `exponential:` prefix |
| [Updating Ibexa DXP](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/), [from 1.13 and 2.x](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_from_1.x_2.x/), [app to 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_app_to_2.5/), [database to 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_db_to_2.5/) | [16.5.2](#1652-bring-the-source-to-the-last-release-of-its-line), [16.5.11](#16511-notes-per-source-version) | Exponential Platform 2.5 packages come from se7enxweb, not from `updates.ez.no` |
| [From 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/update_from_2.5/), [to 3.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.2/), [adapt code to v3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/adapt_code_to_v3/) (templates, configuration, field types, signal slots, online editor, workflow, extensions, REST, other), [to 3.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.3/), [v3.0 deprecations](https://doc.ibexa.co/en/5.0/release_notes/ez_platform_v3.0_deprecations/) | [16.5.2](#1652-bring-the-source-to-the-last-release-of-its-line), [16.5.5](#1655-namespaces-bundles-and-configuration-keys), [16.5.6](#1656-the-database-generation-by-generation), [16.5.10](#16510-porting-custom-bundles) | The 2.5 to 3.0 SQL is in the `upgrade/db/` directory of `se7enxweb/exponential-platform`; the vendor uses `ibexa/installer` |
| [From 3.3 to 3.3.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/update_from_3.3/), [to 4.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/to_4.0/), [v4.0 deprecations](https://doc.ibexa.co/en/5.0/release_notes/ibexa_dxp_v4.0_deprecations/) | [16.5.5](#1655-namespaces-bundles-and-configuration-keys), [16.5.6](#1656-the-database-generation-by-generation), [16.5.7](#1657-admin-twig-field-types-rest-and-graphql) | The `exponential:` commands of the 3.x / 4.6 forks keep `ibexa:` and `ezplatform:` as aliases |
| 4.x minor updates: [4.0 to 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.0/to_4.1/), [4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.1/update_from_4.1/), [4.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.2/update_from_4.2/), [4.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.3/update_from_4.3/), [4.4](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.4/update_from_4.4/), [4.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.5/update_from_4.5/), [4.6.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_from_4.6/) | [16.5.11](#16511-notes-per-source-version) | Most 4.x steps are for the commercial editions (`ibexa:migrations:*`, commerce, corporate accounts); the open source edition only needs the token tables and the class group column |
| [From 4.6 to 5.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_to_5.0/), [5.0.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_5.0/update_from_5.0/) | [16.5.6](#1656-the-database-generation-by-generation), [16.5.7](#1657-admin-twig-field-types-rest-and-graphql), [16.6.4](#1664-the-database-step-by-step) | Exponential v5 commands are `exponential:*` only; the SQLite engine of the Exponential installer has no table rename script |
| The older [2.5 migration page](https://doc.ibexa.co/en/2.5/migrating/migrating_from_ez_publish_platform/) | as the 5.0 page | Same content with `.yml` file names (`ezplatform.yml`, `ezpublish.yml`) |

The old addresses on `doc.ezplatform.com` (`/en/latest/migrating/...`, `/en/2.5/migrating/...`) redirect to the
`doc.ibexa.co` pages above; the per-version `updating` pages of 2.5 are gone (404) and their content is in the update
pages listed.

## 16.4 Before you start: inventory, freeze and backups

### Know what you have

Run these on a copy of the production database (MySQL or MariaDB shown; the statements are plain SQL and run
unchanged in `psql` and `sqlite3`). They use the `ez*` table names of 1.x to 4.x; on a 5.0 database use the
`ibexa_*` names from the [rename table](#the-50-table-renames-and-their-reverse).

```sql
-- Which release the database says it is (2.5 writes ezpublish-version; 3.0 and later write ezplatform-release)
SELECT name, value FROM ezsite_data;

-- Field types in use, with the number of content type fields of each
SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;

-- Which content types use a given field type (example: ezrichtext)
SELECT DISTINCT contentclass_id FROM ezcontentclass_attribute WHERE data_type_string = 'ezrichtext';

-- Locations sorted by content type identifier (6) or name (7): unsupported by the Symfony stack
SELECT node_id, parent_node_id, sort_field FROM ezcontentobject_tree WHERE sort_field IN (6, 7);

-- Password hash types (1-3 MD5 variants, 5 plain text, 6 bcrypt, 7 PHP default)
SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;

-- Languages
SELECT id, locale, name, disabled FROM ezcontent_language;

-- Sizes that decide the downtime: versions, fields, locations
SELECT COUNT(*) FROM ezcontentobject_version;
SELECT COUNT(*) FROM ezcontentobject_attribute;
SELECT COUNT(*) FROM ezcontentobject_tree;
```

Write the answers down; most later steps depend on them. Also list:

- the bundles in `config/bundles.php` (3.x and later) or `app/AppKernel.php` (1.x, 2.x) and, for each third-party
  one, whether it exists for the target generation;
- the custom code under `src/` and its use of the kernel API, events (signal slots before 3.0), field types and
  Twig extensions;
- the siteaccesses, their matching and their hosts; the image variations; the search engine; the HTTP cache (Symfony
  proxy, Varnish, Fastly); the cron entries; the IO handler (local files, DFS cluster);
- the PHP version of every server.

### Freeze and back up

Announce an editorial freeze for the cut-over. Then back up the database and the binary storage:

```bash
mysqldump -u USER -p --single-transaction DATABASE > before-migration.sql     # MySQL or MariaDB
pg_dump -U USER DATABASE > before-migration.sql                               # PostgreSQL
sqlite3 var/data_prod.db ".backup before-migration.db"                         # SQLite (the file DATABASE_URL names)
tar czf storage-before-migration.tgz public/var                                # 3.x and later; web/var on 1.x and 2.x
```

Restore the dump once on a scratch machine before you start: a backup that was never restored is not a backup. Every
command of this chapter that changes a database is first run on such a copy.

## 16.5 Path A: to Exponential Platform Nexus

### 16.5.1 Pick the generation

| Your source | Target line | Where the generation's steps come from |
|---|---|---|
| eZ Platform 1.x (1.7, 1.13) | first to 2.5 with the vendor's steps, then as 2.5 | [16.5.2](#1652-bring-the-source-to-the-last-release-of-its-line) |
| eZ Platform 2.5 | Nexus `1.0.0.x`, Exponential Platform Legacy `v2.5.0.x` | same generation, package swap only |
| eZ Platform 3.0 to 3.2, Ibexa 3.3 | Nexus `1.1.0.x`, Exponential Platform Legacy `v3.3.44.x`, `se7enxweb/oss` 3.3, `se7enxweb/exponential-platform` 3.2.9 | first to 3.3.latest |
| Ibexa 4.0 to 4.6 | Nexus `1.2.0.x`, `se7enxweb/exponential-platform-v4x-dxp-skeleton`, Exponential Platform Legacy `v4.6.23.x` | first to 4.6.latest |
| Ibexa 5.0 | Nexus `1.3.0.x` / the Nexus starter, `se7enxweb/exponential-platform-dxp-skeleton`, Exponential Platform Legacy `v5.0.x` | same generation |

A Nexus line brings a demo site (layouts, design, content). When you migrate an existing site, you want its code and
packages, not its demo content: create the project from the Nexus line (or the matching skeleton), then carry your
own `src/`, `templates/`, `translations/`, `assets/`, configuration and storage into it, and point it at your
database. Never run `exponential:install` against your production database: it creates a new repository.

### 16.5.2 Bring the source to the last release of its line

The forks track the last upstream release of each line, so the source has to be there first. The vendor's sequence
([Updating Ibexa DXP](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/)) is:

- v1.13 or a 2.x below 2.5: to 2.5 LTS (the vendor names 2.5.30, later 2.5.32), then 3.3 LTS, then 4.6 LTS, then 5.0;
- 2.5: to 3.3 LTS (3.3.43), then 4.6, then 5.0;
- 3.3: to the latest 3.3, then 4.6, then 5.0;
- 4.x below 4.6: to 4.6 LTS and its latest patch (4.6.32), then 5.0.

The database scripts of each step, as the vendor names them:

| Step | MySQL / MariaDB | PostgreSQL | Notes |
|---|---|---|---|
| 1.x to 2.2 | `vendor/ezsystems/ezpublish-kernel/data/update/mysql/dbupdate-7.1.0-to-7.2.0.sql` (and `-dfs.sql` for a DFS database) | `.../postgres/...` | utf8 to utf8mb4; may fail on index collisions (indexes were shortened), remove the duplicates and repeat; then set `charset: utf8mb4` for the Doctrine connection |
| to 2.3 | `dbupdate-7.2.0-to-7.3.0.sql` | same | adds `ezcontentobject_trash.trashed` |
| to 2.5 | `dbupdate-7.4.0-to-7.5.0.sql`, then `7.5.2-to-7.5.3`, `7.5.4-to-7.5.5`, `7.5.6-to-7.5.7` | same | 2.5.9: run a reindex afterwards |
| 2.5 to 3.0 | `upgrade/db/mysql/ezplatform-2.5.latest-to-3.0.0.sql` | `upgrade/db/postgresql/...` | in `se7enxweb/exponential-platform`; the vendor ships `vendor/ibexa/installer/upgrade/db/mysql/ezplatform-2.5-to-ibexa-3.3.0.sql` |
| 3.2.3 to 3.2.4 | `upgrade/db/mysql/ezplatform-3.2.3-to-3.2.4.sql` | same | in `se7enxweb/exponential-platform` |
| 3.3.x patches | `ibexa-3.3.1-to-3.3.2.sql`, `3.3.6-to-3.3.7`, `3.3.8-to-3.3.9`, `3.3.24-to-3.3.25`, `3.3.33-to-3.3.34` | same | from 3.3.7 the open source edition without `ibexa/installer` creates `ibexa_setting` by hand |
| 3.3 to 4.0 | `vendor/ibexa/installer/upgrade/db/mysql/ibexa-3.3.latest-to-4.0.0.sql` | `.../postgresql/...` | open source edition: only `ALTER TABLE ezcontentclassgroup ADD COLUMN is_system BOOLEAN NOT NULL DEFAULT false;` |
| 4.x minors | `ibexa-4.0.0-to-4.1.0.sql`, `4.1.latest-to-4.2.0`, `4.2.latest-to-4.3.0`, `4.4.latest-to-4.5.0`, `4.5.latest-to-4.6.0` and patches | same | open source edition: 4.1 to 4.4 need none; 4.5 creates `ibexa_token_type` and `ibexa_token`; 4.6 adds `ibexa_token.revoked` |
| 4.6 to 5.0 | `vendor/ibexa/installer/upgrade/db/mysql/ibexa-4.6.latest-to-5.0.0.sql` | `.../postgresql/...` | the open source edition gets the statements on the vendor page itself; see [16.5.6](#1656-the-database-generation-by-generation) |

The vendor's two rules for these scripts apply unchanged: back up first and clear the caches afterwards, and **never
pass `--force` to `mysql` or `psql`**: a failing statement must stop the run so you can fix its cause.

What the 2.5 to 3.0 script of `se7enxweb/exponential-platform` does (MySQL version; the PostgreSQL file does the same
in its syntax):

```sql
START TRANSACTION;
DELETE FROM ezsite_data WHERE name IN ('ezpublish-version', 'ezplatform-release');
INSERT INTO ezsite_data (name, value) VALUES ('ezplatform-release', '3.0.0');
COMMIT;
ALTER TABLE ezcontentclass_attribute MODIFY data_text1 VARCHAR(255);
ALTER TABLE ezcontentclass_attribute ADD COLUMN is_thumbnail TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `ezkeyword_attribute_link`
    ADD COLUMN `version` INT(11) NOT NULL,
    ADD KEY `ezkeyword_attr_link_oaid_ver` (`objectattribute_id`, `version`);
-- then fills ezkeyword_attribute_link.version from the current versions,
-- and sets the ezuser login pattern '^[^@]+$' where it is empty
```

Two of these lines matter later if you ever go down to the legacy kernel: the `ezpublish-version` row is deleted,
and `ezkeyword_attribute_link.version` is `NOT NULL` without a default on MySQL ([16.6.4](#1664-the-database-step-by-step)).

The vendor's update pages also list the non-database steps per release (Flex endpoint changes, the Symfony 5.3 and 5.4
switches inside 3.3, the Varnish and Fastly VCL changes, the 3.3.41 and 4.6.14 BREACH changes, the
`ibexa:content:remove-duplicate-fields` cleanup, the 4.6.19 Rector package). Follow them on the upstream code; they
are not repeated here.

### 16.5.3 Swap the packages

On the same generation the swap is a Composer change. Before, a 4.6 open source project:

```json
{
    "require": {
        "php": "^7.4 || ^8.0",
        "ibexa/oss": "~4.6.0",
        "netgen/layouts-ibexa": "~1.4.0"
    }
}
```

After, as Nexus `v1.2.0.0` requires it:

```json
{
    "require": {
        "php": ">=8.2",
        "se7enxweb/oss": "~4.6.0",
        "se7enxweb/site-legacy-bundle": "v2.0.0",
        "netgen/layouts-ibexa": "~1.4.0"
    }
}
```

And for Platform v5, the metapackage of the DXP skeleton guide:

```json
{
    "require": {
        "se7enxweb/exponential-platform-dxp": "*"
    }
}
```

The order of work, on a copy of the project:

1. Create a branch. Copy `composer.json` aside.
2. Replace the upstream metapackage by the se7enxweb one of the same generation (table in [16.5.1](#1651-pick-the-generation)).
   Keep your own requirements; remove packages that were only dependencies of the upstream metapackage
   (`composer why <package>` tells you who needs a package).
3. Update on the copy and read the plan Composer prints before accepting it. Then check that no upstream package is
   still installed next to its fork; the fork and the upstream package share namespaces, and the autoloader would
   take either:

   ```bash
   composer why ibexa/admin-ui                              # expect se7enxweb/admin-ui
   composer show | grep -E "^(ibexa|ezsystems|netgen)/"    # upstream packages that remain, each needs a reason
   ```

4. Require tags, not the dev branch aliases that were added and removed again on 2026-03-26 (step 2 of
   [Package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md)).
5. Install the Flex recipes the skeleton points at: the Exponential skeletons place the `se7enxweb/sevenx-recipes`
   endpoint before the upstream recipes, so recipes are the fork-aware ones.
6. `php bin/console cache:clear`, then the asset build of your generation (Webpack Encore: `yarn install` and the
   project's build script, then `php bin/console assets:install --symlink --relative public`).

Version constraints: PHP is the first gate. Nexus `1.3.0.x` refuses PHP below 8.4; the v5 skeleton below 8.3; the
4.6 skeleton accepts 7.4 to 8.5; the 3.x forks 7.3 to 8.5. On PHP 8.4 or 8.5 the older stacks need the
[framework forks](../features/6.0/platform-php85-framework-forks.md), and the Layouts 2.0 stack needs the
[layouts-core fork](../features/6.0/platform-layouts-core-fork.md). The vendor's PHP migration guides
([8.0](https://www.php.net/manual/en/migration80.php) to [8.5](https://www.php.net/manual/en/migration85.php)) apply
to your own code as they do anywhere.

### 16.5.4 Console command names

The Exponential forks gave the commands an `exponential:` primary name on 2026-04-07
([command names](../specifications/6.0/platform-console-commands.md)).

| Generation | Primary name | Old names |
|---|---|---|
| 3.x kernel fork `se7enxweb/ezplatform-kernel` from `v1.3.45` | `exponential:*` (`exponential:reindex` ...) | `ibexa:*` and `ezplatform:*` stay as deprecated aliases |
| 3.x kernel fork up to `v1.3.44` (`v1.3.43`, `v1.3.44` included) | `ibexa:*` (`ibexa:reindex` ...), as upstream | `ezplatform:*` |
| 4.6 core fork (`se7enxweb/core`, branch `4.6`) | `exponential:*` | `ibexa:*` and `ezplatform:*` stay as deprecated aliases |
| Nexus 1.2.0.x | `ibexa:*` (`ibexa:reindex` ...): its `composer.lock` installs upstream `ibexa/core` `4.6.x-dev`, not the fork | none |
| Platform v5 (`se7enxweb/exponential-platform-dxp-core` v5.0.7, the version the Nexus starter installs) | `exponential:*` | no aliases are registered in this version: `ibexa:reindex` and the other renamed commands do not exist |
| Packages that were not forked (cron, GraphQL, ...) | upstream name | `ibexa:cron:run`, `ibexa:graphql:generate-schema` |
| Nexus 1.1.0.x and 1.2.0.x projects | their own `exponential:reindex` (`src/.../ExponentialReindexCommand.php`), a proxy that accepts only `--siteaccess` and runs `ibexa:reindex` (or says it is not registered and stops) | `ibexa:reindex` with all its options |
| Legacy bridge `3.x` from `v3.0.0.30`, `4.x` from `v4.0.0.2`, every `5.x` tag | `exponential:legacy:*` | `ezpublish:legacy:*`, `ezpublish:configure`, `ezpublish:legacybundles:install_extensions` stay as deprecated aliases |
| Legacy bridge `3.x` up to `v3.0.0.29`, `4.x` `v4.0.0.0` and `v4.0.0.1` | `ezpublish:*` | only these names exist |
| Legacy bridge on `master`, `v2.1.10` to `v2.1.12` (2.5 generation) | `ezpublish:*` (`ezpublish:legacy:init` and the rest) | only these names exist |

The v5 kernel registers `exponential:install`, `exponential:reindex`, `exponential:urls:regenerate-aliases`,
`exponential:images:normalize-paths`, `exponential:images:resize-original`, `exponential:io:migrate-files`,
`exponential:user:validate-password-hashes`, `exponential:user:expire-password`, `exponential:content:cleanup-versions`,
`exponential:content:remove-duplicate-fields`, `exponential:copy-subtree`, `exponential:delete-content-translation`,
`exponential:timestamps:to-utc`, `exponential:check-urls`, `exponential:content-type-group:set-system` and
`exponential:debug:config-resolver` (alias `exponential:debug:config`). Rewrite cron entries and deployment scripts
before the cut-over:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d ./deploy 2>/dev/null
```

Each line it prints names an old command; on Platform v5 each one must be changed, on 3.x and 4.6 it keeps working.

### 16.5.5 Namespaces, bundles and configuration keys

Within one generation nothing changes: the forks keep the upstream namespaces. Across generations you apply the
vendor's renames, on the Exponential forks exactly as upstream:

| Change | 2.5 | 3.x | 4.x / 5.0 |
|---|---|---|---|
| Main configuration key | `ezpublish:` | `ezplatform:` | `ibexa:` |
| Config resolver namespace | `ezsettings` | `ezsettings` | `ibexa.site_access.config` |
| Kernel API | `eZ\Publish\API\Repository\ContentService` | same | `Ibexa\Contracts\Core\Repository\ContentService` |
| Config resolver interface | `eZ\Publish\Core\MVC\ConfigResolverInterface` | same | `Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface` |
| Field type SPI | `eZ\Publish\SPI\FieldType\FieldType` (interface) | same, now an abstract class: `extends FieldType` | `Ibexa\Contracts\Core\FieldType\FieldType` |
| Admin UI | `EzSystems\EzPlatformAdminUi\` | same | `Ibexa\AdminUi\` (contracts `Ibexa\Contracts\AdminUi\`) |
| REST | `eZ\Publish\Core\REST` | `EzSystems\EzPlatformRest` | `Ibexa\Rest\` |
| RichText | in the kernel | `EzSystems\EzPlatformRichText\` | `Ibexa\FieldTypeRichText\` |
| HTTP cache | `EzSystems\PlatformHttpCacheBundle\` | same | `Ibexa\Bundle\HttpCache\` |
| Solr | `EzSystems\EzPlatformSolrSearchEngine\` | same | `Ibexa\Solr\` |
| GraphQL | `EzSystems\EzPlatformGraphQL\` | same | `Ibexa\GraphQL\` |
| Project layout | `app/`, `src/AppBundle`, `web/` | `config/`, `src/` (`App\`), `templates/`, `public/` | same as 3.x |
| Bundle registration | `app/AppKernel.php` | `config/bundles.php` | same; on 4.0 remove every entry starting with `eZ`, `EzSystems`, `Ibexa\Platform`, `Silversolutions`, `Siso` |

For the 4.x renames the vendor offers `ibexa/compatibility-layer` (last bundle in `config/bundles.php`), which maps
the old class, service, route, Twig and configuration names at run time; it is not supported on 5.0, where the vendor
uses `ibexa/rector` (`composer require --dev ibexa/rector`, set `IbexaSetList::IBEXA_46`, then
`php vendor/bin/rector --dry-run`) to rewrite the code. Neither has an Exponential fork in the
[package map](../specifications/6.0/platform-package-map.md); both are development tools you run during the
migration and remove afterwards.

Configuration key renames of 4.0 that most projects meet (complete list on the vendor's 4.0 deprecation page):
`ezplatform` and `ezpublish` to `ibexa`, `ez_doctrine_schema` to `ibexa_doctrine_schema`, `ez_io` to `ibexa_io`,
`ez_platform_http_cache` to `ibexa_http_cache`, `ez_search_engine_solr` to `ibexa_solr`, `ezdesign` to
`ibexa_design_engine`, `ezplatform_graphql` to `ibexa_graphql`, `ezrichtext` to `ibexa_fieldtype_richtext`,
`ezplatform_support_tools` to `ibexa_system_info`. Configuration files follow: `ezplatform.yaml` to `ibexa.yaml`,
`ezplatform_admin_ui.yaml` to `ibexa_admin_ui.yaml`, `ezplatform_http_cache.yaml` to `ibexa_http_cache.yaml`,
`ezplatform_solr.yaml` to `ibexa_solr.yaml`; the Nexus 5 project has exactly these names under `config/packages/`.

Signal slots (before 3.0) became Symfony events with a before and an after event per operation; 3.0 also removed
dynamic settings (`$setting$`), resolving configuration in constructors, `ContainerAwareCommand`, the `.class`
container parameters and the eZc database handler (use the `ezpublish.persistence.connection` Doctrine connection,
`ibexa.persistence.connection` on 4.x and later). Section [16.5.10](#16510-porting-custom-bundles) shows the code.

### 16.5.6 The database, generation by generation

| Generation | Core tables | What the generation adds | Exponential difference |
|---|---|---|---|
| 2.5 | the full legacy schema (129 tables in `data/mysql/schema.sql` of the 7.5 kernel) | `ezcontentclass_attribute_ml`, `eznotification`, `ezgmaplocation`, comments and star rating tables | the Exponential 2.5 line runs the legacy kernel on the same tables |
| 3.0 to 3.3 | 50 tables in `schema.yaml` of `ezplatform-kernel` | `ibexa_setting` (3.3.7), columns `is_thumbnail`, `ezkeyword_attribute_link.version`, `ezcontentobject.is_hidden`, `ezuser.password_updated_at`, `ezsearch_object_word_link.language_mask` | the vendor's 3.0 page lists about 80 legacy tables to drop; the se7enxweb bridge branches keep them for the legacy kernel |
| 4.0 to 4.6 | the same 50 `ez*` tables plus `ibexa_setting`, `ibexa_token_type`, `ibexa_token` (core `4.6` branch) | `ezcontentclassgroup.is_system` (4.0), `ibexa_token.revoked` (4.6) | none |
| 5.0 | 52 `ibexa_*` tables | every core table renamed, columns `contentclass_id` and `contentclassattribute_id` renamed | the Exponential installer can also load the legacy kernel schema next to it and supports SQLite |

#### The 5.0 table renames, and their reverse

The vendor's 4.6 to 5.0 script renames the core tables; for the open source edition, which has no `ibexa/installer`,
the vendor page prints the statements themselves. The core part of the rename map, as the page lists it, with the
column renames:

| 4.x name | 5.0 name |
|---|---|
| `ezbinaryfile` | `ibexa_binary_file` |
| `ezcobj_state`, `ezcobj_state_group`, `ezcobj_state_group_language`, `ezcobj_state_language`, `ezcobj_state_link` | `ibexa_object_state`, `ibexa_object_state_group`, `ibexa_object_state_group_language`, `ibexa_object_state_language`, `ibexa_object_state_link` |
| `ezcontent_language` | `ibexa_content_language` |
| `ezcontentbrowsebookmark` | `ibexa_content_bookmark` |
| `ezcontentclass` | `ibexa_content_type` |
| `ezcontentclass_attribute` (`contentclass_id`) | `ibexa_content_type_field_definition` (`content_type_id`) |
| `ezcontentclass_attribute_ml` (`contentclass_attribute_id`) | `ibexa_content_type_field_definition_ml` (`content_type_field_definition_id`) |
| `ezcontentclass_classgroup` (`contentclass_id`) | `ibexa_content_type_group_assignment` (`content_type_id`) |
| `ezcontentclass_name` (`contentclass_id`) | `ibexa_content_type_name` (`content_type_id`) |
| `ezcontentclassgroup` | `ibexa_content_type_group` |
| `ezcontentobject` (`contentclass_id`) | `ibexa_content` (`content_type_id`) |
| `ezcontentobject_attribute` (`contentclassattribute_id`) | `ibexa_content_field` (`content_type_field_definition_id`) |
| `ezcontentobject_link` (`contentclassattribute_id`) | `ibexa_content_relation` (`content_type_field_definition_id`) |
| `ezcontentobject_name`, `ezcontentobject_trash`, `ezcontentobject_tree`, `ezcontentobject_version` | `ibexa_content_name`, `ibexa_content_trash`, `ibexa_content_tree`, `ibexa_content_version` |
| `ezdfsfile` | `ibexa_dfs_file` (on its own database when DFS is used, renamed there) |
| `ezgmaplocation`, `ezimagefile`, `ezkeyword`, `ezmedia` | `ibexa_map_location`, `ibexa_image_file`, `ibexa_keyword`, `ibexa_media` |
| `ezkeyword_attribute_link` | `ibexa_keyword_field_link` |
| `eznode_assignment`, `eznotification`, `ezpackage` | `ibexa_node_assignment`, `ibexa_notification`, `ibexa_package` |
| `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`, `ezrole`, `ezsection` | `ibexa_policy`, `ibexa_policy_limitation`, `ibexa_policy_limitation_value`, `ibexa_role`, `ibexa_section` |
| `ezpreferences` | `ibexa_preferences` on the vendor page; `ibexa_user_preference` in the `schema.yaml` of the Exponential v5 kernel. Check which one your 5.0 database has |
| `ezsearch_object_word_link` (`contentclass_id`, `contentclass_attribute_id`), `ezsearch_word` | `ibexa_search_object_word_link` (`content_type_id`, `content_type_field_definition_id`), `ibexa_search_word` |
| `ezsite_data` | `ibexa_site_data` |
| `ezurl`, `ezurl_object_link` | `ibexa_url`, `ibexa_url_content_link` |
| `ezurlalias`, `ezurlalias_ml`, `ezurlalias_ml_incr`, `ezurlwildcard` | `ibexa_url_alias`, `ibexa_url_alias_ml`, `ibexa_url_alias_ml_incr`, `ibexa_url_wildcard` |
| `ezuser`, `ezuser_accountkey`, `ezuser_role`, `ezuser_setting` | `ibexa_user`, `ibexa_user_accountkey`, `ibexa_user_role`, `ibexa_user_setting` |

The full script also renames the indexes and foreign keys and the tables of the commercial packages (`ezpage_*`,
`ezform_*`, `ezeditorialworkflow_*`, `ezdatebasedpublisher_scheduled_entries`, `ezsite*`); on the open source
edition errors about those missing tables "can safely be ignored", says the vendor. Read the reverse direction of
this table in [16.6.4](#1664-the-database-step-by-step).

The field type identifiers are renamed in 5.0 as well (`ezstring` to `ibexa_string`, `ezrichtext` to
`ibexa_richtext`, `ezimage` to `ibexa_image` and so on, 28 in all). The old identifiers stay supported;
`php bin/console debug:container --tag=ibexa.field_type` lists both (columns `alias` and `legacy_alias`). Field
templates follow the new names (`{% block ezstring_field %}` becomes `{% block ibexa_string_field %}`).

**SQLite.** The Exponential installers can create a platform database in SQLite (3.x and 4.6 kernel forks, v5 core;
[SQLite for Exponential Platform](../features/6.0/platform-sqlite-install.md)). No upgrade script for SQLite exists
upstream or in the Exponential repositories: to move an SQLite database across a generation, convert it to MySQL or
PostgreSQL with the conversion recipes of the skeleton guide, update it there, and convert back if you want to.

**The legacy schema inside a v5 database.** The `CoreInstaller` of `se7enxweb/exponential-platform-dxp-core` loads
the legacy kernel's schema (`ezpublish_legacy/kernel/sql/<engine>/...`) after the platform schema when that
directory exists, with every `CREATE TABLE` turned into `CREATE TABLE IF NOT EXISTS`, so tables without a 5.0
counterpart (`ezbasket`, `ezdiscountrule` and the like) are present for the legacy kernel. This only happens on a
fresh install; a migrated database gets those tables from [16.6.4](#1664-the-database-step-by-step).

### 16.5.7 Admin, Twig, field types, REST and GraphQL

**Admin.** The admin UI is the upstream one of each generation, branded "Exponential Platform DXP" on v5
(`se7enxweb/admin-ui`, [Platform administration interface](../features/6.0/platform-admin-ui-fork.md)). Nexus 5
serves it under `/adminui/` (siteaccess `adminui`); check your own siteaccess list, because the Nexus siteaccess names
(`fh_eng`, `bold_eng`, `bold_ger`, `adminui`) are those of the demo. Custom admin tabs, menus and Universal Discovery
Widget configuration move as on upstream: 3.0 changed the tab and UDW configuration, 4.0 moved the back office to
Bootstrap 5 and the online editor to CKEditor 5, renamed CSS classes from `ez-` to `ibexa-` and JavaScript events
from `ez-` to `ibexa-`.

**Twig.** The functions follow the generation, not Exponential: there are no `exponential_*` Twig functions. The v5
kernel registers `ibexa_render_field`, `ibexa_content_name`, `ibexa_field_value`, `ibexa_image_alias`, `ibexa_render`,
`ibexa_path` and `ibexa_url`. Before and after for a 3.3 template moved to 4.x or 5.0:

```twig
{# 3.3 #}
<h1>{{ ez_content_name(content) }}</h1>
{{ ez_render_field(content, 'body') }}
<a href="{{ path('ez_urlalias', {'locationId': location.id}) }}">...</a>
{% set image = ez_image_alias(content.getField('image'), content.versionInfo, 'medium') %}

{# 4.x and 5.0 #}
<h1>{{ ibexa_content_name(content) }}</h1>
{{ ibexa_render_field(content, 'body') }}
<a href="{{ ibexa_path(location) }}">...</a>
{% set image = ibexa_image_alias(content.getField('image'), content.versionInfo, 'medium') %}
```

Template references use the `@` notation since 3.0 (`@ibexadesign/...` with the design engine,
`@IbexaCore/content_fields.html.twig` on 4.x). Nexus templates mostly use the Netgen Site API functions: in the Nexus
5 project `ng_render_field` appears 223 times, `ibexa_path` 110, `ng_image_alias` 27, `ng_view_content` 12 and
`nglayouts_render_result` 9; templates written for the Netgen Site API on Ibexa move unchanged.

**Field types.** Within a generation every core field type keeps its storage. RichText stays DocBook XML in
`data_text`; XmlText (`ezxmltext`) is not supported by 3.x and later except for converting it to RichText with
`se7enxweb/ezplatform-xmltext-fieldtype` (`ezxmltext:convert-to-richtext`, [16.5.11](#16511-notes-per-source-version)).
The Matrix field type (`ezmatrix`) of 2.5 and later stores a different format from the legacy Matrix datatype; a
legacy matrix is converted with `ezplatform:migrate:legacy_matrix` (2.5).

**REST.** The REST prefix of the v5 stack is `/api/ibexa/v2` (`ibexa.rest.path_prefix` in the default settings of
`ibexa/rest`, used by `config/routes/ibexa_rest.yaml` of Nexus 5; the security firewall pattern is `^/api/ibexa`).
4.0 renamed the prefix from `/api/ezp/v2/` and the media types from `application/vnd.ez.api.*` to
`application/vnd.ibexa.api.*`; clients written for 3.3 or older must be changed, or the prefix parameter overridden.
Nexus 5 authenticates REST with JWT (`php bin/console lexik:jwt:generate-keypair`).

**GraphQL.** The schema is generated per project: `php bin/console ezplatform:graphql:generate-schema` (2.5),
`ibexa:graphql:generate-schema` (3.x and later; not renamed in the Exponential forks). 4.0 renamed the `id` argument
to `contentId`, `_info` to `_contentInfo` and `<ContentType>Content` to `<ContentType>Item`; 5.0 removes the 4.6
schema first (`rm -r config/graphql`, then generate) and makes RelationList pagination permanent. Nexus 5 answers
GraphQL at `/graphql`.

### 16.5.8 Search, HTTP cache, cron, users, images and URL aliases

**Search.** Nexus 5 reads the engine from `SEARCH_ENGINE` in `.env` (legacy by default; Solr optional with
`SOLR_DSN`). Solr is supported with the `se7enxweb/ezplatform-solr-search-engine` fork (3.x) and `ibexa/solr` (v5);
Elasticsearch is a commercial package and not part of Nexus. After a migration, rebuild the index:

```bash
php bin/console exponential:reindex                      # v5, and 3.x/4.6 forks
php bin/console exponential:reindex --iteration-count=100
php bin/console ibexa:reindex                            # 3.x/4.6 forks, alias
php bin/console ezplatform:reindex                       # 2.5
```

From 4.6 the Solr configuration needs the `spellcheck` search component; regenerate the Solr configuration with the
vendor's script and restart Solr. 3.0 removed `ezplatform:solr_create_index`; use the reindex command.

**HTTP cache.** `ibexa/http-cache` (v5) and `se7enxweb/ezplatform-http-cache` (3.x). Nexus 5 sets the purge type with
`HTTPCACHE_PURGE_TYPE` (`local` for the Symfony proxy, `varnish` for Varnish). The VCL files are in
`vendor/ibexa/http-cache/docs/varnish/vcl/` (`varnish5.vcl`, `varnish6.vcl`, `varnish7.vcl`, `parameters.vcl`).
The vendor's VCL changes of 3.3.14, 3.3.41 (no compression of REST and JSON responses) and 5.0.8 apply; take the VCL
of the version you install, not the one you had. 3.0 changed the user context header to `X-User-Context-Hash` and
the purge to `PURGEKEY` with `xkey-softpurge`. `php bin/console fos:httpcache:invalidate:path / --all` purges
everything when a proxy is in use.

**Cron and messenger.** One cron entry runs the Symfony cron jobs:

```bash
*/5 * * * * /usr/bin/php /var/www/site/bin/console ibexa:cron:run --env=prod
```

Nexus 1.0.0.x additionally runs the legacy cron jobs through the bridge (`ngsite.cron`:
`bin/console ezpublish:legacy:script runcronjobs.php`). On v5 the messenger transport is `MESSENGER_TRANSPORT_DSN`;
Nexus 5 ships `doctrine://default?auto_setup=0`, and on SQLite you set `sync://`, because the Doctrine transport
wants a second connection ([SQLite for Exponential Platform](../features/6.0/platform-sqlite-install.md)).

**Users and password hashes.** The hash types are numbered alike in every generation and in the legacy kernel:

| Type | Name | Legacy 6.0 | 2.5 | 3.x, 4.x, 5.0 |
|---|---|---|---|---|
| 1, 2, 3 | MD5 (password, user, site) | yes | yes | removed in 3.0: sign-in fails, users must reset |
| 4 | MySQL `PASSWORD()` | yes | no | no |
| 5 | plain text | yes | yes | removed in 3.0 |
| 6 | bcrypt | yes | yes | yes |
| 7 | PHP default (`password_hash`) | yes, the default (`HashType=php_default`) | yes, the default | yes, the default |
| 256 | invalid | no | no | 5.0 only (`PASSWORD_HASH_INVALID`) |

Before a move to 3.x or later, find the old hashes with the query of [16.4](#164-before-you-start-inventory-freeze-and-backups)
or with the command:

```bash
php bin/console ezplatform:user:validate-password-hashes            # 2.5
php bin/console exponential:user:validate-password-hashes           # Exponential 3.x, 4.6 and v5
```

Users with types 1 to 5 need a new password (the vendor's 3.3.28 and 4.2 notes ask for revoking passwords after
the IBEXA-SA-2022-009 advisory as well). Nexus 5 hashes Symfony passwords with `password_hashers:
PasswordAuthenticatedUserInterface: 'auto'` in `security.yaml`. Sessions do not move: everyone signs in again after
the cut-over.

**Images.** Originals stay where the IO configuration puts them (`ibexa.site_access.config.default.io.root_dir`,
`%webroot_dir%/$var_dir$/$storage_dir$`, that is `public/var/<site>/storage`; `web/var/...` on 1.x and 2.x), and
their paths are stored in `ezimagefile` and in the field's XML. Copy that directory with the database. Variations
are defined per siteaccess under `image_variations` (Nexus 5: `config/app/packages/image.yaml`) with LiipImagine
filter sets; they are generated again on demand. `exponential:images:normalize-paths` repairs paths with unprintable
characters ([16.7](#167-common-issues)).

**URL aliases.** The aliases live in `ezurlalias_ml` (`ibexa_url_alias_ml` on 5.0) and move with the database. The
slug converter defaults to the transformation `urlalias` in every generation (`SlugConverter::DEFAULT_CONFIGURATION`)
and is configured under `ibexa: url_alias: slug_converter: transformation:` (4.x, 5.0). Sites that came from the
legacy kernel with another transformation keep it with the `urlalias_compat` or `urlalias_iri` transformation, as the
vendor's 5.x migration page describes. Rebuild aliases only if the check in [16.8](#168-verify-the-migration) fails:

```bash
php bin/console exponential:urls:regenerate-aliases
```

### 16.5.9 Page building: Page Builder against Netgen Layouts

Nexus builds pages with Netgen Layouts (`netgen/layouts-*` 1.4 on 2.5 to 4.6, 2.0 on v5, the
`se7enxweb/layouts-core` fork on PHP 8.4). The commercial Page Builder (`ezlandingpage`, tables `ezpage_*`) has no
counterpart in Nexus, and no tool converts landing pages into layouts. Plan it as a content task:

1. List the landing pages: `SELECT COUNT(*) FROM ezcontentobject_attribute WHERE data_type_string = 'ezlandingpage';`
2. For each landing page layout, build a Netgen Layouts layout with the same zones and a rule that maps it to the
   location, and recreate the blocks (lists, content, HTML) as layout blocks with collections.
3. Remove the `ezlandingpage` field from the content types once the layouts are live, or keep the field and have the
   template ignore it until then.

The vendor's ezflow migration (`ezsystems/ezflow-migration-toolkit`, `ezflow:migrate`) and landing page migration
(`ezsystems/ezplatform-page-migration`, `ezplatform:page:migrate`, 2.2 and later) are commercial-edition tools for
moving into Page Builder; they are not part of the Exponential packages. Form Builder content (`ezform`) and the
editorial workflow tables are commercial too; export what you need before the move.

### 16.5.10 Porting custom bundles

A checklist for each bundle under `src/` or in your own packages:

- [ ] Does it run on the source generation's last release? Fix that first.
- [ ] Namespaces of the kernel API follow the target generation ([16.5.5](#1655-namespaces-bundles-and-configuration-keys)).
- [ ] Field types: `extends FieldType`, `getName( Value $value, FieldDefinition $fieldDefinition, string $languageCode ): string`
      (3.0), service tags renamed (`ezpublish.fieldType` to `ezplatform.field_type`, later `ibexa.field_type`;
      `ezpublish.storageEngine.legacy.converter` to `ezplatform.field_type.legacy_storage.converter`, later
      `ibexa.field_type.storage.legacy.converter`).
- [ ] Signal slots replaced by event subscribers (3.0).
- [ ] Query types tagged (`ezplatform.query_type`, later `ibexa.query_type`) or autoconfigured; they are no longer
      found by class name (3.0).
- [ ] Commands extend `Symfony\Component\Console\Command\Command` with constructor injection; on Symfony 7 with the
      `#[AsCommand]` attribute.
- [ ] Routes: annotation to attribute (5.0); controllers referenced as `App\Controller\X::action`.
- [ ] Symfony event classes: `GetResponseEvent` to `RequestEvent`, `FilterResponseEvent` to `ResponseEvent`,
      `GetResponseForExceptionEvent` to `ExceptionEvent` (3.0).
- [ ] Twig extensions: `Twig\Extension\AbstractExtension`, `Twig\TwigFunction` (3.0); Twig 3.24 on Nexus 5.
- [ ] Container parameters `*.class` replaced by class names (3.0).
- [ ] Config resolver used at call time, not in the constructor (3.0).
- [ ] JavaScript: `ez-` events and classes to `ibexa-` (4.0); CKEditor 5 plugins instead of AlloyEditor (4.0).
- [ ] REST clients: `/api/ibexa/v2`, `application/vnd.ibexa.api.*` (4.0).
- [ ] Third-party bundles: a release for the target Symfony version exists, or a se7enxweb fork does
      ([package map](../specifications/6.0/platform-package-map.md)).

Before and after, a 2.5 signal slot turned into a 4.x event subscriber:

```php
// 2.5: src/AppBundle/Slot/OnPublishSlot.php
namespace AppBundle\Slot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;

class OnPublishSlot extends Slot
{
    public function receive( Signal $signal )
    {
        if ( !$signal instanceof Signal\ContentService\PublishVersionSignal ) {
            return;
        }
        // $signal->contentId, $signal->versionNo
    }
}
```

```php
// 4.x and 5.0: src/EventSubscriber/OnPublishSubscriber.php
namespace App\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OnPublishSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ PublishVersionEvent::class => 'onPublish' ];
    }

    public function onPublish( PublishVersionEvent $event ): void
    {
        $content = $event->getContent();
        // $content->id, $content->versionInfo->versionNo
    }
}
```

And a 2.5 command turned into a v5 command:

```php
// 2.5
class ReportCommand extends ContainerAwareCommand
{
    protected function configure() { $this->setName( 'app:report' ); }
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $contentService = $this->getContainer()->get( 'ezpublish.api.service.content' );
    }
}
```

```php
// v5 (Symfony 7.4)
#[AsCommand( name: 'app:report' )]
final class ReportCommand extends Command
{
    public function __construct( private readonly ContentService $contentService )
    {
        parent::__construct();
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        return Command::SUCCESS;
    }
}
```

`ContentService` is `Ibexa\Contracts\Core\Repository\ContentService` there; autowiring injects it.

### 16.5.11 Notes per source version

**eZ Platform 1.x.** Go to 2.5 first with the vendor's steps (branch, merge the upstream tag, resolve `composer.json`
and `composer.lock`, then the database scripts of [16.5.2](#1652-bring-the-source-to-the-last-release-of-its-line)).
2.2 changed the database to utf8mb4 and the landing page storage; 2.5 brought Webpack Encore (Node.js and Yarn) next
to Assetic. Sites that came from the 5.x stack hit an exception during the landing page migration when internal
drafts of landing pages exist (they have no row in `ezcontentobject_name`): before the move, set
`InternalDraftsCleanUpLimit` and `InternalDraftsDuration` to 0 in the legacy `content.ini` (in 6.0 the duration is
the array `InternalDraftsDuration[days]`, `[hours]`, `[minutes]`, `[seconds]`) and run the `internal_drafts_cleanup`
cron job. Sites that came from 5.x also meet `defaultLayout` errors for the Page field;
the vendor's temporary layout configuration is on the 2.5 database page.

**eZ Platform 2.5 LTS.** The richest generation for path B: the 7.5 kernel uses the whole legacy schema, and
Exponential carries both Nexus `1.0.0.x` and Exponential Platform Legacy `v2.5.0.x` on it, with the legacy kernel and
the bridge. For path A within 2.5: swap the packages, keep the `app/` layout, keep `ezpublish:` configuration. The
legacy bridge commands of this line keep their `ezpublish:` names.

**eZ Platform 3.3 / Ibexa 3.3 LTS.** Before 3.3 the vendor moved the project to the Symfony 5 layout (`config/`,
`templates/`, `public/`, no `AppBundle`), Symfony Flex and Composer 2.0.13 or newer, and the skeleton merge
(`git merge v3.3.43 --allow-unrelated-histories` from `ibexa/content-skeleton` and so on). On Exponential the
targets are Nexus `1.1.0.x` and Exponential Platform Legacy `v3.3.44.x` (both with the bridge 3.x), `se7enxweb/oss`
3.3 and `se7enxweb/exponential-platform` 3.2.9. Within 3.3 Symfony moved from 5.3 to 5.4 (3.3.13).

**Ibexa 4.x.** 4.0 renamed namespaces, configuration keys, Twig functions, routes and the REST prefix
([16.5.5](#1655-namespaces-bundles-and-configuration-keys), [16.5.7](#1657-admin-twig-field-types-rest-and-graphql)).
The minor updates up to 4.6 are mostly commercial: taxonomy, product catalog, corporate accounts, dashboards and
activity log migrations run through `ibexa:migrations:*`, which the open source edition does not have. For an open
source source system the database work of 4.x is the class group column, the token tables and the `revoked` column;
the rest is the Composer update. On Exponential the target is Nexus `1.2.0.x` or the 4.6 skeleton. 4.6 needs Node.js
18 or newer for the asset build; the Exponential 4.6 skeleton guide uses Node.js 20.

**Ibexa 5.0.** PHP 8.3 or newer, Symfony 7.3 (7.4 from 5.0.7, where `cache:clear` no longer clears the persistence
cache: use `php bin/console cache:pool:clear <pool>`). The tables are `ibexa_*`. On Exponential: Nexus `1.3.0.x` (PHP
8.4 or newer) or the v5 skeleton (8.3 or newer); every renamed kernel command is `exponential:*` only. The
Exponential v5 kernel is `se7enxweb/exponential-platform-dxp-core`, the installed reference uses v5.0.7.

## 16.6 Path B: down to the Exponential 6.0 legacy kernel

### 16.6.1 What the two share

The content model of every generation is the legacy kernel's: content classes, objects, versions, attributes,
locations, URL aliases, roles, sections, states and languages are the same tables with the same meaning. A
comparison of the table lists (6.0.15 `kernel/sql/mysql/kernel_schema.sql`, 128 tables; 2.5 `data/mysql/schema.sql`;
3.3 and 4.6 `schema.yaml`; v5 `schema.yaml`):

| Tables | Legacy 6.0 | 2.5 | 3.3 / 4.6 | 5.0 |
|---|---|---|---|---|
| Content, classes, locations, versions, names, links, URL aliases, URLs, roles and policies, sections, states, languages, users, keywords, media, binary files, images, search words, trash, bookmarks, notifications of the Symfony stack (`eznotification`) | yes (except `eznotification`) | yes | yes, `ez*` | yes, renamed `ibexa_*` |
| Shop, workflow, collaboration, legacy notifications, information collection, RSS, PDF export, sessions, pending actions, scheduled scripts, view counter, wish list, VAT, discounts, orders | yes | yes | dropped by the vendor's 3.0 notes (about 80 tables) | no |
| `ezcontentclass_attribute_ml`, `ezgmaplocation`, `ibexa_setting`, `ibexa_token`, `ibexa_token_type` | no | `_ml` and `ezgmaplocation` | yes | renamed or kept |
| `expaudit_*`, `expmail_*`, `expbookmark_folder`, `ezrss_export_opml_item` | yes, added by 6.0.15 | no | no | no |

Columns that the Symfony stack added to shared tables: `ezcontentobject.is_hidden`, `ezuser.password_updated_at`,
`ezcontentclass_attribute.is_thumbnail`, `ezkeyword_attribute_link.version`,
`ezsearch_object_word_link.language_mask`. In the 4.6 schema all of them are nullable or have a default, so the
legacy kernel, which does not know them, can still insert rows; the exception is `ezkeyword_attribute_link.version`
after the MySQL 2.5 to 3.0 script, which adds it as `NOT NULL` without a default. The legacy kernel inserts keyword
links with `INSERT INTO ezkeyword_attribute_link ( keyword_id, objectattribute_id )`
(`kernel/classes/datatypes/ezkeyword/ezkeyword.php`), which then fails in strict SQL mode; give the column a default
([16.6.4](#1664-the-database-step-by-step)). Columns the legacy kernel added: `ezcontentbrowsebookmark.folder_id` and
`priority`, `ezpdf_export.show_footer` and `footer_text`, `ezrss_export.opml_head` and `podcast_head` (6.0.15).

### 16.6.2 What you lose and what you gain

| You lose | You gain |
|---|---|
| The Symfony admin UI, its content tree, UDW and the CKEditor 5 online editor | The legacy admin (`admin3`, responsive), the Online Editor (`ezoe`) for XmlText |
| Twig templates, Symfony controllers, the Netgen Site API | TPL templates with the override system, template fetch functions, modules and views |
| RichText as stored DocBook, until converted | XmlText with custom tags, the legacy content datatypes (Matrix, Option, Price, Star Rating with its extension, ezflow Page) |
| REST v2 and GraphQL of the Symfony stack | The legacy REST layer (`rest.ini`), the legacy JSON calls of `ezjscore` |
| Page Builder, Form Builder, workflow (commercial) | The legacy workflow events, information collection, shop, RSS import and export, newsletters (`cjw_newsletter`) |
| Netgen Layouts on Symfony | Exponential Layouts, the legacy port of Netgen Layouts ([16.6.7](#1667-a-worked-port-netgen-layouts-and-the-media-site)) |
| Messenger, Symfony cron | `runcronjobs.php` and the cronjob parts |
| | Velocity as the web server, SQLite as a production engine, MongoDB and Oracle drivers, the audit log and the mail preference subsystem of 6.0.15 |

### 16.6.3 Field types

The legacy kernel registers its datatypes in `content.ini` (`[DataTypeSettings] AvailableDataTypes[]`, 35 in the
default file; extensions add more). Every `data_type_string` your inventory shows needs a legacy datatype, or the
legacy kernel cannot load the field.

| Symfony field type | Legacy 6.0 | What to do |
|---|---|---|
| `ezstring`, `eztext`, `ezinteger`, `ezfloat`, `ezboolean`, `ezemail`, `ezurl`, `ezdate`, `ezdatetime`, `eztime`, `ezcountry`, `ezisbn`, `ezauthor`, `ezkeyword`, `ezselection`, `ezbinaryfile`, `ezmedia`, `ezimage`, `ezobjectrelation`, `ezobjectrelationlist`, `ezuser` | same identifier, same storage | nothing |
| `ezgmaplocation` | the `ezgmaplocation` extension (same table) | activate the extension |
| `eztags` (Netgen Tags) | the `eztags` extension | activate it; same tables |
| `ezxmltext` | built in | nothing; this is the legacy format |
| `ezrichtext` (DocBook) | no datatype | convert, or keep the DocBook and adapt the output (below) |
| `ezmatrix` (2.5 and later) | a legacy `ezmatrix` with a different format | no reverse conversion exists; export the values and re-enter them, or keep the field in the Symfony stack |
| `ezimageasset` | no datatype | change the field to `ezobjectrelation`: both keep the id of the target content item in `data_int`, so the value survives; check that `ezcontentobject_link` has the relation rows the legacy kernel expects |
| `ezcontentquery` | no datatype | replace with a template fetch (`fetch( 'content', 'list', ... )`) |
| `ezlandingpage`, `ezform` | no datatype (commercial) | rebuild with Exponential Layouts and information collection; remove the field |
| 5.0 identifiers (`ibexa_string` and the like) | no | if the 5.0 database used the new identifiers, set them back (`UPDATE ezcontentclass_attribute SET data_type_string = 'ezstring' WHERE data_type_string = 'ibexa_string';` and the same on `ezcontentobject_attribute`) |

#### RichText to XmlText

No tool upstream or in the Exponential repositories converts RichText back to XmlText: the conversion of
`se7enxweb/ezplatform-xmltext-fieldtype` (`ezxmltext:convert-to-richtext`, `ezxmltext:import-xml`) goes one way, and
`ezplatform-richtext` has no XmlText output. The mapping you need is the reverse of the forward stylesheet
`lib/FieldType/XmlText/Input/Resources/stylesheets/eZXml2Docbook_core.xsl` of that package:

| RichText (DocBook) | XmlText |
|---|---|
| `<section>` (root) | `<section>` (root, with the `xmlns:image`, `xmlns:xhtml`, `xmlns:custom` namespaces of XmlText) |
| `<para>` | `<paragraph>` |
| `<literallayout>` | `<paragraph>` with `<line>` children |
| `<title ezxhtml:level="N">` | `<header level="N">` |
| `<emphasis>` | `<emphasize>` |
| `<emphasis role="strong">` | `<strong>` |
| `<emphasis role="underlined">`, `role="strikedthrough"` | `<custom name="underline">`, `<custom name="strike">` |
| `<subscript>`, `<superscript>` | `<custom name="sub">`, `<custom name="sup">` |
| `<blockquote>` | `<custom name="quote">` |
| `<itemizedlist>`, `<orderedlist>`, `<listitem>` | `<ul>`, `<ol>`, `<li>` |
| `<informaltable>` or `<table>` with `<caption>`, `<tbody>`, `<tr>`, `<th>`, `<td>` | `<table>` with `<tr>`, `<th>`, `<td>` |
| `<link xlink:href="ezurl://N">` | `<link url_id="N">` |
| `<link xlink:href="ezlocation://N">`, `ezcontent://N` | `<link node_id="N">`, `<link object_id="N">` |
| `<anchor>` | `<anchor>` |
| `<ezembed xlink:href="ezlocation://N">`, `ezcontent://N` (and `<ezembedinline>`) | `<embed node_id="N">`, `<embed object_id="N">` (and `<embed-inline>`) |
| `<eztemplate>`, `<eztemplateinline>` with `<ezcontent>` and `<ezconfig>` | `<custom name="...">` with its attributes |
| `<programlisting>` | `<literal>` |

Two ways to apply it:

1. **Convert the stored XML.** Write an XSL stylesheet or a script from the table, run it over
   `ezcontentobject_attribute.data_text` where `data_type_string = 'ezrichtext'` on a copy, validate the result with
   the legacy XmlText input handler, then change `data_type_string` to `ezxmltext` on the class attribute and on the
   object attributes. This is the only way that leaves the fields editable in the Online Editor.
2. **Keep the DocBook and render it.** This is what the port of the Netgen media site to the legacy kernel did: the
   imported fields are `ezxmltext` attributes that still hold DocBook. The 6.0 kernel renders the DocBook elements
   `ezembed` (with `xlink:href`), `para` and `literallayout` in its XHTML output handler (commit `d7bebcb39b`,
   "Render imported Ibexa docbook elements in XHTML output") and allows `class` attributes on them (`5857c5baa8`);
   the theme extension `se7enxweb/sevenx_themes_media` converts the remaining elements at render time
   (`convertDocBookToEzXml()` in `autoloads/sevenxthemesmediaoperators.php`: `para` to `paragraph`, `emphasis` to
   `emphasize`, the lists, `subscript`, `superscript`, `literallayout` to `literal`, `xlink:href` to `href`). It
   displays; it is not a basis for editing. Convert before editors touch those fields.

A third option, `se7enxweb/richtext-datatype-bundle` (from `NetgenRichTextDataTypeBundle`, required by the Platform
Legacy 2.5 and 4.6 lines), adds an `ezrichtext` datatype to the legacy kernel; its README says it is a prototype that
"only shows the raw XML content of the field in a text area".

### 16.6.4 The database, step by step

The order: make the Symfony-only data legacy-compatible on the source (field types above), then make the schema the
6.0.15 schema, then set the version rows, then rebuild what the legacy kernel keeps itself.

**Step 1: back up and stop the Symfony stack** (or put it in maintenance), as in [16.4](#164-before-you-start-inventory-freeze-and-backups).

**Step 2 (5.0 only): take the table names back.** Either reverse the renames of
[the rename table](#the-50-table-renames-and-their-reverse), or keep the `ibexa_*` names and let the legacy kernel
translate its SQL with `se7enxweb/sevenx_exponential_platform_v5_database_translator`, a legacy extension that
subclasses the MySQL, SQLite and PostgreSQL drivers and rewrites legacy names outbound and Ibexa names inbound (it
registers itself with `[DatabaseSettings] ImplementationAlias[...]` in its `site.ini.append.php`). The reverse
rename, MySQL 8.0 or MariaDB 10.5.2 and newer (for `RENAME COLUMN`), first lines:

```sql
RENAME TABLE ibexa_content TO ezcontentobject;
ALTER TABLE ezcontentobject RENAME COLUMN content_type_id TO contentclass_id;
RENAME TABLE ibexa_content_field TO ezcontentobject_attribute;
ALTER TABLE ezcontentobject_attribute RENAME COLUMN content_type_field_definition_id TO contentclassattribute_id;
RENAME TABLE ibexa_content_type TO ezcontentclass;
RENAME TABLE ibexa_content_type_field_definition TO ezcontentclass_attribute;
ALTER TABLE ezcontentclass_attribute RENAME COLUMN content_type_id TO contentclass_id;
-- ... one line per row of the rename table, columns included
```

PostgreSQL uses `ALTER TABLE ibexa_content RENAME TO ezcontentobject;` and the same `RENAME COLUMN` form. Index names
keep their `ibexa_` names unless you rename them too; the legacy kernel's queries do not name indexes, but
`ezsqldiff.php` in step 3 reports them as differences. Take the list
from your own `ibexa-4.6.latest-to-5.0.0.sql` (or the vendor page) rather than retyping it, and check the
`ezpreferences` row ([16.5.6](#1656-the-database-generation-by-generation)).

**Step 3: make the schema the 6.0.15 schema.** The legacy kernel ships its schema as `share/db_schema.dba` (128
tables, the 6.0.15 additions included) and a comparison tool, `bin/php/ezsqldiff.php`. It takes two schemas, a
`SOURCE` and a `MATCH`, each a live database or a schema file, and prints the SQL that **turns the second (`MATCH`)
into the first (`SOURCE`)**; `--reverse` swaps that. The direction is easy to get wrong and the result of a wrong
direction is a file full of `DROP TABLE` lines for the legacy tables, so put the reference schema first and your
database second:

```bash
php bin/php/ezsqldiff.php --type=mysql --host=HOST --user=USER --password=PASSWORD share/db_schema.dba DATABASE > to-6.0.15.sql
php bin/php/ezsqldiff.php --type=postgresql --host=HOST --user=USER --password=PASSWORD share/db_schema.dba DATABASE > to-6.0.15.sql
```

(`--type` accepts `mysql` and `postgresql`; `--check-only` only sets the exit status.) A quick sanity check of the
direction: `grep -c '^CREATE TABLE' to-6.0.15.sql` should be large on a 3.x or later database (the dropped legacy
tables) and `grep '^DROP TABLE' to-6.0.15.sql` should list only Symfony-side tables (`ibexa_*`, `ezcontentclass_attribute_ml`).
If it lists `ezbasket`, `ezworkflow` and the like, the arguments are the wrong way round. Read `to-6.0.15.sql` before
running it. It contains four kinds of statements:

- `CREATE TABLE` for the legacy tables your database lacks (all of the dropped ones on a 3.x or 4.x database, the
  `exp*` tables of 6.0.15 on every source): keep them;
- changed column definitions and index changes (`ALTER TABLE ... CHANGE`/`ALTER COLUMN`, `DROP INDEX`, `CREATE INDEX`):
  keep them after reading each one; a statement that makes a column narrower than the data in it fails in strict
  mode or cuts values, so compare it with the data first;
- `ALTER TABLE ... ADD` for legacy columns (`folder_id`, `show_footer`, `opml_head` ...): keep them;
- `DROP TABLE` and `DROP COLUMN` for what only the Symfony stack has (`ibexa_*`, `ezcontentclass_attribute_ml`,
  `is_thumbnail`, `password_updated_at` ...). Remove those lines while the Symfony stack may still be needed (rollback,
  or a bridge); the legacy kernel ignores them.

Then run it, without `--force`:

```bash
mysql -u USER -p DATABASE < to-6.0.15.sql
psql -U USER -d DATABASE -f to-6.0.15.sql
```

**Step 4: the column the legacy kernel cannot fill.** On a database that went through the MySQL 2.5 to 3.0 script:

```sql
ALTER TABLE ezkeyword_attribute_link MODIFY version INT(11) NOT NULL DEFAULT 0;   -- MySQL, MariaDB
ALTER TABLE ezkeyword_attribute_link ALTER COLUMN version SET DEFAULT 0;           -- PostgreSQL
```

**Step 5: the version rows.** 3.0 and later delete `ezpublish-version`; the legacy update files only `UPDATE` it, so
insert the rows:

```sql
DELETE FROM ezsite_data WHERE name IN ('ezpublish-version', 'ezpublish-release');
INSERT INTO ezsite_data (name, value) VALUES ('ezpublish-version', '6.0.15stable');
INSERT INTO ezsite_data (name, value) VALUES ('ezpublish-release', '1');
```

The values are the ones `update/database/*/6.0/dbupdate-6.0.0-6.0.15.sql` writes.

A 1.x or 2.x database still has an `ezpublish-version` row, but it does not name a legacy release: the Symfony
kernel's update files write their own kernel version into it (`6.4.0` from a fresh install, which the 2.5 clean data still writes, `6.13.0` after the
vendor's 5.4 to 6.13 file, `7.5.0` to `7.5.7` on 2.5). Do not read it as a legacy version and do not start the legacy
chain of [chapter 11](11-upgrading.md) from it: no legacy update file belongs to those numbers. A 2.5 database
already carries the schema changes of the upstream `6.12/`, `7.2/` and `7.3/` files that chapter 11 describes
([11.3](11-upgrading.md#the-612-72-and-73-directories)), so take steps 3 and 5 like every other source: the
`ezsqldiff.php` output of step 3 is short there (the `exp*` tables and columns of 6.0.15, a few indexes), and step 5
replaces the row.

**Step 6: settings, storage and caches.** Point `settings/override/site.ini.append.php` at the database
(`[DatabaseSettings] DatabaseImplementation`, `Server`, `Port`, `User`, `Password`, `Database`), copy the binary
storage from `public/var/<site>/storage` (or `web/var/...`) to `var/<VarDir>/storage` of the legacy root, keeping the
same `VarDir` name (`[FileSettings] VarDir`, `StorageDir`), because the image paths stored in the database start with
it. Then:

```bash
php bin/php/ezcache.php --clear-all
php bin/php/updatesearchindex.php --clean
```

The legacy search index (`ezsearch_word`, `ezsearch_object_word_link`) is the same engine as the Symfony stack's
legacy search, but rebuild it: the Symfony stack writes `language_mask` and the 5.0 column names, and a clean
rebuild is cheaper than checking. URL aliases need nothing; `php bin/php/updateniceurls.php` exists if the check in
[16.8](#168-verify-the-migration) finds missing ones.

**SQLite.** `ezsqldiff.php` speaks MySQL and PostgreSQL only. For an SQLite source, convert it to MySQL or PostgreSQL,
run the steps there, and move the result to the legacy kernel's SQLite driver with the conversion described in
[chapter 9](09-databases.md); or install 6.0 on SQLite and move the content at the application level.

**MySQL character set.** The Symfony stack uses utf8mb4 connections since 2.2. The legacy kernel's default
`Charset=utf-8` maps to MySQL's three-byte `utf8` on the connection, so characters outside the Basic Multilingual
Plane (emoji) cannot pass through it ([chapter 9](09-databases.md)). Check content for them before the move.

### 16.6.5 Configuration: siteaccess YAML to INI

| Symfony (YAML, 4.x / 5.0 keys) | Legacy (INI) |
|---|---|
| `ibexa.siteaccess.list: [site, admin]` | `settings/override/site.ini.append.php`: `[SiteSettings] SiteList[]=site`, `[SiteAccessSettings] AvailableSiteAccessList[]=site`, `AvailableSiteAccessList[]=admin` |
| `ibexa.siteaccess.default_siteaccess: site` | `[SiteSettings] DefaultAccess=site` |
| `ibexa.siteaccess.match: { URIElement: 1 }` | `[SiteAccessSettings] MatchOrder=uri`, `URIMatchType=element` |
| `ibexa.siteaccess.match: { Map\Host: { www.example.com: site } }` | `[SiteAccessSettings] MatchOrder=host`, `HostMatchType=map`, `HostMatchMapItems[]=www.example.com;site` |
| `ibexa.siteaccess.groups` | no groups: settings shared by siteaccesses go into `settings/override/`, the rest into `settings/siteaccess/<name>/` |
| `ibexa.system.<sa>.languages: [eng-GB, ger-DE]` | `settings/siteaccess/<sa>/site.ini.append.php`: `[RegionalSettings] Locale=eng-GB`, `ContentObjectLocale=eng-GB`, `SiteLanguageList[]=eng-GB`, `SiteLanguageList[]=ger-DE` |
| `ibexa.system.<sa>.design: app` | `[DesignSettings] SiteDesign=app`, `AdditionalSiteDesignList[]=...` |
| `ibexa.system.<sa>.content.view_cache: true` | `[ContentSettings] ViewCaching=enabled` |
| `ibexa.system.<sa>.image_variations` | `image.ini.append.php`: `[AliasSettings] AliasList[]=name`, then a `[name]` block with `Reference=` and `Filters[]=` |
| `ibexa.system.<sa>.content_view` rules | `override.ini.append.php` blocks |
| `ibexa.url_alias.slug_converter.transformation: urlalias` | `[URLTranslator] TransformationGroup=urlalias` (the default of both) |
| `DATABASE_URL` in `.env.local` | `[DatabaseSettings]` in `settings/override/site.ini.append.php` |
| `ibexa.system.<sa>.languages` with untranslated fallback | `[RegionalSettings] ShowUntranslatedObjects`, `SiteLanguageList[]` order |

An image variation, both ways (the filter names are shared, the vendor's migration page shows the same pair):

```yaml
ibexa:
    system:
        site_group:
            image_variations:
                articleimage:
                    reference: null
                    filters:
                        - { name: geometry/scalewidth, params: [770] }
```

```ini
# settings/siteaccess/site/image.ini.append.php
[AliasSettings]
AliasList[]=articleimage

[articleimage]
Reference=
Filters[]=geometry/scalewidth=770
```

A content view rule, both ways:

```yaml
ibexa:
    system:
        site_group:
            content_view:
                full:
                    article:
                        template: '@ibexadesign/full/article.html.twig'
                        match:
                            Identifier\ContentType: article
```

```ini
# settings/siteaccess/site/override.ini.append.php
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

### 16.6.6 Templates: Twig to TPL

TPL does not escape output by itself: every value that comes from content or from the request gets `|wash`. That
is the one rule that, when forgotten, turns a port into a cross-site scripting hole.

| Twig (Symfony stack) | TPL (legacy) |
|---|---|
| `{{ ibexa_content_name(content) }}`, `{{ location.contentInfo.name }}` | `{$node.name|wash}` |
| `{{ ibexa_render_field(content, 'body') }}`, `{{ ng_render_field(content.fields.body) }}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| `ibexa_field_value(content, 'title')` | `$node.data_map.title.content` |
| `ibexa_field_is_empty(content, 'image')` | `$node.data_map.image.has_content|not` |
| `ibexa_image_alias(field, versionInfo, 'medium').uri`, `ng_image_alias(field, 'medium')` | `{$node.data_map.image.content['medium'].url|ezroot}`, or `{attribute_view_gui attribute=$node.data_map.image image_class='medium'}` |
| `{{ ibexa_path(location) }}`, `{{ path(location) }}` | `{$node.url_alias|ezurl}` (`|ezurl('no')` without quotes) |
| `{{ ibexa_url(location) }}` (absolute) | `{$node.url_alias|ezurl('no', 'full')}` |
| `{{ asset('build/app.css') }}` | `{'stylesheets/app.css'|ezdesign}` |
| `{{ 'Read more'|trans }}` | `{'Read more'|i18n('design/site')}` |
| `{% set x = ... %}` | `{def $x=...}` (first time), `{set $x=...}` |
| `{% if %}{% elseif %}{% else %}{% endif %}` | `{if}{elseif}{else}{/if}` |
| `{% for item in items %}{% endfor %}` | `{foreach $items as $item}{/foreach}` |
| `{% include 'parts/teaser.html.twig' with {item: item} %}` | `{include uri='design:parts/teaser.tpl' item=$item}` |
| `{% extends 'pagelayout.html.twig' %}` with blocks | the siteaccess `pagelayout.tpl`, which prints `{$module_result.content}` |
| `{{ render(controller('App\\Controller\\X::list')) }}` | a `fetch()` in the template, a template operator, or a module view |
| a query type behind a content view | `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'class_filter_type', 'include', 'class_filter_array', array( 'article' ), 'limit', 10, 'sort_by', $node.sort_array ) )` |
| `{{ object.published|date('d.m.Y') }}` | `{$node.object.published|l10n('shortdate')}`, or `|datetime('custom', '%d.%m.%Y')` |
| `{{ dump(x) }}` | `{$x|attribute(show)}` |
| `nglayouts_render_zone`, a layout | `fetch( 'explayouts', 'resolve_layout', hash() )` and `{include uri='design:explayouts/layout.tpl' layout=$layout}` |

With `se7enxweb/ngsymfonytools` and the legacy bridge, a TPL template can also include a Twig template
(`symfony_include`) during a transition; see [Site bundles](../features/6.0/platform-site-bundles.md) and
[ngsymfonytools](../features/6.0/extensions/ngsymfonytools.md). For the template system itself read the
[templates and design guide](../guides/templates-and-design.md).

### 16.6.7 A worked port: Netgen Layouts and the media site

The Exponential installation behind this book went this way with a whole site: the Netgen media site as it runs on
the Symfony stack (the Nexus reference) was ported to the 6.0 legacy kernel, with its layouts, its design and its
content. The approach, in the order it was done:

1. **The page builder became legacy extensions.** Exponential Layouts (`se7enxweb/explayouts` and its sibling
   repositories `explayouts_core`, `explayouts_standard`, `explayouts_site_api`, `explayouts_ui`,
   `explayouts_ui_api` and others) re-implements Netgen Layouts for the legacy kernel: `eZPersistentObject` classes on
   `explayouts_*` tables instead of Doctrine on `nglayouts_*`, block and layout types in `explayouts.ini` instead of
   YAML, block templates in the design cascade instead of Twig, rules resolved by `expLayoutsResolver`, and the
   editor as a legacy module. "The port follows the Netgen Layouts data shape, so a Netgen XML/JSON export can be
   imported with minimal transformation" ([Exponential Layouts](../bc/6.0/LAYOUTS.md#migration-and-parity),
   [import and export](../bc/6.0/LAYOUTS.md#import-export-and-share-links)).
2. **The data moved by id.** Rules, collections and manual collection items refer to content and locations by id,
   so the ids were carried over consistently (remapped where they had to change) together with the content; a
   layout block then shows the same content after the move.
3. **The Twig design became TPL by hand.** The media design lives in `se7enxweb/sevenx_themes_media`; a converter
   produced the first drafts, but the templates have been hand-maintained since, and the extension's own FAQ says
   they must not be regenerated. Expect the same: a Twig-to-TPL converter gets the syntax, not the semantics (field
   rendering, image aliases, escaping, the fetch logic behind query types).
4. **RichText stayed DocBook,** rendered as described in [16.6.3](#1663-field-types).
5. **Shared layouts and linked zones** (header, footer, pre-footer) were rebuilt as shared layouts whose zones other
   layouts link to ([Exponential Layouts](../bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones)).

Read [Exponential Layouts](../bc/6.0/LAYOUTS.md) for the data model and the editor, the
[layouts editor API](../specifications/6.0/explayouts-ui-api.md) for the JSON interface, and the
[layouts-core fork](../features/6.0/platform-layouts-core-fork.md) for the Symfony side.

### 16.6.8 Keeping a Symfony stack next to the legacy kernel

You do not have to switch the Symfony stack off. Exponential Platform Legacy and Nexus 1.0.0.x, 1.1.0.x and 1.2.0.x run both
kernels on one database through the legacy bridge: the `legacy_admin` siteaccess (`legacy_mode: true`) serves the
legacy admin, the other siteaccesses serve Twig, and editors' changes appear on both sides. Pick the bridge branch
that matches the platform generation:

| Platform | Bridge (`se7enxweb/legacy-bridge`) | Requires |
|---|---|---|
| eZ Platform 2.5 | `master` (`v2.1.10` to `v2.1.12`; no `2.1.x` branch) | `se7enxweb/exponential ^6.0.10`, `se7enxweb/ezpublish-kernel ^7.5.24` |
| eZ Platform 3.3 | `3.x` (`3.0.0.1` to `v3.0.0.37`) | PHP `^8.0`, `se7enxweb/exponential ^6.0.12`, kernel `~1.3`, Symfony 5.4 |
| Ibexa 4.6 compatible | `4.x` (`v4.0.0.0` to `v4.0.0.3`) | PHP `^8.0`, `se7enxweb/exponential dev-main` |
| Platform v5 | `5.x` (`v5.0.0.0`, `v5.0.1` to `v5.0.10`) | PHP `^8.4`, `ibexa/core ^5.0` (the v5 core fork replaces it), Symfony 7.4, `se7enxweb/exponential dev-main`, `se7enxweb/sevenx_exponential_platform_v5_database_translator` (`v5.0.10`) |

The configuration root is `ez_publish_legacy:` (`enabled`, `root_dir`, `legacy_aware_routes`,
`clear_all_spi_cache_from_legacy`, `clear_all_spi_cache_on_symfony_clear_cache`, and per siteaccess `legacy_mode`
and `templating.view_layout` / `module_layout`), the commands are in [16.5.4](#1654-console-command-names), and
installation and caching are in the [legacy bridge](../features/6.0/legacy-bridge.md) page and its
[specification](../specifications/6.0/legacy-bridge-bundle.md). On a 5.0 database the legacy half needs the table
names of [16.6.4](#1664-the-database-step-by-step) step 2.

This is also the gentlest version of path B: keep the Symfony site live, give editors the legacy admin, port the
templates one content type at a time, and switch the public siteaccess to `legacy_mode: true` (or to Exponential 6.0
alone) when the last template is done.

### 16.6.9 Notes per source version for path B

| Source | Schema step | Field types | Other |
|---|---|---|---|
| 1.x, 2.x up to 2.5 | the legacy schema is complete; `ezsqldiff.php` adds the 6.0.15 tables and columns; version rows (the old row names a Symfony kernel version, not a legacy one) | `ezrichtext` from 2.x on (XmlText content of older sites may still be XmlText) | password hashes all accepted |
| 3.0 to 3.3 | `ezsqldiff.php` recreates the dropped tables; version rows; keyword link default | `ezrichtext`, `ezimageasset`, `ezmatrix` (new format) | sort fields 6 and 7 work again |
| 4.0 to 4.6 | as 3.3; `ibexa_*` side tables can stay | as 3.3 | REST clients on `/api/ibexa/v2` need the legacy REST layer |
| 5.0 | reverse the renames or use the translator extension, then as 4.6 | identifiers back to `ez*` if they were changed | the Exponential v5 installer already creates the legacy tables on fresh installs |

## 16.7 Common issues

The first five are the vendor's [common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/);
the vendor runs their cleanup commands from `EzPublishMigrationBundle`, enabled in the `dev` environment, a bundle
that 3.0 dropped. Back up before any of them.

| Issue | Cause | Fix on the Symfony stack (Exponential) | Fix on the legacy kernel |
|---|---|---|---|
| URL aliases missing or wrong after the migration | aliases created by an older system or a different transformation | `php bin/console exponential:urls:regenerate-aliases` (v5; `ibexa:urls:regenerate-aliases` on 3.x/4.6), which keeps history | `php bin/php/updateniceurls.php` (`--update-nodes`, `--import`), or republish the location |
| Images do not display | image file paths with unprintable UTF-8 characters | `php bin/console exponential:images:normalize-paths` (the vendor writes `ezplatform:images:normalize-path`); check `var_dir` for special characters | the legacy kernel reads the same paths; fix them on the Symfony side first, or rename the files and the `ezimagefile` rows |
| "Unknown relation type 0" through REST after editing | relations with type 0 left by older systems | the vendor's `ezpublish:update:legacy_storage_clean_up_relation_type_eq_zero` (list, dry run, fix) from the dropped migration bundle; on 3.x and later run it on a 2.5 installation first, or delete the rows: `DELETE FROM ezcontentobject_link WHERE relation_type = 0;` after checking them | the legacy kernel does not use the REST API of the Symfony stack; the rows are harmless |
| Language filtering wrong in the legacy search engine | content always available in several translations with the flag on every field instead of the main language | the vendor's `ezpublish:update:legacy_storage_fix_fields_always_available_flag` | rebuild the legacy index (`updatesearchindex.php --clean`); the legacy kernel reads the flag from the object's language mask |
| Sub-items empty, searches fail | empty `sort_key_string` after the upgrade | the vendor's `ezpublish:update:legacy_storage_update_sort_keys` | republish, or rebuild the search index |
| Sub-items tab errors for some locations | sort by content type identifier (6) or name (7), unsupported by the Symfony stack | before migrating, change those locations to name, published or priority (query in [16.4](#164-before-you-start-inventory-freeze-and-backups)) | none: the legacy kernel supports both (`SORT_FIELD_CLASS_IDENTIFIER = 6`, `SORT_FIELD_CLASS_NAME = 7`) |
| Exception in the landing page migration | internal drafts of landing pages without an `ezcontentobject_name` row | in `content.ini` set `InternalDraftsCleanUpLimit=0` and every `InternalDraftsDuration[...]` entry (`days`, `hours`, `minutes`, `seconds`) to 0, then run the `internal_drafts_cleanup` cron job on the legacy side before the move | same cron job (`cronjobs/internal_drafts_cleanup.php`) |
| Users cannot sign in after the move to 3.x or later | MD5 or plain text hashes (types 1 to 5) | `exponential:user:validate-password-hashes`, then a password reset | none: 6.0 accepts all and rehashes on sign-in (`UpdateHash=true`) |
| Login page without styles, anonymous users denied | the `user/login` policy of Anonymous lacks the new siteaccess names | add every siteaccess to the Anonymous role's `user/login` limitation; the v5 kernel removed the siteaccess limitation from the Anonymous login policy of its seed data | same policy in the legacy admin, Roles |
| `ibexa:reindex` (or another command) "is not defined" on Platform v5 | the v5 kernel registers the `exponential:` names only | use `exponential:reindex` and the other primary names ([16.5.4](#1654-console-command-names)) | not applicable |
| Two copies of a class, random behaviour | an upstream package installed next to its fork | `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"`, remove the upstream requirement | not applicable |
| Keyword field cannot be saved in the legacy admin (MySQL) | `ezkeyword_attribute_link.version` `NOT NULL` without default | not applicable | step 4 of [16.6.4](#1664-the-database-step-by-step) |
| Legacy kernel reports a missing table on the first page | a 5.0 database with `ibexa_*` names | not applicable | reverse the renames or activate the translator extension |
| Fields shown as raw XML, or empty, after path B | `ezrichtext` content without a legacy datatype | not applicable | convert or render ([16.6.3](#1663-field-types)) |
| "Failed to create closure from callable ... SilvercommonExtension" during the 4.1 update | commerce leftovers | ignore, as the vendor says | not applicable |
| "non-existent parameter" or "non-existent service payum.storage.doctrine.orm" after 4.6 | bundle order in `config/bundles.php`, old `payum.yaml` | take `config/bundles.php` and `payum.yaml` from the skeleton of the version you install | not applicable |
| Old pages after the switch | HTTP cache (Varnish, Symfony proxy) or the legacy view cache | `fos:httpcache:invalidate:path / --all`, `cache:pool:clear` | `php bin/php/ezcache.php --clear-all` |

## 16.8 Verify the migration

On the Symfony stack (path A):

```bash
php bin/console list exponential                         # the exponential:* commands are registered
php bin/console debug:router | head                      # routes load
php bin/console exponential:debug:config-resolver languages --scope=site
composer why ibexa/core                                  # expect se7enxweb/exponential-platform-dxp-core on v5
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/adminui/
```

On the legacy kernel (path B):

```bash
php bin/php/ezsqldiff.php --type=mysql --host=HOST --user=USER --password=PASSWORD --check-only share/db_schema.dba DATABASE; echo $?
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
```

Content checks for both: count the published objects, locations and URL aliases before and after, and compare them.

```sql
SELECT COUNT(*) FROM ezcontentobject WHERE status = 1;
SELECT COUNT(*) FROM ezcontentobject_tree;
SELECT COUNT(*) FROM ezurlalias_ml WHERE is_original = 1 AND is_alias = 0;
```

Then open a sample of pages of every content type, search for a word you know is on the site, sign in as an editor,
create, edit and publish a test item and delete it, upload an image and see its variations, and check the cron log
after the first run. For path B also compare a page's HTML in the old and the new system side by side; differences
in rendered RichText show up there first.

## 16.9 Rollback

A migration is rolled back by putting the old system back, not by undoing statements.

- **Keep the old code and its server untouched** until the new system has run through at least one full editorial
  cycle. Switch traffic with DNS, the proxy or the virtual host, so switching back is the same operation reversed.
- **Keep the database dump of [16.4](#164-before-you-start-inventory-freeze-and-backups)** and the storage archive.
  Restoring them on the old server is the rollback; content edited on the new system after the switch is lost unless
  you re-enter it, which is the reason for a freeze and a short decision window.
- **Path B with the Symfony tables kept** (the `DROP` lines removed in [16.6.4](#1664-the-database-step-by-step)) can
  return to the Symfony stack without a restore: the shared tables are the same. Rebuild the search index and clear
  the caches there; content created in the legacy kernel in the meantime appears, except where it uses legacy-only
  datatypes.
- **Path A** within one generation is a Composer change: restore `composer.json` and `composer.lock`, install,
  clear the caches.

## 16.10 A plan and its phases

The duration is driven by three numbers from the inventory: the number of custom bundles and templates, the
number of fields of types without a counterpart on the target, and the size of the content tables (which decides the
database and reindex time). Measure the last one with a full dry run on a copy; plan the freeze as that time plus a
margin.

| Phase | Path A | Path B |
|---|---|---|
| 1. Inventory and decision | [16.4](#164-before-you-start-inventory-freeze-and-backups), [16.2](#162-choosing-a-path) | same |
| 2. Source to the last release of its line | vendor steps, on the source | same |
| 3. Code | package swap, command names, renames when the generation changes | template and configuration port, custom code as extensions |
| 4. Data | none within a generation; vendor SQL across | field type conversions, `ezsqldiff.php`, version rows |
| 5. Dry run on a copy, timed | full sequence | full sequence |
| 6. Freeze, cut-over, verification | [16.8](#168-verify-the-migration) | same |
| 7. Decision window, then cleanup | remove development tools (Rector, compatibility layer) | drop the Symfony-only tables if the Symfony stack is retired |

## 16.11 Checklist

- [ ] Inventory taken: versions, field types, sort fields, hash types, languages, sizes, bundles, siteaccesses, search, cache, cron, PHP.
- [ ] Destination chosen with the decision table; the generation of the target matches the source.
- [ ] Backups made and restored once on a scratch machine.
- [ ] Source at the last release of its line, database scripts applied without `--force`.
- [ ] Path A: forks installed, no upstream duplicates, Flex recipes from `se7enxweb/sevenx-recipes`.
- [ ] Path A: cron and deployment scripts use the `exponential:` commands (mandatory on v5).
- [ ] Path A: REST clients on the generation's prefix, GraphQL schema regenerated.
- [ ] Path A: landing pages rebuilt with Netgen Layouts, or kept out of scope.
- [ ] Path B: every `data_type_string` has a legacy datatype; RichText converted or rendered.
- [ ] Path B: 5.0 names reversed or translator active; `ezsqldiff.php` output reviewed and run; keyword link default; version rows.
- [ ] Path B: INI settings for siteaccesses, languages, designs, image aliases and overrides; storage copied with the same `VarDir`.
- [ ] Path B: templates ported, every output washed.
- [ ] Search index rebuilt, caches cleared, HTTP cache purged.
- [ ] Verification of [16.8](#168-verify-the-migration) passed; editors signed in and published.
- [ ] Rollback route tested, decision window set.

## References

In this repository:

- [11. Upgrading](11-upgrading.md) (the 6.0 update files), [9. Databases](09-databases.md), [8. Serving the site](08-serving-the-site.md), [1. Introduction](01-introduction.md).
- Chapters of this part: [14. Migrating from 4.x](14-migrating-from-4x.md), [15. Migrating from 5.x legacy](15-migrating-from-5x-legacy.md), [17. Migration reference](17-migration-reference.md).
- Platform: [Package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md),
  [Platform package map](../specifications/6.0/platform-package-map.md),
  [Platform console command names](../specifications/6.0/platform-console-commands.md),
  [Platform SQLite installer](../specifications/6.0/platform-sqlite-installer.md),
  [Exponential Platform Nexus](../features/6.0/platform-nexus-starter.md),
  [DXP skeleton](../features/6.0/platform-dxp-skeleton.md),
  [Platform administration interface](../features/6.0/platform-admin-ui-fork.md),
  [Layouts on the platform](../features/6.0/platform-layouts-core-fork.md),
  [PHP 8.4 / 8.5 framework forks](../features/6.0/platform-php85-framework-forks.md),
  [Site bundles](../features/6.0/platform-site-bundles.md),
  [SQLite for Exponential Platform](../features/6.0/platform-sqlite-install.md).
- Legacy bridge: [Legacy bridge](../features/6.0/legacy-bridge.md), [bundle specification](../specifications/6.0/legacy-bridge-bundle.md), [ngsymfonytools](../features/6.0/extensions/ngsymfonytools.md).
- Layouts: [Exponential Layouts](../bc/6.0/LAYOUTS.md), [layouts editor API](../specifications/6.0/explayouts-ui-api.md).
- [Templates and design guide](../guides/templates-and-design.md).
- Changelogs and history: [Exponential Platform Nexus](../changelogs/extensions/exponential-platform-nexus.md),
  [Exponential Platform Legacy](../changelogs/extensions/exponential-platform-legacy.md),
  [XmlText field type](../changelogs/extensions/ezplatform-xmltext-fieldtype.md),
  [legacy bridge history](../history/ecosystem/legacyBridge.md).
- Code: `share/db_schema.dba`, `kernel/sql/*/`, `bin/php/ezsqldiff.php`, `update/database/*/6.0/`,
  `kernel/classes/datatypes/ezuser/ezuser.php` (hash types), `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`,
  `settings/site.ini`, `settings/content.ini`, `settings/image.ini`.

Exponential repositories on GitHub:

- [se7enxweb/exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus) and its [releases](https://github.com/se7enxweb/exponential-platform-nexus/releases),
  [se7enxweb/exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter),
  [se7enxweb/exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy) and its [releases](https://github.com/se7enxweb/exponential-platform-legacy/releases),
  [se7enxweb/exponential-platform](https://github.com/se7enxweb/exponential-platform) (the `upgrade/db/` scripts),
  [se7enxweb/exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp),
  [se7enxweb/oss](https://github.com/se7enxweb/oss), [se7enxweb/core](https://github.com/se7enxweb/core).
- [se7enxweb/LegacyBridge](https://github.com/se7enxweb/LegacyBridge) (package `se7enxweb/legacy-bridge`),
  [se7enxweb/ezplatform-xmltext-fieldtype](https://github.com/se7enxweb/ezplatform-xmltext-fieldtype),
  [se7enxweb/sevenx_exponential_platform_v5_database_translator](https://github.com/se7enxweb/sevenx_exponential_platform_v5_database_translator).
- [se7enxweb/explayouts](https://github.com/se7enxweb/explayouts), [se7enxweb/sevenx_themes_media](https://github.com/se7enxweb/sevenx_themes_media),
  [se7enxweb/exponential](https://github.com/se7enxweb/exponential).

The vendor's documentation (the source system):

- Migration: [Migrating from the 4.x and 5.x legacy stack (vendor page)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/),
  [Migrating from the 5.x platform stack (vendor page)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
  (also [latest](https://doc.ibexa.co/en/latest/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
  and the [2.5 version](https://doc.ibexa.co/en/2.5/migrating/migrating_from_ez_publish_platform/)),
  [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/).
- Updates: [Updating Ibexa DXP](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/),
  [from 1.13 and 2.x](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_from_1.x_2.x/),
  [app to 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_app_to_2.5/),
  [database to 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_db_to_2.5/),
  [from 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/update_from_2.5/),
  [to 3.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.2/),
  [adapt code to v3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/adapt_code_to_v3/),
  [to 3.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.3/),
  [3.3 to 3.3.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/update_from_3.3/),
  [3.3 to 4.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/to_4.0/),
  [4.0 to 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.0/to_4.1/),
  [from 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.1/update_from_4.1/),
  [from 4.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.2/update_from_4.2/),
  [from 4.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.3/update_from_4.3/),
  [from 4.4](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.4/update_from_4.4/),
  [from 4.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.5/update_from_4.5/),
  [4.6 to 4.6.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_from_4.6/),
  [4.6 to 5.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_to_5.0/),
  [5.0 to 5.0.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_5.0/update_from_5.0/),
  [3.3 update overview](https://doc.ibexa.co/en/3.3/update_and_migration/update_ibexa_dxp/).
- Breaking changes: [eZ Platform v3.0 deprecations](https://doc.ibexa.co/en/5.0/release_notes/ez_platform_v3.0_deprecations/),
  [Ibexa DXP v4.0 deprecations](https://doc.ibexa.co/en/5.0/release_notes/ibexa_dxp_v4.0_deprecations/),
  the [compatibility layer mappings](https://github.com/ibexa/compatibility-layer/tree/4.0/src/bundle/Resources/mappings),
  the kernel's [6.0 API changes](https://github.com/ezsystems/ezpublish-kernel/blob/v6.7.0/doc/bc/changes-6.0.md) and
  [7.2 upgrade notes](https://github.com/ezsystems/ezpublish-kernel/blob/7.5/doc/upgrade/7.2.md).
- Upstream repositories: [github.com/ezsystems](https://github.com/ezsystems), [github.com/ibexa](https://github.com/ibexa),
  [ezsystems/LegacyBridge](https://github.com/ezsystems/LegacyBridge), [ibexa/recipes](https://github.com/ibexa/recipes).

Frameworks and PHP:

- Symfony upgrade notes: [UPGRADE-4.0](https://github.com/symfony/symfony/blob/4.4/UPGRADE-4.0.md),
  [UPGRADE-5.0](https://github.com/symfony/symfony/blob/5.0/UPGRADE-5.0.md),
  [UPGRADE-6.0](https://github.com/symfony/symfony/blob/6.0/UPGRADE-6.0.md),
  [UPGRADE-7.0](https://github.com/symfony/symfony/blob/7.0/UPGRADE-7.0.md),
  [upgrading a major version](https://symfony.com/doc/current/setup/upgrade_major.html).
- PHP migration guides: [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
  [8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
  [8.5](https://www.php.net/manual/en/migration85.php).
- [Composer replace](https://getcomposer.org/doc/04-schema.md#replace), [MySQL RENAME TABLE](https://dev.mysql.com/doc/refman/8.4/en/rename-table.html),
  [PostgreSQL ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html).

[Contents](README.md) · Previous: [15. Migrating from the 5.x legacy stack](15-migrating-from-5x-legacy.md) · Next: [17. Migration reference](17-migration-reference.md)
