---
title: TPL syntax basics
audience: designer, developer
category: templates
order: 20
summary: The rules of the template language in one page: tags in braces, variables with $, values (strings, numbers, arrays, hashes, booleans), operators with pipe or call syntax, functions with parameters, comments, whitespace, and the braces you must escape.
keywords: tpl syntax, template syntax, braces, tags, variables, strings, numbers, true false, array, hash, pipe operator, function parameters, comments, ldelim, rdelim
featured: true
type: help
---
# TPL syntax basics

This page is for designers who read or write templates. It lists the rules of the template language once, so the
other pages can be short.

## Contents

1. [Text and tags](#text-and-tags)
2. [Values](#values)
3. [Variables](#variables)
4. [Operators](#operators)
5. [Functions](#functions)
6. [Comments](#comments)
7. [Braces in the output](#braces-in-the-output)

## Text and tags

Everything outside braces is printed as it is. Everything inside `{ }` is a template tag:

| Tag | Meaning |
|---|---|
| `{$variable}` | print a value |
| `{$value\|operator}` | print a value passed through operators |
| `{function param=value}` ... `{/function}` | a function with a body |
| `{function param=value}` | a function without a body |
| `{* ... *}` | a comment, never printed |

## Values

| Kind | Written as |
|---|---|
| string | `'single'` or `"double"` quotes |
| integer, float | `42`, `3.5` |
| boolean | `true()`, `false()` (operators, written with parentheses) |
| array | `array( 'a', 'b', 'c' )` |
| hash | `hash( 'key', 'value', 'other', 2 )` |
| test for nothing | `$value\|null` (true when the value is null), `is_set( $value )` |

## Variables

Variables start with `$`. They are created with `{def}`, changed with `{set}`, removed with `{undef}`
([Variables and def](variables-and-def.md)). Attributes of objects and keys of hashes are read with a dot:
`$node.name`, `$node.data_map.title`, `$hash.key`; array items with a number: `$list.0`
([Attributes and dot notation](attributes-and-dot-notation.md)).

## Operators

Two equivalent ways to call an operator:

```tpl
{$text|upcase}                {* pipe: the value on the left is the input *}
{upcase( $text )}             {* call *}
{$text|shorten( 80 )}         {* pipe with parameters *}
{concat( 'a', $b, 'c' )}      {* call with several values *}
```

Operators can be chained: `{$text|strip_tags|shorten( 120 )|wash}`. Comparison and arithmetic are operators too:
`eq( $a, 1 )`, `gt( $count, 10 )`, `sum( $a, 2 )` ([Operators overview](operators-overview.md)).

## Functions

Functions control the template: `{if}`, `{foreach}`, `{def}`, `{include}`, `{cache-block}`. Parameters are
`name=value` pairs or, for some functions, a fixed syntax:

```tpl
{include uri='design:parts/teaser.tpl' node=$child}
{foreach $items as $item} ... {/foreach}
```

See the [Function reference](function-reference.md).

## Comments

```tpl
{* A comment: may span several lines, never reaches the browser. *}
```

Use template comments, not HTML comments (`<!-- -->`), for notes about the template: HTML comments are sent to every
visitor.

## Braces in the output

A literal `{` or `}` in the output (CSS or JavaScript inside a template, which designs avoid anyway) is written as
`{ldelim}` and `{rdelim}`, or the text is wrapped in `{literal}` ... `{/literal}` ([Literal and delimiters](literal-and-delimiters.md)).

## Related pages

- [Templates overview](templates-overview.md)
- [Variables and def](variables-and-def.md)
- [Operators overview](operators-overview.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
