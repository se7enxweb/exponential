# birthday (Datentyp Geburtstag): Chronik

Der Datentyp Geburtstag wechselte im Januar 2024 den Vendor; im September 2026 wurde sein Export-Handler korrigiert, sodass er keine Anfrage mehr beendet, und am 2. Oktober erhielt er die fehlende `ezinfo.php`. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/birthday.md).

Diese Seite führt **jede der 7 Änderungen** des Repositorys `birthday` zwischen 2024-01-28 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/birthday.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/birthday.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/birthday.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Fehlerbehebung | 2 |
| Werkzeuge | 1 |
| Release | 2 |
| ohne Nutzen für Anwender | 2 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-01-28 | 1.3.0 | [`ecc405e`](https://github.com/se7enxweb/birthday/commit/ecc405e) |
| 2026-09-14 | 1.3.1 | [`69f435b`](https://github.com/se7enxweb/birthday/commit/69f435b) |
| 2026-10-02 | 1.3.2 | [`3132895`](https://github.com/se7enxweb/birthday/commit/3132895) |

## Zeitleiste

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/birthday.md#2024-01-3-änderungen).

- 2024-01-28 [`8fbe590`](https://github.com/se7enxweb/birthday/commit/8fbe590) (ohne Nutzen für Anwender) Added github funding information
- 2024-01-28 [`ecc405e`](https://github.com/se7enxweb/birthday/commit/ecc405e) (Werkzeuge) Update composer.json changed package vendor name **Release 1.3.0.**
- 2024-01-28 [`d1f9e9c`](https://github.com/se7enxweb/birthday/commit/d1f9e9c) (Release) Update extension.xml upated version number

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/birthday.md#2026-03-1-änderung).

- 2026-03-02 [`a81888a`](https://github.com/se7enxweb/birthday/commit/a81888a) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/birthday.md#2026-09-1-änderung).

- 2026-09-14 [`69f435b`](https://github.com/se7enxweb/birthday/commit/69f435b) (Fehlerbehebung) Fixed: Fixed the csv export handler, which ended any request that loaded it. **Release 1.3.1.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/birthday.md#2026-10-2-änderungen).

- 2026-10-02 [`a467a63`](https://github.com/se7enxweb/birthday/commit/a467a63) (Fehlerbehebung) Fixed: Added ezinfo.php, which the extension lacked, and corrected extension.xml, which named the product by its old name, gave the license as GPL 2.0 and pointed to a retired website, so that the about page and the upgrade checks show the extension's name, version, license and website.
- 2026-10-02 [`3132895`](https://github.com/se7enxweb/birthday/commit/3132895) (Release) Version 1.3.2 **Release 1.3.2.**

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/birthday.md)
- [Release Notes](../../../changelogs/extensions/birthday.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/birthday.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
