---
title: Your own operators with OW Simple Operator
audience: developer, app-creator
category: development
order: 910
summary: Writing template operators as plain public methods of a class that extends OWSimpleOperator, registering each method in owsimpleoperator.ini, optional arguments, the pipe form, and what happens when the class is registered the usual way instead; every example rendered on a development installation.
keywords: owsimpleoperator, OWSimpleOperator, PermittedClassOperatorList, ClassOperators, ExampleOperator, example_sum, custom operator, template operator class, extension
type: help
---
# Your own operators with OW Simple Operator

With OW Simple Operator (`extension/owsimpleoperator`) an operator is a public method. You write the method,
list it in `owsimpleoperator.ini`, and the base class `OWSimpleOperator` supplies `operatorList()`,
`namedParameterList()`, `namedParameterPerOperator()` and `modify()`. For when the base class is enough and when
a full operator class is the better choice, see [OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md).

## Contents

1. [The class](#the-class)
2. [Registering the methods](#registering-the-methods)
3. [Using them](#using-them)
4. [Optional arguments](#optional-arguments)
5. [Do not register the class the usual way](#do-not-register-the-class-the-usual-way)
6. [Checklist](#checklist)

## The class

The extension ships an example, `extension/owsimpleoperator/autoloads/exampleoperator.php`:

```php
class ExampleOperator extends OWSimpleOperator
{
    /*!
     * Return the sum of two numbers
     */
    public function example_sum( $number1, $number2 )
    {
        return ( $number1 + $number2 );
    }
}
```

A class of your own lives in your extension, for example `extension/myextension/autoloads/c11exampletextoperators.php`.
The examples on this page use this class:

```php
class C11ExampleTextOperators extends OWSimpleOperator
{
    /*!
     * The initials of a name: "Fit & Healthy" -> "FH"
     */
    public function initials( $text )
    {
        $letters = '';
        foreach ( preg_split( '/[^\p{L}\p{N}]+/u', (string)$text, -1, PREG_SPLIT_NO_EMPTY ) as $word )
            $letters .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
        return $letters;
    }

    /*!
     * Reading time in minutes for a text, at $wordsPerMinute (default 200)
     */
    public function reading_minutes( $text, $wordsPerMinute = null )
    {
        $wordsPerMinute = $wordsPerMinute ? (int)$wordsPerMinute : 200;
        $words = str_word_count( strip_tags( (string)$text ) );
        return max( 1, (int)ceil( $words / $wordsPerMinute ) );
    }
}
```

The method name is the operator name, so choose names that no other operator uses (see
[Registration order and overrides](registration-order-and-overrides.md)); a prefix such as your extension's name is
the safe habit. The method returns the value; it never echoes.

## Registering the methods

Each operator is one line of `[ClassOperators] PermittedClassOperatorList[]` in `owsimpleoperator.ini`, written
`class file;class name;method`:

```ini
<?php /* #?ini charset="utf-8"?

[ClassOperators]
PermittedClassOperatorList[]=extension/myextension/autoloads/c11exampletextoperators.php;C11ExampleTextOperators;initials
PermittedClassOperatorList[]=extension/myextension/autoloads/c11exampletextoperators.php;C11ExampleTextOperators;reading_minutes

*/ ?>
```

Put this in `extension/myextension/settings/owsimpleoperator.ini.append.php` (your extension must be active;
the INI files of all active extensions are merged) or in a siteaccess's
settings. Do not start your list with an empty `PermittedClassOperatorList[]` line: in an INI append that empties
the list collected so far and removes the operators other extensions registered.

`extension/owsimpleoperator/autoloads/eztemplateautoload.php` turns these lines into one
`$eZTemplateOperatorArray` entry per class (`script`, `class`, `operator_names`). Clear the INI and template
caches after a change (`php bin/php/ezcache.php --clear-tag=ini --clear-tag=template --allow-root-user`, or
`./console exp:velocity deploy --allow-root-user` on a development installation). Your class needs no entry in the autoload arrays: the
`script` path loads it. `OWSimpleOperator` itself does, and the extension's installation step
(`php bin/php/ezpgenerateautoloads.php -e`) provides it.

## Using them

Not run on a development installation with a file: the examples on this page were rendered by a script that put the two lines above
(and the line for the shipped `ExampleOperator`) into `owsimpleoperator.ini` in memory only, for that one process,
with the class file in a scratch folder. Nothing was written to the installation's settings.

Both call forms work for class operators:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{example_sum( 1, 2 )}
{1|example_sum( 2 )}
{initials( 'Fit & Healthy' )}
{'Bold Agency'|initials}
{$node.name|wash} -> {$node.name|initials}
```

Output:

```text
3
3
FH
BA
Fit &amp; Healthy -> FH
```

In the pipe form the piped value becomes the first argument of the method, and the arguments in the parentheses
follow it: `{1|example_sum( 2 )}` calls `example_sum( 1, 2 )`.

## Optional arguments

`OWSimpleOperator::namedParameterList()` declares ten optional parameters with the default `null` for every
operator, and `modify()` passes all ten to your method. An argument the template leaves out therefore arrives as
`null`, never as your PHP default. That is why the extension's README says: "If an operator argument is optional,
you have to set the default value to null." Give the parameter the default `null` and choose the real default in
the body, as `reading_minutes()` does:

```tpl
{def $text = 'one two three four five six seven eight nine ten'}
{reading_minutes( $text )} {reading_minutes( $text, 4 )} {$text|reading_minutes( 4 )}
```

Output:

```text
1 3 3
```

Ten words at the default 200 words a minute round up to 1; at 4 words a minute to 3, in both call forms.
A method that needs more than ten arguments must raise `$max_operator_parameter` in its class.

## Do not register the class the usual way

An `OWSimpleOperator` subclass must be registered in `owsimpleoperator.ini`. `OWSimpleOperator::operatorList()`
does not read your extension's `autoloads/eztemplateautoload.php`: it looks up the folder of the **parent** class
(through `OWEzpAutoloader::get_class_folder_path()`), which is `extension/owsimpleoperator/autoloads/`, includes
the `eztemplateautoload.php` there and searches it for an entry with your class name. That file only knows the
classes listed in `owsimpleoperator.ini`.

Registered the usual way, with an `$eZTemplateOperatorArray` entry in another extension and nothing in
`owsimpleoperator.ini` (rendered here by registering the entry on the engine in memory):

```tpl
{initials( 'Fit & Healthy' )}
{'Bold Agency'|initials}
```

Output:

```text

Bold Agency
```

Logged by the template engine:

```text
ERROR : Object problem with operator 'initials' 
ERROR : Object problem with operator 'initials'
```

The class loads, but its `operatorList()` comes back empty, so the engine finds no object for the name: the call
form prints nothing and the pipe form passes its input through unchanged. `operatorList()` also writes
`There are no operators registered for the class "C11ExampleTextOperators".` to the debug output.

## Checklist

- The class extends `OWSimpleOperator`; each operator is a public method that returns its result.
- Optional parameters default to `null`.
- Each method has a line in `[ClassOperators] PermittedClassOperatorList[]` with the right file path, class name
  and method name, and no empty `[]` line before it.
- The method name is not already an operator name on any siteaccess that uses it.
- Caches cleared, and the operator tried in both call forms.
- Output that contains user content is escaped in the template (`|wash`), because operator results are not.

## Related pages

- [OW Simple Operator: PHP functions as template operators](owsimpleoperator.md)
- [OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md)
- [Creating an operator, step by step](creating-an-operator-step-by-step.md)
- [The operator class interface](operator-class-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
