---
title: Wishlists and baskets
audience: customer, admin, businesses
category: customer-success
order: 180
summary: What a store owner should know about baskets and wishlists in Exponential - abandoned baskets on the dashboard, basket cleanup by cron job, what happens to a basket on checkout and sign-out, the wishlist for signed-in customers, and the settings to ask your partner about.
keywords: basket, shopping cart, wishlist, abandoned baskets, basket cleanup, cron job, ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, BasketViewName
type: help
---
# Wishlists and baskets

This page is for the owner and the store manager. Baskets and wishlists are where customers show interest before
they buy, and a few settings decide how they behave. This page explains what to watch and what to ask your partner;
the wishlist itself is described on [Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md),
and the checkout steps on [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md).

## Baskets on the dashboard

The Store dashboard shows baskets in progress and abandoned baskets, and lists the latest baskets and unconfirmed
checkouts under "Work to do" ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)). Use
them as signals:

| What you see | What it may mean | What to do |
|---|---|---|
| Abandoned baskets rise after a change | Shipping cost, prices or the checkout form put customers off | Look at what changed that week |
| Many unconfirmed checkouts | Customers stop at the confirmation page or the payment step | Place a test order; see [Payment gateways](payment-gateways.md) |
| Baskets rise but orders do not | Something blocks the checkout | Test the whole checkout today |

## How baskets behave

These are settings in `settings/site.ini [ShopSettings]`; your partner changes them in an override.

| Setting | Shipped | What it means for your customers |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | The basket is emptied only when payment is finished, so a customer who cancels payment still has it |
| `ClearBasketOnLogout` | `disabled` | Signing out does not empty the basket |
| `RedirectAfterAddToBasket` | `basket` | After **Add to basket**, the customer sees the basket; `reload` keeps them where they were |

The comments in `settings/site.ini` explain that some payment systems need `ClearBasketOnCheckout=enabled`; check with
your partner before changing it.

The basket page can be given another name, for example `/shop/cart/`, with `settings/shop.ini [BasketSettings]
BasketViewName`; `/shop/basket/` keeps working for old links
([Shop basket view name](../../../bc/6.0/shop-basket-view-name.md)).

## Basket cleanup

Old baskets are removed by the `basket_cleanup.php` cron job, which is in the infrequent part of the shipped
`settings/cronjob.ini`. `site.ini [Session] BasketCleanup=cronjob` is the shipped setting, so the cleanup only
happens when the cron jobs run ([Cron jobs](../../../guides/cronjobs.md)).

- [ ] Ask your host whether the infrequent cron part runs, and how often.
- [ ] If the dashboard's "Next steps" warns about basket cleanup and baskets kept in sessions, pass it to your
  partner ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## The wishlist

The wishlist (`shop/wishlist`, policy `shop/buy`) lets signed-in customers keep products for later. It belongs to
the user account, and adding to it requires a registered, signed-in user
([Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md)). Your site design needs
the **Add to wishlist** button and a link to the wishlist.

| Owner's question | Answer |
|---|---|
| Can guests use it? | No; it needs an account |
| Can customers share it? | No shared wishlists in the kernel (same page) |
| Can I see what customers wish for? | Not in a shop report; the shop views have no wishlist report (`kernel/shop/module.php`) |

The wishlist is a good reason to offer accounts if your products are considered purchases
([Customer accounts](customer-accounts.md)).

## Personal data

Baskets are tied to a session and wishlists to a user (`ezbasket`, `ezwishlist`), as listed in
[Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md). Include them when you
describe your store's data in your privacy notice.

## Related pages

- [Basket and checkout flow](../../businesses/stores/basket-and-checkout-flow.md)
- [Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md)
- [Store reports](store-reports.md)
- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
