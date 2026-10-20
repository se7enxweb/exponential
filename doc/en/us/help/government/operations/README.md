---
title: Running Exponential in the public sector
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: The operations part of the Governments section: hosting, availability, backups, monitoring, incidents, upgrades and change, people and permissions, and editorial workflows, written for in-house IT teams and the managers who answer for a public website.
keywords: operations, public sector, government IT, in-house IT, hosting, availability, backup, recovery, monitoring, incident, upgrade, change management, roles, permissions, approvals, workflows, continuity
type: help
---
# Running Exponential in the public sector

This part of the Governments section is for the people who keep a public-sector website running after it goes live:
the in-house IT team, the web team, the service owner and the project manager who answers to a change board or an
auditor. It explains how Exponential is hosted, kept available, backed up, monitored, upgraded and governed, and how
staff permissions and editorial approvals are set up. Every page is built on the installation's own documentation:
the guides in `doc/eng-us/guides/`, the feature pages in `doc/eng-us/features/6.0/`, the installation book in `doc/eng-us/install/` and the
pages written for hosting companies in [Hosting](../../hosting/hosting-companies-start-here.md). Those pages are
linked, not repeated; the pages here add what changes when the operator is a public body: change boards, procurement
rules, continuity duties, audits and the expectation that everything is recorded.

## Contents
1. [The eight folders](#the-eight-folders)
2. [Step-by-step tasks](#step-by-step-tasks)
3. [Reading order](#reading-order)
4. [What you need before you start](#what-you-need-before-you-start)
5. [How the facts were checked](#how-the-facts-were-checked)
6. [Related pages](#related-pages)

## The eight folders

| Folder | Answers |
|---|---|
| [Hosting](hosting/README.md) | Where and how to run Exponential: on premise, in a government or sovereign cloud, in containers; which database, which web server; separate environments and an administration of its own; where data lives. |
| [Availability](availability/README.md) | Keeping the site up: several web servers, shared storage, database replicas, the caches in front of PHP, planned downtime with maintenance mode, capacity. |
| [Backups and recovery](backups-and-recovery/README.md) | What to back up, per database engine; restore tests; a disaster recovery plan; recovery point and recovery time as decisions your organization makes. |
| [Monitoring](monitoring/README.md) | The log viewer, error digest and log health, the request logs (`404.log`, `security.log`), CSP reports, audit alerts, cronjobs, system information and outside uptime checks. |
| [Incidents](incidents/README.md) | Runbooks for the bad days: site down, database unreachable, defacement, a compromised account, probe floods, a broken deploy, a mass content error, stale caches, mail problems. |
| [Upgrades and change](upgrades-and-change/README.md) | Upgrading safely, immutable releases, change advisory boards, staging and approval, configuration managed by deployment, rollback. |
| [People and permissions](people-and-permissions/README.md) | Roles, policies and limitations; least-privilege role designs for web teams, department editors, approvers, auditors and contractors; joiners, movers and leavers; two-step sign-in; access reviews. |
| [Editorial workflows](editorial-workflows/README.md) | Approval before publishing, the collaboration inbox, the two-person rule, legal review, multilingual approvals, an emergency bypass that stays on the record, content ownership. |

## Step-by-step tasks

Two task folders hold short, numbered procedures that point back to the folders above:

- [Operations tasks](../tasks/operations/README.md): maintenance windows, backups and restores, caches, cronjobs,
  logs, the audit trail, upgrade checks, certificates, replicas.
- [People and permissions tasks](../tasks/people-and-permissions/README.md): roles for a department, approvers,
  joiners and leavers, password resets, contractors, admin access reviews.

## Reading order

1. **New to Exponential operations:** [Hosting](hosting/README.md), then [Backups and recovery](backups-and-recovery/README.md)
   and [Monitoring](monitoring/README.md).
2. **Preparing for go-live:** [People and permissions](people-and-permissions/README.md) and
   [Editorial workflows](editorial-workflows/README.md), so access and approvals are right from day one.
3. **Running the service:** [Incidents](incidents/README.md) and [Upgrades and change](upgrades-and-change/README.md),
   with the [Operations tasks](../tasks/operations/README.md) at hand.
4. **Planning for growth or stricter continuity duties:** [Availability](availability/README.md).

Project managers who only need the overview can read the README of each folder: each has a one-line answer per page.

## What you need before you start

- **Shell access** to the installation directory (the folder with `index.php` and `console`) for the commands. Run
  them as the site's user; when you are `root`, the PHP scripts need `--allow-root-user`, as
  [Operating a site](../../../guides/operating-a-site.md) explains.
- **An administrator account** in the administration interface for the click paths. The Setup menu
  (`/setup/menu`) holds Cache management, Maintenance, Cronjobs, Log files, System information and Upgrade check; the
  Users menu holds Roles and policies and Check user accounts.
- **A test copy** of the site. Many procedures here (restore tests, upgrades, role changes) should be tried there
  first; [Environments: development, test and production](hosting/environments-dev-test-prod.md) shows how one
  installation carries settings per environment.

## How the facts were checked

Every command, setting and admin label on these pages was checked against the code and documentation of the
repository, and the admin labels against screens of the demonstration server. Where Exponential has no built-in
feature for something a public body may expect (for example automatic database failover, or a content review-date
field), the page says so plainly and lists the options. No page names a real government, agency or customer; examples
use placeholders such as `example.gov` and "the Department of Example".

The section landing page, [Governments: start here](../government-start-here.md), places this part among the other parts of the Governments
section.

## Related pages
- [Hosting companies start here](../../hosting/hosting-companies-start-here.md): the hosting provider's view of the same topics.
- [Operating a site](../../../guides/operating-a-site.md): caches, cronjobs, backups and repairs, day to day.
- [Security and audit](../../../guides/security-and-audit.md): hardening, roles and the audit trail.
- [Security hardening](../../security-hardening.md): the threats one by one.
- [Site administrators course](../../trainers/site-administrators/README.md): a training course on the same tasks.
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
