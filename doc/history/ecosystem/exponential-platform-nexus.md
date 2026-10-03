# exponential-platform-nexus: platform repository history

The history of `exponential-platform-nexus`, one of the platform repositories around Exponential (group: Distributions and starters). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 97 changes from 2025-07-01 to 2026-09-01, all made by the se7enxweb team.

## What it is

Exponential Platform Nexus: Netgen Layouts + Media Site + tags + platform and legacy.

## How it relates to Exponential

The ready site distribution that renders the media site design; 1.x and 2.5.x lines.

## What a user gets

A working site with demo content, layouts and an admin, installed in minutes.

This repository is a Composer project (type `project`). Its README points to its own `doc/INSTALL.md` for the install steps; read that file first, then follow it. The quick start of the Nexus starter (`git clone`, `composer install`, `php bin/console exponential:install exponential-media --no-interaction`) is the closest documented sequence.

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 17 |
| Fixes | 21 |
| Behaviour and upgrade changes | 1 |
| Documentation | 21 |
| Tooling | 25 |
| Releases | 6 |
| No user benefit | 6 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-08-25 | v2.5.0.0 | `9b35c2589` | Update composer.json testing with release version instead of dev-main. Testing. |
| 2025-09-14 | v2.5.0.1 | `eb660eb1a` | Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. |
| 2026-01-28 | v1.0.0.0.1 | `73a455ba3` | Added app/AppKernel rules to support tagsbundle usage by default. Enhancement. |
| 2026-02-10 | v1.0.0.0.2 | `1673ce672` | Change version number to 1.0.0.0.2 |
| 2026-02-26 | v1.0.0.0.3 | `c735c0e8b` | Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab |
| 2026-04-21 | 1.0.0.4 | `2272f3976` | add missing ngsite.default.locations.tree_root.id parameter |
| 2026-04-21 | 1.0.0.5 | `64700fbbf` | wire SQLite database_path parameter into Doctrine DBAL connection |
| 2026-04-21 | 1.0.0.6 | `8bfb5d23a` | add exponential-cjw installer type with full SQLite seed data |

## Changes made by the se7enxweb team, by theme

### Composer requirements (25)

- 2025-07-01 `1e0297e4f` tooling: Update composer.json replaced package vendor name
- 2025-07-01 `f5538cbbf` tooling: Update composer.json increased package php support
- 2025-07-01 `6eedef300` tooling: Update composer.json switch package dep vendor for Exponential legacy and version
- 2025-07-01 `5d53cb661` tooling: Update composer.json change from gplv2 only to gplv2 or later
- 2025-07-01 `c01377f2f` tooling: Update composer.json homepage url vendor name change
- 2025-07-01 `0beb934a3` tooling: Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time.
- 2025-08-24 `333d35374` tooling: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages success
- 2025-08-24 `fef26dacf` tooling: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix.
- 2025-08-24 `dbceda78b` tooling: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix.
- 2025-08-24 `23adafb6b` tooling: Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix.
- 2025-08-25 `528da80f0` tooling: Update composer.json change package name. Forking for changes.
- 2025-08-25 `22872681b` tooling: Update composer.json testing pulling latest changes from child package.
- 2025-08-25 `559dddf68` tooling: Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing.
- 2025-08-25 `7a6abdab9` tooling: Update composer.json replaced Exponential package name with exponential. Testing.
- 2025-08-25 `9b35c2589` release: Update composer.json testing with release version instead of dev-main. Testing.
- 2025-08-25 `544d935ff` tooling: Update composer.json minor change to support replacement installer. testing
- 2026-01-27 `90a3d1b92` tooling: Our first update to the initial composer.json requirements. First commit!
- 2026-01-27 `5c67ccbca` tooling: Clean up indentation of the initial composer.json file internals. CS
- 2026-01-27 `06ea3cc10` tooling: Clean up indentation of the initial composer.json file internals. CS
- 2026-01-28 `03c2e5ecd` tooling: Changes required to successfully install composer package. Tested working.
- 2026-01-28 `b38241f16` tooling: Updated composer.json rules to add cjw-network/cjw-config-processor package. New feature.
- 2026-01-29 `5fafc8ff6` tooling: Updated composer.json to provide additional autoload support for base packages and include tags and admin-ui bundles. New Features.
- 2026-01-30 `d58d9a04b` tooling: Major stability and bugfixes provided with this composer.json configuration. Bugfixes.
- 2026-01-31 `030b6c7f2` tooling: Bugfixes for composer.json configuration defaults for next release. Including yaml configuration file changes which prevent errors with the netgen pla
- 2026-02-27 `c0ad4e66f` tooling: Added 8.0 PHP require composer support. Enhancement for long term support.

### Documentation (21)

- 2025-08-25 `5e5af1cda` docs: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping.
- 2025-08-25 `0627f499c` docs: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation.
- 2025-08-25 `370b0ed19` docs: Updated README.md documentation for package. Removed Logo Images. Documentation.
- 2025-08-25 `b3f0465c9` docs: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-25 `531199a19` docs: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-29 `987808a45` docs: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement.
- 2026-01-29 `4fe3d6c6a` docs: Updated README.md documumentation. Doc
- 2026-02-15 `b85007b72` docs: Updated Current Year in Copyright, License and Readme docs. Enhancement.
- 2026-02-15 `3a672dad6` docs: Updated README.md documentation to provide expanded software features provided (via bundles) and about them in general.
- 2026-02-15 `c7368151f` docs: Updated README.md to update product name in license section. Doc.
- 2026-02-15 `3587d8ddb` docs: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation.
- 2026-02-15 `a763f33d3` docs: Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation.
- 2026-02-15 `a954715ec` docs: Updated Nexus Project Support Documentation in SECURITY.md. Documentation.
- 2026-02-15 `f222971cb` docs: Updated project's documentation. Doc.
- 2026-02-15 `a5ecf11f1` docs: Updated Nexus Project Support Documentation in CONTRIBUTING.md. Documentation.
- 2026-02-15 `d619ac7f7` docs: Updated Nexus Project Support Documentation in doc/INSTALL.md. Documentation bugfix.
- 2026-02-15 `4ef0598cc` docs: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-18 `8d98173e4` docs: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc.
- 2026-02-18 `c9869ce9f` docs: Fixed Broken URLs. Bugfix. Doc.
- 2026-02-19 `7263ea28a` docs: Updated documentation related to installation process required covering symlink installation related to default required app bundle resources for publ
- 2026-02-19 `cf3ab0f3e` docs: Updated documentation related to installation process required currently requiring apcu caching features support for uncached page rendering time redu

### Bug fixes (17)

- 2025-08-24 `c6bf30937` fix: Path to default pagelayout example fix. Bugfix.
- 2025-08-25 `40cf39f1d` fix: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-08-25 `294783f9f` fix: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-09-14 `eb660eb1a` fix: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix.
- 2026-02-15 `e09174bc9` fix: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-16 `2de55c0fc` fix: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix.
- 2026-02-17 `cbff56972` fix: Updated Starter Database Content And Full Dumps SQL to strip out previous project identifier text strings from default database. Bugfix.
- 2026-02-18 `b6760a3b4` fix: Moving build conflicted files into separate storage locations for later installation. Cleanup. Bugfixes.
- 2026-02-18 `ba3d0513a` fix: Added lexik translation tables to sql starter dump files to fix adminui translation dashboard. Bugfix.
- 2026-02-20 `7e40082d5` fix: Added default symlinks to bundles required by default installation in web/bundles/ dir. Bugfix.
- 2026-02-20 `77385bf47` fix: Cleanup of default configuration legacy settings. Cleanup. Bugfixes.
- 2026-02-20 `03f0aa542` fix: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication. 
- 2026-02-26 `f17e5567a` fix: Updated assets build dir path. Bugfix.
- 2026-02-26 `e6a727340` fix: Updated security.yml settings defaults to use csrf features. Bugfix.
- 2026-04-21 `2272f3976` fix: add missing ngsite.default.locations.tree_root.id parameter
- 2026-07-03 `29a12e6d5` fix: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations
- 2026-07-03 `fb7280519` fix: UTF-8 encoding corruption in MySQL installer SQL files

### Features (7)

- 2026-01-28 `f4d2c8036` feature: Added app/config/routing.yml rules to support tagsbundle usage by default. Enhancement.
- 2026-01-28 `73a455ba3` feature: Added app/AppKernel rules to support tagsbundle usage by default. Enhancement.
- 2026-01-29 `c32465a70` feature: Register Netgen bundles in AppKernel.php for AdminUI and SiteBundle support
- 2026-02-15 `7ee98fce3` feature: Added and updated base app files and configuration representing v1.0.0.0.3 of 7x Exponential Platform Nexus. Enhancement.
- 2026-02-15 `1cd0045a4` feature: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/R
- 2026-02-26 `c735c0e8b` feature: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab
- 2026-09-01 `7117b3fff` feature: safe fallback in public/index_rest.php

### Version numbers (5)

- 2025-08-25 `ae60784f3` release: Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing.
- 2025-09-14 `8344b2b44` release: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding
- 2026-02-10 `1673ce672` release: Change version number to 1.0.0.0.2
- 2026-02-15 `7ade2014c` release: Updated README.md to change version number to 1.0.0.0.3
- 2026-02-20 `234b14091` release: Updated composer.json package requirements to  Required package version bump.

### Design and templates (3)

- 2026-02-03 `f83cafa46` feature: Extended example default pagelayout to support dynamic content display by default in a more flexible template code example. Enhancement.
- 2026-02-15 `0bc586124` feature: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix.
- 2026-02-15 `8a18f0060` feature: Added based var dir (including the) site storage dir (images in site content within the db content tree) to the base repository to ensure default inst

### Symfony and platform compatibility (2)

- 2025-08-24 `61e235b6e` fix: Changes required to install and boot ezplatform 2.5 gpl based site
- 2026-02-10 `984ad373f` fix: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This re

### Ready-made web server configuration (2)

- 2025-08-29 `a17b4067b` feature: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement.
- 2025-08-29 `d143c44a9` feature: Added software example robots.txt configuration to web dir. Feature improvement.

### Repository housekeeping (2)

- 2026-02-15 `288e27520` no-user-benefit: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix.
- 2026-02-18 `f20dfb8a9` tooling: Removed junk logs from repo. Cleanup.

### Other changes to the fork (2)

- 2026-02-20 `f14abd123` fix: Revise INSTALL.md for improved clarity
- 2026-09-01 `f8d9fcb85` fix: content_tree_module defaults in admin UI config

### Responsive admin design (admin3) (2)

- 2026-02-25 `e9b0db995` feature: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes.
- 2026-02-26 `62fdfb56b` feature: In legacy.yml settings replaced sitedesign value 'admin' with 'admin3' for long term support and features. Enhancement.

### SQLite support (2)

- 2026-04-21 `64700fbbf` feature: wire SQLite database_path parameter into Doctrine DBAL connection
- 2026-04-21 `8bfb5d23a` feature: add exponential-cjw installer type with full SQLite seed data

### Exponential branding (1)

- 2025-09-14 `f71c97e40` feature: Required changes to boot admin after rebranding and further test results. Bugfix.

### Package renamed to the se7enxweb vendor (1)

- 2026-01-29 `4edb425b3` bc: Replaced incenteev/composer-paramter-handler package with 7x maintained copy for php version support (backport+bugfix). Bugfix

Also: 5 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Nexus](../../features/6.0/platform-nexus-starter.md)
- [Release changelog](../../changelogs/extensions/exponential-platform-nexus.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/exponential-platform-nexus.md)
- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md), [2026-07](months/2026-07.md), [2026-09](months/2026-09.md)
