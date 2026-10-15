{* The tabs of the header's main menu, each drawn by page_topmenuitem.tpl, with the mega menus of the tabs that have
   one. One template for every header that draws the tabs: admin4's page_topmenu.tpl and admin4l's tab menu block
   (design/admin4l/templates/explayouts/block/admin_tab_menu.tpl), so both have the same tabs and mega menus.

   Variables: navigationpart_identifier (the tab marked as current, parts/a4_navigation_part.tpl), ui_context,
   ui_context_edit (edit pages: no mega menu).

   The tabs with a mega menu (prototype, opt-in: menu.ini [TopAdminMenu] MegaMenuTabs[], empty by default; a tab takes
   part when its [Topmenu_<tab>] names a MegaMenuTemplate), for a user who may open the fragment (user/megamenu), not
   on edit pages: a hash of the tab's navigation part to the tab, read by page_topmenuitem.tpl. Only the button and an
   empty panel are drawn, and only on a tab the topmenu operator shows open to the user; a4_mega_menu.js loads the
   links when the panel is first opened, from user/megamenu/<tab>, which checks the tab and each link again. The
   header's cache block is keyed by the user's roles ($user_hash), as the tabs are. *}
{def $a4_mega_tabs = hash()}
{if and( or( is_unset( $ui_context_edit ), $ui_context_edit|not ), ezini_hasvariable( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' ) )}
    {if and( ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' )|count, fetch( 'user', 'can_open', hash( 'uri', 'user/megamenu' ) ) )}
        {foreach ezini( 'TopAdminMenu', 'MegaMenuTabs', 'menu.ini' ) as $a4_mega_tab}
            {if and( ezini_hasvariable( concat( 'Topmenu_', $a4_mega_tab ), 'MegaMenuTemplate', 'menu.ini' ),
                     ezini_hasvariable( concat( 'Topmenu_', $a4_mega_tab ), 'NavigationPartIdentifier', 'menu.ini' ) )}
                {set $a4_mega_tabs = $a4_mega_tabs|merge( hash( ezini( concat( 'Topmenu_', $a4_mega_tab ), 'NavigationPartIdentifier', 'menu.ini' ), $a4_mega_tab ) )}
            {/if}
        {/foreach}
    {/if}
{/if}
{foreach topmenu( $ui_context, true() ) as $menu}
    {include uri='design:page_topmenuitem.tpl' menu_item=$menu navigationpart_identifier=$navigationpart_identifier}
{/foreach}
{undef $a4_mega_tabs}
