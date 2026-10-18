{* The header's context help: a round "?" link, among the header's tools after light/dark, to the help page of the
   page being shown (exphelp/viewer, at the heading the map names), or to the help dashboard when nothing in
   exphelp.ini [ContextHelp] Map[] answers it (expHelpContextRegistry; doc/features/6.0/help-system.md).

   The page layout looks the help up before its cache blocks, as $a4_help, and keys the header's cache block by
   $a4_help.key: that block is shared by every page of a navigation part, so without the key it would keep the
   first page's link. An include without $a4_help (admin4l's header block) looks it up itself. Nothing is drawn
   for a user without exphelp/read (the fetch returns false) or with the help switched off.

   A link, not a button: it opens a page, in the same window; the name says what it opens (aria-label and title,
   "Help: <page title>" when the page is known). The box is as large as the other tools (36px, .a4-header-btn) and
   is in the markup from the first paint, so it moves nothing when the page is drawn. *}
{def $a4_help_ctx = false()}
{if is_set( $a4_help )}
    {set $a4_help_ctx = $a4_help}
{else}
    {def $a4_help_params = module_params()}
    {set $a4_help_ctx = fetch( 'exphelp', 'context', hash( 'module', first_set( $a4_help_params.module_name, '' ),
                                                             'view', first_set( $a4_help_params.function_name, '' ),
                                                             'design', ezini( 'DesignSettings', 'SiteDesign' ) ) )}
    {undef $a4_help_params}
{/if}
{if $a4_help_ctx}
{def $a4_help_label = cond( $a4_help_ctx.title|ne( '' ),
                            'Help: %title'|i18n( 'kernel/exphelp', '', hash( '%title', $a4_help_ctx.title ) ),
                            'Help for this page'|i18n( 'kernel/exphelp' ) )}
<div id="header-help" class="header-help">
    <a class="a4-header-btn a4-help-context" href={$a4_help_ctx.url|ezurl} title="{$a4_help_label|wash}" aria-label="{$a4_help_label|wash}" data-help-level="{$a4_help_ctx.level|wash}">
        <span class="a4-icon a4-bi-question-circle" aria-hidden="true"></span>
    </a>
</div>
{undef $a4_help_label}
{/if}
{undef $a4_help_ctx}
