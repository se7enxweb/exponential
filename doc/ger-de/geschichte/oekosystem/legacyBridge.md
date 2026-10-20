# legacyBridge: Geschichte des Plattform-Repositorys

Die Geschichte von `legacyBridge`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Legacy Bridge und Site-Bundles). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 49 Änderungen von 2025-07-01 bis 2026-04-17, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Bridge, die den Legacy-Kernel von Exponential innerhalb der Symfony-Plattform ausführt (se7enxweb/legacy-bridge).

## Wie es mit Exponential zusammenhängt

Das Stück, mit dem eine Installation sowohl die Legacy-Administration und -Templates als auch den Symfony-Stack ausliefert.

## Was Anwender davon haben

Eine bestehende Website mit Exponential 6 neben Platform 3.x / 4.6 / 5.x betreiben, mit Code, der mit PHP 8.x kompatibel ist, und den Befehlen exponential:legacy:*.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/legacy-bridge
```

## Befehle

Geprüft in `bundle/Command/*.php` des Repositorys (`setName` und `setDescription` jeder Klasse). Führen Sie sie im Symfony-Projekt aus, das die Bridge enthält, als `php bin/console <name>`; `php bin/console <name> --help` gibt die Hilfe zu jedem aus.

| Befehl | Was er tut (aus der Beschreibung des Befehls) | Veralteter Alias (wird noch angenommen) |
|---|---|---|
| `exponential:legacy:init` | Bereitet eine Plattform-Installation für die Verwendung mit Legacy vor. | `ezpublish:legacy:init` |
| `exponential:legacy:configure` | Erzeugt die Konfiguration der Plattform aus einem bestehenden Legacy-Installationsverzeichnis. | `ezpublish:configure` |
| `exponential:legacy:install-extensions` | Installiert Legacy-Erweiterungen, die Symfony-Bundles mitbringen (standardmäßig als Symlink), in das Erweiterungsverzeichnis von Legacy. | `ezpublish:legacybundles:install_extensions` |
| `exponential:legacy:symlink` | Installiert Projekteinstellungen und Design-Dateien für Legacy aus `src` in das Legacy-Verzeichnis. | `ezpublish:legacy:symlink` |
| `exponential:legacy:assets-install` | Installiert die Assets der Legacy-Installation und die Wrapper-Scripts für die Front-Controller. | `ezpublish:legacy:assets_install` |
| `exponential:legacy:script` | Führt ein Legacy-Kommandozeilen-Script innerhalb der Bridge aus. | `ezpublish:legacy:script` |

Hier nicht ausgeführt: Diese Befehle brauchen ein Symfony-Projekt mit installierter Bridge, und das ist diese Installation nicht. Zur Kontrolle öffnen Sie die oben genannten Befehlsklassen.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 8 |
| Fehlerbehebungen | 30 |
| Verhaltens- und Upgrade-Änderungen | 3 |
| Werkzeuge | 5 |
| Releases | 3 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-08-25 | v2.1.10 | `441d49f` | Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix. |
| 2026-02-25 | v2.1.11 | `9195072` | Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. |
| 2026-03-25 | 3.0.0.1 | `54aca93` | create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x |
| 2026-03-25 | 3.0.0.2 | `3da2a70` | relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution |
| 2026-03-25 | 3.0.0.3 | `7820f65` | replace ezsystems/ezpublish-legacy with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility |
| 2026-03-26 | 3.0.0.4 | `2079902` | remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass |
| 2026-03-26 | 3.0.0.5 | `232f212` | Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat |
| 2026-03-26 | 3.0.0.6 | `531ba3b` | replace removed Exponential.config.resolver.core with Exponential.config.resolver |
| 2026-03-26 | 3.0.0.7 | `4b484e5` | Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat |
| 2026-03-26 | 3.0.0.8 | `3a0d4e8` | replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent) |
| 2026-03-26 | 3.0.0.9 | `09fba84` | Fix REST event namespace for eZ Platform REST bundle |
| 2026-03-26 | 3.0.0.10 | `0a59fbf` | Use Twig for legacy response rendering |
| 2026-03-26 | 3.0.0.11 | `c4f1e87` | Use legacy storage connection in config mapper |
| 2026-03-26 | 3.0.0.12 | `9251bef` | Drop obsolete kernel.name usage |
| 2026-03-26 | 3.0.0.13 | `e6af074` | Align HTTP cache purger signatures |
| 2026-03-26 | 3.0.0.14 | `1962ca8` | Fix profiler collector signature |
| 2026-03-26 | 3.0.0.15 | `a903e1e` | Update Twig loader interfaces |
| 2026-03-26 | 3.0.0.16 | `6cde5d5` | Match installed Twig loader API |
| 2026-03-26 | 3.0.0.17 | `5990076` | Update legacy Twig bridge classes |
| 2026-03-26 | 3.0.0.18 | `85c6b7f` | Fix Twig environment typehints |
| 2026-03-26 | 3.0.0.19 | `6cbc9e3` | Stop replacing initialized legacy closures |
| 2026-03-26 | 3.0.0.20 | `bf3952c` | Stop swapping legacy kernel handlers in console |
| 2026-03-26 | 3.0.0.21 | `891de81` | Use Symfony Contracts events |
| 2026-03-26 | 3.0.0.22 | `c8a5224` | Update legacy event dispatching |
| 2026-03-26 | 3.0.0.23 | `b9d41db` | Select CLI legacy handler in console |
| 2026-03-26 | 3.0.0.24 | `05b2c60` | Inject siteaccess into CLI handler factory |
| 2026-03-26 | 3.0.0.25 | `264f981` | Update login cleanup listener for Symfony 5 |
| 2026-03-27 | v3.0.0.26 | `a683980` | Twig 3 compatibility for eZ Platform 3.x |
| 2026-03-27 | v3.0.0.27 | `fc8faa3` | register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCo |
| 2026-03-27 | v3.0.0.28 | `bdae3a5` | refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility |
| 2026-04-06 | v4.0.0.0 | `cb08cb4` | Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement. |
| 2026-04-07 | v4.0.0.1 | `b926fc9` | SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI |
| 2026-04-16 | v4.0.0.2 | `df32ec7` | session.storage removed in Symfony 5.4 factory-based session config |
| 2026-04-17 | v4.0.0.3 | `d61370c` | fall back to sessionStorage when session service is null on Symfony 5.3+ |

## Änderungen des se7enxweb-Teams, nach Thema

### Kompatibilität mit Symfony und der Plattform (9)

- 2026-03-26 `2079902` Fehlerbehebung: remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass
- 2026-03-26 `232f212` Fehlerbehebung: Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat
- 2026-03-26 `3a0d4e8` Fehlerbehebung: replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent)
- 2026-03-27 `a683980` Fehlerbehebung: Twig 3 compatibility for eZ Platform 3.x
- 2026-03-27 `bdae3a5` Fehlerbehebung: refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility
- 2026-04-04 `8317c53` Fehlerbehebung: Ibexa 4.6 compat - service/param aliases, session mapper, csrf token, cache purger, boot guard, lazy kernel init
- 2026-04-04 `ed75494` Fehlerbehebung: Symfony 5.4 compat - create session.storage alias when storage_factory_id is used
- 2026-04-06 `cb08cb4` Fehlerbehebung: Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement.
- 2026-04-16 `df32ec7` Fehlerbehebung: session.storage removed in Symfony 5.4 factory-based session config

### Weitere Änderungen am Fork (9)

- 2026-03-26 `c4f1e87` Fehlerbehebung: Use legacy storage connection in config mapper
- 2026-03-26 `9251bef` Fehlerbehebung: Drop obsolete kernel.name usage
- 2026-03-26 `e6af074` Fehlerbehebung: Align HTTP cache purger signatures
- 2026-03-26 `6cbc9e3` Fehlerbehebung: Stop replacing initialized legacy closures
- 2026-03-26 `bf3952c` Fehlerbehebung: Stop swapping legacy kernel handlers in console
- 2026-03-26 `891de81` Fehlerbehebung: Use Symfony Contracts events
- 2026-03-26 `c8a5224` Fehlerbehebung: Update legacy event dispatching
- 2026-03-26 `b9d41db` Fehlerbehebung: Select CLI legacy handler in console
- 2026-03-26 `05b2c60` Fehlerbehebung: Inject siteaccess into CLI handler factory

### Fehlerbehebungen (8)

- 2026-03-25 `3da2a70` Fehlerbehebung: relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution
- 2026-03-26 `531ba3b` Fehlerbehebung: replace removed Exponential.config.resolver.core with Exponential.config.resolver
- 2026-03-26 `09fba84` Fehlerbehebung: Fix REST event namespace for eZ Platform REST bundle
- 2026-03-26 `1962ca8` Fehlerbehebung: Fix profiler collector signature
- 2026-03-26 `b2957a2` Fehlerbehebung: Fix getCurrentUser() -> getPermissionResolver()->getCurrentUserReference()->getUserId() for eZ Platform 3 API
- 2026-03-27 `fc8faa3` Fehlerbehebung: register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCo
- 2026-04-04 `f269b61` Fehlerbehebung: register legacy_mode under ibexa.site_access.config namespace
- 2026-04-17 `d61370c` Fehlerbehebung: fall back to sessionStorage when session service is null on Symfony 5.3+

### Composer-Abhängigkeiten (6)

- 2025-07-01 `75e0f05` Werkzeuge: Update composer.json switched vendor name
- 2025-07-01 `d9816e9` Werkzeuge: Update composer.json replaced package vendor name
- 2025-08-24 `4b45ba8` Werkzeuge: Update composer.json to include replacement package vendor name for twig/twig and sensio/distribution-bundle. Forking for changes.
- 2025-08-25 `8e158d2` Werkzeuge: Update composer.json testing switch to dev-main for exponential 6.0.x. Testing.
- 2025-08-25 `3c23639` Werkzeuge: Update composer.json replacing pacakge name and version back to tagged releases. Testing.
- 2025-08-25 `441d49f` Release: Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix.

### Design und Templates (5)

- 2026-03-26 `0a59fbf` Funktion: Use Twig for legacy response rendering
- 2026-03-26 `a903e1e` Funktion: Update Twig loader interfaces
- 2026-03-26 `6cde5d5` Funktion: Match installed Twig loader API
- 2026-03-26 `5990076` Funktion: Update legacy Twig bridge classes
- 2026-03-26 `85c6b7f` Funktion: Fix Twig environment typehints

### Kompatibilität mit PHP 8.x (4)

- 2025-08-24 `7d0dcae` Fehlerbehebung: Update LegacyWrapperInstallCommand.php to include working change to run ezplatform-legacy with this bundle. This change fixed transparently but not correctly a path bug (...)
- 2026-03-25 `54aca93` Fehlerbehebung: create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x
- 2026-03-26 `4b484e5` Fehlerbehebung: Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat
- 2026-04-05 `6ca1508` Fehlerbehebung: PHP 8 compat - add int return type and return 0 to Command execute() methods

### Paket auf den Vendor se7enxweb umbenannt (2)

- 2026-03-25 `7820f65` BC: replace ezsystems/ezpublish-legacy with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility
- 2026-04-04 `7b57b96` BC: require se7enxweb/exponential dev-main instead of ^6.0.12

### Responsives Admin-Design (admin3) (1)

- 2026-02-25 `9195072` Funktion: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement.

### Aufräumen (1)

- 2026-03-26 `264f981` Release: Update login cleanup listener for Symfony 5

### Versionsnummern (1)

- 2026-04-06 `8fc9d52` Release: Version API/Specific/Release Switch for se7enxweb/ezplatform-xmltext-fieldtype package requirements. Bugfix.

### Mit der Distribution gelieferte Erweiterungen (1)

- 2026-04-06 `0719670` Funktion: Altering legacyBridge init_ini settings override installation file site.ini.append.php to test additional configuration items and remove ezmbpaex exte

### Unterstützung für SQLite (1)

- 2026-04-07 `b926fc9` Funktion: SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI

### Namen der Konsolenbefehle (1)

- 2026-04-07 `8cea9e8` BC: rename legacy commands to exponential:legacy:* prefix, keep ezpublish:* as deprecated aliases (v4/4.x)

## Verwandte Seiten

- [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md)
- [Spezifikation der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- [Namen der Konsolenbefehle](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Release-Changelog](../../../changelogs/extensions/legacyBridge.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/legacyBridge.md)
- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- Plattform-Ökosystem nach Monat: [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
