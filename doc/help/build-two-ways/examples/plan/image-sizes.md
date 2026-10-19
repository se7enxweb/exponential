---
title: "Plan image sizes"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1020
summary: Galleries take a thumbnail size alias, templates an image class; plan which sizes the design needs.
keywords: image sizes, image alias, thumbnail size, image_class, i480, small
type: help
---
# Plan image sizes

Images are generated in the sizes the design shows, not scaled in the browser.

*Life cycle: Plan, example 21 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The gallery blocks (Gallery, Slider, Thumb gallery, Grid gallery, Sushi bar, Logos) have **Thumbnail size alias** (default `small`).

## With templates

1. Templates choose the alias with `{attribute_view_gui attribute=$node.data_map.image image_class=i480}`; the media theme uses aliases such as `i480` (its image files end in `_i480.jpg`).
2. New aliases are defined in `image.ini` of your extension.

## Which to choose

Reuse the theme's aliases; add one only for a new size.

## Check

- The image files in the page are of the planned size.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
