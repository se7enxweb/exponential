{* exphelp/search (doc/features/6.0/help-system.md): the results page. Variables: help_search (query, total, offset,
   limit, hits, facets), help_filters, help_audiences, help_categories
   (kernel/private/classes/views/exphelp/search.php). Snippets are escaped HTML with <mark>: not washed.
   Skeleton: workstream D/F. *}
<div class="a4-page a4-help a4-help-search-page">
    <h1>{'Search the help'|i18n( 'kernel/exphelp' )}</h1>
    <ul>
    {foreach $help_search.hits as $hit}
        <li><a href={$hit.url|ezurl}>{$hit.title|wash}</a></li>
    {/foreach}
    </ul>
</div>
