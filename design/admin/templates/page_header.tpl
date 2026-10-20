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
   With the Help menu (fetch( 'exphelp', 'context_menu' )) it is a native <details> disclosure, as admin4's "?" menu
   but without a script: a search of the help, the help for this page, the related topics, the quick links and the
   Help center. The header is cached per address and roles (pagelayout.tpl), the same keys the menu depends on. *}
{def $header_help_mp  = module_params()
     $header_help_ctx = fetch( 'exphelp', 'context', hash( 'module', first_set( $header_help_mp.module_name, '' ),
                                                           'view', first_set( $header_help_mp.function_name, '' ),
                                                           'design', ezini( 'DesignSettings', 'SiteDesign', 'site.ini' ) ) )
     $header_help_label = ''
     $header_help_menu = false()}
{if $header_help_ctx}
{if $header_help_ctx.title}
    {set $header_help_label = 'Help: %title'|i18n( 'kernel/exphelp',, hash( '%title', $header_help_ctx.title ) )}
{else}
    {set $header_help_label = 'Help for this page'|i18n( 'kernel/exphelp' )}
{/if}
{set $header_help_menu = fetch( 'exphelp', 'context_menu', hash( 'module', first_set( $header_help_mp.module_name, '' ),
                                                                 'view', first_set( $header_help_mp.function_name, '' ),
                                                                 'design', ezini( 'DesignSettings', 'SiteDesign', 'site.ini' ) ) )}
<div id="header-help" class="header-help">
{if $header_help_menu}
    {def $header_help_names = hash( 'getting_started',    'Getting started'|i18n( 'kernel/exphelp' ),
                                    'keyboard_shortcuts', 'Keyboard shortcuts'|i18n( 'kernel/exphelp' ),
                                    'whats_new',          "What's new"|i18n( 'kernel/exphelp' ),
                                    'report',             'Report an error'|i18n( 'kernel/exphelp' ),
                                    'git',                'Develop with Git'|i18n( 'kernel/exphelp' ) )}
    <details class="header-help-menu">
        <summary class="a4-help-context" title="{'Help menu'|i18n( 'kernel/exphelp' )|wash}"><span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span><span class="header-help-label">{'Help'|i18n( 'kernel/exphelp' )}</span><span class="a4-sr-only">{'Help menu'|i18n( 'kernel/exphelp' )|wash}</span></summary>
        <div class="header-help-panel">
            <form class="header-help-search" action={'/exphelp/search'|ezurl} method="post" role="search">
                <label class="a4-sr-only" for="header-help-q">{'Search the help'|i18n( 'kernel/exphelp' )|wash}</label>
                <input type="search" id="header-help-q" class="a4-input" name="q" maxlength="200" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" />
                <button type="submit" class="a4-btn">{'Search'|i18n( 'kernel/exphelp' )|wash}</button>
            </form>
            <a class="header-help-center" href={'/exphelp/dashboard'|ezurl}>{'Open the Help center'|i18n( 'kernel/exphelp' )|wash}</a>
            <a class="header-help-front" href={$header_help_ctx.url|ezurl} title="{$header_help_label|wash}"><strong>{'Help for this page'|i18n( 'kernel/exphelp' )|wash}</strong>{if $header_help_ctx.title}<span>{$header_help_ctx.title|wash}</span>{/if}</a>
            {if $header_help_menu.topics|count}
            <p class="header-help-heading" id="header-help-topics">{'Related topics'|i18n( 'kernel/exphelp' )|wash}</p>
            <ul class="header-help-list" aria-labelledby="header-help-topics">
                {foreach $header_help_menu.topics as $header_help_item}
                <li><a href={$header_help_item.url|ezurl}>{$header_help_item.title|wash}</a></li>
                {/foreach}
            </ul>
            {/if}
            {if $header_help_menu.links|count}
            <p class="header-help-heading" id="header-help-links">{'Quick links'|i18n( 'kernel/exphelp' )|wash}</p>
            <ul class="header-help-list" aria-labelledby="header-help-links">
                {foreach $header_help_menu.links as $header_help_item}
                <li><a href={$header_help_item.url|ezurl}>{first_set( $header_help_names[$header_help_item.key], $header_help_item.title )|wash}</a></li>
                {/foreach}
            </ul>
            {/if}
        </div>
    </details>
    {undef $header_help_names}
{else}
    <a class="a4-help-context" href={$header_help_ctx.url|ezurl} title="{$header_help_label|wash}" aria-label="{$header_help_label|wash}"><span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span><span class="header-help-label">{'Help'|i18n( 'kernel/exphelp' )}</span></a>
{/if}
</div>
{/if}
{undef $header_help_mp $header_help_ctx $header_help_label $header_help_menu}
