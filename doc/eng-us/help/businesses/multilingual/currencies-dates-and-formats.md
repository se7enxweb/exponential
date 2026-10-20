---
title: Currencies, dates and formats
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 40
summary: How Exponential presents dates, numbers and amounts the way each market expects, and what the built-in shop does with currencies, exchange rates, rounding, VAT by country and country lists - with the questions to settle for every market.
keywords: currency, currencies, dates, date format, numbers, locale, ezmultiprice, exchange rates, PreferredCurrency, rounding, VAT, country, international store, business
type: help
---
# Currencies, dates and formats

This page is for owners and managers of stores and organizations that address customers in several countries. A
German customer expects `24.12.2026`, an American one `12/24/2026`; a Japanese price has no cents. This page explains
which formats Exponential takes care of through the locale of each site, what the built-in shop does with several
currencies, and which questions remain for your tax adviser and your payment provider.

## Contents

1. [Formats follow the site's locale](#formats-follow-the-sites-locale)
2. [Examples from the shipped locales](#examples-from-the-shipped-locales)
3. [Prices in several currencies](#prices-in-several-currencies)
4. [VAT and the customer's country](#vat-and-the-customers-country)
5. [Country lists](#country-lists)
6. [Questions to settle for every market](#questions-to-settle-for-every-market)

## Formats follow the site's locale

Each locale file names, among other things, the date and time formats, day and month names, the first day of the
week, and the number and currency formats of its country
([Languages, section 1](../../../languages.md#1-locales-translations-content-languages-and-siteaccesses)). A site
address formats with the locale it is set to (`site.ini [RegionalSettings] Locale`), so a German siteaccess shows
German dates and a US siteaccess US dates without any template change.

The groups a locale file holds, in brief
([Languages, section 7](../../../languages.md#7-adding-a-locale)):

| Group | What it decides |
|---|---|
| `[Currency]` | Symbol, name and ISO 4217 code of the country's currency; decimal and thousands separators; how many decimals (none for currencies without a minor unit, such as JPY, KRW, VND, IRR); where the symbol and sign go |
| `[Numbers]` | Number format |
| `[DateTime]` | Long and short formats of dates and times; whether the week starts on Monday |
| Month and day names | In the form used inside a date |
| `[CountryNames]` | Country names in the language |

Templates format values through the locale operators such as `l10n` and `datetime`
([Locale operators](../../template-operators/locale-operators.md)).

## Examples from the shipped locales

Values read from the files in `share/locale/`:

| Locale | Short date pattern | Currency code and symbol | Decimals | Week starts Monday |
|---|---|---|---|---|
| `eng-US` | `%m/%d/%Y` (month first) | USD, `$` | 2 | no |
| `ger-DE` | `%d.%m.%Y` | EUR, `€` | 2 | yes |
| `jpn-JP` | `%Y.%m.%d` | `円` | 0 | no |

> **Note**
> The currency in a locale is the currency of the **country**. It decides how amounts are written, not which
> currency your shop charges. The shop's currencies are configured separately, below.

## Prices in several currencies

From [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#currencies-and-rounding):

| What | How |
|---|---|
| One price per product | The `ezprice` datatype |
| One price per currency | The `ezmultiprice` datatype |
| The shop's currency | `shop.ini [CurrencySettings] PreferredCurrency` (shipped: `USD`) |
| Exchange rates for `ezmultiprice` | `[ExchangeRatesSettings]`: base currency `EUR` and the update handler `eZECB` as shipped |
| Rounding | `[MathSettings]`: `round`, precision `2` as shipped |
| Managing currencies | `shop/currencylist` |
| Letting visitors choose | `shop/setpreferredcurrency` |

What this means for a business:

- **Fixed prices per market** (for example a "psychological" price of 19.90 in each currency): use one price per
  currency, entered by your team.
- **Converted prices**: let exchange rates calculate prices from a base currency, and accept that the result changes
  with the rate.
- **Rounding** is configured once for the shop; check its effect on your price points before launch.

Whatever the shop displays, the currency actually charged is decided together with your
payment provider; see [Payment gateways for businesses](../stores/payment-gateways-for-businesses.md) and
[Prices and currencies](../stores/prices-and-currencies.md).

## VAT and the customer's country

The shop's VAT rules decide which VAT type applies by customer country and product category; named VAT types
(standard, reduced, zero) are managed under **Store > VAT types**
([VAT](../../creators/website-creators/products-prices-and-vat.md#vat)). The same page is explicit: "VAT law differs by
country. The shop calculates what you configure; whether that is correct for your business is a question for your
tax adviser." The [stores section](../stores/vat-rules-and-vat-types.md) explains it for businesses.

## Country lists

Every country select shows two groups: a ranked list of the most used countries, then all countries A to Z by their
translated name ([The grouped country list](../../../features/6.0/country-groups.md)). It is used by country fields,
the shop's account form and the VAT rules. For international stores, review the ranked list so your main markets are
on top; the [country groups page for businesses](../stores/country-groups-and-regions.md) explains how.

## Questions to settle for every market

- [ ] Which locale does the market's siteaccess use (language and country)?
- [ ] Do dates, numbers and prices look right on a product page, in the basket and in the order confirmation mail?
- [ ] Which currency is charged, and does the payment provider support it?
- [ ] Fixed prices per currency, or converted prices? Who updates them?
- [ ] Which VAT rules apply, confirmed by a tax adviser?
- [ ] Are shipping costs and delivery countries defined for this market ([Shipping and handling](../stores/shipping-and-handling.md))?
- [ ] Are terms, returns policy and legal notices translated and checked for this market?

## Related pages

- [Languages and locales](languages-and-locales.md)
- [Multilingual checklist](multilingual-checklist.md)
- [Stores](../stores/README.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Languages in Exponential](../../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
