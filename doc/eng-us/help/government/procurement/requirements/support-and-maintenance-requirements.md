---
title: Support and maintenance requirements
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 120
summary: Requirement wording for support and maintenance of an open source CMS - who supports what, severities and response times as blanks, security updates, upgrades, the upstream project's own commitments, and how to compare a supplier's offer with them.
keywords: support, maintenance, SLA, response time, severity, security updates, upgrades, supported versions, partner, 7x, community support, escalation, tender, requirements
type: help
---
# Support and maintenance requirements

This page is for procurement teams and project managers who specify support for an Exponential site. With open
source software there are three layers: the **supplier** you contract, the **upstream project** that releases the
software, and the **community**. Only the first is bound by your contract. The page gives requirement wording and
states what the project itself commits to, so you can see which promises a bidder can make on its own and which
depend on others.

## Contents
1. [The three layers of support](#the-three-layers-of-support)
2. [Requirement wording](#requirement-wording)
3. [What the project commits to](#what-the-project-commits-to)
4. [Comparing bidders](#comparing-bidders)
5. [Pitfalls](#pitfalls)

## The three layers of support

| Layer | Who | Bound by your contract? | Source |
|---|---|---|---|
| Supplier | the company you contract: a partner of the project, a hosting company, 7x, or another integrator | Yes | your contract |
| Upstream project | 7x and the Exponential Foundation, releasing 6.0.x | No, unless you contract 7x directly | [Getting support](../../../project/getting-support.md), [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Community | forums and chats | No; "its developers are not obligated to provide free support" | [Getting support](../../../project/getting-support.md) |

Partners of the project have their own published support levels from the project
([Support levels and response times](../../../partners/support-levels-and-response-times.md)); a bidder who is a
partner can name its level as part of its escalation path.

## Requirement wording

| Id | Requirement |
|---|---|
| SUP-01 | The supplier provides support for the software, the delivered design, extensions and integrations, during <hours, time zone>. |
| SUP-02 | Severities are defined as <definitions>; first-response times are <times>; resolution or workaround targets are <times>. |
| SUP-03 | Released Exponential security fixes are applied to production within <time> of release. |
| SUP-04 | The site is kept on a supported version of Exponential, PHP and the database during the contract; upgrades are planned and announced <period> ahead. |
| SUP-05 | The supplier names its escalation path to the upstream project (partner level, paid support, issue tracker) and states which issues it fixes itself. |
| SUP-06 | Defects in custom work delivered under this contract are corrected without charge for <period>. |
| SUP-07 | Support requests and their handling are logged and reported monthly. |
| SUP-08 | The supplier contributes fixes of general interest back to the project, or states why not. |

Fill the blanks from your own risk assessment. This help gives no values.

## What the project commits to

From [Who is the Exponential Foundation? The support commitment](../../../who-is-the-exponential-foundation.md#the-support-commitment)
and `SECURITY.md`:

- **Maintained line**: Exponential 6.0. Security fixes are made on `main` and published with the next 6.0.x release.
  5.x, 4.x, 3.x and older are not supported.
- **Published versions never change**: a fix arrives as the next version, not as a corrected copy of an old one
  ([Source control and releases](../../../../standards/source-control-and-releases.md)).
- **Private vulnerability reports**: a first answer within one business day.
- **PHP**: 8.0 to 8.5 supported; CI runs the tests on 8.1 to 8.5 and checks that 8.0 boots.
- **No SLA** is published by the project for general support ([SLA options](../../../hosting/business/sla-options.md)).

So SUP-03 is a promise the supplier can make (time to apply a release); a promise of time to *fix* a vulnerability
in the software depends on the project.

## Comparing bidders

| Ask | Good answer shows |
|---|---|
| Who on your staff knows Exponential? | Named roles, training completed ([Course catalogue](../../../trainers/course-catalogue.md)) |
| How do you learn of a new release? | Watching the repository releases and changelog ([6.0.15 changelog](../../../../../changelogs/6.0/6.0.15.md)) |
| How do you test an update before production? | A staging copy and a written procedure ([Upgrading customer sites](../../../hosting/operations/upgrading-customer-sites.md)) |
| What do you do when the fix is upstream? | An issue in the public tracker, a pull request, or the partner ticket route ([Filing bugs as a partner](../../../partners/filing-bugs-as-a-partner.md)) |

## Pitfalls

- Buying a response time shorter than the supplier can staff.
- Leaving extensions out of scope: most custom behavior lives in them.
- No budget for upgrades: plan them per year (see the lifecycle pages of this procurement section).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: support and maintenance clause](../tender-templates/support-and-maintenance-clause.md)
- [Tender template: service levels clause](../tender-templates/service-levels-clause.md)
- [Support tiers](../../../hosting/business/support-tiers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
