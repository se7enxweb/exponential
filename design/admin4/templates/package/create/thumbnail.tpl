{* The thumbnail step of the package creation wizard (package/create): an optional image shown with the package.
   Continue without a file goes on without one. The POST name is that of before: PackageThumbnail (a file). The same
   file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form enctype="multipart/form-data" method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='An image that shows what the package looks like. It is optional: continue without a file to go on without one.'|i18n( 'design/admin/package' )
         field_ids=hash( 'PackageThumbnail', 'PackageThumbnail' )
         field_labels=hash( 'PackageThumbnail', 'Thumbnail'|i18n( 'design/admin/package' ) )}

<section class="a4-card" aria-labelledby="pkg-thumbnail-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-thumbnail-title">{'Thumbnail'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-form">
        <div class="a4-field">
            <label for="PackageThumbnail">{'Image file (optional)'|i18n( 'design/admin/package' )}</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
            <input class="a4-package-file" id="PackageThumbnail" name="PackageThumbnail" type="file" accept="image/*" aria-describedby="{if is_set( $errs['PackageThumbnail'] )}PackageThumbnail-error {/if}PackageThumbnail-help"{if is_set( $errs['PackageThumbnail'] )} aria-invalid="true"{/if} />
            {if is_set( $errs['PackageThumbnail'] )}<p class="a4-field-error" id="PackageThumbnail-error">{$errs['PackageThumbnail']|wash}</p>{/if}
            <span class="a4-help" id="PackageThumbnail-help">{'An image file, for example PNG or JPEG.'|i18n( 'design/admin/package' )}{if first_set( $persistent_data.thumbnail, false() )} {'Chosen: %file'|i18n( 'design/admin/package',, hash( '%file', $persistent_data.thumbnail.filename ) )|wash}{/if}</span>
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
