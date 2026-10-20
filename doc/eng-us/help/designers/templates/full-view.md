---
title: Full view
audience: designer, developer
category: templates
order: 510
summary: The full view, the template of a content page: what it receives ($node, $view_parameters, $language_code), a complete article example, the one h1, sub-items with paging, handing values to the pagelayout, caching rules, and per-class full views.
keywords: full view, node/view/full.tpl, page template, article page, $node, view_parameters, language_code, cache_ttl, persistent_variable, full view override
type: help
---
# Full view

This page is for designers who write the main template of content pages. When a visitor opens a page, the content
module renders the node in the `full` view mode; the pagelayout then frames the result.

## Contents

1. [What the template receives](#what-the-template-receives)
2. [A complete example](#a-complete-example)
3. [Children and paging](#children-and-paging)
4. [Talking to the pagelayout](#talking-to-the-pagelayout)
5. [Caching rules](#caching-rules)
6. [One full view per class](#one-full-view-per-class)

## What the template receives

From `kernel/classes/eznodeviewfunctions.php`: `$node`, `$viewmode`, `$language_code`, `$view_parameters` (the
`(name)/value` parts of the URL, for example `offset`), `$node_path`, `$collection_attributes` and `$validation` (for
information collector forms), and `$persistent_variable`.

## A complete example

```tpl
{* override/templates/full/article.tpl *}
<article class="article"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$language_code}>
    <header>
        <h1>{$node.name|wash}</h1>
        <p class="article-meta">
            <time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}">{$node.object.published|l10n( 'shortdate' )}</time>
        </p>
    </header>
    {if $node.data_map.intro.has_content}
        <div class="article-intro">{attribute_view_gui attribute=$node.data_map.intro}</div>
    {/if}
    {if $node.data_map.image.has_content}
        <figure>{attribute_view_gui attribute=$node.data_map.image image_class='large'}</figure>
    {/if}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

(Check the attribute identifiers of your classes. `parts/exp_content_direction.tpl` is the kernel's part that prints
` lang="..." dir="rtl"` for right-to-left content and nothing otherwise, from the locale code in `exp_locale_code`; see [Right-to-left designs](../theme-design/right-to-left-designs.md).)

## Children and paging

```tpl
{def $limit = 10
     $offset = first_set( $view_parameters.offset, 0 )|int
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'sort_by', $node.sort_array,
                                                 'offset', $offset, 'limit', $limit ) )
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{foreach $children as $child}{node_view_gui content_node=$child view='line'}{/foreach}
{include uri='design:navigator/google.tpl' page_uri=$node.url_alias item_count=$total view_parameters=$view_parameters item_limit=$limit}
```

([Pagination](pagination.md)). `offset` is part of the view cache key, so each page of the list is cached separately.

## Talking to the pagelayout

The full view runs before the pagelayout. To tell it something (hide the side column, add a body class), set the
[persistent variable](persistent-variable.md); to require a stylesheet only here, `ezcss_require`.

## Caching rules

The full view is view-cached (`CachedViewModes` includes `full`):

- nothing visitor-specific in it (names, baskets, "you are signed in");
- `currentdate()`-based output frozen until the next publish; set a short `cache_ttl` if needed
  ([Caching for designers](../theme-design/caching-for-designers.md)).

## One full view per class

The default `node/view/full.tpl` is generic; give each class its own with overrides
([Tutorial: override a full view](../theme-design/tutorial-override-a-full-view.md)).

## Related pages

- [View modes](view-modes.md)
- [Module result and content info](module-result-and-content-info.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
