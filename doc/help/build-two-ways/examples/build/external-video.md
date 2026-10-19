---
title: "An external video"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1116
summary: The External video block (Video URL or embed src, Width, Height, Autoplay), or a video content item.
keywords: video, YouTube, External video block, embed, autoplay, NG Video
type: help
---
# An external video

A video from a video service on a landing page.

*Life cycle: Build, example 17 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **External video** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Video URL or embed src**, **Width (px or %)** (default 100%), **Height (px)** (default 360) and **Autoplay**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The site has a video section (Video, node 164) with *NG Video* content; to show those, use a List block with a collection instead.

## With templates

1. Show a video content item with its own view, so the site's video consent handling applies; the theme loads `exp-youtube-consent.css` for that.

## Which to choose

A one-off video: the block. Videos that are content (with titles, descriptions, search): NG Video items.

## Check

- The video starts only when the visitor asks, unless Autoplay is set.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
