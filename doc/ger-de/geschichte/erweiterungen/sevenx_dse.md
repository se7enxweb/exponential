# sevenx_dse (Database Source Editor): Chronik

Der Database Source Editor wurde am 21. April 2026 importiert (1.0.0) und im September für dauerhaft laufende Worker (eine neue Nonce je Anfrage, ein neuer Worker nach jeder Anfrage an das Dashboard) und für seinen Menübezeichner korrigiert. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/sevenx_dse.md).

Diese Seite führt **jede der 11 Änderungen** des Repositorys `sevenx_dse` zwischen 2026-04-21 und 2026-09-30 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/sevenx_dse.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/sevenx_dse.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/sevenx_dse.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 1 |
| Fehlerbehebung | 4 |
| Sicherheit | 1 |
| Dokumentation | 2 |
| Release | 2 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2026-04-23 | v1.0.0 | [`baa6a74`](https://github.com/se7enxweb/sevenx_dse/commit/baa6a74) |
| 2026-09-22 | v1.1.0 | [`e84b3d1`](https://github.com/se7enxweb/sevenx_dse/commit/e84b3d1) |
| 2026-09-24 | v1.1.1 | [`8274510`](https://github.com/se7enxweb/sevenx_dse/commit/8274510) |
| 2026-09-27 | v1.1.2 | [`420424c`](https://github.com/se7enxweb/sevenx_dse/commit/420424c) |
| 2026-09-29 | v1.1.3 | [`a02495d`](https://github.com/se7enxweb/sevenx_dse/commit/a02495d) |
| 2026-09-30 | v1.1.4 | [`8203c12`](https://github.com/se7enxweb/sevenx_dse/commit/8203c12) |

## Zeitleiste

### 2026-04

Der Monat über alle Erweiterungen: [April 2026](monate/2026-04.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/sevenx_dse.md#2026-04-2-änderungen).

- 2026-04-21 [`f320455`](https://github.com/se7enxweb/sevenx_dse/commit/f320455) (Funktion) Initial Import of 7x Database Source Editor Exponential Extension sevenx_dse.
- 2026-04-23 [`baa6a74`](https://github.com/se7enxweb/sevenx_dse/commit/baa6a74) (Dokumentation) Add screenshots section to README **Release v1.0.0.**

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/sevenx_dse.md#2026-09-9-änderungen).

- 2026-09-22 [`e84b3d1`](https://github.com/se7enxweb/sevenx_dse/commit/e84b3d1) (Fehlerbehebung) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.1.0.**
- 2026-09-24 [`84688cc`](https://github.com/se7enxweb/sevenx_dse/commit/84688cc) (Sicherheit) Fixed: Fixed the AdminNeo content security policy nonce staying the same for every request a persistent worker serves.
- 2026-09-24 [`8274510`](https://github.com/se7enxweb/sevenx_dse/commit/8274510) (Fehlerbehebung) Fixed: Fixed a second DSE dashboard request in the same persistent worker dying on AdminNeo redeclaring its classes. **Release v1.1.1.**
- 2026-09-27 [`420424c`](https://github.com/se7enxweb/sevenx_dse/commit/420424c) (Fehlerbehebung) Fixed: The ezinfo.php and extension.xml report the release version and name the license in full, so the about page shows them **Release v1.1.2.**
- 2026-09-28 [`a8c104f`](https://github.com/se7enxweb/sevenx_dse/commit/a8c104f) (ohne Nutzen für Anwender) GitHub funding metadata, the same as the other se7enxweb packages
- 2026-09-29 [`fdadaae`](https://github.com/se7enxweb/sevenx_dse/commit/fdadaae) (Fehlerbehebung) Fixed: The navigation part has its own identifier and the menu texts are translated
- 2026-09-29 [`a02495d`](https://github.com/se7enxweb/sevenx_dse/commit/a02495d) (Release) Version 1.1.3 **Release v1.1.3.**
- 2026-09-30 [`c2c8e9a`](https://github.com/se7enxweb/sevenx_dse/commit/c2c8e9a) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`8203c12`](https://github.com/se7enxweb/sevenx_dse/commit/8203c12) (Release) Version 1.1.4 **Release v1.1.4.**

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/sevenx_dse.md)
- [Release Notes](../../../changelogs/extensions/sevenx_dse.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/sevenx_dse.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
