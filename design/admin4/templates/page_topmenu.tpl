{* The logo's name carries the version for signed-in users only: an anonymous page of the admin (the 401 page,
   user/register, user/forgotpassword) draws this frame too and should not tell anyone the release. *}
{def $a4_brand_name = $current_user.is_logged_in|choose( 'Exponential', concat( 'Exponential ', fetch( 'setup', 'version' ) ) )}
{* Without the Dashboard tab (left out of menu.ini [TopAdminMenu] Tabs[], in HiddenTabs[], or in SetupTabs[] for a user
   who may open setup/menu) the logo leads to the dashboard, for a user who may open it, instead of the content root:
   the account's pages stay one click away. *}
{def $a4_brand_url = concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )}
{if and( or( ezini( 'TopAdminMenu', 'Tabs', 'menu.ini' )|contains( 'dashboard' )|not,
             and( ezini_hasvariable( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'HiddenTabs', 'menu.ini' )|contains( 'dashboard' ) ),
             and( ezini_hasvariable( 'TopAdminMenu', 'SetupTabs', 'menu.ini' ), ezini( 'TopAdminMenu', 'SetupTabs', 'menu.ini' )|contains( 'dashboard' ),
                  fetch( 'user', 'can_open', hash( 'uri', 'setup/menu' ) ) ) ),
         ezini_hasvariable( 'Topmenu_dashboard', 'URL', 'menu.ini' ),
         fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'dashboard' ) ) )}
    {set $a4_brand_url = first_set( ezini( 'Topmenu_dashboard', 'URL', 'menu.ini' )['default'], $a4_brand_url )}
{/if}
<div id="navbar" class="navbar-main">
    <div id="header-logo" class="header-logo">
        {if $ui_context_edit}
            {* <span title="Exponential {fetch( 'setup', 'version' )}">&nbsp;</span> *}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini (the dashboard without its tab), through ezurl: with the siteaccess
               path when the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={$a4_brand_url|ezurl} title="{$a4_brand_name|wash}" aria-label="{$a4_brand_name|wash}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {else}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini (the dashboard without its tab), through ezurl: with the siteaccess
               path when the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={$a4_brand_url|ezurl} title="{$a4_brand_name|wash}" aria-label="{$a4_brand_name|wash}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {/if}
    </div>
    {* The menu button stands before the menu it opens, so the Tab key goes from it into the open menu; admin4.css draws
       it at the row's end. The menu comes right after the logo, as it is drawn from 1280px; from 576 to 1279px it is
       drawn as the second row, and the Tab key still reaches it before the search and the tools. *}
    {* The phone-width menu button; ezadmin_menubar.js toggles the menu, aria-expanded and the keys. *}
    <div class="navbar-icon" role="button" tabindex="0" aria-expanded="false" aria-controls="a4-navbar-menu" aria-label="{'Menu'|i18n( 'design/admin/pagelayout' )|wash}"><span aria-hidden="true">&#9776;</span></div>
        {* the sections of the search popup, fetched once for both copies of the search (page_search.tpl reads them) *}
        {def $search_sections = cond( $ui_context_edit, array(), fetch( 'section', 'list' ) )}
    <!-- start::Navbar Menu -->
    <div id="a4-navbar-menu" class="navbar-menu {if $ui_context_edit}disabled{/if}" role="navigation" aria-label="{'Main menu'|i18n( 'design/admin/pagelayout' )|wash}">
        <ul class="navbar-bar">
            {* The tab marked as current: the page's navigation part, or Setup's for a page of a tab the Setup menu
               stands in for (parts/a4_navigation_part.tpl) *}
            {def $a4_current_part = ''}
            {set-block variable=a4_current_part}{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}
            {foreach topmenu($ui_context, true() ) as $menu}
                {include uri='design:page_topmenuitem.tpl' menu_item=$menu navigationpart_identifier=$a4_current_part|trim}
            {/foreach}
            {undef $a4_current_part}
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
    <div id="header-search" class="header-search">
        {include uri='design:page_search.tpl'}
        {undef $search_sections}
    </div>
    {* admin4: light, dark or system mode; the button names the mode in use and steps to the next; a status region announces a press. The choice is the user's eZPreference admin_color_mode (this browser's localStorage "exp-admin4-theme" when anonymous); the
       script at the top of page_head_style.tpl applies it before the page is drawn and wires this button. *}
    <div id="header-theme" class="header-theme">
        <button type="button" id="a4-theme-toggle" class="a4-theme-toggle"
                title="{'Switch to dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-light="{'Color mode: light'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-dark="{'Color mode: dark'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-system="{'Color mode: system'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-light="{'Switch to dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-dark="{'Switch to system mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-title-system="{'Switch to light mode'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-sun a4-icon-sun" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-moon a4-icon-moon" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-circle-half a4-icon-system" aria-hidden="true"></span>
            <span class="a4-theme-label">{'Color mode: light'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
        <span id="a4-theme-status" class="a4-sr-only" role="status"></span>
    </div>
    {include uri='design:parts/a4_header_user.tpl'}
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
</div>
 
{undef $a4_brand_name $a4_brand_url}
