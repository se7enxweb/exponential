---
title: Compare hosting offers
audience: government, project-manager, hosting
category: public-sector
order: 120
summary: Step by step, compare hosting offers for an Exponential site of a public body - requirements met, engine, database, backups and restore, security, data location, service levels, support line and exit - on one comparison sheet.
keywords: hosting offers, compare hosting, hosting tender, data location, backups, restore, SLA, engine, database, security, exit, procurement task
type: help
---
# Compare hosting offers

**Who:** the IT specialist and the project manager. **When:** when choosing a host. The hosting section of this help
describes what hosts can offer ([Hosting companies: start here](../../../hosting/hosting-companies-start-here.md),
[Designing hosting plans](../../../hosting/business/designing-hosting-plans.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [The comparison sheet](#the-comparison-sheet)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- Your requirements for hosting and data location ([Hosting and data location requirements](../../procurement/requirements/hosting-and-data-location-requirements.md)).
- The technical requirements of Exponential ([Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md)).

## Steps

1. **Check the basics**: supported PHP version, the database you chose, image tools, Composer, shell access for
   cronjobs ([Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md)).
2. **Check the engine**: Velocity, Apache or nginx with PHP-FPM, or FrankenPHP; Velocity needs PHP 8.1 or later
   ([Choosing a web server](../../../hosting/servers/choosing-a-web-server.md)).
3. **Check backups**: database and files together, keys included, off-site, retention, and a tested restore time
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).
4. **Check security**: TLS, restricting the administration, a request filter, per-customer isolation
   ([Hardening for hosts](../../../hosting/security/hardening-for-hosts.md)).
5. **Check data location** of production, backups and support access.
6. **Check service levels** the host offers and how it measures them ([SLA options](../../../hosting/business/sla-options.md)).
7. **Check the support line**: where hosting support ends and application support begins ([Support tiers](../../../hosting/business/support-tiers.md)).
8. **Check exit**: how you get your data and backups when you leave ([Customer offboarding (for hosts)](../../../hosting/checklists/customer-offboarding.md)).
9. **Compare prices over the term** with your own figures ([Estimate hosting costs with your own figures](estimate-hosting-costs-with-your-own-figures.md)).

## The comparison sheet

```text
                                  Offer A        Offer B        Offer C
PHP / database / engine
Backups: frequency, off-site, retention, last restore test, RPO / RTO
Security: TLS, admin restriction, request filter, isolation
Data location: production / backups / support access
Service levels and measurement
Support line (hosting vs application)
Staging environment included?
Exit: data and backup return
Price per year x term (your model)
```

## Result

A sheet the panel can score, with every gap visible.

## Pitfalls

- Comparing monthly prices of plans with different backup and support levels.
- A host that has never restored an Exponential site.
- No staging environment, which makes every upgrade a test on production.

## Related pages

- [Procurement and training tasks](README.md)
- [Hosting costs](../../procurement/total-cost-of-ownership/hosting-costs.md)
- [Hosting and data location clause](../../procurement/tender-templates/hosting-and-data-location-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
