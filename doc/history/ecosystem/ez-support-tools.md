# ez-support-tools: platform repository history

The history of `ez-support-tools`, one of the platform repositories around Exponential (group: Search, cache and API). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 7 changes from 2024-02-16 to 2026-04-11: 5 made by the se7enxweb team and 2 from the upstream history the fork carries.

## What it is

System information pages for administrators (system info).

## How it relates to Exponential

Renamed to se7enxweb; guards against a null version from InstalledVersions.

## What a user gets

Admin system info page no longer breaks on null package versions.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ez-support-tools
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 1 |
| Fixes | 2 |
| Behaviour and upgrade changes | 1 |
| Tooling | 1 |
| Releases | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-07-01 | v2.3.13 | `3d4ce31` | Update composer.json replaced require section package vendor name |
| 2025-09-28 | v2.3.14 | `bf354af` | Update se7enxweb/ezplatform-core version requirement |

## Changes made by the se7enxweb team, by theme

### Composer requirements (1)

- 2025-07-01 `3d4ce31` release: Update composer.json replaced require section package vendor name

### Package renamed to the se7enxweb vendor (1)

- 2025-09-28 `bc578dd` bc: Update package descriptions and PHP version support

### Other changes to the fork (1)

- 2025-09-28 `bf354af` fix: Update se7enxweb/ezplatform-core version requirement

### Version numbers (1)

- 2026-04-11 `71c5603` release: Version bump for php requirements to allow php 8.5.x. Bugfix.

### Bug fixes (1)

- 2026-04-11 `ea79490` fix: guard against null version from InstalledVersions::getVersion()

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `ef74a0d` Fixed Composer license info and bumped license & copyright year (#120) |
| 2024-03 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `954fd28` Updated EOM and EOL hardcoded dates |

## Related pages

- [Site bundles](../../features/6.0/platform-site-bundles.md)
- [Extension page](../../features/6.0/extensions/ez-support-tools.md)
- [Release changelog](../../changelogs/extensions/ez-support-tools.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ez-support-tools.md)
- Platform ecosystem by month: [2024-02](months/2024-02.md), [2024-03](months/2024-03.md), [2025-07](months/2025-07.md), [2025-09](months/2025-09.md), [2026-04](months/2026-04.md)
