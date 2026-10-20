---
title: "A new component block for the redesign"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1406
summary: A new block type for a component the old design did not have, defined in INI with an existing handler.
keywords: new block type, component, handler, redesign, palette
type: help
---
# A new component block for the redesign

A "pricing table" component editors can drag in.

*Life cycle: Redesign, example 7 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The palette lists it in its Category group.

## With templates

1. Define `[BlockDefinition_pricing]` and `AvailableBlocks[]=pricing` with a handler that has the fields you need (`expLayoutsComponentBlockHandler` gives title, content, image, link, CSS class and ID), and `explayouts/block/pricing.tpl` ([Add a block type](../../add-a-block-type.md)).

## Which to choose

Reuse a handler when its fields fit; write one only for new fields.

## Check

- The block renders `ngl-pricing`.

## Related pages

- [Add a block type](../../add-a-block-type.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
