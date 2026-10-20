---
title: Upgrade readiness
audience: government, admin, hosting, project-manager
category: public-sector
order: 160
summary: A checklist for a public body before, during and after an upgrade of its Exponential site - changelog, upgrade check, staging, backup, maintenance window, the seven update steps, verification and communication.
keywords: upgrade, update, readiness, changelog, upgrade check, staging, backup, maintenance window, Git Manager, Updates and packages, verification, public sector
type: help
---
# Upgrade readiness

This checklist is for the site administrator and project manager of a public body planning an upgrade within the
6.0.x line. The steps come from [Update with Git](../../update-with-git.md), [Upgrading](../../../guides/upgrading.md)
and [The upgrade check](../../../guides/upgrade-check.md).

## Contents

1. [Before](#before)
2. [During](#during)
3. [After](#after)
4. [Upgrades from older lines](#upgrades-from-older-lines)

## Before

- [ ] Current version noted from **Setup > System information** ([System information](../../../guides/system-information.md)).
- [ ] Changelog of every version you pass read, and the upgrade notes it links ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md), [Upgrading, Part C](../../../guides/upgrading.md#part-c-from-any-60x-to-today)).
- [ ] **Setup > Upgrade check** run: files changed in place found and moved into extensions or overrides ([The upgrade check](../../../guides/upgrade-check.md)).
- [ ] Your own extensions checked against the changes ([Upgrading, C4](../../../guides/upgrading.md#c4-check-your-own-extensions)).
- [ ] The upgrade rehearsed on a staging copy with a written test list.
- [ ] A maintenance window agreed and announced.
- [ ] A fresh backup of database and files ([Backup readiness](backup-readiness.md)).

## During

- [ ] Maintenance mode on, administrators allowed ([Maintenance mode](../../../guides/maintenance-mode.md)).
- [ ] The seven steps followed, by command line or administration ([Update with Git](../../update-with-git.md#the-seven-steps)):
  - [ ] update the kernel (Git, or **Git Manager**);
  - [ ] update the extensions (Composer, or **Setup > Updates and packages**);
  - [ ] check and update the database;
  - [ ] clear the caches and reload the code (for example `php bin/php/ezcache.php --clear-all --allow-root-user`, then reload the engine) ([Caches](../../../guides/caches.md));
- [ ] Checked as administrator before visitors return.
- [ ] Maintenance mode off.

## After

- [ ] **Setup > System information** shows the new version and no open health checks.
- [ ] Key journeys tested: home page, search, a form, sign-in, publishing an article, a download.
- [ ] **Setup > Log files** checked for new errors ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] Accessibility spot check on the main templates.
- [ ] Editors told what changed in the administration; training material updated.
- [ ] The upgrade recorded: from, to, date, who, issues.

## Upgrades from older lines

A site on 5.x, 4.x or the Symfony platforms is not supported and needs a migration project, not a routine update
([Supported versions](../procurement/lifecycle/supported-versions.md)):

- [ ] Read [14. Migrating from the 4.x line](../../../install/14-migrating-from-4x.md), [15. Migrating from the 5.x legacy stack](../../../install/15-migrating-from-5x-legacy.md) or [16. Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md).
- [ ] Use the [Migration checklist](../../../install/migration/checklist.md).
- [ ] Budget it as a project ([Planning upgrades in the budget](../procurement/lifecycle/planning-upgrades-in-the-budget.md)).

## Related pages

- [Government checklists](README.md)
- [Upgrading customer sites (for hosts)](../../hosting/operations/upgrading-customer-sites.md)
- [Updates and upgrades (for site builders)](../../creators/website-creators/updates-and-upgrades.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
