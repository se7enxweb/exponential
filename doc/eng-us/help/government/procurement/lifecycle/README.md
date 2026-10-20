---
title: The product lifecycle
audience: government, project-manager, admin, hosting
category: public-sector
order: 10
summary: The lifecycle view of Exponential for a public body - release policy, supported versions, PHP and platform support, the roadmap, upgrade budgets, security patching, a yearly rhythm and the end of a contract.
keywords: lifecycle, release policy, supported versions, PHP support, roadmap, upgrades, budget, security updates, patching, end of contract, public sector
type: help
---
# The product lifecycle

This part of the procurement section is for project managers, contract managers and security officers in a public
body who must plan an Exponential site over a contract of several years. It explains what a version number promises,
which versions get fixes, how the platform under the software ages, and how to budget and contract for keeping the
site current. The main sources are [Source control and releases](../../../../standards/source-control-and-releases.md),
[`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) and [Upgrading](../../../../guides/upgrading.md).

## The pages

| Page | It answers |
|---|---|
| [Release policy and version numbers](release-policy-and-version-numbers.md) | Published versions never change; fixes come as the next release; where changes are recorded |
| [Supported versions](supported-versions.md) | 6.0.x is maintained; 5.x and older are not; how to require a supported version |
| [PHP and platform support](php-and-platform-support.md) | PHP 8.0 to 8.5, six databases, the engines, and planning platform upgrades |
| [Reading the roadmap as a public body](reading-the-roadmap.md) | The roadmap is intent; the changelog is the record; how to request or fund a feature |
| [Planning upgrades in the budget](planning-upgrades-in-the-budget.md) | Three kinds of upgrade, what drives the effort, a budget template |
| [Security updates and patching cadence](security-updates-and-patching-cadence.md) | From report to fix, what the project promises, a patching clause |
| [Keeping a site current over a multi-year contract](keeping-a-site-current.md) | A monthly, quarterly and yearly rhythm with owners and a supplier report |
| [The end of a contract](end-of-a-contract.md) | Renew, re-tender or leave, and the handover each needs |

## Reading order

1. [Release policy and version numbers](release-policy-and-version-numbers.md) and
   [Supported versions](supported-versions.md): what you can rely on.
2. [PHP and platform support](php-and-platform-support.md): the layers below the software.
3. [Security updates and patching cadence](security-updates-and-patching-cadence.md) and
   [Planning upgrades in the budget](planning-upgrades-in-the-budget.md): the obligations and their cost.
4. [Keeping a site current over a multi-year contract](keeping-a-site-current.md): the routine.
5. [Reading the roadmap as a public body](reading-the-roadmap.md) and [The end of a contract](end-of-a-contract.md):
   the future, and the way out.

## Key facts

- A published tag is never changed; a fix reaches you as the next release (`SECURITY.md`).
- 6.0.x is the maintained line; 5.x, 4.x, 3.x and older are not supported (`SECURITY.md`).
- A private security report gets a first answer within one business day; no time to fix is promised (`SECURITY.md`).
- PHP 8.0 to 8.5 are supported, Velocity included from its release 0.0.4.54 ([2. Requirements](../../../../install/02-requirements.md)).
- The project publishes no SLA and no end-of-life dates ([SLA options for Exponential hosting](../../../hosting/business/sla-options.md)).

## Related pages

- [Procuring Exponential in the public sector](../README.md)
- Government: start here (government-start-here.md, the landing page of this help section)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
