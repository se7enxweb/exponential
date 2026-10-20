---
title: Basket, checkout and orders
audience: website-creator, admin
category: website-creation
order: 540
summary: The shop's visitor path in detail - add to basket forms, the basket, customer registration, confirmation, the checkout workflow triggers, order statuses, receipts, order mails and the order administration.
keywords: basket, add to basket, checkout, confirmorder, shop/register, order, order status, order receipt, orderreceipt, order mail, workflow trigger, shop_checkout, website creator
type: help
---
# Basket, checkout and orders

This page is for website creators who build the buying path of a shop, from the add-to-basket button to the order the
shop staff process.

## Contents

1. [Add to basket](#add-to-basket)
2. [The basket](#the-basket)
3. [Checkout](#checkout)
4. [Workflow triggers](#workflow-triggers)
5. [Orders](#orders)
6. [Test order checklist](#test-order-checklist)

## Add to basket

The product's full template posts to `content/action` with the button `ActionAddToBasket` (the form of the ezwebin and
simple themes' product templates):

```text
<form method="post" action={'content/action'|ezurl}>
  <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
  <input type="hidden" name="ContentObjectID" value="{$node.object.id}">
  <input type="hidden" name="ViewMode" value="full">
  <button type="submit" name="ActionAddToBasket">{'Add to basket'|i18n( 'design/mysite' )}</button>
</form>
```

Options are posted along with it when the product has option fields.

## The basket

`shop/basket` (renamable with `[BasketSettings] BasketViewName`, see [Shop basket view name](../../../bc/6.0/shop-basket-view-name.md))
lists the items; `shop/updatebasket` changes quantities. The pagelayout can show a basket count from
`fetch( shop, basket )`, as the simple theme does. Old baskets are removed by the `basket_cleanup` cron part.

## Checkout

| Step | View | Template |
|---|---|---|
| Customer data | `shop/register` (or `shop/userregister`) | `shop/register.tpl`, `shop/userregister.tpl` |
| Confirmation with shipping and VAT | `shop/confirmorder` | `shop/confirmorder.tpl` |
| Checkout and payment | `shop/checkout` | the gateway's pages |
| The order | `shop/orderview` or `shop/orderreceipt/<token>` | `shop/orderview.tpl` |

How customer data is handled is decided by the shop account handler (`shopaccount.ini`).

## Workflow triggers

`settings/workflow.ini` offers shop operations as triggers: `shop_addtobasket`, `shop_updatebasket`,
`before_shop_confirmorder` and `shop_checkout`. A payment gateway event goes on `shop_checkout` (before); shipping
cost on `before_shop_confirmorder` ([Workflows](../../../guides/workflows.md)).

## Orders

- **Store** opens the dashboard; the order list (`shop/orderlist`) and order view (`shop/orderview`) are one click
  away.
- Each order has a status; the release with the dashboard added a status for every stage
  ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).
- Orders can be archived (`shop/archiveorder`) and removed.
- **Permanent receipts**: with `[OrderViewSettings] OrderLinkView=orderreceipt` in `shop.ini`, the confirmation
  page and mail link to `shop/orderreceipt/<token>` instead of `shop/orderview`
  ([Permanent order receipts](../../../features/6.0/order-receipts.md)).
- Customers: `shop/customerlist`; statistics: `shop/statistics`.

## Test order checklist

- [ ] Add to basket from a product page, change the quantity, remove an item.
- [ ] Check out as a visitor and as a logged-in customer.
- [ ] VAT and shipping on the confirmation page are right for two countries.
- [ ] Payment in the gateway's test mode succeeds and fails as expected.
- [ ] The order appears in Store with the right status; the mail arrives.
- [ ] The receipt link works for the customer and not for others.

## Related pages

- [Running a shop: overview](shop-overview.md)
- [Payment gateways](payment-gateways.md)
- [Order list sorting](../../../features/6.0/order-list-sorting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
