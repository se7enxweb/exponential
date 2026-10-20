# twig: platform repository history

The history of `twig`, one of the platform repositories around Exponential (group: Framework forks). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 15 changes from 2023-09-14 to 2026-05-11: 9 made by the se7enxweb team and 6 from the upstream history the fork carries.

## What it is

Fork of Twig 2.x (replaces twig/twig).

## How it relates to Exponential

Template engine of the Symfony 3.4 line made PHP 8.5 safe.

## What a user gets

Templates render without deprecations on PHP 8.4 and 8.5.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/twig
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 3 |
| Fixes | 3 |
| Behaviour and upgrade changes | 1 |
| Security | 1 |
| Tooling | 4 |
| No user benefit | 3 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-08-24 | v2.16.2 | `9e69bb01` | Update NameExpression.php bugfix for php 8.2+ support. Tested working. |
| 2026-01-30 | v2.16.3 | `5d0b35d1` | Update twig/twig version to 2.16.2 |
| 2026-04-09 | v2.16.4 | `d45d841d` | detect PHP 8.5 closure names in CallExpression::reflectCallable() |

## Changes made by the se7enxweb team, by theme

### Composer requirements (3)

- 2025-08-24 `6aabd856` tooling: Update composer.json replaced package vendor name
- 2025-08-24 `6209f2d2` tooling: Update composer.json added tag to test deploy workflow. No change.
- 2026-01-30 `8818ed45` tooling: Update twig/twig version constraint in composer.json

### PHP 8.x compatibility (3)

- 2025-08-24 `9e69bb01` fix: Update NameExpression.php bugfix for php 8.2+ support. Tested working.
- 2026-04-09 `d45d841d` fix: detect PHP 8.5 closure names in CallExpression::reflectCallable()
- 2026-05-11 `85503dec` fix: fix(php8.4+): explicit nullable type parameters throughout src/

### Replace declarations for the upstream package (1)

- 2026-01-30 `8095fbff` bc: Add replace section for twig/twig in composer.json

### Design and templates (1)

- 2026-01-30 `5d0b35d1` feature: Update twig/twig version to 2.16.2

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-09 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `a18da161` Add SourcePolicyInterface to selectively enable the Sandbox based on a template's Source |
| 2023-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `0c9cc7ef` End of maintenance for the 2.x branch; `a4974b29` feature #3893 Add SourcePolicyInterface to selectively enable the Sandbox based on a template's Source |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `a1d84cfb` Bump CI action/cache |
| 2024-09 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | `2102dd13` Fix a security issue when an included sandboxed template has been loaded before without the sandbox context; `19185947` Prepare the 2.16.1 release |

## Related pages

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Release changelog](../../changelogs/extensions/twig.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/twig.md)
- Platform ecosystem by month: [2023-09](months/2023-09.md), [2023-12](months/2023-12.md), [2024-03](months/2024-03.md), [2024-09](months/2024-09.md), [2025-08](months/2025-08.md), [2026-01](months/2026-01.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md), [2026-05](months/2026-05.md)
