---
title: Custom shipping handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 61
summary: Writing a shipping handler for an Exponential store - the three methods eZShippingManager calls, the array getShippingInfo returns with several shipping items and their VAT, how the basket info handler adds shipping to the totals, registration in shop.ini, and models such as free shipping above an amount or rates by country.
keywords: shipping handler, eZShippingManager, getShippingInfo, updateShippingInfo, purgeShippingInfo, shipping_items, vat_value, is_vat_inc, management_link, vatPriceInfo, ShippingSettings, shippinghandlers, free shipping, shipping by country, shipping by weight
type: help
---
# Custom shipping handler

This page is for developers who need shipping rules beyond the flat amount of the
[Simple shipping event](../checkout/simple-shipping-event.md). No shipping handler is shipped in the kernel; the
contract is documented in `kernel/classes/ezshippingmanager.php`, and this page turns it into a recipe.

## Contents

1. [The contract](#the-contract)
2. [What getShippingInfo returns](#what-getshippinginfo-returns)
3. [Registering the handler](#registering-the-handler)
4. [How shipping reaches the totals](#how-shipping-reaches-the-totals)
5. [Common models](#common-models)
6. [Testing](#testing)

## The contract

`eZShippingManager` loads the handler named in `shop.ini [ShippingSettings] Handler` and calls three methods, each
with the basket's product collection id:

| Method | Called | Returns |
|---|---|---|
| `getShippingInfo( $productCollectionID )` | Whenever the basket, the confirmation or totals need shipping | An array (below), or nothing for no shipping |
| `updateShippingInfo( $productCollectionID )` | When an item is added (the `update-shipping-info` step of `addtobasket`) | Anything your handler needs; use it to recalculate or store choices |
| `purgeShippingInfo( $productCollectionID )` | When shipping information should be removed | Nothing |

The class name is `<name>ShippingHandler` in `<name>shippinghandler.php`.

## What getShippingInfo returns

One shipping line:

```php
return array( 'description' => 'Standard delivery',
              'cost'        => 6.50,
              'vat_value'   => 20,
              'is_vat_inc'  => 1 );
```

Several lines, for example when parcels carry different VAT rates:

```php
return array( 'shipping_items' => array(
                  array( 'description' => 'Parcel, standard VAT', 'cost' => 5.00, 'vat_value' => 20, 'is_vat_inc' => 1 ),
                  array( 'description' => 'Parcel, reduced VAT', 'cost' => 3.00, 'vat_value' => 5, 'is_vat_inc' => 1 ) ),
              'description' => 'Total shipping',
              'cost'        => 8.00,
              'vat_value'   => false,
              'is_vat_inc'  => 1 );
```

| Key | Meaning |
|---|---|
| `description` | Text shown to the customer |
| `cost` | Amount as a float |
| `vat_value` | VAT percentage as a number, or `false` when the total combines several rates |
| `is_vat_inc` | `1` if `cost` includes VAT, `0` if not |
| any other key | Passed to templates, for example `management_link` to a page where the customer picks a method |

## Registering the handler

In your extension's `settings/shop.ini.append.php`:

```ini
[ShippingSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop
```

and the class in `extension/mystore_shop/shippinghandlers/mystoreshippinghandler.php`. If the file is not found, the
debug log says "Shipping handler 'mystore' not found" with the directories searched. Remove the simple shipping
workflow from the `confirmorder` trigger when the handler goes live.

## How shipping reaches the totals

`eZShippingManager::vatPriceInfo()` turns your array into amounts per VAT rate, and the shipped basket info handler
(`ezdefault`) adds them to the basket's price information per VAT rate and to the total
([Basket info and confirm order handlers](basket-info-and-confirm-order-handlers.md)). The `handle-shipping` step of
the `confirmorder` operation applies shipping when the order is confirmed
([Checkout operations step by step](../checkout/checkout-operations-step-by-step.md#step-4-confirm-the-order)).

## Common models

| Model | What the handler reads |
|---|---|
| Free shipping above an amount | The product collection's items and their prices |
| Rates by country or zone | The customer's country (`eZVATManager::getUserCountry()`) or the account data |
| Rates by weight | A weight field on product classes, times item counts |
| Customer chooses a method | A choice stored by your own view (linked with `management_link`), read back by `getShippingInfo()` |
| Carrier rate service | A call to the carrier with the address and parcel data; cache the answer per basket |

Whether shipping carries VAT, and at which rate, is a question for your tax adviser.

## Testing

- [ ] Unit tests for the calculation with fixed baskets ([DB-less PHPUnit tests](../../creators/app-creators/db-less-phpunit-tests.md)).
- [ ] Basket, confirmation, order view and mail show the same shipping.
- [ ] Changing quantities changes shipping when it should.
- [ ] Each country or zone you support, and one you do not.
- [ ] No second shipping line from an old simple shipping workflow.

## Related pages

- [Extending the shop](README.md)
- [Shipping and handling (businesses)](../../businesses/stores/shipping-and-handling.md)
- [shop.ini walkthrough](../settings/shop-ini-walkthrough.md#shippingsettings)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
