{* The drafts of the current user (content/draft, "My drafts").

   What drafts are, the messages of the last removal, figures (drafts, of new objects, old drafts, by translation, by
   class), a search and filters by translation, class and age, the order, then one card per draft: its name, class,
   location (or "new object" with where it will be published), translation, version, created, modified and age, with
   Edit, View and Remove (confirmed in place). Below the list: Remove selected, Remove the drafts older than N days and
   Remove all, each confirmed in place. Only the user's own drafts are listed and removed.

   The same file is in design/admin and design/admin4; the look is content/history_exp_style.tpl (.exp-history). The
   list comes from the view (draft_rows, draft_overview, draft_filters ...), worked out by expContentDraftList. The
   POST names of the page before are kept (DeleteIDArray[], RemoveButton, EmptyButton); RemoveDraftButton[id],
   RemoveOldButton and OldDraftDays are new. Everything works without javascript; the script only adds Select all and
   the count of ticked drafts. Guide: doc/guides/drafts-and-pending.md *}
{include uri='design:content/history_exp_style.tpl'}
{include uri='design:content/draft_exp_style.tpl'}

{def $rows = first_set( $draft_rows, array() )
     $overview = first_set( $draft_overview, hash( 'total', 0, 'new_objects', 0, 'old', 0, 'old_days', 30, 'languages', hash(), 'classes', hash() ) )
     $choices = first_set( $draft_choices, hash( 'languages', hash(), 'classes', hash() ) )
     $filters = first_set( $draft_filters, hash( 'language', '', 'class', '', 'age', 0, 'sort', 'modified', 'search', '' ) )
     $links = first_set( $draft_links, hash( 'language', hash( 'all', '' ), 'class', hash( 'all', '' ), 'age', hash( 'all', '' ), 'sort', hash() ) )
     $suffix = first_set( $draft_suffix, '' )
     $count = first_set( $draft_count, 0 )
     $page_limit = first_set( $draft_limit, 25 )
     $feedback = first_set( $draft_feedback, false() )
     $old_counts = first_set( $draft_old_counts, hash() )
     $search_suffix = first_set( $draft_search_suffix, '' )
     $base = '/content/draft'}

<div class="context-block exp-history exp-drafts">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'My drafts (%draft_count)'|i18n( 'design/admin/content/draft',, hash( '%draft_count', $overview.total ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A draft is a version you started and have not published yet: of a new object, or of an object that is already published, which stays as it is until you publish. Only you see and edit your drafts. Remove the ones you no longer need; a draft of a new object takes the new object with it.'|i18n( 'design/admin/content/draft' )}</p>

{if $feedback}
    {if $feedback.removed|gt( 0 )}
<div class="exp-feedback is-ok" role="status">{if eq( $feedback.kind, 'old' )}{if eq( $feedback.removed, 1 )}{'One draft not modified for %days days or more was removed.'|i18n( 'design/admin/content/draft',, hash( '%days', $feedback.days ) )}{else}{'%count drafts not modified for %days days or more were removed.'|i18n( 'design/admin/content/draft',, hash( '%count', $feedback.removed, '%days', $feedback.days ) )}{/if}{elseif eq( $feedback.removed, 1 )}{'One draft was removed.'|i18n( 'design/admin/content/draft' )}{else}{'%count drafts were removed.'|i18n( 'design/admin/content/draft',, hash( '%count', $feedback.removed ) )}{/if}</div>
    {elseif and( eq( $feedback.refused, 0 ), eq( $feedback.kind, 'old' ) )}
<div class="exp-feedback is-info" role="status">{'No draft is that old; nothing was removed.'|i18n( 'design/admin/content/draft' )}</div>
    {elseif eq( $feedback.refused, 0 )}
<div class="exp-feedback is-warn" role="alert">{'No draft was selected. Tick the drafts to remove first.'|i18n( 'design/admin/content/draft' )}</div>
    {/if}
    {if $feedback.refused|gt( 0 )}
<div class="exp-feedback is-warn" role="alert">{'%count of the versions asked for are not drafts of yours and were left as they are.'|i18n( 'design/admin/content/draft',, hash( '%count', $feedback.refused ) )}</div>
    {/if}
{/if}

{if $overview.total|gt( 0 )}
<section aria-labelledby="draft-overview-title">
<h2 class="exp-sr" id="draft-overview-title">{'Overview'|i18n( 'design/admin/content/draft' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if and( eq( $suffix, '' ), eq( $filters.search, '' ) )} is-current{/if}"><a href={$base|ezurl}><strong>{$overview.total}</strong> <span>{'Drafts'|i18n( 'design/admin/content/draft' )}</span></a></li>
    <li class="exp-figure"><strong>{$overview.new_objects}</strong> <span>{'Of new objects'|i18n( 'design/admin/content/draft' )}</span></li>
    <li class="exp-figure{if $overview.old|gt( 0 )} is-attention{/if}{if $filters.age|gt( 0 )} is-current{/if}"><a href={concat( $base, '/(age)/', $overview.old_days )|ezurl}><strong>{$overview.old}</strong> <span>{'Not modified for %days days'|i18n( 'design/admin/content/draft',, hash( '%days', $overview.old_days ) )}</span></a></li>
    <li class="exp-figure"><strong>{$overview.languages|count}</strong> <span>{'Translations'|i18n( 'design/admin/content/draft' )}</span></li>
    <li class="exp-figure"><strong>{$overview.classes|count}</strong> <span>{'Classes'|i18n( 'design/admin/content/draft' )}</span></li>
</ul>
</section>

<section aria-labelledby="draft-find-title">
<h2 class="exp-sr" id="draft-find-title">{'Find drafts'|i18n( 'design/admin/content/draft' )}</h2>
<form class="exp-toolbar" method="get" action={concat( $base, $suffix )|ezurl} role="search">
    <div class="exp-field exp-field-wide">
        <label for="draft-search">{'Find a draft'|i18n( 'design/admin/content/draft' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="draft-search" name="q" value="{$filters.search|wash}" autocomplete="off" maxlength="100" aria-describedby="draft-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/content/draft' )}</button>
            {if $filters.search|ne( '' )}<a class="exp-btn" href={concat( $base, $suffix )|ezurl}>{'Clear search'|i18n( 'design/admin/content/draft' )}</a>{/if}
        </div>
        <span class="exp-help" id="draft-search-help">{'Any part of the name, the location or the class. Upper and lower case are the same.'|i18n( 'design/admin/content/draft' )}</span>
    </div>
    {if or( $choices.languages|count|gt( 1 ), $filters.language|ne( '' ) )}
    <div class="exp-field">
        <span class="exp-field-label" id="draft-language-label"><strong>{'Translation'|i18n( 'design/admin/content/draft' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="draft-language-label">
            <li>{if eq( $filters.language, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/draft' )}</span>{else}<a href={concat( $base, $links.language.all, $search_suffix )|ezurl}>{'All'|i18n( 'design/admin/content/draft' )}</a>{/if}</li>
        {foreach $choices.languages as $locale => $language_name}
            <li>{if eq( $filters.language, $locale )}<span class="current" aria-current="true">{$language_name|wash} <span class="exp-count">{first_set( $overview.languages[$locale], 0 )}</span></span>{else}<a href={concat( $base, $links.language[$locale], $search_suffix )|ezurl}>{$language_name|wash} <span class="exp-count">{first_set( $overview.languages[$locale], 0 )}</span></a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    {if or( $choices.classes|count|gt( 1 ), $filters.class|ne( '' ) )}
    <div class="exp-field">
        <span class="exp-field-label" id="draft-class-label"><strong>{'Class'|i18n( 'design/admin/content/draft' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="draft-class-label">
            <li>{if eq( $filters.class, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/draft' )}</span>{else}<a href={concat( $base, $links.class.all, $search_suffix )|ezurl}>{'All'|i18n( 'design/admin/content/draft' )}</a>{/if}</li>
        {foreach $choices.classes as $identifier => $class_name}
            <li>{if eq( $filters.class, $identifier )}<span class="current" aria-current="true">{$class_name|wash} <span class="exp-count">{first_set( $overview.classes[$identifier], 0 )}</span></span>{else}<a href={concat( $base, $links.class[$identifier], $search_suffix )|ezurl}>{$class_name|wash} <span class="exp-count">{first_set( $overview.classes[$identifier], 0 )}</span></a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    <div class="exp-field">
        <span class="exp-field-label" id="draft-age-label"><strong>{'Not modified for'|i18n( 'design/admin/content/draft' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="draft-age-label">
            <li>{if eq( $filters.age, 0 )}<span class="current" aria-current="true">{'Any time'|i18n( 'design/admin/content/draft' )}</span>{else}<a href={concat( $base, $links.age.all, $search_suffix )|ezurl}>{'Any time'|i18n( 'design/admin/content/draft' )}</a>{/if}</li>
        {foreach $old_counts as $days => $old_count}
            <li>{if eq( $filters.age, $days )}<span class="current" aria-current="true">{'%days days'|i18n( 'design/admin/content/draft',, hash( '%days', $days ) )} <span class="exp-count">{$old_count}</span></span>{else}<a href={concat( $base, $links.age[$days], $search_suffix )|ezurl}>{'%days days'|i18n( 'design/admin/content/draft',, hash( '%days', $days ) )} <span class="exp-count">{$old_count}</span></a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    <div class="exp-field">
        <span class="exp-field-label" id="draft-sort-label"><strong>{'Order'|i18n( 'design/admin/content/draft' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="draft-sort-label">
        {foreach array( hash( 'sort', 'modified', 'text', 'Last modified'|i18n( 'design/admin/content/draft' ) ),
                        hash( 'sort', 'oldest', 'text', 'Oldest first'|i18n( 'design/admin/content/draft' ) ),
                        hash( 'sort', 'name', 'text', 'Name'|i18n( 'design/admin/content/draft' ) ),
                        hash( 'sort', 'class', 'text', 'Class'|i18n( 'design/admin/content/draft' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $filters.sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $base, $links.sort[$tab.sort], $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</form>
</section>
{/if}

<form name="draftaction" method="post" action={concat( $base, $suffix, cond( $view_parameters.offset|gt( 0 ), concat( '/(offset)/', $view_parameters.offset ), '' ), $search_suffix )|ezurl}>

<section class="exp-section" aria-labelledby="draft-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="draft-list-title">{if $filters.search|ne( '' )}{'Drafts containing “%search”'|i18n( 'design/admin/content/draft',, hash( '%search', $filters.search ) )|wash}{else}{'Drafts'|i18n( 'design/admin/content/draft' )}{/if}</h2>
    {if $count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/draft',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $count ), '%count', $count ) )}</span>
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="draft-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/draft' )}</label>
    {/if}
</div>

{if $rows|count|eq( 0 )}
<p class="exp-empty">
{if $overview.total|eq( 0 )}
    {'There are no drafts that belong to you.'|i18n( 'design/admin/content/draft' )} {'A draft appears here when you start editing and leave without publishing.'|i18n( 'design/admin/content/draft' )}
{else}
    {'No draft matches these filters.'|i18n( 'design/admin/content/draft' )} <a href={$base|ezurl}>{'Show all drafts'|i18n( 'design/admin/content/draft' )}</a>
{/if}
</p>
{else}
<ul class="exp-cards" id="draft-list">
{foreach $rows as $row}
    {def $card_id = concat( 'draft-', $row.id )}
<li class="exp-card{if $row.age_days|ge( $overview.old_days )} is-attention{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{'Select draft for removal.'|i18n( 'design/admin/content/draft' )}">
                <input type="checkbox" name="DeleteIDArray[]" value="{$row.id}" data-name="{$row.name|wash}" aria-label="{'Select %name for removal'|i18n( 'design/admin/content/draft',, hash( '%name', $row.name ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title">{$row.class_identifier|class_icon( small, $row.class_name|wash )} <a href={concat( '/content/edit/', $row.object_id, '/', $row.version, '/', $row.language, '/' )|ezurl}>{$row.name|wash}</a></h3>
            <ul class="exp-badges">
                {if $row.is_new}<li class="exp-badge is-info">{'New object'|i18n( 'design/admin/content/draft' )}</li>{/if}
                {if $row.age_days|ge( $overview.old_days )}<li class="exp-badge is-warn">{'%days days old'|i18n( 'design/admin/content/draft',, hash( '%days', $row.age_days ) )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small exp-btn-primary" href={concat( '/content/edit/', $row.object_id, '/', $row.version, '/', $row.language, '/' )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit <%draft_name>.'|i18n( 'design/admin/content/draft',, hash( '%draft_name', $row.name ) )|wash}">{'Edit'|i18n( 'design/admin/content/draft' )}</a>
            {if $row.can_versionread}
            <a class="exp-btn exp-btn-small" href={concat( '/content/versionview/', $row.object_id, '/', $row.version, '/', $row.language, '/' )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/content/draft' )}</a>
            {/if}
            <details class="exp-confirm exp-confirm-inline">
                <summary>{'Remove'|i18n( 'design/admin/content/draft' )}</summary>
                <div>
                    <p>{if $row.is_new}{'This draft of a new object is removed for good, and the new object with it.'|i18n( 'design/admin/content/draft' )}{else}{'This draft is removed for good; the published version stays as it is.'|i18n( 'design/admin/content/draft' )}{/if}</p>
                    <button type="submit" class="exp-btn exp-btn-small exp-btn-danger" name="RemoveDraftButton[{$row.id}]" value="1" aria-describedby="{$card_id}-title">{'Remove for good'|i18n( 'design/admin/content/draft' )}</button>
                </div>
            </details>
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Class'|i18n( 'design/admin/content/draft' )}</dt>
            <dd>{$row.class_name|wash}</dd>
        </div>
        <div>
            <dt>{'Location'|i18n( 'design/admin/content/draft' )}</dt>
            <dd>{if $row.is_new}{if $row.location|ne( '' )}{'New, to be published below %location'|i18n( 'design/admin/content/draft',, hash( '%location', $row.location ) )|wash}{else}{'New object'|i18n( 'design/admin/content/draft' )}{/if}{elseif $row.node_id|gt( 0 )}<a href={concat( '/content/view/full/', $row.node_id )|ezurl}>{if $row.location|ne( '' )}{'Below %location'|i18n( 'design/admin/content/draft',, hash( '%location', $row.location ) )|wash}{else}{'Its page'|i18n( 'design/admin/content/draft' )}{/if}</a>{else}{'Unknown'|i18n( 'design/admin/content/draft' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Language'|i18n( 'design/admin/content/draft' )}</dt>
            <dd><img src="{$row.language|flag_icon}" width="18" height="12" alt="" /> {$row.language_name|wash}</dd>
        </div>
        <div>
            <dt>{'Version'|i18n( 'design/admin/content/draft' )}</dt>
            <dd>{$row.version}</dd>
        </div>
        <div>
            <dt>{'Created'|i18n( 'design/admin/content/draft' )}</dt>
            <dd>{$row.created|l10n( shortdatetime )}</dd>
        </div>
        <div>
            <dt>{'Modified'|i18n( 'design/admin/content/draft' )}</dt>
            <dd>{$row.modified|l10n( shortdatetime )} <span class="exp-muted">({if $row.age_days|eq( 0 )}{'today'|i18n( 'design/admin/content/draft' )}{else}{'%days days ago'|i18n( 'design/admin/content/draft',, hash( '%days', $row.age_days ) )}{/if})</span></dd>
        </div>
    </dl>
</li>
    {undef $card_id}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/draft' )}:</span>
    {foreach first_set( $draft_limit_choices, array( 10, 25, 50 ) ) as $limit_index => $limit_option}
        {if eq( $limit_option, $page_limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_draft_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count drafts per page.'|i18n( 'design/admin/content/draft',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$base
             page_uri_suffix=concat( $suffix, $search_suffix )
             item_count=$count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

{if $overview.total|gt( 0 )}
<div class="exp-bottombar">
    <div class="exp-actions">
        <details class="exp-confirm" id="draft-remove-selected">
            <summary>{'Remove selected'|i18n( 'design/admin/content/draft' )}</summary>
            <div>
                <p id="draft-remove-list">{'The ticked drafts are removed for good. A draft of a new object takes the new object with it.'|i18n( 'design/admin/content/draft' )}</p>
                <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" value="1" id="draft-remove-button" title="{'Remove selected drafts.'|i18n( 'design/admin/content/draft' )}">{'Remove selected for good'|i18n( 'design/admin/content/draft' )}</button>
            </div>
        </details>
        <details class="exp-confirm" id="draft-remove-old">
            <summary>{'Remove old drafts'|i18n( 'design/admin/content/draft' )}</summary>
            <div>
                <label for="draft-old-days">{'Drafts not modified for'|i18n( 'design/admin/content/draft' )}</label>
                <select id="draft-old-days" name="OldDraftDays">
                {foreach $old_counts as $days => $old_count}
                    <option value="{$days}"{if eq( $days, $overview.old_days )} selected="selected"{/if}>{'%days days or more (%count drafts)'|i18n( 'design/admin/content/draft',, hash( '%days', $days, '%count', $old_count ) )}</option>
                {/foreach}
                </select>
                <p>{'They are removed for good, whatever the filters show.'|i18n( 'design/admin/content/draft' )}</p>
                <button type="submit" class="exp-btn exp-btn-danger" name="RemoveOldButton" value="1">{'Remove these drafts for good'|i18n( 'design/admin/content/draft' )}</button>
            </div>
        </details>
        <details class="exp-confirm" id="draft-remove-all">
            <summary>{'Remove all'|i18n( 'design/admin/content/draft' )}</summary>
            <div>
                <p>{'All %count of your drafts are removed for good, whatever the filters show.'|i18n( 'design/admin/content/draft',, hash( '%count', $overview.total ) )}</p>
                <button type="submit" class="exp-btn exp-btn-danger" name="EmptyButton" value="1" title="{'Remove all drafts that belong to you.'|i18n( 'design/admin/content/draft' )}">{'Remove all for good'|i18n( 'design/admin/content/draft' )}</button>
            </div>
        </details>
    </div>
    <p class="exp-meta" id="draft-selected-count" aria-live="polite"></p>
</div>
{/if}

</form>

</div></div></div>
</div>

<script type="text/javascript">
var expDraftText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/content/draft' )|wash( javascript )}',
    removeList: '{'These drafts are removed for good: %list. A draft of a new object takes the new object with it.'|i18n( 'design/admin/content/draft' )|wash( javascript )}',
    removeNone: '{'Tick the drafts to remove in the list first.'|i18n( 'design/admin/content/draft' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var nodes = document.querySelectorAll( '.exp-drafts .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var list = document.getElementById( 'draft-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'draft-select-all' );
    var countEl = document.getElementById( 'draft-selected-count' );
    var removeList = document.getElementById( 'draft-remove-list' );
    var removeButton = document.getElementById( 'draft-remove-button' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), ticked = [], j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) ticked.push( all[j].getAttribute( 'data-name' ) );
        }
        if ( countEl ) countEl.textContent = ticked.length ? expDraftText.selected.split( '%count' ).join( ticked.length ) : '';
        if ( removeList ) removeList.textContent = ticked.length ? expDraftText.removeList.split( '%list' ).join( ticked.join( ', ' ) ) : expDraftText.removeNone;
        if ( removeButton ) removeButton.disabled = !ticked.length;
        if ( selectAll ) { selectAll.checked = ticked.length > 0 && ticked.length === all.length; selectAll.indeterminate = ticked.length > 0 && ticked.length < all.length; }
    }
    list.addEventListener( 'change', update );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), j;
        for ( j = 0; j < all.length; j++ ) all[j].checked = selectAll.checked;
        update();
    } );
    update();
})();
{/literal}
</script>
