---
title: Type operators
audience: developer, designer, app-creator
category: development
order: 140
summary: The 13 operators of eZTemplateTypeOperator (is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null, is_class, is_set, is_unset, get_type, get_class) with their input rules and examples rendered on a development installation.
keywords: type, is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null, is_class, is_set, is_unset, get_type, get_class, eZTemplateTypeOperator
type: help
---
# Type operators

These operators test what kind of value a variable holds, and say it. They are registered by `eZTemplateTypeOperator`
(`lib/eztemplate/classes/eztemplatetypeoperator.php`) through `lib/eztemplate/classes/eztemplateautoload.php`. None
has named parameters.

## Contents

1. [The PHP type tests](#the-php-type-tests)
2. [is_class](#is_class)
3. [is_set and is_unset](#is_set-and-is_unset)
4. [get_type and get_class](#get_type-and-get_class)
5. [Related pages](#related-pages)

## The PHP type tests

Eight operators map one to one to a PHP function (`$PHPNameMap` in the class):

| Operator | PHP function | Generated reference |
|---|---|---|
| `is_array` | `is_array()` | [is_array](../../examples/template-operators/is_array.md) |
| `is_boolean` | `is_bool()` | [is_boolean](../../examples/template-operators/is_boolean.md) |
| `is_integer` | `is_integer()` | [is_integer](../../examples/template-operators/is_integer.md) |
| `is_float` | `is_float()` | [is_float](../../examples/template-operators/is_float.md) |
| `is_numeric` | `is_numeric()` | [is_numeric](../../examples/template-operators/is_numeric.md) |
| `is_string` | `is_string()` | [is_string](../../examples/template-operators/is_string.md) |
| `is_object` | `is_object()` | [is_object](../../examples/template-operators/is_object.md) |
| `is_null` | `is_null()` | [is_null](../../examples/template-operators/is_null.md) |

### is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null

Without parameters they test the piped input. In the interpreter, with parameters they test every parameter and
return true only when all pass (`checkType()`); the input is then ignored. They return a boolean.

The compiled form (`isTransform()`) tests only the first value. Compiled on a development installation, `is_integer( 1, 2, 'three' )` was
true, where the interpreter says false. Test one value per call: `and( $a|is_integer, $b|is_integer )`.


| Parameter | Type | Required | Default |
|---|---|---|---|
| values to test | mixed | no | the input |

A string that holds digits is a string: `'42'|is_integer` is false, `'42'|is_numeric` is true. Attribute values
read from content are often strings for that reason.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
array( 1 )|is_array = {array( 1 )|is_array|choose( 'false', 'true' )}
true()|is_boolean = {true()|is_boolean|choose( 'false', 'true' )}
42|is_integer = {42|is_integer|choose( 'false', 'true' )}
'42'|is_integer = {'42'|is_integer|choose( 'false', 'true' )}
'42'|is_numeric = {'42'|is_numeric|choose( 'false', 'true' )}
1.5|is_float = {1.5|is_float|choose( 'false', 'true' )}
'text'|is_string = {'text'|is_string|choose( 'false', 'true' )}
$node|is_object = {$node|is_object|choose( 'false', 'true' )}
$node.parent_node_id|is_null = {$node.parent_node_id|is_null|choose( 'false', 'true' )}
is_integer( 1, 2, 'three' ) = {is_integer( 1, 2, 'three' )|choose( 'false', 'true' )}
```

Output:

```text
array( 1 )|is_array = true
true()|is_boolean = true
42|is_integer = true
'42'|is_integer = false
'42'|is_numeric = true
1.5|is_float = true
'text'|is_string = true
$node|is_object = true
$node.parent_node_id|is_null = false
is_integer( 1, 2, 'three' ) = false
```

The same template compiled on a development installation; the last line differs:

Output with the template compiler (compiled into a scratch folder):

```text
array( 1 )|is_array = true
true()|is_boolean = true
42|is_integer = true
'42'|is_integer = false
'42'|is_numeric = true
1.5|is_float = true
'text'|is_string = true
$node|is_object = true
$node.parent_node_id|is_null = false
is_integer( 1, 2, 'three' ) = true
```

## is_class

### is_class

Whether a value is an object of a given class. The class name is compared case-insensitively. With one parameter
(the class name) it tests the piped input; with two, it tests the second parameter. A value that is not an object is
of no class, so the answer is false (the code checks `is_object()` before `get_class()`, which would throw a
`TypeError` on PHP 8).

| Parameter | Type | Required | Default |
|---|---|---|---|
| class name | string | yes | none |
| value | mixed | no | the input |

Returns a boolean. Generated reference: [is_class](../../examples/template-operators/is_class.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
$node|is_class( 'eZContentObjectTreeNode' ) = {$node|is_class( 'eZContentObjectTreeNode' )|choose( 'false', 'true' )}
is_class( 'ezcontentobject', $node.object ) = {is_class( 'ezcontentobject', $node.object )|choose( 'false', 'true' )}
'text'|is_class( 'eZContentObject' ) = {'text'|is_class( 'eZContentObject' )|choose( 'false', 'true' )}
```

Output:

```text
$node|is_class( 'eZContentObjectTreeNode' ) = true
is_class( 'ezcontentobject', $node.object ) = true
'text'|is_class( 'eZContentObject' ) = false
```

Use the piped form. The compiled code treats the first value as the object and the second as the class name, so
compiled on a development installation the call form `is_class( 'ezcontentobject', $node.object )` was false.

Output with the template compiler (compiled into a scratch folder):

```text
$node|is_class( 'eZContentObjectTreeNode' ) = true
is_class( 'ezcontentobject', $node.object ) = false
'text'|is_class( 'eZContentObject' ) = false
```

## is_set and is_unset

### is_set

Whether the parameter is set: not `null`, and existing. The parameter is read with the existence check of
`elementValue()`, so an undefined variable or a missing hash key gives false without the "Unknown template variable"
error. It does not use the piped input; without a parameter it reports a missing parameter. A second parameter
logs an "extra parameters" warning.

| Parameter | Type | Required | Default |
|---|---|---|---|
| value | mixed | yes | none |

Returns a boolean. Generated reference: [is_set](../../examples/template-operators/is_set.md).

### is_unset

The opposite of `is_set`, with the same rules.

| Parameter | Type | Required | Default |
|---|---|---|---|
| value | mixed | yes | none |

Returns a boolean. Generated reference: [is_unset](../../examples/template-operators/is_unset.md).

```tpl
{def $settings = hash( 'title', 'Hello' )}
is_set( $settings.title ) = {is_set( $settings.title )|choose( 'false', 'true' )}
is_set( $settings.subtitle ) = {is_set( $settings.subtitle )|choose( 'false', 'true' )}
is_set( $not_defined ) = {is_set( $not_defined )|choose( 'false', 'true' )}
is_unset( $not_defined ) = {is_unset( $not_defined )|choose( 'false', 'true' )}
```

Output:

```text
is_set( $settings.title ) = true
is_set( $settings.subtitle ) = false
is_set( $not_defined ) = false
is_unset( $not_defined ) = true
```

## get_type and get_class

### get_type

Describes the first parameter, or the piped input: `null`, `boolean[true]` or `boolean[false]`,
`object[<class>]`, `array[<count>]`, `string[<length in bytes>]`, and otherwise PHP's `gettype()` (`integer`,
`double`). Useful while debugging a template.

| Parameter | Type | Required | Default |
|---|---|---|---|
| value | mixed | no | the input |

Returns a string. Generated reference: [get_type](../../examples/template-operators/get_type.md).

### get_class

The class name of an object, **in lower case**; an empty string for a value that is not an object.

| Parameter | Type | Required | Default |
|---|---|---|---|
| value | mixed | no | the input |

Returns a string. Generated reference: [get_class](../../examples/template-operators/get_class.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $missing = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
{foreach array( 42, 1.5, 'Fit', true(), array( 1, 2 ), $node, $node.data_map.title, $missing ) as $v}{$v|get_type} {/foreach}

$node|get_class = {$node|get_class}
get_class( $node.object ) = {get_class( $node.object )}
'text'|get_class = [{'text'|get_class}]
```

Output:

```text
integer double string[3] boolean[true] array[2] object[eZContentObjectTreeNode] object[eZContentObjectAttribute] null 
$node|get_class = ezcontentobjecttreenode
get_class( $node.object ) = ezcontentobject
'text'|get_class = []
```

## Related pages

- [Logic operators](logic-operators.md): `null` and `not`
- [Control operators](control-operators.md): `first_set` for fallbacks
- [Attribute and dump operators](attribute-and-dump-operators.md): looking inside objects
- [Template debugging](../designers/templates/template-debugging.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
