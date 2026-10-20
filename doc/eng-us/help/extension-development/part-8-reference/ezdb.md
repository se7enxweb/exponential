---
title: "eZDB: the database layer"
audience: developer
category: extension-development
order: 40
summary: The eZDB methods extensions use - arrayQuery() with offset and limit, query(), escapeString(), generateSQLINStatement(), transactions, lastSerialID(), portable SQL helpers - and safe values.
keywords: eZDB, eZDBInterface, instance, arrayQuery, query, escapeString, generateSQLINStatement, begin, commit, rollback, lastSerialID, databaseName, concatString, subString, bitAnd
type: help
---
# eZDB: the database layer

Part 8, reference 4. `eZDB::instance()` returns the site's database connection, an object of a class that extends
`eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`): `eZMySQLiDB`, `eZPostgreSQLDB`, `eZSQLite3DB`, `expMongoDB`
or a handler from an extension. Write against the interface and your code runs on every engine with SQL. For your own
tables prefer `eZPersistentObject` ([eZPersistentObject](ezpersistentobject.md)), which works on MongoDB too.
Upgrading old database code is covered in
[Database code for PHP 8 and every engine](../part-4-upgrading/database-code-for-php-8.md).

## Reading and writing

| Method | Use |
|---|---|
| `arrayQuery( $sql, $params = array(), $server = false )` | runs a SELECT and returns all rows as arrays; `$params` takes `offset` and `limit`, and also `column` to return one column as a plain list |
| `query( $sql, $server = false )` | runs any other statement; returns a result handle or `false` |
| `lastSerialID( $table = false, $column = false )` | the ID of the row just inserted |
| `escapeString( $str )` | escapes a string for use inside single quotes |
| `generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )` | `column IN ( ... )` for a list, with each value cast to `$type` (for example `'int'`) |
| `errorMessage()` | the last error's text |
| `databaseName()` | the engine's name (`mysql`, `postgresql`, `sqlite`, `mongo` ...) |

```php
<?php
$db = eZDB::instance();
$parentID = (int)$parentNodeID;
$rows = $db->arrayQuery( "SELECT id, name FROM myext_item WHERE parent_id = $parentID ORDER BY name",
                         array( 'offset' => 0, 'limit' => 20 ) );
$ids = $db->arrayQuery( 'SELECT id FROM myext_item', array( 'column' => 'id' ) );
```

## Safe values, always

| Value | Do |
|---|---|
| integer | `(int)$value` |
| string | `"'" . $db->escapeString( (string)$value ) . "'"` |
| list of integers | `$db->generateSQLINStatement( $ids, 'id', false, true, 'int' )` |
| a column or table name from input | never; choose from a fixed list in your code |

## Transactions

```php
<?php
$db = eZDB::instance();
$db->begin();
$db->query( 'DELETE FROM myext_item_tag WHERE item_id = ' . (int)$itemID );
$db->query( 'INSERT INTO myext_item_tag ( item_id, tag_id ) VALUES ( ' . (int)$itemID . ', ' . (int)$tagID . ' )' );
$db->commit();
```

`begin()` and `commit()` nest: the kernel counts them, and only the outermost `commit()` commits. `rollback()` undoes
the whole transaction. Every `begin()` needs exactly one `commit()` or `rollback()` on every path, including error
paths, or the transaction stays open until the end of the request.

## Portable SQL helpers

| Method | Instead of |
|---|---|
| `concatString( $strings = array() )` | `CONCAT()` or `\|\|` |
| `subString( $string, $from, $len = null )` | `SUBSTRING()` variants |
| `bitAnd( $arg1, $arg2 )`, `bitOr( $arg1, $arg2 )` | `&` and `\|` written by hand |

## Not on MongoDB

On an installation that stores its content in MongoDB, the adapter `expMongoDB` runs no SQL: `arrayQuery()` returns
no rows and logs a warning ([MongoDB kernel support](../../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)). Code that
must work there uses `eZPersistentObject`.

## Related pages

- Previous: [eZDebug and logging](ezdebug-and-logging.md)
- Next: [eZPersistentObject](ezpersistentobject.md)
- [Database support](../../database-support.md)
- [Database schema and .dba files](../part-3-extension-developer/database-schema-and-dba-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
