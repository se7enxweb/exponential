# Ecosystem repository: symfony

**Group:** Framework forks. **Period in the ledger:** 2025-08-24 to 2026-04-09. **Changes:** 18 (18 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Fork of the Symfony 3.4 framework split into se7enxweb/symfony.

## How it relates to Exponential

Keeps Symfony 3.4 based Nexus 1.x installs running on PHP 8.2 to 8.5.

## What a user gets

PHP 8 return types, nullable types, closure and reflection fixes.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/symfony
```

## Where to read more

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [Release changelog](../../changelogs/extensions/symfony.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 8 |
| Documentation | 1 |
| Tooling | 6 |
| Releases | 1 |
| No user benefit | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-01-13 | v3.4.50 | `5fc51171c` | Bugfix for composer.json validation test to pass. Bugfix. |

## Changes made by the se7enxweb team, by theme

### PHP 8.x compatibility (7)

- 2025-08-24 `9dcfb7a69` fix: Update ErrorHandler.php adding tested php 8.2 support with hopes it will test working for php 8.4+. New PHP 8 Support.
- 2025-08-24 `df6faf1aa` fix: Update ExceptionCaster.php to include bugfix for php8.4 support as tested with ezplatform 2.5 gpl and confirmed working. New PHP 8.4 Support.
- 2025-08-24 `118d7f4b0` fix: Update LazyLoadingValueHolderGenerator.php to include php 8.2+ tested as working changes to reflect Zend to Laminas Library Transition. New PHP 8 Supp
- 2026-01-28 `f2852d452` fix: PHP 8.x: Add return types to VarDumper Data class
- 2026-01-28 `e39f6b0c1` fix: PHP 8.x closure to method reference in WebProfilerExtension
- 2026-01-29 `fd017e42a` fix: Add explicit nullable types and fix ReflectionProperty deprecation in ErrorHandler.php for PHP 8.1+
- 2026-02-10 `4e267ed5b` fix: PHP85 Compatiblity bugfixes for deprecation warnings. Tested. Bugfixes.

### Composer requirements (6)

- 2025-08-24 `acf172d10` tooling: Update composer.json changed package vendor name
- 2025-08-24 `f68a4f210` tooling: Update composer.json added tag to test deployment workflow. No change.
- 2025-08-24 `1b35573f6` tooling: Update composer.json initial attemp at working around breakdowns in forking package due to dependencies conflicts from other packages. Bugfix attempt.
- 2025-08-24 `cc1e3c14f` tooling: Update composer.json
- 2026-01-13 `5fc51171c` release: Bugfix for composer.json validation test to pass. Bugfix.
- 2026-01-30 `66f8c749c` tooling: Replace twig/twig with se7enxweb/twig in composer.json

### Test tooling (1)

- 2025-08-24 `cd6d0491d` tooling: Update ArrayNode.php tested as working bugfix for our env. Note: This is likely not required anylonger but we must test with it. Incomplete Patch.

### Documentation (1)

- 2026-02-14 `14065e4da` docs: Added bugfixes required by 7x Nexus to support default installations with more graceful features implmentations. Added support for change of behavior 

### Bug fixes (1)

- 2026-04-09 `feb97c92f` fix: guard ini_set() in NativeFileSessionHandler against active/sent sessions

Also: 2 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of symfony](../ledger/symfony.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/symfony.md)
- [Framework forks on PHP 8.5](../../features/6.0/platform-php85-framework-forks.md)
- Platform ecosystem by month: [2025-08](months/2025-08.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)

<!-- rev2-see-also:end -->
