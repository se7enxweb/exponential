---
title: Order statuses
audience: customer, admin, businesses
category: customer-success
order: 90
summary: Choosing and writing down your store's status path in Exponential - the seventeen shipped statuses in plain words, which ones your team uses, a status path worksheet, custom statuses, and reviewing the path once a year.
keywords: order status, status path, pending, paid, packed, shipped, completed, cancelled, refunded, custom status, shop/status, status worksheet
type: help
---
# Order statuses

This page is for the owner and the store manager. It helps you decide which order statuses your team uses, write
them down so everyone sets them the same way, and review them when your process changes. The full table of the
statuses, with what "finished", "no revenue" and "waits for customer" mean for the dashboard, is on
[Store dashboard and order statuses](../../../features/6.0/store-dashboard.md).

## The shipped statuses in plain words

Since 6.0.15 every new installation, and every installation after the 6.0.15 database update, has seventeen
statuses (same page):

| Status (id) | Means for your team |
|---|---|
| Pending (1) | Placed, nobody has looked at it yet |
| Processing (2) | Someone is working on it |
| Delivered (3) | Delivered; the order is finished |
| Awaiting payment (4) | Placed, the money has not arrived |
| Paid (5) | The money has arrived |
| Payment failed (6) | The payment did not go through; no revenue |
| On hold (7) | Stopped for a question or a check |
| Backordered (8) | Waiting for goods |
| Packed (9) | Ready to leave |
| Shipped (10) | Handed to the carrier |
| Ready for pickup (11) | Waiting at your counter for the customer |
| Completed (12) | Done; the order is finished |
| Cancelled (13) | Stopped for good; finished, no revenue |
| Return requested (14) | The customer wants to send it back |
| Returned (15) | The goods are back |
| Partially refunded (16) | Part of the money returned; finished |
| Refunded (17) | All money returned; finished, no revenue |

## Pick your path

Use only the statuses your process needs. Fewer statuses set reliably are worth more than many set at random.
Examples from [Orders and order statuses for businesses](../../businesses/stores/orders-and-order-statuses.md):

| Store type | Path |
|---|---|
| Services, nothing shipped | Pending, Paid, Completed |
| Small parcel shop | Pending, Paid, Packed, Shipped, Completed |
| Shop with pickup | Paid, Packed, Ready for pickup, Completed |
| Shop with returns | ... Shipped, Return requested, Returned, Refunded |

If you take payment online, check which status the payment step sets when the money arrives. For PayPal that is
`paypal.ini [OrderSettings] PaymentMadeOrderStatusID`, shipped as `1000`, a custom status number, not Paid (5); see
[PayPal](paypal.md).

## Status path worksheet

Copy, fill in, and pin it where orders are handled.

```text
STATUS PATH - [store name]          Version: ____   Date: ________

Status            | We set it when ...                    | Who sets it | Next status
------------------|---------------------------------------|-------------|------------
Pending           |                                       |             |
Paid              |                                       |             |
Packed            |                                       |             |
Shipped           |                                       |             |
Completed         |                                       |             |
Cancelled         |                                       |             |
Return requested  |                                       |             |
Refunded          |                                       |             |

Statuses we do NOT use: ______________________________________
Where tracking numbers are noted: ____________________________
Who may set Refunded: ________________________________________
```

## Custom statuses

Statuses with an id below 1000 are internal and cannot be removed by mistake; custom statuses start at 1000. They are
managed under **Store > Order status** (`shop/status`, policy `shop/edit_status`). A custom status is right when your
process has a step the shipped ones do not name, for example "Engraving" or "Awaiting customs".

> **Note**
> If your store created custom statuses with the numbers 4 to 17 before 6.0.15, check the status list after the
> update. The update adds each shipped status only when no status has that number yet
> ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Limiting who sets which status

`shop/setstatus` takes the limitations `FromStatus` and `ToStatus` (`kernel/shop/module.php`), so a role can be
allowed only the moves that match the person's job. The suggestions are on [Orders day to day](orders-day-to-day.md).

## Review once a year

- [ ] Every status on the worksheet is still used; the dashboard's status breakdown shows where orders pile up.
- [ ] No status is used for two different things.
- [ ] Roles still match the people who handle orders.
- [ ] The payment step's status is one on your path.
- [ ] New staff learned the path from the worksheet, not by guessing.

## Related pages

- [Orders day to day](orders-day-to-day.md)
- [Returns and refunds](returns-and-refunds.md)
- [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)
- [Orders and order statuses for businesses](../../businesses/stores/orders-and-order-statuses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
