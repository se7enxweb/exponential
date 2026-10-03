# syndication (content syndication): chronicle

Syndication was published by 7x in September 2025 after being ported from PHP 5 to PHP 8, documented, and extended in July 2026 with HTTP authentication of imports. The autumn added persistent worker safety and a portable schema. See the [feature page](../../features/6.0/extensions/syndication.md).

This page lists **every one of the 19 changes** of the repository `syndication` between 2025-09-13 and 2026-10-02, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/syndication.md); what each release contains is in the [release notes](../../changelogs/extensions/syndication.md); how to use the extension is on its [feature page](../../features/6.0/extensions/syndication.md).

| Kind | Changes |
|---|---|
| feature | 3 |
| fix | 4 |
| security | 2 |
| docs | 4 |
| tooling | 1 |
| release | 4 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2025-09-13 | v1.1.0 | [`018b318`](https://github.com/se7enxweb/syndication/commit/018b318) |
| 2026-07-19 | v1.2.0 | [`42ff4f3`](https://github.com/se7enxweb/syndication/commit/42ff4f3) |
| 2026-09-22 | v1.3.0 | [`ee6e4c3`](https://github.com/se7enxweb/syndication/commit/ee6e4c3) |
| 2026-10-02 | v1.3.1 | [`687bdae`](https://github.com/se7enxweb/syndication/commit/687bdae) |
| 2026-10-02 | v1.3.2 | [`09c7779`](https://github.com/se7enxweb/syndication/commit/09c7779) |

## Timeline

### 2025-09

The month across all extensions: [September 2025](months/2025-09.md). [Ledger of this month](../ledger/syndication.md#2025-09-9-changes).

- 2025-09-13 [`cc6b2a7`](https://github.com/se7enxweb/syndication/commit/cc6b2a7) (feature) Initial patched import of mostly working extension ported from php5 to php8. Requires some kernel changes atm. Inital Import.
- 2025-09-13 [`6942894`](https://github.com/se7enxweb/syndication/commit/6942894) (fix) Bugfixes for php8 support. Bugfixes.
- 2025-09-13 [`c401648`](https://github.com/se7enxweb/syndication/commit/c401648) (fix) Mass commit of debug statement removal with appologies to others affected. These changes represent a working upgraded to php8.3 syndication extension that has been tested and proven to once again work as designed with minor issues (feature click specific rot in admin views). Stable Changeset. Bugfixes + Code Standards.
- 2025-09-13 [`fd374b3`](https://github.com/se7enxweb/syndication/commit/fd374b3) (fix) Bugfix to remove testing die statement. Bugfix.
- 2025-09-13 [`2757d0a`](https://github.com/se7enxweb/syndication/commit/2757d0a) (tooling) Update composer.json updated description
- 2025-09-13 [`018b318`](https://github.com/se7enxweb/syndication/commit/018b318) (release) Update ezinfo.php version bump for upgraded and tested solution **Release v1.1.0.**
- 2025-09-18 [`6a80394`](https://github.com/se7enxweb/syndication/commit/6a80394) (docs) Massive rewrite of extension documentation. Reorganization of file placement. Switched to markdown for docs. Doc.
- 2025-09-18 [`c0a4b13`](https://github.com/se7enxweb/syndication/commit/c0a4b13) (docs) Expanded install doc to mention kernel patch provided and suggest a new feature for the extension to provide in the future. Doc
- 2025-09-18 [`c325218`](https://github.com/se7enxweb/syndication/commit/c325218) (docs) Branding change. Doc.

### 2026-03

The month across all extensions: [March 2026](months/2026-03.md). [Ledger of this month](../ledger/syndication.md#2026-03-1-changes).

- 2026-03-02 [`96e2b3e`](https://github.com/se7enxweb/syndication/commit/96e2b3e) (no user benefit) chore: add GitHub Sponsors funding metadata

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/syndication.md#2026-07-3-changes).

- 2026-07-19 [`cb365d3`](https://github.com/se7enxweb/syndication/commit/cb365d3) (security) Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements.
- 2026-07-19 [`d6bfbd6`](https://github.com/se7enxweb/syndication/commit/d6bfbd6) (release) Version Bump
- 2026-07-19 [`42ff4f3`](https://github.com/se7enxweb/syndication/commit/42ff4f3) (security) Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. **Release v1.2.0.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/syndication.md#2026-09-1-changes).

- 2026-09-22 [`ee6e4c3`](https://github.com/se7enxweb/syndication/commit/ee6e4c3) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.3.0.**

### 2026-10

The month across all extensions: [October 2026](months/2026-10.md). [Ledger of this month](../ledger/syndication.md#2026-10-5-changes).

- 2026-10-02 [`58dbf3c`](https://github.com/se7enxweb/syndication/commit/58dbf3c) (feature) Added share/db_schema.dba, the syndication tables in the engine-neutral schema format, so that an installer can create them on every database Exponential supports and not only on MySQL.
- 2026-10-02 [`687bdae`](https://github.com/se7enxweb/syndication/commit/687bdae) (release) Version 1.3.1, with ezinfo.php's keys in the form the about page reads and an extension.xml carrying the version, license and website. **Release v1.3.1.**
- 2026-10-02 [`9bbf346`](https://github.com/se7enxweb/syndication/commit/9bbf346) (feature) The commands and cronjob parts list a description of what they do
- 2026-10-02 [`2593c19`](https://github.com/se7enxweb/syndication/commit/2593c19) (docs) The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices
- 2026-10-02 [`09c7779`](https://github.com/se7enxweb/syndication/commit/09c7779) (release) Version 1.3.2 **Release v1.3.2.**

## Related

* [Feature page](../../features/6.0/extensions/syndication.md)
* [Release notes](../../changelogs/extensions/syndication.md)
* [Change ledger](../ledger/syndication.md)
