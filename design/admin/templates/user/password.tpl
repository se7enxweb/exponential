{* Change password (admin; admin2 and admin3 fall back to it). The admin's context block with the current password,
   the new one with its requirements and the confirmation; errors inline at each field and in a summary at the top;
   a success state. Works without JavaScript; design:javascript/exp_password_field.js adds show / hide, the live
   checklist, the strength meter, the match feedback and "Generate". Posts what the view has always read:
   oldPassword, newPassword, confirmPassword, OKButton, CancelButton.
   Variables: see the user/password template contract; every new one is optional, so an older kernel still renders. *}
{def $ids = first_set( $field_ids, hash( 'oldPassword', 'password-old', 'newPassword', 'password-new', 'confirmPassword', 'password-confirm' ) )
     $errors = first_set( $field_errors, hash( 'oldPassword', array(), 'newPassword', array(), 'confirmPassword', array() ) )
     $rules = first_set( $password_rules, array() )
     $summary = first_set( $error_summary, array() )
     $changed = first_set( $password_changed, false() )
     $ctx = 'design/admin/user/password_page'}
{* an older kernel: only the three flags *}
{if and( $summary|count|eq( 0 ), $message, or( $oldPasswordNotValid, $newPasswordNotMatch, $newPasswordTooShort ) )}
    {if $oldPasswordNotValid}{set $errors = $errors|merge( hash( 'oldPassword', array( 'Your current password is not correct.'|i18n( $ctx ) ) ) )}
    {elseif $newPasswordTooShort}{set $errors = $errors|merge( hash( 'newPassword', array( 'The new password must be at least %1 characters long.'|i18n( $ctx, '', array( first_set( $min_length, ezini( 'UserSettings', 'MinPasswordLength' ) ) ) ) ) ) )}
    {elseif $newPasswordNotMatch}{set $errors = $errors|merge( hash( 'confirmPassword', array( 'The two new passwords do not match.'|i18n( $ctx ) ) ) )}{/if}
    {foreach array( 'oldPassword', 'newPassword', 'confirmPassword' ) as $f}{foreach $errors[$f] as $t}{set $summary = $summary|append( hash( 'field', $f, 'field_id', $ids[$f], 'text', $t ) )}{/foreach}{/foreach}
{/if}
{if and( $message, $changed|not, $summary|count|eq( 0 ), $oldPasswordNotValid|not, $newPasswordNotMatch|not, $newPasswordTooShort|not, is_unset( $password_changed ) )}{set $changed = true()}{/if}

<style type="text/css">
{literal}
.expw [hidden] { display: none !important; }
.expw .block label.expw-label { display: block; font-weight: bold; margin: 0 0 .25em; }
.expw-input { position: relative; display: flex; max-width: 28em; }
.expw-input input { flex: 1 1 auto; min-width: 0; width: 100%; box-sizing: border-box; padding: .35em .5em; font-size: 1.05em; border: 1px solid #767676; }
.exp-pw-js .expw-input input { padding-right: 5.5em; }
.expw-invalid .expw-input input { border: 2px solid #b00020; }
.expw-toggle { position: absolute; top: 2px; right: 2px; bottom: 2px; padding: 0 .6em; border: 1px solid transparent; background: #f1f1f1; color: #222; cursor: pointer; font-weight: bold; }
.expw-toggle[aria-pressed="true"] { background: #dcdcdc; border-color: #888; }
.expw-error { margin: .3em 0 0; color: #b00020; font-weight: bold; }
.expw-error span { display: block; }
.expw-rules-title { margin: .6em 0 .2em; color: #444; }
.expw-rules { margin: 0; padding: 0; list-style: none; }
.expw-rules li { margin: .15em 0; padding: 0 0 0 1.5em; position: relative; background: none; }
.expw-rules li::before { content: "\25CB"; position: absolute; left: .2em; color: #555; }
.expw-rules li[data-state="met"] { color: #1b5e20; }
.expw-rules li[data-state="met"]::before { content: "\2713"; color: #1b5e20; font-weight: bold; }
.expw-invalid .expw-rules li[data-state="unmet"] { color: #b00020; }
.expw-invalid .expw-rules li[data-state="unmet"]::before { content: "\2717"; color: #b00020; }
.expw-rules li[data-failed="true"]:not([data-state="met"]) { color: #b00020; font-weight: bold; }
.expw-rules li[data-failed="true"]:not([data-state="met"])::before { content: "\2717"; color: #b00020; }
.expw input.defaultbutton { background-color: #4f716d; color: #fff; }
.expw input.defaultbutton:hover, .expw input.defaultbutton:focus { background-color: #425c59; color: #fff; }
.expw-sr { position: absolute; width: 1px; height: 1px; margin: -1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
.expw-meter { margin: .4em 0 0; color: #444; }
.expw-meter[data-score=""] b { visibility: hidden; }
.expw .block label.expw-label, .expw-summary, .expw-rules li { overflow-wrap: anywhere; }
.expw-meter meter { width: 10em; height: .8em; vertical-align: middle; }
.expw-match { margin: .3em 0 0; font-weight: bold; color: #444; }
.expw-match[data-state="match"] { color: #1b5e20; }
.expw-link { padding: 0; border: 0; background: none; color: #0b57a4; text-decoration: underline; cursor: pointer; font: inherit; }
.expw-summary { margin: 0 0 1em; padding: .6em 1em; border: 2px solid #b00020; background: #fdecee; color: #6d0012; }
.expw-summary h2 { margin: 0 0 .3em; font-size: 1.1em; color: #6d0012; }
.expw-summary ul { margin: 0 0 0 1.3em; padding: 0; }
.expw-summary a { color: #6d0012; text-decoration: underline; font-weight: bold; }
.expw-username { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); border: 0; padding: 0; opacity: 0; }
{/literal}
</style>

<div class="expw">
{if $changed}
<div class="message-feedback" role="status" tabindex="-1" autofocus>
    <h2>{'Your password was changed.'|i18n( $ctx )}</h2>
    <p>{'You stay signed in here.'|i18n( $ctx )}{if and( is_set( $sessions_ended ), $sessions_ended|gt( 0 ) )} {'Your other sessions (%count) were signed out.'|i18n( $ctx, '', hash( '%count', $sessions_ended ) )}{elseif first_set( $other_sessions_signed_out, false() )} {'You were signed out on your other devices.'|i18n( $ctx )}{/if}</p>
{if first_set( $notification_sent, false() )}
    <p>{'A confirmation was sent to your e-mail address.'|i18n( $ctx )}</p>
{/if}
    <p><a href={first_set( $redirect_uri, '/' )|ezurl}>{'Continue'|i18n( $ctx )}</a></p>
</div>
{else}

{if $summary|count}
<div class="expw-summary" id="password-error-summary" role="alert" tabindex="-1" aria-labelledby="password-error-summary-title" data-exp-password-summary autofocus>
    <h2 id="password-error-summary-title">{'The password could not be changed.'|i18n( $ctx )}</h2>
    <ul>
{foreach $summary as $e}
        <li>{if $e.field_id}<a href="#{$e.field_id|wash}">{$e.text|wash}</a>{else}{$e.text|wash}{/if}</li>
{/foreach}
    </ul>
</div>
{/if}

<form name="Password" method="post" action={concat( $module.functions.password.uri, '/', $userID )|ezurl} novalidate
      data-exp-password-form{if is_set( $password_js_config_json )} data-exp-password-config="{$password_js_config_json|wash}"{/if}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Change password for <%username>'|i18n( 'design/admin/user/password',, hash( '%username', $userAccount.login ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

<input type="text" class="expw-username" name="expwUsername" value="{$userAccount.login|wash}" autocomplete="username" readonly tabindex="-1" aria-hidden="true" />

{* Username. *}
<div class="block">
<label>{'Username'|i18n( 'design/admin/user/password' )}:</label>
{$userAccount.login|wash}
</div>

{* Current password. *}
<div class="block{if $errors.oldPassword|count} expw-invalid{/if}">
<label class="expw-label" for="{$ids.oldPassword}">{'Current password'|i18n( $ctx )}</label>
<div class="expw-input">
<input id="{$ids.oldPassword}" type="password" name="oldPassword" value="" autocomplete="current-password" spellcheck="false" autocapitalize="off" data-exp-password="current"{if $errors.oldPassword|count} aria-invalid="true" aria-describedby="{$ids.oldPassword}-error"{/if}{if $summary|count|eq( 0 )} autofocus{/if} />
<button type="button" class="expw-toggle" data-exp-password-toggle="{$ids.oldPassword}" aria-controls="{$ids.oldPassword}" aria-pressed="false" hidden><span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
</div>
{if $errors.oldPassword|count}<p class="expw-error" id="{$ids.oldPassword}-error">{foreach $errors.oldPassword as $t}<span>{$t|wash}</span>{/foreach}</p>{/if}
</div>

{* New password. *}
<div class="block{if $errors.newPassword|count} expw-invalid{/if}">
<label class="expw-label" for="{$ids.newPassword}">{'New password'|i18n( $ctx )}</label>
<div class="expw-input">
<input id="{$ids.newPassword}" type="password" name="newPassword" value="" autocomplete="new-password" spellcheck="false" autocapitalize="off" minlength="{first_set( $min_length, ezini( 'UserSettings', 'MinPasswordLength' ) )}" data-exp-password="new" aria-describedby="{if $errors.newPassword|count}{$ids.newPassword}-error {/if}password-new-rules"{if $errors.newPassword|count} aria-invalid="true"{/if} />
<button type="button" class="expw-toggle" data-exp-password-toggle="{$ids.newPassword}" aria-controls="{$ids.newPassword}" aria-pressed="false" hidden><span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
</div>
{if $errors.newPassword|count}<p class="expw-error" id="{$ids.newPassword}-error">{foreach $errors.newPassword as $t}<span>{$t|wash}</span>{/foreach}</p>{/if}
<p class="expw-meter" data-exp-password-meter aria-live="polite" hidden>{'Strength'|i18n( $ctx )}: <meter min="0" max="5" low="2" high="4" optimum="5" value="0" aria-hidden="true"></meter> <b data-exp-password-meter-text></b></p>
<p class="expw-rules-title" id="password-new-rules-title">{'Your new password needs'|i18n( $ctx )}</p>
<ul class="expw-rules" id="password-new-rules" aria-labelledby="password-new-rules-title" data-exp-password-rules>
{if $rules|count}
{foreach $rules as $r}
<li data-exp-password-rule="{$r.id|wash}"{if $r.failed} data-state="unmet" data-failed="true"{/if}>{$r.text|wash}<span class="expw-sr" data-exp-password-rule-state>{if $r.failed}{'not met'|i18n( $ctx )}{/if}</span></li>
{/foreach}
{else}
<li data-exp-password-rule="length">{'At least %1 characters'|i18n( $ctx, '', array( ezini( 'UserSettings', 'MinPasswordLength' ) ) )}<span class="expw-sr" data-exp-password-rule-state></span></li>
{/if}
</ul>
<p><button type="button" class="expw-link" data-exp-password-generate hidden>{'Generate a strong password'|i18n( $ctx )}</button></p>
</div>

{* Confirm new password. *}
<div class="block{if $errors.confirmPassword|count} expw-invalid{/if}">
<label class="expw-label" for="{$ids.confirmPassword}">{'Confirm new password'|i18n( $ctx )}</label>
<div class="expw-input">
<input id="{$ids.confirmPassword}" type="password" name="confirmPassword" value="" autocomplete="new-password" spellcheck="false" autocapitalize="off" data-exp-password="confirm"{if $errors.confirmPassword|count} aria-invalid="true" aria-describedby="{$ids.confirmPassword}-error"{/if} />
<button type="button" class="expw-toggle" data-exp-password-toggle="{$ids.confirmPassword}" aria-controls="{$ids.confirmPassword}" aria-pressed="false" hidden><span data-exp-password-toggle-text>{'Show'|i18n( $ctx )}</span></button>
</div>
{if $errors.confirmPassword|count}<p class="expw-error" id="{$ids.confirmPassword}-error">{foreach $errors.confirmPassword as $t}<span>{$t|wash}</span>{/foreach}</p>{/if}
<p class="expw-match" data-exp-password-match aria-live="polite" hidden></p>
</div>

<p data-exp-password-status aria-live="polite" hidden></p>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
<input class="defaultbutton" type="submit" name="OKButton" value="{'Change password'|i18n( $ctx )}" />
<input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/admin/user/password' )}" formnovalidate />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>
{ezscript( array( 'exp_password_field.js' ) )}
{/if}
</div>
{undef}
