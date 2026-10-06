{* Search statistics (search/stats).

   What is recorded and where, an overview (phrases, searches, and those that found nothing), a search over the
   phrases, a filter for phrases that found nothing, the order, then the table of phrases with how often each was
   searched for and how many results it found on average. Reset statistics empties the log after a confirmation
   that opens in place.

   The same file is in design/admin and design/admin4. The figures, the filter and the order come from the view
   (search_stats_summary, search_stats_show, search_stats_sort); without them (an older view class) the page shows
   the table and the reset as before. Everything works without javascript. Guide: doc/guides/urls-and-aliases.md *}
{include uri='design:search/exp_style.tpl'}

{def $summary = first_set( $search_stats_summary, false() )
     $logging = first_set( $search_stats_logging, false() )
     $search = first_set( $search_stats_search, '' )
     $search_suffix = first_set( $search_stats_suffix, '' )
     $sort = first_set( $search_stats_sort, 'count' )
     $show = first_set( $search_stats_show, 'all' )
     $queries = first_set( $search_stats_queries, hash() )
     $page_max = first_set( $search_stats_page_max, 0 )
     $page_limit = first_set( $limit, $view_parameters.limit, 10 )
     $total = first_set( $search_total_count, $search_list_count )
     $sort_part = cond( $sort|ne( 'count' ), concat( '/(sort)/', $sort ), '' )
     $show_part = cond( $show|ne( 'all' ), concat( '/(show)/', $show ), '' )}

<div class="context-block exp-searchstats">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Search statistics'|i18n( 'design/admin/search/stats' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Which words visitors search for on the site, how often, and how many results they got on average. A phrase that is searched for often and finds nothing points to content that is missing, or to words the content does not use.'|i18n( 'design/admin/search/stats' )}</p>

{if first_set( $search_stats_reset, false() )}
<div class="exp-feedback is-ok" role="status">{'The search statistics were reset. Counting starts again with the next search.'|i18n( 'design/admin/search/stats' )}</div>
{/if}

{if $logging}
    {if $logging.enabled|count|eq( 0 )}
<div class="exp-feedback is-warn">
    <p><strong>{'No siteaccess records searches at the moment.'|i18n( 'design/admin/search/stats' )}</strong></p>
    <p>{'A search is only counted when the siteaccess the visitor searches in has LogSearchStats=enabled in the [SearchSettings] block of its site.ini; it is disabled by default. What is listed below was recorded earlier.'|i18n( 'design/admin/search/stats' )}</p>
</div>
    {else}
<div class="exp-feedback is-info"><p>{'Searches are recorded on: %siteaccesses.'|i18n( 'design/admin/search/stats',, hash( '%siteaccesses', $logging.enabled|implode( ', ' ) ) )|wash}</p></div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="stats-overview-title">
<h2 class="exp-sr" id="stats-overview-title">{'Overview'|i18n( 'design/admin/search/stats' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.phrases}</strong><span>{'Different phrases'|i18n( 'design/admin/search/stats' )}</span></li>
    <li class="exp-figure"><strong>{$summary.searches}</strong><span>{'Searches'|i18n( 'design/admin/search/stats' )}</span></li>
    <li class="exp-figure{if $summary.phrases_none|gt( 0 )} is-attention{/if}{if eq( $show, 'none' )} is-current{/if}"><a href={concat( '/search/stats', $sort_part, '/(show)/none' )|ezurl}><strong>{$summary.phrases_none}</strong> <span>{'Phrases that found nothing'|i18n( 'design/admin/search/stats' )}</span></a></li>
    <li class="exp-figure"><strong>{if $summary.searches|gt( 0 )}{div( mul( $summary.searches_none, 100 ), $summary.searches )|round}&nbsp;%{else}&ndash;{/if}</strong><span>{'Of all searches found nothing'|i18n( 'design/admin/search/stats' )}</span></li>
</ul>
</section>
{/if}

<section aria-labelledby="stats-find-title">
<h2 class="exp-sr" id="stats-find-title">{'Find phrases'|i18n( 'design/admin/search/stats' )}</h2>
<form class="exp-toolbar" method="get" action={concat( '/search/stats', $sort_part, $show_part )|ezurl} role="search">
    <div class="exp-field exp-field-wide">
        <label for="stats-search">{'Find a phrase'|i18n( 'design/admin/search/stats' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="stats-search" name="q" value="{$search|wash}" autocomplete="off" maxlength="200" aria-describedby="stats-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/search/stats' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( '/search/stats', $sort_part, $show_part )|ezurl}>{'Clear search'|i18n( 'design/admin/search/stats' )}</a>{/if}
        </div>
        <span class="exp-help" id="stats-search-help">{'Any part of a phrase. Upper and lower case are the same.'|i18n( 'design/admin/search/stats' )}</span>
    </div>
    <div class="exp-field">
        <span id="stats-show-label"><strong>{'Show'|i18n( 'design/admin/search/stats' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="stats-show-label">
            <li>{if eq( $show, 'all' )}<span class="current" aria-current="true">{'All phrases'|i18n( 'design/admin/search/stats' )}</span>{else}<a href={concat( '/search/stats', $sort_part, $search_suffix )|ezurl}>{'All phrases'|i18n( 'design/admin/search/stats' )}</a>{/if}</li>
            <li>{if eq( $show, 'none' )}<span class="current" aria-current="true">{'Found nothing'|i18n( 'design/admin/search/stats' )}</span>{else}<a href={concat( '/search/stats', $sort_part, '/(show)/none', $search_suffix )|ezurl}>{'Found nothing'|i18n( 'design/admin/search/stats' )}</a>{/if}</li>
        </ul>
    </div>
    <div class="exp-field">
        <span id="stats-sort-label"><strong>{'Order'|i18n( 'design/admin/search/stats' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="stats-sort-label">
        {foreach array( hash( 'sort', 'count', 'text', 'Most searched'|i18n( 'design/admin/search/stats' ) ),
                        hash( 'sort', 'phrase', 'text', 'Phrase A to Z'|i18n( 'design/admin/search/stats' ) ),
                        hash( 'sort', 'fewest', 'text', 'Fewest results'|i18n( 'design/admin/search/stats' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( '/search/stats', cond( $tab.sort|ne( 'count' ), concat( '/(sort)/', $tab.sort ), '' ), $show_part, $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</form>
</section>

<form action={'/search/stats/'|ezurl} method="post">

<section class="exp-section" aria-labelledby="stats-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="stats-list-title">{if eq( $show, 'none' )}{'Phrases that found nothing'|i18n( 'design/admin/search/stats' )}{else}{'Phrases'|i18n( 'design/admin/search/stats' )}{/if}</h2>
    {if $search_list_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/search/stats',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $search_list_count ), '%count', $search_list_count ) )}</span>
    {/if}
</div>

{if $most_frequent_phrase_array|count|eq( 0 )}
<p class="exp-empty">
{if $total|eq( 0 )}
    {'No search has been recorded yet. Searches appear here once a siteaccess records them (LogSearchStats=enabled) and visitors search.'|i18n( 'design/admin/search/stats' )}
{elseif $search|ne( '' )}
    {'No phrase matches this search. Search for a shorter part, or clear the search.'|i18n( 'design/admin/search/stats' )}
{elseif eq( $show, 'none' )}
    {'Every recorded phrase found at least one result.'|i18n( 'design/admin/search/stats' )}
{else}
    {'The list is empty.'|i18n( 'design/admin/search/stats' )}
{/if}
</p>
{else}
<div class="exp-table-wrap">
<table class="exp-table">
<caption class="exp-sr">{'Search phrases with the number of searches and the average number of results'|i18n( 'design/admin/search/stats' )}</caption>
<thead>
<tr>
    <th scope="col">{'Phrase'|i18n( 'design/admin/search/stats' )}</th>
    <th scope="col" class="exp-num">{'Number of phrases'|i18n( 'design/admin/search/stats' )}</th>
    <th scope="col" class="exp-bar-col"><span class="exp-sr">{'Share of the most searched phrase on this page'|i18n( 'design/admin/search/stats' )}</span></th>
    <th scope="col" class="exp-num">{'Average result returned'|i18n( 'design/admin/search/stats' )}</th>
    <th scope="col"><span class="exp-sr">{'Actions'|i18n( 'design/admin/search/stats' )}</span></th>
</tr>
</thead>
<tbody>
{foreach $most_frequent_phrase_array as $phrase}
<tr{if $phrase.result_count|eq( 0 )} class="is-none"{/if}>
    <td class="exp-phrase">{$phrase.phrase|wash}{if $phrase.result_count|eq( 0 )} <span class="exp-badge is-warn">{'Found nothing'|i18n( 'design/admin/search/stats' )}</span>{/if}</td>
    <td class="exp-num">{$phrase.phrase_count}</td>
    <td class="exp-bar-col" aria-hidden="true">{if $page_max|gt( 0 )}<span class="exp-bar"><span style="width: {div( mul( $phrase.phrase_count, 100 ), $page_max )|round}%"></span></span>{/if}</td>
    <td class="exp-num">{$phrase.result_count|l10n( number )}</td>
    <td>{if is_set( $queries[$phrase.id] )}<a class="exp-btn exp-btn-small" href={concat( '/content/search', $queries[$phrase.id] )|ezurl} title="{'Run this search in the administration interface to see what it finds now.'|i18n( 'design/admin/search/stats' )}">{'Search now'|i18n( 'design/admin/search/stats' )}</a>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}

<div class="exp-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/search/stats' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_index|inc, $limit_choice )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_search_stats_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/search/stats',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/search/stats'
             page_uri_suffix=$search_suffix
             item_count=$search_list_count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

<div class="exp-bottombar">
{if $total|gt( 0 )}
    <details class="exp-confirm">
        <summary>{'Reset statistics'|i18n( 'design/admin/search/stats' )}</summary>
        <div>
            <p>{'This removes all %count recorded phrases and their counts for good, not only those shown. It cannot be undone.'|i18n( 'design/admin/search/stats',, hash( '%count', $total ) )}</p>
            <button type="submit" class="exp-btn exp-btn-danger" name="ResetSearchStatsButton" value="{'Reset statistics'|i18n( 'design/admin/search/stats' )}" title="{'Clear the search log.'|i18n( 'design/admin/search/stats' )}">{'Remove all recorded phrases'|i18n( 'design/admin/search/stats' )}</button>
        </div>
    </details>
{else}
    <button type="submit" class="exp-btn" name="ResetSearchStatsButton" value="{'Reset statistics'|i18n( 'design/admin/search/stats' )}" disabled="disabled">{'Reset statistics'|i18n( 'design/admin/search/stats' )}</button>
    <p class="exp-meta">{'There is nothing to reset.'|i18n( 'design/admin/search/stats' )}</p>
{/if}
</div>

</form>

</div></div></div>
</div>
