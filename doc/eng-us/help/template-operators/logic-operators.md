---
title: Logic operators
audience: developer, designer, app-creator
category: development
order: 120
summary: The 13 operators of eZTemplateLogicOperator (eq, ne, lt, gt, le, ge, and, or, not, null, true, false, choose), including the count rule of lt/gt/le/ge that compares strings by length, with examples rendered on a development installation.
keywords: logic, comparison, eq, ne, lt, gt, le, ge, and, or, not, null, true, false, choose, boolean, condition, eZTemplateLogicOperator
type: help
---
# Logic operators

Conditions in `{if}`, `{elseif}` and `{while}` are operator calls, because the template language has no `==`, `<`,
`&&` or `!`. The 13 operators on this page are registered by `eZTemplateLogicOperator`
(`lib/eztemplate/classes/eztemplatelogicoperator.php`) through `lib/eztemplate/classes/eztemplateautoload.php`.

Two of their rules surprise people who know PHP or Smarty, and both are proven below:

- `lt`, `gt`, `le` and `ge` compare **counts**, not values: a non-numeric string counts as its length, so
  `'pear'|lt( 'apple' )` is true (4 is less than 5).
- `and` and `or` return one of their operands, not always a boolean.

## Contents

1. [eq and ne](#eq-and-ne)
2. [lt, gt, le and ge](#lt-gt-le-and-ge)
3. [and and or](#and-and-or)
4. [not, null, true and false](#not-null-true-and-false)
5. [choose](#choose)
6. [Related pages](#related-pages)

In the examples, `choose( 'false', 'true' )` turns a boolean into a word, because a boolean `true` prints as `1` and
`false` prints nothing.

## eq and ne

### eq

With one parameter: whether the piped input equals it. With two or more parameters: whether all parameters are equal
(the input is not used). The comparison is PHP's loose `==`, so `'5'|eq( 5 )` is true. Without a parameter it logs
"Requires one parameter for input checking or two or more for parameter checking" and returns false.

| Parameter | Type | Required | Default |
|---|---|---|---|
| values | mixed | yes, one (with input) or two or more | none |

Returns a boolean. Generated reference: [eq](../../examples/template-operators/eq.md).

### ne

The opposite: with one parameter, whether the input differs from it; with several, whether any two neighbors differ.
Same warning without a parameter.

| Parameter | Type | Required | Default |
|---|---|---|---|
| values | mixed | yes | none |

Returns a boolean. Generated reference: [ne](../../examples/template-operators/ne.md).

```tpl
5|eq( 5 ) = {5|eq( 5 )|choose( 'false', 'true' )}
'5'|eq( 5 ) = {'5'|eq( 5 )|choose( 'false', 'true' )}
eq( 3, 3, 3 ) = {eq( 3, 3, 3 )|choose( 'false', 'true' )}
eq( 3, 3, 4 ) = {eq( 3, 3, 4 )|choose( 'false', 'true' )}
'abc'|ne( 'abd' ) = {'abc'|ne( 'abd' )|choose( 'false', 'true' )}
ne( 1, 1, 2 ) = {ne( 1, 1, 2 )|choose( 'false', 'true' )}
```

Output:

```text
5|eq( 5 ) = true
'5'|eq( 5 ) = true
eq( 3, 3, 3 ) = true
eq( 3, 3, 4 ) = false
'abc'|ne( 'abd' ) = true
ne( 1, 1, 2 ) = true
```

## lt, gt, le and ge

### lt, gt, le, ge

`lt` (less than), `gt` (greater than), `le` (less or equal) and `ge` (greater or equal) compare two values: the piped
input with the named parameter `threshold`, or, without an input, the first parameter with the second.

Each side is first turned into a count by `eZTemplateLogicOperator::comparisonCount()`:

| Value | Counts as |
|---|---|
| `null` | 0 |
| boolean | 0 or 1 |
| array | its number of elements |
| object with `attributes()` | its number of attributes |
| number or numeric string | its value |
| any other string | its length (`strlen`) |

Any other type logs "Unsupported input type" and leaves the value unchanged. Because of the string rule, use
`strcmp` or `strnatcmp` (see [PHP string functions: search and compare](php-string-functions-search-and-compare.md)) to
order words. Compiled templates call `compareCounts()` at run time and apply the same rule.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `threshold` | mixed | yes | `false` |

Returns a boolean. Generated references: [lt](../../examples/template-operators/lt.md),
[gt](../../examples/template-operators/gt.md), [le](../../examples/template-operators/le.md),
[ge](../../examples/template-operators/ge.md).

```tpl
3|lt( 5 ) = {3|lt( 5 )|choose( 'false', 'true' )}
gt( 10, 2 ) = {gt( 10, 2 )|choose( 'false', 'true' )}
5|le( 5 ) = {5|le( 5 )|choose( 'false', 'true' )}
5|ge( 6 ) = {5|ge( 6 )|choose( 'false', 'true' )}
array( 1, 2, 3 )|gt( 2 ) = {array( 1, 2, 3 )|gt( 2 )|choose( 'false', 'true' )}
'banana'|gt( 'apple' ) = {'banana'|gt( 'apple' )|choose( 'false', 'true' )}
'pear'|lt( 'apple' ) = {'pear'|lt( 'apple' )|choose( 'false', 'true' )}
'10'|gt( '9' ) = {'10'|gt( '9' )|choose( 'false', 'true' )}
```

Output:

```text
3|lt( 5 ) = true
gt( 10, 2 ) = true
5|le( 5 ) = true
5|ge( 6 ) = false
array( 1, 2, 3 )|gt( 2 ) = true
'banana'|gt( 'apple' ) = true
'pear'|lt( 'apple' ) = true
'10'|gt( '9' ) = true
```

## and and or

Both evaluate only their **parameters**; the piped input is not used. Each operand is judged like this: `null` is
false, a boolean is itself, an array is true when it has elements, a number is true when it is not 0, an object is
true when it has `attributes()` and `attribute()` methods, and a string is true when it has something other than
whitespace (`and` also treats `''` as false).

### and

Returns `false` at the first false operand; when all are true it returns the **last operand** itself.

| Parameter | Type | Required | Default |
|---|---|---|---|
| operands | mixed | no | none |

Generated reference: [and](../../examples/template-operators/and.md).

### or

Returns the **first true operand** itself, or `false` when none is true. That makes `or` a fallback picker, although
[first_set](control-operators.md#first_set) is clearer for that.

| Parameter | Type | Required | Default |
|---|---|---|---|
| operands | mixed | no | none |

Generated reference: [or](../../examples/template-operators/or.md).

```tpl
and( 1, 'yes', array( 1 ) ) = {and( 1, 'yes', array( 1 ) )|implode( ',' )}
and( 1, '  ', 3 ) = {and( 1, '  ', 3 )|choose( 'false', 'true' )}
or( false(), '', 'first', 'second' ) = {or( false(), '', 'first', 'second' )}
or( 0, array() ) = {or( 0, array() )|choose( 'false', 'true' )}
```

Output:

```text
and( 1, 'yes', array( 1 ) ) = 1
and( 1, '  ', 3 ) = false
or( false(), '', 'first', 'second' ) = first
or( 0, array() ) = false
```

The whitespace rule is the interpreter's. Compiled on a development installation, `and( 1, '  ', 3 )` treated the blank string as true and
returned 3, the last operand, which made the `choose` after it log "Index 3 out of range" and print nothing. Trim
strings before testing them (`$text|trim`) and the two agree.

Output with the template compiler (compiled into a scratch folder):

```text
and( 1, 'yes', array( 1 ) ) = 1
and( 1, '  ', 3 ) = 
or( false(), '', 'first', 'second' ) = first
or( 0, array() ) = false
```

## not, null, true and false

### not

Negates the piped input, or the first parameter when there is no input. "Empty" means: `null`, an empty array, an
object without attributes, a number or numeric string equal to 0 (so `'0'` too), or an empty string. Returns a boolean.

| Parameter | Type | Required | Default |
|---|---|---|---|
| value | mixed | no | the input |

Generated reference: [not](../../examples/template-operators/not.md).

### null

Whether the piped input is exactly `null` (`===`). A fetch that finds nothing returns `null`. No parameters.
Generated reference: [null](../../examples/template-operators/null.md).

### true and false

Return the booleans `true` and `false`. They are how you write a boolean literal: `true()`, `false()`. No parameters.
Generated references: [true](../../examples/template-operators/true.md), [false](../../examples/template-operators/false.md).

```tpl
{def $empty = array()
     $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $missing = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
$empty|not = {$empty|not|choose( 'false', 'true' )}
not( 0 ) = {not( 0 )|choose( 'false', 'true' )}
'0'|not = {'0'|not|choose( 'false', 'true' )}
''|not = {''|not|choose( 'false', 'true' )}
$node|not = {$node|not|choose( 'false', 'true' )}
$missing|null (no node has that id) = {$missing|null|choose( 'false', 'true' )}
true() = {true()|choose( 'false', 'true' )}, false() = {false()|choose( 'false', 'true' )}
```

Output:

```text
$empty|not = true
not( 0 ) = true
'0'|not = true
''|not = true
$node|not = false
$missing|null (no node has that id) = true
true() = true, false() = false
```

## choose

### choose

Picks one of its parameters by the piped input: a number is the index (0 picks the first parameter), a boolean is 0
or 1 (so `false` picks the first and `true` the second), and `null` is 0. Only the chosen parameter is evaluated.
An array or object input logs "Only supports numeric and boolean values"; an index outside the parameters logs
"Index ... out of range" and returns `false`.

| Parameter | Type | Required | Default |
|---|---|---|---|
| choices | mixed | yes | none |

Generated reference: [choose](../../examples/template-operators/choose.md).

```tpl
{def $n = 2
     $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
$n|choose( 'zero', 'one', 'two' ) = {$n|choose( 'zero', 'one', 'two' )}
true()|choose( 'no', 'yes' ) = {true()|choose( 'no', 'yes' )}
The Recipes node {$node.children_count|gt( 0 )|choose( 'has no children', concat( 'has ', $node.children_count, ' children' ) )}.
```

Output:

```text
$n|choose( 'zero', 'one', 'two' ) = two
true()|choose( 'no', 'yes' ) = yes
The Recipes node has 18 children.
```

An index outside the list is an error:

```tpl
3|choose( 'a', 'b' ) = {3|choose( 'a', 'b' )|choose( 'false', 'true' )}
```

Output:

```text
3|choose( 'a', 'b' ) = false
```

Logged by the template engine:

```text
ERROR choose: Index 3 out of range 0 => 1
```

For a two-way choice on any condition, [cond](control-operators.md#cond) reads more naturally.

## Related pages

- [Logic and comparison operators for designers](../designers/templates/logic-and-comparison-operators.md)
- [If and conditions](../designers/templates/if-and-conditions.md)
- [Control operators: cond and first_set](control-operators.md)
- [Type operators](type-operators.md): `is_set`, `is_array` and the other tests
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
