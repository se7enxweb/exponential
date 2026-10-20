# git_manager (git dashboard and backups): chronicle

The git dashboard and backup manager was published in 2024 (documentation, composer) and became a full backup manager in June 2026 (2.0.1). In September 2026 it gained push, remotes, submodules and hardening of backups; on 2 October the Upstream card showed where an installation stands against its branch. See the [feature page](../../features/6.0/extensions/git_manager.md).

This page lists **every one of the 40 changes** of the repository `git_manager` between 2024-01-28 and 2026-10-02, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/git_manager.md); what each release contains is in the [release notes](../../changelogs/extensions/git_manager.md); how to use the extension is on its [feature page](../../features/6.0/extensions/git_manager.md).

| Kind | Changes |
|---|---|
| feature | 13 |
| security | 6 |
| upgrade note | 1 |
| docs | 3 |
| tooling | 3 |
| release | 12 |
| no user benefit | 2 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-06-21 | v2.0.1 | [`c30a9ca`](https://github.com/se7enxweb/git_manager/commit/c30a9ca) |
| 2026-09-27 | v2.0.2 | [`2cac461`](https://github.com/se7enxweb/git_manager/commit/2cac461) |
| 2026-09-28 | v2.0.3 | [`bd0dd1a`](https://github.com/se7enxweb/git_manager/commit/bd0dd1a) |
| 2026-09-28 | v2.0.4 | [`5e2d525`](https://github.com/se7enxweb/git_manager/commit/5e2d525) |
| 2026-09-28 | v2.0.5 | [`361c6b7`](https://github.com/se7enxweb/git_manager/commit/361c6b7) |
| 2026-09-28 | v2.0.6 | [`64458d3`](https://github.com/se7enxweb/git_manager/commit/64458d3) |
| 2026-09-28 | v2.0.7 | [`beb3591`](https://github.com/se7enxweb/git_manager/commit/beb3591) |
| 2026-09-29 | v2.0.8 | [`922e502`](https://github.com/se7enxweb/git_manager/commit/922e502) |
| 2026-09-29 | v2.0.9 | [`14cb63c`](https://github.com/se7enxweb/git_manager/commit/14cb63c) |
| 2026-09-29 | v2.0.10 | [`8ac0f4d`](https://github.com/se7enxweb/git_manager/commit/8ac0f4d) |
| 2026-09-29 | v2.0.11 | [`b38b90a`](https://github.com/se7enxweb/git_manager/commit/b38b90a) |
| 2026-10-02 | v2.0.12 | [`00497e4`](https://github.com/se7enxweb/git_manager/commit/00497e4) |
| 2026-10-02 | v2.0.13 | [`66d1c1c`](https://github.com/se7enxweb/git_manager/commit/66d1c1c) |
| 2026-10-02 | v2.0.14 | [`e33cffe`](https://github.com/se7enxweb/git_manager/commit/e33cffe) |

## Timeline

### 2024-01

The month across all extensions: [January 2024](months/2024-01.md). [Ledger of this month](../ledger/git_manager.md#2024-01-1-changes).

- 2024-01-28 [`301b48e`](https://github.com/se7enxweb/git_manager/commit/301b48e) (no user benefit) Added github funding information

### 2024-10

The month across all extensions: [October 2024](months/2024-10.md). [Ledger of this month](../ledger/git_manager.md#2024-10-2-changes).

- 2024-10-18 [`f8b6a50`](https://github.com/se7enxweb/git_manager/commit/f8b6a50) (docs) Added extension documentation, license, composer support and extension.xml and sponsorship info
- 2024-10-18 [`bc1ad40`](https://github.com/se7enxweb/git_manager/commit/bc1ad40) (tooling) Refined composer.json syntax

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/git_manager.md#2026-03-1-changes).

- 2026-03-02 [`a501db3`](https://github.com/se7enxweb/git_manager/commit/a501db3) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-06

The month across all extensions: [June 2026](months/2026-06.md). [Ledger of this month](../ledger/git_manager.md#2026-06-3-changes).

- 2026-06-21 [`c30a9ca`](https://github.com/se7enxweb/git_manager/commit/c30a9ca) (feature) Add Complete Backup Manager System with AGPL-Compatible Releases and License Bundling **Release v2.0.1.**
- 2026-06-22 [`5022a7c`](https://github.com/se7enxweb/git_manager/commit/5022a7c) (docs) Updated ezinfo.php and extension.xml version file. Doc.
- 2026-06-22 [`3b8a634`](https://github.com/se7enxweb/git_manager/commit/3b8a634) (feature) Update project name from Exponential to Exponential / Exponential

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/git_manager.md#2026-09-27-changes).

- 2026-09-27 [`2cac461`](https://github.com/se7enxweb/git_manager/commit/2cac461) (docs) The extension names its license as GNU General Public License v2.0 (or any later version), in version 2.0.2 **Release v2.0.2.**
- 2026-09-28 [`606296e`](https://github.com/se7enxweb/git_manager/commit/606296e) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`bd0dd1a`](https://github.com/se7enxweb/git_manager/commit/bd0dd1a) (release) Version 2.0.3 **Release v2.0.3.**
- 2026-09-28 [`06cdbdc`](https://github.com/se7enxweb/git_manager/commit/06cdbdc) (upgrade note) The backup view git_manager/dump to git_manager/backup, its policy function dump to backup, and the Setup menu link Dump assets to Backup
- 2026-09-28 [`d1ccf54`](https://github.com/se7enxweb/git_manager/commit/d1ccf54) (feature) bin/php/upgrade-policy-dump-to-backup.php renames the role policies git_manager/dump to git_manager/backup
- 2026-09-28 [`5e2d525`](https://github.com/se7enxweb/git_manager/commit/5e2d525) (release) Version 2.0.4 **Release v2.0.4.**
- 2026-09-28 [`ac672ab`](https://github.com/se7enxweb/git_manager/commit/ac672ab) (security) Fixed: The commit log filter is quoted for the shell
- 2026-09-28 [`676184e`](https://github.com/se7enxweb/git_manager/commit/676184e) (feature) The dashboard and commit details are redesigned, with every action and field name kept
- 2026-09-28 [`361c6b7`](https://github.com/se7enxweb/git_manager/commit/361c6b7) (release) Version 2.0.5 **Release v2.0.5.**
- 2026-09-28 [`f671c23`](https://github.com/se7enxweb/git_manager/commit/f671c23) (feature) The backup page loads no external stylesheet or script
- 2026-09-28 [`f00b7eb`](https://github.com/se7enxweb/git_manager/commit/f00b7eb) (feature) Push to a remote from the dashboard, never forced, behind a policy of its own
- 2026-09-28 [`64458d3`](https://github.com/se7enxweb/git_manager/commit/64458d3) (release) Version 2.0.6 **Release v2.0.6.**
- 2026-09-28 [`956839a`](https://github.com/se7enxweb/git_manager/commit/956839a) (feature) Add, edit, rename and remove remotes, in the dashboard and on the command line
- 2026-09-28 [`1c0b45a`](https://github.com/se7enxweb/git_manager/commit/1c0b45a) (feature) The commit log is folded by default and shows what is not pushed yet
- 2026-09-28 [`beb3591`](https://github.com/se7enxweb/git_manager/commit/beb3591) (release) Version 2.0.7 **Release v2.0.7.**
- 2026-09-29 [`a018d42`](https://github.com/se7enxweb/git_manager/commit/a018d42) (feature) List, add, edit, update and remove submodules, in the dashboard and on the command line
- 2026-09-29 [`4fb2587`](https://github.com/se7enxweb/git_manager/commit/4fb2587) (security) Fixed: Branch names and commit hashes reach git only when valid and quoted
- 2026-09-29 [`9a8c6c9`](https://github.com/se7enxweb/git_manager/commit/9a8c6c9) (security) Fixed: Backups are their owner's only, and the database password is never on a command line
- 2026-09-29 [`25dfaf0`](https://github.com/se7enxweb/git_manager/commit/25dfaf0) (security) Fixed: The AGPL dump leaves out every table with secrets or personal data, and keeps remote_ids
- 2026-09-29 [`2c30f91`](https://github.com/se7enxweb/git_manager/commit/2c30f91) (security) Fixed: The backup page escapes descriptions, timestamps, file names and messages
- 2026-09-29 [`36a5df4`](https://github.com/se7enxweb/git_manager/commit/36a5df4) (security) backup-create.php takes the passphrase from a file, the environment or a hidden prompt
- 2026-09-29 [`922e502`](https://github.com/se7enxweb/git_manager/commit/922e502) (release) Version 2.0.8 **Release v2.0.8.**
- 2026-09-29 [`e2df984`](https://github.com/se7enxweb/git_manager/commit/e2df984) (feature) The "Add a submodule" section is folded by default and remembers whether it was opened
- 2026-09-29 [`14cb63c`](https://github.com/se7enxweb/git_manager/commit/14cb63c) (release) Version 2.0.9 **Release v2.0.9.**
- 2026-09-29 [`8ac0f4d`](https://github.com/se7enxweb/git_manager/commit/8ac0f4d) (release) Version 2.0.10 **Release v2.0.10.**
- 2026-09-29 [`07b92e5`](https://github.com/se7enxweb/git_manager/commit/07b92e5) (feature) The top menu tab, its tooltip and the Setup menu entries have German translations
- 2026-09-29 [`b38b90a`](https://github.com/se7enxweb/git_manager/commit/b38b90a) (release) Version 2.0.11 **Release v2.0.11.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/git_manager.md#2026-10-6-changes).

- 2026-10-02 [`3a3b464`](https://github.com/se7enxweb/git_manager/commit/3a3b464) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`00497e4`](https://github.com/se7enxweb/git_manager/commit/00497e4) (release) Version 2.0.12 **Release v2.0.12.**
- 2026-10-02 [`dee7143`](https://github.com/se7enxweb/git_manager/commit/dee7143) (tooling) The commands start through the shared command helpers
- 2026-10-02 [`66d1c1c`](https://github.com/se7enxweb/git_manager/commit/66d1c1c) (release) Version 2.0.13 **Release v2.0.13.**
- 2026-10-02 [`8bd3c14`](https://github.com/se7enxweb/git_manager/commit/8bd3c14) (feature) The dashboard's Upstream card shows where the installation stands against its branch on origin
- 2026-10-02 [`e33cffe`](https://github.com/se7enxweb/git_manager/commit/e33cffe) (release) Version 2.0.14 **Release v2.0.14.**

## Related pages

- [Feature page](../../features/6.0/extensions/git_manager.md)
- [Release notes](../../changelogs/extensions/git_manager.md)
- [Change ledger](../ledger/git_manager.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
