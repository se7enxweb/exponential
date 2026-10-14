<div id="navbar" class="navbar-main">
    <div id="header-logo" class="header-logo">
        {if $ui_context_edit}
            {* <span title="Exponential {fetch( 'setup', 'version' )}">&nbsp;</span> *}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini, through ezurl: with the siteaccess path when
               the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="Exponential {fetch( 'setup', 'version' )}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {else}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini, through ezurl: with the siteaccess path when
               the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="Exponential {fetch( 'setup', 'version' )}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {/if}
    </div>
    <div id="header-search" class="header-search">
        {include uri='design:page_search.tpl'}
    </div>
    {* admin4: light, dark or system mode; the button names the mode in use and steps to the next. The choice is kept in this browser (localStorage "exp-admin4-theme"); the
       script at the top of page_head_style.tpl applies it before the page is drawn and wires this button. *}
    <div id="header-theme" class="header-theme">
        <button type="button" id="a4-theme-toggle" class="a4-theme-toggle"
                title="{'Switch between light, dark and system mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-light="{'Light mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-dark="{'Dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-system="{'System mode'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-sun a4-icon-sun" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-moon a4-icon-moon" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-circle-half a4-icon-system" aria-hidden="true"></span>
            <span class="a4-theme-label">{'Light mode'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
    </div>
    <div class="navbar-icon">&#9776;</div>
    <!-- begin::Sidebar Controls -->
        <div class="sidebar-controls">
            <button type="button" class="sidebar-control left" aria-label="{'Show or hide the left sidebar'|i18n( 'design/admin/pagelayout' )|wash}" title="{'Show or hide the left sidebar'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span>
            </button>
            <button type="button" class="sidebar-control right" aria-label="{'Show or hide the right sidebar'|i18n( 'design/admin/pagelayout' )|wash}" title="{'Show or hide the right sidebar'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span>
            </button>
        </div>
    <!-- end::Sidebar Controls -->
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
</div>
 