# Ecosystem repository: ezplatform-kernel

**Group:** Kernel. **Period in the ledger:** 2023-12-22 to 2026-04-12. **Changes:** 38 (23 made by the se7enxweb team, 15 upstream history carried by the fork).

## What it is

Fork of the eZ Platform 1.13 / 2.5 / 3.3 kernel (content repository, APIs, Symfony integration).

## How it relates to Exponential

Kernel of the Exponential Platform 3.x line; replaces ezsystems/ezplatform-kernel and ibexa/core 4.x in Composer.

## What a user gets

Exponential branding, PHP 8.5 constraints, exponential:* console commands and SQLite install support on the 3.x / 4.6 line.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-kernel
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 13 |
| Fixes | 9 |
| Behaviour and upgrade changes | 6 |
| Performance | 2 |
| Tooling | 2 |
| No user benefit | 6 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-27 | v1.3.40 | `c61b18361` | Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding. |
| 2025-09-28 | v1.3.41 | `8c070ac8f` | Merge pull request #5 from se7enxweb/fix/missing-configuration-parsers |
| 2025-09-28 | v1.3.42 | `43712cafc` | Merge pull request #7 from se7enxweb/fix/update-vendor-paths-from-ezsystems-to-se7enxweb |
| 2026-03-26 | v1.3.43 | `021397cff` | Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support |
| 2026-03-29 | v1.3.44 | `efaf3e785` | append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql |
| 2026-04-12 | v1.3.45 | `77524ca4e` | switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema |

## Changes made by the se7enxweb team, by theme

### Exponential branding (4)

- 2025-09-27 `a613f1c18` feature: Change 'eZ Platform' to 'Exponential Platform'. Rebranding
- 2025-09-27 `36a12daf1` feature: Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding.
- 2025-09-27 `a7016bf01` feature: Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding.
- 2025-09-27 `c61b18361` feature: Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding.

### SQLite support (4)

- 2026-04-07 `f1970779c` feature: unescape backslash-escaped quotes and newlines in SQLite cleandata.sql
- 2026-04-07 `eac6c8b77` feature: remove autoincrement from ezcontentobject_attribute.id for SQLite composite PK compatibility
- 2026-04-11 `b32c2d6ae` feature: skip doctrine:database:create for SQLite in checkCreateDatabase()
- 2026-04-11 `a5a32b66f` feature: recreate composite-PK tables after Doctrine schema generation

### Replace declarations for the upstream package (3)

- 2026-03-26 `021397cff` bc: Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support
- 2026-03-26 `9b341b163` bc: Add branch-alias for 1.3-se7enx
- 2026-03-26 `7c7b12577` bc: Remove se7enx branch-alias: prevent auto-resolution by external projects

### Package renamed to the se7enxweb vendor (2)

- 2025-09-27 `f54fd6f66` bc: Update package details for se7enxweb integration
- 2025-09-28 `933f42eaf` bc: Fix vendor paths: Replace ezsystems with se7enxweb paths

### Bug fixes (2)

- 2026-03-29 `efaf3e785` fix: append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql
- 2026-04-12 `77524ca4e` fix: switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema

### Composer requirements (1)

- 2025-09-27 `9621fd420` tooling: Update PHP version requirements in composer.json

### Features (1)

- 2025-09-28 `9ab6f7357` feature: Add missing configuration parsers for Content and User Settings views

### Console command names (1)

- 2026-04-07 `4b90bbf4d` bc: rename commands to exponential:* prefix, keep ibexa:* and ezplatform:* as deprecated aliases

Also: 5 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `29489e4fc` IBX-7346: Reindexed reverse-related content after deleting source cont; `4abba9267` IBX-6880: Skipped normalizing directories in the `normalizePath` metho |
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6e5fa6ff0` IBX-7485: Skipped files with corrupted filenames when loading and dele |
| 2024-02 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `aa31bb228` IBX-7172: Fixed Repository Filtering by multiple ObjectStateId criteri |
| 2024-03 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `8ef0e70c4` IBX-7809: Fixed creating `UserMetadata` criterion from `UserGroupLimit |
| 2024-04 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `81464c68c` Fixed missing return types for DebugTemplate class; `245d02e15` IBX-6592: Removed unusable location/subtree limitations from `state/as |
| 2024-05 | 2 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | `98b7b50e6` IBX-5388: Fixed performance issues of content updates after field chan; `b5fe9ad82` IBX-6494: Fixed copying of non-translatable fields to later versions |
| 2024-06 | 2 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | `17e78be8a` IBX-6833: Fixed copying empty fields from a published version; `c88c39759` IBX-8019: Added performance consideration notice to `LocationService:: |
| 2024-08 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `9a6241085` IBX-8562: Fixed flooding content attributes table with duplicates |
| 2024-10 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b19be8dbe` IBX-8562: Command to remove duplicated entries after faulty IBX-5388 f |
| 2025-02 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `553e0dd0c` IBX-9455: Upgraded Twig to ^3.19.0 (#411) |

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-kernel](ledger/ezplatform-kernel.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
