# Ecosystem repository: exponential-platform-nexus-starter

**Group:** Distributions and starters. **Period in the ledger:** 2023-12-13 to 2026-04-26. **Changes:** 248 (16 made by the se7enxweb team, 232 upstream history carried by the fork).

## What it is

Nexus for Platform v5 (Symfony 7.4, PHP 8.4+, SQLite starter).

## How it relates to Exponential

Skeleton for new Platform v5 Nexus projects; uses the se7enxweb forks of layouts-core, richtext and admin-ui.

## What a user gets

composer + one install command gives a v5 site with layouts, REST, GraphQL and admin.

## Where to read more

- [Nexus starter](../../features/6.0/platform-nexus-starter.md)
- [Release changelog](../../changelogs/extensions/exponential-platform-nexus-starter.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 118 |
| Fixes | 23 |
| Behaviour and upgrade changes | 1 |
| Documentation | 15 |
| Tooling | 41 |
| Releases | 1 |
| No user benefit | 49 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-26 | 1.0.0.0 | `40098be54` | Update project title and description in README |

## Changes made by the se7enxweb team, by theme

### Composer requirements (3)

- 2026-03-12 `6546733e6` tooling: Update project name, license, and description in composer.json
- 2026-04-19 `8e3aea2a7` tooling: resolve dependency installation failures on fresh project creation
- 2026-04-19 `7047d3f53` tooling: correct sevenx-recipes Flex endpoint ref from flex/main to master

### Symfony and platform compatibility (2)

- 2026-03-13 `ee6e72d14` fix: Upgraded from Symfony 7.3 (End of Life / Support) to Symfony 7.4. Upgrade.
- 2026-04-19 `b69680701` fix: Remove section on 7x Forks & Upstream Incompatibility Fixes

### Other changes to the fork (2)

- 2026-03-13 `450c92332` fix: Upgraded repo node version from node 18 to node 22. Upgrade
- 2026-04-19 `94c7a8fbf` fix: chore(deps): bump se7enxweb/exponential-platform-dxp-core v5.0.6 → v5.0.7

### SQLite support (2)

- 2026-04-19 `41883758e` feature: v5 Ibexa OSS on Symfony 7.4, PHP 8.4, SQLite dev db
- 2026-04-26 `829e63b43` feature: Importing from exponential-platform-nexus .x Default installation configured for instant use using sqlite as database.

### Documentation (2)

- 2026-04-19 `1aa134eaf` docs: add full 7x INSTALL guide and update README with DB conversion stub
- 2026-04-26 `40098be54` docs: Update project title and description in README

### Repository housekeeping (2)

- 2026-04-26 `4bb4dab3a` tooling: Removed vendor from .gitignore
- 2026-04-26 `385b756c8` tooling: Removed var from being specifically referenced in .gitignore

### Bug fixes (1)

- 2026-04-19 `982545fa8` fix: restore Netgen Layouts bundles/routes after recipe unconfigure; install se7enxweb/layouts-core

Also: 2 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 7 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `539baa6b8` NGSTACK-817 Add nvm statements to ibexa-assets command; `868135b37` NGSTACK-1 fix broken JS in cookie ribbon |
| 2024-01 | 4 | 2 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | `2938bda29` IOTA-649 add internal and external link option for embeded images; `d58326640` Disable phpdoc FQCN imports |
| 2024-02 | 13 | 4 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `60190a2b2` Upgrade to Ibexa 4.6; `20ea872ee` Upgrade to Ibexa 4.6 |
| 2024-03 | 40 | 14 | 5 | 0 | 0 | 0 | 0 | 11 | 0 | 10 | `8b2cb1d9a` NGSTACK-786 upgrade swiper 4 > 11; `5d0564588` NGSTACK-492 introduce simple smoke tests |
| 2024-04 | 12 | 6 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `5ba65e2ff` Add toolbar macro to block item view type templates; `4d9f9bdf2` Add Netgen Toolbar |
| 2023-08 | 9 | 8 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `aab255e60` NGSTACK-752 Initial icon additions and structure example; `319ef10e0` NGSTACK-752 - Update templates and styles |
| 2024-05 | 11 | 4 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 3 | `e455a23b0` NGSTACK-835: replace Better Admin UI with Admin UI Extra; `b13e4c724` NGSTACK-885 add host to git tag during deployment |
| 2024-07 | 6 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `c0852dfb2` Add Ibexa Scheduled Visibility bundle; `f3922cc18` Add ibexa/core 4.6.7+ to conflicts due to issues with duplicate fields |
| 2024-09 | 5 | 1 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `5e205684a` Add missing type config for Nova SEO; `41768aba6` Conflict with ibexa/core < 4.6.10 |
| 2024-10 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `136abf402` Bump ibexa/oss to 4.6.12; `b310c512b` Fix namespace for smoke tests |
| 2024-11 | 5 | 1 | 0 | 0 | 0 | 0 | 1 | 1 | 0 | 2 | `54fb89fa9` NGSTACK-924 raise php version to 8.2; `7ea1e2601` NGSTACK-925 Downgrade phpdoc-parser version beacuse of serializer depe |
| 2024-12 | 59 | 42 | 3 | 0 | 0 | 0 | 9 | 3 | 0 | 2 | `fc24d0b6d` NGSTACK-953 replace headings in bivt tamplates and components; `e185db0d7` NGSTACK-941 removed fieldset and add form-wrapper |
| 2025-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `f50511332` Update code to latest ibexa/oss |
| 2025-03 | 6 | 2 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | `4173341ef` NGSTACK-963 required field mark; `1511afcec` NGSTACK-963 translation |
| 2025-04 | 19 | 14 | 1 | 0 | 0 | 0 | 1 | 1 | 1 | 1 | `4835c0d53` NGSTACK-929 remove redundant alt texts on images on components and biv; `52b8b9a2f` NGSTACK-929 remove skip to cookie banner, make cookie banner focusable |
| 2025-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b139cf911` NGSTACK-962 accessibile accordions |
| 2025-05 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `c1308c2b3` Update CS fixer rules |
| 2025-06 | 5 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `bf4be771b` Sync Flex recipes; `d5085f163` Configure dynamic showcase generation command |
| 2025-07 | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `7229207be` Add Deployer task for dumping a database; `2f6974a62` Make sure fh_group and bold_group siteaccess groups have priority over |
| 2025-08 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `1c3d30531` Use Symfony's own Dotenv component |
| 2025-11 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `94a83b9d0` NGSTACK-1014 Set up global stylesheet, refactor typography and header ; `0100ced58` NGSTACK-1013 Add collapse nav breakpoint variable |
| 2025-12 | 6 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `0cd40ea29` Upgrade project to Ibexa 5; `876f1866c` Upgrade Netgen Layouts and other packages to Symfony 7.3 and PHP 8.4 |
| 2026-01 | 6 | 1 | 1 | 0 | 0 | 0 | 0 | 2 | 0 | 2 | `37164d8a7` Fix frontend build warnings; `113719ee4` NGSTACK-1009 Fix page jumping when sticky header is applied |
| 2026-02 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 0 | `abcd5838e` [TEMP]: Remove usage of Novactive SEO Bundle until it supports Ibexa 5; `3f7f5afb4` Update PHPUnit to v13 |

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential-platform-nexus-starter](../ledger/exponential-platform-nexus-starter.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
