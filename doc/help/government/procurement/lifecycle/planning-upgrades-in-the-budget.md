---
title: Planning upgrades in the budget
audience: government, project-manager, admin
category: public-sector
order: 60
summary: How a public body budgets for Exponential upgrades over a contract term - routine 6.0.x updates, upgrades from older lines, platform upgrades - with the steps each involves, the tools that reduce the effort, and a budget template without invented figures.
keywords: upgrade budget, upgrade planning, maintenance budget, 6.0.x update, migration, upgrade check, staging, effort, multi-year budget, public sector
type: help
---
# Planning upgrades in the budget

This page is for the project manager or budget holder who must plan money and staff time for keeping an Exponential
site current. It explains the kinds of upgrade, what each involves, and gives a template. The steps come from
[Upgrading](../../../../guides/upgrading.md), [11. Upgrading](../../../../install/11-upgrading.md) and
[Update with Git](../../../update-with-git.md).

## Contents

1. [Three kinds of upgrade](#three-kinds-of-upgrade)
2. [What a routine 6.0.x update involves](#what-a-routine-60x-update-involves)
3. [What drives the effort](#what-drives-the-effort)
4. [A budget template](#a-budget-template)
5. [Pitfalls](#pitfalls)

## Three kinds of upgrade

| Kind | When | Effort driver |
|---|---|---|
| Routine update within 6.0.x | each new release, and always after a security fix | number of custom extensions and template overrides |
| Upgrade from an older line (4.x, 5.x, the Symfony platforms) to 6.0 | once, often as its own project | custom PHP code, the content model, data volume |
| Platform upgrade (PHP, database, operating system) | when a platform version ends its support | compatibility of your own code |

Security fixes arrive only as new releases ([Supported versions](supported-versions.md)), so a site that cannot do
routine updates cannot receive fixes. Budget the routine first.

## What a routine 6.0.x update involves

The procedure has seven steps, by command line or in the administration
([Update with Git](../../../update-with-git.md#the-seven-steps)):

1. back up and start a maintenance window ([Maintenance mode](../../../../guides/maintenance-mode.md));
2. see what is waiting;
3. update the kernel (Git, or **Git Manager** in the administration);
4. update the extensions (Composer, or **Setup > Updates and packages**);
5. check and update the database;
6. clear the caches and reload the code;
7. check the site and leave the window.

Before it, **Setup > Upgrade check** compares the installed files with the release manifest and lists files changed in
place, which would be overwritten ([The upgrade check](../../../../guides/upgrade-check.md)). For each period between
the old and new version, the upgrade guide asks you to read its checklist and to check your own extensions
([Upgrading, Part C](../../../../guides/upgrading.md#part-c-from-any-60x-to-today)).

## What drives the effort

| Factor | Lower effort | Higher effort |
|---|---|---|
| Custom code | site built with configuration, overrides in a design extension | changed kernel files, many custom PHP extensions |
| How far behind | one release | many releases, several checklists to work through |
| Test coverage | a staging copy and a written test plan | testing on the live site |
| Engine | one engine, documented | several, undocumented |
| Knowledge | supplier and in-house staff trained | knowledge left with a former supplier |

The cheapest upgrades are frequent small ones. A site that skips releases for years ends up with an upgrade project.

## A budget template

All figures are your own estimates; the project publishes none.

```text
Upgrade budget - <site>                                  term <years>

Per routine update
  Supplier or staff hours (steps 1-7 + regression test)   <h> hours   x <rate>
  Number of updates per year (your policy, e.g. "every release" or "quarterly")   <n>
  Staging environment (if not already in the hosting fee) <amount>/year

Security updates outside the routine                      <n per year, estimate> x <h> hours x <rate>

Platform upgrades over the term
  PHP version change                                      <h> hours x <rate>, in year <y>
  Database version change                                 <h> hours x <rate>, in year <y>
  OS change                                               <h> hours x <rate>, in year <y>

One-off migration (if coming from an older line)          <separate project budget>
Contingency                                               <percentage of the above>
```

Ask bidders to fill in their hours per routine update for your site, so offers can be compared. The total cost of
ownership part of this procurement section shows how this line fits the whole model.

## Pitfalls

- **No staging copy.** Every upgrade is then a test on the live site.
- **Changed kernel files.** The upgrade check finds them; the fix is to move the change into an extension or
  override ([Templates and design](../../../../guides/templates-and-design.md)).
- **Budgeting only the first year.** Upgrades are a yearly cost.
- **Treating a security fix as optional.** A fix arrives only with an upgrade.

## Related pages

- [The product lifecycle](README.md)
- [Security updates and patching cadence](security-updates-and-patching-cadence.md)
- [Upgrading](../../../../guides/upgrading.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
