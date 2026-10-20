# ezpublish-kernel: platform repository history

The history of `ezpublish-kernel`, one of the platform repositories around Exponential (group: Kernel). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 31 changes from 2023-12-22 to 2026-04-10: 30 made by the se7enxweb team and 1 from the upstream history the fork carries.

## What it is

Fork of the 2014.11 era kernel used by the Symfony stack that wraps the legacy kernel.

## How it relates to Exponential

Kept installable on current PHP so Exponential Platform Nexus 1.x and Exponential Platform Legacy can still be built.

## What a user gets

Composer installs on PHP 8.1 to 8.3 and later, PHP 8.5 template fixes, working cache configuration, SQLite installer.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezpublish-kernel
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 5 |
| Fixes | 7 |
| Behaviour and upgrade changes | 1 |
| Tooling | 8 |
| Releases | 8 |
| No user benefit | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-06-11 | v8.0.0 | `d0ba8f675` | Update composer.json version bump |
| 2025-06-11 | v8.0.1 | `f5f432cbe` | Update composer.json version bump for twig dependency |
| 2025-07-01 | v7.5.32 | `66a78a034` | Update composer.json version bump |
| 2025-08-24 | v7.5.33 | `49c4ffdc3` | Update composer.json to include undesired package version number. Bugfix. |
| 2025-09-05 | v7.5.34 | `3ce50c5a1` | Update layout.html.twig replaced product name in debug layout template. Rebranding. |
| 2025-09-26 | v7.5.35 | `89c58e905` | Update kriswallsmith/buzz version constraints |
| 2026-01-13 | v7.5.36 | `95434bfae` | Testing composer workaround preventing installations. |
| 2026-04-09 | v7.5.41 | `23d2b55de` | add SQLite installer support + exponential-oss install type |

## Changes made by the se7enxweb team, by theme

### Composer requirements (7)

- 2025-06-11 `75dfdbf5b` tooling: Update composer.json switched package distro name to 7x. Increased PHP version support for testing.
- 2025-06-28 `3a5ce7adc` tooling: Update composer.json Reduce twig requirement
- 2025-06-28 `5ce71d41f` tooling: Update composer.json version downgrade for doctrine-dbal-schema.
- 2025-07-01 `28c99098f` tooling: Update composer.json swap vendor for dependency
- 2025-08-24 `8d2f36ecb` tooling: Update composer.json replaced package dependency liip for se7enxweb. Forking for changes.
- 2025-08-24 `824576e57` tooling: Update composer.json to include changes to further fork this package. Bugfixes.
- 2026-01-13 `95434bfae` release: Testing composer workaround preventing installations.

### Version numbers (7)

- 2025-06-11 `d0ba8f675` release: Update composer.json version bump
- 2025-06-11 `f5f432cbe` release: Update composer.json version bump for twig dependency
- 2025-07-01 `61141719b` release: Update composer.json version bump for package
- 2025-07-01 `54a42dcde` release: Update composer.json package version bump
- 2025-07-01 `824059c6b` release: Update composer.json package version bump for greater php support
- 2025-07-01 `66a78a034` release: Update composer.json version bump
- 2025-08-24 `49c4ffdc3` release: Update composer.json to include undesired package version number. Bugfix.

### Exponential branding (3)

- 2025-09-05 `3ce50c5a1` feature: Update layout.html.twig replaced product name in debug layout template. Rebranding.
- 2025-09-05 `e1467651a` feature: Update cleandata.sql to replace product name in sql text strings. Rebranding.
- 2026-02-14 `654367b56` feature: Rebranding Default Installation Name and Doc URL. Rebranding.

### Other changes to the fork (3)

- 2025-09-24 `13fd781f3` fix: Update jsrouting-bundle version constraints
- 2025-09-26 `6b9bb39c1` fix: Update PHP version requirement to include 8.3
- 2025-09-26 `89c58e905` fix: Update kriswallsmith/buzz version constraints

### PHP 8.x compatibility (2)

- 2025-08-24 `d297f8e66` fix: Update ScriptHandler.php to include php 8.2+ tested support required by ezplatform 2.5 gpl. New PHP 8 Support.
- 2026-01-30 `529b8f3e3` fix: Fix PHP 8.5 compatibility: Replace closure-based Twig functions with named methods

### Test tooling (2)

- 2025-09-24 `a7c676028` tooling: Update doctrine/doctrine-bundle version constraint to test 3.2 integration
- 2026-02-09 `4be8f2faa` tooling: Bugfixes to enable reasonable cache within platform. Tested as working in nexus 1.0.0.2 github. Required to fix symfony/platform based cache problems.

### SQLite support (2)

- 2026-04-09 `23d2b55de` feature: add SQLite installer support + exponential-oss install type
- 2026-04-10 `d50c34bdb` feature: CoreInstaller was changed to substitute SqliteDbPlatform for bare SqlitePlatform during schema import to ensure composite primary keys are generated c

### Package renamed to the se7enxweb vendor (1)

- 2026-01-28 `037cc410c` bc: Bugfix for 7x ezpublish-kernel to replace vendor name in configuration yaml. Tested.

### Bug fixes (1)

- 2026-02-10 `3063e284a` fix: Updated config/cache.yml to patch to provide required missing yaml configuration block. Cache System Bugfix.

Also: 2 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `4e93c5beb` IBX-7021: Fixed fatal error in DownloadController |

## Related pages

- [SQLite installer specification](../../specifications/6.0/platform-sqlite-installer.md)
- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Release changelog](../../changelogs/extensions/ezpublish-kernel.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezpublish-kernel.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
