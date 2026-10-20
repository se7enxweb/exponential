---
title: "Alternating image and text rows"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1120
summary: The Zig-Zag (List) block, or the List block in view list_zigzag.
keywords: zigzag, alternating, image left right, list_zigzag, Zig-Zag (List)
type: help
---
# Alternating image and text rows

Three stories with the picture alternately left and right.

*Life cycle: Build, example 21 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Zig-Zag (List)** (group *Listing*) into the zone `main`.
3. Fill its collection.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The media design renders `list_zigzag` with `explayouts/block/list/list_zigzag.tpl`; the wrapper gets the class `list-zigzag`.

## With templates

1. Override `explayouts/block/list/list_zigzag.tpl` in your design extension to change the rows for every zig-zag list.

## Which to choose

The view is ready-made; override only to change its markup.

## Check

- The image side alternates from row to row.

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
