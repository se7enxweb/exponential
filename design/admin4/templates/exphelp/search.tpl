{* exphelp/search: the search results page (doc/features/6.0/help-system.md, "Search"). It works without scripts: the
   form is a GET to this page, the filters are plain inputs and the pager plain links. With exphelp.js the same form
   searches as you type and draws its results over the server's list (data-exphelp-server), with the same classes.
   Styles: stylesheets/modules/help.css (.a4-help-*); no inline CSS or script.

   Variables (kernel/private/classes/views/exphelp/search.php): help_search (expHelpSearch::search(): query, total,
   offset, limit, relaxed, results of path/id, title, url?, section, anchor, snippet (escaped HTML with <mark>),
   audience[], category, type; facets of audience/category/type => value => count; suggest), help_filters (audience,
   category, type), help_audiences (id => name, count), help_categories (id => name, icon, count), help_i18n_json. *}
{def $help_ctx = 'kernel/exphelp'
     $help_audience_ids = array( 'editor', 'admin', 'developer', 'user' )
     $help_audience_names = hash( 'editor', 'Editors'|i18n( 'kernel/exphelp' ), 'admin', 'Administrators'|i18n( 'kernel/exphelp' ),
                                  'developer', 'Developers'|i18n( 'kernel/exphelp' ), 'user', 'End users'|i18n( 'kernel/exphelp' ) )
     $help_audience_icons = hash( 'editor', 'pencil-square', 'admin', 'gear', 'developer', 'code-square', 'user', 'person' )
     $help_q = cond( and( is_set( $help_search ), is_set( $help_search.query ) ), $help_search.query, '' )
     $help_f = cond( is_set( $help_filters ), $help_filters, hash() )
     $help_f_audience = cond( is_set( $help_f.audience ), $help_f.audience, '' )
     $help_f_category = cond( is_set( $help_f.category ), $help_f.category, '' )
     $help_f_type = cond( is_set( $help_f.type ), $help_f.type, '' )
     $help_total = cond( and( is_set( $help_search ), is_set( $help_search.total ) ), $help_search.total, 0 )
     $help_offset = cond( and( is_set( $help_search ), is_set( $help_search.offset ) ), $help_search.offset, 0 )
     $help_limit = cond( and( is_set( $help_search ), is_set( $help_search.limit ), $help_search.limit|gt( 0 ) ), $help_search.limit, 20 )
     $help_results = cond( and( is_set( $help_search ), is_set( $help_search.results ) ), $help_search.results, array() )
     $help_facets = cond( and( is_set( $help_search ), is_set( $help_search.facets ) ), $help_search.facets, hash() )
     $help_i18n = cond( is_set( $help_i18n_json ), $help_i18n_json, '' )
     $help_base = concat( '/exphelp/search?q=', $help_q|urlencode, '&audience=', $help_f_audience, '&category=', $help_f_category, '&type=', $help_f_type )
     $help_url = ''
     $help_count = 0}
<div class="a4-page a4-help a4-help-searchpage" data-exphelp{if $help_i18n|ne( '' )} data-exphelp-i18n="{$help_i18n|wash}"{/if}>

    <header class="a4-pagehead">
        <div class="a4-pagehead-main">
            <p class="a4-pagehead-pre"><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( $help_ctx )}</a></p>
            <h1 class="a4-pagehead-title">{if $help_q|ne( '' )}{'Results for “%query”'|i18n( $help_ctx,, hash( '%query', $help_q ) )|wash}{else}{'Search the help'|i18n( $help_ctx )}{/if}</h1>
            {if $help_q|ne( '' )}<p class="a4-pagehead-sub">{if eq( $help_total, 1 )}{'1 page'|i18n( $help_ctx )}{else}{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_total ) )|wash}{/if}</p>{/if}
        </div>
    </header>

    <form class="a4-help-search a4-help-search-wide" action={'/exphelp/search'|ezurl} method="get" role="search" data-exphelp-search data-exphelp-endpoint={'/exphelp/search'|ezurl}>
        <div class="a4-help-search-field">
            <span class="a4-icon a4-bi-search" aria-hidden="true"></span>
            <label class="a4-sr-only" for="help-search-q">{'Search the help'|i18n( $help_ctx )}</label>
            <input type="search" id="help-search-q" name="q" value="{$help_q|wash}" placeholder="{'Search guides, settings, commands…'|i18n( $help_ctx )|wash}" autocomplete="off" spellcheck="false" aria-controls="help-search-results" data-exphelp-search-input />
            <kbd class="a4-kbd" aria-hidden="true">/</kbd>
            <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( $help_ctx )}</button>
        </div>

        <div class="a4-help-facets">
            <fieldset class="a4-help-filters">
                <legend>{'For'|i18n( $help_ctx )}</legend>
                <label class="a4-help-filter"><input type="radio" name="audience" value=""{if $help_f_audience|eq( '' )} checked{/if} data-exphelp-filter /><span>{'Everyone'|i18n( $help_ctx )}</span></label>
                {foreach $help_audience_ids as $aid}
                {set $help_count = cond( and( is_set( $help_facets.audience ), is_set( $help_facets.audience[$aid] ) ), $help_facets.audience[$aid], '' )}
                <label class="a4-help-filter" data-exphelp-facet="audience:{$aid}"><input type="radio" name="audience" value="{$aid}"{if $help_f_audience|eq( $aid )} checked{/if} data-exphelp-filter /><span><span class="a4-icon a4-bi-{$help_audience_icons[$aid]}" aria-hidden="true"></span>{$help_audience_names[$aid]|wash} <span data-exphelp-count>{if $help_q|ne( '' )}{$help_count|wash}{/if}</span></span></label>
                {/foreach}
            </fieldset>
            {if and( is_set( $help_categories ), $help_categories|count|gt( 0 ) )}
            <div class="a4-field">
                <label for="help-search-category">{'Topic'|i18n( $help_ctx )}</label>
                <select id="help-search-category" name="category" data-exphelp-filter>
                    <option value="">{'All topics'|i18n( $help_ctx )}</option>
                    {foreach $help_categories as $cid => $cat}
                    <option value="{$cid|wash}"{if $help_f_category|eq( $cid )} selected{/if}>{$cat.name|wash}</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            {if and( is_set( $help_facets.type ), $help_facets.type|count|gt( 0 ) )}
            <div class="a4-field">
                <label for="help-search-type">{'Type'|i18n( $help_ctx )}</label>
                <select id="help-search-type" name="type" data-exphelp-filter>
                    <option value="">{'All types'|i18n( $help_ctx )}</option>
                    {foreach $help_facets.type as $tid => $tcount}
                    <option value="{$tid|wash}"{if $help_f_type|eq( $tid )} selected{/if}>{$tid|wash} ({$tcount|wash})</option>
                    {/foreach}
                </select>
            </div>
            {/if}
        </div>
    </form>

    <div id="help-search-results" class="a4-help-searchresults" data-exphelp-results aria-live="polite">
        <div data-exphelp-server>
        {if and( is_set( $help_search.relaxed ), $help_search.relaxed, $help_results|count|gt( 0 ) )}
            <p class="a4-help-results__relaxed">{'No page has every word; showing pages with any of them.'|i18n( $help_ctx )}</p>
        {/if}
        {if $help_results|count|gt( 0 )}
            <ul class="a4-help-results__list">
            {foreach $help_results as $hit}
                {if is_set( $hit.url )}{set $help_url = $hit.url}{else}{set $help_url = concat( '/exphelp/viewer/', cond( is_set( $hit.id ), $hit.id, $hit.path ) )}{/if}
                {if and( is_set( $hit.anchor ), $hit.anchor|ne( '' ), $help_url|contains( '#' )|not )}{set $help_url = concat( $help_url, '#', $hit.anchor )}{/if}
                <li class="a4-help-result" data-exphelp-result>
                    <a class="a4-help-result__link" href={$help_url|ezurl}>
                        <span class="a4-help-result__title">{$hit.title|wash}</span>
                        {if and( is_set( $hit.section ), $hit.section|ne( '' ), $hit.section|ne( $hit.title ) )}<span class="a4-help-result__section">{$hit.section|wash}</span>{/if}
                        {if and( is_set( $hit.snippet ), $hit.snippet|ne( '' ) )}<span class="a4-help-result__snippet">{$hit.snippet}</span>{/if}
                        <span class="a4-help-result__meta">
                            {if is_set( $hit.audience )}{foreach $hit.audience as $aid}{if is_set( $help_audience_names[$aid] )}<span class="a4-tag a4-help-pill is-{$aid}">{$help_audience_names[$aid]|wash}</span>{/if}{/foreach}{/if}
                            {if and( is_set( $hit.category ), $hit.category|ne( '' ), is_set( $help_categories ), is_set( $help_categories[$hit.category] ) )}<span class="a4-tag">{$help_categories[$hit.category].name|wash}</span>{/if}
                        </span>
                    </a>
                </li>
            {/foreach}
            </ul>

            {if gt( $help_total, $help_limit )}
            <nav class="a4-help-pager" aria-label="{'Result pages'|i18n( $help_ctx )|wash}">
                {if gt( $help_offset, 0 )}<a class="a4-btn" rel="prev" href={concat( $help_base, '&offset=', max( 0, sub( $help_offset, $help_limit ) ) )|ezurl}>{'Previous'|i18n( $help_ctx )}</a>{/if}
                <span class="a4-meta">{'%from–%to of %total'|i18n( $help_ctx,, hash( '%from', sum( $help_offset, 1 ), '%to', min( $help_total, sum( $help_offset, $help_limit ) ), '%total', $help_total ) )|wash}</span>
                {if lt( sum( $help_offset, $help_limit ), $help_total )}<a class="a4-btn" rel="next" href={concat( $help_base, '&offset=', sum( $help_offset, $help_limit ) )|ezurl}>{'Next'|i18n( $help_ctx )}</a>{/if}
            </nav>
            {/if}
        {elseif $help_q|ne( '' )}
            <div class="a4-empty a4-help-noresults">
                <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-search"></span></span>
                <p><strong>{'No page matches “%query”.'|i18n( $help_ctx,, hash( '%query', $help_q ) )|wash}</strong></p>
                <p>{'Try fewer or other words, or another audience or topic.'|i18n( $help_ctx )}</p>
                {if and( is_set( $help_search.suggest ), $help_search.suggest|count|gt( 0 ) )}
                <p>{'Did you mean:'|i18n( $help_ctx )}
                    {foreach $help_search.suggest as $word}<a class="a4-tag" href={concat( '/exphelp/search?q=', $word|urlencode )|ezurl}>{$word|wash}</a>{delimiter} {/delimiter}{/foreach}
                </p>
                {/if}
                <p><a class="a4-btn" href={'/exphelp/report'|ezurl}><span class="a4-icon a4-bi-exclamation-diamond" aria-hidden="true"></span>{'Missing a page? Tell us'|i18n( $help_ctx )}</a></p>
            </div>
        {else}
            <p class="a4-help-results__status">{'Type a word to search every help page.'|i18n( $help_ctx )}</p>
        {/if}
        </div>
    </div>
</div>
{ezscript_require( 'exphelp.js' )}
{undef}
