# Ecosystem repository: exponential-legacy-installer

**Group:** Distributions and starters. **Period in the ledger:** 2025-08-25 to 2026-06-19. **Changes:** 4 (4 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Composer plugin that installs the legacy kernel and legacy extensions.

## How it relates to Exponential

Fixed for Composer 2.10 path normalization on 2026-06-19.

## What a user gets

composer install keeps working with current Composer.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/exponential-legacy-installer
```

## Where to read more

- [Legacy bridge](../../features/6.0/legacy-bridge.md)
- [Release changelog](../../changelogs/extensions/exponential-legacy-installer.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 1 |
| Releases | 2 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-08-25 | 2.2.1 | `6e8cf01` | Update composer.json replaced package vendor and package name. Forking for changes. |
| 2026-04-11 | 2.2.2 | `d80adb2` | add replace shim for ezsystems counterpart package |
| 2026-06-19 | 2.2.3 | `bddac7c` | Fix Composer 2.10 path normalization failure in legacy kernel installer |

## Changes made by the se7enxweb team, by theme

### Composer requirements (2)

- 2025-08-25 `6e8cf01` release: Update composer.json replaced package vendor and package name. Forking for changes.
- 2026-06-19 `bddac7c` release: Fix Composer 2.10 path normalization failure in legacy kernel installer

### Replace declarations for the upstream package (1)

- 2026-04-11 `d80adb2` bc: add replace shim for ezsystems counterpart package

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of exponential-legacy-installer](../ledger/exponential-legacy-installer.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
