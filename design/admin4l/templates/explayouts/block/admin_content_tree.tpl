{* Admin block (explayouts): the content structure of the left menu. The left menus of the my, content, media and user
   navigation parts (admin_left_menu, parts/<part>/menu.tpl) already draw it, so on those pages this block draws
   nothing: both in one zone would show the tree twice. On every other page it draws the tree on its own. *}
{def $tree_part_name = first_set( $navigation_part.identifier, '' )}
{if $tree_part_name|eq( '' )}{set $tree_part_name = first_set( $module_result.navigation_part, '' )}{/if}
{if or( is_set( $module_result.left_menu ), array( 'ezmynavigationpart', 'ezcontentnavigationpart', 'ezmedianavigationpart', 'ezusernavigationpart' )|contains( $tree_part_name )|not )}
    {include uri='design:parts/content/menu.tpl'}
{/if}
{undef $tree_part_name}
