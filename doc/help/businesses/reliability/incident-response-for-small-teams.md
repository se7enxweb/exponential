---
title: Incident response for small teams
audience: businesses, admin, project-manager, editor
category: business-solutions
order: 200
summary: A plan for the day the site fails that fits a team of two or three people, with roles, the first fifteen minutes, customer communication, where Exponential's tools fit in, the runbooks to follow, and the review afterwards.
keywords: incident response, outage, site down, runbook, on call, communication, status message, post-incident review, small team, security incident, escalation
type: help
---
# Incident response for small teams

This page is for small businesses, stores, libraries and non-profits where "the IT team" is one or two people and the
owner. It gives a plan for the bad day that fits that size: who does what, the first fifteen minutes, how to talk to
customers, which Exponential tools and runbooks to use, and how to learn from it afterwards. The technical runbooks
are in [Hosting runbooks](../../hosting/runbooks/README.md).

## Contents

1. [Prepare on a quiet day](#prepare-on-a-quiet-day)
2. [Three roles, even for two people](#three-roles-even-for-two-people)
3. [The first fifteen minutes](#the-first-fifteen-minutes)
4. [Which runbook](#which-runbook)
5. [Talking to customers](#talking-to-customers)
6. [Security incidents are different](#security-incidents-are-different)
7. [Closing the incident](#closing-the-incident)
8. [The review afterwards](#the-review-afterwards)

## Prepare on a quiet day

An incident plan is written before the incident. Keep one page, printed and stored where the team can find it
without the website:

- [ ] Who is called first, and who is the deputy, with phone numbers.
- [ ] How to reach the hosting partner's support, and what your contract says about response
      (Contracts and SLAs basics).
- [ ] Where the backups are and who can restore them ([Backups that restore](backups-that-restore.md)).
- [ ] How to sign in to the server and to the administration, kept in a password manager, never on the printed page.
- [ ] Where customers will be told: social media, a phone message, a mailing.
- [ ] The links to the runbooks below.

Rehearse once a quarter: pretend the site is down and walk through this page together
([Uptime practice](uptime-practice.md#every-quarter)).

## Three roles, even for two people

| Role | Does | Does not |
|---|---|---|
| **Lead** | Decides: fix, roll back, maintenance page, call the host. Keeps the time | Type commands while deciding |
| **Fixer** | Investigates and repairs, following the runbook | Talk to customers |
| **Communicator** | Tells customers, staff and the owner what is happening and when the next update comes | Promise times the fixer has not given |

With two people, the lead also communicates. With one person, do the steps in order and write down the time of each.

## The first fifteen minutes

1. **Write down the time** the problem was noticed and how.
2. **See it yourself**: open the site and the administration. Note what the page shows and the status
   ([Runbook: site down, first five minutes](../../hosting/runbooks/site-down.md#first-five-minutes)).
3. **Was something changed?** "Errors that started right after a deploy or an update: the cause is almost always in
   that change" ([Runbook: site down](../../hosting/runbooks/site-down.md#which-page-does-the-visitor-see)). The audit
   trail's `system` channel shows recent deploys, settings writes and maintenance
   ([Audit trail for operations](audit-trail-for-operations.md#using-it-after-an-incident)).
4. **Read the newest errors**: `php bin/php/console exp:logs --tail=var/log/error.log --lines=50`, or **Setup > Log
   files** if the administration works. If a customer sent an `ERR-` reference, search for it
   ([Logs and error references](logs-and-error-references.md)).
5. **Check the machine**: disk space, memory, load. "A full disk causes most of the symptoms" of a site that is down
   (same runbook).
6. **Decide**: a quick fix, a rollback of the last change, or a maintenance page while you work.

If customers are meeting errors and the fix will take more than a few minutes, put up an honest maintenance page
([Maintenance mode and planned work, emergencies](maintenance-mode-and-planned-work.md#emergencies)):

```bash
php bin/php/maintenance.php on --message="We are fixing a problem. Back shortly." --allow-admin --allow-root-user
```

## Which runbook

Exponential's hosting section has step-by-step runbooks ([Runbooks](../../hosting/runbooks/README.md)):

| Symptom | Runbook |
|---|---|
| The site does not answer, or shows an error page | [Site down](../../hosting/runbooks/site-down.md) |
| The site answers, but slowly | [Slow site](../../hosting/runbooks/slow-site.md) |
| The disk is full | [Disk full](../../hosting/runbooks/disk-full.md) |
| Velocity does not start | [Velocity will not start](../../hosting/runbooks/velocity-will-not-start.md) |
| HTTPS warnings in the browser | [Certificate expiry](../../hosting/runbooks/certificate-expiry.md) |
| Data must come back from a backup | [Restore from backup](../../hosting/runbooks/restore-from-backup.md) |
| A PHP upgrade went wrong | [PHP upgrade](../../hosting/runbooks/php-upgrade.md) |
| Signs of an intrusion | [Compromised site](../../hosting/runbooks/compromised-site.md) |

The site-down runbook maps what customers see to the cause. For example, "Something went wrong on our side" with an
`ERR-` reference is an uncaught error to look up by its reference; "Down for maintenance" means maintenance mode is
on; "The site is missing the software libraries it needs" means `vendor/` is missing
([Runbook: site down](../../hosting/runbooks/site-down.md#which-page-does-the-visitor-see)).

## Talking to customers

- **Early and honest**: "Our shop is currently unavailable. We are working on it and will update you by 14:30."
- **A next update time**, even if there is no news yet, and keep it.
- **Not on the broken site alone**: use the channels from your plan.
- **No technical detail and no blame**: customers want to know when they can order again.
- **When it is over**: say so, and if orders or messages may have been lost, say what customers should do.

Staff who answer phones or e-mail need the same message, so they say the same thing.

## Security incidents are different

If you suspect an intrusion (unknown administrator accounts, changed files, pages you did not publish), the priority
changes from "back online fast" to "contain and preserve evidence". Follow the
[Compromised site](../../hosting/runbooks/compromised-site.md) runbook: contain, preserve evidence, investigate,
eradicate and rebuild, recover, report and notify.

Exponential's tools help here:

- The audit trail shows sign-ins, role assignments and settings changes, and `audit.php verify` shows whether its
  record was tampered with ([Audit trail for operations](audit-trail-for-operations.md#proof-the-record-was-not-changed)).
- The upgrade check shows release files that were modified
  ([Upgrade check and file consistency](upgrade-check-and-file-consistency.md)).
- `security.log` lists requests that looked like probes or attacks
  ([Logs and error references](logs-and-error-references.md#the-logs-and-what-each-is-for)).

If the cause is a security problem in Exponential itself, report it privately as described in
SECURITY.md, never in a public tracker. Personal data breaches may have legal reporting
duties; see Security basics for businesses and ask your data
protection officer or lawyer. This page is not legal advice.

## Closing the incident

From [Runbook: site down, after the incident](../../hosting/runbooks/site-down.md#after-the-incident):

- [ ] Clear the caches the fix needs, and with Velocity its response cache after any restart.
- [ ] Verify: front page, administration, a sign-in, a publish; for a shop, a test order up to payment.
- [ ] Switch maintenance off if it was on.
- [ ] Tell customers and staff it is over.
- [ ] Write the incident note: start, detection, cause, fix, end, what prevents a repeat.
- [ ] If the cause is a bug in Exponential, report it ([When something goes wrong](../../when-something-goes-wrong.md#filing-a-bug-in-exponential)).

## The review afterwards

Within a week, spend half an hour together on four questions, without blame:

1. How did we find out, and could we have found out sooner? (Monitoring, alerts.)
2. What slowed us down? (Access, knowledge, missing runbook steps.)
3. What would have prevented it? (A check, a rehearsal, a rule about changes.)
4. What do we change, who does it, by when?

Put the actions in your [Reliability scorecard](reliability-scorecard.md) and the risk register
([Risk management](../../leadership/risk-management.md)). The cost of the incident, in your own figures, goes into the
Cost of downtime worksheet.

## Related pages

- [When something goes wrong](../../when-something-goes-wrong.md)
- [Health checks and monitoring](health-checks-and-monitoring.md)
- [Restore drills](restore-drills.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
