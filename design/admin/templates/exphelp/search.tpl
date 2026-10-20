{* exphelp/search: search and browse the help (doc/eng-us/features/6.0/help-system.md, "Search"). The state is in view
   parameters (/exphelp/search/(q)/words/(category)/id/(audience)/id/(type)/id/(offset)/n); without words the page
   browses every page the filters allow, so it is never empty. It works without scripts: the form is a GET that the
   view redirects to the canonical address, the facets and the pager are plain links. With exphelp.js the same form
   searches as you type, draws its results over the server's list (data-exphelp-server) and keeps the address
   canonical (data-exphelp-canonical-base). Styles: stylesheets/modules/help.css (.a4-help-*); no inline CSS or script.

   Variables (kernel/private/classes/views/exphelp/search.php): help_search (expHelpSearch::search(): query, mode
   browse|search, total, offset, limit, relaxed, results of id, path, title, url, section, anchor, snippet (escaped
   HTML with <mark>), audience[], category, type; facets of audience/category/type => value => count; suggest),
   help_filters (audience, category, type), help_facet_urls (facet => value => address; '' clears), help_pager (prev,
   next, from, to, total, pages of number, url, current), help_category (id, name, icon, count, intro) or false,
   help_audiences, help_categories, help_types (id => name, count), help_extension (the extension the search is limited
   to, (extension)/<name>, or false; help_facet_urls.extension[''] searches the whole help again), help_i18n_json. *}
{def $help_ctx = 'kernel/exphelp'
     $help_audience_ids = array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing', 'partner', 'advocate', 'designer', 'hosting', 'app-creator', 'website-creator', 'supporter', 'sponsor', 'trainer', 'press', 'businesses', 'government', 'customer' )
     $help_audience_names = hash( 'editor', 'Editors'|i18n( 'kernel/exphelp' ), 'admin', 'Administrators'|i18n( 'kernel/exphelp' ),
                                  'developer', 'Developers'|i18n( 'kernel/exphelp' ), 'user', 'End users'|i18n( 'kernel/exphelp' ),
                                  'project-manager', 'Project managers'|i18n( 'kernel/exphelp' ), 'sales-marketing', 'Sales & marketing'|i18n( 'kernel/exphelp' ),
                                  'partner', 'Partners'|i18n( 'kernel/exphelp' ),
                                  'advocate', 'Advocates'|i18n( 'kernel/exphelp' ),
                                  'designer', 'Designers'|i18n( 'kernel/exphelp' ),
                                  'hosting', 'Hosting companies'|i18n( 'kernel/exphelp' ),
                                  'app-creator', 'Application creators'|i18n( 'kernel/exphelp' ),
                                  'website-creator', 'Website creators'|i18n( 'kernel/exphelp' ),
                                  'supporter', 'Supporters'|i18n( 'kernel/exphelp' ),
                                  'sponsor', 'Sponsors'|i18n( 'kernel/exphelp' ),
                                  'customer', 'Customers'|i18n( 'kernel/exphelp' ),
                                  'government', 'Governments'|i18n( 'kernel/exphelp' ),
                                  'businesses', 'Businesses'|i18n( 'kernel/exphelp' ),
                                  'press', 'Press, media and influencers'|i18n( 'kernel/exphelp' ),
                                  'trainer', 'Trainers'|i18n( 'kernel/exphelp' ) )
     $help_audience_icons = hash( 'editor', 'pencil-square', 'admin', 'gear', 'developer', 'code-square', 'user', 'person', 'project-manager', 'diagram-3', 'sales-marketing', 'graph-up-arrow', 'partner', 'building', 'advocate', 'people', 'designer', 'palette', 'hosting', 'database', 'app-creator', 'phone', 'website-creator', 'globe', 'supporter', 'star', 'sponsor', 'piggy-bank', 'customer', 'person-check', 'government', 'bank', 'businesses', 'briefcase', 'press', 'megaphone', 'trainer', 'easel' )
     $help_q = cond( and( is_set( $help_search ), is_set( $help_search.query ) ), $help_search.query, '' )
     $help_browse = and( is_set( $help_search.mode ), $help_search.mode|eq( 'browse' ) )
     $help_f = cond( is_set( $help_filters ), $help_filters, hash() )
     $help_f_audience = cond( is_set( $help_f.audience ), $help_f.audience, '' )
     $help_f_category = cond( is_set( $help_f.category ), $help_f.category, '' )
     $help_f_type = cond( is_set( $help_f.type ), $help_f.type, '' )
     $help_total = cond( and( is_set( $help_search ), is_set( $help_search.total ) ), $help_search.total, 0 )
     $help_results = cond( and( is_set( $help_search ), is_set( $help_search.results ) ), $help_search.results, array() )
     $help_facets = cond( and( is_set( $help_search ), is_set( $help_search.facets ) ), $help_search.facets, hash() )
     $help_links = cond( is_set( $help_facet_urls ), $help_facet_urls, hash() )
     $help_cat = cond( and( is_set( $help_category ), $help_category ), $help_category, false() )
     $help_ext = cond( and( is_set( $help_extension ), $help_extension ), $help_extension, false() )
     $help_i18n = cond( is_set( $help_i18n_json ), $help_i18n_json, '' )
     $help_url = ''
     $help_count = 0}
<div class="a4-page a4-help-frame a4-help-searchpage" data-exphelp{if $help_i18n|ne( '' )} data-exphelp-i18n="{$help_i18n|wash}"{/if}>

    <header class="a4-pagehead">
        <div class="a4-pagehead-main">
            <p class="a4-pagehead-pre"><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( $help_ctx )}</a>{if and( $help_cat, $help_q|ne( '' ) )} · <a href={$help_links.category[$help_cat.id]|ezurl}>{$help_cat.name|wash}</a>{/if}</p>
            <h1 class="a4-pagehead-title">{if $help_q|ne( '' )}{'Results for “%query”'|i18n( $help_ctx,, hash( '%query', $help_q ) )|wash}{elseif $help_cat}{if is_set( $help_cat.icon )}<span class="a4-icon a4-bi-{$help_cat.icon|wash}" aria-hidden="true"></span> {/if}{$help_cat.name|wash}{else}{'Browse the help'|i18n( $help_ctx )}{/if}</h1>
            {if and( $help_cat, $help_q|eq( '' ), is_set( $help_cat.intro ), $help_cat.intro|ne( '' ) )}<p class="a4-pagehead-sub a4-help-catintro">{$help_cat.intro|wash}</p>{/if}
            {if $help_ext}<p class="a4-pagehead-sub">{'Only the documentation of the extension %extension.'|i18n( $help_ctx,, hash( '%extension', $help_ext ) )|wash} <a href={concat( 'exphelp/extensions/', $help_ext )|ezurl}>{'Its pages'|i18n( $help_ctx )}</a> · {if is_set( $help_links.extension )}<a href={$help_links.extension['']|ezurl}>{'Search the whole help'|i18n( $help_ctx )}</a>{/if}</p>{/if}
            <p class="a4-pagehead-sub">{if eq( $help_total, 1 )}{'1 page'|i18n( $help_ctx )}{else}{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_total ) )|wash}{/if}</p>
        </div>
    </header>

    <form class="a4-help-search a4-help-search-wide" action={'/exphelp/search'|ezurl} method="get" role="search" data-exphelp-search data-exphelp-endpoint={'/exphelp/search'|ezurl} data-exphelp-canonical-base={'/exphelp/search'|ezurl}>
        <div class="a4-help-search-field">
            <span class="a4-icon a4-bi-search" aria-hidden="true"></span>
            <label class="a4-sr-only" for="help-search-q">{'Search the help'|i18n( $help_ctx )}</label>
            <input type="search" id="help-search-q" name="q" value="{$help_q|wash}" placeholder="{'Search guides, settings, commands…'|i18n( $help_ctx )|wash}" autocomplete="off" spellcheck="false" aria-controls="help-search-results" data-exphelp-search-input />
            <kbd class="a4-kbd" aria-hidden="true">/</kbd>
            <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( $help_ctx )}</button>
        </div>
        {if $help_ext}<input type="hidden" name="extension" value="{$help_ext|wash}" data-exphelp-filter />{/if}

        <div class="a4-help-facets">
            <fieldset class="a4-help-filters">
                <legend>{'For'|i18n( $help_ctx )}</legend>
                <label class="a4-help-filter"><input type="radio" name="audience" value=""{if $help_f_audience|eq( '' )} checked{/if} data-exphelp-filter /><span>{'Everyone'|i18n( $help_ctx )}</span></label>
                {foreach $help_audience_ids as $aid}
                {set $help_count = cond( and( is_set( $help_facets.audience ), is_set( $help_facets.audience[$aid] ) ), $help_facets.audience[$aid], 0 )}
                <label class="a4-help-filter" data-exphelp-facet="audience:{$aid}"><input type="radio" name="audience" value="{$aid}"{if $help_f_audience|eq( $aid )} checked{/if} data-exphelp-filter /><span><span class="a4-icon a4-bi-{$help_audience_icons[$aid]}" aria-hidden="true"></span>{$help_audience_names[$aid]|wash} <span data-exphelp-count>{$help_count|wash}</span></span></label>
                {/foreach}
            </fieldset>
            {if and( is_set( $help_categories ), $help_categories|count|gt( 0 ) )}
            <div class="a4-field">
                <label for="help-search-category">{'Topic'|i18n( $help_ctx )}</label>
                <select id="help-search-category" name="category" data-exphelp-filter>
                    <option value="">{'All topics'|i18n( $help_ctx )}</option>
                    {foreach $help_categories as $cid => $cat}
                    {set $help_count = cond( and( is_set( $help_facets.category ), is_set( $help_facets.category[$cid] ) ), $help_facets.category[$cid], 0 )}
                    <option value="{$cid|wash}"{if $help_f_category|eq( $cid )} selected{/if}>{$cat.name|wash} ({$help_count|wash})</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            {if and( is_set( $help_types ), $help_types|count|gt( 0 ) )}
            <div class="a4-field">
                <label for="help-search-type">{'Type'|i18n( $help_ctx )}</label>
                <select id="help-search-type" name="type" data-exphelp-filter>
                    <option value="">{'All types'|i18n( $help_ctx )}</option>
                    {foreach $help_types as $tid => $type}
                    {set $help_count = cond( and( is_set( $help_facets.type ), is_set( $help_facets.type[$tid] ) ), $help_facets.type[$tid], 0 )}
                    <option value="{$tid|wash}"{if $help_f_type|eq( $tid )} selected{/if}>{$type.name|wash} ({$help_count|wash})</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            <noscript><button type="submit" class="a4-btn">{'Apply filters'|i18n( $help_ctx )}</button></noscript>
        </div>
    </form>

    {if and( is_set( $help_links.category ), is_set( $help_facets.category ), $help_facets.category|count|gt( 0 ) )}
    <nav class="a4-help-topics" aria-label="{'Topics'|i18n( $help_ctx )|wash}">
        <ul class="a4-help-topics__list">
            <li><a class="a4-tag{if $help_f_category|eq( '' )} is-current{/if}" href={$help_links.category['']|ezurl}{if $help_f_category|eq( '' )} aria-current="page"{/if}>{'All topics'|i18n( $help_ctx )}</a></li>
            {foreach $help_categories as $cid => $cat}
            {if and( is_set( $help_facets.category[$cid] ), is_set( $help_links.category[$cid] ) )}
            <li><a class="a4-tag{if $help_f_category|eq( $cid )} is-current{/if}" href={$help_links.category[$cid]|ezurl}{if $help_f_category|eq( $cid )} aria-current="page"{/if}>{$cat.name|wash} <span class="a4-help-topics__count">{$help_facets.category[$cid]|wash}</span></a></li>
            {/if}
            {/foreach}
        </ul>
    </nav>
    {/if}

    <div id="help-search-results" class="a4-help-searchresults" data-exphelp-results aria-live="polite">
        <div data-exphelp-server>
        {if and( is_set( $help_search.relaxed ), $help_search.relaxed, $help_results|count|gt( 0 ) )}
            <p class="a4-help-results__relaxed">{'No page has every word; showing pages with any of them.'|i18n( $help_ctx )}</p>
        {/if}
        {if $help_results|count|gt( 0 )}
            <ul class="a4-help-results__list{if $help_browse} is-browse{/if}">
            {foreach $help_results as $hit}
                {if is_set( $hit.url )}{set $help_url = $hit.url}{else}{set $help_url = concat( '/exphelp/viewer/', cond( is_set( $hit.id ), $hit.id, $hit.path ) )}{/if}
                {if and( is_set( $hit.anchor ), $hit.anchor|ne( '' ), $help_url|contains( '#' )|not )}{set $help_url = concat( $help_url, '#', $hit.anchor )}{/if}
                <li class="a4-help-result" data-exphelp-result>
                    <a class="a4-help-result__link" href={$help_url|ezurl}>
                        <span class="a4-help-result__title">{if and( is_set( $hit.featured ), $hit.featured )}<span class="a4-icon a4-bi-star" aria-hidden="true"></span> {/if}{$hit.title|wash}</span>
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

            {if and( is_set( $help_pager ), $help_pager.total|gt( $help_results|count ) )}
            <nav class="a4-help-pager" aria-label="{'Result pages'|i18n( $help_ctx )|wash}">
                {if $help_pager.prev}<a class="a4-btn" rel="prev" href={$help_pager.prev|ezurl}>{'Previous'|i18n( $help_ctx )}</a>{/if}
                {if $help_pager.pages|count|gt( 1 )}
                <ul class="a4-help-pager__pages">
                    {foreach $help_pager.pages as $page}
                    <li>{if $page.current}<span class="a4-btn a4-help-pager__page is-current" aria-current="page">{$page.number|wash}</span>{else}<a class="a4-btn a4-btn-ghost a4-help-pager__page" href={$page.url|ezurl}>{$page.number|wash}</a>{/if}</li>
                    {/foreach}
                </ul>
                {/if}
                <span class="a4-meta">{'%from–%to of %total'|i18n( $help_ctx,, hash( '%from', $help_pager.from, '%to', $help_pager.to, '%total', $help_pager.total ) )|wash}</span>
                {if $help_pager.next}<a class="a4-btn" rel="next" href={$help_pager.next|ezurl}>{'Next'|i18n( $help_ctx )}</a>{/if}
            </nav>
            {/if}
        {else}
            <div class="a4-empty a4-help-noresults">
                <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-search"></span></span>
                {if $help_q|ne( '' )}
                <p><strong>{'No page matches “%query”.'|i18n( $help_ctx,, hash( '%query', $help_q ) )|wash}</strong></p>
                <p>{'Try fewer or other words, or another audience or topic.'|i18n( $help_ctx )}</p>
                {else}
                <p><strong>{'No page for this choice.'|i18n( $help_ctx )}</strong></p>
                <p>{'Choose another audience, topic or type.'|i18n( $help_ctx )}</p>
                {/if}
                {if and( is_set( $help_search.suggest ), $help_search.suggest|count|gt( 0 ) )}
                <p>{'Did you mean:'|i18n( $help_ctx )}
                    {foreach $help_search.suggest as $word}<a class="a4-tag" href={concat( '/exphelp/search/(q)/', $word|urlencode )|ezurl}>{$word|wash}</a>{delimiter} {/delimiter}{/foreach}
                </p>
                {/if}
                <p><a class="a4-btn" href={'/exphelp/search'|ezurl}>{'Browse all help pages'|i18n( $help_ctx )}</a>
                   <a class="a4-btn" href={'/exphelp/report'|ezurl}><span class="a4-icon a4-bi-exclamation-diamond" aria-hidden="true"></span>{'Missing a page? Tell us'|i18n( $help_ctx )}</a></p>
            </div>
        {/if}
        </div>
    </div>
    {include uri='design:exphelp/parts/license_notice.tpl' license_page_id=''}
</div>
{ezscript_require( 'exphelp.js' )}
{undef}
