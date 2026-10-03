# sevenx_valkey_cache (Redis and Valkey cache): chronicle

The Redis and Valkey cache backend was published on 20 July 2026, with a cache clear script, release preparation for Exponential 6.0.15 and, on 30 July, a single hash for the compiled INI cache. See the [feature page](../../features/6.0/extensions/sevenx_valkey_cache.md).

This page lists **every one of the 4 changes** of the repository `sevenx_valkey_cache` between 2026-07-20 and 2026-07-30, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/sevenx_valkey_cache.md); what each release contains is in the [release notes](../../changelogs/extensions/sevenx_valkey_cache.md); how to use the extension is on its [feature page](../../features/6.0/extensions/sevenx_valkey_cache.md).

| Kind | Changes |
|---|---|
| feature | 1 |
| performance | 2 |
| release | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-07-20 | 1.0.0.0 | [`823d27c`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/823d27c) |
| 2026-07-20 | v1.0.2.0 | [`815d37b`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/815d37b) |

## Timeline

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/sevenx_valkey_cache.md#2026-07-4-changes).

- 2026-07-20 [`823d27c`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/823d27c) (performance) Initial commit of stable extension sevenx_valkey_cache which replaces file cache with redis/valkey storage for high performance caching page speed loading times. Drastic improvements in speed. Requires changes found only in Exponential 6.0.15 or later. **Release 1.0.0.0.**
- 2026-07-20 [`573b201`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/573b201) (feature) Add ezvalkeycacheclear.php CLI script for full cache clearing.
- 2026-07-20 [`815d37b`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/815d37b) (release) Prepare 1.0.1 release and update documentation for Exponential 6.0.15. **Release v1.0.2.0.**
- 2026-07-30 [`2bd09c4`](https://github.com/se7enxweb/sevenx_valkey_cache/commit/2bd09c4) (performance) Store compiled INI cache in a single Redis hash

## Related

* [Feature page](../../features/6.0/extensions/sevenx_valkey_cache.md)
* [Release notes](../../changelogs/extensions/sevenx_valkey_cache.md)
* [Change ledger](../ledger/sevenx_valkey_cache.md)

## See also

* [behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
