# ezplatform-standard-design: platform repository history

The history of `ezplatform-standard-design`, one of the platform repositories around Exponential (group: Admin user interface). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 4 changes from 2025-07-01 to 2026-04-12, all made by the se7enxweb team.

## What it is

Standard design bundle of eZ Platform (Exponential Platform Standard Design Bundle).

## How it relates to Exponential

Renamed to the se7enxweb vendor with PHP constraints widened.

## What a user gets

Front-end design bundle installs on current PHP.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-standard-design
```

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 1 |
| Tooling | 1 |
| Releases | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-07-01 | v0.3.9 | `a1e5c4d` | Update composer.json increased package php version support |
| 2025-09-28 | v0.3.10 | `977038a` | Update package names and descriptions in composer.json |
| 2026-04-12 | v0.3.11 | `d99afe0` | add replace shim for ezsystems/* original package |

## Changes made by the se7enxweb team, by theme

### Composer requirements (3)

- 2025-07-01 `fe98823` tooling: Update composer.json switched package vendor name
- 2025-07-01 `a1e5c4d` release: Update composer.json increased package php version support
- 2025-09-28 `977038a` release: Update package names and descriptions in composer.json

### Replace declarations for the upstream package (1)

- 2026-04-12 `d99afe0` bc: add replace shim for ezsystems/* original package

## Related pages

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Release changelog](../../changelogs/extensions/ezplatform-standard-design.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-standard-design.md)
- Platform ecosystem by month: [2025-07](months/2025-07.md), [2025-09](months/2025-09.md), [2026-04](months/2026-04.md)
