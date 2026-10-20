# ezplatform-richtext: platform repository history

The history of `ezplatform-richtext`, one of the platform repositories around Exponential (group: Field types). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 7 changes from 2025-09-28 to 2026-04-12, all made by the se7enxweb team.

## What it is

Fork of the eZ Platform RichText field type (3.x line).

## How it relates to Exponential

Rich text for Exponential Platform 3.x; PHP 8.5 support, SCSS modernisation, replace declaration.

## What a user gets

Rich text keeps working on PHP 8.5.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-richtext
```

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 2 |
| Behaviour and upgrade changes | 4 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v2.3.26 | `f8dbf98` | Merge pull request #1 from se7enxweb/fix-richtext-paths-2.3 |
| 2026-03-26 | v2.3.27, 2.3 | `d1bd3dd` | Add PHP 8.5 support |
| 2026-03-26 | v2.3.28 | `45f0fe4` | Add replace metadata for ezsystems richtext package |

## Changes made by the se7enxweb team, by theme

### Replace declarations for the upstream package (4)

- 2026-03-26 `6902f46` bc: Add branch-alias for 2.3-se7enx
- 2026-03-26 `26167d2` bc: Remove se7enx branch-alias: prevent auto-resolution by external projects
- 2026-03-26 `45f0fe4` bc: Add replace metadata for ezsystems richtext package
- 2026-04-12 `86c7d54` bc: replace uses wildcard '*' not self.version

### PHP 8.x compatibility (1)

- 2026-03-26 `d1bd3dd` fix: Add PHP 8.5 support

### Bug fixes (1)

- 2026-04-11 `a25d6ee` fix: migrate SCSS  → /, fix deprecated math functions

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Release changelog](../../changelogs/extensions/ezplatform-richtext.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-richtext.md)
- Platform ecosystem by month: [2025-09](months/2025-09.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
