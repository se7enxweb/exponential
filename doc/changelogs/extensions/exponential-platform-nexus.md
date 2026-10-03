# exponential-platform-nexus: release notes

Read this page before you install or update `exponential-platform-nexus`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/exponential-platform-nexus.md).

## 1.0.0.6 (2026-04-21)

**Added**

- Add exponential-cjw installer type with full SQLite seed data (`8bfb5d23a`)

## 1.0.0.5 (2026-04-21)

**Added**

- Wire SQLite database_path parameter into Doctrine DBAL connection (`64700fbbf`)

## 1.0.0.4 (2026-04-21)

**Added**

- Added 8.0 PHP require composer support. Enhancement for long term support. (`c0ad4e66f`)

**Updated**

- Add missing ngsite.default.locations.tree_root.id parameter (`2272f3976`)

## v1.0.0.0.3 (2026-02-26)

**Added**

- Added and updated base app files and configuration representing v1.0.0.0.3 of 7x Exponential Platform Nexus. Enhancement. (`7ee98fce3`)
- Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix. (`0bc586124`)
- Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix. (`288e27520`)
- Added based var dir (including the) site storage dir (images in site content within the db content tree) to the base repository to ensure default inst (`8a18f0060`)
- Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/R (`1cd0045a4`)
- Moving build conflicted files into separate storage locations for later installation. Cleanup. Bugfixes. (`b6760a3b4`)
- Added lexik translation tables to sql starter dump files to fix adminui translation dashboard. Bugfix. (`ba3d0513a`)
- Added default symlinks to bundles required by default installation in web/bundles/ dir. Bugfix. (`7e40082d5`)
- Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab (`c735c0e8b`)

**Updated**

- Updated Current Year in Copyright, License and Readme docs. Enhancement. (`b85007b72`)
- Updated README.md documentation to provide expanded software features provided (via bundles) and about them in general. (`3a672dad6`)
- Updated README.md to change version number to 1.0.0.0.3 (`7ade2014c`)
- Updated README.md to update product name in license section. Doc. (`c7368151f`)
- Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation. (`3587d8ddb`)
- Updated Nexus Installation Instructions in doc/INSTALL.md. Documentation. (`a763f33d3`)
- Updated Nexus Project Support Documentation in SECURITY.md. Documentation. (`a954715ec`)
- Updated project's documentation. Doc. (`f222971cb`)
- Updated Nexus Project Support Documentation in CONTRIBUTING.md. Documentation. (`a5ecf11f1`)
- Updated Nexus Project Support Documentation in doc/INSTALL.md. Documentation bugfix. (`d619ac7f7`)
- Change to account credential handling (wording omitted; see the ledger line) (`e09174bc9`)
- Change to account credential handling (wording omitted; see the ledger line) (`4ef0598cc`)
- Updated Starter Database Content And Full Dumps SQL to strip out previous project identifier text strings from default database. Bugfix. (`cbff56972`)
- Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc. (`8d98173e4`)
- Fixed Broken URLs. Bugfix. Doc. (`c9869ce9f`)
- Updated documentation related to installation process required covering symlink installation related to default required app bundle resources for publ (`7263ea28a`)
- Updated documentation related to installation process required currently requiring apcu caching features support for uncached page rendering time redu (`cf3ab0f3e`)
- Cleanup of default configuration legacy settings. Cleanup. Bugfixes. (`77385bf47`)
- Updated composer.json package requirements to  Required package version bump. (`234b14091`)
- Revise INSTALL.md for improved clarity (`f14abd123`)
- Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes. (`e9b0db995`)
- Updated assets build dir path. Bugfix. (`f17e5567a`)
- Updated security.yml settings defaults to use csrf features. Bugfix. (`e6a727340`)
- In legacy.yml settings replaced sitedesign value 'admin' with 'admin3' for long term support and features. Enhancement. (`62fdfb56b`)

**Removed**

- Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix. (`2de55c0fc`)
- Removed junk logs from repo. Cleanup. (`f20dfb8a9`)
- Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication.  (`03f0aa542`)

## v1.0.0.0.2 (2026-02-10)

**Added**

- Register Netgen bundles in AppKernel.php for AdminUI and SiteBundle support (`c32465a70`)
- Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This re (`984ad373f`)

**Updated**

- Updated README.md documumentation. Doc (`4fe3d6c6a`)
- Updated composer.json to provide additional autoload support for base packages and include tags and admin-ui bundles. New Features. (`5fafc8ff6`)
- Major stability and bugfixes provided with this composer.json configuration. Bugfixes. (`d58d9a04b`)
- Bugfixes for composer.json configuration defaults for next release. Including yaml configuration file changes which prevent errors with the netgen pla (`030b6c7f2`)
- Extended example default pagelayout to support dynamic content display by default in a more flexible template code example. Enhancement. (`f83cafa46`)
- Change version number to 1.0.0.0.2 (`1673ce672`)

**Renamed**

- Replaced incenteev/composer-paramter-handler package with 7x maintained copy for php version support (backport+bugfix). Bugfix (`4edb425b3`)

## v1.0.0.0.1 (2026-01-28)

**Added**

- Added app/config/routing.yml rules to support tagsbundle usage by default. Enhancement. (`f4d2c8036`)
- Added app/AppKernel rules to support tagsbundle usage by default. Enhancement. (`73a455ba3`)

**Updated**

- Our first update to the initial composer.json requirements. First commit! (`90a3d1b92`)
- Clean up indentation of the initial composer.json file internals. CS (`5c67ccbca`)
- Clean up indentation of the initial composer.json file internals. CS (`06ea3cc10`)
- Changes required to successfully install composer package. Tested working. (`03c2e5ecd`)
- Updated composer.json rules to add cjw-network/cjw-config-processor package. New feature. (`b38241f16`)

## v2.5.0.1 (2025-09-14)

**Added**

- Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement. (`a17b4067b`)
- Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement. (`987808a45`)
- Added software example robots.txt configuration to web dir. Feature improvement. (`d143c44a9`)

**Updated**

- Update composer.json minor change to support replacement installer. testing (`544d935ff`)
- Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing. (`ae60784f3`)
- Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping. (`5e5af1cda`)
- Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation. (`0627f499c`)
- Updated README.md documentation for package. Removed Logo Images. Documentation. (`370b0ed19`)
- Updated installation documentation for package. Clarifictaions only. Documentation. (`b3f0465c9`)
- Updated installation documentation for package. Clarifictaions only. Documentation. (`531199a19`)
- Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding (`8344b2b44`)
- Required changes to boot admin after rebranding and further test results. Bugfix. (`f71c97e40`)
- Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. (`eb660eb1a`)

## v2.5.0.0 (2025-08-25)

**Updated**

- Update composer.json replaced package vendor name (`1e0297e4f`)
- Update composer.json increased package php support (`f5538cbbf`)
- Update composer.json switch package dep vendor for Exponential legacy and version (`6eedef300`)
- Update composer.json change from gplv2 only to gplv2 or later (`5d53cb661`)
- Update composer.json homepage url vendor name change (`c01377f2f`)
- Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time. (`0beb934a3`)
- Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages success (`333d35374`)
- Changes required to install and boot ezplatform 2.5 gpl based site (`61e235b6e`)
- Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix. (`fef26dacf`)
- Path to default pagelayout example fix. Bugfix. (`c6bf30937`)
- Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix. (`dbceda78b`)
- Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix. (`23adafb6b`)
- Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. (`40cf39f1d`)
- Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. (`294783f9f`)
- Update composer.json change package name. Forking for changes. (`528da80f0`)
- Update composer.json testing pulling latest changes from child package. (`22872681b`)
- Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing. (`559dddf68`)
- Update composer.json replaced Exponential package name with exponential. Testing. (`7a6abdab9`)
- Update composer.json testing with release version instead of dev-main. Testing. (`9b35c2589`)

## After the last tag

**Added**

- Safe fallback in public/index_rest.php (`7117b3fff`)

**Updated**

- Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations (`29a12e6d5`)
- UTF-8 encoding corruption in MySQL installer SQL files (`fb7280519`)
- content_tree_module defaults in admin UI config (`f8d9fcb85`)

## Related pages

- [Nexus](../../features/6.0/platform-nexus-starter.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
