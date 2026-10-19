---
title: VAT rules and VAT types
audience: businesses, admin, website-creator
category: business-solutions
order: 50
summary: How an Exponential store charges VAT or sales tax - fixed VAT types, VAT rules by customer country and product category, how the best rule is chosen, and what a business must confirm with its tax adviser.
keywords: VAT, sales tax, VAT types, VAT rules, product category, customer country, tax rate, reduced rate, zero rate, DynamicVatTypeName, tax adviser
type: help
---
# VAT rules and VAT types

This page is for business owners and accountants who need to know how the store calculates tax. It explains the two
tools the shop has, fixed VAT types and VAT rules, and how the shop picks the rate for an order line. It describes what
the software does, not what your tax law requires.

> **Warning**
> This page is not tax advice. VAT and sales tax law differ by country and change over time. The shop calculates what
> you configure; whether that is correct for your business is a question for your tax adviser
> ([Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#vat)).

## Contents

1. [Two ways to charge VAT](#two-ways-to-charge-vat)
2. [VAT types](#vat-types)
3. [VAT rules](#vat-rules)
4. [How the shop picks a rule](#how-the-shop-picks-a-rule)
5. [Where the customer's country comes from](#where-the-customers-country-comes-from)
6. [Sales tax outside VAT countries](#sales-tax-outside-vat-countries)
7. [Checklist](#checklist)

## Two ways to charge VAT

| Approach | How it works | Fits |
|---|---|---|
| Fixed VAT type on the price | Each price field names one VAT type, for example "Standard" | Stores that sell in one country, or charge the same rate everywhere |
| VAT rules | The price's VAT type is "Determined by VAT charging rules"; the rate comes from the customer's country and the product's category | Stores that sell to several countries, or have products with different rates |

The name of the dynamic VAT type is set in `settings/shop.ini`, `[VATSettings] DynamicVatTypeName`; the shipped value is
`Determined by VAT charging rules`.

## VAT types

A VAT type is a named percentage, such as standard, reduced or zero. They are managed under **Store > VAT types**
(`shop/vattype`, which needs `shop/setup`). The class is `eZVatType` in `kernel/classes/ezvattype.php`.

- Name VAT types after what they mean to your accountant ("Standard 19", "Reduced 7", "Zero"), so nobody guesses.
- Change a percentage when the law changes; every price that uses the type follows.
- The Store dashboard warns when a 0 percent VAT type is in use, so a zero rate is never an accident
  ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## VAT rules

A VAT rule says: for customers in this country, and products in these categories, use this VAT type. Rules are managed
in `shop/vatrules` and `shop/editvatrule`. Product categories are managed in `shop/productcategories` and assigned to
products through an `ezproductcategory` field.

Two settings connect rules to your content (`shop.ini [VATSettings]`):

| Setting | Meaning |
|---|---|
| `ProductCategoryAttribute` | The identifier of the product field that holds the category |
| `UserCountryAttribute` | The identifier of the user field that holds the customer's country |
| `RequireUserCountry` | Shipped `true`: the shop must know the customer's country to apply rules |

A rule can name a specific country or apply to any country, and can name categories or apply to every category. A
typical rule set for a store with standard and reduced rates:

| Country | Categories | VAT type |
|---|---|---|
| Any | Any | Standard |
| Any | Books | Reduced |
| A country where you are registered for VAT | Any | That country's standard rate |

These rows are an illustration of the mechanism, not a statement of any country's tax rates.

## How the shop picks a rule

The default VAT handler (`kernel/classes/vathandlers/ezdefaultvathandler.php`) scores every rule against the order
line and takes the best match:

1. A rule that names the customer's country exactly beats a rule for any country.
2. A rule that names the product's category exactly beats a rule for any category.
3. A rule must match both the country and the category to count at all.
4. If several rules score the same, the first one wins.

In plain words: write one general rule for "any country, any category", then add specific rules only where the rate
differs. The specific rule will win where it applies.

If the customer's country is not known, the handler uses a placeholder that matches only the "any country" rules
(same file).

## Where the customer's country comes from

The country comes from the customer's account field named in `UserCountryAttribute`, or from the country a visitor
chooses (`shop/setusercountry`). The country select in the shop forms is the grouped list described in
[Country groups and regions](country-groups-and-regions.md); in VAT rules it posts the country code
([The grouped country list](../../../features/6.0/country-groups.md)).

A different VAT logic, for example one based on the delivery address or on a customer's VAT number, can be written as
a VAT handler of your own; `shop.ini [VATSettings]` has `RepositoryDirectories[]` and `ExtensionDirectories[]` for it.
That is developer work ([Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md)
shows the general extension pattern; a partner can help, see Choosing a partner).

## Sales tax outside VAT countries

The shop's terms are "VAT", but the mechanism is a percentage per country and category, so a store that charges sales
tax can use the same tools. Tax that depends on a region inside a country, or that a tax service calculates at checkout,
is not covered by the shipped handler and needs a custom VAT handler or an integration.

## Checklist

- [ ] Your tax adviser has confirmed the rates and where they apply.
- [ ] VAT types are named after their meaning and percentage.
- [ ] Prices use either a fixed VAT type or "Determined by VAT charging rules", consistently per class.
- [ ] With rules: `ProductCategoryAttribute` and `UserCountryAttribute` are set, and every product has a category.
- [ ] A general "any country, any category" rule exists.
- [ ] Test orders from two countries and two categories show the expected VAT on the confirmation page.
- [ ] The dashboard shows no unexpected 0 percent VAT type.

## Related pages

- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Prices and currencies](prices-and-currencies.md)
- [Country groups and regions](country-groups-and-regions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
