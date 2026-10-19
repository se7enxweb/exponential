---
title: "New markup for every grid"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1400
summary: Override the media design's list grid template in your design extension; every List block in grid view changes.
keywords: override, grid, list/grid.tpl, block template, redesign, design extension
type: help
---
# New markup for every grid

Cards in grids get a new structure, on every layout at once.

*Life cycle: Redesign, example 1 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Nothing changes in the layouts: every List block with view `grid` uses your template.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/explayouts/block/list/grid.tpl` to `extension/mysite/design/media/templates/explayouts/block/list/grid.tpl`.
2. Change the markup of the grid; the column variants are in `explayouts/block/list/grid/<n>_columns.tpl`.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

A redesign of a block's look is a template task; the layouts stay as they are.

## Check

- A grid on the home page and one in the Prefooter both show the new markup.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Your own design extension](../../your-own-design-extension.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
