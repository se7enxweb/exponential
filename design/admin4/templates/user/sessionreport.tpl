{* user/sessionreport: "Sign-in report" (kernel/private/classes/views/user/sessionreport.php, policy user/sessions_manage).
   The figures (sessions running, users signed in, today's sign-ins and failed attempts), the last 14 days as a table
   with bars, "Sign a user out everywhere", and the running sessions of every user with a button to sign one out.
   Variables: report_enabled, report_available, report_audit, report_sessions, report_total, report_users,
   report_limit, report_days, report_today, report_message, report_form_token, report_handler, report_patterns
   (expUserSessionReport::patterns(): repeated failures, one network on several accounts, locks, several countries),
   report_pattern_hours.
   No inline CSS: shared components only (.a4-figures, .a4-meter with .a4-pct-*, .a4-table). The same file is in
   design/admin and design/admin4. Guide: doc/eng-us/features/6.0/user-sessions.md *}
{def $methods = hash( 'password', 'Password'|i18n( 'design/admin/user/sessionreport' ),
                      'two_factor', 'Password and two-step code'|i18n( 'design/admin/user/sessionreport' ),
                      'rest', 'REST interface'|i18n( 'design/admin/user/sessionreport' ), 'soap', 'SOAP interface'|i18n( 'design/admin/user/sessionreport' ),
                      'webdav', 'WebDAV'|i18n( 'design/admin/user/sessionreport' ), 'single_sign_on', 'Single sign-on'|i18n( 'design/admin/user/sessionreport' ),
                      'unknown', 'Signed in before sessions were recorded'|i18n( 'design/admin/user/sessionreport' ) )}
<div class="a4-page a4-standalone" id="exp-user-sessionreport">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Sign-in report'|i18n( 'design/admin/user/sessionreport' )}</h1>
        <p class="a4-pagehead-sub">{'Who is signed in to the site, how often accounts sign in and fail to, and signing a user out everywhere. It works with every session handler.'|i18n( 'design/admin/user/sessionreport' )}</p>
    </div>
</div>

{if $report_message}
<div class="a4-notice is-{$report_message.level|wash}" role="status">
<p>{switch match=$report_message.key}
{case match='revoked'}{'The session was signed out. The device has to sign in again.'|i18n( 'design/admin/user/sessionreport' )}{/case}
{case match='signed_out'}{if $report_message.count|gt( 0 )}{'%login was signed out of %count sessions.'|i18n( 'design/admin/user/sessionreport',, hash( '%login', $report_message.login, '%count', $report_message.count ) )|wash}{else}{'%login had no running session.'|i18n( 'design/admin/user/sessionreport',, hash( '%login', $report_message.login ) )|wash}{/if}{/case}
{case match='no_user'}{'No account has that ID, username or e-mail address.'|i18n( 'design/admin/user/sessionreport' )}{/case}
{case match='current'}{'This is the session you are using. To end it, sign out.'|i18n( 'design/admin/user/sessionreport' )}{/case}
{case match='not_found'}{'That session had already ended.'|i18n( 'design/admin/user/sessionreport' )}{/case}
{case match='token'}{'The page had expired. Nothing was changed; please try again.'|i18n( 'design/admin/user/sessionreport' )}{/case}
{case}{/case}
{/switch}</p>
</div>
{/if}

{if $report_enabled|not}
<div class="a4-notice is-info" role="status"><p>{'Sessions are not recorded on this site (site.ini [UserSessionSettings] Tracking=disabled).'|i18n( 'design/admin/user/sessionreport' )}</p></div>
{elseif $report_available|not}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The table expuser_session is missing'|i18n( 'design/admin/user/sessionreport' )}</p>
    <p>{'Sessions are recorded once it is created: php update/common/scripts/6.0/createusersessiontable.php, or the database update of 6.0.15.'|i18n( 'design/admin/user/sessionreport' )}</p>
</div>
{else}

<section class="a4-section" aria-labelledby="report-figures-title">
<h2 class="a4-sr-only" id="report-figures-title">{'Now and today'|i18n( 'design/admin/user/sessionreport' )}</h2>
<ul class="a4-figures">
    <li class="a4-figure"><strong>{$report_total}</strong> <span>{'Sessions running'|i18n( 'design/admin/user/sessionreport' )}</span></li>
    <li class="a4-figure"><strong>{$report_users}</strong> <span>{'Users signed in'|i18n( 'design/admin/user/sessionreport' )}</span></li>
    <li class="a4-figure"><strong>{if $report_audit}{$report_today.signins}{else}{$report_today.sessions}{/if}</strong> <span>{'Sign-ins today'|i18n( 'design/admin/user/sessionreport' )}</span></li>
    {if $report_audit}<li class="a4-figure{if $report_today.failures|gt( 0 )} is-attention{/if}"><strong>{$report_today.failures}</strong> <span>{'Failed attempts today'|i18n( 'design/admin/user/sessionreport' )}</span></li>{/if}
</ul>
</section>

<section class="a4-card a4-section{if $report_patterns|count} is-attention{/if}" aria-labelledby="report-patterns-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="report-patterns-title"><span class="a4-icon a4-bi-exclamation-triangle is-large" aria-hidden="true"></span>{'Suspicious activity in the last %hours hours'|i18n( 'design/admin/user/sessionreport',, hash( '%hours', $report_pattern_hours ) )} <span class="a4-badge">{$report_patterns|count}</span></h2></div>
    <div class="a4-card-body">
    {if $report_patterns|count}
    <ol class="a4-timeline" role="list">
    {foreach $report_patterns as $p}
        <li class="{cond( or( eq( $p.kind, 'network' ), eq( $p.kind, 'locked' ) ), 'is-bad', 'is-warn' )}"><div class="a4-timeline-body">
            {switch match=$p.kind}
            {case match='network'}<strong>{'One network failed to sign in to %accounts accounts (%count attempts)'|i18n( 'design/admin/user/sessionreport',, hash( '%accounts', $p.accounts, '%count', $p.count ) )|wash}</strong> <span class="a4-meta" translate="no">{$p.network|wash}</span>{/case}
            {case match='locked'}<strong>{'Account locked after failed sign-ins'|i18n( 'design/admin/user/sessionreport' )}</strong>{/case}
            {case match='failures'}<strong>{'%count failed sign-ins on one account'|i18n( 'design/admin/user/sessionreport',, hash( '%count', $p.count ) )|wash}</strong>{/case}
            {case match='countries'}<strong>{'Signed in from %count countries at once'|i18n( 'design/admin/user/sessionreport',, hash( '%count', $p.count ) )|wash}</strong> <span class="a4-meta" translate="no">{$p.countries|implode( ', ' )|wash}</span>{/case}
            {case}{/case}
            {/switch}
            {if $p.user_id|gt( 0 )}
            <span class="a4-meta">{if $p.name}{$p.name|wash}{else}{$p.login|wash}{/if}</span>
            <a href={concat( 'user/security/', $p.user_id )|ezurl}>{'Security activity'|i18n( 'design/admin/user/sessionreport' )}</a>
            <a href={concat( 'user/sessions/', $p.user_id )|ezurl}>{'Sessions'|i18n( 'design/admin/user/sessionreport' )}</a>
            {/if}
        </div></li>
    {/foreach}
    </ol>
    {elseif $report_audit|not}
    <p class="a4-empty">{'Failed sign-ins are read from the user audit log, which is not available here.'|i18n( 'design/admin/user/sessionreport' )}</p>
    {else}
    <p class="a4-empty">{'Nothing suspicious: no account with repeated failed sign-ins, no network trying several accounts, no account signed in from several countries.'|i18n( 'design/admin/user/sessionreport' )}</p>
    {/if}
    </div>
</section>

<form method="post" action={'user/sessionreport'|ezurl}>
<input type="hidden" name="ReportToken" value="{$report_form_token|wash}" />

<section class="a4-card a4-section" aria-labelledby="report-signout-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="report-signout-title"><span class="a4-icon a4-bi-box-arrow-right is-large" aria-hidden="true"></span>{'Sign a user out everywhere'|i18n( 'design/admin/user/sessionreport' )}</h2></div>
    <div class="a4-card-body a4-stack">
        <p class="a4-help">{'Every session of the account ends at its next request, also those kept by Remember me. Use it when an account may be in the wrong hands; then reset its password.'|i18n( 'design/admin/user/sessionreport' )}</p>
        <div class="a4-searchrow">
            <label class="a4-sr-only" for="report-signout-user">{'User ID, username or e-mail address'|i18n( 'design/admin/user/sessionreport' )}</label>
            <input class="a4-input" type="text" id="report-signout-user" name="SignOutUser" autocomplete="off" placeholder="{'User ID, username or e-mail address'|i18n( 'design/admin/user/sessionreport' )|wash}" />
            <button type="submit" class="a4-btn a4-btn-outline-danger" name="ForceSignOutButton" value="1">{'Sign out everywhere'|i18n( 'design/admin/user/sessionreport' )}</button>
        </div>
    </div>
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="report-active-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="report-active-title"><span class="a4-icon a4-bi-laptop is-large" aria-hidden="true"></span>{'Signed in now'|i18n( 'design/admin/user/sessionreport' )} <span class="a4-badge">{$report_total}</span></h2>
        {if $report_total|gt( $report_limit )}<p class="a4-meta">{'The %limit most recently used are shown.'|i18n( 'design/admin/user/sessionreport',, hash( '%limit', $report_limit ) )}</p>{/if}</div>
    {if $report_sessions|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="report-active-title" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'User'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col">{'Device'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col">{'Address'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col">{'Signed in'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col">{'Last active'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/user/sessionreport' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $report_sessions as $s}
        <tr>
            <td><a href={concat( 'user/sessions/', $s.user_id )|ezurl}>{if $s.name}{$s.name|wash}{else}{$s.login|wash}{/if}</a> <span class="a4-meta" translate="no">{$s.login|wash}</span></td>
            <td>{$s.label|wash}{if $s.current} <span class="a4-badge is-ok">{'This device'|i18n( 'design/admin/user/sessionreport' )}</span>{/if}</td>
            <td>{if $s.ip}{$s.ip_full|wash}{else}<span class="a4-meta">{'Not recorded'|i18n( 'design/admin/user/sessionreport' )}</span>{/if}{if $s.location} <span class="a4-meta">{$s.location|wash}</span>{/if}</td>
            <td class="a4-nowrap">{$s.created|l10n( 'shortdatetime' )} <span class="a4-meta">{first_set( $methods[$s.method], $s.method )|wash}</span></td>
            <td class="a4-nowrap">{$s.last_active|l10n( 'shortdatetime' )}</td>
            <td>{if $s.current|not}<button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="RevokeSessionButton" value="{$s.id}">{'Sign out'|i18n( 'design/admin/user/sessionreport' )}</button>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <p class="a4-empty">{'No session is signed in.'|i18n( 'design/admin/user/sessionreport' )}</p>
    {/if}
</section>
</form>

<section class="a4-card is-flush a4-section" aria-labelledby="report-days-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="report-days-title"><span class="a4-icon a4-bi-bar-chart-line is-large" aria-hidden="true"></span>{'The last %days days'|i18n( 'design/admin/user/sessionreport',, hash( '%days', $report_days|count ) )}</h2>
        {if $report_audit|not}<p class="a4-meta">{'Sign-ins and failed attempts are counted from the user audit log, which is not there; the new sessions are.'|i18n( 'design/admin/user/sessionreport' )}</p>{/if}</div>
    <div class="a4-table-wrap" role="region" aria-labelledby="report-days-title" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Day'|i18n( 'design/admin/user/sessionreport' )}</th>
            {if $report_audit}<th scope="col">{'Sign-ins'|i18n( 'design/admin/user/sessionreport' )}</th>
            <th scope="col">{'Failed attempts'|i18n( 'design/admin/user/sessionreport' )}</th>{/if}
            <th scope="col">{'New sessions'|i18n( 'design/admin/user/sessionreport' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report_days as $d}
        <tr>
            <th scope="row" class="a4-nowrap">{$d.time|l10n( 'shortdate' )}</th>
            {if $report_audit}<td><span class="a4-num">{$d.signins}</span> <span class="a4-meter is-ok" aria-hidden="true"><span class="a4-pct-{$d.signins_pct}"></span></span></td>
            <td><span class="a4-num">{$d.failures}</span> <span class="a4-meter is-bad" aria-hidden="true"><span class="a4-pct-{$d.failures_pct}"></span></span></td>{/if}
            <td><span class="a4-num">{$d.sessions}</span> <span class="a4-meter is-info" aria-hidden="true"><span class="a4-pct-{$d.sessions_pct}"></span></span></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
<p class="a4-meta">{'Session handler: %handler. The sessions are read from the table expuser_session, so the report works with every handler.'|i18n( 'design/admin/user/sessionreport',, hash( '%handler', $report_handler ) )|wash}</p>

{/if}
</div>
{undef $methods}
