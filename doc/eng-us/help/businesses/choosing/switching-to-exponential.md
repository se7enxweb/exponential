---
title: Switching to Exponential
audience: businesses, project-manager, admin
category: business-solutions
order: 70
summary: What a business needs to know before moving a site to Exponential 6 - from an older release of the eZ Publish years, from eZ Platform or Ibexa, from another host, or from another system such as WordPress or Drupal - the paths the installation book documents, the effort drivers, a phased plan and the decisions only you can make.
keywords: switching, migration, migrate to Exponential, eZ Publish 4.x, 5.x, eZ Platform, Ibexa, WordPress, Drupal, Joomla, Magento, Shopify, move host, staging, rollback, cut-over, redirects, plan
type: help
---
# Switching to Exponential

This page is for owners and managers who consider moving an existing site to Exponential 6, and for the project
manager who will plan it. It explains the documented paths, what drives the effort, and how to plan a switch that
does not put the business at risk. The technical steps are in the installation book; this page tells you which
chapter fits and what to decide.

## Contents

1. [Which path is yours](#which-path-is-yours)
2. [Why switch now](#why-switch-now)
3. [What drives the effort](#what-drives-the-effort)
4. [A phased plan](#a-phased-plan)
5. [Decisions only you can make](#decisions-only-you-can-make)
6. [Risks and how to reduce them](#risks-and-how-to-reduce-them)

## Which path is yours

| Your site runs today | Path | Read |
|---|---|---|
| Exponential 6.0.x, and only the host changes | a move, not a migration | [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md) |
| An older 6.0.x release | an update within the line | [Upgrading, part C](../../../guides/upgrading.md#part-c-from-any-60x-to-today) |
| 4.x (from the eZ Publish years) | a migration to 6 | [Migrating from 4.x](../../../install/14-migrating-from-4x.md) |
| 5.x with the legacy kernel | a migration to 6 | [Migrating from 5.x legacy](../../../install/15-migrating-from-5x-legacy.md) |
| eZ Platform or Ibexa DXP/OSS | two destinations: Exponential Platform Nexus, or the Exponential 6.0 legacy kernel | [Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md) |
| WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, site builders, static HTML | a content migration with export and import | [Migrating to and from Exponential](../../../install/migration/README.md) |

The hosting view of the same paths is in [Migrations for hosts](../../hosting/migrations/README.md).

For the content migration from another system, the extension 7x SQLI Import reads the export files of WordPress,
Drupal, Joomla, Shopify and WooCommerce, spreadsheets and APIs, maps them to your content types without code and
shows a dry run before anything is published ([7x SQLI Import: start here](../../sqli-import/README.md)).

## Why switch now

- **Old versions get no security fixes.** "5.x, 4.x, 3.x and older" are not supported; 6.0 is the maintained line
  (`SECURITY.md`). A site from the eZ Publish years is running without fixes.
- **Current PHP.** Exponential supports PHP 8.0 to 8.5 (PHP 7.4 usable without support) (`README.md`), so the site can move to a supported PHP as well.
- **A shared database heritage.** For 4.x and 5.x legacy sites, the content model and much of the database carry
  over; the chapters explain what changes and what does not
  ([14.1 What changes, and what does not](../../../install/14-migrating-from-4x.md#141-what-changes-and-what-does-not)).
- **No license fee** after the switch (`README.md`, "License"), and no lock-in ([What you do not pay for](../costs/what-you-do-not-pay-for.md)).

## What drives the effort

From the installation book (for example [14.15 How long it takes](../../../install/14-migrating-from-4x.md#1415-how-long-it-takes)):

| Driver | Why it matters |
|---|---|
| Size of the content tables | dump, restore, conversions and reindexing grow with rows |
| Number and size of stored files | copying `var/` takes time |
| Your own extensions and PHP code | porting them to PHP 8 grows with lines of your own code, not with database size |
| Templates and design | old designs may need rework; Twig templates of eZ Platform become TPL on the legacy kernel |
| Search | reindexing is often the longest single step |
| Languages and URLs | URL aliases per node and language must be rebuilt and checked |
| Integrations | payment, single sign-in, feeds and APIs must be retested |

From another system, the effort is in mapping its content to Exponential's content model and keeping URLs working
with redirects ([Migrating to and from Exponential](../../../install/migration/README.md)).

> **Note**
> The project gives no hour or price figures for a migration, and neither does this page. Measure the duration in a
> rehearsal and plan from the measurement, as the installation book advises.

## A phased plan

1. **Inventory.** List content types, languages, extensions, integrations and traffic. The book's chapters start with
   this step.
2. **Choose the path and a partner** if needed ([Choosing a partner](choosing-a-partner.md)).
3. **Rehearse on a staging copy.** Run the whole migration on a copy and measure every step.
4. **Verify.** Check content, URLs, forms, the store and sign-ins on the copy; the chapters have verification
   sections and checklists.
5. **Plan the cut-over.** A content freeze, a maintenance window with maintenance mode, and the final run.
6. **Keep a rollback plan.** The old site and its backups stay ready until the new one is accepted
   ([14.14 Rollback plan](../../../install/14-migrating-from-4x.md#1414-rollback-plan)).
7. **Go live** with the [Go-live checklist](../templates/go-live-checklist.md).
8. **Aftercare.** Watch logs, 404 errors and search for the first weeks.

## Decisions only you can make

- [ ] Which content to move, and which to archive or drop.
- [ ] Whether the design is kept or renewed at the same time (doing both raises risk).
- [ ] When the freeze and the maintenance window happen, considering your busy periods.
- [ ] Who accepts the result, and by which criteria ([Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)).
- [ ] How you tell customers and staff.
- [ ] For eZ Platform and Ibexa sites: which of the two destinations fits.

## Risks and how to reduce them

| Risk | Reduce it by |
|---|---|
| Lost content or broken links | rehearsal, verification lists, redirects for changed URLs |
| Longer downtime than planned | measured rehearsal, a window with margin |
| Custom code that will not run on PHP 8 | porting it before the cut-over ([14.9](../../../install/14-migrating-from-4x.md#149-porting-your-own-extensions-from-php-5-to-php-8)) |
| Passwords that no longer work | the chapters' sections on users and password hashes |
| Search incomplete after the switch | reindexing planned, or finished after the switch if acceptable |
| No way back | a tested rollback plan and kept backups |

## Related pages

- [Migration reference](../../../install/17-migration-reference.md)
- [From older Exponential versions (for hosts)](../../hosting/migrations/from-older-exponential-versions.md)
- [Budgeting for upgrades and maintenance](../costs/budgeting-for-upgrades-and-maintenance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
