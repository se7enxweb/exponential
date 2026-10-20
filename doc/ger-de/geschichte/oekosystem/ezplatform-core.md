# ezplatform-core: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-core`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Kernel). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 8 Änderungen von 2024-03-22 bis 2026-04-11: 2 vom se7enxweb-Team und 6 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Container-Paket, das die Kern-Bundles von Exponential Platform holt.

## Wie es mit Exponential zusammenhängt

Metapaket der Linie 3.x; folgt den Releases seiner Geschwister.

## Was Anwender davon haben

Eine require-Zeile, die die passenden Kern-Bundles bringt.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-core
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Releases | 8 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.44 | `42c6e0c` | Update package names and descriptions in composer.json |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2025-09-28 `42c6e0c` Release: Update package names and descriptions in composer.json

### Versionsnummern (1)

- 2026-04-11 `d454a83` Release: Version Bump to match release. Bugfix.

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `5eb2911` Bumped Ibexa LTS version to v3.3.38 |
| 2024-07 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `8277fec` Bumped Ibexa LTS version to v3.3.39 |
| 2024-08 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `871c1bc` Bumped Ibexa LTS version to v3.3.40 |
| 2024-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `61f1115` Bumped Ibexa LTS version to v3.3.41 |
| 2025-04 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `8502386` Bumped Ibexa LTS version to v3.3.42 |
| 2025-06 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | `a3c4658` Bumped Ibexa LTS version to v3.3.43 |

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-core.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-core.md)
- [Spezifikation des SQLite-Installers](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2024-03](monate/2024-03.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-11](monate/2024-11.md), [2025-04](monate/2025-04.md), [2025-06](monate/2025-06.md), [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
