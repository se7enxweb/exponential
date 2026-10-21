---
title: Grant outcomes for funders
audience: non-profit, businesses, project-manager, editor
category: non-profits
order: 50
summary: How a non-profit turns what its Exponential site records into the outcome section a funder asks for at the end of a grant - collecting figures from collected information, newsletter lists, view counts, search statistics and the audit trail, exporting them without exposing personal data, and an outline with a place for each piece of evidence.
keywords: grant outcomes, outcome statement, evidence, figures, CSV export, collected information, newsletter subscribers, view counts, audit trail, funder, impact
type: help
---
# Grant outcomes for funders

This page is for the person who writes to the funder at the end of a grant period. It shows where each figure lives in
Exponential, how to get it out, and how to keep personal data out of what you send. It assumes the measurements were
switched on in time; if not, read [Measuring outcomes with site data](measuring-outcomes-with-site-data.md) for the
next period.

## Contents

1. [Outputs and outcomes](#outputs-and-outcomes)
2. [Where the figures are](#where-the-figures-are)
3. [Getting the figures out](#getting-the-figures-out)
4. [Keep personal data out](#keep-personal-data-out)
5. [An outline for the funder](#an-outline-for-the-funder)
6. [Checklist](#checklist)

## Outputs and outcomes

| | Output | Outcome |
|---|---|---|
| Means | What you produced | What changed for people |
| Example | 40 resource pages in two languages | Requests for help rose through the form |
| Evidence in Exponential | Content and versions with dates | Collected information counts per month |

The site supplies good evidence of outputs and some evidence of outcomes. Stories from people you served, survey
results and statements from partner organizations come from outside the site.

## Where the figures are

| Figure | Where in Exponential | Source |
|---|---|---|
| Pages published in the period | Content tree; versions with publication dates; audit trail `content` channel | [Versions of an object](../../../guides/content-history.md), [The audit trail](../../../features/6.0/audit-trail.md) |
| Forms received (sign-ups, requests) | **Setup > Collected information**: figures per form, **Export CSV** | [Reading and exporting form submissions](../../creators/website-creators/form-submissions.md) |
| Newsletter subscribers | `cjw_newsletter` subscription list, with status (pending, confirmed, approved ...) | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Newsletters sent | The newsletter archive | same |
| Page views | View counts per node (cron part `updateviewcount`), or an analytics service | [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md) |
| What people looked for | Search statistics, if switched on (`LogSearchStats`) | same |
| Donations through the shop | Orders and shop statistics | [Store dashboard](../../../features/6.0/store-dashboard.md) |
| Event participation | Submissions of the event's sign-up form | [Events and calendars](../communications/events-and-calendars-for-non-profits.md) |

## Getting the figures out

1. **Forms.** In **Setup > Collected information**, the overview shows the number of collections per form. For a
   period, use **Export CSV** and count rows by date in a spreadsheet. The export is UTF-8, at most 50 000 rows, and
   recorded in the audit trail without the values ([Reading and exporting form submissions](../../creators/website-creators/form-submissions.md)).
2. **Newsletter.** Count confirmed subscribers in the subscription list at the start and end of the period.
3. **Content.** List the objects created in the period in a folder, or ask an administrator to read the audit trail
   for publish events (`exp:audit tail`, [The audit trail](../../../features/6.0/audit-trail.md)). Reading the
   audit trail needs `audit/read`.
4. **Views.** Take the view counts or the analytics service's figures for the period.

Write down where every figure came from and on which date you took it. A funder may ask.

## Keep personal data out

- Give **counts**, not names. Delete the CSV exports once you have counted them.
- Do not paste form submissions, e-mail addresses or donor names into what you send.
- If you quote a volunteer or a beneficiary, ask them first and keep the consent.
- See [Donor and volunteer data protection](../operations/donor-and-volunteer-data-protection.md).

## An outline for the funder

| Section | Content | Evidence |
|---|---|---|
| Summary | What the grant achieved in three sentences | |
| Goals and results | Each goal with its planned and actual figure | Figures from the table above |
| Outputs | What was built and published, with addresses | Live links, screenshots |
| Outcomes | What changed, with figures and stories | Collected information, subscribers, stories |
| Accessibility and inclusion | Tests done, languages offered | Test results, accessibility statement |
| Sustainability | Who runs the site now, budget for next year | [Budget lines](budget-lines-for-hosting-and-maintenance.md) |
| Lessons learned | What you would do differently | |

## Checklist

- [ ] Every figure has a source and a date.
- [ ] Counts only; no personal data.
- [ ] CSV exports deleted after counting.
- [ ] Links to live pages work.
- [ ] Sustainability section names a person and a budget.
- [ ] The document published as a download on the site, if the funder allows ([Annual publications as downloads](../transparency/annual-publications-as-downloads.md)).

## Related pages

- [Grants and the website](README.md)
- [Measuring outcomes with site data](measuring-outcomes-with-site-data.md)
- [Analytics and what to measure](../../customers/day-to-day/analytics-and-what-to-measure.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
