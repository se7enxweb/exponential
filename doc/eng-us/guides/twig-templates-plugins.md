# Twig templates: writing operators and functions

This guide is for PHP developers who add template operators or functions and want them in `.twig` (and `.stpl`) as
well as in `.tpl`. The short answer: write them as you always have, for the template system. Every operator and
every template function the template system has registered is a Twig filter, a Twig function or a tag, with no Twig
code of your own.

## An operator

A template operator is a class with `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` and
`modify()`, registered in the extension's `autoloads/eztemplateautoload.php`:

```php
<?php
// extension/<ext>/autoloads/eztemplateautoload.php
$eZTemplateOperatorArray[] = array( 'class' => 'myShoutOperator', 'operator_names' => array( 'shout' ) );
```

```php
<?php
// extension/<ext>/classes/myshoutoperator.php
class myShoutOperator
{
    public function operatorList()
    {
        return array( 'shout' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'shout' => array( 'marks' => array( 'type' => 'integer', 'required' => false, 'default' => 1 ) ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                            &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = strtoupper( (string)$operatorValue ) . str_repeat( '!', (int)$namedParameters['marks'] );
    }
}
```

With the extension active and the autoloads regenerated, all three engines have it:

| `.tpl` | `.stpl` | `.twig` |
|---|---|---|
| `{$name\|shout( 3 )}` | `{$name\|shout:3}` | `{{ name\|shout(3) }}` |

With `name` = `'Ada'` each prints `ADA!!!`.

What to mind:

- **The input.** `modify()` gets the value the template pipes in as `$operatorValue`. An operator that reads its
  parameters only (no input) should say so in `operatorTemplateHints()` (`'input' => false`), or be listed in
  `site.ini [TemplateBridgeSettings] ParameterOperatorList[]`, so `{{ op(a, b) }}` and `{{ a|op(b) }}` are read as
  the `.tpl` reads them.
- **Values, not proxies.** The bridge hands `modify()` the template system's own values: arrays, scalars and the
  objects themselves, never Twig's wrappers. What `modify()` returns is wrapped for Twig again.
- **The name.** If Twig has a filter or function of the same name (`round`, `replace`, `merge` ...), templates call
  yours as `exp_<name>`; a name of your own avoids that.
- **No Twig dependency.** Nothing in the class refers to Twig; the operator works when Twig is not installed.

## A template function

A template function (a class with `functionList()` and `process()`, registered in `$eZTemplateFunctionArray`) is a
Twig function taking one hash when it has no body (`{{ my_box({title: 'x'}) }}`), and a tag with an end tag when it
has one (`{% my_box title='x' %}...{% endmy_box %}`). `[TwigSettings] FunctionList[]` is only needed for a function
the template system does not register through the autoload arrays.

## Checking

```bash
./console exp:template:check --level=strict extension/<ext>/design/<design>/templates
```

reports templates that call an operator name no extension registers, in all three engines.

## Related

- [Operators and fetch functions](twig-templates-operators.md)
- [Template functions and tags](twig-templates-functions.md)
- [Smarty templates: plugins](smarty-templates-plugins.md)
