---
title: Support contract reseller
audience: reseller, partner
category: partnership
order: 120
summary: How to sell support contracts for Exponential sites - what a contract covers, severity levels, the three kinds of support behind you (community, partner tickets, paid 7x support), how to staff it so your promises hold, and a contract summary to copy.
keywords: support contract, support plan, severity, response time, first response, resolution, on call, partner tickets, paid support, community support, escalation, SLA
type: help
---
# Support contract reseller

This page is for resellers who sell support: answers, fixes and advice for an Exponential site, on a schedule, for a
fee. A support contract can stand alone or come with a managed service
([Managed service reseller](managed-service-reseller.md)).

## Contents

1. [What a support contract covers](#what-a-support-contract-covers)
2. [The support behind you](#the-support-behind-you)
3. [Severity levels](#severity-levels)
4. [Staffing so that promises hold](#staffing-so-that-promises-hold)
5. [Contract summary to copy](#contract-summary-to-copy)

## What a support contract covers

| Usually in | Usually out, or billed separately |
|---|---|
| Answering editors' and administrators' questions | New features and redesigns |
| Diagnosing errors from the error reference and the logs | Content entry |
| Fixing faults in the configuration and code you delivered | Faults in code written by others, unless agreed |
| Applying Exponential and extension releases, if agreed | Upgrades that need code changes |
| Reporting bugs in Exponential to the project | A guarantee that the project fixes them by a date |

Write the line between hosting support and application support into the contract
([Support tiers for Exponential hosting](../../hosting/business/support-tiers.md#hosting-support-and-application-support)).

## The support behind you

| Kind | Who | What it promises you | Source |
|---|---|---|---|
| Community support | Everyone | Nothing: "its developers are not obligated to provide free support" | <https://exponential.earth/support> |
| Partner support | Partners, by level | GitHub issue tickets and a response time per level | [Support levels and response times](../../partners/support-levels-and-response-times.md) |
| Paid support from 7x | Anyone | Agreed with 7x | <https://exponential.earth/support> |
| Security reports | Everyone | "a first answer within one business day" | `SECURITY.md` |

None of these is a promise to **your** customer. Your contract promises what your own staff deliver; the support
behind you is your back-up ([Service level design](../offers/service-level-design.md)).

## Severity levels

A common scheme, from the hosting section ([Support tiers for Exponential hosting](../../hosting/business/support-tiers.md#severity-levels)):

| Severity | Definition | Example |
|---|---|---|
| 1 Critical | The site is down or data is being lost or exposed | Every page returns an error; a suspected compromise |
| 2 High | An important function fails, no workaround | Editors cannot publish; sign-in fails |
| 3 Normal | A function fails, a workaround exists | One view mode renders wrongly |
| 4 Low | Questions, wishes | "Can we add a language?" |

A suspected security incident is always severity 1 ([Compromised site](../../hosting/runbooks/compromised-site.md)).

## Staffing so that promises hold

| Promise | Needs |
|---|---|
| Next business day for all requests | One trained person and a deputy |
| Same business day for severity 1 and 2 | Two or more people who know the sites; a written runbook |
| Outside business hours for severity 1 | An on-call rota, paid for, with access and runbooks ([Runbooks](../../hosting/runbooks/README.md)) |

Ask customers for the right information every time: the page address and time, what they did, the **error
reference** from the error page ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)),
whether everyone is affected, and recent changes. Never ask a customer for a password.

## Contract summary to copy

```text
Support contract for <Customer>, site <example.com>
Covered: <sites, siteaccesses, extensions we delivered>
Hours: <days, hours, time zone>    Channels: <ticket / e-mail / phone for severity 1>
First response: S1 <time>, S2 <time>, S3 <time>, S4 <time>
Included hours per month: <n>; extra hours at <rate>
Releases: <we apply Exponential and extension releases within <n> days / on request>
Not covered: <list>
Escalation: <our support lead> -> <our manager>; upstream issues reported to the Exponential project
Reports: <monthly summary of tickets and work done>
```

## Related pages

- [Support tiers you offer](../offers/support-tiers-you-offer.md)
- [Support agreement template](../../customers/partners-and-hosts/support-agreement-template.md)
- [Escalation for resellers](../customers/escalation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
