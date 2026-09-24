{* An order's permanent receipt, inside the site design: print, download and
   a reminder that the address can be kept. Needs $order and $receipt_url. *}
{include uri='design:shop/orderreceipt_style.tpl'}
<div class="order-receipt">
    <div class="order-receipt-actions">
        <button type="button" class="primary" onclick="window.print()">{'Print'|i18n( 'design/standard/shop/orderreceipt' )}</button>
        <a href={concat( $receipt_url, '?download=1' )|ezurl} download>{'Download'|i18n( 'design/standard/shop/orderreceipt' )}</a>
        <span class="order-receipt-hint">{'Bookmark this page to come back to your receipt at any time; you will be asked to sign in.'|i18n( 'design/standard/shop/orderreceipt' )}</span>
    </div>
    {include uri='design:shop/orderreceipt_body.tpl' order=$order receipt_url=$receipt_url}
</div>
<script>
{* Printing: the receipt is lifted out to be a direct child of <body> and
   everything else is hidden, whatever the site design's layout (fixed
   headers, positioned wrappers); put back afterwards. Without scripts the
   print styles alone still hide the site around it. *}
(function () {ldelim}
    var receipt = document.querySelector('.order-receipt'), home = null, next = null;
    if (!receipt) return;
    window.addEventListener('beforeprint', function () {ldelim}
        home = receipt.parentNode; next = receipt.nextSibling;
        document.body.appendChild(receipt);
        document.body.classList.add('order-receipt-printing');
    {rdelim});
    window.addEventListener('afterprint', function () {ldelim}
        document.body.classList.remove('order-receipt-printing');
        if (home) home.insertBefore(receipt, next);
    {rdelim});
{rdelim})();
</script>
