---
title: Daily checklist
audience: customer, editor, admin
category: customer-success
order: 10
summary: Ten minutes a day for whoever looks after an Exponential site - is the site up, what is waiting for approval, which forms came in, the store's orders, alerts in the mailbox - and what to do when something looks wrong.
keywords: daily checklist, daily routine, site up, pending items, approvals, form submissions, orders, audit alerts, mailbox, site owner
type: help
---
# Daily checklist

This page is the ten-minute check for the person who looks after the site on a given day: often the site manager or
a senior editor, in rotation. It catches the problems visitors would otherwise report first. Copy the list and keep
it where the team works.

## The checklist

```text
DAILY CHECKLIST - date: ________     Done by: ________

SITE
[ ] The front page and one inner page open normally (also on a phone)
[ ] Nothing on the front page is out of date (events past, offers ended)

WORK WAITING
[ ] My pending items / items waiting for approval looked at
[ ] Drafts due today are on their way (scheduled items, campaign pages)

WHAT CAME IN
[ ] New form submissions read and handed on (Setup > Collected information)
[ ] New registrations look genuine (the "New user registered" mails)
[ ] The shared mailbox: alert mails, order copies, replies from visitors

STORE (if you sell)
[ ] Store daily routine done (dashboard, orders waiting, failed payments)

IF SOMETHING LOOKS WRONG
[ ] Error page with a reference (ERR-...) noted and sent to the administrator
[ ] Host or partner called if the site is down or very slow
```

## Where each item is done

| Item | Where | Read |
|---|---|---|
| Items waiting for approval | **My pending items** in the administration | [Publishing and versions](../../../publishing-and-versions.md) |
| Scheduled items | The workflow's *Wait until date* event runs with the cron job | [Workflows](../../../../guides/workflows.md) |
| Form submissions | **Setup > Collected information**, policy `infocollector/read` | [Collected information](../../../../guides/collected-information.md) |
| Registration mails | Sent to `RegistrationEmail`, else `AdminEmail`, with signed links to approve or reject | [Signed links in the registration mail](../../../../features/6.0/registration-admin-links.md) |
| Alert mails | The audit trail's alert rules mail the site's `AdminEmail` by default | [The audit trail](../../../../features/6.0/audit-trail.md) |
| Store | **Store** dashboard | [Daily store routine](../../commerce/daily-store-routine.md) |
| Error references | An error page shows a reference such as `ERR-0123456789` | [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md) |

## When something looks wrong

| You see | First step |
|---|---|
| A page with an error reference | Send the reference, the address and the time to your administrator; they find the details in **Setup > Log files** |
| "This form has expired" | Usually a page left open too long; reload and send again ([Form expired page](../../../../features/6.0/form-expired-page.md)) |
| The whole site is down or a maintenance page you did not expect | Call the host ([Site down](../../../hosting/runbooks/site-down.md)) |
| A page you did not change looks changed | Check its versions; the audit trail shows who did what ([The audit trail](../../../../features/6.0/audit-trail.md)) |

More first steps are on [When something goes wrong](../../../when-something-goes-wrong.md).

> **Tip**
> Ten minutes is enough when the list is done every day. If it takes longer, something on it is waiting too long:
> find out what, and fix the cause.

## Related pages

- [Weekly checklist](weekly.md)
- [Daily store routine](../../commerce/daily-store-routine.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)
- [Checklists for owners](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
