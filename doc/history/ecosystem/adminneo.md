# Ecosystem repository: adminneo

**Group:** Database administration. **Period in the ledger:** 2021-06-10 to 2026-03-28. **Changes:** 1121 (0 made by the se7enxweb team, 1121 upstream history carried by the fork).

## What it is

AdminNeo and EditorNeo, a single-file database management tool (fork of Adminer).

## How it relates to Exponential

The code base of the sevenx_dse extension, which embeds it in the Exponential admin.

## What a user gets

Browse tables, run SQL, export and import for MySQL, MariaDB, PostgreSQL, SQLite, MS SQL, Oracle and MongoDB.

The upstream README (checked at HEAD of the clone) lists the supported databases as MySQL, MariaDB, PostgreSQL, MS SQL, SQLite, Oracle, MongoDB and SimpleDB, plus Elasticsearch (beta) and ClickHouse (alpha); the drivers are the `admin/drivers/*.inc.php` files. Inside Exponential the tool is shipped by the `sevenx_dse` extension (`extension/sevenx_dse/adminneo`).

## Where to read more

- [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- [sevenx_dse extension](../../features/6.0/extensions/sevenx_dse.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

<!-- rev2-listed-rows:start -->
## Security, performance and upgrade changes in the history

These changes are classified in the coverage record and are not named elsewhere on this page. Most are upstream history that the fork carries; the date and the commit subject are the ledger entry (see the [full ledger](../ledger/README.md)). Read the subject for what changed; for the exact effect, open the commit in the repository.

### Security (5)

- 2024-08-16 `13258de1` Fix several bugs and security issues in AdminerFileUpload plugin
- 2024-10-07 `2d4b7365` Refactor generating of private key and random strings
- 2025-02-18 `01a3b817` AdminerFileUpload: Simplify random name generating
- 2025-04-10 `e71a753c` Security: Disallow writing temporary files to symlinks
- 2025-09-09 `e9b050b1` More secure randomness on PHP5, 256 bits of entropy in random strings

### Performance (5)

- 2024-11-27 `434adacf` Change colors of default button, optimise its highlighting
- 2025-02-28 `92f18518` Speed up with disabled output buffering
- 2025-03-31 `838b61d3` Optimize retrieving columns for schema
- 2025-10-06 `a424a5ef` GeminiSqlPlugin: Optimise the prompt if no database is selected
- 2025-10-16 `4361cb45` Optimise JS for resetting record value function while editing

### Behaviour and upgrade (13)

- 2021-04-03 `857cbf03` Fix version condition for deprecated mapping types
- 2024-09-06 `08637669` Replace deprecated <acronym> with <abbr>
- 2024-10-15 `dd9a4a2b` Remove deprecated HTML table parameters
- 2024-11-27 `6109a115` Fix some deprecation warnings
- 2025-01-23 `d0576ab1` SQLite: Remove support for SQLite 2
- 2025-03-07 `72f3fe74` Remove legacy code (magic quotes)
- 2025-03-08 `e6c2f27c` MySQL: Drop support for MySQL 4
- 2025-03-19 `867738a6` Remove migration of deprecated MySQL session data
- 2025-04-03 `e94eed87` Editor: Remove support for sending mass e-mails
- 2025-04-05 `8d98bb09` Rename methods for backward keys, strict types
- 2025-04-10 `2a7235a8` Remove backward compatibility for MySQL users logged to AdminNeo 4.11 and older
- 2025-10-24 `3595129a` Move backward keys retrieving to drivers
- 2026-02-08 `4182456b` Avoid deprecated each()

<!-- rev2-listed-rows:end -->

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 442 |
| Fixes | 234 |
| Behaviour and upgrade changes | 14 |
| Security | 6 |
| Performance | 6 |
| Documentation | 128 |
| Tooling | 180 |
| Releases | 45 |
| No user benefit | 66 |

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2021-06 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2c8dbf49` Fix misaligned inputs |
| 2021-09 | 3 | 2 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `898dc9e2` Move Elastic drivers to plugins, driver for Elastic 7+ is the default; `acf168a6` Add Latvian language translation |
| 2021-11 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `61b84cec` Bangla language corrections; `6304f35b` Clickhouse: Support for array values |
| 2024-03 | 18 | 8 | 0 | 0 | 0 | 0 | 0 | 4 | 2 | 4 | `e99ed80a` Update language files; `a18f2fb5` Modernize default design, SVG icons, dark mode |
| 2023-07 | 3 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 0 | 0 | `3e942992` Update French and Italian translations; `834380aa` Update Dutch translation |
| 2023-08 | 4 | 3 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `a693e75e` No-verify plugin breaks others; `2928b7be` Update Czech translation |
| 2024-01 | 7 | 5 | 1 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `55a7d386` Change 'Invalid credentials.' message; `1c5947de` Validate server input |
| 2018-11 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `8e848bfd` Allow responsive styles on larger devices |
| 2021-04 | 6 | 4 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | `9968851f` Add support for "where" field privilege; `9f8dadbb` Add support for "order" field privilege |
| 2023-05 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6f789eac` Enable regular expressions when searching data in all tables; `b71a4565` Fix undefined $sql variable |
| 2021-03 | 2 | 0 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | `01fe709b` Replace deprecated "filtered" query with "bool" query; `e8c9164a` Fix searching if "anywhere" field is selected |
| 2024-08 | 13 | 4 | 3 | 0 | 1 | 0 | 0 | 2 | 2 | 1 | `43a0305a` Fix server URL validation for Oracle connections; `de7dd4b6` Improve URL and email detection |
| 2023-12 | 3 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `09a946cb` Skip dump of generated columns; `00b9fbda` PHP 8.3 error suppression |
| 2024-04 | 3 | 1 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `05582531` Remove all alternative designs; `441e7f05` Check new version against GitHub pages |
| 2024-09 | 30 | 6 | 10 | 1 | 0 | 0 | 2 | 4 | 6 | 1 | `0f2e0473` Show partitioning info in table structure page; `c4ed9500` Add support for translations in plugins |
| 2022-11 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `58cca3f9` MySQL: Add unix_timestamp to functions |
| 2021-08 | 2 | 1 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | `aee800ef` Do not limit unlimited memory, fix number conversion warning; `203162b2` Function to retrieve driver name |
| 2021-10 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `fa791b54` PostgreSQL: Fix exporting CREATE TABLE with sequence default value |
| 2024-10 | 69 | 28 | 16 | 1 | 1 | 0 | 5 | 10 | 7 | 1 | `571be06e` Add Adminer namespace; `62246338` Add 'Home' to breadcrumb navigation |
| 2024-07 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `08e48c86` MySQL: Fix where clause for JSON column |
| 2023-11 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `9ed4c859` Add removal buttons to table data filter |
| 2022-03 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `fc2e0256` MS SQL: Prefix Unicode strings with 'N' so they are treated correctly |
| 2024-11 | 35 | 14 | 5 | 1 | 0 | 1 | 5 | 7 | 2 | 0 | `f93db81c` Update translations; `07868da3` Add styles to input elements |
| 2024-12 | 6 | 5 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `5a4335b7` Add several modes of main navigation; `0d9ce281` Remove AdminerDatabaseHide plugin, add hiddenDatabases and hiddenSchem |
| 2025-01 | 35 | 10 | 7 | 1 | 0 | 0 | 3 | 7 | 3 | 4 | `2e6713ca` Short array syntax; `d9d4694e` Fix and refactor script for updating languages files |
| 2022-07 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `1abaa642` Update lucas-sandery theme; `4e703bf9` Add .gitattributes, exclude tests from archive |
| 2025-02 | 159 | 77 | 36 | 0 | 1 | 1 | 13 | 16 | 4 | 11 | `4b06a49e` Rebrand: Change Editor to EditorNeo; `2f75e188` Hide index column options by default |
| 2021-05 | 2 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `fe76cfcd` Oracle: Support to access tablespaces of granted tables; `e5b5d724` Fix German translation error |
| 2022-02 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `8f8c9a6e` MongoDB: Execute commands against the selected DB |
| 2022-10 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `18502c47` MySQL: Support CHECK constraint |
| 2025-03 | 237 | 104 | 42 | 3 | 0 | 1 | 26 | 36 | 8 | 17 | `332038e5` Rename namespace Adminer -> AdminNeo; `094c384c` Rename folder adminer -> admin |
| 2023-06 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `206b9929` Add seek() to Min_PDOStatement for mssql. |
| 2025-04 | 117 | 52 | 21 | 3 | 1 | 0 | 11 | 24 | 0 | 5 | `2fa10dd1` Rename folder lang -> translations; `c4edfcd0` Use singleton interface to access the Admin class in global functions |
| 2025-05 | 78 | 21 | 17 | 0 | 0 | 0 | 19 | 17 | 2 | 2 | `74b3c0f1` Update web links to adminneo.org; `92cf328a` Include external dependencies to the project |
| 2025-06 | 58 | 29 | 10 | 0 | 0 | 0 | 5 | 12 | 1 | 1 | `aab6853c` Refactor global functions for translations; `3e38a24b` Use primary Database connection as a singleton |
| 2025-07 | 60 | 18 | 13 | 0 | 0 | 0 | 8 | 13 | 0 | 8 | `1c6ec5fd` Refactor collecting error messages; `d9819b6e` Sync Jush with original library |
| 2025-08 | 39 | 10 | 8 | 0 | 0 | 0 | 10 | 5 | 2 | 4 | `d8e7e0bb` Composer: Remove composer internal files from git; `63c15209` Make settings available in Editor and plugins |
| 2025-09 | 19 | 3 | 8 | 0 | 1 | 0 | 2 | 2 | 2 | 1 | `5255ec3b` Avoid PHP and HTML mixing; `1521227e` Add helper function for <input type=hidden> |
| 2025-10 | 34 | 13 | 10 | 1 | 0 | 2 | 2 | 4 | 0 | 2 | `aa8964b2` Print the last release number to compiled plugins (fix #164); `59dc1c25` Add setting for displaying links to referencing tables |
| 2025-11 | 16 | 2 | 6 | 0 | 0 | 0 | 5 | 1 | 2 | 0 | `0452ffe0` Update German and Dutch translations; `be024126` Use input type 'text' instead of 'search' in selection filter |
| 2025-12 | 10 | 0 | 5 | 0 | 1 | 0 | 1 | 1 | 2 | 0 | `befde6f6` Replace function get_random_string() with Random::strongKey(); `22a27f18` Refactor tar_file() function, fix sending TAR data to output (fix #176 |
| 2026-01 | 20 | 6 | 3 | 0 | 0 | 0 | 2 | 8 | 0 | 1 | `8bd548d2` Use int for $limit; `1972472c` Refactor functions |
| 2026-02 | 8 | 1 | 1 | 1 | 0 | 0 | 1 | 2 | 0 | 2 | `c0ab1386` Proper boolean return type for various functions; `9820f622` Tests: Generate tests for compiled version |
| 2026-03 | 9 | 2 | 4 | 0 | 0 | 0 | 0 | 3 | 0 | 0 | `16b66f2e` Tests: Check server and proper extension; `4112a3a2` Declare PHP 8.5 compatibility |

## Full record

- Every change with date, kind, size and release tag: [ledger of adminneo](../ledger/adminneo.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).

<!-- rev2-see-also:start -->
## See also

- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/adminneo.md)
- Platform ecosystem by month: [2018-11](months/2018-11.md), [2021-03](months/2021-03.md), [2021-04](months/2021-04.md), [2021-05](months/2021-05.md), [2021-06](months/2021-06.md), [2021-08](months/2021-08.md), [2021-09](months/2021-09.md), [2021-10](months/2021-10.md), [2021-11](months/2021-11.md), [2022-02](months/2022-02.md), [2022-03](months/2022-03.md), [2022-07](months/2022-07.md), [2022-10](months/2022-10.md), [2022-11](months/2022-11.md), [2023-05](months/2023-05.md), [2023-06](months/2023-06.md), [2023-07](months/2023-07.md), [2023-08](months/2023-08.md), [2023-11](months/2023-11.md), [2023-12](months/2023-12.md), [2024-01](months/2024-01.md), [2024-03](months/2024-03.md), [2024-04](months/2024-04.md), [2024-07](months/2024-07.md), [2024-08](months/2024-08.md), [2024-09](months/2024-09.md), [2024-10](months/2024-10.md), [2024-11](months/2024-11.md), [2024-12](months/2024-12.md), [2025-01](months/2025-01.md), [2025-02](months/2025-02.md), [2025-03](months/2025-03.md), [2025-04](months/2025-04.md), [2025-05](months/2025-05.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2025-10](months/2025-10.md), [2025-11](months/2025-11.md), [2025-12](months/2025-12.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md)

<!-- rev2-see-also:end -->
