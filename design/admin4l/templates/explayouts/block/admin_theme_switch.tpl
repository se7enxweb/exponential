{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
    {* The front page of the default siteaccess: scheme, host, port and siteaccess path as the settings and this
       request call for (ezpSiteAccessURL). The first of the header's tools, before light/dark, the user menu and
       logout, and a round icon button as they are; it opens in the same window and stays a link on edit pages. *}
    <div id="header-site" class="header-site">
        <a class="site-preview a4-header-btn" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
        </a>
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
