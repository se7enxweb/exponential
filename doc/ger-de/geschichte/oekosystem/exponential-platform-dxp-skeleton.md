# exponential-platform-dxp-skeleton: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform-dxp-skeleton`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 21 Änderungen von 2024-05-31 bis 2026-04-10: 15 vom se7enxweb-Team und 6 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Projekt-Skeleton für Platform v5 DXP (create-project).

## Wie es mit Exponential zusammenhängt

Installationsanleitung mit SQLite, MySQL und PostgreSQL, JWT, GraphQL, Solr, Varnish; die Befehle verwenden das Präfix exponential:.

## Was Anwender davon haben

composer create-project und ein dokumentierter Installationsweg.

Dieses Repository ist ein Composer-Projekt (Typ `project`), keine Bibliothek; Sie legen also eine neue Website daraus an:

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton exponential_website
cd exponential_website
```

Den anschließenden Datenbankschritt beschreibt die README des Repositorys (`.env.local`, dann `php bin/console exponential:install` oder der dokumentierte Import).

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 4 |
| Fehlerbehebungen | 3 |
| Dokumentation | 7 |
| Werkzeuge | 5 |
| Ohne Nutzen für Anwender | 2 |

## Änderungen des se7enxweb-Teams, nach Thema

### Dokumentation (6)

- 2026-03-12 `fce8116` Dokumentation: Add nyholm/psr7 to satisfy PSR-17 factory requirement for ibexa/post-install
- 2026-04-08 `aebd307` Dokumentation: expand README with full project header, banners and body sections for v5
- 2026-04-08 `0e7ea1d` Dokumentation: fix license badge to GPL v2+ (or any later version)
- 2026-04-08 `1c15559` Dokumentation: update license badge to full GPL v2 (or any later version) label
- 2026-04-10 `19cc05d` Dokumentation: expand README and add comprehensive INSTALL.md for Platform v5 DXP skeleton
- 2026-04-10 `6271d40` Dokumentation: expand CLI reference, fix phantom commands, clean up composer create-project

### Fehlerbehebungen (2)

- 2026-03-12 `cd0ef5c` Fehlerbehebung: Fix nyholm/psr7 version constraint to ^1.0
- 2026-03-21 `3d4387c` Fehlerbehebung: Reordering recipes run order. Bugfix.

### Marke Exponential (1)

- 2026-03-12 `06a2373` Funktion: Change package vendor/name details in composer.json and doc. Rebranding.

### Composer-Abhängigkeiten (1)

- 2026-03-12 `0112871` Werkzeuge: Add project skeleton composer.json with se7enxweb/exponential-platform-dxp metapackage dependency

### Funktionen (1)

- 2026-03-12 `d30c25a` Funktion: Add sevenx-recipes Flex endpoint before ibexa/recipes

### Aufräumen (1)

- 2026-03-12 `32df31e` Werkzeuge: Remove config files that must come from Flex recipe, not skeleton

### Kompatibilität mit PHP 8.x (1)

- 2026-04-08 `f7182d9` Fehlerbehebung: add v5 INSTALL.md and rewrite README for 5.0.x (Symfony 7.3+, PHP 8.3+, AssetMapper)

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

- [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform-dxp-skeleton.md)
- [SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2024-05](monate/2024-05.md), [2025-01](monate/2025-01.md), [2025-05](monate/2025-05.md), [2025-12](monate/2025-12.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
