---
title: "By hand: a template operator"
audience: developer, app-creator, designer
category: extension-development
order: 110
summary: Add two template operators to mysite_hello by hand - the plug, the registration and the class - check them with php -l, switch them on and use them, with the output the template engine produced.
keywords: tutorial, by hand, template operator, eztemplateautoload.php, ExtensionAutoloadPath, operatorList, namedParameterList, modify, mysite_greet, mysite_initials
type: help
---
# By hand: a template operator

Tutorial 10. You add two operators to the `mysite_hello` extension of the previous tutorial: `mysite_greet( name,
greeting )` and `$text|mysite_initials`. Every file passed `php -l`; the template at the end was rendered by the
template engine with these files.

## 1. The plug

Add to `extension/mysite_hello/settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

## 2. The registration

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

## 3. The class

`extension/mysite_hello/classes/mysitehellooperators.php`:

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

## 4. Check and switch on

```bash
php -l extension/mysite_hello/autoloads/eztemplateautoload.php
php -l extension/mysite_hello/classes/mysitehellooperators.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 5. Use it

Put this in any template you can open, for example an override of the front page:

```text
[{mysite_greet( 'Ada' )|wash}] [{mysite_greet( 'Ada', 'Welcome' )|wash}] [{'Ada Lovelace'|mysite_initials}] [{'émile zola'|mysite_initials}]
```

It renders:

```text
[Hello, Ada!] [Welcome, Ada!] [AL] [ÉZ]
```

## 6. If it does not work

| Message in the debug output | Fix |
|---|---|
| `Operator 'mysite_greet' is not registered` | step 1 is missing, the extension is inactive, or the INI cache was not cleared |
| `Object problem with operator 'mysite_greet'` | the class could not be created: check the `script` path and that `operatorList()` returns the names |
| nothing printed | `modify()` did not set `$operatorValue` for that operator |

## 7. Commit

```bash
git -C extension/mysite_hello add autoloads classes settings
git -C extension/mysite_hello commit -m "Added: The template operators mysite_greet and mysite_initials"
```

## Related pages

- Previous: [By hand: the smallest extension in five minutes](by-hand-minimal-extension.md)
- Next: [By hand: a fetch function](by-hand-fetch-function.md)
- [Template operators](../template-operators.md), [With RAD: a template operator in five minutes](rad-template-operator.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
