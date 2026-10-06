{* The workflow triggers (trigger/list): which workflow runs before or after which operation.

   What a trigger is, an overview, a search and a filter, then one table per module with every operation that
   workflow.ini [OperationSettings] AvailableOperationList offers, before and after, in words ("Before publishing
   content") and as module/operation. Each row has the select that chooses its workflow, as before, and says what
   runs now: the workflow, its events, whether it is enabled and how many of its processes wait. Triggers without a
   workflow are listed too, marked "Not set". Stored triggers for operations the list no longer offers are shown
   below, where they can be removed.

   The same file is in design/admin and design/admin4. The extra data comes from the view (trigger_groups,
   trigger_orphans, trigger_summary, trigger_feedback); without it (an older view class) the page draws the rows
   from possible_triggers as before. Every variable the page had before is still set: possible_triggers, triggers,
   modules, functions, current_module, current_function, show_modules, show_functions, module. Field names
   (WorkflowID_<key>, StoreButton, DeleteIDArray[], RemoveButton) are unchanged. Guide: doc/guides/workflows.md *}
{include uri='design:trigger/exp_style.tpl'}

{def $groups = first_set( $trigger_groups, false() )
     $orphans = first_set( $trigger_orphans, array() )
     $summary = first_set( $trigger_summary, false() )
     $feedback = first_set( $trigger_feedback, false() )}

<form action={$module.functions.list.uri|ezurl} method="post">

<div class="context-block exp-lists exp-triggers">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow triggers (%trigger_count)'|i18n( 'design/admin/trigger/list',, hash( '%trigger_count', $possible_triggers|count ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A trigger connects a workflow to an operation of the system: before or after content is published, an order is confirmed, something is added to the basket. When the operation runs, the workflow runs with it; a workflow run before an operation can hold it back, for example until an approver agrees. Each operation can run one workflow before and one after it.'|i18n( 'design/admin/trigger/list' )}</p>

{if $feedback}
    {if eq( $feedback.type, 'saved' )}
        {if $feedback.changed|gt( 0 )}
<div class="exp-feedback is-ok" role="status">{'Saved: %count triggers changed.'|i18n( 'design/admin/trigger/list',, hash( '%count', $feedback.changed ) )}</div>
        {else}
<div class="exp-feedback is-info" role="status">{'Saved. Nothing had changed.'|i18n( 'design/admin/trigger/list' )}</div>
        {/if}
    {elseif eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{'Removed: %count triggers.'|i18n( 'design/admin/trigger/list',, hash( '%count', $feedback.changed ) )}</div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert">{'No trigger was selected. Tick the triggers to remove first.'|i18n( 'design/admin/trigger/list' )}</div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="trigger-overview-title">
<h2 class="exp-sr" id="trigger-overview-title">{'Overview'|i18n( 'design/admin/trigger/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.possible}</strong><span>{'Possible triggers'|i18n( 'design/admin/trigger/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.set}</strong><span>{'Run a workflow'|i18n( 'design/admin/trigger/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.unset}</strong><span>{'Not set'|i18n( 'design/admin/trigger/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.waiting}</strong><span>{'Processes waiting'|i18n( 'design/admin/trigger/list' )}</span></li>
    <li class="exp-figure{if $summary.attention|gt( 0 )} is-attention{/if}"><strong>{$summary.attention}</strong><span>{'Need attention'|i18n( 'design/admin/trigger/list' )}</span></li>
    {if $summary.orphans|gt( 0 )}<li class="exp-figure is-attention"><strong>{$summary.orphans}</strong><span>{'Not offered any more'|i18n( 'design/admin/trigger/list' )}</span></li>{/if}
</ul>
</section>

<div class="exp-toolbar exp-js-only" hidden>
    <div class="exp-field">
        <label for="trigger-search">{'Find a trigger'|i18n( 'design/admin/trigger/list' )}</label>
        <input type="search" id="trigger-search" autocomplete="off" spellcheck="false" aria-controls="trigger-table" aria-describedby="trigger-filter-count trigger-search-help" />
        <span class="exp-help" id="trigger-search-help">{'Operation in words, module, operation or workflow.'|i18n( 'design/admin/trigger/list' )}</span>
    </div>
    <fieldset class="exp-field">
        <legend>{'Show'|i18n( 'design/admin/trigger/list' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="TriggerFilter" data-filter="1" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/trigger/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="TriggerFilter" data-filter="1" value="set" /><span>{'Run a workflow'|i18n( 'design/admin/trigger/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="TriggerFilter" data-filter="1" value="!set" /><span>{'Not set'|i18n( 'design/admin/trigger/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="TriggerFilter" data-filter="1" value="attention" /><span>{'Need attention'|i18n( 'design/admin/trigger/list' )}</span></label>
        </div>
    </fieldset>
    <p class="exp-filter-count" id="trigger-filter-count" aria-live="polite"></p>
</div>
{/if}

<section class="exp-section" aria-labelledby="trigger-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="trigger-list-title">{'Operations and their workflows'|i18n( 'design/admin/trigger/list' )}</h2>
    <p>{'Choose a workflow for an operation, or No workflow, then Apply changes. A select lists only the workflows whose events all allow that operation.'|i18n( 'design/admin/trigger/list' )}</p>
</div>

{if $possible_triggers|count|eq( 0 )}
<p class="exp-empty">{'No operation is offered for triggers. Add operations to workflow.ini [OperationSettings] AvailableOperationList, for example content_publish.'|i18n( 'design/admin/trigger/list' )}</p>
{elseif $groups}
<div class="exp-table-wrap">
<table class="exp-table" id="trigger-table">
<thead>
<tr>
    <th scope="col">{'When'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Workflow'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Runs now'|i18n( 'design/admin/trigger/list' )}</th>
</tr>
</thead>
{foreach $groups as $group}
<tbody data-list="1">
<tr><th scope="rowgroup" colspan="3">{'Module %module'|i18n( 'design/admin/trigger/list',, hash( '%module', $group.module|wash ) )} <span class="exp-meta" style="display: inline;">{'%set of %count run a workflow'|i18n( 'design/admin/trigger/list',, hash( '%set', $group.set, '%count', $group.rows|count ) )}</span></th></tr>
{foreach $group.rows as $row}
    {def $possible = false()}
    {foreach $possible_triggers as $candidate}{if eq( $candidate.key, $row.key )}{set $possible = $candidate}{break}{/if}{/foreach}
<tr class="{if or( $row.missing, $row.not_allowed )}is-attention{elseif $row.is_set}is-set{/if}" data-search="{concat( $row.label, ' ', $row.module, '/', $row.operation, ' ', $row.connect_type, ' ', first_set( $row.workflow.name, '' ) )|downcase|wash}" data-set="{if $row.is_set}1{else}0{/if}" data-attention="{if or( $row.missing, $row.not_allowed )}1{else}0{/if}">
    <td>
        <label for="trigger-{$row.key|wash}"><strong>{$row.label|wash}</strong></label>
        <span class="exp-meta"><code>{$row.module|wash}/{$row.operation|wash}</code> &middot; {if eq( $row.connect_type, 'before' )}{'before'|i18n( 'design/admin/trigger/list' )}{else}{'after'|i18n( 'design/admin/trigger/list' )}{/if}</span>
    </td>
    <td>
        <select id="trigger-{$row.key|wash}" name="WorkflowID_{$row.key|wash}" data-initial="{if $row.is_set}{$row.workflow_id}{else}-1{/if}" title="{'Select the workflow that should be triggered %type the %function function is executed within the %module module.'|i18n( 'design/admin/trigger/list',, hash( '%type', $row.connect_type, '%function', $row.operation, '%module', $row.module ) )|wash}">
            <option value="-1">{'No workflow'|i18n( 'design/admin/trigger/list' )}</option>
            {if $possible}{foreach $possible.allowed_workflows as $workflow}
            <option value="{$workflow.id}"{if eq( $workflow.id, $row.workflow_id )} selected="selected"{/if}>{$workflow.name|wash}</option>
            {/foreach}{/if}
            {if $row.not_allowed}
            <option value="{$row.workflow_id}" selected="selected">{'%name (not offered here)'|i18n( 'design/admin/trigger/list',, hash( '%name', $row.workflow.name ) )|wash}</option>
            {elseif $row.missing}
            <option value="{$row.workflow_id}" selected="selected">{'Workflow %id (removed)'|i18n( 'design/admin/trigger/list',, hash( '%id', $row.workflow_id ) )|wash}</option>
            {/if}
        </select>
        {if and( $row.allowed_count|eq( 0 ), $row.is_set|not )}<span class="exp-meta">{'No workflow can run here yet.'|i18n( 'design/admin/trigger/list' )}</span>{/if}
    </td>
    <td>
        {if $row.is_set|not}
        <span class="exp-badge is-muted">{'Not set'|i18n( 'design/admin/trigger/list' )}</span>
        {elseif $row.missing}
        <span class="exp-badge is-bad">{'Removed workflow'|i18n( 'design/admin/trigger/list' )}</span>
        <span class="exp-meta">{'The trigger names a workflow that no longer exists. Choose another or No workflow.'|i18n( 'design/admin/trigger/list' )}</span>
        {else}
        <a href={concat( 'workflow/view/', $row.workflow_id )|ezurl}>{$row.workflow.name|wash}</a>
        <span class="exp-meta">{'Events: %count'|i18n( 'design/admin/trigger/list',, hash( '%count', $row.workflow.events ) )}{if $row.waiting|gt( 0 )}, <a href={'workflow/processlist'|ezurl}>{'%count processes waiting'|i18n( 'design/admin/trigger/list',, hash( '%count', $row.waiting ) )}</a>{/if}</span>
        {if $row.workflow.enabled|not}<span class="exp-badge is-warn">{'Disabled'|i18n( 'design/admin/trigger/list' )}</span>{/if}
        {if $row.not_allowed}<span class="exp-meta">{'Its events no longer all allow this operation, so the select does not offer it. It stays until you choose another.'|i18n( 'design/admin/trigger/list' )}</span>{/if}
        {/if}
    </td>
</tr>
    {undef $possible}
{/foreach}
</tbody>
{/foreach}
</table>
</div>
<p class="exp-empty exp-no-match" data-global="1" hidden>{'No trigger matches. Clear the search or choose All.'|i18n( 'design/admin/trigger/list' )}</p>
{else}
{* An older view class: the rows as the page always drew them. *}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Module'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Function'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Connection type'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Workflow'|i18n( 'design/admin/trigger/list' )}</th>
</tr></thead>
<tbody>
{foreach $possible_triggers as $trigger}
<tr>
    <td>{$trigger.module|wash}</td>
    <td>{$trigger.operation|wash}</td>
    <td>{$trigger.connect_type|wash}</td>
    <td><select name="WorkflowID_{$trigger.key|wash}" aria-label="{'Select the workflow that should be triggered %type the %function function is executed within the %module module.'|i18n( 'design/admin/trigger/list',, hash( '%type', $trigger.connect_type, '%function', $trigger.operation, '%module', $trigger.module ) )|wash}">
        <option value="-1">{'No workflow'|i18n( 'design/admin/trigger/list' )}</option>
        {foreach $trigger.allowed_workflows as $workflow}<option value="{$workflow.id}"{if eq( $workflow.id, $trigger.workflow_id )} selected="selected"{/if}>{$workflow.name|wash}</option>{/foreach}
    </select></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="1" title="{'Click this button to store changes if you have modified any of the fields above.'|i18n( 'design/admin/trigger/list' )}">{'Apply changes'|i18n( 'design/admin/trigger/list' )}</button>
    </div>
    <p class="exp-meta">{'A change takes effect for the next operation; processes already waiting keep the workflow they started with.'|i18n( 'design/admin/trigger/list' )} <span id="trigger-changed-count" aria-live="polite"></span></p>
</div>
</section>

{if $orphans|count}
<section class="exp-section" aria-labelledby="trigger-orphans-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="trigger-orphans-title">{'Triggers this list no longer offers'|i18n( 'design/admin/trigger/list' )}</h2>
    <p>{'These triggers are stored, but their operation, or this side of it, is not in workflow.ini [OperationSettings] AvailableOperationList. Add it there to use them again, or remove them.'|i18n( 'design/admin/trigger/list' )}</p>
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/trigger/list' )}</span></th>
    <th scope="col">{'When'|i18n( 'design/admin/trigger/list' )}</th>
    <th scope="col">{'Workflow'|i18n( 'design/admin/trigger/list' )}</th>
</tr></thead>
<tbody>
{foreach $orphans as $orphan}
<tr>
    <td><input type="checkbox" name="DeleteIDArray[]" value="{$orphan.id}" aria-label="{'Select the trigger %label for removal'|i18n( 'design/admin/trigger/list',, hash( '%label', $orphan.label ) )|wash}" /></td>
    <td><strong>{$orphan.label|wash}</strong><span class="exp-meta"><code>{$orphan.module|wash}/{$orphan.function|wash}</code></span></td>
    <td>{if $orphan.workflow_id|gt( 0 )}<a href={concat( 'workflow/view/', $orphan.workflow_id )|ezurl}>{'Workflow %id'|i18n( 'design/admin/trigger/list',, hash( '%id', $orphan.workflow_id ) )}</a>{else}{'None'|i18n( 'design/admin/trigger/list' )}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveButton" value="1">{'Remove selected'|i18n( 'design/admin/trigger/list' )}</button>
    </div>
    <p class="exp-meta">{'Removing one of these changes nothing that runs now: an operation not offered here does not run its triggers. The workflow stays.'|i18n( 'design/admin/trigger/list' )}</p>
</div>
</section>
{/if}

</div></div></div>
</div>

</form>

{undef $groups $orphans $summary $feedback}
{include uri='design:trigger/exp_list_script.tpl' text_shown='%shown of %count triggers shown'|i18n( 'design/admin/trigger/list' ) text_all='Triggers: %count'|i18n( 'design/admin/trigger/list' ) text_selected='%count selected.'|i18n( 'design/admin/trigger/list' )}
<script type="text/javascript">
var expTriggerChanged = '{'%count unsaved changes.'|i18n( 'design/admin/trigger/list' )|wash( javascript )}';
{literal}
(function () {
    var out = document.getElementById( 'trigger-changed-count' );
    var selects = document.querySelectorAll( '.exp-triggers select[data-initial]' );
    function update() {
        var n = 0, i;
        for ( i = 0; i < selects.length; i++ ) {
            var changed = selects[i].value !== selects[i].getAttribute( 'data-initial' );
            var row = selects[i].closest( 'tr' );
            if ( row ) row.classList.toggle( 'is-changed', changed );
            if ( changed ) n++;
        }
        if ( out ) out.textContent = n ? expTriggerChanged.split( '%count' ).join( n ) : '';
    }
    for ( var i = 0; i < selects.length; i++ ) selects[i].addEventListener( 'change', update );
})();
{/literal}
</script>
