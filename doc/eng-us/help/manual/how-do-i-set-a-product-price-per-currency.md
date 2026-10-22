---
title: How do I set a product's price in each currency?
audience: editor
category: manual
order: 175
summary: In a multi-currency price field, the price list shows one row per active currency; Auto prices follow the exchange rate, Set custom price fixes a price in one currency, and Remove selected turns a custom price back into an automatic one.
keywords: multi-currency price, multiprice, custom price, auto price, set custom price, currency, exchange rate, base currency, VAT type, price inc. VAT, price ex. VAT
type: help
---
# How do I set a product's price in each currency?

A product whose class uses a **multi-currency price** field has a price per currency. Most of them are worked out
automatically from one price and the exchange rates; you fix a price by hand where a round amount or a market price
matters. A plain price field has one price only and does not take part in currencies.

## Who can do this

Editors who may edit the product. Currencies and rates are managed in the **Store** tab with `shop/administrate`.

## Steps

1. Edit the product and go to its price field.
   The price list shows each active currency with its value; automatic prices are marked **Auto**.
2. Choose the **VAT type**, and whether the prices you enter include VAT (**Price inc. VAT**) or not
   (**Price ex. VAT**).
3. To fix a price in a currency, choose it under **Select currency**, click **Set custom price** and enter the amount.
4. To change a fixed price, overwrite its value.
5. To let a currency follow the rates again, tick its custom price and click **Remove selected**.
6. Click **Send for publishing**.

## What can go wrong

- **A currency shows 0.00.** The currency is new and the automatic prices have not been calculated. In **Store** >
  **Currencies**, click **Update autoprices** (after **Update auto rates** if the rates come from a provider).
- **A currency is missing from the list.** It is inactive. Activate it in **Store** > **Currencies**.
- **Automatic prices changed by themselves.** They follow the exchange rate whenever the rates and automatic prices
  are updated. Set a custom price where the amount must stay.
- **All automatic prices changed after a removal.** They are calculated from a custom price; removing that price
  changes what they follow. Keep at least one custom price, usually in the shop's main currency.
- **A basket mixes products with plain and multi-currency prices.** Keep all products of a shop on the same kind of
  price field.

## Related pages

- [How do I add a currency to the store?](how-do-i-add-a-currency.md)
- [Prices and currencies](../customers/commerce/prices-and-currencies.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
