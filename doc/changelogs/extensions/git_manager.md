# git_manager (git dashboard and backups): release notes

What each release of `git_manager` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/git_manager.md); the story is in the [chronicle](../../history/extensions/git_manager.md).

## v2.0.14 (2026-10-02)

**Added**

- The dashboard's Upstream card shows where the installation stands against its branch on origin ([`8bd3c14`](https://github.com/se7enxweb/git_manager/commit/8bd3c14))

1 version, merge or metadata commit not listed.

## v2.0.13 (2026-10-02)

**Maintenance, documentation and packaging**

- The commands start through the shared command helpers ([`dee7143`](https://github.com/se7enxweb/git_manager/commit/dee7143))

1 version, merge or metadata commit not listed.

## v2.0.12 (2026-10-02)

**Maintenance, documentation and packaging**

- The command line scripts, cronjob parts and module views are classes the files call ([`3a3b464`](https://github.com/se7enxweb/git_manager/commit/3a3b464))

1 version, merge or metadata commit not listed.

## v2.0.11 (2026-09-29)

**Updated**

- The top menu tab, its tooltip and the Setup menu entries have German translations ([`07b92e5`](https://github.com/se7enxweb/git_manager/commit/07b92e5))

1 version, merge or metadata commit not listed.

## v2.0.10 (2026-09-29)

1 version, merge or metadata commit not listed.

## v2.0.9 (2026-09-29)

**Updated**

- The "Add a submodule" section is folded by default and remembers whether it was opened ([`e2df984`](https://github.com/se7enxweb/git_manager/commit/e2df984))

1 version, merge or metadata commit not listed.

## v2.0.8 (2026-09-29)

**Added**

- List, add, edit, update and remove submodules, in the dashboard and on the command line ([`a018d42`](https://github.com/se7enxweb/git_manager/commit/a018d42))

**Updated**

- Fixed: Branch names and commit hashes reach git only when valid and quoted ([`4fb2587`](https://github.com/se7enxweb/git_manager/commit/4fb2587))
- Fixed: Backups are their owner's only, and the database password is never on a command line ([`9a8c6c9`](https://github.com/se7enxweb/git_manager/commit/9a8c6c9))
- Fixed: The AGPL dump leaves out every table with secrets or personal data, and keeps remote_ids ([`25dfaf0`](https://github.com/se7enxweb/git_manager/commit/25dfaf0))
- Fixed: The backup page escapes descriptions, timestamps, file names and messages ([`2c30f91`](https://github.com/se7enxweb/git_manager/commit/2c30f91))
- backup-create.php takes the passphrase from a file, the environment or a hidden prompt ([`36a5df4`](https://github.com/se7enxweb/git_manager/commit/36a5df4))

1 version, merge or metadata commit not listed.

## v2.0.7 (2026-09-28)

**Added**

- Add, edit, rename and remove remotes, in the dashboard and on the command line ([`956839a`](https://github.com/se7enxweb/git_manager/commit/956839a))

**Updated**

- The commit log is folded by default and shows what is not pushed yet ([`1c0b45a`](https://github.com/se7enxweb/git_manager/commit/1c0b45a))

1 version, merge or metadata commit not listed.

## v2.0.6 (2026-09-28)

**Added**

- Push to a remote from the dashboard, never forced, behind a policy of its own ([`f00b7eb`](https://github.com/se7enxweb/git_manager/commit/f00b7eb))

**Updated**

- The backup page loads no external stylesheet or script ([`f671c23`](https://github.com/se7enxweb/git_manager/commit/f671c23))

1 version, merge or metadata commit not listed.

## v2.0.5 (2026-09-28)

**Updated**

- Fixed: The commit log filter is quoted for the shell ([`ac672ab`](https://github.com/se7enxweb/git_manager/commit/ac672ab))
- The dashboard and commit details are redesigned, with every action and field name kept ([`676184e`](https://github.com/se7enxweb/git_manager/commit/676184e))

1 version, merge or metadata commit not listed.

## v2.0.4 (2026-09-28)

**Added**

- bin/php/upgrade-policy-dump-to-backup.php renames the role policies git_manager/dump to git_manager/backup ([`d1ccf54`](https://github.com/se7enxweb/git_manager/commit/d1ccf54))

**Renamed**

- The backup view git_manager/dump to git_manager/backup, its policy function dump to backup, and the Setup menu link Dump assets to Backup ([`06cdbdc`](https://github.com/se7enxweb/git_manager/commit/06cdbdc)) Upgrade note.

1 version, merge or metadata commit not listed.

## v2.0.3 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`606296e`](https://github.com/se7enxweb/git_manager/commit/606296e))

1 version, merge or metadata commit not listed.

## v2.0.2 (2026-09-27)

**Updated**

- Update project name from Exponential to Exponential / Exponential ([`3b8a634`](https://github.com/se7enxweb/git_manager/commit/3b8a634))

**Maintenance, documentation and packaging**

- Updated ezinfo.php and extension.xml version file. Doc. ([`5022a7c`](https://github.com/se7enxweb/git_manager/commit/5022a7c))
- The extension names its license as GNU General Public License v2.0 (or any later version), in version 2.0.2 ([`2cac461`](https://github.com/se7enxweb/git_manager/commit/2cac461))

## v2.0.1 (2026-06-21)

**Added**

- Add Complete Backup Manager System with AGPL-Compatible Releases and License Bundling ([`c30a9ca`](https://github.com/se7enxweb/git_manager/commit/c30a9ca))

**Maintenance, documentation and packaging**

- Added extension documentation, license, composer support and extension.xml and sponsorship info ([`f8b6a50`](https://github.com/se7enxweb/git_manager/commit/f8b6a50))
- Refined composer.json syntax ([`bc1ad40`](https://github.com/se7enxweb/git_manager/commit/bc1ad40))

2 version, merge or metadata commits not listed.

## Related

* [Feature page](../../features/6.0/extensions/git_manager.md)
* [Chronicle](../../history/extensions/git_manager.md)
* [Change ledger](../../history/ledger/git_manager.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
