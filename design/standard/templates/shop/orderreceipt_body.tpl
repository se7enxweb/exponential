{* The receipt itself, shared by the page (shop/orderreceipt.tpl) and the
   downloadable file (shop/orderreceipt_document.tpl). Needs $order.
   Override it under a site design to restyle the receipt. *}
{def $currency = fetch( 'shop', 'currency', hash( 'code', $order.productcollection.currency_code ) )
     $locale = false()
     $symbol = false()}
{if $currency}
    {set locale = $currency.locale
         symbol = $currency.symbol}
{/if}
<article class="order-receipt-sheet">
    <header class="order-receipt-head">
        <div>
            <p class="order-receipt-site">{ezini( 'SiteSettings', 'SiteName' )|wash}</p>
            <h1>{'Receipt'|i18n( 'design/standard/shop/orderreceipt' )}</h1>
        </div>
        <dl class="order-receipt-meta">
            <dt>{'Order'|i18n( 'design/standard/shop/orderreceipt' )}</dt><dd>#{$order.order_nr}</dd>
            <dt>{'Date'|i18n( 'design/standard/shop/orderreceipt' )}</dt><dd>{$order.created|l10n( 'shortdatetime' )}</dd>
            <dt>{'Status'|i18n( 'design/standard/shop/orderreceipt' )}</dt><dd>{$order.status_name|wash}</dd>
        </dl>
    </header>

    <section class="order-receipt-account">
        {shop_account_view_gui view=html order=$order}
    </section>

    <h2>{'Items'|i18n( 'design/standard/shop/orderreceipt' )}</h2>
    <div class="order-receipt-scroll">
    <table class="order-receipt-table">
        <thead>
            <tr>
                <th>{'Product'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Count'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Price inc. VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Discount'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Total ex. VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Total inc. VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $order.product_items as $item}
            <tr>
                <td>{$item.object_name|wash}</td>
                <td class="num">{$item.item_count}</td>
                <td class="num">{$item.vat_value} %</td>
                <td class="num">{$item.price_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
                <td class="num">{$item.discount_percent}%</td>
                <td class="num">{$item.total_price_ex_vat|l10n( 'currency', $locale, $symbol )}</td>
                <td class="num">{$item.total_price_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>

    <h2>{'Summary'|i18n( 'design/standard/shop/orderreceipt' )}</h2>
    <div class="order-receipt-scroll">
    <table class="order-receipt-table order-receipt-summary">
        <thead>
            <tr>
                <th></th>
                <th class="num">{'Ex. VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
                <th class="num">{'Inc. VAT'|i18n( 'design/standard/shop/orderreceipt' )}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{'Subtotal of items'|i18n( 'design/standard/shop/orderreceipt' )}</td>
                <td class="num">{$order.product_total_ex_vat|l10n( 'currency', $locale, $symbol )}</td>
                <td class="num">{$order.product_total_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
            </tr>
            {foreach $order.order_items as $order_item}
            <tr>
                <td>{$order_item.description|wash}</td>
                <td class="num">{$order_item.price_ex_vat|l10n( 'currency', $locale, $symbol )}</td>
                <td class="num">{$order_item.price_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
            </tr>
            {/foreach}
            <tr class="order-receipt-total">
                <td>{'Order total'|i18n( 'design/standard/shop/orderreceipt' )}</td>
                <td class="num">{$order.total_ex_vat|l10n( 'currency', $locale, $symbol )}</td>
                <td class="num">{$order.total_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
            </tr>
        </tbody>
    </table>
    </div>

    {def $history = fetch( 'shop', 'order_status_history', hash( 'order_id', $order.order_nr ) )}
    {if $history|count()}
    <h2>{'History'|i18n( 'design/standard/shop/orderreceipt' )}</h2>
    <table class="order-receipt-table">
        <tbody>
        {foreach $history as $entry}
            <tr><td>{$entry.modified|l10n( 'shortdatetime' )}</td><td>{$entry.status_name|wash}</td></tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
    {undef $history}

    <footer class="order-receipt-foot">
        {'This receipt stays at this address. Only you, signed in, and the shop can open it.'|i18n( 'design/standard/shop/orderreceipt' )}<br>
        <span class="order-receipt-url">{$receipt_url|ezurl( 'no', 'full' )}</span>
    </footer>
</article>
{undef $currency $locale $symbol}
