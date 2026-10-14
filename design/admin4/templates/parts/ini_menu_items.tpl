{*
   The links of a left menu (parts/ini_menu.tpl): items (hash key, url, name, state), current_uri_string, optional
   labelledby (the id of the menu's heading). The current page's link carries aria-current="page", which the
   stylesheet marks with the accent tint and a bar.
*}
<ul class="leftmenu-items"{if and( is_set( $labelledby ), $labelledby )} aria-labelledby="{$labelledby|wash}"{/if}>
{foreach $items as $item}
    {if eq( $item.state, 'disabled' )}
        <li><div><span class="disabled">{$item.name}</span></div></li>
    {elseif eq( $item.state, 'link' )}
        {* the page itself: aria-current="page"; a page below the link (a view of it): "true", the same mark *}
        {if eq( $current_uri_string, $item.url )}
        <li class="current"><div><a href={$item.url|ezurl} aria-current="page">{$item.name}</a></div></li>
        {elseif $current_uri_string|begins_with( concat( $item.url, '/' ) )}
        <li class="current"><div><a href={$item.url|ezurl} aria-current="true">{$item.name}</a></div></li>
        {else}
        <li><div><a href={$item.url|ezurl}>{$item.name}</a></div></li>
        {/if}
    {else}
        <li class="disabled-no-access"><div><span class="disabled">{$item.name}</span></div></li>
    {/if}
{/foreach}
</ul>
