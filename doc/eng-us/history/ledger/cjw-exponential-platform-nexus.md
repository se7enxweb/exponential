# Change ledger: cjw-exponential-platform-nexus

Every change made to `cjw-exponential-platform-nexus` since the se7enxweb era began, oldest first: 89 changes touching 59420 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 57 |
| Added | 21 |
| Merged | 6 |
| Other | 3 |
| Removed | 2 |

## 2026-02 (84 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-02-07 | `a6fd980c` | Other | Initial commit | 153 | +7856 / −0 |  |
| 2026-02-10 | `2d7b60c8` | Added | Added: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This represents the stable version for testing and further refinement. Upgrades. | 2036 | +266569 / −411 |  |
| 2026-02-10 | `f61a1974` | Merged | Merge pull request #1 from se7enxweb/exppn-stable-build-for-v1002 | 0 | +0 / −0 |  |
| 2026-02-10 | `ff66094e` | Updated | Update README for Exponential Platform Nexus branding | 1 | +18 / −18 |  |
| 2026-02-10 | `c45ee231` | Updated | Update package name and description in composer.json | 1 | +4 / −4 |  |
| 2026-02-10 | `314a7242` | Merged | Merge pull request #2 from se7enxweb/exppn-stable-build-for-v1002 | 0 | +0 / −0 |  |
| 2026-02-10 | `c4f82976` | Added | Added: Added and updated files to fix release related bugs for this project end users. Minor Bugfixes. | 13 | +160 / −96 |  |
| 2026-02-10 | `533c2889` | Merged | Merge pull request #3 from se7enxweb/exppn-stable-build-for-v1002 | 0 | +0 / −0 |  |
| 2026-02-10 | `a34dbed5` | Added | Added: Added public/bundles dirs with symlinks. Required for repo use. Bugfix. | 20 | +20 / −0 |  |
| 2026-02-10 | `00f8c557` | Merged | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2026-02-10 | `bfcf2fe9` | Added | Added: Added missing file to repository. Bugfix. | 1 | +4 / −0 |  |
| 2026-02-12 | `4b30b03d` | Updated | Updated: Updated config/packages/twig.yaml to enable twig template cache. Enhancement. | 1 | +2 / −0 |  |
| 2026-02-12 | `e4f262d2` | Updated | Updated: Upgraded node package system configuration support from node 14 to 20. Enhancement. | 3 | +17893 / −9633 |  |
| 2026-02-12 | `b34eb722` | Updated | Updated: Bugfixes related to default composer update command fatal errors from older composer.json configuration commands not upgraded completely in this repo to date. Composer install/update commands for asset/yarn execution bugfixes. Tested as working. Bugfix. | 4 | +14 / −20127 |  |
| 2026-02-12 | `759eb3cb` | Added | Added: Added web/assets/app/build_dev/fonts files required to repository. Bugfix. | 3 | +9552 / −0 |  |
| 2026-02-12 | `9dd7cc53` | Updated | Updated: Added twig tpl code to test for parameter usage before using variable content. This prevents a fatal error on default installation. Bugfix. | 1 | +7 / −7 |  |
| 2026-02-12 | `77fa6986` | Updated | Updated: Added twig path to src/AppBundle/Resources/views/nglayouts/themes/app custom template overrides. Seemingly required within default install to load correct templates by application design requirements (re: order of designs/paths of included theme templates. Prevents serious default app design problem in dev/prod. Bugfix. | 1 | +2 / −1 |  |
| 2026-02-12 | `7a3d1560` | Updated | Updated: Updated composer.json requirements for site-bundle to v1.7.4 to fetch required bugfixes to default installation. Bugfixes. | 1 | +1 / −1 |  |
| 2026-02-12 | `4b4a0279` | Added | Added: Added src/AppBundle/Resources/views/nglayouts/app/block directory of theme templates (inspired by defaults in the existing design) to present the required look per application design requirements. Loads expected design elements upon requested inclusion. Required for default installation templates requested to be available for use. Bugfix. | 8 | +193 / −0 |  |
| 2026-02-14 | `a0c52739` | Updated | Updated: This change makes the main menu display like the de siteaccess settings configuration. This is a tested bugfix. Bugfix. | 1 | +1 / −1 |  |
| 2026-02-14 | `f9e737e7` | Added | Added: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix. | 1 | +4 / −0 |  |
| 2026-02-14 | `4606b706` | Added | Added: Added .gitattributes conf file to repo in second attempt to remove css/etc from repo language identifier. Bugfix. | 1 | +5 / −0 |  |
| 2026-02-14 | `9b341f60` | Updated | Updated: Mass improvements to cjw nexus repository's stable build. These changes introduce bugfixes, cleanup and in general provide examples for usable performance defaults which are being now introduced as cache yml settings for symfony to use apcu by default for caching. If you can't install apcu into a php extension you can disable these configuration settings introduced here. Enhancements. | 22 | +327 / −84 |  |
| 2026-02-14 | `ac898990` | Added | Added: Refreshed defaults for webpack support after upgrading and rebuilding using node version 20 support recently added. | 47 | +19509 / −35726 |  |
| 2026-02-14 | `f690cba4` | Added | Added: Added missing stub support for photoswipe-init.js, required in default theme templates. Bugfix. | 1 | +83 / −0 |  |
| 2026-02-14 | `ea6afc77` | Updated | Updated: Bugfix for default composer command script call to security check. Bugfix. | 1 | +1 / −1 |  |
| 2026-02-14 | `f77c0cd0` | Updated | Updated: Bugfix for default composer command scripts calls. Removed Double @@ symbols. Also increased default version supported for kernel and admin-ui-bundle. Bugfixes + Enhancements. | 1 | +4 / −4 |  |
| 2026-02-15 | `a0da799d` | Added | Added: Added vendor directory for current stable version cjw exponential platform nexus for easier git based distribution. Composer users should not be affected negatively. Enhancement. | 51485 | +6018827 / −0 |  |
| 2026-02-15 | `05d18701` | Added | Added: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix. | 107 | +4669 / −0 |  |
| 2026-02-15 | `a06f561f` | Added | Added: Added based site storage (images in site content within the db content tree) to the base repository to ensure default installations function as designed. Bugfix. | 1281 | +0 / −0 |  |
| 2026-02-15 | `df30769e` | Added | Added: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/Resources/database/. Default installation requirement. Database dump sql files. Enhancement. | 3 | +92640 / −0 |  |
| 2026-02-15 | `8d5a6981` | Merged | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2026-02-15 | `34be37a7` | Updated | Updated: Updated project's documentation. Doc. | 3 | +512 / −31 |  |
| 2026-02-15 | `bf2c9bca` | Updated | Updated: Reseting default user 'admin' password to 'publ;ish'. Bugfix. | 2 | +2 / −2 |  |
| 2026-02-15 | `871a67a4` | Updated | Updated: Updated README.md documentation on the subject of the default admin user password (which is simply, 'publish'). Doc. | 1 | +4 / −0 |  |
| 2026-02-16 | `560f89b9` | Updated | Updated: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix. | 2 | +12 / −12 |  |
| 2026-02-17 | `4637fec2` | Other | Updatd: Updated sql dumps to further strip out project identifiers and strings not required. Bugfix. | 2 | +729 / −695 |  |
| 2026-02-18 | `9fd063db` | Updated | Updated: Added lexi translation tables from medias-site sql sources into starter schema and full dump to fix adminui administration table not found errors in build. Bugfix. | 2 | +127 / −1 |  |
| 2026-02-18 | `81e185a3` | Updated | Updated: Added src/AppBundle/Exponential to store legacy files var and settings to be installed via symlink or script symlink. | 2036 | +119038 / −0 |  |
| 2026-02-18 | `7a9e35cd` | Updated | Updated: Added src/AppBundle/Exponential to store legacy files var and settings to be installed via symlink or script symlink. | 1 | +29 / −1 |  |
| 2026-02-18 | `b4dacefd` | Updated | Updated: Added src/AppBundle/Exponential to store legacy files var and settings to be installed via symlink or script symlink. | 1292 | +0 / −252 |  |
| 2026-02-18 | `6b34db0f` | Updated | Updated: Added src/AppBundle/Resources/public/ to app bundle public files like img, js, css etc be installed via symlink or script symlink. | 107 | +0 / −0 |  |
| 2026-02-18 | `af62dba2` | Removed | Removed: Cleanup of the stored files in this bundle's Exponential install files. Cleanup. | 608 | +0 / −107913 |  |
| 2026-02-18 | `e92157ff` | Updated | Updated: Updated composer.json conf to latest releases for admin-ui-bundle and tagsbundle. This brings tags bundle support to ng admin ui. Enhancement. | 1 | +3 / −3 |  |
| 2026-02-18 | `c413242f` | Updated | Updated: Updated vendor working copy in git repository to latest composer.json state. Syncing vendor dir for tags bundle support to ng adminui administration ui. Enhancement. | 43 | +1649 / −1401 |  |
| 2026-02-18 | `755895b2` | Updated | Updated: Changes related to rebuild of state of repo design assets and package files. Enhancement. | 5 | +19 / −40 |  |
| 2026-02-18 | `95f821eb` | Updated | Updated: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc. | 2 | +60 / −3 |  |
| 2026-02-18 | `bd649ab8` | Updated | Updated: Fixed Broken URLs. Bugfix. Doc. | 1 | +6 / −6 |  |
| 2026-02-19 | `9ff28682` | Added | Added: Added symlink for the AppBundle into web/bundles. Distribution Bugfix. | 1 | +1 / −0 |  |
| 2026-02-19 | `b4351dff` | Added | Added: Added symlink(s) for the default bundles into web/bundles. Distribution Bugfix. | 20 | +20 / −0 |  |
| 2026-02-20 | `b8b74617` | Added | Added: Added ico version of png favicon for general specific file requests to not 404. Enhancement. | 1 | +0 / −0 |  |
| 2026-02-20 | `354c588a` | Removed | Removed: Removed junk var copy dir. Cleanup. Bugfix. | 5 | +0 / −3746 |  |
| 2026-02-20 | `63b497cc` | Added | Added: Added missing symlink to ezpublish_legacy/var directory in web/var position on fs. Bugfix. | 1 | +1 / −0 |  |
| 2026-02-20 | `609bc1f8` | Updated | Updated: Updated documentation in general and expanded installation instructions. Doc. | 2 | +18 / −4 |  |
| 2026-02-20 | `48fae345` | Updated | Updated: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication. Cleanup. Bugfixes to default configuration. Bugfixes. | 3 | +4 / −29 |  |
| 2026-02-20 | `3ac1bdcb` | Updated | Updated: Cleanup of default configuration legacy settings. Cleanup. Bugfixes. | 1 | +10 / −14 |  |
| 2026-02-20 | `8a2e711e` | Updated | Updated: Updated repo vendor package se7enxweb/admin-ui-bundle to version 2.9.15. Bugfixes. | 10 | +18 / −616 |  |
| 2026-02-20 | `4220c9a0` | Updated | Updated: Updated doc/INSTALL.md greatly. Doc. | 1 | +282 / −73 |  |
| 2026-02-24 | `8ec8dedf` | Updated | Updated: Updated vendor admin-ui-bundle to include latest improvments from 2.9.15 (reinstalled) | 7 | +91 / −29 |  |
| 2026-02-24 | `9ec43267` | Updated | Updated: Updated vendor admin-ui-bundle to include latest improvments surrounding login referer redirection support and enhanced siteaccess in url support from 2.9.15 (reinstalled) | 5 | +153 / −23 |  |
| 2026-02-24 | `17805107` | Added | Added: Added missing required (to boot) CompilerPass class AdminUIUrlAliasRouterPass.php | 1 | +40 / −0 |  |
| 2026-02-25 | `ba7ec957` | Updated | Updated: Bugfixes to ignore matches to user siteaccesses and only process ngadmin_group siteaccesses. Bugfix. | 2 | +33 / −1 |  |
| 2026-02-25 | `20c9d2da` | Merged | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2026-02-25 | `7045fa28` | Updated | Updated: Bugfix for non-admin siteaccess login redirection calculation. Bugfix. | 1 | +12 / −10 |  |
| 2026-02-25 | `641a47ad` | Updated | Updated: Bugfix for admin siteaccess login redirection calculation. Bugfix. | 1 | +13 / −3 |  |
| 2026-02-25 | `7c334a90` | Updated | Updated: Another more specific Bugfix for admin siteaccess login redirection calculation. Bugfix. | 1 | +13 / −10 |  |
| 2026-02-25 | `7dd80e32` | Updated | Updated: A specific Bugfix for legacy_admin siteaccess login redirection calculation. Bugfix. | 1 | +8 / −0 |  |
| 2026-02-25 | `ee3fc3a6` | Updated | Updated: Bugfixes for legacy_admin usage of default installation under legacy_mode: true symfony context. This fix displays the legacy admin view's sidebar child menu items correctly with class match additions in settings. Bugfix. | 1 | +23 / −2 |  |
| 2026-02-25 | `9e867e1b` | Updated | Updated: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes. | 4 | +21 / −8 |  |
| 2026-02-25 | `4c861d88` | Updated | Updated: Bugfix for legacy siteaccess usage. Bugfix | 1 | +29 / −13 |  |
| 2026-02-25 | `1b12d763` | Updated | Updated: Updated app/config/ezplatform_siteaccess.yml to specifiy Map\URI match settings for legacy_admin usage in app configuration. Also specified the design order for siteaccess designs list setting to ensure the legacy admin siteacess design is stable and available by default configuration. Bugfixes. | 1 | +8 / −1 |  |
| 2026-02-25 | `f9c4edb5` | Updated | Updated: Updated bundle/EventListener/SecurityListener.php class to instead use existing session instead of breaking session via legacy code calls. Bugfix. | 1 | +16 / −14 |  |
| 2026-02-25 | `c900c107` | Updated | Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. | 1 | +5 / −2 |  |
| 2026-02-25 | `0ab4ad4d` | Updated | Updated: Updated vendor installed.json/php files. | 2 | +7 / −7 |  |
| 2026-02-25 | `93a47fc8` | Updated | Updated: Updated vendor installed.json/php files. | 2 | +7 / −7 |  |
| 2026-02-25 | `463ac336` | Updated | Updated: Injected Yaml/INI Settings for SiteDesign for legacy_admin siteaccess to use admin3 instead of admin2 design setting for long term support. Enhancement. | 1 | +1 / −1 |  |
| 2026-02-26 | `3518f7bd` | Updated | Updated: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Database content normalization. Enhancement. | 2 | +16 / −16 |  |
| 2026-02-26 | `8cea4339` | Added | Added: Added ezpublish_legacy/extension/app/design/admin3 template override to customize template logic to support eztags tab admin ui features. Enhancement. | 1 | +126 / −0 |  |
| 2026-02-26 | `77024fe6` | Updated | Updated: Updated legacy template override for page_copyright.tpl within site-legacy-bundle to rebrand to 7x Exponential. Updated copyright year range to dynamic code. Enhancement. | 1 | +16 / −0 |  |
| 2026-02-26 | `df6a591b` | Updated | Updated: Updated app/config/security.yml settings to use csrf support by default convention. Enhancment. | 1 | +5 / −5 |  |
| 2026-02-26 | `ea389795` | Updated | Updated: Added RewriteRule to block access to _fragment requests for enhanced security (optional). Enhancement. | 1 | +4 / −0 |  |
| 2026-02-26 | `824e998c` | Updated | Updated: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhancement. | 6 | +20 / −38 |  |
| 2026-02-26 | `d2868983` | Updated | Updated: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhancement. | 1 | +12 / −3 |  |
| 2026-02-27 | `d2d33aa2` | Updated | Updated: Added 8.0 PHP require composer support. Enhancement for long term support. | 1 | +1 / −1 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `d20a9080` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-04 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-22 | `985f7278` | Added | feat: drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config | 8 | +43838 / −2 | 1.0.0.6 |

## 2026-07 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-03 | `38f0be0c` | Updated | Updated: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations | 1 | +8 / −8 |  |
| 2026-07-03 | `13e1b28e` | Updated | Fixed: UTF-8 encoding corruption in MySQL installer SQL files | 2 | +16 / −16 |  |
| 2026-07-03 | `24b43594` | Updated | Fixed: ngadminui tree menu, language config, cjw-exponential-media installer | 10 | +89500 / −54 |  |
