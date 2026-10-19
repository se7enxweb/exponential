---
title: "A gallery with thumbnails"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1125
summary: The Thumb gallery block: a large image with thumbnails below.
keywords: thumb gallery, thumbnails, Thumb gallery block
type: help
---
# A gallery with thumbnails

A product-style gallery: one large picture, small ones to switch.

*Life cycle: Build, example 26 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Thumb gallery** (group *Gallery*) into the zone `main`.
3. Select the block, open the **Design** tab and fill the image source fields.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Override `explayouts/block/gallery/thumb_gallery.tpl` (media design) to change it everywhere.

## Which to choose

No code for the block; override only for a new look.

## Check

- Clicking a thumbnail changes the large image.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
