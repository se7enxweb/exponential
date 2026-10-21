---
title: "A block type of your own"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1134
summary: When editors need a new kind of block again and again, define it once: definition, handler, template.
keywords: new block type, block definition, handler, Notice, explayouts.ini
type: help
---
# A block type of your own

A "Notice" block in two looks (info, warning) that editors drag in like any other.

*Life cycle: Build, example 35 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [With templates](#with-templates)
2. [Then in the Layouts editor](#then-in-the-layouts-editor)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## With templates

1. Define `[BlockDefinition_notice]` and `AvailableBlocks[]=notice` in your extension's `explayouts.ini.append.php` (since explayouts 1.4.42 the extension's place in `ActiveExtensions[]` does not matter for blocks).
2. Write `explayouts/block/notice.tpl`.
3. Clear Template & content and INI caches.

## Then in the Layouts editor

1. The palette's *Basic* group lists **Notice**.
2. Drag it in, choose the view type and type the text on the **Design** tab.
3. **Publish layout**. The pages that use the layout show the block on their next view.

## Which to choose

Worth it as soon as the same markup would otherwise be copied into many HTML snippets.

## Check

- The block renders `ngl-notice ngl-vt-<view type>`.

## Related pages

- [Add a block type](../../add-a-block-type.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
