<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="shop-basket">

<ul>
    <li class="selected">1. {"Shopping cart"|i18n("design/ezwebin/shop/basket")}</li>
    <li>2. {"Account information"|i18n("design/ezwebin/shop/basket")}
</li>
    <li>3. {"Confirm order"|i18n("design/ezwebin/shop/basket")}</li>
    <li>4. {"Payment"|i18n(
"design/ezwebin/shop/basket")}</li>
    <li>5. {"Order receipt"|i18n(
"design/ezwebin/shop/basket")}</li>
</ul>

</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="shop-basket">

<form method="post" action={"/shop/cart/"|ezurl}>

<div class="attribute-header">
    <h1 class="long">{"Cart"|i18n("design/ezwebin/shop/basket")}</h1>
</div>
{section show=$removed_items}
<div class="warning">
    <h2>{"The following items were removed from your basket because the products were changed."|i18n("design/ezwebin/shop/basket",,)}</h2>
    <ul>
    {section name=RemovedItem loop=$removed_items}
        <li> <a href={concat("/content/view/full/",$RemovedItem:item.contentobject.main_node_id,"/")|ezurl}>{$RemovedItem:item.contentobject.name|wash}</a></li>
    {/section}
    </ul>
</div>
{/section}

{if not( $vat_is_known )}
<div class="message-warning">
<h2>{'VAT is unknown'|i18n( 'design/ezwebin/shop/basket' )}</h2>
{'VAT percentage is not yet known for some of the items being purchased.'|i18n( 'design/ezwebin/shop/basket' )}<br/>
{'This probably means that some information about you is not yet available and will be obtained during checkout.'|i18n( 'design/ezwebin/shop/basket' )}
</div>
{/if}

{section show=$error}
<div class="error">
{section show=$error|eq(1)}
<h2>{"Attempted to add object without price to basket."|i18n("design/ezwebin/shop/basket",,)}</h2>
{/section}
</div>
{/section}

{section show=$error}
<div class="error">
{section show=eq($error, "aborted")}
<h2>{"Your payment was aborted."|i18n("design/ezwebin/shop/basket",,)}</h2>
{/section}
</div>
{/section}


    {def $currency = fetch( 'shop', 'currency', hash( 'code', $basket.productcollection.currency_code ) )
         $locale = false()
         $symbol = false()}
    {if $currency}
        {set locale = $currency.locale
             symbol = $currency.symbol}
    {/if}

    {section name=Basket show=$basket.items}


<table class="list"  width="90%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <th>
    {"Quantity"|i18n("design/ezwebin/shop/basket")}
    </th>
{*    <th>
    {"VAT"|i18n("design/ezwebin/shop/basket")}
    </th>
*}
    <th>
    {"Price"|i18n("design/ezwebin/shop/basket")}
    </th>
{*
    <th>
    {"Discount"|i18n("design/ezwebin/shop/basket")}
    </th>
    <th>
    {"Total price ex. VAT"|i18n("design/ezwebin/shop/basket")}
    </th>
*}
    <th>
    {"Total price"|i18n("design/ezwebin/shop/basket")}
    </th>
    <th>&nbsp;
    </th>
</tr>
{section name=ProductItem loop=$basket.items sequence=array(bgdark, bglight)}
<tr class="bglight">
    <td colspan="4"><input type="hidden" name="ProductItemIDList[]" value="{$Basket:ProductItem:item.id}" />
    {*{$Basket:ProductItem:item.id}-*}
    <a href={concat("/content/view/full/",$Basket:ProductItem:item.node_id,"/")|ezurl}>{$Basket:ProductItem:item.object_name}</a></td>
</tr>
<tr class="bgdark">
    <td>
    <input type="text" name="ProductItemCountList[]" value="{$Basket:ProductItem:item.item_count}" size="5" />
    </td>
{*    <td>
    {if ne( $Basket:ProductItem:item.vat_value, -1 )}
        {$Basket:ProductItem:item.vat_value} %
    {else}
        {'Unknown'|i18n( 'design/ezwebin/shop/basket' )}
    {/if}
    </td>
*}
    <td>
    {$Basket:ProductItem:item.price_inc_vat|l10n( 'currency', $locale, $symbol )}
    </td>
{*
    <td>
    {$Basket:ProductItem:item.discount_percent}%
    </td>
    <td>
    {$Basket:ProductItem:item.total_price_ex_vat|l10n( 'currency', $locale, $symbol )}
    </td>
*}
    <td>
    {$Basket:ProductItem:item.total_price_inc_vat|l10n( 'currency', $locale, $symbol )}
    </td>
    <td>
    <input type="checkbox" name="RemoveProductItemDeleteList[]" value="{$Basket:ProductItem:item.id}" />
    </td>
</tr>
<tr class="bglight">
    <td colspan="2"><input class="button" type="submit" name="StoreChangesButton" value="{'Update'|i18n('design/ezwebin/shop/basket')}" /></td>
    <td colspan="2"><input class="button" type="submit" name="RemoveProductItemButton" value="{'Remove'|i18n('design/ezwebin/shop/basket')}" /> </td>
</tr>
{section show=$Basket:ProductItem:item.item_object.option_list}
<tr>
  <td colspan="4" style="padding: 0;">
     <table cellpadding="0" cellspacing="0">
<tr>
<td colspan="3">
{"Selected options"|i18n("design/ezwebin/shop/basket")}
</td>
</tr>
     {section name=Options loop=$Basket:ProductItem:item.item_object.option_list sequence=array(bglight, bgdark)}
      <tr>
        <td width="33%">{$Basket:ProductItem:Options:item.name}</td>
        <td width="33%">{$Basket:ProductItem:Options:item.value}</td>
        <td width="33%">{$Basket:ProductItem:Options:item.price|l10n( 'currency', $locale, $symbol )}</td>
      </tr>
    {/section}
     </table>
   </td>
</tr>
{/section}
{/section}
<tr>
     <td colspan="4">
     <hr size='2' />
     </td>
</tr>
<tr>
     <td colspan="4">
     <strong>{"Subtotal"|i18n("design/ezwebin/shop/basket")}</strong>:     {$basket.total_inc_vat|l10n( 'currency', $locale, $symbol )}
     </td>
{*     <td>
     <strong>{"Subtotal ex. VAT"|i18n("design/ezwebin/shop/basket")}</strong>:
     </td>
     <td>
     </td>
*}
</tr>
<tr>
    <td colspan="4">
    </td>
{*
    <td>
        {$basket.total_ex_vat|l10n( 'currency', $locale, $symbol )}
    </td>
    <td>
    </td>
*}
</tr>
{if is_set( $shipping_info )}
{* Show shipping type/cost. *}
<tr>
     <td colspan="3">
     <a href={$shipping_info.management_link|ezurl}>{'Shipping'|i18n( 'design/ezwebin/shop/basket' )}{if $shipping_info.description} ({$shipping_info.description}){/if}</a>:
     </td>
{*
     <td>
     {$shipping_info.cost|l10n( 'currency', $locale, $symbol )}:
     </td>
*}  
   <td>
     {$shipping_info.cost|l10n( 'currency', $locale, $symbol )}
     </td>
</tr>
{* Show order total *}
<tr>
     <td colspan="3">
     <strong>{'Order total'|i18n( 'design/ezwebin/shop/basket' )}</strong>:
     </td>
{*
     <td>
     <strong>{$total_inc_shipping_ex_vat|l10n( 'currency', $locale, $symbol )}</strong>
     </td>
*}
     <td>
     <strong>{$total_inc_shipping_inc_vat|l10n( 'currency', $locale, $symbol )}</strong>
     </td>
</tr>
{/if}

</table>

<div class="buttonblock">
<input class="button" type="submit" name="CheckoutButton" value="{'Checkout'|i18n('design/ezwebin/shop/basket')}" /> &nbsp;
<input class="button" type="submit" name="ContinueShoppingButton" value="{'Continue shopping'|i18n('design/ezwebin/shop/basket')}" />
</div>

{undef $currency $locale $symbol}

{section-else}

<div class="feedback">
<h3>{"You have no products in your cart."|i18n("design/ezwebin/shop/basket")}</h3>
<h4><a href="/store" title="Shop now!">{"Add a product from our"|i18n("design/ezwebin/shop/basket")} store's selection of products</a></h4>
<a href="/store" title="Shop now!"><img
 src="{"favicon/favicon-banner.png"|ezimage(no)}"
 alt="Shop now!" class="sheep-icon" width=200 height=150></a>
</div>

{/section}

</form>

</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>
