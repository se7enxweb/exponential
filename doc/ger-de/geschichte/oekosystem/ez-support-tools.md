# ez-support-tools: Geschichte des Plattform-Repositorys

Die Geschichte von `ez-support-tools`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2024-02-16 bis 2026-04-11: 5 vom se7enxweb-Team und 2 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Seiten mit Systeminformationen für Administratoren (system info).

## Wie es mit Exponential zusammenhängt

Auf se7enxweb umbenannt; schützt vor einer Version null aus InstalledVersions.

## Was Anwender davon haben

Die Seite Systeminformationen der Administration bricht bei Paketversionen null nicht mehr ab.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ez-support-tools
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 1 |
| Fehlerbehebungen | 2 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 1 |
| Releases | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-07-01 | v2.3.13 | `3d4ce31` | Update composer.json replaced require section package vendor name |
| 2025-09-28 | v2.3.14 | `bf354af` | Update se7enxweb/ezplatform-core version requirement |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2025-07-01 `3d4ce31` Release: Update composer.json replaced require section package vendor name

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-28 `bc578dd` BC: Update package descriptions and PHP version support

### Weitere Änderungen am Fork (1)

- 2025-09-28 `bf354af` Fehlerbehebung: Update se7enxweb/ezplatform-core version requirement

### Versionsnummern (1)

- 2026-04-11 `71c5603` Release: Version bump for php requirements to allow php 8.5.x. Bugfix.

### Fehlerbehebungen (1)

- 2026-04-11 `ea79490` Fehlerbehebung: guard against null version from InstalledVersions::getVersion()

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `ef74a0d` Fixed Composer license info and bumped license & copyright year (#120) |
| 2024-03 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `954fd28` Updated EOM and EOL hardcoded dates |

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Seite der Erweiterung](../../../eng-us/features/6.0/extensions/ez-support-tools.md)
- [Release-Changelog](../../../changelogs/extensions/ez-support-tools.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ez-support-tools.md)
- Plattform-Ökosystem nach Monat: [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2025-07](monate/2025-07.md), [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
