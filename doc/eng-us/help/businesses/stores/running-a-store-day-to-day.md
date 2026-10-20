---
title: Running a store day to day
audience: businesses, admin, editor
category: business-solutions
order: 230
summary: Daily, weekly, monthly and yearly routines for running an Exponential store - handling orders through their statuses, the dashboard's to-do lists, customer questions, catalog care, checks on payments, mail, backups and updates.
keywords: store operations, daily routine, order handling, order processing, weekly review, monthly tasks, catalog maintenance, refunds, returns, store management
type: help
---
# Running a store day to day

This page is for shop managers and owners after launch. It gives routines for every day, week, month and year, so
nothing is forgotten when the store gets busy or when someone is on holiday. Adapt the lists to your store and keep
them where the team can see them.

![The Store dashboard](../../images/help/bu-store.webp)

## Contents

1. [Every day](#every-day)
2. [Handling an order](#handling-an-order)
3. [Returns and refunds](#returns-and-refunds)
4. [Customer questions](#customer-questions)
5. [Every week](#every-week)
6. [Every month](#every-month)
7. [Every year](#every-year)
8. [When the usual person is away](#when-the-usual-person-is-away)

## Every day

Open **Store** in the administration; the dashboard is the first page
([Store dashboard and reports](store-dashboard-and-reports.md)).

- [ ] **Work to do**: handle orders waiting for the shop, longest first.
- [ ] Orders highlighted as waiting too long: act or contact the customer.
- [ ] **Payment failed** orders: check whether the customer tried again.
- [ ] **Next steps**: read anything new.
- [ ] The order mail inbox (the address in `AdminEmail`): nothing unexpected.

## Handling an order

Using the shipped statuses ([Orders and order statuses](orders-and-order-statuses.md)), a typical parcel order goes:

1. **Pending** or **Paid**, depending on the payment step. With PayPal, the status set on payment is the one in
   `PaymentMadeOrderStatusID` ([PayPal payments](paypal-payments.md#order-status-after-payment)).
2. Check the order: items, options, address, any comment the customer left.
3. **Packed** when the parcel is ready; **Backordered** or **On hold** if something is missing.
4. **Shipped** when it leaves; note the carrier's tracking number for customer questions (the shop has no field for
   it by default, so agree on where it goes).
5. **Completed** when it is delivered, or **Ready for pickup** then **Completed** for pickup orders.

Use the order list's column sorting to work through orders by date, status or value
([Order list sorting](../../../features/6.0/order-list-sorting.md)).

> **Tip**
> Change the status at the moment the work is done, not at the end of the day. The dashboard's open orders and waiting
> times are only as accurate as the statuses.

## Returns and refunds

1. The customer asks to return: set **Return requested**.
2. The goods arrive: set **Returned**.
3. Make the refund in your payment provider's account (a status change does not move money).
4. Set **Refunded** or **Partially refunded**.

Refunded orders leave the revenue figures; partially refunded orders count as finished
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md#order-statuses-for-the-whole-lifecycle)).
With ezauthorize, a refund module exists in the administration ([Card payments and PCI basics](card-payments-and-pci-basics.md)).

## Customer questions

| Question | Where to look |
|---|---|
| "Where is my order?" | Order view: status and status history |
| "I lost my confirmation" | Send the receipt link again, if permanent receipts are on ([Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md)) |
| "I was charged but have no order" | The provider's account, then the order list for Pending or Payment failed |
| "Please change my address" | Check the address in the order view, and agree in your team how a change is handled before the order is Shipped |
| "Stop sending me offers" | The unsubscribe link or the customer's e-mail preferences ([Mail preferences](../../../features/6.0/mail-preferences.md)) |

## Every week

- [ ] The fifteen-minute review on [Store dashboard and reports](store-dashboard-and-reports.md#a-weekly-review).
- [ ] Archive completed orders older than your returns period (`shop/archiveorder`).
- [ ] Update products: new items, sold-out items hidden, prices checked.
- [ ] Read the logs or ask the host for a summary ([Log viewer](../../../features/6.0/log-viewer.md)).

## Every month

- [ ] Month-end figures from **Store > Statistics** for your bookkeeping.
- [ ] Search terms without results ([Site search](../../creators/website-creators/site-search.md)).
- [ ] One newsletter edition, to subscribers who opted in ([Newsletters](../newsletters/README.md)).
- [ ] Check that backups ran and that cron jobs (including basket cleanup) run ([Cron jobs](../../../guides/cronjobs.md)).
- [ ] Ask the host or partner about Exponential and extension updates ([Security updates and disclosure](../security/security-updates-and-disclosure.md)).
- [ ] The [Monthly operations checklist](../templates/monthly-operations-checklist.md) template.

## Every year

- [ ] VAT rates and rules still right; ask your tax adviser about changes.
- [ ] Ranked countries match where orders come from ([Country groups and regions](country-groups-and-regions.md)).
- [ ] Roles and staff accounts reviewed ([Access reviews](../security/access-reviews.md)).
- [ ] Old orders and customer data handled per your retention rules ([Data retention and deletion](../security/data-retention-and-deletion.md)).
- [ ] A restore drill ([Restore drills](../reliability/restore-drills.md)).
- [ ] Payment provider contract and fees compared with alternatives.
- [ ] The [Annual review checklist](../templates/annual-review-checklist.md).

## When the usual person is away

Write a one-page handover for whoever covers:

- how to sign in (their own account, with two-step sign-in, never a shared one);
- the status path, with the meaning of each status for your store;
- where the carrier's portal and the payment provider's account are, and who may refund;
- who to call at the host if the site is down ([Site down](../../hosting/runbooks/site-down.md));
- the Store dashboard's "How your shop works" section, printed.

## Related pages

- [Orders and order statuses](orders-and-order-statuses.md)
- [Maintaining your website](../../creators/website-creators/maintaining-your-website.md)
- [Operating a site](../../../guides/operating-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
