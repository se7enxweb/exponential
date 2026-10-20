{* exphelp/extensions (doc/eng-us/features/6.0/help-system.md, "Documentation of the extensions"): the documentation of the
   active extensions. Without an extension: each extension with pages, its overview's title and its number of pages. With
   one (exphelp/extensions/<name>): its pages by folder, opened in the help's viewer, and a search limited to them
   (exphelp/search/(extension)/<name>; exphelp.js searches as you type). Shared classes only (components.css,
   stylesheets/modules/help.css); no inline CSS or script.
   Variables (kernel/private/classes/views/exphelp/extensions.php): help_extensions (name => name, title, count,
   index_id, pages), help_extension (the extension shown, or false), help_folders (name, pages of id, title, summary),
   help_i18n_json.
   The same file is in design/admin and design/admin4. *}
{def $help_ctx = 'kernel/exphelp'}
<div class="a4-page a4-help-frame a4-help-searchpage" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <header class="a4-pagehead">
        <div class="a4-pagehead-main">
            <p class="a4-pagehead-pre"><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( $help_ctx )}</a>{if $help_extension} · <a href={'/exphelp/extensions'|ezurl}>{'Extensions'|i18n( $help_ctx )}</a>{/if}</p>
            {if $help_extension}
            <h1 class="a4-pagehead-title"><span class="a4-icon a4-bi-puzzle" aria-hidden="true"></span> {$help_extension.name|wash}</h1>
            <p class="a4-pagehead-sub">{$help_extension.title|wash} · {if eq( $help_extension.count, 1 )}{'1 page'|i18n( $help_ctx )}{else}{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_extension.count ) )|wash}{/if}</p>
            {else}
            <h1 class="a4-pagehead-title"><span class="a4-icon a4-bi-puzzle" aria-hidden="true"></span> {'Extensions'|i18n( $help_ctx )}</h1>
            <p class="a4-pagehead-sub">{'The documentation the active extensions bring with them: their guides, README, changelog and license.'|i18n( $help_ctx )}</p>
            {/if}
        </div>
    </header>

    {if $help_extension}
    <form class="a4-help-search a4-help-search-wide" action={'/exphelp/search'|ezurl} method="post" role="search" data-exphelp-search data-exphelp-endpoint={'/exphelp/search'|ezurl} data-exphelp-canonical-base={'/exphelp/search'|ezurl}>
        <div class="a4-help-search-field">
            <span class="a4-icon a4-bi-search" aria-hidden="true"></span>
            <label class="a4-sr-only" for="help-ext-q">{'Search the documentation of %extension'|i18n( $help_ctx,, hash( '%extension', $help_extension.name ) )|wash}</label>
            <input type="search" id="help-ext-q" name="q" value="" placeholder="{'Search the documentation of %extension'|i18n( $help_ctx,, hash( '%extension', $help_extension.name ) )|wash}" autocomplete="off" spellcheck="false" aria-controls="help-ext-results" data-exphelp-search-input />
            <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( $help_ctx )}</button>
        </div>
        <input type="hidden" name="extension" value="{$help_extension.name|downcase|wash}" data-exphelp-filter />
        <div id="help-ext-results" data-exphelp-results aria-live="polite" hidden></div>
    </form>

    {foreach $help_folders as $help_folder}
    <section class="a4-card" aria-label="{cond( $help_folder.name|eq( '' ), $help_extension.name, $help_folder.name )|wash}">
        {if $help_folder.name|ne( '' )}<div class="a4-help-section-head"><h2><span class="a4-icon a4-bi-folder" aria-hidden="true"></span>{$help_folder.name|wash}</h2></div>{/if}
        <ul class="a4-help-featured">
        {foreach $help_folder.pages as $help_page}
            <li class="a4-help-feature">
                <a href={concat( 'exphelp/viewer/', $help_page.id )|ezurl}>
                    <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-journal-text"></span></span>
                    <span class="a4-help-feature-text">
                        <span class="a4-help-feature-title">{$help_page.title|wash}</span>
                        {if $help_page.summary|ne( '' )}<span class="a4-help-feature-summary">{$help_page.summary|shorten( 140 )|wash}</span>{/if}
                    </span>
                </a>
            </li>
        {/foreach}
        </ul>
    </section>
    {/foreach}
    {else}
    <section class="a4-card" aria-label="{'Extensions'|i18n( $help_ctx )|wash}">
        {if $help_extensions|count|eq( 0 )}
        <p class="a4-meta">{'No active extension brings documentation of its own.'|i18n( $help_ctx )}</p>
        {else}
        <ul class="a4-help-featured">
        {foreach $help_extensions as $help_ext_item}
            <li class="a4-help-feature">
                <a href={concat( 'exphelp/extensions/', $help_ext_item.name )|ezurl}>
                    <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-puzzle"></span></span>
                    <span class="a4-help-feature-text">
                        <span class="a4-help-feature-title">{$help_ext_item.name|wash}</span>
                        <span class="a4-help-feature-summary">{$help_ext_item.title|wash}</span>
                        <span class="a4-help-feature-meta">{if eq( $help_ext_item.count, 1 )}{'1 page'|i18n( $help_ctx )}{else}{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_ext_item.count ) )|wash}{/if}</span>
                    </span>
                </a>
            </li>
        {/foreach}
        </ul>
        {/if}
    </section>
    {/if}
</div>
{undef}
{ezscript_require( array( 'exphelp.js' ) )}
