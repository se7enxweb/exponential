---
title: Custom VAT handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 62
summary: Replacing the VAT decision of an Exponential store with a handler of your own - the getVatPercent contract, extending eZDefaultVATHandler, registration in shop.ini, RequireUserCountry, and cases such as business customers with a VAT number or digital services.
keywords: VAT handler, getVatPercent, eZDefaultVATHandler, eZVATManager, VATSettings, Handler, vathandlers, dynamic VAT, RequireUserCountry, business customers, reverse charge, digital services, custom tax
type: help
---
# Custom VAT handler

This page is for developers whose VAT rules cannot be expressed with VAT rules by country and product category. The
shop asks a VAT handler for the percentage of each product with dynamic VAT; you can write your own handler and keep
everything else.

## Contents

1. [When you need one](#when-you-need-one)
2. [The contract](#the-contract)
3. [Registering it](#registering-it)
4. [Building on the default handler](#building-on-the-default-handler)
5. [Testing](#testing)

## When you need one

| Situation | Default handler enough? |
|---|---|
| Rate by customer country and product category | Yes ([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md)) |
| A different rate for business customers with a valid VAT number | No |
| The rate depends on the delivery address rather than the account country | No |
| Rates come from an external tax service | No |

The tax rules themselves are for your tax adviser; the handler only implements what you decide.

## The contract

`eZVATManager::getVAT( $object, $country )` (`kernel/classes/ezvatmanager.php`) loads the handler and calls:

```php
function getVatPercent( $object, $country )
```

| Argument | Meaning |
|---|---|
| `$object` | The product's content object |
| `$country` | The customer's country code, or `false` when unknown |

Return the percentage as a number. Returning `null` marks the VAT as unknown. The handler is only asked for products
whose price uses the dynamic VAT type; products with a fixed VAT type keep it.

## Registering it

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[VATSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop
RequireUserCountry=false
```

The class `mystoreVATHandler` goes in `extension/mystore_shop/vathandlers/mystorevathandler.php`. Set
`RequireUserCountry=false` only if your handler does not need the country; the shipped comment says so too
([shop.ini walkthrough](../settings/shop-ini-walkthrough.md#vatsettings)).

## Building on the default handler

Extending `eZDefaultVATHandler` (`kernel/classes/vathandlers/ezdefaultvathandler.php`) keeps the rule-based decision and
lets you handle one exception first:

```php
class mystoreVATHandler extends eZDefaultVATHandler
{
    function getVatPercent( $object, $country )
    {
        if ( $this->isBusinessCustomerWithValidNumber() )
        {
            return 0;
        }
        return parent::getVatPercent( $object, $country );
    }
}
```

`isBusinessCustomerWithValidNumber()` is yours to write; where the VAT number comes from (an account field, the checkout
form of your shop account handler) is your design. The RAD handler wizard has a VAT handler recipe that writes this
skeleton ([Developer path](../roles/developer-path.md#generators-that-save-time)).

## Testing

- [ ] Unit tests of `getVatPercent()` for every case you implement.
- [ ] A test order per case; the confirmation page, order and mail agree.
- [ ] Unknown country: the result is what you intend, not an accidental 0.
- [ ] The Store dashboard shows no unexpected 0 percent warning.

## Related pages

- [Extending the shop](README.md)
- [VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md)
- [VAT checklist](../../customers/commerce/vat-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
