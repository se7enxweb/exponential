---
title: Writing your own fetch function
audience: app-creator, developer
category: app-creation
order: 160
summary: Offer your application's data to templates with fetch() - function_definition.php, the function collection class returning array( 'result' => ... ), parameter types and defaults, permission checks, and testing it.
keywords: fetch function, function_definition.php, FunctionList, call_method, parameter_type, function collection, result, templates, permission, app creator
type: help
---
# Writing your own fetch function

This page is for application creators who want templates to read their data with `fetch( 'expdemo', 'items', hash(
... ) )`. A fetch function is declared in the module's `function_definition.php` and implemented by a static method
that returns `array( 'result' => ... )`.

## Contents

1. [The declaration](#the-declaration)
2. [The implementation](#the-implementation)
3. [Using it](#using-it)
4. [Rules](#rules)

## The declaration

`extension/expdemo/modules/expdemo/function_definition.php`, in the same directory as `module.php`:

```php
<?php
$FunctionList = array();
// fetch( 'expdemo', 'items', hash( 'limit', 5, 'offset', 0 ) )
$FunctionList['items'] = array(
    'name' => 'items',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expDemoFunctionCollection',
                            'method' => 'fetchItems' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'limit',  'type' => 'integer', 'required' => false, 'default' => 10 ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );
```

The kernel's own files follow the same shape (`kernel/exphelp/function_definition.php`, `kernel/content/function_definition.php`).

## The implementation

`extension/expdemo/classes/expdemofunctioncollection.php`:

```php
<?php
class expDemoFunctionCollection
{
    public static function fetchItems( $limit = 10, $offset = 0 )
    {
        $limit = max( 1, min( 100, (int)$limit ) );
        $offset = max( 0, (int)$offset );
        $access = eZUser::currentUser()->hasAccessTo( 'expdemo', 'read' );
        if ( $access['accessWord'] === 'no' )
            return array( 'result' => array() );
        return array( 'result' => expDemoItem::fetchList( $limit, $offset ) );
    }
}
```

Return `array( 'result' => $value )` on success. Regenerate the autoloads and clear the caches afterwards.

## Using it

```text
{def $items = fetch( 'expdemo', 'items', hash( 'limit', 5 ) )}
{foreach $items as $item}<li>{$item.title|wash}</li>{/foreach}
{undef $items}
```

Objects you return should be `eZPersistentObject`s or offer `attribute()` and `hasAttribute()`, so templates can read
`$item.title` ([Persistent objects and custom tables](persistent-objects-and-custom-tables.md)).

## Rules

- Check permissions inside the function; templates can be called from anywhere.
- Bound every limit.
- Keep the method thin: call a service class your views and commands use too.
- Name parameters as the content fetches do (`limit`, `offset`, `sort_by`), so template authors guess right.
- Test the static method with PHPUnit like any class ([DB-less PHPUnit tests](db-less-phpunit-tests.md)).

## Related pages

- [Fetch functions: reading data in templates](fetch-functions.md)
- [Fetch function pattern](../../../standards/patterns/fetch-function.md)
- [Writing template operators](template-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
