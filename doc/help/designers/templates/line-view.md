---
title: Line view
audience: designer, developer
category: templates
order: 520
summary: The line view, the template of a content item in a list: what it receives, a teaser example with image, title, date and intro, parameters from the caller, headings inside lists, and per-class line views.
keywords: line view, node/view/line.tpl, list item, teaser, card, list template, node_view_gui line, line override, item template
type: help
---
# Line view

This page is for designers who style items in lists: children of a folder, search results, latest news. The `line` view
mode draws one item; [node_view_gui](node-view-gui.md) calls it for each node.

## Contents

1. [What it receives](#what-it-receives)
2. [A teaser example](#a-teaser-example)
3. [Headings and links](#headings-and-links)
4. [Per class](#per-class)

## What it receives

`$node`, `$view_parameters` (offset 0), and any parameter the caller passes
(`{node_view_gui content_node=$child view='line' image_class='small'}`). The kernel's default
`design/standard/templates/node/view/line.tpl` prints an icon and the linked name.

## A teaser example

```tpl
{* override/templates/line/article.tpl *}
{default image_class='small' heading='h3'}
<article class="teaser">
    {if $node.data_map.image.has_content}
        <div class="teaser-media">{attribute_view_gui attribute=$node.data_map.image image_class=$image_class}</div>
    {/if}
    <{$heading} class="teaser-title"><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></{$heading}>
    <p class="teaser-meta"><time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}">{$node.object.published|l10n( 'shortdate' )}</time></p>
    {if $node.data_map.intro.has_content}
        <div class="teaser-intro">{$node.data_map.intro.content.output.output_text|strip_tags|shorten( 160 )|wash}</div>
    {/if}
</article>
```

## Headings and links

- The heading level depends on where the list sits: an `h2` list title means `h3` items. Let the caller pass it
  (`heading` above).
- One link per item, on the title; a card whose whole surface is clickable can stretch the title link with CSS instead
  of wrapping everything in `<a>`.

## Per class

An override with `Source=node/view/line.tpl` and `Match[class_identifier]=article` gives articles their line
template ([Node templates by class](node-templates-by-class.md)). Keep the markup of all line templates similar, so lists
mixing classes look consistent.

## Related pages

- [View modes](view-modes.md)
- [node_view_gui](node-view-gui.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
