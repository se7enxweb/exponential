{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}

    <!-- start::Navbar Menu -->
    <div class="navbar-menu {if $ui_context_edit}disabled{/if}">
        <ul class="navbar-bar">
            {foreach topmenu($ui_context, true() ) as $menu}
                {include uri='design:page_topmenuitem.tpl' menu_item=$menu navigationpart_identifier=$navigation_part.identifier}
            {/foreach}
            {* The overflow of the one-line menu (576px and up): ezadmin_menubar.js moves the tabs that do not fit into
               this list and shows the item; without the script it stays hidden and the row scrolls sideways. A
               disclosure button (aria-expanded, aria-controls), not a menu: the list holds plain links. *}
            <li class="a4-nav-more" hidden>
                <button type="button" class="a4-nav-more-toggle" aria-expanded="false" aria-controls="a4-nav-more-list">{'More'|i18n( 'design/admin/pagelayout' )|wash}<span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span></button>
                <ul id="a4-nav-more-list" class="a4-nav-more-list" hidden></ul>
            </li>
            <li class="header-search-mobile">
                {include uri='design:page_search.tpl' search_box='-mobile'}
            </li>
        </ul>
    </div>
    <!-- end::Navbar Menu -->
