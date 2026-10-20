---
title: If and conditions
audience: designer, developer
category: templates
order: 50
summary: Conditional output with if, elseif and else; the comparison and logic operators used in conditions (eq, ne, gt, lt, and, or, not); testing for content, emptiness and existence; and conditions inside attribute values.
keywords: if, elseif, else, condition, eq, ne, gt, lt, ge, le, and, or, not, is_set, has_content, count, empty, conditional output
type: help
---
# If and conditions

This page is for designers who show or hide parts of a template. It covers the `{if}` function and the operators that
build conditions.

## Contents

1. [The if function](#the-if-function)
2. [Building conditions](#building-conditions)
3. [Common tests](#common-tests)
4. [Inside attributes](#inside-attributes)
5. [cond and first_set instead](#cond-and-first_set-instead)

## The if function

```tpl
{if $node.data_map.image.has_content}
    {attribute_view_gui attribute=$node.data_map.image}
{elseif $node.data_map.video.has_content}
    {attribute_view_gui attribute=$node.data_map.video}
{else}
    <p class="no-media">{'No media'|i18n( 'design/mytheme' )}</p>
{/if}
```

(`eZTemplateIfFunction`, with the attributes `elseif` and `else`.)

## Building conditions

Conditions are operators ([Logic and comparison operators](logic-and-comparison-operators.md)):

| Operator | True when |
|---|---|
| `eq( $a, $b )` | equal |
| `ne( $a, $b )` | not equal |
| `gt`, `ge`, `lt`, `le` | greater, greater or equal, less, less or equal |
| `and( $a, $b, ... )` | all true |
| `or( $a, $b, ... )` | at least one true |
| `not( $a )` or `$a\|not` | false |

Pipe form works too: `$count|gt( 10 )`, `$node.class_identifier|eq( 'article' )`. Plain infix comparisons
(`$a == $b`) are not part of the language; use the operators.

## Common tests

```tpl
{if $node.data_map.intro.has_content}          {* a field has content *}
{if $children|count}                           {* a list is not empty *}
{if is_set( $view_parameters.offset )}         {* a value exists *}
{if $current_user.is_logged_in}                {* a visitor is signed in *}
{if $list|contains( $node.node_id )}           {* an array contains a value *}
{if $node.class_identifier|eq( 'article' )}    {* class test *}
```

## Inside attributes

```tpl
<li class="menu-item{if eq( $item.node_id, $current_node_id )} is-current{/if}">
<a href={$item.url_alias|ezurl}{if eq( $item.node_id, $current_node_id )} aria-current="page"{/if}>
```

## cond and first_set instead

For a value rather than markup, `cond( condition, value, else_value )` is shorter
([cond and first_set](cond-and-first-set.md)):

```tpl
<body class="{cond( $current_user.is_logged_in, 'signed-in', 'anonymous' )}">
```

## Related pages

- [Switch and case](switch-case.md)
- [Logic and comparison operators](logic-and-comparison-operators.md)
- [cond and first_set](cond-and-first-set.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
