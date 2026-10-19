---
title: Customer accounts
audience: customer, admin, businesses
category: customer-success
order: 170
summary: Looking after the customers of an Exponential store - guest and account checkout as configured, the customer list, account questions from customers, tidying old accounts, and who on the team may see customer data.
keywords: customer accounts, guest checkout, customer list, shop account handler, shopaccount.ini, registration, password reset, user accounts, customer data, account hygiene
type: help
---
# Customer accounts

This page is for the store manager and the owner. It covers the running store: knowing how your checkout treats
guests and account holders, answering account questions, and keeping the customer data tidy. The choice between guest
and account checkout is explained on
[Customer accounts and guest checkout for businesses](../../businesses/stores/customer-accounts-and-guest-checkout.md).

## Know your setup

How the shop collects customer data is decided by the shop account handler, `settings/shopaccount.ini
[AccountSettings] Handler`, shipped as `ezuser` (same page):

| Handler | What your customers experience |
|---|---|
| `ezuser` (shipped) | Everyone fills a form at checkout; guests buy without an account; signed-in customers find their details filled in |
| `ezdefault` | Customers must sign in; visitors are sent to the login page and back to the basket |
| `ezsimple` | Customers are sent to `shop/register` for their data |
| `bcusauser` | The `ezuser` form with a US address model |

Ask your partner which one your store uses and write it into your store notes. The Store dashboard's "How your shop
works" section also names it ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## The customer list

**Store > Customers** (`shop/customerlist`) shows who bought, and the customer order view
(`shop/customerorderview`) lists one customer's orders, found by customer and e-mail address
(`kernel/shop/module.php`). Both need `shop/administrate`. The dashboard counts new, returning and all-time customers.

> **Warning**
> The customer list and order views show names, addresses and order histories. Give `shop/administrate` only to
> people who need it, each with their own account ([Store security checklist](../../businesses/stores/store-security-checklist.md)).

## Account questions from customers

| Question | Who answers | How |
|---|---|---|
| "I forgot my password" | The customer | The site's own password reset ([Accounts and sign-in](../../accounts-and-sign-in.md)) |
| "I never got the activation mail" | Store manager or administrator | Check the account in **Users**; see [Accounts and sign-in](../../accounts-and-sign-in.md) |
| "Change my e-mail address" | The customer in their profile, or an administrator | Account editing |
| "Stop sending me offers" | The customer, by the link in the mail or the preferences page | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| "Delete my account and data" | Owner decides, administrator acts | [GDPR requests](gdpr-requests.md) |
| "Can I buy without an account?" | Store manager | Yes with `ezuser`; not with `ezdefault` |

## Tidying accounts

**Users > Check user accounts** (`user/hygiene`) lists accounts nobody uses, accounts that are disabled or locked,
accounts that lost their user object, and accounts that share an e-mail address
([Check user accounts](../../../features/6.0/user-hygiene.md)).

- [ ] Quarterly: look at the list with the owner and decide what to do with long-unused customer accounts, in line
  with your retention rules ([Data export and erasure](data-export-and-erasure.md)).
- [ ] Staff accounts of people who left are disabled the same day.
- [ ] Removing a user is final: user objects do not go to the trash (same page). Check open orders first.

## What accounts give your customers

From [Customer accounts and guest checkout for businesses](../../businesses/stores/customer-accounts-and-guest-checkout.md):
signed-in customers get their address filled in, a wishlist ([Wishlists and baskets](wishlists-and-baskets.md)),
discount groups ([Discounts](discounts.md)), and their receipts without the link. Guests get none of these, but
can keep their receipt link if permanent receipts are on ([Invoices and receipts](invoices-and-receipts.md)).

## Related pages

- [Customer accounts and guest checkout for businesses](../../businesses/stores/customer-accounts-and-guest-checkout.md)
- [Accounts and sign-in](../../accounts-and-sign-in.md)
- [GDPR requests](gdpr-requests.md)
- [Check user accounts](../../../features/6.0/user-hygiene.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
