# Ecosystem repository: exponential-platform-dxp

**Group:** Distributions and starters. **Period in the ledger:** 2023-12-21 to 2026-04-19. **Changes:** 112 (20 made by the se7enxweb team, 92 upstream history carried by the fork).

## What it is

Metapackage se7enxweb/exponential-platform-dxp for Platform v5.

## How it relates to Exponential

Pulls the forks as one require; replaced ibexa packages by se7enxweb ones in March / April 2026.

## What a user gets

One package name for a full v5 install.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/exponential-platform-dxp
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 24 |
| Fixes | 1 |
| Behaviour and upgrade changes | 6 |
| Documentation | 1 |
| Tooling | 38 |
| No user benefit | 42 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-12 | 0.0.0.1 | `f7fcc28` | Updated: Change package vendor/name details in composer.json and doc. Rebranding. |
| 2026-03-21 | v0.0.0.2 | `11da176` | Updated: Replacing package vendor for admin-ui package. Rebrading. |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (6)

- 2026-03-21 `11da176` bc: Updated: Replacing package vendor for admin-ui package. Rebrading.
- 2026-04-11 `dcdbf64` bc: Replace ibexa/system-info with se7enxweb/system-info
- 2026-04-17 `596c019` bc: replace ibexa/admin-ui-assets with se7enxweb/admin-ui-assets (v5 pure)
- 2026-04-19 `f38e977` bc: Replace ibexa/fieldtype-richtext with se7enxweb/fieldtype-richtext fork
- 2026-04-19 `36d63a6` bc: Use se7enxweb/fieldtype-richtext 5.0.x-dev branch
- 2026-04-19 `ccc350c` bc: require se7enxweb/layouts-core fork for PHP 8.4 private(set) Twig compatibility

### Exponential branding (4)

- 2026-03-12 `05a2a3d` feature: Updated: Change package vendor/name details in composer.json and doc. Rebranding.
- 2026-03-12 `f7fcc28` feature: Updated: Change package vendor/name details in composer.json and doc. Rebranding.
- 2026-03-21 `6e81075` feature: Updated: Replaced Ibexa Core Package With Fork to Replace Product Name in user facing templates with logos / trademarks. Rebranding.
- 2026-03-21 `2eba385` feature: Updated: Switching core package to tagged release, v5.0.1.0. Rebranding.

### Composer requirements (1)

- 2026-03-17 `2bcb37a` tooling: Rename package and update description in composer.json

### Other changes to the fork (1)

- 2026-04-13 `7f03a97` fix: recipeversion: dev-master → 1.3.x-dev (v5 pure recipe key)

Also: 8 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 4 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `5171945` [CI] IBX-4515: Include tests running on PHP 8.2 (#129); `ec69a98` [CI] IBX-4515: Fix typo in PHP 8.2 job name |
| 2024-01 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `9413966` [CI] Fixed duplicated php-image declaration |
| 2024-02 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `b9a09cc` [Composer] Added conflict with friends-of-behat/mink-browserkit-driver |
| 2024-03 | 7 | 1 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 2 | `bd0a88a` [CI] IBX-6507: Add setup running on Solr (#117); `da4ec55` Set up branch to become 5.0 in the future |
| 2024-04 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c40b4dd` Updated PHP version to 8.3, upgraded actions/checkout version; `7c5087c` IBX-8154: Use default image for 5.0 |
| 2024-05 | 6 | 2 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 3 | `8605b34` Updated copyright year to 2024; `9b6a9fa` Update README.md (#144) |
| 2024-06 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d6a2d57` IBX-8135: Removed hautelook/templated-uri-bundle fork (#151); `29ed284` [CI] Removed 4.5 from job triggering nightly (#158) |
| 2024-07 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `61451f2` IBX-8136: Replace or drop php-http/message-factory & php-http/guzzle6- |
| 2024-08 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `87f0327` IBX-8137: Dropped Swiftmailer bundle (#179) |
| 2024-10 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `0d55272` Added ibexa/core-search (#181) |
| 2024-11 | 5 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 2 | `fa2bfcb` Unpacked symfony/serializer-pack dependency declaration (#187); `9e91740` [composer] Removed dependency on symfony/orm-pack (#188) |
| 2025-01 | 7 | 2 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 3 | `1804822` Updated copyright year to 2025; `05c62ae` Updated copyright year to 2025 |
| 2025-02 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `0c4d6e7` IBX-8470: Upgraded codebase to Symfony 6 (#193); `f20502b` Changed ibexa/admin-ui-assets version back to ~5.0.0 (#197) |
| 2025-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d0780fb` [Behat] Nightly browser tests job removal for example-in-memory-produc |
| 2025-04 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `93a86bf` IBX-9697: [Composer] Added ibexa/twig-componenets (#211); `91b6488` Reverted "IBX-9697: [Composer] Added ibexa/twig-componenets (#211)" (# |
| 2025-05 | 11 | 2 | 0 | 0 | 0 | 0 | 0 | 5 | 0 | 4 | `cbfe72d` IBX-8543: Included newer DBMS versions on CI & reorganized setup (#195; `6cf1e17` IBX-8471: Upgraded codebase to Symfony 7 (#205) |
| 2025-07 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `6ad93c4` IBX-10228: Bumped symfony/* to ^7.3 (#226); `e017f52` [GHA] Created Post Release workflow (#230) |
| 2025-08 | 7 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 3 | `fabc501` [GHA][PostRelease] Fixed typos in reusable workflow path; `0d834d7` Solr 8 CI upgrade (#232) |
| 2025-09 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `4fdbd53` IBX-10493: Included Redis 7.2 on CI (#234); `e27b61c` Fix Nightly workflow authentication (#240) |
| 2025-10 | 7 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 4 | `c4d2556` [CI] Add token selection for 3.3 (#241); `b45edf7` IBX-10494: Included Postgres 18 on CI (#237) |
| 2025-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `4d12ae2` [Composer] Used 5.0-next admin-ui-assets branch (#245) |
| 2025-12 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `5426581` IBX-10946: Included MariaDB 11.4 on CI (#246) |
| 2026-01 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 2 | `24a4d17` added messenger dependency (#252); `ffa91cc` IBX-11108: Included Redis latest & Valkey latest on CI (#248) |
| 2026-02 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `2ccd825` IBX-10495: Remove unused `ibexa/ci-scripts` dependency (#256) |
| 2026-03 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `c85d992` IBX-11328: Included PHP 8.4 in browser tests (#260); `16401f0` [Merge-up] IBX-11328: Included PHP 8.4 in browser tests (#265) |

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential-platform-dxp](ledger/exponential-platform-dxp.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
