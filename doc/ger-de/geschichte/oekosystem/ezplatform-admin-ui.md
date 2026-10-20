# ezplatform-admin-ui: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-admin-ui`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Administrationsoberfläche). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 36 Änderungen von 2023-12-13 bis 2026-04-12: 23 vom se7enxweb-Team und 13 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork der Administrationsoberfläche von eZ Platform 2.x / 3.x (Exponential Platform Admin v2).

## Wie es mit Exponential zusammenhängt

Administrationsoberfläche der Linie 3.x, umbenannt in Exponential Platform DXP.

## Was Anwender davon haben

Marke Exponential, Umstellung von SCSS auf @use/@forward, funktionierende Asset-Pfade unter PHP 8.5.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-admin-ui
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 11 |
| Fehlerbehebungen | 7 |
| Verhaltens- und Upgrade-Änderungen | 3 |
| Sicherheit | 2 |
| Werkzeuge | 3 |
| Ohne Nutzen für Anwender | 10 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.35 | `2c344718` | Update ezplatform-search dependency version |
| 2025-09-28 | v2.3.36 | `6b81e31b` | Merge pull request #1 from se7enxweb/fix/webpack-asset-paths-se7enxweb |
| 2025-09-28 | v2.3.37 | `8ac258af` | Merge pull request #2 from se7enxweb/apply-branding-changes-from-2.2 |
| 2025-09-28 | v2.3.38 | `529b07d4` | Merge pull request #3 from se7enxweb/apply-branding-changes-from-2.2 |
| 2025-09-28 | v2.3.39 | `b126ffcd` | Merge pull request #4 from se7enxweb/fix/update-html-title-to-exponential-platform |
| 2026-03-25 | 2.3.34.1 | `acd8c584` | extend PHP constraint to ^8.5 for eZ Platform 3.3 / se7enxweb fork 2.3.x branch |
| 2026-04-11 | v2.3.40 | `e8c62c9b` | Merge pull request #8 from se7enxweb/2.3 |
| 2026-04-11 | v2.3.41 | `aa915575` | Rebranding text string replacements. Rebranding. |

## Änderungen des se7enxweb-Teams, nach Thema

### Marke Exponential (5)

- 2025-09-28 `cc81d13a` Funktion: Apply comprehensive branding changes from 2.2 branch
- 2025-09-28 `056ea021` Funktion: Patch logo core color bug. Rebranding.
- 2025-09-28 `7c84eb0c` Funktion: Fix HTML title tag branding: Replace 'Ibexa DXP' with 'Exponential Platform'
- 2026-04-11 `e7bbbd38` Funktion: feat(branding): update to Exponential Platform DXP branding
- 2026-04-11 `aa915575` Funktion: Rebranding text string replacements. Rebranding.

### Weitere Änderungen am Fork (3)

- 2025-09-28 `9432f2e9` Fehlerbehebung: Update ezplatform-search dependency version
- 2025-09-28 `2c344718` Fehlerbehebung: Update ezplatform-search dependency version
- 2026-03-25 `acd8c584` Fehlerbehebung: extend PHP constraint to ^8.5 for eZ Platform 3.3 / se7enxweb fork 2.3.x branch

### Fehlerbehebungen (2)

- 2026-04-11 `c3d25812` Fehlerbehebung: encore configs reference ezsystems/ezplatform-admin-ui-assets (installed dep) not se7enxweb
- 2026-04-11 `14b2c03d` Fehlerbehebung: migrate SCSS  → /, fix deprecated color/math functions

### Replace-Deklarationen für das Upstream-Paket (2)

- 2026-04-11 `70396dd7` BC: add replace shim for ezsystems counterpart package
- 2026-04-12 `70c741a0` BC: replace uses wildcard '*' not self.version

### Composer-Abhängigkeiten (1)

- 2025-09-28 `95cbb7cf` Werkzeuge: Change package names and licenses in composer.json

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-28 `7db679cc` BC: Fix webpack asset paths to use se7enxweb package names

Außerdem: 9 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f8d30285` IBX-1464: Added `versionNo` param in the `ezimage` edit form template  |
| 2024-01 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b0db6f5e` IBX-7046: Fixed Dashboard "My Content" & "My Media" edit buttons (#2114) |
| 2024-03 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2ffae963` IBX-7954: Show error message on empty image asset (#2116); `80eeb142` IBX-6540: Added Depth sort to breadcrumbs (#2115) |
| 2024-04 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ef361314` IBX-7983: Handled previewing `ezimage` field with height of 0 (#2117) |
| 2024-06 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f1a75400` IBX-8019: Replaced `LocationService::loadLocationChildren` use with `S |
| 2024-07 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `7d1971ca` IBX-8079: Inconsistent redirect point after role assignment action (#2118); `81d22311` IBX-8350: Text line fields which is not marked as required, but has a minimum length constraint set, is basically treated as required (#2122) |
| 2025-06 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | `acaa620d` IBX-9793: Fixed XSS issues in several places; `71819dd2` [Tests] Fixed failing CI |
| 2025-10 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | `da3bfbfb` [Security] IBX-10200: Fix XSS in reschedule/cancel-schedule modal; `ad89d174` [GHA][Browser tests] Added secrets for browser tests configuration |

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Release-Changelog](../../../changelogs/extensions/ezplatform-admin-ui.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-admin-ui.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-06](monate/2024-06.md), [2024-07](monate/2024-07.md), [2025-06](monate/2025-06.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
