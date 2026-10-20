---
title: "With RAD: a template operator in five minutes"
audience: developer, app-creator, designer
category: extension-development
order: 20
summary: Use the Template extension wizard to write a working extension with an operator and a fetch function, see every file it writes, fill in the operator's one line, switch it on and use it - with the wizard's real output.
keywords: RAD, template extension wizard, setup/templateoperator, template operator, fetch function, fetch alias, template function, generated code, operatorTemplateHints, ExtensionAutoloadPath
type: help
---
# With RAD: a template operator in five minutes

Tutorial 2. The **Template extension** wizard (`/setup/templateoperator`) writes operators, template functions, fetch
functions and fetch aliases. This tutorial makes an operator `mysite_greet` and a fetch function `newest`. The file list
and code excerpts below are the wizard's real output: its generator (`expTemplateExtensionWizard::files()`) was run with
the same input, every generated PHP file passed `php -l`, and the operator, once filled in, was rendered by the template
engine.

## 1. Open the wizard

**Setup > RAD**, then the tool of "Template operator", or `/setup/templateoperator` directly. The page explains the four
kinds it can add: "An operator takes a value and gives one back. A function writes output where it stands and may have
a body. A fetch function reads something, with a policy check first. A fetch alias is a fetch with its arguments already
decided."

## 2. Fill in the form

| Field | Value |
|---|---|
| Operators | `mysite_greet` |
| Fetch functions | `newest` |
| Parameters | `name string required` and, on the next line, `greeting string` |
| Operators take input | off (the operator makes its value from its parameters) |
| Operators produce output | on |
| Extension name | `mysite_hello` |
| Class name | `mysiteHelloOperators` |
| Module for the fetches | `hello` |
| Title | `Hello operators` |

Parameters are "one per line: a name, then a type, then the word required if it is". **What the compiler may assume**
sets the operator's template hints; leave the defaults until you know your operator is pure.

## 3. Read the preview

**Refresh preview**. "Every file, before it is written" lists:

```text
.gitignore
LICENSE
README.md
autoloads/eztemplateautoload.php
autoloads/mysitehellooperators.php
classes/mysitehellooperatorsfunctioncollection.php
composer.json
doc/examples.php
extension.xml
ezinfo.php
modules/hello/function_definition.php
modules/hello/module.php
settings/module.ini.append.php
settings/site.ini.append.php
```

Open a few. `settings/site.ini.append.php` holds the line that is most often forgotten, with a comment that says why:

```ini
[TemplateSettings]
# The extension whose autoloads/eztemplateautoload.php is read. Without this
# line the classes are never loaded and every name below is an unknown
# operator, reported in the debug output and nowhere else.
ExtensionAutoloadPath[]=mysite_hello
```

`autoloads/mysitehellooperators.php` has a proper `__construct()`, `operatorList()`, `namedParameterPerOperator()`,
`namedParameterList()`, `operatorTemplateHints()` and a `modify()` that does nothing yet, each with a comment. The
generated code deliberately does nothing, as the README says: "A template using any of them today prints what it
printed before, which is what makes this safe to install before it is written."

## 4. Create it

**Create in extension/**. If the web server may not write there, **Download as zip** and unpack it into
`extension/mysite_hello` yourself.

## 5. Write the operator

In `autoloads/mysitehellooperators.php`, the `modify()` case for `mysite_greet` reads the parameters and ends with a hint:
"This operator takes no input, so set the value rather than change it". Add one line:

```php
case 'mysite_greet':
{
    $name = $namedParameters['name'];
    $greeting = $namedParameters['greeting'];

    $operatorValue = ( $greeting ? $greeting : 'Hello' ) . ', ' . $name . '!';
} break;
```

## 6. Switch it on

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 7. Use it

```text
[{mysite_greet( 'Ada' )|wash}] [{mysite_greet( 'Ada', 'Welcome' )|wash}]
```

renders `[Hello, Ada!] [Welcome, Ada!]`.

> **Note**
> Operator parameters are given **by position**, in the order of the parameter list. The generated `README.md` and
> `doc/examples.php` show calls written as `{mysite_greet( name='text', greeting='text' )}`; the template engine does not
> accept that form for operators (it reports "Extra characters found in expression"). Write
> `{mysite_greet( 'text', 'text' )}` instead.

## 8. The fetch function

`classes/mysitehellooperatorsfunctioncollection.php` has `fetchNewest()` returning `array( 'result' => array() )`, with
the warning that "Every argument arrives exactly as a template wrote it, which means it may have come from a url". Fill
it in as in [Fetch functions](../fetch-functions.md); the module, its `function_definition.php` and the
`module.ini.append.php` registration are already written.

## What you got for five minutes

A complete, documented, licensed extension with metadata, Composer file, README and examples; an operator wired up
correctly; a fetch function in its own module. Compare it with the hand-written one in
[By hand: a template operator](by-hand-template-operator.md).

## Related pages

- Previous: [The RAD tools tour: Setup > RAD](the-rad-tools-tour.md)
- Next: [With RAD: a datatype](rad-datatype.md)
- [Template operators](../template-operators.md), [Fetch functions](../fetch-functions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
