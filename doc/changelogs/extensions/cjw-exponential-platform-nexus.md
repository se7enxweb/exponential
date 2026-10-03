# cjw-exponential-platform-nexus: release notes

Read this page before you install or update `cjw-exponential-platform-nexus`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/cjw-exponential-platform-nexus.md).

## 1.0.0.6 (2026-04-22)

**Added**

- Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This re (`2d7b60c8`)
- Added and updated files to fix release related bugs for this project end users. Minor Bugfixes. (`c4f82976`)
- Added public/bundles dirs with symlinks. Required for repo use. Bugfix. (`a34dbed5`)
- Added missing file to repository. Bugfix. (`bfcf2fe9`)
- Upgraded node package system configuration support from node 14 to 20. Enhancement. (`e4f262d2`)
- Added web/assets/app/build_dev/fonts files required to repository. Bugfix. (`759eb3cb`)
- Added twig tpl code to test for parameter usage before using variable content. This prevents a fatal error on default installation. Bugfix. (`9dd7cc53`)
- Added twig path to src/AppBundle/Resources/views/nglayouts/themes/app custom template overrides. Seemingly required within default install to load cor (`77fa6986`)
- Added src/AppBundle/Resources/views/nglayouts/app/block directory of theme templates (inspired by defaults in the existing design) to present the requ (`4b4a0279`)
- Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix. (`f9e737e7`)
- Added .gitattributes conf file to repo in second attempt to remove css/etc from repo language identifier. Bugfix. (`4606b706`)
- Refreshed defaults for webpack support after upgrading and rebuilding using node version 20 support recently added. (`ac898990`)
- Added missing stub support for photoswipe-init.js, required in default theme templates. Bugfix. (`f690cba4`)
- Added vendor directory for current stable version cjw exponential platform nexus for easier git based distribution. Composer users should not be affec (`a0da799d`)
- Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix. (`05d18701`)
- Added based site storage (images in site content within the db content tree) to the base repository to ensure default installations function as design (`a06f561f`)
- Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/R (`df30769e`)
- Added lexi translation tables from medias-site sql sources into starter schema and full dump to fix adminui administration table not found errors in b (`9fd063db`)
- Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink. (`81e185a3`)
- Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink. (`7a9e35cd`)
- Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink. (`b4dacefd`)
- Added src/AppBundle/Resources/public/ to app bundle public files like img, js, css etc be installed via symlink or script symlink. (`6b34db0f`)
- Added symlink for the AppBundle into web/bundles. Distribution Bugfix. (`9ff28682`)
- Added symlink(s) for the default bundles into web/bundles. Distribution Bugfix. (`b4351dff`)
- Added ico version of png favicon for general specific file requests to not 404. Enhancement. (`b8b74617`)
- Added missing symlink to ezpublish_legacy/var directory in web/var position on fs. Bugfix. (`63b497cc`)
- Added missing required (to boot) CompilerPass class AdminUIUrlAliasRouterPass.php (`17805107`)
- Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab (`3518f7bd`)
- Added ezpublish_legacy/extension/app/design/admin3 template override to customize template logic to support eztags tab admin ui features. Enhancement. (`8cea4339`)
- Updated app/config/security.yml settings to use csrf support by default convention. Enhancment. (`df6a591b`)
- Added RewriteRule to block access to _fragment requests for enhanced security (optional). Enhancement. (`ea389795`)
- Added 8.0 PHP require composer support. Enhancement for long term support. (`d2d33aa2`)
- Drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config (`985f7278`)

**Updated**

- Initial commit (`a6fd980c`)
- Update README for Exponential Platform Nexus branding (`ff66094e`)
- Update package name and description in composer.json (`c45ee231`)
- Updated config/packages/twig.yaml to enable twig template cache. Enhancement. (`4b30b03d`)
- Bugfixes related to default composer update command fatal errors from older composer.json configuration commands not upgraded completely in this repo  (`b34eb722`)
- Updated composer.json requirements for site-bundle to v1.7.4 to fetch required bugfixes to default installation. Bugfixes. (`7a3d1560`)
- This change makes the main menu display like the de siteaccess settings configuration. This is a tested bugfix. Bugfix. (`a0c52739`)
- Mass improvements to cjw nexus repository's stable build. These changes introduce bugfixes, cleanup and in general provide examples for usable perform (`9b341f60`)
- Bugfix for default composer command script call to security check. Bugfix. (`ea6afc77`)
- Bugfix for default composer command scripts calls. Removed Double @@ symbols. Also increased default version supported for kernel and admin-ui-bundle. (`f77c0cd0`)
- Updated project's documentation. Doc. (`34be37a7`)
- Change to account credential handling (wording omitted; see the ledger line) (`bf2c9bca`)
- Change to account credential handling (wording omitted; see the ledger line) (`871a67a4`)
- Updated sql dumps to further strip out project identifiers and strings not required. Bugfix. (`4637fec2`)
- Updated composer.json conf to latest releases for admin-ui-bundle and tagsbundle. This brings tags bundle support to ng admin ui. Enhancement. (`e92157ff`)
- Updated vendor working copy in git repository to latest composer.json state. Syncing vendor dir for tags bundle support to ng adminui administration u (`c413242f`)
- Changes related to rebuild of state of repo design assets and package files. Enhancement. (`755895b2`)
- Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc. (`95f821eb`)
- Fixed Broken URLs. Bugfix. Doc. (`bd649ab8`)
- Updated documentation in general and expanded installation instructions. Doc. (`609bc1f8`)
- Cleanup of default configuration legacy settings. Cleanup. Bugfixes. (`3ac1bdcb`)
- Updated repo vendor package se7enxweb/admin-ui-bundle to version 2.9.15. Bugfixes. (`8a2e711e`)
- Updated doc/INSTALL.md greatly. Doc. (`4220c9a0`)
- Updated vendor admin-ui-bundle to include latest improvments from 2.9.15 (reinstalled) (`8ec8dedf`)
- Updated vendor admin-ui-bundle to include latest improvments surrounding login referer redirection support and enhanced siteaccess in url support from (`9ec43267`)
- Bugfixes to ignore matches to user siteaccesses and only process ngadmin_group siteaccesses. Bugfix. (`ba7ec957`)
- Bugfix for non-admin siteaccess login redirection calculation. Bugfix. (`7045fa28`)
- Bugfix for admin siteaccess login redirection calculation. Bugfix. (`641a47ad`)
- Another more specific Bugfix for admin siteaccess login redirection calculation. Bugfix. (`7c334a90`)
- A specific Bugfix for legacy_admin siteaccess login redirection calculation. Bugfix. (`7dd80e32`)
- Bugfixes for legacy_admin usage of default installation under legacy_mode: true symfony context. This fix displays the legacy admin view's sidebar chi (`ee3fc3a6`)
- Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes. (`9e867e1b`)
- Bugfix for legacy siteaccess usage. Bugfix (`4c861d88`)
- Updated app/config/ezplatform_siteaccess.yml to specifiy Map\URI match settings for legacy_admin usage in app configuration. Also specified the design (`1b12d763`)
- Updated bundle/EventListener/SecurityListener.php class to instead use existing session instead of breaking session via legacy code calls. Bugfix. (`f9c4edb5`)
- Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. (`c900c107`)
- Updated vendor installed.json/php files. (`0ab4ad4d`)
- Updated vendor installed.json/php files. (`93a47fc8`)
- Injected Yaml/INI Settings for SiteDesign for legacy_admin siteaccess to use admin3 instead of admin2 design setting for long term support. Enhancemen (`463ac336`)
- Updated legacy template override for page_copyright.tpl within site-legacy-bundle to rebrand to 7x Exponential. Updated copyright year range to dynami (`77024fe6`)
- Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhan (`824e998c`)
- Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhan (`d2868983`)

**Removed**

- Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix. (`560f89b9`)
- Cleanup of the stored files in this bundle's ezpublish_legacy install files. Cleanup. (`af62dba2`)
- Removed junk var copy dir. Cleanup. Bugfix. (`354c588a`)
- Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication.  (`48fae345`)

## After the last tag

**Updated**

- Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations (`38f0be0c`)
- UTF-8 encoding corruption in MySQL installer SQL files (`13e1b28e`)
- ngadminui tree menu, language config, cjw-exponential-media installer (`24b43594`)

## Related pages

- [Nexus](../../features/6.0/platform-nexus-starter.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
