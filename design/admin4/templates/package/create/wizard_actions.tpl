{* The actions of a step of the package creation wizard: Back, the one primary action and Cancel.

   On the last step the review of everything the wizard collected comes first (wizard_review.tpl) and the primary
   action is Create package; on every other step it is Continue. Both post NextStepButton, Back posts
   PreviousStepButton (from the first step it returns to the choice of wizard), as before. Cancel is a link to the
   package list and writes nothing. The same file is in design/admin and design/admin4.
   Guide: doc/features/6.0/package-wizard.md *}
{if $current_step.next_step|not}
{include uri="design:package/create/wizard_review.tpl"}
{/if}

<div class="a4-toolbar is-bottom a4-package-wizard-actions">
    <div class="a4-actions">
        <button class="a4-btn" type="submit" name="PreviousStepButton" value="1">{'%arrowleft Back'|i18n( 'design/admin/package',, hash( '%arrowleft', '&laquo;' ) )}</button>
        {if $current_step.next_step}
        <button class="a4-btn a4-btn-primary" type="submit" name="NextStepButton" value="1">{'Continue'|i18n( 'design/admin/package' )}</button>
        {else}
        <button class="a4-btn a4-btn-primary" type="submit" name="NextStepButton" value="1"><span class="a4-icon a4-bi-box-seam is-small" aria-hidden="true"></span> {'Create package'|i18n( 'design/admin/package' )}</button>
        {/if}
    </div>
    <a class="a4-btn a4-btn-ghost a4-package-wizard-cancel" href={'package/list'|ezurl}>{'Cancel'|i18n( 'design/admin/package' )}</a>
</div>
