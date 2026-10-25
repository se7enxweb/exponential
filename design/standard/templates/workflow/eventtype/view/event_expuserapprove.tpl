{* The workflow event "Approve user accounts" in a workflow's view (doc/eng-us/bc/6.0/user-account-approval.md). *}
<div class="element">
{'Accounts that need approval'|i18n( 'design/standard/workflow/eventtype/view' )}:
{foreach $event.required_groups as $group_id}{fetch( content, object, hash( object_id, $group_id ) ).name|wash}{delimiter}, {/delimiter}{/foreach}
</div>

<div class="element">
{'Approver users'|i18n( 'design/standard/workflow/eventtype/view' )}:
{foreach $event.approve_users as $user_id}{fetch( content, object, hash( object_id, $user_id ) ).name|wash}{delimiter}, {/delimiter}{/foreach}
</div>

<div class="element">
{'Approver groups'|i18n( 'design/standard/workflow/eventtype/view' )}:
{foreach $event.approve_groups as $group_id}{fetch( content, object, hash( object_id, $group_id ) ).name|wash}{delimiter}, {/delimiter}{/foreach}
</div>

<div class="element">
{'Users without approval'|i18n( 'design/standard/workflow/eventtype/view' )}:
{foreach $event.selected_usergroups as $group_id}{fetch( content, object, hash( object_id, $group_id ) ).name|wash}{delimiter}, {/delimiter}{/foreach}
</div>

<div class="element">
{'E-mail to the approvers'|i18n( 'design/standard/workflow/eventtype/view' )}:
{if $event.send_mail}{'Yes'|i18n( 'design/standard/workflow/eventtype/view' )}{else}{'No'|i18n( 'design/standard/workflow/eventtype/view' )}{/if}
</div>
