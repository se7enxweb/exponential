---
title: Daily store routine
audience: customer, admin, businesses
category: customer-success
order: 10
summary: A fifteen-minute morning routine for the person who runs an Exponential store - the dashboard, the orders waiting, failed payments, the order mailbox and the handover at the end of the day.
keywords: daily routine, store routine, morning check, store dashboard, open orders, payment failed, order mailbox, store manager, shop routine
type: help
---
# Daily store routine

This page is for the store manager, or the owner who is also the store manager. It gives a short routine for every
working day, in the order that catches problems earliest, and says who on the team does each step. It assumes the
store is live; the full picture of daily, weekly and yearly tasks for businesses is in
[Running a store day to day](../../businesses/stores/running-a-store-day-to-day.md), and this page is the version you
can pin to the wall.

## Before you start

You need an account of your own with the `shop/administrate` policy, which opens the Store tab, the dashboard, the
order list and the customer list (`kernel/shop/module.php`). Sign in with two-step sign-in if your site has it
([Two-factor login](../../../guides/two-factor-login.md)). Never share one shop account between several people: the
order status history and the audit trail then cannot tell who did what.

## The routine

| Step | Where | What to look for | Usually done by |
|---|---|---|---|
| 1 | **Store** (the dashboard, `shop/dashboard`) | Open orders, and how many wait too long | Store manager |
| 2 | Dashboard, **Work to do** | Orders waiting for the shop, longest first | Store manager |
| 3 | Order list, sorted by status | Orders in **Payment failed** or **Awaiting payment** | Store manager |
| 4 | Dashboard, **Next steps** | Anything new since yesterday | Store manager, tells the owner |
| 5 | The order mailbox | The shop's copy of each order mail, customer replies | Store manager or customer service |
| 6 | The payment provider's account | Payments that have no order, refunds waiting | Owner or whoever may refund |
| 7 | Status changes | Every order moved to the status that matches the real work | Whoever did the work |

What the dashboard shows is described in [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md):
orders today and in 7 and 30 days, open orders, orders waiting too long, abandoned baskets, best sellers and the
"Next steps" list worked out from your data and settings.

## Step by step

### 1. Open the dashboard

**Store** opens the dashboard. Read the "At a glance" row first. A sudden drop in orders today, compared with your
usual day, is often the first sign that something on the site or at the payment provider is broken.

### 2. Work through the orders waiting

The "Work to do" list puts the orders that wait longest first. Open each one, check the items, options, address and
any comment the customer left, and move it on. Orders waiting for the customer (Pending, Awaiting payment, Payment
failed, Ready for pickup) are highlighted after a shorter limit than the others
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

### 3. Look at failed payments

Sort the order list by status ([Order list sorting](../../../features/6.0/order-list-sorting.md)). One failed
payment is usually a customer's card. Several in one morning point at the payment step or the provider; see
[Payment gateways](payment-gateways.md).

### 4. Read the "Next steps"

The list is the store's own checklist: pending orders older than two days, no payment step on checkout, a 0 percent
VAT type in use, basket cleanup settings that would delete baskets kept in sessions. An empty list is a good sign.
Anything that needs a setting changed goes to your host or partner.

### 5. Check the order mailbox

The shop sends a copy of every order mail to the address in `site.ini [MailSettings] AdminEmail`
(`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`). If the copies stop arriving, the
customers' mails have probably stopped too; see [Order confirmation mail](order-confirmation-mail.md).

### 6. Compare with the payment provider

Once a day, compare the payments in your provider's account with the paid orders. A payment without an order, or an
order marked paid without a payment, needs a look the same day.

### 7. Set statuses as the work is done

Move each order the moment the work happens: Packed when the parcel is ready, Shipped when it leaves. The dashboard's
waiting times are only as true as the statuses. The meaning of each status is on [Order statuses](order-statuses.md).

## At the end of the day

- [ ] No order older than your promised handling time is still in a "new" status.
- [ ] Every Shipped order has its tracking number noted where your team agreed (the shop has no field for it by default).
- [ ] Customer questions answered or handed over.
- [ ] Anything unusual written down for tomorrow, or for whoever covers.

## When the usual person is away

Whoever covers needs their own account, the status path of your store on one page, and the contacts of the host and
the payment provider. A handover list is on
[Running a store day to day](../../businesses/stores/running-a-store-day-to-day.md).

> **Tip**
> Print the dashboard's "How your shop works" section once and keep it with this routine. It describes the checkout
> exactly as your installation is configured, which is what a stand-in needs.

## Related pages

- [Orders day to day](orders-day-to-day.md)
- [Store weekly checklist](store-weekly-checklist.md)
- [Store reports](store-reports.md)
- [Running a store day to day (for businesses)](../../businesses/stores/running-a-store-day-to-day.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
