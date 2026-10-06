{* Copy a role (role/copy/<id>): what the copy will hold, and a button that makes it.

   Opening the address only shows this page; the copy is made by the form (CopyRoleButton, a POST with the form
   token), so a link or an image on another page can no longer make copies. CancelCopyButton goes back to the role.
   The copy is a new role named "Copy of <name>" with the same policies and no assignments; it opens in the role
   editor. A role with a policy beyond the editor's own access cannot be copied (PreventPrivilegeEscalation); the
   page says so and the button is off. An edit view, drawn without the main card: .exp-standalone gives it its own. The same file is in
   design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

<form method="post" action={concat( '/role/copy/', $role.id )|ezurl} class="exp-roles exp-standalone">
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Copy the <%role_name> role'|i18n( 'design/admin/role/copy',, hash( '%role_name', $role.name ) )|wash}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'The copy is a new role with the same policies, named “Copy of %role_name”. It is not assigned to anyone, so it gives nobody anything until you assign it. It opens in the role editor, where you can rename it and change its policies.'|i18n( 'design/admin/role/copy',, hash( '%role_name', $role.name ) )|wash}</p>

{include uri='design:role/exp_grant_refused.tpl' refusal=first_set( $grant_refused, false() )}
{if and( first_set( $grant_blocked, 0 )|gt( 0 ), first_set( $grant_refused, false() )|not )}
<div class="exp-feedback is-warn" role="note">{'%count policies of this role go beyond your own access, so you cannot copy it. You can only give what you have yourself.'|i18n( 'design/admin/role/grant',, hash( '%count', $grant_blocked ) )}</div>
{/if}

{if $role_summary}
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$role_summary.policies}</strong> <span>{'Policies copied'|i18n( 'design/admin/role/copy' )}</span></li>
    <li class="exp-figure"><strong>{$role_summary.assigned}</strong> <span>{'Assignments of the original (not copied)'|i18n( 'design/admin/role/copy' )}</span></li>
</ul>
{if $role_summary.full_access}
<div class="exp-feedback is-warn" role="note">{'This role has a policy that gives access to everything. The copy will have it too.'|i18n( 'design/admin/role/copy' )}</div>
{/if}
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="CopyRoleButton" value="1"{if first_set( $grant_blocked, 0 )|gt( 0 )} disabled="disabled"{/if}>{'Make the copy'|i18n( 'design/admin/role/copy' )}</button>
        <button type="submit" class="exp-btn" name="CancelCopyButton" value="1">{'Cancel'|i18n( 'design/admin/role/copy' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
