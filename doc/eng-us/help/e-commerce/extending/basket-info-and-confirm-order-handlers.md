---
title: Basket info and confirm order handlers
audience: ecommerce, app-creator, developer
category: e-commerce
order: 65
summary: Two small handlers of the Exponential shop - the basket info handler that adds shipping to the basket's totals per VAT rate, and the confirm order handler that sends the order mail - what each does, when to replace it, and how to register a replacement.
keywords: basket info handler, eZDefaultBasketInfoHandler, updatePriceInfo, price_info, total_price_info, BasketInfoSettings, confirm order handler, eZDefaultConfirmOrderHandler, sendOrderEmail, ConfirmOrderSettings, order mail, order desk
type: help
---
# Basket info and confirm order handlers

This page is for developers who adjust how the basket's totals are calculated or how the order mail is sent. Both are
small handlers with one job each, selected by a setting and replaceable from an extension.

## Contents

1. [The basket info handler](#the-basket-info-handler)
2. [The confirm order handler](#the-confirm-order-handler)
3. [Registering replacements](#registering-replacements)
4. [Testing](#testing)

## The basket info handler

| | |
|---|---|
| Setting | `shop.ini [BasketInfoSettings] Handler` (shipped `ezdefault`) |
| Shipped class | `eZDefaultBasketInfoHandler`, `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php` |
| Method | `updatePriceInfo( $productCollectionID, &$basketInfo )` |

The shipped handler asks the shipping handler for its shipping information, turns it into amounts per VAT rate with
`eZShippingManager::vatPriceInfo()`, and adds those amounts to `$basketInfo['price_info'][<VAT rate>]` and to
`$basketInfo['total_price_info']` (ex VAT, inc VAT and VAT). Templates that show VAT per rate read these arrays.

Replace it when the basket's totals must include something else per VAT rate, for example a handling fee or a deposit
that a shipping handler should not carry. Extend the shipped class and call the parent first, so shipping keeps
working.

## The confirm order handler

| | |
|---|---|
| Setting | `shopaccount.ini [ConfirmOrderSettings] Handler` (shipped `ezdefault`) |
| Shipped class | `eZDefaultConfirmOrderHandler`, `kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php` |
| Methods | `execute( $params )`, `sendOrderEmail( $params )` |

`execute()` sends the mail only when `site.ini [ShopSettings] SendOrderEmail=enabled`. `sendOrderEmail()` renders
`shop/orderemail.tpl` once and sends it to the order's e-mail address and to `[MailSettings] AdminEmail`, from
`EmailSender` (or `AdminEmail`), with the mail category `orders`.

Replace it when:

| Need | Change |
|---|---|
| The shop's copy should go to an order desk, not the site administrator | Send the second mail to an address from your own setting |
| The shop's copy should differ from the customer's (internal notes, pick list) | Render a second template for the shop |
| A copy should go to a fulfillment partner | Add a third receiver |
| Order data should go to another system at the same moment | Prefer an event on the after trigger of `shop_checkout` ([Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md)) |

## Registering replacements

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[BasketInfoSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop

# extension/mystore_shop/settings/shopaccount.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=mystore_shop

[ConfirmOrderSettings]
Handler=mystore
```

Files: `basketinfohandlers/mystorebasketinfohandler.php` (class `mystoreBasketInfoHandler`) and
`confirmorderhandlers/mystoreconfirmorderhandler.php` (class `mystoreConfirmOrderHandler`). The RAD handler wizard
has a basket info handler recipe ([Developer path](../roles/developer-path.md#generators-that-save-time)).

## Testing

- [ ] Basket and confirmation totals per VAT rate add up to the grand total.
- [ ] Order mail: the customer and each other receiver get the right version.
- [ ] `SendOrderEmail=disabled` sends nothing, as before.
- [ ] Test mail addresses are not real customers' ([Testing a store](../operations/testing-a-store.md)).

## Related pages

- [Extending the shop](README.md)
- [Order mail and receipts for operators](../orders/order-mail-and-receipts-for-operators.md)
- [Notifications and mail](../../creators/app-creators/notifications-and-mail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
