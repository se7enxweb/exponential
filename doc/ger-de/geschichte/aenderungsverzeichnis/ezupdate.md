# Änderungsverzeichnis: ezupdate

Jede Änderung an `ezupdate` seit Beginn der se7enxweb-Ära, die älteste zuerst: 34 Änderungen an 193 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 24 |
| Hinzugefügt | 7 |
| Sonstiges | 1 |
| Entfernt | 1 |
| Umbenannt | 1 |

## 2024-11 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-11-02 | `ed04f45` | Hinzugefügt | Added: Initial Commit of Composer Update Exponential Module GUI Features | 23 | +1545 / −0 | v1.0.1 |
| 2024-11-02 | `3cfa7b6` | Aktualisiert | Updated: Revised documentation | 1 | +1 / −1 |  |
| 2024-11-02 | `ffbcbcf` | Aktualisiert | Update ezupdate.ini.append make setting more generic for all users | 1 | +4 / −3 |  |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `d062589` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-06 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-06-21 | `3028031` | Hinzugefügt | Add colored console output to Composer update dashboard | 2 | +94 / −2 |  |

## 2026-09 (18 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-13 | `9e6bedd` | Aktualisiert | Updated: Expanded warning to instruct users to backup first! | 1 | +5 / −2 |  |
| 2026-09-13 | `cc1e7e9` | Aktualisiert | Updated: Updated documentation for v1.0.2 | 3 | +7 / −7 | v1.0.2 |
| 2026-09-22 | `5584bf6` | Aktualisiert | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +4 / −0 | v1.1.0 |
| 2026-09-27 | `9345f63` | Aktualisiert | Updated: The extension names its license as GNU General Public License v2.0 (or any later version) and states version 1.1.1 | 2 | +4 / −4 | v1.1.1 |
| 2026-09-28 | `704c071` | Aktualisiert | Updated: The extension carries translations of the texts it marks, with German | 4 | +119 / −0 |  |
| 2026-09-28 | `1ccff8d` | Aktualisiert | Updated: Version 1.1.2 | 2 | +2 / −2 | v1.1.2 |
| 2026-09-28 | `74a163b` | Entfernt | Removed: The dump and show views, the commit template and the Dump assets link, which belonged to git_manager | 3 | +0 / −79 |  |
| 2026-09-28 | `635d024` | Aktualisiert | Updated: Composer runs without a shell, with a time limit and safe defaults, from a dashboard in the current admin design | 8 | +1171 / −347 |  |
| 2026-09-28 | `56b9c46` | Hinzugefügt | Added: packagist.org browsing, guarded installs, Composer and Exponential package servers, live output of background runs, and a command line | 24 | +4207 / −48 |  |
| 2026-09-28 | `1daf4ab` | Aktualisiert | Updated: Version 1.1.3 | 2 | +14 / −14 | v1.1.3 |
| 2026-09-28 | `0f902d3` | Hinzugefügt | Added: Run again for every Composer run, and Run it for real for a dry run | 10 | +266 / −52 |  |
| 2026-09-28 | `3c4bf3d` | Aktualisiert | Updated: Version 1.1.4 | 2 | +2 / −2 | v1.1.4 |
| 2026-09-28 | `e2d5e64` | Aktualisiert | Updated: The job page says why it cannot follow a run instead of retrying in silence | 6 | +109 / −3 |  |
| 2026-09-28 | `d254abd` | Aktualisiert | Updated: Version 1.1.5 | 2 | +2 / −2 | v1.1.5 |
| 2026-09-28 | `5701600` | Hinzugefügt | Added: Funding, the list composer fund prints, in the admin and on the command line | 13 | +737 / −3 |  |
| 2026-09-28 | `b3f18a4` | Aktualisiert | Updated: Version 1.1.6 | 2 | +2 / −2 | v1.1.6 |
| 2026-09-29 | `ac171db` | Aktualisiert | Updated: The navigation part and the Setup menu entry have German translations | 3 | +42 / −0 |  |
| 2026-09-29 | `146f4a3` | Aktualisiert | Updated: Version 1.1.7 | 2 | +2 / −2 | v1.1.7 |

## 2026-10 (11 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `aab54bc` | Hinzugefügt | Added: Installed packages, and every page in one new design | 20 | +1953 / −634 |  |
| 2026-10-01 | `192d020` | Aktualisiert | Updated: The new texts in English and German, and in the untranslated catalogue | 3 | +1116 / −0 |  |
| 2026-10-01 | `5e96bc7` | Umbenannt | Renamed: The license texts are Markdown files, LICENSE.md and doc/LICENSE.md | 4 | +362 / −680 |  |
| 2026-10-01 | `9afcc14` | Hinzugefügt | Added: Documentation for users, administrators and contributors | 21 | +1434 / −109 |  |
| 2026-10-01 | `3ca2cbb` | Aktualisiert | Updated: Version 1.1.8 | 2 | +2 / −2 | v1.1.8 |
| 2026-10-01 | `b759785` | Aktualisiert | Updated: Pull requests go against main, the branch's new name | 1 | +1 / −1 |  |
| 2026-10-02 | `effbbb9` | Aktualisiert | Updated: The command line scripts, cronjob parts and module views are classes the files call | 18 | +1310 / −971 |  |
| 2026-10-02 | `fcfed0d` | Aktualisiert | Updated: The commands and cronjob parts list a description of what they do | 1 | +1 / −0 |  |
| 2026-10-02 | `781c999` | Aktualisiert | Updated: Version 1.1.9 | 2 | +2 / −2 | v1.1.9 |
| 2026-10-02 | `c1c5e24` | Aktualisiert | Updated: The commands start through the shared command helpers | 1 | +3 / −5 |  |
| 2026-10-02 | `d5edb6f` | Aktualisiert | Updated: Version 1.1.10 | 2 | +2 / −2 | v1.1.10 origin/main origin/HEAD |
