---
title: Store planning for small businesses
audience: businesses, project-manager, website-creator
category: business-solutions
order: 10
summary: The decisions a small business makes before an Exponential store is set up - what to sell, where, in which currencies, how customers pay and receive goods, and who runs the orders.
keywords: store planning, webshop planning, small business, e-commerce plan, products, VAT, shipping, payment, roles, requirements
type: help
---
# Store planning for small businesses

This page is for owners and managers of small businesses who plan an online store on Exponential. It lists the
decisions to take before anyone configures the shop, explains why each one matters, and points to the page that
covers it. Use it as the agenda of your first planning meeting with whoever builds the store.

## Contents

1. [Why plan first](#why-plan-first)
2. [The ten decisions](#the-ten-decisions)
3. [People and roles](#people-and-roles)
4. [What the shop gives you and what to add](#what-the-shop-gives-you-and-what-to-add)
5. [A planning worksheet](#a-planning-worksheet)
6. [After planning](#after-planning)

## Why plan first

In Exponential most store choices are settings, roles and content classes, not code. Changing them later is possible,
but some changes touch every order or product: switching from one currency to several, introducing VAT rules by
country, or changing how customer data is collected at checkout. Deciding these early keeps the setup short and the
first test orders meaningful.

## The ten decisions

| # | Decision | Why it matters | Read |
|---|---|---|---|
| 1 | What you sell: physical goods, services, files | Decides shipping, options and downloads | [Product classes and catalog](product-classes-and-catalog.md) |
| 2 | How many products and how they are grouped | Decides folders, menus and search | [Store search and navigation](store-search-and-navigation.md) |
| 3 | Options per product (size, color) | Decides the price fields and option fields | [Product classes and catalog](product-classes-and-catalog.md) |
| 4 | One currency or several | Decides `ezprice` versus `ezmultiprice` | [Prices and currencies](prices-and-currencies.md) |
| 5 | Which countries you sell to | Decides VAT rules and the country list | [Country groups and regions](country-groups-and-regions.md) |
| 6 | How tax is charged | Decides VAT types and rules | [VAT rules and VAT types](vat-rules-and-vat-types.md) |
| 7 | How goods reach customers | Decides the shipping workflow or handler | [Shipping and handling](shipping-and-handling.md) |
| 8 | How customers pay | Decides the payment gateway | [Payment gateways for businesses](payment-gateways-for-businesses.md) |
| 9 | Accounts or guest checkout | Decides the shop account handler | [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md) |
| 10 | Which order statuses you use | Decides the daily workflow | [Orders and order statuses](orders-and-order-statuses.md) |

## People and roles

The shop's permissions are split into functions in `kernel/shop/module.php`: `buy`, `administrate`, `setup`,
`edit_status` and `setstatus` ([Running a shop: overview](../../creators/website-creators/shop-overview.md)). That
split lets a small business give each person only what their job needs.

| Person | Typical policy | Does |
|---|---|---|
| Visitors and customers | `shop/buy` | Basket, checkout, their own orders |
| Order handler | `shop/administrate`, `shop/setstatus` | Order list, dashboard, statuses, customers |
| Shop owner or accountant | `shop/setup` as well | VAT types and rules, currencies, discount groups |
| Editors | content policies only | Product pages, images, texts |

The `setstatus` function takes the limitations `FromStatus` and `ToStatus` (`kernel/shop/module.php`), so a packing
assistant can, for example, be allowed only to move orders from Paid to Packed. Roles are explained in
[Roles and policies](../../../guides/roles-and-policies.md).

> **Tip**
> Write down who handles orders on weekends and holidays before launch. The Store dashboard highlights orders that wait
> too long ([Store dashboard and reports](store-dashboard-and-reports.md)), but someone has to look.

## What the shop gives you and what to add

The technical overview lists what the shop does not do and what should be planned as extensions or integrations:
stock management, invoices in an accounting system, shipping labels and marketplaces
([Running a shop: overview](../../creators/website-creators/shop-overview.md)). Decide early whether you need any of
these, and whether a partner will build or connect them. See
Choosing a partner and
Questions to ask a partner.

## A planning worksheet

Copy this list into your project brief and fill it in.

- [ ] Product types and the fields each needs (name, number, price, images, options, specifications)
- [ ] Number of products at launch and in one year
- [ ] Shop currency, and other currencies shown to customers
- [ ] Countries you ship to, and the countries you list first in forms
- [ ] VAT or sales tax rates, confirmed with your tax adviser
- [ ] Shipping cost model: one flat amount, or rules by weight, zone or value
- [ ] Payment provider and whether customers stay on your site to pay
- [ ] Guest checkout, customer accounts, or both
- [ ] The order statuses you will use and who changes them
- [ ] The text of the order confirmation mail and the receipt
- [ ] Who receives the shop's copy of each order mail (`[MailSettings] AdminEmail`)
- [ ] Extensions or integrations you need (stock, accounting, labels)
- [ ] Launch date, and the test period before it

A template for the brief is in Project brief template, and a checklist of
requirements in Requirements checklist.

## After planning

1. Give the worksheet to the builder together with
   [Running a shop: overview](../../creators/website-creators/shop-overview.md), which has the setup order.
2. Agree on a test period with real test orders in the payment provider's test mode.
3. Use the [Store launch checklist](store-launch-checklist.md) before opening.
4. Estimate the cost with Total cost of ownership and
   Worked example: small store.

## Related pages

- [What the built-in shop does](what-the-built-in-shop-does.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
