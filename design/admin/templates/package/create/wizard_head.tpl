{* The head of a step of the package creation wizard (package/create): the fields every step posts and the page's title.

   Included first inside the step's <form>, before its box-bc frame. The hidden fields are the ones the view has
   always read (CreatorItemID, CreatorStepID, PackageStep). The invisible NextStepButton comes before every other
   button of the form, so Enter in a field goes on to the next step, never Back or a step's own buttons (Add node,
   Remove selected). The same file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
<input type="hidden" name="CreatorItemID" value="{$creator.id|wash}" />
<input type="hidden" name="CreatorStepID" value="{$current_step.id|wash}" />
<input type="hidden" name="PackageStep" value="1" />
<input type="submit" name="NextStepButton" value="" tabindex="-1" aria-hidden="true" class="a4-sr-only" />

<div class="box-header"><div class="box-ml">
<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'package/list'|ezurl}>{'Packages'|i18n( 'design/admin/package' )}</a> / {'Create package'|i18n( 'design/admin/package' )}</p>
        <h1 class="context-title a4-pagehead-title">{'Package wizard: %wizardname'|i18n( 'design/admin/package',, hash( '%wizardname', $creator.name ) )|wash}</h1>
    </div>
</div>
</div></div>
