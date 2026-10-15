{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}
{* The logo's name carries the version for signed-in users only: an anonymous page of the admin (the 401 page,
   user/register, user/forgotpassword) draws this frame too and should not tell anyone the release. *}
{def $a4_brand_name = $current_user.is_logged_in|choose( 'Exponential', concat( 'Exponential ', fetch( 'setup', 'version' ) ) )}

    <div id="header-logo" class="header-logo">
        {if $ui_context_edit}
            {* <span title="Exponential {fetch( 'setup', 'version' )}">&nbsp;</span> *}
            {* <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential {fetch( 'setup', 'version' )}">
            </a> *}
            {* The content root of content.ini, through ezurl: with the siteaccess path when
               the siteaccess is matched by URI (/admin/...), without when by host. *}
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="{$a4_brand_name|wash}" aria-label="{$a4_brand_name|wash}">
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
            <a class="brand" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl} title="{$a4_brand_name|wash}" aria-label="{$a4_brand_name|wash}">
            </a>
            {* The front page of the default siteaccess: scheme, host, port and siteaccess
               path as the settings and this request call for (ezpSiteAccessURL). *}
            <a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}" aria-label="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
                <span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>
            </a>
        {/if}
    </div>
{undef $a4_brand_name}
