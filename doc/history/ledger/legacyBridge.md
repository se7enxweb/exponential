# Change ledger: legacyBridge

Every change made to `legacyBridge` since the se7enxweb era began, oldest first: 49 changes touching 96 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 36 |
| Other | 11 |
| Removed | 1 |
| Added | 1 |

## 2025-07 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-01 | `75e0f05` | Updated | Update composer.json switched vendor name | 1 | +5 / −5 |  |
| 2025-07-01 | `d9816e9` | Updated | Update composer.json replaced package vendor name | 1 | +1 / −1 |  |

## 2025-08 (5 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-24 | `7d0dcae` | Updated | Update LegacyWrapperInstallCommand.php to include working change to run ezplatform-legacy with this bundle. This change fixed transparently but not correctly a path bug with single quotes wraping targetArg which was 'web'. Tested as working. PHP 8 Support. | 1 | +2 / −1 |  |
| 2025-08-24 | `4b45ba8` | Updated | Update composer.json to include replacement package vendor name for twig/twig and sensio/distribution-bundle. Forking for changes. | 1 | +3 / −3 |  |
| 2025-08-25 | `8e158d2` | Updated | Update composer.json testing switch to dev-main for exponential 6.0.x. Testing. | 1 | +3 / −2 |  |
| 2025-08-25 | `3c23639` | Updated | Update composer.json replacing pacakge name and version back to tagged releases. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `441d49f` | Updated | Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix. | 1 | +2 / −2 | v2.1.10 |

## 2026-02 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-25 | `9195072` | Updated | Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. | 1 | +5 / −2 | v2.1.11 |

## 2026-03 (29 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-25 | `54aca93` | Other | chore: create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x | 1 | +15 / −13 | 3.0.0.1 |
| 2026-03-25 | `3da2a70` | Updated | fix: relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution | 1 | +1 / −1 | 3.0.0.2 |
| 2026-03-25 | `7820f65` | Updated | fix: replace ezsystems/Exponential with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility | 1 | +1 / −1 | 3.0.0.3 |
| 2026-03-26 | `2079902` | Updated | fix: remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass | 2 | +3 / −22 | 3.0.0.4 |
| 2026-03-26 | `232f212` | Updated | Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat | 1 | +3 / −3 | 3.0.0.5 |
| 2026-03-26 | `531ba3b` | Updated | Fix: replace removed ezpublish.config.resolver.core with ezpublish.config.resolver | 4 | +36 / −4 | 3.0.0.6 |
| 2026-03-26 | `4b484e5` | Updated | Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat | 1 | +4 / −4 | 3.0.0.7 |
| 2026-03-26 | `3a0d4e8` | Updated | Fix: replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent) | 9 | +24 / −24 | 3.0.0.8 |
| 2026-03-26 | `09fba84` | Updated | Fix REST event namespace for eZ Platform REST bundle | 1 | +1 / −1 | 3.0.0.9 |
| 2026-03-26 | `0a59fbf` | Other | Use Twig for legacy response rendering | 2 | +4 / −4 | 3.0.0.10 |
| 2026-03-26 | `c4f1e87` | Other | Use legacy storage connection in config mapper | 2 | +5 / −5 | 3.0.0.11 |
| 2026-03-26 | `9251bef` | Removed | Drop obsolete kernel.name usage | 1 | +1 / −1 | 3.0.0.12 |
| 2026-03-26 | `e6af074` | Other | Align HTTP cache purger signatures | 1 | +4 / −6 | 3.0.0.13 |
| 2026-03-26 | `1962ca8` | Updated | Fix profiler collector signature | 1 | +2 / −1 | 3.0.0.14 |
| 2026-03-26 | `a903e1e` | Updated | Update Twig loader interfaces | 1 | +5 / −5 | 3.0.0.15 |
| 2026-03-26 | `6cde5d5` | Other | Match installed Twig loader API | 1 | +5 / −6 | 3.0.0.16 |
| 2026-03-26 | `5990076` | Updated | Update legacy Twig bridge classes | 4 | +28 / −25 | 3.0.0.17 |
| 2026-03-26 | `85c6b7f` | Updated | Fix Twig environment typehints | 1 | +3 / −3 | 3.0.0.18 |
| 2026-03-26 | `6cbc9e3` | Other | Stop replacing initialized legacy closures | 2 | +0 / −6 | 3.0.0.19 |
| 2026-03-26 | `bf3952c` | Other | Stop swapping legacy kernel handlers in console | 1 | +0 / −24 | 3.0.0.20 |
| 2026-03-26 | `891de81` | Other | Use Symfony Contracts events | 4 | +4 / −4 | 3.0.0.21 |
| 2026-03-26 | `c8a5224` | Updated | Update legacy event dispatching | 2 | +14 / −14 | 3.0.0.22 |
| 2026-03-26 | `b9d41db` | Other | Select CLI legacy handler in console | 2 | +25 / −1 | 3.0.0.23 |
| 2026-03-26 | `05b2c60` | Other | Inject siteaccess into CLI handler factory | 2 | +7 / −4 | 3.0.0.24 |
| 2026-03-26 | `264f981` | Updated | Update login cleanup listener for Symfony 5 | 1 | +4 / −5 | 3.0.0.25 |
| 2026-03-26 | `b2957a2` | Updated | Fix getCurrentUser() -> getPermissionResolver()->getCurrentUserReference()->getUserId() for eZ Platform 3 API | 1 | +3 / −3 |  |
| 2026-03-27 | `a683980` | Updated | fix: Twig 3 compatibility for eZ Platform 3.x | 2 | +1 / −9 | v3.0.0.26 |
| 2026-03-27 | `fc8faa3` | Updated | fix: register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCommand | 2 | +54 / −11 | v3.0.0.27 |
| 2026-03-27 | `bdae3a5` | Updated | fix: refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility | 6 | +132 / −42 | v3.0.0.28 |

## 2026-04 (12 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-04 | `7b57b96` | Updated | Fixed: require se7enxweb/exponential dev-main instead of ^6.0.12 | 1 | +1 / −1 |  |
| 2026-04-04 | `8317c53` | Updated | Fix: Ibexa 4.6 compat - service/param aliases, session mapper, csrf token, cache purger, boot guard, lazy kernel init | 7 | +217 / −15 |  |
| 2026-04-04 | `ed75494` | Updated | Fix: Symfony 5.4 compat - create session.storage alias when storage_factory_id is used | 2 | +22 / −0 |  |
| 2026-04-04 | `f269b61` | Updated | Fix: register legacy_mode under ibexa.site_access.config namespace | 2 | +4 / −0 |  |
| 2026-04-05 | `6ca1508` | Updated | Fix: PHP 8 compat - add int return type and return 0 to Command execute() methods | 3 | +9 / −3 |  |
| 2026-04-06 | `8fc9d52` | Updated | Updated: Version API/Specific/Release Switch for se7enxweb/ezplatform-xmltext-fieldtype package requirements. Bugfix. | 1 | +1 / −1 |  |
| 2026-04-06 | `0719670` | Updated | Updated: Altering legacyBridge init_ini settings override installation file site.ini.append.php to test additional configuration items and remove ezmbpaex extension from default activation due to extra database tables requirement technicalities also commonly not-used. Bugfix. | 1 | +20 / −8 |  |
| 2026-04-06 | `cb08cb4` | Updated | Updated: Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement. | 1 | +1 / −0 | v4.0.0.0 |
| 2026-04-07 | `b926fc9` | Other | SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI | 1 | +7 / −0 | v4.0.0.1 |
| 2026-04-07 | `8cea9e8` | Added | feat: rename legacy commands to exponential:legacy:* prefix, keep Exponential:* as deprecated aliases (v4/4.x) | 6 | +12 / −6 |  |
| 2026-04-16 | `df32ec7` | Updated | fix: session.storage removed in Symfony 5.4 factory-based session config | 2 | +3 / −3 | v4.0.0.2 |
| 2026-04-17 | `d61370c` | Updated | fix(session): fall back to sessionStorage when session service is null on Symfony 5.3+ | 1 | +14 / −5 | v4.0.0.3 origin/4.x |
