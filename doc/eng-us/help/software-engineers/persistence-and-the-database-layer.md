---
title: Persistence and the database layer
audience: software-engineer, developer
category: software-engineering
order: 160
summary: How Exponential talks to its database - the eZDB factory and drivers, eZPersistentObject as the table mapper, transactions that nest by counting, the SQL query cache, isolation levels, and the rules every query must follow - with the differences an engineer from an ORM background should expect.
keywords: persistence, database layer, eZDB, eZDBInterface, eZPersistentObject, definition, fetchObjectList, transactions, begin, commit, rollback, isolation level, query cache, ORM, active record, software engineer
type: help
---
# Persistence and the database layer

This page is for engineers who write or review code that reads and writes the database. Exponential has no ORM in
the sense of Doctrine or Hibernate: it has a database factory with one driver per engine, and an active-record style
mapper, `eZPersistentObject`, with one PHP class per table. The authoritative rules are in
[Database and SQL standards](../../standards/databases-and-sql.md).

## Contents

1. [The pieces](#the-pieces)
2. [eZPersistentObject](#ezpersistentobject)
3. [Raw queries](#raw-queries)
4. [Transactions](#transactions)
5. [The SQL query cache](#the-sql-query-cache)
6. [If you come from an ORM](#if-you-come-from-an-orm)

## The pieces

| Piece | Where | Role |
|---|---|---|
| `eZDB` | `lib/ezdb/classes/` | factory: `eZDB::instance()` returns the connection of the current siteaccess |
| `eZDBInterface` | `lib/ezdb/classes/` | the common interface of every driver |
| Drivers | `eZSQLite3DB`, `eZPostgreSQLDB`, `eZMySQLiDB`, `expMongoDB`, `eZNullDB`; `eZOracleDB` from `ezoracle` | one per engine ([Databases: choosing and running one](databases-and-choosing-one.md)) |
| `eZPersistentObject` | `kernel/classes/ezpersistentobject.php` | maps one class to one table |
| `eZDBQueryCache` | `lib/ezdb/classes/ezdbquerycache.php` | optional cache of `SELECT` results |
| Schema handlers | `lib/ezdbschema/classes/` | read and write `.dba` schema files per engine |

The driver is chosen by `site.ini [DatabaseSettings] DatabaseImplementation`, resolved through `ImplementationAlias[]`.

## eZPersistentObject

A subclass returns its table description from `static function definition()`: `fields`, `keys`, `increment_key`,
`class_name`, `name` (the table) and `function_attributes`. Reads use `eZPersistentObject::fetchObject()` and
`fetchObjectList()`; writes use `store()` and `remove()`. These methods escape values for you.

New tables should get a persistent class rather than hand-written SQL, and their schema must reach
`share/db_schema.dba`, the update files of every driver, and every supported driver
([Schema changes and migrations](schema-changes-and-migrations.md)). Worked examples:
[Persistent objects and custom tables](../creators/app-creators/persistent-objects-and-custom-tables.md) and the
[eZPersistentObject reference](../extension-development/part-8-reference/ezpersistentobject.md). The pattern is
described in the catalogue as [Active record](../../standards/patterns/active-record.md).

## Raw queries

Statements are built as strings, so two rules are mandatory for any value from a request, a setting or the database:

1. **Escape every string** with `$db->escapeString( $value )`, inside single quotes.
2. **Cast every number** with `(int)` before it goes into SQL.

```php
$db = eZDB::instance();
$rows = $db->arrayQuery(
    "SELECT id, name FROM ezsection WHERE identifier = '" . $db->escapeString( $identifier ) . "'"
    . " AND id <> " . (int)$excludeId,
    array( 'offset' => 0, 'limit' => 10 )
);
```

Pass offset and limit as parameters, as above, so each driver writes its own `LIMIT` syntax. The dialect differences
are listed on [Writing portable SQL](writing-portable-sql.md).

## Transactions

- `$db->begin()` and `$db->commit()` nest by counting: only the outermost pair sends `BEGIN` and `COMMIT`.
  `$db->rollback()` rolls back the whole transaction.
- Multi-statement writes in kernel code must be wrapped in `begin()` and `commit()`.
- **No driver sets an isolation level**; each engine runs at its own default. Code that depends on one is
  engine-dependent.
- SQLite serializes writers: the driver takes a lock file and runs `BEGIN IMMEDIATE`, waiting up to
  `[DatabaseSettings] SQLiteTransactionWait` seconds ([SQLite transactions](../../bc/6.0/sqlite-transactions.md)).
- MongoDB writes are not transactional: each operation commits on its own.

## The SQL query cache

`querycache.ini` can keep `SELECT` results in memory until a write to one of the tables read makes them stale. It is
off by default (`Mode=off`), and the MongoDB driver does not use it. Once it is on, anything that writes to the
database from outside Exponential (a SQL client, a restore, another program) must clear it afterwards:

```bash
php bin/php/ezcache.php --clear-id=querycache --allow-root-user
```

See [The SQL query cache](../../bc/6.0/sql-query-cache.md).

## If you come from an ORM

| You may expect | In Exponential |
|---|---|
| Entities with relations and lazy loading | a definition array per table; related objects are loaded by explicit calls or `function_attributes` |
| A unit of work that flushes changes | `store()` writes at once |
| Prepared statements with bound parameters | string building with `escapeString()` and casts |
| Generated migrations | `.dba` schema files and SQL update files per driver, written and applied by hand |
| A repository per aggregate | static fetch methods on the persistent class, and fetch functions for templates |

## Related pages

- [Database access](../creators/app-creators/database-access.md)
- [eZDB reference](../extension-development/part-8-reference/ezdb.md)
- [Writing portable SQL](writing-portable-sql.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
