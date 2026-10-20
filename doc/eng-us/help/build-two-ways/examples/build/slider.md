---
title: "A slider of images"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1124
summary: The Slider block, with the same image source fields as the Gallery.
keywords: slider, slideshow, Slider block, images
type: help
---
# A slider of images

A slideshow at the top of a page.

*Life cycle: Build, example 25 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Slider** (group *Gallery*) into the zone `main`.
3. Select the block, open the **Design** tab and fill the source: **Parent node ID**, **Limit**, **Class filter**, **Image attribute identifier**, **Thumbnail size alias**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Override `explayouts/block/gallery/slider.tpl` of the media design in your extension to change the slides' markup.

## Which to choose

Respect `prefers-reduced-motion` in the styles if the slider moves by itself.

## Check

- The slides can be moved with the keyboard.

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
