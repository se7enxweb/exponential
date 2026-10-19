---
title: Country groups and regions
audience: businesses, admin, website-creator
category: business-solutions
order: 60
summary: How the store's country list works for customers - most used countries first, browser autofill, per-site rankings - and what selling to several countries and regions involves.
keywords: country list, country groups, countrygroups.ini, regions, international selling, autofill, checkout form, shipping countries, markets
type: help
---
# Country groups and regions

This page is for business owners who sell to customers in more than one country. It explains the country list your
customers see at checkout, how to put your own markets at the top, and what else changes when you sell across borders.
The technical description is [The grouped country list](../../../features/6.0/country-groups.md).

## Contents

1. [Why the country list matters](#why-the-country-list-matters)
2. [What customers see](#what-customers-see)
3. [Putting your markets first](#putting-your-markets-first)
4. [Autofill and fewer errors](#autofill-and-fewer-errors)
5. [Selling to several countries](#selling-to-several-countries)
6. [Restricting where you sell](#restricting-where-you-sell)
7. [Checklist](#checklist)

## Why the country list matters

The country a customer chooses can decide the VAT rate ([VAT rules and VAT types](vat-rules-and-vat-types.md)) and
the address on the order. A long A to Z list slows customers down, and a wrong pick produces an order you have to
correct by hand. A short ranked list at the top makes the common case one click.

## What customers see

Every country select shows two groups ([The grouped country list](../../../features/6.0/country-groups.md)):

1. **Most used countries**, a ranked list.
2. **All countries**, every country of `settings/country.ini`, sorted A to Z by the translated name in the reader's
   language.

The shop's account form (`shop/userregister`) uses this list in the standard, admin, ezwebin, ezdemo and media designs,
and so do the VAT rules screen and the country field of content and forms. The groups are native browser groups, so
they work with the keyboard, screen readers and every browser's own picker.

The shipped ranking has 37 countries in the order North America, South America, Europe, the United Kingdom, Asia,
New Zealand and Australia, and Africa, with the United States, Canada and Mexico first (same page, "The shipped
ranking").

## Putting your markets first

The ranking is a setting, `settings/countrygroups.ini`, overridable for the whole installation or per siteaccess. A
store that sells mostly in German-speaking countries can list Germany, Austria and Switzerland first; the feature page
shows exactly that example. You can also:

- add more groups before "All countries", each with its own label (`GroupList[]`, `GroupName_<group>`);
- decide whether ranked countries are repeated in the A to Z list (`RepeatInAllCountries`);
- give each site of a multi-site installation its own ranking, through siteaccess settings
  ([Multisite setup](../../creators/website-creators/multisite-setup.md)).

> **Tip**
> Rank the countries you actually ship to, in the order of your order volume. Look at the countries orders came from
> on the Store dashboard ("How your shop works" lists them) and update the ranking once a year.

## Autofill and fewer errors

Browsers fill addresses from the customer's profile. The grouped list was built to make that work
([The grouped country list](../../../features/6.0/country-groups.md#autofill)):

- a customer with a United States address no longer ends up with "United States Minor Outlying Islands";
- the shop's select starts with an empty "Choose a country" option instead of silently preselecting a country;
- country names are no longer shortened, so autofill finds them;
- the media design's account form names all address fields, so a browser can fill the whole address.

Fewer wrong countries means fewer wrong VAT amounts and fewer returned parcels.

## Selling to several countries

Country is one decision among several. A store selling abroad usually also decides:

| Topic | Read |
|---|---|
| VAT per country | [VAT rules and VAT types](vat-rules-and-vat-types.md) |
| Prices in other currencies | [Prices and currencies](prices-and-currencies.md) |
| Shipping cost per zone | [Shipping and handling](shipping-and-handling.md) |
| Store pages in other languages | Multilingual planning |
| Separate sites per country | [Multi-brand planning](../multisite/multi-brand-planning.md) |
| Right-to-left languages | Right-to-left markets |

## Restricting where you sell

The country list itself lists every country of `country.ini`; the ranking only changes the order. A store that ships
only to some countries has these options:

- say so clearly on the product pages, the basket and the terms;
- check the country at the confirmation step with a workflow event on `before_shop_confirmorder`
  ([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#workflow-triggers));
- use a custom shop account handler whose form offers only your countries (`shopaccount.ini [AccountSettings]`,
  [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)).

The second and third options are developer work. The shipped shop does not have a "ship to these countries only"
switch.

## Checklist

- [ ] The ranked group lists your markets in order of volume.
- [ ] Each site of a multi-site installation has the ranking that fits its market.
- [ ] A test with browser autofill picks the right country.
- [ ] VAT rules exist for every country where the rate differs.
- [ ] Customers can see where you ship before they reach checkout.

## Related pages

- [The grouped country list](../../../features/6.0/country-groups.md)
- [VAT rules and VAT types](vat-rules-and-vat-types.md)
- [Shipping and handling](shipping-and-handling.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
