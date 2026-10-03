# Ecosystem repository: site-bundle

**Group:** Legacy bridge and site bundles. **Period in the ledger:** 2023-12-11 to 2026-04-19. **Changes:** 105 (2 made by the se7enxweb team, 103 upstream history carried by the fork).

## What it is

Netgen Site Bundle: common site features (menus, layouts glue, site context) for Ibexa sites.

## How it relates to Exponential

Base bundle of Nexus; renamed to se7enxweb/site-bundle and declared as a replacement of netgen/site-bundle to prevent a dual install.

## What a user gets

Site features Nexus builds on.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/site-bundle
```

## Where to read more

- [Site bundles](../../features/6.0/platform-site-bundles.md)
- [Release changelog](../../changelogs/extensions/site-bundle.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 57 |
| Fixes | 11 |
| Behaviour and upgrade changes | 2 |
| Documentation | 5 |
| Tooling | 17 |
| No user benefit | 13 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-17 | 3.0.5.0 | `cf54aee` | Replaced vendor infos. Rebranding. |
| 2026-04-19 | 3.0.6 | `64e6c89` | add replace netgen/site-bundle:* — prevent dual install with upstream fork |

## Changes made by the se7enxweb team, by theme

### Exponential branding (1)

- 2026-03-17 `cf54aee` feature: Replaced vendor infos. Rebranding.

### Replace declarations for the upstream package (1)

- 2026-04-19 `64e6c89` bc: add replace netgen/site-bundle:* — prevent dual install with upstream fork

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 13 | 3 | 1 | 0 | 0 | 0 | 4 | 2 | 0 | 3 | `2828c52` NGSTACK-813 created configuration and logic for choosing between inlin; `4f271db` NGSTACK-813 option for default behaviour occurs on null instead of -1 |
| 2024-01 | 6 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `4121347` IOTA-384 simplify and rename compiler pass; `b31893d` IOTA-384 rename parameter |
| 2023-10 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `55c0024` IOTA-384 add support for direct download in richtext links; `cceee4b` IOTA-384 make class final |
| 2024-02 | 6 | 3 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | `b98525e` NGSTACK-673: update for breaking change in Site API; `a70fcb9` NGSTACK-822 switch from string mapped to sort clause to FQN |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `61bdf0b` Cast the location ID to int |
| 2024-07 | 47 | 31 | 8 | 0 | 0 | 0 | 0 | 6 | 0 | 2 | `cbaa44c` Fix issues with PHPStan; `8515255` NGSTACK-811 add tag content command |
| 2024-08 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `98e8d7d` NGSTACK-822 logic of ordering changes, string parameter replaced by QN; `a777492` NGSTACK-822 logic of ordering changes, string parameter replaced by QN |
| 2024-09 | 16 | 7 | 1 | 0 | 0 | 0 | 1 | 5 | 0 | 2 | `a4b2381` Restore compatibility with Twig 3.11; `c68a928` Compatibility with Twig 3.13 |
| 2025-05 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `f41dac6` Implement command to generate dynamic showcases; `41079d3` Update CS fixer rules |
| 2025-06 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `804be60` Bump PHP to 8.2; `2135430` Conflict with older versions of netgen/layouts-ibexa |
| 2025-10 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `4b60ca2` NGSTACK-826 change ibexa Visibility criterion to Visible criterion fro; `ae5d21e` NGSTACK-826 add 'ibexa-search-extra' bundle as dependency in composer. |

## Full record

- Every change with date, kind, size and release tag: [ledger of site-bundle](../ledger/site-bundle.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
