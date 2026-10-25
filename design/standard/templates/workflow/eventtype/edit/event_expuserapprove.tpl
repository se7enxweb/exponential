{* The workflow event "Approve user accounts" (doc/eng-us/bc/6.0/user-account-approval.md): which groups' new accounts
   wait for approval, who approves, who needs no approval, and whether the approvers get a mail. *}
<div class="block">

{* Groups whose new accounts need approval *}
<fieldset class="block">
<legend>{'User groups whose new accounts need approval'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
<p>{'An account published into one of these groups, or below one, waits until an approver accepts it. It cannot sign in before.'|i18n( 'design/admin/workflow/eventtype/edit' )}</p>
{if $event.required_groups}
    <table class="list" cellspacing="0">
    <tr>
        <th scope="col" class="tight">&nbsp;</th>
        <th scope="col">{'Group'|i18n( 'design/admin/workflow/eventtype/edit' )}</th>
    </tr>
    {foreach $event.required_groups as $group_id sequence array( 'bglight', 'bgdark' ) as $style}
        <tr class="{$style}">
            <td><input type="checkbox" aria-labelledby="required-group-{$event.id}-{$group_id}" name="DeleteRequiredGroupIDArray_{$event.id}[]" value="{$group_id}" /></td>
            <td id="required-group-{$event.id}-{$group_id}">{fetch( content, object, hash( object_id, $group_id ) ).name|wash}</td>
        </tr>
    {/foreach}
    </table>
{else}
    <p>{'No groups selected.'|i18n( 'design/admin/workflow/eventtype/edit' )}</p>
{/if}
<input class="button" type="submit" name="CustomActionButton[{$event.id}_RemoveRequiredGroups]" value="{'Remove selected'|i18n( 'design/admin/workflow/eventtype/edit' )}"{if $event.required_groups|not} disabled="disabled"{/if} />
<input class="button" type="submit" name="CustomActionButton[{$event.id}_AddRequiredGroups]" value="{'Add groups'|i18n( 'design/admin/workflow/eventtype/edit' )}" />
</fieldset>

{* Users who approve *}
<fieldset class="block">
<legend>{'Users who approve the accounts'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
{if $event.approve_users}
    <table class="list" cellspacing="0">
    <tr>
        <th scope="col" class="tight">&nbsp;</th>
        <th scope="col">{'User'|i18n( 'design/admin/workflow/eventtype/edit' )}</th>
    </tr>
    {foreach $event.approve_users as $user_id sequence array( 'bglight', 'bgdark' ) as $style}
        <tr class="{$style}">
            <td><input type="checkbox" aria-labelledby="approve-user-{$event.id}-{$user_id}" name="DeleteApproveUserIDArray_{$event.id}[]" value="{$user_id}" /></td>
            <td id="approve-user-{$event.id}-{$user_id}">{fetch( content, object, hash( object_id, $user_id ) ).name|wash}</td>
        </tr>
    {/foreach}
    </table>
{else}
    <p>{'No users selected.'|i18n( 'design/admin/workflow/eventtype/edit' )}</p>
{/if}
<input class="button" type="submit" name="CustomActionButton[{$event.id}_RemoveApproveUsers]" value="{'Remove selected'|i18n( 'design/admin/workflow/eventtype/edit' )}"{if $event.approve_users|not} disabled="disabled"{/if} />
<input class="button" type="submit" name="CustomActionButton[{$event.id}_AddApproveUsers]" value="{'Add users'|i18n( 'design/admin/workflow/eventtype/edit' )}" />
</fieldset>

{* Groups who approve *}
<fieldset class="block">
<legend>{'Groups who approve the accounts'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
{if $event.approve_groups}
    <table class="list" cellspacing="0">
    <tr>
        <th scope="col" class="tight">&nbsp;</th>
        <th scope="col">{'Group'|i18n( 'design/admin/workflow/eventtype/edit' )}</th>
    </tr>
    {foreach $event.approve_groups as $group_id sequence array( 'bglight', 'bgdark' ) as $style}
        <tr class="{$style}">
            <td><input type="checkbox" aria-labelledby="approve-group-{$event.id}-{$group_id}" name="DeleteApproveGroupIDArray_{$event.id}[]" value="{$group_id}" /></td>
            <td id="approve-group-{$event.id}-{$group_id}">{fetch( content, object, hash( object_id, $group_id ) ).name|wash}</td>
        </tr>
    {/foreach}
    </table>
{else}
    <p>{'No groups selected.'|i18n( 'design/admin/workflow/eventtype/edit' )}</p>
{/if}
<input class="button" type="submit" name="CustomActionButton[{$event.id}_RemoveApproveGroups]" value="{'Remove selected'|i18n( 'design/admin/workflow/eventtype/edit' )}"{if $event.approve_groups|not} disabled="disabled"{/if} />
<input class="button" type="submit" name="CustomActionButton[{$event.id}_AddApproveGroups]" value="{'Add groups'|i18n( 'design/admin/workflow/eventtype/edit' )}" />
</fieldset>

{* Groups that need no approval *}
<fieldset class="block">
<legend>{'User groups that need no approval (their members publish such accounts directly; approvers never need it)'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
{if $event.selected_usergroups}
    <table class="list" cellspacing="0">
    <tr>
        <th scope="col" class="tight">&nbsp;</th>
        <th scope="col">{'Group'|i18n( 'design/admin/workflow/eventtype/edit' )}</th>
    </tr>
    {foreach $event.selected_usergroups as $group_id sequence array( 'bglight', 'bgdark' ) as $style}
        <tr class="{$style}">
            <td><input type="checkbox" aria-labelledby="exclude-group-{$event.id}-{$group_id}" name="DeleteExcludeUserIDArray_{$event.id}[]" value="{$group_id}" /></td>
            <td id="exclude-group-{$event.id}-{$group_id}">{fetch( content, object, hash( object_id, $group_id ) ).name|wash}</td>
        </tr>
    {/foreach}
    </table>
{else}
    <p>{'No groups selected.'|i18n( 'design/admin/workflow/eventtype/edit' )}</p>
{/if}
<input class="button" type="submit" name="CustomActionButton[{$event.id}_RemoveExcludeUser]" value="{'Remove selected'|i18n( 'design/admin/workflow/eventtype/edit' )}"{if $event.selected_usergroups|not} disabled="disabled"{/if} />
<input class="button" type="submit" name="CustomActionButton[{$event.id}_AddExcludeUser]" value="{'Add groups'|i18n( 'design/admin/workflow/eventtype/edit' )}" />
</fieldset>

{* The mail to the approvers *}
<div class="element">
    <input type="hidden" name="WorkflowEvent_event_expuserapprove_mail_{$event.id}_shown" value="1" />
    <label for="expuserapprove-mail-{$event.id}"><input type="checkbox" id="expuserapprove-mail-{$event.id}" name="WorkflowEvent_event_expuserapprove_mail_{$event.id}" value="1"{if $event.send_mail} checked="checked"{/if} />
    {'Send the approvers an e-mail with the link to the approval (they sign in to approve or deny)'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
</div>

</div>
