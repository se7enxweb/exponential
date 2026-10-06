{* In your pagelayout.tpl: draw the menus menu.ini [SelectedMenu] names.
   {menu name=TopMenu} includes design:menu/<TopMenu>.tpl, chosen when the page layout is compiled.
   ezwebin's menu templates also read $pagedata and $current_node_id; with that design, define them
   first, as its page layout does:
   {def $pagedata = ezpagedata() $current_node_id = $pagedata.node_id} *}
{def $menu_user = fetch( 'user', 'current_user' )}
{cache-block keys=array( $uri_string, $menu_user.role_id_list|implode( ',' ), $menu_user.limited_assignment_value_list|implode( ',' ) )}
<nav class="topmenu" aria-label="Main">
    {menu name=TopMenu}
</nav>
{if ezini( 'SelectedMenu', 'LeftMenu', 'menu.ini' )}
<nav class="leftmenu" aria-label="Section">
    {menu name=LeftMenu}
</nav>
{/if}
{/cache-block}
{undef $menu_user}
