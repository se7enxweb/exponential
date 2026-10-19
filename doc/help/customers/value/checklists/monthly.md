---
title: Monthly checklist
audience: customer, admin, businesses
category: customer-success
order: 30
summary: The monthly checklist for the owner of an Exponential site - releases and updates, backups, security signals, accounts, form data, content checks, costs and the store - with the questions to put to the host and partner and how long it takes.
keywords: monthly checklist, monthly routine, updates, 6.0.x release, backups, audit trail, accounts, form submissions, content checks, costs, host questions
type: help
---
# Monthly checklist

This page is the owner's monthly check, done in the first days of each month with answers from the host or partner.
It keeps the site current and safe, and the costs known. The businesses section has a fuller operations template for
teams that run the server themselves ([Monthly operations checklist](../../../businesses/templates/monthly-operations-checklist.md));
this list is the owner's share of it.

## The checklist

```text
MONTHLY CHECKLIST - month: ________     Owner: ________

RELEASES AND UPDATES (ask the partner)
[ ] Is there a new Exponential 6.0.x release? Planned for: ________
[ ] Extension updates due? Planned for: ________
[ ] After last month's update: was the upgrade check clean?

BACKUPS (ask the host)
[ ] Backups ran every day this month
[ ] Date of the last restore test: ________ (one each quarter)

SECURITY SIGNALS (ask the administrator)
[ ] Audit alerts of the month: any? Answered by: ________
[ ] Unusual failed sign-ins?
[ ] HTTPS certificate renews automatically or is valid for more than 30 days

ACCOUNTS
[ ] Leavers of the month disabled
[ ] New administrator roles intended
[ ] Unused accounts reviewed (Users > Check user accounts)

DATA
[ ] Form submissions processed; old ones removed as your retention schedule says
[ ] Privacy requests of the month closed

CONTENT
[ ] Broken links and content checks reviewed
[ ] Unused images and files tidied (content checks)

COSTS
[ ] Invoices of host, partner and services match the cost register

STORE (if you sell)
[ ] Store monthly checklist done
```

## Where each item is done

| Item | Where, or whom to ask | Read |
|---|---|---|
| New releases | Partner; security fixes are published with the next 6.0.x release | `SECURITY.md`, [Upgrading](../../../../guides/upgrading.md) |
| Upgrade check | **Setup > Upgrade check**; both checks only read | [Upgrade check](../../../../guides/upgrade-check.md) |
| Backups and restore tests | Host | [Backups that restore](../../../businesses/reliability/backups-that-restore.md) |
| Audit alerts | The audit dashboard, policy `audit/read` | [The audit trail](../../../../features/6.0/audit-trail.md) |
| HTTPS | Host | [TLS and HTTPS](../../../hosting/security/tls-and-https.md) |
| Accounts | **Users > Check user accounts** | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| Form submissions | **Setup > Collected information** | [Collected information](../../../../guides/collected-information.md) |
| Privacy requests | Your request log | [GDPR requests](../../commerce/gdpr-requests.md) |
| Content checks | `/contentcheck/...` | [The content checks](../../../../features/6.0/content-checks.md) |
| Costs | Your cost register | [Cost of ownership for owners](../cost-of-ownership-for-owners.md) |
| Store | **Store** | [Store monthly checklist](../../commerce/store-monthly-checklist.md) |

## Good answers from the host and partner

| Question | A good answer sounds like |
|---|---|
| Is there a new release? | "Yes, 6.0.x; we read the changelog, tested it on a copy, and propose to apply it on [date]." Or: "No new release this month." |
| Did backups run? | "Yes, every day; the last restore test was on [date] and took [time]." |
| Any alerts? | "Each alert of the month, with what caused it and what we did." |
| Is the upgrade check clean? | "Yes, no modified or missing files." |

An answer of "I think so" to any of these is a reason to ask again next week.

> **Note**
> A published release is never changed: a fix reaches you as the next release (`SECURITY.md`). Staying current is
> therefore the only way to receive fixes; plan several small updates a year rather than one large one
> ([Budgeting for upgrades and maintenance](../../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)).

## Related pages

- [Weekly checklist](weekly.md)
- [Quarterly checklist](quarterly.md)
- [Monthly operations checklist](../../../businesses/templates/monthly-operations-checklist.md)
- [Store monthly checklist](../../commerce/store-monthly-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
