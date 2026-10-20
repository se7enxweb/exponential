# exponential-platform-legacy: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-legacy`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 46 Änderungen von 2025-07-01 bis 2026-07-08, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Distribution Exponential Platform Legacy 2.5 (Plattform plus Legacy).

## Wie es mit Exponential zusammenhängt

Linie mit Langzeitunterstützung, die den Legacy-Kernel neben dem Symfony-Stack mitführt; SQLite und Oracle sind dokumentiert.

## Was Anwender davon haben

Composer-Projekt für die LTS-Linie 2.5 auf PHP 8.1 und 8.2.

Dieses Repository ist ein Composer-Projekt (Typ `project`), keine Bibliothek; Sie legen also eine neue Website daraus an:

```bash
composer create-project se7enxweb/exponential-platform-legacy:2.5.0.x-dev exponential_website
cd exponential_website
```

Den anschließenden Datenbankschritt beschreibt die README des Repositorys (`.env.local`, dann `php bin/console exponential:install` oder der dokumentierte Import).

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 7 |
| Fehlerbehebungen | 6 |
| Dokumentation | 7 |
| Werkzeuge | 16 |
| Releases | 4 |
| Ohne Nutzen für Anwender | 6 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-08-25 | v2.5.0.0 | `9b35c25` | Update composer.json testing with release version instead of dev-main. Testing. |
| 2025-09-14 | v2.5.0.1 | `eb660eb` | Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. |
| 2026-04-10 | v2.5.0.2 | `2194e96` | README.md and doc/INSTALL.md to document full SQLite and Oracle database support |
| 2026-04-21 | v2.5.0.3 | `b417ef6` | add project app SCSS/webpack build, brand image, and asset pipeline |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (16)

- 2025-07-01 `1e0297e` Werkzeuge: Update composer.json replaced package vendor name
- 2025-07-01 `f5538cb` Werkzeuge: Update composer.json increased package php support
- 2025-07-01 `6eedef3` Werkzeuge: Update composer.json switch package dep vendor for Exponential legacy and version
- 2025-07-01 `5d53cb6` Werkzeuge: Update composer.json change from gplv2 only to gplv2 or later
- 2025-07-01 `c01377f` Werkzeuge: Update composer.json homepage url vendor name change
- 2025-07-01 `0beb934` Werkzeuge: Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time.
- 2025-08-24 `333d353` Werkzeuge: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages success
- 2025-08-24 `fef26da` Werkzeuge: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix.
- 2025-08-24 `dbceda7` Werkzeuge: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix.
- 2025-08-24 `23adafb` Werkzeuge: Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix.
- 2025-08-25 `528da80` Werkzeuge: Update composer.json change package name. Forking for changes.
- 2025-08-25 `2287268` Werkzeuge: Update composer.json testing pulling latest changes from child package.
- 2025-08-25 `559dddf` Werkzeuge: Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing.
- 2025-08-25 `7a6abda` Werkzeuge: Update composer.json replaced Exponential package name with exponential. Testing.
- 2025-08-25 `9b35c25` Release: Update composer.json testing with release version instead of dev-main. Testing.
- 2025-08-25 `544d935` Werkzeuge: Update composer.json minor change to support replacement installer. testing

### Dokumentation (7)

- 2025-08-25 `5e5af1c` Dokumentation: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping.
- 2025-08-25 `0627f49` Dokumentation: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation.
- 2025-08-25 `370b0ed` Dokumentation: Updated README.md documentation for package. Removed Logo Images. Documentation.
- 2025-08-25 `b3f0465` Dokumentation: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-25 `531199a` Dokumentation: Updated installation documentation for package. Clarifictaions only. Documentation.
- 2025-08-29 `987808a` Dokumentation: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement.
- 2026-04-09 `8e94fb2` Dokumentation: rewrite README and INSTALL.md for Exponential Platform Legacy 2.5.0.x

### Fehlerbehebungen (4)

- 2025-08-24 `c6bf309` Fehlerbehebung: Path to default pagelayout example fix. Bugfix.
- 2025-08-25 `40cf39f` Fehlerbehebung: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-08-25 `294783f` Fehlerbehebung: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes.
- 2025-09-14 `eb660eb` Fehlerbehebung: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix.

### Versionsnummern (3)

- 2025-08-25 `ae60784` Release: Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing.
- 2025-09-14 `8344b2b` Release: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding
- 2026-04-09 `31d165d` Release: Version Bump for exponential package requirements to latest release in composer.json. Upgrade,

### Kompatibilität mit Symfony und der Plattform (2)

- 2025-08-24 `61e235b` Fehlerbehebung: Changes required to install and boot ezplatform 2.5 gpl based site
- 2026-04-09 `c168757` Fehlerbehebung: set PHPUnit to ^10.5 (max compatible with Symfony 3.4 stack)

### Fertige Webserver-Konfiguration (2)

- 2025-08-29 `a17b406` Funktion: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement.
- 2025-08-29 `d143c44` Funktion: Added software example robots.txt configuration to web dir. Feature improvement.

### Unterstützung für SQLite (2)

- 2026-04-09 `7021d7e` Funktion: add SQLite installer support for local dev and testing
- 2026-04-10 `2194e96` Funktion: README.md and doc/INSTALL.md to document full SQLite and Oracle database support

### Marke Exponential (1)

- 2025-09-14 `f71c97e` Funktion: Required changes to boot admin after rebranding and further test results. Bugfix.

### Test-Werkzeuge (1)

- 2026-04-09 `b231844` Werkzeuge: upgrade PHPUnit to ^11.5 and modernize phpunit.xml.dist

### Design und Templates (1)

- 2026-04-10 `9e922cc` Funktion: Six new theme directories ported from 3.x to provide the full design chain required for the site siteaccess to render correctly with notices, develope

### Funktionen (1)

- 2026-04-21 `b417ef6` Funktion: add project app SCSS/webpack build, brand image, and asset pipeline

### Pflege des Repositorys (1)

- 2026-07-08 `8479bb7` ohne Nutzen für Anwender: Added .gitattributes file to update GitHub listed language usage to match other repositories in account. Repo Maintinence

Außerdem: 5 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md)
- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Release-Changelog](../../../changelogs/extensions/exponential-platform-legacy.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-legacy.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md), [2026-07](monate/2026-07.md)
