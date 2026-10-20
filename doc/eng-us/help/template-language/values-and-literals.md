---
title: Values and literals
audience: developer, designer, app-creator
category: development
order: 70
summary: The values a template can write down - strings in single or double quotes (no variable interpolation, the escapes the parser knows), integers and floats, true() and false(), arrays and hashes built with array() and hash(), bare identifiers, and what stands in for null - each shown on alpha with get_type.
keywords: values, literals, strings, quotes, escape sequences, numbers, integer, float, boolean, true, false, array, hash, identifier, null, get_type, truthiness
type: help
---
# Values and literals

The expression parser (`lib/eztemplate/classes/eztemplateelementparser.php`) knows six kinds of literal values. This
page lists them with the exact rules of the parser and shows each on alpha. `get_type` (from
`eZTemplateTypeOperator`) prints the PHP type of a value, so it is used here to show what a literal becomes.

## Contents

1. [Strings](#strings)
2. [Numbers](#numbers)
3. [Booleans](#booleans)
4. [Arrays and hashes](#arrays-and-hashes)
5. [Identifiers](#identifiers)
6. [Null](#null)
7. [Truthiness](#truthiness)
8. [Related pages](#related-pages)

## Strings

A string is written in single or double quotes. The two are the same: neither interpolates variables, so
`"Hello $name"` prints the dollar sign and the name literally. Join strings with `concat`.

`unescapeCharacters()` knows these escapes, in both kinds of quotes:

| Escape | Becomes |
|---|---|
| `\n` | a newline |
| `\r` | a carriage return |
| `\t` | a tab |
| `\'` and `\"` | the quote |
| `\\` | one backslash |
| anything else, such as `\q` | kept as written, backslash included |

Inside a tag, a `}` in a string must be written `\}`, or the tag ends there (see
[Lexical structure](lexical-structure.md#a-brace-inside-a-tag)).

```tpl
{def $name = 'World'}
[{'Hello $name'}] [{"Hello $name"}] [{concat( 'Hello ', $name )}]
[{'It\'s'}] [{"say \"hi\""}] [{'tab:\t:'}] [{'back\\slash'}] [{'unknown \q stays'}]
[{'line one\nline two'}]
```

Output on alpha:

```text
[Hello $name] [Hello $name] [Hello World]
[It's] [say "hi"] [tab:	:] [back\slash] [unknown \q stays]
[line one
line two]
```

## Numbers

A number is a run of digits with an optional leading `-` and at most one `.` followed by digits. Without a dot it is
an integer, with one it is a float (`numericEndPos()`). A dot must be followed by a digit: `1.` is the number `1`
followed by a parser error (shown at the end of this section). Floats are printed by PHP, so `1.50` prints `1.5`.

Strings from content or requests stay strings until an operator converts them; `int` and `float` do it explicitly.
`div` returns an integer when the division has no remainder and a float otherwise.

```tpl
[{42}] [{-7}] [{3.14}] [{0.5}] [{1.50}]
[{42|get_type}] [{3.14|get_type}] [{'42'|get_type}] [{'42'|int|get_type}]
[{10|div( 4 )}] [{10|div( 5 )|get_type}]
```

Output on alpha:

```text
[42] [-7] [3.14] [0.5] [1.5]
[integer] [double] [string[2]] [integer]
[2.5] [integer]
```

A dot without a digit after it:

```tpl
[{1.|get_type}]
```

Output on alpha:

```text
[1]
```

Logged by the template engine:

```text
ERROR : parser error @ tl-values/07-number-dot.tpl:1[2]
Extra characters found in expression, they will be ignored.
.|get_type' (1)
```

## Booleans

There is no `true` or `false` keyword: `true()` and `false()` are operators of `eZTemplateLogicOperator` that return the
PHP booleans. Printed, true becomes `1` and false the empty string, as in PHP.

```tpl
[{true()}] [{false()}] [{true()|get_type}] [{false()|get_type}]
[{if 0}0 is true{else}0 is false{/if}] [{if '0'}'0' is true{else}'0' is false{/if}] [{if ''}empty is true{else}empty is false{/if}] [{if array()}empty array is true{else}empty array is false{/if}]
```

Output on alpha:

```text
[1] [] [boolean[true]] [boolean[false]]
[0 is false] ['0' is false] [empty is false] [empty array is false]
```

## Arrays and hashes

`array( ... )` builds a list with keys 0, 1, 2 and `hash( key, value, ... )` a map from pairs. Both come from
`eZTemplateArrayOperator`, so they are operators like any other and can be nested, piped and passed as parameters.
Elements are read with a dot or with brackets: `$list.1`, `$map.red`, `$deep['count']`.

```tpl
{def $list = array( 'a', 'b', 'c' )
     $map  = hash( 'red', '#f00', 'green', '#0f0' )
     $deep = hash( 'sizes', array( 'S', 'M', 'L' ), 'count', 3 )}
[{$list|count}] [{$list.1}] [{$list|implode( '-' )}]
[{$map.red}] {foreach $map as $k => $v}[{$k}={$v}]{/foreach}
[{$deep.sizes.2}] [{$deep['count']}]
[{array()|count}] [{$list|get_type}] [{$map|get_type}]
```

Output on alpha:

```text
[3] [b] [a-b-c]
[#f00] [red=#f00][green=#0f0][L] [3]
[0] [array[3]] [array[2]]
```

`get_type` adds the number of elements for arrays and the length for strings, which makes it useful when debugging.

## Identifiers

A bare word that is not followed by `(` is an identifier, and its value is the word itself. Some operators and
functions use identifiers where other languages would use quoted keywords, for example `attribute( show, 2 )` or
`sequence array( odd, even )`:

```tpl
[{1|choose( zero, one, two )}] [{array( red, green )|implode( '+' )}]
```

Output on alpha:

```text
[one] [red+green]
```

The characters allowed in identifiers and variable names are letters, digits, `_` and `-`
(`identifierEndPosition()`). A dash is part of the name, so `$my-var` is one variable, not a subtraction.

## Null

There is no null literal. `null` exists, but it is a test from `eZTemplateLogicOperator` that is true when its input is
null, not a value. Null values arrive from PHP, for example from a fetch that finds nothing; test them with `is_null`
or `is_set`, and replace a missing variable with `first_set`:

```tpl
{def $empty = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
[{$empty|get_type}] [{if $empty|is_null}is null{/if}] [{if is_set( $empty )}is set{else}not set{/if}] [{first_set( $missing, 'default' )}]
```

Output on alpha:

```text
[null] [is null] [not set] [default]
```

## Truthiness

Conditions use PHP truthiness. `0`, `'0'`, the empty string, an empty array, `false()` and null are false; everything
else, including an array that holds a `0`, is true. The values tested in this loop are `0`, `1`, `''`, `'x'`, `'0'`,
`array()`, `array( 0 )`, `false()` and `true()`:

```tpl
{foreach array( 0, 1, '', 'x', '0', array(), array( 0 ), false(), true() ) as $v}[{if $v}T{else}F{/if}]{/foreach}
```

Output on alpha:

```text
[F][T][F][T][F][F][T][F][T]
```

## Related pages

- Previous: [Lexical structure](lexical-structure.md)
- Next: [Variables in depth](variables-in-depth.md)
- [TPL syntax basics](../designers/templates/tpl-syntax-basics.md)
- [Array operators](../designers/templates/array-operators.md)
- [The type operators](../../examples/template-operators/get_type.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
