{def $liclass='unselected '}
    {if eq( $navigationpart_identifier, $menu_item.navigationpart_identifier ) }
        {set $liclass='selected '}
    {/if}
    <li>
        {if and( $menu_item.enabled, or(is_unset( $menu_item.access ), $menu_item.access ) )}
            <a href={$menu_item.url|ezurl} class="{$liclass}{$menu_item.position} {$menu_item.navigationpart_identifier}" title="{$menu_item.tooltip}">{$menu_item.name|wash}</a>
        {else}
            <a href='#' class="disabled" title="{$menu_item.tooltip}">{$menu_item.name|wash}</a>
        {/if}
    </li>
{undef $liclass}

