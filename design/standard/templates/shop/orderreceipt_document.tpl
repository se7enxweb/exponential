{* The receipt as a file to keep: a whole HTML document with its styles
   inline and nothing from the site around it. Needs $order and $receipt_url. *}<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>{'Receipt'|i18n( 'design/standard/shop/orderreceipt' )} #{$order.order_nr} - {ezini( 'SiteSettings', 'SiteName' )|wash}</title>
{include uri='design:shop/orderreceipt_style.tpl'}
<style>body{ldelim}margin:0;background:#f5f7fa{rdelim}</style>
</head>
<body>
<div class="order-receipt">
    {include uri='design:shop/orderreceipt_body.tpl' order=$order receipt_url=$receipt_url}
</div>
</body>
</html>
