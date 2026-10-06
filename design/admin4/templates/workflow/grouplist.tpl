{* The workflow groups (workflow/grouplist).

   What a workflow group is, an overview of the groups and workflows, a search, then one card per group with its
   workflows: their events, whether they are enabled, the triggers that run them, the processes waiting in them and
   the other groups they belong to, and when the group or one of its workflows last changed. Each card says what
   removing the group would do; Remove selected leads to a confirmation (design:workflow/confirmremovegroup.tpl)
   before anything is removed.

   The same file is in design/admin and design/admin4. The extra data comes from the view (group_overview,
   group_summary, group_feedback); without it (an older view class) the cards show the group's name and the page
   works as before. Every variable the page had before is still set: groups, group_count, limit, view_parameters,
   module. Field names (ContentClass_id_checked[], DeleteGroupButton, NewGroupButton) are unchanged.
   Guide: doc/guides/workflows.md *}
{include uri='design:workflow/exp_style.tpl'}

{def $overview = first_set( $group_overview, hash() )
     $summary = first_set( $group_summary, false() )
     $feedback = first_set( $group_feedback, false() )}

<form name="grouplistform" action={concat( $module.functions.grouplist.uri )|ezurl} method="post">

<div class="context-block exp-lists exp-wfgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow groups (%groups_count)'|i18n( 'design/admin/workflow/grouplist',, hash( '%groups_count', $group_count ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Workflows are kept in groups so that they are easy to find. A group changes nothing about when a workflow runs: that is decided by the triggers. A workflow can belong to more than one group.'|i18n( 'design/admin/workflow/grouplist' )}</p>

{if $feedback}
    {if eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{'Removed: %names.'|i18n( 'design/admin/workflow/grouplist',, hash( '%names', $feedback.names|implode( ', ' )|wash ) )} {'%removed workflows were removed with them, %unlinked stay in their other groups.'|i18n( 'design/admin/workflow/grouplist',, hash( '%removed', $feedback.removed, '%unlinked', $feedback.unlinked ) )}</div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert">{'No group was selected. Tick the groups to remove first.'|i18n( 'design/admin/workflow/grouplist' )}</div>
    {elseif eq( $feedback.type, 'gone' )}
<div class="exp-feedback is-warn" role="alert">{'The selected groups no longer exist; somebody may have removed them already.'|i18n( 'design/admin/workflow/grouplist' )}</div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="wfgroups-overview-title">
<h2 class="exp-sr" id="wfgroups-overview-title">{'Overview'|i18n( 'design/admin/workflow/grouplist' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.groups}</strong><span>{'Groups'|i18n( 'design/admin/workflow/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.workflows}</strong><span>{'Workflows'|i18n( 'design/admin/workflow/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.enabled}</strong><span>{'Enabled'|i18n( 'design/admin/workflow/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.triggered}</strong><span>{'Run by a trigger'|i18n( 'design/admin/workflow/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.waiting}</strong><span>{'Processes waiting'|i18n( 'design/admin/workflow/grouplist' )}</span></li>
    {if $summary.ungrouped|gt( 0 )}<li class="exp-figure is-attention"><strong>{$summary.ungrouped}</strong><span>{'In no group'|i18n( 'design/admin/workflow/grouplist' )}</span></li>{/if}
</ul>
</section>
{/if}

<div class="exp-toolbar">
    <div class="exp-field exp-js-only" hidden>
        <label for="wfgroups-search">{'Find a group or workflow'|i18n( 'design/admin/workflow/grouplist' )}</label>
        <input type="search" id="wfgroups-search" autocomplete="off" spellcheck="false" aria-controls="wfgroups-list" aria-describedby="wfgroups-filter-count" />
    </div>
    <div class="exp-field">
        <span class="exp-help">{'Open a group to create workflows in it, change their order of events or remove them. Triggers connect a workflow to an operation.'|i18n( 'design/admin/workflow/grouplist' )}</span>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary" name="NewGroupButton" value="1" title="{'Create a new workflow group.'|i18n( 'design/admin/workflow/grouplist' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New workflow group'|i18n( 'design/admin/workflow/grouplist' )}</button>
            <a class="exp-btn" href={'trigger/list'|ezurl}>{'Triggers'|i18n( 'design/admin/workflow/grouplist' )}</a>
            <a class="exp-btn" href={'workflow/processlist'|ezurl}>{'Workflow processes'|i18n( 'design/admin/workflow/grouplist' )}</a>
        </div>
    </div>
    <p class="exp-filter-count exp-js-only" id="wfgroups-filter-count" aria-live="polite" hidden></p>
</div>

<section class="exp-section" aria-labelledby="wfgroups-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="wfgroups-list-title">{'All workflow groups'|i18n( 'design/admin/workflow/grouplist' )}</h2>
    {if $group_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/workflow/grouplist',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $group_count ), '%count', $group_count ) )}</span>
    {/if}
    {if $groups|count}<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="wfgroups-select-all" data-select-all="ContentClass_id_checked[]" /> {'Select all on this page'|i18n( 'design/admin/workflow/grouplist' )}</label>{/if}
</div>

{if $groups|count|eq( 0 )}
<p class="exp-empty">{'There are no workflow groups. Create one with New workflow group, then create workflows in it.'|i18n( 'design/admin/workflow/grouplist' )}</p>
{else}
<ul class="exp-secs" id="wfgroups-list" data-list="1">
{foreach $groups as $group}
    {def $info = first_set( $overview[$group.id], false() )
         $card_id = concat( 'wfgroup-', $group.id )}
<li class="exp-sec" id="{$card_id}" data-search="{if $info}{$info.search|wash}{else}{$group.name|downcase|wash}{/if}">
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <label class="exp-select" title="{'Select workflow group for removal.'|i18n( 'design/admin/workflow/grouplist' )}">
                <input type="checkbox" name="ContentClass_id_checked[]" value="{$group.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/workflow/grouplist',, hash( '%name', $group.name ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title"><a href={concat( $module.functions.workflowlist.uri, '/', $group.id )|ezurl}>{$group.name|wash}</a></h3>
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/workflow/grouplist',, hash( '%id', $group.id ) )}</span>
            {if $info}
            <ul class="exp-badges">
                {if $info.workflow_count|eq( 0 )}<li class="exp-badge">{'Empty'|i18n( 'design/admin/workflow/grouplist' )}</li>{else}<li class="exp-badge is-info">{'%count workflows'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $info.workflow_count ) )}</li>{/if}
                {if $info.triggered|gt( 0 )}<li class="exp-badge is-ok">{'%count run by triggers'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $info.triggered ) )}</li>{/if}
                {if $info.waiting|gt( 0 )}<li class="exp-badge is-warn">{'%count processes waiting'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $info.waiting ) )}</li>{/if}
            </ul>
            {/if}
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( $module.functions.workflowlist.uri, '/', $group.id )|ezurl} aria-describedby="{$card_id}-title">{'Open'|i18n( 'design/admin/workflow/grouplist' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( $module.functions.groupedit.uri, '/', $group.id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%workflow_group_name> workflow group.'|i18n( 'design/admin/workflow/grouplist',, hash( '%workflow_group_name', $group.name ) )|wash}">{'Edit'|i18n( 'design/admin/workflow/grouplist' )}</a>
        </div>
    </div>
    {if $info}
    {if $info.workflow_count|gt( 0 )}
    <div class="exp-table-wrap" style="margin-top: 12px;">
    <table class="exp-table">
    <thead><tr>
        <th scope="col">{'Workflow'|i18n( 'design/admin/workflow/grouplist' )}</th>
        <th scope="col">{'Runs'|i18n( 'design/admin/workflow/grouplist' )}</th>
        <th scope="col" class="exp-num">{'Events'|i18n( 'design/admin/workflow/grouplist' )}</th>
        <th scope="col">{'Modified'|i18n( 'design/admin/workflow/grouplist' )}</th>
    </tr></thead>
    <tbody>
    {foreach $info.workflows as $workflow}
    <tr>
        <td><a href={concat( 'workflow/view/', $workflow.id )|ezurl}>{$workflow.name|wash}</a>
            {if $workflow.enabled|not} <span class="exp-badge is-warn">{'Disabled'|i18n( 'design/admin/workflow/grouplist' )}</span>{/if}
            {if $workflow.other_groups|count}<span class="exp-meta">{'Also in:'|i18n( 'design/admin/workflow/grouplist' )} {foreach $workflow.other_groups as $other}<a href={concat( $module.functions.workflowlist.uri, '/', $other.id )|ezurl}>{$other.name|wash}</a>{delimiter}, {/delimiter}{/foreach}</span>{/if}</td>
        <td>{if $workflow.triggers|count}{foreach $workflow.triggers as $label}{$label|wash}{delimiter}<br />{/delimiter}{/foreach}{else}<span class="exp-muted">{'No trigger'|i18n( 'design/admin/workflow/grouplist' )}</span>{/if}
            {if $workflow.waiting|gt( 0 )}<span class="exp-meta"><a href={'workflow/processlist'|ezurl}>{'%count processes waiting'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $workflow.waiting ) )}</a></span>{/if}</td>
        <td class="exp-num">{$workflow.events}</td>
        <td>{$workflow.modified|l10n( shortdatetime )}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {/if}
    <dl class="exp-facts">
        <div>
            <dt>{'Last change'|i18n( 'design/admin/workflow/grouplist' )}</dt>
            <dd>{$info.last_modified|l10n( shortdatetime )}<span class="exp-meta">{'of the group or one of its workflows'|i18n( 'design/admin/workflow/grouplist' )}</span></dd>
        </div>
        <div>
            <dt>{'Removing it'|i18n( 'design/admin/workflow/grouplist' )}</dt>
            <dd>{if and( $info.removes|eq( 0 ), $info.unlinks|eq( 0 ) )}{'Removes only the group.'|i18n( 'design/admin/workflow/grouplist' )}{else}{'Removes %removes workflows that are only in this group; %unlinks stay in their other groups.'|i18n( 'design/admin/workflow/grouplist',, hash( '%removes', $info.removes, '%unlinks', $info.unlinks ) )}{/if}
                {if $info.removes_triggered|gt( 0 )}<span class="exp-meta">{'%count of them are run by triggers, which go with them.'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $info.removes_triggered ) )}</span>{/if}</dd>
        </div>
    </dl>
    {/if}
</li>
    {undef $info $card_id}
{/foreach}
</ul>
<p class="exp-empty exp-no-match" hidden>{'No group on this page matches. Clear the search.'|i18n( 'design/admin/workflow/grouplist' )}</p>
{/if}

{if $group_count|gt( $limit )}
<div class="exp-listfoot"><div class="exp-pager">
{include name=GroupNavigator
         uri='design:navigator/google.tpl'
         page_uri='/workflow/grouplist'
         item_count=$group_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div></div>
{/if}
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="DeleteGroupButton" value="1" aria-describedby="wfgroups-remove-help" title="{'Remove selected workflow groups.'|i18n( 'design/admin/workflow/grouplist' )}"{if $groups|count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/workflow/grouplist' )}</button>
        <button type="submit" class="exp-btn" name="NewGroupButton" value="1" title="{'Create a new workflow group.'|i18n( 'design/admin/workflow/grouplist' )}">{'New workflow group'|i18n( 'design/admin/workflow/grouplist' )}</button>
    </div>
    <p class="exp-meta" id="wfgroups-remove-help">{'Remove selected asks for confirmation first and lists the workflows, triggers and waiting processes it affects.'|i18n( 'design/admin/workflow/grouplist' )} <span class="exp-selected-count" data-for="ContentClass_id_checked[]" aria-live="polite"></span></p>
</div>

</div></div></div>
</div>

</form>

{undef $overview $summary $feedback}
{include uri='design:workflow/exp_list_script.tpl' text_shown='%shown of %count groups on this page shown'|i18n( 'design/admin/workflow/grouplist' ) text_all='Groups on this page: %count'|i18n( 'design/admin/workflow/grouplist' ) text_selected='%count selected.'|i18n( 'design/admin/workflow/grouplist' )}
