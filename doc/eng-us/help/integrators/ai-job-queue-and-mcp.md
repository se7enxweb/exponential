---
title: AI providers, the job queue and MCP
audience: integrator, developer, admin
category: integration
order: 260
summary: What Exponential offers for connecting AI providers and background work - expai (Exponential Intelligence) with Anthropic, OpenAI-compatible services and local models, expqueue as a job queue for integrations, the services an AI may call, and the state of the Model Context Protocol.
keywords: AI, artificial intelligence, expai, Exponential Intelligence, provider, Anthropic, OpenAI-compatible, Ollama, local model, data policy, region, expqueue, job queue, worker, retries, idempotency key, MCP, Model Context Protocol, tools
type: help
---
# AI providers, the job queue and MCP

This page is for integrators asked to connect Exponential to AI services, or to run integration work in the
background with retries. Two extensions that the root `composer.json` requires cover this: **expai** (Exponential
Intelligence, `~0.0.1.16`) and **expqueue** (the job queue, `~0.1.5`). Both are installed with the distribution and
stay inactive until activated.

## Contents

1. [expai: AI providers for editors](#expai-ai-providers-for-editors)
2. [expqueue: a job queue for integrations](#expqueue-a-job-queue-for-integrations)
3. [The Model Context Protocol](#the-model-context-protocol)
4. [Integration questions to settle](#integration-questions-to-settle)

## expai: AI providers for editors

expai is a writing assistant in the administration: editors ask in plain language, and the assistant looks things up
and writes **drafts** as the signed-in editor, with that editor's permissions. It never publishes on its own
([expai](../../features/6.0/extensions/expai.md)).

| Integration point | What it means for an integrator |
|---|---|
| Providers | Anthropic, any OpenAI-compatible service, or a local open-weight model through Ollama; a fallback to the site default |
| Data policy | A region (`us`, `eu`, `local`) and retention per provider, rules per section and class, recorded per request |
| Keys | Only in `settings/override/expai.ini.append.php` (`[AISettings] ApiKey`), never committed |
| Master switch | `[AISettings] Enabled`, also off per siteaccess, plus a veto hook that can refuse a request with a reason |
| Tools | Read services of `expservices` that opt in with `'ai' => true`, and external MCP servers on an allowlist |
| Usage and cost | Every request in a usage log with user ids only; limits and budgets per user, site and provider |
| Audit | Requests, drafts and draft state changes in the audit channel `ai`; prompts, answers and keys are never recorded |
| Queue | Queued work (chains, translation sync, bulk alt text) runs in `expqueue` |

A service of your integration's own `expservices` domain can be offered to the assistant as a tool by declaring it
readable for AI (`'ai' => true` in its declaration), so editors can ask, for example, for a product's ERP data while
writing ([Extending Exponential Intelligence](../creators/app-creators/extending-exponential-intelligence.md)).

## expqueue: a job queue for integrations

expqueue is a database-backed job queue: producers queue a job with one call; workers run it from cron, the command
line or a systemd service, never inside a web request ([expqueue](../../features/6.0/extensions/expqueue.md)).

| Feature | Why an integration wants it |
|---|---|
| Retries with backoff and a dead letter | A failing remote system does not lose work |
| Idempotency keys | The same record queued twice runs once |
| Leases with a reaper | A crashed worker's job is picked up again |
| Priority classes, fair scheduling, quotas, concurrency limits per resource that adapt to rate-limit hints | A bulk sync does not starve urgent jobs, and a provider's rate limit is respected |
| Dependencies, not-before times, job groups with progress, cancel, pause and resume | Multi-step syncs are visible and controllable |
| Setup > Job queue | Operators see and control jobs (`expqueue/view`, `expqueue/manage`) |

Switching it on, from the feature page: activate it globally, create the tables
(`php bin/php/console ext:expqueue:install-schema`), regenerate the autoloads, deploy, and run a worker every minute:

```cron
* * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
```

Check with `php bin/php/console ext:expqueue:enqueue expqueue.noop`, `ext:expqueue:work --once` and
`ext:expqueue:status`. An extension registers its job types and handlers in `expqueue.ini`; the producer API is in the
[expqueue documentation](https://github.com/se7enxweb/expqueue/blob/main/doc/index.md). For an integration that
already uses an outbox table and a cronjob part, expqueue is the next step when retries, priorities and visibility
matter ([Queues with expqueue](../creators/app-creators/queues-with-expqueue.md)).

## The Model Context Protocol

The kernel repository ships **no MCP server**. What exists:

- expai can call **external** MCP servers that an administrator puts on an allowlist.
- The extension point survey (Setup > RAD tools, `settings/rad.ini`) can count MCP tool registries that an extension
  declares.
- Velocity publishes `/.well-known/mcp.json` for its own handlers, not for the content
  ([Velocity discovery, federation and deploy](../../features/6.0/velocity-discovery-federation-and-deploy.md)).

An integration that must let an outside AI agent read content today uses the REST API or `expservices` with a
read-only API key or token of a service account ([API and protocol standards](../../standards/apis-and-protocols.md#model-context-protocol-mcp)).

## Integration questions to settle

- [ ] Which provider and region may process the customer's content, and is that in the customer's data processing
  agreements? (Not legal advice.)
- [ ] Which sections and classes are excluded from AI processing.
- [ ] Who may use the assistant (`expai/write` and the related policies).
- [ ] Budgets and limits per user and per month.
- [ ] Whether a local model is required for confidential content.
- [ ] Which background work runs in expqueue, with which retry policy and dead-letter handling.

## Related pages

- [expai](../../features/6.0/extensions/expai.md), [expqueue](../../features/6.0/extensions/expqueue.md)
- [Exponential Intelligence for app creators](../creators/app-creators/exponential-intelligence.md)
- [Remote services with expservices](remote-services-with-expservices.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
