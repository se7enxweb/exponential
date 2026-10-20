# ezplatform-matrix-fieldtype: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-matrix-fieldtype`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-09-27 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Feldtyp Matrix (Tabelle).

## Wie es mit Exponential zusammenhängt

Auf se7enxweb umbenannt, mit Replace-Shim.

## Was Anwender davon haben

Matrix-Felder lassen sich auf aktuellem PHP installieren.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-matrix-fieldtype
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 2 |
| Werkzeuge | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.2.12 | `2076682` | Update package names to use se7enxweb prefix |
| 2026-04-12 | v2.2.13 | `be2bdb4` | add replace shim for ezsystems/* original package |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2025-09-27 `79894d3` Werkzeuge: Update package name and dependencies in composer.json

### Weitere Änderungen am Fork (1)

- 2025-09-28 `c442d53` Fehlerbehebung: Update ezplatform-graphql version to 2.3.18

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-28 `2076682` BC: Update package names to use se7enxweb prefix

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `be2bdb4` BC: add replace shim for ezsystems/* original package

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-matrix-fieldtype.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-matrix-fieldtype.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
