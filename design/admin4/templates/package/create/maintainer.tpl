{* The maintainer step of the package creation wizard (package/create): who maintains the package and in which role.
   A creator leaves the step out when the user may use no maintainer role. The POST names are those of before:
   PackageMaintainerPerson, PackageMaintainerEmail, PackageMaintainerRole. The same file is in design/admin and
   design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $maintainer_role_list = fetch( package, maintainer_role_list, hash( check_roles, true() ) )
     $k_name = 'Name'|i18n( 'kernel/package' )
     $k_email = 'Email'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Who looks after the package. It is filled in with your own name and email address.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_name, 'PackageMaintainerPerson', $k_email, 'PackageMaintainerEmail' )}

<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>{'Required'|i18n( 'design/admin/package' )}</p>

<section class="a4-card" aria-labelledby="pkg-maintainer-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-maintainer-title">{'Maintainer'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-form is-wide a4-package-form">
        <div class="a4-field">
            <label for="PackageMaintainerPerson">{'Name'|i18n( 'design/admin/package', 'Maintainer name' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageMaintainerPerson" type="text" name="PackageMaintainerPerson" value="{$persistent_data.maintainer_person|wash}" aria-required="true" autocomplete="name"{if is_set( $errs[$k_name] )} aria-invalid="true" aria-describedby="PackageMaintainerPerson-error"{/if} />
            {if is_set( $errs[$k_name] )}<p class="a4-field-error" id="PackageMaintainerPerson-error">{$errs[$k_name]|wash}</p>{/if}
        </div>
        <div class="a4-field">
            <label for="PackageMaintainerEmail">{'Email'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageMaintainerEmail" type="text" inputmode="email" name="PackageMaintainerEmail" value="{$persistent_data.maintainer_email|wash}" aria-required="true" autocomplete="email" spellcheck="false"{if is_set( $errs[$k_email] )} aria-invalid="true" aria-describedby="PackageMaintainerEmail-error"{/if} />
            {if is_set( $errs[$k_email] )}<p class="a4-field-error" id="PackageMaintainerEmail-error">{$errs[$k_email]|wash}</p>{/if}
        </div>
        <div class="a4-field">
            <label for="PackageMaintainerRole">{'Role'|i18n( 'design/admin/package', 'Maintainer role' )}</label>
            <select id="PackageMaintainerRole" name="PackageMaintainerRole" aria-describedby="PackageMaintainerRole-help">
            {foreach $maintainer_role_list as $role}
                <option value="{$role.id|wash}"{if $role.id|eq( first_set( $persistent_data.maintainer_role, '' ) )} selected="selected"{/if}>{$role.name|wash}</option>
            {/foreach}
            </select>
            <span class="a4-help" id="PackageMaintainerRole-help">{'Only the roles you may give are listed.'|i18n( 'design/admin/package' )}</span>
        </div>
    </div>
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $maintainer_role_list $k_name $k_email $errs}
