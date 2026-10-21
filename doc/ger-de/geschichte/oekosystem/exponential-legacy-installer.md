# exponential-legacy-installer: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-legacy-installer`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-08-25 bis 2026-06-19, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Composer-Plugin, das den Legacy-Kernel und Legacy-Erweiterungen installiert.

## Wie es mit Exponential zusammenhängt

Am 2026-06-19 für die Pfadnormalisierung von Composer 2.10 korrigiert.

## Was Anwender davon haben

composer install funktioniert weiter mit aktuellem Composer.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/exponential-legacy-installer
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 1 |
| Releases | 2 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-08-25 | 2.2.1 | `6e8cf01` | Update composer.json replaced package vendor and package name. Forking for changes. |
| 2026-04-11 | 2.2.2 | `d80adb2` | add replace shim for ezsystems counterpart package |
| 2026-06-19 | 2.2.3 | `bddac7c` | Fix Composer 2.10 path normalization failure in legacy kernel installer |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (2)

- 2025-08-25 `6e8cf01` Release: Update composer.json replaced package vendor and package name. Forking for changes.
- 2026-06-19 `bddac7c` Release: Fix Composer 2.10 path normalization failure in legacy kernel installer

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-11 `d80adb2` BC: add replace shim for ezsystems counterpart package

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)
- [Release-Changelog](../../../changelogs/extensions/exponential-legacy-installer.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-legacy-installer.md)
- [SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2025-08](monate/2025-08.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md), [2026-06](monate/2026-06.md)
