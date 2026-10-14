{* audit/dashboard: the audit summary, a card per area, each linking into its detailed view. Variables: can_manage,
   audit_enabled, index_usable, health (chains, key, worst), limited_to, and with the index: volume (days, channels,
   families, today, week, *_url), security (failed_logins, by_ip, by_login, grants, grants_total, refusals,
   refused_views, *_url), alerts (recent, day, rules, problems, recipients, enabled), activity (actors, objects,
   latest, latest_url), source_panels (the panels of the extensions' sources: identifier, label, channel, figures,
   lists, links, note); operations (archives, sinks, index, cron); ms. *}
<div class="context-block a4-audit-view a4-page" id="exp-audit-dashboard">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit dashboard'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $audit_enabled|not}
<div class="message-warning" role="status"><h2>{'Audit is switched off ([AuditSettings] Audit=disabled): nothing new is recorded.'|i18n( 'design/admin/audit' )}</h2></div>
{/if}
{if $limited_to|count}<p class="a4-audit-note">{'Your access is limited to the channels: %channels.'|i18n( 'design/admin/audit',, hash( '%channels', $limited_to|implode( ', ' ) ) )|wash}</p>{/if}

<div class="a4-audit-dash-grid">

{* ---- Health ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-health">
    <h2 class="a4-audit-h" id="au-d-health">{'Health'|i18n( 'design/admin/audit' )} <a href={'audit/archives'|ezurl}>{'Archives'|i18n( 'design/admin/audit' )} &rarr;</a></h2>
    <div class="a4-audit-figures">
        <div class="a4-audit-figure"><strong><span class="a4-audit-result {cond( $audit_enabled, 'success', 'failed' )}">{cond( $audit_enabled, 'enabled'|i18n( 'design/admin/audit' ), 'disabled'|i18n( 'design/admin/audit' ) )}</span></strong><span>{'Audit'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong><span class="a4-audit-result {$health.worst|wash}">{$health.worst|wash}</span></strong><span>{'Hash chains'|i18n( 'design/admin/audit' )}</span></div>
    </div>
    <ul class="a4-audit-list">
    {foreach $health.chains as $c}
        <li><span><span class="a4-audit-result {$c.result|wash}">{$c.result|wash}</span> {$c.channel|wash}{if $c.first_break} <span class="a4-audit-sub">{$c.first_break|wash}</span>{/if}</span><span class="a4-audit-sub a4-audit-n">{if $c.verified_at}{'verified %time'|i18n( 'design/admin/audit',, hash( '%time', $c.verified_at ) )}{else}{'not verified yet'|i18n( 'design/admin/audit' )}{/if}</span></li>
    {/foreach}
    </ul>
    {if $can_manage}
    <form method="post" action={'audit/dashboard'|ezurl}><p><input type="submit" class="button" name="AuditVerifyNowButton" value="{'Verify now'|i18n( 'design/admin/audit' )|wash}" /></p></form>
    {/if}
    {if $health.key.id}
    <p class="a4-audit-sub">{'Signing key %id, fingerprint %fp'|i18n( 'design/admin/audit',, hash( '%id', $health.key.id, '%fp', $health.key.fingerprint ) )|wash}{if is_null( $health.key.age_days )|not}, {'%n days old'|i18n( 'design/admin/audit',, hash( '%n', $health.key.age_days ) )}{/if}</p>
    {if $health.key.rotate}<p class="a4-audit-hintbox">{'The signing key is over a year old: rotate it with exp:audit key rotate.'|i18n( 'design/admin/audit' )}</p>{/if}
    {/if}
</section>

{if $index_usable}
{* ---- Today and 7 days ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-volume">
    <h2 class="a4-audit-h" id="au-d-volume">{'Today and 7 days'|i18n( 'design/admin/audit' )} <a href={$volume.charts_url|ezurl}>{'Charts'|i18n( 'design/admin/audit' )} &rarr;</a></h2>
    <div class="a4-audit-figures">
        <div class="a4-audit-figure"><strong>{$volume.today.total}</strong><span>{'events today'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong>{$volume.week.total}</strong><span>{'in 7 days'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong><a href={$volume.refused_url|ezurl}>{$volume.week.refused}</a></strong><span>{'refused'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong><a href={$volume.failed_url|ezurl}>{$volume.week.failed}</a></strong><span>{'failed'|i18n( 'design/admin/audit' )}</span></div>
    </div>
    <div class="a4-audit-spark" role="img" aria-label="{'Events per day, last 7 days'|i18n( 'design/admin/audit' )|wash}">{foreach $volume.days as $d}<span class="a4-pct-{max( $d.pct|round, 1 )}" title="{$d.day|wash}: {$d.n}"></span>{/foreach}</div>
    <div class="a4-audit-spark-labels" aria-hidden="true">{foreach $volume.days as $d}<span>{$d.label|wash}</span>{/foreach}</div>
    <ul class="a4-audit-list">
    {foreach $volume.channels as $c}
        <li><span><span class="a4-audit-key a4-audit-s{$c.slot}"></span><a href={$c.url|ezurl}>{$c.channel|wash}</a></span><span class="a4-audit-n">{$c.today} / {$c.week}</span></li>
    {/foreach}
    </ul>
    <p class="a4-audit-sub">{'Today / 7 days. Families:'|i18n( 'design/admin/audit' )} {foreach $volume.families as $f}<a href={$f.url|ezurl}>{$f.domain|wash}</a> {$f.n}{delimiter} &middot; {/delimiter}{/foreach}</p>
</section>

{* ---- Security ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-security">
    <h2 class="a4-audit-h" id="au-d-security">{'Security'|i18n( 'design/admin/audit' )} <a href={$security.failed_url|ezurl}>{'Failed logins'|i18n( 'design/admin/audit' )} &rarr;</a></h2>
    <div class="a4-audit-figures">
        <div class="a4-audit-figure"><strong><a href={$security.failed_url|ezurl}>{$security.failed_logins}</a></strong><span>{'failed logins, 24 h'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong><a href={$security.refusals_url|ezurl}>{$security.refusals}</a></strong><span>{'permission refusals, 24 h'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong><a href={$security.grants_url|ezurl}>{$security.grants_total}</a></strong><span>{'role grants, 7 days'|i18n( 'design/admin/audit' )}</span></div>
    </div>
    {if $security.by_ip|count}
    <p class="a4-audit-sub">{'Failed logins by address'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $security.by_ip as $r}<li><a href={$r.url|ezurl}>{if $r.value}{$r.value|wash}{else}&ndash;{/if}</a><span class="a4-audit-n">{$r.n}</span></li>{/foreach}</ul>
    <p class="a4-audit-sub">{'Failed logins by login (hashed for unknown accounts)'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $security.by_login as $r}<li><a href={$r.url|ezurl}>{if $r.value}{$r.value|wash}{else}&ndash;{/if}</a><span class="a4-audit-n">{$r.n}</span></li>{/foreach}</ul>
    {/if}
    {if $security.grants|count}
    <p class="a4-audit-sub">{'Latest role grants'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $security.grants as $e}<li><a href={concat( 'audit/event/', $e.id )|ezurl}>{$e.object|wash}{if $e.target} &rarr; {$e.target|wash}{/if}</a><span class="a4-audit-sub a4-audit-n">{$e.time|wash}</span></li>{/foreach}</ul>
    {/if}
    {if $security.refused_views|count}
    <p class="a4-audit-sub">{'Refused views'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $security.refused_views as $r}<li><code>{$r.value|wash}</code><span class="a4-audit-n">{$r.n}</span></li>{/foreach}</ul>
    {/if}
</section>

{* ---- Alerts ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-alerts">
    <h2 class="a4-audit-h" id="au-d-alerts">{'Alerts'|i18n( 'design/admin/audit' )} <a href={'audit/alerts'|ezurl}>{'Alerts'|i18n( 'design/admin/audit' )} &rarr;</a></h2>
    {if $alerts.enabled|not}<p class="a4-audit-hintbox">{'Alert rules are switched off ([AuditAlertSettings] Alerts=disabled).'|i18n( 'design/admin/audit' )}</p>{/if}
    <div class="a4-audit-figures">
        <div class="a4-audit-figure"><strong>{$alerts.day}</strong><span>{'alerts, 24 h'|i18n( 'design/admin/audit' )}</span></div>
        <div class="a4-audit-figure"><strong>{$alerts.rules}</strong><span>{'rules'|i18n( 'design/admin/audit' )}</span></div>
        {if $alerts.problems}<div class="a4-audit-figure"><strong><span class="a4-audit-result failed">{$alerts.problems}</span></strong><span>{'rules with a problem'|i18n( 'design/admin/audit' )}</span></div>{/if}
    </div>
    {if $alerts.recent|count}
    <ul class="a4-audit-list">{foreach $alerts.recent as $a}<li><span><span class="a4-audit-result {if array( 'critical', 'alert', 'emergency' )|contains( $a.severity )}failed{else}refused{/if}">{$a.severity|wash}</span> <a href={concat( 'audit/event/', $a.id )|ezurl}><code title="{$a.rule|wash}">{$a.rule|wash|explode( '_' )|implode( '_<wbr>' )}</code></a></span><span class="a4-audit-sub a4-audit-n">{$a.count} &middot; {$a.time|wash}</span></li>{/foreach}</ul>
    {else}<p class="a4-audit-note">{'No alert has fired.'|i18n( 'design/admin/audit' )}</p>{/if}
    {if $alerts.recipients|count}
    <p class="a4-audit-sub">{'Alert mail recipients'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $alerts.recipients as $r}<li><code>{$r.name|wash|explode( '_' )|implode( '_<wbr>' )}</code><span class="a4-audit-n">{if eq( $r.count, 1 )}{'1 address'|i18n( 'design/admin/audit' )}{else}{'%n addresses'|i18n( 'design/admin/audit',, hash( '%n', $r.count ) )}{/if}{if $r.problems} &middot; <span class="a4-audit-result failed">{if eq( $r.problems, 1 )}{'1 problem'|i18n( 'design/admin/audit' )}{else}{'%n problems'|i18n( 'design/admin/audit',, hash( '%n', $r.problems ) )}{/if}</span>{/if}</span></li>{/foreach}</ul>
    <p class="a4-audit-cmd">./console exp:audit alerts recipients</p>
    {/if}
</section>

{* ---- Activity ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-activity">
    <h2 class="a4-audit-h" id="au-d-activity">{'Activity'|i18n( 'design/admin/audit' )} <a href={$activity.latest_url|ezurl}>{'Warnings'|i18n( 'design/admin/audit' )} &rarr;</a></h2>
    <p class="a4-audit-sub">{'Top actors today'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $activity.actors as $r}<li>{if $r.url}<a href={$r.url|ezurl}>{$r.value|wash}</a>{else}<span>&ndash;</span>{/if}<span class="a4-audit-n">{$r.n}</span></li>{/foreach}</ul>
    <p class="a4-audit-sub">{'Top objects today'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $activity.objects as $r}<li><a href={$r.url|ezurl}>{$r.value|wash}</a><span class="a4-audit-n">{$r.n}</span></li>{/foreach}</ul>
    <p class="a4-audit-sub">{'Latest warnings and worse'|i18n( 'design/admin/audit' )}</p>
    {if $activity.latest|count}
    <ul class="a4-audit-list">{foreach $activity.latest as $e}<li><span><span class="a4-audit-result {if $e.severity_number|le( 3 )}failed{else}refused{/if}">{$e.severity|wash}</span> <a href={concat( 'audit/event/', $e.id )|ezurl} title="{$e.name|wash}">{$e.label|wash}</a></span><span class="a4-audit-sub a4-audit-n">{$e.time|wash}</span></li>{/foreach}</ul>
    {else}<p class="a4-audit-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

{* ---- The panels of the extensions' sources (audit.ini [AuditSourceSettings] Sources[]) ---- *}
{foreach $source_panels as $p}
<section class="a4-audit-card a4-audit-source-panel" id="au-d-source-{$p.identifier|wash}" aria-labelledby="au-d-source-{$p.identifier|wash}-h">
    <h2 class="a4-audit-h" id="au-d-source-{$p.identifier|wash}-h">{$p.label|wash}{if $p.channel} <a href={concat( 'audit/console/(channel)/', $p.channel )|ezurl}>{'Console'|i18n( 'design/admin/audit' )} &rarr;</a>{/if}</h2>
    {if $p.figures|count}
    <div class="a4-audit-figures">
    {foreach $p.figures as $f}
        <div class="a4-audit-figure"><strong>{if $f.state}<span class="a4-audit-result {$f.state|wash}">{/if}{if $f.url}<a href={$f.url|ezurl}>{$f.value|wash}</a>{else}{$f.value|wash}{/if}{if $f.state}</span>{/if}</strong><span>{$f.label|wash}</span></div>
    {/foreach}
    </div>
    {/if}
    {foreach $p.lists as $l}
    <p class="a4-audit-sub">{$l.title|wash}</p>
    {if $l.items|count}
    <ul class="a4-audit-list">{foreach $l.items as $i}<li>{if $i.url}<a href={$i.url|ezurl}>{$i.label|wash}</a>{else}<span>{$i.label|wash}</span>{/if}<span class="a4-audit-n">{$i.n}</span></li>{/foreach}</ul>
    {else}<p class="a4-audit-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
    {/foreach}
    {if $p.links|count}<div class="a4-audit-quick">{foreach $p.links as $l}<a href={$l.url|ezurl}>{$l.label|wash}</a>{/foreach}</div>{/if}
    {if $p.note}<p class="a4-audit-note">{$p.note|wash}</p>{/if}
</section>
{/foreach}
{else}
<section class="a4-audit-card">
    <h2 class="a4-audit-h">{'Index'|i18n( 'design/admin/audit' )}</h2>
    <p>{'Charts need the audit index (audit.ini [AuditIndexSettings] Index=enabled and its tables). The console still reads the files.'|i18n( 'design/admin/audit' )}</p>
</section>
{/if}

{* ---- Operations ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-ops">
    <h2 class="a4-audit-h" id="au-d-ops">{'Operations'|i18n( 'design/admin/audit' )} {if $can_manage}<a href={'audit/settings'|ezurl}>{'Settings'|i18n( 'design/admin/audit' )} &rarr;</a>{/if}</h2>
    <p class="a4-audit-sub">{'Cronjob part (audit)'|i18n( 'design/admin/audit' )}</p>
    {if $operations.cron.stale}<p class="a4-audit-warnbox">{'The audit cronjob part has not run for over an hour (last: %time). Rotation, archives, sinks and the index wait for it.'|i18n( 'design/admin/audit',, hash( '%time', cond( $operations.cron.text, $operations.cron.text, '-' ) ) )|wash}</p>
    {else}<p>{'Last run %time; daily tasks done for %day.'|i18n( 'design/admin/audit',, hash( '%time', $operations.cron.text, '%day', $operations.cron.daily ) )|wash}</p>{/if}
    <p class="a4-audit-sub">{'Index'|i18n( 'design/admin/audit' )}</p>
    {if $operations.index.usable}
    <ul class="a4-audit-list">
        <li><span>{'Rows'|i18n( 'design/admin/audit' )}</span><span class="a4-audit-n">{$operations.index.rows}</span></li>
        <li><span>{'Behind the files'|i18n( 'design/admin/audit' )}</span><span class="a4-audit-n">{$operations.index.lag|si( byte )}</span></li>
        <li><span>{'Last reindex'|i18n( 'design/admin/audit' )}</span><span class="a4-audit-n">{if $operations.index.last_reindex}{$operations.index.last_reindex|wash}{else}&ndash;{/if}</span></li>
        <li><span>{'Search'|i18n( 'design/admin/audit' )}</span><span class="a4-audit-n">{$operations.index.fulltext|wash}</span></li>
    </ul>
    {else}<p class="a4-audit-hintbox">{'tables missing'|i18n( 'design/admin/audit' )}</p>{/if}
    {if $operations.archives|count}
    <p class="a4-audit-sub">{'Archives'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $operations.archives as $a}<li><span>{$a.channel|wash}{if $a.due} <span class="a4-audit-result refused">{'archive due'|i18n( 'design/admin/audit' )}</span>{/if}</span><span class="a4-audit-sub a4-audit-n a4-audit-wrap">{'oldest live %live, last archive %archive'|i18n( 'design/admin/audit',, hash( '%live', cond( $a.oldest_live, $a.oldest_live, '-' ), '%archive', cond( $a.last_archive, $a.last_archive, '-' ) ) )|wash} &middot; {$a.live_days}/{$a.archive_days} d</span></li>{/foreach}</ul>
    {/if}
    {if $operations.sinks|count}
    <p class="a4-audit-sub">{'Sinks'|i18n( 'design/admin/audit' )}</p>
    <ul class="a4-audit-list">{foreach $operations.sinks as $s}<li><span><code>{$s.name|wash}</code>{if $s.problem} <span class="a4-audit-sub">{$s.problem|wash}</span>{/if}</span><span class="a4-audit-sub a4-audit-n">{if $s.last_delivery}{'last %time'|i18n( 'design/admin/audit',, hash( '%time', $s.last_delivery ) )}{else}&ndash;{/if}{if $s.spooled} &middot; <span class="a4-audit-result refused">{'%n spooled'|i18n( 'design/admin/audit',, hash( '%n', $s.spooled ) )}</span>{/if}{if $s.last_error} &middot; <span class="a4-audit-result failed" title="{$s.last_error|wash}">{'error'|i18n( 'design/admin/audit' )}</span>{/if}</span></li>{/foreach}</ul>
    {/if}
</section>

{* ---- Quick links ---- *}
<section class="a4-audit-card" aria-labelledby="au-d-links">
    <h2 class="a4-audit-h" id="au-d-links">{'Quick links'|i18n( 'design/admin/audit' )}</h2>
    <div class="a4-audit-quick">
        <a href={'audit/console'|ezurl}>{'Console'|i18n( 'design/admin/audit' )}</a>
        <a href={'audit/recent'|ezurl}>{'Recent events'|i18n( 'design/admin/audit' )}</a>
        <a href={'audit/charts'|ezurl}>{'Charts'|i18n( 'design/admin/audit' )}</a>
        <a href={'audit/alerts'|ezurl}>{'Alerts'|i18n( 'design/admin/audit' )}</a>
        <a href={'audit/export'|ezurl}>{'Export'|i18n( 'design/admin/audit' )}</a>
        {if $can_manage}<a href={'audit/archives'|ezurl}>{'Archives'|i18n( 'design/admin/audit' )}</a>
        <a href={'audit/settings'|ezurl}>{'Settings'|i18n( 'design/admin/audit' )}</a>{/if}
    </div>
    <p class="a4-audit-sub">{'On the command line'|i18n( 'design/admin/audit' )}</p>
    <p class="a4-audit-cmd">./console exp:audit status<br />./console exp:audit tail --channel=access --follow<br />./console exp:audit search --name=access.session.login.failed<br />./console exp:audit verify</p>
</section>

</div>

<p class="a4-audit-note">{'Built in %ms ms from the index and the stored chain verification; opening this page is recorded (system.audit.read).'|i18n( 'design/admin/audit',, hash( '%ms', $ms ) )}</p>

{* DESIGN: Content END *}</div></div></div>
</div>
