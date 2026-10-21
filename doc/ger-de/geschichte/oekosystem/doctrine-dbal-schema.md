# doctrine-dbal-schema: Geschichte des Plattform-Repositorys

Die Geschichte von `doctrine-dbal-schema`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Framework-Forks). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 2 Änderungen von 2025-07-01 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Schicht für den Schema-Import über mehrere Datenbanksysteme, die der Installer verwendet.

## Wie es mit Exponential zusammenhängt

Fork mit Unterstützung für PHP 8.1 und einem Replace-Shim.

## Was Anwender davon haben

Schema-Import auf aktuellem PHP; der Installer des Kernels 3.x hängt davon ab.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/doctrine-dbal-schema
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 1 |
| Releases | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-07-01 | v1.0.10 | `3291540` | Update composer.json increased php version support |
| 2026-04-12 | v1.0.11 | `8c2a9dc` | add replace shim for ezsystems/* original package |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2025-07-01 `3291540` Release: Update composer.json increased php version support

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `8c2a9dc` BC: add replace shim for ezsystems/* original package

## Verwandte Seiten

- [Framework-Forks](../../funktionen/6.0/plattform-php85-framework-forks.md)
- [Release-Changelog](../../../changelogs/extensions/doctrine-dbal-schema.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/doctrine-dbal-schema.md)
- Plattform-Ökosystem nach Monat: [2025-07](monate/2025-07.md), [2026-04](monate/2026-04.md)
