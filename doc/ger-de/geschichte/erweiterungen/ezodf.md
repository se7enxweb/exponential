# ezodf (Import und Export von OpenDocument): Chronik

Import und Export von OpenDocument wurden Anfang 2024 neu paketiert und im September und Oktober 2026 für dauerhaft laufende Worker, Übersetzungen und den Namen Exponential vorbereitet. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/ezodf.md).

Diese Seite führt **jede der 23 Änderungen** des Repositorys `ezodf` zwischen 2023-12-23 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/ezodf.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/ezodf.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/ezodf.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 4 |
| Fehlerbehebung | 1 |
| Dokumentation | 4 |
| Werkzeuge | 5 |
| Release | 5 |
| ohne Nutzen für Anwender | 4 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`7cc8e8c`](https://github.com/se7enxweb/ezodf/commit/7cc8e8c) |
| 2024-01-29 | v6.0.1 | [`342e62c`](https://github.com/se7enxweb/ezodf/commit/342e62c) |
| 2026-09-22 | v6.1.0 | [`89a9aa6`](https://github.com/se7enxweb/ezodf/commit/89a9aa6) |
| 2026-09-27 | v6.1.1 | [`da0bf5a`](https://github.com/se7enxweb/ezodf/commit/da0bf5a) |
| 2026-09-27 | v6.1.2 | [`86dabcd`](https://github.com/se7enxweb/ezodf/commit/86dabcd) |
| 2026-09-27 | v6.1.3 | [`5028742`](https://github.com/se7enxweb/ezodf/commit/5028742) |
| 2026-09-30 | v6.1.4 | [`026cb59`](https://github.com/se7enxweb/ezodf/commit/026cb59) |
| 2026-10-02 | v6.1.5 | [`68af781`](https://github.com/se7enxweb/ezodf/commit/68af781) |
| 2026-10-02 | v6.1.6 | [`7e61b8c`](https://github.com/se7enxweb/ezodf/commit/7e61b8c) |

## Zeitleiste

### 2023-12

Der Monat über alle Erweiterungen: [Dezember 2023](monate/2023-12.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezodf.md#2023-12-2-änderungen).

- 2023-12-23 [`9e436c1`](https://github.com/se7enxweb/ezodf/commit/9e436c1) (Werkzeuge) Update composer.json switched package vendor
- 2023-12-24 [`677ec72`](https://github.com/se7enxweb/ezodf/commit/677ec72) (ohne Nutzen für Anwender) Create FUNDING.yml

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezodf.md#2024-01-5-änderungen).

- 2024-01-28 [`c495be7`](https://github.com/se7enxweb/ezodf/commit/c495be7) (Werkzeuge) Update composer.json changed package name to reflect change in maintainer
- 2024-01-28 [`95da383`](https://github.com/se7enxweb/ezodf/commit/95da383) (Werkzeuge) Update composer.json changed contributors credit link
- 2024-01-28 [`7cc8e8c`](https://github.com/se7enxweb/ezodf/commit/7cc8e8c) (ohne Nutzen für Anwender) Merge pull request #1 from a contributor **Release v6.0.0.**
- 2024-01-28 [`5d7dfeb`](https://github.com/se7enxweb/ezodf/commit/5d7dfeb) (ohne Nutzen für Anwender) Updated github funding information
- 2024-01-29 [`342e62c`](https://github.com/se7enxweb/ezodf/commit/342e62c) (Werkzeuge) Update composer.json switched package vendor **Release v6.0.1.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezodf.md#2026-03-1-änderung).

- 2026-03-02 [`60bda6b`](https://github.com/se7enxweb/ezodf/commit/60bda6b) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezodf.md#2026-09-10-änderungen).

- 2026-09-22 [`89a9aa6`](https://github.com/se7enxweb/ezodf/commit/89a9aa6) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v6.1.0.**
- 2026-09-27 [`c1e1d80`](https://github.com/se7enxweb/ezodf/commit/c1e1d80) (Funktion) Visible texts and their translations name Exponential
- 2026-09-27 [`ca6c597`](https://github.com/se7enxweb/ezodf/commit/ca6c597) (Funktion) Templates write <br> as HTML5 does
- 2026-09-27 [`5481bf5`](https://github.com/se7enxweb/ezodf/commit/5481bf5) (Funktion) ezinfo.php reports the extension's name, version, copyright and license
- 2026-09-27 [`da0bf5a`](https://github.com/se7enxweb/ezodf/commit/da0bf5a) (Release) Version 6.1.1 **Release v6.1.1.**
- 2026-09-27 [`a73cebf`](https://github.com/se7enxweb/ezodf/commit/a73cebf) (Dokumentation) extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version)
- 2026-09-27 [`86dabcd`](https://github.com/se7enxweb/ezodf/commit/86dabcd) (Release) Version 6.1.2 **Release v6.1.2.**
- 2026-09-27 [`5028742`](https://github.com/se7enxweb/ezodf/commit/5028742) (Dokumentation) The extension states its version, license and website **Release v6.1.3.**
- 2026-09-30 [`b96e87c`](https://github.com/se7enxweb/ezodf/commit/b96e87c) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`026cb59`](https://github.com/se7enxweb/ezodf/commit/026cb59) (Release) Version 6.1.4 **Release v6.1.4.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezodf.md#2026-10-5-änderungen).

- 2026-10-02 [`c6d3f3f`](https://github.com/se7enxweb/ezodf/commit/c6d3f3f) (Werkzeuge) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`91ae3d2`](https://github.com/se7enxweb/ezodf/commit/91ae3d2) (Dokumentation) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`68af781`](https://github.com/se7enxweb/ezodf/commit/68af781) (Release) Version 6.1.5 **Release v6.1.5.**
- 2026-10-02 [`fcd590d`](https://github.com/se7enxweb/ezodf/commit/fcd590d) (Funktion) English and German translations for every string the admin showed untranslated
- 2026-10-02 [`7e61b8c`](https://github.com/se7enxweb/ezodf/commit/7e61b8c) (Release) Version 6.1.6 **Release v6.1.6.**

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/ezodf.md)
- [Release Notes](../../../changelogs/extensions/ezodf.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/ezodf.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
