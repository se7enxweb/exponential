<div id="navbar" class="navbar-main">
    <div id="header-logo" class="header-logo">
        {if $ui_context_edit}
            {* <span title="Exponential {fetch( 'setup', 'version' )}">&nbsp;</span> *}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini, through ezurl: with the siteaccess path when
               the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="Exponential {fetch( 'setup', 'version' )}" aria-label="Exponential {fetch( 'setup', 'version' )}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {else}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini, through ezurl: with the siteaccess path when
               the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="Exponential {fetch( 'setup', 'version' )}" aria-label="Exponential {fetch( 'setup', 'version' )}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {/if}
    </div>
    <div id="header-search" class="header-search">
        {include uri='design:page_search.tpl'}
    </div>
    {* admin4: light, dark or system mode; the button names the mode in use and steps to the next; a status region announces a press. The choice is the user's eZPreference admin_color_mode (this browser's localStorage "exp-admin4-theme" when anonymous); the
       script at the top of page_head_style.tpl applies it before the page is drawn and wires this button. *}
    <div id="header-theme" class="header-theme">
        <button type="button" id="a4-theme-toggle" class="a4-theme-toggle"
                title="{'Switch to dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-light="{'Colour mode: light'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-dark="{'Colour mode: dark'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-system="{'Colour mode: system'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-light="{'Switch to dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-dark="{'Switch to system mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-system="{'Switch to light mode'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-sun a4-icon-sun" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-moon a4-icon-moon" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-circle-half a4-icon-system" aria-hidden="true"></span>
            <span class="a4-theme-label">{'Colour mode: light'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
        <span id="a4-theme-status" class="a4-sr-only" role="status"></span>
        {* Normal or compact density, the user's eZPreference admin_density (window.a4Pref) like the mode;
           pressed means compact. Applied before the page is drawn by the script in page_head_style.tpl. *}
        <button type="button" id="a4-density-toggle" class="a4-theme-toggle a4-density-toggle" aria-pressed="false"
                title="{'Switch between normal and compact view'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-view-stacked" aria-hidden="true"></span>
            <span class="a4-theme-label">{'Compact view'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
    </div>
    {* The phone-width menu button; ezadmin_menubar.js toggles the menu, aria-expanded and the keys. *}
    <div class="navbar-icon" role="button" tabindex="0" aria-expanded="false" aria-controls="a4-navbar-menu" aria-label="{'Menu'|i18n( 'design/admin/pagelayout' )|wash}"><span aria-hidden="true">&#9776;</span></div>
    <!-- begin::Sidebar Controls -->
        <div class="sidebar-controls">
            <button type="button" class="sidebar-control left" aria-controls="leftmenu" aria-expanded="false" aria-label="{'Left column'|i18n( 'design/admin/pagelayout' )|wash}" title="{'Show or hide the left sidebar'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span>
            </button>
            <button type="button" class="sidebar-control right" aria-controls="rightmenu" aria-expanded="false" aria-label="{'Right column'|i18n( 'design/admin/pagelayout' )|wash}" title="{'Show or hide the right sidebar'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span>
            </button>
        </div>
    <!-- end::Sidebar Controls -->
    <!-- start::Navbar Menu -->
    <div id="a4-navbar-menu" class="navbar-menu {if $ui_context_edit}disabled{/if}" role="navigation" aria-label="{'Main menu'|i18n( 'design/admin/pagelayout' )|wash}">
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
 