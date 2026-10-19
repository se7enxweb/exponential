---
title: "Plan the header and footer menus"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1008
summary: On the Fit & Healthy theme the header menu and the footer's first line are menu.ini lists and the footer's second line is content; plan who maintains which.
keywords: menus, header menu, footer menu, MainMenuID, FooterMenuID, footer links, planning
type: help
---
# Plan the header and footer menus

Know before launch how every menu entry will be changed.

*Life cycle: Plan, example 9 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings and templates](#with-settings-and-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The Layouts editor does not hold these menus: the header and footer are template blocks.

## With settings and templates

1. Header: `menu.ini [SiteInfo] MainMenuID[]`. Footer first line: `FooterMenuID[]`. Footer second line: Menu items in **Media > Configuration > Footer links**.
2. For a menu that follows the tree, plan a template override of `pagelayout/header/main_menu.tpl` ([A header menu that follows the tree](../../header-menu-from-the-tree.md)).

## Which to choose

Lists of node IDs suit stable menus; content-driven menus suit sections editors add.

## Check

- Every menu entry of the design has a source in the plan.

## Related pages

- [Add a header menu item](../../add-a-header-menu-item.md)
- [Footer menu items](../../footer-menu-items.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
