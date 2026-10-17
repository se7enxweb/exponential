{* Setup > Log files > Manage logs (setup/logsmanage; doc/features/6.0/log-viewer.md, "Managing logs"): every log with
   its size, estimated lines, last write, growth, archives and limits, and its actions; Rotate all and Prune all.
   Every action is a POST (form token) that shows its plan first (setup/logs_manage_confirm.tpl).
   Variables: log_logs (key => log of expLogManagerInterface::logs()), log_total (bytes of the logs and their archives),
   log_result (null or op, key, ok, error, steps of the last action), log_switches (Rotate, Truncate, Compress,
   DeleteArchives, Download => bool), log_can_download, log_token_field, log_token, log_bundle_error (null or error,
   total, cap of a refused "Download all logs"), log_bundle_cap, log_bundle_format (zip | tar.gz), log_can_unmask.

   The same file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-setup-logs a4-setup-logmanage">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Manage logs'|i18n( 'design/admin/setup/logs' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri='design:setup/logs_nav.tpl' log_nav_current='manage'
         log_nav_intro='Rotate, empty, compress and prune the log files. Every action shows exactly what it will do before anything changes, and every step is recorded in the audit. The web server owns its logs: they are shown here but never changed.'|i18n( 'design/admin/setup/logs' )}

{if $log_result}
{def $log_done_steps = 0}
{foreach $log_result.steps as $log_s}{if $log_s.done}{set $log_done_steps = inc( $log_done_steps )}{/if}{/foreach}
<div class="a4-notice {if $log_result.ok}is-ok{else}is-bad{/if}" role="status">
    {if $log_result.ok}{'Done: %count steps.'|i18n( 'design/admin/setup/logs',, hash( '%count', $log_done_steps ) )|wash}
    {else}{'Not everything was done (%error): %done of %count steps.'|i18n( 'design/admin/setup/logs',, hash( '%error', $log_result.error, '%done', $log_done_steps, '%count', $log_result.steps|count ) )|wash}{/if}
</div>
{undef $log_done_steps}
{/if}

<ul class="a4-setup-inline a4-setup-log-facts">
    <li>{'%count logs'|i18n( 'design/admin/setup/logs',, hash( '%count', $log_logs|count ) )|wash}</li>
    <li>{'%size with their archives'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_total|si( byte ) ) )|wash}</li>
</ul>

{* Every log at once: only those over their size limit are rotated; prune removes archives beyond Keep and older than their maximum age *}
<form method="post" action={'setup/logsmanage'|ezurl} class="a4-row a4-setup-logmanage-all">
    <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
    {if $log_switches.Rotate}<button type="submit" class="a4-btn a4-btn-primary" name="Op" value="rotate_all"><span class="a4-icon a4-bi-arrow-clockwise" aria-hidden="true"></span>{'Rotate all over their limit…'|i18n( 'design/admin/setup/logs' )}</button>{/if}
    {if $log_switches.DeleteArchives}<button type="submit" class="a4-btn" name="Op" value="prune_all"><span class="a4-icon a4-bi-trash" aria-hidden="true"></span>{'Prune all archives…'|i18n( 'design/admin/setup/logs' )}</button>{/if}
    {if and( $log_switches.Rotate|not, $log_switches.DeleteArchives|not )}<span class="a4-meta">{'The actions are switched off in settings/logviewer.ini [ActionSettings].'|i18n( 'design/admin/setup/logs' )}</span>{/if}
</form>

{* Every log in one archive (expLogBundle): a POST with the form token; masked unless Unredacted (setup/unmasklogs) *}
{if $log_bundle_error}
<div class="a4-notice is-bad" role="alert">
    {if $log_bundle_error.error|eq( 'too_large' )}{'The logs are too large to download at once: %total, more than the limit of %cap ([ActionSettings] MaxBundleBytes in settings/logviewer.ini). Leave the archives out, or download the logs one by one.'|i18n( 'design/admin/setup/logs',, hash( '%total', $log_bundle_error.total|si( byte ), '%cap', $log_bundle_error.cap|si( byte ) ) )|wash}
    {elseif $log_bundle_error.error|eq( 'empty' )}{'There is no log file to download.'|i18n( 'design/admin/setup/logs' )}
    {else}{'The archive could not be written in var/tmp. Check that the web server may write there.'|i18n( 'design/admin/setup/logs' )}{/if}
</div>
{/if}
{if $log_can_download}
<form method="post" action={'setup/logsmanage'|ezurl} class="a4-row a4-setup-logmanage-bundle">
    <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
    <button type="submit" class="a4-btn a4-btn-primary" name="DownloadAllButton" value="1" aria-describedby="log-bundle-help"><span class="a4-icon a4-bi-download" aria-hidden="true"></span>{'Download all logs'|i18n( 'design/admin/setup/logs' )}</button>
    <input type="hidden" name="IncludeArchives" value="0" />
    <label class="a4-check"><input type="checkbox" name="IncludeArchives" value="1" checked="checked" /><span>{'Include archives'|i18n( 'design/admin/setup/logs' )}</span></label>
    {if $log_can_unmask}
    <label class="a4-check"><input type="checkbox" name="Unredacted" value="1" aria-describedby="log-bundle-unredacted" /><span>{'Unredacted'|i18n( 'design/admin/setup/logs' )}</span></label>
    <span class="a4-notice is-warn a4-setup-logmanage-bundle-warn" id="log-bundle-unredacted">{'Unredacted: e-mail addresses, IP addresses, tokens and session ids as written. Only for a secure place; the download is recorded in the audit.'|i18n( 'design/admin/setup/logs' )}</span>
    {/if}
    <span class="a4-help" id="log-bundle-help">{'One %format file of every log, with a MANIFEST.txt of sizes, last writes and checksums. Masked as the viewer shows them; recorded in the audit; at most %cap.'|i18n( 'design/admin/setup/logs',, hash( '%format', cond( $log_bundle_format|eq( 'zip' ), '.zip', '.tar.gz' ), '%cap', $log_bundle_cap|si( byte ) ) )|wash}</span>
</form>
{/if}

<div class="a4-table-wrap">
<table class="a4-table a4-setup-logmanage-table">
<caption class="a4-sr-only">{'The log files, their archives and their limits'|i18n( 'design/admin/setup/logs' )}</caption>
<thead>
<tr>
    <th scope="col">{'Log'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col" class="a4-num">{'Size'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col" class="a4-num">{'Lines (about)'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Last write'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col" class="a4-num">{'Growth'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Archives'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Limits'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Actions'|i18n( 'design/admin/setup/logs' )}</th>
</tr>
</thead>
<tbody>
{foreach $log_logs as $log_key => $log}
<tr>
    <th scope="row">
        <a href={concat( 'setup/logs/view/', $log.key )|ezurl}>{$log.label|wash}</a>
        <div class="a4-meta"><code>{$log.id|wash}</code></div>
        {if $log.writable|not}<span class="a4-badge is-warn" title="{'The web server cannot write this file or its directory: owner %owner, group %group.'|i18n( 'design/admin/setup/logs',, hash( '%owner', $log.owner, '%group', $log.owner_group ) )|wash}">{'Not writable: %owner:%group'|i18n( 'design/admin/setup/logs',, hash( '%owner', $log.owner, '%group', $log.owner_group ) )|wash}</span>{/if}
        {if $log.managed|not}<span class="a4-tag">{'Web server, read only'|i18n( 'design/admin/setup/logs' )}</span>{/if}
    </th>
    <td class="a4-num">{$log.size|si( byte )}{if $log.size|gt( $log.limits.max_size )} <span class="a4-badge is-warn">{'over the limit'|i18n( 'design/admin/setup/logs' )}</span>{/if}</td>
    <td class="a4-num">{$log.lines|wash}</td>
    <td>{if $log.mtime}{$log.mtime|l10n( 'shortdatetime' )}{/if}</td>
    <td class="a4-num">{if is_null( $log.growth )}<span class="a4-meta">–</span>{else}{'%size/h'|i18n( 'design/admin/setup/logs',, hash( '%size', $log.growth|si( byte ) ) )|wash}{/if}</td>
    <td>
        {if $log.archives|count}
        <ul class="a4-setup-logmanage-archives">
        {foreach $log.archives as $log_a}
            <li><a href={concat( 'setup/logs/view/', $log.key, '/(rotation)/', $log_a.rotation )|ezurl}>{$log_a.name|wash}</a> {$log_a.size|si( byte )}{if $log_a.gz} <span class="a4-tag">gz</span>{/if}</li>
        {/foreach}
        </ul>
        <div class="a4-meta">{'%size in all'|i18n( 'design/admin/setup/logs',, hash( '%size', $log.archive_bytes|si( byte ) ) )|wash}</div>
        {else}<span class="a4-meta">{'none'|i18n( 'design/admin/setup/logs' )}</span>{/if}
    </td>
    <td>
        <ul class="a4-setup-logmanage-limits">
            <li>{'Rotate at %size'|i18n( 'design/admin/setup/logs',, hash( '%size', $log.limits.max_size|si( byte ) ) )|wash}</li>
            <li>{'Keep %count'|i18n( 'design/admin/setup/logs',, hash( '%count', $log.limits.keep ) )|wash}</li>
            <li>{if $log.limits.compress}{'Compressed'|i18n( 'design/admin/setup/logs' )}{else}{'Not compressed'|i18n( 'design/admin/setup/logs' )}{/if}</li>
            {if $log.limits.max_age_days|gt( 0 )}<li>{'At most %days days'|i18n( 'design/admin/setup/logs',, hash( '%days', $log.limits.max_age_days ) )|wash}</li>{/if}
        </ul>
    </td>
    <td>
        {if $log.managed}
        <form method="post" action={concat( 'setup/logsmanage/', $log.key )|ezurl} class="a4-setup-logmanage-actions">
            <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
            <input type="hidden" name="Key" value="{$log.key|wash}" />
            {if $log_switches.Rotate}<button type="submit" class="a4-btn a4-btn-small a4-btn-primary" name="Op" value="rotate">{'Rotate now…'|i18n( 'design/admin/setup/logs' )}</button>{/if}
            {if $log_switches.Truncate}<button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="Op" value="truncate">{'Empty (archive first)…'|i18n( 'design/admin/setup/logs' )}</button>{/if}
            {if and( $log_switches.Compress, $log.archives|count )}<button type="submit" class="a4-btn a4-btn-small" name="Op" value="compress">{'Compress archives…'|i18n( 'design/admin/setup/logs' )}</button>{/if}
            {if and( $log_switches.DeleteArchives, $log.archives|count )}
            <span class="a4-setup-logmanage-days">
                <label for="log-days-{$log.key|wash}">{'Older than (days)'|i18n( 'design/admin/setup/logs' )}</label>
                <input type="number" min="0" max="3650" class="a4-input" id="log-days-{$log.key|wash}" name="Days" value="{if $log.limits.max_age_days|gt( 0 )}{$log.limits.max_age_days|wash}{else}30{/if}" />
                <button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="Op" value="prune">{'Delete archives…'|i18n( 'design/admin/setup/logs' )}</button>
            </span>
            {/if}
        </form>
        {/if}
        {if $log_can_download}
        <form method="post" action={'setup/logsaction'|ezurl} class="a4-setup-logmanage-actions">
            <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
            <input type="hidden" name="File" value="{$log.id|wash}" />
            <button type="submit" class="a4-btn a4-btn-small" name="DownloadButton" value="1"><span class="a4-icon a4-bi-download" aria-hidden="true"></span>{'Download'|i18n( 'design/admin/setup/logs' )}</button>
        </form>
        {/if}
    </td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{if $log_logs|count|not}<p class="a4-meta">{'There is no log file this viewer may open yet.'|i18n( 'design/admin/setup/logs' )}</p>{/if}

</div></div></div>
</div>
