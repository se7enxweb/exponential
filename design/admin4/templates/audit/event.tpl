{* audit/event: one audit record in full. Variables: event (expAuditConsole::view), record, raw (indented JSON),
   request, actor, object_fields, target_fields, error_fields (field => text), changes (key, before, after, changed),
   roles (id, name), links (object, target, user, job: module/view paths or null), related (parent, children,
   request, job_count), hash_ok (yes | no | n/a), file_state, prev, hash, console_request_url, console_job_url,
   source (what the source of the channel says: identifier, label, fields (label, value), links (label, url)) or null. *}
<div class="context-block a4-audit-view a4-page" id="exp-audit-event">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$event.label|wash} <span class="a4-audit-result {$event.result|wash}">{$event.result|wash}</span></h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
<dl class="a4-audit-fields">
    <dt>{'Event'|i18n( 'design/admin/audit' )}</dt>
    <dd><code>{$event.name|wash}</code> &middot; <code>{$event.id|wash}</code> &middot; {'severity %s'|i18n( 'design/admin/audit',, hash( '%s', $event.severity ) )|wash}{if $event.reason} &middot; {'reason'|i18n( 'design/admin/audit' )}: <code>{$event.reason|wash}</code>{/if}</dd>
    <dt>{'When'|i18n( 'design/admin/audit' )}</dt>
    <dd>{$event.time|wash} <span class="a4-audit-sub">(UTC {$event.time_utc|wash})</span></dd>
    <dt>{'Channel'|i18n( 'design/admin/audit' )}</dt>
    <dd>{$event.channel|wash}, {'record %seq in %file'|i18n( 'design/admin/audit',, hash( '%seq', $event.seq, '%file', $event.file ) )|wash}{if $event.imported} &middot; {'imported'|i18n( 'design/admin/audit' )}{/if}{if $event.pseudonymised} &middot; {'pseudonymised in the index; the file holds the original'|i18n( 'design/admin/audit' )}{/if}</dd>
    <dt>{'Who'|i18n( 'design/admin/audit' )}</dt>
    <dd>{if $links.user}<a href={$links.user|ezurl}>{$event.actor|wash}</a>{else}{$event.actor|wash}{/if}
        {if $roles|count}&middot; {'roles'|i18n( 'design/admin/audit' )}: {foreach $roles as $r}<a href={concat( 'role/view/', $r.id )|ezurl}>{$r.name|wash}</a>{delimiter}, {/delimiter}{/foreach}{/if}
        {foreach $actor as $k => $v}{if and( $v, array( 'login', 'user_id', 'roles' )|contains( $k )|not )}<br /><span class="a4-audit-sub">{$k|wash}: {$v|wash}</span>{/if}{/foreach}</dd>
    <dt>{'Request'|i18n( 'design/admin/audit' )}</dt>
    <dd>{if $console_request_url}<a href={$console_request_url|ezurl}><code>{$event.request|wash}</code></a>{/if}
        {foreach $request as $k => $v}{if and( $v, ne( $k, 'id' ) )}<br /><span class="a4-audit-sub">{$k|wash}: {$v|wash}</span>{/if}{/foreach}</dd>
    <dt>{'Object'|i18n( 'design/admin/audit' )}</dt>
    <dd>{if $event.object}{if $links.object}<a href={$links.object|ezurl}>{$event.object|wash}</a>{else}{$event.object|wash}{/if}{else}&ndash;{/if}
        {foreach $object_fields as $k => $v}{if array( 'type', 'id', 'name' )|contains( $k )|not}<br /><span class="a4-audit-sub">{$k|wash}: {$v|wash}</span>{/if}{/foreach}</dd>
    <dt>{'Target'|i18n( 'design/admin/audit' )}</dt>
    <dd>{if $event.target}{if $links.target}<a href={$links.target|ezurl}>{$event.target|wash}</a>{else}{$event.target|wash}{/if}{else}&ndash;{/if}
        {foreach $target_fields as $k => $v}{if array( 'type', 'id', 'name' )|contains( $k )|not}<br /><span class="a4-audit-sub">{$k|wash}: {$v|wash}</span>{/if}{/foreach}</dd>
    {if $error_fields|count}
    <dt>{'Error'|i18n( 'design/admin/audit' )}</dt>
    <dd>{foreach $error_fields as $k => $v}{$k|wash}: {$v|wash}{delimiter}<br />{/delimiter}{/foreach}</dd>
    {/if}
    <dt>{'Job'|i18n( 'design/admin/audit' )}</dt>
    <dd>{if $event.job}{if $links.job}<a href={$links.job|ezurl}>{$event.job|wash}</a>{else}{$event.job|wash}{/if} &middot; <a href={$console_job_url|ezurl}>{'%n events of this job'|i18n( 'design/admin/audit',, hash( '%n', $related.job_count ) )}</a>{else}&ndash;{/if}{if $event.run}<br /><span class="a4-audit-sub">{'cronjob run'|i18n( 'design/admin/audit' )}: {$event.run|wash}</span>{/if}</dd>
    <dt>{'Chain'|i18n( 'design/admin/audit' )}</dt>
    <dd>{'hash matches the record'|i18n( 'design/admin/audit' )}: <span class="a4-audit-result {$hash_ok|wash}">{$hash_ok|wash}</span> &middot; {'file'|i18n( 'design/admin/audit' )}: <span class="a4-audit-result {$file_state|wash}">{$file_state|wash}</span>
        <br /><span class="a4-audit-sub">prev <code>{$prev|wash}</code></span><br /><span class="a4-audit-sub">hash <code>{$hash|wash}</code></span></dd>
</dl>
</div>

{if $source}
<section class="a4-audit-card a4-audit-event-source" id="au-event-source-{$source.identifier|wash}" aria-labelledby="au-event-source-h">
<h2 class="a4-audit-h" id="au-event-source-h">{$source.label|wash}</h2>
{if $source.fields|count}
<dl class="a4-audit-fields">
{foreach $source.fields as $f}
    <dt>{$f.label|wash}</dt>
    <dd>{$f.value|wash}</dd>
{/foreach}
</dl>
{/if}
{if $source.links|count}<p class="a4-audit-quick-links">{foreach $source.links as $l}<a href={$l.url|ezurl}>{$l.label|wash}</a>{delimiter} &middot; {/delimiter}{/foreach}</p>{/if}
</section>
{/if}

{if $changes|count}
<h2 class="a4-audit-h">{'Before and after'|i18n( 'design/admin/audit' )}</h2>
<div class="a4-audit-table">
<table class="list a4-audit-changes" cellspacing="0">
<tr><th scope="col">{'Field'|i18n( 'design/admin/audit' )}</th><th scope="col">{'Before'|i18n( 'design/admin/audit' )}</th><th scope="col">{'After'|i18n( 'design/admin/audit' )}</th></tr>
{foreach $changes as $c sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}"><td><code>{$c.key|wash}</code></td><td{if $c.changed} class="a4-audit-changed"{/if}>{if is_null( $c.before )}&ndash;{else}{$c.before|wash}{/if}</td><td{if $c.changed} class="a4-audit-changed"{/if}>{if is_null( $c.after )}&ndash;{else}{$c.after|wash}{/if}</td></tr>
{/foreach}
</table>
</div>
{/if}

{if $related.parent}
<h2 class="a4-audit-h">{'Parent'|i18n( 'design/admin/audit' )}</h2>
<p><a href={concat( 'audit/event/', $related.parent.id )|ezurl}>{$related.parent.label|wash}</a> <span class="a4-audit-sub">{$related.parent.time|wash}, {$related.parent.object|wash}</span></p>
{/if}

{foreach hash( 'children', 'Children', 'request', 'Other events of this request' ) as $key => $title}
{if $related[$key]|count}
<h2 class="a4-audit-h">{$title|i18n( 'design/admin/audit' )} ({$related[$key]|count})</h2>
<div class="a4-audit-table">
<table class="list" cellspacing="0">
{foreach $related[$key] as $e sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td class="a4-audit-time">{$e.time|wash}</td>
    <td><a href={concat( 'audit/event/', $e.id )|ezurl}>{$e.label|wash}</a> <span class="a4-audit-sub"><code>{$e.name|wash}</code></span></td>
    <td>{$e.object|wash}</td>
    <td><span class="a4-audit-result {$e.result|wash}">{$e.result|wash}</span></td>
</tr>
{/foreach}
</table>
</div>
{/if}
{/foreach}

<h2 class="a4-audit-h">{'The record as written'|i18n( 'design/admin/audit' )}</h2>
<p><a href={concat( 'audit/event/', $event.id, '/(format)/json' )|ezurl}>{'Download the record (JSON)'|i18n( 'design/admin/audit' )}</a></p>
<pre class="a4-audit-raw">{$raw|wash}</pre>

{* DESIGN: Content END *}</div></div></div>
</div>
