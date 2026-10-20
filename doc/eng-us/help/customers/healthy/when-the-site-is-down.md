---
title: When the site is down
audience: customer, admin, businesses
category: customer-success
order: 21
summary: What the owner does in the first minutes when an Exponential site does not answer or shows an error page - check what visitors see, call the right person, decide, tell customers, and write it down - with what each error page means.
keywords: site down, outage, error page, incident, 503, 500, Something went wrong, maintenance, call the host, first steps
type: help
---
# When the site is down

Keep this page where you can find it without the site: print it, or save it with your
[escalation contacts](../partners-and-hosts/escalation-contacts-template.md). It is the owner's part of a bad day.
The technical work is done by your host or partner, following runbooks such as
[Site down](../../hosting/runbooks/site-down.md); the plan for a small team is
[Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md).

## The first five minutes

1. **Write down the time** you noticed and how (a customer, a monitor, yourself).
2. **See it yourself**, from two places: your computer and a phone on mobile data. Note exactly what the page says.
3. **Was something changed?** An upgrade, a deploy, a settings change or a big import today? "Errors that started
   right after a deploy or an update: the cause is almost always in that change"
   ([Site down](../../hosting/runbooks/site-down.md)).
4. **Call the right person** with what you saw ([When to call whom](../partners-and-hosts/when-to-call-whom.md)).
5. **Name a lead.** One person decides; the others do not change things in parallel.

## What the page tells you

From [Site down](../../hosting/runbooks/site-down.md):

| Visitors see | Likely cause | Who to call |
|---|---|---|
| "Down for maintenance" (503) | Maintenance mode is on | Whoever started the planned work; if nobody did, your host |
| "We'll be right back" (503) | The database could not be reached | Host |
| "The site is missing the software libraries it needs" (503) | Part of the installation is missing | Host or partner, whoever deployed last |
| "Something went wrong on our side" (500) with `ERR-...` | An error in the application | Partner or host; pass on the reference |
| A blank page, or an error with no reference | PHP failed before Exponential could log | Host |
| A browser connection or certificate warning | Server, network or certificate | Host ([Certificate expiry](../../hosting/runbooks/certificate-expiry.md)) |

If only one page fails, it is not an outage; report it normally ([Reporting problems](reporting-problems.md)).

## Decide

Three options, decided by the lead with the person fixing
([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)):

- **A quick fix**, when the cause is known and small.
- **Roll back the last change**, when the problem started with it.
- **A maintenance page while they work**, when customers meet errors and the fix takes longer. An honest message
  such as "We are fixing a problem. Back shortly." is better than errors
  ([Maintenance mode and planned work](maintenance-mode-and-planned-work.md)).

A restore from backup is the last resort, because everything since the backup is lost
([Backups](backups.md)).

## Tell people

- **Early and honest**, with the time of the next update, and keep it
  ([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)).
- **Not only on the broken site**: use the channels you agreed (mail, social media, phone message).
- **The same message** for everyone who answers customers.

## If it might be a break-in

Unknown administrator accounts, changed files or pages you did not publish change the priority from "back online
fast" to "contain and preserve evidence". Tell your host it is a suspected compromise; do not delete anything
yourself ([Compromised site](../../hosting/runbooks/compromised-site.md),
[Private security reports](private-security-reports.md)).

## Afterwards

- [ ] Fill in the [incident notes](incident-notes-template.md) while you remember.
- [ ] Within a week, a short review without blame: how did we find out, what slowed us down, what would have
      prevented it, what do we change ([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)).
- [ ] Ask your host or partner for their written account of cause and fix.

## Related pages

- [Monitoring](monitoring.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Runbooks for hosts](../../hosting/runbooks/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
