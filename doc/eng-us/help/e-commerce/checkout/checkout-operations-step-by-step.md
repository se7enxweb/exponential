---
title: Checkout operations step by step
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 41
summary: The Exponential checkout traced through the code - addtobasket, updatebasket, the checkout button and account handler, basket verification, confirmorder with country and shipping, and checkout with currency check, payment, activation and the order mail - with each trigger point named.
keywords: checkout operations, addtobasket, updatebasket, confirmorder, checkout, operation_definition, pre_checkout, post_checkout, pre_confirmorder, handle-user-country, handle-shipping, activate-order, send-order-email, basket verify, removed items
type: help
---
# Checkout operations step by step

This page is for developers who need to know exactly what happens at each step of the checkout, for example to place
a workflow event correctly or to understand an order that looks wrong. It follows a customer from the product page to
the placed order and names the operation, the trigger points and the methods that run, from
`kernel/shop/operation_definition.php` and the shop views.

## Contents

1. [Step 1 Add to basket](#step-1-add-to-basket)
2. [Step 2 Update the basket](#step-2-update-the-basket)
3. [Step 3 The checkout button](#step-3-the-checkout-button)
4. [Step 4 Confirm the order](#step-4-confirm-the-order)
5. [Step 5 Check out and pay](#step-5-check-out-and-pay)
6. [Step 6 Back from the provider](#step-6-back-from-the-provider)
7. [The whole sequence](#the-whole-sequence)

## Step 1 Add to basket

Operation `addtobasket` (parameters `object_id`, `option_list`, `quantity`, `basket_id`):

1. Trigger `pre_addtobasket` (the before trigger of `shop_addtobasket`).
2. Method `add-to-basket`: the item and its options are stored in the basket's product collection.
3. Method `update-shipping-info`: the shipping handler, if one is configured, is told the basket changed.
4. Trigger `post_addtobasket`.

Then the customer is sent to the basket or back, as `site.ini [ShopSettings] RedirectAfterAddToBasket` says.

## Step 2 Update the basket

Operation `updatebasket` (parameters `item_count_list`, `item_id_list`): trigger `pre_updatebasket`, method
`update-basket`, trigger `post_updatebasket`. A quantity of zero or less, or one that is not a number, sends the
customer back to the basket with an error (`(error)/invaliditemcount`).

## Step 3 The checkout button

On **Checkout** in the basket (`kernel/private/classes/views/shop/basket.php`):

1. Posted quantities are saved.
2. The shop account handler is asked whether it has the customer's data (`verifyAccountInformation()`). If not, it
   sends the customer to its form (`shop/userregister`, `shop/register`) or to sign in
   ([shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md)). That form creates the order when it is
   submitted.
3. Prices are updated and the basket is **verified**: each item's product must still be published, and its price,
   VAT inclusion and VAT percentage must match what the basket holds. Items that fail are removed from the basket and
   listed on the basket page (`removed_items` in `shop/basket.tpl`).
4. A **temporary order** is created from the basket and its id is kept in the session
   ([Temporary orders and activation](temporary-orders-and-activation.md)).
5. The customer is sent to `shop/confirmorder`.

## Step 4 Confirm the order

Operation `confirmorder` (parameter `order_id`), run when the confirmation page is prepared:

1. Trigger `pre_confirmorder`: the trigger `before_shop_confirmorder` in `workflow.ini`. Shipping events run here
   ([Simple shipping event](simple-shipping-event.md)).
2. Method `handle-user-country`: the customer's country is settled for VAT.
3. Method `handle-shipping`: shipping information from a shipping handler is applied.
4. Method `fetch-order`.

There is no after trigger on `confirmorder`. The page shows the order with shipping and VAT. **Confirm** goes on to
`shop/checkout` (and empties the basket now if `ClearBasketOnCheckout=enabled`); **Cancel** returns to the basket with
its items. An order that is no longer temporary (Back button, a second tab) is not confirmed again: the customer is
sent to its receipt or order view ([Permanent order receipts](../../../features/6.0/order-receipts.md#a-checkout-fix-made-on-the-way)).

## Step 5 Check out and pay

`shop/checkout` runs operation `checkout` (parameter `order_id`):

1. Method `check-currency`.
2. Trigger `pre_checkout`: the before trigger of `shop_checkout`. The payment gateway event runs here
   ([Payment gateway event](payment-gateway-event.md)).
3. Method `activate-order`: the order stops being temporary and gets its order number
   ([Temporary orders and activation](temporary-orders-and-activation.md)).
4. Method `send-order-email`: the confirm order handler sends the order mail.
5. Trigger `post_checkout`: the after trigger of `shop_checkout`; events that need a placed order run here.

If an event on `pre_checkout` redirects (a redirect gateway) or shows a page (gateway selection, a card form), the
operation stops there and continues when the workflow is resumed. If it is cancelled, the customer sees
`shop/cancelcheckout.tpl`.

## Step 6 Back from the provider

When the customer returns to `shop/checkout` after a redirect gateway, the view waits up to 25 seconds, checking every
2 seconds, for the provider's callback to activate the order. If the order is activated, the customer is sent to the
order view or receipt. If not, `shop/checkoutagain.tpl` lets the customer try again, up to three attempts; after that
`shop/cancelcheckout.tpl` is shown with the error code `NO_CALLBACK`
(`kernel/private/classes/views/shop/checkout.php`). This is the most common symptom of a callback that cannot reach
the site ([Troubleshooting checkout](../operations/troubleshooting-checkout.md)).

## The whole sequence

```text
shop/add          addtobasket:  pre_addtobasket > add-to-basket > update-shipping-info > post_addtobasket
shop/basket       updatebasket: pre_updatebasket > update-basket > post_updatebasket
Checkout button   account handler > verify basket > temporary order > shop/confirmorder
shop/confirmorder confirmorder: pre_confirmorder > handle-user-country > handle-shipping > fetch-order
Confirm           shop/checkout
shop/checkout     checkout:     check-currency > pre_checkout > activate-order > send-order-email > post_checkout
```

## Related pages

- [Checkout and workflows](README.md)
- [Workflows on shop triggers](workflows-on-shop-triggers.md)
- [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
