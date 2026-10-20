# exponential-legacy-installer: platform repository history

The history of `exponential-legacy-installer`, one of the platform repositories around Exponential (group: Distributions and starters). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 4 changes from 2025-08-25 to 2026-06-19, all made by the se7enxweb team.

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

## Related pages

- [Legacy bridge](../../features/6.0/legacy-bridge.md)
- [Release changelog](../../changelogs/extensions/exponential-legacy-installer.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/exponential-legacy-installer.md)
- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2025-08](months/2025-08.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md), [2026-06](months/2026-06.md)
