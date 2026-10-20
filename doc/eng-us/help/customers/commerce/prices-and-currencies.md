---
title: Prices and currencies
audience: customer, admin, businesses
category: customer-success
order: 40
summary: Changing prices, currencies and exchange rates in a running Exponential store - who may do it, how to plan a price change, keeping automatic prices and rates up to date, and the checks after every change.
keywords: price change, prices, currencies, exchange rates, automatic prices, custom prices, rounding, preferred currency, currency list, multi-currency
type: help
---
# Prices and currencies

This page is for owners and store managers who change prices or sell in several currencies. It covers the running
store: planning a price change, keeping exchange rates and automatic prices current, and checking the result. How
prices and currencies work, and what to decide before launch, is on
[Prices and currencies for businesses](../../businesses/stores/prices-and-currencies.md).

## Where prices live

A price is a field of the product, so a price change is a content edit of that product. The currency settings are
separate: the currency list (**Store**, `shop/currencylist`) needs the `shop/setup` policy (`kernel/shop/module.php`).

| What changes | Who usually does it | Where |
|---|---|---|
| The price of one product | Product editor or owner | The product's edit form ([Creating and editing content](../../editing-content.md)) |
| Many prices at once | Partner or administrator, or editors one by one | There is no bulk price editor in the shop views |
| Which currencies are active | Owner | `shop/currencylist`, `shop/editcurrency` |
| Exchange rates and automatic prices | Owner or administrator | The currency list's update buttons |
| The shop currency, rounding | Partner, in an override of `settings/shop.ini` | `[CurrencySettings]`, `[MathSettings]` |

## Planning a price change

1. **List** the products that change, with old and new price. **Store > Products overview** (`shop/productsoverview`)
   shows the products of a class with their prices and helps to build the list.
2. **Decide the moment.** Prices change the moment the edited product is published. For a change at midnight, use
   scheduled publication if your site has it ([Workflows](../../../guides/workflows.md)).
3. **Edit** the products. Drafts let several people prepare the changes without customers seeing them
   ([Publishing and versions](../../publishing-and-versions.md)).
4. **Check** the result (below).
5. **Tell** the team and, if needed, your customers.

> **Note**
> A price change does not change orders that were already placed: each order keeps its own lines and totals.

## Several currencies

With `ezmultiprice`, each product carries a price per currency, either a **custom price** set by hand or an
**automatic price** calculated from the base price and the exchange rate
([Prices and currencies for businesses](../../businesses/stores/prices-and-currencies.md)).

| Setting in `settings/shop.ini` | Shipped value | What it means for you |
|---|---|---|
| `[CurrencySettings] PreferredCurrency` | `USD` | The shop's currency; your builder sets yours in an override |
| `[ExchangeRatesSettings] ExchangeRatesUpdateHandler` | `eZECB` | Rates come from the European Central Bank |
| `[ExchangeRatesSettings] BaseCurrency` | `EUR` | The currency the rates are expressed against |
| `[MathSettings] RoundingType` | `round` | How converted amounts are rounded |
| `[MathSettings] RoundingPrecision` | `2` | Digits after the decimal point |

The currency list has **Update auto rates** and **Update autoprices** actions
(`kernel/private/classes/views/shop/currencylist.php`).

### A routine for multi-currency stores

| How often | Task |
|---|---|
| Weekly, or before a campaign | Update auto rates, then update autoprices |
| Monthly | Compare a few converted prices with what your payment provider settles |
| When a market matters more | Give that currency custom prices |
| Yearly | Check that every active currency is one your payment provider accepts |

> **Tip**
> Reference rates are not your bank's rates. A currency can carry a custom rate and a rate factor
> (`kernel/shop/classes/ezcurrencydata.php`), which some stores use to add a margin for currency risk. Decide this
> with whoever does your bookkeeping.

## After every change

- [ ] Open two or three changed products on the public site: the new price shows.
- [ ] Add one to the basket: the basket and confirmation page show the new price and the right VAT.
- [ ] In another currency, if you sell in several: the price is the one you expect.
- [ ] The Store dashboard does not report products without a price
  ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).
- [ ] Campaign pages and newsletters that quote prices are updated.

If the public site still shows the old price, the page may come from a cache. Ask your administrator before clearing
caches during busy hours ([Caches](../../../guides/caches.md)).

## Related pages

- [Prices and currencies for businesses](../../businesses/stores/prices-and-currencies.md)
- [VAT types and rules](vat-types-and-rules.md)
- [Discounts](discounts.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
