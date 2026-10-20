# Change ledger: symfony

Every change made to `symfony` since the se7enxweb era began, oldest first: 18 changes touching 21 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 14 |
| Other | 3 |
| Merged | 1 |

## 2025-08 (8 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-24 | `acf172d106` | Updated | Update composer.json changed package vendor name | 1 | +1 / −1 |  |
| 2025-08-24 | `f68a4f210b` | Updated | Update composer.json added tag to test deployment workflow. No change. | 1 | +1 / −1 |  |
| 2025-08-24 | `9dcfb7a696` | Updated | Update ErrorHandler.php adding tested php 8.2 support with hopes it will test working for php 8.4+. New PHP 8 Support. | 1 | +5 / −5 |  |
| 2025-08-24 | `cd6d0491dd` | Updated | Update ArrayNode.php tested as working bugfix for our env. Note: This is likely not required anylonger but we must test with it. Incomplete Patch. | 1 | +6 / −0 |  |
| 2025-08-24 | `df6faf1aad` | Updated | Update ExceptionCaster.php to include bugfix for php8.4 support as tested with ezplatform 2.5 gpl and confirmed working. New PHP 8.4 Support. | 1 | +1 / −1 |  |
| 2025-08-24 | `118d7f4b03` | Updated | Update LazyLoadingValueHolderGenerator.php to include php 8.2+ tested as working changes to reflect Zend to Laminas Library Transition. New PHP 8 Support | 1 | +1 / −1 |  |
| 2025-08-24 | `1b35573f6e` | Updated | Update composer.json initial attemp at working around breakdowns in forking package due to dependencies conflicts from other packages. Bugfix attempt. | 1 | +5 / −2 |  |
| 2025-08-24 | `cc1e3c14ff` | Updated | Update composer.json | 1 | +2 / −2 |  |

## 2026-01 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-01-13 | `5fc51171c1` | Updated | Updated: Bugfix for composer.json validation test to pass. Bugfix. | 1 | +1 / −1 | v3.4.50 |
| 2026-01-28 | `f2852d4521` | Other | PHP 8.x: Add return types to VarDumper Data class | 1 | +6 / −6 |  |
| 2026-01-28 | `e39f6b0c1b` | Updated | Updated: PHP 8.x closure to method reference in WebProfilerExtension | 1 | +6 / −5 |  |
| 2026-01-29 | `fd017e42a3` | Updated | Updated: Add explicit nullable types and fix ReflectionProperty deprecation in ErrorHandler.php for PHP 8.1+ | 1 | +9 / −3 |  |
| 2026-01-30 | `66f8c749c6` | Other | Replace twig/twig with se7enxweb/twig in composer.json | 1 | +1 / −1 |  |

## 2026-02 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-10 | `4e267ed5bb` | Updated | Update: PHP85 Compatiblity bugfixes for deprecation warnings. Tested. Bugfixes. | 2 | +2 / −2 |  |
| 2026-02-10 | `894ed58062` | Merged | Merge remote-tracking branch 'refs/remotes/origin/3.4' into 3.4 | 0 | +0 / −0 |  |
| 2026-02-14 | `14065e4dab` | Updated | Updated: Added bugfixes required by 7x Nexus to support default installations with more graceful features implmentations. Added support for change of behavior where deprecations statistics generation and storage (affecting performance negatively on dev usage +3k lines per request) are supressed from affecting request performance in any way by default unless the SYMFONY_COLLECT_DEPRECATIONS is set to 1. Enhancements. | 4 | +80 / −11 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `9f6fcd7935` | Other | chore: add GitHub Sponsors funding metadata | 1 | +3 / −0 |  |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-09 | `feb97c92f3` | Updated | fix: guard ini_set() in NativeFileSessionHandler against active/sent sessions | 1 | +7 / −2 |  |
