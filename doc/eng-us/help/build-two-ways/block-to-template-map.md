---
title: How a block maps to its templates
audience: designer, developer, website-creator
category: build-two-ways
order: 550
summary: The connection between a block in the Layouts editor and the files that render it - block definition, view type, item view type, the media design's dispatch templates and the standard fallback - with the table of every block template of the Fit & Healthy theme and how to override one.
keywords: block template, view type, item view type, block.tpl, zone.tpl, layout.tpl, explayouts/block, dispatcher, override, BlockDefinition, sevenx_themes_media, explayouts standard design, template path
type: help
---
# How a block maps to its templates

Every block you see in the editor is drawn by templates. Knowing which ones lets you change any block's markup, for
every page at once, without touching the layouts.

## Contents

1. [The chain](#the-chain)
2. [Where each block type is rendered](#where-each-block-type-is-rendered)
3. [View types and item view types](#view-types-and-item-view-types)
4. [Override one](#override-one)
5. [What the block header in the editor tells you](#what-the-block-header-in-the-editor-tells-you)

## The chain

For a page with a layout, on the media design (paths below `extension/sevenx_themes_media/design/media/templates/`):

| Step | Template | Does |
|---|---|---|
| 1 | `pagelayout.tpl` | resolves the page's layout and includes `explayouts/layout.tpl` |
| 2 | `explayouts/layout.tpl` | places zones `header` before `<main>`, `footer` after it, the rest inside |
| 3 | `explayouts/zone.tpl` | `<section class="zone zone-<identifier>">`, one include per top-level block |
| 4 | `explayouts/block/block.tpl` | the wrapper `<div class="ngl-block ngl-<type> ngl-vt-<view type> ...">`, spacing, background, CSS class and ID, container; then includes step 5 |
| 5 | `explayouts/block/<block type>.tpl` | the block itself |
| 6 | `explayouts/block/<block type>/<view type>.tpl` | for block types whose template dispatches by view type (List, Title, Gallery ...) |
| 7 | `content/views/<item view type>/...` | how each item of a list is shown |

If the media design has no `explayouts/block/<block type>.tpl`, the next design in line answers: Layouts' own
`extension/explayouts/design/standard/templates/explayouts/block/<block type>.tpl`.

## Where each block type is rendered

| Block type (editor name) | Media design | Dispatch by view type |
|---|---|---|
| `title` (Title) | `explayouts/block/title.tpl` | `title/`: `title`, `title_centered`, `centered`, `section`, `section_centered` |
| `list` (List) | `explayouts/block/list.tpl` | `list/`: `list`, `grid`, `grid_featured`, `list_zigzag`, `list_accordion`, `numbered`, `zigzag`, `accordion`, `mini`, `running`, `eatwell` (plus `list_items.tpl`; the grid by columns in `list/grid/`: `2_columns`, `3_columns`, `4_columns`, `6_columns`) |
| `gallery` (Gallery) | `explayouts/block/gallery.tpl` | `gallery/`: `slider`, `grid_gallery`, `thumb_gallery`, `sushi_bar` |
| `column` (Column), `two_columns` | `column.tpl`, `two_columns.tpl` | each child block through `explayouts/block/block.tpl` again (nested) |
| `tpl_block` (Template block) | `tpl_block.tpl` | by template name, see [Your own template blocks](template-blocks.md) |
| `button`, `rich_text`, `html_snippet` | `button.tpl`, `rich_text.tpl`, `html_snippet.tpl` | |
| `exp_component_hero`, `_about`, `_features`, `_lead`, `_logos`, `_quote` | `exp_component_<name>.tpl` | view types `<name>_style_1` ... |
| `product_highlight` | `product_highlight.tpl` | |
| every other type (text, image, map, card, alert, tabs ...) | not in the media design | Layouts' standard design: `extension/explayouts/design/standard/templates/explayouts/block/<type>.tpl` |

The block types and their view types are defined in `extension/explayouts/settings/explayouts.ini.append.php`
(`[BlockDefinition_<type>]`, `ViewTypes[]`).

## View types and item view types

- The **view type** (Design tab, *View type*) picks the block's own look; on the media design it names the dispatch
  template of step 6 and appears as `ngl-vt-<view type>` on the wrapper.
- The **item view type** (Design tab, *Item view type*, for lists: Standard, Standard (with intro), Overlay, Card, Card
  (with intro), Line, Mini, List item, List item (with intro)) picks how each item is drawn, from the content views
  under `content/views/` (`standard`, `overlay`, `card`, `line`, `mini`, `listitem` ...).

Some blocks imported with the theme carry view types that their INI definition does not list: the Prefooter's Title
uses `section_centered` while `[BlockDefinition_title]` lists only `default`. The template still exists and renders
them.

## Override one

Copy the template to the same path in your own design extension and change the copy, for example the grid of the List
block:

```text
extension/sevenx_themes_media/design/media/templates/explayouts/block/list/grid.tpl    the theme's
extension/mysite/design/media/templates/explayouts/block/list/grid.tpl                 yours (wins)
```

Then clear **Template & content**. Every List block in grid view on every layout uses your copy. Setup and the
extension order: [Your own design extension](your-own-design-extension.md).

## What the block header in the editor tells you

Each block in the editor has a header such as `GRID List` or `SECTION_CENTERED Title`: the **view type** in capitals,
then the **block type's name**. Those two give the template path directly: List in `grid` view is
`explayouts/block/list/grid.tpl`.

![The Prefooter in the editor: the headers read COLUMN Column, SECTION_CENTERED Title and GRID List](images/pf-02-prefooter-editor.webp)

## Related pages

- [Add a view type](add-a-view-type.md)
- [Add a block type](add-a-block-type.md)
- [How the header is built](how-the-header-is-built.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
