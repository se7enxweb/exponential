---
title: Foreach loops
audience: designer, developer
category: templates
order: 60
summary: Looping over arrays and fetch results with foreach: keys and items, offset, max and reverse, sequence for alternating classes, delimiter between items, break, continue and skip, and loop habits that keep templates fast.
keywords: foreach, loop, iterate, as, key value, offset, max, reverse, sequence, delimiter, break, continue, skip, zebra rows, list output
type: help
---
# Foreach loops

This page is for designers who print lists. `{foreach}` is the loop you use most.

## Contents

1. [Syntax](#syntax)
2. [Keys and items](#keys-and-items)
3. [Offset, max and reverse](#offset-max-and-reverse)
4. [Delimiter](#delimiter)
5. [Sequence](#sequence)
6. [break, continue, skip](#break-continue-skip)
7. [Habits](#habits)

## Syntax

From the syntax comment of `lib/eztemplate/classes/eztemplateforeachfunction.php`:

```text
{foreach <array> as [$keyVar =>] $itemVar
         [sequence <array> as $sequenceVar]
         [offset <offset>]
         [max <max>]
         [reverse]}
    [{delimiter}...{/delimiter}]
    [{break}]
    [{continue}]
    [{skip}]
{/foreach}
```

## Keys and items

```tpl
{foreach $children as $child}
    <li>{$child.name|wash}</li>
{/foreach}

{foreach $settings as $name => $value}
    <dt>{$name|wash}</dt><dd>{$value|wash}</dd>
{/foreach}
```

## Offset, max and reverse

```tpl
{foreach $articles as $article offset 1 max 3}  {* items 2 to 4 *}
{foreach $steps as $step reverse}               {* last to first *}
```

When the list comes from a fetch, prefer `offset` and `limit` in the fetch: then the database does the work.

## Delimiter

Printed between items, not after the last:

```tpl
{foreach $tags as $tag}{$tag|wash}{delimiter}, {/delimiter}{/foreach}
```

## Sequence

Alternating values, for example row classes:

```tpl
{foreach $rows as $row sequence array( 'odd', 'even' ) as $row_class}
    <tr class="{$row_class}">...</tr>
{/foreach}
```

(For zebra tables, a CSS `:nth-child` rule is usually simpler.)

## break, continue, skip

- `{break}` leaves the loop.
- `{continue}` goes to the next item.
- `{skip}` goes to the next item without printing the delimiter.

```tpl
{foreach $children as $child}
    {if $child.is_hidden}{skip}{/if}
    {node_view_gui content_node=$child view='line'}
{/foreach}
```

## Habits

- No fetch inside a loop: fetch once before ([Fetch performance](fetch-performance.md)).
- `{undef}` loop helper variables after the loop if the template is included elsewhere.
- Print an empty state when the list is empty: `{if $list|count}...{else}...{/if}`.

## Related pages

- [For loops](for-loops.md)
- [While and do loops](while-and-do-loops.md)
- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
