---
title: Block view templates
audience: designer
category: theme-design
order: 730
summary: Writing the template of a layouts block: the variables it receives ($block, $zone, $module_result), block values and collection items, dispatching by view type, drawing the children of a container, and overriding the shipped templates from a design.
keywords: block template, view type template, $block, block.values, block.view_type, collection items, container children, placeholder, explayouts block, override block template, tpl_block
type: help
---
# Block view templates

This page is for designers who write or restyle the template of a layouts block. It answers: what a block template
receives, how it draws its items, and how a container draws its children.

![Layouts, Template editor: the block view templates of the active designs, to read before overriding one](../../images/layouts/layout-template-editor.webp)

## Contents

1. [Where block templates live](#where-block-templates-live)
2. [What a block template receives](#what-a-block-template-receives)
3. [A simple block](#a-simple-block)
4. [A list block with items](#a-list-block-with-items)
5. [Containers and their children](#containers-and-their-children)
6. [Template blocks](#template-blocks)
7. [Rules](#rules)

## Where block templates live

`explayouts/block/<definition identifier>.tpl`, found through the design cascade. The extension's own copies are in
`extension/explayouts/design/standard/templates/explayouts/block/`; a design replaces any of them by putting a file
at the same path, as the media design does in `design/media/templates/explayouts/block/`.

## What a block template receives

| Variable | Content |
|---|---|
| `$block.id` | the block's id |
| `$block.definition_identifier` | `title`, `list`, `two_columns` ... |
| `$block.view_type` | the view type the editor chose |
| `$block.name` | the block's name, if the editor gave one |
| `$block.values` | the block's parameters (for example `title`, `level`, `tag`, `link` of the title block) and, for blocks with a collection, `items` |
| `$block.parent_id`, `$block.placeholder` | for a child of a container: its parent and its place |
| `$zone` | the zone, with all its blocks (`$zone.blocks`) |
| `$module_result` | the page's module result (the `full_view` block prints its `content`) |

## A simple block

The shipped title block (`block/title.tpl`), shortened:

```tpl
<div class="slb slb-title" data-block-id="{$block.id|wash}">
    {if $block.values.title}
        {def $tag = cond( $block.values.tag|ne( '' ), $block.values.tag, concat( 'h', $block.values.level ) )}
        <{$tag} class="title">{$block.values.title|wash}</{$tag}>
        {undef $tag}
    {/if}
</div>
```

## A list block with items

Collection items of the shipped list block are nodes (`$block.values.items`):

```tpl
<div class="slb slb-list" data-block-id="{$block.id|wash}">
    {if $block.name}<h2 class="block-title">{$block.name|wash}</h2>{/if}
    {if $block.values.items|count}
    <ul class="block-list">
        {foreach $block.values.items as $item}
        <li>{node_view_gui content_node=$item view='line'}</li>
        {/foreach}
    </ul>
    {else}
    <p class="block-empty">{'No items found.'|i18n( 'design/mytheme/block' )}</p>
    {/if}
</div>
```

Drawing each item with `node_view_gui` and a view mode (`line`, or one of your own such as `card`) reuses the line
templates and overrides of your design ([node_view_gui](../templates/node-view-gui.md)).

## Containers and their children

A container block draws the blocks whose `parent_id` is its id, by placeholder. From the shipped `block/two_columns.tpl`:

```tpl
{foreach $zone.blocks as $child}
    {if and( is_set( $child.parent_id ), $child.parent_id|eq( $block.id ),
             is_set( $child.placeholder ), $child.placeholder|eq( 'left' ),
             is_set( $child.definition_identifier ), $child.definition_identifier|ne( '' ) )}
        {include uri=concat( 'design:explayouts/block/', $child.definition_identifier, '.tpl' )
                 block=$child zone=$zone module_result=$module_result}
    {/if}
{/foreach}
```

The zone templates draw only top-level blocks (`parent_id` 0); children are always drawn by their container.

## Template blocks

The `tpl_block` block names a template part (`block_name` parameter), drawn from `explayouts/tpl_block/<name>.tpl`.
Designs use it for parts that belong to the page but should be placeable by editors: the media design has
`article_header.tpl`, `article_body.tpl`, `article_media.tpl`; the starter design draws its header and footer this way.

## Rules

- Wash every value (`|wash`); block parameters are editor input.
- Keep the outer element with `data-block-id`: the layouts editor's preview uses it.
- No inline CSS or JavaScript; style by class ([Styling layout blocks](styling-layout-blocks.md)).
- A variable defined with `{def}` in an included template can clash with the includer's; `{undef}` what you define.

## Related pages

- [Block definitions](block-definitions.md)
- [Styling layout blocks](styling-layout-blocks.md)
- [Layouts for designers](layouts-for-designers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
