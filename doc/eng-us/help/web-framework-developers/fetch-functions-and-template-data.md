---
title: "Getting data into templates: fetch functions and operators"
audience: web-framework-developer, developer
category: web-frameworks
order: 120
summary: Where a framework passes view models from the controller, Exponential templates can also pull data themselves - fetch functions declared in function_definition.php, template operators for formatting, and variables set by the view - with the permission and performance rules that come with pulling data in templates.
keywords: fetch, fetch function, function_definition.php, call_method, template operator, view model, view composer, Twig extension, data in templates, eZFunctionHandler, eztemplateautoload, web framework developer
type: help
---
# Getting data into templates: fetch functions and operators

This page is for developers who pass everything a template needs from the controller. Exponential supports that
(`$tpl->setVariable()` in your view), but much of its own design works the other way: templates **pull** data with
`fetch( 'module', 'function', hash( ... ) )`. That is why a content override template can list children, related
items or the current user without any PHP. Your extension can offer its own fetch functions and template operators.

## Contents

1. [Three ways data reaches a template](#three-ways-data-reaches-a-template)
2. [Fetch functions](#fetch-functions)
3. [Writing a fetch function](#writing-a-fetch-function)
4. [Template operators](#template-operators)
5. [Rules for pulling data](#rules-for-pulling-data)

## Three ways data reaches a template

| Way | Framework analogy | Use for |
|---|---|---|
| `$tpl->setVariable( 'items', $items )` in a view | passing variables to `render()` | data of your own module view |
| `fetch( 'expdemo', 'items', hash( 'limit', 5 ) )` in the template | view composers, Twig functions backed by services, template tags that query | data an override or a shared partial needs wherever it is used |
| An operator: `$price\|expdemo_money` | Twig filters, Blade directives, Django template filters | formatting and small computations |

## Fetch functions

The kernel's modules offer many: `fetch( 'content', 'node', hash( 'node_id', 2 ) )`,
`fetch( 'content', 'list', hash( 'parent_node_id', 2, 'limit', 10 ) )`,
`fetch( 'user', 'current_user' )`, `fetch( 'user', 'has_access_to', hash( 'module', 'expdemo', 'function', 'manage' ) )`.
Each module declares them in `function_definition.php`, which maps a fetch to a `call_method` and its parameters;
`eZFunctionHandler` (`lib/ezutils/classes/ezfunctionhandler.php`) executes them
([Fetch functions](../../standards/architecture/kernel-architecture.md#fetch-functions),
[Fetch functions: reading data in templates](../creators/app-creators/fetch-functions.md)).

## Writing a fetch function

From [Writing your own fetch function](../creators/app-creators/writing-a-fetch-function.md):

```php
<?php
// extension/expdemo/modules/expdemo/function_definition.php
$FunctionList = array();
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

The method returns `array( 'result' => $value )`. Objects returned should be `eZPersistentObject`s or offer
`attribute()` and `hasAttribute()`, so templates can read `$item.title`.

```text
{def $items = fetch( 'expdemo', 'items', hash( 'limit', 5 ) )}
{foreach $items as $item}<li>{$item.title|wash}</li>{/foreach}
{undef $items}
```

## Template operators

An operator is a PHP class registered in `extension/<ext>/autoloads/eztemplateautoload.php`, found because the
extension is listed in `site.ini [TemplateSettings] ExtensionAutoloadPath[]`
([Writing template operators](../creators/app-creators/template-operators.md),
[Functions or operators](../template-language/functions-vs-operators.md)). Operators must escape what they build or
return data the template washes.

## Rules for pulling data

- **Permissions are applied by the content fetches** (`content/read` with its limitations). Your own fetches must check
  permissions themselves, because a template can call them from anywhere.
- **Bound every limit.** A fetch without `limit` on a large tree is the most common performance problem.
- **Mind the caches.** Content views are cached; data from your own tables does not expire them. Use a `{cache-block}`
  with an `expiry` or clear the cache when your data changes ([Caching](caching-layers.md)).
- **Keep the method thin.** Call the same service class your views and commands use, and test it with PHPUnit.

## Related pages

- [The view layer](templates-vs-twig-and-blade.md)
- [Fetch function pattern](../../standards/patterns/fetch-function.md)
- [Template operator pattern](../../standards/patterns/template-operator.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
