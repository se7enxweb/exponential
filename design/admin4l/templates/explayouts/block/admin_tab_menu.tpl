{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}

    <!-- start::Navbar Menu -->
    <div class="navbar-menu {if $ui_context_edit}disabled{/if}">
        <ul class="navbar-bar">
            {* The tab marked as current: the page's navigation part, or Setup's for a page of a tab the Setup menu
               stands in for (parts/a4_navigation_part.tpl) *}
            {def $a4_current_part = ''}
            {set-block variable=a4_current_part}{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}
            {foreach topmenu($ui_context, true() ) as $menu}
                {include uri='design:page_topmenuitem.tpl' menu_item=$menu navigationpart_identifier=$a4_current_part|trim}
            {/foreach}
            {undef $a4_current_part}
            <li>
                {if $ui_context_edit}
                    <a href='#' title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout" class="disabled">{'Logout: '|i18n( 'design/admin/pagelayout' )}{fetch('user', 'current_user').login}</a>
                {else}
                    <a href={'/user/logout'|ezurl} title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout">{'Logout: '|i18n( 'design/admin/pagelayout' )}{fetch('user', 'current_user').login}</a>
                {/if}
            </li>
            <li class="header-search-mobile">
                {include uri='design:page_search.tpl' search_box='-mobile'}
                {if is_set( $search_sections )}{undef $search_sections}{/if}
            </li>
        </ul>
    </div>
    <!-- end::Navbar Menu -->
