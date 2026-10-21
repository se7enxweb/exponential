---
title: Agency path
audience: ecommerce, partner, businesses
category: e-commerce
order: 14
summary: A delivery path for agencies and partners who build Exponential stores for clients - discovery questions, scoping what the kernel does against what must be built, a build order, test and handover packages, and maintenance after launch.
keywords: agency, partner, client project, discovery, scoping, estimate, build order, handover, maintenance, store delivery, e-commerce project
type: help
---
# Agency path

This page is for agencies and partners who deliver Exponential stores to clients. It turns the shop's facts into a
delivery path: what to ask in discovery, how to scope kernel features against custom work, the build order, and what
to hand over.

## Contents

1. [Discovery questions](#discovery-questions)
2. [Scoping kernel against custom work](#scoping-kernel-against-custom-work)
3. [Build order](#build-order)
4. [Test and acceptance](#test-and-acceptance)
5. [Handover package](#handover-package)
6. [Maintenance](#maintenance)

## Discovery questions

| Area | Ask | Why it matters |
|---|---|---|
| Catalog | How many products, which kinds, which fields, which options? | Product classes and imports ([Catalog modeling in depth](../catalog/catalog-modeling-in-depth.md)) |
| Prices | One or several currencies? Manual or converted prices? | `ezprice` or `ezmultiprice` ([Price fields compared](../catalog/price-fields-compared.md)) |
| Tax | Which countries, which product tax classes? | VAT types, rules and a VAT handler ([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md)) |
| Shipping | Flat, by weight, by zone, by carrier rate, pickup? | Simple shipping or a handler ([Custom shipping handler](../extending/custom-shipping-handler.md)) |
| Payment | Which provider? Does it have a redirect page? | Existing extension or a new gateway ([Payment gateway development](../extending/payment-gateway-development.md)) |
| Customers | Accounts, guests, B2B groups with discounts? | Account handler, discount groups ([shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md)) |
| Back office | Stock, accounting, ERP, labels? | Integrations, not in the kernel ([Decision checklists](../operations/decision-checklists.md)) |
| Sites | One store or several brands and languages? | [Multi-store and multisite](../operations/multi-store-and-multisite.md) |
| Data | Products to import from where? | [Product imports and migrations](../operations/product-imports-and-migrations.md) |

## Scoping kernel against custom work

| Shipped in the kernel or the repository | Configuration only | Custom work |
|---|---|---|
| Basket, checkout, orders, 17 statuses, dashboard, receipts | VAT types and rules (plus `Handler=ezdefault`), currencies, discount groups, statuses, basket name, receipts | Shipping rules beyond a flat amount |
| PayPal (`ezpaypal`), Authorize.Net (`ezauthorize`) | Gateway settings, the checkout workflow | Gateways for other providers |
| Flat shipping (`ezsimpleshipping`) | `workflow.ini [SimpleShippingWorkflow]` | Stock, coupons, labels, accounting export, download delivery |
| Order statistics, best sellers, "also bought" fetch | Templates that use them | Reports beyond these ([Store figures and exports](../operations/store-figures-and-exports.md)) |

Estimate the custom column as extension work with its own tests and documentation.

## Build order

1. Settings skeleton in an extension or `settings/override/` ([Shop settings walkthroughs](../settings/README.md)).
2. Product classes and a test catalog; then imports.
3. VAT types and rules; currencies; rounding.
4. Templates: product full view, basket, checkout pages, order mail.
5. Shop account handler and the customer data form.
6. Shipping workflow or handler.
7. Payment gateway in test mode and the checkout workflow.
8. Statuses, roles and policies for the client's staff.
9. Landing pages with Layouts ([Content commerce with Layouts](../operations/content-commerce-with-layouts.md)).
10. Tests, accessibility check, launch checklist.

## Test and acceptance

Agree acceptance on a written test plan before the build: [Testing a store](../operations/testing-a-store.md) gives
one, and the client signs off with the [Store launch checklist](../../businesses/stores/store-launch-checklist.md).
Accessibility belongs in acceptance too ([Store accessibility](../operations/store-accessibility.md)).

## Handover package

- [ ] The settings changed, file by file, with the reason for each.
- [ ] The workflows connected to shop triggers, and what each event does.
- [ ] The roles created for store staff and the policies of each.
- [ ] The payment provider account details: who holds them (never in the handover document itself).
- [ ] The cron jobs the store depends on (`basket_cleanup` in `infrequent`, `workflow` in `frequent`).
- [ ] Links to [Running your store](../../customers/commerce/README.md) for the client's staff.
- [ ] The custom code, its tests, and how to update it.

## Maintenance

Offer a calendar, not just a support line: [Operations calendar](../operations/operations-calendar.md) lists the
recurring work of a store. After each Exponential update, run the store test plan again
([Upgrades without surprises](../../businesses/reliability/upgrades-without-surprises.md)).

## Related pages

- [Paths by role](README.md)
- [Partners: start here](../../partners/partners-start-here.md)
- [Choosing a partner](../../businesses/choosing/choosing-a-partner.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
