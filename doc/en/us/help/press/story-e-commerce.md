---
title: "Story: the built-in shop"
audience: press
category: press-media
order: 77
summary: A feature story about e-commerce built into Exponential - products, baskets, orders, VAT, currencies, PayPal - and the 6.0.x improvements such as the store dashboard, order statuses, permanent order receipts and order list sorting, with sources and the claims to avoid.
keywords: story, e-commerce, shop, online store, orders, products, VAT, currencies, PayPal, store dashboard, order receipts, checkout
type: help
---
# Story: the built-in shop

This page is for journalists who cover e-commerce. Many content management systems add a shop through a plugin;
Exponential has one in the kernel, from its early generations.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

The README lists "Advanced e-Commerce Stores (Built in)" among the main features: products, baskets, orders, VAT,
currencies and PayPal payments through the `ezpaypal` extension ([Who is 7x?, A CMS, a CMF and a shop in one](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one)).
The 6.0.x line improved it: the **Store** tab opens a dashboard that "shows on one page ... how the shop is doing,
which orders need attention and how checkout is configured", with an order status for every stage
([Store dashboard and order statuses](../../features/6.0/store-dashboard.md)), and every order can have a receipt "with
an address that never expires" ([Permanent order receipts](../../features/6.0/order-receipts.md)).

## Facts you can print

| Fact | Source |
|---|---|
| E-commerce is built into the kernel | README, "Main Exponential features" |
| PayPal payments through `ezpaypal` | [ezpaypal](../../features/6.0/extensions/ezpaypal.md) |
| A store dashboard and order statuses for every stage | [Store dashboard and order statuses](../../features/6.0/store-dashboard.md) |
| Permanent order receipts customers can bookmark, print or download, with or without an account | [Permanent order receipts](../../features/6.0/order-receipts.md) |
| Order lists can be sorted | [Order list sorting](../../features/6.0/order-list-sorting.md) |
| Country groups for shop settings | [Country groups](../../features/6.0/country-groups.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "A full replacement for <named shop platform>" | Describe what the built-in shop does ([Comparisons done fairly](comparisons-done-fairly.md)) |
| "PCI certified" | No certification is documented; payment handling is through gateways such as PayPal |
| "Shop works on MongoDB" | The checkout on MongoDB is "untested end to end" ([Story: MongoDB as a database](story-mongodb.md)) |

## Angles and headline ideas

- "Content and commerce in one open source system."
- "Receipts that never expire."

## Related pages

- [Story: editing content](story-editing-content.md)
- [Feature stories: index](feature-stories-index.md)
- [Feature highlights for customers](../business/feature-highlights-for-customers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
