{* "Sign in with a passkey" as a form of its own, for a design whose user/login.tpl is its own (doc/eng-us/guides/passkeys.md):
     {include uri='design:user/parts/passkey_login.tpl'}
   placed after the design's sign-in form. Draws nothing unless the view offers passkeys ($passkey_options). The
   browser's passkey autofill of the username field needs the attributes on the design's own form instead (see
   design/standard/templates/user/login.tpl). Variables: passkey_options, passkey_warning, User:redirect_uri. *}
{if first_set( $passkey_options, false() )}
{if first_set( $passkey_warning, false() )}<p class="warning" role="alert">{$passkey_warning|wash}</p>{/if}
<form method="post" action={'/user/login/'|ezurl} class="exp-passkey-login" data-exp-passkey="login" data-exp-passkey-options="{$passkey_options|wash}"
      data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-cancelled="{'The passkey dialog was closed. You can sign in with your password instead.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-failed="{'The passkey could not be used. Please try again, or sign in with your password.'|i18n( 'design/standard/user/passkeys' )|wash}">
    <input type="hidden" name="RedirectURI" value="{first_set( $User:redirect_uri, '' )|wash}" />
    <button class="button" type="button" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">{'Sign in with a passkey'|i18n( 'design/standard/user/passkeys' )}</button>
    <p data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>
</form>
<script src={'javascript/exp_passkey.js'|ezdesign} defer="defer"></script>
{/if}
