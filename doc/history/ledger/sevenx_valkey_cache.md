# Change ledger: sevenx_valkey_cache

Every change made to `sevenx_valkey_cache` since the se7enxweb era began, oldest first: 4 changes touching 18 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Added | 2 |
| Other | 1 |
| Updated | 1 |

## 2026-07 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-20 | `823d27c` | Added | Added: Initial commit of stable extension sevenx_valkey_cache which replaces file cache with redis/valkey storage for high performance caching page speed loading times. Drastic improvements in speed. Requires changes found only in Exponential 6.0.15 or later. | 14 | +2546 / −0 | 1.0.0.0 |
| 2026-07-20 | `573b201` | Added | Add ezvalkeycacheclear.php CLI script for full cache clearing. | 1 | +278 / −0 |  |
| 2026-07-20 | `815d37b` | Other | Prepare 1.0.1 release and update documentation for Exponential 6.0.15. | 2 | +42 / −43 | v1.0.2.0 |
| 2026-07-30 | `2bd09c4` | Updated | Updated: Store compiled INI cache in a single Redis hash | 1 | +38 / −78 |  |
