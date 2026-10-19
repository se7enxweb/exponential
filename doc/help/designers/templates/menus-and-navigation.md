---
title: Menus and navigation
audience: designer, developer
category: templates
order: 670
summary: Building a site's main menu, sub menus and footer links in templates: fetching the root's children with limits and class filters, marking the current page with aria-current, a second level, caching the menu, and accessible menu buttons for small screens.
keywords: menu, navigation, main menu, top menu, sub menu, current page, aria-current, nav, fetch children menu, menu cache, mobile menu, menu toggle
type: help
---
# Menus and navigation

This page is for designers who build the menus of a site. Menus are content: editors add a folder at the top level and
it appears in the menu. The template fetches the menu items and marks the current page.

## Contents

1. [The main menu](#the-main-menu)
2. [Marking the current page](#marking-the-current-page)
3. [A second level](#a-second-level)
4. [Caching the menu](#caching-the-menu)
5. [Small screens](#small-screens)

## The main menu

```tpl
{* pagelayout/menu.tpl *}
{def $root_node_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $menu_items = fetch( 'content', 'list', hash( 'parent_node_id', $root_node_id,
                                                   'sort_by', array( 'priority', true() ),
                                                   'class_filter_type', 'include',
                                                   'class_filter_array', array( 'folder', 'frontpage', 'landing_page' ),
                                                   'limit', 8 ) )}
<nav class="main-nav" aria-label="{'Main navigation'|i18n( 'design/mytheme' )}">
    <ul>
    {foreach $menu_items as $item}
        <li><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></li>
    {/foreach}
    </ul>
</nav>
{undef $root_node_id $menu_items}
```

`content.ini [NodeSettings] RootNode` is the root of the siteaccess's tree (the starter pagelayout reads it the same way).
Use the class identifiers of your installation.

## Marking the current page

Compare with the current page's path, which the pagelayout has in `$module_result.path`:

```tpl
{def $current_path_ids = array()}
{if is_array( $module_result.path )}
    {foreach $module_result.path as $step}{if is_set( $step.node_id )}{set $current_path_ids = $current_path_ids|append( $step.node_id )}{/if}{/foreach}
{/if}
...
<li{if $current_path_ids|contains( $item.node_id )} class="is-active"{/if}>
    <a href={$item.url_alias|ezurl}{if eq( $item.node_id, first_set( $module_result.node_id, 0 ) )} aria-current="page"{/if}>{$item.name|wash}</a>
</li>
```

`aria-current="page"` tells screen readers which item is the current page; the class styles the active branch.

## A second level

Fetch the children of the active top item only, not of every item:

```tpl
{foreach $menu_items as $item}
    {if $current_path_ids|contains( $item.node_id )}
        {def $sub_items = fetch( 'content', 'list', hash( 'parent_node_id', $item.node_id, 'sort_by', $item.sort_array, 'limit', 12 ) )}
        ...
        {undef $sub_items}
    {/if}
{/foreach}
```

## Caching the menu

The menu is the same for everyone with the same roles on the same page:

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=$root_node_id}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

([Cache-block](cache-block.md)). Menus of the ezwebin family are configured with `menu.ini [SelectedMenu]` instead
([The ezwebin design as a base](../theme-design/ezwebin-as-a-base.md)).

## Small screens

A menu button with `aria-expanded` and `aria-controls`, toggled by a script file, never by inline code
([No inline JavaScript](../theme-design/no-inline-javascript.md)); the menu stays usable without the script (for example
a list of links that the script collapses).

## Related pages

- [Breadcrumbs](breadcrumbs.md)
- [Fetching nodes and lists](fetching-nodes-and-lists.md)
- [Keyboard and focus](../theme-design/keyboard-and-focus.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
