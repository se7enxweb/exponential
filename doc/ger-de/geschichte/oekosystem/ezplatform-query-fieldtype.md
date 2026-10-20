# ezplatform-query-fieldtype: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-query-fieldtype`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 3 Änderungen von 2025-09-27 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Feldtyp, der eine Inhaltsabfrage speichert.

## Wie es mit Exponential zusammenhängt

Auf se7enxweb umbenannt, mit Replace-Shim.

## Was Anwender davon haben

Abfragefelder lassen sich auf aktuellem PHP installieren.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-query-fieldtype
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 2 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-27 | v2.3.10 | `0d3008c` | Update package name and dependencies to se7enxweb |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-27 `0d3008c` BC: Update package name and dependencies to se7enxweb

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `9c34928` BC: add replace shim for ezsystems/ezplatform-query-fieldtype

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-query-fieldtype.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-query-fieldtype.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
