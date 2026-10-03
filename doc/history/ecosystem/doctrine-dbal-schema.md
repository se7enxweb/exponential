# Ecosystem repository: doctrine-dbal-schema

**Group:** Framework forks. **Period in the ledger:** 2025-07-01 to 2026-04-12. **Changes:** 2 (2 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Cross-DBMS schema import layer used by the installer.

## How it relates to Exponential

Fork with PHP 8.1 support and a replace shim.

## What a user gets

Schema import on current PHP; the 3.x kernel installer depends on it.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/doctrine-dbal-schema
```

## Where to read more

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Release changelog](../../changelogs/extensions/doctrine-dbal-schema.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 1 |
| Releases | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-07-01 | v1.0.10 | `3291540` | Update composer.json increased php version support |
| 2026-04-12 | v1.0.11 | `8c2a9dc` | add replace shim for ezsystems/* original package |

## Changes made by the se7enxweb team, by theme

### Composer requirements (1)

- 2025-07-01 `3291540` release: Update composer.json increased php version support

### Replace declarations for the upstream package (1)

- 2026-04-12 `8c2a9dc` bc: add replace shim for ezsystems/* original package

## Full record

- Every change with date, kind, size and release tag: [ledger of doctrine-dbal-schema](../ledger/doctrine-dbal-schema.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/doctrine-dbal-schema.md)
- [Framework forks on PHP 8.5](../../features/6.0/platform-php85-framework-forks.md)
- Platform ecosystem by month: [2025-07](months/2025-07.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
