---
title: "A grid of articles in four columns"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1119
summary: The List block in view grid with Number of columns 4 (or the Grid block), item view type Card.
keywords: grid, columns, cards, number of columns, item view type, Grid block
type: help
---
# A grid of articles in four columns

Eight articles as cards, four per row.

*Life cycle: Build, example 20 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **List** (group *Listing*) into the zone `main`. (or **Grid**).
3. Fill the collection (manual or dynamic).
4. Select the block, open the **Design** tab and fill **View type** `grid`, **Number of columns** 4, **Item view type** Card.
5. **Publish layout**. The pages that use the layout show the block on their next view.

The media design draws the grid with `explayouts/block/list/grid.tpl` and `list/grid/4_columns.tpl`.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/explayouts/block/list/grid/4_columns.tpl` to `extension/mysite/design/media/templates/explayouts/block/list/grid/4_columns.tpl` and change your copy; every four-column grid changes ([How a block maps to its templates](../../block-to-template-map.md)).

## Which to choose

Columns are a Design option; change the markup of all four-column grids with the override.

## Check

- Four cards per row on a wide screen, fewer on a phone.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
