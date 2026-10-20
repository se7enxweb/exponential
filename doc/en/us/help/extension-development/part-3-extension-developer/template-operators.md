---
title: Template operators
audience: developer, app-creator, designer
category: extension-development
order: 130
summary: Give templates new operators: the registration, the four methods the engine calls, named parameters and piped input, a complete verified example, testing, and the PHP 8 pitfalls.
keywords: template operator, eztemplateautoload.php, eZTemplateOperatorArray, ExtensionAutoloadPath, operatorList, namedParameterList, namedParameterPerOperator, modify, operatorValue, named parameters, pipe
type: help
---
# Template operators

Part 3, chapter 13. A **template operator** is a function templates can call: `{mysite_greet( 'Ada' )}` or
`{$name|mysite_initials}`. It is the smallest useful piece of PHP an extension can add, and a good first one. This
chapter builds a class with two operators; the class and the template below were rendered by the kernel's template
engine before this page was written.

## Three pieces

| Piece | File |
|---|---|
| The plug | `settings/site.ini.append.php`: `[TemplateSettings] ExtensionAutoloadPath[]=mysite_hello` |
| The registration | `autoloads/eztemplateautoload.php`: which class handles which operator names |
| The class | `classes/mysitehellooperators.php` |

## The plug

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

## The registration

`extension/mysite_hello/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/mysite_hello/classes/mysitehellooperators.php',
    'class' => 'mysiteHelloOperators',
    'operator_names' => array( 'mysite_greet', 'mysite_initials' )
);
```

The engine (`eZTemplate::registerAutoloadOperators()`, `lib/eztemplate/classes/eztemplate.php`) registers the names
right away and loads the class only when a template first uses one of them. Instead of `operator_names` a definition
can give `operator_names_function`, a function that returns the names.

## The class

```php
<?php
/**
 * Template operators of mysite_hello:
 *   {mysite_greet( 'Ada' )}            -> Hello, Ada!
 *   {mysite_greet( 'Ada', 'Welcome' )} -> Welcome, Ada!
 *   {'Ada Lovelace'|mysite_initials}   -> AL
 */
class mysiteHelloOperators
{
    public function operatorList()
    {
        return array( 'mysite_greet', 'mysite_initials' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array(
            'mysite_greet' => array(
                'name'     => array( 'type' => 'string', 'required' => true, 'default' => '' ),
                'greeting' => array( 'type' => 'string', 'required' => false, 'default' => 'Hello' )
            ),
            'mysite_initials' => array()
        );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'mysite_greet':
                $operatorValue = $namedParameters['greeting'] . ', ' . $namedParameters['name'] . '!';
                break;

            case 'mysite_initials':
                $initials = '';
                foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ) ) as $word )
                {
                    if ( $word !== '' )
                        $initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
                }
                $operatorValue = $initials;
                break;
        }
    }
}
```

| Method | Called | Returns |
|---|---|---|
| `operatorList()` | when the class is loaded | the operator names this object handles |
| `namedParameterPerOperator()` | when the class is loaded | `true`: `namedParameterList()` is keyed by operator name |
| `namedParameterList()` | when a call is compiled | each operator's parameters, in order: `type`, `required`, `default` |
| `modify( ... &$operatorValue, $namedParameters )` | for each call | nothing: the result is written into `$operatorValue` |

`$operatorValue` is the input of a piped call (`{$text|mysite_initials}` passes `$text`) and the output of every call.
`$namedParameters` holds the parameters by the names you declared, with defaults filled in.

## The result

The template

```text
[{mysite_greet( 'Ada' )}] [{mysite_greet( 'Ada', 'Welcome' )}] [{'Ada Lovelace'|mysite_initials}] [{'émile zola'|mysite_initials}]
```

renders

```text
[Hello, Ada!] [Welcome, Ada!] [AL] [ÉZ]
```

Note the `mb_` functions: names are UTF-8, and `strtoupper()` would break the `é`.

## Escaping is the template's job

An operator returns a value; the template decides how to print it. Return plain text and let templates `|wash` it,
unless your operator's purpose is to build HTML, in which case escape every value you put into that HTML with
`htmlspecialchars()`.

## Make it pure

The best operators depend only on their parameters: no database writes, no output, no session. They are then safe to
call from cached templates and easy to test. An operator that needs data (a fetch, a setting) can read it, but should
never change anything; changes belong in a module view.

## Common mistakes

| Symptom | Cause |
|---|---|
| `Operator 'x' is not registered` | the plug is missing, the extension is not active, or the INI cache was not cleared |
| `Object problem with operator 'x'` | the class could not be created or `operatorList()` returned nothing: an old-style constructor (a method named like the class) is no constructor in PHP 8 |
| The operator returns nothing | `modify()` did not assign `$operatorValue` |
| A parameter is always the default | it is not in `namedParameterList()` for that operator |

## Testing it

Because the class needs nothing but its parameters, a PHPUnit test can call `modify()` directly:

```php
<?php
class mysiteHelloOperatorsTest extends PHPUnit\Framework\TestCase
{
    public function testInitials()
    {
        $operators = new mysiteHelloOperators();
        $value = 'Ada Lovelace';
        $operators->modify( null, 'mysite_initials', array(), '', '', $value, array() );
        $this->assertSame( 'AL', $value );
    }
}
```

See [Tests for extensions](tests-for-extensions.md).

## Simpler: owsimpleoperator

If you have many small operators, the `owsimpleoperator` extension lets you write each as a plain method; see
[Learning from an extension, step by step](../part-1-from-user-to-developer/learning-from-an-extension-step-by-step.md).

## RAD

**Template extension** (`/setup/templateoperator`) writes operators, fetch functions, attribute operators and template
functions; see [With RAD: a template operator in five minutes](tutorials/rad-template-operator.md).

## Related pages

- Previous: [View results, the path and the pagelayout](view-results-and-the-pagelayout.md)
- Next: [Fetch functions](fetch-functions.md)
- [By hand: a template operator](tutorials/by-hand-template-operator.md)
- [Built-in template operators](../part-8-reference/template-operators-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
