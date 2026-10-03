# Ecosystem repository: ezplatform-admin-ui

**Group:** Admin user interface. **Period in the ledger:** 2023-12-13 to 2026-04-12. **Changes:** 36 (23 made by the se7enxweb team, 13 upstream history carried by the fork).

## What it is

Fork of the eZ Platform 2.x / 3.x admin UI (Exponential Platform Admin v2).

## How it relates to Exponential

Admin interface of the 3.x line, rebranded to Exponential Platform DXP.

## What a user gets

Exponential branding, SCSS migration to @use/@forward, working asset paths on PHP 8.5.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-admin-ui
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 11 |
| Fixes | 7 |
| Behaviour and upgrade changes | 3 |
| Security | 2 |
| Tooling | 3 |
| No user benefit | 10 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v2.3.35 | `2c344718` | Update ezplatform-search dependency version |
| 2025-09-28 | v2.3.36 | `6b81e31b` | Merge pull request #1 from se7enxweb/fix/webpack-asset-paths-se7enxweb |
| 2025-09-28 | v2.3.37 | `8ac258af` | Merge pull request #2 from se7enxweb/apply-branding-changes-from-2.2 |
| 2025-09-28 | v2.3.38 | `529b07d4` | Merge pull request #3 from se7enxweb/apply-branding-changes-from-2.2 |
| 2025-09-28 | v2.3.39 | `b126ffcd` | Merge pull request #4 from se7enxweb/fix/update-html-title-to-exponential-platform |
| 2026-03-25 | 2.3.34.1 | `acd8c584` | extend PHP constraint to ^8.5 for eZ Platform 3.3 / se7enxweb fork 2.3.x branch |
| 2026-04-11 | v2.3.40 | `e8c62c9b` | Merge pull request #8 from se7enxweb/2.3 |
| 2026-04-11 | v2.3.41 | `aa915575` | Updated: Rebranding text string replacements. Rebranding. |

## Changes made by the se7enxweb team, by theme

### Exponential branding (5)

- 2025-09-28 `cc81d13a` feature: Apply comprehensive branding changes from 2.2 branch
- 2025-09-28 `056ea021` feature: Updated: Patch logo core color bug. Rebranding.
- 2025-09-28 `7c84eb0c` feature: Fix HTML title tag branding: Replace 'Ibexa DXP' with 'Exponential Platform'
- 2026-04-11 `e7bbbd38` feature: feat(branding): update to Exponential Platform DXP branding
- 2026-04-11 `aa915575` feature: Updated: Rebranding text string replacements. Rebranding.

### Other changes to the fork (3)

- 2025-09-28 `9432f2e9` fix: Update ezplatform-search dependency version
- 2025-09-28 `2c344718` fix: Update ezplatform-search dependency version
- 2026-03-25 `acd8c584` fix: extend PHP constraint to ^8.5 for eZ Platform 3.3 / se7enxweb fork 2.3.x branch

### Bug fixes (2)

- 2026-04-11 `c3d25812` fix: encore configs reference ezsystems/ezplatform-admin-ui-assets (installed dep) not se7enxweb
- 2026-04-11 `14b2c03d` fix: migrate SCSS  → /, fix deprecated color/math functions

### Replace declarations for the upstream package (2)

- 2026-04-11 `70396dd7` bc: add replace shim for ezsystems counterpart package
- 2026-04-12 `70c741a0` bc: replace uses wildcard '*' not self.version

### Composer requirements (1)

- 2025-09-28 `95cbb7cf` tooling: Change package names and licenses in composer.json

### Package renamed to the se7enxweb vendor (1)

- 2025-09-28 `7db679cc` bc: Fix webpack asset paths to use se7enxweb package names

Also: 9 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f8d30285` IBX-1464: Added `versionNo` param in the `ezimage` edit form template  |
| 2024-01 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b0db6f5e` IBX-7046: Fixed Dashboard "My Content" & "My Media" edit buttons (#211 |
| 2024-03 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2ffae963` IBX-7954: Show error message on empty image asset (#2116); `80eeb142` IBX-6540: Added Depth sort to breadcrumbs (#2115) |
| 2024-04 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ef361314` IBX-7983: Handled previewing `ezimage` field with height of 0 (#2117) |
| 2024-06 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f1a75400` IBX-8019: Replaced `LocationService::loadLocationChildren` use with `S |
| 2024-07 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `7d1971ca` IBX-8079: Inconsistent redirect point after role assignment action (#2; `81d22311` IBX-8350: Text line fields which is not marked as required, but has a  |
| 2025-06 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | `acaa620d` IBX-9793: Fixed XSS issues in several places; `71819dd2` [Tests] Fixed failing CI |
| 2025-10 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | `da3bfbfb` [Security] IBX-10200: Fix XSS in reschedule/cancel-schedule modal; `ad89d174` [GHA][Browser tests] Added secrets for browser tests configuration |

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-admin-ui](ledger/ezplatform-admin-ui.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
