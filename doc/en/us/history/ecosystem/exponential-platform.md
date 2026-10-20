# exponential-platform: platform repository history

The history of `exponential-platform`, one of the platform repositories around Exponential (group: Distributions and starters). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 11 changes from 2025-09-28 to 2026-04-08, all made by the se7enxweb team.

## What it is

Exponential Platform 3.x distribution (Symfony).

## How it relates to Exponential

Install and welcome page of the 3.2.x line.

## What a user gets

Composer project that boots an Exponential Platform 3.2.9 site.

This repository is a Composer project (type `project`), not a library, so you create a new site from it:

```bash
composer create-project se7enxweb/exponential-platform:3.2.x-dev my_project
cd my_project
```

Check the repository README for the database step that follows (`.env.local`, then `php bin/console exponential:install` or the documented import).

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 2 |
| Fixes | 4 |
| Documentation | 2 |
| No user benefit | 3 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v3.2.9 | `7300c617` | Required config yaml content changes to install, boot welcome, edit content. Part 4 the next to last pass. Kernel via sysinfo admin reports version fo |

## Changes made by the se7enxweb team, by theme

### Bug fixes (4)

- 2025-09-28 `20f563ab` fix: Required config yaml content changes to install, boot welcome, edit content. Bugfixes.
- 2025-09-28 `889b2df5` fix: Required config yaml content changes to install, boot welcome, edit content. Part 2. Bugfixes.
- 2025-09-28 `01f0f4d1` fix: Required config yaml content changes to install, boot welcome, edit content. Part 3. Bugfixes.
- 2025-09-28 `7300c617` fix: Required config yaml content changes to install, boot welcome, edit content. Part 4 the next to last pass. Kernel via sysinfo admin reports version fo

### Exponential branding (2)

- 2025-09-28 `140bc09c` feature: Root changes required to install via composer, build dependencies, install db, edit default content and create folder content successfully. Rebranding
- 2025-09-28 `a2bf19e3` feature: Rebranding changes to update welcome_page.html.twig template file URLs to documentation to valid active links matching by context. Rebranding.

### Documentation (2)

- 2026-04-08 `14ee6186` docs: rewrite README, add INSTALL.md, update COPYRIGHT for Exponential Platform 3.2.9
- 2026-04-08 `3c177d17` docs: restoring original license terms in README

Also: 3 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Release changelog](../../changelogs/extensions/exponential-platform.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/exponential-platform.md)
- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2025-09](months/2025-09.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
