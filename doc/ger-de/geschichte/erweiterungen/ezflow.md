# ezflow (Seiten, Zonen und Blöcke): Chronik

eZ Flow erhielt im Dezember 2023 eine Fehlerbehebung für PHP 8.2, dann im September und Oktober 2026 Korrekturen für Oracle bei Block-Pools und Seitenblöcken sowie in 6.1.4 das Entfernen von YUI aus dem Seiten-Editor. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/ezflow.md).

Diese Seite führt **jede der 22 Änderungen** des Repositorys `ezflow` zwischen 2023-12-23 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/ezflow.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/ezflow.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/ezflow.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 6 |
| Fehlerbehebung | 4 |
| Upgrade-Hinweis | 1 |
| Dokumentation | 2 |
| Werkzeuge | 2 |
| Release | 4 |
| ohne Nutzen für Anwender | 3 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-01-28 | v6.0.0 | [`b13c0cb`](https://github.com/se7enxweb/ezflow/commit/b13c0cb) |
| 2024-01-28 | v6.0.1 | [`48be22e`](https://github.com/se7enxweb/ezflow/commit/48be22e) |
| 2024-01-29 | v6.0.2 | [`56db68c`](https://github.com/se7enxweb/ezflow/commit/56db68c) |
| 2026-09-22 | v6.1.0 | [`b7ac2eb`](https://github.com/se7enxweb/ezflow/commit/b7ac2eb) |
| 2026-09-27 | v6.1.1 | [`e8a191f`](https://github.com/se7enxweb/ezflow/commit/e8a191f) |
| 2026-09-27 | v6.1.2 | [`40be4b8`](https://github.com/se7enxweb/ezflow/commit/40be4b8) |
| 2026-09-30 | v6.1.3 | [`1424c02`](https://github.com/se7enxweb/ezflow/commit/1424c02) |
| 2026-10-02 | v6.1.4 | [`d78a00d`](https://github.com/se7enxweb/ezflow/commit/d78a00d) |
| 2026-10-02 | v6.1.5 | [`4f35239`](https://github.com/se7enxweb/ezflow/commit/4f35239) |

## Zeitleiste

### 2023-12

Der Monat über alle Erweiterungen: [Dezember 2023](monate/2023-12.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezflow.md#2023-12-3-änderungen).

- 2023-12-23 [`e593c90`](https://github.com/se7enxweb/ezflow/commit/e593c90) (Werkzeuge) Update composer.json switched package vendor
- 2023-12-24 [`8a05b14`](https://github.com/se7enxweb/ezflow/commit/8a05b14) (ohne Nutzen für Anwender) Create FUNDING.yml
- 2023-12-24 [`7470247`](https://github.com/se7enxweb/ezflow/commit/7470247) (Fehlerbehebung) Bugfix release of ezezflow composer to fix PHP 8.2 bugs

### 2024-01

Der Monat über alle Erweiterungen: [Januar 2024](monate/2024-01.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezflow.md#2024-01-4-änderungen).

- 2024-01-28 [`b13c0cb`](https://github.com/se7enxweb/ezflow/commit/b13c0cb) (ohne Nutzen für Anwender) Updated github funding information **Release v6.0.0.**
- 2024-01-28 [`d04d2d7`](https://github.com/se7enxweb/ezflow/commit/d04d2d7) (Funktion) Added documentation to repository. Mass update from parent repository ezflow-ezpackage
- 2024-01-28 [`48be22e`](https://github.com/se7enxweb/ezflow/commit/48be22e) (Funktion) Switched package vendor name **Release v6.0.1.**
- 2024-01-29 [`56db68c`](https://github.com/se7enxweb/ezflow/commit/56db68c) (Werkzeuge) Update composer.json switched homepage url **Release v6.0.2.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezflow.md#2026-03-1-änderung).

- 2026-03-02 [`b736ed2`](https://github.com/se7enxweb/ezflow/commit/b736ed2) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezflow.md#2026-09-10-änderungen).

- 2026-09-22 [`b7ac2eb`](https://github.com/se7enxweb/ezflow/commit/b7ac2eb) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v6.1.0.**
- 2026-09-27 [`ad3123d`](https://github.com/se7enxweb/ezflow/commit/ad3123d) (Funktion) Visible texts and their translations name Exponential
- 2026-09-27 [`4d45ca4`](https://github.com/se7enxweb/ezflow/commit/4d45ca4) (Funktion) Templates write <br> as HTML5 does
- 2026-09-27 [`a89d5a2`](https://github.com/se7enxweb/ezflow/commit/a89d5a2) (Funktion) ezinfo.php reports the extension's name, version, copyright and license
- 2026-09-27 [`e8a191f`](https://github.com/se7enxweb/ezflow/commit/e8a191f) (Release) Version 6.1.1 **Release v6.1.1.**
- 2026-09-27 [`06d8fd8`](https://github.com/se7enxweb/ezflow/commit/06d8fd8) (Dokumentation) extension.xml and ezinfo.php name the license in full, GNU General Public License v2.0 (or any later version)
- 2026-09-27 [`40be4b8`](https://github.com/se7enxweb/ezflow/commit/40be4b8) (Release) Version 6.1.2 **Release v6.1.2.**
- 2026-09-30 [`29077fe`](https://github.com/se7enxweb/ezflow/commit/29077fe) (Fehlerbehebung) Fixed: A block pool over its limit is trimmed on every database, Oracle included
- 2026-09-30 [`1f32a4d`](https://github.com/se7enxweb/ezflow/commit/1f32a4d) (Fehlerbehebung) Fixed: A page block is found in its page on every database, Oracle included
- 2026-09-30 [`1424c02`](https://github.com/se7enxweb/ezflow/commit/1424c02) (Release) Version 6.1.3 **Release v6.1.3.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/ezflow.md#2026-10-4-änderungen).

- 2026-10-02 [`d78a00d`](https://github.com/se7enxweb/ezflow/commit/d78a00d) (Upgrade-Hinweis) YUI from ezflow; the page editor, block tools, push to block, schedule dialog and timeline run on jQuery **Release v6.1.4.**
- 2026-10-02 [`adf61c1`](https://github.com/se7enxweb/ezflow/commit/adf61c1) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`8cce337`](https://github.com/se7enxweb/ezflow/commit/8cce337) (Dokumentation) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`4f35239`](https://github.com/se7enxweb/ezflow/commit/4f35239) (Release) Version 6.1.5 **Release v6.1.5.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/ezflow.md)
- [Release Notes](../../../changelogs/extensions/ezflow.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/ezflow.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
