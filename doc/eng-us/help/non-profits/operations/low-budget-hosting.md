---
title: Low-budget hosting
audience: non-profit, businesses, hosting, admin
category: business-solutions
order: 20
summary: Choosing hosting for a non-profit's Exponential site on a small budget - what any host must provide, shared hosting, a small virtual server and managed hosting compared by money, time and skills, trying everything on a laptop first with one install command, questions to ask a host, and where saving money becomes expensive - with no prices invented.
keywords: hosting, low budget, cheap hosting, shared hosting, VPS, managed hosting, requirements, PHP, SQLite, Velocity, exp:install, questions for a host, false economies, non-profit
type: help
---
# Low-budget hosting

This page is for the director, treasurer and technical volunteer who choose hosting. It names no prices: compare
current offers yourself and put the result in the [Grant budget worksheet](../templates/grant-budget-worksheet.md).

## Contents

1. [What any host must provide](#what-any-host-must-provide)
2. [Three hosting models](#three-hosting-models)
3. [Try it first, for free](#try-it-first-for-free)
4. [Questions to ask a host](#questions-to-ask-a-host)
5. [False economies](#false-economies)
6. [Checklist](#checklist)

## What any host must provide

From `README.md`, "Requirements", and [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md):

| Need | Note |
|---|---|
| PHP | A current version with the required extensions ([PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md)) |
| A web server | Apache, Nginx, LiteSpeed and others work, or the bundled Velocity, "which needs nothing installed beyond PHP" |
| A database | Optional: SQLite needs no database server; supported in order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Writable directories | For storage and caches ([Writable directories and permissions](../../hosting/basics/writable-directories-and-permissions.md)) |
| Cron | For newsletters, scheduled publishing, cleanups, the audit trail ([Cronjobs](../../../guides/cronjobs.md)) |
| HTTPS | A certificate; often free from the host |
| Mail | For forms and newsletters |

## Three hosting models

| Model | Money | Your time and skills | Fits | Read |
|---|---|---|---|---|
| Shared hosting | Lowest | Low; limited control | Very small sites; Apache or LiteSpeed with `.htaccess`, SQLite or the panel's database; Velocity usually not | [Shared hosting](../../hosting/models/shared-hosting.md) |
| A small virtual server | Low | Someone must patch and secure the server | Most small non-profits with a technical volunteer or partner | [VPS hosting](../../hosting/models/vps-hosting.md) |
| Managed hosting | Higher | Low: the host patches and backs up | Organizations without technical volunteers | [Managed hosting](../../hosting/models/managed-hosting.md) |

Comparison in detail: [Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md) and
[Hosting cost drivers](../../businesses/costs/hosting-cost-drivers.md).

> **Tip**
> The cheapest model is the one your organization can actually look after. A virtual server nobody patches is more
> expensive in the end than managed hosting.

## Try it first, for free

`./console exp:install` "installs the multisite package on **SQLite**, in `eng-US`", with a site and an administration
([Installing Exponential in one command](../../../features/6.0/install-in-one-command.md)). A volunteer can try the
whole product on a laptop before anything is bought, and show it to the board.

## Questions to ask a host

- [ ] Which PHP versions do you offer, and how are they updated?
- [ ] Can I run cron jobs every few minutes?
- [ ] Is HTTPS included, and renewed automatically?
- [ ] How are backups made, how long kept, and how do I restore one? Can I test a restore?
- [ ] Where are the servers (country), and who processes data for us? Will you sign a data processing agreement?
- [ ] Can the site send mail, and with which limits?
- [ ] What happens if we stop paying or you close: how do we get our data?

More: [What to ask your host](../../customers/healthy/what-to-ask-your-host.md) and
[Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md).

## False economies

| Saving | Why it costs more later |
|---|---|
| No backups, or untested ones | A lost site is rebuilt from nothing |
| Skipping updates | Security incidents and harder upgrades later |
| One person holds every password | That person leaves ([When volunteers leave](when-volunteers-leave.md)) |
| The domain registered to a volunteer personally | You can lose your address |
| No cron | Newsletters, scheduled pages and cleanups silently stop |

## Checklist

- [ ] Requirements confirmed with the host.
- [ ] Model chosen for the people you have, not only the price.
- [ ] Domain and hosting contract in the organization's name, two people with access.
- [ ] Backups and restore tested before launch.
- [ ] Cron, HTTPS and mail working.
- [ ] Data processing agreement signed where your law requires it.

## Related pages

- [Operations for non-profits](README.md)
- [SQLite and Velocity for small teams](sqlite-and-velocity-for-small-teams.md)
- [Hosting for a website](../../creators/website-creators/hosting-for-a-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
