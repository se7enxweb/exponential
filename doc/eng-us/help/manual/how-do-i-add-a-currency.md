---
title: How do I add a currency to the store?
audience: admin, customer
category: manual
order: 150
summary: Click New currency in Store > Currencies, enter the code, the symbol and the formatting locale, then make it active and give it a rate; products with multi-currency prices then show a price in it.
keywords: currency, currencies, exchange rate, custom rate, auto rate, factor, locale, multi-currency, shop/currencylist, preferred currency
context: shop/currencylist
type: help
---
# How do I add a currency to the store?

Create the currency, make it active and give it a rate. Products whose price field is a multi-currency price then
show a price in it.

## Who can do this

Users whose role has `shop/administrate`.

## Steps

1. Click **Store**, then **Currencies**.
2. Click **New currency**.
3. Enter the **Currency code** (for example `EUR`) and the **Currency symbol**, and choose the **Formatting locale**.
4. Optionally enter a **Custom rate** and a **Rate factor**, then click **Create**.
5. In the list, set the currency's **Status** to **Active** and click **Apply changes**.
6. If the rates come from a rate provider, click **Update auto rates**, then **Update autoprices**.
7. Under **Preferred currency** in the left menu, choose the currency visitors see first.

![The Currencies page of the Store tab with the button New currency](images/store-currencies.webp)

## What can go wrong

- **Products show no price in the new currency.** Their price field is a simple price, not a multi-currency price, or
  the automatic prices were not updated. Click **Update autoprices**.
- **The amounts are formatted wrongly.** Choose the right **Formatting locale** for the currency.

## Related pages

- [Prices and currencies](../customers/commerce/prices-and-currencies.md)
- [User manual](README.md)
