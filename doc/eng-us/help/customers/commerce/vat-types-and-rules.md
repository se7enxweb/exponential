---
title: VAT types and rules
audience: customer, admin, businesses
category: customer-success
order: 50
summary: Looking after VAT in a running Exponential store - who owns the VAT setup, what to do when a rate changes, adding a rule for a new country or product group, removing a VAT type safely, and testing. Not tax advice.
keywords: VAT, VAT types, VAT rules, rate change, sales tax, product category, customer country, shop/vattype, shop/vatrules, tax adviser, not tax advice
type: help
---
# VAT types and rules

This page is for the owner and whoever does the bookkeeping. It is about keeping the VAT setup of a running store
right: what to do when a rate changes, when you start selling to a new country, or when a new product group has a
different rate. How the shop calculates VAT, and how it picks a rule, is explained on
[VAT rules and VAT types for businesses](../../businesses/stores/vat-rules-and-vat-types.md).

> **Warning**
> This page is not tax or legal advice. VAT and sales tax law differ by country and change over time. The shop
> charges what you configure; whether that is correct for your business is for your tax adviser to confirm.

## The two tools, in one paragraph

A **VAT type** is a named percentage, managed under **Store > VAT types** (`shop/vattype`). A **VAT rule** says which
VAT type applies to customers of a country and products of a category, managed under **Store > VAT rules**
(`shop/vatrules`), with product categories in `shop/productcategories`. All three need the `shop/setup` policy
(`kernel/shop/module.php`). A price uses either a fixed VAT type, or the dynamic type named in
`settings/shop.ini [VATSettings] DynamicVatTypeName` (shipped: "Determined by VAT charging rules"), which hands the
choice to the rules.

## Who owns VAT

| Task | Owner of the task |
|---|---|
| Deciding rates and where they apply | Owner, with the tax adviser |
| Entering VAT types and rules | Owner, or one trusted person with `shop/setup` |
| Setting a product's VAT type or category | Product editors, following a written list |
| Checking test orders after a change | Store manager |
| Keeping a record of what changed and when | Owner; the audit trail records VAT changes in its `commerce` channel ([The audit trail](../../../features/6.0/audit-trail.md)) |

Keep `shop/setup` to as few people as possible. One wrong percentage changes the price of every product that uses
the type.

## When a rate changes

1. Your tax adviser confirms the new rate and the date it applies from.
2. Note which VAT type carries the rate. Name types after their meaning and percentage, so this is obvious.
3. On the day, change the percentage of that VAT type. Every price that uses the type follows; existing orders keep
   the VAT they were placed with, because each order line stores its own price, VAT value and discount
   (table `ezproductcollection_item` in `kernel/sql/mysql/kernel_schema.sql`).
4. Place a test order (below) and check the confirmation page.
5. Update any page or newsletter that quotes prices including VAT.

> **Tip**
> If prices are entered including VAT, a rate change also changes your net income per product unless you change the
> prices too. Decide with your bookkeeper which of the two you keep fixed.

## When you start selling to a new country

- [ ] Your adviser confirms what you must charge customers there.
- [ ] If the rate differs from your general rule: add a VAT rule for that country, with "any category" or the
  categories concerned. A rule naming the country exactly beats an "any country" rule
  (`kernel/classes/vathandlers/ezdefaultvathandler.php`).
- [ ] The country can be chosen in the shop's forms; the country list is grouped with the most used countries first
  ([Country groups and regions](../../businesses/stores/country-groups-and-regions.md)).
- [ ] A test order from that country shows the expected VAT.

## When a product group has its own rate

- [ ] A product category exists for the group (`shop/productcategories`).
- [ ] The product class has a category field and `shop.ini [VATSettings] ProductCategoryAttribute` names it; this is a
  setting your partner makes.
- [ ] Every product of the group has the category set.
- [ ] A VAT rule names the category with the right VAT type.

## Removing a VAT type

When you remove a VAT type, the VAT type page first lists the VAT rules and products that use it, and asks you to
choose another type for them. It refuses to remove a type that a product class uses as its default, and it refuses
to remove the last remaining type; the message suggests keeping one type at zero percent instead
(`kernel/private/classes/views/shop/vattype.php`).

The Store dashboard warns when a 0 percent VAT type is in use, so a zero rate is never an accident
([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)).

## Testing after any VAT change

Use two test orders, ideally from two countries and with two product categories
([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)):

- [ ] The confirmation page shows the VAT you expect on each line.
- [ ] Shipping is treated the way your adviser told you.
- [ ] The order list shows totals with and without VAT that match
  ([Order list sorting](../../../features/6.0/order-list-sorting.md)).
- [ ] Cancel or remove the test orders the way your team agreed, so they do not count as revenue.

The recurring version of these checks is the [VAT checklist](vat-checklist.md).

## Related pages

- [VAT checklist](vat-checklist.md)
- [VAT rules and VAT types for businesses](../../businesses/stores/vat-rules-and-vat-types.md)
- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Prices and currencies](prices-and-currencies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
