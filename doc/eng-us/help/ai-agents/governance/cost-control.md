---
title: Cost control
audience: ai-agents, admin, businesses, project-manager
category: ai-agents
order: 82
summary: How Exponential Intelligence keeps AI costs visible and capped - an estimated cost for every request from a price table (or the provider's reported cost), the dashboard and log, limits per conversation, session, user, day, month, site and provider, budgets with reservations for queued work, lanes and batches for cheaper bulk work, and the costs of agents outside it.
keywords: cost control, AI cost, tokens, price table, estimated cost, limits, MaxCostPerUserPerDay, MaxCostPerSitePerMonth, ProviderMaxCostPerMonth, budgets, SiteMonthly, held jobs, lanes, batch, flex, prompt caching, dashboard
type: help
---
# Cost control

This page is for administrators and managers who pay for AI use. Hosted providers bill per request, by the tokens
sent and received. Exponential Intelligence estimates every request's cost, shows it, and stops requests at the limits
you set. A local model costs no fees, but server capacity.

## Contents

1. [What a request costs](#what-a-request-costs)
2. [Where you see it](#where-you-see-it)
3. [Limits](#limits)
4. [Budgets for queued work](#budgets-for-queued-work)
5. [Lanes and batches](#lanes-and-batches)
6. [Habits that save money](#habits-that-save-money)
7. [Agents outside the assistant](#agents-outside-the-assistant)

## What a request costs

The cost of a request is, in this order: the cost the provider reported (some providers report it with every
answer), the prices of its model in the settings (`InputPerMTok`, `OutputPerMTok` and the cache prices, per million
tokens), or a price table by model id. A model of another provider without prices has an **unknown** cost rather than
a guessed one; a local model is free. Prices in another currency are converted with the ECB reference rate of the day.

These are **estimates** from list prices. The provider's invoice is authoritative.

Every edit form shows a "Defaults in use" line with the model, region and estimated cost of a click, and the alt text
note names the cost of each suggestion.

## Where you see it

| Place | Shows | Needs |
|---|---|---|
| Dashboard > Exponential Intelligence | Estimated cost, tokens, requests and drafts with trends, a chart per day, the limits | `expai/write` (own usage); `expai/log` for the whole site and the top users |
| EI: Log (`expai/log`) | Every request with model, tokens, cost, time, tools and errors, filterable | `expai/write` (own), `expai/log` (all) |
| EI: Budgets (`expai/budgets`) | The site budget, job groups, lanes, batches, held jobs | `expai/log` |
| Audit > Dashboard | Requests and the estimated cost of 7 days | `audit/read` on the channel `ai` |
| EI: Summary | Each provider's requests, tokens and cost this month, every limit that is set | `expai/summary` |

## Limits

Set any of these in `[Limits]` of your expai settings; empty means no limit:

| Limit | Resets |
|---|---|
| `MaxTokensPerConversation` | With "New conversation" |
| `MaxTokensPerSession` | When the user signs in again |
| `MaxTokensPerUserPerDay`, `MaxCostPerUserPerDay` | At midnight |
| `MaxTokensPerUserPerMonth`, `MaxCostPerUserPerMonth` | On the 1st |
| `MaxTokensPerSitePerMonth`, `MaxCostPerSitePerMonth` | On the 1st, all users together |
| `MaxRequestsPerTurn` | The most model calls one message may lead to |
| `ProviderMaxTokensPerMonth[<provider>]`, `ProviderMaxCostPerMonth[<provider>]` | On the 1st; only requests to that provider are stopped |

A request stopped by a limit is not sent; the editor sees the limit and when it resets. While a limit is set, each
request reserves its estimate before it is sent and settles with the real numbers afterwards, so parallel requests
cannot overrun it together.

## Budgets for queued work

Queued work (chains, translation sync, alt text reports, Vector background runs) spends money while nobody watches.
Two budgets keep it in bounds ([extension documentation: budgets](https://github.com/se7enxweb/expai/blob/main/doc/budgets.md)):

| Budget | Set in | Counts |
|---|---|---|
| Monthly site budget | `[Budgets] SiteMonthly` (else `[Limits] MaxCostPerSitePerMonth`) | Every request of the month plus the reservations of queued requests |
| Job group budget | Per job group, or `[Budgets] DefaultGroupBudget` | The group's settled cost plus its reservations |

A queued job reserves its estimate times a safety factor. When the reservation does not fit, the job is **held**, with
a message that names the budget and the figures, and is checked again right before its call. Held jobs resume when they
fit again: through the cron part `expai`, `./console ext:expai:queue resume`, or the button "Resume held jobs" on
EI: Budgets (policy `expai/settings`). A new month or a raised budget frees the money.

## Lanes and batches

A lane is one way of sending a request to a provider. The extension records, per provider, what it found in the
provider's documentation:

| Lane | Price, typically | Speed |
|---|---|---|
| `standard` | List price | Seconds |
| `flex` | About half, where offered | Minutes; may be refused when the provider is busy |
| `batch` | About half | Within hours (24 hours at most at the providers listed) |
| `fast` | A premium | Faster; needs its own policy `expai/lane_fast` |

The panel and the field actions always use the standard lane. Queued requests take the lane the lane policy chooses,
so bulk work can go through a batch lane. **The extension never uses a lane whose price it would have to guess**: a
lane without a confirmed price ships disabled. Plan mode in Vector shows a cost estimate before bulk work starts.

## Habits that save money

- Set a monthly site budget and a per-user daily limit from the first day; raise them when you know the real use.
- Use a smaller model as the site default for short field actions, and allow a larger one only where it helps.
- Use plan mode and the batch lane for bulk work.
- Keep prompt caching on (`[AISettings] PromptCaching`, enabled by default): it makes repeated context cheaper.
- Review the dashboard monthly, and the top users and models.

## Agents outside the assistant

The REST API and expservices cost nothing per call themselves; the agent's own provider bills the agent's use. Limits
for that live in the agent platform, not in Exponential. Let an agent page through lists with small limits (REST
`/limit/<n>`, the `limit` argument of expservices lists) so it does not pull large answers into its context.

## Related pages

- [Providers and models](../expai/providers-and-models.md)
- [Chains and queued work](../expai/chains-and-queued-work.md)
- [Total cost of ownership](../../businesses/costs/total-cost-of-ownership.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
