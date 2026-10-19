---
title: Maintenance agreement template
audience: customer, partner, hosting
category: customer-success
order: 8
summary: A template for the agreement that keeps an Exponential site current - release updates, security fixes, PHP upgrades, extension upkeep, checks after each update, reports and the yearly review - with fields to fill in. Not legal advice.
keywords: maintenance agreement, template, updates, upgrades, security releases, PHP upgrade, extensions, upgrade check, report, not legal advice
type: help
---
# Maintenance agreement template

**Template. Not legal advice.** Fixes reach an Exponential site only as the next release (`SECURITY.md`), so the
upkeep of the site is a task that never ends. This template puts that task in writing. It follows the upgrade
practice described in [Upgrades with your provider](../healthy/upgrades-with-your-provider.md),
[Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md) and
[Keeping customers current](../../partners/keeping-customers-current.md). Values in square brackets are fields to
agree. Have your legal adviser review the result.

## Parties and scope

| Field | Value |
|---|---|
| Customer | [organization name] |
| Provider | [host, partner or developer] |
| Sites and environments | [addresses; live, staging] |
| Starting version | [Exponential version], PHP [version], database [engine and version], engine [Velocity / Apache or nginx with PHP-FPM / other] |
| Valid from | [date] |

## Updates

| Task | Agreed |
|---|---|
| Follow the maintained line | Upgrade to the newest 6.0.x release within [agreed time] of its release |
| Releases with security fixes | Applied within [agreed time] of release |
| Extensions | Updated with each upgrade; extensions without a maintained release reported to the customer |
| PHP version | Kept on a PHP branch with security support; moves planned [time] ahead ([PHP upgrade](../../hosting/runbooks/php-upgrade.md)) |
| Operating system and database server | [who, how often] |
| Tried on a copy first | [always / for releases that change the database / never] |
| Backup right before each update | Database and files ([Backups](../healthy/backups.md)) |
| Windows | [day and time, time zone], announced [days ahead] |

## After each update the customer receives

| Evidence | Source |
|---|---|
| Old and new version | **Setup > System information** |
| Upgrade check result, with files changed on purpose explained | **Setup > Upgrade check** ([The upgrade check](../../../guides/upgrade-check.md)) |
| Summary of what changes for editors and visitors | The changelog and behavior change notes, in plain words |
| Checks done | Front page, sign-in, publish, forms, shop, each site and language |
| Open items | Settings to review, extensions waiting for a release |

## Regular checks

| Check | Frequency | Reported |
|---|---|---|
| System information health checks | [interval] | [yes / no] |
| Error digest and logs | [interval] | Summary [interval] |
| Audit trail verification | [interval] | [yes / no] |
| Accounts and administrator role review | [interval] | Changes listed |
| Restore test | [interval] | The test record ([Restore test](../healthy/restore-test.md)) |

## Custom code

| Field | Agreed |
|---|---|
| Where the site's code is kept | [repository, owned by the customer or copyable on request] |
| Changes to Exponential's own files | [none / kept in version control with a reason] |
| General fixes contributed upstream | [yes / no] ([Contributing code back](../../partners/contributing-code-back.md)) |
| Work to adapt custom code at an upgrade | [included up to [hours] / quoted separately] |

## Reporting and review

| Field | Value |
|---|---|
| Maintenance report | [monthly / quarterly]: versions, updates applied, checks, open items |
| Yearly review | [date], following [Reviewing your provider each year](reviewing-your-provider-each-year.md) |
| Inventory | The provider keeps a record of versions, engine, database and extensions for the site |

## Term and exit

| Field | Value |
|---|---|
| Term and notice | [term], [notice period] |
| At the end | Handover as in the [Handover checklist](handover-checklist.md) |

## Signatures

| | Customer | Provider |
|---|---|---|
| Name and role | [name, role] | [name, role] |
| Date | [date] | [date] |

## Related pages

- [How updates work](../healthy/how-updates-work.md)
- [SLA template](sla-template.md)
- [Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
