---
title: Post-incident review
audience: government, admin, project-manager
category: public-sector
order: 150
summary: Holding a blameless review after an incident on a public website - building the timeline from the audit trail and logs, finding causes and contributing factors, agreeing actions, and the record your organization and auditors expect.
keywords: post-incident review, postmortem, lessons learned, blameless, timeline, root cause, contributing factors, actions, audit trail, evidence, continuous improvement
type: help
---
# Post-incident review

This page is for incident leads and service owners. A review turns an incident into improvements. Many public bodies
require one for serious incidents; even where not required, it is the cheapest way to avoid a repeat.

## Contents
1. [When to hold one](#when-to-hold-one)
2. [Build the timeline](#build-the-timeline)
3. [Causes and contributing factors](#causes-and-contributing-factors)
4. [Actions](#actions)
5. [The record](#the-record)
6. [Related pages](#related-pages)

## When to hold one

After every severity 1 incident, after any incident involving personal data or a security breach, and after a near
miss that could have been severe. Within a few working days, while memory is fresh. Blameless: the question is why
the system and process allowed it, not who to blame.

## Build the timeline

| Source | Gives |
|---|---|
| Incident notes | what people saw and did, with times |
| Audit trail | sign-ins, content changes, settings writes, cache clears, deploys, maintenance on and off, with times and users |
| `error.log`, `security.log`, `404.log` | when errors and attacks began and ended |
| Monitoring | when the outside check failed and recovered |
| Web server and Velocity logs | request volumes and statuses |

```bash
./console exp:audit search --from='<start>' --to='<end>' --limit=500 --allow-root-user
./console exp:logs --search=<keyword> --scope=all --from='<start>' --to='<end>'
```

Export the audit records for the file ([Export audit records for an auditor](../../tasks/operations/export-audit-records-for-an-auditor.md)).

## Causes and contributing factors

Ask for each: what happened, why was it possible, why was it not caught sooner, why did recovery take as long as it did.
Typical findings on Exponential sites: a cronjob part silently paused, a change deployed without staging, a role
broader than needed, an expired certificate without an owner, logs pruned before the investigation, backups never
restore-tested.

## Actions

Each action has an owner and a date, and is small enough to finish. Examples linked to this section:

| Finding | Action |
|---|---|
| Nobody noticed for hours | add an outside check ([External uptime monitoring](../monitoring/external-uptime-monitoring.md)) |
| Restore took too long | schedule restore tests ([Restore testing](../backups-and-recovery/restore-testing.md)) |
| Too many people with full access | access review ([Admin access reviews](../people-and-permissions/admin-access-reviews.md)) |
| Change broke production | staging step ([Staging and approval](../upgrades-and-change/staging-and-approval.md)) |
| Evidence missing | longer retention ([Log retention and rotation](../monitoring/log-retention-and-rotation.md)) |

## The record

Summary, impact (who could not do what, for how long), timeline, causes, actions with owners, and what went well.
Share it with the change board and the security team; publish a short public summary if your organization does so
for service incidents.

## Related pages
- [Incident response basics](incident-response-basics.md)
- [Data breach notification framing](data-breach-notification-framing.md)
- [Disaster recovery plan](../backups-and-recovery/disaster-recovery-plan.md)
- [Hosting runbooks](../../../hosting/runbooks/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
