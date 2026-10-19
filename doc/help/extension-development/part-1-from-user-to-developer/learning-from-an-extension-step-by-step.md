---
title: Learning from an extension, step by step
audience: user, developer, designer, app-creator
category: extension-development
order: 180
summary: Two real extensions taken apart file by file: str_replace, one operator in its 2005 shape with a PHP 8 bug and its one-line fix, and owsimpleoperator.
keywords: learn, read, example, str_replace, ezstr_replace, owsimpleoperator, template operator, eztemplateautoload, ExtensionAutoloadPath, operatorList, modify, constructor, PHP 8
type: help
---
# Learning from an extension, step by step

Part 1, chapter 18. The fastest way to understand extensions is to take a small one apart. This chapter reads two real
extensions from your installation, file by file, in the order the kernel reads them. You will meet the template
operator mechanism here first; [Template operators](../part-3-extension-developer/template-operators.md) covers it
fully later.

## Extension 1: str_replace

`extension/str_replace` adds one template operator, `ezstr_replace`, that wraps PHP's `str_replace()`. Its README
dates the first release to April 2005, and the code is still in that original shape, which makes it a perfect
specimen: small, complete, and old enough to teach a PHP 8 lesson.

### Step 1: list the files

```bash
find extension/str_replace -type f -not -path '*/.git/*' | sort
```

```text
extension/str_replace/LICENSE
extension/str_replace/LICENSE.md
extension/str_replace/README.md
extension/str_replace/autoloads/eztemplateautoload.php
extension/str_replace/autoloads/str_replace_controloperator.php
extension/str_replace/composer.json
extension/str_replace/doc/readme.txt
extension/str_replace/ezinfo.php
extension/str_replace/settings/site.ini.append.php
```

Three files do the work: one setting, one registration, one class.

### Step 2: the setting that plugs it in

`settings/site.ini.append.php`:

```ini
[TemplateSettings]
ExtensionAutoloadPath[]=str_replace
```

`ExtensionAutoloadPath[]` tells the template engine: "also read `extension/str_replace/autoloads/eztemplateautoload.php`".
That is the whole plug. Verify how the engine reads it: `grep -n "ExtensionAutoloadPath" lib/eztemplate/classes/eztemplate.php`.

### Step 3: the registration

`autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] =
  array( 'script' => 'extension/str_replace/autoloads/str_replace_controloperator.php',
         'class' => 'MyStrReplaceOperator',
         'operator_names' => array( 'ezstr_replace' ) );
```

It names the file, the class and the operator names. The engine registers the names at once but loads the class only
when a template uses one of them (`eZTemplate::registerAutoloadOperators()` and `loadAndRegisterOperators()` in
`lib/eztemplate/classes/eztemplate.php`).

### Step 4: the class

The class has four methods the engine calls:

| Method | Called | Returns |
|---|---|---|
| `operatorList()` | when the class is loaded | the operator names this object handles |
| `namedParameterPerOperator()` | when the class is loaded | `true`: the parameter list below is per operator |
| `namedParameterList()` | when a template calls the operator | each operator's parameters: name, type, required, default |
| `modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )` | for each call | nothing; it writes the result into `$operatorValue` |

The interesting part of `modify()`:

```php
$subject = $namedParameters['subject'] !== null
    ? $namedParameters['subject']
    : $operatorValue;
$operatorValue = str_replace( $search, $replace, $subject );
```

`$operatorValue` is both the input and the output: when a template writes `{$text|ezstr_replace( 'a', 'b' )}`, the
piped `$text` arrives in `$operatorValue`, and whatever the method leaves there is the result.

### Step 5: the PHP 8 lesson

The class sets up its operator list in a method named like the class:

```php
function MyStrReplaceOperator()
{
    $this->Operators = array( 'ezstr_replace');
}
```

That was a constructor in PHP 4 and PHP 5. **PHP 8 no longer treats a method named like its class as a constructor**,
so it is never called, `$this->Operators` stays `null`, and `operatorList()` returns `null`. Rendered on PHP 8.5 with
the book's test harness, a template calling `ezstr_replace` produces a PHP warning, "foreach() argument must be of type
array|object, null given", in `eZTemplate::registerOperatorsInternal()`, followed by the template error
"Object problem with operator 'ezstr_replace'", and the operator prints nothing.

The fix is one line, renaming the method to `__construct`:

```php
public function __construct()
{
    $this->Operators = array( 'ezstr_replace' );
}
```

With that change the same template prints `Hello Exponential` for `{ezstr_replace( 'world', 'Exponential', 'Hello world' )}`.
This book's harness rendered both versions before this page was written. This is the most common PHP 8 problem in old
extensions; Old-style constructors and static calls shows
how to find and fix every case.

### Step 6: the metadata

`ezinfo.php` names the extension, version `1.0.0`, the copyright of 7x and of the original author (2005), and the
license. Its `info()` method is not declared `static`, which still works but differs from the convention the kernel
reads best; see [ezinfo.php](../part-3-extension-developer/ezinfo-php.md). `composer.json` declares the package
`se7enxweb/str_replace` of type `ezpublish-legacy-extension`, `GPL-2.0-or-later`, and requires `"php": ">=5.0.0"`,
another sign of its age: a modernized package states the PHP versions it was tested on.

## Extension 2: owsimpleoperator

`extension/owsimpleoperator` (from OPEN WIDE, GPL 3 or later according to its README and `ezinfo.php`) solves the
boilerplate problem you just saw: it lets you write an operator as a plain method, and lets templates call PHP
functions you permit.

### Its files

```bash
ls extension/owsimpleoperator/autoloads extension/owsimpleoperator/settings
```

```text
exampleoperator.php  eztemplateautoload.php  owezpautoloader.php  owsimpleoperator.php  phpfunctionoperator.php
owsimpleoperator.ini.append.php  site.ini.append.php
```

The plug is the same `ExtensionAutoloadPath[]=owsimpleoperator` in `site.ini.append.php`. The difference is in the
registration: `eztemplateautoload.php` builds `$eZTemplateOperatorArray` **from settings**:

- `[PHPFunctions] PermittedFunctionList[]` in `owsimpleoperator.ini` lists PHP functions that templates may call as
  operators. The shipped list is `time`, `mktime`, `getdate`, `str_replace` and `str_rot13`.
- `[ClassOperators] PermittedClassOperatorList[]` lists `file;class;method` triples; each method becomes an operator.

### An operator as a method

`autoloads/exampleoperator.php`:

```php
<?php
class ExampleOperator extends OWSimpleOperator
{
    public function example_sum( $number1, $number2 )
    {
        return ( $number1 + $number2 );
    }
}
```

The base class `OWSimpleOperator` implements `operatorList()`, `namedParameterList()` and `modify()` for you: it calls
the method named like the operator with the template's arguments and uses the return value. To switch the example on
you would add, in your own settings:

```ini
[ClassOperators]
PermittedClassOperatorList[]=extension/owsimpleoperator/autoloads/exampleoperator.php;ExampleOperator;example_sum
```

### Try the PHP function operators

On an installation where `owsimpleoperator` is active (check with
`./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`), a template can write:

```text
[{str_rot13( 'Exponential' )}] [{str_replace( 'world', 'there', 'Hello world' )}]
```

which renders `[Rkcbaragvny] [Hello there]`. That output was produced by the kernel's template engine when this
page was written.

> **Warning**
> Every function in `PermittedFunctionList[]` can be called by every template with any argument. List only functions
> without side effects that read nothing outside their arguments. A function like `file_get_contents` would let a
> template read any file the web server can read; the shipped settings file says exactly that in its comment.

## What you learned

| Concept | Where you saw it |
|---|---|
| An extension plugs in by adding to a setting | `ExtensionAutoloadPath[]` |
| Operators are registered by name, loaded on first use | `eztemplateautoload.php` |
| The operator contract: `operatorList`, `namedParameterList`, `modify` | `MyStrReplaceOperator` |
| `$operatorValue` is input and output | `modify()` |
| PHP 8 ignores old-style constructors | `function MyStrReplaceOperator()` |
| A base class can remove boilerplate | `OWSimpleOperator` |
| Settings can widen what templates may do, so keep them narrow | `PermittedFunctionList[]` |

## Related pages

- Previous: [Evaluating an old extension before you use it](evaluating-an-old-extension.md)
- Next: [Debug output and logs for developers](debug-output-and-logs-for-developers.md)
- [Template operators](../part-3-extension-developer/template-operators.md)
- [By hand: a template operator](../part-3-extension-developer/tutorials/by-hand-template-operator.md)
- [owsimpleoperator in the extension catalogue](../../../features/6.0/extensions/owsimpleoperator.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
