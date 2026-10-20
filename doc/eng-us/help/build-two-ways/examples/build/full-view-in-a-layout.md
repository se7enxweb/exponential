---
title: "The page's own content inside a layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1132
summary: The Full view block places the page's full view (the content itself) between other blocks.
keywords: full view, Full view block, page content, placeholder, article body
type: help
---
# The page's own content inside a layout

An article page whose layout adds blocks above and below the article itself.

*Life cycle: Build, example 33 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Full / Article*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Full view** (group *Placeholders*) into the zone `main`. Place it where the article should appear.
3. Add other blocks before or after it.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The Fit & Healthy article layouts use template blocks for the parts of the article (header, image, body); the Full view block is the one-block way.

## With templates

1. The full view itself is a template: the theme maps each class with `override.ini` (for example `node/view/full#ng_article` to `node/view/full/full_ng_article.tpl`).

## Which to choose

Layouts decide what surrounds the content; the full view template decides how the content looks.

## Check

- The article appears once, where the block is.

## Related pages

- [Build a campaign page, start to finish](../../build-a-campaign-page.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
