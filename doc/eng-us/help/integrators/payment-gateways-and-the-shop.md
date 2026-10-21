---
title: Payment gateways and the shop
audience: integrator, developer
category: integration
order: 250
summary: Integrating the shop module with payment providers and ERP systems - how payment gateways plug into the checkout workflow, registering a gateway of your own, the ezpaypal extension as a model, passing orders to an ERP on checkout, reading orders from PHP, and the commerce audit events.
keywords: shop, payment gateway, paymentgateways.ini, eZPaymentGateway, eZRedirectGateway, eZPaymentCallbackChecker, eZPaymentGatewayType, registerGateway, event_ezpaymentgateway, ezpaypal, checkout, confirmorder, eZOrder, ERP, order export, commerce audit
type: help
---
# Payment gateways and the shop

This page is for integrators who connect the shop module of Exponential with a payment provider or pass orders to an
ERP. The shop runs its checkout through operations with triggers (`shop / checkout`, `shop / confirmorder`), and
payment is a **workflow event** (*Payment gateway*, `event_ezpaymentgateway`) that hands over to the gateway chosen in
`paymentgateways.ini`.

## Contents

1. [How payment fits into the checkout](#how-payment-fits-into-the-checkout)
2. [A gateway of your own](#a-gateway-of-your-own)
3. [Passing orders to an ERP](#passing-orders-to-an-erp)
4. [Reading orders from PHP](#reading-orders-from-php)
5. [Audit events of the shop](#audit-events-of-the-shop)
6. [Checklist](#checklist)

## How payment fits into the checkout

```text
 basket -> shop/confirmorder (trigger before) -> shop/checkout (trigger)
                                                   |
                                                   v
                                     workflow with the event "Payment gateway"
                                                   |
                         gateway from paymentgateways.ini (for example ezpaypal)
                                                   |
                         customer redirected to the provider, provider calls back
                                                   |
                                     order status set, checkout continues
```

| Part | Where |
|---|---|
| Gateway list | `settings/paymentgateways.ini [GatewaysSettings] AvailableGateways[]`, `GatewaysDirectories[]` (default `kernel/shop/paymentgateways`) |
| Base classes | `eZPaymentGateway` and `eZRedirectGateway` (`kernel/shop/classes/`), `eZPaymentCallbackChecker` for the provider's callback |
| Registration | `eZPaymentGatewayType::registerGateway( $gateway, $class_name, $description )` (`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`) |
| A real gateway | `ezpaypal` (required as `~1.2.3`): `classes/ezpaypalgateway.php` registers itself with `registerGateway(...)`; the module `paypal` answers the callback at `/paypal/notify_url` |
| Class reference | `doc/eng-us/shop/index.html` (the shop's class documentation) |

To use a gateway: activate it, configure it (for `ezpaypal`: `settings/override/paypal.ini.append.php`, `Business`,
the sandbox `ServerName` while testing), create a workflow with the *Payment gateway* event, and connect it at
**Setup > Triggers** to the checkout ([ezpaypal](../../features/6.0/extensions/ezpaypal.md)).

## A gateway of your own

Follow `ezpaypal`'s layout:

1. A class extending `eZRedirectGateway` (for providers that redirect the customer) or `eZPaymentGateway`.
2. At the end of the class file, `eZPaymentGatewayType::registerGateway( '<gateway id>', '<class name>', '<label>' );`
3. The gateway's directory in `paymentgateways.ini [GatewaysSettings] GatewaysDirectories[]` and its id in
   `AvailableGateways[]`, in your extension's settings.
4. A module view for the provider's callback (notify URL) that checks the callback with a class extending
   `eZPaymentCallbackChecker`: verify the provider's signature, the amount and the currency against the order, and
   only then set the order status.
5. Test with the provider's sandbox; never send real card data through a development installation.

> **Note**
> Payment integrations carry card-scheme and legal obligations (PCI DSS, strong customer authentication). Redirect
> and hosted-field integrations keep card data away from Exponential and are the usual choice. This is not legal or
> compliance advice.

## Passing orders to an ERP

Use the outbox pattern ([Workflows and triggers](workflows-and-triggers-as-hooks.md#the-outbox-pattern-notice-fast-send-later)):
a workflow event on the checkout records the order id (the operations' parameter `order_id`), and a cronjob part
sends the order to the ERP with retries. An event on `shop / confirmorder` (before) can also ask the ERP for stock and
return `STATUS_REJECTED` when an item is not available.

## Reading orders from PHP

```php
<?php
$order = eZOrder::fetch( $orderID );
if ( $order instanceof eZOrder )
{
    $payload = array(
        'order_id' => (int)$order->attribute( 'id' ),
        'customer' => $order->accountName(),
        'email'    => $order->accountEmail(),
        'currency' => $order->currencyCode(),
        'total'    => $order->totalIncVAT(),
        'items'    => array(),
    );
    foreach ( $order->productItems() as $item )
    {
        $payload['items'][] = $item;   // per item: the product object, count, price; map to the ERP's article numbers
    }
}
```

Map each product to the ERP's article number with the product object's `remote_id`, set by your product import
([Mapping classes to external data](mapping-content-classes-to-external-data.md)). `accountInformation()` returns the
customer's address data as the shop account handler stored it.

## Audit events of the shop

The `commerce` channel records order removals and VAT, currency and discount changes by default; `commerce.order.create`,
`commerce.order.status` and `commerce.payment.approve` exist and are **off** by default. Switch them on in
`[AuditEventSettings] Enabled[]` when the ERP integration needs a trace, or to feed them to the audit webhook
([Outgoing webhooks](outgoing-webhooks-from-the-audit-trail.md)).

## Checklist

- [ ] Gateway tested end to end in the provider's sandbox, including cancelled and failed payments.
- [ ] Callback verified (signature, amount, currency, order id) before the order status changes.
- [ ] Orders sent to the ERP from a cronjob with retries; the ERP refuses duplicates by order id.
- [ ] Product ids mapped by `remote_id`, not by object id.
- [ ] Order data treated as personal data ([Protecting customer data](../partners/protecting-customer-data.md)).

## Related pages

- [ezpaypal](../../features/6.0/extensions/ezpaypal.md)
- [Store dashboard](../../features/6.0/store-dashboard.md), [Order receipts](../../features/6.0/order-receipts.md)
- [Workflows and triggers as integration hooks](workflows-and-triggers-as-hooks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
