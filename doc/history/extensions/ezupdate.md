# ezupdate (updates and packages): chronicle

The Composer update screen of November 2024 became a package manager between 28 September and 2 October 2026: packagist.org browsing, guarded installs, package servers, live runs, funding, an installed packages inventory and documentation. See the [feature page](../../features/6.0/extensions/ezupdate.md).

This page lists **every one of the 34 changes** of the repository `ezupdate` between 2024-11-02 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/ezupdate.md); what each release contains is in the [release notes](../../changelogs/extensions/ezupdate.md); how to use the extension is on its [feature page](../../features/6.0/extensions/ezupdate.md).

| Kind | Changes |
|---|---|
| feature | 15 |
| fix | 1 |
| upgrade note | 1 |
| docs | 4 |
| tooling | 3 |
| release | 9 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2024-11-02 | v1.0.1 | [`ed04f45`](https://github.com/se7enxweb/ezupdate/commit/ed04f45) |
| 2026-09-13 | v1.0.2 | [`cc1e7e9`](https://github.com/se7enxweb/ezupdate/commit/cc1e7e9) |
| 2026-09-22 | v1.1.0 | [`5584bf6`](https://github.com/se7enxweb/ezupdate/commit/5584bf6) |
| 2026-09-27 | v1.1.1 | [`9345f63`](https://github.com/se7enxweb/ezupdate/commit/9345f63) |
| 2026-09-28 | v1.1.2 | [`1ccff8d`](https://github.com/se7enxweb/ezupdate/commit/1ccff8d) |
| 2026-09-28 | v1.1.3 | [`1daf4ab`](https://github.com/se7enxweb/ezupdate/commit/1daf4ab) |
| 2026-09-28 | v1.1.4 | [`3c4bf3d`](https://github.com/se7enxweb/ezupdate/commit/3c4bf3d) |
| 2026-09-28 | v1.1.5 | [`d254abd`](https://github.com/se7enxweb/ezupdate/commit/d254abd) |
| 2026-09-28 | v1.1.6 | [`b3f18a4`](https://github.com/se7enxweb/ezupdate/commit/b3f18a4) |
| 2026-09-29 | v1.1.7 | [`146f4a3`](https://github.com/se7enxweb/ezupdate/commit/146f4a3) |
| 2026-10-01 | v1.1.8 | [`3ca2cbb`](https://github.com/se7enxweb/ezupdate/commit/3ca2cbb) |
| 2026-10-02 | v1.1.9 | [`781c999`](https://github.com/se7enxweb/ezupdate/commit/781c999) |
| 2026-10-02 | v1.1.10 | [`d5edb6f`](https://github.com/se7enxweb/ezupdate/commit/d5edb6f) |

## Timeline

### 2024-11

The month across all extensions: [November 2024](months/2024-11.md). [Ledger of this month](../ledger/ezupdate.md#2024-11-3-changes).

- 2024-11-02 [`ed04f45`](https://github.com/se7enxweb/ezupdate/commit/ed04f45) (feature) Initial Commit of Composer Update Exponential Module GUI Features **Release v1.0.1.**
- 2024-11-02 [`3cfa7b6`](https://github.com/se7enxweb/ezupdate/commit/3cfa7b6) (docs) Revised documentation
- 2024-11-02 [`ffbcbcf`](https://github.com/se7enxweb/ezupdate/commit/ffbcbcf) (feature) Update ezupdate.ini.append make setting more generic for all users

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/ezupdate.md#2026-03-1-changes).

- 2026-03-02 [`d062589`](https://github.com/se7enxweb/ezupdate/commit/d062589) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-06

The month across all extensions: [June 2026](months/2026-06.md). [Ledger of this month](../ledger/ezupdate.md#2026-06-1-changes).

- 2026-06-21 [`3028031`](https://github.com/se7enxweb/ezupdate/commit/3028031) (feature) Add colored console output to Composer update dashboard

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/ezupdate.md#2026-09-18-changes).

- 2026-09-13 [`9e6bedd`](https://github.com/se7enxweb/ezupdate/commit/9e6bedd) (feature) Expanded warning to instruct users to backup first!
- 2026-09-13 [`cc1e7e9`](https://github.com/se7enxweb/ezupdate/commit/cc1e7e9) (docs) Updated documentation for v1.0.2 **Release v1.0.2.**
- 2026-09-22 [`5584bf6`](https://github.com/se7enxweb/ezupdate/commit/5584bf6) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.1.0.**
- 2026-09-27 [`9345f63`](https://github.com/se7enxweb/ezupdate/commit/9345f63) (docs) The extension names its license as GNU General Public License v2.0 (or any later version) and states version 1.1.1 **Release v1.1.1.**
- 2026-09-28 [`704c071`](https://github.com/se7enxweb/ezupdate/commit/704c071) (feature) The extension carries translations of the texts it marks, with German
- 2026-09-28 [`1ccff8d`](https://github.com/se7enxweb/ezupdate/commit/1ccff8d) (release) Version 1.1.2 **Release v1.1.2.**
- 2026-09-28 [`74a163b`](https://github.com/se7enxweb/ezupdate/commit/74a163b) (upgrade note) The dump and show views, the commit template and the Dump assets link, which belonged to git_manager
- 2026-09-28 [`635d024`](https://github.com/se7enxweb/ezupdate/commit/635d024) (feature) Composer runs without a shell, with a time limit and safe defaults, from a dashboard in the current admin design
- 2026-09-28 [`56b9c46`](https://github.com/se7enxweb/ezupdate/commit/56b9c46) (feature) packagist.org browsing, guarded installs, Composer and Exponential package servers, live output of background runs, and a command line
- 2026-09-28 [`1daf4ab`](https://github.com/se7enxweb/ezupdate/commit/1daf4ab) (release) Version 1.1.3 **Release v1.1.3.**
- 2026-09-28 [`0f902d3`](https://github.com/se7enxweb/ezupdate/commit/0f902d3) (feature) Run again for every Composer run, and Run it for real for a dry run
- 2026-09-28 [`3c4bf3d`](https://github.com/se7enxweb/ezupdate/commit/3c4bf3d) (release) Version 1.1.4 **Release v1.1.4.**
- 2026-09-28 [`e2d5e64`](https://github.com/se7enxweb/ezupdate/commit/e2d5e64) (feature) The job page says why it cannot follow a run instead of retrying in silence
- 2026-09-28 [`d254abd`](https://github.com/se7enxweb/ezupdate/commit/d254abd) (release) Version 1.1.5 **Release v1.1.5.**
- 2026-09-28 [`5701600`](https://github.com/se7enxweb/ezupdate/commit/5701600) (feature) Funding, the list composer fund prints, in the admin and on the command line
- 2026-09-28 [`b3f18a4`](https://github.com/se7enxweb/ezupdate/commit/b3f18a4) (release) Version 1.1.6 **Release v1.1.6.**
- 2026-09-29 [`ac171db`](https://github.com/se7enxweb/ezupdate/commit/ac171db) (feature) The navigation part and the Setup menu entry have German translations
- 2026-09-29 [`146f4a3`](https://github.com/se7enxweb/ezupdate/commit/146f4a3) (release) Version 1.1.7 **Release v1.1.7.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/ezupdate.md#2026-10-11-changes).

- 2026-10-01 [`aab54bc`](https://github.com/se7enxweb/ezupdate/commit/aab54bc) (feature) Installed packages, and every page in one new design
- 2026-10-01 [`192d020`](https://github.com/se7enxweb/ezupdate/commit/192d020) (feature) The new texts in English and German, and in the untranslated catalogue
- 2026-10-01 [`5e96bc7`](https://github.com/se7enxweb/ezupdate/commit/5e96bc7) (docs) The license texts are Markdown files, LICENSE.md and doc/LICENSE.md
- 2026-10-01 [`9afcc14`](https://github.com/se7enxweb/ezupdate/commit/9afcc14) (feature) Documentation for users, administrators and contributors
- 2026-10-01 [`3ca2cbb`](https://github.com/se7enxweb/ezupdate/commit/3ca2cbb) (release) Version 1.1.8 **Release v1.1.8.**
- 2026-10-01 [`b759785`](https://github.com/se7enxweb/ezupdate/commit/b759785) (tooling) Pull requests go against main, the branch's new name
- 2026-10-02 [`effbbb9`](https://github.com/se7enxweb/ezupdate/commit/effbbb9) (tooling) The command line scripts, cronjob parts and module views are classes the files call
- 2026-10-02 [`fcfed0d`](https://github.com/se7enxweb/ezupdate/commit/fcfed0d) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`781c999`](https://github.com/se7enxweb/ezupdate/commit/781c999) (release) Version 1.1.9 **Release v1.1.9.**
- 2026-10-02 [`c1c5e24`](https://github.com/se7enxweb/ezupdate/commit/c1c5e24) (tooling) The commands start through the shared command helpers
- 2026-10-02 [`d5edb6f`](https://github.com/se7enxweb/ezupdate/commit/d5edb6f) (release) Version 1.1.10 **Release v1.1.10.**

## Related

* [Feature page](../../features/6.0/extensions/ezupdate.md)
* [Release notes](../../changelogs/extensions/ezupdate.md)
* [Change ledger](../ledger/ezupdate.md)
