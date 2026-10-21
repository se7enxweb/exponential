# ezplatform-kernel: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-kernel`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Kernel). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 38 Änderungen von 2023-12-22 bis 2026-04-12: 23 vom se7enxweb-Team und 15 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Kernels von eZ Platform 1.13 / 2.5 / 3.3 (Content-Repository, APIs, Symfony-Integration).

## Wie es mit Exponential zusammenhängt

Kernel der Linie Exponential Platform 3.x; ersetzt in Composer ezsystems/ezplatform-kernel und ibexa/core 4.x.

## Was Anwender davon haben

Marke Exponential, Versionsbedingungen für PHP 8.5, Konsolenbefehle exponential:* und Unterstützung der Installation mit SQLite in der Linie 3.x / 4.6.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-kernel
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 13 |
| Fehlerbehebungen | 9 |
| Verhaltens- und Upgrade-Änderungen | 6 |
| Leistung | 2 |
| Werkzeuge | 2 |
| Ohne Nutzen für Anwender | 6 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-27 | v1.3.40 | `c61b18361` | Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding. |
| 2025-09-28 | v1.3.41 | `8c070ac8f` | Merge pull request #5 from se7enxweb/fix/missing-configuration-parsers |
| 2025-09-28 | v1.3.42 | `43712cafc` | Merge pull request #7 from se7enxweb/fix/update-vendor-paths-from-ezsystems-to-se7enxweb |
| 2026-03-26 | v1.3.43 | `021397cff` | Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support |
| 2026-03-29 | v1.3.44 | `efaf3e785` | append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql |
| 2026-04-12 | v1.3.45 | `77524ca4e` | switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema |

## Änderungen des se7enxweb-Teams, nach Thema

### Marke Exponential (4)

- 2025-09-27 `a613f1c18` Funktion: Change 'eZ Platform' to 'Exponential Platform'. Rebranding
- 2025-09-27 `36a12daf1` Funktion: Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding.
- 2025-09-27 `a7016bf01` Funktion: Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding.
- 2025-09-27 `c61b18361` Funktion: Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding.

### Unterstützung für SQLite (4)

- 2026-04-07 `f1970779c` Funktion: unescape backslash-escaped quotes and newlines in SQLite cleandata.sql
- 2026-04-07 `eac6c8b77` Funktion: remove autoincrement from ezcontentobject_attribute.id for SQLite composite PK compatibility
- 2026-04-11 `b32c2d6ae` Funktion: skip doctrine:database:create for SQLite in checkCreateDatabase()
- 2026-04-11 `a5a32b66f` Funktion: recreate composite-PK tables after Doctrine schema generation

### Replace-Deklarationen für das Upstream-Paket (3)

- 2026-03-26 `021397cff` BC: Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support
- 2026-03-26 `9b341b163` BC: Add branch-alias for 1.3-se7enx
- 2026-03-26 `7c7b12577` BC: Remove se7enx branch-alias: prevent auto-resolution by external projects

### Paket auf den Vendor se7enxweb umbenannt (2)

- 2025-09-27 `f54fd6f66` BC: Update package details for se7enxweb integration
- 2025-09-28 `933f42eaf` BC: Fix vendor paths: Replace ezsystems with se7enxweb paths

### Fehlerbehebungen (2)

- 2026-03-29 `efaf3e785` Fehlerbehebung: append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql
- 2026-04-12 `77524ca4e` Fehlerbehebung: switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema

### Composer-Abhängigkeiten (1)

- 2025-09-27 `9621fd420` Werkzeuge: Update PHP version requirements in composer.json

### Funktionen (1)

- 2025-09-28 `9ab6f7357` Funktion: Add missing configuration parsers for Content and User Settings views

### Namen der Konsolenbefehle (1)

- 2026-04-07 `4b90bbf4d` BC: rename commands to exponential:* prefix, keep ibexa:* and ezplatform:* as deprecated aliases

Außerdem: 5 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `29489e4fc` IBX-7346: Reindexed reverse-related content after deleting source content (#396); `4abba9267` IBX-6880: Skipped normalizing directories in the `normalizePath` metho |
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6e5fa6ff0` IBX-7485: Skipped files with corrupted filenames when loading and deleting content |
| 2024-02 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `aa31bb228` IBX-7172: Fixed Repository Filtering by multiple ObjectStateId criteria |
| 2024-03 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `8ef0e70c4` IBX-7809: Fixed creating `UserMetadata` criterion from `UserGroupLimit |
| 2024-04 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `81464c68c` Fixed missing return types for DebugTemplate class; `245d02e15` IBX-6592: Removed unusable location/subtree limitations from `state/as |
| 2024-05 | 2 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | `98b7b50e6` IBX-5388: Fixed performance issues of content updates after field changes; `b5fe9ad82` IBX-6494: Fixed copying of non-translatable fields to later versions |
| 2024-06 | 2 | 0 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | `17e78be8a` IBX-6833: Fixed copying empty fields from a published version; `c88c39759` IBX-8019: Added performance consideration notice to `LocationService:: |
| 2024-08 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `9a6241085` IBX-8562: Fixed flooding content attributes table with duplicates |
| 2024-10 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `b19be8dbe` IBX-8562: Command to remove duplicated entries after faulty IBX-5388 fix |
| 2025-02 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `553e0dd0c` IBX-9455: Upgraded Twig to ^3.19.0 (#411) |

## Verwandte Seiten

- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Namen der Konsolenbefehle](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Release-Changelog](../../../changelogs/extensions/ezplatform-kernel.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-kernel.md)
- [Spezifikation des SQLite-Installers](../../spezifikationen/6.0/platform-sqlite-installer.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-06](monate/2024-06.md), [2024-08](monate/2024-08.md), [2024-10](monate/2024-10.md), [2025-02](monate/2025-02.md), [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
