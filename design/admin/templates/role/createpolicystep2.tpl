{* The policy wizard, step two: the function (role/edit/<draft>, after CustomFunction, Step2 or DiscardLimitation).

   The module chosen in step one, then its functions: full access to one (AddFunction: added at once) or limited
   access (Limitation: step three). A module without functions, or every module, cannot be limited this way; the page
   says what to do instead. Step1 goes back to step one, Cancel to the editor. Every name is the one the view has
   always read (CurrentModule, ModuleFunction, AddFunction, Limitation, Step1). The same file is in design/admin and
   design/admin4. Guide: doc/guides/roles-and-policies.md *}

<form method="post" action={concat( $module.functions.edit.uri, '/', $role.id, '/' )|ezurl} class="a4-page a4-standalone">
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create a new policy for the <%role_name> role'|i18n( 'design/admin/role/createpolicystep2',, hash( '%role_name', $role.name ) )|wash}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<ol class="a4-steps" aria-label="{'Steps'|i18n( 'design/admin/role/createpolicystep1' )}">
    <li class="is-done">{'Module: %module'|i18n( 'design/admin/role/createpolicystep2',, hash( '%module', cond( $current_module|eq( '*' ), 'All modules'|i18n( 'design/admin/role/createpolicystep2' ), $current_module ) ) )|wash}</li>
    <li class="is-current" aria-current="step">{'Function'|i18n( 'design/admin/role/createpolicystep1' )}</li>
    <li>{'Limitations'|i18n( 'design/admin/role/createpolicystep1' )}</li>
</ol>

<input type="hidden" name="CurrentModule" value="{$current_module|wash}" />

{if $no_functions|not}
<div class="a4-card">
<div class="a4-form">
    <div class="a4-field">
        <label for="ezrole-createpolizy-function">{'Function'|i18n( 'design/admin/role/createpolicystep2' )}</label>
        <select id="ezrole-createpolizy-function" name="ModuleFunction" aria-describedby="ezrole-createpolizy-function-help">
        {foreach $functions as $function_name}
            <option value="{$function_name|wash}">{$function_name|wash}</option>
        {/foreach}
        </select>
        <span class="a4-help" id="ezrole-createpolizy-function-help">{'The function of the %module module the users of the role may use.'|i18n( 'design/admin/role/createpolicystep2',, hash( '%module', $current_module ) )|wash}</span>
    </div>
</div>
<div class="a4-role-choice">
    <div>
        <strong>{'Full access to the function'|i18n( 'design/admin/role/createpolicystep2' )}</strong>
        <p>{'Everywhere, without limitations. The policy is added at once.'|i18n( 'design/admin/role/createpolicystep2' )}</p>
        <button class="a4-btn a4-btn-primary" type="submit" name="AddFunction" value="1">{'Grant full access'|i18n( 'design/admin/role/createpolicystep2' )}</button>
    </div>
    <div>
        <strong>{'Limited access'|i18n( 'design/admin/role/createpolicystep2' )}</strong>
        <p>{'Only in some sections, classes, subtrees, languages or siteaccesses, as the function supports. If it supports none, the policy gives full access to it.'|i18n( 'design/admin/role/createpolicystep2' )}</p>
        <button class="a4-btn" type="submit" name="Limitation" value="1">{'Grant limited access'|i18n( 'design/admin/role/createpolicystep2' )}</button>
    </div>
</div>
</div>
{else}
<div class="a4-notice is-warn" role="alert">
{if $current_module|eq( '*' )}
{'It is not possible to grant limited access to all modules at once. To grant unlimited access to all modules and their functions, go back to step one and select "Grant access to all functions". To grant limited access to different functions within different modules, you must set up a collection of policies.'|i18n( 'design/admin/role/createpolicystep2',, hash( '%module_name', $current_module ) )}
{else}
{'The selected module (%module_name) does not support limitations on the function level. Please go back to step one and use the "Grant access to all functions" option instead.'|i18n( 'design/admin/role/createpolicystep2',, hash( '%module_name', $current_module ) )|wash}
{/if}
</div>
{/if}

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn" type="submit" name="Step1" value="1">{'Go back to step one'|i18n( 'design/admin/role/createpolicystep2' )}</button>
        <button class="a4-btn" type="submit" name="CancelPolicyButton" value="1">{'Cancel'|i18n( 'design/admin/role/createpolicystep2' )}</button>
    </div>
    <p class="a4-meta">{'Cancel goes back to the role editor; nothing has been added.'|i18n( 'design/admin/role/createpolicystep1' )}</p>
</div>

</div></div></div>
</div>
</form>
