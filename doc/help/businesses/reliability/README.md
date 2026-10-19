---
title: Reliability for businesses
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 1
summary: The landing page of the reliability section. It explains why a business can count on Exponential staying stable, and how to run a site so that customers are never turned away, with a table of every page in the section.
keywords: reliability, stability, uptime, availability, backups, monitoring, upgrades, maintenance, Velocity, caching, incident response, business continuity
type: help
---
# Reliability for businesses

This section is for business owners, managers and decision makers who depend on an Exponential website, shop,
intranet or app backend to earn money or to serve members, and for the person who keeps that site running. It
answers two questions: why you can rely on Exponential staying stable over many years, and what you must do so that
the site never fails your customers.

The pages are written in plain language first. Each one then names the commands, settings and administration pages
that do the work, and links to the technical guide where the details live, so nothing is duplicated.

## Contents

1. [What this section covers](#what-this-section-covers)
2. [Who it is for](#who-it-is-for)
3. [Every page in this section](#every-page-in-this-section)
4. [A suggested order](#a-suggested-order)
5. [The five habits in one list](#the-five-habits-in-one-list)
6. [Neighboring sections](#neighboring-sections)

## What this section covers

Reliability is not one feature. It is the sum of a stable product, a careful release line, and a few habits that
the team running the site keeps every week. This section covers all three:

- **The product**: a long-lived release line, published versions that never change, a supported PHP range, and a
  maintained security policy (SECURITY.md).
- **The tools that ship with it**: health checks, logs with error references, a tamper-evident audit trail,
  maintenance mode, an upgrade check, caches, the Exponential Velocity server and its watchdog.
- **The habits**: backups that are restored on purpose, monitoring that someone reads, upgrades that are rehearsed,
  and an incident plan that fits a small team.

> **Note**
> No page in this section promises an uptime percentage. The project publishes no such figure, and availability
> depends on your hosting, network and staff as much as on the software
> ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md#uptime-in-a-hosting-plan)).
> What the pages give you is the means to keep a promise you make yourself.

## Who it is for

| You are | Start with |
|---|---|
| An owner or manager deciding whether to rely on Exponential | [Why stability matters](why-stability-matters.md), then [Release line and support policy](release-line-and-support-policy.md) |
| A manager who has to budget for operations | [Uptime practice](uptime-practice.md) and the [Reliability scorecard](reliability-scorecard.md) |
| The person who runs the site day to day | [Health checks and monitoring](health-checks-and-monitoring.md), [Backups that restore](backups-that-restore.md), [Cronjobs and background jobs](cronjobs-and-background-jobs.md) |
| A small team that has to react when something breaks | [Incident response for small teams](incident-response-for-small-teams.md) |
| A shop owner expecting a sale or a campaign | [Peak traffic planning](peak-traffic-planning.md) and [Caching for availability](caching-for-availability.md) |

## Every page in this section

| Page | What it answers |
|---|---|
| [Why stability matters](why-stability-matters.md) | What an unstable site costs a business, and what Exponential does about it |
| [Release line and support policy](release-line-and-support-policy.md) | Which versions are maintained, how releases work, which PHP versions run it |
| [Uptime practice](uptime-practice.md) | The routine that keeps a site available, week by week |
| [Health checks and monitoring](health-checks-and-monitoring.md) | What to watch from outside and inside, and with which commands |
| [Logs and error references](logs-and-error-references.md) | How a customer's "something went wrong" becomes a log line you can act on |
| [Audit trail for operations](audit-trail-for-operations.md) | Who changed what, and proof that the record was not edited |
| [Backups that restore](backups-that-restore.md) | What to back up, how, and how often |
| [Restore drills](restore-drills.md) | How to prove a backup works before you need it |
| [Velocity for businesses](velocity-for-businesses.md) | What the bundled application server gives a business, in plain words |
| [Graceful deploys](graceful-deploys.md) | Shipping changes without dropping visitors' requests |
| [Caching for availability](caching-for-availability.md) | How caches keep a site answering under load |
| [Static and response cache](static-and-response-cache.md) | The two caches that answer without running the application |
| [Peak traffic planning](peak-traffic-planning.md) | Preparing for a sale, a campaign or a newsletter send |
| [Clustering and high availability](clustering-and-high-availability.md) | When one server is not enough, and what more servers cost |
| [Database choice for reliability](database-choice-for-reliability.md) | Which database fits which business, in the project's official order |
| [Maintenance mode and planned work](maintenance-mode-and-planned-work.md) | Taking the site offline cleanly, and telling customers |
| [Upgrades without surprises](upgrades-without-surprises.md) | Planning and rehearsing an upgrade |
| [Upgrade check and file consistency](upgrade-check-and-file-consistency.md) | Proving the installation matches the release before and after an upgrade |
| [Preview before you publish](preview-before-you-publish.md) | Drafts, versions, approval and preview, so a mistake never reaches customers |
| [Incident response for small teams](incident-response-for-small-teams.md) | A plan for the bad day that fits two or three people |
| [Cronjobs and background jobs](cronjobs-and-background-jobs.md) | The work nobody waits for, and how to know it ran |
| [Reliability scorecard](reliability-scorecard.md) | A checklist to score your own operations |
| [Reliability FAQ](reliability-faq.md) | Short answers to the questions managers ask most |

## A suggested order

1. Read [Why stability matters](why-stability-matters.md) and
   [Release line and support policy](release-line-and-support-policy.md) to understand what the product gives you.
2. Read [Uptime practice](uptime-practice.md) to see the whole routine on one page.
3. Set up the basics: [Health checks and monitoring](health-checks-and-monitoring.md),
   [Backups that restore](backups-that-restore.md), [Cronjobs and background jobs](cronjobs-and-background-jobs.md).
4. Prove them: [Restore drills](restore-drills.md) and
   [Incident response for small teams](incident-response-for-small-teams.md).
5. Plan change: [Upgrades without surprises](upgrades-without-surprises.md),
   [Maintenance mode and planned work](maintenance-mode-and-planned-work.md),
   [Graceful deploys](graceful-deploys.md).
6. Plan growth: [Caching for availability](caching-for-availability.md),
   [Peak traffic planning](peak-traffic-planning.md),
   [Clustering and high availability](clustering-and-high-availability.md).
7. Score yourself with the [Reliability scorecard](reliability-scorecard.md) every quarter.

## The five habits in one list

- [ ] Something outside your server checks the site and the administration login page, and alerts a person.
- [ ] A backup of the database and the files exists from the last seven days, and has been restored at least once
      on another machine ([Operating a site, the weekly checklist](../../../guides/operating-a-site.md#10-a-weekly-checklist)).
- [ ] The cronjobs run, and **Setup > System information** shows no failed health check.
- [ ] Every upgrade starts with a backup and the upgrade check, inside a maintenance window.
- [ ] Two people know where the logs, the audit trail and the backups are.

## Neighboring sections

- Businesses start here and
  Why businesses rely on Exponential: the section's entry pages.
- [Costs](../costs/README.md): the cost of downtime and the return on stability, in numbers you fill in yourself.
- [Security](../security/README.md): security and data protection basics for businesses.
- [Choosing hosting and partners](../choosing/README.md): who runs the servers for you.
- [Templates](../templates/README.md): checklists to copy, including a monthly operations checklist.
- Governments have their own section: Governments start here.
- The technical side for hosting staff: [Hosting operations](../../hosting/operations/README.md) and
  [Runbooks](../../hosting/runbooks/README.md).
- For leaders: [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md) and
  [Risk management](../../leadership/risk-management.md).

## Related pages

- [Operating a site](../../../guides/operating-a-site.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
