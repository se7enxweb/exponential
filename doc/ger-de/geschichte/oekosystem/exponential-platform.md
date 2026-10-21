# exponential-platform: Geschichte des Plattform-Repositorys

Die Geschichte von `exponential-platform`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 11 Änderungen von 2025-09-28 bis 2026-04-08, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Distribution Exponential Platform 3.x (Symfony).

## Wie es mit Exponential zusammenhängt

Installation und Willkommensseite der Linie 3.2.x.

## Was Anwender davon haben

Composer-Projekt, das eine Website mit Exponential Platform 3.2.9 startet.

Dieses Repository ist ein Composer-Projekt (Typ `project`), keine Bibliothek; Sie legen also eine neue Website daraus an:

```bash
composer create-project se7enxweb/exponential-platform:3.2.x-dev my_project
cd my_project
```

Den anschließenden Datenbankschritt beschreibt die README des Repositorys (`.env.local`, dann `php bin/console exponential:install` oder der dokumentierte Import).

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 2 |
| Fehlerbehebungen | 4 |
| Dokumentation | 2 |
| Ohne Nutzen für Anwender | 3 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v3.2.9 | `7300c617` | Required config yaml content changes to install, boot welcome, edit content. Part 4 the next to last pass. Kernel via sysinfo admin reports version fo |

## Änderungen des se7enxweb-Teams, nach Thema

### Fehlerbehebungen (4)

- 2025-09-28 `20f563ab` Fehlerbehebung: Required config yaml content changes to install, boot welcome, edit content. Bugfixes.
- 2025-09-28 `889b2df5` Fehlerbehebung: Required config yaml content changes to install, boot welcome, edit content. Part 2. Bugfixes.
- 2025-09-28 `01f0f4d1` Fehlerbehebung: Required config yaml content changes to install, boot welcome, edit content. Part 3. Bugfixes.
- 2025-09-28 `7300c617` Fehlerbehebung: Required config yaml content changes to install, boot welcome, edit content. Part 4 the next to last pass. Kernel via sysinfo admin reports version fo

### Marke Exponential (2)

- 2025-09-28 `140bc09c` Funktion: Root changes required to install via composer, build dependencies, install db, edit default content and create folder content successfully. Rebranding
- 2025-09-28 `a2bf19e3` Funktion: Rebranding changes to update welcome_page.html.twig template file URLs to documentation to valid active links matching by context. Rebranding.

### Dokumentation (2)

- 2026-04-08 `14ee6186` Dokumentation: rewrite README, add INSTALL.md, update COPYRIGHT for Exponential Platform 3.2.9
- 2026-04-08 `3c177d17` Dokumentation: restoring original license terms in README

Außerdem: 3 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/exponential-platform.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/exponential-platform.md)
- [SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
