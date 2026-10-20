---
title: "Move inline styles into the stylesheet"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1528
summary: style="" and <style> in old templates go into your stylesheet, packed with the theme's.
keywords: inline styles, stylesheet, cleanup, CSP, migration
type: help
---
# Move inline styles into the stylesheet

Templates without inline CSS, styles cached.

*Life cycle: Migrate, example 29 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Blocks with typed styles in HTML snippets: replace them too.

## With templates

1. Replace each `style=""` by a class; add the rules to `mysite.css`; add the file to the theme's list via your `pagelayout/head/style.tpl` override.

## Which to choose

One stylesheet, no inline CSS.

## Check

- No `style=` left in your templates.

## Related pages

- [Styling your blocks](../../styling-your-blocks.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
