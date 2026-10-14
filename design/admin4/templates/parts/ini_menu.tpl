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

{if $uri_string}
    {def $current_uri_string = $uri_string}
{else}
    {def $current_uri_string = ezini( 'SiteSettings', 'IndexPage')}
{/if}


{if ezini_hasvariable( $ini_section, 'Links', 'menu.ini' )}
    {def $ini_menu_url_list   = ezini( $ini_section, 'Links', 'menu.ini' )
         $name_list  = ezini( $ini_section, 'LinkNames', 'menu.ini' )
         $menu_name  = ''
         $check      = array()
         $has_access = true()
         $item_name = ''
         $disabled = true()
         $enabled_hash = hash()
         $enabled_defaults = hash( 'default', 'true', 'edit', 'false', 'browse', 'false' )
         $check_view = ezini( 'MenuAccessSettings', 'CheckViewAccess', 'menu.ini' )|ne( 'disabled' )
         $hide_no_access = ezini( 'MenuAccessSettings', 'NoAccessLinks', 'menu.ini' )|ne( 'disabled' )
         $items = array()}

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

    {if $has_access}
        {foreach $ini_menu_url_list as $link_key => $link_url}
            {if $hidden_links|contains( $link_key )}{continue}{/if}
            {if is_set( $name_list[ $link_key ] )}
                {set $item_name = $name_list[$link_key]|d18n($i18n_section)|wash}
            {else}
                {set $item_name = first_set( $i18n_hash[ $link_key ], $link_key )|wash}
            {/if}

            {* Check if link should be disabled *}
            {if ezini_hasvariable( $ini_section, concat( 'Enabled_', $link_key ), 'menu.ini' )}
                {set $enabled_hash = $enabled_defaults|merge( ezini( $ini_section, concat( 'Enabled_', $link_key ), 'menu.ini' ) )}
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
            {if ezini_hasvariable( $ini_section, concat( 'PolicyList_', $link_key ), 'menu.ini' )}
                {foreach ezini( $ini_section, concat( 'PolicyList_', $link_key ), 'menu.ini' ) as $policy}
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

            {if $has_access}
                {set $items = $items|append( hash( 'key', $link_key, 'url', $link_url, 'name', $item_name, 'state', cond( $disabled, 'disabled', 'link' ) ) )}
            {elseif $hide_no_access|not}
                {set $items = $items|append( hash( 'key', $link_key, 'url', $link_url, 'name', $item_name, 'state', 'no-access' ) )}
            {/if}
        {/foreach}
    {/if}

    {if $items|count}
        {def $menu_title_id = concat( 'leftmenu-title-', $ini_section|explode( '_' )|implode( '-' ) )
             $groups = array()
             $grouped_keys = array()
             $group_items = array()
             $group_open = false()
             $group_id = ''
             $group_links = array()
             $group_list = cond( ezini_hasvariable( $ini_section, 'Groups', 'menu.ini' ), ezini( $ini_section, 'Groups', 'menu.ini' ), array() )}
        {* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
        {if $menu_name}<h4 id="{$menu_title_id|wash}">{if is_set( $i18n_hash[ $menu_name ] )}{$i18n_hash[ $menu_name ]|wash}{else}{$menu_name|d18n($i18n_section)|wash}{/if}</h4>{/if}
        {* DESIGN: Header END *}</div></div>

        {* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

        {* An empty Groups[] (reset in an override) means one plain list, as no Groups[] does *}
        {if $group_list|count}
            {def $group_names = cond( ezini_hasvariable( $ini_section, 'GroupNames', 'menu.ini' ), ezini( $ini_section, 'GroupNames', 'menu.ini' ), hash() )}
            {* The groups in their order, each with the links it names; then "More" for the rest *}
            {foreach $group_list as $group}
                {set $group_items = array()
                     $group_links = cond( ezini_hasvariable( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' ), ezini( $ini_section, concat( 'GroupLinks_', $group ), 'menu.ini' ), array() )}
                {* A link already placed in an earlier group stays there: no link shows twice *}
                {foreach $items as $item}
                    {if and( $group_links|contains( $item.key ), $grouped_keys|contains( $item.key )|not )}
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
            {undef $group_names}
        {else}
            {include uri='design:parts/ini_menu_items.tpl' items=$items current_uri_string=$current_uri_string labelledby=cond( $menu_name, $menu_title_id, '' )}
        {/if}

        {* DESIGN: Content END *}</div></div></div>
        {undef $menu_title_id $groups $grouped_keys $group_items $group_open $group_id $group_links $group_list}
    {/if}
    {undef $ini_menu_url_list $menu_name $check $has_access $items $check_view $hide_no_access}
{/if}
