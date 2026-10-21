# exponential-platform-nexus-starter: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-nexus-starter`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 248 Änderungen von 2023-12-13 bis 2026-04-26: 16 vom se7enxweb-Team und 232 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Nexus für Platform v5 (Symfony 7.4, PHP 8.4+, SQLite-Starter).

## Wie es mit Exponential zusammenhängt

Skeleton für neue Nexus-Projekte auf Platform v5; verwendet die Forks von se7enxweb für layouts-core, richtext und admin-ui.

## Was Anwender davon haben

Composer und ein Installationsbefehl ergeben eine v5-Website mit Layouts, REST, GraphQL und Administration.

## Installation

Das Repository ist ein Projekt, keine Bibliothek: Seine composer.json hat keinen Paketnamen, `composer require` ist also nicht anwendbar. Der Quick Start der README (geprüft an der README am HEAD des Klons) lautet: das Projekt klonen, die PHP-Abhängigkeiten installieren, `.env` nach `.env.local` kopieren, die Demo-Datenbank installieren (SQLite braucht keinen Datenbankserver), die Assets mit Node 22 bauen, das JWT-Schlüsselpaar und das GraphQL-Schema erzeugen und den Entwicklungsserver starten.

```bash
composer install
cp .env .env.local
php bin/console exponential:install exponential-media --no-interaction
yarn install && yarn build:prod
php bin/console assets:install --symlink --relative public && yarn ibexa:build
php bin/console lexik:jwt:generate-keypair
php bin/console ibexa:graphql:generate-schema
```

Diese Befehle gehören zu einem Symfony-Projekt, nicht zu dieser Installation; führen Sie sie im geklonten Starter aus. Zur Kontrolle lesen Sie den Abschnitt Quick Start in der README des Starters.

## Sicherheits-, Leistungs- und Upgrade-Änderungen in der Geschichte

Diese Änderungen sind im Abdeckungsnachweis eingeordnet und werden sonst nirgends auf dieser Seite genannt. Die meisten sind Upstream-Geschichte, die der Fork mitführt; Datum und Betreffzeile des Commits sind der Eintrag im Änderungsverzeichnis (siehe das [vollständige Änderungsverzeichnis](../aenderungsverzeichnis/README.md)). Die Betreffzeile sagt, was sich geändert hat; die genaue Wirkung zeigt der Commit im Repository.

### Verhalten und Upgrade (1)

- 2024-02-08 `7d6bc3dc6` NGSTACK-673: update for breaking change in Site API

<!-- rev2-listed-rows:end -->

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 118 |
| Fehlerbehebungen | 23 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Dokumentation | 15 |
| Werkzeuge | 41 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 49 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-26 | 1.0.0.0 | `40098be54` | Update project title and description in README |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (3)

- 2026-03-12 `6546733e6` Werkzeuge: Update project name, license, and description in composer.json
- 2026-04-19 `8e3aea2a7` Werkzeuge: resolve dependency installation failures on fresh project creation
- 2026-04-19 `7047d3f53` Werkzeuge: correct sevenx-recipes Flex endpoint ref from flex/main to master

### Kompatibilität mit Symfony und der Plattform (2)

- 2026-03-13 `ee6e72d14` Fehlerbehebung: Upgraded from Symfony 7.3 (End of Life / Support) to Symfony 7.4. Upgrade.
- 2026-04-19 `b69680701` Fehlerbehebung: Remove section on 7x Forks & Upstream Incompatibility Fixes

### Weitere Änderungen am Fork (2)

- 2026-03-13 `450c92332` Fehlerbehebung: Upgraded repo node version from node 18 to node 22. Upgrade
- 2026-04-19 `94c7a8fbf` Fehlerbehebung: chore(deps): bump se7enxweb/exponential-platform-dxp-core v5.0.6 → v5.0.7

### Unterstützung für SQLite (2)

- 2026-04-19 `41883758e` Funktion: v5 Ibexa OSS on Symfony 7.4, PHP 8.4, SQLite dev db
- 2026-04-26 `829e63b43` Funktion: Importing from exponential-platform-nexus .x Default installation configured for instant use using sqlite as database.

### Dokumentation (2)

- 2026-04-19 `1aa134eaf` Dokumentation: add full 7x INSTALL guide and update README with DB conversion stub
- 2026-04-26 `40098be54` Dokumentation: Update project title and description in README

### Pflege des Repositorys (2)

- 2026-04-26 `4bb4dab3a` Werkzeuge: Removed vendor from .gitignore
- 2026-04-26 `385b756c8` Werkzeuge: Removed var from being specifically referenced in .gitignore

### Fehlerbehebungen (1)

- 2026-04-19 `982545fa8` Fehlerbehebung: restore Netgen Layouts bundles/routes after recipe unconfigure; install se7enxweb/layouts-core

Außerdem: 2 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 7 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `539baa6b8` NGSTACK-817 Add nvm statements to ibexa-assets command; `868135b37` NGSTACK-1 fix broken JS in cookie ribbon |
| 2024-01 | 4 | 2 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | `2938bda29` IOTA-649 add internal and external link option for embeded images; `d58326640` Disable phpdoc FQCN imports |
| 2024-02 | 13 | 4 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `60190a2b2` Upgrade to Ibexa 4.6; `20ea872ee` Upgrade to Ibexa 4.6 |
| 2024-03 | 40 | 14 | 5 | 0 | 0 | 0 | 0 | 11 | 0 | 10 | `8b2cb1d9a` NGSTACK-786 upgrade swiper 4 > 11; `5d0564588` NGSTACK-492 introduce simple smoke tests |
| 2024-04 | 12 | 6 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `5ba65e2ff` Add toolbar macro to block item view type templates; `4d9f9bdf2` Add Netgen Toolbar |
| 2023-08 | 9 | 8 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `aab255e60` NGSTACK-752 Initial icon additions and structure example; `319ef10e0` NGSTACK-752 - Update templates and styles |
| 2024-05 | 11 | 4 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 3 | `e455a23b0` NGSTACK-835: replace Better Admin UI with Admin UI Extra; `b13e4c724` NGSTACK-885 add host to git tag during deployment |
| 2024-07 | 6 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `c0852dfb2` Add Ibexa Scheduled Visibility bundle; `f3922cc18` Add ibexa/core 4.6.7+ to conflicts due to issues with duplicate fields in the database |
| 2024-09 | 5 | 1 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `5e205684a` Add missing type config for Nova SEO; `41768aba6` Conflict with ibexa/core < 4.6.10 |
| 2024-10 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `136abf402` Bump ibexa/oss to 4.6.12; `b310c512b` Fix namespace for smoke tests |
| 2024-11 | 5 | 1 | 0 | 0 | 0 | 0 | 1 | 1 | 0 | 2 | `54fb89fa9` NGSTACK-924 raise php version to 8.2; `7ea1e2601` NGSTACK-925 Downgrade phpdoc-parser version beacuse of serializer dependency |
| 2024-12 | 59 | 42 | 3 | 0 | 0 | 0 | 9 | 3 | 0 | 2 | `fc24d0b6d` NGSTACK-953 replace headings in bivt tamplates and components; `e185db0d7` NGSTACK-941 removed fieldset and add form-wrapper |
| 2025-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `f50511332` Update code to latest ibexa/oss |
| 2025-03 | 6 | 2 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | `4173341ef` NGSTACK-963 required field mark; `1511afcec` NGSTACK-963 translation |
| 2025-04 | 19 | 14 | 1 | 0 | 0 | 0 | 1 | 1 | 1 | 1 | `4835c0d53` NGSTACK-929 remove redundant alt texts on images on components and bivts, display only explicit alternative text if set; `52b8b9a2f` NGSTACK-929 remove skip to cookie banner, make cookie banner focusable on load, create focus trap on cookie banner |
| 2025-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b139cf911` NGSTACK-962 accessibile accordions |
| 2025-05 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `c1308c2b3` Update CS fixer rules |
| 2025-06 | 5 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `bf4be771b` Sync Flex recipes; `d5085f163` Configure dynamic showcase generation command |
| 2025-07 | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `7229207be` Add Deployer task for dumping a database; `2f6974a62` Make sure fh_group and bold_group siteaccess groups have priority over frontend_group |
| 2025-08 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `1c3d30531` Use Symfony's own Dotenv component |
| 2025-11 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `94a83b9d0` NGSTACK-1014 Set up global stylesheet, refactor typography and header styles; `0100ced58` NGSTACK-1013 Add collapse nav breakpoint variable |
| 2025-12 | 6 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `0cd40ea29` Upgrade project to Ibexa 5; `876f1866c` Upgrade Netgen Layouts and other packages to Symfony 7.3 and PHP 8.4 |
| 2026-01 | 6 | 1 | 1 | 0 | 0 | 0 | 0 | 2 | 0 | 2 | `37164d8a7` Fix frontend build warnings; `113719ee4` NGSTACK-1009 Fix page jumping when sticky header is applied |
| 2026-02 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 0 | `abcd5838e` [TEMP]: Remove usage of Novactive SEO Bundle until it supports Ibexa 5; `3f7f5afb4` Update PHPUnit to v13 |

## Verwandte Seiten

- [Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md)
- [Release-Changelog](../../../changelogs/extensions/exponential-platform-nexus-starter.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-nexus-starter.md)
- [SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-08](monate/2023-08.md), [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-07](monate/2024-07.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2024-12](monate/2024-12.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-03](monate/2025-03.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
