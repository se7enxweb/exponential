---
title: How do I give a group of customers a discount?
audience: admin, customer
category: manual
order: 140
summary: Create a discount group in Store > Discounts, add a discount rule with a percent and the products it covers, then add the users or user groups who get it.
keywords: discount, discount group, discount rule, percent, customers, shop/discountgroup, member prices
context: shop/discountgroup
type: help
---
# How do I give a group of customers a discount?

A discount group has rules (a percent off, for some or all products) and customers (users or user groups). Everyone
in the group sees the reduced prices when signed in.

## Who can do this

Users whose role has `shop/administrate`.

## Steps

1. Click **Store**, then **Discounts**.
   The page **Discount groups** opens.
2. Click **New discount group**, enter a name (for example "Members") and save it.
3. Click the group's name to open it.
4. Under **Discount rules**, click **New discount rule**.
5. Enter a **Name** and the **Discount percent**. Under **Product types** choose **Any** or the classes the discount
   covers, or list **Individual products** with **Add products**. Click **OK**.
6. Under **Customers (users and user groups)**, click **Add customers** and choose the users or user groups.
7. Sign in as a test member and check a product price.

## What can go wrong

- **The customer still sees the full price.** They are not signed in, or not in the group. Discounts apply only to
  signed-in customers of the group.
- **A customer is in two groups.** They get the higher discount of a product. Keep groups apart where that matters.
- **The discount should end on a date.** Discount groups have no end date. Remove the customers or the rule on that
  day.

## Related pages

- [Discounts](../customers/commerce/discounts.md)
- [Prices and currencies](../customers/commerce/prices-and-currencies.md)
- [User manual](README.md)
