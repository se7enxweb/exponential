# Ecosystem repository: ezplatform-search

**Group:** Search, cache and API. **Period in the ledger:** 2025-09-27 to 2026-04-12. **Changes:** 4 (4 made by the se7enxweb team, 0 upstream history carried by the fork).

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

## Where to read more

- [Release changelog](../../changelogs/extensions/ezplatform-search.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

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

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-search](../ledger/ezplatform-search.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
