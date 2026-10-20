---
title: Why stability matters
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: What an unstable website costs a business, and the concrete things in Exponential's history, release rules and shipped tools that keep a site stable for years.
keywords: stability, reliability, long-lived, release line, published tags, return on investment, downtime, trust, maintenance cost
type: help
---
# Why stability matters

This page is for owners and managers who earn money, members or goodwill through a website and want to know whether
Exponential is a safe foundation. It explains what instability costs, and which facts about Exponential's history,
release rules and shipped tools make a stable site the normal case rather than a lucky one. Every claim links to its
source.

## Contents

1. [What instability costs a business](#what-instability-costs-a-business)
2. [A code base with a long memory](#a-code-base-with-a-long-memory)
3. [Release rules that protect you](#release-rules-that-protect-you)
4. [Tools that ship with every installation](#tools-that-ship-with-every-installation)
5. [What stability does for return on investment](#what-stability-does-for-return-on-investment)
6. [What stability does not mean](#what-stability-does-not-mean)
7. [Questions to ask yourself](#questions-to-ask-yourself)

## What instability costs a business

A site that fails costs more than the minutes it is down:

- **Lost sales and sign-ups** while the shop, the registration form or the donation page does not answer.
- **Lost trust**: a customer who meets an error page twice may not come back.
- **Staff time**: every incident pulls people away from planned work, often outside office hours.
- **Rework**: a change that breaks something has to be found, undone and done again.
- **Upgrade fear**: a team that has been burned stops upgrading, falls behind on security fixes, and makes the next
  upgrade bigger and riskier.

The size of each item is different for every organization. To put your own numbers on it, use the
[Cost of downtime worksheet](../costs/cost-of-downtime-worksheet.md) in the costs section. This page does not
quote industry figures, because the sources of this help give none.

## A code base with a long memory

Exponential did not start recently. Its code was first the code of eZ Publish, developed from 1999; the history of
the repository goes back to 2 September 2002 and contains every commit since
([Who is 7x?, the eZ Publish years](../../who-is-7x.md#the-ez-publish-years-1999-to-2021)). The content model you
use today (classes, objects, versions, siteaccesses and the template language) was introduced in that early
generation and is still the model of Exponential (same source).

The README states the project's status plainly: "Exponential has made it beyond it's end of life in 2021 and
survived. Current releases are primarily aimed at easing the requirements to support current versions of the PHP
language" ([README, "Exponential Project Status"](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-project-status)).

For a business this means:

- A site built years ago can still be upgraded along a documented path
  ([Upgrading](../../../guides/upgrading.md) covers versions from 3.10 to today).
- The skills your team or partner already has keep their value.
- Content you entered once stays where it is; you do not rebuild it for each new major version.

## Release rules that protect you

The rules below are written down in the repository, not just practiced:

| Rule | What it means for you | Source |
|---|---|---|
| A published version never changes | The version you tested is exactly the version you deploy | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Security fixes come with the next 6.0.x release | You get fixes by upgrading within the same line | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Exponential 6.0 is the maintained line | You know which line to be on | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Every package is on GitHub and Packagist | You can always get the exact code of your version | [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#the-rules-every-release-follows) |

Details are on [Release line and support policy](release-line-and-support-policy.md).

## Tools that ship with every installation

Stability also comes from being able to see trouble early and recover quickly. These are part of the product, not
paid add-ons:

| Tool | What it does for a business | Read |
|---|---|---|
| **Setup > System information** health checks | Lists what needs attention, with what to do, before it becomes an outage | [System information](../../../guides/system-information.md) |
| Error references | A customer's error page shows a reference that leads straight to the log lines | [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md) |
| Audit trail | Records who did what, and proves the record was not changed | [Audit trail](../../../features/6.0/audit-trail.md) |
| Maintenance mode | Takes the site offline with a clean page and a 503 status search engines understand | [Maintenance mode](../../../guides/maintenance-mode.md) |
| Upgrade check | Compares files and database with the release before you upgrade | [The upgrade check](../../../guides/upgrade-check.md) |
| Caches and Velocity | Keep the site answering when many people visit at once | [Caching for availability](caching-for-availability.md) |
| Velocity watchdog | Starts the Velocity server again after a crash | [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md#velocity-health-endpoint-and-watchdog) |

## What stability does for return on investment

A stable platform lowers cost in ways that add up over the years:

- **Fewer incidents** means less unplanned staff time.
- **Routine upgrades** instead of rebuilds: moving to the newest 6.0.x is a recurring task, not a project
  ([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#what-the-project-commits-to)).
- **Longer life of what you built**: designs, extensions and content models keep working across releases.
- **Predictable budgets**: when upgrades are small and regular, they can be planned as operating cost.

To turn this into your own figures, see [ROI of stability](../costs/roi-of-stability.md) and
[Total cost of ownership](../costs/total-cost-of-ownership.md).

## What stability does not mean

Be clear with your team and your customers about the limits:

- Stability of the software does not replace backups, monitoring and a tested restore. A disk can fail on any
  platform.
- The project publishes no uptime figure and no service level agreement. Commercial terms are offered by 7x and by
  hosting partners, not by the open source project
  ([SLA options](../../hosting/business/sla-options.md)).
- A stable line still needs upgrading. A site that stays on an old tag only gets security fixes by moving to a newer
  6.0.x ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- Your own extensions and templates are your responsibility; the upgrade check shows where they differ from the
  release.

## Questions to ask yourself

- [ ] Do we know which Exponential version and which PHP version our site runs?
- [ ] Do we know who would notice if the site stopped answering at night?
- [ ] When was our last backup restored on another machine?
- [ ] Is upgrading a routine on our calendar, or something we avoid?
- [ ] Could two different people on our team handle an incident?

If any answer is "no" or "not sure", the [Reliability scorecard](reliability-scorecard.md) turns these into a plan.

## Related pages

- [Reliability for businesses](README.md)
- [Why businesses rely on Exponential](../why-businesses-rely-on-exponential.md)
- [Release line and support policy](release-line-and-support-policy.md)
- [Risk management](../../leadership/risk-management.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
