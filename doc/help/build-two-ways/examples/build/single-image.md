---
title: "One image with a link"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1110
summary: The Image block (Image URL, Alt text, Link URL), or an image attribute printed in a template.
keywords: image, picture, Image block, alt text, link, banner image
type: help
---
# One image with a link

One picture, linked to a page.

*Life cycle: Build, example 11 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Image** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Image URL**, **Alt text** and **Link URL**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

Image URL takes an address; an image from the Media library is addressed by its stored file (see the Hero walkthrough, which used a `/var/<site>/storage/images/...` path).

## With templates

1. Print the image field of a content item with an image alias, so the size is generated for you:

```smarty
{def $article = fetch( 'content', 'node', hash( 'node_id', 93 ) )}
<a href={$article.url_alias|ezurl}>{attribute_view_gui attribute=$article.data_map.image image_class=i480}</a>
{undef $article}
```

## Which to choose

The template way follows the content (a new picture on the article shows everywhere); the block holds one fixed address.

## Check

- The image has alternative text.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
