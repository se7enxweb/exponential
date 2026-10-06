{* Change password (user/password), the fallback of every site design.

   One card: the current password, the new one with its requirements, the new one again, "Change password" and
   "Cancel". Field names, the form token and the button names are those the view reads (oldPassword, newPassword,
   confirmPassword, OKButton, CancelButton), so every design and extension posting here keeps working.

   The server is authoritative: it renders the requirement list (with the rules that failed marked), every error
   next to its field (aria-describedby) and in a summary at the top of the form with links to the fields, and the
   first field with an error gets the focus. javascript/exp_password_field.js adds, when it runs, the live
   checklist, the strength meter, show/hide, "passwords match" and "Generate a strong password"; every control it
   needs is in the markup with the hidden attribute, so without JavaScript none of them is shown.

   The typed passwords are never written back into the page. Variables and hooks: doc/features/6.0/
   modern-password-change.md. Drawn by stylesheets/exp_password_page.css, linked here so it reaches the page
   whatever the pagelayout loads. *}
{def $pw_t = $password_js_config.i18n
     $pw_first_error = cond( $field_errors.oldPassword|count, 'password-old',
                             $field_errors.newPassword|count, 'password-new',
                             $field_errors.confirmPassword|count, 'password-confirm', '' )}
<link rel="stylesheet" type="text/css" href={'stylesheets/exp_password_page.css'|ezdesign} />
<div class="exp-pw">
<div class="maincontentheader">
<h1>{'Change your password'|i18n( 'design/standard/user/password' )}</h1>
</div>

{if $password_changed}
<div class="exp-pw-notice exp-pw-success" role="status">
    <h2>{'Your password has been changed'|i18n( 'design/standard/user/password' )}</h2>
    <p>{'You are still signed in here. Use the new password the next time you sign in.'|i18n( 'design/standard/user/password' )}</p>
    {if or( and( is_set( $other_sessions_signed_out ), $other_sessions_signed_out ), and( is_set( $sessions_ended ), $sessions_ended|gt( 0 ) ) )}<p>{'You were signed out on your other devices.'|i18n( 'design/standard/user/password' )}</p>{/if}
    {if $notification_sent}<p>{'We sent a confirmation to your e-mail address.'|i18n( 'design/standard/user/password' )}</p>{/if}
    <p class="exp-pw-actions"><a class="defaultbutton exp-pw-button exp-pw-primary" href={$redirect_uri|ezurl}>{'Continue'|i18n( 'design/standard/user/password' )}</a></p>
</div>
{else}

<form class="exp-pw-card" action={concat( $module.functions.password.uri, '/', $userID )|ezurl} method="post" name="Password" novalidate
      data-exp-password-form data-exp-password-config="{$password_js_config_json|wash}">
{if $has_errors}
    <div class="exp-pw-notice exp-pw-summary" id="password-error-summary" role="alert" tabindex="-1" aria-labelledby="password-error-summary-title" data-exp-password-summary>
        <h2 id="password-error-summary-title">{'Your password was not changed'|i18n( 'design/standard/user/password' )}</h2>
        <ul>
        {foreach $error_summary as $pw_error}
            <li>{if $pw_error.field_id}<a href="#{$pw_error.field_id}">{$pw_error.text|wash}</a>{else}{$pw_error.text|wash}{/if}</li>
        {/foreach}
        </ul>
    </div>
{/if}
    <p class="exp-pw-who">{'Signed in as %1.'|i18n( 'design/standard/user/password', '', array( $userAccount.login|wash ) )}</p>

    {* current password *}
    <div class="exp-pw-field{if $field_errors.oldPassword|count} exp-pw-has-error{/if}">
        <label for="password-old">{'Current password'|i18n( 'design/standard/user/password' )}</label>
        {if $field_errors.oldPassword|count}
        <p class="exp-pw-error" id="password-old-error">{foreach $field_errors.oldPassword as $pw_text}<span>{$pw_text|wash}</span>{/foreach}</p>
        {/if}
        <div class="exp-pw-row">
            <input id="password-old" class="halfbox" type="password" name="oldPassword" autocomplete="current-password" required data-exp-password="current"{if $field_errors.oldPassword|count} aria-invalid="true" aria-describedby="password-old-error"{/if}{if eq( $pw_first_error, 'password-old' )} autofocus{/if} />
            <button type="button" class="exp-pw-toggle" aria-controls="password-old" aria-pressed="false" data-exp-password-toggle="password-old" hidden><span data-exp-password-toggle-text>{$pw_t.show|wash}</span><span class="exp-pw-vh"> {'current password'|i18n( 'design/standard/user/password' )}</span></button>
        </div>
    </div>

    {* new password, its requirements and the strength meter *}
    <div class="exp-pw-field{if $field_errors.newPassword|count} exp-pw-has-error{/if}">
        <label for="password-new">{'New password'|i18n( 'design/standard/user/password' )}</label>
        {if $field_errors.newPassword|count}
        <p class="exp-pw-error" id="password-new-error">{foreach $field_errors.newPassword as $pw_text}<span>{$pw_text|wash}</span>{/foreach}</p>
        {/if}
        <div class="exp-pw-row">
            <input id="password-new" class="halfbox" type="password" name="newPassword" autocomplete="new-password" required minlength="{$min_length}" data-exp-password="new" aria-describedby="{if $field_errors.newPassword|count}password-new-error {/if}password-new-rules"{if $field_errors.newPassword|count} aria-invalid="true"{/if}{if eq( $pw_first_error, 'password-new' )} autofocus{/if} />
            <button type="button" class="exp-pw-toggle" aria-controls="password-new" aria-pressed="false" data-exp-password-toggle="password-new" hidden><span data-exp-password-toggle-text>{$pw_t.show|wash}</span><span class="exp-pw-vh"> {'new password'|i18n( 'design/standard/user/password' )}</span></button>
        </div>
        <p class="exp-pw-rules-title" id="password-new-rules-title">{'Your new password needs:'|i18n( 'design/standard/user/password' )}</p>
        <ul class="exp-pw-rules" id="password-new-rules" aria-labelledby="password-new-rules-title" data-exp-password-rules>
        {foreach $password_rules as $pw_rule}
            <li data-exp-password-rule="{$pw_rule.id|wash}"{if $pw_rule.failed} data-state="unmet" data-failed="true"{/if}>{$pw_rule.text|wash}<span class="exp-pw-vh">, </span><span class="exp-pw-vh" data-exp-password-rule-state>{if $pw_rule.failed}{$pw_t.ruleUnmet|wash}{/if}</span></li>
        {/foreach}
        </ul>
        <div class="exp-pw-meter" data-exp-password-meter hidden>
            <span class="exp-pw-meter-caption">{$pw_t.strength|wash}:</span>
            <span class="exp-pw-meter-bar" aria-hidden="true"><span></span></span>
            <span class="exp-pw-meter-label" data-exp-password-meter-text aria-live="polite"></span>
        </div>
        <p class="exp-pw-generate-row"><button type="button" class="button exp-pw-button" data-exp-password-generate hidden>{'Generate a strong password'|i18n( 'design/standard/user/password' )}</button></p>
        <p class="exp-pw-status" data-exp-password-status role="status" hidden></p>
    </div>

    {* new password again *}
    <div class="exp-pw-field{if $field_errors.confirmPassword|count} exp-pw-has-error{/if}">
        <label for="password-confirm">{'Confirm new password'|i18n( 'design/standard/user/password' )}</label>
        {if $field_errors.confirmPassword|count}
        <p class="exp-pw-error" id="password-confirm-error">{foreach $field_errors.confirmPassword as $pw_text}<span>{$pw_text|wash}</span>{/foreach}</p>
        {/if}
        <div class="exp-pw-row">
            <input id="password-confirm" class="halfbox" type="password" name="confirmPassword" autocomplete="new-password" required data-exp-password="confirm"{if $field_errors.confirmPassword|count} aria-invalid="true" aria-describedby="password-confirm-error"{/if}{if eq( $pw_first_error, 'password-confirm' )} autofocus{/if} />
            <button type="button" class="exp-pw-toggle" aria-controls="password-confirm" aria-pressed="false" data-exp-password-toggle="password-confirm" hidden><span data-exp-password-toggle-text>{$pw_t.show|wash}</span><span class="exp-pw-vh"> {'new password again'|i18n( 'design/standard/user/password' )}</span></button>
        </div>
        <p class="exp-pw-match" data-exp-password-match aria-live="polite" hidden></p>
    </div>

    <div class="buttonblock exp-pw-actions">
        <input class="defaultbutton exp-pw-button exp-pw-primary" type="submit" name="OKButton" value="{'Change password'|i18n( 'design/standard/user/password' )}" />
        <input class="button exp-pw-button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/standard/user/password' )}" formnovalidate />
    </div>
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>
{ezscript( array( 'exp_password_field.js' ) )}
{/if}
</div>
{undef $pw_t $pw_first_error}
