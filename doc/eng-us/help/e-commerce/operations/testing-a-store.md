---
title: Testing a store
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 71
summary: A complete test plan for an Exponential store - test environment, test data and test mail, the catalog, basket, checkout, payment, VAT, shipping, orders, permissions, receipts and cleanup - with what to automate and what to repeat after every update.
keywords: testing a store, test plan, test order, test mode, sandbox, test data, file mail transport, regression test, acceptance, VAT test, payment test, permissions test, cleanup
type: help
---
# Testing a store

This page is for developers, agencies and merchants who must be sure a store works before customers find out it does
not. It gives a test plan in the order a customer meets the store, plus the setup that keeps tests safe. The short
launch list for owners is [Store launch checklist](../../businesses/stores/store-launch-checklist.md); this page is
the full plan behind it.

## Contents

1. [A safe test environment](#a-safe-test-environment)
2. [Test data](#test-data)
3. [The plan](#the-plan)
4. [What to automate](#what-to-automate)
5. [After every update](#after-every-update)
6. [Cleaning up](#cleaning-up)

## A safe test environment

- Test on a copy of the site, not on the live store, whenever you can
  ([Develop with Git](../../develop-with-git.md), [Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- Put every payment provider in its test mode: `paypal.ini [ServerSettings]` pointing at the sandbox, or
  `ezauthorize.ini TestMode=true` ([paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md)).
- Stop real mail: `site.ini [MailSettings] Transport=file` writes mails to files instead of sending them. Check which
  value is in effect, because an override file may set another transport
  ([Shop settings walkthroughs](../settings/README.md#checking-the-value-in-effect)).
- Use test customer accounts with addresses on reserved domains such as `example.invalid`, never real customers'
  addresses or data.
- The provider's callback must reach the test site; a test site behind a login or VPN cannot receive it.

## Test data

| Item | Prepare |
|---|---|
| Products | One per class, one with options, one without a price, one unpublished |
| VAT | One product per product category; customers for each country with a rule and one without |
| Currencies | One order per currency you sell in |
| Customers | A guest, a registered customer, a member of each discount group, a staff user per role |

## The plan

**Catalog**

- [ ] Every product class shows a price and the add-to-basket form.
- [ ] **Store > Products overview** lists all products of each class with prices.
- [ ] Content without a price field cannot be added to the basket (the kernel cancels the add), and products with an
  empty price are caught by the dashboard's "products without a price" figure.

**Basket**

- [ ] Add, change quantity, remove; zero and invalid quantities give an error.
- [ ] `RedirectAfterAddToBasket` behaves as decided.
- [ ] Change a product's price while it is in a basket: on checkout the item is removed and listed as removed
  ([Checkout operations step by step](../checkout/checkout-operations-step-by-step.md#step-3-the-checkout-button)).

**Customer data and confirmation**

- [ ] Guest and signed-in checkout reach the confirmation page.
- [ ] Shipping and VAT are right for each test country and product category.
- [ ] Discounts apply to the right groups only ([Discounts](../../customers/commerce/discounts.md)).
- [ ] Cancel returns to a full basket; reloading the page does not add shipping twice.

**Payment**

- [ ] Successful payment: order placed with a number, status as configured, mail in the test mailbox.
- [ ] Cancelled payment: no placed order, the basket is back, retry works.
- [ ] Refused card or failed payment: a clear message.
- [ ] Gateway selection, when several gateways are selected.

**Orders and staff**

- [ ] Each staff role sees exactly the screens and status changes it should
  ([Status transitions and permissions](../orders/status-transitions-and-permissions.md#testing-the-roles)).
- [ ] Archive and unarchive; the dashboard figures change as expected.

**Receipts and mail**

- [ ] The order mail's content and links; the receipt opens on another device without signing in, if receipts are on.

**Accessibility and devices**

- [ ] The full path with a keyboard only and on a phone ([Store accessibility](store-accessibility.md)).

## What to automate

- Calculations in your own handlers: DB-less unit tests ([DB-less PHPUnit tests](../../creators/app-creators/db-less-phpunit-tests.md)).
- Repeatable flows: the repository's own tests for the commerce services
  (`tests/tests/extension/expservices/commerce/`) show how basket and order operations can be exercised from code
  ([Testing applications](../../creators/app-creators/testing-applications.md)).
- Visual checks of product, basket and checkout pages after design changes.

## After every update

Repeat the payment section and one full order per payment method after every Exponential or extension update, and
after any change to shop settings or workflows ([Upgrades without surprises](../../businesses/reliability/upgrades-without-surprises.md)).

## Cleaning up

On a test copy, the shop cleanup script removes all baskets, wishlists and orders. On a live store, remove test orders
one by one and never run that script ([Archive, remove and cleanup](../orders/archive-remove-and-cleanup.md)). Switch
providers back to live mode and mail back to the real transport, and place one small real order.

## Related pages

- [Operating a store](README.md)
- [Basket, checkout and orders: test order checklist](../../creators/website-creators/basket-checkout-and-orders.md#test-order-checklist)
- [Basket and checkout flow: test the path](../../businesses/stores/basket-and-checkout-flow.md#test-the-path)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
