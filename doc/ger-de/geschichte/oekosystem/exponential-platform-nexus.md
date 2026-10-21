# exponential-platform-nexus: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-nexus`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 97 Änderungen von 2025-07-01 bis 2026-09-01, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Exponential Platform Nexus: Netgen Layouts + Media Site + Tags + Plattform und Legacy.

## Wie es mit Exponential zusammenhängt

Die fertige Website-Distribution, die das Design der Media-Site darstellt; Linien 1.x und 2.5.x.

## Was Anwender davon haben

Eine funktionierende Website mit Demo-Inhalten, Layouts und Administration, in Minuten installiert.

Dieses Repository ist ein Composer-Projekt (Typ `project`). Für die Installationsschritte verweist seine README auf seine eigene `doc/INSTALL.md`; lesen Sie zuerst diese Datei und folgen Sie ihr dann. Der Quick Start des Nexus-Starters (`git clone`, `composer install`, `php bin/console exponential:install exponential-media --no-interaction`) ist die am nächsten verwandte dokumentierte Abfolge.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 17 |
| Fehlerbehebungen | 21 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Dokumentation | 21 |
| Werkzeuge | 25 |
| Releases | 6 |
| Ohne Nutzen für Anwender | 6 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-08-25 | v2.5.0.0 | `9b35c2589` | Update composer.json testing with release version instead of dev-main. Testing. |
| 2025-09-14 | v2.5.0.1 | `eb660eb1a` | Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. |
| 2026-01-28 | v1.0.0.0.1 | `73a455ba3` | Added app/AppKernel rules to support tagsbundle usage by default. Enhancement. |
| 2026-02-10 | v1.0.0.0.2 | `1673ce672` | Change version number to 1.0.0.0.2 |
| 2026-02-26 | v1.0.0.0.3 | `c735c0e8b` | Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab |
| 2026-04-21 | 1.0.0.4 | `2272f3976` | add missing ngsite.default.locations.tree_root.id parameter |
| 2026-04-21 | 1.0.0.5 | `64700fbbf` | wire SQLite database_path parameter into Doctrine DBAL connection |
| 2026-04-21 | 1.0.0.6 | `8bfb5d23a` | add exponential-cjw installer type with full SQLite seed data |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (25)

- 2025-07-01 `1e0297e4f` Werkzeuge: Update composer.json replaced package vendor name
- 2025-07-01 `f5538cbbf` Werkzeuge: Update composer.json increased package php support
- 2025-07-01 `6eedef300` Werkzeuge: Update composer.json switch package dep vendor for Exponential legacy and version
- 2025-07-01 `5d53cb661` Werkzeuge: Update composer.json change from gplv2 only to gplv2 or later
- 2025-07-01 `c01377f2f` Werkzeuge: Update composer.json homepage url vendor name change
- 2025-07-01 `0beb934a3` Werkzeuge: Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time.
- 2025-08-24 `333d35374` Werkzeuge: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages success
- 2025-08-24 `fef26dacf` Werkzeuge: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix.
- 2025-08-24 `dbceda78b` Werkzeuge: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix.
- 2025-08-24 `23adafb6b` Werkzeuge: Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix.
- 2025-08-25 `528da80f0` Werkzeuge: Update composer.json change package name. Forking for changes.
- 2025-08-25 `22872681b` Werkzeuge: Update composer.json testing pulling latest changes from child package.
- 2025-08-25 `559dddf68` Werkzeuge: Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing.
- 2025-08-25 `7a6abdab9` Werkzeuge: Update composer.json replaced Exponential package name with exponential. Testing.
- 2025-08-25 `9b35c2589` Release: Update composer.json testing with release version instead of dev-main. Testing.
- 2025-08-25 `544d935ff` Werkzeuge: Update composer.json minor change to support replacement installer. testing
- 2026-01-27 `90a3d1b92` Werkzeuge: Our first update to the initial composer.json requirements. First commit!
- 2026-01-27 `5c67ccbca` Werkzeuge: Clean up indentation of the initial composer.json file internals. CS
- 2026-01-27 `06ea3cc10` Werkzeuge: Clean up indentation of the initial composer.json file internals. CS
- 2026-01-28 `03c2e5ecd` Werkzeuge: Changes required to successfully install composer package. Tested working.
- 2026-01-28 `b38241f16` Werkzeuge: Updated composer.json rules to add cjw-network/cjw-config-processor package. New feature.
- 2026-01-29 `5fafc8ff6` Werkzeuge: Updated composer.json to provide additional autoload support for base packages and include tags and admin-ui bundles. New Features.
- 2026-01-30 `d58d9a04b` Werkzeuge: Major stability and bugfixes provided with this composer.json configuration. Bugfixes.
- 2026-01-31 `030b6c7f2` Werkzeuge: Bugfixes for composer.json configuration defaults for next release. Including yaml configuration file changes which prevent errors with the netgen pla
- 2026-02-27 `c0ad4e66f` Werkzeuge: Added 8.0 PHP require composer support. Enhancement for long term support.

### Dokumentation (21)

- 2025-08-25 `5e5af1cda` Dokumentation: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping.
- 2025-08-25 `0627f499c` Dokumentation: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation.
- 2025-08-25 `370b0ed19` Dokumentation: Updated README.md documentation for package. Removed Logo Images. Documentation.
- 2025-08-25 `b3f0465c9` Dokumentation: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-25 `531199a19` Dokumentation: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-29 `987808a45` Dokumentation: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement.
- 2026-01-29 `4fe3d6c6a` Dokumentation: Updated README.md documumentation. Doc
- 2026-02-15 `b85007b72` Dokumentation: Updated Current Year in Copyright, License and Readme docs. Enhancement.
- 2026-02-15 `3a672dad6` Dokumentation: Updated README.md documentation to provide expanded software features provided (via bundles) and about them in general.
- 2026-02-15 `c7368151f` Dokumentation: Updated README.md to update product name in license section. Doc.
- 2026-02-15 `3587d8ddb` Dokumentation: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation.
- 2026-02-15 `a763f33d3` Dokumentation: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation.
- 2026-02-15 `a954715ec` Dokumentation: Updated Nexus Project Support Documentation in SECURITY.md. Documentation.
- 2026-02-15 `f222971cb` Dokumentation: Updated project's documentation. Doc.
- 2026-02-15 `a5ecf11f1` Dokumentation: Updated Nexus Project Support Documentation in CONTRIBUTING.md. Documentation.
- 2026-02-15 `d619ac7f7` Dokumentation: Updated Nexus Project Support Documentation in doc/INSTALL.md. Documentation bugfix.
- 2026-02-15 `4ef0598cc` Dokumentation: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-18 `8d98173e4` Dokumentation: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc.
- 2026-02-18 `c9869ce9f` Dokumentation: Fixed Broken URLs. Bugfix. Doc.
- 2026-02-19 `7263ea28a` Dokumentation: Updated documentation related to installation process required covering symlink installation related to default required app bundle resources for publ
- 2026-02-19 `cf3ab0f3e` Dokumentation: Updated documentation related to installation process required currently requiring apcu caching features support for uncached page rendering time redu

### Fehlerbehebungen (17)

- 2025-08-24 `c6bf30937` Fehlerbehebung: Path to default pagelayout example fix. Bugfix.
- 2025-08-25 `40cf39f1d` Fehlerbehebung: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-08-25 `294783f9f` Fehlerbehebung: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-09-14 `eb660eb1a` Fehlerbehebung: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix.
- 2026-02-15 `e09174bc9` Fehlerbehebung: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-16 `2de55c0fc` Fehlerbehebung: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix.
- 2026-02-17 `cbff56972` Fehlerbehebung: Updated Starter Database Content And Full Dumps SQL to strip out previous project identifier text strings from default database. Bugfix.
- 2026-02-18 `b6760a3b4` Fehlerbehebung: Moving build conflicted files into separate storage locations for later installation. Cleanup. Bugfixes.
- 2026-02-18 `ba3d0513a` Fehlerbehebung: Added lexik translation tables to sql starter dump files to fix adminui translation dashboard. Bugfix.
- 2026-02-20 `7e40082d5` Fehlerbehebung: Added default symlinks to bundles required by default installation in web/bundles/ dir. Bugfix.
- 2026-02-20 `77385bf47` Fehlerbehebung: Cleanup of default configuration legacy settings. Cleanup. Bugfixes.
- 2026-02-20 `03f0aa542` Fehlerbehebung: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication. 
- 2026-02-26 `f17e5567a` Fehlerbehebung: Updated assets build dir path. Bugfix.
- 2026-02-26 `e6a727340` Fehlerbehebung: Updated security.yml settings defaults to use csrf features. Bugfix.
- 2026-04-21 `2272f3976` Fehlerbehebung: add missing ngsite.default.locations.tree_root.id parameter
- 2026-07-03 `29a12e6d5` Fehlerbehebung: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations
- 2026-07-03 `fb7280519` Fehlerbehebung: UTF-8 encoding corruption in MySQL installer SQL files

### Funktionen (7)

- 2026-01-28 `f4d2c8036` Funktion: Added app/config/routing.yml rules to support tagsbundle usage by default. Enhancement.
- 2026-01-28 `73a455ba3` Funktion: Added app/AppKernel rules to support tagsbundle usage by default. Enhancement.
- 2026-01-29 `c32465a70` Funktion: Register Netgen bundles in AppKernel.php for AdminUI and SiteBundle support
- 2026-02-15 `7ee98fce3` Funktion: Added and updated base app files and configuration representing v1.0.0.0.3 of 7x Exponential Platform Nexus. Enhancement.
- 2026-02-15 `1cd0045a4` Funktion: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/R
- 2026-02-26 `c735c0e8b` Funktion: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab
- 2026-09-01 `7117b3fff` Funktion: safe fallback in public/index_rest.php

### Versionsnummern (5)

- 2025-08-25 `ae60784f3` Release: Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing.
- 2025-09-14 `8344b2b44` Release: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding
- 2026-02-10 `1673ce672` Release: Change version number to 1.0.0.0.2
- 2026-02-15 `7ade2014c` Release: Updated README.md to change version number to 1.0.0.0.3
- 2026-02-20 `234b14091` Release: Updated composer.json package requirements to  Required package version bump.

### Design und Templates (3)

- 2026-02-03 `f83cafa46` Funktion: Extended example default pagelayout to support dynamic content display by default in a more flexible template code example. Enhancement.
- 2026-02-15 `0bc586124` Funktion: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix.
- 2026-02-15 `8a18f0060` Funktion: Added based var dir (including the) site storage dir (images in site content within the db content tree) to the base repository to ensure default inst

### Kompatibilität mit Symfony und der Plattform (2)

- 2025-08-24 `61e235b6e` Fehlerbehebung: Changes required to install and boot ezplatform 2.5 gpl based site
- 2026-02-10 `984ad373f` Fehlerbehebung: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This re

### Fertige Webserver-Konfiguration (2)

- 2025-08-29 `a17b4067b` Funktion: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement.
- 2025-08-29 `d143c44a9` Funktion: Added software example robots.txt configuration to web dir. Feature improvement.

### Pflege des Repositorys (2)

- 2026-02-15 `288e27520` ohne Nutzen für Anwender: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix.
- 2026-02-18 `f20dfb8a9` Werkzeuge: Removed junk logs from repo. Cleanup.

### Weitere Änderungen am Fork (2)

- 2026-02-20 `f14abd123` Fehlerbehebung: Revise INSTALL.md for improved clarity
- 2026-09-01 `f8d9fcb85` Fehlerbehebung: content_tree_module defaults in admin UI config

### Responsives Admin-Design (admin3) (2)

- 2026-02-25 `e9b0db995` Funktion: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes.
- 2026-02-26 `62fdfb56b` Funktion: In legacy.yml settings replaced sitedesign value 'admin' with 'admin3' for long term support and features. Enhancement.

### Unterstützung für SQLite (2)

- 2026-04-21 `64700fbbf` Funktion: wire SQLite database_path parameter into Doctrine DBAL connection
- 2026-04-21 `8bfb5d23a` Funktion: add exponential-cjw installer type with full SQLite seed data

### Marke Exponential (1)

- 2025-09-14 `f71c97e40` Funktion: Required changes to boot admin after rebranding and further test results. Bugfix.

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-01-29 `4edb425b3` BC: Replaced incenteev/composer-paramter-handler package with 7x maintained copy for php version support (backport+bugfix). Bugfix

Außerdem: 5 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Nexus](../../../eng-us/features/6.0/platform-nexus-starter.md)
- [Release-Changelog](../../../changelogs/extensions/exponential-platform-nexus.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-nexus.md)
- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md), [2026-07](monate/2026-07.md), [2026-09](monate/2026-09.md)
