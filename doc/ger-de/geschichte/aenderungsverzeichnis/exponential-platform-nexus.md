# Änderungsverzeichnis: exponential-platform-nexus

Jede Änderung an `exponential-platform-nexus` seit Beginn der se7enxweb-Ära, die älteste zuerst: 97 Änderungen an 5187 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 77 |
| Hinzugefügt | 13 |
| Zusammengeführt | 3 |
| Sonstiges | 3 |
| Entfernt | 1 |

## 2025-07 (6 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-01 | `1e0297e4f` | Aktualisiert | Update composer.json replaced package vendor name | 1 | +2 / −2 |  |
| 2025-07-01 | `f5538cbbf` | Aktualisiert | Update composer.json increased package php support | 1 | +1 / −1 |  |
| 2025-07-01 | `6eedef300` | Aktualisiert | Update composer.json switch package dep vendor for Exponential and version | 1 | +1 / −1 |  |
| 2025-07-01 | `5d53cb661` | Aktualisiert | Update composer.json change from gplv2 only to gplv2 or later | 1 | +1 / −1 |  |
| 2025-07-01 | `c01377f2f` | Aktualisiert | Update composer.json homepage url vendor name change | 1 | +1 / −1 |  |
| 2025-07-01 | `0beb934a3` | Aktualisiert | Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time. | 1 | +4 / −5 |  |

## 2025-08 (27 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-24 | `333d35374` | Aktualisiert | Updated: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages successfully. this is the first major progress point. after composer started running post package installation scripts (code already installed) it fails due to sub package dependencies like symfony / ez platform / sensio / and all other symfony based ecosystem packages that went previously unmaintained. Rejoice, 7x has already successfully patched this platform post packages installation so the composer install scripts run successfully all we need to do is fork various repositories and patch the files and release them as composer packages (forked by 7x). this list is long. more as it happens. Feature stable composer2 php8.2 installation. | 1 | +22 / −15 |  |
| 2025-08-24 | `61e235b6e` | Aktualisiert | Update: Changes required to install and boot ezplatform 2.5 gpl based site | 11 | +103 / −11 |  |
| 2025-08-24 | `fef26dacf` | Aktualisiert | Updated: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix. | 1 | +3 / −2 |  |
| 2025-08-24 | `c6bf30937` | Aktualisiert | Updated: Path to default pagelayout example fix. Bugfix. | 1 | +1 / −1 |  |
| 2025-08-24 | `dbceda78b` | Aktualisiert | Updated: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix. | 1 | +2 / −2 |  |
| 2025-08-24 | `23adafb6b` | Aktualisiert | Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix. | 1 | +3 / −3 |  |
| 2025-08-24 | `ca14ee18e` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `40cf39f1d` | Aktualisiert | Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. | 5 | +35 / −20 |  |
| 2025-08-25 | `294783f9f` | Aktualisiert | Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. | 1 | +2 / −2 |  |
| 2025-08-25 | `528da80f0` | Aktualisiert | Update composer.json change package name. Forking for changes. | 1 | +3 / −3 |  |
| 2025-08-25 | `22872681b` | Aktualisiert | Update composer.json testing pulling latest changes from child package. | 1 | +1 / −1 |  |
| 2025-08-25 | `559dddf68` | Aktualisiert | Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing. | 1 | +3 / −1 |  |
| 2025-08-25 | `7a6abdab9` | Aktualisiert | Update composer.json replaced Exponential package name with exponential. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `9b35c2589` | Aktualisiert | Update composer.json testing with release version instead of dev-main. Testing. | 1 | +1 / −1 | v2.5.0.0 |
| 2025-08-25 | `544d935ff` | Aktualisiert | Update composer.json minor change to support replacement installer. testing | 1 | +1 / −0 |  |
| 2025-08-25 | `ae60784f3` | Aktualisiert | Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `5e5af1cda` | Aktualisiert | Updated: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping. | 9 | +788 / −75 |  |
| 2025-08-25 | `d28397851` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `0627f499c` | Aktualisiert | Updated: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation. | 1 | +43 / −47 |  |
| 2025-08-25 | `6892bf66d` | Sonstiges | Create FUNDING.yml | 1 | +3 / −0 |  |
| 2025-08-25 | `370b0ed19` | Aktualisiert | Updated: Updated README.md documentation for package. Removed Logo Images. Documentation. | 1 | +0 / −3 |  |
| 2025-08-25 | `96c2f6c6e` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `b3f0465c9` | Aktualisiert | Updated: Updated installation documentation for package. Clarifictaions only. Documentation. | 1 | +11 / −1 |  |
| 2025-08-25 | `531199a19` | Aktualisiert | Updated: Updated installation documentation for package. Clarifictaions only. Documentation. | 1 | +4 / −0 |  |
| 2025-08-29 | `a17b4067b` | Hinzugefügt | Added: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement. | 1 | +55 / −0 |  |
| 2025-08-29 | `987808a45` | Hinzugefügt | Added: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement. | 1 | +55 / −0 |  |
| 2025-08-29 | `d143c44a9` | Hinzugefügt | Added: Added software example robots.txt configuration to web dir. Feature improvement. | 1 | +2 / −0 |  |

## 2025-09 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-14 | `8344b2b44` | Aktualisiert | Updated: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding | 1 | +1 / −1 |  |
| 2025-09-14 | `f71c97e40` | Aktualisiert | Updated: Required changes to boot admin after rebranding and further test results. Bugfix. | 1 | +1 / −1 |  |
| 2025-09-14 | `eb660eb1a` | Aktualisiert | Updated: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. | 1 | +2 / −2 | v2.5.0.1 origin/2.5.0.1 |

## 2026-01 (13 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-01-27 | `90a3d1b92` | Aktualisiert | Updated: Our first update to the initial composer.json requirements. First commit! | 1 | +64 / −14 |  |
| 2026-01-27 | `5c67ccbca` | Aktualisiert | Updated: Clean up indentation of the initial composer.json file internals. CS | 1 | +4 / −4 |  |
| 2026-01-27 | `06ea3cc10` | Aktualisiert | Updated: Clean up indentation of the initial composer.json file internals. CS | 1 | +4 / −4 |  |
| 2026-01-28 | `03c2e5ecd` | Aktualisiert | Updated: Changes required to successfully install composer package. Tested working. | 1 | +8 / −8 |  |
| 2026-01-28 | `b38241f16` | Aktualisiert | Updated: Updated composer.json rules to add cjw-network/cjw-config-processor package. New feature. | 1 | +1 / −0 |  |
| 2026-01-28 | `f4d2c8036` | Aktualisiert | Updated: Added app/config/routing.yml rules to support tagsbundle usage by default. Enhancement. | 1 | +18 / −0 |  |
| 2026-01-28 | `73a455ba3` | Aktualisiert | Updated: Added app/AppKernel rules to support tagsbundle usage by default. Enhancement. | 1 | +3 / −0 | v1.0.0.0.1 |
| 2026-01-29 | `4fe3d6c6a` | Aktualisiert | Updated: Updated README.md documumentation. Doc | 1 | +18 / −18 |  |
| 2026-01-29 | `5fafc8ff6` | Aktualisiert | Updated: Updated composer.json to provide additional autoload support for base packages and include tags and admin-ui bundles. New Features. | 1 | +6 / −2 |  |
| 2026-01-29 | `c32465a70` | Aktualisiert | Updated: Register Netgen bundles in AppKernel.php for AdminUI and SiteBundle support | 1 | +4 / −0 |  |
| 2026-01-29 | `4edb425b3` | Aktualisiert | Updated: Replaced incenteev/composer-paramter-handler package with 7x maintained copy for php version support (backport+bugfix). Bugfix | 1 | +1 / −1 |  |
| 2026-01-30 | `d58d9a04b` | Aktualisiert | Updated: Major stability and bugfixes provided with this composer.json configuration. Bugfixes. | 1 | +12 / −10 |  |
| 2026-01-31 | `030b6c7f2` | Aktualisiert | Updated: Bugfixes for composer.json configuration defaults for next release. Including yaml configuration file changes which prevent errors with the netgen platform based integrations. Enhancements. | 7 | +28 / −13 |  |

## 2026-02 (40 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-03 | `f83cafa46` | Aktualisiert | Updated: Extended example default pagelayout to support dynamic content display by default in a more flexible template code example. Enhancement. | 1 | +132 / −2 |  |
| 2026-02-10 | `984ad373f` | Hinzugefügt | Added: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This represents the stable version for testing and further refinement. Upgrades. | 2036 | +266553 / −395 |  |
| 2026-02-10 | `1673ce672` | Aktualisiert | Change version number to 1.0.0.0.2 | 1 | +1 / −1 | v1.0.0.0.2 |
| 2026-02-15 | `7ee98fce3` | Hinzugefügt | Added: Added and updated base app files and configuration representing v1.0.0.0.3 of 7x Exponential Platform Nexus. Enhancement. | 95 | +30777 / −17640 |  |
| 2026-02-15 | `0bc586124` | Hinzugefügt | Added: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix. | 107 | +4669 / −0 |  |
| 2026-02-15 | `288e27520` | Hinzugefügt | Added: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix. | 1 | +9 / −0 |  |
| 2026-02-15 | `b85007b72` | Aktualisiert | Updated: Updated Current Year in Copyright, License and Readme docs. Enhancement. | 3 | +9 / −9 |  |
| 2026-02-15 | `3a672dad6` | Aktualisiert | Updated: Updated README.md documentation to provide expanded software features provided (via bundles) and about them in general. | 1 | +399 / −0 |  |
| 2026-02-15 | `7ade2014c` | Aktualisiert | Updated: Updated README.md to change version number to 1.0.0.0.3 | 1 | +1 / −1 |  |
| 2026-02-15 | `c7368151f` | Aktualisiert | Updated: Updated README.md to update product name in license section. Doc. | 1 | +4 / −4 |  |
| 2026-02-15 | `8a18f0060` | Hinzugefügt | Added: Added based var dir (including the) site storage dir (images in site content within the db content tree) to the base repository to ensure default installations function as designed. Bugfix. | 1314 | +450 / −0 |  |
| 2026-02-15 | `1cd0045a4` | Hinzugefügt | Added: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/Resources/database/. Default installation requirement. Database dump sql files. Enhancement. | 3 | +92640 / −0 |  |
| 2026-02-15 | `3587d8ddb` | Aktualisiert | Updated: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation. | 1 | +98 / −15 |  |
| 2026-02-15 | `a763f33d3` | Aktualisiert | Updated: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation. | 1 | +4 / −4 |  |
| 2026-02-15 | `a954715ec` | Aktualisiert | Updated: Updated Nexus Project Support Documentation in SECURITY.md. Documentation. | 1 | +6 / −7 |  |
| 2026-02-15 | `f222971cb` | Aktualisiert | Updated: Updated project's documentation. Doc. | 1 | +1 / −1 |  |
| 2026-02-15 | `a5ecf11f1` | Aktualisiert | Updated: Updated Nexus Project Support Documentation in CONTRIBUTING.md. Documentation. | 1 | +7 / −7 |  |
| 2026-02-15 | `d619ac7f7` | Aktualisiert | Updated: Updated Nexus Project Support Documentation in doc/INSTALL.md. Documentation bugfix. | 1 | +1 / −1 |  |
| 2026-02-15 | `e09174bc9` | Aktualisiert | Updated: Reseting default user 'admin' password to 'publ;ish'. Bugfix. | 2 | +2 / −2 |  |
| 2026-02-15 | `4ef0598cc` | Aktualisiert | Updated: Updated README.md documentation on the subject of the default admin user password (which is simply, 'publish'). Doc. | 1 | +4 / −0 |  |
| 2026-02-16 | `2de55c0fc` | Aktualisiert | Updated: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix. | 2 | +12 / −12 |  |
| 2026-02-17 | `cbff56972` | Aktualisiert | Updated: Updated Starter Database Content And Full Dumps SQL to strip out previous project identifier text strings from default database. Bugfix. | 2 | +729 / −695 |  |
| 2026-02-18 | `f20dfb8a9` | Entfernt | Removed: Removed junk logs from repo. Cleanup. | 5 | +0 / −3746 |  |
| 2026-02-18 | `b6760a3b4` | Hinzugefügt | Added: Moving build conflicted files into separate storage locations for later installation. Cleanup. Bugfixes. | 1470 | +1167 / −0 |  |
| 2026-02-18 | `ba3d0513a` | Aktualisiert | Updated: Added lexik translation tables to sql starter dump files to fix adminui translation dashboard. Bugfix. | 2 | +127 / −1 |  |
| 2026-02-18 | `8d98173e4` | Aktualisiert | Updated: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc. | 1 | +58 / −1 |  |
| 2026-02-18 | `c9869ce9f` | Aktualisiert | Updated: Fixed Broken URLs. Bugfix. Doc. | 1 | +7 / −7 |  |
| 2026-02-19 | `7263ea28a` | Aktualisiert | Updated: Updated documentation related to installation process required covering symlink installation related to default required app bundle resources for public and Exponential required files/symlinks. Installation documenation. Enhancements. | 2 | +16 / −4 |  |
| 2026-02-19 | `cf3ab0f3e` | Aktualisiert | Updated: Updated documentation related to installation process required currently requiring apcu caching features support for uncached page rendering time reduction. Requirements for Installation documenation. Enhancements. | 1 | +4 / −2 |  |
| 2026-02-20 | `7e40082d5` | Hinzugefügt | Added: Added default symlinks to bundles required by default installation in web/bundles/ dir. Bugfix. | 20 | +20 / −0 |  |
| 2026-02-20 | `77385bf47` | Aktualisiert | Updated: Cleanup of default configuration legacy settings. Cleanup. Bugfixes. | 1 | +10 / −10 |  |
| 2026-02-20 | `234b14091` | Aktualisiert | Updated: Updated composer.json package requirements to se7enxweb/admin-ui-bundle@2.9.15. Required package version bump. | 1 | +1 / −1 |  |
| 2026-02-20 | `f14abd123` | Sonstiges | Revise INSTALL.md for improved clarity | 1 | +282 / −73 |  |
| 2026-02-20 | `03f0aa542` | Aktualisiert | Updated: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication. Cleanup. Bugfixes to default configuration. Bugfixes. | 3 | +4 / −29 |  |
| 2026-02-25 | `e9b0db995` | Aktualisiert | Updated: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes. | 5 | +43 / −22 |  |
| 2026-02-26 | `f17e5567a` | Aktualisiert | Updated: Updated assets build dir path. Bugfix. | 1 | +4 / −4 |  |
| 2026-02-26 | `e6a727340` | Aktualisiert | Updated: Updated security.yml settings defaults to use csrf features. Bugfix. | 1 | +3 / −5 |  |
| 2026-02-26 | `62fdfb56b` | Aktualisiert | Updated: In legacy.yml settings replaced sitedesign value 'admin' with 'admin3' for long term support and features. Enhancement. | 1 | +1 / −1 |  |
| 2026-02-26 | `c735c0e8b` | Aktualisiert | Updated: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Database content normalization. Enhancement. | 2 | +16 / −16 | v1.0.0.0.3 origin/1.0.0.3 origin/1.0.0.0.3 |
| 2026-02-27 | `c0ad4e66f` | Aktualisiert | Updated: Added 8.0 PHP require composer support. Enhancement for long term support. | 1 | +1 / −1 |  |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `7b1116137` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-04 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-21 | `2272f3976` | Aktualisiert | fix: add missing ngsite.default.locations.tree_root.id parameter | 2 | +11 / −0 | 1.0.0.4 |
| 2026-04-21 | `64700fbbf` | Aktualisiert | fix: wire SQLite database_path parameter into Doctrine DBAL connection | 2 | +5 / −0 | 1.0.0.5 |
| 2026-04-21 | `8bfb5d23a` | Hinzugefügt | feat: add exponential-cjw installer type with full SQLite seed data | 7 | +43830 / −2 | 1.0.0.6 |

## 2026-07 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-03 | `29a12e6d5` | Aktualisiert | Updated: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations | 1 | +8 / −8 |  |
| 2026-07-03 | `fb7280519` | Aktualisiert | Fixed: UTF-8 encoding corruption in MySQL installer SQL files | 2 | +16 / −16 |  |

## 2026-09 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-01 | `f8d9fcb85` | Aktualisiert | Updated: content_tree_module defaults in admin UI config | 1 | +5 / −0 |  |
| 2026-09-01 | `7117b3fff` | Hinzugefügt | Added: safe fallback in public/index_rest.php | 1 | +20 / −0 |  |
