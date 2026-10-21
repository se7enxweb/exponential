# Änderungsverzeichnis: syndication

Jede Änderung an `syndication` seit Beginn der se7enxweb-Ära, die älteste zuerst: 19 Änderungen an 230 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 16 |
| Hinzugefügt | 2 |
| Sonstiges | 1 |

## 2025-09 (9 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-13 | `cc6b2a7` | Hinzugefügt | Added: Initial patched import of mostly working extension ported from php5 to php8. Requires some kernel changes atm. Inital Import. | 85 | +9167 / −0 |  |
| 2025-09-13 | `6942894` | Aktualisiert | Updated: Bugfixes for php8 support. Bugfixes. | 9 | +10 / −10 |  |
| 2025-09-13 | `c401648` | Aktualisiert | Updated: Mass commit of debug statement removal with appologies to others affected. These changes represent a working upgraded to php8.3 syndication extension that has been tested and proven to once again work as designed with minor issues (feature click specific rot in admin views). Stable Changeset. Bugfixes + Code Standards. | 9 | +26 / −29 |  |
| 2025-09-13 | `fd374b3` | Aktualisiert | Updated: Bugfix to remove testing die statement. Bugfix. | 1 | +1 / −3 |  |
| 2025-09-13 | `2757d0a` | Aktualisiert | Update composer.json updated description | 1 | +1 / −1 |  |
| 2025-09-13 | `018b318` | Aktualisiert | Update ezinfo.php version bump for upgraded and tested solution | 1 | +3 / −2 | v1.1.0 |
| 2025-09-18 | `6a80394` | Aktualisiert | Updated: Massive rewrite of extension documentation. Reorganization of file placement. Switched to markdown for docs. Doc. | 7 | +103 / −11 |  |
| 2025-09-18 | `c0a4b13` | Aktualisiert | Updated: Expanded install doc to mention kernel patch provided and suggest a new feature for the extension to provide in the future. Doc | 1 | +3 / −1 |  |
| 2025-09-18 | `c325218` | Aktualisiert | Updated: Branding change. Doc. | 1 | +1 / −1 |  |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `96e2b3e` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-07 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-19 | `cb365d3` | Aktualisiert | Updated: Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. | 67 | +88 / −30 |  |
| 2026-07-19 | `d6bfbd6` | Aktualisiert | Updated: Version Bump | 1 | +1 / −1 |  |
| 2026-07-19 | `42ff4f3` | Aktualisiert | Updated: Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. | 1 | +37 / −0 | v1.2.0 |

## 2026-09 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-22 | `ee6e4c3` | Aktualisiert | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +4 / −0 | v1.3.0 |

## 2026-10 (5 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-02 | `58dbf3c` | Hinzugefügt | Added: Added share/db_schema.dba, the syndication tables in the engine-neutral schema format, so that an installer can create them on every database Exponential supports and not only on MySQL. | 1 | +796 / −0 |  |
| 2026-10-02 | `687bdae` | Aktualisiert | Updated: Version 1.3.1, with ezinfo.php's keys in the form the about page reads and an extension.xml carrying the version, license and website. | 2 | +15 / −5 | v1.3.1 |
| 2026-10-02 | `9bbf346` | Aktualisiert | Updated: The commands and cronjob parts list a description of what they do | 2 | +226 / −224 |  |
| 2026-10-02 | `2593c19` | Aktualisiert | Updated: The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices | 37 | +37 / −0 |  |
| 2026-10-02 | `09c7779` | Aktualisiert | Updated: Version 1.3.2 | 2 | +2 / −2 | v1.3.2 origin/main origin/HEAD |
