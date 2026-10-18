<div id="header-logo">
{if $ui_context_edit}
    <span title="Exponential{if fetch( 'user', 'current_user' ).is_logged_in} {fetch( 'setup', 'version' )}{/if}">&nbsp;</span>
{else}
    <a href="{ezini('SiteSettings', 'DefaultPage', 'site.ini')|ezurl( 'no' )}" title="Exponential{if fetch( 'user', 'current_user' ).is_logged_in} {fetch( 'setup', 'version' )}{/if}">
    </a>
{/if}
</div>

<div id="header-search">
{include uri='design:page_search.tpl'}
</div>

<div id="header-usermenu">
{if $ui_context_edit}
    <span title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout" class="disabled">{'Logout'|i18n( 'design/admin/pagelayout' )}</span>
{else}
    <a href={'/user/logout'|ezurl} title="{'Logout from the system.'|i18n( 'design/admin/pagelayout' )}" id="header-usermenu-logout">{'Logout'|i18n( 'design/admin/pagelayout' )}</a>
{/if}
</div>


{* The context Help control: the help page of the module and view on screen (fetch( 'exphelp', 'context' ), the help
   system's context map for this design; its url carries the heading anchor), only for a user who may read the help.
   The header is cached per address and roles (pagelayout.tpl), the same keys the help page depends on. *}
{def $header_help_mp  = module_params()
     $header_help_ctx = fetch( 'exphelp', 'context', hash( 'module', first_set( $header_help_mp.module_name, '' ),
                                                           'view', first_set( $header_help_mp.function_name, '' ),
                                                           'design', ezini( 'DesignSettings', 'SiteDesign', 'site.ini' ) ) )
     $header_help_label = ''}
{if $header_help_ctx}
{if $header_help_ctx.title}
    {set $header_help_label = 'Help: %title'|i18n( 'kernel/exphelp',, hash( '%title', $header_help_ctx.title ) )}
{else}
    {set $header_help_label = 'Help for this page'|i18n( 'kernel/exphelp' )}
{/if}
<div id="header-help" class="header-help">
    <a class="a4-help-context" href={$header_help_ctx.url|ezurl} title="{$header_help_label|wash}" aria-label="{$header_help_label|wash}"><span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span><span class="header-help-label">{'Help'|i18n( 'kernel/exphelp' )}</span></a>
</div>
{/if}
{undef $header_help_mp $header_help_ctx $header_help_label}
