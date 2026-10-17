{* Setup > Log files > Health (setup/logshealth; doc/features/6.0/log-viewer.md, "Health"). Variables: log_health
   (expLogHealth::summary(): total, logs_bytes, archive_bytes, budget, share, percent, state, fastest, over_limit,
   not_writable, owners, mixed_owners, rows), log_health_errors (key => time, time_text, message, source, permalink),
   log_health_settings (key => list of name, value, file, section, var), log_health_links (key => the permalink of the
   log), log_health_manager (bool: the log manager is there), log_can_manage (bool: setup/managelogs).
   Read only. The same file is in design/admin and design/admin4. *}
{def $log_setting_names = hash( 'max_size', 'Largest size'|i18n( 'design/admin/setup/logs' ),
                                'keep', 'Archives kept'|i18n( 'design/admin/setup/logs' ),
                                'compress', 'Archives compressed'|i18n( 'design/admin/setup/logs' ),
                                'max_age_days', 'Archives removed after'|i18n( 'design/admin/setup/logs' ) )}
<div class="context-block a4-page a4-setup-logs a4-setup-loghealth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Log health'|i18n( 'design/admin/setup/logs' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-meta">{'How much disk the logs use, which grow fastest, which are over their limit or cannot be written by the web server, the newest error of each, and the rotation settings in effect. Nothing on this page changes a file.'|i18n( 'design/admin/setup/logs' )}</p>

{if $log_health_manager|not}
<div class="a4-notice is-warn" role="status">{'The log manager is not available, so sizes, limits and archives cannot be listed.'|i18n( 'design/admin/setup/logs' )}</div>
{/if}

{* ---- disk use against the budget ---- *}
<section class="a4-card a4-setup-loghealth-disk {switch match=$log_health.state}{case match='bad'}is-bad{/case}{case match='warn'}is-attention{/case}{case}is-ok{/case}{/switch}" aria-labelledby="loghealth-disk-title">
    <h2 id="loghealth-disk-title">{'Disk use'|i18n( 'design/admin/setup/logs' )}</h2>
    <p class="a4-setup-loghealth-total">
        <strong>{$log_health.total|si( byte )}</strong>
        {if $log_health.budget|gt( 0 )}
            {'of a budget of %budget (%percent %)'|i18n( 'design/admin/setup/logs',, hash( '%budget', $log_health.budget|si( byte ), '%percent', $log_health.percent ) )|wash}
        {else}
            {'(no budget set)'|i18n( 'design/admin/setup/logs' )}
        {/if}
    </p>
    {if $log_health.budget|gt( 0 )}
    <meter class="a4-setup-loghealth-meter" min="0" max="{$log_health.budget|wash}" low="{mul( $log_health.budget, 0.8 )|int}" high="{$log_health.budget|wash}" optimum="0" value="{$log_health.total|wash}" aria-labelledby="loghealth-disk-title">{$log_health.percent|wash} %</meter>
    {/if}
    <ul class="a4-setup-inline a4-meta">
        <li>{'Current files: %size'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_health.logs_bytes|si( byte ) ) )|wash}</li>
        <li>{'Archives: %size'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_health.archive_bytes|si( byte ) ) )|wash}</li>
        <li>{'Budget: logviewer.ini [HealthSettings] DiskBudget'|i18n( 'design/admin/setup/logs' )}</li>
    </ul>
</section>

{* ---- what needs a look ---- *}
<div class="a4-setup-loghealth-grid">
<section class="a4-card" aria-labelledby="loghealth-fast-title">
    <h2 id="loghealth-fast-title">{'Fastest growing'|i18n( 'design/admin/setup/logs' )}</h2>
    {if $log_health.fastest|count}
    <ol class="a4-setup-loghealth-list">
    {foreach $log_health.fastest as $log_row}
        <li><a href={$log_health_links[ $log_row.key ]|ezurl}>{$log_row.key|wash}</a>
            <span class="a4-meta">{'%size per hour'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_row.growth|int|si( byte ) ) )|wash}</span></li>
    {/foreach}
    </ol>
    {else}
    <p class="a4-meta">{'No log is known to grow yet: growth is measured between rotations.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
</section>

<section class="a4-card{if $log_health.over_limit|count} is-attention{/if}" aria-labelledby="loghealth-limit-title">
    <h2 id="loghealth-limit-title">{'Over their size limit'|i18n( 'design/admin/setup/logs' )}</h2>
    {if $log_health.over_limit|count}
    <ul class="a4-setup-loghealth-list">
    {foreach $log_health.over_limit as $log_row}
        <li><a href={$log_health_links[ $log_row.key ]|ezurl}>{$log_row.key|wash}</a>
            <span class="a4-badge is-warn">{'%size of %limit'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_row.size|si( byte ), '%limit', $log_row.limits.max_size|si( byte ) ) )|wash}</span>
            {if $log_can_manage}<a class="a4-meta" href={concat( 'setup/logsmanage/', $log_row.key )|ezurl}>{'Manage'|i18n( 'design/admin/setup/logs' )}</a>{/if}</li>
    {/foreach}
    </ul>
    {else}
    <p class="a4-meta">{'Every log is within its limit.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
</section>

<section class="a4-card{if $log_health.not_writable|count} is-bad{/if}" aria-labelledby="loghealth-write-title">
    <h2 id="loghealth-write-title">{'Not writable by the web server'|i18n( 'design/admin/setup/logs' )}</h2>
    {if $log_health.not_writable|count}
    <p>{'The web server cannot append to these files, so what it logs is lost. They were usually created by a command line or Exponential Velocity running as another user.'|i18n( 'design/admin/setup/logs' )}</p>
    <ul class="a4-setup-loghealth-list">
    {foreach $log_health.not_writable as $log_row}
        <li><a href={$log_health_links[ $log_row.key ]|ezurl}>{$log_row.key|wash}</a>
            {if is_set( $log_row.owner )}<span class="a4-tag is-mono">{$log_row.owner|wash}{if and( is_set( $log_row.owner_group ), $log_row.owner_group )}:{$log_row.owner_group|wash}{/if}</span>{/if}</li>
    {/foreach}
    </ul>
    {else}
    <p class="a4-meta">{'The web server can write every log.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
    {if $log_health.mixed_owners}
    <div class="a4-notice is-warn" role="status">{'The log files belong to different users (%owners). A file created by one user may not be writable by the other; give them one owner, or a shared group with group write.'|i18n( 'design/admin/setup/logs',, hash( '%owners', $log_health.owners|implode( ', ' ) ) )|wash}</div>
    {/if}
</section>
</div>

{* ---- every log: size, archives, newest error ---- *}
<section class="a4-card" aria-labelledby="loghealth-logs-title">
    <h2 id="loghealth-logs-title">{'Every log'|i18n( 'design/admin/setup/logs' )}</h2>
    {if $log_health.rows|count}
    <div class="a4-table-wrap">
    <table class="a4-table a4-setup-loghealth-table">
        <thead><tr>
            <th scope="col">{'Log'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Size'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Archives'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Changed'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Newest error'|i18n( 'design/admin/setup/logs' )}</th>
        </tr></thead>
        <tbody>
        {foreach $log_health.rows as $log_key => $log_row}
        <tr{if or( $log_row.over_limit, $log_row.writable|not )} class="is-attention"{/if}>
            <th scope="row"><a href={$log_health_links[ $log_key ]|ezurl}>{$log_key|wash}</a>
                {if $log_row.over_limit}<span class="a4-badge is-warn">{'over limit'|i18n( 'design/admin/setup/logs' )}</span>{/if}
                {if $log_row.writable|not}<span class="a4-badge is-bad">{'not writable'|i18n( 'design/admin/setup/logs' )}</span>{/if}</th>
            <td>{$log_row.size|si( byte )}</td>
            <td>{if is_set( $log_row.archives )}{$log_row.archives|count}{else}0{/if} · {$log_row.archive_bytes|si( byte )}</td>
            <td>{if and( is_set( $log_row.mtime ), $log_row.mtime )}{$log_row.mtime|l10n( 'shortdatetime' )}{/if}</td>
            <td class="a4-setup-loghealth-error">{if is_set( $log_health_errors[ $log_key ] )}
                <a href={$log_health_errors[ $log_key ].permalink|ezurl}>{$log_health_errors[ $log_key ].time_text|wash}</a>
                <span class="a4-setup-loghealth-message">{$log_health_errors[ $log_key ].message|wash}</span>
            {else}<span class="a4-meta">{'none in the last lines'|i18n( 'design/admin/setup/logs' )}</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <p class="a4-meta">{'There is no log to show.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
</section>

{* ---- the effective rotation settings, read only ---- *}
<section class="a4-card" aria-labelledby="loghealth-settings-title">
    <h2 id="loghealth-settings-title">{'Rotation settings in effect'|i18n( 'design/admin/setup/logs' )}</h2>
    <p class="a4-meta">{'Read only. Each value names the settings file, section and variable it comes from; change it there (in an override), not here.'|i18n( 'design/admin/setup/logs' )}</p>
    {if $log_health_settings|count}
    <div class="a4-table-wrap">
    <table class="a4-table a4-setup-loghealth-settings">
        <thead><tr>
            <th scope="col">{'Log'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Setting'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Value'|i18n( 'design/admin/setup/logs' )}</th>
            <th scope="col">{'Set in'|i18n( 'design/admin/setup/logs' )}</th>
        </tr></thead>
        <tbody>
        {foreach $log_health_settings as $log_key => $log_list}
        {foreach $log_list as $log_index => $log_setting}
        <tr>
            {if $log_index|eq( 0 )}<th scope="rowgroup" rowspan="{$log_list|count}">{$log_key|wash}</th>{/if}
            <td>{$log_setting_names[ $log_setting.name ]|wash}</td>
            <td class="a4-setup-loghealth-value">{switch match=$log_setting.name}
                {case match='max_size'}{if $log_setting.value}{$log_setting.value|si( byte )}{else}{'no limit'|i18n( 'design/admin/setup/logs' )}{/if}{/case}
                {case match='compress'}{if $log_setting.value}{'yes'|i18n( 'design/admin/setup/logs' )}{else}{'no'|i18n( 'design/admin/setup/logs' )}{/if}{/case}
                {case match='max_age_days'}{if $log_setting.value}{'%days days'|i18n( 'design/admin/setup/logs',, hash( '%days', $log_setting.value ) )|wash}{else}{'never'|i18n( 'design/admin/setup/logs' )}{/if}{/case}
                {case}{if is_set( $log_setting.value )}{$log_setting.value|wash}{/if}{/case}
            {/switch}</td>
            <td>{if $log_setting.file}<code>{$log_setting.file|wash} [{$log_setting.section|wash}] {$log_setting.var|wash}</code>{else}<span class="a4-meta">{'built-in default'|i18n( 'design/admin/setup/logs' )}</span>{/if}</td>
        </tr>
        {/foreach}
        {/foreach}
        </tbody>
    </table>
    </div>
    {/if}
</section>

</div></div></div>
</div>
{undef $log_setting_names}
