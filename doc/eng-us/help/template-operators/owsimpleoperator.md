---
title: OW Simple Operator - PHP functions as template operators
audience: developer, designer, app-creator
category: development
order: 900
summary: What the owsimpleoperator extension does, how its operators are registered from owsimpleoperator.ini, the five PHP functions it offers on this installation (time, mktime, getdate, str_replace, str_rot13), and how to call them, with every example rendered on alpha.
keywords: owsimpleoperator, OW Simple Operator, OWSimpleOperator, PHPFunctionOperator, PermittedFunctionList, time, mktime, getdate, str_replace, str_rot13, php function operator
type: help
---
# OW Simple Operator: PHP functions as template operators

OW Simple Operator (`extension/owsimpleoperator`, version 1.2.7 in `ezinfo.php`, GPL v3 or later) is a small
extension for writing template operators with less code. It was made by Open Wide; the copyright is held by Open Wide and Brookins Consulting
(see the extension's `README.md` and `doc/COPYRIGHT.md`). It does two things:

1. it turns PHP functions that are listed in a setting into template operators, and
2. it gives you a base class, `OWSimpleOperator`, where every public method of your class becomes an operator,
   without `operatorList()`, `namedParameterList()` or `modify()` of your own.

This page covers the first part and how the extension is wired in. The second part is in
[Your own operators with OW Simple Operator](owsimpleoperator-your-own-operators.md); the traps of both are in
[OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md).

## Contents

1. [How it is wired in](#how-it-is-wired-in)
2. [The operators it offers here](#the-operators-it-offers-here)
3. [Calling a PHP function operator](#calling-a-php-function-operator)
4. [Allowing another PHP function](#allowing-another-php-function)
5. [Debug output](#debug-output)

## How it is wired in

The extension registers its operators like any other extension (see
[Registering operators](registering-operators.md)):

- `settings/site.ini.append.php` adds `ExtensionAutoloadPath[]=owsimpleoperator` to `[TemplateSettings]`, so the
  template engine reads `extension/owsimpleoperator/autoloads/eztemplateautoload.php`.
- That file does not hard-code operator names. It reads `owsimpleoperator.ini`:
  - `[PHPFunctions] PermittedFunctionList[]` becomes the operator names of the class `PHPFunctionOperator`
    (an empty subclass of `OWSimpleOperator`);
  - every line of `[ClassOperators] PermittedClassOperatorList[]`, written `file;Class;method`, adds the
    operator `method` served by `Class` from `file`.
- `OWSimpleOperator::modify()` then calls the method of the same name on the operator class if there is one, and
  otherwise the PHP function of that name.

The shipped `settings/owsimpleoperator.ini.append.php`:

```ini
[OWSimpleOperatorSettings]
DebugOutput=disabled

[PHPFunctions]
PermittedFunctionList[]
PermittedFunctionList[]=time
PermittedFunctionList[]=mktime
PermittedFunctionList[]=getdate
PermittedFunctionList[]=str_replace
PermittedFunctionList[]=str_rot13

[ClassOperators]
PermittedClassOperatorList[]
#PermittedClassOperatorList[]=extension/owsimpleoperator/autoloads/exampleoperator.php;ExampleOperator;example_sum
```

On alpha the extension is active on the `site` and `admin` siteaccesses, and these five names are its operators.

## The operators it offers here

### time, mktime, getdate, str_replace and str_rot13

| Operator | PHP function | Arguments, in PHP order | Returns |
|---|---|---|---|
| `time` | `time()` | none | the current Unix timestamp (integer) |
| `mktime` | `mktime()` | hour, minute, second, month, day, year (each optional) | a Unix timestamp in the server's time zone |
| `getdate` | `getdate()` | timestamp (optional, default now) | a hash: `seconds`, `minutes`, `hours`, `mday`, `wday`, `mon`, `year`, `yday`, `weekday`, `month`, `0` |
| `str_replace` | `str_replace()` | search, replace, subject | the subject with every search string replaced |
| `str_rot13` | `str_rot13()` | string | the string with ROT13 applied |

Each takes up to ten arguments (`$max_operator_parameter = 10` in `OWSimpleOperator`); arguments you do not give
are left out of the PHP call, so the PHP function's own defaults apply.

`str_replace` has the same name as an operator of the extension swark. The owsimpleoperator registration is read
later, so it wins on alpha (see [Registration order and overrides](registration-order-and-overrides.md)).
The kernel's own date operators (`currentdate`, `maketime`, `makedate`, `datetime`, `l10n`) cover most date needs
without this extension: see [Locale operators](locale-operators.md).

## Calling a PHP function operator

Use the call form and give every argument in the order of the PHP function:

```tpl
{* call form: every argument in the parentheses *}
{str_replace( 'great', 'very good', 'This is a great example!' )}
{str_rot13( 'Exponential' )}
{'Exponential'|str_rot13}
```

Output on alpha:

```text

This is a very good example!
Rkcbaragvny
Rkcbaragvny
```

`str_rot13` takes a single argument, so the pipe form works for it as well (`{'Exponential'|str_rot13}`).

The date functions:

```tpl
{def $now = time()
     $parts = getdate( mktime( 12, 30, 0, 7, 4, 2026 ) )}
{if $now|gt( 1700000000 )}time() returned a Unix timestamp: an integer{/if}

mktime( 0, 0, 0, 1, 1, 2026 ) = {mktime( 0, 0, 0, 1, 1, 2026 )}
{$parts.weekday} {$parts.mday} {$parts.month} {$parts.year}, {$parts.hours}:{$parts.minutes}
```

Output on alpha:

```text
time() returned a Unix timestamp: an integer
mktime( 0, 0, 0, 1, 1, 2026 ) = 1767254400
Saturday 4 July 2026, 12:30
```

`mktime()` uses the time zone of the PHP process: on alpha that is Pacific time, so midnight on 1 January 2026
is `1767254400` and not the UTC value. Format timestamps for people with the kernel's `l10n` or `datetime`
operators, which follow the siteaccess locale.

With more than one argument, do not pipe the first argument in. `OWSimpleOperator::modify()` puts the piped value
in front of the argument list and removes the **last** argument you gave, so the PHP function gets one argument
too few:

```tpl
{* pipe form: the piped value becomes the FIRST argument and the LAST given argument is dropped *}
{'This is a great example!'|str_replace( 'great', 'very good' )}
```

Output on alpha:

```text
(no output)
```

Logged by the template engine:

```text
EXCEPTION ArgumentCountError: str_replace() expects at least 3 arguments, 2 given
```

The template engine does not catch PHP's `ArgumentCountError`: rendering stops at that tag. Details in
[OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md#the-pipe-form-drops-an-argument).

A PHP function that is not listed in `PermittedFunctionList[]` is not an operator. The engine leaves the tag
empty and logs a warning:

```tpl
{* a function that is not in PermittedFunctionList[] is no operator at all *}
{strrev( 'abc' )}
```

Output on alpha:

```text
(no output)
```

Logged by the template engine:

```text
WARNING : Operator 'strrev' is not registered
```

## Allowing another PHP function

Add it to `PermittedFunctionList[]` in an `owsimpleoperator.ini.append.php` of your own extension or siteaccess
(not in the extension's file, which an update would replace), then clear the INI and template caches:

```ini
[PHPFunctions]
PermittedFunctionList[]=ucwords
```

The comment in the shipped settings file states the rule: every template can call a listed function with any
argument, so list only functions without side effects that read nothing outside their arguments.
`file_get_contents`, for example, would let any template read any file the web server can read. The extension's
test `tests/owSimpleOperatorDefaultSettingsTest.php` refuses a list of such functions (file, process, `eval`-like and
configuration functions) in the shipped settings and checks that every listed function exists in the running PHP.

Before you allow a function, check whether the template library already has it: the operators of
`eZTemplateStringsOperator` wrap sixty PHP string functions with checked parameters
([PHP string functions](php-string-functions-transform.md)), and `settings/template.ini` `[PHP] PHPOperatorList[]` maps
single-argument PHP functions to operators ([PHP function operators](php-function-operators.md)).

## Debug output

`[OWSimpleOperatorSettings] DebugOutput=enabled` writes every call with its arguments and its result to the debug
output (`eZDebug::writeDebug()`), for example `PHP Function: str_rot13("Exponential") => "Rkcbaragvny"`. Keep it
disabled outside development.

## Related pages

- [Your own operators with OW Simple Operator](owsimpleoperator-your-own-operators.md)
- [OW Simple Operator: limitations and risks](owsimpleoperator-limitations.md)
- [Creating an operator, step by step](creating-an-operator-step-by-step.md): the full operator class without the base class
- [Registration order and overrides](registration-order-and-overrides.md)
- [Template operators](README.md): all operator families
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
