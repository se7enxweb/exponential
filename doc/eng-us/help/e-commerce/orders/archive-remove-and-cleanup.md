---
title: Archive, remove and cleanup
audience: ecommerce, customer, admin
category: e-commerce
order: 53
summary: The three ways orders leave the order list in Exponential and what each does to the data - archiving, removing a single order, and the shop cleanup that deletes every order - with their audit events, the basket and wishlist cleanup, and a safe retention practice.
keywords: archive order, unarchive, remove order, removeorder, cleanupOrder, purge orders, cleanup.php shop, basket cleanup, wishlist cleanup, retention, audit, commerce.order.delete, commerce.order.purge
type: help
---
# Archive, remove and cleanup

This page is for store managers, merchants and administrators who keep the order list manageable. Orders can leave
the list in three very different ways. Two of them destroy data that may be needed for accounting, customer questions
or data protection requests, so it pays to know exactly what each does.

## Contents

1. [Three ways, side by side](#three-ways-side-by-side)
2. [Archiving](#archiving)
3. [Removing an order](#removing-an-order)
4. [The shop cleanup script](#the-shop-cleanup-script)
5. [Baskets and wishlists](#baskets-and-wishlists)
6. [A retention practice](#a-retention-practice)

## Three ways, side by side

| | Archive | Remove one order | Shop cleanup |
|---|---|---|---|
| Where | Order list, **Archive** button (`shop/archiveorder`) | `shop/removeorder` after confirmation | `update/common/scripts/cleanup.php shop` |
| Policy | `shop/administrate` | `shop/administrate` | shell access |
| Order data | Kept; `is_archived` set to 1 | Deleted | Every order deleted |
| Reversible | Yes, `shop/unarchiveorder` | No | No |
| Statistics | Archived orders still count | Gone | Gone |
| Audit event | `commerce.order.archive` / `.unarchive` | `commerce.order.delete` | `commerce.order.purge` |

## Archiving

`eZOrder::archiveOrder()` only sets a flag. The order disappears from the order list and appears in
**Store > Archive** (`shop/archivelist`); its items, totals, status and history stay. Archive orders once they are
finished, so the order list shows only work to do. The customer's own order list and receipts keep working.

## Removing an order

`shop/removeorder` asks for confirmation and then calls `eZOrder::cleanupOrder()` for each selected order, which
deletes the order row, its product collection and product lines, and its status history
(`kernel/classes/ezorder.php`). The order list of the standard design no longer shows a remove button (the code keeps
the old `RemoveButton` for compatibility, `kernel/private/classes/views/shop/orderlist.php`), so removal is a
deliberate step.

Remove an order only when it must not exist: a test order on a live site, or data you are legally obliged to erase and
have no obligation to keep. For data protection requests, follow
[Data export and erasure](../../customers/commerce/data-export-and-erasure.md) and record the request in your log
([GDPR request log template](../../customers/commerce/gdpr-request-log-template.md)).

## The shop cleanup script

`update/common/scripts/cleanup.php` with the name `shop` removes **all** baskets, all wishlists and all orders
(`eZBasket::cleanup()`, `eZWishList::cleanup()`, `eZOrder::cleanup()`). It is meant for test and development
databases, for example before handing over a store whose test orders must go. Never run it on a live store. Take a
backup first even on a test copy ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).

## Baskets and wishlists

| Data | Removed by | When |
|---|---|---|
| Baskets of expired sessions | `basket_cleanup.php` cron script, part `infrequent` | As `site.ini [Session] BasketCleanup*` says ([site.ini shop settings walkthrough](../settings/site-ini-shop-settings-walkthrough.md#session-basket-cleanup)) |
| Wishlists | Only by the customer, or by the shop cleanup script | Never automatically |
| Temporary orders | Not removed by basket cleanup | Never shown as orders ([Temporary orders and activation](../checkout/temporary-orders-and-activation.md)) |

## A retention practice

1. Decide with your accountant how long order records must be kept, and with your data protection adviser how long
   customer data may be kept.
2. Archive finished orders weekly ([Store weekly checklist](../../customers/commerce/store-weekly-checklist.md)).
3. Once a year, review archived orders older than the retention period
   ([Operations calendar](../operations/operations-calendar.md)).
4. Before removing anything, make sure the figures you need are kept elsewhere: monthly statistics, the accounting
   system ([Store figures and exports](../operations/store-figures-and-exports.md)).
5. Remove in small, recorded batches, and check the audit trail afterwards.

## Related pages

- [Order operations](README.md)
- [Orders day to day](../../customers/commerce/orders-day-to-day.md)
- [GDPR requests](../../customers/commerce/gdpr-requests.md)
- [Commerce audit trail](../operations/commerce-audit-trail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
