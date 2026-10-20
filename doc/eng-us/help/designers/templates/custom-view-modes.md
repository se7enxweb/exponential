---
title: Custom view modes
audience: designer, developer
category: templates
order: 550
summary: Adding view modes of your own (card, teaser, hero, payload): a default node/view template for the mode, class overrides for it, using it from node_view_gui and layout blocks, reaching it by URL, caching, and the editor's embed choices.
keywords: custom view mode, new view mode, card view, teaser view, node/view custom, view mode template, payload view, view mode for blocks, AvailableViewModes
type: help
---
# Custom view modes

This page is for designers who need more shapes than `full`, `line` and `embed`: a card in a grid, a big teaser in a
hero, a compact item in a sidebar. A view mode is just a name with templates behind it.

## Contents

1. [Creating a view mode](#creating-a-view-mode)
2. [Using it](#using-it)
3. [By URL](#by-url)
4. [For editors' embeds](#for-editors-embeds)
5. [Examples in the shipped designs](#examples-in-the-shipped-designs)

## Creating a view mode

1. A default template for every class: `design/mytheme/templates/node/view/card.tpl`.

   ```tpl
   <article class="card">
       <h3 class="card-title"><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
   </article>
   ```

2. Overrides for classes that need more:

   ```ini
   [mytheme_card_article]
   Source=node/view/card.tpl
   MatchFile=card/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

3. Clear the template override cache.

Prefix the mode with nothing special, but avoid names the kernel already uses (`full`, `line`, `embed`, `embed-inline`,
`text`, `text_linked`, `plain`, `tiny`, `listitem`, `thumb`, `sitemap`, `print`, `pdf`).

## Using it

```tpl
<div class="card-grid">
{foreach $children as $child}{node_view_gui content_node=$child view='card'}{/foreach}
</div>
```

Layout block templates can draw their items in your mode too ([Block view templates](../theme-design/block-view-templates.md)).

## By URL

`/content/view/card/<node id>` renders the mode as a page. Only modes in `site.ini [ContentSettings] CachedViewModes`
are view-cached when opened this way.

## For editors' embeds

To let editors pick your mode when they embed content in rich text, add it to `content.ini [embed] AvailableViewModes[]`
in your extension's settings, and give it a `content/view/<mode>.tpl` too (embeds draw objects with `content_view_gui`).

## Examples in the shipped designs

The media design uses frameless modes for its own purposes: `payload` (the pagelayout prints only the content) and
`layout_preview` (a reduced frame), both read from `$module_result.content_info.viewmode` in its pagelayout.

## Related pages

- [View modes](view-modes.md)
- [Node templates by class](node-templates-by-class.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
