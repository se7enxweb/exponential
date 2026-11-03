{* The search page of a site (content/search), for every site design: a design's content/search.tpl that holds
   {include uri='design:content/front_search_page.tpl'} gets the same filtered search as the administration
   (fetch content/filtered_search, expContentSearchFilters): the search as view parameters (/(text)/easter/(class)/16,
   never a query string), the form posted and answered with a 303 to that address, a filter bar (content type, sort
   order), the active filters as chips that each link to the search without them, "Did you mean" (eZ Find), the
   results as the design's line views and the pages. An old ?SearchText= link opens the empty page, as in the
   administration. The look is stylesheets/front_search.css (--exps-* tokens); a design restyles it with those tokens.
   Parameters (optional): result_view (the node view of a result, default line), result_template (instead: a template
   included for each result with $node, for a design that draws results its own way), title (the heading).
   Guide: doc/eng-us/help/searching.md ("Searching on the site"). *}
{def $page = fetch( 'content', 'filtered_search', hash( 'offset', first_set( $view_parameters.offset, 0 ) ) )
     $f = $page.filters
     $search_query = $page.query
     $search_has_text = $f.text|trim|ne( '' )
     $front_excluded = ezini( 'SiteSearchSuggestSettings', 'ExcludeClasses' )
     $front_view = first_set( $result_view, 'line' )
     $sort_names = hash( 'relevance', 'Relevance'|i18n( 'design/admin/content/search' ),
                         'name_asc', 'Name A–Z'|i18n( 'design/admin/content/search' ),
                         'name_desc', 'Name Z–A'|i18n( 'design/admin/content/search' ),
                         'modified_desc', 'Modified, newest first'|i18n( 'design/admin/content/search' ),
                         'modified_asc', 'Modified, oldest first'|i18n( 'design/admin/content/search' ),
                         'published_desc', 'Published, newest first'|i18n( 'design/admin/content/search' ),
                         'published_asc', 'Published, oldest first'|i18n( 'design/admin/content/search' ) )}
{if is_set( $search_data )}{set search_data = $page.search_data}{/if}
<link rel="stylesheet" href={'stylesheets/front_search.css'|ezdesign} />
<div class="exps-search" id="search-page">
<h1 class="exps-search-title">{first_set( $title, 'Search'|i18n( 'design/admin/content/search' ) )|wash}</h1>
<form class="exps-search-form" id="search-form" action={'/content/search/'|ezurl} method="post" role="search" aria-label="{'Search'|i18n( 'design/admin/content/search' )|wash}">
<div class="exps-search-row">
    <label class="exps-sr-only" for="Search">{'Search for'|i18n( 'design/admin/content/search' )}</label>
    <input class="exps-search-input" type="search" name="SearchText" id="Search" value="{$f.text|wash}" maxlength="1024" />
    <button type="submit" class="exps-btn exps-btn-primary" name="SearchButton">{'Search'|i18n( 'design/admin/content/search' )}</button>
    {include uri='design:parts/front_search_suggest.tpl' suggest_id='search-page-suggest'}
</div>
<div class="exps-search-columns">
<details class="exps-search-filters" open="open">
    <summary>{'Filters'|i18n( 'design/admin/content/search' )}</summary>
    <fieldset class="exps-search-filter">
        <legend>{'Content type'|i18n( 'design/admin/content/search' )}</legend>
        <ul class="exps-plain">
        {foreach $page.class_groups as $group}{foreach $group.classes as $class}
        {if and( $front_excluded|contains( $class.identifier )|not, or( $class.selected, $class.count|is_null, $class.count|gt( 0 ) ) )}
            <li><label class="exps-check"><input type="checkbox" name="ClassID[]" value="{$class.id}"{if $class.selected} checked="checked"{/if} /> <span>{$class.name|wash}</span>{if $class.count|is_null|not} <span class="exps-count">({$class.count})</span>{/if}</label></li>
        {/if}
        {/foreach}{/foreach}
        </ul>
    </fieldset>
    <div class="exps-search-filter">
        <label for="search-sort">{'Sort by'|i18n( 'design/admin/content/search' )}</label>
        <select id="search-sort" name="SortBy">
            {foreach $page.sorts as $sort}
            <option value="{$sort}"{if eq( $sort, $f.sort )} selected="selected"{/if}>{$sort_names[$sort]|wash}</option>
            {/foreach}
        </select>
    </div>
    {foreach $page.subtrees as $subtree}<input type="hidden" name="SubTreeArray[]" value="{$subtree.node_id}" />{/foreach}
    <div class="exps-actions">
        <button type="submit" class="exps-btn exps-btn-primary">{'Apply'|i18n( 'design/admin/content/search' )}</button>
        {if $page.has_filters}<a class="exps-btn" href={concat( '/content/search', $search_has_text|choose( '', concat( '/(text)/', $f.text|view_parameter ) ) )|ezurl}>{'Reset'|i18n( 'design/admin/content/search' )}</a>{/if}
    </div>
</details>

<section class="exps-search-main" aria-labelledby="search-results-title">
{if or( $f.text_shortened, $page.text_shortened )}
<p class="exps-notice" role="status">{'The search text was too long and was shortened.'|i18n( 'design/admin/content/search' )}</p>
{/if}
<h2 class="exps-search-count" id="search-results-title">{if $page.requested|not}{'Search'|i18n( 'design/admin/content/search' )}{elseif $search_has_text}{'Results for “%text” (%count)'|i18n( 'design/admin/content/search',, hash( '%text', $f.text, '%count', $page.count ) )|wash}{else}{'Results (%count)'|i18n( 'design/admin/content/search',, hash( '%count', $page.count ) )|wash}{/if}</h2>

{if $page.chips}
<ul class="exps-plain exps-search-chips" aria-label="{'Active filters'|i18n( 'design/admin/content/search' )|wash}">
{foreach $page.chips as $chip}
    {def $chip_name = ''}
    {switch match=$chip.type}
    {case match='class'}{set $chip_name = 'Content type'|i18n( 'design/admin/content/search' )}{/case}
    {case match='subtree'}{set $chip_name = 'Location'|i18n( 'design/admin/content/search' )}{/case}
    {case match='section'}{set $chip_name = 'Section'|i18n( 'design/admin/content/search' )}{/case}
    {case match='language'}{set $chip_name = 'Language'|i18n( 'design/admin/content/search' )}{/case}
    {case}{set $chip_name = 'Filters'|i18n( 'design/admin/content/search' )}{/case}
    {/switch}
    <li><a class="exps-chip" href={concat( '/content/search', $chip.query )|ezurl} title="{'Remove this filter'|i18n( 'design/admin/content/search' )|wash}"><span>{$chip_name|wash}: <bdi>{$chip.label|wash}</bdi></span> <span aria-hidden="true">×</span><span class="exps-sr-only">{'Remove this filter'|i18n( 'design/admin/content/search' )}</span></a></li>
    {undef $chip_name}
{/foreach}
</ul>
{/if}

{if $page.spellcheck}
<p class="exps-notice">{'Did you mean:'|i18n( 'kernel/exphelp' )} <a href={concat( '/content/search', $page.spellcheck.query )|ezurl}>{$page.spellcheck.text|wash}</a></p>
{/if}

{if $page.failed}
<p class="exps-notice exps-notice-bad" role="alert">{'The search could not be run. Try fewer or simpler words, or search again later.'|i18n( 'design/admin/content/search' )}</p>
{elseif $page.requested|not}
<p class="exps-search-empty">{'Type the words to search for.'|i18n( 'design/admin/content/search' )}</p>
{elseif $page.count|not}
<div class="exps-search-empty">
    <p><strong>{if $search_has_text}{'No results were found for “%text”.'|i18n( 'design/admin/content/search',, hash( '%text', $f.text ) )|wash}{else}{'No results were found.'|i18n( 'design/admin/content/search' )}{/if}</strong></p>
    <p>{'Search tips'|i18n( 'design/admin/content/search' )}</p>
    <ul>
        <li>{'Check spelling of keywords.'|i18n( 'design/admin/content/search' )}</li>
        <li>{'Try more general keywords.'|i18n( 'design/admin/content/search' )}</li>
        {if $page.chips}<li>{'Remove a filter above.'|i18n( 'design/admin/content/search' )}</li>{/if}
    </ul>
</div>
{elseif $page.result|not}
<p class="exps-notice" role="status">{'There are no matches on this page.'|i18n( 'design/admin/content/search' )} <a href={concat( '/content/search', $search_query )|ezurl}>{'Go to the first page'|i18n( 'design/admin/content/search' )}</a></p>
{else}
<ol class="exps-plain exps-search-results">
{foreach $page.result as $result}
    <li class="exps-search-result">{if is_set( $result_template )}{include uri=$result_template node=$result}{else}{node_view_gui view=$front_view content_node=$result}{/if}</li>
{/foreach}
</ol>
<nav class="exps-search-pager" aria-label="{'Pages'|i18n( 'design/admin/audit' )|wash}">
{include name=Navigator
         uri='design:navigator/google.tpl'
         page_uri='/content/search'
         page_uri_suffix=$search_query
         item_count=$page.count
         view_parameters=hash( 'offset', $page.offset )
         item_limit=$f.limit}
</nav>
{/if}
</section>
</div>
</form>
</div>
{undef $page $f $search_query $search_has_text $front_excluded $front_view $sort_names}
