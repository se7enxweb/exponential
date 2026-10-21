---
title: Checkout and workflows
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 40
summary: The Exponential checkout from the inside - the four shop operations and their trigger points, the temporary order, and the workflow events that add shipping, take payment or branch by condition - with a page for each part.
keywords: checkout, workflow, trigger, shop operation, confirmorder, shop/checkout, temporary order, payment gateway event, simple shipping, multiplexer, before_shop_confirmorder, shop_checkout
type: help
---
# Checkout and workflows

This part is for developers and technically minded merchants who need to know what happens between **Add to basket**
and a placed order. The customer's view of the path is described in
[Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md); these pages explain the machinery
underneath: operations, triggers, workflow processes and the events that run in them.

## Contents

1. [The idea in three sentences](#the-idea-in-three-sentences)
2. [The pages of this part](#the-pages-of-this-part)
3. [Where workflows are managed](#where-workflows-are-managed)
4. [What runs in the background](#what-runs-in-the-background)

## The idea in three sentences

Each shop step that changes something is an **operation** (`kernel/shop/operation_definition.php`): `addtobasket`,
`updatebasket`, `confirmorder` and `checkout`. Each operation has **trigger points**; a workflow connected to a
trigger runs its **events** there, and an event can accept, reject, show a page or send the customer elsewhere and
wait. Shipping and payment are such events.

## The pages of this part

| Page | Answers |
|---|---|
| [Checkout operations step by step](checkout-operations-step-by-step.md) | What each operation does, in which order, from basket to activated order |
| [Temporary orders and activation](temporary-orders-and-activation.md) | Why an order exists before it is placed, and what activation changes |
| [Workflows on shop triggers](workflows-on-shop-triggers.md) | Which events fit which trigger, and how to set them up |
| [Payment gateway event](payment-gateway-event.md) | Selecting gateways, the customer's choice, redirect and callback |
| [Simple shipping event](simple-shipping-event.md) | The flat shipping line and its limits |
| [Multiplexer in the shop](multiplexer-in-the-shop.md) | Running a sub-workflow only for some customers or products |

## Where workflows are managed

| Screen | Address | Use |
|---|---|---|
| Workflows | **Setup > Workflows** | Create a workflow and add events to it |
| Triggers | **Setup > Triggers** | Connect a workflow to a trigger |
| Workflow processes | **Setup > Workflow processes** | See processes that are waiting, for example a payment in progress |

All three are described in [Workflows](../../../guides/workflows.md). One workflow per trigger: to run several events
on one trigger, put them in one workflow in the order they should run.

## What runs in the background

- The `workflow.php` cron script (part `frequent` in `settings/cronjob.ini`) resumes processes that were deferred to
  cron ([Workflows](../../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)).
- The `basket_cleanup.php` script (part `infrequent`) removes baskets of expired sessions.
- A redirect gateway's callback arrives as a separate request from the provider; it is not a cron job, so the site
  must be reachable from the internet ([Payment gateway event](payment-gateway-event.md)).

## Related pages

- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Workflows, triggers and events](../../creators/app-creators/workflows-and-events.md)
- [workflow.ini for the shop](../settings/workflow-ini-for-the-shop.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
