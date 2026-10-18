---
title: For loops
audience: designer, developer
category: templates
order: 80
summary: Counting loops with for: from a number to a number, counting down, sequences, and typical uses such as star ratings, placeholders and page numbers.
keywords: for, for loop, counting, range, 1 to 5, count down, template loop, rating stars, page numbers
type: help
---
# For loops

This page is for designers who need to repeat something a number of times.

## Contents

1. [Syntax](#syntax)
2. [Examples](#examples)
3. [Notes](#notes)

## Syntax

From `lib/eztemplate/classes/eztemplateforfunction.php`:

```text
{for <number> to <number> as $itemVar [sequence <array> as $seqVar]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/for}
```

`{for 1 to 5 as $i}` counts up; `{for 5 to 1 as $i}` counts down.

## Examples

Five stars, filled up to a rating:

```tpl
<span class="rating" role="img" aria-label="{'%count of 5 stars'|i18n( 'design/mytheme',, hash( '%count', $rating ) )|wash}">
{for 1 to 5 as $star}
    <span class="star{if le( $star, $rating )} is-filled{/if}" aria-hidden="true"></span>
{/for}
</span>
```

Page numbers (when you do not use the shipped navigator templates, see Pagination):

```tpl
{for 1 to $page_count as $page}
    <a href={concat( $node.url_alias, '/(offset)/', $page|dec|mul( $limit ) )|ezurl}>{$page}</a>{delimiter} {/delimiter}
{/for}
```

## Notes

- The bounds can be variables and operators: `{for 1 to $items|count as $i}`.
- For lists, `{foreach}` with `offset` and `max` is clearer ([Foreach loops](foreach-loops.md)).

## Related pages

- [Foreach loops](foreach-loops.md)
- [Math operators](math-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
