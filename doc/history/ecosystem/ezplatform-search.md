# ezplatform-search: platform repository history

The history of `ezplatform-search`, one of the platform repositories around Exponential (group: Search, cache and API). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 4 changes from 2025-09-27 to 2026-04-12, all made by the se7enxweb team.

## What it is

Platform search bundle.

## How it relates to Exponential

Renamed to the se7enxweb vendor.

## What a user gets

Search bundle installs from se7enxweb.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-search
```

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 2 |
| Releases | 1 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-27 | v1.2.8 | `32fd5cc` | Update package name and license in composer.json |

## Changes made by the se7enxweb team, by theme

### Replace declarations for the upstream package (2)

- 2026-04-11 `72ad0cf` bc: add replace shim for ezsystems counterpart package
- 2026-04-12 `eeaa2ba` bc: replace uses wildcard '*' not self.version

### Composer requirements (1)

- 2025-09-27 `32fd5cc` release: Update package name and license in composer.json

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Release changelog](../../changelogs/extensions/ezplatform-search.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-search.md)
- Platform ecosystem by month: [2025-09](months/2025-09.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
