{* The policy wizard, step three: the limitations (role/edit/<draft>, after Limitation or back from the content
   browser).

   One list per limitation the function supports (sections, classes, languages, siteaccesses ...), "Any" meaning no
   limitation of that kind; nodes and subtrees are picked in the content browser and listed with a remove button.
   Several limitations together must all be met; several values of one limitation are alternatives. OK
   (AddLimitation) adds the policy to the draft. Every name is the one the view has always read (the limitation names
   with [], DeleteNodeIDArray[], DeleteNodeButton, BrowseLimitationNodeButton, DeleteSubtreeIDArray[],
   DeleteSubtreeButton, BrowseLimitationSubtreeButton, Step1, Step2, AddLimitation, Cancel, CurrentModule,
   CurrentFunction). The same file is in design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

<form method="post" action={concat( $module.functions.edit.uri, '/', $role.id, '/' )|ezurl} class="exp-roles exp-standalone">
{* Enter in a field presses the first button of the form: OK, not a remove button *}
<input type="submit" name="AddLimitation" value="{'OK'|i18n( 'design/admin/role/createpolicystep3' )}" tabindex="-1" aria-hidden="true" class="exp-hidden-default exp-sr" />
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create a new policy for the <%role_name> role'|i18n( 'design/admin/role/createpolicystep3',, hash( '%role_name', $role.name ) )|wash}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<ol class="exp-steps" aria-label="{'Steps'|i18n( 'design/admin/role/createpolicystep1' )}">
    <li class="is-done">{'Module: %module'|i18n( 'design/admin/role/createpolicystep2',, hash( '%module', cond( $current_module|eq( '*' ), 'All modules'|i18n( 'design/admin/role/createpolicystep2' ), $current_module ) ) )|wash}</li>
    <li class="is-done">{'Function: %function'|i18n( 'design/admin/role/createpolicystep3',, hash( '%function', $current_function ) )|wash}</li>
    <li class="is-current" aria-current="step">{'Limitations'|i18n( 'design/admin/role/createpolicystep1' )}</li>
</ol>

<p class="exp-intro">{'Choose where the function may be used. "Any" leaves a limitation out. Several limitations must all be met; several values of one limitation are alternatives. Press OK to add the policy to the draft of the role.'|i18n( 'design/admin/role/createpolicystep3' )}</p>

<input type="hidden" name="CurrentModule" value="{$current_module|wash}" />
<input type="hidden" name="CurrentFunction" value="{$current_function|wash}" />

{include uri='design:role/exp_limitations.tpl' i18n_context='design/admin/role/createpolicystep3'}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="AddLimitation" value="1">{'OK'|i18n( 'design/admin/role/createpolicystep3' )}</button>
        <button class="exp-btn" type="submit" name="Step2" value="1">{'Go back to step two'|i18n( 'design/admin/role/createpolicystep3' )}</button>
        <button class="exp-btn" type="submit" name="Step1" value="1">{'Go back to step one'|i18n( 'design/admin/role/createpolicystep3' )}</button>
        <button class="exp-btn" type="submit" name="Cancel" value="1">{'Cancel'|i18n( 'design/admin/role/createpolicystep3' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
