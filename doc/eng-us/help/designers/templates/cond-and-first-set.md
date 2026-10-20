---
title: cond and first_set
audience: designer, developer
category: templates
order: 220
summary: Two operators that pick a value: cond (the first value whose condition is true, like a chain of ternaries) and first_set (the first value that is set), with typical uses for classes, defaults and fallbacks.
keywords: cond, first_set, ternary, conditional value, default value, fallback, choose value, inline if
type: help
---
# cond and first_set

This page is for designers who need a value that depends on a condition, without an `{if}` block. Both operators come
from `eZTemplateControlOperator` (`lib/eztemplate/classes/eztemplatecontroloperator.php`).

## Contents

1. [cond](#cond)
2. [first_set](#first_set)
3. [Which one](#which-one)

## cond

`cond( condition1, value1, condition2, value2, ..., default )` returns the value after the first true condition, or the
last argument when none is true:

```tpl
<body class="{cond( $current_user.is_logged_in, 'signed-in', 'anonymous' )}">

{def $badge = cond( $node.is_hidden, 'Hidden'|i18n( 'design/mytheme' ),
                    $node.object.published|gt( $recent ), 'New'|i18n( 'design/mytheme' ),
                    '' )}
```

The simple design's pagelayout uses
`cond( $current_user.is_logged_in, fetch( shop, basket ).is_empty, 1 )` to read the basket only for signed-in users.

## first_set

`first_set( $a, $b, ..., fallback )` returns the first argument that is set (defined), whatever its value:

```tpl
{def $offset = first_set( $view_parameters.offset, 0 )}
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

The admin pagelayouts use `first_set( $site.text_direction, 'ltr' )` so a page rendered by an older kernel without the
variable stays left to right.

## Which one

| You want | Use |
|---|---|
| a value depending on a condition | `cond` |
| a value that may not exist, with a default | `first_set` |
| the first non-empty text | `cond( $a\|trim\|ne( '' ), $a, $b )` (`first_set` would return an empty but set `$a`) |

## Related pages

- [If and conditions](if-and-conditions.md)
- [Logic and comparison operators](logic-and-comparison-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
