---
title: Database access and SQL
audience: app-creator, developer
category: app-creation
order: 220
summary: Talk to the database safely and portably - eZDB, arrayQuery and query, escaping and casting, transactions, the six supported engines and what differs between them, and when to use eZPersistentObject instead.
keywords: database, SQL, eZDB, arrayQuery, query, escapeString, transactions, begin, commit, rollback, SQLite, PostgreSQL, MySQL, MariaDB, MongoDB, Oracle, portability, app creator
type: help
---
# Database access and SQL

This page is for application creators who need SQL. Exponential runs on SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle
and MySQL ([Database support](../../database-support.md)), so portable code matters more than on a single-engine
product. Prefer `eZPersistentObject` ([Persistent objects and custom tables](persistent-objects-and-custom-tables.md));
use SQL for reports and bulk work.

## Contents

1. [The connection](#the-connection)
2. [Reading and writing](#reading-and-writing)
3. [Escaping](#escaping)
4. [Transactions](#transactions)
5. [Writing portable SQL](#writing-portable-sql)

## The connection

```php
$db = eZDB::instance();
```

The instance uses the siteaccess's `[DatabaseSettings]`; one connection per request (per worker under Velocity).

## Reading and writing

```php
$rows = $db->arrayQuery( 'SELECT id, title FROM expdemo_item WHERE user_id = ' . (int)$userID . ' ORDER BY created DESC',
                         array( 'offset' => 0, 'limit' => 20 ) );

$db->query( "UPDATE expdemo_item SET title = '" . $db->escapeString( $title ) . "' WHERE id = " . (int)$id );
```

`arrayQuery()` returns an array of rows (associative arrays); its second argument takes `offset` and `limit`, which the
driver turns into the engine's syntax. `query()` returns `true` or `false` for statements that change data.

## Escaping

| Value | Do |
|---|---|
| integer | cast: `(int)$id` |
| string | `$db->escapeString( $value )` inside quotes |
| list of integers | `implode( ',', array_map( 'intval', $ids ) )` |
| identifiers (table, column) | never from input; pick from an allow list |

Never build SQL from request input without these.

## Transactions

```php
$db->begin();
try
{
    // several writes
    $db->commit();
}
catch ( Exception $e )
{
    $db->rollback();
    throw $e;
}
```

Transactions nest: only the outermost `commit()` writes. On SQLite, writers queue for the write lock; keep transactions
short ([SQLite transactions](../../../bc/6.0/sqlite-transactions.md)).

## Writing portable SQL

- Use `arrayQuery()` offsets and limits, not `LIMIT` in the string.
- Avoid engine functions (`IFNULL`, `GROUP_CONCAT`, `MOD()`): the eztags fixes for SQLite replaced `MOD()` with `%`
  ([eztags](../../../features/6.0/extensions/eztags.md)).
- Do not rely on `SET @var := ...` or multi-statement strings.
- MongoDB has no joins: the kernel's driver translates common statements; for your own tables, prefer
  `eZPersistentObject`, which every engine supports ([MongoDB support](../../../features/6.0/mongodb-database-support.md)).
- Test on SQLite and on the production engine.
- The SQL query cache can cache read results (`querycache.ini`) ([SQL query cache](../../../bc/6.0/sql-query-cache.md)).

## Related pages

- [Persistent objects and custom tables](persistent-objects-and-custom-tables.md)
- [Database schema and .dba files](schema-and-dba-files.md)
- [Databases and SQL standard](../../../standards/databases-and-sql.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
