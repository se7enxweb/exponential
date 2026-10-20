---
title: "A new shared band for many page types"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1410
summary: Create a shared layout (Single zone) for a band such as a newsletter sign-up, and link a zone of each page layout to it.
keywords: new shared layout, newsletter band, zone linking, redesign
type: help
---
# A new shared band for many page types

One newsletter band, shown on articles, topics and landing pages.

*Life cycle: Redesign, example 11 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Shared layouts > New shared layout**, type **Single zone**; build the band; publish.
2. In each page layout, link its `pre_footer` (or another free zone) to the new shared layout's `main`.

## With templates

1. A template block placed in Header / Footer's `pre_footer` would be the template way, but only layouts linking that zone would show it.

## Which to choose

A shared layout per band keeps each band editable on its own.

## Check

- `exp:layouts list` shows the new identifier under *Links to* for each page layout.

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
