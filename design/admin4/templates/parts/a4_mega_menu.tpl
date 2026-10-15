{*
   The links of a left menu as the groups of admin4's mega menu (parts/ini_menu.tpl with menu_layout='tiles' and
   tiles_layout='mega';
   setup/megamenu.tpl). The links, their groups and the access checks are those of the menu: a link the user may not
   open is not here, or, with menu.ini NoAccessLinks=disabled, its name without link.

   Variables: groups (hash id, name, items; translated when the name is translated already), mega_id_prefix,
   i18n_section (the groups' names), known_icons, and per item, from the menu.ini section the link comes from:
   * item.icon (LinkIcons[]): an icon name of known_icons (design.ini [AdminDesignSettings] IconList[]), else the
     plug icon
   * item.text (LinkDescriptions[]): one line on what the function does, translated in item.context
   No figures (LinkFigureTemplates[]): the menu asks no counts. Each group is a section with a heading (h2, drawn
   small); each link is a plain link, its description is the link's description (aria-describedby).
*}
{def $mega_icon = ''
     $mega_text = ''}
{foreach $groups as $group}
<section class="a4-mega-group" aria-labelledby="{$mega_id_prefix|wash}-{$group.id|wash}">
    {if $group.name}
    <h2 class="a4-mega-group-title" id="{$mega_id_prefix|wash}-{$group.id|wash}">{if is_set( $group.translated )}{$group.name|wash}{else}{$group.name|d18n( $i18n_section )|wash}{/if}</h2>
    {else}
    <h2 class="a4-sr-only" id="{$mega_id_prefix|wash}-{$group.id|wash}">{'Functions'|i18n( 'design/admin/setup/setupmenu' )}</h2>
    {/if}
    <ul class="a4-mega-links">
    {foreach $group.items as $item}
        {if eq( $item.state, 'disabled' )}{continue}{/if}
        {set $mega_icon = 'plug'
             $mega_text = ''}
        {if and( is_set( $item.icon ), $known_icons|contains( $item.icon ) )}
            {set $mega_icon = $item.icon}
        {/if}
        {if and( is_set( $item.text ), $item.text )}
            {set $mega_text = $item.text|d18n( first_set( $item.context, $i18n_section ) )}
        {/if}
        <li class="a4-mega-link{if eq( $item.state, 'no-access' )} is-disabled{/if}">
            <span class="a4-icon a4-bi-{$mega_icon|wash}" aria-hidden="true"></span>
            {if eq( $item.state, 'link' )}
            <a href={$item.url|ezurl}{if $mega_text} aria-describedby="{$mega_id_prefix|wash}-{$item.key|wash}-text"{/if}>{$item.name}</a>
            {else}
            <span class="a4-mega-name">{$item.name}</span>
            {/if}
            {if $mega_text}<span class="a4-mega-text" id="{$mega_id_prefix|wash}-{$item.key|wash}-text">{$mega_text|wash}</span>{/if}
        </li>
    {/foreach}
    </ul>
</section>
{/foreach}
{undef $mega_icon $mega_text}
