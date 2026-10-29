{* user/security: "Security activity", the recent security events of one account (kernel/private/classes/views/user/security.php,
   expUserSecurityActivity over the user audit log): a timeline, newest first, filtered (all, sign-ins, account
   changes, problems) and paged, with the counts of the last 30 days on top and links to "Your devices" and the
   password page. Read only.
   Variables: security_user, security_user_name, security_user_id, security_own, security_available, security_filter,
   security_filters, security_events, security_count, security_limit, security_summary, security_retention_days,
   security_action, view_parameters.
   No inline CSS: shared components only (.a4-figures, .a4-tabs, .a4-timeline). The same file is in design/admin and
   design/admin4. Guide: doc/eng-us/features/6.0/user-sessions.md *}
{def $kinds = hash(
        'signin', 'Signed in'|i18n( 'design/admin/user/security' ),
        'signin_failed', 'Failed sign-in attempt'|i18n( 'design/admin/user/security' ),
        'signout', 'Signed out'|i18n( 'design/admin/user/security' ),
        'revoke', 'Sessions signed out'|i18n( 'design/admin/user/security' ),
        'new_device', 'Sign-in from a new device'|i18n( 'design/admin/user/security' ),
        'new_location', 'Sign-in from a new location'|i18n( 'design/admin/user/security' ),
        'reauth', 'Identity confirmed ("Verify it is you")'|i18n( 'design/admin/user/security' ),
        'reauth_failed', 'Identity check failed'|i18n( 'design/admin/user/security' ),
        'passkey_use', 'Signed in with a passkey'|i18n( 'design/admin/user/security' ),
        'passkey_failed', 'Passkey sign-in failed'|i18n( 'design/admin/user/security' ),
        'second_factor_refused', 'Sign-in refused: two-step code missing or wrong'|i18n( 'design/admin/user/security' ),
        'twofactor_failed', 'Wrong two-step code'|i18n( 'design/admin/user/security' ),
        'locked', 'Account locked after failed sign-ins'|i18n( 'design/admin/user/security' ),
        'unlocked', 'Account unlocked'|i18n( 'design/admin/user/security' ),
        'password_change', 'Password changed'|i18n( 'design/admin/user/security' ),
        'password_change_failed', 'Password change failed'|i18n( 'design/admin/user/security' ),
        'password_reset', 'Password reset'|i18n( 'design/admin/user/security' ),
        'password_reset_request', 'New password requested'|i18n( 'design/admin/user/security' ),
        'email_change', 'E-mail address changed'|i18n( 'design/admin/user/security' ),
        'login_change', 'Username changed'|i18n( 'design/admin/user/security' ),
        'twofactor_on', 'Two-step sign-in turned on'|i18n( 'design/admin/user/security' ),
        'twofactor_off', 'Two-step sign-in turned off'|i18n( 'design/admin/user/security' ),
        'recovery_used', 'Recovery code used'|i18n( 'design/admin/user/security' ),
        'recovery_issued', 'New recovery codes'|i18n( 'design/admin/user/security' ),
        'app_password_create', 'App password created'|i18n( 'design/admin/user/security' ),
        'app_password_revoke', 'App password revoked'|i18n( 'design/admin/user/security' ),
        'passkey_add', 'Passkey added'|i18n( 'design/admin/user/security' ),
        'passkey_remove', 'Passkey removed'|i18n( 'design/admin/user/security' ),
        'enabled', 'Account enabled'|i18n( 'design/admin/user/security' ),
        'disabled', 'Account disabled'|i18n( 'design/admin/user/security' ) )
     $filter_names = hash( 'all', 'All'|i18n( 'design/admin/user/security' ), 'signin', 'Sign-ins and sessions'|i18n( 'design/admin/user/security' ),
                           'account', 'Account changes'|i18n( 'design/admin/user/security' ), 'problems', 'Needs attention'|i18n( 'design/admin/user/security' ) )
     $by_names = hash( 'self', cond( $security_own, 'by you'|i18n( 'design/admin/user/security' ), 'by the user'|i18n( 'design/admin/user/security' ) ),
                       'admin', 'by %login'|i18n( 'design/admin/user/security' ), 'system', '' )
     $detail_names = hash( 'method', 'Method'|i18n( 'design/admin/user/security' ), 'browser', 'Browser'|i18n( 'design/admin/user/security' ),
                           'os', 'System'|i18n( 'design/admin/user/security' ), 'device', 'Device'|i18n( 'design/admin/user/security' ),
                           'location', 'Place'|i18n( 'design/admin/user/security' ), 'country', 'Country'|i18n( 'design/admin/user/security' ),
                           'count', 'Sessions'|i18n( 'design/admin/user/security' ), 'scope', 'Which'|i18n( 'design/admin/user/security' ),
                           'channel', 'Channel'|i18n( 'design/admin/user/security' ), 'attempts', 'Failed attempts'|i18n( 'design/admin/user/security' ),
                           'left', 'Codes left'|i18n( 'design/admin/user/security' ), 'handler', 'Sign-in handler'|i18n( 'design/admin/user/security' ) )
     $level_class = hash( 'ok', 'is-ok', 'warn', 'is-warn', 'bad', 'is-bad', 'info', 'is-info' )
     $sessions_url = cond( $security_own, 'user/sessions', concat( 'user/sessions/', $security_user_id ) )}
<div class="a4-page a4-standalone" id="exp-user-security">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $security_own}{'Security activity'|i18n( 'design/admin/user/security' )}{else}{'Security activity of %name'|i18n( 'design/admin/user/security',, hash( '%name', $security_user_name ) )|wash}{/if}</h1>
        <p class="a4-pagehead-sub">{if $security_own}{'Sign-ins and changes to the security of your account. If you see something you did not do, change your password and sign out the devices you do not recognize.'|i18n( 'design/admin/user/security' )}{else}{'Sign-ins and changes to the security of this account.'|i18n( 'design/admin/user/security' )}{/if}</p>
    </div>
    <div class="a4-pagehead-actions">
        <a class="a4-btn" href={$sessions_url|ezurl}><span class="a4-icon a4-bi-laptop" aria-hidden="true"></span> {if $security_own}{'Your devices'|i18n( 'design/admin/user/security' )}{else}{'Sessions'|i18n( 'design/admin/user/security' )}{/if}</a>
        {if $security_own}<a class="a4-btn" href={'user/password'|ezurl}><span class="a4-icon a4-bi-key" aria-hidden="true"></span> {'Change password'|i18n( 'design/admin/user/security' )}</a>{/if}
        <a class="a4-btn" href={cond( $security_own, 'user/edit', concat( 'user/edit/', $security_user_id ) )|ezurl}>{'Back to the account'|i18n( 'design/admin/user/security' )}</a>
    </div>
</div>

{if $security_available|not}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The security activity is not recorded'|i18n( 'design/admin/user/security' )}</p>
    <p>{'It is read from the user audit log, whose table expuser_audit is missing or not supported on this database. Run php update/common/scripts/6.0/createuseraudittable.php, or the database update of 6.0.15.'|i18n( 'design/admin/user/security' )}</p>
</div>
{else}

<section class="a4-section" aria-labelledby="security-summary-title">
<h2 class="a4-sr-only" id="security-summary-title">{'The last %days days'|i18n( 'design/admin/user/security',, hash( '%days', $security_summary.days ) )}</h2>
<ul class="a4-figures">
    <li class="a4-figure"><strong>{$security_summary.signins}</strong> <span>{'Sign-ins in the last %days days'|i18n( 'design/admin/user/security',, hash( '%days', $security_summary.days ) )}</span></li>
    <li class="a4-figure{if $security_summary.failures|gt( 0 )} is-attention{/if}"><strong>{$security_summary.failures}</strong> <span>{'Failed attempts'|i18n( 'design/admin/user/security' )}</span></li>
    <li class="a4-figure"><strong>{$security_summary.changes}</strong> <span>{'Account changes'|i18n( 'design/admin/user/security' )}</span></li>
</ul>
</section>

<section class="a4-card a4-section" aria-labelledby="security-events-title">
    <div class="a4-card-head a4-row is-between">
        <h2 class="a4-h3 a4-card-title" id="security-events-title"><span class="a4-icon a4-bi-clock-history is-large" aria-hidden="true"></span>{'Recent security activity'|i18n( 'design/admin/user/security' )} <span class="a4-badge">{$security_count}</span></h2>
        <nav aria-label="{'Show'|i18n( 'design/admin/user/security' )|wash}">
        <ul class="a4-tabs">
        {foreach $security_filters as $f}
            <li>{if eq( $f, $security_filter )}<span class="is-current" aria-current="true">{$filter_names[$f]|wash}</span>{else}<a href={concat( $security_action, cond( eq( $f, 'all' ), '', concat( '?filter=', $f ) ) )|ezurl}>{$filter_names[$f]|wash}</a>{/if}</li>
        {/foreach}
        </ul>
        </nav>
    </div>
    <div class="a4-card-body">
    {if $security_events|count}
    <ol class="a4-timeline" role="list">
    {foreach $security_events as $e}
        <li class="{first_set( $level_class[$e.level], 'is-info' )}">
            <time class="a4-timeline-time" datetime="{$e.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )|wash}">{$e.time|l10n( 'shortdatetime' )}</time>
            <div class="a4-timeline-body">
                <strong><span class="a4-icon a4-bi-{$e.icon|wash}" aria-hidden="true"></span> {first_set( $kinds[$e.kind], $e.name )|wash}</strong>
                {if and( eq( $e.by, 'admin' ), $e.actor_login )}<span class="a4-timeline-meta">{'by %login'|i18n( 'design/admin/user/security',, hash( '%login', $e.actor_login ) )|wash}</span>{elseif eq( $e.by, 'self' )}<span class="a4-timeline-meta">{$by_names.self|wash}</span>{/if}
                {if $e.ip}<span class="a4-timeline-meta">{$e.ip|wash}</span>{/if}
                {if $e.host}<span class="a4-timeline-meta">{$e.host|wash}</span>{/if}
                {foreach $e.details as $key => $value}<span class="a4-timeline-meta">{first_set( $detail_names[$key], $key )|wash}: {$value|wash}</span>{/foreach}
                {if ne( $e.result, 'success' )}<span class="a4-badge {first_set( $level_class[$e.level], '' )}">{cond( eq( $e.result, 'failed' ), 'failed'|i18n( 'design/admin/user/security' ), eq( $e.result, 'refused' ), 'refused'|i18n( 'design/admin/user/security' ), $e.result )|wash}</span>{/if}
            </div>
        </li>
    {/foreach}
    </ol>
    {else}
    <p class="a4-empty">{'No security activity recorded yet.'|i18n( 'design/admin/user/security' )}</p>
    {/if}
    </div>
    <div class="a4-listfoot">
        <div class="a4-pager">
        {include name=navigator
                 uri='design:navigator/google.tpl'
                 page_uri=concat( '/', $security_action )
                 page_uri_suffix=cond( eq( $security_filter, 'all' ), '', concat( '?filter=', $security_filter ) )
                 item_count=$security_count
                 view_parameters=$view_parameters
                 item_limit=$security_limit}
        </div>
    </div>
</section>
<p class="a4-meta">{if $security_retention_days|gt( 0 )}{'Security events are kept for %days days.'|i18n( 'design/admin/user/security',, hash( '%days', $security_retention_days ) )}{else}{'Security events are kept until an administrator removes them.'|i18n( 'design/admin/user/security' )}{/if}</p>

{/if}
</div>
{undef $kinds $filter_names $by_names $detail_names $level_class $sessions_url}
