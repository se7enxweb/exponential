---
title: Template functions, includes and cache blocks
audience: app-creator, developer
category: app-creation
order: 180
summary: The template language features an application uses most - include, the *_view_gui functions, def/set/undef, foreach, set-block, cache-block with keys and expiry, and how to structure templates of an extension.
keywords: template functions, include, node_view_gui, attribute_view_gui, content_view_gui, def, set, undef, foreach, set-block, cache-block, keys, expiry, subtree_expiry, templates, app creator
type: help
---
# Template functions, includes and cache blocks

This page is for application creators who write the templates of their extension. The template language is described
in [6. The template language in ten minutes](../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes);
this page collects what applications use most.

## Contents

1. [Variables](#variables)
2. [Includes and template parts](#includes-and-template-parts)
3. [The view functions](#the-view-functions)
4. [Loops and conditions](#loops-and-conditions)
5. [Cache blocks](#cache-blocks)
6. [Structuring an extension's templates](#structuring-an-extensions-templates)

## Variables

| Function | Use |
|---|---|
| `{def $a = ...}` | declare (once per template) |
| `{set $a = ...}` | change |
| `{undef $a}` | free; undefine what you defined at the end of a template |
| `{set-block variable=$html}...{/set-block}` | capture output into a variable |

## Includes and template parts

```text
{include uri='design:expdemo/parts/item_card.tpl' item=$item show_image=true()}
```

Parameters become variables of the included template; it is found through the design chain, so sites can override it.
Use parts for markup that repeats.

## The view functions

| Function | Renders |
|---|---|
| `{node_view_gui content_node=$node view='line'}` | a node with `node/view/line.tpl` or its override |
| `{content_view_gui content_object=$object view='embed'}` | an object |
| `{attribute_view_gui attribute=$node.data_map.body}` | an attribute with its datatype's template |
| `{attribute_edit_gui attribute=$attribute}` | an attribute's edit field |

## Loops and conditions

```text
{foreach $items as $index => $item}
  {if $index|eq( 0 )}<h2>{$item.title|wash}</h2>{else}<p>{$item.title|wash}</p>{/if}
  {delimiter}<hr>{/delimiter}
{/foreach}
```

`{foreach $list as $item max 5 offset 2}` limits the loop; `{break}` and `{continue}` work as in PHP.

## Cache blocks

```text
{cache-block keys=array( $item.id, $current_user.contentobject_id ) expiry=600}
  ... expensive output ...
{/cache-block}
```

| Parameter | Meaning |
|---|---|
| `keys` | what the output depends on |
| `expiry` | seconds; the default is 7200 (two hours, `eZTemplateCacheFunction::DEFAULT_TTL`); `0` never expires by time |
| `subtree_expiry` | cleared when content below that node or address is published |
| `ignore_content_expiry` | not cleared by publishing |

Data from your own tables is not tied to publishing: set an `expiry` that matches how stale it may be, or clear the
`template-block` cache when your data changes ([Caching in applications](caching-in-applications.md)).

## Structuring an extension's templates

```text
extension/expdemo/design/standard/templates/expdemo/
├── list.tpl            one per view
├── item.tpl
└── parts/item_card.tpl reusable parts
```

- One template per view, named after it.
- Parts in `parts/`; override templates (per class) in `override/templates/`.
- No inline `<script>` or `<style>`, no `style=""`: CSS and JS go through `design.ini` lists
  ([Stylesheets and scripts](../website-creators/stylesheets-and-scripts.md)).
- `|wash` on every printed value; `|i18n( 'extension/expdemo' )` on every text.
- Check each with `php bin/php/eztemplatecheck.php`.

## Related pages

- [View results, templates and the pagelayout](view-results-and-the-pagelayout.md)
- [Template function pattern](../../../standards/patterns/template-function.md)
- [View cache and cache blocks](../website-creators/view-cache-and-cache-blocks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
