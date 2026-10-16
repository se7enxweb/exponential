{* The image step of the site style wizard (package/create, creator ezstyle): the images the stylesheets use. Each
   image is added on its own: Add image (or Continue with a file chosen) adds it and stays on the step; Continue
   without a file goes on. The POST name is that of before: PackageImageFile (a file); Add image posts
   NextStepButton like Continue, read by eZStylePackageCreator. The same file is in design/admin and design/admin4.
   Guide: doc/features/6.0/package-wizard.md *}
{def $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form enctype="multipart/form-data" method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Add the images the stylesheets use, one at a time. Continue without a file when all are added.'|i18n( 'design/admin/package' )
         field_ids=hash( 'PackageImageFile', 'PackageImageFile' )
         field_labels=hash( 'PackageImageFile', 'Image file'|i18n( 'design/admin/package' ) )}

<section class="a4-card" aria-labelledby="pkg-images-title">
    <div class="a4-card-head">
        <h3 class="a4-h3" id="pkg-images-title">{'Image files'|i18n( 'design/admin/package' )}</h3>
        <p>{'%count added'|i18n( 'design/admin/package',, hash( '%count', first_set( $persistent_data.imagefiles, array() )|count ) )|wash}</p>
    </div>
    {if first_set( $persistent_data.imagefiles, false() )}
    <ul class="a4-package-review-list" aria-label="{'Currently added image files'|i18n( 'design/admin/package' )|wash}">
        {foreach $persistent_data.imagefiles as $imagefile}
        <li><code>{$imagefile.filename|wash}</code></li>
        {/foreach}
    </ul>
    {else}
    <p class="a4-empty">{'No image added yet.'|i18n( 'design/admin/package' )}</p>
    {/if}
    <div class="a4-form a4-package-add-file">
        <div class="a4-field">
            <label for="PackageImageFile">{'Image file'|i18n( 'design/admin/package' )}</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
            <input class="a4-package-file" id="PackageImageFile" name="PackageImageFile" type="file" accept="image/*" aria-describedby="{if is_set( $errs['PackageImageFile'] )}PackageImageFile-error {/if}PackageImageFile-help"{if is_set( $errs['PackageImageFile'] )} aria-invalid="true"{/if} />
            {if is_set( $errs['PackageImageFile'] )}<p class="a4-field-error" id="PackageImageFile-error">{$errs['PackageImageFile']|wash}</p>{/if}
            <span class="a4-help" id="PackageImageFile-help">{'An image file, for example PNG or JPEG.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-actions">
            <button class="a4-btn" type="submit" name="NextStepButton" value="1"><span class="a4-icon a4-bi-upload is-small" aria-hidden="true"></span> {'Add image'|i18n( 'design/admin/package' )}</button>
        </div>
    </div>
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $errs}
