{* shop/myorders: the signed-in customer's own orders, newest first. Variables: $order_list, $order_count, $offset,
   $limit, $view_parameters. Each order links where the shop links orders ($order.link_url: the order view or the
   permanent receipt, shop.ini [OrderViewSettings] OrderLinkView). Override it in the site design to restyle it. *}
<div class="shop-myorders">
<div class="attribute-header">
    <h1 class="long">{'Orders'|i18n( 'design/admin/shop/dashboard' )}</h1>
</div>

{if $order_list}
{def $currency = false()
     $locale = false()
     $symbol = false()}
<table class="list shop-myorders-list">
<thead>
<tr>
    <th scope="col">{'Order'|i18n( 'design/standard/shop' )}</th>
    <th scope="col">{'Date'|i18n( 'design/standard/shop' )}</th>
    <th scope="col">{'Status'|i18n( 'design/standard/shop/orderreceipt' )}</th>
    <th scope="col">{'Total inc. VAT'|i18n( 'design/standard/shop' )}</th>
    <th scope="col">{'Details'|i18n( 'design/admin/node/view/full' )}</th>
</tr>
</thead>
<tbody>
{foreach $order_list as $order sequence array( 'bglight', 'bgdark' ) as $style}
{set $currency = fetch( 'shop', 'currency', hash( 'code', $order.productcollection.currency_code ) )}
{if $currency}
    {set $locale = $currency.locale
         $symbol = $currency.symbol}
{else}
    {set $locale = false()
         $symbol = false()}
{/if}
<tr class="{$style}">
    <td>{$order.order_nr|wash}</td>
    <td>{$order.created|l10n( 'shortdatetime' )}</td>
    <td>{$order.status_name|wash}</td>
    <td>{$order.total_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
    <td><a href={$order.link_url|ezurl}>{'View'|i18n( 'design/standard/shop' )}</a></td>
</tr>
{/foreach}
</tbody>
</table>
{undef $currency $locale $symbol}

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/shop/myorders'
         item_count=$order_count
         view_parameters=$view_parameters
         item_limit=$limit}
{else}
<p class="shop-myorders-empty">{'No orders yet.'|i18n( 'design/admin/shop/dashboard' )}</p>
{/if}
</div>
