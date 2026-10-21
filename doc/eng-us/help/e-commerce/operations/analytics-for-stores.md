---
title: Analytics for stores
audience: ecommerce, businesses, website-creator
category: e-commerce
order: 75
summary: What an Exponential store can measure - the Store dashboard, order statistics, bestseller and also-bought fetch functions, view counts, search statistics and the audit trail - and what the bcwebsitestatistics extension or another analytics service adds, with privacy notes.
keywords: analytics, store analytics, dashboard, order statistics, best_sell_list, related_purchase, view counts, search statistics, bcwebsitestatistics, ezreceipt, Google Analytics, conversion, abandoned baskets, consent
type: help
---
# Analytics for stores

This page is for merchants and marketers who want to know how the store performs, and for the developers who set up
measurement. Exponential measures a good deal on its own from orders and content; visitor analytics such as traffic
sources and conversion funnels need an analytics service.

## Contents

1. [What the store measures itself](#what-the-store-measures-itself)
2. [Questions and where the answer is](#questions-and-where-the-answer-is)
3. [Adding an analytics service](#adding-an-analytics-service)
4. [Privacy](#privacy)

## What the store measures itself

| Source | Measures | Where |
|---|---|---|
| Store dashboard | Orders today, 7 and 30 days with trends; revenue and average order per currency; new and returning customers; open and late orders; baskets in progress and abandoned; revenue per day; status breakdown; best sellers | `shop/dashboard` ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)) |
| Order statistics | Products sold and totals per year and month | `shop/statistics` |
| `fetch( 'shop', 'best_sell_list', ... )` | Best-selling products below a node, optionally in a time window | templates (`kernel/shop/function_definition.php`) |
| `fetch( 'shop', 'related_purchase', ... )` | Products bought together with a product | templates |
| View counts | Page views per node from the access log (`updateviewcount` cron part) | `fetch( 'content', 'view_top_list', ... )` |
| Search statistics | What visitors search for and how many results they get | **Setup > Search statistics** |
| Audit trail, `commerce` channel | Orders created, payments approved, status changes, VAT, currency and discount changes | **Audit** tab ([Commerce audit trail](commerce-audit-trail.md)) |

The general overview of measurement is in [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md).

## Questions and where the answer is

| Question | Answer from |
|---|---|
| How much did we sell last month, per product? | `shop/statistics` |
| Are sales going up? | Dashboard trends against the 30 days before |
| Which orders wait too long? | Dashboard, **Work to do** |
| Do customers drop out at checkout? | Dashboard: baskets in progress and abandoned, unconfirmed checkouts |
| What do people search for and not find? | Search statistics |
| Which product pages are popular but rarely bought? | View counts next to order statistics |
| Where do visitors come from? | An analytics service |

## Adding an analytics service

- **`bcwebsitestatistics`** adds the Google Analytics script and, with `OrderSubmit=enabled`, reports placed orders
  through its workflow event type `ezreceipt`, which may only be connected to the after trigger of `shop_checkout`
  ([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)).
- **Another service**: load its script as a file through your design's script list, and report an order from the
  receipt or order view template, or from an event of your own on the after trigger of `shop_checkout`
  ([Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md)). Under a Content Security Policy the
  service's hosts must be allowed ([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md#under-a-content-security-policy)).

Report order totals, currency and product identifiers; never names, addresses or e-mail addresses.

## Privacy

Analytics of visitors usually needs their consent; load scripts only after consent and describe them in the privacy
page. This is not legal advice. The kernel's own figures come from your database and logs and are not sent anywhere.
For data requests from customers, see [GDPR requests](../../customers/commerce/gdpr-requests.md).

## Related pages

- [Operating a store](README.md)
- [Store reports](../../customers/commerce/store-reports.md)
- [Store dashboard and reports](../../businesses/stores/store-dashboard-and-reports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
