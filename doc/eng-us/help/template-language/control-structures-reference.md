---
title: Control structures reference
audience: developer, designer, app-creator
category: development
order: 130
summary: Every parameter of if, elseif and else, switch and case (match, in, key and the default case), foreach (key, sequence, offset, max, reverse), for, while and do, and the loop helpers delimiter (with modulo), break, continue and skip - with their exact behavior from the code, each shown on a development installation.
keywords: control structures, if, elseif, else, switch, case, match, in, key, foreach, offset, max, reverse, sequence, for, while, do, delimiter, modulo, break, continue, skip, loops
type: help
---
# Control structures reference

This page lists every parameter of the branching and looping functions, as implemented in `lib/eztemplate/classes/`
(`eztemplateiffunction.php`, `eztemplateswitchfunction.php`, `eztemplateforeachfunction.php`,
`eztemplateforfunction.php`, `eztemplatewhilefunction.php`, `eztemplatedofunction.php` and the shared loop helper
`eztemplateloop.php`). The special loop grammar is parsed in `eztemplatemultipassparser.php`. For gentler introductions
see the designer pages linked at the end.

## Contents

1. [if, elseif, else](#if-elseif-else)
2. [What counts as true](#what-counts-as-true)
3. [switch and case](#switch-and-case)
4. [foreach](#foreach)
5. [break, continue, skip](#break-continue-skip)
6. [delimiter and modulo](#delimiter-and-modulo)
7. [for](#for)
8. [while](#while)
9. [do](#do)
10. [Related pages](#related-pages)

## if, elseif, else

```text
{if <condition>} ... [{elseif <condition>} ...] [{else} ...] {/if}
```

The condition is one expression; combine tests with `and()`, `or()` and `not()`. `elseif` and `else` are child tags of
`if` (its `function_attributes`).

```tpl
{foreach array( 0, 1, 7 ) as $n}
{if $n|eq( 0 )}none{elseif $n|eq( 1 )}one{else}{$n} items{/if}
{/foreach}
```

Output:

```text
noneone7 items
```

## What counts as true

Conditions use PHP truthiness: `0`, `'0'`, `''`, an empty array, `false()` and null are false. The values tested here
are `0`, `1`, `''`, `'x'`, `'0'`, `array()`, `array( 0 )`, `false()`, `true()`:

```tpl
{foreach array( 0, 1, '', 'x', '0', array(), array( 0 ), false(), true() ) as $v}[{if $v}T{else}F{/if}]{/foreach}
```

Output:

```text
[F][T][F][T][F][F][T][F][T]
```

## switch and case

```text
{switch match=<value>}
  {case match=<value>} ... {/case}
  {case in=<array> [key=<array of attribute names>]} ... {/case}
  {case} ... {/case}        (the default)
{/switch}
```

| Parameter | Of | Meaning |
|---|---|---|
| `match` | `switch` | the value to compare; required |
| `match` | `case` | this case is taken when it equals the switch value (`==`) |
| `in` | `case` | this case is taken when the switch value is in the array (`in_array`) |
| `key` | `case` with `in` | compare the switch value with this attribute of each array element instead |
| none | `case` | the default case |

The first matching case wins. A second case with the same `match` value is skipped with a warning.

```tpl
{foreach array( 1, 3, 'other' ) as $v}
{switch match=$v}{case match=1}one{/case}{case in=array( 2, 3 )}two or three{/case}{case}default for {$v}{/case}{/switch}
{/foreach}
```

Output:

```text
onetwo or threedefault for other
```

With `key`, the code passes the value to `eZTemplate::variableAttribute()`, which walks a **list** of attribute names. A
plain string is not turned into a list there, so only the array form finds the element; this is what a development installation does today:

```tpl
{def $people = array( hash( 'id', 7, 'name', 'Ada' ), hash( 'id', 9, 'name', 'Lin' ) )}
key as a string: {switch match=9}{case in=$people key='id'}found{/case}{case}not found{/case}{/switch}
key as an array: {switch match=9}{case in=$people key=array( 'id' )}found{/case}{case}not found{/case}{/switch}
```

Output:

```text
key as a string: not foundkey as an array: found
```

When nothing matches and there is no default case, the switch prints nothing and warns:

```tpl
[{switch match=5}{case match=1}one{/case}{/switch}]
```

Output:

```text
[]
```

Logged by the template engine:

```text
WARNING switch: No case match and no default case
```

## foreach

```text
{foreach <array> as [$key =>] $item
         [sequence <array> as $seq] [offset <n>] [max <n>] [reverse]}
  ...
{/foreach}
```

| Parameter | Meaning |
|---|---|
| `<array> as $item` | the array and the variable for each value; required |
| `$key =>` | a variable for each key |
| `sequence <array> as $seq` | cycles through the array, one value per iteration |
| `offset <n>` | start at position n; out of range is reset with a warning |
| `max <n>` | at most n iterations; a negative value is ignored with a warning |
| `reverse` | iterate from the end; a bare word, no value |

`offset` and `max` count positions, not keys; with `reverse` the offset counts from the end. The key, item and
sequence variables are ordinary variables of the file, not of a namespace.

```tpl
{foreach array( 'a', 'b', 'c', 'd', 'e' ) as $i => $v sequence array( 'x', 'y' ) as $s offset 1 max 3}[{$i}:{$v}:{$s}]{delimiter}|{/delimiter}{/foreach}
{foreach array( 1, 2, 3, 4 ) as $v reverse}{$v}{/foreach}
{foreach hash( 'one', 1, 'two', 2 ) as $key => $value}{$key}={$value} {/foreach}
```

Output:

```text
[1:b:x]|[2:c:y]|[3:d:x]4321one=1 two=2
```

An empty array runs the body zero times, without a warning:

```tpl
[{foreach array() as $v}{$v}{/foreach}] empty array: no output
```

Output:

```text
[] empty array: no output
```

After the loop, do not rely on the loop variables. An interpreted template removes a variable the loop created, a
compiled one keeps it with its last value; a variable that existed before the loop is overwritten in both modes:

```tpl
{foreach array( 1, 2 ) as $v}{/foreach}
[{if is_set( $v )}v still set{else}v removed after the loop{/if}]
{def $item = 'before'}
{foreach array( 1, 2 ) as $item}{/foreach}
[{$item}]
```

Output:

```text
[v removed after the loop][2]
```

Output with the template compiler (compiled into a scratch folder):

```text
[v still set][2]
```

With template compiling enabled, as on a live site, the compiled result is the one visitors get.

`foreach` does not loop over the characters of a string or over a number range; use `for` for ranges, and see
[Sections versus foreach](sections-vs-foreach.md) for what `section` does with strings.

## break, continue, skip

All four loops (`foreach`, `for`, `while`, `do`) accept these child tags, also inside an `if` within the loop
(`eZTemplateLoop::processChildren()`):

| Tag | Ends | Next iteration | Delimiter after this iteration | Sequence advances |
|---|---|---|---|---|
| `{break}` | the loop | no | no | no |
| `{continue}` | this iteration | yes | **yes** | no |
| `{skip}` | this iteration | yes | no | no |

In this loop 2 is continued (its delimiter still prints, hence the double dash), 4 is skipped (no delimiter), and 6
breaks:

```tpl
{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{if eq( $v, 2 )}{continue}{/if}{if eq( $v, 4 )}{skip}{/if}{if eq( $v, 6 )}{break}{/if}{$v}{delimiter}-{/delimiter}{/foreach}
```

Output:

```text
1--3-5-
```

## delimiter and modulo

`{delimiter}...{/delimiter}` is printed between iterations. It must be a direct child of the loop. With `modulo=<n>` it
is only printed when the iteration index divides by n:

```tpl
{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{$v}{delimiter modulo=2} / {/delimiter}{/foreach}
```

Output:

```text
12 / 34 / 56
```

## for

```text
{for <first> to <last> as $i [sequence <array> as $seq]} ... {/for}
```

Counts from first to last inclusive, upward or downward. The loop helpers apply; in the third loop the delimiter after
3 is printed before the `{break}` of the next iteration runs:

```tpl
{for 1 to 5 as $i sequence array( 'o', 'e' ) as $s}{$i}{$s} {/for}
{for 5 to 1 as $i}{$i}{/for}
{for 0 to 10 as $i}{if $i|gt( 3 )}{break}{/if}{$i}{delimiter},{/delimiter}{/for}
```

Output:

```text
1o 2e 3o 4e 5o 543210,1,2,3,
```

## while

```text
{while <condition> [sequence <array> as $seq]} ... {/while}
```

The condition is evaluated before every iteration. Change the tested variable inside the body with `{set}`, or the
loop does not end:

```tpl
{def $n = 0}
{while $n|lt( 3 ) sequence array( 'a', 'b' ) as $s}[{$n}{$s}]{set $n = $n|inc}{/while}
```

Output:

```text
[0a][1b][2a]
```

## do

```text
{do} ... {/do while <condition> [sequence <array> as $seq]}
```

The condition is written in the end tag and tested after each iteration, so the body runs at least once even when the
condition is false from the start:

```tpl
{def $n = 5}
{do}[{$n}]{set $n = $n|inc}{/do while $n|lt( 3 )}
```

Output:

```text
[5]
```

## Related pages

- Previous: [Functions versus operators](functions-vs-operators.md)
- Next: [Sections versus foreach](sections-vs-foreach.md)
- [If and conditions](../designers/templates/if-and-conditions.md), [Switch and case](../designers/templates/switch-case.md)
- [Foreach loops](../designers/templates/foreach-loops.md), [For loops](../designers/templates/for-loops.md), [While and do loops](../designers/templates/while-and-do-loops.md)
- [foreach](../../examples/template-functions/foreach.md), [switch](../../examples/template-functions/switch.md), generated from the code
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
