---
title: Measuring ROI
audience: customer, businesses, project-manager
category: customer-success
order: 10
summary: How an owner measures the return on a site that runs on Exponential - choosing a few measures that matter, where each figure comes from in the software and outside it, how often to collect it, and the traps that make ROI figures wrong.
keywords: ROI, return on investment, measuring, KPIs, figures, store dashboard, form submissions, newsletter, search phrases, staff time, costs, benefits
type: help
---
# Measuring ROI

This page is for owners and managers who must say, after launch, whether the site is worth what it costs. Return on
investment compares what the site brings with what it costs. The formula is simple; the hard part is honest figures.
This page tells you which figures to collect and where they come from. The calculation itself is on
[ROI example assumptions](roi-example-assumptions.md) and the [ROI worksheet](roi-worksheet.md).

## The formula

ROI = (benefits - costs) / costs x 100 %, over a period you choose, usually one year for a running site. The same
formula is used in the businesses section ([ROI worksheet template](../../businesses/templates/roi-worksheet-template.md)).

## Choose few measures

Pick three to five measures that match why your organization has the site. Measuring everything leads to reports
nobody reads.

| Why you have the site | Measures that fit |
|---|---|
| To sell | Orders, revenue, average order, returning customers |
| To get inquiries | Form submissions per month, and how many became business |
| To inform members or the public | Newsletter subscribers, questions answered by the site instead of by phone |
| To publish (news, magazine, documentation) | Items published, time from draft to publication, readers |
| To work together (intranet) | Staff time saved on finding and sharing information |

## Where the figures come from

| Figure | Where in Exponential | Source |
|---|---|---|
| Orders, revenue, customers, abandoned baskets | Store dashboard; product statistics per month | [Store dashboard and order statuses](../../../features/6.0/store-dashboard.md), [Store reports](../commerce/store-reports.md) |
| Form submissions | **Setup > Collected information**, with CSV export | [Collected information](../../../guides/collected-information.md) |
| Newsletter subscribers | The `cjw_newsletter` administration | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| What visitors search for | Search phrases are stored (`ezsearch_search_phrase`) | [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md) |
| Content published | Versions and publishing history of content | [Publishing and versions](../../publishing-and-versions.md) |
| Cost of an AI provider, if used | The Exponential Intelligence dashboard shows cost, tokens and requests | `README.md`, "Exponential Intelligence" |
| Visitor numbers | Not built in as a report; a statistics tool | [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md) |

Figures from outside the site: your bookkeeping (real income, fees, refunds), your host's and partner's invoices,
your staff's time records, and your phone or mail statistics for questions the site now answers.

## How often

| Measure | Collect | Review |
|---|---|---|
| Store figures | Monthly, from the product statistics | Quarterly |
| Form submissions, subscribers | Monthly | Quarterly |
| Costs | With each invoice | Yearly, in the [annual value review](annual-value-review.md) |
| Staff time saved | Once a year, by a short survey or time sample | Yearly |

## Traps

- **Counting what only moved.** An order placed online that would have come by phone anyway is not new income. Count
  only what the site adds, or say clearly that you cannot separate it.
- **Order value is not income.** The store dashboard shows order value; fees, refunds and VAT are in your bookkeeping
  ([Store reports](../commerce/store-reports.md)).
- **Time saved is not money saved** unless the time is used for something else. Use a realization factor and say
  which one ([ROI worksheet template](../../businesses/templates/roi-worksheet-template.md)).
- **Missing costs.** Staff time for content work is usually the largest cost, and the one most often left out
  ([Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)).
- **Test data.** Test orders and test form submissions inflate the figures; remove them.

## Writing it up

One page is enough: the measures you chose, the figures with their source, the costs, the result, and what you will
change. Bring it to the [annual value review](annual-value-review.md).

## Related pages

- [ROI example assumptions](roi-example-assumptions.md)
- [ROI worksheet](roi-worksheet.md)
- [Return on investment of stability](../../businesses/costs/roi-of-stability.md)
- [Cost of ownership for owners](cost-of-ownership-for-owners.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
