# ezplatform-search: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-search`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-09-27 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Such-Bundle der Plattform.

## Wie es mit Exponential zusammenhängt

Auf den Vendor se7enxweb umbenannt.

## Was Anwender davon haben

Das Such-Bundle wird von se7enxweb installiert.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-search
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 2 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-27 | v1.2.8 | `32fd5cc` | Update package name and license in composer.json |

## Änderungen des se7enxweb-Teams, nach Thema

### Replace-Deklarationen für das Upstream-Paket (2)

- 2026-04-11 `72ad0cf` BC: add replace shim for ezsystems counterpart package
- 2026-04-12 `eeaa2ba` BC: replace uses wildcard '*' not self.version

### Composer-Abhängigkeiten (1)

- 2025-09-27 `32fd5cc` Release: Update package name and license in composer.json

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-search.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-search.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
