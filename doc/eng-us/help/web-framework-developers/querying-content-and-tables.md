---
title: "Queries: content fetches, eZDB and SQL"
audience: web-framework-developer, developer
category: web-frameworks
order: 150
summary: How to query data in Exponential when you are used to a query builder - content fetches with class, attribute and depth filters for content, condition arrays for persistent objects, eZDB arrayQuery and escaping for your own SQL, transactions, portability across SQLite, PostgreSQL, MySQL/MariaDB, Oracle and MongoDB, and the query cache.
keywords: query, query builder, SQL, eZDB, arrayQuery, escapeString, subTreeByNodeID, AttributeFilter, ClassFilterArray, SortBy, Limit, fetchObjectList, transactions, portability, SQLite, MongoDB, web framework developer
type: help
---
# Queries: content fetches, eZDB and SQL

This page is for developers who reach for a query builder (`$qb->where()`, `Model::where()`, querysets, Arel). There is
no general query builder in the kernel. You query in one of three ways, depending on the data.

## Contents

1. [Which way for which data](#which-way-for-which-data)
2. [Content: fetch parameters](#content-fetch-parameters)
3. [Your tables: condition arrays](#your-tables-condition-arrays)
4. [Your tables: SQL through eZDB](#your-tables-sql-through-ezdb)
5. [Portability](#portability)
6. [Performance](#performance)

## Which way for which data

| Data | Way | Gives you |
|---|---|---|
| Content (nodes, objects) | `eZContentObjectTreeNode::subTreeByNodeID()` and the template `fetch( 'content', 'list', ... )` | permissions applied, language handling, sorting by attribute |
| Your persistent objects | `eZPersistentObject::fetchObjectList()` with conditions | objects, escaping, every engine |
| Reports, joins, aggregates on your tables | `eZDB::instance()->arrayQuery()` | rows as arrays |
| Full text search across content | the search engine (built-in, or Solr through `ezfind`) | relevance, facets with Solr ([Site search](../creators/website-creators/site-search.md)) |

## Content: fetch parameters

```php
$nodes = eZContentObjectTreeNode::subTreeByNodeID( array(
    'ClassFilterType'  => 'include',
    'ClassFilterArray' => array( 'event' ),
    'AttributeFilter'  => array( array( 'event/start_date', '>=', time() ) ),
    'SortBy'           => array( array( 'attribute', true, 'event/start_date' ) ),
    'Depth'            => 3,
    'Limit'            => 20,
    'Offset'           => 0 ), $parentNodeID );
```

The parameter array is the query builder for content: class filters, attribute filters, depth, sort, limit and offset.
The current user's `content/read` policy is applied unless you pass `'Limitation' => array()`, which you must never do
in code that shows results to users ([The content API](../creators/app-creators/the-content-api.md#fetching-lists)).
Templates use the same parameters in lower case through `fetch( 'content', 'list', hash( ... ) )`
([Fetch functions](../creators/app-creators/fetch-functions.md)).

## Your tables: condition arrays

```php
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null,
                                              array( 'user_id' => (int)$userID ),
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => 0, 'length' => 20 ) );
```

Conditions are arrays; values are escaped for you ([ORM: eZPersistentObject](orm-and-ezpersistentobject.md)).

## Your tables: SQL through eZDB

From [Database access and SQL](../creators/app-creators/database-access.md):

```php
$db = eZDB::instance();
$rows = $db->arrayQuery( 'SELECT id, title FROM expdemo_item WHERE user_id = ' . (int)$userID . ' ORDER BY created DESC',
                         array( 'offset' => 0, 'limit' => 20 ) );
$db->query( "UPDATE expdemo_item SET title = '" . $db->escapeString( $title ) . "' WHERE id = " . (int)$id );
```

`arrayQuery()` and `query()` take a finished SQL string, not a statement with placeholders as PDO does: cast integers,
wrap strings in `escapeString()`, and pick identifiers from an allow list. `query()` returns `true` or `false` for statements that change data.

## Portability

The driver is chosen by `site.ini [DatabaseSettings] DatabaseImplementation`; the kernel ships drivers for MySQL/MariaDB
(`eZMySQLiDB`), PostgreSQL, SQLite (`eZSQLite3DB`) and MongoDB (`expMongoDB`), and Oracle comes from the `ezoracle`
extension ([Database support](../database-support.md)). To keep your SQL working on all of them:

- Use `arrayQuery()` offsets and limits, not `LIMIT` in the string.
- Avoid engine-specific functions such as `IFNULL`, `GROUP_CONCAT` or `SET @var := ...`.
- MongoDB has no joins: the driver translates common statements; for your own tables prefer `eZPersistentObject`.
- Test on SQLite (the default for new installations) and on the production engine.

## Performance

- Always limit lists; count with `subTreeCountByNodeID()` or `eZPersistentObject::count()`.
- In long scripts, fetch in pages and clear in-memory caches between them (`eZContentObject::clearCache()`).
- The SQL query cache can keep read results (`querycache.ini`) ([SQL query cache](../../bc/6.0/sql-query-cache.md)).
- The debug bar shows the queries of a page and their time ([Debugging](debugging-logging-and-errors.md)).

## Related pages

- [Database access and SQL](../creators/app-creators/database-access.md)
- [Databases](../../install/09-databases.md)
- [Profiling and performance](../creators/app-creators/profiling-and-performance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
