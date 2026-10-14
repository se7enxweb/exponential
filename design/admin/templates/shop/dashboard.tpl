{* Store dashboard (shop/dashboard). All figures come from kernel/shop/dashboard.php as $dashboard. *}
{def $d = $dashboard
     $ctx = 'design/admin/shop/dashboard'
     $fmt = false()
     $p = false()
     $main = $dashboard.main_currency
     $mainfmt = $dashboard.format[$dashboard.main_currency]
     $t = false()}


<div class="context-block a4-page a4-shop-dash">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<div class="a4-shop-head">
    <h1 class="context-title">{'Store dashboard'|i18n( $ctx )}</h1>
    <span class="a4-shop-meta">{'As of %time. Amounts include VAT. Periods count from midnight.'|i18n( $ctx, , hash( '%time', $d.now|l10n( 'shortdatetime' ) ) )}</span>
</div>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="a4-badges a4-shop-pills">
    {if $d.checklist_counts.critical}<a class="a4-badge is-bad" href="#sd-next">{'%count need action now'|i18n( $ctx, , hash( '%count', $d.checklist_counts.critical ) )}</a>{/if}
    {if $d.checklist_counts.warning}<a class="a4-badge is-warn" href="#sd-next">{'%count to check'|i18n( $ctx, , hash( '%count', $d.checklist_counts.warning ) )}</a>{/if}
    {if $d.checklist_counts.info}<a class="a4-badge is-info" href="#sd-next">{'%count tips'|i18n( $ctx, , hash( '%count', $d.checklist_counts.info ) )}</a>{/if}
    {if $d.checklist|count|eq( 0 )}<span class="a4-badge is-ok">{'Nothing to do'|i18n( $ctx )}</span>{/if}
</div>
<div class="a4-shop-toc">
    <a href="#sd-glance">{'At a glance'|i18n( $ctx )}</a>
    <a href="#sd-orders">{'Orders'|i18n( $ctx )}</a>
    <a href="#sd-products">{'Products and baskets'|i18n( $ctx )}</a>
    <a href="#sd-how">{'How your shop works'|i18n( $ctx )}</a>
    <a href="#sd-next">{'Next steps'|i18n( $ctx )}</a>
</div>

{* ------------------------------------------------------------------ At a glance *}
<h2 id="sd-glance">{'At a glance'|i18n( $ctx )}</h2>

<div class="a4-shop-tiles">
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Orders today'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.periods.today.orders}</div>
        <div class="a4-shop-sub">{foreach $d.periods.today.revenue as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )} {/foreach}{if $d.periods.today.orders|eq( 0 )}{'no revenue yet'|i18n( $ctx )}{/if}</div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Last 7 days'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.periods.days7.orders}</div>
        <div class="a4-shop-sub">{'%count items sold'|i18n( $ctx, , hash( '%count', $d.periods.days7.items ) )}</div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Last 30 days'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.periods.days30.orders}</div>
        <div class="a4-shop-sub">
            {set $t = $d.trends.orders}
            {if is_null( $t )}<span class="a4-shop-up">{'new'|i18n( $ctx )}</span>{elseif $t|gt( 0 )}<span class="a4-shop-up">+{$t} %</span>{elseif $t|lt( 0 )}<span class="a4-shop-down">{$t} %</span>{else}&plusmn;0 %{/if}
            {'vs. %count in the 30 days before'|i18n( $ctx, , hash( '%count', $d.periods.previous30.orders ) )}
        </div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Revenue, 30 days'|i18n( $ctx )}</div>
        {if $d.periods.days30.revenue|count}
            {foreach $d.periods.days30.revenue as $cur => $sum}
            <div class="a4-shop-value{if $d.periods.days30.revenue|count|gt( 1 )} small{/if}">{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}</div>
            {/foreach}
        {else}
            <div class="a4-shop-value">&ndash;</div>
        {/if}
        <div class="a4-shop-sub">
            {set $t = $d.trends.revenue}
            {if is_null( $t )}<span class="a4-shop-up">{'new'|i18n( $ctx )}</span>{elseif $t|gt( 0 )}<span class="a4-shop-up">+{$t} %</span>{elseif $t|lt( 0 )}<span class="a4-shop-down">{$t} %</span>{else}&plusmn;0 %{/if}
            {'%currency vs. the 30 days before'|i18n( $ctx, , hash( '%currency', $main ) )}
        </div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Average order, 30 days'|i18n( $ctx )}</div>
        {if $d.periods.days30.average|count}
            {foreach $d.periods.days30.average as $cur => $sum}
            <div class="a4-shop-value{if $d.periods.days30.average|count|gt( 1 )} small{/if}">{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}</div>
            {/foreach}
        {else}
            <div class="a4-shop-value">&ndash;</div>
        {/if}
        <div class="a4-shop-sub">{'%items items per order'|i18n( $ctx, , hash( '%items', cond( $d.periods.days30.orders|gt( 0 ), div( $d.periods.days30.items, $d.periods.days30.orders )|l10n( 'number' ), 0 ) ) )}</div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Customers, 30 days'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.periods.days30.customers}</div>
        <div class="a4-shop-sub">{'%new new, %repeat buy again, %all in total'|i18n( $ctx, , hash( '%new', $d.all_time.new_customers_30, '%repeat', $d.all_time.repeat_customers, '%all', $d.all_time.customers ) )}</div>
    </div>
    <div class="a4-shop-tile{if or( $d.overdue.pending, $d.overdue.processing, $d.overdue.other )} alert{/if}">
        <div class="a4-shop-label">{'Open orders'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.all_time.open}</div>
        <div class="a4-shop-sub">{'not yet finished; %late waiting too long'|i18n( $ctx, , hash( '%late', sum( $d.overdue.pending, $d.overdue.processing, $d.overdue.other ) ) )}</div>
    </div>
    <div class="a4-shop-tile">
        <div class="a4-shop-label">{'Baskets'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.baskets.fresh.count} / {$d.baskets.abandoned.count}</div>
        <div class="a4-shop-sub">{'in progress / abandoned (idle over %hours h)'|i18n( $ctx, , hash( '%hours', $d.limits.abandoned_hours ) )}</div>
    </div>
    <div class="a4-shop-tile{if $d.products.no_price} alert{/if}">
        <div class="a4-shop-label">{'Products'|i18n( $ctx )}</div>
        <div class="a4-shop-value">{$d.products.total}</div>
        <div class="a4-shop-sub">{'%count without a price'|i18n( $ctx, , hash( '%count', $d.products.no_price ) )}</div>
    </div>
</div>

<h3>{'Revenue per day, last 30 days (%currency)'|i18n( $ctx, , hash( '%currency', $main ) )}</h3>
{if $d.max_daily_revenue|gt( 0 )}
<div class="a4-shop-chart" role="img" aria-label="{'Revenue per day, last 30 days (%currency)'|i18n( $ctx, , hash( '%currency', $main ) )|wash}">
{foreach $d.daily as $day}
    <div class="a4-shop-bar{if $day.is_today} today{/if}" title="{$day.date|l10n( 'shortdate' )}: {$day.revenue|l10n( 'currency', $mainfmt.locale, $mainfmt.symbol )|wash} &middot; {'%count orders'|i18n( $ctx, , hash( '%count', $day.orders ) )|wash}">{if $day.orders}<em>{$day.orders}</em>{/if}<span class="a4-pct-{$day.height|round}"></span></div>
{/foreach}
</div>
<div class="a4-shop-axis">
{foreach $d.daily as $index => $day}
    <span>{if or( $day.is_monday, $day.is_today, $index|eq( 0 ) )}{$day.date|datetime( 'custom', '%d.%m' )}{/if}</span>
{/foreach}
</div>
<p class="a4-shop-note">{'Bar height is the revenue of the day, the number above it the orders. The dark bar is today. Highest day: %max.'|i18n( $ctx, , hash( '%max', $d.max_daily_revenue|l10n( 'currency', $mainfmt.locale, $mainfmt.symbol ) ) )}</p>
{else}
<p class="a4-shop-empty">{'No orders in the last 30 days.'|i18n( $ctx )}</p>
{/if}

<div class="a4-shop-grid2">
<div class="a4-shop-card">
    <h3>{'Order status'|i18n( $ctx )}</h3>
    {if $d.all_time.orders}
    <div class="a4-shop-stack">
        {foreach $d.statuses as $s}{if $s.open}<span class="a4-shop-g-{$s.group} a4-pct-{$s.percent|round}" title="{$s.name|wash}: {$s.open}"></span>{/if}{/foreach}
    </div>
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Status'|i18n( $ctx )}</th><th class="a4-num">{'Orders'|i18n( $ctx )}</th><th class="a4-num">{'Archived'|i18n( $ctx )}</th><th>{'Longest in status'|i18n( $ctx )}</th></tr>
    {foreach $d.statuses as $s}
    <tr>
        <td class="a4-nowrap"><span class="a4-shop-dot a4-shop-g-{$s.group}"></span>{$s.name|wash}{if $s.is_active|not} <span class="a4-tag">{'inactive'|i18n( $ctx )}</span>{/if}</td>
        <td class="a4-num">{$s.open}</td>
        <td class="a4-num">{$s.archived}</td>
        <td>{if and( $s.oldest, $s.open, $s.status_id|ne( 3 ) )}{'since %date'|i18n( $ctx, , hash( '%date', $s.oldest|l10n( 'shortdate' ) ) )}{else}&ndash;{/if}</td>
    </tr>
    {/foreach}
    <tr><td><strong>{'All orders'|i18n( $ctx )}</strong></td><td class="a4-num"><strong>{sub( $d.all_time.orders, $d.all_time.archived )}</strong></td><td class="a4-num"><strong>{$d.all_time.archived}</strong></td><td>{if $d.all_time.first_order}{'first order %date'|i18n( $ctx, , hash( '%date', $d.all_time.first_order|l10n( 'shortdate' ) ) )}{/if}</td></tr>
    </table></div>
    {else}
    <p class="a4-shop-empty">{'No orders yet.'|i18n( $ctx )}</p>
    {/if}
</div>

<div class="a4-shop-card">
    <h3>{'Top products, last 30 days'|i18n( $ctx )}</h3>
    {if $d.top_products|count}
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Product'|i18n( $ctx )}</th><th class="a4-num">{'Sold'|i18n( $ctx )}</th><th class="a4-num">{'Orders'|i18n( $ctx )}</th><th class="a4-num">{'Revenue'|i18n( $ctx )}</th></tr>
    {foreach $d.top_products as $tp}
    <tr>
        <td>{if $tp.node_id}<a href={concat( '/content/view/full/', $tp.node_id )|ezurl}>{$tp.name|wash}</a>{else}{$tp.name|wash} <span class="a4-tag is-bad">{'removed'|i18n( $ctx )}</span>{/if}
            <div class="a4-meter a4-shop-meter"><span class="a4-pct-{$tp.percent|round}"></span></div></td>
        <td class="a4-num">{$tp.quantity}</td>
        <td class="a4-num">{$tp.orders}</td>
        <td class="a4-num">{foreach $tp.revenue as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td>
    </tr>
    {/foreach}
    </table></div>
    <p class="a4-shop-note"><a href={'/shop/statistics'|ezurl}>{'Product statistics per month and year'|i18n( $ctx )}</a></p>
    {else}
    <p class="a4-shop-empty">{'Nothing sold in the last 30 days.'|i18n( $ctx )}</p>
    {/if}
</div>
</div>

{* ------------------------------------------------------------------ Orders *}
<h2 id="sd-orders">{'Orders'|i18n( $ctx )}</h2>

<h3>{'Waiting for you'|i18n( $ctx )}</h3>
<p class="a4-shop-lead">{'Every order that is not finished and not archived, the one waiting longest first. Highlighted: an order waiting for the customer (Pending, Awaiting payment, Payment failed, Ready for pickup) for more than %pending days, or in any other open status for more than %processing days.'|i18n( $ctx, , hash( '%pending', $d.limits.pending_days, '%processing', $d.limits.processing_days ) )}</p>
{if $d.waiting|count}
<div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
<tr><th>{'Order'|i18n( $ctx )}</th><th>{'Customer'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th><th>{'Waiting'|i18n( $ctx )}</th><th class="a4-num">{'Total'|i18n( $ctx )}</th></tr>
{foreach $d.waiting as $o}
<tr{if $o.overdue} class="overdue"{/if}>
    <td><a href={concat( '/shop/orderview/', $o.id, '/' )|ezurl}>#{$o.order_nr}</a><div class="a4-shop-note">{$o.created|l10n( 'shortdatetime' )}</div></td>
    <td><a href={concat( '/shop/customerorderview/', $o.user_id, '/', $o.email|rawurlencode )|ezurl}>{$o.customer|wash}</a></td> {* Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 5a17c08bfc), Copyright (C) Mugo Web, GPL v2 *}
    <td class="a4-nowrap"><span class="a4-shop-dot a4-shop-g-{$o.status_group}"></span>{$o.status_name|wash}</td>
    <td>{if $o.wait_days|gt( 0 )}{'%count days'|i18n( $ctx, , hash( '%count', $o.wait_days ) )}{else}{'%count hours'|i18n( $ctx, , hash( '%count', $o.wait_hours ) )}{/if}{if $o.overdue} <span class="a4-tag is-bad">{'too long'|i18n( $ctx )}</span>{/if}</td>
    <td class="a4-num">{$o.total|l10n( 'currency', $d.format[$o.currency].locale, $d.format[$o.currency].symbol )}</td>
</tr>
{/foreach}
</table></div>
{if $d.all_time.open|gt( $d.waiting|count )}<p class="a4-shop-note">{'%shown of %count open orders shown.'|i18n( $ctx, , hash( '%shown', $d.waiting|count, '%count', $d.all_time.open ) )} <a href={'/shop/orderlist'|ezurl}>{'All orders'|i18n( $ctx )}</a></p>{/if}
{else}
<p class="a4-shop-empty">{'No open orders: everything is finished or archived.'|i18n( $ctx )}</p>
{/if}

<h3>{'Latest orders'|i18n( $ctx )}</h3>
{if $d.latest|count}
<div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
<tr><th>{'Order'|i18n( $ctx )}</th><th>{'Placed'|i18n( $ctx )}</th><th>{'Customer'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th><th class="a4-num">{'Total'|i18n( $ctx )}</th></tr>
{foreach $d.latest as $o}
<tr>
    <td><a href={concat( '/shop/orderview/', $o.id, '/' )|ezurl}>#{$o.order_nr}</a></td>
    <td>{$o.created|l10n( 'shortdatetime' )}</td>
    <td><a href={concat( '/shop/customerorderview/', $o.user_id, '/', $o.email|rawurlencode )|ezurl}>{$o.customer|wash}</a></td>
    <td><span class="a4-shop-dot {if $o.is_archived}a4-shop-sa{else}a4-shop-g-{$o.status_group}{/if}"></span>{$o.status_name|wash}{if $o.is_archived} <span class="a4-tag">{'archived'|i18n( $ctx )}</span>{/if}</td>
    <td class="a4-num">{$o.total|l10n( 'currency', $d.format[$o.currency].locale, $d.format[$o.currency].symbol )}</td>
</tr>
{/foreach}
</table></div>
<p class="a4-shop-note"><a href={'/shop/orderlist'|ezurl}>{'Order list'|i18n( $ctx )}</a> &middot; <a href={'/shop/archivelist'|ezurl}>{'Archive'|i18n( $ctx )}</a> &middot; <a href={'/shop/customerlist'|ezurl}>{'Customers'|i18n( $ctx )}</a></p>
{else}
<p class="a4-shop-empty">{'No orders yet.'|i18n( $ctx )}</p>
{/if}

{* ------------------------------------------------------------------ Products and baskets *}
<h2 id="sd-products">{'Products and baskets'|i18n( $ctx )}</h2>

<div class="a4-shop-grid2">
<div class="a4-shop-card">
    <h3>{'Baskets and unfinished checkouts'|i18n( $ctx )}</h3>
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th></th><th class="a4-num">{'Count'|i18n( $ctx )}</th><th class="a4-num">{'Items'|i18n( $ctx )}</th><th class="a4-num">{'Value'|i18n( $ctx )}</th></tr>
    <tr><td>{'In progress (active in the last %hours hours)'|i18n( $ctx, , hash( '%hours', $d.limits.abandoned_hours ) )}</td><td class="a4-num">{$d.baskets.fresh.count}</td><td class="a4-num">{$d.baskets.fresh.items}</td>
        <td class="a4-num">{foreach $d.baskets.fresh.value as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td></tr>
    <tr><td>{'Abandoned (idle longer)'|i18n( $ctx )}</td><td class="a4-num">{$d.baskets.abandoned.count}</td><td class="a4-num">{$d.baskets.abandoned.items}</td>
        <td class="a4-num">{foreach $d.baskets.abandoned.value as $cur => $sum}{$sum|l10n( 'currency', $d.format[$cur].locale, $d.format[$cur].symbol )}<br />{/foreach}</td></tr>
    <tr><td>{'Empty baskets'|i18n( $ctx )}</td><td class="a4-num">{$d.baskets.empty}</td><td class="a4-num">0</td><td class="a4-num">&ndash;</td></tr>
    <tr><td>{'Checkouts not confirmed, 30 days'|i18n( $ctx )}</td><td class="a4-num">{$d.unfinished_checkouts.count}</td><td class="a4-num">&ndash;</td><td class="a4-num">&ndash;</td></tr>
    </table></div>
    <p class="a4-shop-note">{'A basket belongs to a visitor session and holds products at list price before VAT rules and discounts. An unconfirmed checkout is an order the customer saw on the confirmation page and left without confirming; it stays temporary, has no order number and is not counted anywhere else on this page.'|i18n( $ctx )}{if $d.baskets.oldest} {'Oldest basket: %date.'|i18n( $ctx, , hash( '%date', $d.baskets.oldest|l10n( 'shortdatetime' ) ) )}{/if}</p>
</div>

<div class="a4-shop-card">
    <h3>{'Products'|i18n( $ctx )}</h3>
    {if $d.products.classes|count}
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Product class'|i18n( $ctx )}</th><th>{'Price attribute'|i18n( $ctx )}</th><th class="a4-num">{'Published'|i18n( $ctx )}</th><th class="a4-num">{'No price'|i18n( $ctx )}</th></tr>
    {foreach $d.products.classes as $c}
    <tr><td><a href={concat( '/class/view/', $c.id )|ezurl}>{$c.name|wash}</a></td><td><code>{$c.price_attribute|wash}</code> ({$c.datatype|wash})</td><td class="a4-num">{$c.products}</td><td class="a4-num">{if $c.no_price}<span class="a4-tag is-bad">{$c.no_price}</span>{else}0{/if}</td></tr>
    {/foreach}
    </table></div>
    <p class="a4-shop-note">{'A class is a product class when it has a price attribute (%types). Only its published objects can be added to the basket.'|i18n( $ctx, , hash( '%types', 'ezprice, ezmultiprice' ) )}</p>
    {else}
    <p class="a4-shop-empty">{'No class has a price attribute, so nothing can be sold yet.'|i18n( $ctx )}</p>
    {/if}
    {if $d.products.no_price_list|count}
    <h3>{'Products without a price'|i18n( $ctx )}</h3>
    <ul>
    {foreach $d.products.no_price_list as $np}
        <li>{if $np.node_id}<a href={concat( '/content/view/full/', $np.node_id )|ezurl}>{$np.name|wash}</a>{else}{$np.name|wash}{/if} &middot; <a href={concat( '/content/edit/', $np.object_id )|ezurl}>{'Edit'|i18n( $ctx )}</a></li>
    {/foreach}
    </ul>
    {/if}
    <p class="a4-shop-note"><a href={'/shop/productsoverview'|ezurl}>{'Products overview with prices'|i18n( $ctx )}</a></p>
</div>
</div>

{* ------------------------------------------------------------------ How your shop works *}
<h2 id="sd-how">{'How your shop works'|i18n( $ctx )}</h2>
<p class="a4-shop-lead">{'What a customer goes through, and where each step is set up on this installation. Settings in italics are INI files, the links open the admin page that changes them.'|i18n( $ctx )}</p>

<h3>{'The checkout, step by step'|i18n( $ctx )}</h3>
<ol class="a4-shop-flow">
    <li>
        <strong class="a4-shop-step">{'The customer adds a product to the basket'|i18n( $ctx )}</strong>
        {'Any published object of a product class has a "Buy" button (shop/add). The price is taken from its price attribute at that moment, with the VAT type the price uses.'|i18n( $ctx )}
        <div class="a4-shop-where">{'After adding, the visitor goes to: %target'|i18n( $ctx, , hash( '%target', concat( '<code>', $d.config.redirect_after_add|wash, '</code>' ) ) )} &middot; <i>site.ini [ShopSettings] RedirectAfterAddToBasket</i></div>
    </li>
    <li>
        <strong class="a4-shop-step">{'The basket'|i18n( $ctx )}</strong>
        {'The basket is kept per session at %url. Quantities can be changed there; discounts for the signed-in user are shown.'|i18n( $ctx, , hash( '%url', concat( '<code>/shop/', $d.config.basket_view|wash, '</code>' ) ) )}
        <div class="a4-shop-where">
            <i>shop.ini [BasketSettings] BasketViewName</i> = <code>{$d.config.basket_view|wash}</code> &middot;
            <i>site.ini [ShopSettings] ClearBasketOnCheckout</i> = <code>{$d.config.clear_basket_on_checkout|wash}</code> &middot;
            <i>site.ini [Session] BasketCleanup</i> = <code>{$d.config.basket_cleanup|wash}</code>, {'sessions'|i18n( $ctx )}: <code>{$d.config.session_handler|wash}</code>
        </div>
    </li>
    <li>
        <strong class="a4-shop-step">{'Customer details'|i18n( $ctx )}</strong>
        {switch match=$d.config.account_handler}
        {case match='ezuser'}{'Shop account handler "ezuser": the customer fills in name, e-mail and address on shop/userregister (prefilled for signed-in users). Guests can buy.'|i18n( $ctx )}{/case}
        {case match='ezsimple'}{'Shop account handler "ezsimple": a short form (name, e-mail, address) on shop/register.'|i18n( $ctx )}{/case}
        {case match='ezdefault'}{'Shop account handler "ezdefault": the customer must be signed in; name and e-mail come from the user account, no address is asked.'|i18n( $ctx )}{/case}
        {case}{'Shop account handler "%handler" (custom): it decides which details are asked for.'|i18n( $ctx, , hash( '%handler', $d.config.account_handler|wash ) )}{/case}
        {/switch}
        <div class="a4-shop-where"><i>shopaccount.ini [AccountSettings] Handler</i> = <code>{$d.config.account_handler|wash}</code></div>
    </li>
    <li>
        <strong class="a4-shop-step">{'Confirmation page'|i18n( $ctx )}</strong>
        {'shop/confirmorder shows the order with VAT and any extra lines. Here the order exists for the first time, still temporary.'|i18n( $ctx )}
        {if $d.config.shipping_handler}{'Shipping cost comes from the shipping handler "%handler".'|i18n( $ctx, , hash( '%handler', $d.config.shipping_handler|wash ) )}{else}{'No shipping handler is set, so no shipping cost is added.'|i18n( $ctx )}{/if}
        {if $d.config.dynamic_vat}{'VAT is chosen per country and product category by the VAT rules.'|i18n( $ctx )}{else}{'VAT is the fixed VAT type stored in each product price; VAT rules are not used.'|i18n( $ctx )}{/if}
        <div class="a4-shop-where"><i>shop.ini [ShippingSettings] Handler</i> = <code>{if $d.config.shipping_handler}{$d.config.shipping_handler|wash}{else}{'not set'|i18n( $ctx )}{/if}</code> &middot; <i>shop.ini [VATSettings] Handler</i> = <code>{if $d.config.vat_handler}{$d.config.vat_handler|wash}{else}{'not set'|i18n( $ctx )}{/if}</code></div>
    </li>
    <li{if $d.config.payment_in_checkout|not} class="missing"{/if}>
        <strong class="a4-shop-step">{'Payment'|i18n( $ctx )}</strong>
        {if $d.config.payment_in_checkout}
            {'shop/checkout runs a workflow with a payment gateway event before the order is final.'|i18n( $ctx )}
        {else}
            {'Not set up: nothing runs before shop/checkout, so the order is accepted without payment. Payment is a workflow with a "Payment Gateway" event, connected to the trigger shop / checkout / before.'|i18n( $ctx )}
        {/if}
        <div class="a4-shop-where">
            {'Gateways available'|i18n( $ctx )}: {if $d.gateways|count}{foreach $d.gateways as $g}<code>{$g.name|wash}</code> {/foreach}{else}{'none'|i18n( $ctx )}{/if}
            {if $d.config.paypal_active} &middot; <i>paypal.ini [PaypalSettings] Business</i> {if $d.config.paypal_business_set}<span class="a4-tag is-ok">{'set'|i18n( $ctx )}</span>{else}<span class="a4-tag is-bad">{'empty'|i18n( $ctx )}</span>{/if}{/if}
            &middot; <a href={'/trigger/list'|ezurl}>{'Triggers'|i18n( $ctx )}</a> &middot; <a href={'/workflow/grouplist'|ezurl}>{'Workflows'|i18n( $ctx )}</a>
        </div>
    </li>
    <li>
        <strong class="a4-shop-step">{'The order is placed'|i18n( $ctx )}</strong>
        {'It gets its order number and the status Pending, the basket is emptied and the confirm order handler runs.'|i18n( $ctx )}
        {if $d.config.send_order_email}{'It sends the order e-mail to the customer and a copy to the site administrator address.'|i18n( $ctx )}{else}{'Order e-mails are switched off.'|i18n( $ctx )}{/if}
        {'The customer is shown the receipt (shop/%view).'|i18n( $ctx, , hash( '%view', $d.config.order_link_view|wash ) )}
        <div class="a4-shop-where"><i>shopaccount.ini [ConfirmOrderSettings] Handler</i> = <code>{$d.config.confirm_handler|wash}</code> &middot; <i>site.ini [ShopSettings] SendOrderEmail</i> = <code>{if $d.config.send_order_email}enabled{else}disabled{/if}</code> &middot; <i>site.ini [MailSettings] AdminEmail</i> {if $d.config.admin_email_set}<span class="a4-tag is-ok">{'set'|i18n( $ctx )}</span>{else}<span class="a4-tag is-bad">{'empty'|i18n( $ctx )}</span>{/if}</div>
    </li>
    <li>
        <strong class="a4-shop-step">{'You process the order'|i18n( $ctx )}</strong>
        {'In the order list you move each order through its statuses (below), and archive it when it is done. Archived orders keep counting in revenue and statistics, they just leave the order list.'|i18n( $ctx )}
        <div class="a4-shop-where"><a href={'/shop/orderlist'|ezurl}>{'Order list'|i18n( $ctx )}</a> &middot; <a href={'/shop/status'|ezurl}>{'Order status'|i18n( $ctx )}</a> &middot; <a href={'/shop/archivelist'|ezurl}>{'Archive'|i18n( $ctx )}</a></div>
    </li>
</ol>

{if $d.triggers|count}
<h3>{'Workflows connected to the shop'|i18n( $ctx )}</h3>
<div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
<tr><th>{'Trigger'|i18n( $ctx )}</th><th>{'Workflow'|i18n( $ctx )}</th><th>{'Events'|i18n( $ctx )}</th></tr>
{foreach $d.triggers as $tr}
<tr><td><code>shop / {$tr.function|wash} / {$tr.connect_type}</code></td><td><a href={concat( '/workflow/view/', $tr.workflow_id )|ezurl}>{$tr.workflow|wash}</a></td><td>{$tr.events|implode( ', ' )|wash}</td></tr>
{/foreach}
</table></div>
{/if}

<div class="a4-shop-grid2">
<div class="a4-shop-card">
    <h3>{'Order statuses and what they mean'|i18n( $ctx )}</h3>
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Status'|i18n( $ctx )}</th><th>{'Meaning'|i18n( $ctx )}</th></tr>
    {foreach $d.statuses as $s}
    <tr><td class="a4-nowrap"><span class="a4-shop-dot a4-shop-g-{$s.group}"></span>{$s.name|wash} <span class="a4-shop-note">({$s.status_id})</span></td>
        <td>{$s.meaning|wash}
            {if $s.is_active|not} {'Inactive: cannot be chosen.'|i18n( $ctx )}{/if}</td></tr>
    {/foreach}
    </table></div>
    <p class="a4-shop-note">{'Who may change a status, and from which status to which, is set with the policy shop/setstatus in the roles.'|i18n( $ctx )} <a href={'/shop/status'|ezurl}>{'Order status'|i18n( $ctx )}</a> &middot; <a href={'/role/list'|ezurl}>{'Roles and policies'|i18n( $ctx )}</a></p>
</div>

<div class="a4-shop-card">
    <h3>{'VAT'|i18n( $ctx )}</h3>
    <p>{if $d.config.dynamic_vat}{'Dynamic VAT is on: products whose price uses "%dynamic" get the VAT rule for the customer country and product category.'|i18n( $ctx, , hash( '%dynamic', ezini( 'VATSettings', 'DynamicVatTypeName', 'shop.ini' )|wash ) )}{else}{'Dynamic VAT is off (shop.ini [VATSettings] Handler is not set): every product price carries its own VAT type, and VAT rules are not applied.'|i18n( $ctx )}{/if}</p>
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'VAT type'|i18n( $ctx )}</th><th class="a4-num">%</th><th class="a4-num">{'Products'|i18n( $ctx )}</th></tr>
    {foreach $d.vat_types as $vt}
    <tr><td>{$vt.name|wash}</td><td class="a4-num">{$vt.percentage|l10n( 'number' )}</td><td class="a4-num">{$vt.products}</td></tr>
    {/foreach}
    {if $d.products.dynamic_vat}<tr><td>{ezini( 'VATSettings', 'DynamicVatTypeName', 'shop.ini' )|wash}</td><td class="a4-num">&ndash;</td><td class="a4-num">{$d.products.dynamic_vat}</td></tr>{/if}
    </table></div>
    <p class="a4-shop-note">{'VAT rules: %count.'|i18n( $ctx, , hash( '%count', $d.vat_rules|count ) )}{foreach $d.vat_rules as $vr} <span class="a4-tag">{$vr.country|wash}: {$vr.vat_type|wash}{if $vr.categories} ({$vr.categories|wash}){/if}</span>{/foreach}
        {'Product categories: %count.'|i18n( $ctx, , hash( '%count', $d.product_categories|count ) )}{foreach $d.product_categories as $pc} <span class="a4-tag">{$pc.name|wash}</span>{/foreach}</p>
    {if $d.order_countries|count}
    <p class="a4-shop-note">{'Orders of the last 60 days came from'|i18n( $ctx )}:
    {foreach $d.order_countries as $oc} <span class="a4-tag{if and( $d.config.dynamic_vat, $oc.has_rule|not )} is-warn{/if}">{$oc.name|wash} {$oc.orders}{if $d.config.dynamic_vat}{if $oc.has_rule} &check;{else} &ndash; {'no rule'|i18n( $ctx )}{/if}{/if}</span>{/foreach}</p>
    {/if}
    <p class="a4-shop-note"><a href={'/shop/vattype'|ezurl}>{'VAT types'|i18n( $ctx )}</a> &middot; <a href={'/shop/vatrules'|ezurl}>{'VAT rules'|i18n( $ctx )}</a> &middot; <a href={'/shop/productcategories'|ezurl}>{'Product categories'|i18n( $ctx )}</a></p>
</div>

<div class="a4-shop-card">
    <h3>{'Currencies'|i18n( $ctx )}</h3>
    {if $d.currencies|count}
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Code'|i18n( $ctx )}</th><th>{'Symbol'|i18n( $ctx )}</th><th class="a4-num">{'Rate'|i18n( $ctx )}</th><th>{'Status'|i18n( $ctx )}</th></tr>
    {foreach $d.currencies as $cu}
    <tr><td>{$cu.code|wash}</td><td>{$cu.symbol|wash}</td><td class="a4-num">{$cu.rate|wash}</td><td>{if $cu.is_active}<span class="a4-tag is-ok">{'active'|i18n( $ctx )}</span>{else}<span class="a4-tag">{'inactive'|i18n( $ctx )}</span>{/if}</td></tr>
    {/foreach}
    </table></div>
    {else}
    <p>{'No currencies are defined. That is fine for a shop in one currency with simple prices (ezprice): prices are shown in the currency of the site locale, %code. Currencies are needed for multi-currency prices (ezmultiprice).'|i18n( $ctx, , hash( '%code', $d.config.locale_currency|wash ) )}</p>
    {/if}
    <dl class="a4-shop-kv">
        <dt>{'Site locale currency'|i18n( $ctx )}</dt><dd><code>{$d.config.locale_currency|wash}</code></dd>
        <dt><i>shop.ini PreferredCurrency</i></dt><dd><code>{if $d.config.preferred_currency}{$d.config.preferred_currency|wash}{else}{'not set'|i18n( $ctx )}{/if}</code></dd>
        <dt>{'Exchange rates'|i18n( $ctx )}</dt><dd><code>{$d.config.rates_handler|wash}</code>, {'base'|i18n( $ctx )} <code>{$d.config.base_currency|wash}</code></dd>
    </dl>
    <p class="a4-shop-note"><a href={'/shop/currencylist'|ezurl}>{'Currencies'|i18n( $ctx )}</a> &middot; <a href={'/shop/preferredcurrency'|ezurl}>{'Preferred currency'|i18n( $ctx )}</a></p>
</div>

<div class="a4-shop-card">
    <h3>{'Discounts'|i18n( $ctx )}</h3>
    {if $d.discount_rules|count}
    <div class="a4-table-wrap a4-shop-scroll"><table class="a4-table is-compact">
    <tr><th>{'Discount group'|i18n( $ctx )}</th><th class="a4-num">{'Rules'|i18n( $ctx )}</th><th class="a4-num">{'Up to'|i18n( $ctx )}</th><th class="a4-num">{'Customers'|i18n( $ctx )}</th></tr>
    {foreach $d.discount_rules as $dr}
    <tr><td><a href={concat( '/shop/discountgroupview/', $dr.id )|ezurl}>{$dr.name|wash}</a></td><td class="a4-num">{$dr.sub_rules}</td><td class="a4-num">{$dr.max_percent|l10n( 'number' )} %</td><td class="a4-num">{$dr.members}</td></tr>
    {/foreach}
    </table></div>
    {else}
    <p>{'No discount groups. A discount group gives users or user groups a percentage off chosen products, classes or sections; the discount appears in the basket once they sign in.'|i18n( $ctx )}</p>
    {/if}
    <p class="a4-shop-note"><a href={'/shop/discountgroup'|ezurl}>{'Discounts'|i18n( $ctx )}</a></p>
</div>
</div>

{* ------------------------------------------------------------------ Next steps *}
<h2 id="sd-next">{'Next steps'|i18n( $ctx )}</h2>
<p class="a4-shop-lead">{'Worked out from the orders, products and settings above, most urgent first.'|i18n( $ctx )}</p>
{if $d.checklist|count}
<ul class="a4-shop-checklist">
{foreach $d.checklist as $item}
    <li class="{$item.level}"><span class="a4-shop-level">{if $item.level|eq( 'critical' )}{'Action'|i18n( $ctx )}{elseif $item.level|eq( 'warning' )}{'Check'|i18n( $ctx )}{else}{'Tip'|i18n( $ctx )}{/if}</span>{$item.text|wash}{if $item.url} <a href={$item.url|ezurl}>{$item.link_text|wash} &rsaquo;</a>{/if}</li>
{/foreach}
</ul>
{else}
<ul class="a4-shop-checklist"><li class="ok">{'Nothing to do: orders are moving and the shop is fully set up.'|i18n( $ctx )}</li></ul>
{/if}

<p class="a4-shop-note">{'Figures computed in %ms ms.'|i18n( $ctx, , hash( '%ms', $d.build_ms ) )}</p>

{* DESIGN: Content END *}</div></div></div>
</div>
{undef}
