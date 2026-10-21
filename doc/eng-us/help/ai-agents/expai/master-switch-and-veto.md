---
title: The master switch and the veto hook
audience: ai-agents, admin, developer
category: ai-agents
order: 32
summary: How to turn Exponential Intelligence off everywhere or per siteaccess with one setting, what happens at every entry point when it is off, and how the veto hook refuses single requests by rule (section, class, subtree, siteaccess, provider, region, purpose) or by an event listener that fails closed.
keywords: master switch, AISettings Enabled, DisabledSiteAccesses, switch off AI, kill switch, veto, RequestVeto, rules, expai/request/veto, event listener, fail closed, maintenance window, compliance
type: help
---
# The master switch and the veto hook

This page is for administrators who need to stop the assistant, entirely or in part, and for developers who connect
it to a compliance check. Two controls are checked before anything else (permissions, data policy, limits, budgets),
in one place that every call to a provider goes through.

## Contents

1. [The master switch](#the-master-switch)
2. [What happens when it is off](#what-happens-when-it-is-off)
3. [The veto rules](#the-veto-rules)
4. [A veto listener](#a-veto-listener)
5. [What is recorded](#what-is-recorded)

## The master switch

```ini
[AISettings]
Enabled=disabled
# or: on, but off on these siteaccesses
Enabled=enabled
DisabledSiteAccesses[]=intranet_admin
```

`Enabled` is off unless it says `enabled` or `true`. A siteaccess can also set it in its own
`settings/siteaccess/<siteaccess>/expai.ini.append.php`. Or do not activate the extension on that siteaccess at all.
Changes of the switch written through `exp:ini` or the settings views are recorded in the audit trail as
`system.setting.write`.

## What happens when it is off

From the extension's [switch and veto page](https://github.com/se7enxweb/expai/blob/main/doc/switch-and-veto.md):

| Entry point | When off |
|---|---|
| The Intelligence tab | Not drawn |
| Edit form controls and field actions | Not drawn; a request is refused ("Exponential Intelligence is switched off.") |
| Vector | The page says AI is not ready; every function that needs a provider is refused |
| The dashboard | Says "Switched off" |
| Chains, translation sync, other queued work | Nothing new is queued |
| Queued requests already waiting | Held, and resumed by the cron part once the assistant is on again |
| Model test and probe | Refused |
| Any other provider call | Refused before anything is sent |

Provider batches sent before the switch went off are still collected, so nothing already paid for is lost.

## The veto rules

A veto refuses one request with a reason the editor reads and that is logged. It is checked after the master switch
and before the limits, on every provider call and before a request is queued.

```ini
[RequestVeto]
Rules[]=no_ai_on_users
Rules[]=maintenance

[RequestVeto_no_ai_on_users]
Sections[]=2
Reason=Exponential Intelligence is not used for user accounts.

[RequestVeto_maintenance]
SiteAccesses[]=intranet_admin
Purposes[]=queue.*
Reason=Queued AI work is paused on the intranet until Monday.
```

| Condition | Matches when |
|---|---|
| `Sections[]`, `Classes[]`, `Subtrees[]` | The content of the request is in that section, of that class, or at or below that node |
| `SiteAccesses[]` | The siteaccess of the request (or the one a queued request came from) |
| `Providers[]`, `Regions[]` | The provider, or its region (`us`, `eu`, `local`) |
| `Purposes[]` | The purpose, such as `panel`, `vector`, a field action name, `queue.*` |
| `Reason` | The text shown and logged |

The first rule whose conditions all match refuses the request; a rule without conditions refuses every request. The
content conditions are checked not only for the starting node but for **every object the tools read**: an object
that a rule names is refused or withheld from a list, with the reason.

## A veto listener

For decisions the settings cannot express (a compliance service, office hours, a per-user flag), register a filter:

```ini
[Event]
Listeners[]=expai/request/veto@myComplianceCheck::veto
```

The listener receives the request context (user, node, object, class, section, siteaccess, provider, region,
retention, model, purpose, and what the data policy requires) and returns it; setting `veto` to a text refuses the
request. A listener that fails or returns nothing refuses the request too: **the hook fails closed**.

## What is recorded

Every refusal is a row of the usage log with the error class `veto` and the rule or listener that refused it, and an
audit event `system.expai.request.refused`. No tokens are counted and no cost is estimated, because nothing was sent
([The audit trail for AI](../governance/audit-trail-for-ai.md)).

## Related pages

- [Permissions for AI features](permissions-for-ai-features.md)
- [Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)
- [Workflows and events](../../creators/app-creators/workflows-and-events.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
