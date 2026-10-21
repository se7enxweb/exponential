---
title: Status transitions and permissions
audience: ecommerce, customer, admin
category: e-commerce
order: 52
summary: How Exponential decides who may change an order's status - the shop/setstatus policy with its FromStatus and ToStatus limitations, how several policies combine, role designs for packing staff, customer service and managers, and how to test them.
keywords: setstatus, FromStatus, ToStatus, order status permissions, canModifyStatus, statusModificationList, limitation, role design, packing staff, customer service, shop/edit_status
type: help
---
# Status transitions and permissions

This page is for merchants and administrators who decide who may move orders from one status to another. The shop
has a policy function for exactly this, `shop/setstatus`, with two limitations. Used well, it turns your status
process into enforced steps.

## Contents

1. [The policy and its limitations](#the-policy-and-its-limitations)
2. [How the check works](#how-the-check-works)
3. [Role designs](#role-designs)
4. [Editing the status list itself](#editing-the-status-list-itself)
5. [Testing the roles](#testing-the-roles)

## The policy and its limitations

`kernel/shop/module.php` defines `setstatus` with two limitations, both lists of order statuses:

| Limitation | Meaning |
|---|---|
| `FromStatus` | The order's current status must be in this list |
| `ToStatus` | The new status must be in this list |

Without limitations, `shop/setstatus` allows any change. The status choices offered on the order view are the
statuses the current user may set for that order (`eZOrder::statusModificationList()`).

## How the check works

`eZOrder::canModifyStatus()` (`kernel/classes/ezorder.php`) reads the user's access to `shop/setstatus`:

- Access "yes" (a policy without limitations): every change is allowed.
- Access "limited": each policy is checked on its own. A policy allows the change when the current status is in its
  `FromStatus` (if set) and the new status is in its `ToStatus` (if set). If **any one** policy allows it, the change
  is allowed.

So limitations combine within one policy (both must hold) and policies combine with "or". To give someone two separate
steps, give them two policies, not one policy with both lists merged: merging would also allow the cross-combinations.

## Role designs

Example with a parcel process (Pending, Paid, Packed, Shipped, Completed, On hold, Cancelled, Refunded):

| Role | `shop/setstatus` policies | Effect |
|---|---|---|
| Packing | 1: From Paid, To Packed or On hold. 2: From Packed or On hold, To Shipped or Packed | Can only move paid orders through packing and shipping |
| Customer service | 1: From Pending or Awaiting payment, To Cancelled. 2: From Shipped or Delivered, To Return requested | Can cancel unpaid orders and open returns |
| Store manager | No limitation | Any change, including refunds |
| Owner | No limitation, plus `shop/edit_status` and `shop/setup` | Also edits the status list, VAT and prices |

All shop staff roles also need `shop/administrate` to see the order list and order view in the Store tab
([Paths by role](../roles/README.md#the-shop-policy-functions)). Build the roles in **Setup > Roles and policies**
([Roles and policies](../../../guides/roles-and-policies.md)).

## Editing the status list itself

`shop/status` (policy `shop/edit_status`) edits the list of statuses: add custom statuses (ids from 1000), rename them,
and set whether they are active. Keep this with the owner: a renamed status changes what every role's limitations
mean.

## Testing the roles

- [ ] Sign in as a test user of each role, not as an administrator.
- [ ] Open an order in each status and check the offered choices against the table you designed.
- [ ] Try a change that should be refused; it must not be offered.
- [ ] Check the order's status history shows the test user and time.
- [ ] With the audit trail on, check `commerce.order.status` records the changes ([Commerce audit trail](../operations/commerce-audit-trail.md)).

## Related pages

- [Order operations](README.md)
- [Order lifecycle operations](order-lifecycle-operations.md)
- [Orders day to day](../../customers/commerce/orders-day-to-day.md)
- [Policy functions and limitations](../../creators/app-creators/policy-functions-and-limitations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
