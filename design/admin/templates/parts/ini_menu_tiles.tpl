{*
   The links of a left menu as an overview page of tiles (parts/ini_menu.tpl with menu_layout='tiles'; the Setup
   overview, setup/setupmenu.tpl). The links, their groups and the access checks are those of the menu: a link the
   user may not open is not here, or, with menu.ini NoAccessLinks=disabled, a tile without link and without figure,
   as the menu shows its name without link.

   Variables: groups (hash id, name, items; translated when the name is translated already), tile_id_prefix,
   i18n_section (the groups' names), known_icons, and per item, from the menu.ini section the link comes from:
   * item.icon (LinkIcons[]): an icon name; only a name of known_icons (design.ini [AdminDesignSettings] IconList[],
     each with a mask rule .a4-bi-<name>) is drawn, any other name or none draws the plug icon
   * item.text (LinkDescriptions[]): one line on what the function does, translated in item.context
   * item.figure (LinkFigureTemplates[]): a design: template drawing the tile's figure, included with
     figure_key = the item's key, on a tile the user may open only; anything else draws none
   A tile is a card with a Bootstrap icon, the link's name as its link (stretched over the card) and the description
   and figure as the link's description.
*}
{def $tile_icon = ''
     $tile_text = ''
     $tile_figure = ''}
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
        {* the icon only from the list of icons that have a mask rule: no empty square, no markup from a setting *}
        {set $tile_icon = 'plug'
             $tile_text = ''
             $tile_figure = ''}
        {if and( is_set( $item.icon ), $known_icons|contains( $item.icon ) )}
            {set $tile_icon = $item.icon}
        {/if}
        {if and( is_set( $item.text ), $item.text )}
            {set $tile_text = $item.text|d18n( first_set( $item.context, $i18n_section ) )}
        {/if}
        {if and( is_set( $item.figure ), $item.figure|begins_with( 'design:' ) )}
            {set $tile_figure = $item.figure}
        {/if}
        <li class="a4-card a4-setup-tile{if eq( $item.state, 'no-access' )} is-disabled{/if}">
            <span class="a4-setup-tile-icon" aria-hidden="true"><span class="a4-icon a4-bi-{$tile_icon|wash}"></span></span>
            <span class="a4-setup-tile-body">
                {if eq( $item.state, 'link' )}
                <a class="a4-setup-tile-link" href={$item.url|ezurl}{if or( $tile_text, $tile_figure )} aria-describedby="{$tile_id_prefix|wash}-{$item.key|wash}-text"{/if}>{$item.name}</a>
                {if or( $tile_text, $tile_figure )}
                <span class="a4-setup-tile-text" id="{$tile_id_prefix|wash}-{$item.key|wash}-text">
                    {if $tile_text}<span>{$tile_text|wash}</span>{/if}
                    {* the figure only for a function the user may open: its fetch asks no rights of its own *}
                    {if $tile_figure}{include uri=$tile_figure figure_key=$item.key}{/if}
                </span>
                {/if}
                {else}
                <span class="a4-setup-tile-link">{$item.name}</span>
                {if $tile_text}<span class="a4-setup-tile-text"><span>{$tile_text|wash}</span></span>{/if}
                {/if}
            </span>
        </li>
    {/foreach}
    </ul>
</section>
{/foreach}
{undef $tile_icon $tile_text $tile_figure}
