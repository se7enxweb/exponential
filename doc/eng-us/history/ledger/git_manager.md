# Change ledger: git_manager

Every change made to `git_manager` since the se7enxweb era began, oldest first: 40 changes touching 172 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 30 |
| Added | 8 |
| Other | 1 |
| Renamed | 1 |

## 2024-01 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-01-28 | `301b48e` | Added | Added: Added github funding information | 1 | +3 / −0 |  |

## 2024-10 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2024-10-18 | `f8b6a50` | Added | Added: Added extension documentation, license, composer support and extension.xml and sponsorship info | 6 | +744 / −4 |  |
| 2024-10-18 | `bc1ad40` | Updated | Updated: Refined composer.json syntax | 1 | +1 / −2 |  |

## 2026-03 (1 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `a501db3` | Other | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-06 (3 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-06-21 | `c30a9ca` | Added | Add Complete Backup Manager System with AGPL-Compatible Releases and License Bundling | 22 | +5855 / −44 | v2.0.1 |
| 2026-06-22 | `5022a7c` | Updated | Updated: Updated ezinfo.php and extension.xml version file. Doc. | 2 | +4 / −4 |  |
| 2026-06-22 | `3b8a634` | Updated | Update project name from Exponential to Exponential / Exponential | 1 | +1 / −1 |  |

## 2026-09 (27 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-27 | `2cac461` | Updated | Updated: The extension names its license as GNU General Public License v2.0 (or any later version), in version 2.0.2 | 2 | +4 / −4 | v2.0.2 |
| 2026-09-28 | `606296e` | Updated | Updated: Every visible text of the extension is a translation string, with German | 6 | +907 / −61 |  |
| 2026-09-28 | `bd0dd1a` | Updated | Updated: Version 2.0.3 | 2 | +2 / −2 | v2.0.3 |
| 2026-09-28 | `06cdbdc` | Renamed | Renamed: The backup view git_manager/dump to git_manager/backup, its policy function dump to backup, and the Setup menu link Dump assets to Backup | 6 | +336 / −297 |  |
| 2026-09-28 | `d1ccf54` | Added | Added: bin/php/upgrade-policy-dump-to-backup.php renames the role policies git_manager/dump to git_manager/backup | 1 | +56 / −0 |  |
| 2026-09-28 | `5e2d525` | Updated | Updated: Version 2.0.4 | 2 | +2 / −2 | v2.0.4 |
| 2026-09-28 | `ac672ab` | Updated | Fixed: The commit log filter is quoted for the shell | 1 | +9 / −6 |  |
| 2026-09-28 | `676184e` | Updated | Updated: The dashboard and commit details are redesigned, with every action and field name kept | 8 | +894 / −349 |  |
| 2026-09-28 | `361c6b7` | Updated | Updated: Version 2.0.5 | 2 | +2 / −2 | v2.0.5 |
| 2026-09-28 | `f671c23` | Updated | Updated: The backup page loads no external stylesheet or script | 1 | +0 / −2 |  |
| 2026-09-28 | `f00b7eb` | Added | Added: Push to a remote from the dashboard, never forced, behind a policy of its own | 9 | +424 / −3 |  |
| 2026-09-28 | `64458d3` | Updated | Updated: Version 2.0.6 | 2 | +2 / −2 | v2.0.6 |
| 2026-09-28 | `956839a` | Added | Added: Add, edit, rename and remove remotes, in the dashboard and on the command line | 4 | +246 / −1 |  |
| 2026-09-28 | `1c0b45a` | Updated | Updated: The commit log is folded by default and shows what is not pushed yet | 7 | +535 / −39 |  |
| 2026-09-28 | `beb3591` | Updated | Updated: Version 2.0.7 | 2 | +2 / −2 | v2.0.7 |
| 2026-09-29 | `a018d42` | Added | Added: List, add, edit, update and remove submodules, in the dashboard and on the command line | 10 | +785 / −55 |  |
| 2026-09-29 | `4fb2587` | Updated | Fixed: Branch names and commit hashes reach git only when valid and quoted | 1 | +33 / −8 |  |
| 2026-09-29 | `9a8c6c9` | Updated | Fixed: Backups are their owner's only, and the database password is never on a command line | 4 | +131 / −31 |  |
| 2026-09-29 | `25dfaf0` | Updated | Fixed: The AGPL dump leaves out every table with secrets or personal data, and keeps remote_ids | 1 | +50 / −9 |  |
| 2026-09-29 | `2c30f91` | Updated | Fixed: The backup page escapes descriptions, timestamps, file names and messages | 1 | +9 / −9 |  |
| 2026-09-29 | `36a5df4` | Updated | Updated: backup-create.php takes the passphrase from a file, the environment or a hidden prompt | 1 | +43 / −6 |  |
| 2026-09-29 | `922e502` | Updated | Updated: Version 2.0.8 | 2 | +2 / −2 | v2.0.8 |
| 2026-09-29 | `e2df984` | Updated | Updated: The "Add a submodule" section is folded by default and remembers whether it was opened | 3 | +32 / −1 |  |
| 2026-09-29 | `14cb63c` | Updated | Updated: Version 2.0.9 | 2 | +2 / −2 | v2.0.9 |
| 2026-09-29 | `8ac0f4d` | Updated | Updated: Version 2.0.10 | 2 | +2 / −2 | v2.0.10 |
| 2026-09-29 | `07b92e5` | Updated | Updated: The top menu tab, its tooltip and the Setup menu entries have German translations | 3 | +87 / −0 |  |
| 2026-09-29 | `b38b90a` | Updated | Updated: Version 2.0.11 | 2 | +2 / −2 | v2.0.11 |

## 2026-10 (6 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-02 | `3a3b464` | Updated | Updated: The command line scripts, cronjob parts and module views are classes the files call | 24 | +2038 / −1488 |  |
| 2026-10-02 | `00497e4` | Updated | Updated: Version 2.0.12 | 2 | +2 / −2 | v2.0.12 |
| 2026-10-02 | `dee7143` | Updated | Updated: The commands start through the shared command helpers | 7 | +17 / −23 |  |
| 2026-10-02 | `66d1c1c` | Updated | Updated: Version 2.0.13 | 2 | +2 / −2 | v2.0.13 |
| 2026-10-02 | `8bd3c14` | Added | Added: The dashboard's Upstream card shows where the installation stands against its branch on origin | 14 | +1303 / −0 |  |
| 2026-10-02 | `e33cffe` | Updated | Updated: Version 2.0.14 | 2 | +2 / −2 | v2.0.14 origin/master origin/HEAD |
