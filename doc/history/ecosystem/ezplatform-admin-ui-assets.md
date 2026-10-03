# ezplatform-admin-ui-assets: platform repository history

The history of `ezplatform-admin-ui-assets`, one of the platform repositories around Exponential (group: Admin user interface). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 3 changes from 2025-06-03 to 2026-04-12: 2 made by the se7enxweb team and 1 from the upstream history the fork carries.

## What it is

External asset dependencies for the 2.x / 3.x admin UI.

## How it relates to Exponential

Renamed to se7enxweb/ezplatform-admin-ui-assets with a replace declaration.

## What a user gets

Admin assets install from the se7enxweb vendor.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-admin-ui-assets
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 1 |
| Behaviour and upgrade changes | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-12 | v5.3.6 | `12de333` | add replace shim for ezsystems/* original package |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (1)

- 2026-04-11 `a6a16e7` bc: rename package to se7enxweb/ezplatform-admin-ui-assets

### Replace declarations for the upstream package (1)

- 2026-04-12 `12de333` bc: add replace shim for ezsystems/* original package

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2025-06 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `219b71b` IBX-9793: Replace taggify with fork in package.json |

## Related pages

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Release changelog](../../changelogs/extensions/ezplatform-admin-ui-assets.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-admin-ui-assets.md)
- Platform ecosystem by month: [2025-06](months/2025-06.md), [2026-04](months/2026-04.md)
