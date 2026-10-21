# nxc_powercontent (Inhalte aus Code und REST): Chronik

Powercontent wurde im Oktober 2024 für die REST-Erweiterung erweitert, im Dezember 2025 für PHP 8.5 korrigiert, erhielt im Juni 2026 Aktionen zum Kopieren und Verbergen, im Juli 2026 eine Fetch-Funktion für Klassenlisten und im Oktober 2026 eine Korrektur des PDF-Exports. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/nxc_powercontent.md).

Diese Seite führt **jede der 12 Änderungen** des Repositorys `nxc_powercontent` zwischen 2024-10-29 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/nxc_powercontent.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/nxc_powercontent.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/nxc_powercontent.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 5 |
| Fehlerbehebung | 2 |
| Dokumentation | 1 |
| Release | 3 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-10-29 | v1.1.0 | [`58c1fc4`](https://github.com/se7enxweb/nxc_powercontent/commit/58c1fc4) |
| 2026-06-21 | v1.2.0 | [`85f728e`](https://github.com/se7enxweb/nxc_powercontent/commit/85f728e) |
| 2026-07-12 | v1.3.0 | [`5bbe3d2`](https://github.com/se7enxweb/nxc_powercontent/commit/5bbe3d2) |
| 2026-09-22 | v1.4.0 | [`3d0e28a`](https://github.com/se7enxweb/nxc_powercontent/commit/3d0e28a) |
| 2026-10-02 | v1.4.1 | [`c43b6af`](https://github.com/se7enxweb/nxc_powercontent/commit/c43b6af) |
| 2026-10-02 | v1.4.2 | [`739ebfd`](https://github.com/se7enxweb/nxc_powercontent/commit/739ebfd) |
| 2026-10-02 | v1.4.3 | [`038c6e9`](https://github.com/se7enxweb/nxc_powercontent/commit/038c6e9) |

## Zeitleiste

### 2024-10

Der Monat über alle Erweiterungen: [Oktober 2024](monate/2024-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2024-10-1-änderung).

- 2024-10-29 [`58c1fc4`](https://github.com/se7enxweb/nxc_powercontent/commit/58c1fc4) (Funktion) Extend and refactor solution for use within a rest api extension ezprestapi for v2 legacy rest crud calls functionality **Release v1.1.0.**

### 2025-12

Der Monat über alle Erweiterungen: [Dezember 2025](monate/2025-12.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2025-12-1-änderung).

- 2025-12-29 [`e554e30`](https://github.com/se7enxweb/nxc_powercontent/commit/e554e30) (Fehlerbehebung) Updated class method to provide nullable type bugfix required for php 8.5 support. Enhancement

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2026-03-1-änderung).

- 2026-03-02 [`976044c`](https://github.com/se7enxweb/nxc_powercontent/commit/976044c) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-06

Der Monat über alle Erweiterungen: [Juni 2026](monate/2026-06.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2026-06-1-änderung).

- 2026-06-21 [`85f728e`](https://github.com/se7enxweb/nxc_powercontent/commit/85f728e) (Funktion) Add Copy and Hide/Unhide action handlers to content/action.php **Release v1.2.0.**

### 2026-07

Der Monat über alle Erweiterungen: [Juli 2026](monate/2026-07.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2026-07-1-änderung).

- 2026-07-12 [`5bbe3d2`](https://github.com/se7enxweb/nxc_powercontent/commit/5bbe3d2) (Funktion) feat(nxc_powercontent): add class_list fetch function and update related methods for broader compatibility **Release v1.3.0.**

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2026-09-1-änderung).

- 2026-09-22 [`3d0e28a`](https://github.com/se7enxweb/nxc_powercontent/commit/3d0e28a) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.4.0.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/nxc_powercontent.md#2026-10-6-änderungen).

- 2026-10-02 [`d55466a`](https://github.com/se7enxweb/nxc_powercontent/commit/d55466a) (Dokumentation) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`c43b6af`](https://github.com/se7enxweb/nxc_powercontent/commit/c43b6af) (Release) Version 1.4.1 **Release v1.4.1.**
- 2026-10-02 [`dbb4c21`](https://github.com/se7enxweb/nxc_powercontent/commit/dbb4c21) (Funktion) The PDF export no longer stops with a fatal error: the content cache info is read from an object instance, as PHP 8 requires
- 2026-10-02 [`739ebfd`](https://github.com/se7enxweb/nxc_powercontent/commit/739ebfd) (Release) Version 1.4.2 **Release v1.4.2.**
- 2026-10-02 [`23a8bc3`](https://github.com/se7enxweb/nxc_powercontent/commit/23a8bc3) (Funktion) content/edit declares its redirect helper only once, so a persistent PHP worker can run it more than once
- 2026-10-02 [`038c6e9`](https://github.com/se7enxweb/nxc_powercontent/commit/038c6e9) (Release) Version 1.4.3 **Release v1.4.3.**

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/nxc_powercontent.md)
- [Release Notes](../../../changelogs/extensions/nxc_powercontent.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/nxc_powercontent.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
