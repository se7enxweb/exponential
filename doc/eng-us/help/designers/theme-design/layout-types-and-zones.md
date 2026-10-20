---
title: Layout types and zones
audience: designer
category: theme-design
order: 710
summary: The layout types defined in explayouts.ini (1 to 4 columns, hero, sidebars, featured, mosaic, header/main/footer, admin types), their zones, the template that draws each grid, and how to add a layout type of your own.
keywords: layout types, LayoutType, zones, Zones[], layout template, 1_column, 2_column, hero, sidebar_left, layout_2, header main footer, grid, add layout type, explayouts.ini
type: help
---
# Layout types and zones

This page is for designers who decide the grids editors can choose. A **layout type** fixes the zones of a layout and,
optionally, the template that places them. Editors pick the type when they create a layout and cannot change it later,
because blocks are attached to zones.

## Contents

1. [The shipped layout types](#the-shipped-layout-types)
2. [A layout type in the settings](#a-layout-type-in-the-settings)
3. [The layout type template](#the-layout-type-template)
4. [Adding a layout type](#adding-a-layout-type)

## The shipped layout types

From `extension/explayouts/settings/explayouts.ini.append.php`:

| Type | Name | Zones | Template |
|---|---|---|---|
| `1_column` | 1 column | `main` | `layouts/1_column.tpl` |
| `2_column` | 2 columns | `left`, `right` | `layouts/2_column.tpl` |
| `3_column` | 3 columns | `left`, `main`, `right` | `layouts/3_column.tpl` |
| `4_column` | 4 columns | `col1` to `col4` | `layouts/4_column.tpl` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` | `layouts/hero.tpl` |
| `sidebar_left`, `sidebar_right` | Sidebar left / right | `sidebar`, `main` | `layouts/sidebar_*.tpl` |
| `featured` | Featured | `hero`, `feature1` to `feature3`, `bottom` | `layouts/featured.tpl` |
| `mosaic` | Mosaic | `a` to `e` | `layouts/mosaic.tpl` |
| `layout_1` | Single zone | `main` | none |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` | none |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` | none |
| `admin_3col`, `admin_2col`, `admin_full` | Admin types (`Group=admin`) | `header`, `topmenu`, `left` (3col, 2col), `right` (3col), `main_top`, `main`, `main_bottom`, `footer` | none; drawn by the admin4l pagelayout |

A type without `Template=` has its zones drawn one under another by `layout.tpl`; your CSS places them.

## A layout type in the settings

```ini
[LayoutType_2_column]
Name=2 columns
# The template that draws the zones in their places (explayouts/layout.tpl uses it).
Template=explayouts/layouts/2_column.tpl
Zones[]=left
Zones[]=right
```

## The layout type template

`layouts/2_column.tpl` draws each zone in its place and lets `zone_blocks.tpl` draw the blocks:

```tpl
<div class="slb slb-2-column" data-layout-id="{$layout.id|wash}">
    {foreach $layout.zones as $zone}
        {if eq( $zone.identifier, 'left' )}
            <div class="sevenx-zone sevenx-zone-left" data-zone-id="{$zone.id|wash}">
                {include uri='design:explayouts/zone_blocks.tpl' zone=$zone module_result=$module_result}
            </div>
        {/if}
        ...
    {/foreach}
</div>
```

## Adding a layout type

In your design extension's `settings/explayouts.ini.append.php`:

```ini
[LayoutType_mytheme_split]
Name=Split: wide and narrow
Template=explayouts/layouts/mytheme_split.tpl
Zones[]=wide
Zones[]=narrow
```

and the template `design/mytheme/templates/explayouts/layouts/mytheme_split.tpl` in the style above. Use a prefix
of your theme in the identifier. Clear the INI cache and the template override cache; the type appears when editors
create a layout. The starter theme limits which layout types belong to its design with its own
`cjw_jumper.ini [LayoutSettings] LayoutTypes[]`.

## Related pages

- [Layouts for designers](layouts-for-designers.md)
- [Block definitions](block-definitions.md)
- [Shared layouts and linked zones](shared-layouts-and-linked-zones.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
