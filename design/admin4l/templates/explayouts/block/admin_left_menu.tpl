{* Admin block (explayouts): what admin4's page_leftmenu.tpl puts inside #leftmenu-design. The part parameter forces a
   navigation part; empty is the one of the page. The #leftmenu wrapper belongs to the zone. *}
{if and( is_set( $block.values.part ), $block.values.part|ne( '' ) )}
    {include uri=concat( 'design:parts/', $block.values.part, '/menu.tpl' )}
{elseif is_set( $module_result.left_menu )}
    {include uri=$module_result.left_menu}
{else}
    {def $navigation_part_name = $navigation_part.identifier}
    {if $navigation_part_name|eq('')}
        {set $navigation_part_name = $module_result.navigation_part}
    {/if}
    {def $extract_length = sub( count_chars( $navigation_part_name ), '14' )
         $part_name = $navigation_part_name|extract( '2', $extract_length )}
    {* a kernel navigation part with the exp prefix: exp<name>navigationpart => parts/<name>/menu.tpl *}
    {if $navigation_part_name|begins_with( 'exp' )}{set $part_name = $navigation_part_name|explode( 'navigationpart' )[0]|extract( 3 )}{/if}
    {include uri=concat( 'design:parts/', $part_name, '/menu.tpl' )}
    {undef $extract_length $part_name $navigation_part_name}
{/if}
