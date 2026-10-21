# Änderungsverzeichnis: git_manager

Jede Änderung an `git_manager` seit Beginn der se7enxweb-Ära, die älteste zuerst: 40 Änderungen an 172 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 30 |
| Hinzugefügt | 8 |
| Sonstiges | 1 |
| Umbenannt | 1 |

## 2024-01 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `301b48e` | Hinzugefügt | Added: Added github funding information | 1 | +3 / −0 |  |

## 2024-10 (2 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-10-18 | `f8b6a50` | Hinzugefügt | Added: Added extension documentation, license, composer support and extension.xml and sponsorship info | 6 | +744 / −4 |  |
| 2024-10-18 | `bc1ad40` | Aktualisiert | Updated: Refined composer.json syntax | 1 | +1 / −2 |  |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `a501db3` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-06 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-06-21 | `c30a9ca` | Hinzugefügt | Add Complete Backup Manager System with AGPL-Compatible Releases and License Bundling | 22 | +5855 / −44 | v2.0.1 |
| 2026-06-22 | `5022a7c` | Aktualisiert | Updated: Updated ezinfo.php and extension.xml version file. Doc. | 2 | +4 / −4 |  |
| 2026-06-22 | `3b8a634` | Aktualisiert | Update project name from Exponential to Exponential / Exponential | 1 | +1 / −1 |  |

## 2026-09 (27 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-27 | `2cac461` | Aktualisiert | Updated: The extension names its license as GNU General Public License v2.0 (or any later version), in version 2.0.2 | 2 | +4 / −4 | v2.0.2 |
| 2026-09-28 | `606296e` | Aktualisiert | Updated: Every visible text of the extension is a translation string, with German | 6 | +907 / −61 |  |
| 2026-09-28 | `bd0dd1a` | Aktualisiert | Updated: Version 2.0.3 | 2 | +2 / −2 | v2.0.3 |
| 2026-09-28 | `06cdbdc` | Umbenannt | Renamed: The backup view git_manager/dump to git_manager/backup, its policy function dump to backup, and the Setup menu link Dump assets to Backup | 6 | +336 / −297 |  |
| 2026-09-28 | `d1ccf54` | Hinzugefügt | Added: bin/php/upgrade-policy-dump-to-backup.php renames the role policies git_manager/dump to git_manager/backup | 1 | +56 / −0 |  |
| 2026-09-28 | `5e2d525` | Aktualisiert | Updated: Version 2.0.4 | 2 | +2 / −2 | v2.0.4 |
| 2026-09-28 | `ac672ab` | Aktualisiert | Fixed: The commit log filter is quoted for the shell | 1 | +9 / −6 |  |
| 2026-09-28 | `676184e` | Aktualisiert | Updated: The dashboard and commit details are redesigned, with every action and field name kept | 8 | +894 / −349 |  |
| 2026-09-28 | `361c6b7` | Aktualisiert | Updated: Version 2.0.5 | 2 | +2 / −2 | v2.0.5 |
| 2026-09-28 | `f671c23` | Aktualisiert | Updated: The backup page loads no external stylesheet or script | 1 | +0 / −2 |  |
| 2026-09-28 | `f00b7eb` | Hinzugefügt | Added: Push to a remote from the dashboard, never forced, behind a policy of its own | 9 | +424 / −3 |  |
| 2026-09-28 | `64458d3` | Aktualisiert | Updated: Version 2.0.6 | 2 | +2 / −2 | v2.0.6 |
| 2026-09-28 | `956839a` | Hinzugefügt | Added: Add, edit, rename and remove remotes, in the dashboard and on the command line | 4 | +246 / −1 |  |
| 2026-09-28 | `1c0b45a` | Aktualisiert | Updated: The commit log is folded by default and shows what is not pushed yet | 7 | +535 / −39 |  |
| 2026-09-28 | `beb3591` | Aktualisiert | Updated: Version 2.0.7 | 2 | +2 / −2 | v2.0.7 |
| 2026-09-29 | `a018d42` | Hinzugefügt | Added: List, add, edit, update and remove submodules, in the dashboard and on the command line | 10 | +785 / −55 |  |
| 2026-09-29 | `4fb2587` | Aktualisiert | Fixed: Branch names and commit hashes reach git only when valid and quoted | 1 | +33 / −8 |  |
| 2026-09-29 | `9a8c6c9` | Aktualisiert | Fixed: Backups are their owner's only, and the database password is never on a command line | 4 | +131 / −31 |  |
| 2026-09-29 | `25dfaf0` | Aktualisiert | Fixed: The AGPL dump leaves out every table with secrets or personal data, and keeps remote_ids | 1 | +50 / −9 |  |
| 2026-09-29 | `2c30f91` | Aktualisiert | Fixed: The backup page escapes descriptions, timestamps, file names and messages | 1 | +9 / −9 |  |
| 2026-09-29 | `36a5df4` | Aktualisiert | Updated: backup-create.php takes the passphrase from a file, the environment or a hidden prompt | 1 | +43 / −6 |  |
| 2026-09-29 | `922e502` | Aktualisiert | Updated: Version 2.0.8 | 2 | +2 / −2 | v2.0.8 |
| 2026-09-29 | `e2df984` | Aktualisiert | Updated: The "Add a submodule" section is folded by default and remembers whether it was opened | 3 | +32 / −1 |  |
| 2026-09-29 | `14cb63c` | Aktualisiert | Updated: Version 2.0.9 | 2 | +2 / −2 | v2.0.9 |
| 2026-09-29 | `8ac0f4d` | Aktualisiert | Updated: Version 2.0.10 | 2 | +2 / −2 | v2.0.10 |
| 2026-09-29 | `07b92e5` | Aktualisiert | Updated: The top menu tab, its tooltip and the Setup menu entries have German translations | 3 | +87 / −0 |  |
| 2026-09-29 | `b38b90a` | Aktualisiert | Updated: Version 2.0.11 | 2 | +2 / −2 | v2.0.11 |

## 2026-10 (6 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-02 | `3a3b464` | Aktualisiert | Updated: The command line scripts, cronjob parts and module views are classes the files call | 24 | +2038 / −1488 |  |
| 2026-10-02 | `00497e4` | Aktualisiert | Updated: Version 2.0.12 | 2 | +2 / −2 | v2.0.12 |
| 2026-10-02 | `dee7143` | Aktualisiert | Updated: The commands start through the shared command helpers | 7 | +17 / −23 |  |
| 2026-10-02 | `66d1c1c` | Aktualisiert | Updated: Version 2.0.13 | 2 | +2 / −2 | v2.0.13 |
| 2026-10-02 | `8bd3c14` | Hinzugefügt | Added: The dashboard's Upstream card shows where the installation stands against its branch on origin | 14 | +1303 / −0 |  |
| 2026-10-02 | `e33cffe` | Aktualisiert | Updated: Version 2.0.14 | 2 | +2 / −2 | v2.0.14 origin/master origin/HEAD |
