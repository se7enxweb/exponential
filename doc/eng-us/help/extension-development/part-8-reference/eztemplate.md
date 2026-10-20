---
title: eZTemplate from PHP
audience: developer
category: extension-development
order: 120
summary: Render templates from PHP with eZTemplate - factory(), setVariable(), fetch('design:...'), hasErrors() - in module views, cronjobs and mail, and how template operators and functions are registered.
keywords: eZTemplate, factory, setVariable, hasVariable, variable, fetch, design resource, file resource, hasErrors, errorLog, render, template from PHP, eztemplateautoload
type: help
---
# eZTemplate from PHP

Part 8, reference 12. Module views, mail and some cronjobs render templates from PHP. `eZTemplate`
(`lib/eztemplate/classes/eztemplate.php`) is the template engine. This page covers using it; writing operators and
functions for it is in [Template operators](../part-3-extension-developer/template-operators.md) and
[Template functions](../part-3-extension-developer/template-functions.md).

## The usual three lines

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', $items );
$html = $tpl->fetch( 'design:myext/list.tpl' );
```

| Method | Use |
|---|---|
| `eZTemplate::factory()` (static) | the template engine, set up with the site's settings, operators and functions |
| `setVariable( $var, $val, $namespace = '', $scopeSafe = false )` | make a value available as `$var` in the template |
| `hasVariable( $var, $namespace = '', $attrs = null )`, `variable( $var, ... )` | read variables back |
| `fetch( $template = false, $extraParameters = false, $returnResourceData = false )` | render and return the output |
| `hasErrors()`, `errorLog()` | whether rendering reported errors, and which |
| `resetVariables()` | clear the variables before rendering another template with the same engine |

## Resources

The template name has a resource prefix:

| Prefix | Finds the template |
|---|---|
| `design:` | through the design chain, with override rules: `design:myext/list.tpl` |
| `file:` | at a file path relative to the installation root |

Always use `design:` for templates of your extension, so designs and overrides can replace them
([The design chain](../part-2-builder/the-design-chain.md)).

## In a module view

```php
<?php
$Module = $Params['Module'];
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', myExtItem::fetchByParent( 2 ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:myext/list.tpl' );
$Result['path'] = array( array( 'url' => false, 'text' => ezpI18n::tr( 'extension/myext', 'Items' ) ) );
```

The kernel then draws `$Result['content']` inside the pagelayout ([View results, the path and the pagelayout](../part-3-extension-developer/view-results-and-the-pagelayout.md)).

## Variables in templates are values

Objects passed with `setVariable()` are read in templates through `attribute()`, `hasAttribute()` and
`attributes()`. Plain arrays work directly. A PHP object without those methods shows nothing in a template.

## Errors

Template errors (an unknown variable, an operator that does not exist) are written to the debug output with the
template's name and line. Check `hasErrors()` in scripts and tests:

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', 'World' );
$out = $tpl->fetch( 'design:myext/hello.tpl' );
if ( $tpl->hasErrors() )
{
    eZDebug::writeError( $tpl->errorLog(), __METHOD__ );
}
```

## Caching

Compiled templates are cached under the site's cache directory. A new template file needs the template-override
cache cleared before an override rule finds it, and rendered output kept in view caches or `cache-block`s stays until
those caches are cleared ([Cache tags, cache blocks and clearing caches](cache-tags-and-clearing.md)).

## Related pages

- Previous: [eZSys and eZExtension](ezsys-and-ezextension.md)
- Next: [eZModule and module results](ezmodule.md)
- [Built-in template operators](template-operators-reference.md), [Built-in template functions](template-functions-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
