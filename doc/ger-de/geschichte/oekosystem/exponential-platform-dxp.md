# exponential-platform-dxp: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-dxp`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 112 Änderungen von 2023-12-21 bis 2026-04-19: 20 vom se7enxweb-Team und 92 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Metapaket se7enxweb/exponential-platform-dxp für Platform v5.

## Wie es mit Exponential zusammenhängt

Holt die Forks mit einem einzigen require; ersetzte im März und April 2026 die Pakete von ibexa durch die von se7enxweb.

## Was Anwender davon haben

Ein Paketname für eine vollständige v5-Installation.

Dies ist ein Metapaket. Eine neue Website entsteht aus dem Skeleton, das es verlangt:

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton exponential_website
```

Ein bestehendes Projekt kann es mit `composer require se7enxweb/exponential-platform-dxp` hinzufügen.

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 24 |
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 6 |
| Dokumentation | 1 |
| Werkzeuge | 38 |
| Ohne Nutzen für Anwender | 42 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-12 | 0.0.0.1 | `f7fcc28` | Change package vendor/name details in composer.json and doc. Rebranding. |
| 2026-03-21 | v0.0.0.2 | `11da176` | Replacing package vendor for admin-ui package. Rebrading. |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (6)

- 2026-03-21 `11da176` BC: Replacing package vendor for admin-ui package. Rebrading.
- 2026-04-11 `dcdbf64` BC: Replace ibexa/system-info with se7enxweb/system-info
- 2026-04-17 `596c019` BC: replace ibexa/admin-ui-assets with se7enxweb/admin-ui-assets (v5 pure)
- 2026-04-19 `f38e977` BC: Replace ibexa/fieldtype-richtext with se7enxweb/fieldtype-richtext fork
- 2026-04-19 `36d63a6` BC: Use se7enxweb/fieldtype-richtext 5.0.x-dev branch
- 2026-04-19 `ccc350c` BC: require se7enxweb/layouts-core fork for PHP 8.4 private(set) Twig compatibility

### Marke Exponential (4)

- 2026-03-12 `05a2a3d` Funktion: Change package vendor/name details in composer.json and doc. Rebranding.
- 2026-03-12 `f7fcc28` Funktion: Change package vendor/name details in composer.json and doc. Rebranding.
- 2026-03-21 `6e81075` Funktion: Replaced Ibexa Core Package With Fork to Replace Product Name in user facing templates with logos / trademarks. Rebranding.
- 2026-03-21 `2eba385` Funktion: Switching core package to tagged release, v5.0.1.0. Rebranding.

### Composer-Abhängigkeiten (1)

- 2026-03-17 `2bcb37a` Werkzeuge: Rename package and update description in composer.json

### Weitere Änderungen am Fork (1)

- 2026-04-13 `7f03a97` Fehlerbehebung: recipeversion: dev-master → 1.3.x-dev (v5 pure recipe key)

Außerdem: 8 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 4 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `5171945` [CI] IBX-4515: Include tests running on PHP 8.2 (#129); `ec69a98` [CI] IBX-4515: Fix typo in PHP 8.2 job name |
| 2024-01 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `9413966` [CI] Fixed duplicated php-image declaration |
| 2024-02 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `b9a09cc` [Composer] Added conflict with friends-of-behat/mink-browserkit-driver:v1.6.2 (#136) |
| 2024-03 | 7 | 1 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 2 | `bd0a88a` [CI] IBX-6507: Add setup running on Solr (#117); `da4ec55` Set up branch to become 5.0 in the future |
| 2024-04 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c40b4dd` Updated PHP version to 8.3, upgraded actions/checkout version; `7c5087c` IBX-8154: Use default image for 5.0 |
| 2024-05 | 6 | 2 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 3 | `8605b34` Updated copyright year to 2024; `9b6a9fa` Update README.md (#144) |
| 2024-06 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d6a2d57` IBX-8135: Removed hautelook/templated-uri-bundle fork (#151); `29ed284` [CI] Removed 4.5 from job triggering nightly (#158) |
| 2024-07 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `61451f2` IBX-8136: Replace or drop php-http/message-factory & php-http/guzzle6-adapter dependencies (#161) |
| 2024-08 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `87f0327` IBX-8137: Dropped Swiftmailer bundle (#179) |
| 2024-10 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `0d55272` Added ibexa/core-search (#181) |
| 2024-11 | 5 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 2 | `fa2bfcb` Unpacked symfony/serializer-pack dependency declaration (#187); `9e91740` [composer] Removed dependency on symfony/orm-pack (#188) |
| 2025-01 | 7 | 2 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 3 | `1804822` Updated copyright year to 2025; `05c62ae` Updated copyright year to 2025 |
| 2025-02 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `0c4d6e7` IBX-8470: Upgraded codebase to Symfony 6 (#193); `f20502b` Changed ibexa/admin-ui-assets version back to ~5.0.0 (#197) |
| 2025-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d0780fb` [Behat] Nightly browser tests job removal for example-in-memory-product-catalog (4.6) (#206) |
| 2025-04 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `93a86bf` IBX-9697: [Composer] Added ibexa/twig-componenets (#211); `91b6488` Reverted "IBX-9697: [Composer] Added ibexa/twig-componenets (#211)" (#213) |
| 2025-05 | 11 | 2 | 0 | 0 | 0 | 0 | 0 | 5 | 0 | 4 | `cbfe72d` IBX-8543: Included newer DBMS versions on CI & reorganized setup (#195); `6cf1e17` IBX-8471: Upgraded codebase to Symfony 7 (#205) |
| 2025-07 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `6ad93c4` IBX-10228: Bumped symfony/* to ^7.3 (#226); `e017f52` [GHA] Created Post Release workflow (#230) |
| 2025-08 | 7 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 3 | `fabc501` [GHA][PostRelease] Fixed typos in reusable workflow path; `0d834d7` Solr 8 CI upgrade (#232) |
| 2025-09 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `4fdbd53` IBX-10493: Included Redis 7.2 on CI (#234); `e27b61c` Fix Nightly workflow authentication (#240) |
| 2025-10 | 7 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 4 | `c4d2556` [CI] Add token selection for 3.3 (#241); `b45edf7` IBX-10494: Included Postgres 18 on CI (#237) |
| 2025-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `4d12ae2` [Composer] Used 5.0-next admin-ui-assets branch (#245) |
| 2025-12 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `5426581` IBX-10946: Included MariaDB 11.4 on CI (#246) |
| 2026-01 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 2 | `24a4d17` added messenger dependency (#252); `ffa91cc` IBX-11108: Included Redis latest & Valkey latest on CI (#248) |
| 2026-02 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `2ccd825` IBX-10495: Remove unused `ibexa/ci-scripts` dependency (#256) |
| 2026-03 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `c85d992` IBX-11328: Included PHP 8.4 in browser tests (#260); `16401f0` [Merge-up] IBX-11328: Included PHP 8.4 in browser tests (#265) |

## Verwandte Seiten

- [DXP-Skeleton und Metapaket](../../../eng-us/features/6.0/platform-dxp-skeleton.md)
- [Release-Changelog](../../../changelogs/extensions/exponential-platform-dxp.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-dxp.md)
- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-06](monate/2024-06.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-03](monate/2025-03.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
