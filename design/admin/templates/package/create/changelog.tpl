{* The changelog step of the package creation wizard (package/create), the last step of every creator: who made the
   change and what changed, then the review of the whole package (wizard_review.tpl, from wizard_actions.tpl) and
   Create package. The POST names are those of before: PackageChangelogPerson, PackageChangelogEmail,
   PackageChangelogText. The same file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $k_name = 'Name'|i18n( 'kernel/package' )
     $k_email = 'Email'|i18n( 'kernel/package' )
     $k_text = 'Changelog'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='The first entry in the package changelog. Then check the review below and create the package.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_name, 'PackageChangelogPerson', $k_email, 'PackageChangelogEmail', $k_text, 'PackageChangelogText' )}

<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>{'Required'|i18n( 'design/admin/package' )}</p>

<section class="a4-card" aria-labelledby="pkg-changelog-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-changelog-title">{'Changelog entry'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-form is-wide a4-package-form">
        <div class="a4-field">
            <label for="PackageChangelogPerson">{'Name'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageChangelogPerson" type="text" name="PackageChangelogPerson" value="{$persistent_data.changelog_person|wash}" aria-required="true" autocomplete="name"{if is_set( $errs[$k_name] )} aria-invalid="true" aria-describedby="PackageChangelogPerson-error"{/if} />
            {if is_set( $errs[$k_name] )}<p class="a4-field-error" id="PackageChangelogPerson-error">{$errs[$k_name]|wash}</p>{/if}
        </div>
        <div class="a4-field">
            <label for="PackageChangelogEmail">{'Email'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageChangelogEmail" type="text" inputmode="email" name="PackageChangelogEmail" value="{$persistent_data.changelog_email|wash}" aria-required="true" autocomplete="email" spellcheck="false"{if is_set( $errs[$k_email] )} aria-invalid="true" aria-describedby="PackageChangelogEmail-error"{/if} />
            {if is_set( $errs[$k_email] )}<p class="a4-field-error" id="PackageChangelogEmail-error">{$errs[$k_email]|wash}</p>{/if}
        </div>
        <div class="a4-field a4-field-wide">
            <label for="PackageChangelogText">{'Changes'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <textarea id="PackageChangelogText" rows="8" name="PackageChangelogText" aria-required="true" aria-describedby="{if is_set( $errs[$k_text] )}PackageChangelogText-error {/if}PackageChangelogText-help"{if is_set( $errs[$k_text] )} aria-invalid="true"{/if}>{$persistent_data.changelog_text|wash}</textarea>
            {if is_set( $errs[$k_text] )}<p class="a4-field-error" id="PackageChangelogText-error">{$errs[$k_text]|wash}</p>{/if}
            <span class="a4-help" id="PackageChangelogText-help">{'Start an entry with a marker ( %emstart-%emend (dash) or %emstart*%emend (asterisk) ) at the beginning of the line. The change will continue to the next change marker.'|i18n( 'design/admin/package',, hash( '%emstart', '<em>', '%emend', '</em>' ) )}</span>
        </div>
    </div>
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $k_name $k_email $k_text $errs}
