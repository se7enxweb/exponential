# sevenx_valkey_cache (Redis and Valkey cache): release notes

Read this page before you install or update `sevenx_valkey_cache`, or to find out which release brought a change.

What each release of `sevenx_valkey_cache` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/sevenx_valkey_cache.md); the story is in the [chronicle](../../history/extensions/sevenx_valkey_cache.md).

## Unreleased

**Updated**

- Store compiled INI cache in a single Redis hash ([`2bd09c4`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/2bd09c4))

## v1.0.2.0 (2026-07-20)

**Added**

- Add ezvalkeycacheclear.php CLI script for full cache clearing. ([`573b201`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/573b201))

1 version, merge or metadata commit not listed.

## 1.0.0.0 (2026-07-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v1.0.1.0 (2026-07-20)

No commit of this repository is tagged only with this release in the ledger window (see the chronicle).

## v1.0.1 (2026-07-20)

**Added**

- Initial commit of stable extension sevenx_valkey_cache which replaces file cache with redis/valkey storage for high performance caching page speed loading times. Drastic improvements in speed. Requires changes found only in Exponential 6.0.15 or later. ([`823d27c`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/823d27c))

## Related pages

- [Feature page](../../features/6.0/extensions/sevenx_valkey_cache.md)
- [Chronicle](../../history/extensions/sevenx_valkey_cache.md)
- [Change ledger](../../history/ledger/sevenx_valkey_cache.md)
- [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-07](../../history/extensions/months/2026-07.md)
