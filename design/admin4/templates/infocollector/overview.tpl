{* Setup > Collected information (infocollector/overview).

   What information collection is, the figures (forms with collections, collections, the last 30 and 7 days, the
   latest submission), a search by name and sorting, then one card per object with its class, its collections (in
   all and recently), the first and the last submission, and the links to the collections, the object and the CSV
   export. Ticked objects lose all their collections through a confirmation page. When little or nothing has been
   collected, the objects that can collect are listed so the empty page says where to look.

   The same file is in design/admin and design/admin4. Every field and button name of the old page is kept
   (ObjectIDArray[], RemoveObjectCollectionButton), the page works without javascript, and the figures only
   appear when the view hands them over. Guide: doc/guides/collected-information.md *}
{include uri='design:infocollector/exp_style.tpl'}

{def $summary = first_set( $info_summary, false() )
     $search = first_set( $info_search, '' )
     $sort = first_set( $info_sort, 'name' )
     $order = first_set( $info_order, 'asc' )
     $feedback = first_set( $info_feedback, false() )
     $can_filter = first_set( $info_can_filter, false() )
     $recent_days = first_set( $info_recent_days, 30 )
     $candidates = first_set( $info_candidates, array() )
     $candidate_count = first_set( $info_candidate_count, 0 )}

<form name="objects" method="post" action={'/infocollector/overview/'|ezurl}>

<div class="context-block exp-info">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Collected information'|i18n( 'design/admin/infocollector/overview' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Forms, polls and feedback pages collect what visitors send when their class has attributes marked as information collectors. Each sending is one collection. Here you see which objects have collected, how much and how recently, read the collections, export them as CSV and remove them.'|i18n( 'design/admin/infocollector/overview' )}</p>

{if $feedback}
    {if eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status"><p>{'%collections collections of %objects objects were removed.'|i18n( 'design/admin/infocollector/overview',, hash( '%collections', $feedback.collections, '%objects', $feedback.objects ) )}</p></div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert"><p>{'Nothing was selected. Tick the objects whose collections should be removed first.'|i18n( 'design/admin/infocollector/overview' )}</p></div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="info-overview-title">
<h2 class="exp-sr" id="info-overview-title">{'Overview'|i18n( 'design/admin/infocollector/overview' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.objects}</strong><span>{'Objects with collections'|i18n( 'design/admin/infocollector/overview' )}</span></li>
    <li class="exp-figure"><strong>{$summary.collections}</strong><span>{'Collections in all'|i18n( 'design/admin/infocollector/overview' )}</span></li>
    <li class="exp-figure"><strong>{$summary.recent}</strong><span>{'In the last %days days'|i18n( 'design/admin/infocollector/overview',, hash( '%days', $recent_days ) )}</span></li>
    <li class="exp-figure"><strong>{$summary.week}</strong><span>{'In the last 7 days'|i18n( 'design/admin/infocollector/overview' )}</span></li>
    <li class="exp-figure"><strong>{if $summary.latest|gt( 0 )}{$summary.latest|l10n( shortdate )}{else}-{/if}</strong><span>{'Latest collection'|i18n( 'design/admin/infocollector/overview' )}</span></li>
    {if $summary.unlisted|gt( 0 )}
    <li class="exp-figure is-attention"><strong>{$summary.unlisted}</strong><span>{'Objects removed or in the trash, with collections not listed here'|i18n( 'design/admin/infocollector/overview' )}</span></li>
    {/if}
</ul>
</section>
{/if}

{if and( $can_filter, or( $object_count|gt( 0 ), $search|ne( '' ) ) )}
<section aria-labelledby="info-filter-title">
<h2 class="exp-sr" id="info-filter-title">{'Find'|i18n( 'design/admin/infocollector/overview' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field">
        <label for="info-search">{'Find an object'|i18n( 'design/admin/infocollector/overview' )}</label>
        <input type="search" id="info-search" name="InfoSearch" value="{$search|wash}" maxlength="100" autocomplete="off" spellcheck="false" aria-describedby="info-search-help" />
        <span class="exp-help" id="info-search-help">{'Part of the name of a form, poll or page.'|i18n( 'design/admin/infocollector/overview' )}</span>
    </div>
    <div class="exp-field">
        <div class="exp-actions">
            <button class="exp-btn exp-btn-primary" type="submit" name="InfoFilterButton" value="1">{'Update list'|i18n( 'design/admin/infocollector/overview' )}</button>
            {if $search|ne( '' )}<button class="exp-btn" type="submit" name="InfoClearSearchButton" value="1">{'Clear search'|i18n( 'design/admin/infocollector/overview' )}</button>{/if}
        </div>
    </div>
</div>
</section>
{/if}

<section class="exp-section" aria-labelledby="info-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="info-list-title">{'Objects that have collected information (%object_count)'|i18n( 'design/admin/infocollector/overview',, hash( '%object_count', $object_count ) )}</h2>
    {if $object_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/infocollector/overview',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $object_count ), '%count', $object_count ) )}</span>
    {/if}
    {if $object_array}
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="info-select-all" /> {'Select all on this page'|i18n( 'design/admin/infocollector/overview' )}</label>
    {/if}
</div>

{if $object_array}
    {if $can_filter}
<p class="exp-sortbar">
    <span>{'Sort by'|i18n( 'design/admin/infocollector/overview' )}:</span>
    {def $sort_names = hash( 'name', 'Name'|i18n( 'design/admin/infocollector/overview' ), 'collections', 'Collections'|i18n( 'design/admin/infocollector/overview' ), 'last', 'Last collection'|i18n( 'design/admin/infocollector/overview' ), 'first', 'First collection'|i18n( 'design/admin/infocollector/overview' ), 'class', 'Type'|i18n( 'design/admin/infocollector/overview' ) )
         $next = ''}
    {foreach array( 'name', 'collections', 'last', 'first', 'class' ) as $key}
        {if eq( $key, $sort )}
            {set $next = cond( eq( $order, 'asc' ), 'desc', 'asc' )}
            <a href={concat( '/infocollector/overview/(sortby)/', $key, '/(order)/', $next )|ezurl} aria-current="true" class="current" title="{'Sorted; select to reverse the order.'|i18n( 'design/admin/infocollector/overview' )}"><strong>{$sort_names[$key]|wash}</strong> {if eq( $order, 'asc' )}&uarr;{else}&darr;{/if}</a>
        {else}
            <a href={concat( '/infocollector/overview/(sortby)/', $key )|ezurl}>{$sort_names[$key]|wash}</a>
        {/if}
    {/foreach}
    {undef $sort_names $next}
</p>
    {/if}

<ul class="exp-rows" id="info-list">
{foreach $object_array as $item}
    {def $row_id = concat( 'info-object-', $item.contentobject_id )}
<li class="exp-row exp-form-card" id="{$row_id}">
    <div class="exp-row-head">
        <div class="exp-row-title">
            <label class="exp-select" title="{'Select collections for removal.'|i18n( 'design/admin/infocollector/overview' )}">
                <input type="checkbox" name="ObjectIDArray[]" value="{$item.contentobject_id}" aria-label="{'Select the collections of %name for removal'|i18n( 'design/admin/infocollector/overview',, hash( '%name', $item.name ) )|wash}" />
            </label>
            {$item.class_identifier|icon( 'small', $item.class_name|wash )}
            <h3 id="{$row_id}-title"><a href={concat( '/infocollector/collectionlist/', $item.contentobject_id )|ezurl}>{$item.name|wash}</a></h3>
            <ul class="exp-badges">
                <li class="exp-badge">{$item.class_name|wash}</li>
                <li class="exp-badge is-info">{if $item.collections|eq( 1 )}{'1 collection'|i18n( 'design/admin/infocollector/overview' )}{else}{'%count collections'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $item.collections ) )}{/if}</li>
                {if is_set( $item.recent_collections )}
                    {if $item.recent_collections|gt( 0 )}<li class="exp-badge is-ok">{'%count in the last %days days'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $item.recent_collections, '%days', $recent_days ) )}</li>{/if}
                {/if}
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small exp-btn-primary" href={concat( '/infocollector/collectionlist/', $item.contentobject_id )|ezurl}>{'Collections'|i18n( 'design/admin/infocollector/overview' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/infocollector/export/', $item.contentobject_id )|ezurl} title="{'Download every collection of this object as a CSV file for a spreadsheet.'|i18n( 'design/admin/infocollector/overview' )}">{'Export CSV'|i18n( 'design/admin/infocollector/overview' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/content/view/full/', $item.main_node_id )|ezurl}>{'Object'|i18n( 'design/admin/infocollector/overview' )}</a>
        </div>
    </div>
    <dl class="exp-facts">
        <div><dt>{'First collection'|i18n( 'design/admin/infocollector/overview' )}</dt><dd>{$item.first_collection|l10n( shortdatetime )}</dd></div>
        <div><dt>{'Last collection'|i18n( 'design/admin/infocollector/overview' )}</dt><dd>{$item.last_collection|l10n( shortdatetime )}</dd></div>
        <div><dt>{'Collections'|i18n( 'design/admin/infocollector/overview' )}</dt><dd><a href={concat( '/infocollector/collectionlist/', $item.contentobject_id )|ezurl}>{$item.collections}</a></dd></div>
        <div><dt>{'Object ID'|i18n( 'design/admin/infocollector/overview' )}</dt><dd>{$item.contentobject_id}</dd></div>
    </dl>
</li>
    {undef $row_id}
{/foreach}
</ul>
{else}
<p class="exp-empty">
{if $search|ne( '' )}{'No object with collections matches “%search”. Clear the search to see all of them.'|i18n( 'design/admin/infocollector/overview',, hash( '%search', $search|wash ) )}
{else}{'There are no objects that have collected any information.'|i18n( 'design/admin/infocollector/overview' )} {'An object collects once a visitor sends its form; the objects that can are listed below.'|i18n( 'design/admin/infocollector/overview' )}{/if}
</p>
{/if}

<div class="exp-listfoot">
    {if and( $limit_choices, $object_count|gt( 10 ) )}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/infocollector/overview' )}:</span>
        {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
        {foreach $limit_choices as $limit_index => $limit_option}
            {if eq( $limit_index|inc, $limit_choice )}<span class="current">{$limit_option}</span>{else}<a href={concat( '/user/preferences/set/admin_infocollector_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $limit_option ) )}">{$limit_option}</a>{/if}
        {/foreach}
    </p>
    {/if}
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/infocollector/overview'
             item_count=$object_count
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
</div>

<div class="exp-bottombar">
    <p class="exp-meta">{'Removing deletes every collection of the ticked objects; the objects themselves stay. Asks first.'|i18n( 'design/admin/infocollector/overview' )}</p>
    <div class="exp-actions">
    {if $object_array}
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveObjectCollectionButton" value="{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}" title="{'Remove all information that was collected by the selected objects.'|i18n( 'design/admin/infocollector/overview' )}">{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}</button>
    {else}
        <button class="exp-btn" type="submit" name="RemoveObjectCollectionButton" value="{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}" disabled="disabled">{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}</button>
    {/if}
    </div>
</div>
</section>

{if $candidates}
<section class="exp-section" aria-labelledby="info-candidates-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="info-candidates-title">{'Objects that can collect but have nothing yet (%count)'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $candidate_count ) )}</h2>
    <p>{'Their class has information collector attributes. Open one on the site and send its form to test it; what is sent appears above.'|i18n( 'design/admin/infocollector/overview' )}{if $candidate_count|gt( $candidates|count )} {'The first %shown by name are shown.'|i18n( 'design/admin/infocollector/overview',, hash( '%shown', $candidates|count ) )}{/if}</p>
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr><th scope="col">{'Name'|i18n( 'design/admin/infocollector/overview' )}</th><th scope="col">{'Type'|i18n( 'design/admin/infocollector/overview' )}</th><th scope="col" class="exp-num">{'Object ID'|i18n( 'design/admin/infocollector/overview' )}</th></tr></thead>
<tbody>
{foreach $candidates as $candidate}
<tr>
    <td>{$candidate.class_identifier|icon( 'small', $candidate.class_name|wash )} <a href={concat( '/content/view/full/', $candidate.main_node_id )|ezurl}>{$candidate.name|wash}</a></td>
    <td>{$candidate.class_name|wash}</td>
    <td class="exp-num">{$candidate.contentobject_id}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>
{/if}

</div></div></div>
</div>

</form>

{literal}
<script>
(function () {
    var list = document.getElementById('info-list'), all = document.getElementById('info-select-all');
    if (!list || !all) return;
    var boxes = function () { return Array.prototype.slice.call(list.querySelectorAll('input[name="ObjectIDArray[]"]')); };
    all.parentNode.hidden = false;
    all.addEventListener('change', function () { boxes().forEach(function (b) { b.checked = all.checked; }); });
    list.addEventListener('change', function () { var b = boxes(); all.checked = b.length > 0 && b.every(function (x) { return x.checked; }); });
})();
</script>
{/literal}
{undef $summary $search $sort $order $feedback $can_filter $recent_days $candidates $candidate_count}
