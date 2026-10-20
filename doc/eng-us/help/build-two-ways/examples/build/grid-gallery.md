---
title: "A photo grid"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1126
summary: The Grid gallery block: all images in a grid.
keywords: grid gallery, photo grid, Grid gallery block
type: help
---
# A photo grid

All photos of an event in a grid.

*Life cycle: Build, example 27 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Grid gallery** (group *Gallery*) into the zone `main`.
3. Select the block, open the **Design** tab and fill the image source fields; **Limit** decides how many.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Override `explayouts/block/gallery/grid_gallery.tpl` (media design).

## Which to choose

Raise Limit for long galleries, or use a gallery content item.

## Check

- The grid wraps at narrow widths.

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
