{*
   Reusable menu template using menu.ini settings
   for links and names.

   Two input variables are expected as input:
   * ini_section : The ini section to read settings from
   * i18n_hash : (optional) Hash for i18n values

   See parts/setup/menu.tpl for example!

   A link is shown only to a user who may follow it: its PolicyList_<key>[] (node ids or module/function)
   and, with menu.ini [MenuAccessSettings] CheckViewAccess=enabled, its address itself, checked the way the
   kernel checks the request (fetch( 'user', 'can_open' )). A link the user cannot open is left out, or shown
   without a link with NoAccessLinks=disabled. A menu with no link left is left out.
   * hidden_links : (optional) keys of links to leave out whatever the user may do (the audit menu passes
     fetch( 'audit', 'hidden_menu_links' ): the links into channels the user may not read)

   Groups (optional, menu.ini): Groups[] names the groups of a long menu in their order, GroupNames[<group>] their
   titles, GroupLinks_<group>[] the link keys in each. A group is a <details> with its title as summary, open unless
   the user closed it (admin4.js keeps that per browser) and always open when it holds the current page; links in
   no group (an extension's, say) follow in a last group "More"; a link named in two groups shows in the first. Without
   Groups[], or with Groups[] reset to no values, the menu is one list, as before. Access and the order of links inside a group are those of Links[].
   LeadLinks[] (optional, menu.ini) names links shown before the groups, in no group (the Setup menu's overview).
   TrailLinks[] (optional, menu.ini) names links drawn after all other links and groups, in the order TrailLinks[]
   names them, also after the links extensions append (the Dashboard menu's Change password and Edit profile). It
   works with and without Groups[]: in a grouped menu they stand after the groups and "More", in no group; in one list
   they are its last items. Only the menu's own Links[] keys count, a link of a GroupSections[] section never; a key
   in LeadLinks[] and TrailLinks[] is a trail link. Access is checked as for every link. In the tiles they are the
   last tiles, a group without title after the others in a grouped menu.
   GroupTabs[<group>]=<tab> (optional): the group stands for a top tab and is drawn only while that tab is not in the
   top menu (left out of [TopAdminMenu] Tabs[], named in HiddenTabs[], or named in SetupTabs[] for a user who may
   open setup/menu); while the tab is shown, the group and the
   links of its GroupLinks_<group>[] are not drawn at all. GroupSections[<group>]=<section> (optional) adds the links
   of another menu.ini section to the group, after its own: that section's Links[], LinkNames[] (translated in its
   context, design/admin/parts/<name>/menu), PolicyList[], PolicyList_<key>[], Enabled_<key>[] and tile settings; the
   i18n_hash names them as <group>-<key>. A link of this menu to an address the added section has too is left out, so
   the moved tab's menu keeps it. Both are read only where groups are (doc/features/6.0/admin4-design.md, "Moving a
   tab into Setup").
   * section_hidden_links : (optional) hash of a section name to the keys of its links to leave out, as hidden_links
     for the sections GroupSections[] adds

   * menu_layout : (optional) 'tiles' draws the same links, groups and access checks as an overview page of tiles
     (parts/ini_menu_tiles.tpl; the Setup overview, setup/setupmenu.tpl) instead of the menu; the lead links are
     left out there. A tile takes its icon, description and figure from the menu's section as well:
     LinkIcons[<key>] (a name of design.ini [AdminDesignSettings] IconList[], else the plug icon),
     LinkDescriptions[<key>] (translated in the menu's context, like LinkNames[]) and LinkFigureTemplates[<key>]
     (a design: template included with figure_key=<key>).
   * tiles_layout : (optional, with menu_layout='tiles') 'mega' draws the same groups as those of admin4's mega menu
     (parts/a4_mega_menu.tpl; setup/megamenu.tpl): a heading and one line per link with its icon and description,
     without figures.
*}

{if is_unset( $ini_section )}
    {def $ini_section  = 'Leftmenu_'
         $i18n_section = 'design/admin/parts/menu'}
{elseif $ini_section|contains('_')}
    {def $i18n_section = concat('design/admin/parts/', $ini_section|explode('_')[1], '/menu')}
{else}
    {def $i18n_section = 'design/admin/parts/menu'}
{/if}

{if is_unset( $i18n_hash )}
    {def $i18n_hash = hash()}
{/if}

{if is_unset( $hidden_links )}
    {def $hidden_links = array()}
{/if}

{if is_unset( $section_hidden_links )}
    {def $section_hidden_links = hash()}
{/if}

{if is_unset( $menu_layout )}
    {def $menu_layout = 'menu'}
{/if}

{if is_unset( $tiles_layout )}
    {def $tiles_layout = ''}
{/if}

{if $uri_string}
    {def $current_uri_string = $uri_string}
{else}
    {def $current_uri_string = ezini( 'SiteSettings', 'IndexPage')}
{/if}


{if ezini_hasvariable( $ini_section, 'Links', 'menu.ini' )}
    {def $ini_menu_url_list   = array()
         $name_list  = hash()
         $menu_name  = ''
         $check      = array()
         $has_access = true()
         $item_name = ''
         $disabled = true()
         $enabled_hash = hash()
         $enabled_defaults = hash( 'default', 'true', 'edit', 'false', 'browse', 'false' )
         $check_view = ezini( 'MenuAccessSettings', 'CheckViewAccess', 'menu.ini' )|ne( 'disabled' )
         $hide_no_access = ezini( 'MenuAccessSettings', 'NoAccessLinks', 'menu.ini' )|ne( 'disabled' )
         $items = array()
         $group_list = array()
         $menu_sections = array( hash( 'section', $ini_section, 'group', '' ) )
         $tab_links = array()
         $added_urls = array()
         $section = ''
         $section_group = ''
         $section_context = $i18n_section
         $section_hidden = array()
         $section_icons = hash()
         $section_texts = hash()
         $section_figures = hash()
         $item_key = ''}

    {if ezini_hasvariable( $ini_section, 'Name', 'menu.ini' )}
        {set $menu_name = ezini( $ini_section, 'Name', 'menu.ini' )}
    {/if}

    {* Check access globally *}
    {if ezini_hasvariable( $ini_section, 'PolicyList', 'menu.ini' )}
        {foreach ezini( $ini_section, 'PolicyList', 'menu.ini' ) as $policy}
            {if $policy|contains('/')}
                {set $check = $policy|explode('/')}
                {if fetch( 'user', 'has_access_to', hash( 'module', $check[0], 'function', $check[1] ) )|not}
                    {set $has_access = false()}
                    {break}
                {/if}
            {else}
                {set $check = fetch('content', 'node', hash( 'node_id', $policy ))}
                {if and( $check, $check.can_read )|not}
                    {set $has_access = false()}
                    {break}
                {/if}
            {/if}
        {/foreach}
    {/if}

    {* The groups to draw: a group standing for a top tab (GroupTabs[]) only while the tab is not in the top menu,
       and then with the links of the section it adds (GroupSections[]) *}
    {if and( $has_access, ezini_hasvariable( $ini_section, 'Groups', 'menu.ini' ) )}
        {def $group_tabs = cond( ezini_hasvariable( $ini_section, 'GroupTabs', 'menu.ini' ), ezini( $ini_section, 'GroupTabs', 'menu.ini' ), hash() )
             $group_sections = cond( ezini_hasvariable( $ini_section, 'GroupSections', 'menu.ini' ), ezini( $ini_section, 'GroupSections', 'menu.ini' ), hash() )
             $top_tabs = cond( ezini_hasvariable( 'TopAdminMenu', 'Tabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'Tabs', 'menu.ini' ), array() )
             $hidden_tabs = cond( ezini_hasvariable( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' ), array() )
             $setup_tabs = cond( ezini_hasvariable( 'TopAdminMenu', 'SetupTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'SetupTabs', 'menu.ini' ), array() )
             $group_tab = ''
             $group_section = ''}
        {* SetupTabs[] leave the top menu for a user who may open the Setup overview, as the topmenu operator does *}
        {if and( $setup_tabs|count, fetch( 'user', 'can_open', hash( 'uri', 'setup/menu' ) ) )}
            {set $hidden_tabs = $hidden_tabs|merge( $setup_tabs )}
        {/if}
        {foreach ezini( $ini_section, 'Groups', 'menu.ini' ) as $group}
            {set $group_tab = first_set( $group_tabs[ $group ], '' )
                 $group_section = first_set( $group_sections[ $group ], '' )}
            {if and( $group_tab, $top_tabs|contains( $group_tab ), $hidden_tabs|contains( $group_tab )|not )}
                {* the tab is in the top menu: its group and the links named in it are not drawn here *}
                {if ezini_hasvariable( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' )}
                    {set $tab_links = $tab_links|merge( ezini( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' ) )}
                {/if}
                {continue}
            {/if}
            {set $group_list = $group_list|append( $group )}
            {if and( $group_section, $group_section|ne( $ini_section ), ezini_hasvariable( $group_section, 'Links', 'menu.ini' ) )}
                {set $menu_sections = $menu_sections|append( hash( 'section', $group_section, 'group', $group ) )}
            {/if}
        {/foreach}
        {undef $group_tabs $group_sections $top_tabs $hidden_tabs $setup_tabs $group_tab $group_section}
    {/if}

    {if $has_access}
        {foreach $menu_sections as $menu_section}
        {set $section = $menu_section.section
             $section_group = $menu_section.group
             $ini_menu_url_list = ezini( $section, 'Links', 'menu.ini' )
             $name_list = cond( ezini_hasvariable( $section, 'LinkNames', 'menu.ini' ), ezini( $section, 'LinkNames', 'menu.ini' ), hash() )
             $section_hidden = array()
             $section_context = $i18n_section}
        {if $section_group}
            {* a section a group adds: its own context, hidden links and access to the whole menu *}
            {set $section_context = concat( 'design/admin/parts/', $section|explode( '_' )[1], '/menu' )
                 $section_hidden = first_set( $section_hidden_links[ $section ], array() )}
            {if ezini_hasvariable( $section, 'PolicyList', 'menu.ini' )}
                {foreach ezini( $section, 'PolicyList', 'menu.ini' ) as $policy}
                    {if $policy|contains('/')}
                        {set $check = $policy|explode('/')}
                        {if fetch( 'user', 'has_access_to', hash( 'module', $check[0], 'function', $check[1] ) )|not}
                            {set $ini_menu_url_list = array()}
                            {break}
                        {/if}
                    {else}
                        {set $check = fetch('content', 'node', hash( 'node_id', $policy ))}
                        {if and( $check, $check.can_read )|not}
                            {set $ini_menu_url_list = array()}
                            {break}
                        {/if}
                    {/if}
                {/foreach}
            {/if}
        {/if}
        {* the tiles' settings of the section: icon, description and figure per link key *}
        {set $section_icons = cond( and( eq( $menu_layout, 'tiles' ), ezini_hasvariable( $section, 'LinkIcons', 'menu.ini' ) ), ezini( $section, 'LinkIcons', 'menu.ini' ), hash() )
             $section_texts = cond( and( eq( $menu_layout, 'tiles' ), ezini_hasvariable( $section, 'LinkDescriptions', 'menu.ini' ) ), ezini( $section, 'LinkDescriptions', 'menu.ini' ), hash() )
             $section_figures = cond( and( eq( $menu_layout, 'tiles' ), ezini_hasvariable( $section, 'LinkFigureTemplates', 'menu.ini' ) ), ezini( $section, 'LinkFigureTemplates', 'menu.ini' ), hash() )}
        {foreach $ini_menu_url_list as $link_key => $link_url}
            {if $section_group|not}
                {if $hidden_links|contains( $link_key )}{continue}{/if}
                {* a link named in the group of a tab that is in the top menu *}
                {if $tab_links|contains( $link_key )}{continue}{/if}
            {elseif $section_hidden|contains( $link_key )}
                {* hidden here as in its own menu: a Setup link to the same address stays out as well *}
                {set $added_urls = $added_urls|append( $link_url|trim( '/' ) )}
                {continue}
            {/if}
            {set $item_key = cond( $section_group, concat( $section_group, '-', $link_key ), $link_key )}
            {if is_set( $name_list[ $link_key ] )}
                {if $section_group}
                {set $item_name = $name_list[$link_key]|d18n($section_context)|wash}
                {else}
                {set $item_name = $name_list[$link_key]|d18n($i18n_section)|wash}
                {/if}
            {else}
                {set $item_name = first_set( $i18n_hash[ $item_key ], $link_key )|wash}
            {/if}

            {* Check if link should be disabled *}
            {if ezini_hasvariable( $section, concat( 'Enabled_', $link_key ), 'menu.ini' )}
                {set $enabled_hash = $enabled_defaults|merge( ezini( $section, concat( 'Enabled_', $link_key ), 'menu.ini' ) )}
            {else}
                {set $enabled_hash = $enabled_defaults}
            {/if}

            {if is_set( $enabled_hash[$ui_context] )}
                {set $disabled = $enabled_hash[$ui_context]}
            {else}
                {set $disabled = $enabled_hash['default']|eq( 'false' )}
            {/if}

            {* Check access per link: its policies, then its address *}
            {set $has_access = true()}
            {if ezini_hasvariable( $section, concat( 'PolicyList_', $link_key ), 'menu.ini' )}
                {foreach ezini( $section, concat( 'PolicyList_', $link_key ), 'menu.ini' ) as $policy}
                    {if $policy|contains('/')}
                        {set $check = $policy|explode('/')}
                        {if fetch( 'user', 'has_access_to', hash( 'module', $check[0], 'function', $check[1] ) )|not}
                            {set $has_access = false()}
                            {break}
                        {/if}
                    {else}
                        {set $check = fetch('content', 'node', hash( 'node_id', $policy ))}
                        {if and( $check, $check.can_read )|not}
                            {set $has_access = false()}
                            {break}
                        {/if}
                    {/if}
                {/foreach}
            {/if}
            {if and( $has_access, $check_view, $link_url|begins_with( 'http' )|not )}
                {set $has_access = fetch( 'user', 'can_open', hash( 'uri', $link_url ) )}
            {/if}

            {if or( $has_access, $hide_no_access|not )}
                {set $items = $items|append( hash( 'key', $item_key, 'url', $link_url, 'name', $item_name,
                                                   'state', cond( $has_access, cond( $disabled, 'disabled', 'link' ), 'no-access' ),
                                                   'group', $section_group,
                                                   'icon', first_set( $section_icons[ $link_key ], '' ),
                                                   'text', first_set( $section_texts[ $link_key ], '' ),
                                                   'context', $section_context,
                                                   'figure', first_set( $section_figures[ $link_key ], '' ) ) )}
                {if $section_group}{set $added_urls = $added_urls|append( $link_url|trim( '/' ) )}{/if}
            {/if}
        {/foreach}
        {/foreach}
        {set $has_access = true()}
    {/if}

    {* A link of this menu to an address an added section has too shows in that section's group only *}
    {if $added_urls|count}
        {def $kept_items = array()}
        {foreach $items as $item}
            {if or( $item.group, $added_urls|contains( $item.url|trim( '/' ) )|not )}
                {set $kept_items = $kept_items|append( $item )}
            {/if}
        {/foreach}
        {set $items = $kept_items}
        {undef $kept_items}
    {/if}

    {* TrailLinks[]: the menu's own links named there move to the end, in that order; the rest keep theirs *}
    {def $trail_links = array()
         $trail_items = array()
         $trail_keys = array()}
    {if ezini_hasvariable( $ini_section, 'TrailLinks', 'menu.ini' )}
        {set $trail_links = ezini( $ini_section, 'TrailLinks', 'menu.ini' )}
    {/if}
    {if and( $trail_links|count, $items|count )}
        {def $kept_items = array()}
        {foreach $trail_links as $trail_key}
            {foreach $items as $item}
                {if and( $item.group|not, eq( $item.key, $trail_key ), $trail_keys|contains( $item.key )|not )}
                    {set $trail_items = $trail_items|append( $item )
                         $trail_keys = $trail_keys|append( $item.key )}
                {/if}
            {/foreach}
        {/foreach}
        {foreach $items as $item}
            {if or( $item.group, $trail_keys|contains( $item.key )|not )}
                {set $kept_items = $kept_items|append( $item )}
            {/if}
        {/foreach}
        {set $items = $kept_items|merge( $trail_items )}
        {undef $kept_items}
    {/if}

    {if $items|count}
        {def $menu_title_id = concat( 'leftmenu-title-', $ini_section|explode( '_' )|implode( '-' ) )
             $groups = array()
             $grouped_keys = array()
             $group_items = array()
             $group_open = false()
             $group_id = ''
             $group_links = array()
             $lead_links = array()
             $lead_items = array()}
        {* not cond( ..., array() ): an empty array literal there gives no value, and a menu without LeadLinks[]
           (its tiles) then matched every link as a lead link *}
        {if ezini_hasvariable( $ini_section, 'LeadLinks', 'menu.ini' )}
            {set $lead_links = ezini( $ini_section, 'LeadLinks', 'menu.ini' )}
        {/if}

        {* An empty Groups[] (reset in an override) means one plain list, as no Groups[] does *}
        {if $group_list|count}
            {def $group_names = cond( ezini_hasvariable( $ini_section, 'GroupNames', 'menu.ini' ), ezini( $ini_section, 'GroupNames', 'menu.ini' ), hash() )}
            {* The lead links (LeadLinks[], the menu's overview) come before the groups and belong to none *}
            {foreach $items as $item}
                {if and( $lead_links|contains( $item.key ), $trail_keys|contains( $item.key )|not )}
                    {set $lead_items = $lead_items|append( $item )
                         $grouped_keys = $grouped_keys|append( $item.key )}
                {/if}
            {/foreach}
            {* The groups in their order, each with the links it names; then "More" for the rest *}
            {* The trail links (TrailLinks[]) come after the groups and "More" and belong to none *}
            {set $grouped_keys = $grouped_keys|merge( $trail_keys )}
            {foreach $group_list as $group}
                {set $group_items = array()
                     $group_links = cond( ezini_hasvariable( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' ), ezini( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' ), array() )}
                {* A link already placed in an earlier group stays there: no link shows twice. A link of a section the
                   group adds (GroupSections[]) is in that group. *}
                {foreach $items as $item}
                    {if and( or( $group_links|contains( $item.key ), eq( $item.group, $group ) ), $grouped_keys|contains( $item.key )|not )}
                        {set $group_items = $group_items|append( $item )}
                    {/if}
                {/foreach}
                {foreach $group_items as $item}
                    {set $grouped_keys = $grouped_keys|append( $item.key )}
                {/foreach}
                {if $group_items|count}
                    {set $groups = $groups|append( hash( 'id', $group, 'name', first_set( $group_names[ $group ], $group ), 'items', $group_items ) )}
                {/if}
            {/foreach}
            {set $group_items = array()}
            {foreach $items as $item}
                {if $grouped_keys|contains( $item.key )|not}
                    {set $group_items = $group_items|append( $item )}
                {/if}
            {/foreach}
            {if $group_items|count}
                {set $groups = $groups|append( hash( 'id', 'more', 'name', 'More'|i18n( 'design/admin/pagelayout' ), 'translated', true(), 'items', $group_items ) )}
            {/if}
            {undef $group_names}
        {/if}

        {if eq( $menu_layout, 'tiles' )}
            {* The overview page (setup/menu): the same links, groups and access as the menu, as tiles. The lead
               links are the overview itself and are left out; a menu without groups is one group without title. *}
            {if $group_list|count|not}
                {foreach $items as $item}
                    {if $lead_links|contains( $item.key )|not}
                        {set $group_items = $group_items|append( $item )}
                    {/if}
                {/foreach}
                {if $group_items|count}
                    {set $groups = array( hash( 'id', 'all', 'name', '', 'translated', true(), 'items', $group_items ) )}
                {/if}
            {elseif $trail_items|count}
                {* grouped: the trail links are the last tiles, a group without title *}
                {set $groups = $groups|append( hash( 'id', 'trail', 'name', '', 'translated', true(), 'items', $trail_items ) )}
            {/if}
            {* each item carries the icon, description (with the context it is translated in) and figure of its
               section's LinkIcons[], LinkDescriptions[] and LinkFigureTemplates[], read above *}
            {if eq( $tiles_layout, 'mega' )}
            {* admin4's mega menu: the same groups, one line per link, no figures *}
            {include uri='design:parts/a4_mega_menu.tpl' groups=$groups
                     known_icons=cond( ezini_hasvariable( 'AdminDesignSettings', 'IconList', 'design.ini' ), ezini( 'AdminDesignSettings', 'IconList', 'design.ini' ), array() )
                     mega_id_prefix=concat( 'a4-mega-', $ini_section|explode( '_' )|implode( '-' ) ) i18n_section=$i18n_section}
            {else}
            {include uri='design:parts/ini_menu_tiles.tpl' groups=$groups
                     known_icons=cond( ezini_hasvariable( 'AdminDesignSettings', 'IconList', 'design.ini' ), ezini( 'AdminDesignSettings', 'IconList', 'design.ini' ), array() )
                     tile_id_prefix=concat( 'a4-tiles-', $ini_section|explode( '_' )|implode( '-' ) ) i18n_section=$i18n_section}
            {/if}
        {else}
        {* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
        {if $menu_name}<h4 id="{$menu_title_id|wash}">{if is_set( $i18n_hash[ $menu_name ] )}{$i18n_hash[ $menu_name ]|wash}{else}{$menu_name|d18n($i18n_section)|wash}{/if}</h4>{/if}
        {* DESIGN: Header END *}</div></div>

        {* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

        {if $group_list|count}
            {if $lead_items|count}
                <div class="a4-navlead">{include uri='design:parts/ini_menu_items.tpl' items=$lead_items current_uri_string=$current_uri_string}</div>
            {/if}
            {foreach $groups as $group}
                {set $group_open = false()
                     $group_id = concat( $menu_title_id, '-', $group.id )}
                {foreach $group.items as $item}
                    {if and( eq( $item.state, 'link' ), or( eq( $current_uri_string, $item.url ), $current_uri_string|begins_with( concat( $item.url, '/' ) ) ) )}{set $group_open = true()}{/if}
                {/foreach}
                <details class="a4-navgroup" id="{$group_id|wash}" data-a4-navgroup="{$group_id|wash}" open{if $group_open} data-a4-current{/if}>
                    <summary class="a4-nav-title">{if is_set( $group.translated )}{$group.name|wash}{else}{$group.name|d18n( $i18n_section )|wash}{/if}</summary>
                    {include uri='design:parts/ini_menu_items.tpl' items=$group.items current_uri_string=$current_uri_string}
                </details>
            {/foreach}
            {if $trail_items|count}
                <div class="a4-navtrail">{include uri='design:parts/ini_menu_items.tpl' items=$trail_items current_uri_string=$current_uri_string}</div>
            {/if}
        {else}
            {include uri='design:parts/ini_menu_items.tpl' items=$items current_uri_string=$current_uri_string labelledby=cond( $menu_name, $menu_title_id, '' )}
        {/if}

        {* DESIGN: Content END *}</div></div></div>
        {/if}
        {undef $menu_title_id $groups $grouped_keys $group_items $group_open $group_id $group_links $lead_links $lead_items}
    {/if}
    {undef $trail_links $trail_items $trail_keys}
    {undef $ini_menu_url_list $name_list $menu_name $check $has_access $item_name $disabled $enabled_hash $enabled_defaults $items $check_view $hide_no_access
           $group_list $menu_sections $tab_links $added_urls $section $section_group $section_context $section_hidden
           $section_icons $section_texts $section_figures $item_key}
{/if}
