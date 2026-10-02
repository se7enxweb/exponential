{* audit/archives (audit/manage): per channel its live files, archives and manifests and the verification state of
   each live file. Read-only. Variables: channels (channel, files, live_count, live_bytes, oldest_live, archives,
   archive_bytes, oldest_archive, manifests, format, live_days, archive_days, state), log_dir, archive_dir,
   archiver, key_id, key_fingerprint. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-archives">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit archives'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
<p class="au-note">{'Live files in %log; archives in %archive. Archiving, verifying and restoring are done by the audit cronjob part and by exp:audit (verify, archive, restore).'|i18n( 'design/admin/audit',, hash( '%log', $log_dir, '%archive', cond( $archive_dir, $archive_dir, '-' ) ) )|wash}</p>
{if $key_id}<p>{'Signing key %id, fingerprint %fp'|i18n( 'design/admin/audit',, hash( '%id', $key_id, '%fp', $key_fingerprint ) )|wash}</p>{/if}
</div>

<div class="au-table">
<table class="list" cellspacing="0">
<tr>
    <th>{'Channel'|i18n( 'design/admin/audit' )}</th>
    <th>{'Live files'|i18n( 'design/admin/audit' )}</th>
    <th>{'Oldest live'|i18n( 'design/admin/audit' )}</th>
    <th>{'Archives'|i18n( 'design/admin/audit' )}</th>
    <th>{'Oldest archive'|i18n( 'design/admin/audit' )}</th>
    <th>{'Kept'|i18n( 'design/admin/audit' )}</th>
    <th>{'State'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $channels as $c sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><a href={concat( 'audit/archives/(channel)/', $c.channel )|ezurl}>{$c.channel|wash}</a></td>
    <td>{$c.live_count} ({$c.live_bytes|si( byte )})</td>
    <td>{$c.oldest_live|wash}</td>
    <td>{$c.archives}{if $c.archive_bytes} ({$c.archive_bytes|si( byte )}){/if}{if $c.format} <span class="au-sub">{$c.format|wash}</span>{/if}</td>
    <td>{$c.oldest_archive|wash}</td>
    <td>{if $c.live_days}{'%live days live, %archive days archived'|i18n( 'design/admin/audit',, hash( '%live', $c.live_days, '%archive', $c.archive_days ) )}{else}&ndash;{/if}</td>
    <td><span class="au-result {$c.state|wash}">{$c.state|wash}</span></td>
</tr>
{/foreach}
</table>
</div>

{foreach $channels as $c}
{if $c.files|count}
<h2 class="au-h">{'Channel %channel'|i18n( 'design/admin/audit',, hash( '%channel', $c.channel ) )|wash}</h2>
<div class="au-table">
<table class="list" cellspacing="0">
<tr><th>{'File'|i18n( 'design/admin/audit' )}</th><th>{'Size'|i18n( 'design/admin/audit' )}</th><th>{'Records'|i18n( 'design/admin/audit' )}</th><th>{'Verified'|i18n( 'design/admin/audit' )}</th><th>{'State'|i18n( 'design/admin/audit' )}</th></tr>
{foreach $c.files as $f sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><code>{$f.name|wash}</code></td>
    <td>{$f.bytes|si( byte )}</td>
    <td>{$f.records}</td>
    <td>{$f.verified_at|wash}</td>
    <td><span class="au-result {$f.verified|wash}">{$f.verified|wash}</span>{if $f.break_line} <span class="au-sub">{'first break at record %line'|i18n( 'design/admin/audit',, hash( '%line', $f.break_line ) )}</span>{/if}</td>
</tr>
{/foreach}
</table>
</div>
{if $c.manifests|count}<p class="au-note">{'Latest manifests'|i18n( 'design/admin/audit' )}: {$c.manifests|implode( ', ' )|wash}</p>{/if}
{/if}
{/foreach}

{* DESIGN: Content END *}</div></div></div>
</div>
