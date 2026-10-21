---
title: Headless commerce with expservices
audience: ecommerce, app-creator, developer
category: e-commerce
order: 78
summary: Driving the Exponential shop over HTTP with the expservices extension - the commerce service domains (product, basket, order, payment, shipping, VAT, currency, discount, wishlist), authentication with sessions or API tokens, policies per service, typical uses and the caveats of a first-draft extension.
keywords: headless commerce, expservices, remote services, exporder, expbasket, expproduct, exppayment, expshipping, expvat, expcurrency, expdiscount, expwishlist, API token, ezjscore call, JSON, mobile app, integration
type: help
---
# Headless commerce with expservices

This page is for developers who want an app, a script or another system to use the shop: read products and prices,
fill a basket, look up orders, or pull figures. The `expservices` extension, shipped in the repository's `extension/`
folder, exposes the administration's functions as remote services over ezjscore, and the shop is one of its groups.
The general introduction is [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md).

## Contents

1. [The commerce domains](#the-commerce-domains)
2. [Calling a service](#calling-a-service)
3. [Who may call what](#who-may-call-what)
4. [Typical uses](#typical-uses)
5. [Caveats](#caveats)

## The commerce domains

Registered in `extension/expservices/settings/ezjscore.ini.append.php`, classes in
`extension/expservices/classes/commerce/`:

| Domain | Some of its services |
|---|---|
| `expproduct` | `classes`, `list`, `search`, `view`, `price`, `prices`, `options`, `categories`, `priceRange`, `related`, `vat`, `setPrice` |
| `expbasket` | `view`, `items`, `count`, `totals`, `add`, `update`, `remove`, `empty`, `refreshPrices`, `setCurrency`, `startCheckout`, `review`, `cancelCheckout`, `placeOrder` |
| `exporder` | `mine`, `myOrder`, `myReceipt`, `statuses`, `list`, `view`, `byNumber`, `items`, `history`, `setStatus`, `archive`, `unarchive`, `remove`, `search`, `statistics`, `customers`, `dashboard` |
| `exppayment` | `list`, `view`, `byOrder`, `myStatus`, `gateways`, `workflowEvents`, `approve` |
| `expshipping` | `status`, `handler`, `simpleShipping`, `basket`, `order`, `vatOfShipping`, `settings` |
| `expvat` | `types`, `rules`, `categories`, `forProduct`, `userCountry`, `setUserCountry`, `countries` and the create, update and remove services |
| `expcurrency` | `list`, `rate`, `convert`, `preferred`, `setPreferred`, `updateRates`, `updateAutoprices` and more |
| `expdiscount` | `groups`, `rules`, `members`, `forUser`, `mine` and the edit services |
| `expwishlist` | `view`, `items`, `add`, `remove`, `moveToBasket`, `popular` |

The full, current list comes from the service catalog itself (`expservices::catalog`), which describes every service
with its parameters ([Remote services (feature)](../../../features/6.0/remote-services-expservices.md)).

## Calling a service

```bash
curl -s -H 'Authorization: Bearer <token>' \
  'https://example.com/ezjscore/call/exporder::byNumber::1042?ContentType=json'
```

Every answer is an envelope: `{"ok":true,"data":...,"meta":{...}}` or `{"ok":false,"error":{...}}`; lists are paged.
The master switch is `expservices.ini [Services] Enabled`.

## Who may call what

Each service declares the policy it needs and checks it as the calling user. The order services for staff, such as
`exporder::list`, `statistics` and `customers`, need `shop/administrate`; services named `mine`, `myOrder` and
`myReceipt` answer for the signed-in customer's own orders. Writes need POST. Give each script or app its own user
and personal API token with only the policies it needs; tokens are shown once, stored only as a hash, and their
creation and revocation are audited.

## Typical uses

| Use | Services |
|---|---|
| A mobile app showing products and prices | `expproduct::list`, `view`, `prices` |
| A customer's order history in an app | `exporder::mine`, `myOrder`, `myReceipt` |
| A nightly figures export | `exporder::list`, `statistics` ([Store figures and exports](store-figures-and-exports.md)) |
| A packing station that marks orders shipped | `exporder::byNumber`, `setStatus` (with a role limited by `FromStatus` and `ToStatus`) |
| Price updates from a supplier system | `expproduct::setPrice` |

For content in apps in general and OAuth for third parties, the REST API is the other route
([Headless Exponential](../../creators/app-creators/headless-exponential.md)).

## Caveats

- The extension was added on 2 October 2026 as version 0.1.0 and its feature page calls it a first draft. Pin the
  services you use in your tests and check them after updates.
- Placing orders over services still runs the checkout's workflows; a redirect payment still needs the customer at
  the provider. Test the full path before building an app checkout on it.
- Never send card data through these services.

## Related pages

- [Operating a store](README.md)
- [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md)
- [Mobile and headless (businesses)](../../businesses/mobile-and-headless/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
