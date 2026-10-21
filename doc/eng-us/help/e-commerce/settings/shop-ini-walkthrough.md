---
title: shop.ini walkthrough
audience: ecommerce, website-creator, admin
category: e-commerce
order: 31
summary: Every block of settings/shop.ini explained with its shipped value - CurrencySettings, MathSettings, ExchangeRatesSettings, ECBExchangeRatesSettings, ShippingSettings, BasketSettings, BasketInfoSettings, VATSettings, OrderViewSettings and OrderReceiptSettings - and when to change each.
keywords: shop.ini, CurrencySettings, PreferredCurrency, MathSettings, RoundingType, RoundingPrecision, RoundingTarget, ExchangeRatesSettings, BaseCurrency, eZECB, ShippingSettings, BasketSettings, BasketViewName, BasketInfoSettings, VATSettings, OrderViewSettings, OrderLinkView, OrderReceiptSettings, Secret, BcryptCost
type: help
---
# shop.ini walkthrough

This page is for developers and administrators who configure the shop. It goes through `settings/shop.ini` block by
block, with the shipped value, what each setting does and when to change it. Put changes in
`settings/override/shop.ini.append.php` or a siteaccess file ([Shop settings walkthroughs](README.md#where-shop-settings-go)).

## Contents

1. [CurrencySettings](#currencysettings)
2. [MathSettings](#mathsettings)
3. [ExchangeRatesSettings](#exchangeratessettings)
4. [ShippingSettings](#shippingsettings)
5. [BasketSettings](#basketsettings)
6. [BasketInfoSettings](#basketinfosettings)
7. [VATSettings](#vatsettings)
8. [OrderViewSettings](#orderviewsettings)
9. [OrderReceiptSettings](#orderreceiptsettings)

## CurrencySettings

| Setting | Shipped | Meaning | Change when |
|---|---|---|---|
| `PreferredCurrency` | `USD` | The shop's currency: the currency of `ezprice` prices and the default for visitors | Almost always: set your own currency code |

## MathSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `MathHandler` | `eZPHPMath` | Floating point arithmetic in PHP; `eZBCMath` uses the bcmath extension |
| `MathScale` | `10` | Digits after the decimal point for `eZBCMath` only |
| `RoundingType` | `round` | How converted prices are rounded: `none`, `round`, `ceil`, `floor` |
| `RoundingPrecision` | `2` | Digits kept after the decimal point |
| `RoundingTarget` | `false` | Forces the last digits: with precision 2, target `5` turns 89.468543 into 89.45 and target `99` into 89.99 |

`RoundingTarget` is the setting behind "charm prices" for converted currencies. It affects automatic conversions, not
prices entered by hand.

## ExchangeRatesSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `ExchangeRatesUpdateHandler` | `eZECB` | The handler that fetches rates when **Update autorates** is pressed |
| `RepositoryDirectories[]` | `kernel/shop/classes/exchangeratehandlers` | Where handlers are found |
| `ExtensionDirectories[]` | empty | Extensions that carry handlers in `exchangeratehandlers/<name>/<name>handler.php` |
| `BaseCurrency` | `EUR` | The currency the rates are relative to |

The block `[ECBExchangeRatesSettings]` holds where the shipped handler fetches the European Central Bank's daily rates
(`ServerName`, `ServerPort`, `RatesURI`). Check from your server that the address answers before you rely on it. A
handler of your own is described in [Exchange rate handlers](../extending/exchange-rate-handlers.md).

## ShippingSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Handler` | commented out (`#Handler=ezdefault`) | The shipping handler; none is active |
| `RepositoryDirectories[]` | `kernel/classes/shippinghandlers` | Where handlers are looked for (this directory is not shipped) |
| `ExtensionDirectories[]` | empty | Extensions with a `shippinghandlers/` directory |

Leave `Handler` unset unless you have written a handler: no handler file is shipped in the kernel, and the comment's
`ezdefault` would not be found. Flat shipping uses a workflow event instead
([Simple shipping event](../checkout/simple-shipping-event.md), [Custom shipping handler](../extending/custom-shipping-handler.md)).

## BasketSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `BasketViewName` | `basket` | The address name of the basket (`/shop/<name>/`); `/shop/basket/` keeps working whatever you choose |

Letters, digits, dash and underscore are allowed; anything else falls back to `basket`. A design may add
`shop/<name>.tpl`; `shop/cart.tpl` ships as an example ([Shop basket view name](../../../bc/6.0/shop-basket-view-name.md)).

## BasketInfoSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Handler` | `ezdefault` | The basket info handler that adds calculated price information to the basket |
| `RepositoryDirectories[]` | `kernel/classes/basketinfohandlers` | Where handlers are found |
| `ExtensionDirectories[]` | empty | Extensions with a `basketinfohandlers/` directory |

See [Basket info and confirm order handlers](../extending/basket-info-and-confirm-order-handlers.md).

## VATSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Handler` | commented out (`#Handler=ezdefault`) | The VAT handler; without it VAT rules are not used |
| `ProductCategoryAttribute` | empty | Identifier of the product category field |
| `UserCountryAttribute` | empty | Identifier of the user's country field |
| `RequireUserCountry` | `true` | Report a missing customer country |
| `DynamicVatTypeName` | `Determined by VAT charging rules` | The label of the dynamic VAT type |
| `RepositoryDirectories[]`, `ExtensionDirectories[]` | `kernel/classes/vathandlers`, empty | Where handlers are found (`vathandlers/` in extensions) |

Details and the rule priority are in [Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md).

## OrderViewSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `OrderLinkView` | `orderview` | What a placed order links to after checkout, in the customer's order lists and in the order mail: `orderview` (`shop/orderview/<id>`) or `orderreceipt` (`shop/orderreceipt/<token>`) |

With `orderview`, a guest's order link stops working when the session ends. With `orderreceipt`, the link never
expires and opens for whoever holds it. The administration always links to `shop/orderview`
([Permanent order receipts](../../../features/6.0/order-receipts.md)).

## OrderReceiptSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Secret` | empty | Signs receipt addresses. Empty: generated on first use and kept in `<VarDir>/secrets/orderreceipt.key` |
| `BcryptCost` | `5` | Cost of the signature, 4 to 6 |

Set `Secret` (32 characters or more, in an override file) only when several installations share one database and
must open each other's receipts. Changing it changes every receipt address already sent to customers.

## Related pages

- [Shop settings walkthroughs](README.md)
- [Price fields compared](../catalog/price-fields-compared.md)
- [Prices and currencies (businesses)](../../businesses/stores/prices-and-currencies.md)
- [Order receipts and confirmation mail](../../businesses/stores/order-receipts-and-confirmation-mail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
