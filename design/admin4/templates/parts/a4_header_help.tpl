{* The header's context help: a round "?" link, among the header's tools after light/dark, to the help page of the
   page being shown (exphelp/viewer, at the heading the map names), or to the help dashboard when nothing in
   exphelp.ini [ContextHelp] Map[] answers it (expHelpContextRegistry; doc/eng-us/features/6.0/help-system.md).

   The page layout looks the help up before its cache blocks, as $a4_help, and keys the header's cache block by
   $a4_help.key: that block is shared by every page of a navigation part, so without the key it would keep the
   first page's link. An include without $a4_help (admin4l's header block) looks it up itself. Nothing is drawn
   for a user without exphelp/read (the fetch returns false) or with the help switched off.

   A link, not a button: it opens a page, in the same window; the name says what it opens (aria-label and title,
   "Help: <page title>" when the page is known). The box is as large as the other tools (36px, .a4-header-btn) and
   is in the markup from the first paint, so it moves nothing when the page is drawn. *}
{* The Help menu: the same "?" as a menu button (aria-expanded, aria-controls) in the place of the link, as the site
   menu's globe (parts/a4_header_site.tpl); admin4.js shows the button and hides the link, so without the script the
   link stays and opens the help of the page. Its panel: a search of the help (exphelp/search; the view answers the
   posted form with its canonical address), the Help center right below it, the help of this page, the topics related
   to it and the quick links (fetch exphelp/context_menu, from the small list of served pages, never the index; drawn
   once per page and module in the header's cache block, keyed by $a4_help.key). Arrow keys, Home, End and Esc as in
   the site menu; the keys follow this order. *}
{def $a4_help_ctx = false()
     $a4_help_params = module_params()}
{if is_set( $a4_help )}
    {set $a4_help_ctx = $a4_help}
{else}
    {set $a4_help_ctx = fetch( 'exphelp', 'context', hash( 'module', first_set( $a4_help_params.module_name, '' ),
                                                             'view', first_set( $a4_help_params.function_name, '' ),
                                                             'design', ezini( 'DesignSettings', 'SiteDesign' ) ) )}
{/if}
{if $a4_help_ctx}
{def $a4_help_label = cond( $a4_help_ctx.title|ne( '' ),
                            'Help: %title'|i18n( 'kernel/exphelp', '', hash( '%title', $a4_help_ctx.title ) ),
                            'Help for this page'|i18n( 'kernel/exphelp' ) )
     $a4_help_menu_label = 'Help menu'|i18n( 'kernel/exphelp' )
     $a4_help_menu = fetch( 'exphelp', 'context_menu', hash( 'module', first_set( $a4_help_params.module_name, '' ),
                                                               'view', first_set( $a4_help_params.function_name, '' ),
                                                               'design', ezini( 'DesignSettings', 'SiteDesign' ) ) )
     $a4_help_link_names = hash( 'getting_started',    'Getting started'|i18n( 'kernel/exphelp' ),
                                 'keyboard_shortcuts', 'Keyboard shortcuts'|i18n( 'kernel/exphelp' ),
                                 'whats_new',          "What's new"|i18n( 'kernel/exphelp' ),
                                 'report',             'Report an error'|i18n( 'kernel/exphelp' ),
                                 'git',                'Develop with Git'|i18n( 'kernel/exphelp' ) )}
<div id="header-help" class="header-help">
    <a class="a4-header-btn a4-help-context" href={$a4_help_ctx.url|ezurl} title="{$a4_help_label|wash}" aria-label="{$a4_help_label|wash}" data-help-level="{$a4_help_ctx.level|wash}">
        <span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span>
    </a>
    <div id="a4-help-menu-wrap" class="a4-site-menu-wrap a4-help-menu-wrap" hidden>
        <button type="button" id="a4-help-toggle" class="a4-header-btn a4-help-toggle" aria-expanded="false" aria-controls="a4-help-menu" title="{$a4_help_menu_label|wash}">
            <span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span>
            <span class="a4-sr-only">{$a4_help_menu_label|wash}</span>
        </button>
        <div id="a4-help-menu" class="a4-site-menu a4-help-menu" hidden>
            <form class="a4-help-menu-search" action={'/exphelp/search'|ezurl} method="post" role="search">
                <label class="a4-sr-only" for="a4-help-menu-q">{'Search the help'|i18n( 'kernel/exphelp' )|wash}</label>
                <input type="search" id="a4-help-menu-q" class="a4-input" name="q" maxlength="200" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" />
                <button type="submit" class="a4-btn">{'Search'|i18n( 'kernel/exphelp' )|wash}</button>
            </form>
            <a class="a4-help-menu-center" href={'/exphelp/dashboard'|ezurl}>
                <span class="a4-site-menu-name">{'Open the Help center'|i18n( 'kernel/exphelp' )|wash}</span>
            </a>
            <a class="a4-site-menu-front" href={$a4_help_ctx.url|ezurl}>
                <span class="a4-site-menu-name">{'Help for this page'|i18n( 'kernel/exphelp' )|wash}</span>
                {if $a4_help_ctx.title|ne( '' )}<span class="a4-site-menu-meta">{$a4_help_ctx.title|wash}</span>{/if}
            </a>
            {if and( $a4_help_menu, $a4_help_menu.topics|count|gt( 0 ) )}
            <p class="a4-site-menu-heading" id="a4-help-menu-topics">{'Related topics'|i18n( 'kernel/exphelp' )|wash}</p>
            <ul class="a4-site-menu-list" aria-labelledby="a4-help-menu-topics">
                {foreach $a4_help_menu.topics as $a4_help_item}
                <li><a href={$a4_help_item.url|ezurl}><span class="a4-site-menu-name">{$a4_help_item.title|wash}</span></a></li>
                {/foreach}
            </ul>
            {/if}
            {if and( $a4_help_menu, $a4_help_menu.links|count|gt( 0 ) )}
            <p class="a4-site-menu-heading" id="a4-help-menu-links">{'Quick links'|i18n( 'kernel/exphelp' )|wash}</p>
            <ul class="a4-site-menu-list" aria-labelledby="a4-help-menu-links">
                {foreach $a4_help_menu.links as $a4_help_item}
                <li><a href={$a4_help_item.url|ezurl}><span class="a4-site-menu-name">{first_set( $a4_help_link_names[$a4_help_item.key], $a4_help_item.title )|wash}</span></a></li>
                {/foreach}
            </ul>
            {/if}
        </div>
    </div>
</div>
{undef $a4_help_label $a4_help_menu_label $a4_help_menu $a4_help_link_names}
{/if}
{undef $a4_help_ctx $a4_help_params}
