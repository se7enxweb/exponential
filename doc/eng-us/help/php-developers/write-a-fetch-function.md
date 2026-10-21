---
title: Write a fetch function
audience: php-developer, developer
category: php-development
order: 260
summary: A task guide for giving templates data through fetch( 'module', 'function', hash( ... ) ) - the function_definition.php entry, the static method and its result array, parameters, errors, permissions and limits, and how eZModuleFunctionInfo calls your code.
keywords: fetch function, function_definition.php, FunctionList, call_method, parameter_type, parameters, result, error, eZModuleFunctionInfo, function collection, template fetch, permissions, limits
type: help
---
# Write a fetch function

This task guide is for PHP developers who want templates to read their data with
`fetch( 'acmereport', 'items', hash( 'limit', 5 ) )`. A fetch function is a declaration in the module's
`function_definition.php` plus a static PHP method. The template side is described in
[Fetch functions: reading data in templates](../creators/app-creators/fetch-functions.md).

## Contents

1. [Step 1: the declaration](#step-1-the-declaration)
2. [Step 2: the method](#step-2-the-method)
3. [Step 3: use it](#step-3-use-it)
4. [How the kernel calls you](#how-the-kernel-calls-you)
5. [The result array](#the-result-array)
6. [Rules](#rules)

## Step 1: the declaration

`extension/acmereport/modules/acmereport/function_definition.php`, beside `module.php`:

```php
<?php
$FunctionList = array();
$FunctionList['items'] = array(
    'name' => 'items',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'acmeReportFunctionCollection',
                            'method' => 'fetchItems' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'status', 'type' => 'string',  'required' => false, 'default' => false ),
        array( 'name' => 'limit',  'type' => 'integer', 'required' => false, 'default' => 10 ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );
```

The module must be registered (`[ModuleSettings] ExtensionRepositories[]` and `ModuleList[]`); the kernel's own
declarations look the same (`kernel/content/function_definition.php`).

## Step 2: the method

`extension/acmereport/classes/acmereportfunctioncollection.php`:

```php
<?php
class acmeReportFunctionCollection
{
    /**
     * @param string|false $status
     * @param int $limit
     * @param int $offset
     * @return array array( 'result' => acmeReportItem[] )
     */
    public static function fetchItems( $status = false, $limit = 10, $offset = 0 )
    {
        $access = eZUser::currentUser()->hasAccessTo( 'acmereport', 'read' );
        if ( $access['accessWord'] === 'no' )
            return array( 'result' => array() );

        $conds = null;
        if ( $status !== false && in_array( $status, array( 'open', 'closed' ), true ) )
            $conds = array( 'status' => $status );

        $items = eZPersistentObject::fetchObjectList( acmeReportItem::definition(), null, $conds,
                                                      array( 'created' => 'desc' ),
                                                      array( 'offset' => max( 0, (int)$offset ),
                                                             'length' => max( 1, min( 100, (int)$limit ) ) ) );
        return array( 'result' => is_array( $items ) ? $items : array() );
    }
}
```

Then `php bin/php/ezpgenerateautoloads.php -e` and clear the caches.

## Step 3: use it

```text
{def $items = fetch( 'acmereport', 'items', hash( 'status', 'open', 'limit', 5 ) )}
{foreach $items as $item}
    <li>{$item.status|wash} {$item.amount|wash}</li>
{/foreach}
{undef $items}
```

## How the kernel calls you

`eZModuleFunctionInfo` (`lib/ezutils/classes/ezmodulefunctioninfo.php`) reads the declaration, maps the named
parameters of the `hash()` to the method's parameters **in the order of `parameters`**, fills defaults, and calls
`call_method`. Keep the method's parameter list in the same order as the declaration.

## The result array

| You return | The template gets |
|---|---|
| `array( 'result' => $value )` | `$value` |
| `array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) )` | `null` |
| anything without `result` (also `array( 'result' => null )`) | `null`, and an error in the log: the function "did not return a result value" |

Return an empty array or `false` inside `result` for "nothing found", so templates can test it without errors in the log.

## Rules

- **Check permissions inside the function.** Templates are included from many places; the function is the only place
  that sees every call.
- **Bound every limit** and cast every parameter, as for any input.
- **Keep it thin.** Call the service your views and commands use.
- **Name parameters like the content fetches** (`limit`, `offset`, `sort_by`), so template authors guess right.
- **Cache-friendly.** Fetches inside `{cache-block}` run only when the block expires; do not depend on side effects.
- **Test the static method** like any other ([PHPUnit without a database](phpunit-without-a-database.md)).

## Related pages

- [Writing your own fetch function (application creators)](../creators/app-creators/writing-a-fetch-function.md)
- [Fetch functions reference](../extension-development/part-8-reference/fetch-functions-reference.md)
- [Fetch function pattern](../../standards/patterns/fetch-function.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
