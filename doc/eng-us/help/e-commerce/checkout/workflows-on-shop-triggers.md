---
title: Workflows on shop triggers
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 43
summary: Which workflow events belong on which shop trigger in Exponential, how to set up the shipping and payment workflows step by step, what an event on each trigger can and cannot do, and the mistakes that break a checkout.
keywords: workflow, trigger, shop_checkout, before_shop_confirmorder, shop_addtobasket, shop_updatebasket, before, after, payment workflow, shipping workflow, event order, workflow process, Setup Triggers
type: help
---
# Workflows on shop triggers

This page is for developers and administrators who connect workflows to the shop. It maps each shop trigger to the
events that fit there, walks through setting up the usual two workflows, and lists the mistakes that break a
checkout.

## Contents

1. [The trigger map](#the-trigger-map)
2. [Set up the shipping workflow](#set-up-the-shipping-workflow)
3. [Set up the payment workflow](#set-up-the-payment-workflow)
4. [Several events on one trigger](#several-events-on-one-trigger)
5. [Events of your own](#events-of-your-own)
6. [Mistakes that break a checkout](#mistakes-that-break-a-checkout)

## The trigger map

| Trigger in Setup > Triggers | Operation point | What exists at that moment | Fits |
|---|---|---|---|
| `shop` `addtobasket` before | `pre_addtobasket` | The product (`object_id`), chosen options, quantity | Refusing an item (for example a product that may not be sold to this customer) |
| `shop` `addtobasket` after | `post_addtobasket` | The item is in the basket | Logging, recommendations |
| `shop` `updatebasket` before / after | `pre_updatebasket`, `post_updatebasket` | Item ids and counts | Quantity limits |
| `shop` `confirmorder` before | `pre_confirmorder` | The temporary order | Shipping (`ezsimpleshipping` or your own event), surcharges as order items |
| `shop` `checkout` before | `pre_checkout` | The temporary order with its final total | Payment (`ezpaymentgateway`) |
| `shop` `checkout` after | `post_checkout` | The placed order with its number; the mail has been sent | Reporting, exports to other systems, analytics |

Which triggers appear at all is decided by `workflow.ini [OperationSettings]`
([workflow.ini for the shop](../settings/workflow-ini-for-the-shop.md)). Which event types may be put on a trigger is
decided by each event type: `ezsimpleshipping` only allows `shop` `confirmorder` before.

## Set up the shipping workflow

1. Set the amount: `ShippingCost` and `ShippingDescription` in `settings/override/workflow.ini.append.php`.
2. **Setup > Workflows**, open a group, **New workflow**, name it "Shipping".
3. Add the event **Simple shipping**, save.
4. **Setup > Triggers**: for `shop` `confirmorder` before, choose "Shipping", save.
5. Place a test order: the confirmation page shows the shipping line.

Details and limits: [Simple shipping event](simple-shipping-event.md).

## Set up the payment workflow

1. Activate the gateway extension and set its provider file to test mode
   ([paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md)).
2. **Setup > Workflows**, **New workflow**, name it "Payment".
3. Add the event **Payment Gateway**; in the event, select the gateways customers may use; save.
4. **Setup > Triggers**: for `shop` `checkout` before, choose "Payment", save.
5. Place a test order and complete the payment in the provider's test mode.

Details: [Payment gateway event](payment-gateway-event.md).

## Several events on one trigger

A trigger has one workflow. To run several events at one point, add them to that workflow in order. Events run one
after the other; the next starts only when the previous accepted. On `pre_checkout`, put checks that may refuse an
order before the payment event, so that a customer is never charged for an order that a later event refuses.

To run an event only for some customers or products, wrap a second workflow in a multiplexer event
([Multiplexer in the shop](multiplexer-in-the-shop.md)).

## Events of your own

An event type is a class extending `eZWorkflowEventType` with an `execute()` method that returns a status: accepted,
rejected, show a template, redirect, or defer to cron. Generate one with **Setup > RAD**'s workflow event wizard
(`setup/workflowevent`) and read [Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md).
Restrict it to the triggers it understands with `setTriggerTypes()`, as `eZSimpleShippingType` does.

For an event on `post_checkout`, the process parameters carry `order_id`; fetch the order with `eZOrder::fetch()` and
read its number, items and totals. Never read card data: there is none to read, and there must not be.

## Mistakes that break a checkout

| Mistake | Symptom |
|---|---|
| Payment event on `checkout` after instead of before | Orders are placed and mailed before anyone pays |
| Shipping event on `checkout` before | No shipping on the confirmation page; total changes after confirming |
| Simple shipping event and a shipping handler both active | Two independent shipping charges can appear; use one or the other |
| Workflow edited but not saved | Old behavior continues; check **Setup > Triggers** |
| `workflow.php` cron part not running | Deferred processes never resume ([Workflows](../../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)) |
| No payment workflow at all | Every order is placed unpaid; the dashboard's "Next steps" warns about it |

## Related pages

- [Checkout and workflows](README.md)
- [Workflows](../../../guides/workflows.md)
- [Workflows, triggers and events](../../creators/app-creators/workflows-and-events.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
