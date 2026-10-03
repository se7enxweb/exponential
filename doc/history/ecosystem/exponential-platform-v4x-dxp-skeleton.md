# Ecosystem repository: exponential-platform-v4x-dxp-skeleton

**Group:** Distributions and starters. **Period in the ledger:** 2024-05-31 to 2026-04-10. **Changes:** 25 (19 made by the se7enxweb team, 6 upstream history carried by the fork).

## What it is

Project skeleton for the Platform 4.6.x DXP.

## How it relates to Exponential

Same as the v5 skeleton for the 4.6 line (Symfony 5.4).

## What a user gets

create-project for 4.6.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/exponential-platform-v4x-dxp-skeleton
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 4 |
| Fixes | 3 |
| Behaviour and upgrade changes | 3 |
| Documentation | 5 |
| Tooling | 8 |
| No user benefit | 2 |

## Changes made by the se7enxweb team, by theme

### Composer requirements (4)

- 2026-03-12 `0112871` tooling: Add project skeleton composer.json with se7enxweb/exponential-platform-dxp metapackage dependency
- 2026-04-03 `6fa7457` tooling: Updated: Changing composer.json requirements in an initial attempt at downgrading package to Platform v4.6.x and Symfony 5.3. Forking.
- 2026-04-03 `d5a228f` tooling: Updated: Reajusting requirements. Finetuning base composer requirements. Bugfix.
- 2026-04-03 `ef08011` tooling: Updated: Reajusting requirements. Finetuning base composer requirements. Bugfix.

### Documentation (4)

- 2026-03-12 `fce8116` docs: Add nyholm/psr7 to satisfy PSR-17 factory requirement for ibexa/post-install
- 2026-04-08 `3a4c2ea` docs: add comprehensive README.md and INSTALL.md for v4.6.x DXP skeleton
- 2026-04-10 `2a8a0b3` docs: fix repo links, update license badge to GPL v2 (or any later version)
- 2026-04-10 `973d071` docs: remove version constraint from composer create-project commands

### Bug fixes (3)

- 2026-03-12 `cd0ef5c` fix: Fix nyholm/psr7 version constraint to ^1.0
- 2026-03-21 `3d4387c` fix: Updated: Reordering recipes run order. Bugfix.
- 2026-04-10 `fe5528f` fix: set extra.symfony.require to 5.4.* to prevent Flex version mismatch

### Console command names (2)

- 2026-04-10 `89df8e6` bc: rename ibexa:install → exponential:install in README and INSTALL
- 2026-04-10 `b079aba` bc: fix all deprecated command names in README and INSTALL

### Exponential branding (1)

- 2026-03-12 `06a2373` feature: Updated: Change package vendor/name details in composer.json and doc. Rebranding.

### Features (1)

- 2026-03-12 `d30c25a` feature: Add sevenx-recipes Flex endpoint before ibexa/recipes

### Cleanup (1)

- 2026-03-12 `32df31e` tooling: Remove config files that must come from Flex recipe, not skeleton

### Package renamed to the se7enxweb vendor (1)

- 2026-04-03 `c8b8b63` bc: Updated: Updated README.md to address version name and composer package name changes. Forking.

Also: 2 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `eeab899` Updated copyright year to 2024 |
| 2025-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `1edfed8` Updated copyright year to 2025 |
| 2025-05 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `fb57aec` ENG-140: Added auto-assign reviewers GH workflow (#7) |
| 2025-12 | 2 | 0 | 0 | 0 | 0 | 0 | 1 | 1 | 0 | 0 | `6132377` [Composer] Fixed license information; `c32b551` [Doc] Fixed outdated license information |
| 2026-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `27119aa` [Browser tests] Switch to actions/create-github-app-token (#8) |

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential-platform-v4x-dxp-skeleton](ledger/exponential-platform-v4x-dxp-skeleton.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
