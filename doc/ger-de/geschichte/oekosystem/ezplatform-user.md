# ezplatform-user: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-user`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-09-28 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Benutzer-Bundle (Anmeldung, Registrierung, Profil).

## Wie es mit Exponential zusammenhängt

Auf se7enxweb umbenannt, mit Versionsbedingung für PHP 8.5.

## Was Anwender davon haben

Benutzerfunktionen unter PHP 8.5.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-user
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 3 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.13 | `2b88e8c` | Update package names and PHP version requirements |
| 2026-03-25 | 2.3.13.1 | `c30a5c7` | extend PHP constraint to ^8.5 for se7enxweb fork 2.3.x branch |
| 2026-04-11 | v2.3.14 | `fb47d83` | add replace shim for ezsystems counterpart package |
| 2026-04-12 | v2.3.15 | `aa19600` | replace uses wildcard '*' not self.version |

## Änderungen des se7enxweb-Teams, nach Thema

### Replace-Deklarationen für das Upstream-Paket (2)

- 2026-04-11 `fb47d83` BC: add replace shim for ezsystems counterpart package
- 2026-04-12 `aa19600` BC: replace uses wildcard '*' not self.version

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-28 `2b88e8c` BC: Update package names and PHP version requirements

### Weitere Änderungen am Fork (1)

- 2026-03-25 `c30a5c7` Fehlerbehebung: extend PHP constraint to ^8.5 for se7enxweb fork 2.3.x branch

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-user.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-user.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
