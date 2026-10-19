---
title: VAT checklist
audience: customer, admin, businesses
category: customer-success
order: 60
summary: A checklist to review the VAT setup of an Exponential store once a quarter, before a rate change and at the end of the year - VAT types, rules, product categories, test orders and the records your adviser needs. Template, not tax advice.
keywords: VAT checklist, VAT review, quarterly VAT check, VAT types, VAT rules, tax adviser, test order, year end, not tax advice
type: help
---
# VAT checklist

This page is a template checklist for the owner and the bookkeeper. Copy it, fill it in each time, and keep the
filled copies with your VAT records. The explanations are on [VAT types and rules](vat-types-and-rules.md).

> **Warning**
> Template, not tax advice. The checklist helps you check that the store charges what you decided. What you must
> charge is for your tax adviser to confirm.

## When to use it

| Occasion | Sections to do |
|---|---|
| Every quarter | 1 to 4 |
| Before a rate change takes effect | 1, 2, 4 |
| When you start selling to a new country or a new product group | 2, 3, 4 |
| At the end of the year | All |
| After an Exponential update or a change by your partner | 4 |

## The checklist

```text
VAT CHECKLIST - [store name]      Date: __________   Done by: __________

1. VAT TYPES  (Store > VAT types, shop/vattype)
[ ] Every VAT type is named after its meaning and percentage
[ ] Every percentage matches what the adviser confirmed (list below)
[ ] No VAT type is unused and confusing; any 0 percent type is intended
[ ] The Store dashboard's "Next steps" shows no unexpected 0 percent VAT type

   Name                      | Percentage | Confirmed by adviser on
   --------------------------|------------|------------------------
                             |            |
                             |            |

2. VAT RULES  (Store > VAT rules, shop/vatrules)
[ ] A general rule exists for "any country, any category"
[ ] Each country-specific rule is still wanted and confirmed
[ ] Each category-specific rule names an existing product category
[ ] No two rules say different things for the same country and category

3. PRODUCTS AND CATEGORIES
[ ] Every price uses a fixed VAT type or the dynamic type, consistently per class
[ ] With rules: every product has its product category set
[ ] New products since the last check have the right VAT type or category
[ ] Shipping cost and its VAT treatment confirmed with the adviser

4. TEST ORDERS
[ ] Test order 1: country ______, category ______ -> VAT shown ______  expected ______
[ ] Test order 2: country ______, category ______ -> VAT shown ______  expected ______
[ ] Totals with and without VAT in the order list match the confirmation page
[ ] Test orders handled as agreed (canceled or removed), so they are not counted

5. RECORDS (year end)
[ ] Month figures from Store > Product statistics saved for each month
[ ] Changes to VAT types and rules this year listed (audit trail, commerce channel)
[ ] Questions for the adviser written down

Signed off: __________________   Next check due: __________
```

## Where each item is checked

| Item | Where | Source |
|---|---|---|
| VAT types | **Store > VAT types** | `shop/vattype`, policy `shop/setup` (`kernel/shop/module.php`) |
| VAT rules and categories | **Store > VAT rules**, **Store > Product categories** | `shop/vatrules`, `shop/productcategories` |
| 0 percent warning | Store dashboard, "Next steps" | [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md) |
| Dynamic VAT type name | `settings/shop.ini [VATSettings] DynamicVatTypeName` | shipped "Determined by VAT charging rules" |
| Totals with and without VAT | **Store > Orders**, sortable columns | [Order list sorting](../../../features/6.0/order-list-sorting.md) |
| Month figures | **Store > Product statistics** (`shop/statistics`) | [Store reports](store-reports.md) |
| Record of changes | The audit trail, channel `commerce` | [The audit trail](../../../features/6.0/audit-trail.md) |

## If something is wrong

- A wrong percentage: correct the VAT type at once and tell your adviser which orders were placed with the wrong
  rate. Orders keep the VAT they were placed with; correcting an order is a bookkeeping matter, not a shop setting.
- A missing rule: add it and place a test order.
- A product without category: set it and check the product's price on the confirmation page.

## Related pages

- [VAT types and rules](vat-types-and-rules.md)
- [VAT rules and VAT types for businesses](../../businesses/stores/vat-rules-and-vat-types.md)
- [Store monthly checklist](store-monthly-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
