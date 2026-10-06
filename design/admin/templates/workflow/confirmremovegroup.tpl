{* The confirmation of Remove selected on the workflow group list (workflow/grouplist).

   Shown instead of the list when Remove selected is pressed: the groups, the workflows that are removed with them
   (only those in no other group), their triggers and waiting processes, and the workflows that stay in another
   group. Remove posts the same ids again with ConfirmRemoveButton; Cancel is a link back and changes nothing.

   Variables: remove_groups (eZWorkflowGroup objects), remove_overview (group id => what the list's cards show),
   remove_plan (remove: workflow ids, unlink: workflow id => the groups it stays in), module.
   Guide: doc/guides/workflows.md *}
{include uri='design:workflow/exp_style.tpl'}
{def $removed_triggered = 0
     $removed_waiting = 0}
{foreach $remove_groups as $group}{if is_set( $remove_overview[$group.id] )}{set $removed_waiting = sum( $removed_waiting, $remove_overview[$group.id].removes_waiting )
     $removed_triggered = sum( $removed_triggered, $remove_overview[$group.id].removes_triggered )}{/if}{/foreach}

<div class="context-block exp-lists exp-wfgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Remove workflow groups?'|i18n( 'design/admin/workflow/grouplist' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form method="post" action={concat( $module.functions.grouplist.uri )|ezurl}>
<section class="exp-confirm" aria-labelledby="wfgroups-confirm-title">
    <h2 class="exp-h2" id="wfgroups-confirm-title">{'What happens'|i18n( 'design/admin/workflow/grouplist' )}</h2>
    <ul class="exp-consequences">
        <li>{'%count workflows that belong only to these groups are removed, with their events.'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $remove_plan.remove|count ) )}</li>
        {if $remove_plan.unlink|count}<li>{'%count workflows that also belong to another group stay there.'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $remove_plan.unlink|count ) )}</li>{/if}
        {if $removed_triggered|gt( 0 )}<li><strong>{'%count of the removed workflows are run by triggers. Those triggers are removed too, so the operations they belong to run without a workflow from then on.'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $removed_triggered ) )}</strong></li>{/if}
        {if $removed_waiting|gt( 0 )}<li><strong>{'%count processes wait in the removed workflows and could never finish. Cancel them first on the workflow processes page.'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $removed_waiting ) )}</strong> <a href={'workflow/processlist'|ezurl}>{'Workflow processes'|i18n( 'design/admin/workflow/grouplist' )}</a></li>{/if}
    </ul>

    <ul class="exp-secs">
    {foreach $remove_groups as $group}
        {def $info = first_set( $remove_overview[$group.id], false() )}
        <li class="exp-sec">
            <input type="hidden" name="ContentClass_id_checked[]" value="{$group.id}" />
            <div class="exp-sec-title">
                <h3>{$group.name|wash}</h3>
                <span class="exp-meta">{'ID %id'|i18n( 'design/admin/workflow/grouplist',, hash( '%id', $group.id ) )}</span>
            </div>
            {if and( $info, $info.workflows|count )}
            <div class="exp-table-wrap" style="margin-top: 12px;">
            <table class="exp-table">
            <thead><tr>
                <th scope="col">{'Workflow'|i18n( 'design/admin/workflow/grouplist' )}</th>
                <th scope="col">{'What happens to it'|i18n( 'design/admin/workflow/grouplist' )}</th>
                <th scope="col">{'Runs'|i18n( 'design/admin/workflow/grouplist' )}</th>
            </tr></thead>
            <tbody>
            {foreach $info.workflows as $workflow}
            <tr>
                <td><a href={concat( 'workflow/view/', $workflow.id )|ezurl}>{$workflow.name|wash}</a></td>
                <td>{if is_set( $remove_plan.unlink[$workflow.id] )}<span class="exp-badge is-ok">{'Stays'|i18n( 'design/admin/workflow/grouplist' )}</span> <span class="exp-meta">{'in:'|i18n( 'design/admin/workflow/grouplist' )} {foreach $workflow.other_groups as $other}{$other.name|wash}{delimiter}, {/delimiter}{/foreach}</span>{else}<span class="exp-badge is-bad">{'Removed'|i18n( 'design/admin/workflow/grouplist' )}</span>{/if}</td>
                <td>{if $workflow.triggers|count}{foreach $workflow.triggers as $label}{$label|wash}{delimiter}<br />{/delimiter}{/foreach}{else}<span class="exp-muted">{'No trigger'|i18n( 'design/admin/workflow/grouplist' )}</span>{/if}{if $workflow.waiting|gt( 0 )}<span class="exp-meta">{'%count processes waiting'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $workflow.waiting ) )}</span>{/if}</td>
            </tr>
            {/foreach}
            </tbody>
            </table>
            </div>
            {else}
            <p class="exp-meta" style="margin-top: 8px;">{'The group is empty: only the group is removed.'|i18n( 'design/admin/workflow/grouplist' )}</p>
            {/if}
        </li>
        {undef $info}
    {/foreach}
    </ul>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <input type="hidden" name="DeleteGroupButton" value="1" />
        <button type="submit" class="exp-btn exp-btn-danger" name="ConfirmRemoveButton" value="1">{'Remove %count groups'|i18n( 'design/admin/workflow/grouplist',, hash( '%count', $remove_groups|count ) )}</button>
        <a class="exp-btn exp-cancel" href={concat( $module.functions.grouplist.uri )|ezurl}>{'Cancel'|i18n( 'design/admin/workflow/grouplist' )}</a>
    </div>
    <p class="exp-meta">{'Nothing has been removed yet.'|i18n( 'design/admin/workflow/grouplist' )}</p>
</div>
</form>

</div></div></div>
</div>
{undef $removed_triggered $removed_waiting}
