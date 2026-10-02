{* audit/settings (audit/manage): the effective audit.ini with the origin of every value, the keys as fingerprints,
   the sinks and format handlers with their problems, and the index state. Read-only. Variables: blocks (name,
   rows: variable, value, is_list, secret, origin, overridden), key_rows, installation, sinks, formats, index,
   audit_enabled, log_dir, index_settings. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-settings">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit settings'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
<p>{'Audit is %state. Live files: %dir.'|i18n( 'design/admin/audit',, hash( '%state', cond( $audit_enabled, 'enabled'|i18n( 'design/admin/audit' ), 'disabled'|i18n( 'design/admin/audit' ) ), '%dir', $log_dir ) )|wash}</p>
<p class="au-note">{'This page only shows the settings. Write them with exp:ini, for example: ./console exp:ini set audit.ini/AuditConsoleSettings/PageSize 100 override. Every write to audit.ini is recorded (system.audit.setting.write).'|i18n( 'design/admin/audit' )}</p>
</div>

<div class="au-cards">
<section class="au-card">
    <h2 class="au-h">{'Index'|i18n( 'design/admin/audit' )}</h2>
    <dl class="au-fields">
        <dt>{'Index'|i18n( 'design/admin/audit' )}</dt><dd>{cond( $index.enabled, 'enabled'|i18n( 'design/admin/audit' ), 'disabled'|i18n( 'design/admin/audit' ) )}, {cond( $index.installed, 'tables installed'|i18n( 'design/admin/audit' ), 'tables missing'|i18n( 'design/admin/audit' ) )} ({$index.engine|wash})</dd>
        <dt>{'Search'|i18n( 'design/admin/audit' )}</dt><dd>{$index.fulltext|wash}</dd>
        <dt>{'Rows'|i18n( 'design/admin/audit' )}</dt><dd>{$index.rows}</dd>
        <dt>{'Behind the files'|i18n( 'design/admin/audit' )}</dt><dd>{$index.lag|si( byte )}</dd>
        <dt>{'Last indexed'|i18n( 'design/admin/audit' )}</dt><dd>{$index.updated|wash}</dd>
        <dt>{'Pseudonymised after'|i18n( 'design/admin/audit' )}</dt><dd>{'%n days'|i18n( 'design/admin/audit',, hash( '%n', $index_settings.pseudonymiseAfterDays ) )}</dd>
        <dt>{'Kept in the index'|i18n( 'design/admin/audit' )}</dt><dd>{'%n days'|i18n( 'design/admin/audit',, hash( '%n', $index_settings.keepDays ) )}</dd>
    </dl>
</section>

<section class="au-card">
    <h2 class="au-h">{'Keys'|i18n( 'design/admin/audit' )}</h2>
    {if $installation}<p>{'Installation %id'|i18n( 'design/admin/audit',, hash( '%id', $installation ) )|wash}</p>{/if}
    {if $key_rows|count}
    <table class="list" cellspacing="0">
    <tr><th>{'Key'|i18n( 'design/admin/audit' )}</th><th>{'Fingerprint'|i18n( 'design/admin/audit' )}</th></tr>
    {foreach $key_rows as $k}<tr><td>{$k.kind|wash} {$k.id|wash}{if and( eq( $k.kind, 'signing' ), $k.active )} <span class="au-result success">{'active'|i18n( 'design/admin/audit' )}</span>{/if}</td><td><code>{$k.fingerprint|wash}</code></td></tr>{/foreach}
    </table>
    {else}<p class="au-note">{'No keys yet: they are made with the first event.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

<section class="au-card">
    <h2 class="au-h">{'Sinks'|i18n( 'design/admin/audit' )}</h2>
    {if $sinks|count}
    <table class="list" cellspacing="0">
    {foreach $sinks as $s}<tr><td><code>{$s.name|wash}</code></td><td>{if $s.problem}<span class="au-result refused">{'not working'|i18n( 'design/admin/audit' )}</span> <span class="au-sub">{$s.problem|wash}</span>{else}<span class="au-result success">{'ready'|i18n( 'design/admin/audit' )}</span>{/if}</td></tr>{/foreach}
    </table>
    {else}<p class="au-note">{'No sinks are registered here.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

<section class="au-card">
    <h2 class="au-h">{'Archive formats'|i18n( 'design/admin/audit' )}</h2>
    {if $formats|count}
    <table class="list" cellspacing="0">
    {foreach $formats as $f}<tr><td><code>{$f.name|wash}</code> <span class="au-sub">{$f.extension|wash}</span></td><td>{if $f.problem}<span class="au-result refused">{'unavailable'|i18n( 'design/admin/audit' )}</span> <span class="au-sub">{$f.problem|wash}</span>{else}<span class="au-result success">{'available'|i18n( 'design/admin/audit' )}</span>{/if}</td></tr>{/foreach}
    </table>
    {else}<p class="au-note">{'No format handlers are registered here.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>
</div>

<h2 class="au-h">audit.ini</h2>
{foreach $blocks as $b}
<details class="au-data"{if array( 'AuditSettings', 'AuditIndexSettings', 'AuditConsoleSettings' )|contains( $b.name )} open="open"{/if}>
<summary><code>[{$b.name|wash}]</code></summary>
<div class="au-table">
<table class="list" cellspacing="0">
<tr><th>{'Variable'|i18n( 'design/admin/audit' )}</th><th>{'Value'|i18n( 'design/admin/audit' )}</th><th>{'From'|i18n( 'design/admin/audit' )}</th></tr>
{foreach $b.rows as $r sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><code>{$r.variable|wash}</code></td>
    <td>{if $r.is_list}<pre class="au-raw" style="max-height: 12em; margin: 0">{$r.value|wash}</pre>{else}<code>{$r.value|wash}</code>{/if}</td>
    <td class="au-sub">{if $r.overridden}<strong>{$r.origin|wash}</strong>{else}{$r.origin|wash}{/if}</td>
</tr>
{/foreach}
</table>
</div>
</details>
{/foreach}

{* DESIGN: Content END *}</div></div></div>
</div>
