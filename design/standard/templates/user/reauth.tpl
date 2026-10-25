{* "Verify it's you" (expReauth, doc/eng-us/guides/verify-its-you.md) on a site design: shown instead of a view that
   needs a fresh confirmation. One form per way to confirm (password, passkey, a code of the authenticator app); each
   posts to the same address with the request's own fields hidden, so after a right answer the action runs as it was
   sent. The administration designs have their own copy (admin, admin4). Variables: methods, group, minutes, failed,
   failed_reason, failures_left, replay, replay_get, had_files, passkey_options, login, action_uri, cancel_uri. *}
{def $why = hash( 'account', 'You are about to change how you sign in or the security of your account.'|i18n( 'design/standard/user/reauth' ),
                  'users', 'You are about to manage users, roles or policies.'|i18n( 'design/standard/user/reauth' ),
                  'system', 'You are about to change system settings or run a setup action.'|i18n( 'design/standard/user/reauth' ),
                  'export', 'You are about to export data.'|i18n( 'design/standard/user/reauth' ),
                  'content', 'You are about to remove or publish content.'|i18n( 'design/standard/user/reauth' ) )
     $has_password = $methods|contains( 'password' )
     $has_passkey = and( $methods|contains( 'passkey' ), $passkey_options )
     $has_code = $methods|contains( 'code' )}
<div class="maincontentheader">
<h1>{"Verify it's you"|i18n( 'design/standard/user/reauth' )}</h1>
</div>

<p>{if is_set( $why[$group] )}{$why[$group]|wash}{else}{'This action needs you to confirm who you are.'|i18n( 'design/standard/user/reauth' )}{/if}{' '}
{'Confirm it is you, signed in as %login. You will not be asked again for %minutes minutes.'|i18n( 'design/standard/user/reauth',, hash( '%login', $login, '%minutes', $minutes ) )|wash}</p>

{if $failed}
<div class="warning" role="alert">
<h2>{switch match=$failed_reason}
    {case match='credentials'}{'That password is not right.'|i18n( 'design/standard/user/reauth' )}{/case}
    {case match='code'}{'That code is not right, or it was used already.'|i18n( 'design/standard/user/reauth' )}{/case}
    {case match='method'}{'Choose one of the ways below.'|i18n( 'design/standard/user/reauth' )}{/case}
    {case}{'The passkey could not be checked.'|i18n( 'design/standard/user/reauth' )}{/case}
{/switch}</h2>
<p>{'%count tries are left before you are signed out.'|i18n( 'design/standard/user/reauth',, hash( '%count', $failures_left ) )}</p>
</div>
{/if}
{if $had_files}
<div class="warning" role="status"><p>{'The files you chose cannot be sent again: after confirming, choose them once more.'|i18n( 'design/standard/user/reauth' )}</p></div>
{/if}

{if or( $has_password, $has_passkey )}
<form method="post" action={$action_uri|ezurl} id="reauth-form"{if $has_passkey}
      data-exp-passkey="reauth" data-exp-passkey-options="{$passkey_options|wash}"
      data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-cancelled="{'The passkey dialog was closed. Nothing was changed.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-failed="{'The passkey could not be used. Please try again.'|i18n( 'design/standard/user/passkeys' )|wash}"{/if}>
{include uri='design:user/parts/reauth_replay.tpl'}
{if $has_password}
<input type="hidden" name="ExpReauthMethod" value="password" />
<div class="block">
<label for="reauth-password">{'Password'|i18n( 'design/standard/user/reauth' )}</label><div class="labelbreak"></div>
<input class="halfbox" type="password" id="reauth-password" name="ExpReauthPassword" autocomplete="current-password" required="required" autofocus="autofocus" />
</div>
{/if}
<div class="buttonblock">
{if $has_password}<button class="defaultbutton" type="submit" name="ExpReauthConfirmButton" value="1">{'Confirm'|i18n( 'design/standard/user/reauth' )}</button>{/if}
{if $has_passkey}<button class="button" type="button" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">{'Use a passkey'|i18n( 'design/standard/user/reauth' )}</button>{/if}
</div>
{if $has_passkey}<p class="feedback" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>{/if}
</form>
{/if}

{if $has_code}
<form method="post" action={$action_uri|ezurl}>
{include uri='design:user/parts/reauth_replay.tpl'}
<input type="hidden" name="ExpReauthMethod" value="code" />
<div class="block">
<label for="reauth-code">{'Code from your authenticator app'|i18n( 'design/standard/user/reauth' )}</label><div class="labelbreak"></div>
<input class="halfbox" type="text" id="reauth-code" name="ExpReauthCode" inputmode="numeric" autocomplete="one-time-code" maxlength="9" required="required" />
</div>
<div class="buttonblock"><button class="button" type="submit" name="ExpReauthConfirmButton" value="1">{'Confirm'|i18n( 'design/standard/user/reauth' )}</button></div>
</form>
{/if}

{if $methods|count|eq( 0 )}
<div class="warning"><p>{'This account has no way to confirm who you are (no password, passkey or authenticator app). Sign out and in again, or ask an administrator.'|i18n( 'design/standard/user/reauth' )}</p></div>
{/if}

<p><a href={$cancel_uri|ezurl}>{'Cancel'|i18n( 'design/standard/user/reauth' )}</a></p>
{if $has_passkey}<script src={'javascript/exp_passkey.js'|ezdesign} defer="defer"></script>{/if}
{undef $why $has_password $has_passkey $has_code}
