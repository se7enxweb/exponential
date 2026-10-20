---
title: Returns and refunds
audience: customer, admin, businesses
category: customer-success
order: 140
summary: Handling returns and refunds in an Exponential store - the return statuses, the steps from request to refund, who may refund, keeping money and statuses in line, and a returns log. Not legal advice on consumer rights.
keywords: returns, refunds, return requested, returned, refunded, partially refunded, cancellation, consumer rights, returns log, refund in provider account
type: help
---
# Returns and refunds

This page is for the store manager, customer service and the owner. It gives a routine for returns and refunds that
keeps the money, the goods and the order statuses in line. The status meanings are on
[Order statuses](order-statuses.md); the general routine for businesses is on
[Running a store day to day](../../businesses/stores/running-a-store-day-to-day.md).

> **Note**
> Not legal advice. Which returns and withdrawal rights your customers have depends on where you sell and to whom.
> Write your returns terms with your lawyer or trade association, and put them on the site before checkout.

## The statuses for after the end

Since 6.0.15 the shop ships four statuses for what happens after an order ended
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)):

| Status (id) | Set when | Effect on the dashboard |
|---|---|---|
| Return requested (14) | The customer asks to return | Not finished: the order is open again |
| Returned (15) | The goods are back with you | Not finished |
| Partially refunded (16) | Part of the money went back | Finished, still counted as revenue |
| Refunded (17) | All the money went back | Finished, no revenue |

A canceled order before shipping is simply **Cancelled** (13): finished, no revenue.

## The steps

1. **Request**: the customer asks. Check it against your returns terms. Set **Return requested**, and tell the
   customer how to send the goods.
2. **Arrival**: check the goods. Set **Returned**.
3. **Refund**: make the refund in your payment provider's account. A status change in Exponential does not move money.
4. **Record**: set **Refunded** or **Partially refunded**, and note the amount and reason in your returns log.
5. **Stock**: if you keep stock outside the shop, update it; the shop has no stock management.

> **Tip**
> Set the status when each step really happens. The dashboard counts Return requested and Returned as open orders,
> so returns waiting on your side show up in the store manager's list.

## Who may refund

Refunds move money, so keep them to few people:

| Step | Who |
|---|---|
| Set Return requested and Returned | Customer service (`shop/setstatus` limited with `ToStatus`, `kernel/shop/module.php`) |
| Make the refund in the provider's account | The owner, or one named person |
| Set Refunded and Partially refunded | Whoever made the refund |

Each status change is kept in the order's status history (`kernel/classes/ezorderstatushistory.php`).

If your store uses the `ezauthorize` extension, it has a refund module in the administration
([Card payments and PCI basics for businesses](../../businesses/stores/card-payments-and-pci-basics.md)); with
PayPal and other redirect providers, refunds are made in the provider's account.

## A returns log

The order status says where a return stands, not why. Keep a short log:

```text
RETURNS LOG
Order | Customer request date | Reason | Goods back on | Condition | Refund amount | Refunded on | By | Status set
------|-----------------------|--------|---------------|-----------|---------------|-------------|----|-----------
```

Do not copy customers' addresses or payment details into it; the order number is enough to find them.

## Monthly check

- [ ] Every Refunded or Partially refunded order has a matching refund in the provider's statement.
- [ ] No order stayed in Return requested or Returned longer than your terms allow.
- [ ] Reasons in the log reviewed: a product that comes back often needs a better description or a supplier talk.
- [ ] Revenue on the dashboard falls as expected for refunded orders (refunded orders count as no revenue).

## Related pages

- [Order statuses](order-statuses.md)
- [Orders day to day](orders-day-to-day.md)
- [Payment gateways](payment-gateways.md)
- [Orders and order statuses for businesses](../../businesses/stores/orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
