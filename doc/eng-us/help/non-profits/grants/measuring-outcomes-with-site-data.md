---
title: Measuring outcomes with site data
audience: non-profit, businesses, project-manager, admin
category: business-solutions
order: 60
summary: What an Exponential site counts by itself (form submissions, newsletter subscriptions, view counts, search statistics, shop figures, the audit trail), what is off by default and must be switched on before the period you report on, what needs an analytics service such as bcwebsitestatistics, and the consent questions that come with it.
keywords: measuring, metrics, outcomes, KPIs, view counts, search statistics, LogSearchStats, updateviewcount, analytics, bcwebsitestatistics, consent, audit trail, non-profit
type: help
---
# Measuring outcomes with site data

This page is for program managers and the technical lead. It says what Exponential can count, what must be switched on
first, and what needs an outside service. The rule that matters most: **switch measurements on before the period you
will report on.** Figures that were never recorded cannot be produced afterward.

## Contents

1. [What Exponential counts itself](#what-exponential-counts-itself)
2. [Switch on before you need it](#switch-on-before-you-need-it)
3. [An analytics service](#an-analytics-service)
4. [Consent and privacy](#consent-and-privacy)
5. [Choosing a few good measures](#choosing-a-few-good-measures)
6. [Setup checklist](#setup-checklist)

## What Exponential counts itself

From [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md) and
[Analytics and what to measure](../../customers/day-to-day/analytics-and-what-to-measure.md):

| Source | What it counts | Default |
|---|---|---|
| Collected information | Every form submission, per form | On, whenever a form exists |
| `cjw_newsletter` | Subscriptions per list with status; sent editions | On, when the newsletter is set up |
| View counts | Page views per node, from the access log, by the `updateviewcount` cron part | Needs the cron part to run |
| Search statistics | Phrases searched, counts, average results | **Off** (`LogSearchStats=disabled` in `settings/site.ini`) |
| Shop statistics | Orders and revenue | On, when the shop is used |
| Audit trail | Who changed what in the administration | On by default |
| Request logs | Requests with timing, for operations | See [Request logs](../../../features/6.0/request-logs.md) |

These answer operational questions; they do not tell you where visitors come from or how long they stay.

## Switch on before you need it

| If you will report | Make sure, before the period starts |
|---|---|
| What people searched for | Search statistics switched on by an administrator |
| Page views | The cron part `updateviewcount` runs ([Cronjobs](../../../guides/cronjobs.md)) |
| Sign-ups per channel | One form per channel, or a hidden field that says where it came from |
| Newsletter growth | A count of confirmed subscribers on day one, written down |
| Visitors and sources | An analytics service installed and consent handled |

## An analytics service

- `bcwebsitestatistics` adds the Google Analytics script to pages and can report shop orders; you must set your own
  tracking id (`Urchin`) in `settings/override/bcwebsitestatistics.ini.append.php`
  ([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)).
- You can add another analytics service's script to the page layout instead
  ([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).
- A site that sends a Content Security Policy must allow the service's hosts
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Consent and privacy

Analytics services usually set cookies and send visitor data to a third party. In many countries that needs consent,
and your privacy notice must name the service. Read [Cookies](../../government/privacy/cookies.md),
[Cookie banners and consent](../../government/privacy/cookie-banners-and-consent.md) and
[Analytics](../../government/privacy/analytics.md) before you add one. The figures Exponential counts itself stay on
your server.

> **Note**
> Basics, not legal advice. Ask your data protection advisor which rules apply to you.

## Choosing a few good measures

Funders prefer three honest figures to thirty vague ones. Good measures for a non-profit site:

| Measure | Why it is good |
|---|---|
| Volunteer applications per month | Directly linked to a goal, counted by the site |
| Requests for help through the form | Shows people reached the service |
| Confirmed newsletter subscribers | Consent-based, hard to inflate |
| Downloads of a key resource | Shows use of what the grant produced |
| Searches with no results | Shows what to write next |

Avoid measures you cannot explain to a board member in one sentence.

## Setup checklist

- [ ] Three to five measures chosen, each tied to a goal.
- [ ] Search statistics switched on, if needed.
- [ ] `updateviewcount` cron part running, if page views matter.
- [ ] Analytics service decided, consent handled, privacy notice updated.
- [ ] Starting figures written down with the date.
- [ ] A monthly reminder to record the figures ([Monthly checklist](../checklists/monthly-checklist.md)).

## Related pages

- [Grants and the website](README.md)
- [Grant outcomes for funders](grant-outcomes-for-funders.md)
- [Statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
