---
title: "A map on the contact page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1115
summary: The Map block (Map embed URL, Width, Height) on the contact page's layout.
keywords: map, Map block, embed, contact page, location, iframe
type: help
---
# A map on the contact page

A map with the studio's address above the contact form.

*Life cycle: Build, example 16 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *the layout of the contact page*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Map** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Map embed URL** (the embed address the map service gives), **Width** (default `100%`) and **Height (px)** (default 400).
4. **Publish layout**. The pages that use the layout show the block on their next view.

Contact (node 185) is a *Contact form* page; check **Layouts > Layout mappings** for which layout it gets, or give it one ([Which layout does a page get?](../../layout-mappings.md)).

## With templates

1. Add the `<iframe>` of the map to the full view template of the contact form in your design extension; mind the site's Content Security Policy for frames.

## Which to choose

The block keeps the map with the page layout and editable.

## Check

- The map loads, and the page still works if it does not (keyboard, no console errors).

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
