{* Change password (admin4 and admin4l): one card with the current password, the new one with its requirements and
   strength, and the confirmation; errors inline at each field and in a summary at the top; a success state.
   Works without JavaScript; design:javascript/exp_password_field.js adds show / hide, the live checklist, the
   meter, the match feedback and "Generate". Posts what the view has always read: oldPassword, newPassword,
   confirmPassword, OKButton, CancelButton (to the same address).
   Variables: see the user/password template contract (password_rules, field_errors, field_ids, error_summary,
   password_changed, password_js_config_json ...); every one is optional, so an older kernel still renders. *}
{def $ids = first_set( $field_ids, hash( 'oldPassword', 'password-old', 'newPassword', 'password-new', 'confirmPassword', 'password-confirm' ) )
     $errors = first_set( $field_errors, hash( 'oldPassword', array(), 'newPassword', array(), 'confirmPassword', array() ) )
     $rules = first_set( $password_rules, array() )
     $summary = first_set( $error_summary, array() )
     $changed = first_set( $password_changed, false() )
     $ctx = 'design/admin/user/password_page'
     $eye = '<svg class="a4pw-eye" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></svg><svg class="a4pw-eye-off" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7a10 10 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'}
{* an older kernel: only the three flags *}
{if and( $summary|count|eq( 0 ), $message, or( $oldPasswordNotValid, $newPasswordNotMatch, $newPasswordTooShort ) )}
    {if $oldPasswordNotValid}{set $errors = $errors|merge( hash( 'oldPassword', array( 'Your current password is not correct.'|i18n( $ctx ) ) ) )}
    {elseif $newPasswordTooShort}{set $errors = $errors|merge( hash( 'newPassword', array( 'The new password must be at least %1 characters long.'|i18n( $ctx, '', array( first_set( $min_length, ezini( 'UserSettings', 'MinPasswordLength' ) ) ) ) ) ) )}
    {elseif $newPasswordNotMatch}{set $errors = $errors|merge( hash( 'confirmPassword', array( 'The two new passwords do not match.'|i18n( $ctx ) ) ) )}{/if}
    {foreach array( 'oldPassword', 'newPassword', 'confirmPassword' ) as $f}{foreach $errors[$f] as $t}{set $summary = $summary|append( hash( 'field', $f, 'field_id', $ids[$f], 'text', $t ) )}{/foreach}{/foreach}
{/if}
{if and( $message, $changed|not, $summary|count|eq( 0 ), $oldPasswordNotValid|not, $newPasswordNotMatch|not, $newPasswordTooShort|not, is_unset( $password_changed ) )}{set $changed = true()}{/if}

<div class="a4pw">
    <div class="a4pw-head">
        <h1>{'Change password'|i18n( $ctx )}</h1>
        <p>{'Signed in as %login.'|i18n( $ctx, '', hash( '%login', $userAccount.login|wash ) )}</p>
    </div>

{if $changed}
    <div class="a4pw-done" role="status" tabindex="-1" autofocus>
        <svg class="a4pw-done-icon" viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="currentColor"/><path d="M7 12.5l3.2 3.2L17 9" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div>
            <h2>{'Your password was changed.'|i18n( $ctx )}</h2>
            <p>{'You stay signed in here.'|i18n( $ctx )}{if and( is_set( $sessions_ended ), $sessions_ended|gt( 0 ) )} {'Your other sessions (%count) were signed out.'|i18n( $ctx, '', hash( '%count', $sessions_ended ) )}{elseif first_set( $other_sessions_signed_out, false() )} {'You were signed out on your other devices.'|i18n( $ctx )}{/if}</p>
{if first_set( $notification_sent, false() )}
            <p>{'A confirmation was sent to your e-mail address.'|i18n( $ctx )}</p>
{/if}
            <p class="a4pw-done-actions"><a class="a4pw-btn primary" href={first_set( $redirect_uri, '/' )|ezurl}>{'Continue'|i18n( $ctx )}</a></p>
        </div>
    </div>
{else}

{if $summary|count}
    <div class="a4pw-summary" id="password-error-summary" role="alert" tabindex="-1" aria-labelledby="password-error-summary-title" data-exp-password-summary autofocus>
        <h2 id="password-error-summary-title">{'The password could not be changed.'|i18n( $ctx )}</h2>
        <ul>
{foreach $summary as $e}
            <li>{if $e.field_id}<a href="#{$e.field_id|wash}">{$e.text|wash}</a>{else}{$e.text|wash}{/if}</li>
{/foreach}
        </ul>
    </div>
{/if}

<form name="Password" class="a4pw-card" method="post" action={concat( $module.functions.password.uri, '/', $userID )|ezurl} novalidate
      data-exp-password-form{if is_set( $password_js_config_json )} data-exp-password-config="{$password_js_config_json|wash}"{/if}>

    {* for password managers: the account the password belongs to *}
    <input type="text" class="a4pw-username" name="a4pwUsername" value="{$userAccount.login|wash}" autocomplete="username" readonly tabindex="-1" aria-hidden="true" />

    {* Current password *}
    <div class="a4pw-field{if $errors.oldPassword|count} invalid{/if}">
        <label class="a4pw-label" for="{$ids.oldPassword}">{'Current password'|i18n( $ctx )}</label>
        <div class="a4pw-input">
            <input id="{$ids.oldPassword}" type="password" name="oldPassword" value="" autocomplete="current-password" spellcheck="false" autocapitalize="off"
                   data-exp-password="current"{if $errors.oldPassword|count} aria-invalid="true" aria-describedby="{$ids.oldPassword}-error"{/if}{if $summary|count|eq( 0 )} autofocus{/if} />
            <button type="button" class="a4pw-toggle" data-exp-password-toggle="{$ids.oldPassword}" aria-controls="{$ids.oldPassword}" aria-pressed="false" hidden>{$eye}<span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
        </div>
{if $errors.oldPassword|count}
        <p class="a4pw-error" id="{$ids.oldPassword}-error">{foreach $errors.oldPassword as $t}<span>{$t|wash}</span>{/foreach}</p>
{/if}
    </div>

    {* New password *}
    <div class="a4pw-field{if $errors.newPassword|count} invalid{/if}">
        <div class="a4pw-label-row">
            <label class="a4pw-label" for="{$ids.newPassword}">{'New password'|i18n( $ctx )}</label>
            <button type="button" class="a4pw-link" data-exp-password-generate hidden>{'Generate a strong password'|i18n( $ctx )}</button>
        </div>
        <div class="a4pw-input">
            <input id="{$ids.newPassword}" type="password" name="newPassword" value="" autocomplete="new-password" spellcheck="false" autocapitalize="off"
                   minlength="{first_set( $min_length, ezini( 'UserSettings', 'MinPasswordLength' ) )}" data-exp-password="new"
                   aria-describedby="{if $errors.newPassword|count}{$ids.newPassword}-error {/if}password-new-rules"{if $errors.newPassword|count} aria-invalid="true"{/if} />
            <button type="button" class="a4pw-toggle" data-exp-password-toggle="{$ids.newPassword}" aria-controls="{$ids.newPassword}" aria-pressed="false" hidden>{$eye}<span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
        </div>
{if $errors.newPassword|count}
        <p class="a4pw-error" id="{$ids.newPassword}-error">{foreach $errors.newPassword as $t}<span>{$t|wash}</span>{/foreach}</p>
{/if}
        <div class="a4pw-meter" data-exp-password-meter aria-live="polite" hidden>
            <span class="a4pw-meter-bar" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></span>
            <span class="a4pw-meter-label">{'Strength'|i18n( $ctx )}: <b data-exp-password-meter-text></b></span>
        </div>
        <p class="a4pw-rules-title" id="password-new-rules-title">{'Your new password needs'|i18n( $ctx )}</p>
        <ul class="a4pw-rules" id="password-new-rules" aria-labelledby="password-new-rules-title" data-exp-password-rules>
{if $rules|count}
{foreach $rules as $r}
            <li data-exp-password-rule="{$r.id|wash}"{if $r.failed} data-state="unmet" data-failed="true"{/if}><span class="a4pw-rule-icon" aria-hidden="true"></span><span>{$r.text|wash}</span><span class="a4pw-sr" data-exp-password-rule-state>{if $r.failed}{'not met'|i18n( $ctx )}{/if}</span></li>
{/foreach}
{else}
            <li data-exp-password-rule="length"><span class="a4pw-rule-icon" aria-hidden="true"></span><span>{'At least %1 characters'|i18n( $ctx, '', array( ezini( 'UserSettings', 'MinPasswordLength' ) ) )}</span><span class="a4pw-sr" data-exp-password-rule-state></span></li>
{/if}
        </ul>
    </div>

    {* Confirm new password *}
    <div class="a4pw-field{if $errors.confirmPassword|count} invalid{/if}">
        <label class="a4pw-label" for="{$ids.confirmPassword}">{'Confirm new password'|i18n( $ctx )}</label>
        <div class="a4pw-input">
            <input id="{$ids.confirmPassword}" type="password" name="confirmPassword" value="" autocomplete="new-password" spellcheck="false" autocapitalize="off"
                   data-exp-password="confirm"{if $errors.confirmPassword|count} aria-invalid="true" aria-describedby="{$ids.confirmPassword}-error"{/if} />
            <button type="button" class="a4pw-toggle" data-exp-password-toggle="{$ids.confirmPassword}" aria-controls="{$ids.confirmPassword}" aria-pressed="false" hidden>{$eye}<span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
        </div>
{if $errors.confirmPassword|count}
        <p class="a4pw-error" id="{$ids.confirmPassword}-error">{foreach $errors.confirmPassword as $t}<span>{$t|wash}</span>{/foreach}</p>
{/if}
        <p class="a4pw-match" data-exp-password-match aria-live="polite" hidden></p>
    </div>

    <p class="a4pw-status" data-exp-password-status aria-live="polite" hidden></p>

    <div class="a4pw-actions">
        <button class="a4pw-btn primary" type="submit" name="OKButton" value="OK">{'Change password'|i18n( $ctx )}</button>
        <button class="a4pw-btn" type="submit" name="CancelButton" value="Cancel" formnovalidate>{'Cancel'|i18n( $ctx )}</button>
    </div>
</form>
{ezscript( array( 'exp_password_field.js' ) )}
{/if}
</div>
{undef}
