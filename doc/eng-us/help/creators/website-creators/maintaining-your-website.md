---
title: Maintaining your website
audience: website-creator, admin
category: website-creation
order: 690
summary: Keep a live site healthy - daily, weekly, monthly and yearly tasks, the administration pages that show what needs attention, logs, cronjobs, updates, backups and content hygiene.
keywords: maintenance, operations, site health, cronjobs, logs, updates, backups, content checks, link check, monitoring, website creator
type: help
---
# Maintaining your website

This page is for website creators who look after a site after launch. Most work is a routine of checks; the
administration shows what needs attention. The full operations handbook is
[After installing](../../../install/10-after-installing.md) and [Operating a site](../../../guides/operating-a-site.md).

## Contents

1. [The routine](#the-routine)
2. [Where to look](#where-to-look)
3. [Content hygiene](#content-hygiene)
4. [When something breaks](#when-something-breaks)

## The routine

| How often | Task | Read |
|---|---|---|
| Daily | glance at the error log and failed cronjobs | [Log viewer](../../../features/6.0/log-viewer.md), [Cronjobs](../../../guides/cronjobs.md) |
| Weekly | backups ran; form submissions processed; broken links | [Backups and restores](backups-and-restores.md), [Broken links and content checks](link-and-content-checks.md) |
| Monthly | updates of Exponential and extensions; unused media; user accounts | [Updates and upgrades](updates-and-upgrades.md), [User hygiene](../../../features/6.0/user-hygiene.md) |
| Quarterly | restore test; accessibility re-check; roles review | [Testing accessibility](testing-accessibility.md), [Roles and policies](../../../guides/roles-and-policies.md) |
| Yearly | certificates and domains; PHP version; content review with owners | [Going live](going-live-hosts-https.md), [Requirements](../../../install/02-requirements.md) |

## Where to look

| Page | Shows |
|---|---|
| **Setup > Cronjobs** | which parts ran, failed or need attention |
| **Setup > Logs** | error and warning logs with a digest |
| **Setup > System information** | PHP, database, extensions, versions |
| **Setup > Upgrade check** | file consistency and database checks ([Upgrade check](../../../guides/upgrade-check.md)) |
| **Setup > Caches** | cache sizes and clearing |
| Content checks | unreferenced media, broken relations, storage problems |
| **Store** dashboard | shop issues, if you run one |

## Content hygiene

- Remove or archive outdated pages; redirect their addresses ([Redirects and moved content](redirects-and-moved-content.md)).
- Empty the trash after its review period ([Trash](../../../features/6.0/trash-who-and-where.md)).
- Clean old versions with `exp:cleanupversions` (read `--help` first).
- Remove unused media found by the content checks.

## When something breaks

1. Look at the error log first; errors carry a reference you can search
   ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
2. Clear the smallest cache that explains the problem.
3. Use maintenance mode for repairs that change data ([Maintenance mode during changes](maintenance-mode.md)).
4. Report product bugs ([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Backups and restores](backups-and-restores.md)
- [Updates and upgrades for site owners](updates-and-upgrades.md)
- [System information](../../../guides/system-information.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
