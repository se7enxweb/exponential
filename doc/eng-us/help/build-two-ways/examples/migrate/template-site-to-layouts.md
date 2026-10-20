---
title: "Migrate a template-built site to Layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1513
summary: Keep the design; turn the page layout's parts into template blocks of a shared Header / Footer; add page layouts per class; map them.
keywords: migration, template site, layouts, template blocks, shared layout
type: help
---
# Migrate a template-built site to Layouts

An existing site gets the Layouts editor without a redesign.

*Life cycle: Migrate, example 14 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Create a shared Header / Footer with those template blocks; create a layout per page type linking `header` and `footer`; map each by class; add blocks where editors need them.

## With templates

1. Save the header, breadcrumb and footer parts as templates the Template block can name (`explayouts/tpl_block/<name>.tpl`), or map them by place as the media theme does.

## Which to choose

This is how the Fit & Healthy theme is built: template blocks carry the old chrome.

## Check

- Every page looks as before; editors can add blocks in `main`.

## Related pages

- [Your own template blocks](../../template-blocks.md)
- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
