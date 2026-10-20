---
title: "No breadcrumb on landing pages"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1421
summary: Landing page layouts link the shared header including the breadcrumb; unlink and rebuild, or hide it by CSS on those layouts.
keywords: breadcrumb, landing page, unlink, CSS, redesign
type: help
---
# No breadcrumb on landing pages

Landing pages without a breadcrumb.

*Life cycle: Redesign, example 22 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Option A: unlink the landing layouts' header zone and add the header template block alone.

## With templates

1. Option B: keep the link and hide the breadcrumb with CSS where the layout says it is a landing page (the layout wrapper is `zone-layout-layout2` for all of them, so add a CSS class via a block of the layout or the body).

## Which to choose

Option A is explicit but detaches those layouts from header changes.

## Check

- Landing pages have no breadcrumb; articles still do.

## Related pages

- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
