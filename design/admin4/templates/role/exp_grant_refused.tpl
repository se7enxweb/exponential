{* The refusal of a grant the editor could not hold themselves (site.ini [RoleSettings] PreventPrivilegeEscalation),
   kept by expRoleGrantCheck::remember() and shown once by role/edit, role/view and role/copy.
   Variable: refusal (what: policy, save, assign, copy, or deactivated_group: the group of deactivated users was
   left out of an assignment, see expUserDeactivation::withoutGroup(); grants: the refused policies in words). Every
   text is escaped. The same file is in design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{if and( $refusal, eq( $refusal.what, 'deactivated_group' ) )}
<div class="a4-notice is-warn" role="alert" id="role-deactivated-group-refused">
    <p><strong>{'The role was not assigned to the group of deactivated users.'|i18n( 'design/admin/role/grant' )}</strong>
    {'That group has no roles, so the accounts moved into it by Deactivate and move can do nothing. Any other users and groups you chose were assigned.'|i18n( 'design/admin/role/grant' )}</p>
</div>
{elseif $refusal}
<div class="a4-notice is-bad" role="alert" id="role-grant-refused">
    <p><strong>{switch match=$refusal.what}
        {case match='save'}{'The role was not saved.'|i18n( 'design/admin/role/grant' )}{/case}
        {case match='assign'}{'The role was not assigned.'|i18n( 'design/admin/role/grant' )}{/case}
        {case match='copy'}{'The role was not copied.'|i18n( 'design/admin/role/grant' )}{/case}
        {case}{'The policy was not added.'|i18n( 'design/admin/role/grant' )}{/case}
    {/switch}</strong>
    {'You can only give what you have yourself. These policies go beyond your own access:'|i18n( 'design/admin/role/grant' )}</p>
    <ul>
    {foreach $refusal.grants as $grant_text}
        <li>{$grant_text|wash}</li>
    {/foreach}
    </ul>
    <p>{'Narrow them to what your own roles allow, or ask an administrator with full access.'|i18n( 'design/admin/role/grant' )}</p>
</div>
{/if}
