---
title: Writing template operators
audience: app-creator, developer
category: app-creation
order: 170
summary: Add your own template operators - the autoload registration file, the operator class with operatorList, namedParameterList and modify, piped and parameter input, escaping, and when an operator is the wrong tool.
keywords: template operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterList, modify, operatorValue, ExtensionAutoloadPath, templates, app creator
type: help
---
# Writing template operators

This page is for application creators who give templates a new operator, such as `{$price|expdemo_money( 'EUR' )}`.
An operator is a PHP class registered in the extension's `autoloads/eztemplateautoload.php`. The RAD tool
**Template extension** (`/setup/templateoperator`) writes a working one for you ([RAD tools](rad-tools.md)).

## Contents

1. [Registration](#registration)
2. [The operator class](#the-operator-class)
3. [Input and output](#input-and-output)
4. [Rules](#rules)
5. [When not to write an operator](#when-not-to-write-an-operator)

## Registration

```ini
# extension/expdemo/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[TemplateSettings]
ExtensionAutoloadPath[]=expdemo
*/ ?>
```

```php
<?php
// extension/expdemo/autoloads/eztemplateautoload.php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/expdemo/autoloads/expdemotemplateoperators.php',
    'class' => 'expDemoTemplateOperators',
    'operator_names' => array( 'expdemo_money' ) );
```

The shipped `ezstarrating` extension registers its operators the same way
(`extension/ezstarrating/autoloads/eztemplateautoload.php`).

## The operator class

```php
<?php
class expDemoTemplateOperators
{
    function operatorList()
    {
        return array( 'expdemo_money' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'expdemo_money' => array(
            'currency' => array( 'type' => 'string', 'required' => false, 'default' => 'EUR' ) ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'expdemo_money':
                $operatorValue = number_format( (float)$operatorValue, 2 ) . ' ' . $namedParameters['currency'];
                break;
        }
    }
}
```

## Input and output

- The piped value (`$price|expdemo_money`) arrives in `$operatorValue`; set it to the result.
- Named parameters arrive in `$namedParameters`, with their defaults applied.
- An operator used without input (`{expdemo_money( 12 )}`) gets `null` as `$operatorValue`; declare the value as a
  parameter if that is how it is used.

## Rules

- Return values, not HTML: let the template print and `wash` them. If you must return HTML, escape every piece of
  input with `htmlspecialchars()`.
- Keep operators fast and side-effect free; they run on every render and inside cache blocks.
- Prefix new operator names with your extension (`expdemo_...`) so they never collide.
- Regenerate autoloads and clear the template caches after adding one.

## When not to write an operator

| Need | Better |
|---|---|
| Read data | a fetch function ([Writing your own fetch function](writing-a-fetch-function.md)) |
| String formatting | the shipped string operators ([String template operators](../../../features/6.0/string-template-operators.md)) |
| Permission checks | [Role and policy template operators](../../../features/6.0/role-and-policy-template-operators.md) |
| Markup reused in many templates | an `{include}` of a template part |

## Related pages

- [Template functions, includes and cache blocks](template-functions-and-includes.md)
- [Template operator pattern](../../../standards/patterns/template-operator.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
