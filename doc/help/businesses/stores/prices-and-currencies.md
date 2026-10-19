---
title: Prices and currencies
audience: businesses, admin, website-creator
category: business-solutions
order: 40
summary: How an Exponential store stores prices, sells in one or several currencies, converts with exchange rates and rounds amounts - and the decisions a business makes about it.
keywords: price, currency, multi-currency, ezprice, ezmultiprice, exchange rates, ECB, rounding, preferred currency, currency list
type: help
---
# Prices and currencies

This page is for business owners who decide how their store shows prices and in which currencies. It explains the two
price fields, how exchange rates and rounding work, and what to decide before the first product is entered. The
settings are described in [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md).

## Contents

1. [Two kinds of price](#two-kinds-of-price)
2. [The shop currency](#the-shop-currency)
3. [Selling in several currencies](#selling-in-several-currencies)
4. [Exchange rates](#exchange-rates)
5. [Rounding](#rounding)
6. [Prices and VAT](#prices-and-vat)
7. [Decisions and checklist](#decisions-and-checklist)

## Two kinds of price

| Field | What it holds | Fits |
|---|---|---|
| `ezprice` | One price, with a VAT type and the choice "price includes VAT" or not | Stores that sell in one currency |
| `ezmultiprice` | One price per currency | Stores that show prices in several currencies |

Source: [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#price-datatypes). The
price field is chosen per product class. Switching later means editing every product, so decide before mass entry.

## The shop currency

`settings/shop.ini` sets the shop's currency in `[CurrencySettings] PreferredCurrency`; the shipped value is `USD`.
Your builder sets your currency in an override file, never in the shipped file (the comment at the top of
`settings/shop.ini` says so).

Currencies are managed in the administration under **Store** (`shop/currencylist`, which needs the `shop/setup`
policy). Each currency has a code, a symbol, a locale for formatting, and an active or inactive status
(`kernel/shop/classes/ezcurrencydata.php`).

## Selling in several currencies

With `ezmultiprice`, a visitor can choose their currency (`shop/setpreferredcurrency`), and the product shows the price
for that currency. For each currency a product can carry:

- a **custom price** that you set by hand, for markets where you want a fixed, attractive price; or
- an **automatic price** calculated from the base price and the exchange rate.

The currency list has the actions **Update autoprices** and **Update auto rates**
(`kernel/private/classes/views/shop/currencylist.php`).

> **Tip**
> Set custom prices in the currencies of your main markets and let the others be automatic. Customers in your main
> markets then see round prices, and the rest still see their own currency.

Currency choice is one part of selling to other countries; formats for dates and numbers are covered in
[Currencies, dates and formats](../multilingual/currencies-dates-and-formats.md).

## Exchange rates

| Setting in `shop.ini` | Shipped value | Meaning |
|---|---|---|
| `[ExchangeRatesSettings] ExchangeRatesUpdateHandler` | `eZECB` | Rates come from the European Central Bank |
| `[ExchangeRatesSettings] BaseCurrency` | `EUR` | The currency the rates are expressed against |
| `[ECBExchangeRatesSettings] RatesURI` | `stats/eurofxref/eurofxref-daily.xml` | The daily reference rates file |

Each currency can also carry a **custom rate** that overrides the automatic one, and a **rate factor**
(`custom_rate_value`, `rate_factor` in `kernel/shop/classes/ezcurrencydata.php`). A business can use the factor to add a
margin for currency risk.

Another rate source can be plugged in as a handler in an extension; the comments in `settings/shop.ini` describe the
class it must implement (`eZExchangeRatesUpdateHandler`). This is developer work.

> **Note**
> Reference rates are not the rates your payment provider or bank uses. Check with them how a payment in another
> currency is settled before you rely on automatic prices.

## Rounding

| Setting in `shop.ini` | Shipped | Meaning |
|---|---|---|
| `[MathSettings] RoundingType` | `round` | `none`, `round`, `ceil` or `floor` |
| `[MathSettings] RoundingPrecision` | `2` | Digits kept after the decimal point |
| `[MathSettings] RoundingTarget` | `false` | Round to a target ending |

The comment in `settings/shop.ini` gives an example: 89.468543 rounded with precision 2 is 89.47; with a target of 5 it
becomes 89.45, and with a target of 99 it becomes 89.99. Rounding targets are how a store produces price endings such
as .99 for converted prices.

`[MathSettings] MathHandler` chooses between PHP floating point (`eZPHPMath`, shipped) and the bcmath extension
(`eZBCMath`).

## Prices and VAT

With `ezprice`, each price says whether it includes VAT and which VAT type applies. A store selling to consumers
usually enters prices including VAT; a store selling to businesses often enters them without. The confirmation page
shows the VAT, and the order list shows totals both with and without VAT
([Order list sorting](../../../features/6.0/order-list-sorting.md)). The tax side is on
[VAT rules and VAT types](vat-rules-and-vat-types.md).

## Decisions and checklist

- [ ] One currency (`ezprice`) or several (`ezmultiprice`)?
- [ ] The shop currency, set in an override of `[CurrencySettings] PreferredCurrency`.
- [ ] Which currencies are active, and which get custom prices.
- [ ] Rate source: the shipped ECB rates, custom rates, or a handler of your own.
- [ ] Rounding type, precision and target endings.
- [ ] Prices entered with or without VAT.
- [ ] Your payment provider accepts every currency you show.

## Related pages

- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [VAT rules and VAT types](vat-rules-and-vat-types.md)
- [Payment gateways for businesses](payment-gateways-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
