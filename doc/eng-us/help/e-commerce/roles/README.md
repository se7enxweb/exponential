---
title: Paths by role
audience: ecommerce, customer, businesses
category: e-commerce
order: 10
summary: Who does what in an Exponential store - merchant, store manager, developer and agency - with the shop policy functions each role needs, the split between merchant work and developer work, and a path of pages for each.
keywords: roles, merchant, store manager, developer, agency, partner, shop policies, shop/administrate, shop/setup, shop/setstatus, shop/edit_status, shop/buy, responsibilities
type: help
---
# Paths by role

This page is for anyone who has to decide who on a store team does what. It splits the work of an Exponential store
into four roles, names the shop permissions each needs, and links to a reading path per role.

## Contents

1. [Four roles](#four-roles)
2. [Merchant work and developer work](#merchant-work-and-developer-work)
3. [The shop policy functions](#the-shop-policy-functions)
4. [Who decides, who does, who is asked](#who-decides-who-does-who-is-asked)
5. [The paths](#the-paths)

## Four roles

| Role | Typical person | Owns |
|---|---|---|
| Merchant | The owner, a director, a product manager | Decisions: what to sell, prices, VAT, payment provider, discounts, budget |
| Store manager | Shop staff, customer service, packing | Daily operations: orders, statuses, customers, returns |
| Developer | In-house developer or a partner's developer | Product classes, templates, settings, workflows, handlers, gateways |
| Agency | A partner that builds and maintains stores for clients | Scoping, the build, testing, handover, maintenance |

Small stores combine roles: the owner is often merchant and store manager at once. Keep the permissions apart anyway,
so a mistake in daily work cannot change VAT or prices.

## Merchant work and developer work

Some shop tasks are done in the administration by staff; others need a change in settings files or code. Knowing which
is which avoids waiting on the wrong person.

| Task | Done by | Where |
|---|---|---|
| Add or edit a product, its price and images | Merchant or editor | Content edit of the product |
| Create VAT types and VAT rules | Merchant | **Store > VAT types**, `shop/vatrules` |
| Add currencies, update exchange rates | Merchant | `shop/currencylist` |
| Create discount groups and rules | Merchant | `shop/discountgroup` |
| Change an order's status, archive orders | Store manager | `shop/orderview`, `shop/orderlist` |
| Add a custom order status | Merchant with `shop/edit_status` | `shop/status` |
| Switch on VAT rules (VAT handler) | Developer | `shop.ini [VATSettings] Handler` |
| Choose the customer data form (account handler) | Developer | `shopaccount.ini [AccountSettings] Handler` |
| Connect a payment gateway | Developer | `paymentgateways.ini`, a workflow on `shop_checkout` |
| Shipping rules beyond a flat amount | Developer | a shipping handler ([Custom shipping handler](../extending/custom-shipping-handler.md)) |
| Rename the basket address | Developer | `shop.ini [BasketSettings] BasketViewName` |
| Turn on permanent receipts | Developer | `shop.ini [OrderViewSettings] OrderLinkView` |

## The shop policy functions

The shop module has five policy functions (`kernel/shop/module.php`). Every shop view needs one of them, except the
receipt view `shop/orderreceipt`, which has no policy function because its signed token is the key
([Permanent order receipts](../../../features/6.0/order-receipts.md)).

| Function | Views | Give it to |
|---|---|---|
| `buy` | `add`, `basket`, `updatebasket`, `register`, `userregister`, `confirmorder`, `checkout`, `orderview`, `wishlist`, `preferredcurrency`, `setpreferredcurrency`, `setusercountry` | Visitors and customers (the anonymous role too, if guests may buy) |
| `administrate` | `dashboard`, `orderlist`, `archivelist`, `archiveorder`, `unarchiveorder`, `removeorder`, `customerlist`, `customerorderview`, `statistics`, `productsoverview` | Store managers and the merchant |
| `setup` | `vattype`, `vatrules`, `editvatrule`, `productcategories`, `discountgroup`, `discountgroupedit`, `discountruleedit`, `discountgroupview`, `currencylist`, `editcurrency` | The merchant, or whoever owns prices and tax |
| `edit_status` | `status` (the list of order statuses) | The merchant |
| `setstatus` | `setstatus`, limited by `FromStatus` and `ToStatus` | Store managers and packing staff, limited to their steps |

How the `FromStatus` and `ToStatus` limitations combine is explained in
[Status transitions and permissions](../orders/status-transitions-and-permissions.md). Roles are built as usual
([Roles and policies](../../../guides/roles-and-policies.md)).

> **Note**
> `administrate` covers removing orders (`shop/removeorder`). If store managers should not delete orders, keep that in
> mind when you grant it, and rely on archiving instead ([Archive, remove and cleanup](../orders/archive-remove-and-cleanup.md)).

## Who decides, who does, who is asked

| Decision | Merchant | Store manager | Developer | Agency |
|---|---|---|---|---|
| Payment provider | decides | is asked | implements | advises |
| VAT setup | decides, with a tax adviser | is told | switches on the handler | advises |
| Order statuses used | decides | is asked | adjusts templates if needed | advises |
| Shipping model | decides | is asked | writes or configures | advises |
| Go-live date | decides | is asked | confirms tests | confirms tests |

## The paths

- [Merchant path](merchant-path.md)
- [Store manager path](store-manager-path.md)
- [Developer path](developer-path.md)
- [Agency path](agency-path.md)

## Related pages

- [E-commerce: start here](../e-commerce-start-here.md)
- [Running your store](../../customers/commerce/README.md), which has a "who usually does what" table for a running store
- [Store planning for small businesses](../../businesses/stores/store-planning-for-small-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
