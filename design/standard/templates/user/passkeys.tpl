{* The passkeys of the signed-in user's own account: user/passkeys (doc/eng-us/guides/passkeys.md), on a site design.
   The list, "Add a passkey" with a name (the browser's passkey dialog runs from design:javascript/exp_passkey.js),
   Rename and Remove (after a confirmation). The administration designs have their own copy (admin, admin4).
   Variables: available, switched_off, passkeys, can_add, max_per_user, options_json, default_name, message (kind,
   text), confirm_passkey, reauth_seconds, own, account, action_uri, policy, password_sign_in, sent_by_policy,
   last_locked, console_slots. With the policy that sent the user here a notice says why; templates of
   [PasskeySettings] ConsoleSlots[] follow the list. *}
<div class="maincontentheader">
<h1>{'Passkeys'|i18n( 'design/standard/user/passkeys' )}</h1>
</div>
<p><a href={'user/edit'|ezurl}>{'My account'|i18n( 'design/standard/user/passkeys' )}</a></p>

<p>{'A passkey signs you in with your fingerprint, face, screen lock or security key instead of your password. It is stored on your device or in your password manager; this site keeps only its public key, so a leak here cannot reveal it.'|i18n( 'design/standard/user/passkeys' )}</p>

{if $message}
<div class="{if eq( $message.kind, 'ok' )}feedback{else}warning{/if}" role="status"><p>{$message.text|wash}</p></div>
{/if}

{if $sent_by_policy}
<div class="{if eq( $policy, 'required' )}warning{else}feedback{/if}" role="status">
<h2>{if eq( $policy, 'required' )}{'Your account needs a passkey'|i18n( 'design/standard/user/passkeys' )}{else}{'Add a passkey to your account'|i18n( 'design/standard/user/passkeys' )}{/if}</h2>
<p>{if eq( $policy, 'required' )}{'Add a passkey to go on. You then sign in with it instead of a password alone.'|i18n( 'design/standard/user/passkeys' )}{else}{'Signing in with a passkey is faster and safer than a password. Add one now, or go on without it.'|i18n( 'design/standard/user/passkeys' )}{/if}</p>
{if ne( $policy, 'required' )}<form method="post" action={$action_uri|ezurl}><button class="button" type="submit" name="SkipPasskeyButton" value="1">{'Not now'|i18n( 'design/standard/user/passkeys' )}</button></form>{/if}
</div>
{/if}

{if $available|not}
<div class="warning" role="status"><p>
    {if $switched_off}{'Passkeys are switched off on this site.'|i18n( 'design/standard/user/passkeys' )}
    {else}{'Passkeys cannot be used on this site yet: the administrator has to run the database update first.'|i18n( 'design/standard/user/passkeys' )}{/if}
</p></div>
{else}

{if $confirm_passkey}
<div class="warning">
<h2>{'Remove the passkey "%name"?'|i18n( 'design/standard/user/passkeys',, hash( '%name', $confirm_passkey.name ) )|wash}</h2>
<p>{'It can no longer sign in to this account. Remove it from your device or password manager as well.'|i18n( 'design/standard/user/passkeys' )}</p>
<form method="post" action={$action_uri|ezurl}>
    <input type="hidden" name="PasskeyID" value="{$confirm_passkey.id}" />
    <button class="button" type="submit" name="ConfirmRemovePasskeyButton" value="1">{'Remove the passkey'|i18n( 'design/standard/user/passkeys' )}</button>
    <a href={$action_uri|ezurl}>{'Cancel'|i18n( 'design/standard/user/passkeys' )}</a>
</form>
</div>
{/if}

<h2>{'Your passkeys'|i18n( 'design/standard/user/passkeys' )}</h2>
{if $passkeys|count|eq( 0 )}
<p>{'You have no passkey yet. Add one below: the next time you sign in, choose "Sign in with a passkey".'|i18n( 'design/standard/user/passkeys' )}</p>
{else}
<table class="list" cellspacing="0">
<caption>{'%count of at most %max'|i18n( 'design/standard/user/passkeys',, hash( '%count', $passkeys|count, '%max', $max_per_user ) )}</caption>
<tr>
    <th scope="col">{'Name'|i18n( 'design/standard/user/passkeys' )}</th>
    <th scope="col">{'Authenticator'|i18n( 'design/standard/user/passkeys' )}</th>
    <th scope="col">{'Added'|i18n( 'design/standard/user/passkeys' )}</th>
    <th scope="col">{'Last used'|i18n( 'design/standard/user/passkeys' )}</th>
    <th scope="col">{'Kind'|i18n( 'design/standard/user/passkeys' )}</th>
    <th scope="col">{'Actions'|i18n( 'design/standard/user/passkeys' )}</th>
</tr>
{foreach $passkeys as $passkey sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td>{$passkey.name|wash}</td>
    <td>{if $passkey.authenticator_name}{$passkey.authenticator_name|wash}{else}{'Unknown'|i18n( 'design/standard/user/passkeys' )}{/if}</td>
    <td>{$passkey.created|l10n( 'shortdatetime' )}</td>
    <td>{if $passkey.never_used}{'Not yet'|i18n( 'design/standard/user/passkeys' )}{else}{$passkey.last_used|l10n( 'shortdatetime' )}{/if}</td>
    <td>{if $passkey.synced}{'Synced'|i18n( 'design/standard/user/passkeys' )}{else}{'This device or key'|i18n( 'design/standard/user/passkeys' )}{/if} ({$passkey.algorithm_name|wash})</td>
    <td>
        <form method="post" action={$action_uri|ezurl}>
            <input type="hidden" name="PasskeyID" value="{$passkey.id}" />
            <label for="passkey-name-{$passkey.id}">{'New name'|i18n( 'design/standard/user/passkeys' )}</label>
            <input type="text" id="passkey-name-{$passkey.id}" name="PasskeyNewName" value="{$passkey.name|wash}" maxlength="100" />
            <button class="button" type="submit" name="RenamePasskeyButton" value="1">{'Rename'|i18n( 'design/standard/user/passkeys' )}</button>
            {if $last_locked}{'Your last passkey: add another before you remove it.'|i18n( 'design/standard/user/passkeys' )}{else}<button class="button" type="submit" name="RemovePasskeyButton" value="1">{'Remove'|i18n( 'design/standard/user/passkeys' )}</button>{/if}
        </form>
    </td>
</tr>
{/foreach}
</table>
{/if}

<h2>{'Add a passkey'|i18n( 'design/standard/user/passkeys' )}</h2>
{if $can_add}
<form method="post" action={$action_uri|ezurl} id="passkey-register"
      data-exp-passkey="register" data-exp-passkey-options="{$options_json|wash}"
      data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-cancelled="{'The passkey dialog was closed. Nothing was changed.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-failed="{'The passkey could not be made. Please try again.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-unsupported="{'This browser cannot make passkeys. Use a current browser, or sign in with your password.'|i18n( 'design/standard/user/passkeys' )|wash}">
<div class="block">
<label for="passkey-new-name">{'Name'|i18n( 'design/standard/user/passkeys' )}</label><div class="labelbreak"></div>
<input class="halfbox" type="text" id="passkey-new-name" name="PasskeyName" value="" maxlength="100" placeholder="{$default_name|wash}" />
<p>{'For example the device or password manager it is on, so you can tell your passkeys apart.'|i18n( 'design/standard/user/passkeys' )}</p>
</div>
<div class="buttonblock">
<button class="defaultbutton" type="button" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">{'Add a passkey'|i18n( 'design/standard/user/passkeys' )}</button>
</div>
<p class="feedback" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>
</form>
{else}
<p>{'This account has as many passkeys as it may have (%max). Remove one to add another.'|i18n( 'design/standard/user/passkeys',, hash( '%max', $max_per_user ) )}</p>
{/if}
{foreach $console_slots as $slot}{include uri=$slot account=$account own=$own}{/foreach}
{/if}
<script src={'javascript/exp_passkey.js'|ezdesign} defer="defer"></script>
