{* "Was this helpful?" under a Released help topic (doc/eng-us/features/6.0/help-content.md, "Was this helpful?"): yes,
   or no with a reason from a fixed list. A POST to exphelp/feedback (the kernel adds its form token); nothing about
   the reader is stored. After the answer the page says thank you instead.

   Variables: help_id, thanks (bool), reasons (expHelpContentFeedback::REASONS), prefix (ids).
   The same file is in design/admin and design/admin4. *}
<section class="a4-card a4-help-feedback" aria-labelledby="{$prefix}-fb-title">
    <h2 class="a4-help-section-head" id="{$prefix}-fb-title"><span class="a4-icon a4-bi-chat-square-text" aria-hidden="true"></span> {'Was this helpful?'|i18n( 'kernel/exphelp' )}</h2>
    {if $thanks}
    <p class="a4-notice is-ok" role="status">{'Thank you. Your answer helps us improve the help.'|i18n( 'kernel/exphelp' )}</p>
    {else}
    <form class="a4-help-feedback-form" method="post" action={concat( 'exphelp/feedback/', $help_id )|ezurl}>
        <div class="a4-actions">
            <button type="submit" class="a4-btn" name="Helpful" value="yes"><span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span> {'Yes'|i18n( 'kernel/exphelp' )}</button>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="{$prefix}-fb-reason">{'If not, what was wrong?'|i18n( 'kernel/exphelp' )}</label>
            <select class="a4-input" id="{$prefix}-fb-reason" name="Reason">
                <option value="">{'No reason given'|i18n( 'kernel/exphelp' )}</option>
                {foreach $reasons as $fb_r}
                <option value="{$fb_r|wash}">{include uri='design:exphelp/parts/feedback_reason.tpl' reason=$fb_r}</option>
                {/foreach}
            </select>
        </div>
        <div class="a4-actions">
            <button type="submit" class="a4-btn" name="Helpful" value="no"><span class="a4-icon a4-bi-x-lg" aria-hidden="true"></span> {'No'|i18n( 'kernel/exphelp' )}</button>
        </div>
        <p class="a4-meta">{'Only your answer is kept, not who you are.'|i18n( 'kernel/exphelp' )}</p>
    </form>
    {/if}
</section>
