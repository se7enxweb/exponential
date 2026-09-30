{* Store dashboard (shop/dashboard). All figures come from kernel/shop/dashboard.php as $dashboard. *}
{def $d = $dashboard
     $ctx = 'design/admin/shop/dashboard'
     $fmt = false()
     $p = false()
     $main = $dashboard.main_currency
     $mainfmt = $dashboard.format[$dashboard.main_currency]
     $t = false()}

<style type="text/css">{literal}
.shop-dash { --sd-accent: #ff5500; --sd-ink: #3a3d41; --sd-muted: #6f6e6b; --sd-line: #e4e3e4; --sd-soft: #f5f5f5;
             --sd-link: #005b7f; --sd-crit: #c0392b; --sd-warn: #c77700; --sd-info: #005b7f; --sd-ok: #2e7d32;
             --sd-pending: #e67e22; --sd-processing: #2471a3; --sd-delivered: #2e7d32; --sd-custom: #7d3c98; --sd-archived: #9a9a9a;
             color: var(--sd-ink); }
.shop-dash * { box-sizing: border-box; }
.shop-dash h2 { font-size: 1.35em; margin: 1.6em 0 .5em; padding-bottom: .25em; border-bottom: 2px solid var(--sd-accent); }
.shop-dash h2:first-child { margin-top: .4em; }
.shop-dash h3 { font-size: 1.05em; margin: 1.1em 0 .4em; }
.shop-dash p.sd-lead { color: var(--sd-muted); margin: 0 0 .8em; }
.shop-dash .sd-head { display: flex; flex-wrap: wrap; gap: .6em 1.2em; align-items: baseline; justify-content: space-between; }
.shop-dash .sd-head h1 { margin: 0; }
.shop-dash .sd-meta { color: var(--sd-muted); font-size: .9em; }
.shop-dash .sd-pills { display: flex; flex-wrap: wrap; gap: .4em; margin: .6em 0 0; }
.shop-dash .sd-pill { display: inline-block; padding: .15em .7em; border-radius: 1em; font-size: .9em; font-weight: bold; color: #fff; text-decoration: none; }
.shop-dash a.sd-pill:hover { opacity: .85; color: #fff; }
.shop-dash .sd-pill.critical { background: var(--sd-crit); } .shop-dash .sd-pill.warning { background: var(--sd-warn); }
.shop-dash .sd-pill.info { background: var(--sd-info); } .shop-dash .sd-pill.ok { background: var(--sd-ok); }
.shop-dash .sd-tiles { display: grid; grid-template-columns: repeat(auto-fit, minmax(10.5em, 1fr)); gap: .7em; margin: .8em 0; }
.shop-dash .sd-tile { background: #fff; border: 1px solid var(--sd-line); border-top: 3px solid var(--sd-accent); border-radius: 4px; padding: .6em .75em .7em; min-width: 0; }
.shop-dash .sd-tile.alert { border-top-color: var(--sd-crit); background: #fff6f4; }
.shop-dash .sd-tile .sd-label { font-size: .82em; color: var(--sd-muted); text-transform: uppercase; letter-spacing: .03em; }
.shop-dash .sd-tile .sd-value { font-size: 1.7em; font-weight: bold; line-height: 1.2; margin: .1em 0; overflow-wrap: anywhere; }
.shop-dash .sd-tile .sd-value.small { font-size: 1.25em; }
.shop-dash .sd-tile .sd-sub { font-size: .85em; color: var(--sd-muted); }
.shop-dash .sd-up { color: var(--sd-ok); font-weight: bold; } .shop-dash .sd-down { color: var(--sd-crit); font-weight: bold; }
.shop-dash .sd-grid2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(20em, 1fr)); gap: 0 1.4em; }
.shop-dash .sd-card { min-width: 0; }
.shop-dash .sd-chart { display: flex; align-items: flex-end; gap: 2px; height: 9em; padding: .3em 0 0; border-bottom: 1px solid var(--sd-muted); }
.shop-dash .sd-chart .sd-bar { flex: 1 1 0; min-width: 0; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; position: relative; }
.shop-dash .sd-chart .sd-bar span { display: block; background: var(--sd-accent); border-radius: 2px 2px 0 0; opacity: .85; }
.shop-dash .sd-chart .sd-bar.today span { background: var(--sd-ink); }
.shop-dash .sd-chart .sd-bar:hover span { opacity: 1; }
.shop-dash .sd-chart .sd-bar em { display: block; text-align: center; font-style: normal; font-size: .7em; line-height: 1.3; color: var(--sd-muted); }
.shop-dash .sd-axis { display: flex; gap: 2px; font-size: .72em; color: var(--sd-muted); }
.shop-dash .sd-axis span { flex: 1 1 0; min-width: 0; text-align: left; overflow: visible; white-space: nowrap; }
.shop-dash .sd-stack { display: flex; height: 1.1em; border-radius: 3px; overflow: hidden; background: var(--sd-soft); margin: .3em 0 .6em; }
.shop-dash .sd-stack span { display: block; height: 100%; }
.shop-dash .sd-dot { display: inline-block; width: .75em; height: .75em; border-radius: 50%; margin-right: .35em; vertical-align: middle; }
.shop-dash .s1 { background: var(--sd-pending); } .shop-dash .s2 { background: var(--sd-processing); }
.shop-dash .s3 { background: var(--sd-delivered); } .shop-dash .sc { background: var(--sd-custom); } .shop-dash .sa { background: var(--sd-archived); }
.shop-dash .sd-scroll { overflow-x: auto; margin: 0 0 .8em; }
.shop-dash table.sd-table { width: 100%; border-collapse: collapse; font-size: .95em; }
.shop-dash table.sd-table th { background: var(--sd-muted); color: #fff; text-align: left; padding: .35em .5em; font-weight: bold; }
.shop-dash table.sd-table td.nw { white-space: nowrap; }
.shop-dash table.sd-table td { padding: .35em .5em; border-bottom: 1px solid var(--sd-line); vertical-align: top; }
.shop-dash table.sd-table tr:nth-child(even) td { background: #fafafa; }
.shop-dash table.sd-table td.num, .shop-dash table.sd-table th.num { text-align: right; white-space: nowrap; }
.shop-dash table.sd-table tr.overdue td { background: #fff1e8; }
.shop-dash .sd-meter { background: var(--sd-soft); height: .5em; border-radius: .25em; overflow: hidden; margin-top: .2em; }
.shop-dash .sd-meter span { display: block; height: 100%; background: var(--sd-accent); }
.shop-dash .sd-tag { display: inline-block; font-size: .8em; padding: 0 .45em; border-radius: .6em; background: var(--sd-soft); color: var(--sd-ink); white-space: nowrap; }
.shop-dash .sd-tag.bad { background: #fdecea; color: var(--sd-crit); } .shop-dash .sd-tag.good { background: #e8f5e9; color: var(--sd-ok); }
.shop-dash .sd-tag.warn { background: #fff3e0; color: var(--sd-warn); }
.shop-dash .sd-empty { color: var(--sd-muted); font-style: italic; padding: .5em 0; }
.shop-dash ol.sd-flow { list-style: none; margin: .5em 0 1em; padding: 0; counter-reset: sdstep; }
.shop-dash ol.sd-flow > li { position: relative; margin: 0 0 .6em; padding: .55em .8em .6em 3em; border: 1px solid var(--sd-line); border-left: 3px solid var(--sd-accent); border-radius: 4px; background: #fff; }
.shop-dash ol.sd-flow > li:before { counter-increment: sdstep; content: counter(sdstep); position: absolute; left: .7em; top: .55em; width: 1.6em; height: 1.6em; line-height: 1.6em; text-align: center; border-radius: 50%; background: var(--sd-accent); color: #fff; font-weight: bold; }
.shop-dash ol.sd-flow > li.missing { border-left-color: var(--sd-crit); }
.shop-dash ol.sd-flow > li.missing:before { background: var(--sd-crit); }
.shop-dash ol.sd-flow strong.sd-step { display: block; }
.shop-dash ol.sd-flow .sd-where { font-size: .85em; color: var(--sd-muted); margin-top: .2em; }
.shop-dash code { background: var(--sd-soft); padding: 0 .25em; border-radius: 2px; font-size: .92em; overflow-wrap: anywhere; }
.shop-dash dl.sd-kv { display: grid; grid-template-columns: minmax(8em, 40%) 1fr; gap: .25em .8em; margin: .4em 0 1em; }
.shop-dash dl.sd-kv dt { color: var(--sd-muted); } .shop-dash dl.sd-kv dd { margin: 0; overflow-wrap: anywhere; }
.shop-dash ul.sd-checklist { list-style: none; margin: .5em 0; padding: 0; }
.shop-dash ul.sd-checklist li { margin: 0 0 .5em; padding: .55em .8em .55em 2.6em; border-radius: 4px; position: relative; background: #fff; border: 1px solid var(--sd-line); }
.shop-dash ul.sd-checklist li:before { position: absolute; left: .7em; top: .45em; font-weight: bold; width: 1.3em; height: 1.3em; line-height: 1.3em; text-align: center; border-radius: 50%; color: #fff; }
.shop-dash ul.sd-checklist li.critical { border-left: 4px solid var(--sd-crit); } .shop-dash ul.sd-checklist li.critical:before { content: "!"; background: var(--sd-crit); }
.shop-dash ul.sd-checklist li.warning { border-left: 4px solid var(--sd-warn); } .shop-dash ul.sd-checklist li.warning:before { content: "!"; background: var(--sd-warn); }
.shop-dash ul.sd-checklist li.info { border-left: 4px solid var(--sd-info); } .shop-dash ul.sd-checklist li.info:before { content: "i"; background: var(--sd-info); }
.shop-dash ul.sd-checklist li.ok { border-left: 4px solid var(--sd-ok); } .shop-dash ul.sd-checklist li.ok:before { content: "\2713"; background: var(--sd-ok); }
.shop-dash ul.sd-checklist .sd-level { font-size: .78em; text-transform: uppercase; letter-spacing: .04em; font-weight: bold; margin-right: .4em; }
.shop-dash ul.sd-checklist li.critical .sd-level { color: var(--sd-crit); } .shop-dash ul.sd-checklist li.warning .sd-level { color: var(--sd-warn); }
.shop-dash ul.sd-checklist li.info .sd-level { color: var(--sd-info); }
.shop-dash .sd-toc { display: flex; flex-wrap: wrap; gap: .3em 1em; font-size: .92em; margin: .7em 0 0; padding: .45em .7em; background: var(--sd-soft); border-radius: 4px; }
.shop-dash .sd-note { font-size: .88em; color: var(--sd-muted); }
{/literal}</style>

<div class="context-block shop-dash">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<div class="sd-head">
    <h1 class="context-title">{'Store dashboard'|i18n( $ctx )}</h1>
    <span class="sd-meta">{'As of %time. Amounts include VAT. Periods count from midnight.'|i18n( $ctx, , hash( '%time', $d.now|l10n( 'shortdatetime' ) ) )}</span>
</div>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="sd-pills">
    {if $d.checklist_counts.critical}<a class="sd-pill critical" href="#sd-next">{'%count need action now'|i18n( $ctx, , hash( '%count', $d.checklist_counts.critical ) )}</a>{/if}
    {if $d.checklist_counts.warning}<a class="sd-pill warning" href="#sd-next">{'%count to check'|i18n( $ctx, , hash( '%count', $d.checklist_counts.warning ) )}</a>{/if}
    {if $d.checklist_counts.info}<a class="sd-pill info" href="#sd-next">{'%count tips'|i18n( $ctx, , hash( '%count', $d.checklist_counts.info ) )}</a>{/if}
    {if $d.checklist|count|eq( 0 )}<span class="sd-pill ok">{'Nothing to do'|i18n( $ctx )}</span>{/if}
</div>
<div class="sd-toc">
    <a href="#sd-glance">{'At a glance'|i18n( $ctx )}</a>
    <a href="#sd-orders">{'Orders'|i18n( $ctx )}</a>
    <a href="#sd-products">{'Products and baskets'|i18n( $ctx )}</a>
    <a href="#sd-how">{'How your shop works'|i18n( $ctx )}</a>
    <a href="#sd-next">{'Next steps'|i18n( $ctx )}</a>
</div>

{* ------------------------------------------------------------------ At a glance *}
<h2 id="sd-glance">{'At a glance'|i18n( $ctx )}</h2>

<div class="sd-tiles">
    <div class="sd-tile">
        <div class="sd-label">{'Orders today'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.periods.today.orders}</div>
        <div class="sd-sub">{foreach $d.periods.today.revenue as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )} {/foreach}{if $d.periods.today.orders|eq( 0 )}{'no revenue yet'|i18n( $ctx )}{/if}</div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Last 7 days'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.periods.days7.orders}</div>
        <div class="sd-sub">{'%count items sold'|i18n( $ctx, , hash( '%count', $d.periods.days7.items ) )}</div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Last 30 days'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.periods.days30.orders}</div>
        <div class="sd-sub">
            {set $t = $d.trends.orders}
            {if is_null( $t )}<span class="sd-up">{'new'|i18n( $ctx )}</span>{elseif $t|gt( 0 )}<span class="sd-up">+{$t} %</span>{elseif $t|lt( 0 )}<span class="sd-down">{$t} %</span>{else}&plusmn;0 %{/if}
            {'vs. %count in the 30 days before'|i18n( $ctx, , hash( '%count', $d.periods.previous30.orders ) )}
        </div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Revenue, 30 days'|i18n( $ctx )}</div>
        {if $d.periods.days30.revenue|count}
            {foreach $d.periods.days30.revenue as $cur => $sum}
            <div class="sd-value{if $d.periods.days30.revenue|count|gt( 1 )} small{/if}">{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}</div>
            {/foreach}
        {else}
            <div class="sd-value">&ndash;</div>
        {/if}
        <div class="sd-sub">
            {set $t = $d.trends.revenue}
            {if is_null( $t )}<span class="sd-up">{'new'|i18n( $ctx )}</span>{elseif $t|gt( 0 )}<span class="sd-up">+{$t} %</span>{elseif $t|lt( 0 )}<span class="sd-down">{$t} %</span>{else}&plusmn;0 %{/if}
            {'%currency vs. the 30 days before'|i18n( $ctx, , hash( '%currency', $main ) )}
        </div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Average order, 30 days'|i18n( $ctx )}</div>
        {if $d.periods.days30.average|count}
            {foreach $d.periods.days30.average as $cur => $sum}
            <div class="sd-value{if $d.periods.days30.average|count|gt( 1 )} small{/if}">{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}</div>
            {/foreach}
        {else}
            <div class="sd-value">&ndash;</div>
        {/if}
        <div class="sd-sub">{'%items items per order'|i18n( $ctx, , hash( '%items', cond( $d.periods.days30.orders|gt( 0 ), div( $d.periods.days30.items, $d.periods.days30.orders )|l10n( 'number' ), 0 ) ) )}</div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Customers, 30 days'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.periods.days30.customers}</div>
        <div class="sd-sub">{'%new new, %repeat buy again, %all in total'|i18n( $ctx, , hash( '%new', $d.all_time.new_customers_30, '%repeat', $d.all_time.repeat_customers, '%all', $d.all_time.customers ) )}</div>
    </div>
    <div class="sd-tile{if or( $d.overdue.pending, $d.overdue.processing, $d.overdue.other )} alert{/if}">
        <div class="sd-label">{'Open orders'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.all_time.open}</div>
        <div class="sd-sub">{'not yet delivered; %late waiting too long'|i18n( $ctx, , hash( '%late', sum( $d.overdue.pending, $d.overdue.processing, $d.overdue.other ) ) )}</div>
    </div>
    <div class="sd-tile">
        <div class="sd-label">{'Baskets'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.baskets.fresh.count} / {$d.baskets.abandoned.count}</div>
        <div class="sd-sub">{'in progress / abandoned (idle over %hours h)'|i18n( $ctx, , hash( '%hours', $d.limits.abandoned_hours ) )}</div>
    </div>
    <div class="sd-tile{if $d.products.no_price} alert{/if}">
        <div class="sd-label">{'Products'|i18n( $ctx )}</div>
        <div class="sd-value">{$d.products.total}</div>
        <div class="sd-sub">{'%count without a price'|i18n( $ctx, , hash( '%count', $d.products.no_price ) )}</div>
    </div>
</div>

<h3>{'Revenue per day, last 30 days (%currency)'|i18n( $ctx, , hash( '%currency', $main ) )}</h3>
{if $d.max_daily_revenue|gt( 0 )}
<div class="sd-chart" role="img" aria-label="{'Revenue per day, last 30 days (%currency)'|i18n( $ctx, , hash( '%currency', $main ) )|wash}">
{foreach $d.daily as $day}
    <div class="sd-bar{if $day.is_today} today{/if}" title="{$day.date|l10n( 'shortdate' )}: {$day.revenue|l10n( 'currency', $mainfmt.locale, $mainfmt.symbol )|wash} &middot; {'%count orders'|i18n( $ctx, , hash( '%count', $day.orders ) )|wash}">{if $day.orders}<em>{$day.orders}</em>{/if}<span style="height: {$day.height}%"></span></div>
{/foreach}
</div>
<div class="sd-axis">
{foreach $d.daily as $index => $day}
    <span>{if or( $day.is_monday, $day.is_today, $index|eq( 0 ) )}{$day.date|datetime( 'custom', '%d.%m' )}{/if}</span>
{/foreach}
</div>
<p class="sd-note">{'Bar height is the revenue of the day, the number above it the orders. The dark bar is today. Highest day: %max.'|i18n( $ctx, , hash( '%max', $d.max_daily_revenue|l10n( 'currency', $mainfmt.locale, $mainfmt.symbol ) ) )}</p>
{else}
<p class="sd-empty">{'No orders in the last 30 days.'|i18n( $ctx )}</p>
{/if}

<div class="sd-grid2">
<div class="sd-card">
    <h3>{'Order status'|i18n( $ctx )}</h3>
    {if $d.all_time.orders}
    <div class="sd-stack">
        {foreach $d.statuses as $s}{if $s.open}<span class="{if $s.status_id|eq( 1 )}s1{elseif $s.status_id|eq( 2 )}s2{elseif $s.status_id|eq( 3 )}s3{else}sc{/if}" style="width: {$s.percent}%" title="{$s.name|wash}: {$s.open}"></span>{/if}{/foreach}
    </div>
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Status'|i18n( $ctx )}</th><th class="num">{'Orders'|i18n( $ctx )}</th><th class="num">{'Archived'|i18n( $ctx )}</th><th>{'Longest in status'|i18n( $ctx )}</th></tr>
    {foreach $d.statuses as $s}
    <tr>
        <td class="nw"><span class="sd-dot {if $s.status_id|eq( 1 )}s1{elseif $s.status_id|eq( 2 )}s2{elseif $s.status_id|eq( 3 )}s3{else}sc{/if}"></span>{$s.name|wash}{if $s.is_active|not} <span class="sd-tag">{'inactive'|i18n( $ctx )}</span>{/if}</td>
        <td class="num">{$s.open}</td>
        <td class="num">{$s.archived}</td>
        <td>{if and( $s.oldest, $s.open, $s.status_id|ne( 3 ) )}{'since %date'|i18n( $ctx, , hash( '%date', $s.oldest|l10n( 'shortdate' ) ) )}{else}&ndash;{/if}</td>
    </tr>
    {/foreach}
    <tr><td><strong>{'All orders'|i18n( $ctx )}</strong></td><td class="num"><strong>{sub( $d.all_time.orders, $d.all_time.archived )}</strong></td><td class="num"><strong>{$d.all_time.archived}</strong></td><td>{if $d.all_time.first_order}{'first order %date'|i18n( $ctx, , hash( '%date', $d.all_time.first_order|l10n( 'shortdate' ) ) )}{/if}</td></tr>
    </table></div>
    {else}
    <p class="sd-empty">{'No orders yet.'|i18n( $ctx )}</p>
    {/if}
</div>

<div class="sd-card">
    <h3>{'Top products, last 30 days'|i18n( $ctx )}</h3>
    {if $d.top_products|count}
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Product'|i18n( $ctx )}</th><th class="num">{'Sold'|i18n( $ctx )}</th><th class="num">{'Orders'|i18n( $ctx )}</th><th class="num">{'Revenue'|i18n( $ctx )}</th></tr>
    {foreach $d.top_products as $tp}
    <tr>
        <td>{if $tp.node_id}<a href={concat( '/content/view/full/', $tp.node_id )|ezurl}>{$tp.name|wash}</a>{else}{$tp.name|wash} <span class="sd-tag bad">{'removed'|i18n( $ctx )}</span>{/if}
            <div class="sd-meter"><span style="width: {$tp.percent}%"></span></div></td>
        <td class="num">{$tp.quantity}</td>
        <td class="num">{$tp.orders}</td>
        <td class="num">{foreach $tp.revenue as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td>
    </tr>
    {/foreach}
    </table></div>
    <p class="sd-note"><a href={'/shop/statistics'|ezurl}>{'Product statistics per month and year'|i18n( $ctx )}</a></p>
    {else}
    <p class="sd-empty">{'Nothing sold in the last 30 days.'|i18n( $ctx )}</p>
    {/if}
</div>
</div>

{* ------------------------------------------------------------------ Orders *}
<h2 id="sd-orders">{'Orders'|i18n( $ctx )}</h2>

<h3>{'Waiting for you'|i18n( $ctx )}</h3>
<p class="sd-lead">{'Every order that is not delivered and not archived, the one waiting longest first. Highlighted: Pending for more than %pending days, or any other open status for more than %processing days.'|i18n( $ctx, , hash( '%pending', $d.limits.pending_days, '%processing', $d.limits.processing_days ) )}</p>
{if $d.waiting|count}
<div class="sd-scroll"><table class="sd-table">
<tr><th>{'Order'|i18n( $ctx )}</th><th>{'Customer'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th><th>{'Waiting'|i18n( $ctx )}</th><th class="num">{'Total'|i18n( $ctx )}</th></tr>
{foreach $d.waiting as $o}
<tr{if $o.overdue} class="overdue"{/if}>
    <td><a href={concat( '/shop/orderview/', $o.id, '/' )|ezurl}>#{$o.order_nr}</a><div class="sd-note">{$o.created|l10n( 'shortdatetime' )}</div></td>
    <td><a href={concat( '/shop/customerorderview/', $o.user_id, '/', $o.email )|ezurl}>{$o.customer|wash}</a></td>
    <td class="nw"><span class="sd-dot {if $o.status_id|eq( 1 )}s1{elseif $o.status_id|eq( 2 )}s2{else}sc{/if}"></span>{$o.status_name|wash}</td>
    <td>{if $o.wait_days|gt( 0 )}{'%count days'|i18n( $ctx, , hash( '%count', $o.wait_days ) )}{else}{'%count hours'|i18n( $ctx, , hash( '%count', $o.wait_hours ) )}{/if}{if $o.overdue} <span class="sd-tag bad">{'too long'|i18n( $ctx )}</span>{/if}</td>
    <td class="num">{$o.total|l10n( 'currency', $d.format[$o.currency].locale, $d.format[$o.currency].symbol )}</td>
</tr>
{/foreach}
</table></div>
{if $d.all_time.open|gt( $d.waiting|count )}<p class="sd-note">{'%shown of %count open orders shown.'|i18n( $ctx, , hash( '%shown', $d.waiting|count, '%count', $d.all_time.open ) )} <a href={'/shop/orderlist'|ezurl}>{'All orders'|i18n( $ctx )}</a></p>{/if}
{else}
<p class="sd-empty">{'No open orders: everything is delivered or archived.'|i18n( $ctx )}</p>
{/if}

<h3>{'Latest orders'|i18n( $ctx )}</h3>
{if $d.latest|count}
<div class="sd-scroll"><table class="sd-table">
<tr><th>{'Order'|i18n( $ctx )}</th><th>{'Placed'|i18n( $ctx )}</th><th>{'Customer'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th><th class="num">{'Total'|i18n( $ctx )}</th></tr>
{foreach $d.latest as $o}
<tr>
    <td><a href={concat( '/shop/orderview/', $o.id, '/' )|ezurl}>#{$o.order_nr}</a></td>
    <td>{$o.created|l10n( 'shortdatetime' )}</td>
    <td><a href={concat( '/shop/customerorderview/', $o.user_id, '/', $o.email )|ezurl}>{$o.customer|wash}</a></td>
    <td><span class="sd-dot {if $o.is_archived}sa{elseif $o.status_id|eq( 1 )}s1{elseif $o.status_id|eq( 2 )}s2{elseif $o.status_id|eq( 3 )}s3{else}sc{/if}"></span>{$o.status_name|wash}{if $o.is_archived} <span class="sd-tag">{'archived'|i18n( $ctx )}</span>{/if}</td>
    <td class="num">{$o.total|l10n( 'currency', $d.format[$o.currency].locale, $d.format[$o.currency].symbol )}</td>
</tr>
{/foreach}
</table></div>
<p class="sd-note"><a href={'/shop/orderlist'|ezurl}>{'Order list'|i18n( $ctx )}</a> &middot; <a href={'/shop/archivelist'|ezurl}>{'Archive'|i18n( $ctx )}</a> &middot; <a href={'/shop/customerlist'|ezurl}>{'Customers'|i18n( $ctx )}</a></p>
{else}
<p class="sd-empty">{'No orders yet.'|i18n( $ctx )}</p>
{/if}

{* ------------------------------------------------------------------ Products and baskets *}
<h2 id="sd-products">{'Products and baskets'|i18n( $ctx )}</h2>

<div class="sd-grid2">
<div class="sd-card">
    <h3>{'Baskets and unfinished checkouts'|i18n( $ctx )}</h3>
    <div class="sd-scroll"><table class="sd-table">
    <tr><th></th><th class="num">{'Count'|i18n( $ctx )}</th><th class="num">{'Items'|i18n( $ctx )}</th><th class="num">{'Value'|i18n( $ctx )}</th></tr>
    <tr><td>{'In progress (active in the last %hours hours)'|i18n( $ctx, , hash( '%hours', $d.limits.abandoned_hours ) )}</td><td class="num">{$d.baskets.fresh.count}</td><td class="num">{$d.baskets.fresh.items}</td>
        <td class="num">{foreach $d.baskets.fresh.value as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td></tr>
    <tr><td>{'Abandoned (idle longer)'|i18n( $ctx )}</td><td class="num">{$d.baskets.abandoned.count}</td><td class="num">{$d.baskets.abandoned.items}</td>
        <td class="num">{foreach $d.baskets.abandoned.value as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td></tr>
    <tr><td>{'Empty baskets'|i18n( $ctx )}</td><td class="num">{$d.baskets.empty}</td><td class="num">0</td><td class="num">&ndash;</td></tr>
    <tr><td>{'Checkouts not confirmed, 30 days'|i18n( $ctx )}</td><td class="num">{$d.unfinished_checkouts.count}</td><td class="num">&ndash;</td><td class="num">&ndash;</td></tr>
    </table></div>
    <p class="sd-note">{'A basket belongs to a visitor session and holds products at list price before VAT rules and discounts. An unconfirmed checkout is an order the customer saw on the confirmation page and left without confirming; it stays temporary, has no order number and is not counted anywhere else on this page.'|i18n( $ctx )}{if $d.baskets.oldest} {'Oldest basket: %date.'|i18n( $ctx, , hash( '%date', $d.baskets.oldest|l10n( 'shortdatetime' ) ) )}{/if}</p>
</div>

<div class="sd-card">
    <h3>{'Products'|i18n( $ctx )}</h3>
    {if $d.products.classes|count}
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Product class'|i18n( $ctx )}</th><th>{'Price attribute'|i18n( $ctx )}</th><th class="num">{'Published'|i18n( $ctx )}</th><th class="num">{'No price'|i18n( $ctx )}</th></tr>
    {foreach $d.products.classes as $c}
    <tr><td><a href={concat( '/class/view/', $c.id )|ezurl}>{$c.name|wash}</a></td><td><code>{$c.price_attribute|wash}</code> ({$c.datatype|wash})</td><td class="num">{$c.products}</td><td class="num">{if $c.no_price}<span class="sd-tag bad">{$c.no_price}</span>{else}0{/if}</td></tr>
    {/foreach}
    </table></div>
    <p class="sd-note">{'A class is a product class when it has a price attribute (%types). Only its published objects can be added to the basket.'|i18n( $ctx, , hash( '%types', 'ezprice, ezmultiprice' ) )}</p>
    {else}
    <p class="sd-empty">{'No class has a price attribute, so nothing can be sold yet.'|i18n( $ctx )}</p>
    {/if}
    {if $d.products.no_price_list|count}
    <h3>{'Products without a price'|i18n( $ctx )}</h3>
    <ul>
    {foreach $d.products.no_price_list as $np}
        <li>{if $np.node_id}<a href={concat( '/content/view/full/', $np.node_id )|ezurl}>{$np.name|wash}</a>{else}{$np.name|wash}{/if} &middot; <a href={concat( '/content/edit/', $np.object_id )|ezurl}>{'Edit'|i18n( $ctx )}</a></li>
    {/foreach}
    </ul>
    {/if}
    <p class="sd-note"><a href={'/shop/productsoverview'|ezurl}>{'Products overview with prices'|i18n( $ctx )}</a></p>
</div>
</div>

{* ------------------------------------------------------------------ How your shop works *}
<h2 id="sd-how">{'How your shop works'|i18n( $ctx )}</h2>
<p class="sd-lead">{'What a customer goes through, and where each step is set up on this installation. Settings in italics are INI files, the links open the admin page that changes them.'|i18n( $ctx )}</p>

<h3>{'The checkout, step by step'|i18n( $ctx )}</h3>
<ol class="sd-flow">
    <li>
        <strong class="sd-step">{'The customer adds a product to the basket'|i18n( $ctx )}</strong>
        {'Any published object of a product class has a "Buy" button (shop/add). The price is taken from its price attribute at that moment, with the VAT type the price uses.'|i18n( $ctx )}
        <div class="sd-where">{'After adding, the visitor goes to: %target'|i18n( $ctx, , hash( '%target', concat( '<code>', $d.config.redirect_after_add|wash, '</code>' ) ) )} &middot; <i>site.ini [ShopSettings] RedirectAfterAddToBasket</i></div>
    </li>
    <li>
        <strong class="sd-step">{'The basket'|i18n( $ctx )}</strong>
        {'The basket is kept per session at %url. Quantities can be changed there; discounts for the signed-in user are shown.'|i18n( $ctx, , hash( '%url', concat( '<code>/shop/', $d.config.basket_view|wash, '</code>' ) ) )}
        <div class="sd-where">
            <i>shop.ini [BasketSettings] BasketViewName</i> = <code>{$d.config.basket_view|wash}</code> &middot;
            <i>site.ini [ShopSettings] ClearBasketOnCheckout</i> = <code>{$d.config.clear_basket_on_checkout|wash}</code> &middot;
            <i>site.ini [Session] BasketCleanup</i> = <code>{$d.config.basket_cleanup|wash}</code>, {'sessions'|i18n( $ctx )}: <code>{$d.config.session_handler|wash}</code>
        </div>
    </li>
    <li>
        <strong class="sd-step">{'Customer details'|i18n( $ctx )}</strong>
        {switch match=$d.config.account_handler}
        {case match='ezuser'}{'Shop account handler "ezuser": the customer fills in name, e-mail and address on shop/userregister (prefilled for signed-in users). Guests can buy.'|i18n( $ctx )}{/case}
        {case match='ezsimple'}{'Shop account handler "ezsimple": a short form (name, e-mail, address) on shop/register.'|i18n( $ctx )}{/case}
        {case match='ezdefault'}{'Shop account handler "ezdefault": the customer must be signed in; name and e-mail come from the user account, no address is asked.'|i18n( $ctx )}{/case}
        {case}{'Shop account handler "%handler" (custom): it decides which details are asked for.'|i18n( $ctx, , hash( '%handler', $d.config.account_handler|wash ) )}{/case}
        {/switch}
        <div class="sd-where"><i>shopaccount.ini [AccountSettings] Handler</i> = <code>{$d.config.account_handler|wash}</code></div>
    </li>
    <li>
        <strong class="sd-step">{'Confirmation page'|i18n( $ctx )}</strong>
        {'shop/confirmorder shows the order with VAT and any extra lines. Here the order exists for the first time, still temporary.'|i18n( $ctx )}
        {if $d.config.shipping_handler}{'Shipping cost comes from the shipping handler "%handler".'|i18n( $ctx, , hash( '%handler', $d.config.shipping_handler|wash ) )}{else}{'No shipping handler is set, so no shipping cost is added.'|i18n( $ctx )}{/if}
        {if $d.config.dynamic_vat}{'VAT is chosen per country and product category by the VAT rules.'|i18n( $ctx )}{else}{'VAT is the fixed VAT type stored in each product price; VAT rules are not used.'|i18n( $ctx )}{/if}
        <div class="sd-where"><i>shop.ini [ShippingSettings] Handler</i> = <code>{if $d.config.shipping_handler}{$d.config.shipping_handler|wash}{else}{'not set'|i18n( $ctx )}{/if}</code> &middot; <i>shop.ini [VATSettings] Handler</i> = <code>{if $d.config.vat_handler}{$d.config.vat_handler|wash}{else}{'not set'|i18n( $ctx )}{/if}</code></div>
    </li>
    <li{if $d.config.payment_in_checkout|not} class="missing"{/if}>
        <strong class="sd-step">{'Payment'|i18n( $ctx )}</strong>
        {if $d.config.payment_in_checkout}
            {'shop/checkout runs a workflow with a payment gateway event before the order is final.'|i18n( $ctx )}
        {else}
            {'Not set up: nothing runs before shop/checkout, so the order is accepted without payment. Payment is a workflow with a "Payment Gateway" event, connected to the trigger shop / checkout / before.'|i18n( $ctx )}
        {/if}
        <div class="sd-where">
            {'Gateways available'|i18n( $ctx )}: {if $d.gateways|count}{foreach $d.gateways as $g}<code>{$g.name|wash}</code> {/foreach}{else}{'none'|i18n( $ctx )}{/if}
            {if $d.config.paypal_active} &middot; <i>paypal.ini [PaypalSettings] Business</i> {if $d.config.paypal_business_set}<span class="sd-tag good">{'set'|i18n( $ctx )}</span>{else}<span class="sd-tag bad">{'empty'|i18n( $ctx )}</span>{/if}{/if}
            &middot; <a href={'/trigger/list'|ezurl}>{'Triggers'|i18n( $ctx )}</a> &middot; <a href={'/workflow/grouplist'|ezurl}>{'Workflows'|i18n( $ctx )}</a>
        </div>
    </li>
    <li>
        <strong class="sd-step">{'The order is placed'|i18n( $ctx )}</strong>
        {'It gets its order number and the status Pending, the basket is emptied and the confirm order handler runs.'|i18n( $ctx )}
        {if $d.config.send_order_email}{'It sends the order e-mail to the customer and a copy to the site administrator address.'|i18n( $ctx )}{else}{'Order e-mails are switched off.'|i18n( $ctx )}{/if}
        {'The customer is shown the receipt (shop/%view).'|i18n( $ctx, , hash( '%view', $d.config.order_link_view|wash ) )}
        <div class="sd-where"><i>shopaccount.ini [ConfirmOrderSettings] Handler</i> = <code>{$d.config.confirm_handler|wash}</code> &middot; <i>site.ini [ShopSettings] SendOrderEmail</i> = <code>{if $d.config.send_order_email}enabled{else}disabled{/if}</code> &middot; <i>site.ini [MailSettings] AdminEmail</i> {if $d.config.admin_email_set}<span class="sd-tag good">{'set'|i18n( $ctx )}</span>{else}<span class="sd-tag bad">{'empty'|i18n( $ctx )}</span>{/if}</div>
    </li>
    <li>
        <strong class="sd-step">{'You process the order'|i18n( $ctx )}</strong>
        {'In the order list you move each order through its statuses (below), and archive it when it is done. Archived orders keep counting in revenue and statistics, they just leave the order list.'|i18n( $ctx )}
        <div class="sd-where"><a href={'/shop/orderlist'|ezurl}>{'Order list'|i18n( $ctx )}</a> &middot; <a href={'/shop/status'|ezurl}>{'Order status'|i18n( $ctx )}</a> &middot; <a href={'/shop/archivelist'|ezurl}>{'Archive'|i18n( $ctx )}</a></div>
    </li>
</ol>

{if $d.triggers|count}
<h3>{'Workflows connected to the shop'|i18n( $ctx )}</h3>
<div class="sd-scroll"><table class="sd-table">
<tr><th>{'Trigger'|i18n( $ctx )}</th><th>{'Workflow'|i18n( $ctx )}</th><th>{'Events'|i18n( $ctx )}</th></tr>
{foreach $d.triggers as $tr}
<tr><td><code>shop / {$tr.function|wash} / {$tr.connect_type}</code></td><td><a href={concat( '/workflow/view/', $tr.workflow_id )|ezurl}>{$tr.workflow|wash}</a></td><td>{$tr.events|implode( ', ' )|wash}</td></tr>
{/foreach}
</table></div>
{/if}

<div class="sd-grid2">
<div class="sd-card">
    <h3>{'Order statuses and what they mean'|i18n( $ctx )}</h3>
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Status'|i18n( $ctx )}</th><th>{'Meaning'|i18n( $ctx )}</th></tr>
    {foreach $d.statuses as $s}
    <tr><td class="nw"><span class="sd-dot {if $s.status_id|eq( 1 )}s1{elseif $s.status_id|eq( 2 )}s2{elseif $s.status_id|eq( 3 )}s3{else}sc{/if}"></span>{$s.name|wash} <span class="sd-note">({$s.status_id})</span></td>
        <td>{if $s.status_id|eq( 1 )}{'Every new order starts here. Check that it is paid, then move it on.'|i18n( $ctx )}
            {elseif $s.status_id|eq( 2 )}{'You have accepted the order and are packing or shipping it.'|i18n( $ctx )}
            {elseif $s.status_id|eq( 3 )}{'Shipped and finished. Delivered orders no longer count as open.'|i18n( $ctx )}
            {else}{'A status added for this shop (numbers from 1000 on), for example by a payment extension. Counts as open until the order is Delivered.'|i18n( $ctx )}{/if}
            {if $s.is_active|not} {'Inactive: cannot be chosen.'|i18n( $ctx )}{/if}</td></tr>
    {/foreach}
    </table></div>
    <p class="sd-note">{'Who may change a status, and from which status to which, is set with the policy shop/setstatus in the roles.'|i18n( $ctx )} <a href={'/shop/status'|ezurl}>{'Order status'|i18n( $ctx )}</a> &middot; <a href={'/role/list'|ezurl}>{'Roles and policies'|i18n( $ctx )}</a></p>
</div>

<div class="sd-card">
    <h3>{'VAT'|i18n( $ctx )}</h3>
    <p>{if $d.config.dynamic_vat}{'Dynamic VAT is on: products whose price uses "%dynamic" get the VAT rule for the customer country and product category.'|i18n( $ctx, , hash( '%dynamic', ezini( 'VATSettings', 'DynamicVatTypeName', 'shop.ini' )|wash ) )}{else}{'Dynamic VAT is off (shop.ini [VATSettings] Handler is not set): every product price carries its own VAT type, and VAT rules are not applied.'|i18n( $ctx )}{/if}</p>
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'VAT type'|i18n( $ctx )}</th><th class="num">%</th><th class="num">{'Products'|i18n( $ctx )}</th></tr>
    {foreach $d.vat_types as $vt}
    <tr><td>{$vt.name|wash}</td><td class="num">{$vt.percentage|l10n( 'number' )}</td><td class="num">{$vt.products}</td></tr>
    {/foreach}
    {if $d.products.dynamic_vat}<tr><td>{ezini( 'VATSettings', 'DynamicVatTypeName', 'shop.ini' )|wash}</td><td class="num">&ndash;</td><td class="num">{$d.products.dynamic_vat}</td></tr>{/if}
    </table></div>
    <p class="sd-note">{'VAT rules: %count.'|i18n( $ctx, , hash( '%count', $d.vat_rules|count ) )}{foreach $d.vat_rules as $vr} <span class="sd-tag">{$vr.country|wash}: {$vr.vat_type|wash}{if $vr.categories} ({$vr.categories|wash}){/if}</span>{/foreach}
        {'Product categories: %count.'|i18n( $ctx, , hash( '%count', $d.product_categories|count ) )}{foreach $d.product_categories as $pc} <span class="sd-tag">{$pc.name|wash}</span>{/foreach}</p>
    {if $d.order_countries|count}
    <p class="sd-note">{'Orders of the last 60 days came from'|i18n( $ctx )}:
    {foreach $d.order_countries as $oc} <span class="sd-tag{if and( $d.config.dynamic_vat, $oc.has_rule|not )} warn{/if}">{$oc.name|wash} {$oc.orders}{if $d.config.dynamic_vat}{if $oc.has_rule} &check;{else} &ndash; {'no rule'|i18n( $ctx )}{/if}{/if}</span>{/foreach}</p>
    {/if}
    <p class="sd-note"><a href={'/shop/vattype'|ezurl}>{'VAT types'|i18n( $ctx )}</a> &middot; <a href={'/shop/vatrules'|ezurl}>{'VAT rules'|i18n( $ctx )}</a> &middot; <a href={'/shop/productcategories'|ezurl}>{'Product categories'|i18n( $ctx )}</a></p>
</div>

<div class="sd-card">
    <h3>{'Currencies'|i18n( $ctx )}</h3>
    {if $d.currencies|count}
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Code'|i18n( $ctx )}</th><th>{'Symbol'|i18n( $ctx )}</th><th class="num">{'Rate'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th></tr>
    {foreach $d.currencies as $cu}
    <tr><td>{$cu.code|wash}</td><td>{$cu.symbol|wash}</td><td class="num">{$cu.rate|wash}</td><td>{if $cu.is_active}<span class="sd-tag good">{'active'|i18n( $ctx )}</span>{else}<span class="sd-tag">{'inactive'|i18n( $ctx )}</span>{/if}</td></tr>
    {/foreach}
    </table></div>
    {else}
    <p>{'No currencies are defined. That is fine for a shop in one currency with simple prices (ezprice): prices are shown in the currency of the site locale, %code. Currencies are needed for multi-currency prices (ezmultiprice).'|i18n( $ctx, , hash( '%code', $d.config.locale_currency|wash ) )}</p>
    {/if}
    <dl class="sd-kv">
        <dt>{'Site locale currency'|i18n( $ctx )}</dt><dd><code>{$d.config.locale_currency|wash}</code></dd>
        <dt><i>shop.ini PreferredCurrency</i></dt><dd><code>{if $d.config.preferred_currency}{$d.config.preferred_currency|wash}{else}{'not set'|i18n( $ctx )}{/if}</code></dd>
        <dt>{'Exchange rates'|i18n( $ctx )}</dt><dd><code>{$d.config.rates_handler|wash}</code>, {'base'|i18n( $ctx )} <code>{$d.config.base_currency|wash}</code></dd>
    </dl>
    <p class="sd-note"><a href={'/shop/currencylist'|ezurl}>{'Currencies'|i18n( $ctx )}</a> &middot; <a href={'/shop/preferredcurrency'|ezurl}>{'Preferred currency'|i18n( $ctx )}</a></p>
</div>

<div class="sd-card">
    <h3>{'Discounts'|i18n( $ctx )}</h3>
    {if $d.discount_rules|count}
    <div class="sd-scroll"><table class="sd-table">
    <tr><th>{'Discount group'|i18n( $ctx )}</th><th class="num">{'Rules'|i18n( $ctx )}</th><th class="num">{'Up to'|i18n( $ctx )}</th><th class="num">{'Customers'|i18n( $ctx )}</th></tr>
    {foreach $d.discount_rules as $dr}
    <tr><td><a href={concat( '/shop/discountgroupview/', $dr.id )|ezurl}>{$dr.name|wash}</a></td><td class="num">{$dr.sub_rules}</td><td class="num">{$dr.max_percent|l10n( 'number' )} %</td><td class="num">{$dr.members}</td></tr>
    {/foreach}
    </table></div>
    {else}
    <p>{'No discount groups. A discount group gives users or user groups a percentage off chosen products, classes or sections; the discount appears in the basket once they sign in.'|i18n( $ctx )}</p>
    {/if}
    <p class="sd-note"><a href={'/shop/discountgroup'|ezurl}>{'Discounts'|i18n( $ctx )}</a></p>
</div>
</div>

{* ------------------------------------------------------------------ Next steps *}
<h2 id="sd-next">{'Next steps'|i18n( $ctx )}</h2>
<p class="sd-lead">{'Worked out from the orders, products and settings above, most urgent first.'|i18n( $ctx )}</p>
{if $d.checklist|count}
<ul class="sd-checklist">
{foreach $d.checklist as $item}
    <li class="{$item.level}"><span class="sd-level">{if $item.level|eq( 'critical' )}{'Action'|i18n( $ctx )}{elseif $item.level|eq( 'warning' )}{'Check'|i18n( $ctx )}{else}{'Tip'|i18n( $ctx )}{/if}</span>{$item.text|wash}{if $item.url} <a href={$item.url|ezurl}>{$item.link_text|wash} &rsaquo;</a>{/if}</li>
{/foreach}
</ul>
{else}
<ul class="sd-checklist"><li class="ok">{'Nothing to do: orders are moving and the shop is fully set up.'|i18n( $ctx )}</li></ul>
{/if}

<p class="sd-note">{'Figures computed in %ms ms.'|i18n( $ctx, , hash( '%ms', $d.build_ms ) )}</p>

{* DESIGN: Content END *}</div></div></div>
</div>
{undef}
