---
title: Header menu and the content tree
audience: editor, website-creator, admin, designer
category: layouts
order: 150
summary: What editors change in the header menu by working in the content tree - the item's text, its link, its place - and what needs the menu settings or a template change instead. The shipped menu is a fixed list, not a query.
keywords: header menu, content tree, rename menu item, move page, menu item text, URL alias, editor, fixed list, dynamic menu
type: help
---
# Header menu and the content tree

The header menu of the Fit & Healthy theme is a **fixed list** of node IDs (`menu.ini [SiteInfo] MainMenuID[]`), not a
query on the tree. Publishing a new folder under the site root does **not** add it to the menu. What the list points
to, though, is content, and editors control that part from the content tree.

## Contents

1. [What editors change from the tree](#what-editors-change-from-the-tree)
2. [What needs the menu settings](#what-needs-the-menu-settings)
3. [Which pages are in the menu now](#which-pages-are-in-the-menu-now)
4. [A menu that follows the tree](#a-menu-that-follows-the-tree)

## What editors change from the tree

| In the content tree | Effect on the header menu |
|---|---|
| Rename the page (its name field) and publish | The item shows the new name: the menu prints the node's name |
| Change the page's URL (URL alias) | The item links to the new URL: the menu prints the node's URL alias |
| Move the page to another place | The item stays and follows the page: a move keeps the node ID |
| Send the page to the trash | The item disappears: the template skips IDs whose node cannot be fetched |

Publishing a page clears the page caches for it. If an old name still shows on other pages, clear the **Content** cache
in the Clear cache box ([Caches after a change](caches-after-a-change.md)).

## What needs the menu settings

| Task | Where |
|---|---|
| Add a page to the menu | [Add a header menu item](add-a-header-menu-item.md) |
| Change the order | [Reorder or remove header menu items](reorder-or-remove-header-menu-items.md) |
| Take a page out of the menu but keep the page | Remove its ID from `MainMenuID[]` |
| A different menu per site or language | Each siteaccess has its own `menu.ini`; `exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>` |

## Which pages are in the menu now

On the Fit & Healthy site (siteaccess `site`) the list is:

| Node ID | Page | Content class | URL |
|---|---|---|---|
| 92 | Fitness | Landing page (`ng_landing_page`) | `/fitness` |
| 130 | Healthy eating | Landing page | `/healthy-eating` |
| 144 | Recipes | Category (`ng_category`) | `/recipes` |
| 163 | Running | Topic (`ng_topic`) | `/running` |
| 164 | Video | Landing page | `/video` |
| 26210 | Tour | Landing page | `/exponential-live` |

All six are children of the site root, **Fit & Healthy** (node 89). Which layout a page gets is decided by Layouts >
Layout mappings; the menu and the layouts are independent of each other.

## A menu that follows the tree

A menu that adds every new section by itself (for example "the children of the site root of the classes Landing page,
Category and Topic") is a **template** change: `pagelayout/header/main_menu.tpl` would fetch a list of children
instead of reading `menu.ini`. That is the classic template way of building and belongs in your own design extension,
so the theme stays untouched; see [Two ways to build](two-ways-to-build.md) for how the two ways combine. The
Layouts editor can show such a list inside a page today: a **List** block with a dynamic collection on a parent node.

## Related pages

- [How the header is built](how-the-header-is-built.md)
- [Find a node ID](find-a-node-id.md)
- [Header menu troubleshooting](header-menu-troubleshooting.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
