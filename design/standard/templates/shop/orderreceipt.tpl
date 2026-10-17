{* An order's permanent receipt, inside the site design: print, download and
   a reminder that the address can be kept. Needs $order and $receipt_url. *}
{include uri='design:shop/orderreceipt_style.tpl'}
<div class="order-receipt">
    <div class="order-receipt-actions">
        <button type="button" class="primary order-receipt-print">{'Print'|i18n( 'design/standard/shop/orderreceipt' )}</button>
        <a href={concat( $receipt_url, '?download=1' )|ezurl} download>{'Download'|i18n( 'design/standard/shop/orderreceipt' )}</a>
        <span class="order-receipt-hint">{'Bookmark this page to come back to your receipt at any time; you will be asked to sign in.'|i18n( 'design/standard/shop/orderreceipt' )}</span>
    </div>
    {include uri='design:shop/orderreceipt_body.tpl' order=$order receipt_url=$receipt_url}
</div>
{* The print button and the printing (javascript/shop_orderreceipt.js, a file of its own for a Content Security
   Policy without 'unsafe-inline') *}
<script src={'javascript/shop_orderreceipt.js'|ezdesign}></script>
