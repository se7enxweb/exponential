---
title: Exchange rate handlers
audience: ecommerce, app-creator, developer
category: e-commerce
order: 66
summary: Where an Exponential store gets its exchange rates - the eZECB handler, the eZExchangeRatesUpdateHandler base class with its rate list and error codes, writing a handler for another rate source, registration, and scheduling rate and price updates.
keywords: exchange rates, exchange rate handler, eZExchangeRatesUpdateHandler, eZECB, eZECBHandler, requestRates, rateList, baseCurrency, ExchangeRatesUpdateHandler, BaseCurrency, Update autorates, Update autoprices, multi-currency
type: help
---
# Exchange rate handlers

This page is for developers of stores that sell in several currencies with automatic prices
([Price fields compared](../catalog/price-fields-compared.md#how-ezmultiprice-handles-currencies)). Rates are fetched
by an exchange rates update handler when someone presses **Update autorates** in **Store > Currencies**. The kernel
ships one handler, for the European Central Bank; this page shows how to use another source.

## Contents

1. [The shipped handler](#the-shipped-handler)
2. [The base class](#the-base-class)
3. [Writing a handler](#writing-a-handler)
4. [Registering it](#registering-it)
5. [Keeping rates and prices current](#keeping-rates-and-prices-current)

## The shipped handler

`eZECBHandler` (`kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php`) reads the server, port and address
from `shop.ini [ECBExchangeRatesSettings]` and the daily rates file there. Its rates are relative to the euro, matching
the shipped `[ExchangeRatesSettings] BaseCurrency=EUR` ([shop.ini walkthrough](../settings/shop-ini-walkthrough.md#exchangeratessettings)).

## The base class

`eZExchangeRatesUpdateHandler` (`kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php`):

| Member | Meaning |
|---|---|
| `initialize( $params )` | Reads settings; the base reads `BaseCurrency` |
| `requestRates()` | Fetches rates and stores them with `setRateList()`; returns a status |
| `rateList()`, `setRateList()` | The rates, keyed by currency code |
| `baseCurrency()`, `setBaseCurrency()` | The currency the rates are relative to |
| `OK`, `CANT_CREATE_HANDLER`, `FAILED`, `EMPTY_RATE_LIST`, `UNKNOWN_BASE_CURRENCY`, `INVALID_BASE_CROSS_RATE` | Status codes |

## Writing a handler

1. Extend `eZExchangeRatesUpdateHandler`; read your source's settings in `initialize()` after calling the parent.
2. In `requestRates()`, fetch the rates, build an array `currency code => rate` relative to your base currency, call
   `setRateList()` and return `self::OK`, or a failure code.
3. Fail safely: on a network error or an empty answer, return a failure code and leave the old rates in place, so
   prices are never converted with nonsense.

The RAD handler wizard has an exchange rate handler recipe ([Developer path](../roles/developer-path.md#generators-that-save-time)).

## Registering it

`eZExchangeRatesUpdateHandler::create()` lowercases the name and includes `<directory>/<name>/<name>handler.php`, class
`<name>handler`:

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[ExchangeRatesSettings]
ExchangeRatesUpdateHandler=mystorerates
ExtensionDirectories[]=mystore_shop
BaseCurrency=EUR
```

File: `extension/mystore_shop/exchangeratehandlers/mystorerates/mystorerateshandler.php`, class `mystoreratesHandler`.

## Keeping rates and prices current

Two separate steps update a multi-currency store: **Update autorates** fetches rates, **Update autoprices** recalculates
automatic prices (`eZShopFunctions::updateAutoRates()`, `updateAutoprices()`). Neither runs on its own in the shipped
settings. Either make it part of a routine ([Operations calendar](../operations/operations-calendar.md)) or write a
cron part that calls both ([Writing a cronjob part](../../creators/app-creators/writing-a-cronjob-part.md)). Custom
prices are never touched by either step.

## Related pages

- [Extending the shop](README.md)
- [Prices and currencies (running your store)](../../customers/commerce/prices-and-currencies.md)
- [Prices and currencies (businesses)](../../businesses/stores/prices-and-currencies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
