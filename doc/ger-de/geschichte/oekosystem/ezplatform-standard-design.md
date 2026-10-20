# ezplatform-standard-design: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-standard-design`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Administrationsoberfläche). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-07-01 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Standard-Design-Bundle von eZ Platform (Exponential Platform Standard Design Bundle).

## Wie es mit Exponential zusammenhängt

Auf den Vendor se7enxweb umbenannt, mit erweiterten PHP-Versionsbedingungen.

## Was Anwender davon haben

Das Design-Bundle für das Frontend lässt sich auf aktuellem PHP installieren.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-standard-design
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 1 |
| Releases | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-07-01 | v0.3.9 | `a1e5c4d` | Update composer.json increased package php version support |
| 2025-09-28 | v0.3.10 | `977038a` | Update package names and descriptions in composer.json |
| 2026-04-12 | v0.3.11 | `d99afe0` | add replace shim for ezsystems/* original package |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (3)

- 2025-07-01 `fe98823` Werkzeuge: Update composer.json switched package vendor name
- 2025-07-01 `a1e5c4d` Release: Update composer.json increased package php version support
- 2025-09-28 `977038a` Release: Update package names and descriptions in composer.json

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `d99afe0` BC: add replace shim for ezsystems/* original package

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Release-Changelog](../../../changelogs/extensions/ezplatform-standard-design.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-standard-design.md)
- Plattform-Ökosystem nach Monat: [2025-07](monate/2025-07.md), [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
