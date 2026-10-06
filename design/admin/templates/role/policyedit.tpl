{* Edit the limitations of one policy (role/policyedit/<policy>).

   The policy's module and function, then the same limitation fields as the third step of the policy wizard
   (role/exp_limitations.tpl). OK (UpdatePolicy) keeps the change in the draft of the role and goes back to the role
   editor; Cancel (DiscardChange) drops it. A function without limitations has nothing to edit here. Every name is
   the one the view has always read; every value is escaped. The same file is in design/admin and design/admin4.
   Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

<form method="post" action={concat( $Module.functions.policyedit.uri, '/', $policy_id, '/' )|ezurl} class="exp-roles exp-standalone">
{if $function_limitations}<input type="submit" name="UpdatePolicy" value="{'OK'|i18n( 'design/admin/role/policyedit' )}" tabindex="-1" aria-hidden="true" class="exp-hidden-default exp-sr" />{/if}
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Edit <%policy_name> policy for <%role_name> role'|i18n( 'design/admin/role/policyedit',, hash( '%policy_name', concat( $current_module, '/', $current_function ), '%role_name', $role_name ) )|wash}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<dl class="exp-facts exp-panel">
    <div><dt>{'Module'|i18n( 'design/admin/role/policyedit' )}</dt><dd><code>{$current_module|wash}</code></dd></div>
    <div><dt>{'Function'|i18n( 'design/admin/role/policyedit' )}</dt><dd><code>{$current_function|wash}</code></dd></div>
    <div><dt>{'Role'|i18n( 'design/admin/role/policyedit' )}</dt><dd>{$role_name|wash}</dd></div>
</dl>

{if $function_limitations}
<p class="exp-intro">{'Choose where the function may be used. "Any" leaves a limitation out. The change is kept in the draft of the role; it reaches the users when you save the role.'|i18n( 'design/admin/role/policyedit' )}</p>
{include uri='design:role/exp_limitations.tpl' i18n_context='design/admin/role/policyedit'}
{else}
<p class="exp-empty">{'The function limitations of this policy cannot be edited. This is either because the function does not support limitations or because the function was assigned without limitations when the policy was created.'|i18n( 'design/admin/role/policyedit' )}</p>
{/if}

<input type="hidden" name="CurrentModule" value="{$current_module|wash}" />
<input type="hidden" name="CurrentFunction" value="{$current_function|wash}" />

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="UpdatePolicy" value="1"{if not( $function_limitations )} disabled="disabled"{/if}>{'OK'|i18n( 'design/admin/role/policyedit' )}</button>
        <button class="exp-btn" type="submit" name="DiscardChange" value="1">{'Cancel'|i18n( 'design/admin/role/policyedit' )}</button>
    </div>
    <p class="exp-meta">{'Both go back to the role editor.'|i18n( 'design/admin/role/policyedit' )}</p>
</div>

</div></div></div>
</div>
</form>
