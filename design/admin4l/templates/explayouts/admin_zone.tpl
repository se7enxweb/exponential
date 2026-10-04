{* admin4l: the blocks of one zone of an admin layout, in order. Parameters: layout (prepared layout),
   zone_identifier. A zone that is linked to a shared layout arrives with its blocks already resolved.
   The main zone always shows the module result: when no admin_module_result block is placed in it, the
   page's own main area is drawn after the blocks. *}
{def $has_result = false()}
{foreach $layout.zones as $zone}
    {if $zone.identifier|eq( $zone_identifier )}
        {foreach $zone.blocks as $block}
            {* optional parameters: only (draw just these definitions) and skip (draw all but these), for the page parts
               admin4 draws outside the zone's wrapper: context menu, debug marker, overlay *}
            {if and( is_set( $block.parent_id ), $block.parent_id|eq( 0 ), is_set( $block.definition_identifier ), $block.definition_identifier|ne( '' ),
                     or( is_set( $only )|not, $only|contains( $block.definition_identifier ) ),
                     or( is_set( $skip )|not, $skip|contains( $block.definition_identifier )|not ) )}
                {if $block.definition_identifier|eq( 'admin_module_result' )}{set $has_result = true()}{/if}
                {include uri=concat( 'design:explayouts/block/', $block.definition_identifier, '.tpl' ) block=$block zone=$zone}
            {/if}
        {/foreach}
    {/if}
{/foreach}
{if and( $zone_identifier|eq( 'main' ), $has_result|not )}{include uri='design:page_mainarea.tpl'}{/if}
{undef $has_result}
