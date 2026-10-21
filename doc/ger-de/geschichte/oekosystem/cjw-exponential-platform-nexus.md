# cjw-exponential-platform-nexus: Geschichte des Plattform-Repositorys

Die Geschichte von `cjw-exponential-platform-nexus`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 89 Änderungen von 2026-02-07 bis 2026-07-03, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Die CJW-Variante von Nexus (se7enxweb/cjw-exponential-platform-nexus).

## Wie es mit Exponential zusammenhängt

Dieselbe Codebasis wie Nexus; ergänzt den Installer-Typ exponential-cjw mit SQLite-Startdaten (April 2026).

## Was Anwender davon haben

Nexus mit den Startinhalten von CJW.

Dieses Repository ist ein Composer-Projekt (Typ `project`), die CJW-Variante von Nexus. Installieren Sie es als Projekt (Klon oder `composer create-project`), nicht mit `composer require`; die genauen Schritte stehen in seiner README.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 27 |
| Fehlerbehebungen | 26 |
| Dokumentation | 12 |
| Werkzeuge | 15 |
| Ohne Nutzen für Anwender | 9 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-22 | 1.0.0.6 | `985f7278` | drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config |

## Änderungen des se7enxweb-Teams, nach Thema

### Fehlerbehebungen (24)

- 2026-02-10 `c4f82976` Fehlerbehebung: Added and updated files to fix release related bugs for this project end users. Minor Bugfixes.
- 2026-02-10 `a34dbed5` Fehlerbehebung: Added public/bundles dirs with symlinks. Required for repo use. Bugfix.
- 2026-02-10 `bfcf2fe9` Fehlerbehebung: Added missing file to repository. Bugfix.
- 2026-02-12 `759eb3cb` Fehlerbehebung: Added web/assets/app/build_dev/fonts files required to repository. Bugfix.
- 2026-02-15 `bf2c9bca` Fehlerbehebung: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-16 `560f89b9` Fehlerbehebung: Removed project keys from database content (sql dumps) replaced with generic keys instead that are no conflict and fake emails. Bugfix.
- 2026-02-17 `4637fec2` Fehlerbehebung: Updated sql dumps to further strip out project identifiers and strings not required. Bugfix.
- 2026-02-18 `9fd063db` Fehlerbehebung: Added lexi translation tables from medias-site sql sources into starter schema and full dump to fix adminui administration table not found errors in b
- 2026-02-19 `9ff28682` Fehlerbehebung: Added symlink for the AppBundle into web/bundles. Distribution Bugfix.
- 2026-02-19 `b4351dff` Fehlerbehebung: Added symlink(s) for the default bundles into web/bundles. Distribution Bugfix.
- 2026-02-20 `63b497cc` Fehlerbehebung: Added missing symlink to ezpublish_legacy/var directory in web/var position on fs. Bugfix.
- 2026-02-20 `48fae345` Fehlerbehebung: Removed undesired yml configuration statements. Also refactored authentication for whole web app to be able to re-intrudce csrf token authentication. 
- 2026-02-20 `3ac1bdcb` Fehlerbehebung: Cleanup of default configuration legacy settings. Cleanup. Bugfixes.
- 2026-02-20 `8a2e711e` Fehlerbehebung: Updated repo vendor package se7enxweb/admin-ui-bundle to version 2.9.15. Bugfixes.
- 2026-02-25 `ba7ec957` Fehlerbehebung: Bugfixes to ignore matches to user siteaccesses and only process ngadmin_group siteaccesses. Bugfix.
- 2026-02-25 `7045fa28` Fehlerbehebung: Bugfix for non-admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `641a47ad` Fehlerbehebung: Bugfix for admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `7c334a90` Fehlerbehebung: Another more specific Bugfix for admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `7dd80e32` Fehlerbehebung: A specific Bugfix for legacy_admin siteaccess login redirection calculation. Bugfix.
- 2026-02-25 `4c861d88` Fehlerbehebung: Bugfix for legacy siteaccess usage. Bugfix
- 2026-02-25 `f9c4edb5` Fehlerbehebung: Updated bundle/EventListener/SecurityListener.php class to instead use existing session instead of breaking session via legacy code calls. Bugfix.
- 2026-07-03 `38f0be0c` Fehlerbehebung: Fixed rich text HTML and UTF-8 encoding corruption in Netgen Layouts block translations
- 2026-07-03 `13e1b28e` Fehlerbehebung: UTF-8 encoding corruption in MySQL installer SQL files
- 2026-07-03 `24b43594` Fehlerbehebung: ngadminui tree menu, language config, cjw-exponential-media installer

### Dokumentation (12)

- 2026-02-12 `9dd7cc53` Dokumentation: Added twig tpl code to test for parameter usage before using variable content. This prevents a fatal error on default installation. Bugfix.
- 2026-02-12 `77fa6986` Dokumentation: Added twig path to src/AppBundle/Resources/views/nglayouts/themes/app custom template overrides. Seemingly required within default install to load cor
- 2026-02-15 `34be37a7` Dokumentation: Updated project's documentation. Doc.
- 2026-02-15 `871a67a4` Dokumentation: change to account credential handling (wording omitted; see the ledger line)
- 2026-02-18 `81e185a3` Dokumentation: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `7a9e35cd` Dokumentation: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `b4dacefd` Dokumentation: Added src/AppBundle/ezpublish_legacy to store legacy files var and settings to be installed via symlink or script symlink.
- 2026-02-18 `6b34db0f` Dokumentation: Added src/AppBundle/Resources/public/ to app bundle public files like img, js, css etc be installed via symlink or script symlink.
- 2026-02-18 `95f821eb` Dokumentation: Expanding install documentation to cover nodejs, yarn and symlink installation topics and required steps. Doc.
- 2026-02-18 `bd649ab8` Dokumentation: Fixed Broken URLs. Bugfix. Doc.
- 2026-02-20 `609bc1f8` Dokumentation: Updated documentation in general and expanded installation instructions. Doc.
- 2026-02-20 `4220c9a0` Dokumentation: Updated doc/INSTALL.md greatly. Doc.

### Composer-Abhängigkeiten (8)

- 2026-02-10 `c45ee231` Werkzeuge: Update package name and description in composer.json
- 2026-02-12 `b34eb722` Werkzeuge: Bugfixes related to default composer update command fatal errors from older composer.json configuration commands not upgraded completely in this repo 
- 2026-02-12 `7a3d1560` Werkzeuge: Updated composer.json requirements for site-bundle to v1.7.4 to fetch required bugfixes to default installation. Bugfixes.
- 2026-02-14 `ea6afc77` Werkzeuge: Bugfix for default composer command script call to security check. Bugfix.
- 2026-02-14 `f77c0cd0` Werkzeuge: Bugfix for default composer command scripts calls. Removed Double @@ symbols. Also increased default version supported for kernel and admin-ui-bundle.
- 2026-02-15 `a0da799d` Werkzeuge: Added vendor directory for current stable version cjw exponential platform nexus for easier git based distribution. Composer users should not be affec
- 2026-02-18 `e92157ff` Werkzeuge: Updated composer.json conf to latest releases for admin-ui-bundle and tagsbundle. This brings tags bundle support to ng admin ui. Enhancement.
- 2026-02-27 `d2d33aa2` Werkzeuge: Added 8.0 PHP require composer support. Enhancement for long term support.

### Design und Templates (7)

- 2026-02-12 `4b30b03d` Funktion: Updated config/packages/twig.yaml to enable twig template cache. Enhancement.
- 2026-02-12 `4b4a0279` Funktion: Added src/AppBundle/Resources/views/nglayouts/app/block directory of theme templates (inspired by defaults in the existing design) to present the requ
- 2026-02-14 `f690cba4` Funktion: Added missing stub support for photoswipe-init.js, required in default theme templates. Bugfix.
- 2026-02-15 `05d18701` Funktion: Added web/bundles/app directory structure as required for main app design assets img/js/css/etc. Bugfix.
- 2026-02-15 `a06f561f` Funktion: Added based site storage (images in site content within the db content tree) to the base repository to ensure default installations function as design
- 2026-02-18 `755895b2` Funktion: Changes related to rebuild of state of repo design assets and package files. Enhancement.
- 2026-02-25 `1b12d763` Funktion: Updated app/config/ezplatform_siteaccess.yml to specifiy Map\URI match settings for legacy_admin usage in app configuration. Also specified the design

### Pflege des Repositorys (7)

- 2026-02-14 `f9e737e7` ohne Nutzen für Anwender: Added .gitattributes conf file to repo in first attempt to remove js from repo language identifier. Bugfix.
- 2026-02-14 `4606b706` ohne Nutzen für Anwender: Added .gitattributes conf file to repo in second attempt to remove css/etc from repo language identifier. Bugfix.
- 2026-02-18 `af62dba2` Werkzeuge: Cleanup of the stored files in this bundle's ezpublish_legacy install files. Cleanup.
- 2026-02-18 `c413242f` Werkzeuge: Updated vendor working copy in git repository to latest composer.json state. Syncing vendor dir for tags bundle support to ng adminui administration u
- 2026-02-20 `354c588a` Werkzeuge: Removed junk var copy dir. Cleanup. Bugfix.
- 2026-02-25 `0ab4ad4d` Werkzeuge: Updated vendor installed.json/php files.
- 2026-02-25 `93a47fc8` Werkzeuge: Updated vendor installed.json/php files.

### Responsives Admin-Design (admin3) (7)

- 2026-02-25 `ee3fc3a6` Funktion: Bugfixes for legacy_admin usage of default installation under legacy_mode: true symfony context. This fix displays the legacy admin view's sidebar chi
- 2026-02-25 `9e867e1b` Funktion: Bugfixes in legacy settings for usage of the admin3 legacy admin siteaccess / administrator. Bugfixes.
- 2026-02-25 `c900c107` Funktion: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement.
- 2026-02-25 `463ac336` Funktion: Injected Yaml/INI Settings for SiteDesign for legacy_admin siteaccess to use admin3 instead of admin2 design setting for long term support. Enhancemen
- 2026-02-26 `8cea4339` Funktion: Added ezpublish_legacy/extension/app/design/admin3 template override to customize template logic to support eztags tab admin ui features. Enhancement.
- 2026-02-26 `824e998c` Funktion: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhan
- 2026-02-26 `d2868983` Funktion: Switched to admin3 design and added admin2 as additional design and switched to eng-GB GUI Locale setting (optional) affecting legacy admin use. Enhan

### Funktionen (6)

- 2026-02-12 `e4f262d2` Funktion: Upgraded node package system configuration support from node 14 to 20. Enhancement.
- 2026-02-14 `ac898990` Funktion: Refreshed defaults for webpack support after upgrading and rebuilding using node version 20 support recently added.
- 2026-02-15 `df30769e` Funktion: Added default project source code's unique solution features depend upon a starter database structure and content sql. Now provided in src/AppBundle/R
- 2026-02-24 `17805107` Funktion: Added missing required (to boot) CompilerPass class AdminUIUrlAliasRouterPass.php
- 2026-02-26 `3518f7bd` Funktion: Updated default database sql content to change eng-GB translation of node 2 from 'eZ Platform' to 'Sites' to match german translation in ger-DE. Datab
- 2026-02-26 `df6a591b` Funktion: Updated app/config/security.yml settings to use csrf support by default convention. Enhancment.

### Marke Exponential (3)

- 2026-02-10 `ff66094e` Funktion: Update README for Exponential Platform Nexus branding
- 2026-02-20 `b8b74617` Funktion: Added ico version of png favicon for general specific file requests to not 404. Enhancement.
- 2026-02-26 `77024fe6` Funktion: Updated legacy template override for page_copyright.tpl within site-legacy-bundle to rebrand to 7x Exponential. Updated copyright year range to dynami

### Mit der Distribution gelieferte Erweiterungen (3)

- 2026-02-14 `9b341f60` Funktion: Mass improvements to cjw nexus repository's stable build. These changes introduce bugfixes, cleanup and in general provide examples for usable perform
- 2026-02-24 `9ec43267` Funktion: Updated vendor admin-ui-bundle to include latest improvments surrounding login referer redirection support and enhanced siteaccess in url support from
- 2026-02-26 `ea389795` Funktion: Added RewriteRule to block access to _fragment requests for enhanced security (optional). Enhancement.

### Test-Werkzeuge (2)

- 2026-02-14 `a0c52739` Werkzeuge: This change makes the main menu display like the de siteaccess settings configuration. This is a tested bugfix. Bugfix.
- 2026-02-24 `8ec8dedf` Werkzeuge: Updated vendor admin-ui-bundle to include latest improvments from 2.9.15 (reinstalled)

### Weitere Änderungen am Fork (1)

- 2026-02-07 `a6fd980c` Fehlerbehebung: Initial commit

### Kompatibilität mit Symfony und der Plattform (1)

- 2026-02-10 `2d7b60c8` Fehlerbehebung: Added file changes to merge first media-site 1.x latest repo files, #2 merge the first stable nga compatible version of nexus 1.0.0.2 for cjw. This re

### Unterstützung für SQLite (1)

- 2026-04-22 `985f7278` Funktion: drift exponential-platform-nexus 1.0.0.6 — exponential-cjw installer, SQLite seed data, ngadminui, SQLite DB config

Außerdem: 7 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Nexus](../../funktionen/6.0/plattform-nexus-starter.md)
- [Release-Changelog](../../../changelogs/extensions/cjw-exponential-platform-nexus.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/cjw-exponential-platform-nexus.md)
- [SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md), [2026-07](monate/2026-07.md)
