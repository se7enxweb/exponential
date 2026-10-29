---
title: Keeping customers current
audience: reseller, partner, hosting
category: reselling
order: 390
summary: How a reseller keeps every customer site on the maintained Exponential release and a supported PHP version - what the project supports, a site inventory, the four parts of an update, the tools (ezupdate, the upgrade check, maintenance mode), an update rhythm, pricing upgrades, and talking with customers about them.
keywords: upgrades, updates, keeping current, release line, 6.0.x, SECURITY.md, PHP versions, inventory, ezupdate, git_manager, upgrade check, maintenance mode, rhythm, pricing, old sites
type: help
---
# Keeping customers current

This page is for technical leads at resellers who maintain many customer sites. A reseller that keeps sites current
is selling security; one that does not is selling risk. The partners' version is
[Keeping customer sites current](../../partners/keeping-customers-current.md).

## Contents

1. [What the project supports](#what-the-project-supports)
2. [Keep an inventory](#keep-an-inventory)
3. [The four parts of an update](#the-four-parts-of-an-update)
4. [Tools](#tools)
5. [An update rhythm](#an-update-rhythm)
6. [Pricing upgrades](#pricing-upgrades)
7. [Talking with customers](#talking-with-customers)

## What the project supports

| Version | Supported (from `SECURITY.md`) |
|---|---|
| 6.0.x, the current line (`main`, and the next tag cut from it) | Yes |
| 6.0.x, released tags (`v6.0.0` to `v6.0.14`) | Fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | No |

"A published tag is never changed": a fix reaches a site only through an update. PHP 7.4 to 8.5 are supported for the
6.0.15 line; PHP 7.4 again from 6.0.15 ([Release line and support](../../customers/healthy/release-line-and-support.md)).
The project publishes no release calendar and no end-of-life date for 6.0.

## Keep an inventory

```text
Customer site inventory
Customer | Site | Exponential version | PHP | Database | Engine | Extensions (versions) | Last update | Next planned
---------|------|---------------------|-----|----------|--------|-----------------------|-------------|-------------
```

The version shows on **Setup > System information** and on **Info > About**, which also lists every extension with
its version ([System information](../../../guides/system-information.md)). Review the inventory monthly: any site
below the newest 6.0.x release or on a PHP version near its end gets a plan.

## The four parts of an update

| Part | What changes |
|---|---|
| The code of the kernel | The files of Exponential, from a release tag or a branch |
| The extensions | Updated with Composer to match the new kernel |
| The database | A release may bring an SQL file that is applied once, after a backup |
| Caches and the running server | Caches cleared, PHP-FPM reloaded or Exponential Velocity restarted |

Source: [How updates work](../../customers/healthy/how-updates-work.md); the full procedure is
[Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md).

## Tools

| Tool | Use |
|---|---|
| **Setup > Upgrade check** | Compares the files with the release and the database with its schema; read-only ([The upgrade check](../../../guides/upgrade-check.md)) |
| `ezupdate` extension | **Setup > Updates and packages**, with a check that changes nothing, a dry run and a backup confirmation ([ezupdate](../../../features/6.0/extensions/ezupdate.md)) |
| `git_manager` extension | Git operations from the administration ([git_manager](../../../features/6.0/extensions/git_manager.md)) |
| Maintenance mode | **Setup > Maintenance**, or `php bin/php/maintenance.php` ([Maintenance mode](../../../guides/maintenance-mode.md)) |
| Automated checks | Check many sites "without updating anything unattended" ([Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md)) |

## An update rhythm

| Release carries | Apply within | Steps |
|---|---|---|
| A security fix | Your promised number of days ([Service level design](../offers/service-level-design.md)) | Staging test, backup, update, verify, report |
| Fixes and features | The next monthly window | Same |
| A PHP version change | Planned a year ahead | Test on staging with the new PHP, then switch |

After each update, record three results: the new version on **Setup > System information**, a clean upgrade check,
and a test item published ([How updates work](../../customers/healthy/how-updates-work.md#what-tells-you-an-update-worked)).

## Pricing upgrades

- Routine 6.0.x updates belong in the care plan.
- PHP version changes: in the plan for sites you built, priced for sites with code you did not write.
- Upgrades from 5.x, 4.x or older are projects; the migration chapters of the installation book describe the paths
  ([Upgrading](../../../guides/upgrading.md)).
- An extension that is no longer maintained can hold back an upgrade; price its replacement separately.

## Talking with customers

- Explain updates as protection, not as a sale.
- Summarize each release in the customer's words: what changes for editors, for visitors, and anything they must do
  ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).
- Plan large steps a year ahead ([Yearly checklist](../checklists/yearly-checklist.md)).
- Sites from the era before the product was renamed are natural customers for an upgrade project.

## Related pages

- [Keeping customer sites current](../../partners/keeping-customers-current.md)
- [Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md)
- [Upgrades with your provider](../../customers/healthy/upgrades-with-your-provider.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
