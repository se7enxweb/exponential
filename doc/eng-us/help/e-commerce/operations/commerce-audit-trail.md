---
title: Commerce audit trail
audience: ecommerce, admin, businesses
category: e-commerce
order: 79
summary: What the Exponential audit trail records about a store - the commerce events for orders, payments, VAT, currencies and discounts, what is never recorded, who may read the commerce channel, retention, verification and exports, and how to answer common questions with it.
keywords: audit trail, commerce channel, commerce.order.create, commerce.order.status, commerce.payment.approve, commerce.vat.change, commerce.currency.change, commerce.discount.change, commerce.order.delete, commerce.order.purge, audit/read, Channel limitation, retention, exp:audit verify
type: help
---
# Commerce audit trail

This page is for merchants, administrators and auditors who need to know who changed what in a store. Exponential
6.0.15 records security-relevant events in a tamper-evident log, on by default; the shop writes its events to the
`commerce` channel ([The audit trail](../../../features/6.0/audit-trail.md)).

## Contents

1. [The commerce events](#the-commerce-events)
2. [What is never recorded](#what-is-never-recorded)
3. [Who may read it](#who-may-read-it)
4. [Answering questions](#answering-questions)
5. [Retention and proof](#retention-and-proof)

## The commerce events

Found in the shop's code (the `expAuditHook::emit()` calls):

| Event | When |
|---|---|
| `commerce.basket.checkout` | A checkout starts: basket, order, item count and total |
| `commerce.order.create` | An order is activated: order number, total, currency |
| `commerce.order.status` | An order's status changes |
| `commerce.order.archive`, `commerce.order.unarchive` | An order is archived or brought back |
| `commerce.order.item.remove` | An item is removed from an order |
| `commerce.order.delete` | One order is removed (`shop/removeorder`) |
| `commerce.order.purge` | All orders are removed (the shop cleanup script) |
| `commerce.payment.approve` | A payment object is approved by a gateway's callback |
| `commerce.vat.change` | VAT types or rules change |
| `commerce.currency.change` | Currencies or rates change |
| `commerce.discount.change` | Discount groups or rules change |

CSV and package imports and exports (`data.*`) go to the same channel. Changes to a product's price are content
changes: they are in the `content` channel as publishing events, and the product's version history shows the old and
new values ([Content history](../../../guides/content-history.md)).

## What is never recorded

Card data, customer addresses on order events, passwords and secrets. Order events carry ids, numbers, totals,
currency and status, which is what an auditor needs and nothing that a leak of the log would expose
([Audit guide](../../../bc/6.0/audit.md)).

## Who may read it

The **Audit** tab needs `audit/read`. Its `Channel` limitation lets you give a shop auditor the `commerce` channel
only, without access to sign-ins or content. Exports need `audit/export`.

## Answering questions

| Question | Filter |
|---|---|
| Who changed the VAT rules last month? | `commerce.vat.change`, the month |
| Who moved order 1042 to Refunded? | `commerce.order.status`, object order 1042 |
| Was the payment of an order confirmed by the provider? | `commerce.payment.approve` for the order |
| Did anyone delete orders? | `commerce.order.delete`, `commerce.order.purge` |
| How many checkouts started but were not placed? | `commerce.basket.checkout` against `commerce.order.create` |

The same questions from a shell: `./console exp:audit tail --channel=commerce`.

## Retention and proof

The shipped retention for the `commerce` channel is 90 days live and 730 days as archives (`[AuditChannel_commerce]`
`LiveDays`, `ArchiveDays`). If you must keep proof longer, raise `ArchiveDays` or export and store the records.
`./console exp:audit verify --channel=commerce` checks that the chain of records is intact. The `frequent` cron part
must run for indexing, rotation and archives.

## Related pages

- [Operating a store](README.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Store security checklist](../../businesses/stores/store-security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
