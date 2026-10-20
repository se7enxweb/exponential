---
title: Reliability FAQ
audience: businesses, project-manager, sales-marketing, admin
category: business-solutions
order: 230
summary: Short, sourced answers to the questions owners and managers ask most about Exponential's stability, support, uptime, backups, upgrades, Velocity, databases and what to do when something goes wrong.
keywords: FAQ, questions, reliability, stability, uptime, support, security fixes, PHP, backups, upgrades, Velocity, database, downtime, SLA
type: help
---
# Reliability FAQ

This page is for owners, managers and decision makers who want quick answers. Each answer is short, says where the
fact comes from, and links to the page with the details. If your question is not here, start at
[Reliability for businesses](README.md) or the section-wide [Businesses FAQ](../businesses-faq.md).

## Contents

1. [Stability and support](#stability-and-support)
2. [Uptime and monitoring](#uptime-and-monitoring)
3. [Backups and recovery](#backups-and-recovery)
4. [Upgrades and changes](#upgrades-and-changes)
5. [Performance and growth](#performance-and-growth)
6. [When something goes wrong](#when-something-goes-wrong)

## Stability and support

**Is Exponential still maintained?**
Yes. "Exponential 6.0 is the maintained line"; security fixes are made on `main` and published with the next 6.0.x
release ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Exponential 6.0.0 was released on 1 January 2024 and 6.0.14 on
7 June 2026 ([Who is 7x?](../../who-is-7x.md#the-60x-releases)). See
[Release line and support policy](release-line-and-support-policy.md).

**How old is the code base?**
The repository's history goes back to 2 September 2002; the code started as eZ Publish, developed from 1999
([Who is 7x?](../../who-is-7x.md#the-ez-publish-years-1999-to-2021)). See
[Why stability matters](why-stability-matters.md).

**Can a released version change under us?**
No. "A published tag is never changed" ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). A fix comes as the next version.

**Which PHP versions are supported?**
PHP 8.0 is the oldest supported version, 8.1 to 8.5 are supported, and Velocity runs on 8.0 or later (from its release 0.0.4.54)
([README, "What version of PHP is required"](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required)).

**Is there an official end-of-life date or support contract?**
The project publishes neither a release calendar nor a service level agreement. Commercial support and hosting come
from 7x and hosting partners ([SLA options](../../hosting/business/sla-options.md),
[Working with 7x and the project](../choosing/working-with-7x-and-the-project.md)).

**How fast are security reports answered?**
SECURITY.md says: "You can expect a first answer within one business day" for a report sent privately as it
describes.

## Uptime and monitoring

**What uptime does Exponential guarantee?**
None, and no page of this help states a percentage. Availability depends on your hosting, network and operations as
much as on the software. The figures of an availability promise are "a business decision; the sources give none"
([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md#uptime-in-a-hosting-plan)). See
[Uptime practice](uptime-practice.md).

**Is there a health URL we can monitor?**
"There is no single health URL; combine a check from outside with the status commands inside"
([10.11](../../../install/10-after-installing.md#1011-monitoring-and-health-checks)). Check the front page and the
administration login page from outside, and run `exp:system:info`, the cache status and audit verify on the server.
On Velocity, `/Q/health` and `exp:velocity status --json` help too. See
[Health checks and monitoring](health-checks-and-monitoring.md).

**Does planned maintenance hurt our search ranking?**
Maintenance mode answers `503` with `Retry-After`, which search engines read as "come back later": "the pages keep
their place in the index" ([Maintenance mode guide](../../../guides/maintenance-mode.md#1-what-maintenance-mode-does)).
See [Maintenance mode and planned work](maintenance-mode-and-planned-work.md).

## Backups and recovery

**What must be backed up?**
The database and the files (`var/storage`, `settings/override`, `settings/siteaccess`, your own extensions and
designs), taken at the same time ([Backups and restores](../../creators/website-creators/backups-and-restores.md#what-to-back-up)).
See [Backups that restore](backups-that-restore.md).

**How do we know our backups work?**
Restore one on another machine, on a schedule, and time it. "A backup you have never restored is a hope"
([Operating a site](../../../guides/operating-a-site.md#6-back-up-and-restore)). See [Restore drills](restore-drills.md).

**An editor deleted a page. Do we need a restore?**
Usually not. Removed items go to the trash, and earlier versions can be made into a new draft
([Publishing, versions and the trash](../../publishing-and-versions.md)). See
[Preview before you publish](preview-before-you-publish.md#going-back).

## Upgrades and changes

**How often should we upgrade?**
Treat moving to the newest 6.0.x as a recurring task; upgrade in the next window when a security release exists
([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#deciding-when-to-upgrade)). See
[Upgrades without surprises](upgrades-without-surprises.md).

**How do we know an upgrade will not overwrite our changes?**
Run **Setup > Upgrade check** before you start: it lists release files that were modified by hand, and only reads
([The upgrade check](../../../guides/upgrade-check.md)). See
[Upgrade check and file consistency](upgrade-check-and-file-consistency.md).

**Can a deploy happen without dropping visitors?**
On Velocity, `graceful` "re-executes the server without releasing the listening socket, so a deploy does not drop
requests", and `exp:velocity deploy` runs every step in the right order and stops at the first failure
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it), [Deploying](../../../guides/deploying.md#7-ship-a-php-change-with-one-command)).
See [Graceful deploys](graceful-deploys.md).

**Can updates install themselves?**
`ezupdate` checks for updates and runs Composer from the administration, but "has no schedule of its own that updates
a site unattended", and updating is off until an administrator turns it on
([Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md#what-it-does)).

## Performance and growth

**Do we need Velocity?**
No. Exponential runs on Apache, Nginx and other web servers, and the README notes that "Exponential 6.0.15+ is fast
under load on Apache or Nginx too". Velocity raises the ceiling where your hosting allows a long-lived process. See
[Velocity for businesses](velocity-for-businesses.md).

**What do the Velocity figures mean for us?**
The README documents 0.36 ms per cached page and 8,941 requests per second sustained on a 12-core machine, and explains
how they were measured. They are not a promise for your site; measure your own pages with `exp:benchmark`. See
[Velocity for businesses](velocity-for-businesses.md#the-documented-figures-read-honestly).

**Which database is the most reliable choice?**
The project's official order is 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle ·
6. MySQL ([Database support](../../database-support.md#the-supported-databases-in-order)). SQLite suits one machine;
for many simultaneous writers or several web servers use a database server. See
[Database choice for reliability](database-choice-for-reliability.md).

**When do we need more than one server?**
When one machine with caches on cannot carry your measured peak, or when the cost of downtime justifies redundancy.
"A single machine with enough CPU and memory is the simpler choice for most sites"
([Several web nodes](../../hosting/operations/several-web-nodes.md)). See
[Clustering and high availability](clustering-and-high-availability.md).

**How do we prepare for a sale or a campaign?**
Plan weeks ahead, measure, warm the caches, freeze changes and watch on the day. See
[Peak traffic planning](peak-traffic-planning.md).

## When something goes wrong

**A customer saw "Something went wrong". What now?**
Ask for the `ERR-` reference on the page; an administrator finds the exact request with it in **Setup > Log files**
([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)). See
[Logs and error references](logs-and-error-references.md).

**Who changed this setting, and when?**
The audit trail records settings writes, deploys, cache clears, maintenance and content changes, and can prove the
record was not altered ([Audit trail](../../../features/6.0/audit-trail.md)). See
[Audit trail for operations](audit-trail-for-operations.md).

**The site is down. Where do we start?**
With your incident plan and the site-down runbook. See
[Incident response for small teams](incident-response-for-small-teams.md) and
[Runbook: site down](../../hosting/runbooks/site-down.md).

**We found a bug in Exponential itself.**
Report it in the public tracker; security problems go privately to the address in SECURITY.md
([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Reliability for businesses](README.md)
- [Reliability scorecard](reliability-scorecard.md)
- [Businesses FAQ](../businesses-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
