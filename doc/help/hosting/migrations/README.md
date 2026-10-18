---
title: Migrating customers: overview
audience: hosting
category: hosting
order: 10
summary: The migration pages for hosting companies: bringing a customer's Exponential site to your hosting, upgrading sites from older versions and other platforms, moving between hosts and changing the database engine, with links to the installation book's migration chapters.
keywords: migration, migrating customers, move site, transfer, onboarding, older versions, 4.x, 5.x, eZ Platform, Ibexa, database engine, hosting change
type: help
---
# Migrating customers: overview

This section is for hosting companies that move customer sites onto their platform. It answers: how do you bring a
running Exponential site in, what changes when the site is on an older version or another platform, and how do you
change the database engine on the way?

The deep technical work is described in the installation book, chapters 14 to 17. These pages arrange it for a host:
the assessment, the plan, the move, the checks, and the responsibilities between you and the customer.

## Pages in this section

| Page | What it gives you |
|---|---|
| [Migrating customers to your hosting](migrating-customers-to-your-hosting.md) | the whole process for a host: assessment, offer, staging, cut-over, aftercare |
| [From older Exponential versions](from-older-exponential-versions.md) | sites of the 3.x, 4.x and 5.x eras and older 6.0.x releases |
| [From eZ Platform and Ibexa](from-ez-platform-and-ibexa.md) | Symfony-based sites and their two destinations |
| [Moving a site between hosts](moving-a-site-between-hosts.md) | an Exponential 6 site that only changes servers |
| [Changing the database engine](changing-the-database-engine.md) | moving a site to another of the six databases |

## Which page do I need?

| The customer's site runs ... | Read |
|---|---|
| Exponential 6.0.x, another host | [Moving a site between hosts](moving-a-site-between-hosts.md) |
| eZ Publish 3.x, 4.x or 5.x legacy (the old name of the product, discontinued by its creator after almost 18 years) | [From older Exponential versions](from-older-exponential-versions.md) |
| eZ Platform 1.x to 3.x, Ibexa DXP or OSS 3.3 to 5.0 | [From eZ Platform and Ibexa](from-ez-platform-and-ibexa.md) |
| any of these, and the database should change | also [Changing the database engine](changing-the-database-engine.md) |

> **Note**
> Only the 6.0 line is maintained; 5.x, 4.x, 3.x and older get no security fixes (`SECURITY.md`). A customer on an
> old version is a migration project, not a hosting move. Price and plan it as one.

## The installation book's migration chapters

- [Chapter 14: Migrating from the 4.x line](../../../install/14-migrating-from-4x.md)
- [Chapter 15: Migrating from the 5.x legacy stack](../../../install/15-migrating-from-5x-legacy.md)
- [Chapter 16: Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md)
- [Chapter 17: Migration reference](../../../install/17-migration-reference.md)
- [Chapter 11: Upgrading](../../../install/11-upgrading.md)

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Restore and restore tests](../operations/restore-and-restore-tests.md)
- [Go-live checklist for a customer site](../checklists/new-customer-site-go-live.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
