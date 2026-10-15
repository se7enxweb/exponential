{* audit/charts: events per day per channel (stacked bars, fixed channel colours), refusals and failures per day,
   logins and failed logins per day, the most active actors and the most frequent events; each chart has its
   numbers in a table below it. Variables: index_usable, filters, days, console_url, channel_options, severities,
   names, audit_sources (audit/sources.tpl), and with the index: day_rows, legend, top_actors, top_names, total,
   max_day, from, source_charts (the charts of the extensions' sources: title, console_url, series, rows). *}
<div class="context-block a4-audit-view a4-page" id="exp-audit-charts">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit charts'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<form class="a4-audit-filter" method="post" action={'audit/charts'|ezurl} aria-label="{'Filter the charts'|i18n( 'design/admin/audit' )|wash}">
    <div>
        <label for="au-channel">{'Channel'|i18n( 'design/admin/audit' )}</label>
        <select id="au-channel" name="Channel">
            <option value="">{'All'|i18n( 'design/admin/audit' )}</option>
            {foreach $channel_options as $c}<option value="{$c.channel|wash}"{if and( is_set( $filters.channel ), eq( $filters.channel, $c.channel ) )} selected="selected"{/if}>{$c.label|wash}</option>{/foreach}
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
    <div class="a4-audit-actions">
        <input class="button defaultbutton" type="submit" name="Filter" value="{'Filter'|i18n( 'design/admin/audit' )|wash}" />
        <a href={'audit/charts'|ezurl}>{'Reset'|i18n( 'design/admin/audit' )}</a>
    </div>
</form>

{include uri='design:audit/sources.tpl'}

{if $index_usable|not}
<div class="block a4-audit-empty"><p>{'Charts need the audit index (audit.ini [AuditIndexSettings] Index=enabled and its tables). The console still reads the files.'|i18n( 'design/admin/audit' )}</p></div>
{else}

<p class="a4-audit-bar"><span>{'%total events since %from'|i18n( 'design/admin/audit',, hash( '%total', $total, '%from', $from ) )}</span><a href={$console_url|ezurl}>{'These events in the console'|i18n( 'design/admin/audit' )}</a></p>

<div class="a4-audit-cards">
<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-c1">
    <h2 class="a4-audit-h" id="au-c1">{'Events per day and channel'|i18n( 'design/admin/audit' )}</h2>
    <ul class="a4-audit-legend">{foreach $legend as $l}<li><span class="a4-audit-key a4-audit-s{$l.slot}"></span>{$l.channel|wash}</li>{/foreach}</ul>
    <div class="a4-audit-rows">
    {foreach $day_rows as $d}
        <span class="a4-audit-lab">{$d.label|wash}</span>
        <span class="a4-audit-track" role="img" aria-label="{$d.day|wash}: {$d.sum}">{foreach $d.segments as $s}{if $s.n}<span class="a4-audit-seg a4-audit-s{$s.slot} a4-pct-{max( $s.pct|round, 1 )}" title="{$d.day|wash} &middot; {$s.channel|wash}: {$s.n}" tabindex="0"></span>{/if}{/foreach}</span>
        <span class="a4-audit-val">{$d.sum}</span>
    {/foreach}
    </div>
    <details class="a4-audit-data"><summary>{'The numbers'|i18n( 'design/admin/audit' )}</summary>
    <table class="list" cellspacing="0"><tr><th scope="col">{'Day'|i18n( 'design/admin/audit' )}</th>{foreach $legend as $l}<th scope="col">{$l.channel|wash}</th>{/foreach}<th scope="col">{'Total'|i18n( 'design/admin/audit' )}</th></tr>
    {foreach $day_rows as $d}<tr><td>{$d.day|wash}</td>{foreach $d.segments as $s}<td>{$s.n}</td>{/foreach}<td>{$d.sum}</td></tr>{/foreach}</table>
    </details>
</section>

<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-c2">
    <h2 class="a4-audit-h" id="au-c2">{'Refusals and failures per day'|i18n( 'design/admin/audit' )}</h2>
    <ul class="a4-audit-legend"><li><span class="a4-audit-key a4-audit-serious"></span>{'refused'|i18n( 'design/admin/audit' )}</li><li><span class="a4-audit-key a4-audit-critical"></span>{'failed'|i18n( 'design/admin/audit' )}</li></ul>
    <div class="a4-audit-rows">
    {foreach $day_rows as $d}
        <span class="a4-audit-lab">{$d.label|wash}</span>
        <span class="a4-audit-track">{if $d.refused}<span class="a4-audit-seg a4-audit-serious a4-pct-{max( $d.refused_pct|round, 1 )}" title="{$d.day|wash} &middot; {'refused'|i18n( 'design/admin/audit' )}: {$d.refused}" tabindex="0"></span>{/if}{if $d.failed}<span class="a4-audit-seg a4-audit-critical a4-pct-{max( $d.failed_pct|round, 1 )}" title="{$d.day|wash} &middot; {'failed'|i18n( 'design/admin/audit' )}: {$d.failed}" tabindex="0"></span>{/if}</span>
        <span class="a4-audit-val">{sum( $d.refused, $d.failed )}</span>
    {/foreach}
    </div>
    <details class="a4-audit-data"><summary>{'The numbers'|i18n( 'design/admin/audit' )}</summary>
    <table class="list" cellspacing="0"><tr><th scope="col">{'Day'|i18n( 'design/admin/audit' )}</th><th scope="col">{'refused'|i18n( 'design/admin/audit' )}</th><th scope="col">{'failed'|i18n( 'design/admin/audit' )}</th></tr>
    {foreach $day_rows as $d}<tr><td>{$d.day|wash}</td><td>{$d.refused}</td><td>{$d.failed}</td></tr>{/foreach}</table>
    </details>
</section>

<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-c3">
    <h2 class="a4-audit-h" id="au-c3">{'Logins and failed logins per day'|i18n( 'design/admin/audit' )}</h2>
    <ul class="a4-audit-legend"><li><span class="a4-audit-key a4-audit-s1"></span>{'logins'|i18n( 'design/admin/audit' )}</li><li><span class="a4-audit-key a4-audit-critical"></span>{'failed logins'|i18n( 'design/admin/audit' )}</li></ul>
    <div class="a4-audit-rows">
    {foreach $day_rows as $d}
        <span class="a4-audit-lab">{$d.label|wash}</span>
        <span class="a4-audit-track a4-audit-pair">
            <span class="a4-audit-seg a4-audit-s1 a4-pct-{$d.logins_pct|round}" title="{$d.day|wash} &middot; {'logins'|i18n( 'design/admin/audit' )}: {$d.logins}" tabindex="0"></span>
            <span class="a4-audit-seg a4-audit-critical a4-pct-{$d.failed_logins_pct|round}" title="{$d.day|wash} &middot; {'failed logins'|i18n( 'design/admin/audit' )}: {$d.failed_logins}" tabindex="0"></span>
        </span>
        <span class="a4-audit-val">{$d.logins} / {$d.failed_logins}</span>
    {/foreach}
    </div>
</section>

<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-c4">
    <h2 class="a4-audit-h" id="au-c4">{'Most active actors'|i18n( 'design/admin/audit' )}</h2>
    {if $top_actors|count}
    <div class="a4-audit-rows">
    {foreach $top_actors as $a}
        <span class="a4-audit-lab"><a href={concat( 'audit/console/(login)/', $a.value )|ezurl}>{if $a.value}{$a.value|wash}{else}&ndash;{/if}</a></span>
        <span class="a4-audit-track"><span class="a4-audit-seg a4-audit-s1 a4-pct-{max( $a.pct|round, 1 )}" title="{$a.value|wash}: {$a.n}" tabindex="0"></span></span>
        <span class="a4-audit-val">{$a.n}</span>
    {/foreach}
    </div>
    {else}<p class="a4-audit-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-c5">
    <h2 class="a4-audit-h" id="au-c5">{'Most frequent events'|i18n( 'design/admin/audit' )}</h2>
    {if $top_names|count}
    <div class="a4-audit-rows">
    {foreach $top_names as $a}
        <span class="a4-audit-lab"><a href={concat( 'audit/console/(name)/', $a.value )|ezurl} title="{$a.value|wash}">{$a.label|wash}</a></span>
        <span class="a4-audit-track"><span class="a4-audit-seg a4-audit-s1 a4-pct-{max( $a.pct|round, 1 )}" title="{$a.value|wash}: {$a.n}" tabindex="0"></span></span>
        <span class="a4-audit-val">{$a.n}</span>
    {/foreach}
    </div>
    {else}<p class="a4-audit-note">{'No events.'|i18n( 'design/admin/audit' )}</p>{/if}
</section>

{foreach $source_charts as $ci => $sc}
<section class="a4-audit-card a4-audit-chart" aria-labelledby="au-cs{$ci}" id="au-chart-{$sc.identifier|wash}-{$ci}">
    <h2 class="a4-audit-h" id="au-cs{$ci}">{$sc.title|wash}</h2>
    <ul class="a4-audit-legend">{foreach $sc.series as $s}<li><span class="a4-audit-key a4-audit-s{$s.slot}"></span><a href={$s.url|ezurl} title="{$s.name|wash}">{$s.label|wash}</a> {$s.total}</li>{/foreach}</ul>
    <div class="a4-audit-rows">
    {foreach $sc.rows as $r}
        <span class="a4-audit-lab">{$r.label|wash}</span>
        <span class="a4-audit-track a4-audit-multi">{foreach $r.values as $v}<span class="a4-audit-seg a4-audit-s{$v.slot} a4-pct-{$v.pct|round}" title="{$r.day|wash} &middot; {$v.label|wash}: {$v.n}"{if $v.n} tabindex="0"{/if}></span>{/foreach}</span>
        <span class="a4-audit-val">{$r.text|wash}</span>
    {/foreach}
    </div>
    <details class="a4-audit-data"><summary>{'The numbers'|i18n( 'design/admin/audit' )}</summary>
    <table class="list" cellspacing="0"><tr><th scope="col">{'Day'|i18n( 'design/admin/audit' )}</th>{foreach $sc.series as $s}<th scope="col">{$s.label|wash}</th>{/foreach}</tr>
    {foreach $sc.rows as $r}<tr><td>{$r.day|wash}</td>{foreach $r.values as $v}<td>{$v.n}</td>{/foreach}</tr>{/foreach}</table>
    </details>
    <p class="a4-audit-sub"><a href={$sc.console_url|ezurl}>{'These events in the console'|i18n( 'design/admin/audit' )}</a></p>
</section>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>
</div>
