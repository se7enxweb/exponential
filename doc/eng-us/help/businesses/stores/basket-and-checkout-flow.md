---
title: Basket and checkout flow
audience: businesses, project-manager, website-creator
category: business-solutions
order: 80
summary: The customer's path through an Exponential store step by step - add to basket, basket, customer data, confirmation, payment, placed order - with the business choices at each step.
keywords: basket, cart, checkout, add to basket, confirm order, checkout flow, abandoned basket, basket cleanup, conversion, customer journey
type: help
---
# Basket and checkout flow

This page is for business owners and project managers who want to understand, and test, the path a customer takes from
a product page to a placed order. It describes each step as the customer sees it, which choices the business makes
there, and what can go wrong. The technical description is
[Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md).

## Contents

1. [The path at a glance](#the-path-at-a-glance)
2. [Step 1 Add to basket](#step-1-add-to-basket)
3. [Step 2 The basket](#step-2-the-basket)
4. [Step 3 Customer data](#step-3-customer-data)
5. [Step 4 Confirmation](#step-4-confirmation)
6. [Step 5 Payment](#step-5-payment)
7. [Step 6 The placed order](#step-6-the-placed-order)
8. [Abandoned baskets](#abandoned-baskets)
9. [Test the path](#test-the-path)

## The path at a glance

| Step | Address | Business choice |
|---|---|---|
| 1. Add to basket | `content/action` from the product page | Stay on the page or jump to the basket |
| 2. Basket | `shop/basket` | The address name of the basket |
| 3. Customer data | `shop/register` or `shop/userregister` | Account or guest ([Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)) |
| 4. Confirmation | `shop/confirmorder` | Shipping and VAT shown here |
| 5. Payment | `shop/checkout` and the provider | The payment gateway |
| 6. Placed order | `shop/orderview` or `shop/orderreceipt/<token>` | Classic order view or permanent receipt |

Sources: [Running a shop: overview](../../creators/website-creators/shop-overview.md#the-visitors-path) and
[Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#checkout).

## Step 1 Add to basket

The product page has an **Add to basket** button. What happens next is a setting,
`site.ini [ShopSettings] RedirectAfterAddToBasket`:

- `basket` (shipped): the customer is taken to the basket to see the new item;
- `reload`: the customer goes back to where they were and can keep shopping.

Stores where customers usually buy one item often keep `basket`; stores where customers fill a basket with many items
often prefer `reload` with a basket count in the page header (the simple theme shows one, per the technical page).

## Step 2 The basket

The basket lists items, quantities and totals; customers change quantities or remove items. The basket's address name
is a setting (`shop.ini [BasketSettings] BasketViewName`, shipped `basket`), so a store can call it `cart` or a word in
its own language ([Shop basket view name](../../../bc/6.0/shop-basket-view-name.md)).

Two more settings in `site.ini [ShopSettings]` decide when a basket is emptied:

| Setting | Shipped | Meaning |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | The basket is kept until payment has finished, so a customer who cancels payment can go back with the basket intact |
| `ClearBasketOnLogout` | `disabled` | Logging out does not empty the basket |

The comments in `settings/site.ini` explain that some payment systems need `ClearBasketOnCheckout=enabled`; check your
gateway's documentation.

## Step 3 Customer data

The customer gives their name, e-mail and address, or signs in. Which form appears is decided by the shop account
handler in `shopaccount.ini` ([Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)). The
country select puts the most used countries first ([Country groups and regions](country-groups-and-regions.md)).

## Step 4 Confirmation

The confirmation page shows the order with shipping and VAT. Shipping cost is added here by a workflow on the
`before_shop_confirmorder` trigger ([Shipping and handling](shipping-and-handling.md)). Since 6.0.15, **Cancel** on
this step brings the customer back to their basket with its items, instead of an empty basket, and the Back button or
a second tab no longer runs the confirmation twice
([Permanent order receipts](../../../features/6.0/order-receipts.md#a-checkout-fix-made-on-the-way)).

## Step 5 Payment

On checkout, the payment gateway runs as a workflow on the `shop_checkout` trigger. A redirect gateway such as PayPal
sends the customer to the provider; a form gateway asks for card details on your site
([Payment gateways for businesses](payment-gateways-for-businesses.md)).

## Step 6 The placed order

The order is placed and the confirmation mail goes out to the customer and to the shop
([Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md)). The customer sees the order, or a
permanent receipt they can print, download and bookmark.

## Abandoned baskets

Not every basket becomes an order. The Store dashboard counts baskets in progress and abandoned, and lists the latest
baskets and unconfirmed checkouts ([Store dashboard and reports](store-dashboard-and-reports.md)).

Old baskets are removed by the `basket_cleanup` cron part (`settings/cronjob.ini`, part `infrequent`), as
`site.ini [Session] BasketCleanup=cronjob` says. The dashboard's "Next steps" warns when basket cleanup would delete
baskets kept in PHP sessions ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)). Make sure
the cron jobs run ([Cron jobs](../../../guides/cronjobs.md)).

> **Tip**
> A high number of abandoned baskets at the confirmation step often means a surprise: shipping cost or VAT the customer
> did not expect. Show shipping terms early.

## Test the path

- [ ] Add a product from its page; check where the customer lands.
- [ ] Change a quantity and remove an item in the basket.
- [ ] Check out as a guest and as a signed-in customer.
- [ ] Cancel on the confirmation page; the basket is still there.
- [ ] Cancel at the payment provider; the customer can try again.
- [ ] Complete a payment in test mode; the order and the mail arrive.
- [ ] Repeat on a phone.

The full test list is in [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#test-order-checklist).

## Related pages

- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)
- [Store launch checklist](store-launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
