---
title: Dealer and supplier portals
audience: businesses, project-manager, partner
category: business-solutions
order: 60
summary: What Exponential offers for trade portals - dealers ordering through the built-in shop with discount groups per user group, product information and price lists by tier, suppliers sending documents through forms or uploads, and the honest list of what the shop does not do.
keywords: dealer portal, supplier portal, B2B, trade portal, discount groups, group prices, shop/buy, built-in shop, orders, price lists, product information, supplier uploads, information collector, stock, invoices, integrations
type: help
---
# Dealer and supplier portals

This page is for sales and purchasing managers who want a portal for trade partners. Dealers want to see their
prices and order; suppliers want to know what is expected and send documents back. Exponential's built-in shop, its
permissions and its forms cover a good part of this. The page also says plainly what is not built in, so that
integrations can be planned from the start.

## Contents

1. [Dealer portals](#dealer-portals)
2. [Ordering with the built-in shop](#ordering-with-the-built-in-shop)
3. [Prices per dealer group](#prices-per-dealer-group)
4. [Product information and price lists](#product-information-and-price-lists)
5. [Supplier portals](#supplier-portals)
6. [What is not built in](#what-is-not-built-in)
7. [Checklist](#checklist)
8. [Related pages](#related-pages)

## Dealer portals

A dealer portal combines three things that already exist in Exponential:

| Need | Feature | Page |
|---|---|---|
| Only dealers may order | `shop/buy` given to the dealer role, not to Anonymous | [Running a shop: overview](../../creators/website-creators/shop-overview.md#permissions) |
| Dealers see their own prices | discount groups per user group | [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#discounts) |
| Dealers see dealer-only material | sections and tiers | [Customer and partner areas](customer-and-partner-areas.md) |

## Ordering with the built-in shop

The kernel includes a shop: products are content with a price field, dealers put them in a basket, check out,
confirm and pay through a payment gateway, and orders are managed in the **Store** tab of the administration
([Running a shop: overview](../../creators/website-creators/shop-overview.md)).

| Step | View |
|---|---|
| Add to basket | `shop/add` |
| Basket | `shop/basket` |
| Customer data | `shop/register` or `shop/userregister` |
| Confirmation with shipping and VAT | `shop/confirmorder` |
| Payment | a workflow on the checkout trigger with a gateway event ([Payment gateways](../../creators/website-creators/payment-gateways.md)) |
| Order list, statuses, statistics | `shop/orderlist`, the Store dashboard ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)) |

Shop permissions: dealers need `shop/buy`; your order desk needs `shop/administrate`, and `shop/setup` for VAT and
currencies. Since `shop/buy` is a policy like any other, ordering can be restricted to the dealer role while the
public site only shows products.

The details of a shop, from VAT rules to receipts, are in the [Stores](../stores/README.md) section.

## Prices per dealer group

Discount groups (`shop/discountgroup`) give user groups a percentage off products or classes, with rules per group
(`shop/discountruleedit`) ([Products, prices and VAT, Discounts](../../creators/website-creators/products-prices-and-vat.md#discounts)).

| Dealer tier | User group | Discount rule (example) |
|---|---|---|
| Standard dealer | Dealers | a percentage on the class Product |
| Premium dealer | Premium dealers | a higher percentage on the class Product |

The percentages in this table are placeholders; your price policy decides them. Prices in several currencies use the
`ezmultiprice` datatype and the currency list (`shop/currencylist`)
([Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)). VAT by country is handled by
VAT types and VAT rules.

> **Note**
> Discount groups give percentages. A fully separate price list per dealer (fixed net prices per product and dealer)
> is not a documented feature of the shop; plan it as an extension or keep price lists as documents (below).

## Product information and price lists

Not every portal needs ordering. Many dealers mainly need:

- product pages with specifications, images and downloads, readable only by dealers;
- price lists as files per tier ([Sharing files with partners](sharing-files-with-partners.md));
- product news, followed with **Notify me** ([Notifications: the user's guide](../../../features/6.0/notifications.md));
- a PDF of a part of the catalog ([PDF exports](../../../guides/pdf-exports.md)).

Products and documents can be tagged across product lines with `eztags`
([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)).

## Supplier portals

Suppliers usually read requirements and send documents back:

| Need | Feature | Page |
|---|---|---|
| Read specifications and quality rules | pages in a supplier section | [Customer and partner areas](customer-and-partner-areas.md) |
| Upload certificates into their own folder | `content/create` for the File class, Subtree limitation per supplier | [Sharing files with partners](sharing-files-with-partners.md#letting-partners-upload) |
| Send a form with an attachment | information collector with `enhancedezbinaryfile` | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md) |
| Staff are told about new uploads | Notify me on the supplier folders | [Notifications for staff](../intranets/notifications-for-staff.md) |
| Uploads checked before use | an Approve workflow on the supplier section | [Approval workflows](../intranets/approval-workflows.md) |

## What is not built in

The shop overview lists what the shop does not do: stock management, invoices in an accounting system, shipping
labels and marketplaces; plan these as extensions or integrations
([Running a shop, What the shop does not do](../../creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).
For a trade portal also plan, if needed: purchase-order approval inside the dealer's organization, credit limits,
and an interface to your ERP. Exponential's REST API and remote services are the usual starting point for such
integrations ([Headless with the REST API](../mobile-and-headless/headless-with-the-rest-api.md),
[Remote services and apps](../../../guides/remote-services-and-apps.md)).

## Checklist

- [ ] `shop/buy` only in the dealer role (and wherever the public may buy, if at all).
- [ ] Discount groups and rules per dealer tier.
- [ ] Dealer-only product information in a dealer section.
- [ ] Price lists as files per tier, with owners.
- [ ] Supplier upload folders with a limited role.
- [ ] Integrations for stock, invoices and ERP scoped with a partner.
- [ ] A full test order as a dealer user.

## Related pages

- [Stores](../stores/README.md)
- [How businesses sell online](../how-businesses-sell-online.md)
- [Selling through networks and partners](../mobile-and-headless/selling-through-networks-and-partners.md)
- [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
