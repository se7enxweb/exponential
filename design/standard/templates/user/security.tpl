{* user/security: "Security activity" on the public site and in the designs without an administration look of their own
   (the administration designs have their own copy). The recent security events of the account, newest first, with
   the counts of the last 30 days, a filter and paging. Read only.
   Variables: see design/admin4/templates/user/security.tpl. Its look is in stylesheets/user_sessions.css.
   Guide: doc/eng-us/guides/your-devices-and-sessions.md *}
{def $kinds = hash(
        'signin', 'Signed in'|i18n( 'design/standard/user/security' ),
        'signin_failed', 'Failed sign-in attempt'|i18n( 'design/standard/user/security' ),
        'signout', 'Signed out'|i18n( 'design/standard/user/security' ),
        'revoke', 'Sessions signed out'|i18n( 'design/standard/user/security' ),
        'new_device', 'Sign-in from a new device'|i18n( 'design/standard/user/security' ),
        'new_location', 'Sign-in from a new location'|i18n( 'design/standard/user/security' ),
        'reauth', 'Identity confirmed ("Verify it is you")'|i18n( 'design/standard/user/security' ),
        'reauth_failed', 'Identity check failed'|i18n( 'design/standard/user/security' ),
        'passkey_use', 'Signed in with a passkey'|i18n( 'design/standard/user/security' ),
        'passkey_failed', 'Passkey sign-in failed'|i18n( 'design/standard/user/security' ),
        'second_factor_refused', 'Sign-in refused: two-step code missing or wrong'|i18n( 'design/standard/user/security' ),
        'twofactor_failed', 'Wrong two-step code'|i18n( 'design/standard/user/security' ),
        'locked', 'Account locked after failed sign-ins'|i18n( 'design/standard/user/security' ),
        'unlocked', 'Account unlocked'|i18n( 'design/standard/user/security' ),
        'password_change', 'Password changed'|i18n( 'design/standard/user/security' ),
        'password_change_failed', 'Password change failed'|i18n( 'design/standard/user/security' ),
        'password_reset', 'Password reset'|i18n( 'design/standard/user/security' ),
        'password_reset_request', 'New password requested'|i18n( 'design/standard/user/security' ),
        'email_change', 'E-mail address changed'|i18n( 'design/standard/user/security' ),
        'login_change', 'Username changed'|i18n( 'design/standard/user/security' ),
        'twofactor_on', 'Two-step sign-in turned on'|i18n( 'design/standard/user/security' ),
        'twofactor_off', 'Two-step sign-in turned off'|i18n( 'design/standard/user/security' ),
        'recovery_used', 'Recovery code used'|i18n( 'design/standard/user/security' ),
        'recovery_issued', 'New recovery codes'|i18n( 'design/standard/user/security' ),
        'app_password_create', 'App password created'|i18n( 'design/standard/user/security' ),
        'app_password_revoke', 'App password revoked'|i18n( 'design/standard/user/security' ),
        'passkey_add', 'Passkey added'|i18n( 'design/standard/user/security' ),
        'passkey_remove', 'Passkey removed'|i18n( 'design/standard/user/security' ),
        'enabled', 'Account enabled'|i18n( 'design/standard/user/security' ),
        'disabled', 'Account disabled'|i18n( 'design/standard/user/security' ) )
     $filter_names = hash( 'all', 'All'|i18n( 'design/standard/user/security' ), 'signin', 'Sign-ins and sessions'|i18n( 'design/standard/user/security' ),
                           'account', 'Account changes'|i18n( 'design/standard/user/security' ), 'problems', 'Needs attention'|i18n( 'design/standard/user/security' ) )}
<link rel="stylesheet" type="text/css" href={'stylesheets/user_sessions.css'|ezdesign} />
<div class="us-page" id="exp-user-security">

<h1>{if $security_own}{'Security activity'|i18n( 'design/standard/user/security' )}{else}{'Security activity of %name'|i18n( 'design/standard/user/security',, hash( '%name', $security_user_name ) )|wash}{/if}</h1>
<p class="us-intro">{'Sign-ins and changes to the security of your account. If you see something you did not do, change your password and sign out the devices you do not recognize.'|i18n( 'design/standard/user/security' )}</p>
<p><a href={cond( $security_own, 'user/sessions', concat( 'user/sessions/', $security_user_id ) )|ezurl}>{'Your devices'|i18n( 'design/standard/user/security' )}</a>{if $security_own} &middot; <a href={'user/password'|ezurl}>{'Change password'|i18n( 'design/standard/user/security' )}</a>{/if}</p>

{if $security_available|not}
<div class="us-notice is-warn" role="status"><p>{'The security activity is not recorded on this site yet.'|i18n( 'design/standard/user/security' )}</p></div>
{else}

<ul class="us-figures">
    <li><strong>{$security_summary.signins}</strong> {'Sign-ins in the last %days days'|i18n( 'design/standard/user/security',, hash( '%days', $security_summary.days ) )}</li>
    <li{if $security_summary.failures|gt( 0 )} class="is-bad"{/if}><strong>{$security_summary.failures}</strong> {'Failed attempts'|i18n( 'design/standard/user/security' )}</li>
    <li><strong>{$security_summary.changes}</strong> {'Account changes'|i18n( 'design/standard/user/security' )}</li>
</ul>

<p class="us-filter">{foreach $security_filters as $f}{if eq( $f, $security_filter )}<strong aria-current="true">{$filter_names[$f]|wash}</strong>{else}<a href={concat( $security_action, cond( eq( $f, 'all' ), '', concat( '?filter=', $f ) ) )|ezurl}>{$filter_names[$f]|wash}</a>{/if}{delimiter} &middot; {/delimiter}{/foreach}</p>

{if $security_events|count}
<ol class="us-events">
{foreach $security_events as $e}
    <li class="is-{$e.level|wash}">
        <time datetime="{$e.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )|wash}">{$e.time|l10n( 'shortdatetime' )}</time>
        <strong>{first_set( $kinds[$e.kind], $e.name )|wash}</strong>
        {if $e.ip}<span class="us-meta">{$e.ip|wash}</span>{/if}
        {if is_set( $e.details.location )}<span class="us-meta">{$e.details.location|wash}</span>{/if}
        {if and( is_set( $e.details.browser ), is_set( $e.details.os ) )}<span class="us-meta">{$e.details.browser|wash}, {$e.details.os|wash}</span>{/if}
    </li>
{/foreach}
</ol>
{include name=navigator uri='design:navigator/google.tpl' page_uri=concat( '/', $security_action )
         page_uri_suffix=cond( eq( $security_filter, 'all' ), '', concat( '?filter=', $security_filter ) )
         item_count=$security_count view_parameters=$view_parameters item_limit=$security_limit}
{else}
<p class="us-meta">{'No security activity recorded yet.'|i18n( 'design/standard/user/security' )}</p>
{/if}
{if $security_own}
<form method="post" action={$security_action|ezurl}>
    <p><button type="submit" class="us-button" name="DownloadButton" value="1">{'Download my sign-in data'|i18n( 'design/standard/user/security' )}</button>
    <span class="us-meta">{'Your sessions and security events as a JSON file.'|i18n( 'design/standard/user/security' )}</span></p>
</form>
{/if}

{/if}
</div>
{undef $kinds $filter_names}
