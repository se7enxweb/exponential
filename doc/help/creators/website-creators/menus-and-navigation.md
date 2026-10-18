---
title: Menus and navigation
audience: website-creator, admin
category: website-creation
order: 150
summary: How menus are built on an Exponential site - from the content tree, limited by class lists in menu.ini, with breadcrumbs, hidden items, sort order and a template you can copy - and how to keep them fast.
keywords: menu, navigation, top menu, left menu, breadcrumb, path, menu.ini, TopIdentifierList, LeftIdentifierList, fetch content list, hidden, priority, sort, website creator
type: help
---
# Menus and navigation

This page is for website creators who build the navigation of a site. On an Exponential site the menus are not a
separate list you maintain: they are made from the content tree. A folder under the root becomes a menu entry, its
children become a submenu. You decide which classes count as menu entries and how they are sorted.

## Contents

1. [Menus come from the content tree](#menus-come-from-the-content-tree)
2. [Which classes appear: menu.ini](#which-classes-appear-menuini)
3. [A top menu template](#a-top-menu-template)
4. [Order, hiding and links to elsewhere](#order-hiding-and-links-to-elsewhere)
5. [Breadcrumbs](#breadcrumbs)
6. [Keeping menus fast](#keeping-menus-fast)
7. [Checklist](#checklist)

## Menus come from the content tree

| You want | You do |
|---|---|
| a new entry in the main menu | create a folder (or another menu class) directly under the root folder |
| a submenu | create children under that folder |
| an entry out of the menu, page still online | hide it, or use a class that is not in the menu lists |
| a different order | change the sort order of the parent, or the priorities of the children |

Editors therefore change the navigation by changing content, with no developer involved
([Structuring the content tree](structuring-the-content-tree.md)).

## Which classes appear: menu.ini

The ezwebin-based designs read the lists of `[MenuContentSettings]` in `menu.ini`. The kernel's `settings/menu.ini`
ships `folder`, `feedback_form`, `gallery` and `forum`; `extension/ezwebin/settings/menu.ini.append.php` adds more
(`documentation_page`, `forums`, `event_calendar`, `multicalendar`, `link`, `blog` ...). Set your own per siteaccess:

```ini
# settings/siteaccess/<site>/menu.ini.append.php
[MenuContentSettings]
TopIdentifierList[]
TopIdentifierList[]=folder
TopIdentifierList[]=landing_page
LeftIdentifierList[]
LeftIdentifierList[]=folder
LeftIdentifierList[]=article
```

The empty line first clears the inherited list. `[SelectedMenu]` and `AvailableMenuArray[]` (TopOnly, LeftOnly,
DoubleTop, LeftTop) choose which menu templates a design shows.

## A top menu template

When you write your own design, a top menu is a fetch of the root folder's children, limited to the menu classes:

```text
{def $root_node_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $menu_items = fetch( 'content', 'list', hash(
         'parent_node_id', $root_node_id,
         'class_filter_type', 'include',
         'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ),
         'sort_by', fetch( 'content', 'node', hash( 'node_id', $root_node_id ) ).sort_array ) )
     $path_ids = array()}
{foreach $module_result.path as $path_item}
  {if is_set( $path_item.node_id )}{set $path_ids = $path_ids|append( $path_item.node_id )}{/if}
{/foreach}
<nav aria-label="{'Main'|i18n( 'design/mysite' )}">
  <ul>
  {foreach $menu_items as $item}
    <li{if $path_ids|contains( $item.node_id )} class="current"{/if}>
      <a href={$item.url_alias|ezurl}{if $item.node_id|eq( $module_result.node_id )} aria-current="page"{/if}>{$item.name|wash}</a>
    </li>
  {/foreach}
  </ul>
</nav>
{undef $root_node_id $menu_items $path_ids}
```

The path items of a content page carry `node_id` (`kernel/classes/eznodeviewfunctions.php`), so `contains` marks the
menu entry the current page is in. Complete, working menus of the ezwebin-based designs are in
`extension/ezwebin/design/ezwebin/templates/menu/` (`flat_top.tpl`, `flat_left.tpl`, `double_top.tpl`).

`fetch( 'content', 'list', ... )` returns only content the visitor may read and, by default, not hidden content, so
the menu follows permissions by itself (Fetch functions).

## Order, hiding and links to elsewhere

- **Order** comes from the parent's sort field (name, published date, priority ...). For hand-made order, sort the
  root folder by priority and set the priorities in its sub-items list.
- **Hiding** a node takes it and its subtree off the site for visitors; editors still see it in the administration.
- **A menu entry that points elsewhere** (another site, a page deep in the tree): use a `link` class item with an
  `ezurl` field, and make the line template link to the field's value.

## Breadcrumbs

The path of the current page is in `$module_result.path`; each item has `text`, `url` and, for content, `url_alias`.
The simple theme's `parts/path.tpl` shows how to print it, with `{delimiter}` for the separators. Mark it up as a
`nav` with an `aria-label` ([Accessibility for site builders](accessibility-for-site-builders.md)).

## Keeping menus fast

Menus run on every page. Put them inside a `cache-block` whose keys include whatever changes the output (the
current address if you mark the active item, the user's role if menus differ by permission). Publishing clears the
template block cache, so new folders appear without a manual clear
([View cache and cache blocks](view-cache-and-cache-blocks.md)).

## Checklist

- [ ] The menu lists only classes meant for navigation.
- [ ] The root folder's sort order is the order you want.
- [ ] The current page is marked with `aria-current="page"` and its section in the menu with a class.
- [ ] Menus are in a cache block with the right keys.
- [ ] Hidden and restricted content does not show up for visitors (check as Anonymous).

## Related pages

- [The pagelayout: your site's frame](the-pagelayout.md)
- [Structuring the content tree](structuring-the-content-tree.md)
- [Classic menu settings](../../../guides/classic-menu-settings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
