{*
   The links of a left menu as the groups of admin4's mega menu (parts/ini_menu.tpl with menu_layout='tiles' and
   tiles_layout='mega'; setup/megamenu.tpl). The links, their groups and the access checks are those of the menu.

   Variables: groups (hash id, name, items; translated when the name is translated already), mega_links,
   mega_id_prefix, i18n_section (the groups' names), known_icons, and per item, from the menu.ini section the link
   comes from:
   * item.icon (LinkIcons[]): an icon name of known_icons (design.ini [AdminDesignSettings] IconList[]), else the
     plug icon
   * item.text (LinkDescriptions[]): one line on what the function does, translated in item.context
   mega_links (the section's MegaMenuLinks[]) chooses the links shown: by link key (a link of a section a group adds,
   GroupSections[], as <group>-<key>, e.g. audit-console), in that order, at most 12, only
   links the user may open (a link of an inactive extension is not in the groups at all); each keeps its group, the
   groups follow the order of their first chosen link, and a group with none is left out. With no mega_links every
   link is shown, in the menu's groups, and a link the user may not open shows its name without link
   (NoAccessLinks=disabled), as in the menu.
   No figures (LinkFigureTemplates[]): the menu asks no counts. Each group is a section with a heading (h2, drawn
   small); each link is a plain link, its description is the link's description (aria-describedby).
*}
{def $mega_max = 12
     $mega_groups = $groups
     $mega_picked = array()
     $mega_keys = array()
     $mega_item_keys = array()
     $mega_url = ''
     $mega_order = array()
     $mega_items = array()}
{if and( is_set( $mega_links ), $mega_links|count )}
    {foreach $mega_links as $mega_key}
        {if $mega_picked|count|ge( $mega_max )}{break}{/if}
        {if $mega_keys|contains( $mega_key )}{continue}{/if}
        {* a key of this menu whose link is not drawn because a group's added section (GroupSections[], a tab moved
           into Setup) has the same address, as the Setup link audit (audit/settings) and the audit group's Settings:
           that link stands for it. mega_urls is this menu's Links[]. *}
        {set $mega_url = ''}
        {if is_set( $mega_urls )}{if is_set( $mega_urls[ $mega_key ] )}{set $mega_url = $mega_urls[ $mega_key ]|trim( '/' )}{/if}{/if}
        {* by its key first, then, when that drew nothing, by the address *}
        {foreach array( 'key', 'url' ) as $mega_pass}
            {if or( $mega_keys|contains( $mega_key ), and( eq( $mega_pass, 'url' ), $mega_url|not ) )}{continue}{/if}
            {foreach $groups as $group}
                {foreach $group.items as $item}
                    {if and( eq( $item.state, 'link' ), $mega_keys|contains( $mega_key )|not, $mega_item_keys|contains( $item.key )|not,
                             cond( eq( $mega_pass, 'key' ), eq( $item.key, $mega_key ), and( $item.group, eq( $item.url|trim( '/' ), $mega_url ) ) ) )}
                        {set $mega_picked = $mega_picked|append( hash( 'group', $group.id, 'item', $item ) )
                             $mega_keys = $mega_keys|append( $mega_key )
                             $mega_item_keys = $mega_item_keys|append( $item.key )}
                        {if $mega_order|contains( $group.id )|not}
                            {set $mega_order = $mega_order|append( $group.id )}
                        {/if}
                    {/if}
                {/foreach}
            {/foreach}
        {/foreach}
    {/foreach}
    {* the chosen links in their groups: a group in the order of its first chosen link, its links in the chosen order *}
    {set $mega_groups = array()}
    {foreach $mega_order as $mega_group_id}
        {set $mega_items = array()}
        {foreach $mega_picked as $mega_pick}
            {if eq( $mega_pick.group, $mega_group_id )}{set $mega_items = $mega_items|append( $mega_pick.item )}{/if}
        {/foreach}
        {foreach $groups as $group}
            {if eq( $group.id, $mega_group_id )}{set $mega_groups = $mega_groups|append( $group|merge( hash( 'items', $mega_items ) ) )}{/if}
        {/foreach}
    {/foreach}
{/if}
{def $mega_icon = ''
     $mega_text = ''}
{foreach $mega_groups as $group}
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
{undef $mega_icon $mega_text $mega_max $mega_groups $mega_picked $mega_keys $mega_item_keys $mega_url $mega_order $mega_items}
