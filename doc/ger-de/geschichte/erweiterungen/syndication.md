# syndication (Syndikation von Inhalten): Chronik

Syndication wurde im September 2025 von 7x veröffentlicht, nachdem es von PHP 5 auf PHP 8 portiert und dokumentiert worden war, und im Juli 2026 um eine HTTP-Authentifizierung für Importe erweitert. Im Herbst kamen Sicherheit für dauerhaft laufende Worker und ein portables Schema hinzu. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/syndication.md).

Diese Seite führt **jede der 19 Änderungen** des Repositorys `syndication` zwischen 2025-09-13 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/syndication.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/syndication.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/syndication.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 3 |
| Fehlerbehebung | 4 |
| Sicherheit | 2 |
| Dokumentation | 4 |
| Werkzeuge | 1 |
| Release | 4 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2025-09-13 | v1.1.0 | [`018b318`](https://github.com/se7enxweb/syndication/commit/018b318) |
| 2026-07-19 | v1.2.0 | [`42ff4f3`](https://github.com/se7enxweb/syndication/commit/42ff4f3) |
| 2026-09-22 | v1.3.0 | [`ee6e4c3`](https://github.com/se7enxweb/syndication/commit/ee6e4c3) |
| 2026-10-02 | v1.3.1 | [`687bdae`](https://github.com/se7enxweb/syndication/commit/687bdae) |
| 2026-10-02 | v1.3.2 | [`09c7779`](https://github.com/se7enxweb/syndication/commit/09c7779) |

## Zeitleiste

### 2025-09

Der Monat über alle Erweiterungen: [September 2025](monate/2025-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/syndication.md#2025-09-9-änderungen).

- 2025-09-13 [`cc6b2a7`](https://github.com/se7enxweb/syndication/commit/cc6b2a7) (Funktion) Initial patched import of mostly working extension ported from php5 to php8. Requires some kernel changes atm. Inital Import.
- 2025-09-13 [`6942894`](https://github.com/se7enxweb/syndication/commit/6942894) (Fehlerbehebung) Bugfixes for php8 support. Bugfixes.
- 2025-09-13 [`c401648`](https://github.com/se7enxweb/syndication/commit/c401648) (Fehlerbehebung) Mass commit of debug statement removal with appologies to others affected. These changes represent a working upgraded to php8.3 syndication extension that has been tested and proven to once again work as designed with minor issues (feature click specific rot in admin views). Stable Changeset. Bugfixes + Code Standards.
- 2025-09-13 [`fd374b3`](https://github.com/se7enxweb/syndication/commit/fd374b3) (Fehlerbehebung) Bugfix to remove testing die statement. Bugfix.
- 2025-09-13 [`2757d0a`](https://github.com/se7enxweb/syndication/commit/2757d0a) (Werkzeuge) Update composer.json updated description
- 2025-09-13 [`018b318`](https://github.com/se7enxweb/syndication/commit/018b318) (Release) Update ezinfo.php version bump for upgraded and tested solution **Release v1.1.0.**
- 2025-09-18 [`6a80394`](https://github.com/se7enxweb/syndication/commit/6a80394) (Dokumentation) Massive rewrite of extension documentation. Reorganization of file placement. Switched to markdown for docs. Doc.
- 2025-09-18 [`c0a4b13`](https://github.com/se7enxweb/syndication/commit/c0a4b13) (Dokumentation) Expanded install doc to mention kernel patch provided and suggest a new feature for the extension to provide in the future. Doc
- 2025-09-18 [`c325218`](https://github.com/se7enxweb/syndication/commit/c325218) (Dokumentation) Branding change. Doc.

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/syndication.md#2026-03-1-änderung).

- 2026-03-02 [`96e2b3e`](https://github.com/se7enxweb/syndication/commit/96e2b3e) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-07

Der Monat über alle Erweiterungen: [Juli 2026](monate/2026-07.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/syndication.md#2026-07-3-änderungen).

- 2026-07-19 [`cb365d3`](https://github.com/se7enxweb/syndication/commit/cb365d3) (Sicherheit) Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements.
- 2026-07-19 [`d6bfbd6`](https://github.com/se7enxweb/syndication/commit/d6bfbd6) (Release) Version Bump
- 2026-07-19 [`42ff4f3`](https://github.com/se7enxweb/syndication/commit/42ff4f3) (Sicherheit) Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. **Release v1.2.0.**

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/syndication.md#2026-09-1-änderung).

- 2026-09-22 [`ee6e4c3`](https://github.com/se7enxweb/syndication/commit/ee6e4c3) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.3.0.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/syndication.md#2026-10-5-änderungen).

- 2026-10-02 [`58dbf3c`](https://github.com/se7enxweb/syndication/commit/58dbf3c) (Funktion) Added share/db_schema.dba, the syndication tables in the engine-neutral schema format, so that an installer can create them on every database Exponential supports and not only on MySQL.
- 2026-10-02 [`687bdae`](https://github.com/se7enxweb/syndication/commit/687bdae) (Release) Version 1.3.1, with ezinfo.php's keys in the form the about page reads and an extension.xml carrying the version, license and website. **Release v1.3.1.**
- 2026-10-02 [`9bbf346`](https://github.com/se7enxweb/syndication/commit/9bbf346) (Funktion) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`2593c19`](https://github.com/se7enxweb/syndication/commit/2593c19) (Dokumentation) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`09c7779`](https://github.com/se7enxweb/syndication/commit/09c7779) (Release) Version 1.3.2 **Release v1.3.2.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/syndication.md)
- [Release Notes](../../../changelogs/extensions/syndication.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/syndication.md)
- [Spezifikation](../../spezifikationen/6.0/syndikation.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
