---
title: Fetch functions
audience: developer, app-creator, designer
category: extension-development
order: 140
summary: Let templates fetch your data: function_definition.php, the collection class and its result array, a verified example, fetch aliases, and extended attribute filters for content fetches.
keywords: fetch function, function_definition.php, call_method, parameters, function collection, result array, fetch, fetch_alias, fetchalias.ini, extended attribute filter, extendedattributefilter.ini
type: help
---
# Fetch functions

Part 3, chapter 14. `fetch( 'content', 'list', ... )` is a **fetch function** of the module `content`. Your module can
offer its own: `fetch( 'hello', 'newest', hash( 'class_identifier', 'article' ) )`. Fetch functions are the right way
to give templates data that needs PHP, without putting logic in templates. The engine is `eZFunctionHandler` and
`eZModuleFunctionInfo` (`lib/ezutils/classes/`).

## Where it lives

A fetch function belongs to a module, so your extension needs a module folder registered with
`ExtensionRepositories[]` ([Modules and views](modules-and-views.md)). The module may have no views at all; then it
exists only for its fetch functions.

```text
extension/mysite_hello/
    modules/hello/function_definition.php
    classes/mysitehellofunctioncollection.php
```

## function_definition.php

```php
<?php
$FunctionList = array();

$FunctionList['newest'] = array(
    'name' => 'newest',
    'call_method' => array( 'class' => 'mysiteHelloFunctionCollection',
                            'method' => 'fetchNewest' ),
    'parameters' => array(
        array( 'name' => 'class_identifier', 'type' => 'string', 'required' => true ),
        array( 'name' => 'parent_node_id', 'type' => 'integer', 'required' => false, 'default' => 2 ),
        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 5 )
    )
);
```

The parameters are passed to the method **in the order listed**, matched by name from the template's `hash()`;
missing optional ones get their `default`. The eztags extension's `modules/tags/function_definition.php` is a larger
real example.

## The collection class

```php
<?php
/**
 * Fetch functions of the module "hello":
 *   fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 3 ) )
 */
class mysiteHelloFunctionCollection
{
    public static function fetchNewest( $classIdentifier, $parentNodeID = 2, $limit = 5 )
    {
        $limit = max( 1, min( (int)$limit, 50 ) );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType' => 'include',
                   'ClassFilterArray' => array( (string)$classIdentifier ),
                   'SortBy' => array( 'published', false ),
                   'Limit' => $limit ),
            (int)$parentNodeID
        );
        return array( 'result' => is_array( $nodes ) ? $nodes : array() );
    }
}
```

The method **returns an array with the key `result`**; the engine hands `result` to the template
(`eZModuleFunctionInfo::execute()`: no `result` key is reported as "did not return a result value"). Return
`array( 'result' => false )` for "nothing found", the convention the eztags collection uses. Called with the kernel
loaded on sample content, the method above returned the three newest nodes of the class it was given.

`subTreeByNodeID()` applies the current user's read permissions, like every content fetch; so does your function.

## Using it

```text
{def $newest = fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 3 ) )}
{foreach $newest as $node}
    {node_view_gui content_node=$node view='line'}
{/foreach}
{undef $newest}
```

## Fetch aliases

`fetchalias.ini` gives a short name to a fetch with fixed parameters, used with `fetch_alias()`:

```ini
[newest_articles]
Module=hello
FunctionName=newest
Constant[class_identifier]=article
Constant[limit]=3
```

```text
{def $newest = fetch_alias( 'newest_articles', hash() )}
```

The kernel's `settings/fetchalias.ini` documents the format and ships examples (`[object]`, `[news_list]`).

## Extended attribute filters

Sometimes the content fetches (`list`, `tree`) are right but need one more SQL condition, for example a join with your
own table. An **extended attribute filter** adds tables, joins and columns to them:

```ini
# extension/mysite_hello/settings/extendedattributefilter.ini.append.php
[MysiteRatedFilter]
ExtensionName=mysite_hello
FileName=classes/mysiteratedfilter.php
ClassName=mysiteRatedFilter
MethodName=createSqlParts
```

```php
<?php
class mysiteRatedFilter
{
    public function createSqlParts( $params )
    {
        $minimum = isset( $params['minimum'] ) ? (int)$params['minimum'] : 0;
        return array(
            'tables' => ', mysite_rating r',
            'joins'  => " r.contentobject_id = ezcontentobject.id AND r.score >= $minimum AND ",
            'columns' => ''
        );
    }
}
```

```text
{fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                  'extended_attribute_filter', hash( 'id', 'MysiteRatedFilter',
                                                                     'params', hash( 'minimum', 4 ) ) ) )}
```

The kernel calls `createSqlParts( $params )` and inserts `tables`, `joins` and `columns` into its query
(`createExtendedAttributeFilterSQLStrings()` in `kernel/classes/ezcontentobjecttreenode.php`). Everything you put in
the SQL must be safe: cast numbers, escape strings with `eZDB::instance()->escapeString()`.

## RAD

**Template extension** (`/setup/templateoperator`) writes fetch functions with their definition and collection class;
see [With RAD: a template operator in five minutes](tutorials/rad-template-operator.md).

## Related pages

- Previous: [Template operators](template-operators.md)
- Next: [Template functions](template-functions.md)
- [By hand: a fetch function](tutorials/by-hand-fetch-function.md)
- [Fetching content in templates](../part-2-builder/fetch-for-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
