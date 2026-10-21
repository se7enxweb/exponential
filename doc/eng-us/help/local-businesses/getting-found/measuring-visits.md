---
title: Measuring visits
audience: local-business, businesses, customer
category: business-solutions
order: 27
summary: How a small business finds out whether the website brings customers - what Exponential records by itself (site search terms, view counts, shop statistics, form submissions), adding an analytics service with bcwebsitestatistics, privacy and consent, and five numbers worth watching.
keywords: analytics, statistics, visits, bcwebsitestatistics, Google Analytics, search statistics, form submissions, shop statistics, privacy, consent, measuring, local business
type: help
---
# Measuring visits

This page is for the owner of a small business who wants to know if the website is worth its cost. You do not need a
dashboard full of charts. A few numbers, looked at once a month, tell you most of what you need.

## Contents

1. [What Exponential records by itself](#what-exponential-records-by-itself)
2. [Adding an analytics service](#adding-an-analytics-service)
3. [Privacy and consent](#privacy-and-consent)
4. [Five numbers worth watching](#five-numbers-worth-watching)
5. [Ask your customers](#ask-your-customers)

## What Exponential records by itself

From [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md):

| Record | Tells you | Where |
|---|---|---|
| Site search statistics | what visitors look for on your site, and what they do not find | [Site search and what visitors look for](../../customers/day-to-day/site-search-and-what-visitors-look-for.md) |
| Form submissions | how many contact or quote requests came in | **Setup > Collected information** ([Form submissions](../../creators/website-creators/form-submissions.md)) |
| Shop statistics | orders, best sellers, abandoned baskets | Store dashboard ([Store reports](../../customers/commerce/store-reports.md)) |
| Web server logs | requests per page | your host |

## Adding an analytics service

The `bcwebsitestatistics` extension (in the standard `composer.json`) adds the Google Analytics script to every page
and can report shop orders through a workflow
([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)). It needs an account with that service
and your own tracking identifier in `bcwebsitestatistics.ini`. Another analytics service can be added by your builder
as a script in the pagelayout
([Adding an analytics service](../../creators/website-creators/statistics-and-analytics.md#adding-an-analytics-service)).

Analytics is an outside service: the account, its terms and its data are with the provider.

## Privacy and consent

An analytics script sends data about your visitors to another company. In many countries that needs a consent banner
and a line in your privacy note. This is not legal advice; ask your adviser. See
[Privacy and consent](../../creators/website-creators/statistics-and-analytics.md#privacy-and-consent) and
[GDPR basics](../../businesses/security/gdpr-basics.md).

If you do not want analytics at all, the records Exponential keeps by itself plus asking customers (below) are often
enough for a local business.

## Five numbers worth watching

| Number | Where | What a change means |
|---|---|---|
| Contact form messages per month | Collected information | the site turns visitors into enquiries |
| Calls or visits that mention the website | ask at the counter or on the phone | the site works offline too |
| Top site search terms | search statistics | something visitors want is hard to find |
| Orders per month (if you sell) | Store dashboard | the shop is used |
| Visits to the opening hours or contact page | analytics, if used | people plan to come |

Write the numbers into a simple monthly table and compare with the same month last year; local businesses are
seasonal ([Seasonal changes](../running-it/seasonal-changes.md)).

## Ask your customers

The cheapest and most reliable measurement: ask new customers "How did you find us?" and keep a tally for a month.
Many local businesses learn more from this than from any chart.

## Related pages

- [Analytics and what to measure](../../customers/day-to-day/analytics-and-what-to-measure.md)
- [Measuring ROI](../../customers/value/measuring-roi.md)
- [Weekly and monthly routine](../running-it/weekly-and-monthly-routine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
