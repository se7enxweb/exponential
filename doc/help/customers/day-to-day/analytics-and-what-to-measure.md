---
title: Analytics and what to measure
audience: customer, businesses, project-manager
category: customer-success
order: 21
summary: What a site owner can measure on an Exponential site - what the software records itself (search statistics, view counts, the audit trail, store figures), what needs an analytics service, consent, and a short list of questions worth a number each month.
keywords: analytics, measuring, KPIs, statistics, search statistics, view counts, audit trail, store dashboard, consent, Google Analytics, reporting
type: help
---
# Analytics and what to measure

This page is for the owner and the manager who report on how the site is doing. Exponential records some figures
itself; visitor analytics (where visitors come from, how long they stay) needs a service you add
([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)). Start from the
questions you want answered, not from the numbers a tool happens to show.

## Contents

1. [Questions first](#questions-first)
2. [What Exponential records](#what-exponential-records)
3. [Adding an analytics service](#adding-an-analytics-service)
4. [Consent and privacy](#consent-and-privacy)
5. [A monthly report](#a-monthly-report)

## Questions first

| Question | Where the answer comes from |
|---|---|
| What do visitors look for and not find? | search statistics |
| Which pages are popular? | view counts, or an analytics service |
| Is the team publishing what we planned? | the audit trail, compared with the [Content calendar](content-calendar.md) |
| Does the newsletter reach people? | the newsletter's own statistics ([Newsletter routine](../growth/newsletter-routine.md)) |
| How does the store do? | the store dashboard and shop statistics |
| Where do visitors come from, on which devices? | an analytics service only |

## What Exponential records

From [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md):

| Source | What | Where |
|---|---|---|
| Search statistics | phrases searched, counts, average results; **off by default** (`LogSearchStats=disabled` in `settings/site.ini`) | **Setup > Search statistics** |
| View counts | page views per node, counted by the `updateviewcount` cron part from the access log | used by templates for "most read" lists |
| Request logs | requests with timing, for operations | [Request logs](../../../features/6.0/request-logs.md) |
| Shop statistics | orders and revenue | `shop/statistics`, the [Store dashboard](../../../features/6.0/store-dashboard.md) |
| Audit trail | who changed what in the administration | [The audit trail](../../../features/6.0/audit-trail.md) |

These answer operational questions; they are not a replacement for an analytics service (same source). The project
managers' page [Measuring a project: KPIs](../../leadership/kpis-from-logs-and-audit.md) lists more figures the
product produces, with the page or command for each, and a monthly report template.

> **Note**
> Reading the audit trail needs `audit/read`; only Administrator holds it on a new installation
> ([Measuring a project: KPIs](../../leadership/kpis-from-logs-and-audit.md)). A reporting role limited to the
> channels you need can be made without the rights to change anything.

## Adding an analytics service

- The extension `bcwebsitestatistics` adds the Google Analytics script and can report shop orders
  ([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)).
- Another service is added as a script file through the design, with its host allowed in the site's Content
  Security Policy ([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).

Ask your partner which one your site uses and who has the account. The account belongs to your organization, not to
the partner.

## Consent and privacy

Analytics usually needs the visitor's consent in many jurisdictions: load the script only after consent, describe it
on the privacy page, and prefer services you can self-host or that set no cookies when your audience expects it
([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)). Search phrases
can be personal data too, which is why search statistics are off by default
([Site search](../../creators/website-creators/site-search.md)). This is not legal advice; ask whoever is
responsible for data protection.

## A monthly report

**Template.** Keep it to one page:

```text
MONTH: __________   Prepared by: ______________
Published this month (from the audit trail or the calendar): ____ items, planned: ____
Top 5 search phrases without results: ____________________________________
Action taken on them: ____________________________________________________
Most viewed pages (view counts or analytics): ____________________________
Newsletter: editions sent ____, notes: ___________________________________
Store (if any): orders ____, notes: ______________________________________
One thing we change next month: __________________________________________
```

Compare each figure with the same month before; the first measurement is your baseline
([Measuring a project: KPIs](../../leadership/kpis-from-logs-and-audit.md)).

## Related pages

- [Site search and what visitors look for](site-search-and-what-visitors-look-for.md)
- [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)
- [Store dashboard and reports](../../businesses/stores/store-dashboard-and-reports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
