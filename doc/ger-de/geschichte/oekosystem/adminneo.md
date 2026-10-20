# adminneo: Geschichte des Plattform-Repositorys

Die Geschichte von `adminneo`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Datenbankverwaltung). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 1121 Änderungen von 2021-06-10 bis 2026-03-28, alle aus der Upstream-Geschichte, die der Fork mitführt; das se7enxweb-Team hat keine davon gemacht. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

AdminNeo und EditorNeo, ein Werkzeug zur Datenbankverwaltung in einer einzigen Datei (Fork von Adminer).

## Wie es mit Exponential zusammenhängt

Die Codebasis der Erweiterung sevenx_dse, die es in die Administration von Exponential einbettet.

## Was Anwender davon haben

Tabellen durchsehen, SQL ausführen, exportieren und importieren für MySQL, MariaDB, PostgreSQL, SQLite, MS SQL, Oracle und MongoDB.

Die README des Upstreams (geprüft am HEAD des Klons) nennt als unterstützte Datenbanken MySQL, MariaDB, PostgreSQL, MS SQL, SQLite, Oracle, MongoDB und SimpleDB, dazu Elasticsearch (Beta) und ClickHouse (Alpha); die Treiber sind die Dateien `admin/drivers/*.inc.php`. In Exponential liefert die Erweiterung `sevenx_dse` das Werkzeug aus (`extension/sevenx_dse/adminneo`).

## Sicherheits-, Leistungs- und Upgrade-Änderungen in der Geschichte

Diese Änderungen sind im Abdeckungsnachweis eingeordnet und werden sonst nirgends auf dieser Seite genannt. Die meisten sind Upstream-Geschichte, die der Fork mitführt; Datum und Betreffzeile des Commits sind der Eintrag im Änderungsverzeichnis (siehe das [vollständige Änderungsverzeichnis](../aenderungsverzeichnis/README.md)). Die Betreffzeile sagt, was sich geändert hat; die genaue Wirkung zeigt der Commit im Repository.

### Sicherheit (5)

- 2024-08-16 `13258de1` Fix several bugs and security issues in AdminerFileUpload plugin
- 2024-10-07 `2d4b7365` Refactor generating of private key and random strings
- 2025-02-18 `01a3b817` AdminerFileUpload: Simplify random name generating
- 2025-04-10 `e71a753c` Security: Disallow writing temporary files to symlinks
- 2025-09-09 `e9b050b1` More secure randomness on PHP5, 256 bits of entropy in random strings

### Leistung (5)

- 2024-11-27 `434adacf` Change colors of default button, optimise its highlighting
- 2025-02-28 `92f18518` Speed up with disabled output buffering
- 2025-03-31 `838b61d3` Optimize retrieving columns for schema
- 2025-10-06 `a424a5ef` GeminiSqlPlugin: Optimise the prompt if no database is selected
- 2025-10-16 `4361cb45` Optimise JS for resetting record value function while editing

### Verhalten und Upgrade (13)

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

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 442 |
| Fehlerbehebungen | 234 |
| Verhaltens- und Upgrade-Änderungen | 14 |
| Sicherheit | 6 |
| Leistung | 6 |
| Dokumentation | 128 |
| Werkzeuge | 180 |
| Releases | 45 |
| Ohne Nutzen für Anwender | 66 |

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
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
| 2024-12 | 6 | 5 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | `5a4335b7` Add several modes of main navigation; `0d9ce281` Remove AdminerDatabaseHide plugin, add hiddenDatabases and hiddenSchemas config options |
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
| 2025-12 | 10 | 0 | 5 | 0 | 1 | 0 | 1 | 1 | 2 | 0 | `befde6f6` Replace function get_random_string() with Random::strongKey(); `22a27f18` Refactor tar_file() function, fix sending TAR data to output (fix #176) |
| 2026-01 | 20 | 6 | 3 | 0 | 0 | 0 | 2 | 8 | 0 | 1 | `8bd548d2` Use int for $limit; `1972472c` Refactor functions |
| 2026-02 | 8 | 1 | 1 | 1 | 0 | 0 | 1 | 2 | 0 | 2 | `c0ab1386` Proper boolean return type for various functions; `9820f622` Tests: Generate tests for compiled version |
| 2026-03 | 9 | 2 | 4 | 0 | 0 | 0 | 0 | 3 | 0 | 0 | `16b66f2e` Tests: Check server and proper extension; `4112a3a2` Declare PHP 8.5 compatibility |

## Verwandte Seiten

- [Datenbankverwaltung AdminNeo](../../../eng-us/features/6.0/adminneo-database-manager.md)
- [Erweiterung sevenx_dse](../../../eng-us/features/6.0/extensions/sevenx_dse.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/adminneo.md)
- Plattform-Ökosystem nach Monat: [2018-11](monate/2018-11.md), [2021-03](monate/2021-03.md), [2021-04](monate/2021-04.md), [2021-05](monate/2021-05.md), [2021-06](monate/2021-06.md), [2021-08](monate/2021-08.md), [2021-09](monate/2021-09.md), [2021-10](monate/2021-10.md), [2021-11](monate/2021-11.md), [2022-02](monate/2022-02.md), [2022-03](monate/2022-03.md), [2022-07](monate/2022-07.md), [2022-10](monate/2022-10.md), [2022-11](monate/2022-11.md), [2023-05](monate/2023-05.md), [2023-06](monate/2023-06.md), [2023-07](monate/2023-07.md), [2023-08](monate/2023-08.md), [2023-11](monate/2023-11.md), [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2024-10](monate/2024-10.md), [2024-11](monate/2024-11.md), [2024-12](monate/2024-12.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-03](monate/2025-03.md), [2025-04](monate/2025-04.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md)
