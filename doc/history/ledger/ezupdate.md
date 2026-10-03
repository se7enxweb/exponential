# Change ledger: ezupdate

Every change made to `ezupdate` since the se7enxweb era began, oldest first: 34 changes touching 193 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 24 |
| Added | 7 |
| Other | 1 |
| Removed | 1 |
| Renamed | 1 |

## 2024-11 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-11-02 | `ed04f45` | Added | Added: Initial Commit of Composer Update Exponential Module GUI Features | 23 | +1545 / −0 | v1.0.1 |
| 2024-11-02 | `3cfa7b6` | Updated | Updated: Revised documentation | 1 | +1 / −1 |  |
| 2024-11-02 | `ffbcbcf` | Updated | Update ezupdate.ini.append make setting more generic for all users | 1 | +4 / −3 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `d062589` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-06 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-06-21 | `3028031` | Added | Add colored console output to Composer update dashboard | 2 | +94 / −2 |  |

## 2026-09 (18 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-13 | `9e6bedd` | Updated | Updated: Expanded warning to instruct users to backup first! | 1 | +5 / −2 |  |
| 2026-09-13 | `cc1e7e9` | Updated | Updated: Updated documentation for v1.0.2 | 3 | +7 / −7 | v1.0.2 |
| 2026-09-22 | `5584bf6` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 1 | +4 / −0 | v1.1.0 |
| 2026-09-27 | `9345f63` | Updated | Updated: The extension names its license as GNU General Public License v2.0 (or any later version) and states version 1.1.1 | 2 | +4 / −4 | v1.1.1 |
| 2026-09-28 | `704c071` | Updated | Updated: The extension carries translations of the texts it marks, with German | 4 | +119 / −0 |  |
| 2026-09-28 | `1ccff8d` | Updated | Updated: Version 1.1.2 | 2 | +2 / −2 | v1.1.2 |
| 2026-09-28 | `74a163b` | Removed | Removed: The dump and show views, the commit template and the Dump assets link, which belonged to git_manager | 3 | +0 / −79 |  |
| 2026-09-28 | `635d024` | Updated | Updated: Composer runs without a shell, with a time limit and safe defaults, from a dashboard in the current admin design | 8 | +1171 / −347 |  |
| 2026-09-28 | `56b9c46` | Added | Added: packagist.org browsing, guarded installs, Composer and Exponential package servers, live output of background runs, and a command line | 24 | +4207 / −48 |  |
| 2026-09-28 | `1daf4ab` | Updated | Updated: Version 1.1.3 | 2 | +14 / −14 | v1.1.3 |
| 2026-09-28 | `0f902d3` | Added | Added: Run again for every Composer run, and Run it for real for a dry run | 10 | +266 / −52 |  |
| 2026-09-28 | `3c4bf3d` | Updated | Updated: Version 1.1.4 | 2 | +2 / −2 | v1.1.4 |
| 2026-09-28 | `e2d5e64` | Updated | Updated: The job page says why it cannot follow a run instead of retrying in silence | 6 | +109 / −3 |  |
| 2026-09-28 | `d254abd` | Updated | Updated: Version 1.1.5 | 2 | +2 / −2 | v1.1.5 |
| 2026-09-28 | `5701600` | Added | Added: Funding, the list composer fund prints, in the admin and on the command line | 13 | +737 / −3 |  |
| 2026-09-28 | `b3f18a4` | Updated | Updated: Version 1.1.6 | 2 | +2 / −2 | v1.1.6 |
| 2026-09-29 | `ac171db` | Updated | Updated: The navigation part and the Setup menu entry have German translations | 3 | +42 / −0 |  |
| 2026-09-29 | `146f4a3` | Updated | Updated: Version 1.1.7 | 2 | +2 / −2 | v1.1.7 |

## 2026-10 (11 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `aab54bc` | Added | Added: Installed packages, and every page in one new design | 20 | +1953 / −634 |  |
| 2026-10-01 | `192d020` | Updated | Updated: The new texts in English and German, and in the untranslated catalogue | 3 | +1116 / −0 |  |
| 2026-10-01 | `5e96bc7` | Renamed | Renamed: The license texts are Markdown files, LICENSE.md and doc/LICENSE.md | 4 | +362 / −680 |  |
| 2026-10-01 | `9afcc14` | Added | Added: Documentation for users, administrators and contributors | 21 | +1434 / −109 |  |
| 2026-10-01 | `3ca2cbb` | Updated | Updated: Version 1.1.8 | 2 | +2 / −2 | v1.1.8 |
| 2026-10-01 | `b759785` | Updated | Updated: Pull requests go against main, the branch's new name | 1 | +1 / −1 |  |
| 2026-10-02 | `effbbb9` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 18 | +1310 / −971 |  |
| 2026-10-02 | `fcfed0d` | Updated | Updated: The commands and cronjob parts list a description of what they do | 1 | +1 / −0 |  |
| 2026-10-02 | `781c999` | Updated | Updated: Version 1.1.9 | 2 | +2 / −2 | v1.1.9 |
| 2026-10-02 | `c1c5e24` | Updated | Updated: The commands start through the shared command helpers | 1 | +3 / −5 |  |
| 2026-10-02 | `d5edb6f` | Updated | Updated: Version 1.1.10 | 2 | +2 / −2 | v1.1.10 origin/main origin/HEAD |
