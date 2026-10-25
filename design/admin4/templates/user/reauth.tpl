{* "Verify it's you" (expReauth, doc/eng-us/guides/verify-its-you.md), admin and admin4: shown instead of a view that
   needs a fresh confirmation. One form per way to confirm (password, passkey, a code of the authenticator app); each
   posts to the same address with the request's own fields hidden ($replay), so after a right answer the action runs
   as it was sent. Cancel goes back. Only shared components of components.css; the same file is in design/admin.
   Variables: methods, group, minutes, failed, failed_reason, failures_left, replay (name, value), replay_get,
   had_files, passkey_options, login, action_uri, cancel_uri. *}
{def $why = hash( 'account', 'You are about to change how you sign in or the security of your account.'|i18n( 'design/standard/user/reauth' ),
                  'users', 'You are about to manage users, roles or policies.'|i18n( 'design/standard/user/reauth' ),
                  'system', 'You are about to change system settings or run a setup action.'|i18n( 'design/standard/user/reauth' ),
                  'export', 'You are about to export data.'|i18n( 'design/standard/user/reauth' ),
                  'content', 'You are about to remove or publish content.'|i18n( 'design/standard/user/reauth' ) )
     $has_password = $methods|contains( 'password' )
     $has_passkey = and( $methods|contains( 'passkey' ), $passkey_options )
     $has_code = $methods|contains( 'code' )}
<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<h1 class="context-title"><span class="a4-icon a4-bi-shield-lock is-large" aria-hidden="true"></span> {"Verify it's you"|i18n( 'design/standard/user/reauth' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{if is_set( $why[$group] )}{$why[$group]|wash}{else}{'This action needs you to confirm who you are.'|i18n( 'design/standard/user/reauth' )}{/if}{' '}
{'Confirm it is you, signed in as %login. You will not be asked again for %minutes minutes.'|i18n( 'design/standard/user/reauth',, hash( '%login', $login, '%minutes', $minutes ) )|wash}</p>

{if $failed}
<div class="a4-notice is-bad" role="alert">
    <span class="a4-notice-title">{switch match=$failed_reason}
        {case match='credentials'}{'That password is not right.'|i18n( 'design/standard/user/reauth' )}{/case}
        {case match='code'}{'That code is not right, or it was used already.'|i18n( 'design/standard/user/reauth' )}{/case}
        {case match='method'}{'Choose one of the ways below.'|i18n( 'design/standard/user/reauth' )}{/case}
        {case}{'The passkey could not be checked.'|i18n( 'design/standard/user/reauth' )}{/case}
    {/switch}</span>
    {'%count tries are left before you are signed out.'|i18n( 'design/standard/user/reauth',, hash( '%count', $failures_left ) )}
</div>
{/if}
{if $had_files}
<div class="a4-notice is-warn" role="status">{'The files you chose cannot be sent again: after confirming, choose them once more.'|i18n( 'design/standard/user/reauth' )}</div>
{/if}

<div class="a4-grid">
{if or( $has_password, $has_passkey )}
<section class="a4-card" aria-labelledby="reauth-password-title">
    <div class="a4-card-head"><h2 class="a4-h3" id="reauth-password-title">{if $has_password}{'With your password'|i18n( 'design/standard/user/reauth' )}{else}{'With a passkey'|i18n( 'design/standard/user/reauth' )}{/if}</h2></div>
    <form method="post" action={$action_uri|ezurl} class="a4-form" id="reauth-form"{if $has_passkey}
          data-exp-passkey="reauth" data-exp-passkey-options="{$passkey_options|wash}"
          data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
          data-msg-cancelled="{'The passkey dialog was closed. Nothing was changed.'|i18n( 'design/standard/user/passkeys' )|wash}"
          data-msg-failed="{'The passkey could not be used. Please try again.'|i18n( 'design/standard/user/passkeys' )|wash}"{/if}>
        {include uri='design:user/parts/reauth_replay.tpl'}
        {if $has_password}
        <input type="hidden" name="ExpReauthMethod" value="password" />
        <div class="a4-field">
            <label for="reauth-password">{'Password'|i18n( 'design/standard/user/reauth' )}</label>
            <input type="password" id="reauth-password" name="ExpReauthPassword" autocomplete="current-password" required="required" autofocus="autofocus" />
        </div>
        {/if}
        <div class="a4-actions">
            {if $has_password}<button class="a4-btn a4-btn-primary" type="submit" name="ExpReauthConfirmButton" value="1">{'Confirm'|i18n( 'design/standard/user/reauth' )}</button>{/if}
            {if $has_passkey}<button class="a4-btn{if $has_password|not} a4-btn-primary{/if}" type="button" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">
                <span class="a4-icon a4-bi-key" aria-hidden="true"></span> {'Use a passkey'|i18n( 'design/standard/user/reauth' )}</button>{/if}
        </div>
        {if $has_passkey}<p class="a4-notice is-info" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>{/if}
    </form>
</section>
{/if}

{if $has_code}
<section class="a4-card" aria-labelledby="reauth-code-title">
    <div class="a4-card-head"><h2 class="a4-h3" id="reauth-code-title">{'With a code from your authenticator app'|i18n( 'design/standard/user/reauth' )}</h2></div>
    <form method="post" action={$action_uri|ezurl} class="a4-form">
        {include uri='design:user/parts/reauth_replay.tpl'}
        <input type="hidden" name="ExpReauthMethod" value="code" />
        <div class="a4-field">
            <label for="reauth-code">{'Code'|i18n( 'design/standard/user/reauth' )}</label>
            <input type="text" id="reauth-code" name="ExpReauthCode" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]{ldelim}6,9{rdelim}" maxlength="9" required="required" />
        </div>
        <div class="a4-actions"><button class="a4-btn{if or( $has_password, $has_passkey )|not} a4-btn-primary{/if}" type="submit" name="ExpReauthConfirmButton" value="1">{'Confirm'|i18n( 'design/standard/user/reauth' )}</button></div>
    </form>
</section>
{/if}
</div>

{if $methods|count|eq( 0 )}
<div class="a4-notice is-warn" role="status">{'This account has no way to confirm who you are (no password, passkey or authenticator app). Sign out and in again, or ask an administrator.'|i18n( 'design/standard/user/reauth' )}</div>
{/if}

<p class="a4-actions"><a class="a4-btn a4-btn-ghost" href={$cancel_uri|ezurl}>{'Cancel'|i18n( 'design/standard/user/reauth' )}</a></p>

</div></div></div>

</div>
{if $has_passkey}<script src={'javascript/exp_passkey.js'|ezdesign} defer="defer"></script>{/if}
{undef $why $has_password $has_passkey $has_code}
