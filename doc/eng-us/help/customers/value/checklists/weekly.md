---
title: Weekly checklist
audience: customer, editor, admin
category: customer-success
order: 20
summary: Thirty minutes a week for the site manager of an Exponential site - content that needs attention, broken links and not-found pages, accounts, searches without results, the store, and the questions for the host - with the administrator's own weekly checks.
keywords: weekly checklist, weekly routine, broken links, 404, not found, search phrases, accounts, backups, host questions, site manager
type: help
---
# Weekly checklist

This page is the weekly check for the site manager, ideally in a fixed slot. It looks a little further than the daily
list: what has drifted during the week. The technical weekly checks belong to your administrator or host; the last
section says what to ask them.

## The checklist

```text
WEEKLY CHECKLIST - week of ________     Done by: ________

CONTENT
[ ] Old drafts reviewed: finish, hand over, or remove (My drafts)
[ ] Pages with dates (events, offers, opening hours) still right
[ ] New content of the week has images with alternative text

LINKS AND ADDRESSES
[ ] Broken links list looked at (Setup > Link management), the worst fixed
[ ] Not-found pages of the week: any address visitors use that needs a redirect?

VISITORS
[ ] Search terms without results noted; content or synonyms to add
[ ] Questions from visitors this week: any the site should answer?

ACCOUNTS
[ ] New staff accounts intended; leavers of the week disabled
[ ] Nobody shares an account

STORE (if you sell)
[ ] Store weekly checklist done

ASK THE ADMINISTRATOR OR HOST (answer "yes" expected)
[ ] Did every backup of the week run?
[ ] Do the cron jobs run?
[ ] Is the audit chain intact?
[ ] Anything new in the error log?
```

## Where each item is done

| Item | Where | Read |
|---|---|---|
| Drafts | **My drafts** | [Publishing and versions](../../../publishing-and-versions.md) |
| Broken links | **Setup > Link management** (`/url/list`); the link check runs in the `infrequent` cron part | [Broken links and content checks](../../../creators/website-creators/link-and-content-checks.md) |
| Not-found pages | `404.log`, read in **Setup > Log files** | [Request logs](../../../../features/6.0/request-logs.md) |
| Redirects for moved pages | URL aliases and wildcards | [URLs and aliases](../../../../guides/urls-and-aliases.md) |
| Search terms | The site's search statistics | [Site search](../../../creators/website-creators/site-search.md) |
| Accounts | **Users > Check user accounts** | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| Store | Store dashboard | [Store weekly checklist](../../commerce/store-weekly-checklist.md) |

## The administrator's weekly checks

The guide for operators lists a weekly checklist that your administrator or host can run in a few minutes
([Operating a site](../../../../guides/operating-a-site.md), section 10):

1. The cache status ends in PASS.
2. The audit status shows every chain intact.
3. The error log shows nothing new that is not understood.
4. The cron entries are still installed.
5. A backup from the last seven days exists and has been restored at least once on another machine.

You do not need to run these yourself. Ask for the five answers once a week, by mail or in a short call.

> **Note**
> A "yes" to "did the backup run" is not the same as a backup that restores. Ask when the last restore test was,
> and plan one each quarter ([Quarterly checklist](quarterly.md)).

## Related pages

- [Daily checklist](daily.md)
- [Monthly checklist](monthly.md)
- [Store weekly checklist](../../commerce/store-weekly-checklist.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
