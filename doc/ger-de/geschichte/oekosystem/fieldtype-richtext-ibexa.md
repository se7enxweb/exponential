# fieldtype-richtext-ibexa: Geschichte des Plattform-Repositorys

Die Geschichte von `fieldtype-richtext-ibexa`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 244 Änderungen von 2023-12-14 bis 2026-04-19: 2 vom se7enxweb-Team und 242 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Feldtyps RichText von Ibexa für Platform v5 (se7enxweb/fieldtype-richtext).

## Wie es mit Exponential zusammenhängt

Rich-Text-Bearbeitung für Exponential Platform v5; korrigiert fest eingetragene Vendor-Pfade, sodass XSL-Stylesheets und der Webpack-Alias im Fork aufgelöst werden.

## Was Anwender davon haben

Das Rich-Text-Feld wird korrekt dargestellt und bearbeitet, wenn es aus Paketen von se7enxweb installiert ist.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/fieldtype-richtext
```

## Sicherheits-, Leistungs- und Upgrade-Änderungen in der Geschichte

Diese Änderungen sind im Abdeckungsnachweis eingeordnet und werden sonst nirgends auf dieser Seite genannt. Die meisten sind Upstream-Geschichte, die der Fork mitführt; Datum und Betreffzeile des Commits sind der Eintrag im Änderungsverzeichnis (siehe das [vollständige Änderungsverzeichnis](../aenderungsverzeichnis/README.md)). Die Betreffzeile sagt, was sich geändert hat; die genaue Wirkung zeigt der Commit im Repository.

### Sicherheit (1)

- 2025-04-08 `c8808a5` [CS] Fixed outstanding CS issue in XMLSanitizer.php

### Leistung (1)

- 2025-07-16 `4bdd9a7` IBX-10331: Fixed memory leak while compiling assets (#248)

### Verhalten und Upgrade (7)

- 2024-08-05 `78f1364` IBX-8533: Dropped deprecated webpack config names (#176)
- 2025-02-16 `5768fc4` [CLI] Replaced deprecated Command::{$defaultName, $defaultDescription} with the AsCommand attribute
- 2025-02-21 `82d57a3` Removed deprecated ez_richtext_config global Twig variable (#209)
- 2025-02-25 `11c7ca7` [Twig] Removed usage of deprecated spaceless filter (#210)
- 2025-05-09 `17f8078` [Rector] Replaced deprecated Relation consts with RelationType enum
- 2025-05-16 `6d04149` [symfony/dependency-injection] Replaced deprecated !tagged YAML tag to !tagged_iterator
- 2025-05-22 `d4cbe17` IBX-9940: Removed Sass deprecations (#231)

<!-- rev2-listed-rows:end -->

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 51 |
| Fehlerbehebungen | 42 |
| Verhaltens- und Upgrade-Änderungen | 13 |
| Sicherheit | 4 |
| Leistung | 1 |
| Dokumentation | 1 |
| Werkzeuge | 42 |
| Ohne Nutzen für Anwender | 90 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-19 | 5.0.0 | `1d4b855` | Fork: rename to se7enxweb/fieldtype-richtext, fix  webpack alias for se7enxweb/admin-ui compatibility |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (2)

- 2026-04-19 `1d4b855` BC: Fork: rename to se7enxweb/fieldtype-richtext, fix  webpack alias for se7enxweb/admin-ui compatibility
- 2026-04-19 `95289ad` BC: replace hardcoded vendor/ibexa/fieldtype-richtext paths with se7enxweb

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2ba22d5` IBX-7329: Enhancing UX: Name Updates for Product 4.6 LTS+ (#138); `97c1b76` IBX-7441: Fixed custom attributes in link (#140) |
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `cba4967` Added event to manipulate CKEditor config before initialization  (#141) |
| 2024-02 | 11 | 1 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | `058d349` IBX-6932: Fixed adding custom attrs to list (#139); `ef78778` IBX-6379: Fixed anchor in formatted (#144) |
| 2024-03 | 17 | 2 | 4 | 0 | 0 | 0 | 0 | 3 | 0 | 8 | `f3cb1bc` Added test for translation extraction (#151); `f315398` IBX-7503: Fixed custom attributes (#152) |
| 2024-04 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `158be52` IBX-8119: Upgraded minimum PHP version to 8.3; `1e5ed43` Made 'Link to' field in link module required (#157) |
| 2024-05 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `8568d2e` IBX-8121: Fixed code style for 5.0; `e005bb6` Updated copyright year to 2024 |
| 2024-06 | 7 | 1 | 0 | 1 | 0 | 0 | 1 | 1 | 0 | 3 | `ddc118f` IBX-8139: Dropped class_alias BC layer statements from all classes (#161); `b1ec1be` Added translation extractor for custom tags (#162) |
| 2024-07 | 13 | 4 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 7 | `1d29e0c` IBX-8434: Added ProviderConfiguratorInterface to allow configuration post-processing; `5ebad1e` IBX-8150: Added option to enable attributes types (#167) |
| 2024-08 | 18 | 3 | 1 | 1 | 0 | 0 | 0 | 5 | 0 | 8 | `c5d18f3` [Rector] Applied all Symfony 5.x rectors to the production codebase (#172); `dac171d` IBX-8150: Added option to enable attributes types (#179) |
| 2024-09 | 10 | 1 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `1d7a8da` IBX-8875: Dropped deprecated code (#185); `2ef456b` IBX-8824: The popup for custom tags with multiple fields is not scrollable (#183) |
| 2024-10 | 4 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `e9bbcaf` IBX-8845: Added alignments for custom tags (#187); `9e9924f` IBX-9097: [PB] Error with Richtext validator in nested_attribute (#191) |
| 2024-11 | 3 | 0 | 1 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | `c268ebf` IBX-8534: Dropped deprecated context and getIndexData from Storage (#190); `ef174e2` IBX-8805: Dropped deprecated Twig Functions&Filters (#192) |
| 2024-12 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `f6b1f85` IBX-9154: The dropdown for custom class is illegible (#193); `fe4db77` IBX-9319-TS-support (#198) |
| 2025-01 | 6 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `21f1dbc` IBX-9297: Custom CSS can not be removed from link element (#199); `ef64b0a` IBX-9296: Custom CSS classes not working for "embedimage" (#197) |
| 2025-02 | 23 | 2 | 6 | 3 | 0 | 0 | 0 | 8 | 0 | 4 | `ab8c8af` IBX-9298: Fixed visual indication of selected custom CSS classes (#200); `ce80180` Fixed obsolete `ibexa_content` controller references (#205) |
| 2025-03 | 10 | 2 | 2 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `be60c4a` Fixed PHP 8.1 PHPstan issue report; `cb92634` [PHPStan] Set `treatPhpDocTypesAsCertain` to `false` (#219) |
| 2025-04 | 12 | 1 | 2 | 0 | 2 | 0 | 0 | 0 | 0 | 7 | `b5a8382` IBX-9727: Added missing type hints (#222); `53d1e60` Backported fix for backward-compatibility issue on XMLSanitizer |
| 2025-05 | 16 | 5 | 1 | 3 | 0 | 0 | 0 | 4 | 0 | 3 | `c20af5d` IBX-9916: Upgrade frontend dependencies (#229); `82d9e36` IBX-8471: Upgraded codebase to Symfony 7 (#217) |
| 2025-06 | 7 | 3 | 0 | 0 | 1 | 0 | 0 | 2 | 0 | 1 | `a8ca1c5` IBX-9947: Rebranded field type identifiers (#238); `4a4a170` IBX-9867: Fixed XSS in embed in rich text |
| 2025-07 | 17 | 4 | 3 | 0 | 0 | 1 | 0 | 4 | 0 | 5 | `63136d4` IBX-9727: Applied missing strict types (#252); `372c366` IBX-10262: Renamed and refactored `CustomTagsValidator` to support bot |
| 2025-08 | 3 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `adce2b9` Fixed PHPStan issue (#260); `1f07cef` IBX-10316: Fixed editing linked embed image (#261) |
| 2025-09 | 11 | 5 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | `e0ecc27` IBX-10507: Allowed nullable type declarations for method parameters across the codebase (#265); `52ab56d` IBX-9981: Added go to and edit image (#264) |
| 2025-10 | 10 | 0 | 3 | 0 | 1 | 0 | 0 | 3 | 0 | 3 | `00554a9` [CI] Adjusted gh actions and PHPStan (#268); `33590a1` IBX-10288: Fix XSS in custom tags |
| 2025-11 | 10 | 0 | 3 | 0 | 0 | 0 | 0 | 1 | 0 | 6 | `b58bebc` IBX-9657: Added option to set shouldFireInputEvent in updateInput via attribute to prevent error message removal (#274); `5c5d806` IBX-10725: Fixed image filter dropdown on first selection (#271) |
| 2025-12 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `2091241` IBX-9266: Allowed the selection of site access when creating a RTE link (#267); `f2903e6` [Composer] Fixed license information |
| 2026-01 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `80e31c1` IBX-9266: Fixed siteaccess handling for image embed links (#280) |
| 2026-02 | 6 | 1 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 3 | `f6647c6` IBX-11179: Updated PHP versions in CI configuration with 8.3 and 8.4 (#284); `c701a4a` IBX-10897: Fixed links on embed image (#282) |
| 2026-03 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `84ae86e` IBX-10827: Added option to extend Exponential.rng schema (#281); `0efe2d9` IBX-11247: Bumped `symfony/*` to 7.4 LTS (#286) |
| 2026-04 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `a84e349` Refactor PHP code to fix phpstan and added error baselines. (#291) |

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Release-Changelog](../../../changelogs/extensions/fieldtype-richtext-ibexa.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/fieldtype-richtext-ibexa.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-06](monate/2024-06.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2024-12](monate/2024-12.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-03](monate/2025-03.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
