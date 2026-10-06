{* The policy wizard, step one: the module (role/edit/<draft>, after CreatePolicy).

   Choose a module, then either every function of it (AddModule: the policy is added at once) or one function
   (CustomFunction: step two). With javascript the functions of the chosen module are offered here already, and
   AddFunction (full access to that function) and Limitation (step three) can be pressed from this step. "Every
   module" with every function is a policy that gives access to everything: the page says so before it is added.
   Cancel goes back to the editor; nothing has been added to the draft yet. Every name is the one the view has always
   read (CurrentModule, ModuleFunction, AddModule, CustomFunction, AddFunction, Limitation). The same file is in
   design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

<form id="createpolicyform" action={concat( $module.functions.edit.uri, '/', $role.id, '/' )|ezurl} method="post" class="exp-roles exp-standalone">
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create a new policy for the <%role_name> role'|i18n( 'design/admin/role/createpolicystep1',, hash( '%role_name', $role.name ) )|wash}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<ol class="exp-steps" aria-label="{'Steps'|i18n( 'design/admin/role/createpolicystep1' )}">
    <li class="is-current" aria-current="step">{'Module'|i18n( 'design/admin/role/createpolicystep1' )}</li>
    <li>{'Function'|i18n( 'design/admin/role/createpolicystep1' )}</li>
    <li>{'Limitations'|i18n( 'design/admin/role/createpolicystep1' )}</li>
</ol>

<p class="exp-intro">{'A policy lets the users of the role use a module: all of it, or one of its functions, possibly only in some sections, classes or subtrees. The policy is added to the draft of the role; it reaches its users when you save the role.'|i18n( 'design/admin/role/createpolicystep1' )}</p>

<div class="exp-panel">
<div class="exp-form-fields">
    <div class="exp-field">
        <label for="ezrole-createpolizy-module">{'Module'|i18n( 'design/admin/role/createpolicystep1' )}</label>
        <select id="ezrole-createpolizy-module" name="CurrentModule" aria-describedby="ezrole-createpolizy-module-help">
            <option value="*">{'Every module'|i18n( 'design/admin/role/createpolicystep1' )}</option>
            {foreach $modules as $module_name}
            <option value="{$module_name|wash}">{$module_name|wash}</option>
            {/foreach}
        </select>
        <span class="exp-help" id="ezrole-createpolizy-module-help">{'The module the users of the role may use, such as content (reading and editing content), user (logging in) or shop.'|i18n( 'design/admin/role/createpolicystep1' )}</span>
    </div>
    <div class="exp-field exp-js-only" hidden>
        <label for="ezrole-createpolizy-function">{'Function'|i18n( 'design/admin/role/createpolicystep2' )}</label>
        <select id="ezrole-createpolizy-function" name="ModuleFunction" disabled="disabled">
            <option value="*">{'Every function'|i18n( 'design/admin/role/createpolicystep1' )}</option>
        </select>
    </div>
</div>
<p class="exp-feedback is-bad" id="ezrole-createpolizy-everything" role="note">{'Every module with every function gives access to everything, including roles, users and setup. Give it only to administrators.'|i18n( 'design/admin/role/createpolicystep1' )}</p>

<div class="exp-choice">
    <div>
        <strong>{'All functions of the module'|i18n( 'design/admin/role/createpolicystep1' )}</strong>
        <p>{'Unlimited access to everything the module does. The policy is added at once.'|i18n( 'design/admin/role/createpolicystep1' )}</p>
        <button class="exp-btn exp-btn-primary" type="submit" name="AddModule" value="1">{'Grant access to all functions'|i18n( 'design/admin/role/createpolicystep1' )}</button>
    </div>
    <div>
        <strong>{'One function'|i18n( 'design/admin/role/createpolicystep1' )}</strong>
        <p>{'Access to one function only, such as reading content; the next step lets you limit it to sections, classes or subtrees where the function supports it.'|i18n( 'design/admin/role/createpolicystep1' )}</p>
        <span class="exp-actions">
            <button class="exp-btn button-module" type="submit" name="CustomFunction" value="1">{'Grant access to one function'|i18n( 'design/admin/role/createpolicystep1' )}</button>
            <button class="exp-btn button-function exp-js-only" hidden disabled="disabled" type="submit" name="AddFunction" value="1">{'Grant full access'|i18n( 'design/admin/role/createpolicystep2' )}</button>
            <button class="exp-btn button-function exp-js-only" hidden disabled="disabled" type="submit" name="Limitation" value="1">{'Grant limited access'|i18n( 'design/admin/role/createpolicystep2' )}</button>
        </span>
    </div>
</div>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn" type="submit" name="CancelPolicyButton" value="1">{'Cancel'|i18n( 'design/admin/role/createpolicystep1' )}</button>
    </div>
    <p class="exp-meta">{'Cancel goes back to the role editor; nothing has been added.'|i18n( 'design/admin/role/createpolicystep1' )}</p>
</div>

</div></div></div>
</div>
</form>

<script type="text/javascript">
var expPolicyModules = {ldelim}{rdelim}, expPolicyEveryFunction = '{'Every function'|i18n( 'design/admin/role/createpolicystep1' )|wash( javascript )}';
{foreach $module_list as $listed_module}{if $listed_module}
expPolicyModules['{$listed_module.name|wash( javascript )}'] = [{foreach $listed_module.available_functions as $fn => $lim}'{$fn|wash( javascript )}'{delimiter}, {/delimiter}{/foreach}];
{/if}{/foreach}
{literal}
(function () {
    var form = document.getElementById( 'createpolicyform' );
    var moduleSel = document.getElementById( 'ezrole-createpolizy-module' );
    var fnSel = document.getElementById( 'ezrole-createpolizy-function' );
    var warn = document.getElementById( 'ezrole-createpolizy-everything' );
    if ( !form || !moduleSel || !fnSel ) return;
    var nodes = form.querySelectorAll( '.exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    function setOptions( list, disable ) {
        while ( fnSel.firstChild ) fnSel.removeChild( fnSel.firstChild );
        list.forEach( function ( item ) { var o = document.createElement( 'option' ); o.value = item; o.textContent = item; fnSel.appendChild( o ); } );
        fnSel.disabled = !!disable;
        if ( disable ) { fnSel.innerHTML = ''; var o = document.createElement( 'option' ); o.value = '*'; o.textContent = expPolicyEveryFunction; fnSel.appendChild( o ); }
    }
    function update() {
        var list = expPolicyModules[ moduleSel.value ];
        var has = !!( list && list.length );
        setOptions( has ? list : [], !has );
        form.querySelectorAll( '.button-function' ).forEach( function ( b ) { b.disabled = !has; } );
        form.querySelector( '.button-module' ).disabled = has;
        if ( warn ) warn.hidden = moduleSel.value !== '*';
    }
    moduleSel.addEventListener( 'change', update );
    update();
})();
{/literal}
</script>
