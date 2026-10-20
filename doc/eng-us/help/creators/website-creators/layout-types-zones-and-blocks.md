---
title: Layout types, zones and blocks
audience: website-creator, developer
category: website-creation
order: 290
summary: The twelve layout types and their zones, the 46 block types grouped by purpose, view types, block parameters, containers, and collections with offset, limit and pinning - a reference for planning layouts.
keywords: layout type, zones, blocks, block types, view type, list block, hero, full_view, tpl_block, container, collection, manual, dynamic, query, pinning, offset, limit, website creator
type: help
---
# Layout types, zones and blocks

This page is for website creators who plan layouts. It lists what ships, so you can choose a layout type and blocks
before you open the editor. Source: [Exponential Layouts](../../../bc/6.0/LAYOUTS.md); the layout types are defined in
`[LayoutType_*]` blocks of `extension/explayouts/settings/explayouts.ini.append.php`.

## Contents

1. [Layout types](#layout-types)
2. [Blocks by purpose](#blocks-by-purpose)
3. [View types and parameters](#view-types-and-parameters)
4. [Containers](#containers)
5. [Collections](#collections)
6. [Planning sheet](#planning-sheet)

## Layout types

Twelve ship as standard. The type is chosen at creation and cannot be changed, because blocks belong to zones.

| Identifier | Name | Zones |
|---|---|---|
| `1_column` | 1 column | `main` |
| `2_column` | 2 columns | `left`, `right` |
| `3_column` | 3 columns | `left`, `main`, `right` |
| `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
| `sidebar_left` | Sidebar left | `sidebar`, `main` |
| `sidebar_right` | Sidebar right | `main`, `sidebar` |
| `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
| `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
| `layout_1` | Single zone | `main` |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

Use `layout_1`, `layout_2` or `layout_4` for a whole page shell (they include header and footer zones), and the column
types for a content area.

## Blocks by purpose

Forty-six block types ship, grouped here as in the reference:

| Purpose | Block types |
|---|---|
| Text and markup | `text`, `title`, `rich_text`, `markdown`, `html`, `quote` |
| Media | `image`, `video`, `gallery`, `thumb_gallery`, `grid_gallery`, `slider`, `carousel`, `map` |
| Content listing | `list`, `grid`, `list_zigzag`, `list_accordion`, `sushi_bar`, `single`, `full_view` |
| Layout and structure | `two_columns`, `three_columns`, `four_columns`, `column`, `spacer`, `divider` |
| Interface elements | `button`, `card`, `accordion`, `tabs`, `alert`, `badge`, `progress` |
| Page sections | `hero`, `about`, `features`, `lead`, `logos` |
| Styled components | the component family for hero, features, about, logos, quote and lead, each with several styles |
| Templates | `tpl_block` |

Key blocks:

- `list` is the workhorse: six view types (`list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`,
  `grid_featured`). Change the view type before reaching for another block.
- `full_view` renders the current content item; most page-shell layouts need exactly one.
- `html` prints raw markup: give it only to trusted authors, and remember a Content Security Policy blocks inline
  scripts in it.
- `tpl_block` renders a named template a developer wrote; the escape hatch for anything special.

## View types and parameters

A block's **view type** is one of several shapes it renders in; its **parameters** configure it (title, number of
items, CSS class, link). The **CSS class** parameter lets you style a block with a class of your theme without a
template change.

## Containers

`two_columns`, `three_columns`, `four_columns` and `column` hold other blocks, so a zone can be split further. Keep
nesting shallow: two levels are easy to edit, four are not.

## Collections

Listing blocks own a collection:

| Kind | Use |
|---|---|
| Manual | hand-picked items through the content browser |
| Dynamic | a query (children of a node, a content search, tagged content ...) evaluated when the page renders |

**Offset** and **Limit** cut the result: a "featured" block with limit 1 and a "more" block with offset 1 and limit 6
make a lead-plus-list from one query. **Pinning** puts a hand-picked item at a fixed position of a dynamic collection
([Pinning](../../../bc/6.0/LAYOUTS.md#pinning-mixing-hand-picked-and-dynamic-content)).

## Planning sheet

| Page | Layout type | Zone | Blocks (type / view type) | Collection |
|---|---|---|---|---|
| `<front page>` | `layout_2` | `header` | linked to the shared header | |
| | | `main` | `hero`; `list` / `grid_featured` | dynamic: newest articles, limit 7 |
| | | `footer` | linked to the shared footer | |

## Related pages

- [Landing pages with the layouts editor](landing-pages-with-layouts.md)
- [Layout rules: which page gets which layout](layout-rules.md)
- [explayouts_ui_api](../../../features/6.0/extensions/explayouts_ui_api.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
