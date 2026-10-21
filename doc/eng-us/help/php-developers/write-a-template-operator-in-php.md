---
title: Write a template operator in PHP
audience: php-developer, developer
category: php-development
order: 270
summary: A task guide for a template operator written in PHP - the eztemplateautoload.php registration, the operator class with operatorList(), namedParameterPerOperator(), namedParameterList() and modify(), input and output values, escaping, performance, and testing modify() without a template.
keywords: template operator, eztemplateautoload.php, eZTemplateOperatorArray, ExtensionAutoloadPath, operatorList, namedParameterPerOperator, namedParameterList, modify, operatorValue, namedParameters, operatorTemplateHints, wash, template engine, PHP
type: help
---
# Write a template operator in PHP

This task guide is for PHP developers who give templates a new operator, such as `{$amount|acmereport_money( 'EUR' )}`.
An operator is a PHP class that the template engine (`lib/eztemplate/`) calls with the piped value and the parameters.
Template authors find the shipped operators in the [template operators](../template-operators/README.md) section; the
application view is [Writing template operators](../creators/app-creators/template-operators.md).

## Contents

1. [Step 1: register the autoload path](#step-1-register-the-autoload-path)
2. [Step 2: list the operator](#step-2-list-the-operator)
3. [Step 3: the operator class](#step-3-the-operator-class)
4. [Step 4: use it](#step-4-use-it)
5. [Input and output](#input-and-output)
6. [Escaping](#escaping)
7. [Performance and compiled templates](#performance-and-compiled-templates)
8. [Testing modify()](#testing-modify)

## Step 1: register the autoload path

`extension/acmereport/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[TemplateSettings]
ExtensionAutoloadPath[]=acmereport
*/ ?>
```

The template engine then reads `extension/acmereport/autoloads/eztemplateautoload.php`, as it does for
`ezjscore` (`extension/ezjscore/settings/site.ini.append.php`).

## Step 2: list the operator

`extension/acmereport/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/acmereport/autoloads/acmereporttemplateoperators.php',
    'class' => 'acmeReportTemplateOperators',
    'operator_names' => array( 'acmereport_money' ) );
```

The engine loads the class only when a template uses one of the names, so many operators cost nothing until used.
`extension/ezjscore/autoloads/eztemplateautoload.php` registers `json_encode`, `ezscript` and others the same way.

## Step 3: the operator class

`extension/acmereport/autoloads/acmereporttemplateoperators.php`:

```php
<?php
class acmeReportTemplateOperators
{
    function operatorList()
    {
        return array( 'acmereport_money' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'acmereport_money' => array(
                'currency' => array( 'type' => 'string', 'required' => false, 'default' => 'EUR' ),
                'decimals' => array( 'type' => 'integer', 'required' => false, 'default' => 2 ) ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'acmereport_money':
            {
                $decimals = max( 0, min( 4, (int)$namedParameters['decimals'] ) );
                $operatorValue = number_format( (float)$operatorValue, $decimals ) . ' ' . $namedParameters['currency'];
            } break;
        }
    }
}
```

## Step 4: use it

```text
{$item.amount|acmereport_money()|wash}
{$item.amount|acmereport_money( 'USD', 0 )|wash}
```

Regenerate the autoloads and clear the template caches (`php bin/php/ezcache.php --clear-tag=template`); under Velocity
use `exp:velocity deploy`.

## Input and output

| What | Where it arrives |
|---|---|
| the piped value (`$x\|op`) | `$operatorValue`; set it to the result (it is passed by reference) |
| named parameters | `$namedParameters`, defaults applied |
| raw parameters | `$operatorParameters`, unevaluated; use `$tpl->elementValue()` only if you know you need them |
| the operator's name | `$operatorName`, so one class can serve several operators |

An operator used without a piped value (`{acmereport_money( 12 )}`) gets `null` in `$operatorValue`; declare the value
as a parameter if that is how it is used.

## Escaping

Return values, not HTML, and let the template print them through `|wash`. If an operator must return markup, escape
every piece of input in it with `htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' )` (the
flags are needed for PHP 8.0, see [Escaping output from PHP](escaping-output-from-php.md)), and never put inline
`style=""` or `<script>` into it ([Content Security Policy for your code](../creators/app-creators/content-security-policy.md)).

## Performance and compiled templates

- Operators run on every render, also inside loops. Keep `modify()` fast and free of database queries; data belongs in
  a fetch function ([Write a fetch function](write-a-fetch-function.md)).
- Templates are compiled to PHP. Kernel operators that are pure can describe themselves with
  `operatorTemplateHints()` so the compiler inlines them (`kernel/common/ezkerneloperator.php` is an example); your
  operator works without it.
- No side effects: inside a `{cache-block}` the operator does not run again until the block expires.

## Testing modify()

`modify()` is a plain method; call it in a DB-less test:

```php
public function testMoneyFormatsTwoDecimals()
{
    $operators = new acmeReportTemplateOperators();
    $value = 12.5;
    $operators->modify( null, 'acmereport_money', array(), '', '', $value,
                        array( 'currency' => 'EUR', 'decimals' => 2 ) );
    $this->assertSame( '12.50 EUR', $value );
}
```

## Related pages

- [Template operators (extension developers)](../extension-development/part-3-extension-developer/template-operators.md)
- [Template operator pattern](../../standards/patterns/template-operator.md)
- [The template language](../template-language/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
