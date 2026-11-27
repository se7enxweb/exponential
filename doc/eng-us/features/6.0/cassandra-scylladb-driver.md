# Apache Cassandra and ScyllaDB driver

This page is for administrators whose organisation keeps data in Apache Cassandra or ScyllaDB and wants that data in
Exponential, and for developers who work on the driver. From Exponential 6.0.15 the kernel has a driver for the
wide-column databases that speak CQL: `expCassandraDB` (Cassandra) and `expScyllaDB` (ScyllaDB) in `lib/ezdb/classes/`,
with the schema handler `expCassandraSchema` and the import command `exp:cassandra:import`.

**What it is for.** The driver reaches two levels of the three a database can reach in Exponential:

| Level | Cassandra and ScyllaDB |
|---|---|
| (a) The site's database | **No.** A site cannot run on Cassandra or ScyllaDB; [why](#why-no-site-runs-on-cassandra), with the measurements |
| (b) A data table connection | **Yes.** Persistent classes, the data table layer's schema, writer and describer, the connection's test, on a keyspace |
| (c) An import source | **Yes.** `exp:cassandra:import` copies the tables of a keyspace into the site's database or into any data table connection; a synchronisation `table_to_content` makes content of a table's rows |

## Contents

1. [Requirements](#requirements)
2. [A data table connection](#a-data-table-connection)
3. [Importing a keyspace](#importing-a-keyspace)
4. [Settings](#settings)
5. [How the SQL is answered](#how-the-sql-is-answered)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Why no site runs on Cassandra](#why-no-site-runs-on-cassandra)
8. [How it is built](#how-it-is-built)
9. [Tests](#tests)

## Requirements

| What | Where it comes from |
|---|---|
| Apache Cassandra 4.0 to 5.0, or ScyllaDB 2025.1 or later | Tested: Cassandra 5.0.9 and ScyllaDB 2025.1.17 (the images `cassandra:5.0` and `scylladb/scylla:2025.1`) |
| PHP 8.1 or later | The library needs it; on PHP 7.4 and 8.0 the engines are listed as "no driver on this server" |
| The library `mroosz/php-cassandra` (1.2 or later) | `composer require mroosz/php-cassandra:^1.2`; `composer.json` suggests it. No PHP extension is needed |

**Why this library.** There are three ways from PHP to CQL. DataStax's PHP extension (`datastax/php-driver`) is
unmaintained since 2017 and builds on no PHP 8. Its fork for ScyllaDB's users (`he4rt/scylladb-php-driver`) is a C++
extension on top of a C++ driver, built per PHP version, which no host offers as a package. Cassandra's HTTP gateways
are no help either: Stargate is retired, the Data API exists only in DataStax's own products, and ScyllaDB's Alternator
speaks DynamoDB's API, not CQL. `mroosz/php-cassandra` is a client of CQL's native protocol (versions 3 to 5) written in
PHP, MIT licensed, maintained (1.2.0, November 2025), tested by its author against Cassandra 2.1 to 5 and ScyllaDB 6.2
to 2026.x, with authentication, TLS, prepared statements, paging and LZ4 compression. Nothing to compile, and the
driver loads it from `vendor/mroosz/php-cassandra/src` even where Composer's autoloader does not know it. Two faults of
its 1.2.0 the driver works around: it writes the serial consistency of a statement wrongly (the driver leaves it to the
server's default, `SERIAL`), and it fails to send a statement of more than 128 KiB on protocol 5 (the driver speaks
protocol 4 unless `ProtocolVersion=5`).

## A data table connection

A connection of the engine `cassandra` or `scylladb` is a keyspace. Make it on **Setup > Data tables > Connections**
or declare it:

```ini
[Connection_events]
Name=Event store
Engine=scylladb
Server=scylla1.example.com,scylla2.example.com
Port=9042
Database=events
User=exponential
Secret=env:SCYLLA_PASSWORD
Consistency=LOCAL_QUORUM
```

`Database` is the keyspace and must exist (or `[CassandraSettings] AutoCreateKeyspace=enabled`). `Server` may list
several nodes; a lost node is skipped. The options `ssl` (TLS on), `ssl_ca` (the CA file) and `consistency` become the
connection's `[CassandraSettings]`. **Test connection** reports the server's version (ScyllaDB's own, not the 3.0.8
it reports to Cassandra clients) and the number of tables.

Everything of the data table layer works on it:

```php
$db = expDataConnections::db( 'events' );                                   // an expScyllaDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myEvent::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                   // CREATE TABLE, indexes, the definition
$event = new myEvent( array( 'id' => null, 'kind' => 'login' ) );
$event->store();                                                              // id 1, 2, 3 ... from the counter
$list = myEvent::fetchObjectList( myEvent::definition(), null, array( 'kind' => 'login' ), array( 'id' => 'desc' ), 10 );
```

## Importing a keyspace

```bash
./console exp:cassandra:import --source=events --list
./console exp:cassandra:import --source=events --prefix=ev_                    # the plan: tables, columns, SQL
./console exp:cassandra:import --source=events --prefix=ev_ --apply            # into the site's database
./console exp:cassandra:import --server=cass1,cass2 --keyspace=shop --user=reader --secret=env:CASSANDRA_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

The command reads every table of the keyspace (or `--tables`), makes it in the target through the data table layer
(the site's database, or `--connection`), and copies the rows page by page. It uses the import engine of
`exp:access:import`, so the modes (`create`, `append`, `replace`), names (lower case ASCII, a prefix, keywords with a
trailing `_`) and reports are the same. The CQL types become:

| CQL | In the target |
|---|---|
| `int`, `smallint`, `tinyint`, `boolean` | `int` (a boolean as 1 or 0) |
| `bigint`, `counter` | `bigint` |
| `varint`, `decimal` | `decimal` (38 digits) |
| `float`, `double` | `double` |
| `text`, `varchar`, `ascii` | `varchar(255)` in the key, long text otherwise |
| `uuid`, `timeuuid` | `char(38)` |
| `timestamp`, `date` | `varchar(19)`, `Y-m-d H:i:s` in UTC |
| `time`, `duration`, `inet` | `varchar(64)` |
| `blob` | long text, base64 |
| `list`, `set`, `map`, tuples, user types, vectors | long text, JSON |

The key of the new table is the partition key and the clustering columns; each secondary index becomes an index.

## Settings

`site.ini [CassandraSettings]` (a connection's options override `TLS`, `TLSCAFile` and `Consistency`):

| Setting | Default | What |
|---|---|---|
| `Consistency` | `LOCAL_QUORUM` | Of reads and writes |
| `SerialConsistency` | `LOCAL_SERIAL` | Of the reads of the id counters |
| `ReplicationStrategy`, `ReplicationFactor`, `DataCenters` | `SimpleStrategy`, `1`, empty | A keyspace the driver makes; `DataCenters=dc1:3,dc2:3` for NetworkTopologyStrategy |
| `Tablets` | `disabled` | ScyllaDB: keyspaces without tablets (ScyllaDB 2025.1 does no lightweight transactions on tablets) |
| `AutoCreateKeyspace` | `disabled` | Make a missing keyspace when the connection opens |
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, empty, `enabled` | TLS to the nodes |
| `ConnectTimeout`, `RequestTimeout` | `5`, `30` | Seconds |
| `Compression` | `disabled` | LZ4 (PHP's `lz4` extension) |
| `ProtocolVersion` | `4` | 4, or 5 (see the library's limit above) |
| `PageSize` | `5000` | Rows a page |
| `ScanLimit` | `100000` | The most rows a statement may read for what CQL cannot do; more is refused, never answered from a part |
| `AllowFiltering` | `enabled` | Comparisons of other columns go to the server with `ALLOW FILTERING` |
| `InsertCheck` | `read` | A duplicate key: `read` (a lookup first), `lwt` (`IF NOT EXISTS`, exact under concurrency), `none` (written over) |
| `InListChunkSize` | `100` | Values of an `IN` a statement |
| `JournalLimit` | `10000` | Writes of a transaction a rollback can undo |
| `ReconnectOnLoss` | `enabled` | A lost connection outside a transaction is opened again |

## How the SQL is answered

The persistent classes and the data table layer write SQL; the driver parses it (`expCassandraDialect`) and answers it
with CQL and PHP:

- **SELECT.** A WHERE that names the whole partition key with `=` or `IN` is a key lookup (with `=` on the clustering
  columns in order). `=` on a column with a secondary index is an index lookup. Comparisons of other columns go to the
  server with `ALLOW FILTERING`. What CQL cannot say (`OR`, `LIKE`, `IN` on other columns, `NOT`, `BETWEEN`, functions,
  arithmetic, `ORDER BY`, `OFFSET`, `DISTINCT`, aggregates over a filter) is done in PHP on the rows the server sent,
  page by page, up to `ScanLimit` rows. `LIMIT` goes to the server when nothing is left for PHP; `COUNT(*)` is counted
  there. Text compares as Cassandra compares it: case-sensitive, by code point.
- **INSERT.** A key that exists is refused (`1062`, as MySQL) after a lookup (`InsertCheck=read`), or by
  `IF NOT EXISTS` (`lwt`). `REPLACE`, `INSERT OR REPLACE` and `INSERT IGNORE` keep their meanings; `sqlUpsert()` writes
  `REPLACE` or `INSERT ... ON DUPLICATE KEY UPDATE`, which the driver answers with a lookup and an update of the named
  columns. The defaults of the table's definition fill the columns a statement leaves out.
- **New ids.** An `auto_increment` column takes the next number of its counter row in `exp_cassandra_sequence`, moved
  on with compare-and-set (a lightweight transaction), so two writers never get the same id. An id a statement gives
  itself moves the counter past it. `lastSerialID()` is this connection's last id per table.
- **UPDATE, DELETE.** The rows the WHERE finds are read and written by their key: an `UPDATE` of a missing row makes
  none (CQL's own would). A changed key deletes the old row and writes the new one. `DELETE` by the whole key needs no
  read; `DELETE` without WHERE is `TRUNCATE`.
- **Transactions.** Cassandra has none across rows. Writes go to the server at once; `rollback()` undoes this
  connection's writes of the transaction by compensating writes (an inserted row deleted, an updated or deleted row
  written back). Others may see the writes in between, and a process that dies inside a transaction leaves them.
- **Refused.** Joins, sub-selects, `GROUP BY`, `HAVING`, `UNION`, `CASE`, functions the driver does not know,
  `INSERT ... SELECT`: `expDBUnsupportedException` inside, `false` (or `eZDBException`) outside, with the name of what
  was asked. Nothing is answered wrongly or with an empty result.
- **CQL.** Every other statement is CQL already (`CREATE TABLE`, `CREATE INDEX`, `DROP`, `TRUNCATE`); `cql( $cql, $values )`
  sends CQL with bound values and returns every page.

`expCassandraSchema` makes a `.dba` table into CQL: the first field of the primary key is the partition key, the others
clustering columns (a lookup by the first field, or by all, is a key lookup); a table without a key gets
`exp_row_id uuid`; each other index becomes a secondary index on its first column (unique indexes are not enforced:
Cassandra knows no constraint but the key). CQL keeps no `NOT NULL`, default, length or `auto_increment`, so the
definition itself is kept as JSON in `exp_cassandra_schema`: the driver reads the defaults and the counted-up column
there, and the upgrade check gets the table back as it was written.

## What works and what does not

Tested on Cassandra 5.0.9 and ScyllaDB 2025.1.17 (both: the live suites, 30 tests).

| Works | Does not, or differently |
|---|---|
| A data table connection: test, tables, persistent classes (`store()`, `fetchObject()`, `fetchObjectList()` with conditions, sorts and limits, `count()`, `removeObject()`) | A site database ([why](#why-no-site-runs-on-cassandra)) |
| `expDataTableSchema` plan and apply: create, and an upgrade that adds columns; the describer | Changing a column's type (CQL changes none: held as a note); dropping a column only when asked |
| `expDataTableWriter`: insert, update, upsert by any key, unchanged rows left alone | A unique index other than the key is not enforced |
| SELECT with `=`, `<>`, `<`, `>`, `IN`, `LIKE`, `BETWEEN`, `IS NULL`, `OR`, `NOT`, arithmetic, `&`, `|`, `LOWER`, `UPPER`, `SUBSTRING`, `CONCAT`, `MD5`, `COALESCE`, `ORDER BY`, `LIMIT`, `OFFSET`, `DISTINCT`, `COUNT`, `MIN`, `MAX`, `SUM`, `AVG` | Joins, sub-selects, `GROUP BY`, `HAVING`, `UNION`, `CASE`: refused by name |
| New ids from a counter (compare and set): unique across connections and nodes | Ids are not in insertion order across writers' transactions; each new id costs a lightweight transaction |
| A duplicate key refused (`read`: a lookup; `lwt`: `IF NOT EXISTS`) | `read` is not exact under concurrent writers of the same key (use `lwt`) |
| `rollback()` by compensating writes | No isolation: others see the writes before the commit; a crash leaves them |
| Text of any size (200 KB tested), Unicode, quotes | A statement over 128 KiB on protocol 5 (the library; protocol 4 is the default) |
| Authentication (ScyllaDB with `PasswordAuthenticator`), several nodes, TLS by setting | Temporary tables, `LOCK TABLES` (does nothing), `GROUP_CONCAT` (`sqlGroupConcat()` throws) |
| The import of any table: composite keys, clustering order, `uuid`, `timestamp`, `boolean`, `decimal`, `varint`, `blob`, `list`, `map`, an index | Counters, user types and tuples are imported as values (JSON), not as their CQL types |

### Measured

The same operations on a 5,300-row table, one node of each in a container on the development database server (12
cores, load average 35 to 40 from its own sites during the run), the client PHP 8.5 on the same host. SQLite (a file)
for scale.

| Operation | Cassandra 5.0.9 | ScyllaDB 2025.1.17 | SQLite |
|---|---|---|---|
| Connect (protocol agreed, keyspace) | 345 ms | 220 ms | 32 ms |
| Create a table (with an index and its definition) | 1,839 ms | 156 ms | 4 ms |
| INSERT with a new id (counter, compare and set) | 17.6 ms | 17.8 ms | 0.08 ms |
| INSERT with its id, `InsertCheck=none` / `read` / `lwt` | 10.7 / 14.8 / 15.6 ms | 15.1 / 21.7 / 21.6 ms | 0.15 ms |
| SELECT by primary key | 2.2 ms | 0.6 ms | 0.02 ms |
| SELECT by an indexed column (about 170 rows) | 13.9 ms | 14.2 ms | 0.12 ms |
| SELECT filtered by the server (`ALLOW FILTERING`, whole table) | 28 ms | 22 ms | 0.46 ms |
| SELECT filtered in PHP (`LIKE`, whole table read) | 106 ms | 83 ms | 0.61 ms |
| SELECT ... ORDER BY ... LIMIT 10 (whole table, sorted in PHP) | 291 ms | 217 ms | 3.5 ms |
| SELECT COUNT(*) of the table | 133 ms | 25 ms | 0.02 ms |
| UPDATE by primary key (read, then write) | 10.7 ms | 1.0 ms | 0.20 ms |
| UPDATE by an indexed column (about 10 rows) | 26 ms | 24 ms | 0.12 ms |
| DELETE by primary key | 0.8 ms | 0.8 ms | 0.06 ms |
| Rollback of 100 updated rows | 194 ms | 68 ms | 0.04 ms |
| Import of 2,000 rows into SQLite (`exp:cassandra:import`) | 0.20 s | 0.51 s | |

An INSERT that gives its own id outside a transaction moves the id counter at once (a lightweight transaction per
statement, about 10 ms); inside a transaction the counter is moved once, at the commit. Bulk writes belong in
transactions for that reason.

## Why no site runs on Cassandra

The kernel was asked, not guessed. A demo site (the package `sevenx_multisite`, 291 objects, 153 tables) was installed
on SQLite and 13 page views were recorded statement by statement: the front page and five site pages, a search, the
admin login, two node views, the dashboard, the class list and a user. Each statement was put through the driver's own
parser and planner against the kernel schema as `expCassandraSchema` makes it, and the site's tables were loaded into
Cassandra and ScyllaDB (20,443 rows; the search index of 104,943 rows left out) and every SELECT replayed there.

| Measured | With warm caches | With the view cache cleared |
|---|---|---|
| Statements of the 13 page views | 1,408 | 2,087 |
| Of them SQLite's own catalogue (`sqlite_master`, `PRAGMA`; another driver asks its own) | 301 | 303 |
| **Joins** (refused) | **208** | **559** |
| `GROUP BY`, sub-selects, index hints (refused) | 5 | 10 |
| Answerable: SELECTs (key lookup 322, index 752, server filter 116, scan 18 in the cold run), writes | 893 | 1,214 |
| Page views without a refused statement | **0 of 13** | **0 of 13** |

- **Every request needs a join.** The URL of a page is resolved through `ezurlalias_ml` joined with itself, on every
  request, even when the page then comes from the view cache: the front page with warm caches sent three statements,
  all three joins. The cold front page sent 111 refused statements and 25 answerable ones.
- **What CQL can answer, it answers correctly, but slowly.** Of the 2,001 SELECTs of the cold run, Cassandra answered
  1,208 with the same rows as SQLite (15 in another order where the statement leaves the order open) and refused 793.
  The answerable ones took 12.5 s on Cassandra and 3.8 s on ScyllaDB, against 0.97 s and 0.58 s on SQLite for all
  2,001: 7 to 13 times slower before a single join is answered.
- **MongoDB's way does not carry over.** The MongoDB adapter answers joins by copying the collections a statement
  names into an in-memory SQLite database (`MongoSQLFallback`), on top of 63 MongoDB branches in 51 kernel files.
  MongoDB can filter and sort on any field; Cassandra reads a table without its key only as a whole (about 100 ms for
  5,300 rows above), so such a copy would read whole tables for each of the 559 joins of 13 page views.
- **No transactions, no temporary tables, no locks.** Publishing writes dozens of rows in one transaction, the search
  and the tree use temporary tables, the kernel locks tables for counters. Cassandra has none of these; compensation is
  no isolation, and every new id is a lightweight transaction (about 18 ms).

So a site on Cassandra would need the kernel's queries rewritten around joins, as MongoDB's port did, and would still
pay a lightweight transaction per new row and give up transactions. Exponential does not offer it; Cassandra and
ScyllaDB are where the data comes from or goes to: data table connections and an import source.

## How it is built

| File | What |
|---|---|
| `lib/ezdb/classes/expcassandradb.php` | `expCassandraDB`: connection, the planner of SELECTs, writes, ids, the rollback journal, the catalogue |
| `lib/ezdb/classes/expscylladb.php` | `expScyllaDB`: the same driver under the name `scylladb` |
| `lib/ezdb/classes/expcassandradialect.php` | `expCassandraDialect`: the SQL subset parsed, conditions and aggregates in PHP, the types of CQL and `.dba` |
| `lib/ezdb/classes/expcassandratransport.php`, `expcassandraphptransport.php` | The layer under the driver, and its implementation over `mroosz/php-cassandra` |
| `lib/ezdbschema/classes/expcassandraschema.php` | `expCassandraSchema` (`dbschema.ini`: `cassandra`, `scylladb`) |
| `kernel/classes/cassandra/expcassandrareader.php` | `expCassandraReader`: a keyspace read for the import engine of `expAccessImport` |
| `kernel/private/classes/commands/expcassandraimport.php`, `bin/php/expcassandraimport.php` | `exp:cassandra:import` |

Registered in `site.ini [DatabaseSettings] ImplementationAlias[cassandra]`, `[scylladb]`, `[scylla]`; `dbschema.ini`;
`datatable.ini [ConnectionSettings] Engines[]` (`cassandra`, `scylladb`); `expDataConnections` (engine kind
`widecolumn`), `expDataTableSchema` (create and upgrade), `expDataTableDescriber`; `expDBSQLHelpers` (dialect
`cassandra`: `upsert()` as above, `groupConcat()` refused, `unixTimestamp()` and `fromUnixTimestamp()` computed in PHP).

## Tests

- `tests/tests/lib/ezdb/cassandra/` and `tests/tests/kernel/classes/cassandra/` (testsuite `cassandra`, no server): the
  dialect (parser, refusals, conditions, projection, types, upsert, error categories), the driver over a recording
  transport (plans, statements as sent, ids, duplicate checks, rollback, errors, helpers), the schema handler, the
  import's reader.
- `.../live/` (testsuite `cassandra-live`): every test on Cassandra and on ScyllaDB: schema and ids, SELECT plans,
  inserts, updates and deletes, transactions, refusals, concurrent ids, text and foreign types; a persistent class, the
  schema plan and upgrade and the writer on a data table connection; the import. Servers: `EXP_TEST_CASSANDRA_HOST`,
  `_PORT`, `_USER`, `_PASSWORD`, `_KEYSPACE`, the same for `EXP_TEST_SCYLLADB_`; without them the tests skip.
- CI: the job "Cassandra and ScyllaDB" runs both suites on PHP 8.1 and 8.5 against Cassandra 5.0 and ScyllaDB 2025.1
  (with authentication) in containers.

Related: [Database support](../../help/database-support.md), [Data tables](data-tables.md),
[MS Access support](ms-access-support.md) (the import engine), [MongoDB as the database](mongodb-database-support.md).
