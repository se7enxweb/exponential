---
title: Sections versus foreach
audience: developer, designer, app-creator
category: templates
order: 140
summary: The section function in full - loop over arrays, numbers and strings, name and the namespace it creates, var and the iterator with last-value, sequence, offset, max, reverse, show with section-else, the section-exclude and section-include filters and delimiter with modulo - each next to its foreach, for or if equivalent, all rendered on alpha, plus the one compiled-mode difference found.
keywords: section, foreach, legacy, loop, show, section-else, section-exclude, section-include, var, last-value, sequence, offset, max, reverse, delimiter, modulo, namespace, convert, migration
type: help
---
# Sections versus foreach

`section` was the loop and the condition of the template language before `foreach`, `for` and `if` arrived (see
[History](history.md#the-improved-syntax-2005-version-36)). It is still registered, older designs and extensions use it
everywhere, and it can do a few things the newer functions do differently. This page documents every parameter of
`eZTemplateSectionFunction` (`lib/eztemplate/classes/eztemplatesectionfunction.php`) and shows the modern equivalent
next to each. For a short conversion guide see [Section (legacy)](../designers/templates/section-legacy.md).

## Contents

1. [Parameters](#parameters)
2. [Loop, namespace and the loop variables](#loop-namespace-and-the-loop-variables)
3. [Numbers and strings](#numbers-and-strings)
4. [offset, max, reverse](#offset-max-reverse)
5. [show and section-else](#show-and-section-else)
6. [Filters: section-exclude and section-include](#filters-section-exclude-and-section-include)
7. [var and last-value](#var-and-last-value)
8. [delimiter and modulo](#delimiter-and-modulo)
9. [A difference in compiled templates](#a-difference-in-compiled-templates)
10. [Related pages](#related-pages)

## Parameters

| Parameter | Meaning | Modern equivalent |
|---|---|---|
| `loop` | an array, a number or a string to iterate | `foreach` (arrays), `for` (numbers) |
| `name` | the namespace for the loop variables | not needed: `foreach ... as $item` |
| `var` | one iterator variable instead of the namespace | `foreach ... as $key => $item` |
| `last-value` | with `var`: `$it.last` holds the previous iterator | keep the previous item in a variable |
| `sequence` | an array cycled through, `$Name:sequence` | `sequence <array> as $s` |
| `offset`, `max` | skip and limit; an array counts as its length | `offset`, `max` of `foreach` |
| `reverse` | iterate backwards | `reverse` |
| `show` | process the body only when true; without `loop` it is an `if` | `if` |
| child `section-else` | what to show when `show` is false | `else` |
| child `section-exclude match=...` | drop items for which the match is true | `{if ...}{skip}{/if}` |
| child `section-include match=...` | take items back in | an `if` around the body |
| child `delimiter [modulo=n] [match=...]` | text between iterations | `delimiter` of the loop |

## Loop, namespace and the loop variables

With `name=Loop`, each iteration sets `item`, `key`, `index` (from 0), `number` (from 1) and, with a `sequence`,
`sequence` in the namespace `Loop`, read as `$Loop:item` or, inside the section, `$:item` (see
[Namespaces](namespaces.md)). Without a name they land in the current namespace as `$item`, `$key` and so on, and are
removed after the loop.

```tpl
{section name=Loop loop=array( 'a', 'b', 'c' ) sequence=array( 'odd', 'even' )}[{$Loop:item}/{$Loop:key}/{$Loop:index}/{$Loop:number}/{$Loop:sequence}]{delimiter},{/delimiter}{/section}
```

Output on alpha:

```text
[a/0/0/1/odd],[b/1/1/2/even],[c/2/2/3/odd]
```

The same with `foreach`:

```tpl
{foreach array( 'a', 'b', 'c' ) as $key => $item sequence array( 'odd', 'even' ) as $seq}[{$item}/{$key}/{$key}/{$key|inc}/{$seq}]{delimiter},{/delimiter}{/foreach}
```

Output on alpha:

```text
[a/0/0/1/odd],[b/1/1/2/even],[c/2/2/3/odd]
```

## Numbers and strings

`loop` also takes a number, iterating `item` from 1 to n (or -1 to -n for a negative number), and a string, iterating
its characters. `foreach` does neither: use `for`, or split the string with `str_split`:

```tpl
[{section loop=3}{$item}{/section}] [{section loop=-3}{$item}{/section}] [{section loop='abc' reverse=true()}{$item}{/section}]
[{for 1 to 3 as $i}{$i}{/for}] [{foreach 'abc'|str_split|reverse as $c}{$c}{/foreach}]
```

Output on alpha:

```text
[123] [-1-2-3] [cba]
[123] [cba]
```

## offset, max, reverse

`offset` drops the first n items and `max` stops after n processed items (an array given to either counts as its
length; a negative offset is reset to 0 with a warning). With `reverse` the keys are reversed before the offset is
applied:

```tpl
[{section name=S loop=array( 1, 2, 3, 4, 5, 6 ) offset=1 max=3}{$S:item}{/section}]
[{foreach array( 1, 2, 3, 4, 5, 6 ) as $v offset 1 max 3}{$v}{/foreach}]
[{section name=R loop=array( 1, 2, 3 ) reverse=true()}{$R:item}{/section}] [{foreach array( 1, 2, 3 ) as $v reverse}{$v}{/foreach}]
```

Output on alpha:

```text
[234]
[234]
[321] [321]
```

## show and section-else

`show` decides whether the body runs at all. It is false for `false()`, 0, an empty string and an empty array. The
children after `{section-else}` run instead. Without `loop`, `section show=...` is simply a condition:

```tpl
{def $results = array()}
[{section show=$results|count}results{section-else}nothing found{/section}]
[{if $results|count}results{else}nothing found{/if}]
```

Output on alpha:

```text
[nothing found]
[nothing found]
```

```tpl
{def $user = hash( 'name', 'Ada' )}
[{section show=$user.name}Hello {$user.name}{/section}]
```

Output on alpha:

```text
[Hello Ada]
```

`section-else` belongs to `show`: an empty `loop` without `show` prints nothing at all, not the else part:

```tpl
[{section loop=array()}x{section-else}else part{/section}]
[{section loop=array() show=array()}x{section-else}else part{/section}]
```

Output on alpha:

```text
[]
[else part]
```

## Filters: section-exclude and section-include

Filter tags are evaluated for every item in the section's namespace, in order. An `exclude` whose `match` is true
removes the item, an `include` whose `match` is true takes it back; the last filter that matches decides. An excluded
item does not count for `max` and gets no delimiter. The `foreach` form uses `{skip}`:

```tpl
[{section name=F loop=array( 1, 2, 3, 4, 5, 6 )}{section-exclude match=$:item|mod( 2 )|eq( 0 )}{$F:item}{/section}]
[{section name=G loop=array( 1, 2, 3, 4, 5, 6 )}{section-exclude match=true()}{section-include match=$:item|gt( 4 )}{$G:item}{/section}]
[{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{if $v|mod( 2 )|eq( 0 )}{skip}{/if}{$v}{/foreach}]
```

Output on alpha:

```text
[135]
[56]
[135]
```

## var and last-value

`var=it` gives one iterator object instead of a namespace (`eZTemplateSectionIterator`). Printed, it is the current
item; its attributes are `item`, `key`, `index`, `number`, `sequence`, and the item's own attributes. With
`last-value=true()` the attribute `last` holds the iterator of the previous iteration:

```tpl
[{section var=it loop=array( 'a', 'b', 'c' ) last-value=true()}{$it}{if $it.last} (after {$it.last}){/if}; {/section}]
[{section var=it loop=array( 'a', 'b' )}{$it.item}/{$it.key}/{$it.index}/{$it.number} {/section}]
```

Output on alpha:

```text
[a; b (after a); c (after b); ]
[a/0/0/1 b/1/1/2 ]
```

## delimiter and modulo

As with the newer loops, `delimiter` is printed between processed items, and `modulo=n` limits it to every n-th. A
`section` delimiter also accepts `match=<condition>`:

```tpl
[{section name=D loop=array( 1, 2, 3, 4 )}{delimiter modulo=2}|{/delimiter}{$D:item}{/section}]
[{foreach array( 1, 2, 3, 4 ) as $v}{$v}{delimiter modulo=2}|{/delimiter}{/foreach}]
```

Output on alpha:

```text
[12|34]
[12|34]
```

## A difference in compiled templates

Every example on this page was also rendered through the template compiler. One gives a different result: a reversed
loop over a **string**. Interpreted, `{section loop='abc' reverse=true()}` prints `cba`; compiled it prints only `c`,
while the `foreach` form gives `cba` both ways:

```tpl
[{section loop=3}{$item}{/section}] [{section loop=-3}{$item}{/section}] [{section loop='abc' reverse=true()}{$item}{/section}]
[{for 1 to 3 as $i}{$i}{/for}] [{foreach 'abc'|str_split|reverse as $c}{$c}{/foreach}]
```

Output on alpha:

```text
[123] [-1-2-3] [cba]
[123] [cba]
```

Output on alpha with the template compiler (compiled into a scratch folder):

```text
[123] [-1-2-3] [c]
[123] [cba]
```

Live sites compile their templates (`site.ini` [TemplateSettings] `TemplateCompile=enabled` by default), so convert
such a loop to `foreach` with `str_split|reverse`.

## Related pages

- Previous: [Control structures reference](control-structures-reference.md)
- [Section (legacy)](../designers/templates/section-legacy.md)
- [section](../../examples/template-functions/section.md), generated from the code
- [Foreach loops](../designers/templates/foreach-loops.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
