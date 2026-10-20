---
title: RPO and RTO as decisions
audience: government, project-manager, admin, hosting
category: public-sector
order: 110
summary: For service owners and continuity planners in public bodies. What a recovery point objective and a recovery time objective mean for an Exponential site, why they are organizational decisions and not properties of the software, and which technical measures meet which values.
keywords: RPO, RTO, recovery point objective, recovery time objective, continuity, decision, service owner, backup frequency, replication, restore time, measured, cost, risk
type: help
---
# RPO and RTO as decisions

This page is for service owners, continuity planners and IT managers in public bodies. It explains the recovery point
objective (RPO) and the recovery time objective (RTO) for a website, why they are decisions your organization makes,
and which technical measures meet which values. It gives no figures: the right values depend on what the site is for,
and only measured restore times may be promised.

## Contents

1. [Two questions](#two-questions)
2. [Who decides](#who-decides)
3. [What meets which objective](#what-meets-which-objective)
4. [Measure before you promise](#measure-before-you-promise)
5. [Writing the decision down](#writing-the-decision-down)
6. [Checklist](#checklist)

## Two questions

- **Recovery point (RPO)**: how much recent work may be lost? If the last usable backup is from midnight and the
  failure happens at four in the afternoon, a day's publishing must be redone.
- **Recovery time (RTO)**: how long may the site be unavailable before it is back?

Both are commitments a host can offer in a contract ([SLA options](../../../hosting/business/sla-options.md)); inside
a public body they are part of the service's continuity requirements.

## Who decides

The values come from the service, not from IT:

| Question for the service owner | Effect |
|---|---|
| What does the public lose if the site is down for an hour, a day, a week? | the RTO |
| How much editing happens per day, and could it be redone? | the RPO |
| Are there legal deadlines published only on this site? | a stricter RTO in those periods |
| Are forms or submissions collected that cannot be re-entered? | a stricter RPO |

IT then states what each value costs, and the organization chooses. Record the choice and its owner.

## What meets which objective

| Measure | Improves | Notes |
|---|---|---|
| Nightly database and file backup off site | RPO of up to a day; RTO of a restore | the baseline ([What to back up](what-to-back-up.md)) |
| More frequent database backups | RPO | consistent online dumps exist for SQLite (`.backup`), PostgreSQL and InnoDB |
| The database's own replication or point-in-time recovery | RPO close to the failure | PostgreSQL has it of its own; SQLite has none ([Database high availability](../availability/database-high-availability.md)) |
| A standby machine ready for restores | RTO | removes the time to obtain a machine |
| Several web servers and a database server | RTO for a web server failure | [High availability overview](../availability/high-availability-overview.md) |
| A practiced restore procedure | RTO | measured in [Restore testing](restore-testing.md) |

Note that uploads (files) must come from the same moment as the database for a clean restore; a database with a
better RPO than its files restores pages whose newest images are missing.

## Measure before you promise

"Name the restore time you measured in a test, not one you hope for"
([Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md)). Run a restore test,
time it from the decision to restore to "the checks pass", and use that as the basis of the RTO, with a margin you
choose. Repeat the measurement after growth or major changes.

## Writing the decision down

| Field | Content |
|---|---|
| Site | name, purpose |
| RPO | the value, and the measure that meets it |
| RTO | the value, the measured restore time and the date of the measurement |
| Owner | the service owner who decided |
| Exceptions | periods with stricter values (elections, deadlines) |
| Review | the date of the next review |

Put it in the [disaster recovery plan](disaster-recovery-plan.md) and the [hosting decision record](../hosting/hosting-decision-record.md).

## Checklist

- [ ] The service owner decided RPO and RTO, in writing.
- [ ] The technical measures meet them, including files at the same moment as the database.
- [ ] The RTO rests on a measured restore.
- [ ] Stricter periods are named.
- [ ] A review date is set.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Availability targets and service levels](../availability/availability-targets-and-service-levels.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
