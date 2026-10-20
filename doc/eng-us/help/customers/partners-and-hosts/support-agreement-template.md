---
title: Support agreement template
audience: customer, hosting, partner
category: customer-success
order: 7
summary: A template for the support terms between a site owner and the host or partner of an Exponential site - channels, hours, severity levels, first responses, what is included and excluded, what the customer must provide, and escalation - with fields to fill in. Not legal advice.
keywords: support agreement, template, support terms, severity, first response, channels, hours, escalation, application support, hosting support, not legal advice
type: help
---
# Support agreement template

**Template. Not legal advice.** A structure for the support terms of an Exponential site, from the owner's side. It
is built from the hosting section's tier sheet and severity levels ([Support tiers](../../hosting/business/support-tiers.md))
and from what partners are advised to put in their own support statements
([Support levels and response times](../../partners/support-levels-and-response-times.md)). Values in square
brackets are fields to agree; no figures are suggested. Have your legal adviser review the result.

## Parties and scope

| Field | Value |
|---|---|
| Customer | [organization name] |
| Provider | [host or partner name] |
| Sites covered | [site addresses] |
| Tier or plan name | [name] |
| Valid from | [date] |

## Channels and hours

| Field | Value |
|---|---|
| Channels | [ticket / e-mail / phone / chat] |
| Hours | [hours], [time zone] |
| Outside hours, for severity 1 | [phone number of a role, not a person's private number] |
| Language | [language] |

## Severity levels

The definitions follow [Support tiers](../../hosting/business/support-tiers.md); adjust the examples to your site.

| Severity | Definition | Example | First response | Updates while open |
|---|---|---|---|---|
| 1 Critical | The site is down or data is being lost or exposed | Every page returns an error; a suspected compromise | [agreed time] | Every [agreed interval] |
| 2 High | An important function fails, no workaround | Editors cannot publish; sign-in fails | [agreed time] | Every [agreed interval] |
| 3 Normal | A function fails, a workaround exists | One view renders wrongly | [agreed time] | [agreed interval] |
| 4 Low | Questions, wishes | "Can we add a language?" | [agreed time] | As agreed |

"A suspected security incident is always severity 1" (same page).

| Field | Value |
|---|---|
| "First response" means | [first answer by a person / start of work / ...] |
| Counted in | [business hours / clock hours], [time zone] |

Partners are told that the program does not publish whether its own response times mean first answer or resolution,
or business or clock hours, and to get these in writing
([Support levels and response times](../../partners/support-levels-and-response-times.md)). Settle the same two
points in your own agreement.

## Included and not included

| Area | Included? |
|---|---|
| Hosting support: server, PHP, engine, database server, backups, restores, certificates, mail delivery | [yes / no] |
| Applying Exponential updates | [yes / no / see maintenance agreement] |
| Application support: content classes, templates, design | [yes / no / up to [hours] a month] |
| The customer's own extensions and custom code | [yes / no / as follows] |
| Editorial questions ("how do I publish?") | [yes / no] |
| Data fixes inside the content | [yes / no] |
| Upgrades that need code changes | [quoted separately / included] |
| Reporting bugs in Exponential to the project and following them | [yes / no] |

The line between the two kinds of support is the most common source of friction
([Support tiers](../../hosting/business/support-tiers.md)).

## What the customer provides

- A report with the page address, the time with time zone, the error reference if shown, what was done and what
  was expected, and whether it affects everyone or one account ([Reporting problems](../healthy/reporting-problems.md)).
- Current contacts ([Escalation contacts template](escalation-contacts-template.md)).
- No passwords: "Never ask a customer to send a password"; a throwaway test account is used instead
  ([Support tiers](../../hosting/business/support-tiers.md)).

## Escalation

| Step | When | To |
|---|---|---|
| 1 | The first response time has passed | [role at the provider] |
| 2 | Severity 1 continues beyond [agreed time] | [manager role at the provider] |
| 3 | The cause is in Exponential itself | The provider reports it upstream ([GitHub issues](../healthy/github-issues.md)) or privately for security ([Private security reports](../healthy/private-security-reports.md)) |

## Reporting

| Field | Value |
|---|---|
| Ticket report | [monthly / quarterly]: tickets per severity, response times met |
| Sent to | [role] |

## Signatures

| | Customer | Provider |
|---|---|---|
| Name and role | [name, role] | [name, role] |
| Date | [date] | [date] |

## Related pages

- [SLA template](sla-template.md)
- [When to call whom](when-to-call-whom.md)
- [Contracts and SLAs](contracts-and-slas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
