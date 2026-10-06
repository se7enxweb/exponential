{* The sections list (section/list).

   What a section is, an overview of all of them, a search and a filter, then one card per section with its
   identifier, navigation part, objects, the roles that name it and whether it can be removed, and its View, Edit and
   Assign actions. Ticked sections are removed through a confirmation page, which says why a section in use cannot go.

   The same file is in design/admin and design/admin4. The usage figures come from the view (section_overview,
   section_summary); without them (an older view class) the cards show what the section rows hold and nothing is
   lost. Everything works without javascript: the selection and the buttons submit the form. The script adds the
   search, the filter and the selection count. Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}

{def $has_overview = and( is_set( $section_overview ), is_set( $section_summary ) )
     $can_edit = first_set( $section_can_edit, true() )
     $can_assign_any = $allowed_assign_sections|contains( '*' )}

<div class="context-block exp-sections">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Sections'|i18n( 'design/admin/section/list' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Sections divide the content into groups. Every object belongs to exactly one section. Roles can allow or deny access by section, the section decides which top menu tab is active while its content is viewed, and templates can be overridden for one section.'|i18n( 'design/admin/section/list' )}</p>

{def $feedback = first_set( $section_feedback, false() )}
{if $feedback}
    {if eq( $feedback.type, 'created' )}
<div class="exp-feedback is-ok" role="status">{'The section %name was created. Assign content to it with Assign content on its card.'|i18n( 'design/admin/section/list',, hash( '%name', $feedback.names[0]|wash ) )}</div>
    {elseif eq( $feedback.type, 'saved' )}
<div class="exp-feedback is-ok" role="status">{'The section %name was saved.'|i18n( 'design/admin/section/list',, hash( '%name', $feedback.names[0]|wash ) )}</div>
    {elseif eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{'Removed: %names.'|i18n( 'design/admin/section/list',, hash( '%names', $feedback.names|implode( ', ' )|wash ) )}</div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert">{'No section was selected. Tick the sections to remove first.'|i18n( 'design/admin/section/list' )}</div>
    {/if}
{/if}

<form name="sections" method="post" action={'/section/list/'|ezurl}>

{if $has_overview}
<section aria-labelledby="section-overview-title">
<h2 class="exp-sr" id="section-overview-title">{'Overview'|i18n( 'design/admin/section/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$section_summary.sections}</strong><span>{'Sections'|i18n( 'design/admin/section/list' )}</span></li>
    <li class="exp-figure"><strong>{$section_summary.published}</strong><span>{'Published objects'|i18n( 'design/admin/section/list' )}</span></li>
    <li class="exp-figure"><strong>{$section_summary.in_roles}</strong><span>{'Used by roles'|i18n( 'design/admin/section/list' )}</span></li>
    <li class="exp-figure"><strong>{$section_summary.empty}</strong><span>{'Without published objects'|i18n( 'design/admin/section/list' )}</span></li>
    <li class="exp-figure"><strong>{$section_summary.removable}</strong><span>{'Can be removed'|i18n( 'design/admin/section/list' )}</span></li>
    <li class="exp-figure{if $section_summary.attention|gt(0)} is-attention{/if}"><strong>{$section_summary.attention}</strong><span>{'Need attention'|i18n( 'design/admin/section/list' )}</span></li>
</ul>
</section>
{/if}

<section aria-labelledby="section-controls-title">
<h2 class="exp-sr" id="section-controls-title">{'Find and create'|i18n( 'design/admin/section/list' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field exp-js-only" hidden>
        <label for="section-search">{'Find a section'|i18n( 'design/admin/section/list' )}</label>
        <input type="search" id="section-search" autocomplete="off" spellcheck="false" aria-controls="section-list" aria-describedby="section-filter-count section-search-help" />
        <span class="exp-help" id="section-search-help">{'Name, identifier, ID, navigation part or role.'|i18n( 'design/admin/section/list' )}</span>
    </div>
    {if $can_edit}
    <div class="exp-field">
        <span class="exp-help">{'A new section starts empty: give it a name and an identifier, then assign content to it.'|i18n( 'design/admin/section/list' )}</span>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary" name="CreateSectionButton" value="1" title="{'Create a new section.'|i18n( 'design/admin/section/list' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New section'|i18n( 'design/admin/section/list' )}</button>
        </div>
    </div>
    {/if}
    {if $has_overview}
    <fieldset class="exp-field exp-field-wide exp-js-only" hidden>
        <legend>{'Show'|i18n( 'design/admin/section/list' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/section/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="content" /><span>{'With published objects'|i18n( 'design/admin/section/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="empty" /><span>{'Without published objects'|i18n( 'design/admin/section/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="roles" /><span>{'Used by roles'|i18n( 'design/admin/section/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="removable" /><span>{'Can be removed'|i18n( 'design/admin/section/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="SectionFilter" value="attention" /><span>{'Need attention'|i18n( 'design/admin/section/list' )}</span></label>
        </div>
    </fieldset>
    {/if}
    <p class="exp-filter-count exp-js-only" id="section-filter-count" aria-live="polite" hidden></p>
</div>
</section>

<section class="exp-section" aria-labelledby="section-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="section-list-title">{'All sections'|i18n( 'design/admin/section/list' )}</h2>
    {if $section_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/section/list',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $section_count ), '%count', $section_count ) )}</span>
    {/if}
    {if $can_edit}
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="section-select-all" /> {'Select all on this page'|i18n( 'design/admin/section/list' )}</label>
    {/if}
</div>

{if $section_array|count|eq(0)}
<p class="exp-empty">{'There are no sections. A section is needed for every object; create one with New section.'|i18n( 'design/admin/section/list' )}</p>
{else}
<ul class="exp-secs" id="section-list">
{foreach $section_array as $section}
    {def $info = first_set( $section_overview[$section.id], false() )
         $sec_id = concat( 'section-', $section.id )
         $can_assign = or( $can_assign_any, $allowed_assign_sections|contains( $section.id ) )}
<li class="exp-sec{if and( $info, $info.attention )} is-attention{/if}" id="{$sec_id}"
    {if $info}data-search="{$info.search|wash}" data-published="{$info.published}" data-roles="{if $info.used_by_roles}1{else}0{/if}" data-removable="{if $info.removable}1{else}0{/if}" data-attention="{if $info.attention}1{else}0{/if}"{else}data-search="{concat( $section.name, ' ', $section.identifier, ' ', $section.id )|downcase|wash}"{/if}>
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            {if $can_edit}
            <label class="exp-select" title="{'Select section for removal.'|i18n( 'design/admin/section/list' )}">
                <input type="checkbox" name="SectionIDArray[]" value="{$section.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/section/list',, hash( '%name', $section.name ) )|wash}" />
            </label>
            {/if}
            <h3 id="{$sec_id}-title"><a href={concat( '/section/view/', $section.id )|ezurl}>{$section.name|wash}</a></h3>
            {if $section.identifier|ne( '' )}<code class="exp-title-key">{$section.identifier|wash}</code>{/if}
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/section/list',, hash( '%id', $section.id ) )}</span>
            {if $info}
            <ul class="exp-badges">
                {if $info.published|gt(0)}
                <li class="exp-badge is-info">{'%count published'|i18n( 'design/admin/section/list',, hash( '%count', $info.published ) )}</li>
                {else}
                <li class="exp-badge">{'No published objects'|i18n( 'design/admin/section/list' )}</li>
                {/if}
                {if $info.used_by_roles}
                <li class="exp-badge is-info">{'Used by roles'|i18n( 'design/admin/section/list' )}</li>
                {/if}
                {if $info.removable}
                <li class="exp-badge is-ok">{'Can be removed'|i18n( 'design/admin/section/list' )}</li>
                {/if}
                {if $info.missing_identifier}
                <li class="exp-badge is-warn">{'No identifier'|i18n( 'design/admin/section/list' )}</li>
                {/if}
                {if $info.navigation_part_known|not}
                <li class="exp-badge is-warn">{'Unknown navigation part'|i18n( 'design/admin/section/list' )}</li>
                {/if}
            </ul>
            {/if}
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( '/section/view/', $section.id )|ezurl} aria-describedby="{$sec_id}-title">{'View'|i18n( 'design/admin/section/list' )}</a>
            {if $can_edit}
            <a class="exp-btn exp-btn-small" href={concat( '/section/edit/', $section.id, '/' )|ezurl} aria-describedby="{$sec_id}-title" title="{'Edit the <%section_name> section.'|i18n( 'design/admin/section/list',, hash( '%section_name', $section.name ) )|wash}">{'Edit'|i18n( 'design/admin/section/list' )}</a>
            {/if}
            {if $can_assign}
            <a class="exp-btn exp-btn-small" href={concat( '/section/assign/', $section.id, '/' )|ezurl} aria-describedby="{$sec_id}-title" title="{'Assign a subtree to the <%section_name> section.'|i18n( 'design/admin/section/list',, hash( '%section_name', $section.name ) )|wash}">{'Assign content'|i18n( 'design/admin/section/list' )}</a>
            {else}
            <span class="exp-btn exp-btn-small is-disabled" aria-disabled="true" title="{'You are not allowed to assign the <%section_name> section.'|i18n( 'design/admin/section/list',, hash( '%section_name', $section.name ) )|wash}">{'Assign content'|i18n( 'design/admin/section/list' )}</span>
            {/if}
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Navigation part'|i18n( 'design/admin/section/list' )}</dt>
            <dd>{if $info}{$info.navigation_part_name|wash} <code>{$info.navigation_part|wash}</code>{else}<code>{$section.navigation_part_identifier|wash}</code>{/if}</dd>
        </div>
        {if $info}
        <div>
            <dt>{'Objects'|i18n( 'design/admin/section/list' )}</dt>
            <dd>{if $info.objects|eq(0)}{'None'|i18n( 'design/admin/section/list' )}{else}{'%count published'|i18n( 'design/admin/section/list',, hash( '%count', $info.published ) )}{if $info.drafts|gt(0)}, {'%count drafts'|i18n( 'design/admin/section/list',, hash( '%count', $info.drafts ) )}{/if}{if $info.archived|gt(0)}, {'%count archived'|i18n( 'design/admin/section/list',, hash( '%count', $info.archived ) )}{/if}{/if}</dd>
        </div>
        <div>
            <dt title="{'Roles with a policy limited to this section'|i18n( 'design/admin/section/list' )}">{'Roles'|i18n( 'design/admin/section/list' )}</dt>
            <dd>{if $info.roles|count|eq(0)}{'None'|i18n( 'design/admin/section/list' )}{else}{foreach $info.roles as $role}<a href={concat( '/role/view/', $role.id )|ezurl}>{$role.name|wash}</a>{delimiter}, {/delimiter}{/foreach}{/if}</dd>
        </div>
        <div>
            <dt title="{'Roles assigned to users or groups with the limitation to this section'|i18n( 'design/admin/section/list' )}">{'Role assignments'|i18n( 'design/admin/section/list' )}</dt>
            <dd>{if $info.assignment_count|eq(0)}{'None'|i18n( 'design/admin/section/list' )}{else}{$info.assignment_count}{/if}</dd>
        </div>
        {/if}
    </dl>
</li>
    {undef $info $sec_id $can_assign}
{/foreach}
</ul>
<p class="exp-empty" id="section-no-match" hidden>{'No section on this page matches. Clear the search or choose All.'|i18n( 'design/admin/section/list' )}</p>
{/if}

<div class="exp-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/section/list' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_index|inc, $limit_choice )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_section_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/section/list',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/section/list'
             item_count=$section_count
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
</div>
</section>

{if $can_edit}
<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveSectionButton" value="1" id="section-remove" aria-describedby="section-remove-help" title="{'Remove selected sections.'|i18n( 'design/admin/section/list' )}">{'Remove selected'|i18n( 'design/admin/section/list' )}</button>
        <button type="submit" class="exp-btn" name="CreateSectionButton" value="1" title="{'Create a new section.'|i18n( 'design/admin/section/list' )}">{'New section'|i18n( 'design/admin/section/list' )}</button>
    </div>
    <p class="exp-meta" id="section-remove-help">{'Remove selected asks for confirmation first. A section that still holds objects, or that a role or role assignment names, is never removed.'|i18n( 'design/admin/section/list' )} <span id="section-selected-count" aria-live="polite"></span></p>
</div>
{/if}

</form>

</div></div></div>
</div>

<script type="text/javascript">
var expSectionText = {ldelim}
    shown: '{'%shown of %count sections on this page shown'|i18n( 'design/admin/section/list' )|wash( javascript )}',
    allShown: '{'%count sections on this page'|i18n( 'design/admin/section/list' )|wash( javascript )}',
    selected: '{'%count selected.'|i18n( 'design/admin/section/list' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var list = document.getElementById( 'section-list' );
    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expSectionText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( '.exp-sections .exp-js-only', function ( el ) { el.hidden = false; } );
    if ( !list ) return;

    var searchEl = document.getElementById( 'section-search' );
    var countEl = document.getElementById( 'section-filter-count' );
    var noMatchEl = document.getElementById( 'section-no-match' );

    function chosen() {
        var picked = document.querySelector( 'input[name="SectionFilter"]:checked' );
        return picked ? picked.value : '';
    }

    // The search and the filter together decide which cards are shown; hidden cards keep their tick.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosen(), shown = 0, total = 0;
        each( '#section-list > .exp-sec', function ( card ) {
            total++;
            var hay = card.getAttribute( 'data-search' ) || '', ok = true, i;
            if ( state === 'content' ) ok = card.getAttribute( 'data-published' ) !== '0';
            else if ( state === 'empty' ) ok = card.getAttribute( 'data-published' ) === '0';
            else if ( state === 'roles' ) ok = card.getAttribute( 'data-roles' ) === '1';
            else if ( state === 'removable' ) ok = card.getAttribute( 'data-removable' ) === '1';
            else if ( state === 'attention' ) ok = card.getAttribute( 'data-attention' ) === '1';
            for ( i = 0; ok && i < words.length; i++ ) ok = hay.indexOf( words[i] ) !== -1;
            card.hidden = !ok;
            if ( ok ) shown++;
        } );
        if ( countEl ) countEl.textContent = shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } );
        if ( noMatchEl ) noMatchEl.hidden = shown !== 0;
    }
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    each( 'input[name="SectionFilter"]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();

    // The selection: a count beside Remove selected, and a tick for every card shown.
    var selectAll = document.getElementById( 'section-select-all' );
    var selectedEl = document.getElementById( 'section-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="SectionIDArray[]"]' ); }
    function updateSelection() {
        var all = boxes(), n = 0, visible = 0, visibleTicked = 0, i;
        for ( i = 0; i < all.length; i++ ) {
            var card = all[i].closest( '.exp-sec' );
            if ( card ) card.classList.toggle( 'is-selected', all[i].checked );
            if ( all[i].checked ) n++;
            if ( card && !card.hidden ) { visible++; if ( all[i].checked ) visibleTicked++; }
        }
        if ( selectedEl ) selectedEl.textContent = n ? tr( 'selected', { count: n } ) : '';
        if ( selectAll ) { selectAll.checked = visible > 0 && visibleTicked === visible; selectAll.indeterminate = visibleTicked > 0 && visibleTicked < visible; }
    }
    list.addEventListener( 'change', updateSelection );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), i;
        for ( i = 0; i < all.length; i++ ) {
            var card = all[i].closest( '.exp-sec' );
            if ( card && !card.hidden ) all[i].checked = selectAll.checked;
        }
        updateSelection();
    } );
    if ( searchEl ) searchEl.addEventListener( 'input', updateSelection );
    each( 'input[name="SectionFilter"]', function ( radio ) { radio.addEventListener( 'change', updateSelection ); } );
    updateSelection();
})();
{/literal}
</script>
