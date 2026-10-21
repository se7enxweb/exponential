---
title: Exponential Layouts in the ecosystem
audience: ecosystem, editor, designer, developer
category: ecosystem
order: 13
summary: Exponential Layouts' place in the ecosystem - the visual page builder that brings the Netgen Layouts concept to the Exponential kernel, its packages, who it is for, how it relates to Nexus and the DXP, and where to read more.
keywords: Exponential Layouts, page builder, layouts, zones, blocks, rules, Netgen Layouts, explayouts, Nexus, DXP, editors, designers
type: help
---
# Exponential Layouts in the ecosystem

**"A visual page builder for Exponential. It brings the Netgen Layouts concept to the Exponential kernel: a layout is
a saved arrangement of blocks in zones, and a rule decides which pages get it"** (`README.md`).

## Contents

1. [Its place in the ecosystem](#its-place-in-the-ecosystem)
2. [The packages](#the-packages)
3. [Across the product family](#across-the-product-family)
4. [Where to read more](#where-to-read-more)

## Its place in the ecosystem

- Required in `composer.json` since 6.0.15
  ([The product family at a glance](../../who-is-the-exponential-foundation.md#the-product-family-at-a-glance)).
- It ports a concept from another PHP project, Netgen Layouts, with credit
  ([How the project grows](../../who-is-the-exponential-foundation.md#how-the-project-grows)).
- It is for "Editors who rearrange pages without template work, and designers"
  ([Exponential Layouts](../../who-is-the-exponential-foundation.md#exponential-layouts)).

## The packages

`se7enxweb/explayouts` and the eleven `explayouts-*` packages: "Exponential Layouts, the visual page builder, its
editor, API, content browser and queries" ([Who is 7x?](../../who-is-7x.md#the-extension-ecosystem)).

## Across the product family

| Product | Layouts |
|---|---|
| Exponential 6 | Exponential Layouts (`explayouts`) |
| Exponential Platform Nexus | Ships the layouts editor: its `composer.json` requires `netgen/layouts-core`, `netgen/layouts-standard` and `netgen/layouts-ui` |
| Exponential Platform DXP | Requires `se7enxweb/layouts-core` but ships no layouts editor (no layouts UI or standard blocks) |

Source: [The products one by one](../../who-is-the-exponential-foundation.md#the-products-one-by-one).

## Where to read more

- For editors: [The layouts editor](../../layouts-editor.md).
- For designers: [Block definitions](../../designers/theme-design/block-definitions.md) and
  [Block view templates](../../designers/theme-design/block-view-templates.md).
- For advocates: [Talking points: Exponential Layouts](../../advocates/talking-points-exponential-layouts.md).

## Related pages

- [Products](README.md)
- [The layouts editor](../../layouts-editor.md)
- [Landing pages with layouts](../../customers/growth/landing-pages-with-layouts.md)
- [Upstream projects and forks](../php-stack/upstream-projects-and-forks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
