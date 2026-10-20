---
title: The swark operators
audience: developer, designer, app-creator
category: development
order: 430
summary: How the swark extension registers its 37 one-class-per-operator template operators, the SwarkOperator base class with name=default parameter strings and compiled execute() calls, which of its names other extensions or the kernel take over on alpha, and a proven case where the owner of an operator changes in the middle of a request.
keywords: swark, SwarkOperator, SwarkOperators, operator collision, strpos, substr, ltrim, rtrim, str_replace, json_encode, registration order, ExtensionAutoloadPath
type: help
---
# The swark operators

The extension `swark` (`extension/swark/`, GPL 2.0, written in 2008 for the product this kernel descends from) adds
small utility operators, one class per operator in `extension/swark/autoloads/classes/`. This page explains how they
are built and registered; the two pages after it document each operator:

- [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md)
- [Swark operators: request and system](swark-operators-request-and-system.md)

## Contents

1. [Registration](#registration)
2. [The base class](#the-base-class)
3. [Names that collide on alpha](#names-that-collide-on-alpha)
4. [Ownership can change during a request](#ownership-can-change-during-a-request)

## Registration

`extension/swark/settings/site.ini.append.php` adds `swark` to `[TemplateSettings] ExtensionAutoloadPath[]`. The
autoload file `extension/swark/autoloads/eztemplateautoload.php` contains one line:

```php
$eZTemplateOperatorArray = SwarkOperators::operators();
```

`SwarkOperators::operators()` (`extension/swark/autoloads/SwarkOperators.php`) maps 37 operator names to classes and
returns one definition per name (`'class' => ..., 'operator_names' => array( <name> )`). Its `template.ini.append.php`
also lists `serialize` and `unserialize` in `[PHP] PHPOperatorList[]`, which the kernel's `template.ini` already does.

## The base class

Every operator extends `SwarkOperator` (`autoloads/classes/SwarkOperator.php`). Its constructor takes the operator name
and parameter strings: `'name'` is a required parameter, `'name=value'` an optional one whose default is the **string**
after `=` (so `not_found_key=-1` defaults to the string `-1`). All parameters have the type `mixed`. `modify()` calls
the class's static `execute( $operatorValue, $namedParameters )` and uses its return value.

`operatorTemplateHints()` marks every swark operator for element transformation: the compiler writes a direct call to
`<Class>::execute()` into the compiled template, filling missing parameters with their defaults. Only
`variable_names` switches that off (its `transformation()` returns `false`), because it needs the template object.

## Names that collide on alpha

Seven swark names are also registered elsewhere. On alpha `swark` comes before `owsimpleoperator` and `ezjscore` in
`ExtensionAutoloadPath[]`, and a later definition of a name replaces an earlier one:

| Name | swark behavior | Registered last by | Result on alpha |
|---|---|---|---|
| `json_encode` | `$value|json_encode` or `json_encode( $value )` | ezjscore (`json_encode( $hash )`) | ezjscore's operator |
| `str_replace` | `$text|str_replace( search, replace )` | owsimpleoperator (PHP `str_replace`) | owsimpleoperator's operator |
| `strpos`, `strrpos` | returns -1 when not found | kernel `eZTemplateStringsOperator` (returns `false`) | depends on first use, see below |
| `substr` | `length` 0 means "to the end" | kernel `eZTemplateStringsOperator` | depends on first use |
| `ltrim`, `rtrim` | optional `charlist` | kernel `eZTemplateStringsOperator` | depends on first use |

The swark classes for these names are documented with the others; on alpha a template cannot rely on reaching them.

## Ownership can change during a request

The kernel's `eZTemplateStringsOperator` is registered from `lib/eztemplate/classes/eztemplateautoload.php` before the
extensions, so swark's definitions of `strpos`, `strrpos`, `substr`, `ltrim` and `rtrim` replace it in the operator
table. But when any operator of the kernel class runs for the first time, the engine creates that object and
registers it for **every** name in its `operatorList()` (`eZTemplate::registerOperatorsInternal()`), taking these five
names back for the rest of the request. The result of `strpos` therefore depends on whether an operator such as
`strlen` ran earlier in the request, in any template:

```tpl
1 strpos before:  [{'abc'|strpos( 'z' )}] {'abc'|strpos( 'z' )|get_type}
2 strlen:         {'abc'|strlen}
3 strpos after:   [{'abc'|strpos( 'z' )}] {'abc'|strpos( 'z' )|get_type}
```

Output on alpha:

```text
1 strpos before:  [-1] integer
2 strlen:         3
3 strpos after:   [] boolean[false]
```

The compiled template gives the same result. Avoid relying on either variant of these names: test with `|gt( -1 )`
and `|ne( false() )`, or use `contains`/`begins_with` of the template library. The general rules are in
[Registration order and overrides](registration-order-and-overrides.md).

## Related pages

- [Registration order and overrides](registration-order-and-overrides.md)
- [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md)
- [Swark operators: request and system](swark-operators-request-and-system.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
