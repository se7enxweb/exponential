---
title: "Make an inventory of the page types"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1000
summary: Before building, list which kinds of pages the site has and which layout each gets: the content classes in the tree and the layout mappings.
keywords: inventory, page types, content classes, layout mappings, planning, audit
type: help
---
# Make an inventory of the page types

Know every page type (home, landing page, article, recipe, topic, video, contact, legal page) and how it is drawn today.

*Life cycle: Plan, example 1 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [In the content tree and the console](#in-the-content-tree-and-the-console)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layout mappings**: each mapping names a layout, its targets and its conditions.
2. Note per mapping the class condition (for example `ng_recipe` for Full / Recipe) and the target (Location 89 for the home page).
3. Open **Layouts > Shared layouts** to see what all of them share (Header / Footer, Prefooter).

## In the content tree and the console

1. In **Content structure**, open the site root (Fit & Healthy) and note the classes of its children: Landing page, Topic, Category, Contact form, Article, Container.
2. Run `./console exp:layouts list` for the layouts with their mappings (column *Rules*) and links (column *Links to*).

## Which to choose

Both views are needed: the content classes say what exists, the mappings say how each is drawn.

## Check

- Every class that has pages appears in a mapping, or is knowingly left to Default Layout (path prefix `/`, priority 10).

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
