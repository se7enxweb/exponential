<form action={'/search/stats/'|ezurl} method="post">
{let item_type=ezpreference( 'admin_search_stats_limit' )
     number_of_items=min( $item_type, 3)|choose( 10, 10, 25, 50 )}

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Search statistics'|i18n( 'design/admin/search/stats' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$most_frequent_phrase_array}
{* Items per page and view mode selector. *}
<div class="context-toolbar">
<div class="button-left">
    <p class="table-preferences">
{* The sizes come from admininterface.ini [PaginationSettings]; the preference
   stores the position in that list. *}
{foreach $limit_choices as $limit_index => $limit_option}
    {if eq( $limit_index|inc, $limit_choice )}
        <span class="current">{$limit_option}</span>
    {else}
        <a href={concat( '/user/preferences/set/admin_search_stats_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/search/stats',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
    {/if}
{/foreach}
</p>
</div>
<div class="float-break"></div>
</div>

<table class="list" cellspacing="0">
<tr>
    <th>{'Phrase'|i18n( 'design/admin/search/stats' )}</th>
    <th class="tight">{'Number of phrases'|i18n( 'design/admin/search/stats' )}</th>
    <th class="tight">{'Average result returned'|i18n( 'design/admin/search/stats' )}</th>
</tr>
{section var=Phrases loop=$most_frequent_phrase_array sequence=array( bglight, bgdark )}
<tr class="{$Phrases.sequence}">
    <td>{$Phrases.item.phrase|wash}</td>
    <td class="number" align="right">{$Phrases.item.phrase_count}</td>
    <td class="number" align="right">{$Phrases.item.result_count|l10n( number )}</td>
</tr>
{/section}
</table>
{section-else}
<div class="block">
<p>{'The list is empty.'|i18n( 'design/admin/search/stats' )}</p>
</div>
{/section}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat( '/search/stats')
         item_count=$search_list_count
         view_parameters=$view_parameters
         item_limit=$number_of_items}
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">

{if $most_frequent_phrase_array|count}
    <input class="button" type="submit" name="ResetSearchStatsButton" value="{'Reset statistics'|i18n( 'design/admin/search/stats' )}" title="{'Clear the search log.'|i18n( 'design/admin/search/stats' )}" />
{else}
    <input class="button-disabled" type="submit" name="ResetSearchStatsButton" value="{'Reset statistics'|i18n( 'design/admin/search/stats' )}" disabled="disabled" />
{/if}

</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

{/let}
</form>
