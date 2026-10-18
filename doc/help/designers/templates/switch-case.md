---
title: Switch and case
audience: designer, developer
category: templates
order: 90
summary: Choosing between several outputs with switch and case: matching a value, matching one of several values with in, the default case, why conditions belong in if, and when if or an override is the better tool.
keywords: switch, case, match, in, process, default case, multiple choices, template switch, class switch
type: help
---
# Switch and case

This page is for designers who print one of several outputs depending on a value.

## Contents

1. [Syntax](#syntax)
2. [Matching several values](#matching-several-values)
3. [A condition](#a-condition)
4. [When not to use it](#when-not-to-use-it)

## Syntax

From `lib/eztemplate/classes/eztemplateswitchfunction.php`:

```tpl
{switch match=$node.class_identifier}
{case match='article'}
    <span class="badge">{'Article'|i18n( 'design/mytheme' )}</span>
{/case}
{case match='event'}
    <span class="badge">{'Event'|i18n( 'design/mytheme' )}</span>
{/case}
{case}
    {* no match: the case without a match is the default *}
{/case}
{/switch}
```

The media design's title template uses a switch on the error type and number to name error pages.

## Matching several values

```tpl
{case in=array( 'article', 'blog_post' )}
    ...
{/case}
```

## A condition

`{case}` compares values only (`match` or `in`). A `process=` parameter appears in the class's comment as a TODO and
is not implemented. For a range or any other condition, use `{if}`:

```tpl
{if gt( $children|count, 5 )}
    ...
{else}
    ...
{/if}
```

## When not to use it

- Per content class markup belongs in override templates, not in one big switch (Node templates by class).
- Two branches are clearer with `{if}`.

## Related pages

- [If and conditions](if-and-conditions.md)
- Override basics
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
