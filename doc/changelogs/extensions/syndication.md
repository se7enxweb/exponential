# syndication (content syndication): release notes

What each release of `syndication` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/syndication.md); the story is in the [chronicle](../../history/extensions/syndication.md).

## v1.3.2 (2026-10-02)

**Updated**

- The commands and cronjob parts list a description of what they do ([`9bbf346`](https://github.com/se7enxweb/syndication/commit/9bbf346))

**Maintenance, documentation and packaging**

- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`2593c19`](https://github.com/se7enxweb/syndication/commit/2593c19))

1 version, merge or metadata commit not listed.

## v1.3.1 (2026-10-02)

**Added**

- Added share/db_schema.dba, the syndication tables in the engine-neutral schema format, so that an installer can create them on every database Exponential supports and not only on MySQL. ([`58dbf3c`](https://github.com/se7enxweb/syndication/commit/58dbf3c))

1 version, merge or metadata commit not listed.

## v1.3.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`ee6e4c3`](https://github.com/se7enxweb/syndication/commit/ee6e4c3))

## v1.2.0 (2026-07-19)

**Updated**

- Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. ([`cb365d3`](https://github.com/se7enxweb/syndication/commit/cb365d3))
- Refactoring syndication internals to provide for http acl authentication to protect sources and other required usablity bugfixes. Enhancements. ([`42ff4f3`](https://github.com/se7enxweb/syndication/commit/42ff4f3))

**Maintenance, documentation and packaging**

- Massive rewrite of extension documentation. Reorganization of file placement. Switched to markdown for docs. Doc. ([`6a80394`](https://github.com/se7enxweb/syndication/commit/6a80394))
- Expanded install doc to mention kernel patch provided and suggest a new feature for the extension to provide in the future. Doc ([`c0a4b13`](https://github.com/se7enxweb/syndication/commit/c0a4b13))
- Branding change. Doc. ([`c325218`](https://github.com/se7enxweb/syndication/commit/c325218))

2 version, merge or metadata commits not listed.

## v1.1.0 (2025-09-13)

**Added**

- Initial patched import of mostly working extension ported from php5 to php8. Requires some kernel changes atm. Inital Import. ([`cc6b2a7`](https://github.com/se7enxweb/syndication/commit/cc6b2a7))

**Updated**

- Bugfixes for php8 support. Bugfixes. ([`6942894`](https://github.com/se7enxweb/syndication/commit/6942894))
- Mass commit of debug statement removal with appologies to others affected. These changes represent a working upgraded to php8.3 syndication extension that has been tested and proven to once again work as designed with minor issues (feature click specific rot in admin views). Stable Changeset. Bugfixes + Code Standards. ([`c401648`](https://github.com/se7enxweb/syndication/commit/c401648))
- Bugfix to remove testing die statement. Bugfix. ([`fd374b3`](https://github.com/se7enxweb/syndication/commit/fd374b3))

**Maintenance, documentation and packaging**

- Update composer.json updated description ([`2757d0a`](https://github.com/se7enxweb/syndication/commit/2757d0a))

1 version, merge or metadata commit not listed.

## Related

* [Feature page](../../features/6.0/extensions/syndication.md)
* [Chronicle](../../history/extensions/syndication.md)
* [Change ledger](../../history/ledger/syndication.md)
