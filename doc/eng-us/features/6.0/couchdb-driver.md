# Apache CouchDB driver

This page is for administrators whose organisation keeps data in Apache CouchDB and wants that data in Exponential, or
wants a data table of Exponential kept in CouchDB, and for developers who work on the driver. From Exponential 6.0.15
the kernel has a CouchDB driver, `expCouchDBDB` in `lib/ezdb/classes/`, with the schema handler `expCouchDBSchema` and
the import command `exp:couchdb:import`.

**What it is for.** CouchDB is a document store reached over HTTP: JSON documents with revisions, queried with Mango
(selectors on fields) or with views; it has no SQL, no joins and no transactions. The driver reaches two levels of the
three a database can reach in Exponential:

| Level | CouchDB |
|---|---|
| (a) The site's database | **No.** [Why](#why-no-site-runs-on-couchdb), with the measurements |
| (b) A data table connection | **Yes.** Persistent classes, the data table layer's schema, writer and describer, the connection's test |
| (c) An import source | **Yes.** `exp:couchdb:import` copies the documents of a database (one table, or one per value of a type field) into the site's database or any data table connection |

## Contents

1. [Requirements](#requirements)
2. [A data table connection](#a-data-table-connection)
3. [Importing a database](#importing-a-database)
4. [Settings](#settings)
5. [How the SQL is answered](#how-the-sql-is-answered)
6. [What works and what does not](#what-works-and-what-does-not)
7. [Why no site runs on CouchDB](#why-no-site-runs-on-couchdb)
8. [How it is built](#how-it-is-built)
9. [Tests](#tests)

## Requirements

| What | Where it comes from |
|---|---|
| Apache CouchDB 3.3 or 3.4 | Tested: CouchDB 3.4.3 (the image `couchdb:3.4.3`), and 3.3.3 in CI |
| PHP 8.0 or later with `curl` and `sqlite3` | Both are in every PHP build Exponential runs on; no library, no extension of CouchDB's own |

**Why HTTP.** CouchDB has one interface, its HTTP API; there is no binary protocol and no PHP extension to choose. The
driver speaks it with PHP's curl extension (`expCouchDBClient`, one handle per connection so the TCP connection is
reused). A SQL interface for CouchDB does not exist; that is why the driver answers SQL on an in-memory SQLite copy
(below), and why it needs PHP's `sqlite3`.

## A data table connection

A connection of the engine `couchdb` is one CouchDB database. Make it on **Setup > Data tables > Connections** or
declare it:

```ini
[Connection_orders]
Name=Order store
Engine=couchdb
Server=couch1.example.com
Port=5984
Database=orders
User=exponential
Secret=env:COUCHDB_PASSWORD
```

`Database` is the CouchDB database and must exist (or `[CouchDBSettings] AutoCreateDatabase=enabled`). `Server` may be
written `https://couch1.example.com:6984` for TLS (or set `SSL=enabled`; `SSLCA` names a CA file). **Test connection**
reports CouchDB's version and the number of tables.

Everything of the data table layer works on it:

```php
$db = expDataConnections::db( 'orders' );                                    // an expCouchDBDB
$plan = expDataTableSchema::plan( expDataTableSchema::fromDefinition( myOrder::definition() ), $db );
expDataTableSchema::apply( $schema, $db );                                    // the definition kept, Mango indexes made
$order = new myOrder( array( 'id' => null, 'state' => 'new' ) );
$order->store();                                                               // id 1, 2, 3 ... from the counter
$list = myOrder::fetchObjectList( myOrder::definition(), null, array( 'state' => 'new' ), array( 'id' => 'desc' ), 10 );
```

A table is the set of documents whose field `exp_table` names it; a row is one document, its `_id` the table and the
primary key (`order:17`; a key of several columns as a JSON array, `line:["17","3"]`; a table without a key gets a
UUID). Other applications can read and write those documents with CouchDB's own tools.

## Importing a database

```bash
./console exp:couchdb:import --source=orders --list
./console exp:couchdb:import --source=orders --type-field=type --prefix=shop_              # the plan
./console exp:couchdb:import --source=orders --type-field=type --prefix=shop_ --apply      # into the site's database
./console exp:couchdb:import --server=couch1 --database=shop --user=reader --secret=env:COUCH_PASSWORD \
    --connection=archive --mode=replace --apply --format=json
```

Which documents make a table:

- **`--type-field=type`**: one table per value of that field (a document without it is left out);
- **a database the CouchDB driver wrote**: one table per `exp_table`, with the definition the driver kept (columns,
  types, the key);
- **otherwise**: one table, named after the database, of every document. Design documents are always left out.

A table without a kept definition gets the top-level fields of a sample of its documents (`--sample`, 500) and
`doc_id`, the document's `_id`, as its primary key. `_rev`, `_attachments` and `_conflicts` are left out. The types are
inferred:

| In the documents | In the target |
|---|---|
| only integers | `int` (`bigint` beyond 32 bits) |
| numbers | `double` |
| `true`, `false` | `int`, 1 or 0 |
| text no longer than 255 characters | `varchar(255)` |
| longer text | long text |
| objects, arrays | long text, JSON |

The command uses the import engine of `exp:access:import`, so the modes (`create`, `append`, `replace`), the names (lower
case ASCII, a prefix, keywords with a trailing `_`) and the reports are the same. A field a later document has but the
sample had not is not imported: raise `--sample`.

## Settings

`site.ini [CouchDBSettings]` (a connection's `SSL` and `SSLCA` override `TLS` and `TLSCAFile`):

| Setting | Default | What |
|---|---|---|
| `TLS`, `TLSCAFile`, `TLSVerifyPeer` | `disabled`, empty, `enabled` | HTTPS to the node |
| `ConnectTimeout`, `RequestTimeout` | `5`, `60` | Seconds |
| `AutoCreateDatabase` | `disabled` | Make a missing database when the connection opens |
| `ScanLimit` | `100000` | The most documents a statement may read; more is refused, never answered from a part |
| `BatchSize` | `1000` | Documents a request reads or writes |
| `JournalLimit` | `10000` | Writes of a transaction a rollback can undo |

## How the SQL is answered

The persistent classes and the data table layer write SQL; the driver's dialect is SQLite's (`sqlHelperDialect()` is
`sqlite`), so their upserts, limits and `GROUP_CONCAT` are written for SQLite. Each statement is answered in two steps:

1. **The documents it needs are read from CouchDB**: every table the statement names. Where it reads one table and
   compares columns with `=` to literals, only those: by their `_id` when the comparisons name the whole primary key (one
   request), with Mango when they name the first column of an index of the table (the driver makes a Mango index per
   index of the definition, and one on `exp_table`), else by the range of the table's ids (`_all_docs`, `table:` ...),
   filtered by the driver. Mango without an index evaluates its selector on every document of the database, several
   times slower than the id range here.
2. **The statement runs on an in-memory SQLite copy** of those documents (the columns with SQLite's types, the
   definition's defaults, the primary key and unique indexes). The answer is SQLite's, exactly: joins, `GROUP BY`,
   sub-selects, `CASE`, functions.

A **write** runs on the copy too. Triggers on the copy record the rows it inserted, changed and deleted, and the driver
writes those back with `_bulk_docs`, each change with the revision it was read at:

- a document someone else changed in between is a **conflict**: the statement fails, nothing is lost silently;
- an `INSERT` of a key that exists is error **1062** (CouchDB's conflict on the `_id`); `INSERT OR IGNORE` skips it;
  `REPLACE` and SQLite's `ON CONFLICT ... DO UPDATE` (the helpers' upsert) read the table so the copy sees the row;
- a changed key moves the document (the old one is deleted, the new one written);
- **new ids**: the counted-up column takes the next numbers of the counter document `exp_couchdb_sequence:<table>`,
  moved on with its revision (compare and set, retried on a conflict), so two writers never get the same id; an id a
  statement gives itself moves the counter past it.

**Transactions.** CouchDB has none. Writes reach the server at once; `rollback()` undoes this connection's writes of the
transaction by compensating writes (an inserted document deleted, a changed or deleted one written back), up to
`JournalLimit`. Others see the writes in between, and a process that dies inside a transaction leaves them.

**DDL.** `CREATE TABLE` (the schema handler writes MySQL's form; SQLite's `INTEGER PRIMARY KEY AUTOINCREMENT` is read too)
keeps the definition in the document `exp_couchdb_schema:<table>` and makes the Mango indexes; `DROP TABLE` deletes the
table's documents, its definition, counter and indexes; `ALTER TABLE ... ADD` gives every document the new field with its
default, `DROP COLUMN` removes it, `MODIFY` changes the definition only; `TRUNCATE` deletes the documents. A column whose
name starts with `_` is refused (CouchDB's own fields).

## What works and what does not

Tested on CouchDB 3.4.3 (the live suites) and against a stand-in in memory (the DB-less suite).

| Works | Does not, or differently |
|---|---|
| A data table connection: test, tables, persistent classes (`store()`, `fetchObject()`, `fetchObjectList()` with conditions, sorts and limits, `count()`, `removeObject()`) | A site database ([why](#why-no-site-runs-on-couchdb)) |
| `expDataTableSchema` plan and apply: create, and an upgrade that adds columns; the describer (from the kept definition) | Changing a column's type changes the definition, not the documents |
| `expDataTableWriter`: insert, update, upsert by any key, unchanged rows left alone | Isolation: others see a transaction's writes at once; a rollback is compensation |
| All of SQLite's SQL on the tables a statement names: joins, `GROUP BY`, `HAVING`, sub-selects, `UNION`, `CASE`, functions | Statements that need more than `ScanLimit` documents are refused |
| New ids from a counter document (compare and set): unique across connections | Each statement reads the documents it needs again: there is no cache between statements |
| A duplicate key refused (1062), a write conflict reported | Data in attachments (not read) |
| Text of any size (100 KB tested), Unicode, quotes and backslashes | A field named `_...` |
| The import of any database: one table, by a type field, or the driver's own tables | Attachments are not imported |

### Measured

The operations of a data table on a table of 5,300 rows, CouchDB 3.4.3 in a container capped at two cores and 512 MB
on the development database server (12 cores, a load of 20 to 50 from its own sites during the run), the client PHP
8.5 on the same host:

| Operation | CouchDB 3.4.3 |
|---|---|
| Connect (the welcome and the database) | 86 ms |
| Create a table (its definition and a Mango index) | 141 ms |
| INSERT, 100 rows a statement | 6.6 ms a row |
| INSERT of one row with a new id (the counter's compare and set, then the document) | 305 ms |
| SELECT by primary key (the document by its id) | 15 ms |
| SELECT by an indexed column (about 170 rows, Mango) | 961 ms |
| SELECT by a column without an index (the table's id range, filtered) | 5.9 s |
| SELECT ... LIKE, ORDER BY ... LIMIT 10, COUNT(*), GROUP BY (the whole table read) | 8.1 s, 9.4 s, 9.6 s, 10.2 s |
| UPDATE by primary key | 267 ms |
| UPDATE by key, then a SELECT through the index | 1.8 s |
| UPDATE by an indexed column (about 170 rows) | 2.7 s |
| DELETE by primary key | 137 ms |
| 100 updates by key in a transaction, rolled back / committed | 22.8 s / 22.0 s |
| A join of the table with itself on the key, 50 rows | 9.0 s |

What is read by key or through an index is answered in milliseconds to a second; whatever needs the whole table
costs the time to read it (here about 900 documents a second). Keep CouchDB tables small or give their conditions an
index, and write in batches: one statement of 100 rows costs about what two single inserts do.

## Why no site runs on CouchDB

The kernel was asked, not guessed. A demo site (the package `sevenx_multisite`, 291 objects, 154 tables) installed on
SQLite was copied into CouchDB through the driver (154 tables in 121 s; 20,443 rows in 179 s, 8.8 ms a row; the
search index of 104,943 rows left out), and the SELECTs of 13 recorded page views with the view cache cleared (the
front page and five site pages, a search, the admin login, two node views, the dashboard, the class list and a user)
were replayed on both:

| Measured | |
|---|---|
| SELECTs replayed (SQLite's own catalogue left out) | 1,778 |
| Answered with SQLite's rows | 1,537 (the others: `MD5()` missing in the copy, `SELECT *` listing the copy's own columns, an SQLite index hint; the first two are fixed since) |
| Time on SQLite / on CouchDB | **2.3 s / 803 s (352 times)**: 0.18 s against 62 s a page view |
| One SELECT on CouchDB, on average | 450 ms |

- **Every statement reads its tables again.** CouchDB has no joins, so a statement over three tables reads the
  documents of all three (by id range or Mango), and nothing is kept from one statement to the next. The kernel sends
  about 160 statements a page view with cold caches, most of them joins; reading the tables they name is what the 803
  seconds are.
- **Mango is not an SQL index.** A selector served by an index still reads the documents through CouchDB's query
  server; without one it evaluates every document of the database. 5,300 documents took 6 to 10 s here by the id
  range and 11 to 17 s with Mango (CouchDB in a container capped at two cores, on a host with a load of 20 to 50).
- **No transactions.** Publishing writes dozens of rows that must appear together or not at all; the driver's
  compensation undoes its own writes but gives no isolation, and a process that dies leaves half a version.

So CouchDB holds data next to a site, not the site: a data table connection, or the source of an import.

## How it is built

| File | What |
|---|---|
| `lib/ezdb/classes/expcouchdbdb.php` | `expCouchDBDB`: the connection, reading documents (by id, Mango, id range), the in-memory copy, writes with revisions, ids, the rollback journal, DDL |
| `lib/ezdb/classes/expcouchdbclient.php` | `expCouchDBClient`: CouchDB's HTTP API over curl |
| `lib/ezdb/classes/expcouchsqllexer.php` | `expCouchSQLLexer`: the tokens of a statement (shared with the Couchbase driver); the DDL parser is `expCouchbaseDialect::ddl()` |
| `lib/ezdbschema/classes/expcouchdbschema.php` | `expCouchDBSchema` (`dbschema.ini`: `couchdb`) |
| `kernel/classes/couch/expcouchdbreader.php` | `expCouchDBReader`: a database read for the import engine of `expAccessImport` |
| `kernel/private/classes/commands/expcouchdbimport.php`, `bin/php/expcouchdbimport.php` | `exp:couchdb:import` |

Registered in `site.ini [DatabaseSettings] ImplementationAlias[couchdb]`; `dbschema.ini`; `datatable.ini
[ConnectionSettings] Engines[]` (`couchdb`); `expDataConnections` (engine kind `document`), `expDataTableSchema` (create
and upgrade), `expDataTableDescriber`, `expKernelTableSchema::tables()`.

## Tests

- `tests/tests/lib/ezdb/couch/expCouchDBDBTest.php` and `tests/tests/kernel/classes/couch/expCouchReadersTest.php`
  (testsuite `couch`, no server): the driver over `expCouchDBFake`, a small CouchDB in memory (revisions, conflicts,
  `_find`, `_bulk_docs`, `_all_docs`, indexes); the reader.
- `.../live/` (testsuite `couch-live`): the driver (`expCouchLiveTest`: schema, ids, selects with joins and sub-selects,
  writes, transactions, upserts, ALTER, text), the data table connection (`expCouchDataTableLiveTest`) and the import
  (`expCouchImportLiveTest`). Server: `EXP_TEST_COUCHDB_HOST`, `_PORT`, `_USER`, `_PASSWORD`; without it the tests skip.
- CI: the job "Couchbase and CouchDB" runs both suites on PHP 8.5 and 8.1 against CouchDB 3.4.3 and 3.3.3 in containers.

Related: [Couchbase driver](couchbase-driver.md), [Database support](../../help/database-support.md),
[Choosing a database](../../help/choosing-a-database.md), [Data tables](data-tables.md),
[MS Access support](ms-access-support.md) (the import engine), [MongoDB as the database](mongodb-database-support.md).
