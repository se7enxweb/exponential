---
title: "A carousel of content items"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1128
summary: The Carousel block (Slides per view, Autoplay in ms, Image attribute identifier, plus the list options) with a collection.
keywords: carousel, Carousel block, slides per view, autoplay, collection
type: help
---
# A carousel of content items

Articles that slide by, three at a time.

*Life cycle: Build, example 29 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Carousel** (group *Gallery*) into the zone `main`.
3. Fill its collection (manual or dynamic).
4. Select the block, open the **Design** tab and fill **Slides per view** (default 1), **Autoplay (ms, 0 to disable)** and **Image attribute identifier** (default `image`).
5. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Copy `extension/explayouts/design/standard/templates/explayouts/block/carousel.tpl` to `extension/mysite/design/media/templates/explayouts/block/carousel.tpl` and change your copy; every block of that type changes on every layout ([How a block maps to its templates](../../block-to-template-map.md)).

## Which to choose

Keep Autoplay at 0 unless there is a pause control.

## Check

- Three items visible with Slides per view 3.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
