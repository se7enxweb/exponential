---
title: "Fetch functions: how they work"
audience: developer, app-creator
category: development
order: 700
summary: What happens when a template calls fetch() - eZFunctionHandler, eZModuleFunctionInfo, the module's function_definition.php and its function collection class - the definition format, results and errors, the module repository search order, and how an extension adds a fetch module, extends a kernel module's fetch functions or replaces them, each rendered on alpha.
keywords: fetch, fetch function, function_definition.php, FunctionList, call_method, function collection, eZFunctionHandler, eZModuleFunctionInfo, module repositories, ExtensionRepositories, override fetch function, extend fetch function
type: help
---
# Fetch functions: how they work

`fetch( module, function, hash( ... ) )` is how a template reads data: content, users, sections, your own tables.
This page follows a call through the code and shows how an extension adds or changes fetch functions. The
parameters of every fetch function of this installation are listed in the
[fetch function reference](../../examples/fetch-functions/README.md); a first fetch function of your own is
written step by step in [Writing your own fetch function](../creators/app-creators/writing-a-fetch-function.md).

## Contents

1. [The path of a call](#the-path-of-a-call)
2. [function_definition.php](#function_definitionphp)
3. [The function collection](#the-function-collection)
4. [When something is wrong](#when-something-is-wrong)
5. [Where modules are found](#where-modules-are-found)
6. [Adding a fetch module in an extension](#adding-a-fetch-module-in-an-extension)
7. [Extending a kernel module's fetch functions](#extending-a-kernel-modules-fetch-functions)
8. [Replacing them, and why not to](#replacing-them-and-why-not-to)
9. [Compiled templates](#compiled-templates)

## The path of a call

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $count = fetch( 'content', 'list_count', hash( 'parent_node_id', 89 ) )}
{$node.name|wash}: {$count} children
```

Output on alpha:

```text
Fit &amp; Healthy: 16 children
```

1. The template operator `fetch` (`eZTemplateExecuteOperator::modify()`, `lib/eztemplate/classes/eztemplateexecuteoperator.php`)
   calls `eZFunctionHandler::execute( 'content', 'node', array( 'node_id' => 89 ) )`.
2. `eZFunctionHandler::moduleFunctionInfo( 'content' )` (`lib/ezutils/classes/ezfunctionhandler.php`) creates an
   `eZModuleFunctionInfo` for the module once per request and keeps it in `$GLOBALS['eZGlobalModuleFunctionList']`.
3. `eZModuleFunctionInfo::loadDefinition()` (`lib/ezutils/classes/ezmodulefunctioninfo.php`) looks for
   `<repository>/content/function_definition.php` in each module repository and includes the **first** one it
   finds. That file sets `$FunctionList`.
4. `execute()` looks up `node` in `$FunctionList`, builds the argument list from the definition's `parameters`
   in their order (your value, else the `default`, else `null`; a missing required parameter stops the call), and
   calls `call_method` `class::method` on an instance of the class.
5. The method returns `array( 'result' => ... )`; the template gets the `result`.

## function_definition.php

The kernel's `kernel/content/function_definition.php`, entry `node`, shows the format:

```php
$FunctionList['node'] = array( 'name' => 'node',
                               'operation_types' => array( 'read' ),
                               'call_method' => array( 'class' => 'eZContentFunctionCollection',
                                                       'method' => 'fetchContentNode' ),
                               'parameter_type' => 'standard',
                               'parameters' => array( array( 'name' => 'node_id',
                                                             'type' => 'integer',
                                                             'required' => false,
                                                             'default' => false ),
                                                      array( 'name' => 'node_path',
                                                             'type' => 'string',
                                                             'required' => false,
                                                             'default' => false ),
                                                      // ...
                                                    ) );
```

| Key | Meaning |
|---|---|
| `name` | the function name, as used in `fetch()` |
| `operation_types` | `read` for every fetch function in the kernel; documentation |
| `call_method` | `class` and `method` that do the work; the class is created with `new` and must be autoloadable |
| `parameter_type` | `standard` |
| `parameters` | in the order of the method's arguments: `name`, `type`, `required`, `default` |

The template passes parameters **by name** in a hash; the definition maps them to the method's arguments **by
position**. `type` is not checked; a parameter of `type` `array` matters to [fetch aliases](fetch-aliases.md),
which split such constants at `;`.

## The function collection

By convention one class per module holds all its fetch functions, the "function collection"
(`eZContentFunctionCollection` in `kernel/content/ezcontentfunctioncollection.php`). Each method returns an array:

- `array( 'result' => $value )` on success: the template gets `$value`;
- `array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) )` when the
  thing does not exist or may not be read: the template gets `null`.

Check permissions in the method (`canRead()` on nodes and objects, or the user's policies): a template can call
any fetch function with any parameters, and the kernel's collections filter by the current user's access.

## When something is wrong

Fetch never stops the page. Every problem is written to the debug output and the template gets `null`:

```tpl
[{fetch( 'nosuchmodule', 'list', hash() )}]
```

Output on alpha:

```text
[]
```

(`Missing function definition file for module: nosuchmodule` and
`Cannot execute function 'list' in module 'nosuchmodule', no valid data`.)

```tpl
{def $result = fetch( 'content', 'nosuchfunction', hash() )}
{if is_null( $result )}no result{/if}
```

Output on alpha:

```text
no result
```

(`No such function 'nosuchfunction' in module 'content'`.)

```tpl
{def $result = fetch( 'content', 'node', hash() )}
{if is_null( $result )}no result{else}{$result.name|wash}{/if}
```

Output on alpha:

```text
no result
```

`content/node` has no required parameter, so this call reaches `eZContentFunctionCollection::fetchContentNode()`,
which returns no node without a node id or path. For a function with a required parameter left out,
`eZModuleFunctionInfo::executeClassMethod()` does not call the method at all and the debug output says which
parameter was missing. Test fetch results with `is_null()` or `is_set()` before using them.

## Where modules are found

The module repositories come from `module.ini` `[ModuleSettings]`: `ModuleRepositories[]` (the kernel's `kernel`
and `kernel/private/modules`) and, for each extension listed in `ExtensionRepositories[]` and active, its
`modules` folder. `eZModule::activeModuleRepositories()` (`lib/ezutils/classes/ezmodule.php`) merges the two lists
and **reverses** them, so extensions are searched before the kernel, and of the extensions the one listed last in
`ExtensionRepositories[]` first. On alpha's `site` siteaccess the search order starts with
`extension/sevenx_alpha_settings/modules`, `extension/expui/modules`, `extension/ezjscore/modules`, ... and ends
with `kernel/private/modules` and `kernel`; 28 modules have fetch functions, 21 of them in the kernel. No extension
on alpha has a `function_definition.php` for a kernel module.

## Adding a fetch module in an extension

A module that only offers fetch functions needs no views: a folder with a `function_definition.php` is enough.

```
extension/myextension/modules/c11demo/function_definition.php
extension/myextension/classes/c11demofunctioncollection.php
extension/myextension/settings/module.ini.append.php
```

`module.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=myextension

*/ ?>
```

The definition (`modules/c11demo/function_definition.php`):

```php
<?php
$FunctionList = array();
$FunctionList['children_by_class'] = array(
    'name' => 'children_by_class',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'C11DemoFunctionCollection',
                            'method' => 'fetchChildrenByClass' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'parent_node_id',
                                  'type' => 'integer',
                                  'required' => true ),
                           array( 'name' => 'limit',
                                  'type' => 'integer',
                                  'required' => false,
                                  'default' => 50 ) ) );
```

The collection (`classes/c11demofunctioncollection.php`, then `php bin/php/ezpgenerateautoloads.php -e`):

```php
class C11DemoFunctionCollection
{
    /**
     * The children of a node, counted per class identifier: array( identifier => count ), largest first.
     */
    public function fetchChildrenByClass( $parentNodeID, $limit )
    {
        $parent = eZContentObjectTreeNode::fetch( (int)$parentNodeID );
        if ( !$parent instanceof eZContentObjectTreeNode || !$parent->canRead() )
            return array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) );

        $counts = array();
        foreach ( $parent->subTree( array( 'Depth' => 1, 'Limit' => (int)$limit ) ) as $child )
        {
            $identifier = $child->attribute( 'class_identifier' );
            $counts[$identifier] = ( $counts[$identifier] ?? 0 ) + 1;
        }
        arsort( $counts );
        return array( 'result' => $counts );
    }
}
```

Not run on alpha as an installed extension: the script that rendered the examples in this and the next two
sections put a scratch module repository in front of the others in memory (`eZModule::setGlobalPathList()`, for its
own process) and loaded the classes from a scratch folder.

```tpl
{foreach fetch( 'c11demo', 'children_by_class', hash( 'parent_node_id', 89 ) ) as $identifier => $count}
{$identifier}: {$count}

{/foreach}
```

Output on alpha:

```text
ng_landing_page: 5
ng_topic: 4
ng_article: 2
ng_category: 1
ng_contact_form: 1
ng_container: 1
ng_site_info: 1
folder: 1
```

## Extending a kernel module's fetch functions

Because only the first `function_definition.php` found for a module name is read, an extension cannot add one
fetch function to `content` or `section` by shipping a file with just that function: it would hide all the
kernel's. Include the kernel's file and add to its list:

```php
<?php
/**
 * Extends the kernel's section fetch functions instead of replacing them: the first function_definition.php
 * found for a module is the only one read, so this file includes the kernel's and adds to its list.
 */
include 'kernel/section/function_definition.php';

$FunctionList['c11_identifiers'] = array(
    'name' => 'c11_identifiers',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'C11DemoSectionFunctionCollection',
                            'method' => 'fetchIdentifiers' ),
    'parameter_type' => 'standard',
    'parameters' => array() );
```

(`extension/myextension/modules/section/function_definition.php`; `C11DemoSectionFunctionCollection::fetchIdentifiers()`
returns the sorted identifiers of `eZSection::fetchList()`.) Both the kernel's `section/list` and the new function
work:

```tpl
{def $sections = fetch( 'section', 'list' )
     $identifiers = fetch( 'section', 'c11_identifiers' )}
section/list: {$sections|count} sections
section/c11_identifiers: {$identifiers|implode( ', ' )}
```

Output on alpha:

```text
section/list: 10 sections
section/c11_identifiers: , , design, help_internal, help_public, media, sample_collaboration, setup, standard, users
```

(Two sections on alpha have an empty identifier, hence the two leading commas.) The include path is relative to
the installation root, which is the working directory of every Exponential request and script.

Even so, a function with your own module name (`fetch( 'myext', ... )`) is the better choice: it cannot collide
with a future kernel function of the same name, and it needs no copy of a kernel file's behavior.

## Replacing them, and why not to

The same file without the `include` replaces the kernel's section fetch functions on every siteaccess where the
extension is active:

```tpl
{def $sections = fetch( 'section', 'list' )
     $identifiers = fetch( 'section', 'c11_identifiers' )}
section/list: {$sections|count} sections
section/c11_identifiers: {$identifiers|implode( ', ' )}
```

Output on alpha:

```text
section/list: 0 sections
section/c11_identifiers: , , design, help_internal, help_public, media, sample_collaboration, setup, standard, users
```

`section/list` now returns nothing; every template that uses it, in the admin too, silently breaks. If you must
change what a kernel fetch function returns, keep its name and parameters, include the kernel's file, and replace
only that entry's `call_method` with a subclass of the kernel's collection that calls `parent::` for the cases you do
not change. Re-check it on every kernel upgrade.

## Compiled templates

For `fetch()` calls whose module and function names are constants, the compiler (`eZTemplateExecuteOperator::fetchTransform()`)
reads the function definition at compile time and writes a direct call of the collection method into the compiled
template, guarded by `class_exists()`/`method_exists()`. Two consequences:

- after changing a `function_definition.php` (parameters, order, `call_method`), clear the template cache
  (`php bin/php/ezcache.php --clear-tag=template --allow-root-user`), or compiled templates keep calling the old
  method with the old argument order;
- a definition that cannot be loaded at compile time (class missing) makes the compiler fall back to a run-time call.

All examples on this page gave the same result interpreted and compiled (into a scratch folder) on alpha.

## Related pages

- [Fetch aliases](fetch-aliases.md)
- [Execute operators](execute-operators.md): the `fetch` and `fetch_alias` operators
- [Fetch function reference](../../examples/fetch-functions/README.md)
- [Writing your own fetch function](../creators/app-creators/writing-a-fetch-function.md)
- [Fetch functions: reading data in templates](../creators/app-creators/fetch-functions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
