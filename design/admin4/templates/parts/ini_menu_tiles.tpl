{*
   The links of a left menu as an overview page of tiles (parts/ini_menu.tpl with menu_layout='tiles'; the Setup
   overview, setup/setupmenu.tpl). The links, their groups and the access checks are those of the menu: a link the
   user may not open is not here.

   Variables: groups (hash id, name, items; translated when the name is translated already), tiles (link key =>
   hash icon, text), tile_figures (a template drawing a tile's figure, '' for none), tile_id_prefix, i18n_section.
   A tile is a card with a Bootstrap icon, the link's name as its link (stretched over the card) and the description
   and figure as the link's description. A link with no entry in tiles (an extension's) takes the plug icon.
*}
{foreach $groups as $group}
<section class="a4-section a4-setup-tilegroup" aria-labelledby="{$tile_id_prefix|wash}-{$group.id|wash}">
    {if $group.name}
    <h2 class="a4-h2 a4-setup-tilegroup-title" id="{$tile_id_prefix|wash}-{$group.id|wash}">{if is_set( $group.translated )}{$group.name|wash}{else}{$group.name|d18n( $i18n_section )|wash}{/if}</h2>
    {else}
    <h2 class="a4-sr-only" id="{$tile_id_prefix|wash}-{$group.id|wash}">{'Functions'|i18n( 'design/admin/setup/setupmenu' )}</h2>
    {/if}
    <ul class="a4-setup-tiles">
    {foreach $group.items as $item}
        {if eq( $item.state, 'disabled' )}{continue}{/if}
        <li class="a4-card a4-setup-tile{if eq( $item.state, 'no-access' )} is-disabled{/if}">
            <span class="a4-setup-tile-icon" aria-hidden="true"><span class="a4-icon {if is_set( $tiles[ $item.key ] )}{$tiles[ $item.key ].icon|wash}{else}a4-bi-plug{/if}"></span></span>
            <span class="a4-setup-tile-body">
                {if eq( $item.state, 'link' )}
                <a class="a4-setup-tile-link" href={$item.url|ezurl}{if or( is_set( $tiles[ $item.key ] ), $tile_figures )} aria-describedby="{$tile_id_prefix|wash}-{$item.key|wash}-text"{/if}>{$item.name}</a>
                {else}
                <span class="a4-setup-tile-link">{$item.name}</span>
                {/if}
                <span class="a4-setup-tile-text" id="{$tile_id_prefix|wash}-{$item.key|wash}-text">
                    {if is_set( $tiles[ $item.key ] )}<span>{$tiles[ $item.key ].text|wash}</span>{/if}
                    {if $tile_figures}{include uri=$tile_figures figure_key=$item.key}{/if}
                </span>
            </span>
        </li>
    {/foreach}
    </ul>
</section>
{/foreach}
