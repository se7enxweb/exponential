# Ecosystem repository: admin-ui-assets

**Group:** Admin user interface. **Period in the ledger:** 2024-01-23 to 2026-04-04. **Changes:** 44 (3 made by the se7enxweb team, 41 upstream history carried by the fork).

## What it is

External JavaScript and CSS dependencies of the Platform v5 admin UI.

## How it relates to Exponential

Required by se7enxweb/admin-ui; renamed to se7enxweb/admin-ui-assets with replace declarations.

## What a user gets

Admin assets install from the se7enxweb vendor.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/admin-ui-assets
```

## Where to read more

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 18 |
| Fixes | 2 |
| Behaviour and upgrade changes | 1 |
| Tooling | 11 |
| No user benefit | 12 |

## Changes made by the se7enxweb team, by theme

### Composer requirements (1)

- 2026-04-04 `fb65cf36` tooling: Update package name and license in composer.json

Also: 2 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c4d58590` IBX-7411 Added chartjs-plugin-datalabels package (#20) |
| 2024-02 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `27863be9` IBX-7411: Fixed path for chartjs and chartjs plugin (#21) |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `397e64ed` Set up branch to become 5.0 in the future |
| 2024-04 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ce8b3a13` IBX-8119: Upgraded minimum PHP version to 8.3 |
| 2024-05 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `6e3dfb4e` Updated copyright year to 2024 |
| 2024-06 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | `6fd31b17` IBX-8139: Dropped class_alias BC layer statements from all classes (#2 |
| 2025-01 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `f39e3f2b` Updated copyright year to 2025 |
| 2025-02 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f1141eb4` IBX-8470: Upgraded codebase to Symfony 6 (#24) |
| 2025-05 | 6 | 2 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 1 | `585f4712` IBX-9939: Design System aliases (#30); `5faaa4e5` IBX-9916: Upgrade frontend dependencies (#29) |
| 2025-06 | 8 | 3 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 2 | `0a913972` Added code style configuration; `a0a70291` IBX-10162: Removed taggify dependency (#34) |
| 2025-07 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `4706321b` Update Design System version to 1.0.0-rc1 (#37); `baaded20` Update Design System to 1.0.0 (#38) |
| 2025-08 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `b90a4acc` IBX-10352: Escape output printed in GitHub Actions; `37028185` IBX-10552: [CKEditor] Packages version bump (#39) |
| 2025-09 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `0b673819` Fixed conflicts after merge (#40) |
| 2025-10 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `9c0c11cf` IBX-10792: Prepare scripts for dev version of admin-ui-assets (#43); `111e7ac6` IBX-10749: Add ids-core to assets (#41) |
| 2025-11 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ef9c1114` Update prepare next script to update branch instead of overwriting (#4 |
| 2025-12 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `d95d707b` Update ibexa Design System version to ^v1.0.0 (#45); `005ced50` [Composer] Fixed license information |
| 2026-04 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `070c589c` Added empty workflow to trigger on DS update (#46) |

## Full record

- Every change with date, kind, size and release tag: [ledger of admin-ui-assets](../ledger/admin-ui-assets.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
