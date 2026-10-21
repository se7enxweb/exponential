# Änderungsverzeichnis: legacyBridge

Jede Änderung an `legacyBridge` seit Beginn der se7enxweb-Ära, die älteste zuerst: 49 Änderungen an 96 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 36 |
| Sonstiges | 11 |
| Entfernt | 1 |
| Hinzugefügt | 1 |

## 2025-07 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-01 | `75e0f05` | Aktualisiert | Update composer.json switched vendor name | 1 | +5 / −5 |  |
| 2025-07-01 | `d9816e9` | Aktualisiert | Update composer.json replaced package vendor name | 1 | +1 / −1 |  |

## 2025-08 (5 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-24 | `7d0dcae` | Aktualisiert | Update LegacyWrapperInstallCommand.php to include working change to run ezplatform-legacy with this bundle. This change fixed transparently but not correctly a path bug with single quotes wraping targetArg which was 'web'. Tested as working. PHP 8 Support. | 1 | +2 / −1 |  |
| 2025-08-24 | `4b45ba8` | Aktualisiert | Update composer.json to include replacement package vendor name for twig/twig and sensio/distribution-bundle. Forking for changes. | 1 | +3 / −3 |  |
| 2025-08-25 | `8e158d2` | Aktualisiert | Update composer.json testing switch to dev-main for exponential 6.0.x. Testing. | 1 | +3 / −2 |  |
| 2025-08-25 | `3c23639` | Aktualisiert | Update composer.json replacing pacakge name and version back to tagged releases. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `441d49f` | Aktualisiert | Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix. | 1 | +2 / −2 | v2.1.10 |

## 2026-02 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-25 | `9195072` | Aktualisiert | Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. | 1 | +5 / −2 | v2.1.11 |

## 2026-03 (29 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-25 | `54aca93` | Sonstiges | chore: create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x | 1 | +15 / −13 | 3.0.0.1 |
| 2026-03-25 | `3da2a70` | Aktualisiert | fix: relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution | 1 | +1 / −1 | 3.0.0.2 |
| 2026-03-25 | `7820f65` | Aktualisiert | fix: replace ezsystems/Exponential with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility | 1 | +1 / −1 | 3.0.0.3 |
| 2026-03-26 | `2079902` | Aktualisiert | fix: remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass | 2 | +3 / −22 | 3.0.0.4 |
| 2026-03-26 | `232f212` | Aktualisiert | Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat | 1 | +3 / −3 | 3.0.0.5 |
| 2026-03-26 | `531ba3b` | Aktualisiert | Fix: replace removed ezpublish.config.resolver.core with ezpublish.config.resolver | 4 | +36 / −4 | 3.0.0.6 |
| 2026-03-26 | `4b484e5` | Aktualisiert | Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat | 1 | +4 / −4 | 3.0.0.7 |
| 2026-03-26 | `3a0d4e8` | Aktualisiert | Fix: replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent) | 9 | +24 / −24 | 3.0.0.8 |
| 2026-03-26 | `09fba84` | Aktualisiert | Fix REST event namespace for eZ Platform REST bundle | 1 | +1 / −1 | 3.0.0.9 |
| 2026-03-26 | `0a59fbf` | Sonstiges | Use Twig for legacy response rendering | 2 | +4 / −4 | 3.0.0.10 |
| 2026-03-26 | `c4f1e87` | Sonstiges | Use legacy storage connection in config mapper | 2 | +5 / −5 | 3.0.0.11 |
| 2026-03-26 | `9251bef` | Entfernt | Drop obsolete kernel.name usage | 1 | +1 / −1 | 3.0.0.12 |
| 2026-03-26 | `e6af074` | Sonstiges | Align HTTP cache purger signatures | 1 | +4 / −6 | 3.0.0.13 |
| 2026-03-26 | `1962ca8` | Aktualisiert | Fix profiler collector signature | 1 | +2 / −1 | 3.0.0.14 |
| 2026-03-26 | `a903e1e` | Aktualisiert | Update Twig loader interfaces | 1 | +5 / −5 | 3.0.0.15 |
| 2026-03-26 | `6cde5d5` | Sonstiges | Match installed Twig loader API | 1 | +5 / −6 | 3.0.0.16 |
| 2026-03-26 | `5990076` | Aktualisiert | Update legacy Twig bridge classes | 4 | +28 / −25 | 3.0.0.17 |
| 2026-03-26 | `85c6b7f` | Aktualisiert | Fix Twig environment typehints | 1 | +3 / −3 | 3.0.0.18 |
| 2026-03-26 | `6cbc9e3` | Sonstiges | Stop replacing initialized legacy closures | 2 | +0 / −6 | 3.0.0.19 |
| 2026-03-26 | `bf3952c` | Sonstiges | Stop swapping legacy kernel handlers in console | 1 | +0 / −24 | 3.0.0.20 |
| 2026-03-26 | `891de81` | Sonstiges | Use Symfony Contracts events | 4 | +4 / −4 | 3.0.0.21 |
| 2026-03-26 | `c8a5224` | Aktualisiert | Update legacy event dispatching | 2 | +14 / −14 | 3.0.0.22 |
| 2026-03-26 | `b9d41db` | Sonstiges | Select CLI legacy handler in console | 2 | +25 / −1 | 3.0.0.23 |
| 2026-03-26 | `05b2c60` | Sonstiges | Inject siteaccess into CLI handler factory | 2 | +7 / −4 | 3.0.0.24 |
| 2026-03-26 | `264f981` | Aktualisiert | Update login cleanup listener for Symfony 5 | 1 | +4 / −5 | 3.0.0.25 |
| 2026-03-26 | `b2957a2` | Aktualisiert | Fix getCurrentUser() -> getPermissionResolver()->getCurrentUserReference()->getUserId() for eZ Platform 3 API | 1 | +3 / −3 |  |
| 2026-03-27 | `a683980` | Aktualisiert | fix: Twig 3 compatibility for eZ Platform 3.x | 2 | +1 / −9 | v3.0.0.26 |
| 2026-03-27 | `fc8faa3` | Aktualisiert | fix: register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCommand | 2 | +54 / −11 | v3.0.0.27 |
| 2026-03-27 | `bdae3a5` | Aktualisiert | fix: refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility | 6 | +132 / −42 | v3.0.0.28 |

## 2026-04 (12 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-04 | `7b57b96` | Aktualisiert | Fixed: require se7enxweb/exponential dev-main instead of ^6.0.12 | 1 | +1 / −1 |  |
| 2026-04-04 | `8317c53` | Aktualisiert | Fix: Ibexa 4.6 compat - service/param aliases, session mapper, csrf token, cache purger, boot guard, lazy kernel init | 7 | +217 / −15 |  |
| 2026-04-04 | `ed75494` | Aktualisiert | Fix: Symfony 5.4 compat - create session.storage alias when storage_factory_id is used | 2 | +22 / −0 |  |
| 2026-04-04 | `f269b61` | Aktualisiert | Fix: register legacy_mode under ibexa.site_access.config namespace | 2 | +4 / −0 |  |
| 2026-04-05 | `6ca1508` | Aktualisiert | Fix: PHP 8 compat - add int return type and return 0 to Command execute() methods | 3 | +9 / −3 |  |
| 2026-04-06 | `8fc9d52` | Aktualisiert | Updated: Version API/Specific/Release Switch for se7enxweb/ezplatform-xmltext-fieldtype package requirements. Bugfix. | 1 | +1 / −1 |  |
| 2026-04-06 | `0719670` | Aktualisiert | Updated: Altering legacyBridge init_ini settings override installation file site.ini.append.php to test additional configuration items and remove ezmbpaex extension from default activation due to extra database tables requirement technicalities also commonly not-used. Bugfix. | 1 | +20 / −8 |  |
| 2026-04-06 | `cb08cb4` | Aktualisiert | Updated: Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement. | 1 | +1 / −0 | v4.0.0.0 |
| 2026-04-07 | `b926fc9` | Sonstiges | SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI | 1 | +7 / −0 | v4.0.0.1 |
| 2026-04-07 | `8cea9e8` | Hinzugefügt | feat: rename legacy commands to exponential:legacy:* prefix, keep Exponential:* as deprecated aliases (v4/4.x) | 6 | +12 / −6 |  |
| 2026-04-16 | `df32ec7` | Aktualisiert | fix: session.storage removed in Symfony 5.4 factory-based session config | 2 | +3 / −3 | v4.0.0.2 |
| 2026-04-17 | `d61370c` | Aktualisiert | fix(session): fall back to sessionStorage when session service is null on Symfony 5.3+ | 1 | +14 / −5 | v4.0.0.3 origin/4.x |
