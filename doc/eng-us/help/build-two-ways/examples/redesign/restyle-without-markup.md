---
title: "Restyle blocks without touching markup"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1407
summary: Most redesigns need only CSS on the wrapper classes ngl-<type>, ngl-vt-<view type>, bg-color-*, whitespace-*.
keywords: CSS, restyle, wrapper classes, ngl-block, redesign, stylesheet
type: help
---
# Restyle blocks without touching markup

New colors, spacing and type, no template changes.

*Life cycle: Redesign, example 8 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Editors keep using the same options; the classes they set (CSS class) can be styled too.

## With templates

1. Add your stylesheet to the media design's list by overriding `pagelayout/head/style.tpl` ([Styling your blocks](../../styling-your-blocks.md)), and style the classes the wrapper already has.

## Which to choose

CSS first; override templates only where the structure must change.

## Check

- The packed CSS contains your rules.

## Related pages

- [Styling your blocks](../../styling-your-blocks.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
