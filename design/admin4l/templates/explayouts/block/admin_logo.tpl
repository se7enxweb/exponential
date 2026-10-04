{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}

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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M32 32C14.3 32 0 46.3 0 64l0 96c0 17.7 14.3 32 32 32s32-14.3 32-32l0-64 64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 32zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7 14.3 32 32 32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0 0-64zM320 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0 0 64c0 17.7 14.3 32 32 32s32-14.3 32-32l0-96c0-17.7-14.3-32-32-32l-96 0zM448 352c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 64-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0c17.7 0 32-14.3 32-32l0-96z"/></svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M32 32C14.3 32 0 46.3 0 64l0 96c0 17.7 14.3 32 32 32s32-14.3 32-32l0-64 64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 32zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7 14.3 32 32 32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0 0-64zM320 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0 0 64c0 17.7 14.3 32 32 32s32-14.3 32-32l0-96c0-17.7-14.3-32-32-32l-96 0zM448 352c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 64-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0c17.7 0 32-14.3 32-32l0-96z"/></svg>
            </a>
        {/if}
    </div>
