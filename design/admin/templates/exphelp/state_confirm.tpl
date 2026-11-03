{* exphelp/state: the confirmation of a step back in the review workflow (doc/eng-us/features/6.0/help-content.md, "The
   review workflow"). Back to Review from Released hides the page from readers; back to Draft from Review sends it to its
   author again. The form posts the same HelpState with HelpStateConfirm, or HelpStateCancel to go back unchanged; the
   kernel adds its form token to the POST form. No script: the page itself is the confirmation.

   Variables (kernel/private/classes/views/exphelp/state.php): help_title, help_current, help_target (state
   identifiers), help_hides (the step hides a released page from readers), help_state_url, help_back_url.
   The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-help-frame" data-exphelp>
    <header class="a4-help-pagehead">
        <h1 class="a4-help-title" id="exphelp-confirm-title">{if $help_hides}{'Hide this page from readers?'|i18n( 'kernel/exphelp' )}{else}{'Send this page back to Draft?'|i18n( 'kernel/exphelp' )}{/if}</h1>
    </header>
    <section class="a4-card a4-help-workflow" aria-labelledby="exphelp-confirm-title">
        <p class="a4-notice is-warn">{if $help_hides}{'Readers no longer see %title until someone releases it again.'|i18n( 'kernel/exphelp',, hash( '%title', $help_title ) )|wash}{else}{'%title goes back to Draft for more work. Readers still do not see it, and it needs a new review before it is released.'|i18n( 'kernel/exphelp',, hash( '%title', $help_title ) )|wash}{/if}</p>
        <form class="a4-actions" method="post" action={$help_state_url|ezurl}>
            <input type="hidden" name="HelpState" value="{$help_target|wash}" />
            <button type="submit" class="a4-btn a4-btn-danger" name="HelpStateConfirm" value="1">{if eq( $help_target, 'review' )}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Review'|i18n( 'kernel/exphelp' )}{else}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Draft'|i18n( 'kernel/exphelp' )}{/if}</button>
            <button type="submit" class="a4-btn a4-btn-ghost" name="HelpStateCancel" value="1">{'Cancel'|i18n( 'kernel/exphelp' )}</button>
        </form>
    </section>
</div>
