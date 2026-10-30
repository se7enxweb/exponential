{* The passkeys of an account: user/passkeys (one's own) and user/passkeys/<user id> (another account's, for an
   administrator with user/passkeys_manage), doc/eng-us/guides/passkeys.md; admin and admin4.

   The list (name, authenticator, made, last used, synced or on one device), "Add a passkey" with a name (the browser's
   passkey dialog runs from design:javascript/exp_passkey.js; without WebAuthn the button stays hidden and a note says
   so), Rename in a fold and Remove behind a confirmation; for another account only the list and Revoke. When the
   passkey policy sent the user here, a notice says why ("Not now" for a suggestion). Templates of
   [PasskeySettings] ConsoleSlots[] follow the list. Only the shared components and tokens of components.css; the same
   file is in design/admin. Variables: available, switched_off, passkeys, can_add, max_per_user, options_json,
   default_name, message (kind, text), confirm_passkey, reauth_seconds, own, account, action_uri, policy,
   password_sign_in, sent_by_policy, last_locked, console_slots. *}
<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title"><span class="a4-icon a4-bi-key is-large" aria-hidden="true"></span> {if $own}{'Passkeys'|i18n( 'design/standard/user/passkeys' )}{else}{'Passkeys of %login'|i18n( 'design/standard/user/passkeys',, hash( '%login', $account.login ) )|wash}{/if}</h1>

</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $own}
<p class="a4-intro">{'A passkey signs you in with your fingerprint, face, screen lock or security key instead of your password. It is stored on your device or in your password manager; this site keeps only its public key, so a leak here cannot reveal it.'|i18n( 'design/standard/user/passkeys' )}</p>
{else}
<p class="a4-intro">{'The passkeys this account signs in with. Revoke one that is on a lost or stolen device: it cannot sign in any more. The owner adds new ones themselves.'|i18n( 'design/standard/user/passkeys' )}</p>
{/if}

{if $message}
<div class="a4-notice is-{if eq( $message.kind, 'ok' )}ok{else}bad{/if}" role="status">{$message.text|wash}</div>
{/if}

{if $sent_by_policy}
<div class="a4-notice is-{if eq( $policy, 'required' )}warn{else}info{/if}" role="status">
    <span class="a4-notice-title">{if eq( $policy, 'required' )}{'Your account needs a passkey'|i18n( 'design/standard/user/passkeys' )}{else}{'Add a passkey to your account'|i18n( 'design/standard/user/passkeys' )}{/if}</span>
    {if eq( $policy, 'required' )}{'Add a passkey to go on. You then sign in with it instead of a password alone.'|i18n( 'design/standard/user/passkeys' )}
    {else}{'Signing in with a passkey is faster and safer than a password. Add one now, or go on without it.'|i18n( 'design/standard/user/passkeys' )}
    <form method="post" action={$action_uri|ezurl} class="a4-actions"><button class="a4-btn a4-btn-ghost" type="submit" name="SkipPasskeyButton" value="1">{'Not now'|i18n( 'design/standard/user/passkeys' )}</button></form>{/if}
</div>
{/if}

{if $available|not}
<div class="a4-notice is-warn" role="status">
    {if $switched_off}{'Passkeys are switched off on this site.'|i18n( 'design/standard/user/passkeys' )}
    {else}{'Passkeys cannot be used on this site yet: the administrator has to run the database update first.'|i18n( 'design/standard/user/passkeys' )}{/if}
</div>
{else}

{if $confirm_passkey}
<section class="a4-section" aria-labelledby="passkey-confirm-title">
<div class="a4-notice is-warn">
    <h2 class="a4-h3 a4-notice-title" id="passkey-confirm-title">{if $own}{'Remove the passkey "%name"?'|i18n( 'design/standard/user/passkeys',, hash( '%name', $confirm_passkey.name ) )|wash}{else}{'Revoke the passkey "%name"?'|i18n( 'design/standard/user/passkeys',, hash( '%name', $confirm_passkey.name ) )|wash}{/if}</h2>
    <p>{if $own}{'It can no longer sign in to this account. Remove it from your device or password manager as well.'|i18n( 'design/standard/user/passkeys' )}{else}{'It can no longer sign in to this account. If it was the last one, the owner signs in with the password, or you set a new one.'|i18n( 'design/standard/user/passkeys' )}{/if}</p>
    <form method="post" action={$action_uri|ezurl} class="a4-actions">
        <input type="hidden" name="PasskeyID" value="{$confirm_passkey.id}" />
        <button class="a4-btn a4-btn-danger" type="submit" name="ConfirmRemovePasskeyButton" value="1">{if $own}{'Remove the passkey'|i18n( 'design/standard/user/passkeys' )}{else}{'Revoke the passkey'|i18n( 'design/standard/user/passkeys' )}{/if}</button>
        <a class="a4-btn" href={$action_uri|ezurl}>{'Cancel'|i18n( 'design/standard/user/passkeys' )}</a>
    </form>
</div>
</section>
{/if}

<section class="a4-section" aria-labelledby="passkey-list-title">
<div class="a4-section-head"><h2 class="a4-h2" id="passkey-list-title">{if $own}{'Your passkeys'|i18n( 'design/standard/user/passkeys' )}{else}{'Passkeys'|i18n( 'design/standard/user/passkeys' )}{/if}</h2>
<span class="a4-meta">{'%count of at most %max'|i18n( 'design/standard/user/passkeys',, hash( '%count', $passkeys|count, '%max', $max_per_user ) )}</span></div>
{if $passkeys|count|eq( 0 )}
<p class="a4-empty">{if $own}{'You have no passkey yet. Add one below: the next time you sign in, choose "Sign in with a passkey".'|i18n( 'design/standard/user/passkeys' )}{else}{'This account has no passkey.'|i18n( 'design/standard/user/passkeys' )}{/if}</p>
{else}
<div class="a4-card is-flush">
<div class="a4-table-wrap">
<table class="a4-table">
    <caption class="a4-sr-only">{'Passkeys'|i18n( 'design/standard/user/passkeys' )}</caption>
    <thead><tr>
        <th scope="col">{'Name'|i18n( 'design/standard/user/passkeys' )}</th>
        <th scope="col">{'Authenticator'|i18n( 'design/standard/user/passkeys' )}</th>
        <th scope="col">{'Added'|i18n( 'design/standard/user/passkeys' )}</th>
        <th scope="col">{'Last used'|i18n( 'design/standard/user/passkeys' )}</th>
        <th scope="col">{'Kind'|i18n( 'design/standard/user/passkeys' )}</th>
        <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/standard/user/passkeys' )}</span></th>
    </tr></thead>
    <tbody>
    {foreach $passkeys as $passkey}
    <tr>
        <td><strong>{$passkey.name|wash}</strong></td>
        <td>{if $passkey.authenticator_name}{$passkey.authenticator_name|wash}{else}<span class="a4-meta">{'Unknown'|i18n( 'design/standard/user/passkeys' )}</span>{/if}</td>
        <td class="a4-nowrap">{$passkey.created|l10n( 'shortdatetime' )}</td>
        <td class="a4-nowrap">{if $passkey.never_used}<span class="a4-meta">{'Not yet'|i18n( 'design/standard/user/passkeys' )}</span>{else}{$passkey.last_used|l10n( 'shortdatetime' )}{/if}</td>
        <td>{if $passkey.synced}<span class="a4-badge is-info">{'Synced'|i18n( 'design/standard/user/passkeys' )}</span>{else}<span class="a4-badge">{'This device or key'|i18n( 'design/standard/user/passkeys' )}</span>{/if}
            <span class="a4-meta">{$passkey.algorithm_name|wash}</span></td>
        <td>
            {if $own}
            <details class="a4-fold">
                <summary>{'Rename'|i18n( 'design/standard/user/passkeys' )}</summary>
                <form method="post" action={$action_uri|ezurl} class="a4-fold-body a4-form">
                    <input type="hidden" name="PasskeyID" value="{$passkey.id}" />
                    <div class="a4-field">
                        <label for="passkey-name-{$passkey.id}">{'New name'|i18n( 'design/standard/user/passkeys' )}</label>
                        <input type="text" id="passkey-name-{$passkey.id}" name="PasskeyNewName" value="{$passkey.name|wash}" maxlength="100" required="required" />
                    </div>
                    <div class="a4-actions"><button class="a4-btn" type="submit" name="RenamePasskeyButton" value="1">{'Save the name'|i18n( 'design/standard/user/passkeys' )}</button></div>
                </form>
            </details>
            {/if}
            {if and( $own, $last_locked )}
            <span class="a4-meta">{'Your last passkey: add another before you remove it.'|i18n( 'design/standard/user/passkeys' )}</span>
            {else}
            <form method="post" action={$action_uri|ezurl} class="a4-actions">
                <input type="hidden" name="PasskeyID" value="{$passkey.id}" />
                <button class="a4-btn a4-btn-outline-danger a4-btn-small" type="submit" name="RemovePasskeyButton" value="1">{if $own}{'Remove'|i18n( 'design/standard/user/passkeys' )}{else}{'Revoke'|i18n( 'design/standard/user/passkeys' )}{/if}</button>
            </form>
            {/if}
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
</div>
</div>
{/if}
{if and( $own|not, $password_sign_in|not )}<p class="a4-help">{'This account signs in with passkeys only (passkey policy): with none left, it signs in with its password again.'|i18n( 'design/standard/user/passkeys' )}</p>{/if}
</section>

{if $own}
<section class="a4-section" aria-labelledby="passkey-add-title">
<div class="a4-card">
    <div class="a4-card-head"><h2 class="a4-h3" id="passkey-add-title">{'Add a passkey'|i18n( 'design/standard/user/passkeys' )}</h2></div>
    {if $can_add}
    <form method="post" action={$action_uri|ezurl} class="a4-form" id="passkey-register"
          data-exp-passkey="register" data-exp-passkey-options="{$options_json|wash}"
          data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
          data-msg-cancelled="{'The passkey dialog was closed. Nothing was changed.'|i18n( 'design/standard/user/passkeys' )|wash}"
          data-msg-failed="{'The passkey could not be made. Please try again.'|i18n( 'design/standard/user/passkeys' )|wash}"
          data-msg-unsupported="{'This browser cannot make passkeys. Use a current browser, or sign in with your password.'|i18n( 'design/standard/user/passkeys' )|wash}">
        <div class="a4-field">
            <label for="passkey-new-name">{'Name'|i18n( 'design/standard/user/passkeys' )}</label>
            <input type="text" id="passkey-new-name" name="PasskeyName" value="" maxlength="100" placeholder="{$default_name|wash}" aria-describedby="passkey-new-name-help" />
            <p class="a4-help" id="passkey-new-name-help">{'For example the device or password manager it is on, so you can tell your passkeys apart.'|i18n( 'design/standard/user/passkeys' )}</p>
        </div>
        <div class="a4-actions">
            <button class="a4-btn a4-btn-primary" type="button" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">
                <span class="a4-icon a4-bi-plus-lg" aria-hidden="true"></span> {'Add a passkey'|i18n( 'design/standard/user/passkeys' )}</button>
        </div>
        <p class="a4-notice is-info" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>
    </form>
    {else}
    <p class="a4-meta">{'This account has as many passkeys as it may have (%max). Remove one to add another.'|i18n( 'design/standard/user/passkeys',, hash( '%max', $max_per_user ) )}</p>
    {/if}
</div>
</section>
{/if}

{foreach $console_slots as $slot}{include uri=$slot account=$account own=$own}{/foreach}
{/if}

</div></div></div>

</div>
{if $own}{ezscript( array( 'exp_passkey.js' ) )}{/if}
