# enhancedselection2 (Datentyp Auswahl): Chronik

Der Auswahl-Datentyp, der Bezeichner speichert, wechselte 2024 den Vendor und erhielt Ende September und im Oktober 2026 eine Korrektur für PostgreSQL, Übersetzungen und eine Reparatur seiner Templates für die Mehrfachauswahl. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/enhancedselection2.md).

Diese Seite führt **jede der 15 Änderungen** des Repositorys `enhancedselection2` zwischen 2024-03-06 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/enhancedselection2.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/enhancedselection2.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/enhancedselection2.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 4 |
| Fehlerbehebung | 1 |
| Dokumentation | 1 |
| Werkzeuge | 4 |
| Release | 4 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-03-06 | 2.1.1 | [`9bc66e1`](https://github.com/se7enxweb/enhancedselection2/commit/9bc66e1) |
| 2024-03-06 | 2.1.2 | [`4a50d0e`](https://github.com/se7enxweb/enhancedselection2/commit/4a50d0e) |
| 2026-09-27 | 2.1.3 | [`98febe4`](https://github.com/se7enxweb/enhancedselection2/commit/98febe4) |
| 2026-09-28 | 2.1.4 | [`a9a3342`](https://github.com/se7enxweb/enhancedselection2/commit/a9a3342) |
| 2026-09-30 | 2.1.5 | [`c434950`](https://github.com/se7enxweb/enhancedselection2/commit/c434950) |
| 2026-10-02 | 2.1.6 | [`2fdaddf`](https://github.com/se7enxweb/enhancedselection2/commit/2fdaddf) |
| 2026-10-02 | 2.1.7 | [`ec64b8b`](https://github.com/se7enxweb/enhancedselection2/commit/ec64b8b) |

## Zeitleiste

### 2024-03

Der Monat über alle Erweiterungen: [März 2024](monate/2024-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/enhancedselection2.md#2024-03-2-änderungen).

- 2024-03-06 [`9bc66e1`](https://github.com/se7enxweb/enhancedselection2/commit/9bc66e1) (Werkzeuge) Update composer.json switched package vendor **Release 2.1.1.**
- 2024-03-06 [`4a50d0e`](https://github.com/se7enxweb/enhancedselection2/commit/4a50d0e) (Werkzeuge) Update composer.json switched installer package vendor **Release 2.1.2.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/enhancedselection2.md#2026-03-1-änderung).

- 2026-03-02 [`92b30f8`](https://github.com/se7enxweb/enhancedselection2/commit/92b30f8) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/enhancedselection2.md#2026-09-6-änderungen).

- 2026-09-27 [`98febe4`](https://github.com/se7enxweb/enhancedselection2/commit/98febe4) (Funktion) ezinfo.php states the extension's name, version, copyright, license and website **Release 2.1.3.**
- 2026-09-28 [`e6afa97`](https://github.com/se7enxweb/enhancedselection2/commit/e6afa97) (Fehlerbehebung) Fixed: Selections of an attribute that has no id yet are not looked up, and ids are compared as integers, so PostgreSQL installs the content
- 2026-09-28 [`1e4df6b`](https://github.com/se7enxweb/enhancedselection2/commit/1e4df6b) (Funktion) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`a9a3342`](https://github.com/se7enxweb/enhancedselection2/commit/a9a3342) (Release) Version 2.1.4 **Release 2.1.4.**
- 2026-09-30 [`fecc9b7`](https://github.com/se7enxweb/enhancedselection2/commit/fecc9b7) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`c434950`](https://github.com/se7enxweb/enhancedselection2/commit/c434950) (Release) Version 2.1.5 **Release 2.1.5.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/enhancedselection2.md#2026-10-6-änderungen).

- 2026-10-02 [`998dc8f`](https://github.com/se7enxweb/enhancedselection2/commit/998dc8f) (Werkzeuge) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`23745c4`](https://github.com/se7enxweb/enhancedselection2/commit/23745c4) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`c32095c`](https://github.com/se7enxweb/enhancedselection2/commit/c32095c) (Funktion) The edit and collect templates of a multiple selection parse again: a stray closing parenthesis made the template parser reject the checkbox list, so the edit form of a class using it showed no fields
- 2026-10-02 [`2fdaddf`](https://github.com/se7enxweb/enhancedselection2/commit/2fdaddf) (Release) Version 2.1.6 **Release 2.1.6.**
- 2026-10-02 [`16a2231`](https://github.com/se7enxweb/enhancedselection2/commit/16a2231) (Werkzeuge) The commands start through the shared command helpers
- 2026-10-02 [`ec64b8b`](https://github.com/se7enxweb/enhancedselection2/commit/ec64b8b) (Release) Version 2.1.7 **Release 2.1.7.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/enhancedselection2.md)
- [Release Notes](../../../changelogs/extensions/enhancedselection2.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/enhancedselection2.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
