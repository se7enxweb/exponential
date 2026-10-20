# ezplatform-graphql: platform repository history

The history of `ezplatform-graphql`, one of the platform repositories around Exponential (group: Search, cache and API). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 4 changes from 2025-09-28 to 2026-04-12, all made by the se7enxweb team.

## What it is

GraphQL server for the content repository.

## How it relates to Exponential

Renamed to se7enxweb, replaces bdunogier/ezplatform-graphql-bundle.

## What a user gets

GraphQL API on forked installs.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-graphql
```

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 4 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v2.3.18 | `7c97b44` | Replace se7enxweb/graphql-php with ibexa/graphql-php |
| 2026-04-12 | v2.3.20 | `be8418d` | use se7enxweb vendor path for package root dir |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (3)

- 2025-09-28 `92a4adb` bc: Update package names from ezsystems to se7enxweb
- 2025-09-28 `7c97b44` bc: Replace se7enxweb/graphql-php with ibexa/graphql-php
- 2026-04-12 `be8418d` bc: use se7enxweb vendor path for package root dir

### Replace declarations for the upstream package (1)

- 2026-04-12 `86aad7d` bc: add replace shim for ezsystems/ezplatform-graphql

## Related pages

- [Release changelog](../../changelogs/extensions/ezplatform-graphql.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-graphql.md)
- Platform ecosystem by month: [2025-09](months/2025-09.md), [2026-04](months/2026-04.md)
