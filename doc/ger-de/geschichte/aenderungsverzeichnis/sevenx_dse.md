# Änderungsverzeichnis: sevenx_dse

Jede Änderung an `sevenx_dse` seit Beginn der se7enxweb-Ära, die älteste zuerst: 11 Änderungen an 250 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 8 |
| Hinzugefügt | 2 |
| Sonstiges | 1 |

## 2026-04 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-21 | `f320455` | Sonstiges | Initial Import of 7x Database Source Editor Exponential Extension sevenx_dse. | 233 | +51337 / −0 |  |
| 2026-04-23 | `baa6a74` | Hinzugefügt | Add screenshots section to README | 1 | +11 / −0 | v1.0.0 |

## 2026-09 (9 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `e84b3d1` | Aktualisiert | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +23 / −19 | v1.1.0 |
| 2026-09-24 | `84688cc` | Aktualisiert | Fixed: Fixed the AdminNeo content security policy nonce staying the same for every request a persistent worker serves. | 1 | +5 / −5 |  |
| 2026-09-24 | `8274510` | Aktualisiert | Fixed: Fixed a second DSE dashboard request in the same persistent worker dying on AdminNeo redeclaring its classes. | 1 | +10 / −0 | v1.1.1 |
| 2026-09-27 | `420424c` | Aktualisiert | Fixed: The ezinfo.php and extension.xml report the release version and name the license in full, so the about page shows them | 2 | +4 / −4 | v1.1.2 |
| 2026-09-28 | `a8c104f` | Hinzugefügt | Added: GitHub funding metadata, the same as the other se7enxweb packages | 1 | +1 / −1 |  |
| 2026-09-29 | `fdadaae` | Aktualisiert | Fixed: The navigation part has its own identifier and the menu texts are translated | 5 | +127 / −1 |  |
| 2026-09-29 | `a02495d` | Aktualisiert | Updated: Version 1.1.3 | 2 | +2 / −2 | v1.1.3 |
| 2026-09-30 | `c2c8e9a` | Aktualisiert | Updated: The description calls the product Exponential | 1 | +1 / −1 |  |
| 2026-09-30 | `8203c12` | Aktualisiert | Updated: Version 1.1.4 | 2 | +2 / −2 | v1.1.4 origin/main origin/HEAD |
