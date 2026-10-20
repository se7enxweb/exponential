---
title: "By hand: a fetch function"
audience: developer, app-creator, designer
category: extension-development
order: 120
summary: Give templates fetch( 'hello', 'newest', ... ): register a module folder, write function_definition.php and the collection class, and call it from a template, verified.
keywords: tutorial, by hand, fetch function, function_definition.php, call_method, function collection, ExtensionRepositories, result array, fetch
type: help
---
# By hand: a fetch function

Tutorial 11. You give templates `fetch( 'hello', 'newest', hash( 'class_identifier', 'article' ) )`, the newest nodes of a
class. A fetch function belongs to a module, so this tutorial also registers the module folder that the next tutorial
fills with views. The files below passed `php -l`, and the template call at the end was rendered by the template engine
with the module folder added to the module path.

## 1. Register the module folder

`extension/mysite_hello/settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

## 2. The definition

`extension/mysite_hello/modules/hello/function_definition.php`:

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

## 3. The collection class

`extension/mysite_hello/classes/mysitehellofunctioncollection.php`:

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

The limit is clamped and the IDs cast, because a template may pass values from the address. The result is wrapped in
`array( 'result' => ... )`, which the kernel unwraps.

## 4. Check and switch on

```bash
php -l extension/mysite_hello/modules/hello/function_definition.php
php -l extension/mysite_hello/classes/mysitehellofunctioncollection.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 5. Use it

```text
{def $newest = fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 2 ) )}
{foreach $newest as $node}[{$node.name|wash}]{/foreach}
{fetch( 'hello', 'newest', hash( 'class_identifier', 'nothing_like_this' ) )|count}
{undef $newest}
```

On the installation used for this book (with its own article class), the first line printed the names of the two
newest articles and the second `0`.

## 6. If it does not work

| Message | Fix |
|---|---|
| `Missing function definition file for module: hello` | `ExtensionRepositories[]` missing or the extension inactive; caches |
| `Function 'newest' in module 'hello' did not return a result value` | the method must return `array( 'result' => ... )` |
| `Class 'mysiteHelloFunctionCollection' not found` | regenerate autoloads |

## 7. Commit

```bash
git -C extension/mysite_hello add modules classes settings
git -C extension/mysite_hello commit -m "Added: The fetch function hello/newest"
```

## Related pages

- Previous: [By hand: a template operator](by-hand-template-operator.md)
- Next: [By hand: a module with a view](by-hand-module-with-a-view.md)
- [Fetch functions](../fetch-functions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
