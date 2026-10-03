# ezplatform-http-cache: platform repository history

The history of `ezplatform-http-cache`, one of the platform repositories around Exponential (group: Search, cache and API). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 7 changes from 2024-11-28 to 2026-04-12: 6 made by the se7enxweb team and 1 from the upstream history the fork carries.

## What it is

HTTP cache handling (Varnish / Symfony) for the platform.

## How it relates to Exponential

PHP 8.5 support, replace declaration.

## What a user gets

Reverse-proxy caching works on PHP 8.5.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-http-cache
```

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 1 |
| Behaviour and upgrade changes | 4 |
| Releases | 1 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v2.3.17 | `fdd5c57` | Update package name and dependencies in composer.json |
| 2026-03-26 | v2.3.18 | `2b7205c` | Add PHP 8.5 support |
| 2026-03-26 | v2.3.19 | `fabe744` | Add replace metadata for ezsystems http-cache package |

## Changes made by the se7enxweb team, by theme

### Replace declarations for the upstream package (4)

- 2026-03-26 `74a81c0` bc: Add branch-alias for 2.3-se7enx
- 2026-03-26 `fc50ab1` bc: Remove se7enx branch-alias: prevent auto-resolution by external projects
- 2026-03-26 `fabe744` bc: Add replace metadata for ezsystems http-cache package
- 2026-04-12 `4703d2d` bc: replace uses wildcard '*' not self.version

### Composer requirements (1)

- 2025-09-28 `fdd5c57` release: Update package name and dependencies in composer.json

### PHP 8.x compatibility (1)

- 2026-03-26 `2b7205c` fix: Add PHP 8.5 support

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |  |

## Related pages

- [Release changelog](../../changelogs/extensions/ezplatform-http-cache.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-http-cache.md)
- Platform ecosystem by month: [2024-11](months/2024-11.md), [2025-09](months/2025-09.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
