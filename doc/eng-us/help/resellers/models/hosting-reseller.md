---
title: Hosting reseller
audience: reseller, hosting, partner
category: reselling
order: 70
summary: How a hosting company or a reseller of hosting builds and sells Exponential hosting plans - what the plan must include technically, which engine and database fit which plan, what to promise and what to leave to the customer, licensing for hosts, and where the hosting section of the help takes over.
keywords: hosting reseller, hosting plan, shared hosting, VPS, managed hosting, Velocity, PHP-FPM, SQLite, PostgreSQL, isolation, backups, plan design, reseller hosting, white-label hosting
type: help
---
# Hosting reseller

This page is for hosting companies, and for agencies that resell hosting capacity, who want to sell plans for
Exponential sites. The hosting section of the help covers the technical side in depth
([Hosting companies: start here](../../hosting/hosting-companies-start-here.md)); this page is the reseller's view:
what you sell, what it costs you and what you may promise.

## Contents

1. [Three ways to resell hosting](#three-ways-to-resell-hosting)
2. [What a plan must include](#what-a-plan-must-include)
3. [Technical choices that shape the price](#technical-choices-that-shape-the-price)
4. [What you may and may not promise](#what-you-may-and-may-not-promise)
5. [Licensing for a hosting reseller](#licensing-for-a-hosting-reseller)
6. [Checklist](#checklist)

## Three ways to resell hosting

| Way | You own | Your risk | Notes |
|---|---|---|---|
| Your own infrastructure | Servers or cloud accounts | Highest: you operate everything | Full control of price and quality |
| Reselling a hosting provider's capacity under your brand | The customer relationship | Medium: you depend on the provider's quality | Check that the provider's servers meet the requirements below |
| White-label 7x hosting | The customer relationship | Medium | "White-Label 7x Services" from Professional; terms not published, ask the partner team ([White-label reseller](white-label-reseller.md)) |

## What a plan must include

The hosting section names the building blocks of every plan ([Designing hosting plans](../../hosting/business/designing-hosting-plans.md)):

| Block | Decide |
|---|---|
| Engine | Exponential Velocity, or Apache or nginx with PHP-FPM |
| PHP | Which versions in the supported range 8.0 to 8.5, and who chooses |
| Database | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle or MySQL (the official order) |
| Storage | A quota for `var/storage`, caches and logs |
| Isolation | A system user and PHP-FPM pool per site, a VPS, a container or a dedicated machine |
| Backups | Database plus `var/storage`, `settings/override` and `settings/siteaccess`, how often and how long |
| Updates | Customer, host on request, or host on a schedule |
| Support | What you answer, how fast ([Support tiers you offer](../offers/support-tiers-you-offer.md)) |

The minimum server is on [Hosting requirements for your offers](../offers/hosting-requirements-for-offers.md).

## Technical choices that shape the price

| Choice | Cheaper | Dearer |
|---|---|---|
| Database | SQLite 3, the default: "a full production database for a site served from one machine", no database server to run | A database server (PostgreSQL first) for plans with several web nodes |
| Engine | Apache or nginx with one PHP-FPM pool per customer, on shared machines | Velocity with its own worker pool per site, on VPS, dedicated or managed plans |
| Isolation | Pools on a shared machine | A VPS or container per customer ([Multi-tenant isolation](../../hosting/security/multi-tenant-isolation.md)) |
| Environments | Production only | Production plus staging |

Sources: [Designing hosting plans](../../hosting/business/designing-hosting-plans.md),
[Database support](../../database-support.md). Measure before you set limits
([Benchmarking a hosted site](../../hosting/performance/benchmarking-a-hosted-site.md)).

## What you may and may not promise

| You may promise | Only if you deliver it yourself |
|---|---|
| Uptime of your servers | Uptime figures need your own monitoring ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)) |
| Backups and restore tests | A restore that was actually tested ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)) |
| Applying Exponential updates | A process that applies them ([Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md)) |
| Security fixes within a set time | Your own staff and schedule; the project publishes no SLA, only "a first answer within one business day" to private security reports (`SECURITY.md`) |

Never promise that the project or 7x will fix a customer's problem within a time: your partner plan's response time
is what 7x promises you ([Service level design](../offers/service-level-design.md)).

## Licensing for a hosting reseller

Running Exponential for customers is not restricted, needs no fee and no permission. Images, templates and one-click
installers that hand copies to customers bring the distribution duties
([Licensing for hosts](../../hosting/business/licensing-for-hosts.md),
[Distribution obligations checklist](../responsibilities/distribution-obligations-checklist.md)).

## Checklist

- [ ] Plan sheet written for each plan, with every block above decided.
- [ ] Servers checked against the requirements ([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)).
- [ ] Backup and restore tested on a real customer-sized site.
- [ ] Support tiers and severity levels written ([Support tiers for Exponential hosting](../../hosting/business/support-tiers.md)).
- [ ] Offboarding procedure written, so customers can leave cleanly ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).
- [ ] Marketing text reviewed for correct names ([Naming and brand for resellers](../sales/naming-and-brand.md)).

## Related pages

- [Hosting companies: start here](../../hosting/hosting-companies-start-here.md)
- [Hosting partners](../../partners/hosting-partners.md)
- [Marketing Exponential hosting](../../hosting/business/marketing-exponential-hosting.md)
- [Managed service reseller](managed-service-reseller.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
