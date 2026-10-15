{* The fragment of user/megamenu/<tab>: the groups and links of a top tab's menu for admin4's mega menu (menu.ini
   [TopAdminMenu] MegaMenuTabs[], page_topmenuitem.tpl, a4_mega_menu.js), drawn without the page layout.

   $mega_template is the tab's [Topmenu_<tab>] MegaMenuTemplate, checked by the view (a design: template of a tab
   named in MegaMenuTabs[] and open to the user). It draws the tab's menu as the mega menu's groups: parts/ini_menu.tpl
   with menu_layout='tiles' and tiles_layout='mega' (parts/a4_mega_menu.tpl), with the menu's names, groups, icons,
   descriptions and access checks, without figures: parts/setup/mega_menu.tpl for Setup, parts/user/mega_menu.tpl for
   Users. a4_mega_menu.js takes the element marked data-a4-mega-groups and nothing else.
   Guide: doc/features/6.0/admin4-design.md ("Mega menu") *}
<div class="a4-mega-groups" data-a4-mega-groups>
{include uri=$mega_template}
</div>
