# Ecosystem repository: exponential-platform-dxp-skeleton

**Group:** Distributions and starters. **Period in the ledger:** 2024-05-31 to 2026-04-10. **Changes:** 21 (15 made by the se7enxweb team, 6 upstream history carried by the fork).

## What it is

Project skeleton for Platform v5 DXP (create-project).

## How it relates to Exponential

Install guide with SQLite, MySQL and PostgreSQL, JWT, GraphQL, Solr, Varnish; commands use the exponential: prefix.

## What a user gets

composer create-project and a documented install path.

This repository is a Composer project (type `project`), not a library, so you create a new site from it:

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton exponential_website
cd exponential_website
```

Check the repository README for the database step that follows (`.env.local`, then `php bin/console exponential:install` or the documented import).

## Where to read more

- [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 4 |
| Fixes | 3 |
| Documentation | 7 |
| Tooling | 5 |
| No user benefit | 2 |

## Changes made by the se7enxweb team, by theme

### Documentation (6)

- 2026-03-12 `fce8116` docs: Add nyholm/psr7 to satisfy PSR-17 factory requirement for ibexa/post-install
- 2026-04-08 `aebd307` docs: expand README with full project header, banners and body sections for v5
- 2026-04-08 `0e7ea1d` docs: fix license badge to GPL v2+ (or any later version)
- 2026-04-08 `1c15559` docs: update license badge to full GPL v2 (or any later version) label
- 2026-04-10 `19cc05d` docs: expand README and add comprehensive INSTALL.md for Platform v5 DXP skeleton
- 2026-04-10 `6271d40` docs: expand CLI reference, fix phantom commands, clean up composer create-project

### Bug fixes (2)

- 2026-03-12 `cd0ef5c` fix: Fix nyholm/psr7 version constraint to ^1.0
- 2026-03-21 `3d4387c` fix: Reordering recipes run order. Bugfix.

### Exponential branding (1)

- 2026-03-12 `06a2373` feature: Change package vendor/name details in composer.json and doc. Rebranding.

### Composer requirements (1)

- 2026-03-12 `0112871` tooling: Add project skeleton composer.json with se7enxweb/exponential-platform-dxp metapackage dependency

### Features (1)

- 2026-03-12 `d30c25a` feature: Add sevenx-recipes Flex endpoint before ibexa/recipes

### Cleanup (1)

- 2026-03-12 `32df31e` tooling: Remove config files that must come from Flex recipe, not skeleton

### PHP 8.x compatibility (1)

- 2026-04-08 `f7182d9` fix: add v5 INSTALL.md and rewrite README for 5.0.x (Symfony 7.3+, PHP 8.3+, AssetMapper)

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

- Every change with date, kind, size and release tag: [ledger of exponential-platform-dxp-skeleton](../ledger/exponential-platform-dxp-skeleton.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/exponential-platform-dxp-skeleton.md)
- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2024-05](months/2024-05.md), [2025-01](months/2025-01.md), [2025-05](months/2025-05.md), [2025-12](months/2025-12.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
