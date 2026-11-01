{* The moderation/recent view (Setup > Recent content): the most recently created or modified objects, a page at a time,
   with the filters (a GET form the view turns into view parameters), sortable column heads, the page sizes (the
   preference admin_moderation_limit) and, with moderation/act, a checkbox per row and the actions on the selection
   (posted to moderation/action). The address and the browser come from the audit and are shown with
   moderation/personaldata only.

   Variables: rows (expModerationList::decorate()), total, filters (expModerationList::filters()), filter_suffix,
   page_uri, view_parameters, limit, sizes (size, position), sort_links, creator_name, classes, sections, siteaccesses,
   states, can_act, personal, audit, hold_ready, job_threshold, elapsed (ms), queries, problem, return_url.

   The same file is in design/admin and design/admin4 (admin2, admin3 and admin4l inherit it), drawn with the shared
   components only; page/moderation_recent.js adds "select all", the selection count and shows the fields of the
   chosen action. Guide: doc/eng-us/features/6.0/moderation.md *}
{def $base = '/moderation/recent'
     $sort_text = hash( 'modified', 'Modified'|i18n( 'design/admin/moderation' ),
                        'published', 'Created'|i18n( 'design/admin/moderation' ),
                        'name', 'Name'|i18n( 'design/admin/moderation' ),
                        'class', 'Class'|i18n( 'design/admin/moderation' ),
                        'section', 'Section'|i18n( 'design/admin/moderation' ),
                        'creator', 'Creator'|i18n( 'design/admin/moderation' ) )}
<div class="a4-page a4-moderation" id="exp-moderation-recent">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Recent content'|i18n( 'design/admin/moderation' )}</h1>
        <p class="a4-pagehead-sub">{'The most recently created and modified content of the whole site, newest first: what visitors submitted, what is held for review, drafts waiting for their publication. Select items to release, hold, hide, reveal, remove or move them to another section or state.'|i18n( 'design/admin/moderation' )}</p>
    </div>
</div>

{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The list could not be read: %problem'|i18n( 'design/admin/moderation',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{if $hold_ready|not}<div class="a4-notice is-warn" role="status"><p>{'The review hold is not set up yet, so nothing is held and the hold actions are not offered. The upgrade step 6.0.15-review-hold sets it up: ./console exp:upgrade:scripts --only=6.0.15-review-hold --apply'|i18n( 'design/admin/moderation' )}</p></div>{/if}

<form class="a4-filterbar" method="get" action={$base|ezurl} role="search" aria-label="{'Filter the list'|i18n( 'design/admin/moderation' )}">
    <input type="hidden" name="Filter" value="1" />
    <div class="a4-field">
        <label for="moderation-from">{'From'|i18n( 'design/admin/moderation' )}</label>
        <input class="a4-input" type="date" id="moderation-from" name="from" value="{$filters.from|wash}" />
    </div>
    <div class="a4-field">
        <label for="moderation-to">{'To'|i18n( 'design/admin/moderation' )}</label>
        <input class="a4-input" type="date" id="moderation-to" name="to" value="{$filters.to|wash}" />
    </div>
    <div class="a4-field">
        <label for="moderation-date">{'Date'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-date" name="date">
            <option value="modified"{if eq( $filters.date, 'modified' )} selected="selected"{/if}>{'Modified'|i18n( 'design/admin/moderation' )}</option>
            <option value="published"{if eq( $filters.date, 'published' )} selected="selected"{/if}>{'Created (first published)'|i18n( 'design/admin/moderation' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-class">{'Class'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-class" name="class">
            <option value="">{'All'|i18n( 'design/admin/moderation' )}</option>
            {foreach $classes as $class}<option value="{$class.identifier|wash}"{if eq( $filters.class, $class.identifier )} selected="selected"{/if}>{$class.name|wash}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-status">{'Status'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-status" name="status">
            <option value="">{'All'|i18n( 'design/admin/moderation' )}</option>
            <option value="published"{if eq( $filters.status, 'published' )} selected="selected"{/if}>{'Published'|i18n( 'design/admin/moderation' )}</option>
            <option value="draft"{if eq( $filters.status, 'draft' )} selected="selected"{/if}>{'Draft, never published'|i18n( 'design/admin/moderation' )}</option>
            <option value="pending"{if eq( $filters.status, 'pending' )} selected="selected"{/if}>{'Pending (waiting for approval or a date)'|i18n( 'design/admin/moderation' )}</option>
            <option value="archived"{if eq( $filters.status, 'archived' )} selected="selected"{/if}>{'In the trash'|i18n( 'design/admin/moderation' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-held">{'Held for review'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-held" name="held">
            <option value="">{'All'|i18n( 'design/admin/moderation' )}</option>
            <option value="yes"{if eq( $filters.held, 'yes' )} selected="selected"{/if}>{'Held'|i18n( 'design/admin/moderation' )}</option>
            <option value="no"{if eq( $filters.held, 'no' )} selected="selected"{/if}>{'Not held'|i18n( 'design/admin/moderation' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-hidden">{'Hidden'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-hidden" name="hidden">
            <option value="">{'All'|i18n( 'design/admin/moderation' )}</option>
            <option value="yes"{if eq( $filters.hidden, 'yes' )} selected="selected"{/if}>{'Hidden'|i18n( 'design/admin/moderation' )}</option>
            <option value="no"{if eq( $filters.hidden, 'no' )} selected="selected"{/if}>{'Visible'|i18n( 'design/admin/moderation' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-section">{'Section'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-section" name="section">
            <option value="">{'All'|i18n( 'design/admin/moderation' )}</option>
            {foreach $sections as $section}<option value="{$section.id}"{if eq( $filters.section, $section.id )} selected="selected"{/if}>{$section.name|wash}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <label for="moderation-siteaccess">{'Submitted on'|i18n( 'design/admin/moderation' )}</label>
        <select id="moderation-siteaccess" name="siteaccess" aria-describedby="moderation-siteaccess-help">
            <option value="">{'Any siteaccess'|i18n( 'design/admin/moderation' )}</option>
            {foreach $siteaccesses as $siteaccess}<option value="{$siteaccess|wash}"{if eq( $filters.siteaccess, $siteaccess )} selected="selected"{/if}>{$siteaccess|wash}</option>{/foreach}
        </select>
        <span class="a4-help" id="moderation-siteaccess-help">{'Only what the audit recorded.'|i18n( 'design/admin/moderation' )}</span>
    </div>
    <div class="a4-field">
        <label for="moderation-creator">{'Creator'|i18n( 'design/admin/moderation' )}</label>
        <input class="a4-input" type="text" id="moderation-creator" name="creator" value="{if $filters.creator}{$filters.creator}{/if}" autocomplete="off" maxlength="150" aria-describedby="moderation-creator-help" />
        <span class="a4-help" id="moderation-creator-help">{if $creator_name}{'Now: %name'|i18n( 'design/admin/moderation',, hash( '%name', $creator_name ) )|wash}{else}{'A login or a user id.'|i18n( 'design/admin/moderation' )}{/if}</span>
    </div>
    <input type="hidden" name="sort" value="{$filters.sort|wash}" />
    <input type="hidden" name="order" value="{$filters.order|wash}" />
    <div class="a4-field">
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary">{'Filter'|i18n( 'design/admin/moderation' )}</button>
            {if ne( $filter_suffix, '' )}<a class="a4-btn" href={$base|ezurl}>{'Clear filters'|i18n( 'design/admin/moderation' )}</a>{/if}
        </div>
    </div>
</form>

<section class="a4-card is-flush a4-section" aria-labelledby="moderation-list-title">
<form method="post" action={'moderation/action'|ezurl} id="moderation-form">
    <input type="hidden" name="ReturnURL" value="{$return_url|wash}" />
    <div class="a4-card-head">
        <h2 class="a4-h3 a4-card-title" id="moderation-list-title">{'Items'|i18n( 'design/admin/moderation' )}</h2>
        <p class="a4-meta">{if $total}{'%from to %to of %count'|i18n( 'design/admin/moderation',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $total ), '%count', $total ) )}{else}{'No item'|i18n( 'design/admin/moderation' )}{/if}
           <span class="a4-muted">{'(read in %ms ms)'|i18n( 'design/admin/moderation',, hash( '%ms', $elapsed ) )}</span></p>
    </div>

    {if and( $can_act, $rows|count )}
    <div class="a4-card-body a4-toolbar" role="group" aria-label="{'Actions on the selected items'|i18n( 'design/admin/moderation' )}">
        <label class="a4-meta exp-js-only" hidden><input type="checkbox" data-moderation-all="ObjectIDList[]" /> {'Select all on this page'|i18n( 'design/admin/moderation' )}</label>
        <span class="a4-meta exp-js-only" hidden data-moderation-count data-text="{'%count selected'|i18n( 'design/admin/moderation' )|wash}"></span>
        <div class="a4-field">
            <label for="moderation-action">{'Action'|i18n( 'design/admin/moderation' )}</label>
            <select id="moderation-action" name="ModerationAction">
                <option value="">{'Choose an action'|i18n( 'design/admin/moderation' )}</option>
                {if $hold_ready}
                <option value="release">{'Release the hold'|i18n( 'design/admin/moderation' )}</option>
                <option value="hold">{'Hold for review'|i18n( 'design/admin/moderation' )}</option>
                {/if}
                <option value="hide">{'Hide'|i18n( 'design/admin/moderation' )}</option>
                <option value="reveal">{'Reveal'|i18n( 'design/admin/moderation' )}</option>
                <option value="trash">{'Move to trash'|i18n( 'design/admin/moderation' )}</option>
                <option value="delete">{'Delete'|i18n( 'design/admin/moderation' )}</option>
                <option value="section">{'Assign section'|i18n( 'design/admin/moderation' )}</option>
                {if $states|count}<option value="state">{'Assign state'|i18n( 'design/admin/moderation' )}</option>{/if}
            </select>
        </div>
        <div class="a4-field" data-moderation-for="hold release">
            <label for="moderation-reason">{'Reason'|i18n( 'design/admin/moderation' )}</label>
            <input class="a4-input" type="text" id="moderation-reason" name="Reason" maxlength="500" aria-describedby="moderation-reason-help" />
            <span class="a4-help" id="moderation-reason-help">{'Needed to hold; kept with the hold and in the audit.'|i18n( 'design/admin/moderation' )}</span>
        </div>
        <div class="a4-field" data-moderation-for="section">
            <label for="moderation-section-to">{'Section'|i18n( 'design/admin/moderation' )}</label>
            <select id="moderation-section-to" name="SectionID">
                <option value="">{'Choose a section'|i18n( 'design/admin/moderation' )}</option>
                {foreach $sections as $section}<option value="{$section.id}">{$section.name|wash}</option>{/foreach}
            </select>
        </div>
        {if $states|count}
        <div class="a4-field" data-moderation-for="state">
            <label for="moderation-state-to">{'State'|i18n( 'design/admin/moderation' )}</label>
            <select id="moderation-state-to" name="StateID">
                <option value="">{'Choose a state'|i18n( 'design/admin/moderation' )}</option>
                {foreach $states as $group}<optgroup label="{$group.group|wash}">{foreach $group.states as $state}<option value="{$state.id}">{$state.name|wash}</option>{/foreach}</optgroup>{/foreach}
            </select>
        </div>
        {/if}
        <div class="a4-field">
            <button type="submit" class="a4-btn a4-btn-primary" name="ApplyButton" value="1">{'Apply to the selected'|i18n( 'design/admin/moderation' )}</button>
        </div>
        <p class="a4-help">{'Each item is checked against your own permissions. Moving to the trash and deleting ask you to confirm. More than %count items run in the background.'|i18n( 'design/admin/moderation',, hash( '%count', $job_threshold ) )}</p>
    </div>
    {/if}

    {if $rows|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="moderation-list-title" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            {if $can_act}<th scope="col"><span class="a4-sr-only">{'Select'|i18n( 'design/admin/moderation' )}</span></th>{/if}
            {foreach array( 'modified', 'published', 'name', 'class' ) as $column}
            <th scope="col"{if $sort_links[$column].current} aria-sort="{if eq( $filters.order, 'asc' )}ascending{else}descending{/if}"{/if}><a href={$sort_links[$column].url|ezurl}>{$sort_text[$column]|wash}{if $sort_links[$column].current} {if eq( $filters.order, 'asc' )}&#8593;{else}&#8595;{/if}{/if}</a></th>
            {/foreach}
            <th scope="col">{'Status'|i18n( 'design/admin/moderation' )}</th>
            <th scope="col">{'Publication'|i18n( 'design/admin/moderation' )}</th>
            <th scope="col"{if $sort_links.section.current} aria-sort="{if eq( $filters.order, 'asc' )}ascending{else}descending{/if}"{/if}><a href={$sort_links.section.url|ezurl}>{$sort_text.section|wash}{if $sort_links.section.current} {if eq( $filters.order, 'asc' )}&#8593;{else}&#8595;{/if}{/if}</a></th>
            <th scope="col"{if $sort_links.creator.current} aria-sort="{if eq( $filters.order, 'asc' )}ascending{else}descending{/if}"{/if}><a href={$sort_links.creator.url|ezurl}>{$sort_text.creator|wash}{if $sort_links.creator.current} {if eq( $filters.order, 'asc' )}&#8593;{else}&#8595;{/if}{/if}</a></th>
            {if $personal}<th scope="col">{'Submitted from'|i18n( 'design/admin/moderation' )}</th>{/if}
        </tr></thead>
        <tbody>
        {foreach $rows as $row}
        <tr>
            {if $can_act}<td><input type="checkbox" name="ObjectIDList[]" value="{$row.id}" aria-label="{'Select %name'|i18n( 'design/admin/moderation',, hash( '%name', $row.name ) )|wash}" /></td>{/if}
            <td class="a4-nowrap">{if $row.modified}{$row.modified|l10n( 'shortdatetime' )}{else}<span class="a4-muted">-</span>{/if}</td>
            <td class="a4-nowrap">{if $row.published}{$row.published|l10n( 'shortdatetime' )}{else}<span class="a4-muted">{'not yet'|i18n( 'design/admin/moderation' )}</span>{/if}</td>
            <td>{if $row.node_id}<a href={concat( '/content/view/full/', $row.node_id )|ezurl}>{$row.name|wash}</a>{elseif $row.name}{$row.name|wash}{else}<span class="a4-muted">{'(no name)'|i18n( 'design/admin/moderation' )}</span>{/if}
                <br /><span class="a4-meta">{'ID %id'|i18n( 'design/admin/moderation',, hash( '%id', $row.id ) )}</span></td>
            <td>{$row.class_name|wash}</td>
            <td><ul class="a4-badges">
                {switch match=$row.status}
                {case match='published'}<li class="a4-badge is-ok">{'Published'|i18n( 'design/admin/moderation' )}</li>{/case}
                {case match='archived'}<li class="a4-badge is-bad">{'In the trash'|i18n( 'design/admin/moderation' )}</li>{/case}
                {case}<li class="a4-badge is-info">{'Draft'|i18n( 'design/admin/moderation' )}</li>{/case}
                {/switch}
                {if $row.pending}<li class="a4-badge is-warn">{'Pending'|i18n( 'design/admin/moderation' )}</li>{/if}
                {if $row.hidden}<li class="a4-badge">{'Hidden'|i18n( 'design/admin/moderation' )}</li>{elseif $row.invisible}<li class="a4-badge">{'Hidden by its parent'|i18n( 'design/admin/moderation' )}</li>{/if}
                {if $row.held}<li class="a4-badge is-warn" {if $row.hold_reason}title="{$row.hold_reason|wash}"{/if}>{if $row.auto}{'Held: new submission'|i18n( 'design/admin/moderation' )}{else}{'Held for review'|i18n( 'design/admin/moderation' )}{/if}</li>{/if}
            </ul>
            {if and( $row.held, $row.hold_reason )}<span class="a4-meta">{$row.hold_reason|shorten( 80 )|wash}{if $row.hold_by} ({$row.hold_by|wash}){/if}</span>{/if}</td>
            <td>{switch match=$row.version_status}
                {case match='pending'}{'Version %version pending'|i18n( 'design/admin/moderation',, hash( '%version', $row.version ) )}{/case}
                {case match='draft'}{if eq( $row.status, 'published' )}{'Version %version in progress'|i18n( 'design/admin/moderation',, hash( '%version', $row.version ) )}{else}{'Draft, version %version'|i18n( 'design/admin/moderation',, hash( '%version', $row.version ) )}{/if}{/case}
                {case}{'Version %version published'|i18n( 'design/admin/moderation',, hash( '%version', $row.current_version ) )}{/case}
                {/switch}
                {if $row.scheduled}<br /><span class="a4-meta">{'Scheduled for %date'|i18n( 'design/admin/moderation',, hash( '%date', $row.scheduled|l10n( 'shortdatetime' ) ) )}</span>{/if}</td>
            <td>{$row.section_name|wash}</td>
            <td>{if $row.owner_node_id}<a href={concat( '/content/view/full/', $row.owner_node_id )|ezurl}>{$row.owner_name|wash}</a>{elseif $row.owner_name}{$row.owner_name|wash}{else}<span class="a4-muted">{$row.owner_id}</span>{/if}</td>
            {if $personal}<td>{if $row.recorded}
                {if $row.ip}<code class="a4-code">{$row.ip|wash}</code>{else}<span class="a4-muted">{'no address'|i18n( 'design/admin/moderation' )}</span>{/if}
                {if $row.ua}<br /><span class="a4-meta" title="{$row.ua|wash}">{$row.ua|shorten( 60 )|wash}</span>{/if}
                {if $row.siteaccess}<br /><span class="a4-meta">{$row.siteaccess|wash}</span>{/if}
                {else}<span class="a4-muted">{'not recorded'|i18n( 'design/admin/moderation' )}</span>{/if}</td>{/if}
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{if ne( $filter_suffix, '' )}{'Nothing matches these filters.'|i18n( 'design/admin/moderation' )} <a href={$base|ezurl}>{'Show all'|i18n( 'design/admin/moderation' )}</a>{else}{'There is no content yet.'|i18n( 'design/admin/moderation' )}{/if}</p></div>
    {/if}
</form>
</section>

<div class="a4-listfoot">
    <p class="a4-sizes">
        <span>{'Per page'|i18n( 'design/admin/moderation' )}:</span>
    {foreach $sizes as $size}
        {if eq( $size.size, $limit )}<span class="current" aria-current="true">{$size.size}</span>{else}<a href={concat( '/user/preferences/set/admin_moderation_limit/', $size.position )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/moderation',, hash( '%count', $size.size ) )}">{$size.size}</a>{/if}
    {/foreach}
    </p>
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$page_uri
             item_count=$total
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
</div>

{if and( $personal, $audit|not )}<p class="a4-help">{'The address and the browser of a submission come from the audit index, which is not available here: the column says "not recorded".'|i18n( 'design/admin/moderation' )}</p>
{elseif $personal}<p class="a4-help">{'The address and the browser are those the audit recorded for the item (the auto-hold always records them; publishing and creating only where switched on), as its privacy settings keep them.'|i18n( 'design/admin/moderation' )}</p>{/if}

</div>
{if and( $can_act, $rows|count )}{ezscript( array( 'page/moderation_recent.js' ) )}{/if}
{undef $base $sort_text}
