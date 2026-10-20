---
title: "Launch a second site on the same installation"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1231
summary: Bold Agency and Starter run beside Fit & Healthy here: their own root nodes, siteaccesses, menus and layouts, with mappings by subtree and siteaccess.
keywords: multisite, second site, siteaccess, subtree, Bold Agency, Starter
type: help
---
# Launch a second site on the same installation

Another site with its own layouts, without disturbing the first.

*Life cycle: Launch, example 32 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Its layouts and shared layouts (Bold Agency - Home, Starter - C - Header / Footer ...).
2. Mappings with a **Subtree** target on its root and, if needed, a **Siteaccess** condition (the Starter mappings use `starter, starter_ger`).

## With templates

1. Its own siteaccess settings (`menu.ini`, `site.ini`) and, if the design differs, its own design extension.

## Which to choose

Separate shared layouts per site keep their chrome apart.

## Check

- Each site shows its own header.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
