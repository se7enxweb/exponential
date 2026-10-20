---
title: Support tiers for Exponential hosting
audience: hosting
category: hosting
order: 40
summary: Support levels a host can offer for Exponential sites, the line between hosting support and application support, severity definitions, what support staff should collect from a customer, and when to escalate to the project.
keywords: support, support tiers, levels, severity, escalation, hosting support, application support, tickets, diagnostics, error reference, logs, bug report, security report
type: help
---
# Support tiers for Exponential hosting

This page is for managers who define the support offer of a hosting company and for the support staff who work in
it. It answers: which tiers can you offer, where hosting support ends and application support begins, how you rate
severity, what you collect from a customer, and when a problem goes to the Exponential project.

All tiers on this page are options. Times and prices are yours to set; no source states any.

## Contents

1. [Hosting support and application support](#hosting-support-and-application-support)
2. [Tier options](#tier-options)
3. [Severity levels](#severity-levels)
4. [What to collect from the customer](#what-to-collect-from-the-customer)
5. [Escalation to the project](#escalation-to-the-project)
6. [Tools your support staff use](#tools-your-support-staff-use)
7. [A tier sheet to copy](#a-tier-sheet-to-copy)

## Hosting support and application support

| Hosting support (the host's job in every tier) | Application support (only where the tier includes it) |
|---|---|
| the server, PHP, the engine, the database server | content classes, templates, design |
| backups and restores | the customer's extensions and custom code |
| TLS certificates | editorial questions ("how do I publish?") |
| mail delivery from the server | data fixes inside the content |
| applying Exponential updates, where agreed | upgrades that need code changes |

Write this table into your support terms. Most friction in hosting support comes from an unclear line.

> **Tip**
> Editorial questions often have an answer in this help. Point customers to the Editors and Users audiences of the
> help dashboard, for example [Getting started](../../getting-started.md) and [Editing content](../../editing-content.md).

## Tier options

| Tier | Typically includes | Typically suits |
|---|---|---|
| Basic | hosting support by ticket, business hours | shared plans |
| Standard | hosting support, faster first response, phone or chat | VPS plans |
| Managed | hosting support plus Exponential updates, cache and performance tuning, restore on request | managed plans |
| Application | managed plus help with templates, extensions and configuration, by agreement | customers without their own developers |
| Development | project work: migrations, new features, extensions | billed as a project |

## Severity levels

| Severity | Definition | Example |
|---|---|---|
| 1 Critical | the site is down or data is being lost or exposed | every page returns an error; a suspected compromise |
| 2 High | an important function fails, no workaround | editors cannot publish; sign-in fails |
| 3 Normal | a function fails, a workaround exists | one view mode renders wrongly |
| 4 Low | questions, wishes | "can we add a language?" |

A suspected security incident is always severity 1: follow [Compromised site](../runbooks/compromised-site.md).

## What to collect from the customer

1. The address of the page and the time it happened (with time zone).
2. What the customer did and what they expected.
3. The **error reference** shown on the error page, if any. The administrator can look it up; see
   [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md).
4. Whether the problem happens for everyone or one account.
5. Recent changes: an update, a new extension, a settings change.

Never ask a customer to send a password. Use a throwaway test account if you must reproduce something signed in.

## Escalation to the project

| Situation | Where it goes |
|---|---|
| A reproducible bug in Exponential itself | the software issue tracker, as described on [When something goes wrong](../../when-something-goes-wrong.md) |
| A security vulnerability | privately, as `SECURITY.md` describes; never a public issue ([Security updates and disclosure](../security/security-updates-and-disclosure.md)) |
| A question about the project or community | the community issue tracker or the forums ([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#community-places)) |
| A paid support need beyond your team | a support contract with 7x or another supplier ([Working with the project and 7x](working-with-the-project-and-7x.md)) |

Before you escalate, remove passwords, secrets and personal data from logs and screenshots; `SECURITY.md` asks
for the same.

## Tools your support staff use

| Tool | What it shows | Page |
|---|---|---|
| Log viewer in the administration | errors, warnings and the request logs in one place | [Log viewer and rotation](../operations/log-viewer-and-rotation.md) |
| Request logs | every request with status and timing | [Request logs](../operations/request-logs.md) |
| Audit trail | who changed what in the administration | [Audit trail](../security/audit-trail.md) |
| Velocity control panel | workers, cache, memory of the engine | [Velocity for hosts](../servers/velocity-for-hosts.md) |
| Runbooks | step by step for common incidents | [Runbooks](../runbooks/README.md) |

## A tier sheet to copy

```text
Support tier: <name>
Channels:              <ticket | e-mail | phone | chat>
Hours:                 <hours, time zone>
First response:        S1 <time>  S2 <time>  S3 <time>  S4 <time>
Included:              hosting support; <updates>; <restores>; <application support: yes/no>
Not included:          <custom development, third-party extensions, ...>
Escalation:            to <role>, then to the project or a contracted supplier
Customer obligations:  report with page, time and error reference; keep contacts current
```

## Related pages

- [SLA options](sla-options.md)
- [Monitoring overview](../operations/monitoring-overview.md)
- [Site down](../runbooks/site-down.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
