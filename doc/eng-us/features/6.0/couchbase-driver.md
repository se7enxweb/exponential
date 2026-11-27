# Couchbase driver

This page is for administrators whose organisation keeps data in Couchbase and wants that data in Exponential, or wants
a data table of Exponential kept in Couchbase, and for developers who work on the driver. From Exponential 6.0.15 the
kernel has a Couchbase driver, `expCouchbaseDB` in `lib/ezdb/classes/`, with the dialect `expCouchbaseDialect`, the schema
handler `expCouchbaseSchema` and the import command `exp:couchbase:import`.

**What it is for.** Couchbase is a document database whose query language, SQL++ (N1QL), is close to SQL: joins,
sub-selects, `GROUP BY`, `UNION`, and transactions. That made it the one document store where a whole site was worth
trying, and it was tried: the kernel's recorded statements were replayed on Couchbase and its writes measured. The
driver reaches two levels of the three a database can reach in Exponential:

| Level | Couchbase |
|---|---|
| (a) The site's database | **No.** The SQL can be answered; the speed cannot. [Why](#why-no-site-runs-on-couchbase), with the measurements |
| (b) A data table connection | **Yes.** Persistent classes, the data table layer's schema, writer and describer, the connection's test, in a scope of a bucket |
| (c) An import source | **Yes.** `exp:couchbase:import` copies the collections of a scope into the site's database or any data table connection |

## Contents

1. [Requirements](#requirements)
2. [A data table connection](#a-data-table-connection)
3. [Importing a scope](#importing-a-scope)
4. [Settings](#settings)
5. [How the SQL is answered](#how-the-sql-is-answered)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Why no site runs on Couchbase](#why-no-site-runs-on-couchbase)
8. [How it is built](#how-it-is-built)
9. [Tests](#tests)

## Requirements

| What | Where it comes from |
|---|---|
| Couchbase Server 7.2 or later, Community or Enterprise, with the Query and Index services | Tested: Community 7.6.2 (the image `couchbase/server:community-7.6.2`), and 7.2.4 in CI |
| PHP 8.0 or later with `curl` | In every PHP build Exponential runs on; no Couchbase SDK |
| A bucket, and a user with the Query roles on it (select, insert, update, delete, manage index, manage scopes) | The bucket is made in Couchbase's console or with `couchbase-cli bucket-create`; the driver makes scopes and collections |

**Why the Query service's REST API, not the SDK.** Couchbase's PHP SDK (the PECL extension `couchbase`, 4.x) is a C++
extension over Couchbase's C++ SDK, built per PHP version; no distribution or host offers it as a package. The driver
needs nothing of it but SQL++, and the Query service answers the same SQL++ over HTTP (`POST /query/service`, JSON),
transactions included (`BEGIN WORK` gives a `txid` that each statement of the transaction carries). PHP's curl extension
is enough (`expCouchbaseQueryService`, one handle per connection, so the connection is reused). What the SDK would add is
the key-value API: a write of a document by its key without the Query service, a few milliseconds faster.

## A data table connection

A connection of the engine `couchbase` is a scope of a bucket. Make it on **Setup > Data tables > Connections** or
declare it:

```ini
[Connection_catalogue]
Name=Product catalogue
Engine=couchbase
Server=cb1.example.com,cb2.example.com
Port=8093
Database=shop.catalogue
User=exponential
Secret=env:COUCHBASE_PASSWORD
```

`Database` is `bucket.scope` (a bucket alone: its `_default` scope). The scope must exist, or
`[CouchbaseSettings] AutoCreateScope=enabled`. `Server` lists the nodes of the Query service (a node that does not answer
is skipped); `https://cb1.example.com:18093` or `SSL=enabled` turns TLS on, `SSLCA` names a CA file, and
`ScanConsistency` overrides the setting of the same name. **Test connection** reports the server's version and edition
and the number of collections.

Everything of the data table layer works on it:

```php
$db = expDataConnections::db( 'catalogue' );                                 // an expCouchbaseDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myItem::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                    // the collection, its indexes, the definition
$item = new myItem( array( 'id' => null, 'sku' => 'A-1' ) );
$item->store();                                                                // id 1, 2, 3 ... from the counter
$list = myItem::fetchObjectList( myItem::definition(), null, array( 'sku' => 'A-1' ), array( 'id' => 'desc' ), 10 );
```

A table is a collection of the scope; a row is a JSON document whose fields are the columns and whose key is the
row's primary key (one column: its value, `"17"`; several: a JSON array, `["17","3"]`; a table without a key gets a
UUID). Other applications can read and write those documents with Couchbase's own tools and SDKs.

## Importing a scope

```bash
./console exp:couchbase:import --source=catalogue --list
./console exp:couchbase:import --source=catalogue --prefix=cb_                       # the plan: tables, columns, SQL
./console exp:couchbase:import --source=catalogue --prefix=cb_ --apply               # into the site's database
./console exp:couchbase:import --server=cb1,cb2 --bucket=travel-sample.inventory --user=reader --secret=env:CB_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

The command reads every collection of the scope (or `--tables`), makes it in the target through the data table layer
(the site's database, or `--connection`), and copies the documents page by page, in the order of their keys. A
collection the driver made keeps its columns, types and key. Any other collection gets the top-level fields of a sample
of its documents (`--sample`, 500) and `doc_id`, the document's key, as its primary key; the types are inferred as for
[CouchDB](couchdb-driver.md#importing-a-database) (integers, `double`, booleans as 1/0, text, objects and arrays as JSON).
The command uses the import engine of `exp:access:import`: the modes `create`, `append` and `replace`, the names and
the reports are the same.

## Settings

`site.ini [CouchbaseSettings]`:

| Setting | Default | What |
|---|---|---|
| `Scope` | empty | The scope when `Database` names the bucket alone (empty: `_default`) |
| `ScanConsistency` | `request_plus` | `request_plus`: a read through an index waits until the index has every write before it (exact); `not_bounded`: the index as it is (fast, may miss the last writes); `auto`: `request_plus` after this connection wrote |
| `TransactionTimeout` | `120s` | The longest a transaction may stay open |
| `DurabilityLevel` | empty | Of a transaction's writes (the server's default is `majority`; `none` on one node with replicas configured) |
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, empty, `enabled` | HTTPS to the Query service |
| `ConnectTimeout`, `RequestTimeout` | `5`, `75` | Seconds |
| `AutoCreateScope` | `disabled` | Make a missing scope when the connection opens |
| `PrimaryIndex` | `enabled` | A primary index on every collection the driver makes (statements no other index serves need it) |
| `IndexWait` | `120` | Seconds to wait for the indexes of a new collection to come online |
| `TranslationCache` | `500` | SELECTs whose SQL++ a connection keeps |

## How the SQL is answered

The persistent classes and the data table layer write SQL; the driver's dialect is MySQL's forms (`sqlHelperDialect()`
is `couchbase`: `LIMIT`, `ON DUPLICATE KEY UPDATE`, `GROUP_CONCAT`), and `expCouchbaseDialect` turns each statement into
SQL++:

- **Names** are quoted with backticks (SQL++ reserves many more words: `value`, `user`, `path` ...) and written in lower
  case. A column without its table in a statement over several tables gets the table that has it (SQL++ reads an
  unqualified name there as a table). A sub-select that repeats a table name of the query around it gets a name of its
  own (SQL++ refuses the repeat).
- **Types.** SQL++ compares types strictly (`1 = '1'` is false); MySQL converts. The driver keeps each table's
  definition (in the collection `exp_couchbase_schema`), so a string compared with a numeric column becomes a number and
  a number compared with a text column a string, in `=`, `IN`, `BETWEEN` and `LIKE`; an `INSERT` or `UPDATE` stores each
  value as its column's type.
- **`NULL` and MISSING.** An `INSERT` writes every column (the definition's defaults for those it leaves out); `IS NULL`
  is `IS NOT VALUED`, so the missing side of a `LEFT JOIN` is NULL as in SQL.
- **Operators and functions**: `a & b`, `a | b` are `BITAND()`, `BITOR()`; `MD5('literal')` is computed by the driver
  (SQL++ has no MD5); `CONCAT`, `SUBSTRING` (`SUBSTR1`), `IFNULL` and `COALESCE` (`IFMISSINGORNULL`), `CAST`, `IF`,
  `LENGTH`, `UNIX_TIMESTAMP`, `FROM_UNIXTIME`, `GROUP_CONCAT` (`CONCAT2` over `ARRAY_AGG`).
- **SELECT** `*` is `SELECT t.*`; the rows come back in the order and with the names of the select list (SQL++ sorts an
  object's fields), an expression without a name named by its text as MySQL names it; a sub-select in an expression is
  `SELECT RAW ...[0]`, after `IN` `SELECT RAW ...`; `LIMIT o, n` is `LIMIT n OFFSET o`. `COUNT` over a whole collection
  gets a filter on the key: the collection's statistics, which answer it otherwise, lag behind the writes.
- **Keys.** A `WHERE` that names the whole primary key with `=` or `IN` reads the documents by their keys (`USE KEYS`):
  no index, no wait for one. Everything else goes through the indexes the schema handler made (one per index of the
  definition, and a primary index).
- **INSERT** writes documents with their keys (`INSERT INTO t (KEY, VALUE) VALUES ...`); a key that exists is error
  **1062**; `REPLACE` is `UPSERT`, `INSERT IGNORE` skips a duplicate, `ON DUPLICATE KEY UPDATE` updates the document by
  its key. `INSERT ... SELECT` is one SQL++ statement. **New ids**: the counted-up column takes the next number of the
  counter document of its table in `exp_couchbase_sequence` (`UPDATE ... USE KEYS ... SET v = v + n RETURNING`, atomic,
  outside the transaction, as MySQL's `auto_increment`); an id a statement gives itself moves the counter past it.
- **UPDATE** and **DELETE** are SQL++'s own; an `UPDATE` that changes a key column moves the documents to their new keys.
- **Transactions** are Couchbase's: `BEGIN WORK`, every statement with the `txid`, `COMMIT WORK`, `ROLLBACK WORK`.
  Reads in a transaction see its writes; others do not until the commit. DDL and the id counters run outside it.
- **DDL.** `CREATE TABLE` makes the collection, a primary index and the indexes of the definition (created deferred and
  built together), waits until they are online, and keeps the definition; `ALTER TABLE ... ADD` gives every document the
  new field with its default, `DROP COLUMN` removes it, `MODIFY` changes the definition. Native SQL++ can be sent with
  `$db->send( $statement, $options )`, or through `query()` behind a leading `/*sqlpp*/`.
- **Refused** by name, never answered wrongly: `RIGHT` and `FULL JOIN`, `MD5` of a column, `ORDER BY` a position, `DIV`,
  `XOR`, an `UPDATE` or `DELETE` of several tables, an unknown function: `expDBUnsupportedException` inside, `false` (or
  `eZDBException`) with error 1235 outside.

## What works and what does not

Tested on Couchbase Server Community 7.6.2 (the live suites, the replay below) and against a recording stand-in (the
DB-less suite).

| Works | Does not, or differently |
|---|---|
| A data table connection: test, collections, persistent classes (`store()`, `fetchObject()`, `fetchObjectList()` with conditions, sorts and limits, `count()`, `removeObject()`) | A site database ([why](#why-no-site-runs-on-couchbase)) |
| `expDataTableSchema` plan and apply: create, and an upgrade that adds columns; the describer (from the kept definition) | Making a table takes one to two minutes (a collection and its indexes, built and waited for) |
| `expDataTableWriter`: insert, update, upsert by any key, unchanged rows left alone | A unique index other than the key is not enforced (Couchbase knows no unique index) |
| SELECT with joins (comma joins, `JOIN`, `LEFT JOIN`, `USING`), sub-selects (`IN`, `EXISTS`, scalar), derived tables, `GROUP BY`, `HAVING`, `UNION`, `DISTINCT`, `CASE`, `LIKE`, `BETWEEN`, bit operations, the functions listed above | A comma join followed by `JOIN ... ON` in one `FROM` (SQL++ refuses it); `RIGHT` and `FULL JOIN`; `MD5` of a column; `ORDER BY` a position |
| Real transactions: isolation, `ROLLBACK`, reads that see the transaction's writes | `LOCK TABLES` does nothing (a transaction is the lock); a commit of many writes takes long |
| New ids from counter documents: unique across connections, outside the transaction as MySQL's | A rolled-back insert leaves a gap in the ids, as on MySQL |
| A duplicate key refused (1062); `REPLACE`, `INSERT IGNORE`, `ON DUPLICATE KEY UPDATE`, `INSERT ... SELECT`; a changed key moves the document | `ScanConsistency=not_bounded` is fast but may miss the last writes of others |
| Text of any size (100 KB tested), Unicode, quotes and backslashes; numbers compared and stored as their column's type | Changing a column's type changes the definition, not the stored values |
| The import of any collection: driver tables with their definitions, other documents with sampled fields and `doc_id` | Fields a sample did not meet are not imported (`--sample`) |

### Measured

The operations of a data table on a table of 5,300 rows, Couchbase Server Community 7.6.2 on one node in a container
capped at two cores and 3 GB on the development database server (12 cores, a load of 20 to 50 from its own sites during
the run), the client PHP 8.5 on the same host, `ScanConsistency=request_plus`. The node held this table only; with the
site of the replay below in the same bucket (154 more collections, about 440 indexes) the same benchmark could not
finish: a read through an index after the 5,300 inserts did not come back within 100 s, the indexer behind its writes.

| Operation | Couchbase 7.6.2 |
|---|---|
| Connect (the version, the scope) | 571 ms |
| Create a table (the collection, a primary index, its indexes built and online, the definition) | 108 s |
| INSERT, 100 rows a statement | 3.2 ms a row |
| INSERT of one row with a new id (the counter, then the document) | 15 ms |
| SELECT by primary key (`USE KEYS`) | 5.4 ms |
| SELECT by an indexed column (about 170 rows) | 34 ms |
| SELECT by a column without an index (the primary index, the whole collection) | 1.0 s |
| SELECT ... LIKE, ORDER BY ... LIMIT 10, GROUP BY (the whole collection) | 0.9 s, 1.2 s, 1.0 s |
| SELECT COUNT(*) of the table | 34 ms |
| UPDATE by primary key | 26 ms |
| UPDATE by key, then a SELECT through an index (the wait for the indexer) | 206 ms |
| UPDATE by an indexed column (about 170 rows) | 348 ms |
| DELETE by primary key | 9.3 ms |
| 100 updates by key in a transaction, rolled back / committed | 0.73 s / 0.98 s |
| A join of the table with itself on the key, 50 rows | 1.3 s |

Reads by key and through an index take milliseconds; a read that must see a write before it waits about 200 ms for
the indexer; whatever needs a whole collection costs about a second for 5,300 documents. For a data table that is
workable; give its conditions indexes, and keep writes and the reads that depend on them in a transaction.

## Why no site runs on Couchbase

Couchbase was the serious candidate: SQL++ has the joins, sub-selects, `GROUP BY` and transactions the kernel uses,
and the Community edition does all of them (comma joins, `LEFT JOIN`, correlated `EXISTS`, `BEGIN WORK` with
isolation, all measured on 7.6.2). So the kernel was asked. A demo site (the package `sevenx_multisite`, 291 objects,
154 tables) installed on SQLite was copied into a scope through the driver itself (its `CREATE TABLE` and `INSERT`;
the search index of 104,943 rows left out), and the SELECTs of 13 recorded page views with the view cache cleared
(the front page and five site pages, a search, the admin login, two node views, the dashboard, the class list and a
user) were replayed on both, translated by `expCouchbaseDialect`:

| Measured | |
|---|---|
| SELECTs replayed (SQLite's own catalogue left out) | 1,778 |
| Answered | 1,774 (4 refused by Couchbase: a comma join followed by an `INNER JOIN` in one `FROM`) |
| **Same rows as SQLite** | **1,751 (98.5 %)**; of the 23 others, 14 are the replay's own (the plain SQLite file has no `MD5()`, an SQLite `rowid`), 9 are joins on language masks that still answer differently |
| Time on SQLite / on Couchbase | **1.9 s / 282 s (147 times)**: 0.15 s against 21.7 s a page view |
| One SELECT on Couchbase, on average | 160 ms (a key lookup about 8 ms; through an index 20 to 60 ms; a join of several collections up to seconds) |
| The site's tables made (154 collections, a primary index each and 436 indexes in all) | **about 1 h 40 min**, 20 to 75 s a table, slower as their number grew; once the Query service did not answer for 75 s |
| The site's 20,443 rows written | 108 s (5.3 ms a row, 200 rows a statement) |

- **The SQL can be answered.** 98.5 % of the kernel's reads came back right in the replay, after one round of
  fixes the replay showed (a sub-select's unqualified column, read by SQL++ as the outer row's field). The rest are
  edge cases that could be closed.
- **The speed cannot.** Reads alone are 147 times SQLite's: every statement is an HTTP request that the Query service
  parses, plans and runs against the Index service. A page view that SQLite answers in 0.15 s took 21.7 s.
- **Writes are worse.** An index of Couchbase is updated after the write. A read that must see the writes before it
  (`scan_consistency=request_plus`, which the kernel's read-after-write needs) waits for the indexer: about **210 ms**
  after each write here, against 10 to 14 ms for the write itself. Inside a transaction the reads see its writes at
  once, but a commit of 50 inserts took about **1 s**. Publishing an object writes and reads back dozens of rows.
- **The schema is heavy for a small node.** The kernel's 154 tables need 154 collections and about 440 indexes; on one
  node with 3 GB the indexer and the Query service slowed down to over a minute a table and used the whole memory. A
  site install would spend most of two hours making its tables before the first row.

A full site install on Couchbase was therefore not attempted: the measurements above already put every page view
at tens of seconds. Couchbase stays where it fits: a data table connection and an import source.

## How it is built

| File | What |
|---|---|
| `lib/ezdb/classes/expcouchbasedb.php` | `expCouchbaseDB`: the connection, the catalogue of definitions, SELECT, INSERT with keys and ids, UPDATE with moved keys, DDL, transactions |
| `lib/ezdb/classes/expcouchbasedialect.php` | `expCouchbaseDialect`: the SQL as SQL++ (names, types, operators, functions, sub-selects, key lookups), INSERT and DDL parsed |
| `lib/ezdb/classes/expcouchbasequeryservice.php` | `expCouchbaseQueryService`: the Query service's REST API over curl |
| `lib/ezdb/classes/expcouchsqllexer.php` | `expCouchSQLLexer`: the tokens of a statement (shared with the CouchDB driver) |
| `lib/ezdbschema/classes/expcouchbaseschema.php` | `expCouchbaseSchema` (`dbschema.ini`: `couchbase`) |
| `kernel/classes/couch/expcouchbasereader.php` | `expCouchbaseReader`: a scope read for the import engine of `expAccessImport` |
| `kernel/private/classes/commands/expcouchbaseimport.php`, `bin/php/expcouchbaseimport.php` | `exp:couchbase:import` |

Registered in `site.ini [DatabaseSettings] ImplementationAlias[couchbase]`; `dbschema.ini`; `datatable.ini
[ConnectionSettings] Engines[]` (`couchbase`); `expDataConnections` (engine kind `document`), `expDataTableSchema` (create
and upgrade), `expDataTableDescriber`, `expKernelTableSchema::tables()`; `expDBSQLHelpers` (dialect `couchbase`).

## Tests

- `tests/tests/lib/ezdb/couch/` and `tests/tests/kernel/classes/couch/` (testsuite `couch`, no server): the lexer and the
  dialect (`expCouchbaseDialectTest`), the driver over a recording stand-in for the Query service (`expCouchbaseDBTest`:
  requests, rows, documents and keys, ids, upserts, moved keys, transactions, DDL), the reader.
- `.../live/` (testsuite `couch-live`): the driver (`expCouchLiveTest`), the data table connection
  (`expCouchDataTableLiveTest`) and the import (`expCouchImportLiveTest`), the same scenarios as on CouchDB. Server:
  `EXP_TEST_COUCHBASE_HOST`, `_PORT`, `_USER`, `_PASSWORD`, `_BUCKET`; without it the tests skip.
- CI: the job "Couchbase and CouchDB" runs both suites on PHP 8.5 and 8.1 against Couchbase Server Community 7.6.2 and
  7.2.4 in containers.

Related: [Apache CouchDB driver](couchdb-driver.md), [Database support](../../help/database-support.md),
[Choosing a database](../../help/choosing-a-database.md), [Data tables](data-tables.md),
[MongoDB as the database](mongodb-database-support.md), [MS Access support](ms-access-support.md) (the import engine).
