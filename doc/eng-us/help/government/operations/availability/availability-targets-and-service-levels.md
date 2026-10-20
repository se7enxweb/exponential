---
title: Availability targets and service levels
audience: government, project-manager, hosting, admin
category: public-sector
order: 130
summary: For public bodies that set availability targets for a website, internally or in a contract with a provider. Which commitments are possible, what each depends on, how to measure them, and why the targets are your organization's decision, not a property of the software.
keywords: availability target, service level, SLA, OLA, uptime, maintenance windows, support response, recovery point, recovery time, contract, provider, measurement, continuity duty
type: help
---
# Availability targets and service levels

This page is for project managers, service owners and contract managers in public bodies who define how available a
site must be, either as an internal target or in a contract with a hosting provider. It answers which commitments
can be made, what each depends on, how to measure it, and which exclusions to state. The hosting page
[SLA options](../../../hosting/business/sla-options.md) covers the same ground from a provider's side.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The software publishes no service level](#the-software-publishes-no-service-level)
2. [Commitments you can define](#commitments-you-can-define)
3. [What each commitment depends on](#what-each-commitment-depends-on)
4. [Measuring](#measuring)
5. [Exclusions to state](#exclusions-to-state)
6. [Checklist](#checklist)

## The software publishes no service level

The Exponential project publishes no service level agreement. The only response commitment in the project's own files
is in `SECURITY.md`: a first answer to a private security report "within one business day"
([SLA options](../../../hosting/business/sla-options.md)). Every target on this page is therefore a decision of your
organization, met by your design, staff and suppliers.

## Commitments you can define

| Commitment | Means | Decided by |
|---|---|---|
| Availability | the site answers, measured from outside, in defined hours | service owner |
| Maintenance windows | planned work only in announced windows | service owner and change board |
| Support response | a first answer within a time, per severity | IT service desk |
| Recovery point (RPO) | at most this much recent content can be lost | service owner, with records management |
| Recovery time (RTO) | the site is back within this time after a failure | service owner, with IT |
| Security updates | a released security fix is applied within a time | security officer and IT |

RPO and RTO are covered on [RPO and RTO as decisions](../backups-and-recovery/rpo-and-rto-as-decisions.md).

## What each commitment depends on

| Commitment | Depends on | Pages |
|---|---|---|
| Availability | machines, network, engine, database, and how many single points of failure remain | [High availability overview](high-availability-overview.md) |
| Maintenance windows | the change calendar; maintenance mode | [Maintenance mode and planned downtime](maintenance-mode-and-planned-downtime.md) |
| Support response | staffing and on-call arrangements, not the software | your service desk |
| Recovery point | backup frequency, database replication | [Backup schedules and retention](../backups-and-recovery/backup-schedules-and-retention.md) |
| Recovery time | a tested restore and its measured duration | [Restore testing](../backups-and-recovery/restore-testing.md) |
| Security updates | following releases; a tested upgrade path | [Security updates and disclosure](../upgrades-and-change/security-updates-and-disclosure.md) |

Never promise a recovery time you have not measured in a test: "Name the restore time you measured in a test, not one
you hope for" ([Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md)).

## Measuring

- **From outside**: an uptime monitor requests the front page and the administration's sign-in page; the front page
  alone can be served from a cache while the database is down ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md)).
- **Planned maintenance**: a 503 with `Retry-After` during an announced window; configure the monitor so it does not
  count as an outage.
- **From inside**: `exp:velocity status` exits non-zero when the server does not answer; **Setup > System
  information** runs health checks ([System information](../../../../guides/system-information.md)).
- **Restores**: record the duration of every restore test.

## Exclusions to state

Typical exclusions in an internal target or a contract, to adapt:

- announced maintenance windows;
- failures of services outside the scope (the organization's network, DNS, a central proxy, third-party services
  activated on the site);
- content errors made by editors (handled by restore or correction, not by availability);
- force majeure, as your contract templates define it.

## Checklist

- [ ] Each commitment has an owner and a value decided by the organization.
- [ ] The design and the tested restore time support the values.
- [ ] External monitoring measures availability and knows maintenance windows.
- [ ] Exclusions are written down.
- [ ] A provider's contract matches the internal targets, reviewed by legal counsel.

## Related pages

- [Availability](README.md) index of this folder
- [Support tiers](../../../hosting/business/support-tiers.md)
- [Disaster recovery plan](../backups-and-recovery/disaster-recovery-plan.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
