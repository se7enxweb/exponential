{* user/sessions: "Your devices", the signed-in sessions of a user (kernel/private/classes/views/user/sessions.php).
   The running sessions as cards, this device first, each with a button to sign it out; "Sign out all other
   sessions"; the "New sign-in" mail on or off (own page only); the sessions that ended in the last days as a table.
   Variables: sessions_user, sessions_user_name, sessions_user_id, sessions_own, sessions_can_administer,
   sessions_enabled, sessions_available, sessions_active, sessions_ended, sessions_current_id, sessions_retention_days,
   sessions_mail_setting, sessions_mail, sessions_message, sessions_form_token, sessions_action.
   No inline CSS: shared components only. The same file is in design/admin and design/admin4.
   Guide: doc/eng-us/features/6.0/user-sessions.md *}
{def $device_icons = hash( 'desktop', 'laptop', 'mobile', 'phone', 'tablet', 'tablet', 'app', 'terminal', 'bot', 'cpu', 'unknown', 'globe' )
     $device_names = hash( 'desktop', 'Computer'|i18n( 'design/admin/user/sessions' ), 'mobile', 'Phone'|i18n( 'design/admin/user/sessions' ),
                           'tablet', 'Tablet'|i18n( 'design/admin/user/sessions' ), 'app', 'Program or app'|i18n( 'design/admin/user/sessions' ),
                           'bot', 'Automated client'|i18n( 'design/admin/user/sessions' ), 'unknown', 'Unknown device'|i18n( 'design/admin/user/sessions' ) )
     $methods = hash( 'password', 'Password'|i18n( 'design/admin/user/sessions' ),
                      'two_factor', 'Password and two-step code'|i18n( 'design/admin/user/sessions' ),
                      'rest', 'REST interface'|i18n( 'design/admin/user/sessions' ), 'soap', 'SOAP interface'|i18n( 'design/admin/user/sessions' ),
                      'webdav', 'WebDAV'|i18n( 'design/admin/user/sessions' ), 'single_sign_on', 'Single sign-on'|i18n( 'design/admin/user/sessions' ),
                      'unknown', 'Signed in before sessions were recorded'|i18n( 'design/admin/user/sessions' ) )
     $reasons = hash( 'signout', 'Signed out'|i18n( 'design/admin/user/sessions' ), 'revoked', 'Signed out from this page'|i18n( 'design/admin/user/sessions' ),
                      'revoked_admin', 'Signed out by an administrator'|i18n( 'design/admin/user/sessions' ),
                      'revoked_others', 'Signed out with "all other sessions"'|i18n( 'design/admin/user/sessions' ),
                      'replaced', 'Replaced by a new sign-in'|i18n( 'design/admin/user/sessions' ),
                      'password_change', 'Ended by a password change'|i18n( 'design/admin/user/sessions' ),
                      'expired', 'Expired'|i18n( 'design/admin/user/sessions' ) )
     $others = 0}
{foreach $sessions_active as $s}{if $s.current|not}{set $others = inc( $others )}{/if}{/foreach}
<div class="a4-page a4-standalone" id="exp-user-sessions">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $sessions_own}{'Your devices'|i18n( 'design/admin/user/sessions' )}{else}{'Sessions of %name'|i18n( 'design/admin/user/sessions',, hash( '%name', $sessions_user_name ) )|wash}{/if}</h1>
        <p class="a4-pagehead-sub">{if $sessions_own}{'Where your account is signed in right now. If you do not recognise a device, sign it out and change your password.'|i18n( 'design/admin/user/sessions' )}{else}{'Where this account is signed in right now. Signing the user out ends every session at its next request.'|i18n( 'design/admin/user/sessions' )}{/if}</p>
    </div>
    <div class="a4-pagehead-actions">
        <a class="a4-btn" href={cond( $sessions_own, 'user/edit', concat( 'user/edit/', $sessions_user_id ) )|ezurl}>{'Back to the account'|i18n( 'design/admin/user/sessions' )}</a>
    </div>
</div>

{if $sessions_message}
<div class="a4-notice is-{$sessions_message.level|wash}" role="status">
<p>{switch match=$sessions_message.key}
{case match='revoked'}{'The session was signed out. The device has to sign in again.'|i18n( 'design/admin/user/sessions' )}{/case}
{case match='revoked_others'}{if $sessions_message.count|gt( 0 )}{'%count other sessions were signed out.'|i18n( 'design/admin/user/sessions',, hash( '%count', $sessions_message.count ) )}{else}{'There were no other sessions to sign out.'|i18n( 'design/admin/user/sessions' )}{/if}{/case}
{case match='revoked_all'}{if $sessions_message.count|gt( 0 )}{'%count sessions of this user were signed out.'|i18n( 'design/admin/user/sessions',, hash( '%count', $sessions_message.count ) )}{else}{'This user had no sessions to sign out.'|i18n( 'design/admin/user/sessions' )}{/if}{/case}
{case match='current'}{'This is the session you are using. To end it, sign out.'|i18n( 'design/admin/user/sessions' )}{/case}
{case match='not_found'}{'That session had already ended.'|i18n( 'design/admin/user/sessions' )}{/case}
{case match='mail_on'}{'You will get an e-mail when your account signs in from a new device.'|i18n( 'design/admin/user/sessions' )}{/case}
{case match='mail_off'}{'You will no longer get an e-mail about sign-ins from new devices.'|i18n( 'design/admin/user/sessions' )}{/case}
{case match='token'}{'The page had expired. Nothing was changed; please try again.'|i18n( 'design/admin/user/sessions' )}{/case}
{case}{/case}
{/switch}</p>
</div>
{/if}

{if $sessions_enabled|not}
<div class="a4-notice is-info" role="status"><p>{'Sessions are not recorded on this site (site.ini [UserSessionSettings] Tracking=disabled).'|i18n( 'design/admin/user/sessions' )}</p></div>
{elseif $sessions_available|not}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The table expuser_session is missing'|i18n( 'design/admin/user/sessions' )}</p>
    <p>{'Sessions are recorded once it is created: php update/common/scripts/6.0/createusersessiontable.php, or the database update of 6.0.15.'|i18n( 'design/admin/user/sessions' )}</p>
</div>
{else}

<form method="post" action={$sessions_action|ezurl}>
<input type="hidden" name="SessionsToken" value="{$sessions_form_token|wash}" />

<section class="a4-section" aria-labelledby="sessions-active-title">
<div class="a4-section-head a4-row is-between">
    <h2 class="a4-h2" id="sessions-active-title">{'Signed in now'|i18n( 'design/admin/user/sessions' )} <span class="a4-badge">{$sessions_active|count}</span></h2>
    {if or( $others|gt( 0 ), $sessions_own|not )}
    <details class="a4-confirm">
        <summary class="a4-btn a4-btn-outline-danger">{if $sessions_own}{'Sign out all other sessions'|i18n( 'design/admin/user/sessions' )}{else}{'Sign out everywhere'|i18n( 'design/admin/user/sessions' )}{/if}</summary>
        <div class="a4-fold-body is-warn">
            <p>{if $sessions_own}{'Every device except this one is signed out at its next request, also those that stay signed in with Remember me.'|i18n( 'design/admin/user/sessions' )}{else}{'Every session of this user is signed out at its next request, also those that stay signed in with Remember me.'|i18n( 'design/admin/user/sessions' )}{/if}</p>
            <button type="submit" class="a4-btn a4-btn-danger" name="RevokeOthersButton" value="1">{if $sessions_own}{'Yes, sign out the other sessions'|i18n( 'design/admin/user/sessions' )}{else}{'Yes, sign out every session'|i18n( 'design/admin/user/sessions' )}{/if}</button>
        </div>
    </details>
    {/if}
</div>

{if $sessions_active|count}
<ul class="a4-cards">
{foreach $sessions_active as $s}
    <li class="a4-card{if $s.current} is-selected{/if}" id="session-{$s.id}">
        <div class="a4-card-head">
            <h3 class="a4-h3 a4-card-title"><span class="a4-icon a4-bi-{first_set( $device_icons[$s.device], 'globe' )|wash} is-large" aria-hidden="true"></span>{$s.label|wash}</h3>
            {if $s.current}<span class="a4-badge is-ok">{'This device'|i18n( 'design/admin/user/sessions' )}</span>{/if}
        </div>
        <dl class="a4-facts is-rows">
            <dt>{'Device'|i18n( 'design/admin/user/sessions' )}</dt><dd>{first_set( $device_names[$s.device], $s.device )|wash}</dd>
            <dt>{'Address'|i18n( 'design/admin/user/sessions' )}</dt><dd><span title="{'Shown as its network; the full address is kept for the administrators.'|i18n( 'design/admin/user/sessions' )|wash}">{$s.ip|wash}</span>{if and( $s.first_ip, ne( $s.first_ip, $s.ip ) )} <span class="a4-meta">{'signed in from %ip'|i18n( 'design/admin/user/sessions',, hash( '%ip', $s.first_ip ) )|wash}</span>{/if}{if $sessions_own|not} <code class="a4-code">{$s.ip_full|wash}</code>{/if}</dd>
            <dt>{'Signed in'|i18n( 'design/admin/user/sessions' )}</dt><dd>{$s.created|l10n( 'shortdatetime' )} <span class="a4-meta">{first_set( $methods[$s.method], $s.method )|wash}</span></dd>
            <dt>{'Last active'|i18n( 'design/admin/user/sessions' )}</dt><dd>{if $s.current}{'Now'|i18n( 'design/admin/user/sessions' )}{else}{$s.last_active|l10n( 'shortdatetime' )}{/if}</dd>
            <dt>{'Stays signed in'|i18n( 'design/admin/user/sessions' )}</dt><dd>{if $s.remember_until}{'Remember me, until %date'|i18n( 'design/admin/user/sessions',, hash( '%date', $s.remember_until|l10n( 'shortdate' ) ) )|wash}{else}{'Until the browser is closed'|i18n( 'design/admin/user/sessions' )}{/if}</dd>
            {if $s.siteaccess}<dt>{'Signed in at'|i18n( 'design/admin/user/sessions' )}</dt><dd><code class="a4-code">{$s.siteaccess|wash}</code></dd>{/if}
        </dl>
        <details class="a4-fold"><summary>{'Browser identification'|i18n( 'design/admin/user/sessions' )}</summary><div class="a4-fold-body"><code class="a4-code">{$s.user_agent|wash}</code></div></details>
        {if $s.current}
        <p class="a4-meta">{'To end this session, sign out.'|i18n( 'design/admin/user/sessions' )} <a href={'user/logout'|ezurl}>{'Sign out'|i18n( 'design/admin/user/sessions' )}</a></p>
        {else}
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="RevokeSessionButton" value="{$s.id}">{'Sign out this session'|i18n( 'design/admin/user/sessions' )}</button>
        {/if}
    </li>
{/foreach}
</ul>
{else}
<p class="a4-empty">{'No session is signed in.'|i18n( 'design/admin/user/sessions' )}</p>
{/if}
</section>

{if $sessions_own}
<section class="a4-card a4-section" aria-labelledby="sessions-mail-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sessions-mail-title"><span class="a4-icon a4-bi-envelope is-large" aria-hidden="true"></span>{'Sign-in alerts'|i18n( 'design/admin/user/sessions' )}</h2></div>
    <div class="a4-card-body a4-stack">
        {if $sessions_mail_setting}
        <label class="a4-check"><input type="checkbox" name="SignInMail" value="1"{if $sessions_mail} checked="checked"{/if} /> {'E-mail me when my account signs in from a new device or browser'|i18n( 'design/admin/user/sessions' )}</label>
        <p class="a4-help">{'Sent to %email. It tells you the device, the approximate place and the time, and links to this page.'|i18n( 'design/admin/user/sessions',, hash( '%email', $sessions_user.email ) )|wash}</p>
        <div><button type="submit" class="a4-btn" name="SaveMailButton" value="1">{'Save'|i18n( 'design/admin/user/sessions' )}</button></div>
        {else}
        <p class="a4-meta">{'This site does not send sign-in alerts.'|i18n( 'design/admin/user/sessions' )}</p>
        {/if}
    </div>
</section>
{/if}

</form>

<section class="a4-card is-flush a4-section" aria-labelledby="sessions-ended-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sessions-ended-title">{'Recently ended'|i18n( 'design/admin/user/sessions' )}</h2>
        <p class="a4-meta">{'Sessions that ended in the last %days days.'|i18n( 'design/admin/user/sessions',, hash( '%days', $sessions_retention_days ) )}</p></div>
    {if $sessions_ended|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="sessions-ended-title" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Device'|i18n( 'design/admin/user/sessions' )}</th>
            <th scope="col">{'Address'|i18n( 'design/admin/user/sessions' )}</th>
            <th scope="col">{'Signed in'|i18n( 'design/admin/user/sessions' )}</th>
            <th scope="col">{'Ended'|i18n( 'design/admin/user/sessions' )}</th>
            <th scope="col">{'How'|i18n( 'design/admin/user/sessions' )}</th>
        </tr></thead>
        <tbody>
        {foreach $sessions_ended as $s}
        <tr>
            <td><span class="a4-icon a4-bi-{first_set( $device_icons[$s.device], 'globe' )|wash}" aria-hidden="true"></span> {$s.label|wash}</td>
            <td>{$s.ip|wash}</td>
            <td class="a4-nowrap">{$s.created|l10n( 'shortdatetime' )}</td>
            <td class="a4-nowrap">{if $s.ended}{$s.ended|l10n( 'shortdatetime' )}{else}{$s.last_active|l10n( 'shortdatetime' )}{/if}</td>
            <td>{if $s.end_reason}{first_set( $reasons[$s.end_reason], $s.end_reason )|wash}{else}{'Expired'|i18n( 'design/admin/user/sessions' )}{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <p class="a4-empty">{'None.'|i18n( 'design/admin/user/sessions' )}</p>
    {/if}
</section>

{/if}
</div>
{undef $device_icons $device_names $methods $reasons $others}
