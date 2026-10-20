---
title: SLA template
audience: customer, hosting, partner
category: customer-success
order: 6
summary: A template for a service level agreement between a site owner and the host or partner of an Exponential site - availability, maintenance windows, support, backups, recovery, security updates, exclusions and reporting - with fields to fill in and no figures. Not legal advice.
keywords: SLA, template, service level agreement, availability, RPO, RTO, maintenance window, security updates, exclusions, reporting, not legal advice
type: help
---
# SLA template

**Template. Not legal advice.** This is a structure for a service level agreement (SLA) for an Exponential site,
written from the owner's side. It follows the commitments described in
[SLA options](../../hosting/business/sla-options.md) and
[Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md). Every value in square brackets is
a field for you and your provider to agree; this page gives no figures, because the right ones depend on your site,
your budget and what your provider can staff. Have your legal adviser review the result.

## How to use it

1. Decide your needs first: how much data loss and downtime you can accept
   ([Backups](../healthy/backups.md), [Cost of downtime worksheet](../../businesses/costs/cost-of-downtime-worksheet.md)).
2. Fill in the fields with your provider. Delete sections that do not apply.
3. Attach the [Who does what](who-does-what.md) table and the
   [Support agreement](support-agreement-template.md).
4. Agree how each value is measured and reported. A promise without a measure cannot be checked.

## Parties and scope

| Field | Value |
|---|---|
| Customer | [organization name] |
| Provider | [host or partner name] |
| Sites covered | [site addresses and siteaccesses] |
| Environments covered | [live / staging / test] |
| Valid from, version | [date], [version of this agreement] |

## Service levels

| Commitment | Agreed value | Measured by | Reported |
|---|---|---|---|
| Availability | [percentage] per [month] | [external check] on [address of a page that touches the database] | [monthly] |
| Maintenance windows | [day and time, time zone] | Announced [days ahead] by [channel] | Each window |
| Support first response | As in the support agreement | The provider's ticket system | [monthly] |
| Backups | [what] every [interval], kept [period], stored [where] | Backup timestamps | [monthly] |
| Recovery point (RPO) | [time] | Backup timestamps | [monthly] |
| Recovery time (RTO) | [time] | The duration of the last restore test | After each test |
| Restore tests | Every [interval], result sent to [role] | The test record ([Restore test](../healthy/restore-test.md)) | After each test |
| Exponential security releases | Applied within [agreed time] of release | Site version and the audit trail's deploy records | [monthly] |
| PHP and OS security updates | Applied within [agreed time] | Provider's change log | [monthly] |

> **Tip**
> For security, the wording that can be measured is "a released fix is applied within a time"
> ([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)).

## Exclusions

Suppliers usually exclude the following ([SLA options](../../hosting/business/sla-options.md)). Read them as
carefully as the promises.

| Exclusion | Agreed |
|---|---|
| The customer's own code, templates and extensions, and configuration the customer changed | [yes / no / as follows] |
| Extensions the provider did not supply | [yes / no / as follows] |
| Outages caused by the customer's content, such as an import that fills the disk | [yes / no / as follows] |
| Planned maintenance in announced windows | [yes / no / as follows] |
| [other exclusion] | [terms] |

## When a level is missed

| Field | Value |
|---|---|
| Credits or other remedy | [the remedy, if any] |
| How the customer claims it | [process, deadline] |
| Repeated misses | [what happens, for example a review or the right to end the contract] |

## Reporting

| Report | Content | Frequency | To |
|---|---|---|---|
| Service report | Availability, incidents, restore tests, updates applied, versions | [monthly / quarterly] | [role] |
| Incident report | Timeline, cause, fix, data lost, actions ([Incident notes template](../healthy/incident-notes-template.md)) | After each severity 1 or 2 incident | [role] |

## Review

| Field | Value |
|---|---|
| Review date | [date each year] |
| Review owner | [role on each side] |
| Basis | [Reviewing your provider each year](reviewing-your-provider-each-year.md) |

## Signatures

| | Customer | Provider |
|---|---|---|
| Name and role | [name, role] | [name, role] |
| Date | [date] | [date] |

## Related pages

- [Contracts and SLAs](contracts-and-slas.md)
- [Support agreement template](support-agreement-template.md)
- [Maintenance agreement template](maintenance-agreement-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
