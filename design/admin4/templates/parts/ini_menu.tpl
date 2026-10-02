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

{if $uri_string}
    {def $current_uri_string = $uri_string}
{else}
    {def $current_uri_string = ezini( 'SiteSettings', 'IndexPage')}
{/if}


{if ezini_hasvariable( $ini_section, 'Links', 'menu.ini' )}
    {def $url_list   = ezini( $ini_section, 'Links', 'menu.ini' )
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
        {foreach $url_list as $link_key => $link_url}
            {if is_set( $name_list[ $link_key ] )}
                {set $item_name = $name_list[$link_key]|d18n($i18n_section)}
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
                {set $items = $items|append( hash( 'url', $link_url, 'name', $item_name, 'state', cond( $disabled, 'disabled', 'link' ) ) )}
            {elseif $hide_no_access|not}
                {set $items = $items|append( hash( 'url', $link_url, 'name', $item_name, 'state', 'no-access' ) )}
            {/if}
        {/foreach}
    {/if}

    {if $items|count}
        {* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
        {if $menu_name}<h4>{if is_set( $i18n_hash[ $menu_name ] )}{$i18n_hash[ $menu_name ]|wash}{else}{$menu_name|d18n($i18n_section)}{/if}</h4>{/if}
        {* DESIGN: Header END *}</div></div>

        {* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

        <ul class="leftmenu-items">
        {foreach $items as $item}
            {if eq( $item.state, 'disabled' )}
                <li><div><span class="disabled">{$item.name}</span></div></li>
            {elseif eq( $item.state, 'link' )}
                <li{if $current_uri_string|begins_with( $item.url )} class="current"{/if}><div><a href={$item.url|ezurl}>{$item.name}</a></div></li>
            {else}
                <li class="disabled-no-access"><div><span class="disabled">{$item.name}</span></div></li>
            {/if}
        {/foreach}
        </ul>

        {* DESIGN: Content END *}</div></div></div>
    {/if}
    {undef $url_list $menu_name $check $has_access $items $check_view $hide_no_access}
{/if}
