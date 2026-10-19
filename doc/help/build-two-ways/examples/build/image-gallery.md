---
title: "An image gallery from a folder"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1123
summary: The Gallery block (Parent node ID, Limit, Class filter, Image attribute identifier, Thumbnail size alias), or a gallery content item.
keywords: gallery, images, Gallery block, parent node, thumbnail size, folder
type: help
---
# An image gallery from a folder

Twelve photos from a folder of the Media library.

*Life cycle: Build, example 24 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Gallery** (group *Gallery*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Parent node ID**, **Limit** (default 12), **Class filter**, **Image attribute identifier** (default `image`), **Thumbnail size alias** (default `small`).
4. **Publish layout**. The pages that use the layout show the block on their next view.

The media design dispatches galleries to `explayouts/block/gallery/` (`slider`, `grid_gallery`, `thumb_gallery`, `sushi_bar`).

## With templates

1. The site's *NG Gallery* class (for example "10 Workout Terms You Need To Know" in Fitness) is a gallery as content, shown by its full view.

## Which to choose

Photos for a page: the block. A gallery that is a story of its own: an NG Gallery item.

## Check

- A new image in the folder shows up after publishing.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
