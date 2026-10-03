# Ecosystem repository: oss

**Group:** Distributions and starters. **Period in the ledger:** 2023-12-21 to 2026-04-12. **Changes:** 10 (5 made by the se7enxweb team, 5 upstream history carried by the fork).

## What it is

Metapackage for the platform open source edition (3.3 / 4.6 line).

## How it relates to Exponential

Renamed ibexa/oss to se7enxweb/oss; every ezsystems package replaced by its se7enxweb fork.

## What a user gets

A single require without any ezsystems/* package.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/oss
```

## Where to read more

- [DXP skeleton and metapackage](../../features/6.0/platform-dxp-skeleton.md)
- [Release changelog](../../changelogs/extensions/oss.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 2 |
| Fixes | 2 |
| Behaviour and upgrade changes | 3 |
| Tooling | 3 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-25 | 3.3.0.1 | `2407521` | rename ibexa/oss -> se7enxweb/oss for 3.3.0.x branch |
| 2026-04-11 | v3.3.0.2 | `63289fd` | Replace ezsystems packages with se7enxweb versions |
| 2026-04-12 | v3.3.0.5 | `4db62cb` | add se7enxweb/ezplatform-alloyeditor-element-width to require |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (3)

- 2026-04-11 `63289fd` bc: Replace ezsystems packages with se7enxweb versions
- 2026-04-12 `d4e22fe` bc: use se7enxweb/* forks for design-engine, graphql, http-cache, query-fieldtype, richtext, search
- 2026-04-12 `d9319bb` bc: complete se7enxweb/* migration — zero ezsystems/* in require

### Other changes to the fork (1)

- 2026-03-25 `2407521` fix: rename ibexa/oss -> se7enxweb/oss for 3.3.0.x branch

### Bug fixes (1)

- 2026-04-12 `4db62cb` fix: add se7enxweb/ezplatform-alloyeditor-element-width to require

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `5171945` [CI] IBX-4515: Include tests running on PHP 8.2 (#129) |
| 2024-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `b9a09cc` [Composer] Added conflict with friends-of-behat/mink-browserkit-driver |
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c01eb77` IBX-8154: Run tests on PHP 8.3 (3.3) (#150) |
| 2024-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `106ab6c` Unpacked symfony/serializer-pack dependency declaration |
| 2025-10 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `c4d2556` [CI] Add token selection for 3.3 (#241) |

## Full record

- Every change with date, kind, size and release tag: [ledger of oss](../ledger/oss.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/oss.md)
- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2024-02](months/2024-02.md), [2024-05](months/2024-05.md), [2024-11](months/2024-11.md), [2025-10](months/2025-10.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
