{* audit/charts: events per day per channel (stacked bars, fixed channel colours), refusals and failures per day,
   logins and failed logins per day, the most active actors and the most frequent events; each chart has its
   numbers in a table below it. Variables: index_usable, filters, days, console_url, channel_names, severities,
   names, and with the index: day_rows, legend, top_actors, top_names, total, max_day, from. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-charts">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit charts'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form class="au-filter" method="post" action={'audit/charts'|ezurl} aria-label="{'Filter the charts'|i18n( 'design/admin/audit' )|wash}">
    <div>
        <label for="au-channel">{'Channel'|i18n( 'design/admin/audit' )}</label>
        <select id="au-channel" name="Channel">
            <option value="">{'All'|i18n( 'design/admin/audit' )}</option>
            {foreach $channel_names as $c}<option value="{$c|wash}"{if and( is_set( $filters.channel ), eq( $filters.channel, $c ) )} selected="selected"{/if}>{$c|wash}</option>{/foreach}
        </select>
    </div>
    <div>
        <label for="au-name">{'Event name'|i18n( 'design/admin/audit' )}</label>
        <input id="au-name" type="text" name="Name" list="au-names" placeholder="access.*" value="{first_set( $filters.name, '' )|wash}" />
        <datalist id="au-names">{foreach $names as $n}<option value="{$n|wash}"></option>{/foreach}</datalist>
    </div>
    <div>
        <label for="au-from">{'From'|i18n( 'design/admin/audit' )}</label>
        <input id="au-from" type="date" name="From" value="{first_set( $filters.from, '' )|wash}" />
    </div>
    <div>
        <label for="au-to">{'To'|i18n( 'design/admin/audit' )}</label>
        <input id="au-to" type="date" name="To" value="{first_set( $filters.to, '' )|wash}" />
    </div>
    <div class="au-actions">
        <input class="button defaultbutton" type="submit" name="Filter" value="{'Filter'|i18n( 'design/admin/audit' )|wash}" />
        <a href={'audit/charts'|ezurl}>{'Reset'|i18n( 'design/admin/audit' )}</a>
    </div>
</form>

{if $index_usable|not}
<div class="block au-empty"><p>{'Charts need the audit index (audit.ini [AuditIndexSettings] Index=enabled and its tables). The console still reads the files.'|i18n( 'design/admin/audit' )}</p></div>
{else}

<p class="au-bar"><span>{'%total events since %from'|i18n( 'design/admin/audit',, hash( '%total', $total, '%from', $from ) )}</span><a href={$console_url|ezurl}>{'These events in the console'|i18n( 'design/admin/audit' )}</a></p>

<div class="au-cards">
<section class="au-card au-chart" aria-labelledby="au-c1">
    <h2 class="au-h" id="au-c1">{'Events per day and channel'|i18n( 'design/admin/audit' )}</h2>
    <ul class="au-legend">{foreach $legend as $l}<li><span class="au-key s{$l.slot}"></span>{$l.channel|wash}</li>{/foreach}</ul>
    <div class="au-rows">
    {foreach $day_rows as $d}
        <span class="au-lab">{$d.label|wash}</span>
        <span class="au-track" role="img" aria-label="{$d.day|wash}: {$d.sum}">{foreach $d.segments as $s}{if $s.n}<span class="au-seg s{$s.slot}" style="width: {$s.pct}%" title="{$d.day|wash} &middot; {$s.channel|wash}: {$s.n}" tabindex="0"></span>{/if}{/foreach}</span>
        <span class="au-val">{$d.sum}</span>
    {/foreach}
    </div>
    <details class="au-data"><summary>{'The numbers'|i18n( 'design/admin/audit' )}</summary>
    <table class="list" cellspacing="0"><tr><th>{'Day'|i18n( 'design/admin/audit' )}</th>{foreach $legend as $l}<th>{$l.channel|wash}</th>{/foreach}<th>{'Total'|i18n( 'design/admin/audit' )}</th></tr>
    {foreach $day_rows as $d}<tr><td>{$d.day|wash}</td>{foreach $d.segments as $s}<td>{$s.n}</td>{/foreach}<td>{$d.sum}</td></tr>{/foreach}</table>
    </details>
</section>

<section class="au-card au-chart" aria-labelledby="au-c2">
    <h2 class="au-h" id="au-c2">{'Refusals and failures per day'|i18n( 'design/admin/audit' )}</h2>
    <ul class="au-legend"><li><span class="au-key serious"></span>{'refused'|i18n( 'design/admin/audit' )}</li><li><span class="au-key critical"></span>{'failed'|i18n( 'design/admin/audit' )}</li></ul>
    <div class="au-rows">
    {foreach $day_rows as $d}
        <span class="au-lab">{$d.label|wash}</span>
        <span class="au-track">{if $d.refused}<span class="au-seg serious" style="width: {$d.refused_pct}%" title="{$d.day|wash} &middot; {'refused'|i18n( 'design/admin/audit' )}: {$d.refused}" tabindex="0"></span>{/if}{if $d.failed}<span class="au-seg critical" style="width: {$d.failed_pct}%" title="{$d.day|wash} &middot; {'failed'|i18n( 'design/admin/audit' )}: {$d.failed}" tabindex="0"></span>{/if}</span>
        <span class="au-val">{sum( $d.refused, $d.failed )}</span>
    {/foreach}
    </div>
    <details class="au-data"><summary>{'The numbers'|i18n( 'design/admin/audit' )}</summary>
    <table class="list" cellspacing="0"><tr><th>{'Day'|i18n( 'design/admin/audit' )}</th><th>{'refused'|i18n( 'design/admin/audit' )}</th><th>{'failed'|i18n( 'design/admin/audit' )}</th></tr>
    {foreach $day_rows as $d}<tr><td>{$d.day|wash}</td><td>{$d.refused}</td><td>{$d.failed}</td></tr>{/foreach}</table>
    </details>
</section>

<section class="au-card au-chart" aria-labelledby="au-c3">
    <h2 class="au-h" id="au-c3">{'Logins and failed logins per day'|i18n( 'design/admin/audit' )}</h2>
    <ul class="au-legend"><li><span class="au-key s1"></span>{'logins'|i18n( 'design/admin/audit' )}</li><li><span class="au-key critical"></span>{'failed logins'|i18n( 'design/admin/audit' )}</li></ul>
    <div class="au-rows">
    {foreach $day_rows as $d}
        <span class="au-lab">{$d.label|wash}</span>
        <span class="au-track" style="flex-direction: column; height: 18px; gap: 2px">
            <span class="au-seg s1" style="width: {$d.logins_pct}%; height: 8px; border-radius: 0 4px 4px 0" title="{$d.day|wash} &middot; {'logins'|i18n( 'design/admin/audit' )}: {$d.logins}" tabindex="0"></span>
            <span class="au-seg critical" style="width: {$d.failed_logins_pct}%; height: 8px; border-radius: 0 4px 4px 0" title="{$d.day|wash} &middot; {'failed logins'|i18n( 'design/admin/audit' )}: {$d.failed_logins}" tabindex="0"></span>
        </span>
        <span class="au-val">{$d.logins} / {$d.failed_logins}</span>
    {/foreach}
    </div>
</section>

<section class="au-card au-chart" aria-labelledby="au-c4">
    <h2 class="au-h" id="au-c4">{'Most active actors'|i18n( 'design/admin/audit' )}</h2>
    {if $top_actors|count}
    <div class="au-rows">
    {foreach $top_actors as $a}
        <span class="au-lab"><a href={concat( 'audit/console/(login)/', $a.value )|ezurl}>{if $a.value}{$a.value|wash}{else}&ndash;{/if}</a></span>
        <span class="au-track"><span class="au-seg s1" style="width: {$a.pct}%" title="{$a.value|wash}: {$a.n}" tabindex="0"></span></span>
        <span class="au-val">{$a.n}</span>
    {/foreach}
    </div>
    {else}<p class="au-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

<section class="au-card au-chart" aria-labelledby="au-c5">
    <h2 class="au-h" id="au-c5">{'Most frequent events'|i18n( 'design/admin/audit' )}</h2>
    {if $top_names|count}
    <div class="au-rows">
    {foreach $top_names as $a}
        <span class="au-lab"><a href={concat( 'audit/console/(name)/', $a.value )|ezurl} title="{$a.value|wash}">{$a.label|wash}</a></span>
        <span class="au-track"><span class="au-seg s1" style="width: {$a.pct}%" title="{$a.value|wash}: {$a.n}" tabindex="0"></span></span>
        <span class="au-val">{$a.n}</span>
    {/foreach}
    </div>
    {else}<p class="au-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>
</div>
