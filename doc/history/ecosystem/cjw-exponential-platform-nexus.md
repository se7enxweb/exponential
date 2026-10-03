# Ecosystem repository: cjw-exponential-platform-nexus

**Group:** Distributions and starters. **Period in the ledger:** 2026-02-07 to 2026-07-03. **Changes:** 89 (89 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

CJW flavour of Nexus (se7enxweb/cjw-exponential-platform-nexus).

## How it relates to Exponential

Same code base as Nexus; adds the exponential-cjw installer type with SQLite seed data (April 2026).

## What a user gets

Nexus with the CJW starter content.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/cjw-exponential-platform-nexus
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 27 |
| Fixes | 26 |
| Documentation | 12 |
| Tooling | 15 |
| No user benefit | 9 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-22 | 1.0.0.6 | `985f7278` | drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config |

## Changes made by the se7enxweb team, by theme

### Bug fixes (24)

- 2026-02-10 `c4f82976` fix: Added: Added and updated files to fix release related bugs for this project end users. Minor Bugfixes.
- 2026-02-10 `a34dbed5` fix: Added: Added public/bundles dirs with symlinks. Required for repo use. Bugfix.
- 2026-02-10 `bfcf2fe9` fix: Added: Added missing file to repository. Bugfix.
- 2026-02-12 `759eb3cb` fix: Added: Added web/assets/app/build_dev/fonts files required to repository. Bugfix.
- 2026-02-15 `bf2c9bca` fix: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-16 `560f89b9` fix: Updated: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix.
- 2026-02-17 `4637fec2` fix: Updatd: Updated sql dumps to further strip out project identifiers and strings not required. Bugfix.
- 2026-02-18 `9fd063db` fix: Updated: Added lexi translation tables from medias-site sql sources into starter schema and full dump to fix adminui administration table not found er
- 2026-02-19 `9ff28682` fix: Added: Added symlink for the AppBundle into web/bundles. Distribution Bugfix.
- 2026-02-19 `b4351dff` fix: Added: Added symlink(s) for the default bundles into web/bundles. Distribution Bugfix.
- 2026-02-20 `63b497cc` fix: Added: Added missing symlink to ezpublish_legacy/var directory in web/var position on fs. Bugfix.
- 2026-02-20 `48fae345` fix: Updated: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authent
- 2026-02-20 `3ac1bdcb` fix: Updated: Cleanup of default configuration legacy settings. Cleanup. Bugfixes.
- 2026-02-20 `8a2e711e` fix: Updated: Updated repo vendor package se7enxweb/admin-ui-bundle to version 2.9.15. Bugfixes.
- 2026-02-25 `ba7ec957` fix: Updated: Bugfixes to ignore matches to user siteaccesses and only process ngadmin_group siteaccesses. Bugfix.
- 2026-02-25 `7045fa28` fix: Updated: Bugfix for non-admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `641a47ad` fix: Updated: Bugfix for admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `7c334a90` fix: Updated: Another more specific Bugfix for admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `7dd80e32` fix: Updated: A specific Bugfix for legacy_admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `4c861d88` fix: Updated: Bugfix for legacy siteaccess usage. Bugfix
- 2026-02-25 `f9c4edb5` fix: Updated: Updated bundle/EventListener/SecurityListener.php class to instead use existing session instead of breaking session via legacy code calls. Bu
- 2026-07-03 `38f0be0c` fix: Updated: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations
- 2026-07-03 `13e1b28e` fix: Fixed: UTF-8 encoding corruption in MySQL installer SQL files
- 2026-07-03 `24b43594` fix: Fixed: ngadminui tree menu, language config, cjw-exponential-media installer

### Documentation (12)

- 2026-02-12 `9dd7cc53` docs: Updated: Added twig tpl code to test for parameter usage before using variable content. This prevents a fatal error on default installation. Bugfix.
- 2026-02-12 `77fa6986` docs: Updated: Added twig path to src/AppBundle/Resources/views/nglayouts/themes/app custom template overrides. Seemingly required within default install to
- 2026-02-15 `34be37a7` docs: Updated: Updated project's documentation. Doc.
- 2026-02-15 `871a67a4` docs: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-18 `81e185a3` docs: Updated: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `7a9e35cd` docs: Updated: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `b4dacefd` docs: Updated: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `6b34db0f` docs: Updated: Added src/AppBundle/Resources/public/ to app bundle public files like img, js, css etc be installed via symlink or script symlink.
- 2026-02-18 `95f821eb` docs: Updated: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc.
- 2026-02-18 `bd649ab8` docs: Updated: Fixed Broken URLs. Bugfix. Doc.
- 2026-02-20 `609bc1f8` docs: Updated: Updated documentation in general and expanded installation instructions. Doc.
- 2026-02-20 `4220c9a0` docs: Updated: Updated doc/INSTALL.md greatly. Doc.

### Composer requirements (8)

- 2026-02-10 `c45ee231` tooling: Update package name and description in composer.json
- 2026-02-12 `b34eb722` tooling: Updated: Bugfixes related to default composer update command fatal errors from older composer.json configuration commands not upgraded completely in t
- 2026-02-12 `7a3d1560` tooling: Updated: Updated composer.json requirements for site-bundle to v1.7.4 to fetch required bugfixes to default installation. Bugfixes.
- 2026-02-14 `ea6afc77` tooling: Updated: Bugfix for default composer command script call to security check. Bugfix.
- 2026-02-14 `f77c0cd0` tooling: Updated: Bugfix for default composer command scripts calls. Removed Double @@ symbols. Also increased default version supported for kernel and admin-u
- 2026-02-15 `a0da799d` tooling: Added: Added vendor directory for current stable version cjw exponential platform nexus for easier git based distribution. Composer users should not b
- 2026-02-18 `e92157ff` tooling: Updated: Updated composer.json conf to latest releases for admin-ui-bundle and tagsbundle. This brings tags bundle support to ng admin ui. Enhancement
- 2026-02-27 `d2d33aa2` tooling: Updated: Added 8.0 PHP require composer support. Enhancement for long term support.

### Design and templates (7)

- 2026-02-12 `4b30b03d` feature: Updated: Updated config/packages/twig.yaml to enable twig template cache. Enhancement.
- 2026-02-12 `4b4a0279` feature: Added: Added src/AppBundle/Resources/views/nglayouts/app/block directory of theme templates (inspired by defaults in the existing design) to present t
- 2026-02-14 `f690cba4` feature: Added: Added missing stub support for photoswipe-init.js, required in default theme templates. Bugfix.
- 2026-02-15 `05d18701` feature: Added: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix.
- 2026-02-15 `a06f561f` feature: Added: Added based site storage (images in site content within the db content tree) to the base repository to ensure default installations function as
- 2026-02-18 `755895b2` feature: Updated: Changes related to rebuild of state of repo design assets and package files. Enhancement.
- 2026-02-25 `1b12d763` feature: Updated: Updated app/config/ezplatform_siteaccess.yml to specifiy Map\URI match settings for legacy_admin usage in app configuration. Also specified t

### Repository housekeeping (7)

- 2026-02-14 `f9e737e7` no-user-benefit: Added: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix.
- 2026-02-14 `4606b706` no-user-benefit: Added: Added .gitattributes conf file to repo in second attempt to remove css/etc from repo language identifier. Bugfix.
- 2026-02-18 `af62dba2` tooling: Removed: Cleanup of the stored files in this bundle's ezpublish_legacy install files. Cleanup.
- 2026-02-18 `c413242f` tooling: Updated: Updated vendor working copy in git repository to latest composer.json state. Syncing vendor dir for tags bundle support to ng adminui adminis
- 2026-02-20 `354c588a` tooling: Removed: Removed junk var copy dir. Cleanup. Bugfix.
- 2026-02-25 `0ab4ad4d` tooling: Updated: Updated vendor installed.json/php files.
- 2026-02-25 `93a47fc8` tooling: Updated: Updated vendor installed.json/php files.

### Responsive admin design (admin3) (7)

- 2026-02-25 `ee3fc3a6` feature: Updated: Bugfixes for legacy_admin usage of default installation under legacy_mode: true symfony context. This fix displays the legacy admin view's si
- 2026-02-25 `9e867e1b` feature: Updated: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes.
- 2026-02-25 `c900c107` feature: Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement.
- 2026-02-25 `463ac336` feature: Updated: Injected Yaml/INI Settings for SiteDesign for legacy_admin siteaccess to use admin3 instead of admin2 design setting for long term support. E
- 2026-02-26 `8cea4339` feature: Added: Added ezpublish_legacy/extension/app/design/admin3 template override to customize template logic to support eztags tab admin ui features. Enhan
- 2026-02-26 `824e998c` feature: Updated: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin u
- 2026-02-26 `d2868983` feature: Updated: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin u

### Features (6)

- 2026-02-12 `e4f262d2` feature: Updated: Upgraded node package system configuration support from node 14 to 20. Enhancement.
- 2026-02-14 `ac898990` feature: Added: Refreshed defaults for webpack support after upgrading and rebuilding using node version 20 support recently added.
- 2026-02-15 `df30769e` feature: Added: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppB
- 2026-02-24 `17805107` feature: Added: Added missing required (to boot) CompilerPass class AdminUIUrlAliasRouterPass.php
- 2026-02-26 `3518f7bd` feature: Updated: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-
- 2026-02-26 `df6a591b` feature: Updated: Updated app/config/security.yml settings to use csrf support by default convention. Enhancment.

### Exponential branding (3)

- 2026-02-10 `ff66094e` feature: Update README for Exponential Platform Nexus branding
- 2026-02-20 `b8b74617` feature: Added: Added ico version of png favicon for general specific file requests to not 404. Enhancement.
- 2026-02-26 `77024fe6` feature: Updated: Updated legacy template override for page_copyright.tpl within site-legacy-bundle to rebrand to 7x Exponential. Updated copyright year range 

### Extensions bundled with the distribution (3)

- 2026-02-14 `9b341f60` feature: Updated: Mass improvements to cjw nexus repository's stable build. These changes introduce bugfixes, cleanup and in general provide examples for usabl
- 2026-02-24 `9ec43267` feature: Updated: Updated vendor admin-ui-bundle to include latest improvments surrounding login referer redirection support and enhanced siteaccess in url sup
- 2026-02-26 `ea389795` feature: Updated: Added RewriteRule to block access to _fragment requests for enhanced security (optional). Enhancement.

### Test tooling (2)

- 2026-02-14 `a0c52739` tooling: Updated: This change makes the main menu display like the de siteaccess settings configuration. This is a tested bugfix. Bugfix.
- 2026-02-24 `8ec8dedf` tooling: Updated: Updated vendor admin-ui-bundle to include latest improvments from 2.9.15 (reinstalled)

### Other changes to the fork (1)

- 2026-02-07 `a6fd980c` fix: Initial commit

### Symfony and platform compatibility (1)

- 2026-02-10 `2d7b60c8` fix: Added: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. 

### SQLite support (1)

- 2026-04-22 `985f7278` feature: drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config

Also: 7 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of cjw-exponential-platform-nexus](ledger/cjw-exponential-platform-nexus.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
