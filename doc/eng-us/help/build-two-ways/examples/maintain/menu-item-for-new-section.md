---
title: "A new section joins the header menu"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1325
summary: Publish the section page, find its node ID, add it to MainMenuID[], clear Template & content.
keywords: new section, header menu, node ID, MainMenuID, maintenance
type: help
---
# A new section joins the header menu

The new "Yoga" section appears in the menu.

*Life cycle: Maintain, example 26 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With content and settings](#with-content-and-settings)
3. [With a tree-driven menu](#with-a-tree-driven-menu)
4. [Which to choose](#which-to-choose)
5. [Check](#check)

## In the Layouts editor

Not in the Layouts editor; give the section its layout by a mapping if needed.

## With content and settings

1. Publish the page; note `Node <id>`; `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] <id> siteaccess:site`; `./console exp:cache clear --tag=template,content`.

## With a tree-driven menu

1. If the header uses the tree-driven override ([A header menu that follows the tree](../../header-menu-from-the-tree.md)), publishing the section is enough.

## Which to choose

Fixed list: two commands. Tree-driven menu: nothing.

## Check

- "Yoga" is in the menu.

## Related pages

- [Add a header menu item](../../add-a-header-menu-item.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
