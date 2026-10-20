---
title: After an incident
audience: customer, admin, hosting
category: customer-success
order: 70
summary: What the owner of an Exponential site does in the days after an outage, a data mistake, a failed update or a security problem - confirm it is over, find out what happened with the logs and the audit trail, tell the people affected, fix the cause, and keep notes.
keywords: incident, after an incident, post-incident review, outage, error reference, log viewer, audit trail, security report, lessons learned, incident notes
type: help
---
# After an incident

This page is the checklist for the owner once the immediate problem is over: the site was down, content was lost or
changed, an update went wrong, or someone may have got in. The work during the incident belongs to your host or
partner and their runbooks; this page is about the days after, so the same thing does not happen again. The general
approach for small teams is on [Incident response for small teams](../../../businesses/reliability/incident-response-for-small-teams.md).

## The checklist

```text
AFTER AN INCIDENT - [short name]     Started: ________   Ended: ________   Owner: ________

IS IT OVER?
[ ] The site works for visitors and staff (front page, a form, the store if any)
[ ] Maintenance mode is off, if it was used
[ ] Scheduled jobs run again (cron jobs not left paused)

WHAT HAPPENED (with the administrator or host)
[ ] Error references collected (ERR-...) and looked up in the logs
[ ] The audit trail of the time read: who did what
[ ] Cause written in one or two sentences
[ ] Whether personal data or payments were involved: yes / no / unknown

WHO NEEDS TO KNOW
[ ] Team informed of what happened and what changes
[ ] Customers or members affected informed, if needed
[ ] Payment provider informed, if payments may be involved
[ ] Your adviser asked whether a notification duty applies (not legal advice)
[ ] A vulnerability in Exponential reported privately to security@se7enx.com
[ ] A bug in Exponential reported in the issue tracker

FIX THE CAUSE
[ ] What will prevent it next time, with an owner and a date
[ ] Checklists updated, if a check would have caught it
[ ] Backups and a restore test confirmed after any restore

NOTES
[ ] Incident notes filed with the timeline, cause, effect, actions
```

## Where each item is done

| Item | Where | Read |
|---|---|---|
| Maintenance mode | **Setup > Maintenance** | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| Paused cron parts | **Setup > Cronjobs** | [Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md) |
| Error references | **Setup > Log files**, field **Error reference** | [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md) |
| The errors of the last day | **Setup > Log files > Error digest** | [The log viewer](../../../../features/6.0/log-viewer.md) |
| Who did what | The audit trail; it records sign-ins, content changes, settings writes, cache clears, deploys, orders and VAT changes | [The audit trail](../../../../features/6.0/audit-trail.md) |
| Content lost or changed | Versions and the trash | [Publishing and versions](../../../publishing-and-versions.md) |
| A restore | Host | [Restore from backup](../../../hosting/runbooks/restore-from-backup.md) |
| A compromised site | Host | [Compromised site](../../../hosting/runbooks/compromised-site.md) |

## Reporting to the project

- **A security problem** in Exponential is reported privately to security@se7enx.com, never in a public tracker
  (`SECURITY.md`, [When something goes wrong](../../../when-something-goes-wrong.md)). Leave out passwords, secrets and
  personal data.
- **A bug** in Exponential goes to <https://github.com/se7enxweb/exponential/issues>. The help's report page in the
  administration, `/exphelp/report`, prefills the issue and lists the diagnostics to copy, never passwords, keys,
  database hosts or personal data ([The help system](../../../../features/6.0/help-system.md)).
- **A problem with your own site's code or setup** goes to your partner.

## Incident notes

Keep one short note per incident, even a small one:

```text
INCIDENT NOTE - [short name]
When it started / was noticed / ended: ________ / ________ / ________
What visitors or staff saw: ______________________________________________
Cause: ___________________________________________________________________
Effect (pages, orders, data, hours): _____________________________________
What we did during it: ___________________________________________________
What we change so it does not happen again (owner, date): ________________
Reported to the project: no / bug # ______ / security report sent ________
```

> **Note**
> Not legal advice. If personal data or payments may have been involved, notification duties can have short
> deadlines. Ask your lawyer or data protection officer at once, and your payment provider if payments are involved.

## Related pages

- [Incident response for small teams](../../../businesses/reliability/incident-response-for-small-teams.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)
- [Daily checklist](daily.md)
- [Checklists for owners](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
