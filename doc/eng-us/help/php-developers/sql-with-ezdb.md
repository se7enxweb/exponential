---
title: SQL with eZDB, safely
audience: php-developer, developer
category: php-development
order: 180
summary: Writing SQL in Exponential with eZDB - instance(), query() and arrayQuery() with limit and offset, escapeString() for strings and (int) casts for numbers, generateSQLINStatement(), transactions with begin(), commit() and rollback(), the return values to check, and when to use eZPersistentObject instead.
keywords: eZDB, SQL, escapeString, int cast, SQL injection, arrayQuery, query, limit, offset, generateSQLINStatement, begin, commit, rollback, transactionCounter, lastSerialID, eZDBInterface, prepared statements
type: help
---
# SQL with eZDB, safely

This page is for PHP developers who write SQL. Exponential reaches every database through `eZDB`
(`lib/ezdb/classes/ezdb.php`), which returns a driver implementing `eZDBInterface`
(`lib/ezdb/classes/ezdbinterface.php`). The driver interface has no bound parameters for arbitrary SQL, so safety comes
from two habits: escape every string, cast every number.

## Contents

1. [Getting the connection](#getting-the-connection)
2. [Reading](#reading)
3. [Writing](#writing)
4. [Escaping: the two rules](#escaping-the-two-rules)
5. [Lists of values](#lists-of-values)
6. [Transactions](#transactions)
7. [Return values](#return-values)
8. [When not to write SQL](#when-not-to-write-sql)

## Getting the connection

```php
$db = eZDB::instance();
```

`eZDB::instance()` returns the shared connection for the current siteaccess's `[DatabaseSettings]`. Do not create your
own connection with `forceNewInstance` unless you really need a second one; it costs a connection and escapes the
transaction state of the request.

## Reading

```php
$userID = (int)$userID;
$rows = $db->arrayQuery(
    "SELECT id, title, created FROM acme_report_item WHERE user_id = $userID ORDER BY created DESC",
    array( 'offset' => 0, 'limit' => 20 )
);
foreach ( $rows as $row )
{
    // $row['id'], $row['title'], $row['created'] are strings (or null)
}

$titles = $db->arrayQuery( "SELECT title FROM acme_report_item WHERE user_id = $userID",
                           array( 'column' => 'title' ) );     // a flat list of one column
```

Pass `limit` and `offset` as parameters instead of writing `LIMIT` into the SQL: the driver writes the syntax its
database understands (Oracle and SQLite differ from MySQL).

## Writing

```php
$title = $db->escapeString( $title );
$now = time();
$db->query( "INSERT INTO acme_report_item ( user_id, title, created ) VALUES ( $userID, '$title', $now )" );
$newID = $db->lastSerialID( 'acme_report_item', 'id' );
```

## Escaping: the two rules

| Value | Rule | Example |
|---|---|---|
| string | `$db->escapeString()`, inside single quotes | `"... WHERE title = '" . $db->escapeString( $title ) . "'"` |
| integer | `(int)` | `"... WHERE id = " . (int)$id` |
| float | `(float)` | `"... WHERE amount > " . (float)$min` |
| column or table name from input | an allow list, never the input itself | `in_array( $sort, array( 'title', 'created' ), true )` |
| sort direction | a fixed choice | `$dir = $desc ? 'DESC' : 'ASC';` |

Cast or escape as close to the SQL as possible, even when the value "was already checked": the next change to the code
around it may remove the check. Values from `$Params`, `eZHTTPTool`, settings, imports and the database itself are all
input ([Validating input and casting](validating-input-and-casting.md)).

## Lists of values

```php
$ids = array_map( 'intval', $ids );
$in = $db->generateSQLINStatement( $ids, 'id', false, true, 'int' );   // id IN ( 1, 2, 3 ); drivers may override it
$rows = $db->arrayQuery( "SELECT id, title FROM acme_report_item WHERE $in" );
```

With a type (`'int'`), `generateSQLINStatement()` casts every element through `implodeWithTypeCast()`; let the
driver build the statement rather than concatenating it yourself, since a driver can write it differently for its
database.

## Transactions

```php
$db->begin();
$item->store();
$log->store();
$db->commit();
```

- `begin()` and `commit()` nest: an inner pair inside an outer one only counts (`transactionCounter()`), and the outer
  `commit()` commits. Every `begin()` needs exactly one `commit()` or `rollback()` on every path, including early
  returns and exceptions.
- Transactions are used only when `[DatabaseSettings] Transactions=enabled`.
- Keep transactions short; on SQLite a writer waits for the writers ahead of it
  ([SQLite transactions](../../bc/6.0/sqlite-transactions.md)).

## Return values

| Call | Returns |
|---|---|
| `query()` | `true` or `false` for most statements |
| `arrayQuery()` | an array of rows (empty when nothing matches) |
| `lastSerialID()` | the new id after an insert |

Check `query()`'s result when it matters. Do not rely on `SET @var := ...` session variables through `query()`; for
inserts that need the new id, `eZPersistentObject::store()` sets it for you. Results also differ across engines in
edge cases, for example an empty result and a failed statement on the MongoDB adapter
([Code for every supported database](code-for-every-supported-database.md)).

## When not to write SQL

| Need | Better |
|---|---|
| rows of your own table | `eZPersistentObject` conditions, escaped for you ([Persistent object definitions in depth](persistent-object-definitions-in-depth.md)) |
| content | `eZContentObjectTreeNode::subTreeByNodeID()` and the content API ([The content API in PHP](../creators/app-creators/the-content-api.md)) |
| the schema | `.dba` files and the schema handler ([Database schema and .dba files](../creators/app-creators/schema-and-dba-files.md)) |

## Related pages

- [eZDB reference](../extension-development/part-8-reference/ezdb.md)
- [Database access and SQL](../creators/app-creators/database-access.md)
- [Databases and SQL standard](../../standards/databases-and-sql.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
