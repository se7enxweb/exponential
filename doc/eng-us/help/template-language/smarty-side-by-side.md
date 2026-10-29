---
title: Smarty and the template language side by side
audience: developer, designer, app-creator
category: templates
order: 30
summary: A side-by-side table of Smarty syntax and the Exponential template language - output, modifiers and operators, assignment, defaults, conditions, loops, cycling, include, literal, delimiters, comments, capture, escaping, dates, caching and inheritance - with every template-language example rendered on a development installation.
keywords: Smarty, comparison, side by side, syntax, modifiers, operators, assign, def, capture, set-block, cycle, sequence, include, literal, ldelim, escape, wash, date_format, datetime
type: help
---
# Smarty and the template language side by side

If you know Smarty you can read most templates of Exponential at once: the braces, the `$`, the pipe and the block tags
are the same idea. The details differ, and the details are what break a template. This page puts the two next to each
other. The Smarty column is there for comparison only and was not run here; every template-language example was
rendered on a development installation.

> **Exponential runs Smarty templates itself:** a design can hold Smarty 5 templates (`.stpl`) next to its `.tpl`
> templates, with every operator, fetch function, template function, override rule and cache of the template system.
> If you would rather keep writing Smarty, see [Smarty templates (.stpl) next to .tpl](../../features/6.0/smarty-templates.md)
> and [Moving from .tpl to .stpl](moving-from-tpl-to-stpl.md), which also converts templates in both directions.

> **Exponential runs Smarty templates itself:** a design can hold Smarty 5 templates (`.stpl`) next to its `.tpl`
> templates, with every operator, fetch function, template function, override rule and cache of the template system.
> If you would rather keep writing Smarty, see [Smarty templates (.stpl) next to .tpl](../../features/6.0/smarty-templates.md)
> and [Moving from .tpl to .stpl](moving-from-tpl-to-stpl.md), which also converts templates in both directions.

## Contents

1. [The table](#the-table)
2. [Output and modifiers](#output-and-modifiers)
3. [Assignment and defaults](#assignment-and-defaults)
4. [Conditions and loops](#conditions-and-loops)
5. [Include, literal, delimiters and comments](#include-literal-delimiters-and-comments)
6. [Capture](#capture)
7. [Dates and counts](#dates-and-counts)
8. [Related pages](#related-pages)

## The table

| Task | Smarty, for comparison (not run here) | Template language |
|---|---|---|
| Print a variable | `{$title}` | `{$title}` |
| Array key / object property | `{$user.name}`, `{$user->name}` | `{$user.name}` for both (see [Attribute access](attribute-access.md)) |
| Modifier / operator | `{$title\|upper}` | `{$title\|upcase}` |
| Modifier with arguments | `{$title\|truncate:16}` | `{$title\|shorten( 16 )}` |
| Chain | `{$title\|trim\|upper}` | `{$title\|trim\|upcase}` |
| Function-style call | `{counter}`, plugins | `{sum( 1, 2 )}`: every operator can also be called |
| Arithmetic | `{$a + 1}` | `{$a\|sum( 1 )}` or `{sum( $a, 1 )}`: no infix operators |
| Comparison | `{if $a > 1}` | `{if $a\|gt( 1 )}` |
| Logic | `{if $a && !$b}` | `{if and( $a, not( $b ) )}` |
| Assign | `{assign var=x value=1}`, `{$x = 1}` | `{def $x = 1}` to create, `{set $x = 2}` to change |
| Default value | `{$nick\|default:'none'}` | `{first_set( $nick, 'none' )}` |
| Ternary | `{($a) ? 'y' : 'n'}` | `{cond( $a, 'y', 'n' )}` |
| If | `{if}{elseif}{else}{/if}` | `{if}{elseif}{else}{/if}` |
| Loop with key | `{foreach $arr as $k => $v}` | `{foreach $arr as $k => $v}` |
| Loop over nothing | `{foreachelse}` | test first: `{if $arr\|count}` |
| Alternate row classes | `{cycle values="odd,even"}` | `sequence array( 'odd', 'even' ) as $row` on the loop |
| Number range | `{for $i=1 to 3}` | `{for 1 to 3 as $i}` |
| Include | `{include file='part.tpl' who='a'}` | `{include uri='design:part.tpl' who='a'}` |
| Literal text with braces | `{literal}...{/literal}` | `{literal}...{/literal}` |
| A single brace | `{ldelim}`, `{rdelim}` | `{ldelim}`, `{rdelim}` |
| Comment | `{* ... *}` | `{* ... *}` |
| Capture output | `{capture name=x}...{/capture}` | `{set-block variable=x}...{/set-block}` |
| Escape for HTML | `{$s\|escape}` (or auto-escaping when enabled) | `{$s\|wash}`: never automatic |
| Format a date | `{$t\|date_format:'%Y-%m-%d'}` | `{$t\|datetime( 'custom', '%Y-%m-%d' )}` |
| Count | `{$arr\|count}` | `{$arr\|count}` |
| Raw PHP | `{php}` | none: logic goes into an operator class |
| Cache part of a page | `{nocache}`, caching per template | `{cache-block keys=...}...{/cache-block}` |
| Reuse a layout | `{extends}` and `{block}` | the pagelayout plus overrides; `$module_result.content` is the page body |

## Output and modifiers

Operators take the place of modifiers. They are called with parentheses and comma-separated arguments, not with
colons:

```tpl
{def $title = '  The Exponential template language  '}
[{$title}]
[{$title|trim|upcase}]
[{$title|trim|shorten( 16 )}]
[{$title|trim|count_chars}]
[{'<b>bold</b> & more'|wash}]
```

Output:

```text
[  The Exponential template language  ]
[THE EXPONENTIAL TEMPLATE LANGUAGE]
[The Exponenti...]
[33]
[&lt;b&gt;bold&lt;/b&gt; &amp; more]
```

`shorten( 16 )` counts the three dots it appends into the 16 characters. `count_chars` is the length of a string.

## Assignment and defaults

`{def}` creates a variable and `{set}` changes one that exists; Smarty's `assign` does both. A default value comes from
`first_set`, the first parameter that is set; `cond` is the template language's ternary:

```tpl
{def $name = 'Ada' $visits = 3}
{set $visits = $visits|inc}
{$name} has visited {$visits} times.
Nickname: {first_set( $nickname, 'none given' )}
Greeting: {cond( $visits|gt( 3 ), 'welcome back', 'hello' )}
```

Output:

```text
Ada has visited 4 times.
Nickname: none given
Greeting: welcome back
```

## Conditions and loops

`if` and `foreach` read almost the same. Cycling through values is a parameter of the loop (`sequence`), and a range is
written `for <first> to <last> as $var`:

```tpl
{def $count = 2}
{if $count|eq( 0 )}none{elseif $count|eq( 1 )}one{else}{$count} items{/if}
{foreach hash( 'a', 'apple', 'b', 'banana', 'c', 'cherry' ) as $key => $fruit sequence array( 'odd', 'even' ) as $row}
<li class="{$row}">{$key}: {$fruit}</li>
{/foreach}
{for 1 to 3 as $i}[{$i}]{/for}
```

Output:

```text
2 items<li class="odd">a: apple</li><li class="even">b: banana</li><li class="odd">c: cherry</li>[1][2][3]
```

The newlines around each function tag disappeared from the output: the parser removes one newline before and one after
every function tag (see [Lexical structure](lexical-structure.md#whitespace-around-tags)). Smarty keeps them.

## Include, literal, delimiters and comments

`include` takes a `uri`, usually `design:...`, which the design resource resolves through the design bases and the
override system. This example includes a small file of its own with the `file:` resource, so it runs outside a design;
the included file is:

```tpl
<p>Hello, {$who|wash}!</p>
```

and the template that includes it, with a literal block, the two delimiter functions and a comment:

```tpl
{include uri='file:var/tmp/examples/tl-smarty/parts/greeting.tpl' who='designer'}
{* a comment is never output *}
{literal}<style>.box { color: red; }</style>{/literal}
{ldelim}not a tag{rdelim}
```

Output:

```text
<p>Hello, designer!</p>

<style>.box { color: red; }</style>{not a tag}
```

## Capture

`set-block` renders its content into a variable, like Smarty's `capture`. The result is an ordinary string that operators
can work on:

```tpl
{set-block variable=badge}<span class="badge">{'new'|upcase}</span>{/set-block}
Captured {$badge|count_chars} characters: {$badge}
```

Output:

```text
Captured 30 characters: <span class="badge">NEW</span>
```

## Dates and counts

Dates are timestamps; `datetime( 'custom', <format> )` formats them with the `%` placeholders of the locale code, and
`l10n( 'shortdate' )` uses the short date format of the current locale:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
Published: {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
Short date: {$node.object.published|l10n( 'shortdate' )}
Fields: {$node.data_map|count}
```

Output:

```text
Published: 2023-01-25
Short date: 01/25/2023
Fields: 18
```

## Related pages

- [Moving from Exponential templates (.tpl) to Exponential Smarty templates (.stpl)](moving-from-tpl-to-stpl.md) and [Smarty templates (.stpl) next to .tpl](../../features/6.0/smarty-templates.md): Smarty templates run by Exponential itself
- Previous: [History of the template language](history.md)
- Next: [Smarty and the template language: the differences in depth](smarty-differences-in-depth.md)
- [Moving a template from Smarty](moving-from-smarty.md)
- [TPL syntax basics](../designers/templates/tpl-syntax-basics.md)
- [Set-block and append-block](../designers/templates/set-block-and-append-block.md)
- [Wash and escaping](../designers/templates/wash-and-escaping.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
