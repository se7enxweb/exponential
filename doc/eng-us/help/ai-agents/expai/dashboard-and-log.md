---
title: The dashboard and the log
audience: ai-agents, editor, admin
category: ai-agents
order: 36
summary: Where Exponential Intelligence shows what it did and what it cost - Dashboard > Exponential Intelligence with cost, tokens, requests, drafts, limits and Start writing; EI: Log with every request and the versions it wrote; the menu entries and the policies that open them.
keywords: dashboard, EI: Log, expai/dashboard, expai/log, usage, cost, tokens, requests, drafts, limits, Start writing, menu, EI: Vector, EI: Chains, EI: Budgets, EI: Models, EI: Summary
type: help
---
# The dashboard and the log

This page is for editors who want to see their own use of the assistant, and for administrators who watch the whole
site. Everything the assistant does is visible in two places: the dashboard page and the log
([extension documentation: dashboard](https://github.com/se7enxweb/expai/blob/main/doc/dashboard.md)).

## Contents

1. [The dashboard page](#the-dashboard-page)
2. [Start writing](#start-writing)
3. [EI: Log](#ei-log)
4. [The menu entries](#the-menu-entries)

## The dashboard page

**Dashboard > Exponential Intelligence** (`expai/dashboard`, policy `expai/write`) shows:

- the estimated cost, tokens, requests and drafts, with trends and a chart per day;
- the limits of the chosen period and how much of each is used;
- recent drafts and the versions waiting for your approval;
- with `expai/log`, the whole site and the top users instead of only your own use;
- the status, for example "Switched off" when the master switch is off.

## Start writing

The dashboard's **Start writing** card preselects a location (the dashboard's default node, or the content root) and
says that the assistant works there and anywhere below it, within your own permissions. **Write** opens the
Intelligence tab of that node with your request filled in; you press Send. **change** lets you choose another
location first.

## EI: Log

**EI: Log** (`expai/log`) lists requests and the versions the assistant wrote:

- with `expai/write`, your own requests and versions; with `expai/log`, everyone's, with a filter by user;
- per request: model, provider, region, tokens, estimated cost, time, the tools used, errors and refusals;
- turns with their totals; opening a turn shows its requests.

The texts of requests and answers are not stored in the log ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md#what-the-installation-keeps)).

## The menu entries

| Menu | Entry | Opens | Policies |
|---|---|---|---|
| Dashboard | Exponential Intelligence | `expai/dashboard` | `expai/write` |
| Dashboard | EI: Vector | `expai/vector` | `expai/write`, `expai/vector` |
| Dashboard | EI: Chains | `expai/chain` | `expai/write`, `expai/chain` |
| Dashboard | EI: Log | `expai/log` | `expai/write` |
| Dashboard | EI: Help | the extension's documentation in the help center | `exphelp/read` |
| Design | Missing alt text | `expai/alttext` | `expai/write` |
| Setup | EI: Summary | `expai/summary` | `expai/summary` |
| Setup | EI: Models | `expai/models` | `expai/model_manage` |
| Setup | EI: Budgets | `expai/budgets` | `expai/log` |

Entries are shown only to users with their policies. Every expai page also has a "?" link to its page in the help
center.

## Related pages

- [Cost control](../governance/cost-control.md)
- [Permissions for AI features](permissions-for-ai-features.md)
- [The audit trail for AI](../governance/audit-trail-for-ai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
