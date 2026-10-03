# Ecosystem repository: ezplatform-user

**Group:** Search, cache and API. **Period in the ledger:** 2025-09-28 to 2026-04-12. **Changes:** 4 (4 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

User bundle (login, registration, profile).

## How it relates to Exponential

Renamed to se7enxweb with PHP 8.5 constraint.

## What a user gets

User features on PHP 8.5.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-user
```

## Where to read more

- [Release changelog](../../changelogs/extensions/ezplatform-user.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 1 |
| Behaviour and upgrade changes | 3 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v2.3.13 | `2b88e8c` | Update package names and PHP version requirements |
| 2026-03-25 | 2.3.13.1 | `c30a5c7` | extend PHP constraint to ^8.5 for se7enxweb fork 2.3.x branch |
| 2026-04-11 | v2.3.14 | `fb47d83` | add replace shim for ezsystems counterpart package |
| 2026-04-12 | v2.3.15 | `aa19600` | replace uses wildcard '*' not self.version |

## Changes made by the se7enxweb team, by theme

### Replace declarations for the upstream package (2)

- 2026-04-11 `fb47d83` bc: add replace shim for ezsystems counterpart package
- 2026-04-12 `aa19600` bc: replace uses wildcard '*' not self.version

### Package renamed to the se7enxweb vendor (1)

- 2025-09-28 `2b88e8c` bc: Update package names and PHP version requirements

### Other changes to the fork (1)

- 2026-03-25 `c30a5c7` fix: extend PHP constraint to ^8.5 for se7enxweb fork 2.3.x branch

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-user](../ledger/ezplatform-user.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
