---
title: "Restyle the hero"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1419
summary: Override explayouts/block/hero.tpl from Layouts' standard design in your design extension.
keywords: hero, override, standard design, redesign, block template
type: help
---
# Restyle the hero

The hero text over the image instead of below.

*Life cycle: Redesign, example 20 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Every Hero block changes; the editors' fields stay the same.

## With templates

1. Copy `extension/explayouts/design/standard/templates/explayouts/block/hero.tpl` to `extension/mysite/design/media/templates/explayouts/block/hero.tpl` (the media design has none, so the standard one is used today) and change it.
2. Clear **Template & content**.

## Which to choose

Templates.

## Check

- The campaign page shows the new hero.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
