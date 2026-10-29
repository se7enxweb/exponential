---
title: Creating an operator, step by step
audience: developer, app-creator
category: development
order: 520
summary: The whole path from an idea to a working template operator in an extension - choose a free name, write the class, register it, clear caches, try it interpreted and compiled, deploy - or let the template extension wizard write it; each step checked against a development installation.
keywords: create operator, new template operator, extension, eztemplateautoload.php, ExtensionAutoloadPath, operator class, template extension wizard, setup/templateoperator, RAD, test, compiled
type: help
---
# Creating an operator, step by step

This page walks through adding a template operator to an extension, in the order you do it, with the checks that
catch the usual mistakes. The code of a complete small operator class is in the cookbook,
[A custom template operator](../../examples/cookbook/custom-template-operator.md); every method and argument is
explained in [The operator class interface](operator-class-reference.md). Here the focus is the process.

## Contents

1. [Decide that it should be an operator](#1-decide-that-it-should-be-an-operator)
2. [Choose a free name](#2-choose-a-free-name)
3. [Let the wizard write the files, or write them yourself](#3-let-the-wizard-write-the-files-or-write-them-yourself)
4. [Write the class](#4-write-the-class)
5. [Register it](#5-register-it)
6. [Make it known](#6-make-it-known)
7. [Try it, interpreted and compiled](#7-try-it-interpreted-and-compiled)
8. [Ship it](#8-ship-it)

## 1. Decide that it should be an operator

An operator takes a value and gives one back: `{$value|my_operator( 3 )}`. Use something else when:

| You want to | Use |
|---|---|
| read data (content, users, your own tables) | a [fetch function](fetch-functions-how-they-work.md) or a [fetch alias](fetch-aliases.md) |
| write output with a body, or control what is rendered | a [template function](../template-language/custom-template-functions.md) |
| call one PHP function with one argument | `template.ini` `[PHP] PHPOperatorList[]` ([PHP function operators](php-function-operators.md)) |
| combine existing operators | a template include, or a `set-block` |

An operator must not change data: it runs while templates are compiled and rendered, its results are cached with
the page, and it runs for every visitor who can see the template.

## 2. Choose a free name

Operator names are global per siteaccess, and a name another class also registers leads to the trouble described
in [Registration order and overrides](registration-order-and-overrides.md). Use your extension's prefix
(`myext_initials`, or `exp_` in Exponential's own code) and check that it is free on every siteaccess that will use
it: look it up in the [operator index](operator-index.md), and render `{'x'|myext_initials}` before you register
anything. An unregistered name leaves the tag empty and logs `Operator 'myext_initials' is not registered`; if you
get any other result, the name is taken.

## 3. Let the wizard write the files, or write them yourself

The admin's template extension wizard (Setup, RAD, "Template operator"; module view `setup/templateoperator`,
code in `kernel/setup/exptemplateextensionwizard.php`) writes a working extension with any mix of operators,
template functions, fetch functions and fetch aliases: the operator class with its parameter list, compile-time
hints and a `modify()` that dispatches on the operator name, `autoloads/eztemplateautoload.php`,
`settings/site.ini.append.php`, `ezinfo.php`, `extension.xml` and `composer.json`. See
[RAD tools](../../features/6.0/rad-tools.md). The rest of this page is what those files contain and why, for when
you write them by hand or review what the wizard wrote.

## 4. Write the class

Put the class in your extension, for example `extension/myextension/autoloads/myextensionoperators.php`. The
minimum is `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` and `modify()`. A class with
two operators, `exp_initials` and `exp_reading_time( words_per_minute )`, works like this on a development installation:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $text = 'one two three four five six seven eight nine ten eleven twelve'}
{$node.name|wash} -> {$node.name|exp_initials}
{$text|exp_reading_time} minute(s) at 200 words a minute, {$text|exp_reading_time( 5 )} at 5
{exp_initials( 'Bold Agency' )}
```

Output:

```text
Fit &amp; Healthy -> FH
1 minute(s) at 200 words a minute, 3 at 5
```

(Not run as an installed extension: the class was registered on the engine in memory by the script that rendered
the example, from a scratch folder; see [The operator class interface](operator-class-reference.md#the-sample-class).)

Checks while writing:

- `php -l` the file.
- Assign the result to `$operatorValue`; return text unescaped (the template washes it: note `{$node.name|wash}`
  above prints `&amp;`, while `exp_initials` works on the raw name).
- Give every optional parameter a `default`, and check required parameters in `modify()` with a
  `$tpl->warning( $operatorName, ..., $placement )`.
- Leave `operatorTemplateHints()` out for a first version. Add hints later only with a test of the compiled result
  ([why](operator-class-reference.md#operatortemplatehints)).

## 5. Register it

`extension/myextension/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionoperators.php',
                                    'class' => 'myExtensionOperators',
                                    'operator_names' => array( 'exp_initials', 'exp_reading_time' ) );
```

`extension/myextension/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

and the extension in `[ExtensionSettings] ActiveExtensions[]` (or a siteaccess's `ActiveAccessExtensions[]`). The
details, including registering through `template.ini` and access extensions, are in
[Registering operators](registering-operators.md).

## 6. Make it known

The INI cache holds the list of autoload folders, and compiled templates hold the result of compiling against the
old operator list:

```bash
php bin/php/ezpgenerateautoloads.php -e        # only if other classes of the extension changed
php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user
```

On a development installation, where Velocity keeps the template engine's registrations from its warm-up, use the one deploy command,
which clears these caches, reloads PHP-FPM and restarts Velocity:

```bash
./console exp:velocity deploy --allow-root-user
```

## 7. Try it, interpreted and compiled

A development installation compiles templates (`[TemplateSettings] TemplateCompile=enabled`), and the compiled template is what visitors
get. Try the operator both ways:

- in a development installation, switch `TemplateCompile` off in a siteaccess override, reload the page, switch it
  on again, clear the template cache, reload: both results must be the same;
- use each parameter form: piped and called (`{exp_initials( 'Bold Agency' )}`), with and without optional
  parameters, with a missing required parameter (expect a warning, not a PHP error);
- look at the debug output for `Operator '...' is not registered`, `Object problem with operator '...'` (the class
  was not found or has no `modify()`) and `Parameter '...' missing`.

A required parameter left out, on a development installation:

```tpl
{150|exp_clamp( 0, 100 )} {-5|exp_clamp( 0, 100 )} {42|exp_clamp( 0, 100 )}
{42|exp_clamp( 0 )}
```

Output:

```text
100 0 42
42
```

Logged by the template engine:

```text
WARNING eZTemplateOperatorElement: Parameter 'max' (1) missing
WARNING exp_clamp: min and max must be numbers
```

## 8. Ship it

- Keep the operator's class small and free of side effects; put shared logic in a class of its own that a unit
  test can call without a template.
- Document each operator: name, input, parameters, return value, an example. The family pages in this section
  are the model.
- List the operators in the extension's README with the prefix you chose, so others can avoid the names.
- On an upgrade of Exponential, re-check that no new kernel operator took one of your names
  ([Registration order and overrides](registration-order-and-overrides.md#rules)).

## Related pages

- [The operator class interface](operator-class-reference.md)
- [Registering operators](registering-operators.md)
- [A custom template operator](../../examples/cookbook/custom-template-operator.md) (cookbook)
- [Your own operators with OW Simple Operator](owsimpleoperator-your-own-operators.md): the shortcut and its limits
- [Overriding kernel operators](overriding-kernel-operators.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
