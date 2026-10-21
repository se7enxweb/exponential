# ezjscore (JavaScript-Kern und Packer): Chronik

Das eigenständige Repository von ezjscore war seit 2013 leer. Am 1. Oktober 2026 wurde es wieder auf den Stand der Kopie im Kernel gebracht (1.4.0 mit jQuery 4), und am 2. Oktober wurde YUI entfernt (1.5.0) und die Funktionen für die Optionen der Tabelle der Unterelemente kamen hinzu. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/ezjscore.md).

Diese Seite führt **jede der 15 Änderungen** des Repositorys `ezjscore` zwischen 2024-01-28 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/ezjscore.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/ezjscore.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/ezjscore.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 3 |
| Fehlerbehebung | 1 |
| Upgrade-Hinweis | 2 |
| Dokumentation | 1 |
| Werkzeuge | 1 |
| Release | 5 |
| ohne Nutzen für Anwender | 2 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2026-10-01 | 1.4.0 | [`66d2310`](https://github.com/se7enxweb/ezjscore/commit/66d2310) |
| 2026-10-02 | 1.4.1 | [`5ee94f6`](https://github.com/se7enxweb/ezjscore/commit/5ee94f6) |
| 2026-10-02 | 1.5.0 | [`0544db8`](https://github.com/se7enxweb/ezjscore/commit/0544db8) |
| 2026-10-02 | 1.5.1 | [`eae7434`](https://github.com/se7enxweb/ezjscore/commit/eae7434) |
| 2026-10-02 | 1.5.2 | [`e2ea4db`](https://github.com/se7enxweb/ezjscore/commit/e2ea4db) |
| 2026-10-02 | 1.5.3 | [`9e8cfd0`](https://github.com/se7enxweb/ezjscore/commit/9e8cfd0) |
| 2026-10-02 | 1.5.4 | [`4eafcf0`](https://github.com/se7enxweb/ezjscore/commit/4eafcf0) |
| 2026-10-02 | 1.5.5 | [`1d48bd8`](https://github.com/se7enxweb/ezjscore/commit/1d48bd8) |

## Zeitleiste

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezjscore.md#2024-01-1-änderung).

- 2024-01-28 [`61006a2`](https://github.com/se7enxweb/ezjscore/commit/61006a2) (ohne Nutzen für Anwender) Added github funding information

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezjscore.md#2026-03-1-änderung).

- 2026-03-02 [`031a161`](https://github.com/se7enxweb/ezjscore/commit/031a161) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezjscore.md#2026-10-13-änderungen).

- 2026-10-01 [`66d2310`](https://github.com/se7enxweb/ezjscore/commit/66d2310) (Upgrade-Hinweis) Updated the repository to ezjscore 1.4.0, the version Exponential 6 ships in its kernel, with jQuery 4.0.0, jQuery Migrate 4.0.2 and jQuery UI 1.14.2, so that ezjscore can again be installed and followed on its own. **Release 1.4.0.**
- 2026-10-02 [`33c9efb`](https://github.com/se7enxweb/ezjscore/commit/33c9efb) (Fehlerbehebung) Fixed: The CSS packer keeps the space before a colon in selectors, where it is the descendant combinator
- 2026-10-02 [`5ee94f6`](https://github.com/se7enxweb/ezjscore/commit/5ee94f6) (Release) Version 1.4.1 **Release 1.4.1.**
- 2026-10-02 [`0544db8`](https://github.com/se7enxweb/ezjscore/commit/0544db8) (Upgrade-Hinweis) YUI; ezjscore loads jQuery and jQuery UI only **Release 1.5.0.**
- 2026-10-02 [`8a19de6`](https://github.com/se7enxweb/ezjscore/commit/8a19de6) (Werkzeuge) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`eae7434`](https://github.com/se7enxweb/ezjscore/commit/eae7434) (Release) Version 1.5.1 **Release 1.5.1.**
- 2026-10-02 [`f24bcfb`](https://github.com/se7enxweb/ezjscore/commit/f24bcfb) (Dokumentation) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`e2ea4db`](https://github.com/se7enxweb/ezjscore/commit/e2ea4db) (Release) Version 1.5.2 **Release 1.5.2.**
- 2026-10-02 [`9e8cfd0`](https://github.com/se7enxweb/ezjscore/commit/9e8cfd0) (Funktion) The expsubitems server functions that serve the admin subitems list's Table options **Release 1.5.3.**
- 2026-10-02 [`f68a5a6`](https://github.com/se7enxweb/ezjscore/commit/f68a5a6) (Funktion) Server function blocks in the settings for the subitems columns
- 2026-10-02 [`4eafcf0`](https://github.com/se7enxweb/ezjscore/commit/4eafcf0) (Release) Version 1.5.4 **Release 1.5.4.**
- 2026-10-02 [`eb438e1`](https://github.com/se7enxweb/ezjscore/commit/eb438e1) (Funktion) The server router lets expservices classes answer in their own envelope, errors included
- 2026-10-02 [`1d48bd8`](https://github.com/se7enxweb/ezjscore/commit/1d48bd8) (Release) Version 1.5.5 **Release 1.5.5.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Release Notes](../../../changelogs/extensions/ezjscore.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/ezjscore.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
