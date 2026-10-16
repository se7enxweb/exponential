{* The CSS step of the site style wizard (package/create, creator ezstyle): the two stylesheets of the style, both
   required, each a .css file. The POST names are those of before: PackageSiteCSSFile, PackageClassesCSSFile (files),
   read by eZStylePackageCreator. The same file is in design/admin and design/admin4.
   Guide: doc/features/6.0/package-wizard.md *}
{def $k_css = 'CSS file'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form enctype="multipart/form-data" method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='A site style is two stylesheets: one for the site and one for the content classes. Both are required.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_css, 'PackageSiteCSSFile' )}

<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>{'Required'|i18n( 'design/admin/package' )}</p>

<section class="a4-card" aria-labelledby="pkg-css-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-css-title">{'Stylesheets'|i18n( 'design/admin/package' )}</h3></div>
    {if is_set( $errs[$k_css] )}<p class="a4-field-error" id="pkg-css-error">{$errs[$k_css]|wash}</p>{/if}
    <input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
    <div class="a4-form is-wide a4-package-form">
        <div class="a4-field">
            <label for="PackageSiteCSSFile">{'Site CSS file'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-package-file" id="PackageSiteCSSFile" name="PackageSiteCSSFile" type="file" accept=".css,text/css" aria-required="true" aria-describedby="{if is_set( $errs[$k_css] )}pkg-css-error {/if}package-site-css-help"{if is_set( $errs[$k_css] )} aria-invalid="true"{/if} />
            <span class="a4-help" id="package-site-css-help">{'Please select the site CSS file to be included in the package.'|i18n( 'design/admin/package' )}{if first_set( $persistent_data.sitecssfile, false() )} {'Chosen: %file'|i18n( 'design/admin/package',, hash( '%file', $persistent_data.sitecssfile.filename ) )|wash}{/if}</span>
        </div>
        <div class="a4-field">
            <label for="PackageClassesCSSFile">{'Classes CSS file'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-package-file" id="PackageClassesCSSFile" name="PackageClassesCSSFile" type="file" accept=".css,text/css" aria-required="true" aria-describedby="{if is_set( $errs[$k_css] )}pkg-css-error {/if}package-classes-css-help"{if is_set( $errs[$k_css] )} aria-invalid="true"{/if} />
            <span class="a4-help" id="package-classes-css-help">{'Please select the classes CSS file to be included in the package.'|i18n( 'design/admin/package' )}{if first_set( $persistent_data.classescssfile, false() )} {'Chosen: %file'|i18n( 'design/admin/package',, hash( '%file', $persistent_data.classescssfile.filename ) )|wash}{/if}</span>
        </div>
    </div>
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $k_css $errs}
