---
title: "A customer quote"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1104
summary: The Quote block with Quote text, Author, Source and Alignment, or the same in a template.
keywords: quote, testimonial, Quote block, author, source, alignment
type: help
---
# A customer quote

One sentence of praise, with the name of who said it.

*Life cycle: Build, example 5 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Quote** (group *Components*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Quote text**, **Author**, **Source** and **Alignment** (default `left`).
4. **Publish layout**. The pages that use the layout show the block on their next view.

The standard template writes `<div class="slb slb-quote quote-<alignment>">`.

## With templates

1. Copy `extension/explayouts/design/standard/templates/explayouts/block/quote.tpl` to `extension/mysite/design/media/templates/explayouts/block/quote.tpl` and change your copy; every block of that type changes on every layout ([How a block maps to its templates](../../block-to-template-map.md)). For example to add a `<cite>` around the source.

## Which to choose

The block for the quote itself; the override when every quote on the site should look different.

## Check

- `quote-left` (or your alignment) in the markup.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
