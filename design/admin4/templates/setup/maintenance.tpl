{* Setup > Maintenance: take the public site offline for a maintenance window and back (expMaintenance).

   The page leads with the state - online, or in maintenance with why, by whom, since and until when - and the one
   action that state calls for. Offline, it then says what visitors are shown and who still gets in; online, it is
   the form for the next window: the message, the expected duration, who still gets in, a preview of the page
   visitors will get, and a confirmation before the site goes offline. Below: the same from a shell, the recent
   changes from the audit, and how it works.

   The same file is in design/admin and design/admin4, so every administration design resolves it. It uses the shared
   admin components (components.css) and stylesheets/modules/setup.css (.a4-setup-maint).
   Everything works without javascript: both buttons submit the form, the confirmation is a required checkbox, the
   folding sections are <details>. The script below adds the duration presets, the live preview, the address check
   and the copy buttons. Form field names, button names and the action are those of the earlier page. *}

{* A view class from before the redesign (a server that has not loaded the new one yet) hands over only the
   state; the page then shows the state and the form, without the preview, the history and the commands. *}
{if is_set( $maintenance_info )|not}
    {def $maintenance_info = hash( 'on', $maintenance_on, 'reason', cond( and( $maintenance_on, is_set( $maintenance.reason ) ), $maintenance.reason, 'manual' ),
                                   'by', cond( is_set( $maintenance.by ), $maintenance.by, '' ),
                                   'since', cond( is_set( $maintenance.since ), $maintenance.since, 0 ),
                                   'until', cond( is_set( $maintenance.until ), $maintenance.until, 0 ),
                                   'lease', 0, 'running_text', '', 'remaining_text', '', 'overdue', false(), 'overdue_text', '',
                                   'message', cond( is_set( $maintenance.message ), $maintenance.message, '' ),
                                   'custom_message', and( is_set( $maintenance.message ), $maintenance.message|ne( '' ) ),
                                   'allow_ips', cond( is_set( $maintenance.allow_ips ), $maintenance.allow_ips, array() ),
                                   'allow_paths', cond( is_set( $maintenance.allow_paths ), $maintenance.allow_paths, array() ),
                                   'admin_open', true(), 'client_allowed', false(), 'run', '', 'page', '' )
         $maintenance_preview = false()
         $maintenance_history = false()
         $maintenance_commands = false()
         $maintenance_presets = array()
         $maintenance_max_minutes = 10080}
{/if}
{def $mi = $maintenance_info}

<div class="context-block a4-page a4-setup-maint">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Maintenance'|i18n( 'design/admin/setup/maintenance' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Takes the public site offline while you work on it: every visitor gets a maintenance page instead of the site, and the administration stays reachable so the site can be brought back from here.'|i18n( 'design/admin/setup/maintenance' )}</p>

{if $feedback}
<div class="a4-notice {if $feedback[0]}is-ok{else}is-bad{/if}" role="{if $feedback[0]}status{else}alert{/if}">
    <p><span class="a4-meta">[{currentdate()|l10n( shortdatetime )}]</span> {$feedback[1]|wash}</p>
    {if is_set( $feedback[2] )}<p>{$feedback[2]|wash}</p>{/if}
</div>
{/if}

<form name="maintenanceform" method="post" action={'/setup/maintenance/'|ezurl}>

{* The state, and the one action it calls for *}
<section class="a4-setup-state{if $mi.on} is-on{/if}" aria-labelledby="maintenance-state-title">
    <div>
        <div class="a4-setup-state-head">
            <span class="a4-dot" aria-hidden="true"></span>
            <h2 class="a4-setup-state-title" id="maintenance-state-title">{if $mi.on}{'The site is in maintenance mode'|i18n( 'design/admin/setup/maintenance' )}{else}{'The site is online'|i18n( 'design/admin/setup/maintenance' )}{/if}</h2>
            {if $mi.on}
                {if eq( $mi.reason, 'manual' )}<span class="a4-badge is-warn">{'Maintenance window'|i18n( 'design/admin/setup/maintenance' )}</span>
                {elseif eq( $mi.reason, 'setup' )}<span class="a4-badge is-info">{'Setup wizard running'|i18n( 'design/admin/setup/maintenance' )}</span>
                {elseif eq( $mi.reason, 'install' )}<span class="a4-badge is-info">{'An installation is running'|i18n( 'design/admin/setup/maintenance' )}</span>
                {else}<span class="a4-badge is-bad">{'Unreadable marker'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}
                {if $mi.overdue}<span class="a4-badge is-bad">{'Past the expected end'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}
            {/if}
        </div>
        <p class="a4-setup-state-text">{if $mi.on}{'Visitors get the maintenance page (503). The administration stays reachable.'|i18n( 'design/admin/setup/maintenance' )}{else}{'Visitors get the site as usual. Prepare a maintenance window below; nothing changes until you confirm it.'|i18n( 'design/admin/setup/maintenance' )}{/if}</p>
    </div>
    {if $mi.on}
    <div class="a4-setup-state-action">
        <button type="submit" class="a4-btn a4-btn-primary a4-btn-large" name="SwitchOffButton" value="1"><span class="a4-icon a4-bi-check-lg is-medium" aria-hidden="true"></span>{'Bring the site back online'|i18n( 'design/admin/setup/maintenance' )}</button>
        {if or( eq( $mi.reason, 'setup' ), eq( $mi.reason, 'install' ) )}
        <span class="a4-meta">{'An installation is still running: visitors would meet a half-built site.'|i18n( 'design/admin/setup/maintenance' )}</span>
        {/if}
    </div>
    <dl class="a4-facts">
        {if $mi.by|ne( '' )}<div><dt>{'Switched on by'|i18n( 'design/admin/setup/maintenance' )}</dt><dd>{$mi.by|wash}</dd></div>{/if}
        <div><dt>{'Since'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.since|gt(0)}{$mi.since|l10n( shortdatetime )}{if $mi.running_text|ne( '' )} <span class="a4-meta">({'for %duration'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.running_text ) )})</span>{/if}{else}&mdash;{/if}</dd></div>
        <div><dt>{'Expected back'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.until|gt(0)}{$mi.until|l10n( shortdatetime )}
                {if $mi.overdue}<br /><span class="a4-badge is-bad">{if $mi.overdue_text|ne( '' )}{'%duration ago, and it does not end by itself'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.overdue_text ) )}{else}{'Passed, and it does not end by itself'|i18n( 'design/admin/setup/maintenance' )}{/if}</span>{/if}
                {if $mi.remaining_text|ne( '' )} <span class="a4-meta">({'in %duration'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.remaining_text ) )})</span>{/if}
            {else}<span class="a4-muted">{'Not given'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</dd></div>
        {if $mi.lease|gt(0)}<div><dt>{'Ends unless the wizard is used again'|i18n( 'design/admin/setup/maintenance' )}</dt><dd>{$mi.lease|l10n( shortdatetime )}</dd></div>{/if}
        <div><dt>{'You'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.client_allowed}{'See the public site (%ip is allowed)'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}{else}{'See the maintenance page on the public site (%ip)'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}{/if}</dd></div>
    </dl>
    {/if}
</section>

{if $mi.on}
{* Offline: what visitors are told and who still gets in *}
<div class="a4-setup-plan">
    <div>
        <section class="a4-card" aria-labelledby="maintenance-told-title">
            <div class="a4-card-head"><h2 class="a4-h2" id="maintenance-told-title">{'What visitors are told'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
        <div class="a4-card-body">
            {if $mi.custom_message}<p>{$mi.message|wash}</p>{else}<p class="a4-muted">{'No message of its own: the page says its default text.'|i18n( 'design/admin/setup/maintenance' )}</p>{/if}
        </div>
        </section>
        <section class="a4-card" aria-labelledby="maintenance-who-title">
            <div class="a4-card-head"><h2 class="a4-h2" id="maintenance-who-title">{'Who still gets in'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
        <div class="a4-card-body">
            <ul class="a4-setup-access">
                {foreach $mi.allow_paths as $mt_path}
                <li><strong>{if or( eq( $mt_path, '/admin' ), eq( $mt_path, 'admin' ), eq( $mt_path, '/admin/' ) )}{'The administration'|i18n( 'design/admin/setup/maintenance' )}{else}{'Pages below'|i18n( 'design/admin/setup/maintenance' )}{/if}</strong> <code>{$mt_path|wash}</code></li>
                {/foreach}
                {foreach $mi.allow_ips as $mt_ip}
                <li><strong>{'Address'|i18n( 'design/admin/setup/maintenance' )}</strong> <code>{$mt_ip|wash}</code>{if eq( $mt_ip, $client_ip )} <span class="a4-badge is-info">{'You'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</li>
                {/foreach}
                {if and( $mi.allow_paths|count|eq(0), $mi.allow_ips|count|eq(0) )}
                <li><span class="a4-muted">{'Nobody: every page request gets the maintenance page.'|i18n( 'design/admin/setup/maintenance' )}</span></li>
                {/if}
            </ul>
        </div>
        </section>
    </div>
{else}
{* Online: the next window, step by step *}
<div class="a4-setup-plan">
    <section class="a4-card" aria-labelledby="maintenance-plan-title">
        <div class="a4-card-head"><h2 class="a4-h2" id="maintenance-plan-title">{'Plan the maintenance window'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
    <div class="a4-card-body">
        <ol class="a4-setup-formsteps">
            <li>
                <span class="a4-setup-step" aria-hidden="true">1</span>
                <div class="a4-setup-step-body">
                    <label for="MaintenanceMessage">{'Message for visitors (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <textarea class="a4-input" id="MaintenanceMessage" name="MaintenanceMessage" rows="3" maxlength="500" aria-describedby="maintenance-message-hint maintenance-message-count"></textarea>
                    <span class="a4-setup-hint" id="maintenance-message-hint">{'Left empty, the page says: %text'|i18n( 'design/admin/setup/maintenance',, hash( '%text', concat( '"', 'We are working on the site and will be back shortly.', '"' ) ) )|wash}</span>
                    <span class="a4-setup-counter a4-setup-js-only" id="maintenance-message-count" hidden></span>
                </div>
            </li>
            <li>
                <span class="a4-setup-step" aria-hidden="true">2</span>
                <div class="a4-setup-step-body">
                    <label for="MaintenanceMinutes">{'Expected duration in minutes (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <div class="a4-row">
                        <input type="number" class="a4-input a4-setup-minutes" min="0" max="{$maintenance_max_minutes}" step="1" inputmode="numeric" id="MaintenanceMinutes" name="MaintenanceMinutes" value="" aria-describedby="maintenance-minutes-hint maintenance-minutes-result" />
                        <div class="a4-setup-presets a4-setup-js-only" role="group" aria-label="{'Common durations'|i18n( 'design/admin/setup/maintenance' )|wash}" hidden>
                        {foreach $maintenance_presets as $mt_preset}
                            <button type="button" class="a4-setup-preset" data-minutes="{$mt_preset.minutes}" aria-pressed="false">{$mt_preset.label|wash}</button>
                        {/foreach}
                        </div>
                    </div>
                    <span class="a4-setup-hint" id="maintenance-minutes-result" aria-live="polite"></span>
                    <span class="a4-setup-hint" id="maintenance-minutes-hint">{'Visitors are told when to expect the site back, and search engines when to try again. Maintenance does not end by itself: you switch it off here.'|i18n( 'design/admin/setup/maintenance' )}</span>
                </div>
            </li>
            <li>
                <span class="a4-setup-step" aria-hidden="true">3</span>
                <div class="a4-setup-step-body">
                    <span class="a4-setup-step-title" id="maintenance-who-title">{'Who still gets in'|i18n( 'design/admin/setup/maintenance' )}</span>
                    <ul class="a4-setup-access" aria-labelledby="maintenance-who-title">
                        <li><strong>{'The administration'|i18n( 'design/admin/setup/maintenance' )}</strong> <code>/admin</code> <span class="a4-meta">{'always, so the site can be brought back from here'|i18n( 'design/admin/setup/maintenance' )}</span></li>
                    </ul>
                    <label class="a4-check" for="MaintenanceAllowMe"><input type="checkbox" id="MaintenanceAllowMe" name="MaintenanceAllowMe" value="1" /> <span>{'Let my own address (%ip) still see the site'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}<br /><span class="a4-setup-hint">{'To check your work on the public site while everyone else gets the maintenance page.'|i18n( 'design/admin/setup/maintenance' )}</span></span></label>
                    <label for="MaintenanceAllowIPs">{'Other addresses that still see the site (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <textarea class="a4-input is-code" id="MaintenanceAllowIPs" name="MaintenanceAllowIPs" rows="2" spellcheck="false" autocomplete="off" aria-describedby="maintenance-ips-hint maintenance-ip-check"></textarea>
                    <span class="a4-setup-hint" id="maintenance-ips-hint">{'IPv4 or IPv6 addresses, separated by commas, spaces or new lines; for example the testers of your team. Ranges are not supported.'|i18n( 'design/admin/setup/maintenance' )}</span>
                    <p class="a4-setup-ipcheck" id="maintenance-ip-check" aria-live="polite" hidden></p>
                </div>
            </li>
        </ol>

        {* The guard: a required checkbox, so the browser refuses to send the form without it, script or not *}
        <div class="a4-setup-danger-box">
            <label class="a4-check" for="maintenance-confirm"><input type="checkbox" id="maintenance-confirm" required="required" /> <span>{'I understand that the public site goes offline for every visitor not listed above, until I switch maintenance off again.'|i18n( 'design/admin/setup/maintenance' )}</span></label>
            <div class="a4-setup-confirm-row">
                <button type="submit" class="a4-btn a4-btn-danger a4-btn-large" name="SwitchOnButton" value="1"><span class="a4-icon a4-bi-power is-medium" aria-hidden="true"></span>{'Take the site offline'|i18n( 'design/admin/setup/maintenance' )}</button>
                <span class="a4-meta">{'Also empties the caches that answer ahead of the site.'|i18n( 'design/admin/setup/maintenance' )}</span>
            </div>
        </div>
    </div>
    </section>
{/if}

    {* What visitors get: the real page, filled in, in a frame that runs none of its scripts *}
    {if $maintenance_preview}
    <section class="a4-card a4-setup-preview" aria-labelledby="maintenance-preview-title">
        <div class="a4-section-head">
            <h2 class="a4-h2" id="maintenance-preview-title">{if $mi.on}{'What visitors see now'|i18n( 'design/admin/setup/maintenance' )}{else}{'Preview: what visitors will see'|i18n( 'design/admin/setup/maintenance' )}{/if}</h2>
        </div>
        <div class="a4-setup-frame-wrap">
            <div class="a4-setup-frame-bar"><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><code>503 &middot; {$maintenance_preview.file|wash}</code></div>
            {* allow-same-origin only: the page loads its fonts, but none of its scripts run *}
            <iframe class="a4-setup-frame" id="maintenance-preview-frame" sandbox="allow-same-origin" loading="lazy" referrerpolicy="no-referrer"
                    {if $mi.on|not}data-template="{$maintenance_preview.template|wash}" data-message-slot="{$maintenance_preview.message_slot|wash}" data-until-slot="{$maintenance_preview.until_slot|wash}" data-default-message="{$maintenance_preview.default_message|wash}" data-default-until="{$maintenance_preview.default_until|wash}"{/if}
                    title="{'Preview of the maintenance page'|i18n( 'design/admin/setup/maintenance' )|wash}"
                    srcdoc="{$maintenance_preview.html|wash}"></iframe>
        </div>
        <p class="a4-meta">{if $mi.on}{'Served with status 503 and never cached; images, styles and scripts of the site are still served.'|i18n( 'design/admin/setup/maintenance' )}{else}<span class="a4-setup-js-only" hidden>{'Follows what you type.'|i18n( 'design/admin/setup/maintenance' )} </span>{'The page comes from %file.'|i18n( 'design/admin/setup/maintenance',, hash( '%file', $maintenance_preview.file|wash ) )}{/if}</p>
    </section>
    {/if}
</div>

{* The same from a shell, for a window opened before a deploy or when the administration cannot be reached *}
{if $maintenance_commands}
<details class="a4-fold" id="maintenance-shell">
    <summary>
        <h2 class="a4-h2">{'From a shell'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="a4-meta">{'The same switch, run in the installation directory'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="a4-fold-body a4-stack">
        <ul class="a4-setup-cmds">
            {foreach $maintenance_commands as $mt_cmd}
            <li class="a4-setup-cmd-row">
                <span class="a4-setup-cmd-label" id="maintenance-cmd-{$mt_cmd.key|wash}">{$mt_cmd.label|wash}</span>
                <code class="a4-setup-cmd" id="maintenance-cmd-{$mt_cmd.key|wash}-text" aria-labelledby="maintenance-cmd-{$mt_cmd.key|wash}">{$mt_cmd.command|wash}</code>
                <button type="button" class="a4-btn a4-btn-small a4-setup-copy a4-setup-js-only" data-copy-from="maintenance-cmd-{$mt_cmd.key|wash}-text" hidden
                        aria-label="{'Copy the command: %what'|i18n( 'design/admin/setup/maintenance',, hash( '%what', $mt_cmd.label ) )|wash}">{'Copy'|i18n( 'design/admin/setup/maintenance' )}</button>
            </li>
            {/foreach}
        </ul>
        <p class="a4-meta">{'Switch on takes --message="...", --until=30m, 2h or a date and time, and --allow-ip=1.2.3.4,5.6.7.8. Run as root, add --allow-root-user. ./console exp:maintenance on|off|status does the same.'|i18n( 'design/admin/setup/maintenance' )}</p>
    </div>
</details>
{/if}

{* The latest changes, from the audit *}
{if $maintenance_history}
<details class="a4-fold" id="maintenance-history" open="open">
    <summary>
        <h2 class="a4-h2" id="maintenance-history-title">{'Recent changes'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="a4-meta">{'From the audit, newest first'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="a4-fold-body a4-stack">
    {if and( $maintenance_history.available, $maintenance_history.rows|count|gt(0) )}
        <div class="a4-table-wrap" role="region" aria-labelledby="maintenance-history-title" tabindex="0">
        <table class="a4-table">
        <thead><tr>
            <th scope="col">{'When'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Change'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Reason'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Expected back'|i18n( 'design/admin/setup/maintenance' )}</th>
        </tr></thead>
        <tbody>
        {foreach $maintenance_history.rows as $mt_row}
        <tr>
            <td class="a4-nowrap" data-label="{'When'|i18n( 'design/admin/setup/maintenance' )|wash}"><a href={concat( 'audit/event/', $mt_row.id )|ezurl}>{$mt_row.time|l10n( shortdatetime )}</a></td>
            <td data-label="{'Change'|i18n( 'design/admin/setup/maintenance' )|wash}">{if eq( $mt_row.mode, 'on' )}<span class="a4-badge is-warn">{'Switched on'|i18n( 'design/admin/setup/maintenance' )}</span>{elseif eq( $mt_row.mode, 'off' )}<span class="a4-badge is-ok">{'Switched off'|i18n( 'design/admin/setup/maintenance' )}</span>{else}<span class="a4-badge is-info">{'Changed'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</td>
            <td data-label="{'Reason'|i18n( 'design/admin/setup/maintenance' )|wash}">{if eq( $mt_row.reason, 'manual' )}{'Maintenance window'|i18n( 'design/admin/setup/maintenance' )}{elseif eq( $mt_row.reason, 'setup' )}{'Installation'|i18n( 'design/admin/setup/maintenance' )}{elseif eq( $mt_row.reason, '' )}&mdash;{else}{'Unknown'|i18n( 'design/admin/setup/maintenance' )}{/if}</td>
            <td data-label="{'By'|i18n( 'design/admin/setup/maintenance' )|wash}">{if $mt_row.login|ne( '' )}{$mt_row.login|wash}{else}&mdash;{/if}{if $mt_row.cli} <span class="a4-meta">({'shell'|i18n( 'design/admin/setup/maintenance' )})</span>{/if}</td>
            <td class="a4-nowrap" data-label="{'Expected back'|i18n( 'design/admin/setup/maintenance' )|wash}">{if $mt_row.until|gt(0)}{$mt_row.until|l10n( shortdatetime )}{else}&mdash;{/if}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
    {elseif $maintenance_history.available}
        <p class="a4-empty">{'Maintenance has not been switched on or off since the audit began.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {elseif eq( $maintenance_history.why, 'no-access' )}
        <p class="a4-empty">{'Reading the changes needs the audit/read right for the system channel.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {elseif eq( $maintenance_history.why, 'no-index' )}
        <p class="a4-empty">{'The audit index is not available, so the changes cannot be listed here.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {else}
        <p class="a4-empty">{'The audit is not available, so the changes cannot be listed here.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {/if}
    {if $maintenance_history.console|ne( '' )}
        <p><a href={$maintenance_history.console|ezurl}>{'Every change in the audit console'|i18n( 'design/admin/setup/maintenance' )}</a></p>
    {/if}
    </div>
</details>
{/if}

<details class="a4-fold" id="maintenance-how">
    <summary>
        <h2 class="a4-h2">{'How it works'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="a4-meta">{'What is served, what is not, and where the state is kept'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="a4-fold-body a4-stack">
        <ul class="a4-plain">
            <li>{'In maintenance mode every page request is answered with the maintenance page (503, never cached) before the settings or the database are used; images, styles and scripts are still served. Use it while you change the site, or it is switched on by the kickstarter while it installs.'|i18n( 'design/admin/setup/maintenance' )}</li>
            <li>{'The administration stays reachable while the site is offline, so it can be switched off again here.'|i18n( 'design/admin/setup/maintenance' )} {'An administration reached by its own host name rather than /admin is not let through; switch off from a shell then.'|i18n( 'design/admin/setup/maintenance' )}</li>
            <li>{'The state is the file %file: it exists while maintenance is on. Switching on and off is recorded in the audit.'|i18n( 'design/admin/setup/maintenance',, hash( '%file', 'var/maintenance.json' ) )}</li>
            <li>{'The page is errors/maintenance.html of the first active extension that has one, else share/maintenance.html.'|i18n( 'design/admin/setup/maintenance' )}</li>
        </ul>
    </div>
</details>

</form>

<p class="a4-sr-only" id="maintenance-copy-status" role="status" aria-live="polite"></p>

</div></div></div>
</div>
<script type="text/javascript">
var expMaintenanceText = {ldelim}
    back: '{'Expected back around %local (%utc UTC).'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    tooLong: '{'At most %max minutes (a week).'|i18n( 'design/admin/setup/maintenance',, hash( '%max', $maintenance_max_minutes ) )|wash( javascript )}',
    chars: '{'%count of 500 characters'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    notAddresses: '{'Not addresses, these would be left out: %list'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copied: '{'Copied'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copiedLong: '{'The command is on the clipboard.'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copyFailed: '{'Could not copy. Select the text and copy it by hand.'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var root = document.querySelector( '.a4-setup-maint' );
    if ( !root ) return;
    function each( selector, fn ) { var n = root.querySelectorAll( selector ), i; for ( i = 0; i < n.length; i++ ) fn( n[i] ); }
    function tr( name, values ) { var s = expMaintenanceText[name], k; for ( k in values || {} ) s = s.split( '%' + k ).join( values[k] ); return s; }
    function text( el, value ) { if ( el ) el.textContent = value; }
    function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
    function escapeHtml( s ) { return String( s ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#039;' ); }

    each( '.a4-setup-js-only', function ( el ) { el.hidden = false; } );

    var message = document.getElementById( 'MaintenanceMessage' );
    var minutes = document.getElementById( 'MaintenanceMinutes' );
    var minutesResult = document.getElementById( 'maintenance-minutes-result' );
    var count = document.getElementById( 'maintenance-message-count' );
    var ips = document.getElementById( 'MaintenanceAllowIPs' );
    var ipCheck = document.getElementById( 'maintenance-ip-check' );
    var frame = document.getElementById( 'maintenance-preview-frame' );
    var preview = frame && frame.hasAttribute( 'data-template' ) ? {
        template: frame.getAttribute( 'data-template' ), messageSlot: frame.getAttribute( 'data-message-slot' ),
        untilSlot: frame.getAttribute( 'data-until-slot' ), defaultMessage: frame.getAttribute( 'data-default-message' ),
        defaultUntil: frame.getAttribute( 'data-default-until' ) } : null;

    function minutesValue() {
        var m = minutes ? parseInt( minutes.value, 10 ) : 0;
        return isNaN( m ) || m < 0 ? 0 : m;
    }

    // The preview: the real page with what is typed in the message and the expected end
    var timer = null;
    function renderPreview() {
        if ( !frame || !preview ) return;
        var msg = message ? message.value.trim() : '';
        var m = Math.min( minutesValue(), 10080 );
        var until = preview.defaultUntil;
        if ( m > 0 ) {
            var d = new Date( Date.now() + m * 60000 );
            until = 'Expected back at ' + pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) + ' UTC.';
        }
        msg = msg !== '' ? msg : preview.defaultMessage;
        // In place when the frame's page can be reached (it is same-origin and runs no script): loading it again
        // for every key would abort its font downloads and flash. The page shows the default texts at first.
        var doc = null;
        try { doc = frame.contentDocument; } catch ( e ) {}
        if ( doc && doc.body && doc.readyState === 'complete' && doc.URL === 'about:srcdoc' ) {
            replaceText( doc.body, shown.msg, msg );
            replaceText( doc.body, shown.until, until );
        } else {
            frame.setAttribute( 'srcdoc', preview.template.split( preview.messageSlot ).join( escapeHtml( msg ) )
                                                          .split( preview.untilSlot ).join( escapeHtml( until ) ) );
        }
        shown = { msg: msg, until: until };
    }
    var shown = { msg: preview ? preview.defaultMessage : '', until: preview ? preview.defaultUntil : '' };
    function replaceText( node, from, to ) {
        if ( from === to || from === '' ) return;
        var walker = node.ownerDocument.createTreeWalker( node, 4 /* NodeFilter.SHOW_TEXT */ );
        while ( walker.nextNode() ) {
            var t = walker.currentNode;
            if ( t.nodeValue.indexOf( from ) !== -1 ) t.nodeValue = t.nodeValue.split( from ).join( to );
        }
    }
    function schedulePreview() { window.clearTimeout( timer ); timer = window.setTimeout( renderPreview, 120 ); }

    function updateMinutes() {
        var m = minutesValue();
        each( '.a4-setup-preset', function ( b ) { b.setAttribute( 'aria-pressed', String( parseInt( b.getAttribute( 'data-minutes' ), 10 ) === m ) ); } );
        if ( !minutesResult ) return;
        if ( m > 10080 ) { text( minutesResult, tr( 'tooLong' ) ); return; }
        if ( m === 0 ) { text( minutesResult, '' ); return; }
        var d = new Date( Date.now() + m * 60000 );
        text( minutesResult, tr( 'back', { local: d.toLocaleString( document.documentElement.lang || undefined, { weekday: 'short', hour: '2-digit', minute: '2-digit' } ),
                                           utc: pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) } ) );
    }
    if ( minutes ) minutes.addEventListener( 'input', function () { updateMinutes(); schedulePreview(); } );
    each( '.a4-setup-preset', function ( b ) {
        b.addEventListener( 'click', function () {
            if ( !minutes ) return;
            minutes.value = b.getAttribute( 'data-minutes' );
            updateMinutes(); schedulePreview();
        } );
    } );

    function updateCount() { if ( message && count ) text( count, tr( 'chars', { count: message.value.length } ) ); }
    if ( message ) message.addEventListener( 'input', function () { updateCount(); schedulePreview(); } );

    // The same check as the server's: what is not an address is left out, so it is said before sending
    var v4 = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;
    function isAddress( s ) {
        if ( v4.test( s ) ) return true;
        if ( s.indexOf( ':' ) === -1 || !/^[0-9a-fA-F:.]+$/.test( s ) ) return false;
        var tail = s.match( /:(\d+\.\d+\.\d+\.\d+)$/ );
        if ( tail && !v4.test( tail[1] ) ) return false;
        var parts = s.split( '::' );
        if ( parts.length > 2 ) return false;
        var groups = s.replace( /:\d+\.\d+\.\d+\.\d+$/, ':0:0' ).split( ':' ).filter( function ( g ) { return g !== ''; } );
        for ( var i = 0; i < groups.length; i++ ) if ( !/^[0-9a-fA-F]{1,4}$/.test( groups[i] ) ) return false;
        return parts.length === 2 ? groups.length < 8 : groups.length === 8;
    }
    function checkAddresses() {
        if ( !ips || !ipCheck ) return;
        var bad = ips.value.split( /[\s,;]+/ ).filter( function ( e ) { return e !== '' && !isAddress( e ); } );
        ipCheck.hidden = bad.length === 0;
        ips.setAttribute( 'aria-invalid', bad.length ? 'true' : 'false' );
        text( ipCheck, bad.length ? tr( 'notAddresses', { list: bad.join( ', ' ) } ) : '' );
    }
    if ( ips ) ips.addEventListener( 'input', checkAddresses );

    updateMinutes(); updateCount(); checkAddresses();

    // Copy buttons
    var copyStatus = document.getElementById( 'maintenance-copy-status' );
    function copyText( value, button ) {
        function done( ok ) {
            text( copyStatus, ok ? tr( 'copiedLong' ) : tr( 'copyFailed' ) );
            if ( !ok ) return;
            var label = button.textContent;
            text( button, tr( 'copied' ) );
            window.setTimeout( function () { text( button, label ); }, 1600 );
        }
        function fallback() {
            var area = document.createElement( 'textarea' );
            area.value = value; area.setAttribute( 'readonly', '' );
            area.style.position = 'fixed'; area.style.opacity = '0';
            document.body.appendChild( area ); area.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) {}
            document.body.removeChild( area );
            button.focus();
            return ok;
        }
        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, function () { done( fallback() ); } );
            return;
        }
        done( fallback() );
    }
    each( '.a4-setup-copy', function ( button ) {
        button.addEventListener( 'click', function () {
            var from = document.getElementById( button.getAttribute( 'data-copy-from' ) );
            copyText( from ? from.textContent : '', button );
        } );
    } );
})();
{/literal}
</script>
{undef $mi}
