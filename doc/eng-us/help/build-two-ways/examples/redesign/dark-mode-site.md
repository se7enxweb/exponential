---
title: "Dark color scheme for the site"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1430
summary: CSS with prefers-color-scheme on the design's classes; no template or layout change.
keywords: dark mode, prefers-color-scheme, CSS, redesign
type: help
---
# Dark color scheme for the site

The site follows the visitor's dark setting.

*Life cycle: Redesign, example 31 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change; check every background color editors can choose.

## With templates

1. Add `@media (prefers-color-scheme: dark) { ... }` rules to your stylesheet for the page background, text and the `bg-color-*` classes.

## Which to choose

CSS.

## Check

- Text contrast at least 4.5:1 in both schemes.

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
