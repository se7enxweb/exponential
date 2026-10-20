# exponential-platform-v4x-dxp-skeleton: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-v4x-dxp-skeleton`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 25 Änderungen von 2024-05-31 bis 2026-04-10: 19 vom se7enxweb-Team und 6 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Projekt-Skeleton für Platform 4.6.x DXP.

## Wie es mit Exponential zusammenhängt

Wie das v5-Skeleton, für die Linie 4.6 (Symfony 5.4).

## Was Anwender davon haben

create-project für 4.6.

Dieses Repository ist ein Composer-Projekt (Typ `project`), keine Bibliothek; Sie legen also eine neue Website daraus an:

```bash
composer create-project se7enxweb/exponential-platform-v4x-dxp-skeleton exponential_website
cd exponential_website
```

Den anschließenden Datenbankschritt beschreibt die README des Repositorys (`.env.local`, dann `php bin/console exponential:install` oder der dokumentierte Import).

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 4 |
| Fehlerbehebungen | 3 |
| Verhaltens- und Upgrade-Änderungen | 3 |
| Dokumentation | 5 |
| Werkzeuge | 8 |
| Ohne Nutzen für Anwender | 2 |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (4)

- 2026-03-12 `0112871` Werkzeuge: Add project skeleton composer.json with se7enxweb/exponential-platform-dxp metapackage dependency
- 2026-04-03 `6fa7457` Werkzeuge: Changing composer.json requirements in an initial attempt at downgrading package to Platform v4.6.x and Symfony 5.3. Forking.
- 2026-04-03 `d5a228f` Werkzeuge: Reajusting requirements. Finetuning base composer requirements. Bugfix.
- 2026-04-03 `ef08011` Werkzeuge: Reajusting requirements. Finetuning base composer requirements. Bugfix.

### Dokumentation (4)

- 2026-03-12 `fce8116` Dokumentation: Add nyholm/psr7 to satisfy PSR-17 factory requirement for ibexa/post-install
- 2026-04-08 `3a4c2ea` Dokumentation: add comprehensive README.md and INSTALL.md for v4.6.x DXP skeleton
- 2026-04-10 `2a8a0b3` Dokumentation: fix repo links, update license badge to GPL v2 (or any later version)
- 2026-04-10 `973d071` Dokumentation: remove version constraint from composer create-project commands

### Fehlerbehebungen (3)

- 2026-03-12 `cd0ef5c` Fehlerbehebung: Fix nyholm/psr7 version constraint to ^1.0
- 2026-03-21 `3d4387c` Fehlerbehebung: Reordering recipes run order. Bugfix.
- 2026-04-10 `fe5528f` Fehlerbehebung: set extra.symfony.require to 5.4.* to prevent Flex version mismatch

### Namen der Konsolenbefehle (2)

- 2026-04-10 `89df8e6` BC: rename ibexa:install → exponential:install in README and INSTALL
- 2026-04-10 `b079aba` BC: fix all deprecated command names in README and INSTALL

### Marke Exponential (1)

- 2026-03-12 `06a2373` Funktion: Change package vendor/name details in composer.json and doc. Rebranding.

### Funktionen (1)

- 2026-03-12 `d30c25a` Funktion: Add sevenx-recipes Flex endpoint before ibexa/recipes

### Aufräumen (1)

- 2026-03-12 `32df31e` Werkzeuge: Remove config files that must come from Flex recipe, not skeleton

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-04-03 `c8b8b63` BC: Updated README.md to address version name and composer package name changes. Forking.

Außerdem: 2 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `eeab899` Updated copyright year to 2024 |
| 2025-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `1edfed8` Updated copyright year to 2025 |
| 2025-05 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `fb57aec` ENG-140: Added auto-assign reviewers GH workflow (#7) |
| 2025-12 | 2 | 0 | 0 | 0 | 0 | 0 | 1 | 1 | 0 | 0 | `6132377` [Composer] Fixed license information; `c32b551` [Doc] Fixed outdated license information |
| 2026-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `27119aa` [Browser tests] Switch to actions/create-github-app-token (#8) |

## Verwandte Seiten

- [DXP-Skeleton](../../../eng-us/features/6.0/platform-dxp-skeleton.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-v4x-dxp-skeleton.md)
- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2024-05](monate/2024-05.md), [2025-01](monate/2025-01.md), [2025-05](monate/2025-05.md), [2025-12](monate/2025-12.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
