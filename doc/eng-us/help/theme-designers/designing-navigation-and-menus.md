---
title: "Designing navigation and menus"
audience: theme-designer
category: theme-design
order: 19
summary: How theme designers design navigation for a site whose structure editors change - menus built from the content tree, depth and width limits, the current page, breadcrumbs, sub navigation, small screens, and what to agree with the developer and editors.
keywords: theme designer, navigation, main menu, sub menu, breadcrumbs, content tree, menu depth, mega menu, current page, mobile menu, information architecture
type: help
---
# Designing navigation and menus

This page is for theme designers who design the menus of a site. In most Exponential sites the main menu is not a
fixed list in a template: it is fetched from the **content tree**, so it changes when editors add, rename, hide or
reorder folders. A good navigation design works for the tree the site has today and for the one it will have next
year. The template side is in [Menus and navigation](../designers/templates/menus-and-navigation.md) and
[Breadcrumbs](../designers/templates/breadcrumbs.md).

## Contents

1. [Where menus come from](#where-menus-come-from)
2. [Designing for a tree that changes](#designing-for-a-tree-that-changes)
3. [Depth](#depth)
4. [The current page and the path](#the-current-page-and-the-path)
5. [Breadcrumbs](#breadcrumbs)
6. [Sub navigation](#sub-navigation)
7. [Small screens and keyboards](#small-screens-and-keyboards)
8. [What to agree before drawing](#what-to-agree-before-drawing)

## Where menus come from

| Menu | Usual source | Who changes it |
|---|---|---|
| Main menu | children of the site's root node, filtered by class (often folders) | editors, by adding, hiding or sorting folders |
| Sub navigation | children of the current section's top folder | editors |
| Breadcrumbs | the path of the current node | follows the tree |
| Footer links | a folder of links, or fixed template text | editors or developers |
| Utility links (sign in, language) | template and settings | developers |

Ask which source each menu of the site uses. A menu fixed in a template can be designed to the pixel; a menu fetched
from the tree must handle what editors do.

## Designing for a tree that changes

- **Number of items.** Draw the main menu with the current count, and with two more and two fewer. Decide what happens
  when items do not fit: wrap to a second line, move to a "More" menu, or switch to the small-screen menu earlier.
- **Label length.** Folder names are written by editors and translated. Draw the longest label in the longest language
  of the site ([Designing for many languages](designing-for-many-languages.md)).
- **Order.** The order follows the sort order editors set on the parent node. Do not design a menu that depends on a
  particular item being first or last.
- **Hidden items.** Editors can hide a node, and the menu fetch normally leaves it out; nothing in the design should
  depend on a particular item existing.

## Depth

Each level of a menu you design is a fetch, a cache and a test case. Most content sites need:

- one level in the main menu;
- the second level either in a dropdown or as sub navigation on the section's pages, not both;
- deeper levels only in sub navigation and breadcrumbs.

Large "mega menus" are possible, but they are content as well: who maintains the groupings and the short
descriptions? If the answer is "editors", the content model needs fields for it
([Mapping a design to content classes](mapping-a-design-to-content-classes.md)).

## The current page and the path

Mark both the current page and its ancestors: when a visitor reads an article inside "News", the "News" item in the
main menu shows that they are in that section. The mark must not rely on color alone, and the current link carries
`aria-current="page"` ([Designing interactive states](designing-interactive-states.md#current-and-selected)).

## Breadcrumbs

Breadcrumbs follow the node's path in the tree, which may be deeper and more technical than the menu. Decide:

- whether the front page is the first crumb and how it is labeled;
- how long paths shorten on small screens (often only the parent, as a "back to" link);
- that the current page is the last crumb, not a link.

## Sub navigation

Sub navigation (a side menu of the current section) helps in deep sections. Design it for the section with the most
children and for a section with none, in which case it should disappear rather than show an empty box.

## Small screens and keyboards

- At narrow widths the main menu moves behind a menu button. Design the open state, the close control and what
  happens to the second level (an expanding list is easier than nested panels).
- Every level must be reachable with the keyboard, and dropdowns open on activation, not only on hover
  ([Keyboard and focus](../designers/theme-design/keyboard-and-focus.md#menus-disclosures-and-dialogs)).
- The navigation is a landmark (`nav` with a label) and the menu button announces whether it is expanded
  ([Landmarks and headings](../designers/theme-design/landmarks-and-headings.md)).

## What to agree before drawing

- [ ] The source of each menu (tree, folder of links, template).
- [ ] Which classes appear in the main menu (usually folders only).
- [ ] The number of levels in each menu.
- [ ] How many items editors expect to add in the next year.
- [ ] Whether menus differ per siteaccess or language.

## Related pages

- [Menus and navigation](../designers/templates/menus-and-navigation.md)
- [Designing headers and footers](designing-headers-and-footers.md)
- [Cache-block](../designers/templates/cache-block.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
