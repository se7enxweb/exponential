{* audit/console: the audit timeline, newest first, with filters, search and paging, and the chain state per
   channel. Variables: events, total, offset, limit, page, pages, newer_url, older_url, source (index | files),
   fulltext, unindexed, index_run, filters, active_filters, filter_url, export_url, charts_url, channel_names,
   chains, severities, names, audit_enabled, can_manage, limited_to. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-console">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit console'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $audit_enabled|not}
<div class="message-warning"><h2>{'Audit is switched off ([AuditSettings] Audit=disabled): nothing new is recorded.'|i18n( 'design/admin/audit' )}</h2></div>
{/if}

<div class="block">
<ul class="au-chains" aria-label="{'Hash chain per channel'|i18n( 'design/admin/audit' )|wash}">
{foreach $chains as $c}
    <li><span class="au-chain {$c.result|wash}" title="{$c.first_break|wash}">{$c.channel|wash}: {$c.result|wash}{if $c.first_break} ({$c.first_break|wash}){/if}</span></li>
{/foreach}
{if $chains|count|not}<li class="au-note">{'No audit files yet.'|i18n( 'design/admin/audit' )}</li>{/if}
</ul>
<form method="post" action={$filter_url|ezurl} class="au-verify">
    <p class="au-note">{'The chain status is the result of the last verification (daily maintenance, exp:audit verify or this button).'|i18n( 'design/admin/audit' )}{foreach $chains as $c}{if $c.verified_at} {$c.channel|wash}: {$c.verified_at|wash}{/if}{/foreach}
    {if $can_manage}<input type="submit" class="button" name="AuditVerifyNowButton" value="{'Verify now'|i18n( 'design/admin/audit' )|wash}" />{/if}</p>
</form>
{if $limited_to|count}<p class="au-note">{'Your access is limited to the channels: %channels.'|i18n( 'design/admin/audit',, hash( '%channels', $limited_to|implode( ', ' ) ) )|wash}</p>{/if}

<form class="au-filter" method="post" action={'audit/console'|ezurl} role="search" aria-label="{'Filter the audit events'|i18n( 'design/admin/audit' )|wash}">
    <div>
        <label for="au-channel">{'Channel'|i18n( 'design/admin/audit' )}</label>
        <select id="au-channel" name="Channel">
            <option value="">{'All'|i18n( 'design/admin/audit' )}</option>
            {foreach $channel_names as $c}<option value="{$c|wash}"{if and( is_set( $filters.channel ), eq( $filters.channel, $c ) )} selected="selected"{/if}>{$c|wash}</option>{/foreach}
        </select>
    </div>
    <div>
        <label for="au-name">{'Event name'|i18n( 'design/admin/audit' )}</label>
        <input id="au-name" type="text" name="Name" list="au-names" placeholder="access.session.*" value="{first_set( $filters.name, '' )|wash}" />
        <datalist id="au-names">{foreach $names as $n}<option value="{$n|wash}"></option>{/foreach}</datalist>
    </div>
    <div>
        <label for="au-login">{'Login'|i18n( 'design/admin/audit' )}</label>
        <input id="au-login" type="text" name="Login" value="{first_set( $filters.login, '' )|wash}" />
    </div>
    <div>
        <label for="au-user">{'User ID'|i18n( 'design/admin/audit' )}</label>
        <input id="au-user" type="text" name="User" inputmode="numeric" value="{first_set( $filters.user, '' )|wash}" />
    </div>
    <div>
        <label for="au-result">{'Result'|i18n( 'design/admin/audit' )}</label>
        <select id="au-result" name="Result">
            <option value="">{'All'|i18n( 'design/admin/audit' )}</option>
            {foreach array( 'success', 'refused', 'failed' ) as $r}<option value="{$r}"{if and( is_set( $filters.result ), eq( $filters.result, $r ) )} selected="selected"{/if}>{$r|i18n( 'design/admin/audit' )}</option>{/foreach}
        </select>
    </div>
    <div>
        <label for="au-severity">{'Severity at least'|i18n( 'design/admin/audit' )}</label>
        <select id="au-severity" name="Severity">
            <option value="">{'All'|i18n( 'design/admin/audit' )}</option>
            {foreach $severities as $s}<option value="{$s}"{if and( is_set( $filters.severity ), eq( $filters.severity, $s ) )} selected="selected"{/if}>{$s}</option>{/foreach}
        </select>
    </div>
    <div>
        <label for="au-from">{'From'|i18n( 'design/admin/audit' )}</label>
        <input id="au-from" type="date" name="From" value="{first_set( $filters.from, '' )|wash}" />
    </div>
    <div>
        <label for="au-to">{'To'|i18n( 'design/admin/audit' )}</label>
        <input id="au-to" type="date" name="To" value="{first_set( $filters.to, '' )|wash}" />
    </div>
    <div>
        <label for="au-object">{'Object'|i18n( 'design/admin/audit' )}</label>
        <input id="au-object" type="text" name="Object" placeholder="node:275" value="{first_set( $filters.object, '' )|wash}" />
    </div>
    <div>
        <label for="au-ip">{'Address'|i18n( 'design/admin/audit' )}</label>
        <input id="au-ip" type="text" name="IP" placeholder="203.0.113.0/24" value="{first_set( $filters.ip, '' )|wash}" />
    </div>
    <div class="au-wide">
        <label for="au-q">{'Search'|i18n( 'design/admin/audit' )}</label>
        <input id="au-q" type="text" name="Q" value="{first_set( $filters.q, '' )|wash}" />
    </div>
    {foreach hash( 'request', 'Request', 'job', 'Job', 'run', 'Run', 'target', 'Target', 'legacy_file', 'LegacyFile', 'parent', 'Parent' ) as $k => $param}{if is_set( $filters[$k] )}<input type="hidden" name="{$param}" value="{$filters[$k]|wash}" />{/if}{/foreach}
    <div class="au-actions">
        <input class="button defaultbutton" type="submit" name="Filter" value="{'Filter'|i18n( 'design/admin/audit' )|wash}" />
        <a href={'audit/console'|ezurl}>{'Reset'|i18n( 'design/admin/audit' )}</a>
    </div>
</form>

{if $active_filters|count}
<ul class="au-active" aria-label="{'Active filters'|i18n( 'design/admin/audit' )|wash}">
{foreach $active_filters as $a}
    <li>{$a.key|wash}: <strong>{$a.value|wash}</strong> <a href={$a.remove_url|ezurl} title="{'Remove this filter'|i18n( 'design/admin/audit' )|wash}" aria-label="{'Remove this filter'|i18n( 'design/admin/audit' )|wash}">&times;</a></li>
{/foreach}
</ul>
{/if}

<div class="au-bar">
    <span>{'%total events'|i18n( 'design/admin/audit',, hash( '%total', $total ) )}{if eq( $source, 'files' )} &middot; <span class="au-note">{'read from the files (no index)'|i18n( 'design/admin/audit' )}</span>{elseif $unindexed} &middot; <span class="au-note">{'%n not indexed yet, read from the files'|i18n( 'design/admin/audit',, hash( '%n', $unindexed ) )}</span>{/if}</span>
    <span><a href={$charts_url|ezurl}>{'Charts of this filter'|i18n( 'design/admin/audit' )}</a> &middot; <a href={$export_url|ezurl}>{'Export'|i18n( 'design/admin/audit' )}</a></span>
</div>
</div>

{if $events|count}
<div class="au-table">
<table class="list" cellspacing="0">
<tr>
    <th>{'Time'|i18n( 'design/admin/audit' )}</th>
    <th>{'Event'|i18n( 'design/admin/audit' )}</th>
    <th>{'Actor'|i18n( 'design/admin/audit' )}</th>
    <th>{'Object'|i18n( 'design/admin/audit' )}</th>
    <th>{'Result'|i18n( 'design/admin/audit' )}</th>
    <th>{'Request'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $events as $e sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}{if $e.depth} au-child{/if}">
    <td class="au-time" title="{$e.time_utc|wash}">{$e.time|wash}</td>
    <td class="au-name">{if $e.depth}&#8627; {/if}<a href={concat( 'audit/event/', $e.id )|ezurl}>{$e.label|wash}</a><br /><code>{$e.name|wash}</code> <span class="au-sub">{$e.channel|wash} #{$e.seq}, {$e.severity|wash}{if $e.pseudonymised}, {'pseudonymised'|i18n( 'design/admin/audit' )}{/if}</span></td>
    <td>{if $e.login}<a href={$e.login_url|ezurl}>{$e.actor|wash}</a>{else}{$e.actor|wash}{/if}{if $e.ip}<br /><span class="au-sub au-nowrap">{$e.ip|wash}</span>{/if}</td>
    <td>{if $e.object_type}<a href={$e.object_url|ezurl}>{$e.object|wash}</a>{/if}{if $e.target}<br /><span class="au-sub">&rarr; {$e.target|wash}</span>{/if}</td>
    <td><span class="au-result {$e.result|wash}">{$e.result|wash}</span>{if $e.reason}<br /><span class="au-sub">{$e.reason|wash}</span>{/if}</td>
    <td>{if $e.request}<a href={$e.request_url|ezurl}><code>{$e.request|shorten( 14 )|wash}</code></a>{/if}{if $e.engine}<br /><span class="au-sub">{$e.engine|wash}{if $e.siteaccess}, {$e.siteaccess|wash}{/if}</span>{/if}</td>
</tr>
{/foreach}
</table>
</div>

<nav class="au-pager" aria-label="{'Pages'|i18n( 'design/admin/audit' )|wash}">
    <span>{if $newer_url}<a href={$newer_url|ezurl} rel="prev">&larr; {'Newer'|i18n( 'design/admin/audit' )}</a>{/if}</span>
    <span>{'Page %page of %pages'|i18n( 'design/admin/audit',, hash( '%page', $page, '%pages', $pages ) )}</span>
    <span>{if $older_url}<a href={$older_url|ezurl} rel="next">{'Older'|i18n( 'design/admin/audit' )} &rarr;</a>{/if}</span>
</nav>
{else}
<div class="block au-empty"><p>{'No audit events match this filter.'|i18n( 'design/admin/audit' )}</p></div>
{/if}

<p class="au-note">{'Search: %kind. Opening this page is itself recorded (system.audit.read).'|i18n( 'design/admin/audit',, hash( '%kind', $fulltext ) )|wash}</p>

{* DESIGN: Content END *}</div></div></div>
</div>
