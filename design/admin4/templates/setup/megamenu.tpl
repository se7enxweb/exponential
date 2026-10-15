{* The fragment of setup/megamenu: the groups and links of the Setup menu for admin4's mega menu (menu.ini
   [TopAdminMenu] MegaMenuTabs[], page_topmenuitem.tpl, a4_mega_menu.js), drawn without the page layout.

   The same source as the left menu and the Setup overview: parts/setup/menu.tpl hands [Leftmenu_setup] to
   parts/ini_menu.tpl as for the overview's tiles (menu_layout='tiles'), drawn by parts/a4_mega_menu.tpl
   (tiles_layout='mega'): the same links, names, groups, icons, descriptions and access checks, without figures.
   a4_mega_menu.js takes the element marked data-a4-mega-groups and nothing else.
   Guide: doc/features/6.0/admin4-design.md ("Mega menu") *}
{def $setup_menu_layout = 'tiles'
     $setup_tiles_layout = 'mega'}
<div class="a4-mega-groups" data-a4-mega-groups>
{include uri='design:parts/setup/menu.tpl'}
</div>
{undef $setup_menu_layout $setup_tiles_layout}
