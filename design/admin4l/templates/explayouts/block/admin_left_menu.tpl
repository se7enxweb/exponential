{* Admin block (explayouts): what admin4's page_leftmenu.tpl puts inside #leftmenu-design. The part parameter forces a
   navigation part; empty is the one of the page. The #leftmenu wrapper belongs to the zone. *}
{if and( is_set( $block.values.part ), $block.values.part|ne( '' ) )}
    {include uri=concat( 'design:parts/', $block.values.part, '/menu.tpl' )}
{elseif is_set( $module_result.left_menu )}
    {include uri=$module_result.left_menu}
{else}
    {* the page's navigation part, or Setup's for a page of a tab the Setup menu stands in for *}
    {def $navigation_part_name = ''}
    {set-block variable=navigation_part_name}{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}
    {set $navigation_part_name = $navigation_part_name|trim}
    {def $extract_length = sub( count_chars( $navigation_part_name ), '14' )
         $part_name = $navigation_part_name|extract( '2', $extract_length )}
    {* the same rule as admin4's page_leftmenu.tpl: a kernel navigation part with the exp prefix, named in
       $kernel_exp_parts: exp<name>navigationpart => parts/<name>/menu.tpl; an extension's part without the ez prefix:
       <name>navigationpart => parts/<name>/menu.tpl *}
    {def $kernel_exp_parts = hash( 'expauditnavigationpart', 'audit' )}
    {if is_set( $kernel_exp_parts[ $navigation_part_name ] )}{set $part_name = $kernel_exp_parts[ $navigation_part_name ]}
    {elseif $navigation_part_name|begins_with( 'ez' )|not}{set $part_name = $navigation_part_name|explode( 'navigationpart' )[0]}{/if}
    {undef $kernel_exp_parts}
    {include uri=concat( 'design:parts/', $part_name, '/menu.tpl' )}
    {undef $extract_length $part_name $navigation_part_name}
{/if}
