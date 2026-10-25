{* The way from a user's profile to "Your devices" (user/sessions), for a signed in user when sessions are recorded.
   Its look is in stylesheets/user_sessions.css, linked here so it needs nothing of the page it is on. *}
{if and( fetch( 'user', 'current_user' ).is_logged_in, ne( ezini( 'UserSessionSettings', 'Tracking' ), 'disabled' ) )}
<link rel="stylesheet" type="text/css" href={'stylesheets/user_sessions.css'|ezdesign} />
<div class="us-account-link">
    <p><b>{'Your devices'|i18n( 'design/standard/user/sessions' )}:</b>
        {'See where your account is signed in, and sign out a device you do not know.'|i18n( 'design/standard/user/sessions' )}</p>
    <a class="us-account-link-button" href={'user/sessions'|ezurl}>{'Manage devices'|i18n( 'design/standard/user/sessions' )}</a>
</div>
{/if}
