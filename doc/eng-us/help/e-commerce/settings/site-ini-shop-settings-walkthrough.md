---
title: site.ini shop settings walkthrough
audience: ecommerce, website-creator, admin
category: e-commerce
order: 32
summary: The shop settings that live in site.ini - ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail and ProductDatatypeStringList in ShopSettings, the BasketCleanup settings in Session, and the mail settings the order mail uses - with shipped values and when to change them.
keywords: site.ini, ShopSettings, ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail, ProductDatatypeStringList, BasketCleanup, BasketCleanupTime, BasketCleanupIdleTime, BasketCleanupFetchLimit, BasketCleanupAverageFrequency, MailSettings, AdminEmail, EmailSender, AllowedRedirectHosts
type: help
---
# site.ini shop settings walkthrough

This page is for developers and administrators. Some shop behavior is set in `settings/site.ini`, not in `shop.ini`,
which makes it easy to miss. This page lists every such setting with its shipped value and the reason to change it.

## Contents

1. [ShopSettings](#shopsettings)
2. [Session: basket cleanup](#session-basket-cleanup)
3. [MailSettings used by the order mail](#mailsettings-used-by-the-order-mail)
4. [SiteSettings: redirects to a payment provider](#sitesettings-redirects-to-a-payment-provider)
5. [Choosing values together](#choosing-values-together)

## ShopSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | The basket is emptied when `shop/checkout` has finished, that is after payment. A customer who cancels payment comes back to a full basket |
| | `enabled` | The basket is emptied as soon as the customer confirms on `shop/confirmorder`. The comment in `site.ini` says some payment systems need this; the basket is then gone if payment is cancelled |
| `ClearBasketOnLogout` | `disabled` | Logging out keeps the basket; `enabled` empties it |
| `RedirectAfterAddToBasket` | `basket` | After **Add to basket** the customer sees the basket; `reload` sends them back to where they were |
| `SendOrderEmail` | `enabled` | The confirm order handler sends the order mail to the customer and to the shop; `disabled` sends none |
| `ProductDatatypeStringList[]` | commented out | The datatypes that hold prices; when unset, `ezprice` and `ezmultiprice` ([Custom price datatypes](../extending/custom-price-datatypes.md)) |

What these mean for the customer is described in [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md).

## Session: basket cleanup

Baskets belong to sessions. The `[Session]` block decides how baskets of expired sessions are removed:

| Setting | Shipped | Meaning |
|---|---|---|
| `BasketCleanup` | `cronjob` | Clean up in the `basket_cleanup` cron script; `pageload` lets PHP's session handling do it |
| `BasketCleanupTime` | `120` | Seconds one cleanup run may take |
| `BasketCleanupIdleTime` | `2` | Seconds of pause between batches, to keep the load down |
| `BasketCleanupFetchLimit` | `3000` | Baskets fetched per batch |
| `BasketCleanupAverageFrequency` | `10` | On average the cleanup runs once in ten cron runs |

The script `basket_cleanup.php` is in the `infrequent` cron part (`settings/cronjob.ini`), so that part must run
([Cron jobs](../../../guides/cronjobs.md)). The Store dashboard warns when basket cleanup would delete baskets kept
in PHP sessions.

## MailSettings used by the order mail

The shipped confirm order handler (`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`) uses:

| Setting | Use |
|---|---|
| `[MailSettings] EmailSender` | The sender of the order mail; if empty, `AdminEmail` is used |
| `[MailSettings] AdminEmail` | The shop's copy of every order mail goes to this address |

The mail body and subject come from the template `shop/orderemail.tpl`. If the shop's copy should go to an order desk
rather than the site administrator, that needs a confirm order handler of your own
([Basket info and confirm order handlers](../extending/basket-info-and-confirm-order-handlers.md)).

## SiteSettings: redirects to a payment provider

A redirect gateway sends the customer to another host. The `ezpaypal` extension adds
`[SiteSettings] AllowedRedirectHosts[]=www.paypal.com` in its own `site.ini.append`; a gateway of your own may need
the same for its provider's host.

## Choosing values together

| Store | `ClearBasketOnCheckout` | `RedirectAfterAddToBasket` | `ClearBasketOnLogout` |
|---|---|---|---|
| Few items per order | `disabled` | `basket` | `disabled` |
| Many items per order | `disabled` | `reload` | `disabled` |
| Gateway documentation requires early clearing | `enabled` | as above | as above |
| Shared or public computers (kiosk) | `disabled` | as above | `enabled` |

## Related pages

- [Shop settings walkthroughs](README.md)
- [shop.ini walkthrough](shop-ini-walkthrough.md)
- [Order mail and receipts for operators](../orders/order-mail-and-receipts-for-operators.md)
- [Temporary orders and activation](../checkout/temporary-orders-and-activation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
