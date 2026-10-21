---
title: Product categories and VAT rules
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 24
summary: How VAT by customer country and product category really works in Exponential - the VAT handler that must be switched on, product categories as tax classes, how the default handler ranks VAT rules, the customer's country, and how to test the result.
keywords: VAT rules, product category, ezproductcategory, VAT handler, eZDefaultVATHandler, VATSettings, Handler=ezdefault, dynamic VAT, Determined by VAT charging rules, UserCountryAttribute, ProductCategoryAttribute, RequireUserCountry, rule priority
type: help
---
# Product categories and VAT rules

This page is for developers and merchants who charge VAT by the customer's country and the product's tax class. The
business view is in [VAT rules and VAT types](../../businesses/stores/vat-rules-and-vat-types.md) and the owner's
checks in [VAT checklist](../../customers/commerce/vat-checklist.md); this page explains the mechanism, including one
setting the other pages do not mention: VAT rules only work when a VAT handler is switched on.

## Contents

1. [Fixed VAT and dynamic VAT](#fixed-vat-and-dynamic-vat)
2. [Switching on dynamic VAT](#switching-on-dynamic-vat)
3. [Product categories](#product-categories)
4. [How the default handler picks a rule](#how-the-default-handler-picks-a-rule)
5. [The customer's country](#the-customers-country)
6. [A worked rule set](#a-worked-rule-set)
7. [Testing VAT](#testing-vat)

## Fixed VAT and dynamic VAT

| Mode | The product's VAT type | VAT applied |
|---|---|---|
| Fixed | A named VAT type, for example "Standard 20%" | Always that percentage |
| Dynamic | "Determined by VAT charging rules" (`shop.ini [VATSettings] DynamicVatTypeName`) | Chosen at buying time by the VAT handler from the VAT rules |

Internally the dynamic VAT type has the id `-1` (`eZVatType::dynamicVatType()` in `kernel/classes/ezvattype.php`).
When the handler cannot determine a percentage, the VAT is marked as unknown.

## Switching on dynamic VAT

`eZVATManager` (`kernel/classes/ezvatmanager.php`) uses VAT rules only when `shop.ini [VATSettings] Handler` is set.
In the shipped `settings/shop.ini` the line is commented out (`#Handler=ezdefault`), so dynamic VAT is off, and the
"Determined by VAT charging rules" choice is offered only once a handler is configured. To switch it on, in
`settings/override/shop.ini.append.php`:

```ini
[VATSettings]
Handler=ezdefault
ProductCategoryAttribute=tax_class
UserCountryAttribute=country
RequireUserCountry=true
```

| Setting | Meaning |
|---|---|
| `Handler` | The VAT handler; `ezdefault` is `eZDefaultVATHandler` in `kernel/classes/vathandlers/` |
| `ProductCategoryAttribute` | Identifier of the `ezproductcategory` field on product classes |
| `UserCountryAttribute` | Identifier of the country field on the user class |
| `RequireUserCountry` | `true`: a missing country is reported; set `false` only with a custom handler that does not need it |

Clear the INI cache afterwards. The Store dashboard's "How your shop works" section then shows the VAT rules and the
countries orders came from ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Product categories

A product category is a tax class, not a catalog folder. Create them in **Store > Product categories**
(`shop/productcategories`, policy `shop/setup`), add an `ezproductcategory` field to each product class, and set it on
every product. Typical categories: "Standard", "Reduced (food, books)", "Zero rated", "Digital services".

## How the default handler picks a rule

Each VAT rule has a country (or `*` for any country), a list of product categories (or none, meaning any category)
and a VAT type. For a product and a country, `eZDefaultVATHandler::chooseVatType()` scores every rule:

| Rule matches | Score |
|---|---|
| Exact country and exact category | 4 |
| Exact country, rule has no categories | 3 |
| Any country (`*`) and exact category | 2 |
| Any country, no categories | 1 |
| Country or category does not match | 0 (not used) |

The highest score wins; among equal scores, the first rule found wins. If no rule scores above 0, an error is logged
and the VAT type "None" with 0 percent is used. A product without a category, or a customer without a country, only
matches rules with "any" on that side. Two practical consequences:

- Always create one catch-all rule (`*`, no categories) so that nothing falls through to 0 percent by accident.
- Do not create two rules with the same country and category; the result then depends on the order rules are fetched.

## The customer's country

`eZVATManager::getUserCountry()` first takes the country a visitor chose with `shop/setusercountry` (the preferred
country), and otherwise reads the user's `UserCountryAttribute` field. The confirmation step runs the `handle-user-country` method of the
`confirmorder` operation before shipping is calculated. Guests and new customers therefore need a country field on the
checkout form that your account handler stores
([shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md)). The country list itself, with the most
used countries first, is explained in [Country groups and regions](../../businesses/stores/country-groups-and-regions.md).

## A worked rule set

A store in country A sells to A, to B and to the rest of the world; books have a reduced rate in A:

| Country | Categories | VAT type |
|---|---|---|
| A | Books | Reduced A |
| A | (any) | Standard A |
| B | (any) | Standard B |
| `*` | (any) | Zero |

A book bought from A scores 4 on the first rule: reduced. A shirt from A scores 3 on the second: standard. Anything
from B scores 3 on the third. Anything from elsewhere scores 1 on the last. Whether these are the right rates is for
your tax adviser.

## Testing VAT

- [ ] For each rule, one test order with a product of that category and a customer of that country.
- [ ] A product without a category and a customer without a country: the catch-all applies.
- [ ] The confirmation page, order view and order mail show the same VAT.
- [ ] The dashboard shows no 0 percent VAT type warning you did not expect.

## Related pages

- [Catalog modeling](README.md)
- [Custom VAT handler](../extending/custom-vat-handler.md)
- [VAT types and rules (running your store)](../../customers/commerce/vat-types-and-rules.md)
- [shop.ini walkthrough](../settings/shop-ini-walkthrough.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
