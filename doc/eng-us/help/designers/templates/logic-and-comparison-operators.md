---
title: Logic and comparison operators
audience: designer, developer
category: templates
order: 200
summary: The operators that build conditions: eq, ne, lt, gt, le, ge, and, or, not, true, false, null, choose, and the type tests is_set, is_unset, is_array, is_string, is_numeric, is_object, is_null, get_type, get_class.
keywords: eq, ne, lt, gt, le, ge, and, or, not, true, false, null, choose, is_set, is_unset, is_array, is_string, is_numeric, is_object, is_null, get_type, get_class, comparison, logic
type: help
---
# Logic and comparison operators

This page is a reference for conditions in templates. Logic operators come from `eZTemplateLogicOperator`, type tests
from `eZTemplateTypeOperator` (`lib/eztemplate/classes/`).

## Contents

1. [Comparison](#comparison)
2. [Logic](#logic)
3. [Constants](#constants)
4. [Type tests](#type-tests)
5. [choose](#choose)

## Comparison

| Operator | Means |
|---|---|
| `eq( $a, $b )` | equal (also `$a\|eq( $b )`) |
| `ne( $a, $b )` | not equal |
| `lt`, `le` | less than, less or equal |
| `gt`, `ge` | greater than, greater or equal |

`eq` with more than two values is true when all are equal; matching is loose (`0` equals `false()`).

> **Note**
> `lt`, `le`, `gt` and `ge` compare **counts**, as the class's comment defines them: a number is its value, an array its
> number of items, an object its number of attributes, a boolean 0 or 1, and any other string its **length**. So
> `$children|gt( 3 )` means "more than three children", and `'10'|gt( 9 )` compares numbers, but a non-numeric string
> is compared by length. Compare dates as timestamps (numbers).

## Logic

```tpl
{if and( $node.can_edit, $current_user.is_logged_in )} ... {/if}
{if or( $a, $b, $c )} ... {/if}
{if $node.is_hidden|not} ... {/if}
```

`and` returns the last true value (or false), `or` the first true value, so `or( $title, $name )` also works as
"the first non-empty" (but [first_set](cond-and-first-set.md) tests "set" rather than "true").

## Constants

`true()` and `false()` create booleans. `null` is a test: `$value|null` is true when the value is null.

## Type tests

| Operator | True when the value |
|---|---|
| `is_set( $x )` / `is_unset( $x )` | is defined / is not |
| `is_array`, `is_string`, `is_integer`, `is_float`, `is_numeric`, `is_boolean` | has that type |
| `is_object`, `is_class( 'eZContentObjectTreeNode' )` | is an object, of that class |
| `is_null` | is null |

`get_type` and `get_class` return the type and the class name, useful while debugging.

## choose

`$index|choose( 'zero', 'one', 'two' )` returns the parameter at the position of the input: a compact way to map a
small number to a text. For true/false, `cond()` is clearer.

## Related pages

- [If and conditions](if-and-conditions.md)
- [cond and first_set](cond-and-first-set.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
