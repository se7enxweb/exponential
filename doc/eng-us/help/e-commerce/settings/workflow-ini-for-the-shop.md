---
title: workflow.ini for the shop
audience: ecommerce, website-creator, admin
category: e-commerce
order: 34
summary: The parts of settings/workflow.ini that matter to a store - the available event types, the four shop triggers in AvailableOperationList, the SimpleShippingWorkflow amount and description, and how extensions add event types of their own.
keywords: workflow.ini, EventSettings, AvailableEventTypes, OperationSettings, AvailableOperationList, before_shop_confirmorder, shop_checkout, shop_addtobasket, shop_updatebasket, SimpleShippingWorkflow, ShippingCost, ShippingDescription, ExtensionDirectories
type: help
---
# workflow.ini for the shop

This page is for developers and administrators who connect workflows to the shop. `settings/workflow.ini` decides
which workflow event types exist, which operations can trigger a workflow, and the amount of the flat shipping event.
The workflows themselves are created in the administration.

## Contents

1. [EventSettings](#eventsettings)
2. [OperationSettings](#operationsettings)
3. [SimpleShippingWorkflow](#simpleshippingworkflow)
4. [Event types from extensions](#event-types-from-extensions)
5. [Checking the result](#checking-the-result)

## EventSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/workflowtypes` | Where kernel event types are found |
| `ExtensionDirectories[]` | empty | Extensions with a `workflowtypes/event/<name>/` directory |
| `AvailableEventTypes[]` | `event_ezapprove`, `event_ezwaituntildate`, `event_ezsimpleshipping`, `event_ezmultiplexer`, `event_ezpaymentgateway`, `event_ezfinishuserregister` | The event types offered when you build a workflow |

Of these, three matter to a store:

| Event type | Allowed triggers (from the class) | Store use |
|---|---|---|
| `ezsimpleshipping` | `shop` / `confirmorder` / `before` only | Adds one flat shipping line ([Simple shipping event](../checkout/simple-shipping-event.md)) |
| `ezpaymentgateway` | any | Runs the payment gateway; put it on `shop_checkout` ([Payment gateway event](../checkout/payment-gateway-event.md)) |
| `ezmultiplexer` | any | Runs another workflow when conditions match ([Multiplexer in the shop](../checkout/multiplexer-in-the-shop.md)) |

`ezapprove` and `ezwaituntildate` are tied to content publishing and `ezfinishuserregister` to user registration; they
cannot be connected to shop triggers.

## OperationSettings

`AvailableOperationList[]` lists the operations that may start a workflow. An entry without a prefix
(`shop_checkout`) offers both a **before** and an **after** trigger; an entry starting with `before_` or `after_`
offers only that one (the trigger list page parses the names this way, `kernel/private/classes/views/trigger/list.php`).
Shipped:

| Entry | Operation | Triggers offered | Typical event |
|---|---|---|---|
| `content_publish` | `content/publish` | before, after | approval |
| `before_shop_confirmorder` | `shop/confirmorder` | before | shipping |
| `shop_checkout` | `shop/checkout` | before (payment), after (once the order is activated and mailed) | payment; reporting |
| `shop_addtobasket` | `shop/addtobasket` | before, after | checks on items |
| `shop_updatebasket` | `shop/updatebasket` | before, after | checks on quantities |

The `confirmorder` operation has no after trigger point at all (`kernel/shop/operation_definition.php`), which is why
its entry carries the `before_` prefix. How each trigger fits into its operation is in
[Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md).

## SimpleShippingWorkflow

| Setting | Shipped | Meaning |
|---|---|---|
| `ShippingCost` | `12` | The flat amount added to every order, in the shop's currency; converted to the order's currency |
| `ShippingDescription` | `Shipping` | The text of the shipping line on the order |

The amount is the same for every order. Set it in `settings/override/workflow.ini.append.php`.

## Event types from extensions

An extension adds an event type by placing it in `workflowtypes/event/<name>/<name>type.php`, adding its own name to
`ExtensionDirectories[]` and the type to `AvailableEventTypes[]` in its `workflow.ini.append.php`. The
`bcwebsitestatistics` extension, for example, adds an `ezreceipt` event type that runs after checkout to report orders
to an analytics service ([Analytics for stores](../operations/analytics-for-stores.md)). Writing one is described in
[Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md).

## Checking the result

**Setup > Triggers** lists every trigger from `AvailableOperationList[]` with the workflow connected to it, and
**Setup > Workflows** shows each workflow's events ([Workflows](../../../guides/workflows.md#11-the-trigger-list-page)).
The Store dashboard's "How your shop works" names the workflows on the shop triggers and warns when there is no payment
step on checkout.

## Related pages

- [Shop settings walkthroughs](README.md)
- [Checkout and workflows](../checkout/README.md)
- [Workflows, triggers and events](../../creators/app-creators/workflows-and-events.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
