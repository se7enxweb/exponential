{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}

    <!-- start::Navbar Menu -->
    <div class="navbar-menu {if $ui_context_edit}disabled{/if}">
        <ul class="navbar-bar">
            {foreach topmenu($ui_context, true() ) as $menu}
                {include uri='design:page_topmenuitem.tpl' menu_item=$menu navigationpart_identifier=$navigation_part.identifier}
            {/foreach}
            <li>
                {if $ui_context_edit}
                    <a href='#' title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout" class="disabled">{'Logout: '|i18n( 'design/admin/pagelayout' )}{fetch('user', 'current_user').login}</a>
                {else}
                    <a href={'/user/logout'|ezurl} title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout">{'Logout: '|i18n( 'design/admin/pagelayout' )}{fetch('user', 'current_user').login}</a>
                {/if}
            </li>
            <li class="header-search-mobile">
                {include uri='design:page_search.tpl' search_box='-mobile'}
            </li>
        </ul>
    </div>
    <!-- end::Navbar Menu -->
