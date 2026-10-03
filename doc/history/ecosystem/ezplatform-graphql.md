# Ecosystem repository: ezplatform-graphql

**Group:** Search, cache and API. **Period in the ledger:** 2025-09-28 to 2026-04-12. **Changes:** 4 (4 made by the se7enxweb team, 0 upstream history carried by the fork).

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

## Where to read more

- [Release changelog](../../changelogs/extensions/ezplatform-graphql.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

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

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-graphql](../ledger/ezplatform-graphql.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
