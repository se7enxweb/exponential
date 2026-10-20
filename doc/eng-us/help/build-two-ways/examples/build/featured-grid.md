---
title: "A grid with one featured item"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1122
summary: The List block in view grid_featured: the first item large, the rest in a grid.
keywords: grid_featured, featured item, magazine grid, List block
type: help
---
# A grid with one featured item

The lead story large, four more small.

*Life cycle: Build, example 23 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **List** (group *Listing*) into the zone `main`.
3. Fill the collection; the first item is the featured one.
4. Select the block, open the **Design** tab and fill **View type** `grid_featured`.
5. **Publish layout**. The pages that use the layout show the block on their next view.

The media design has `explayouts/block/list/grid_featured.tpl`.

## With templates

1. Override `explayouts/block/list/grid_featured.tpl` to change which item is featured or how.

## Which to choose

Order the items to choose the featured one; no code needed.

## Check

- The first item of the collection is the large one.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
