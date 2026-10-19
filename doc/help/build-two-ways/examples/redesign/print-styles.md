---
title: "Print styles for articles"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1429
summary: Print rules belong in your stylesheet (@media print) on the classes the templates write.
keywords: print, @media print, stylesheet, articles, redesign
type: help
---
# Print styles for articles

Articles print without header, footer and teasers.

*Life cycle: Redesign, example 30 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change.

## With templates

1. In `mysite.css`: `@media print { .site-header, .site-footer, .zone-pre_footer { display: none; } }` - the classes come from `pagelayout/header.tpl`, `pagelayout/footer.tpl` and `explayouts/zone.tpl`.

## Which to choose

CSS.

## Check

- The print preview shows only the article.

## Related pages

- [Styling your blocks](../../styling-your-blocks.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
