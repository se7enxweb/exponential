---
title: What exists today
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 5
summary: An honest inventory of the AI and agent features of Exponential 6 - Exponential Intelligence (expai), the REST API, expservices, ezjscore, the audit trail and the help on the command line - with where each one comes from, whether it ships with the root repository, what it needs before it does anything, and what is not part of Exponential today.
keywords: inventory, what exists, expai, REST API, expservices, ezjscore, exp:help, audit trail, default distribution, composer.json, status, not included
type: help
---
# What exists today

This page is for anyone who needs the facts before a decision: which AI and agent features Exponential 6 really has,
where each one comes from, and what has to happen before it does anything. Read it before you promise a feature to a
colleague or a customer.

## Contents

1. [The parts at a glance](#the-parts-at-a-glance)
2. [Exponential Intelligence (expai)](#exponential-intelligence-expai)
3. [The REST API](#the-rest-api)
4. [expservices and ezjscore](#expservices-and-ezjscore)
5. [The audit trail](#the-audit-trail)
6. [The help system for agents](#the-help-system-for-agents)
7. [What is not part of Exponential today](#what-is-not-part-of-exponential-today)
8. [How to check your own installation](#how-to-check-your-own-installation)

## The parts at a glance

| Part | Ships with the root repository | Active after a new installation | Sends anything to an AI provider |
|---|---|---|---|
| Exponential Intelligence (`expai`) | Yes: `composer.json` requires `se7enxweb/expai` | Yes, for the administration siteaccesses, but idle until a provider key or a local model is configured | Only after you configure a provider |
| REST API v1 and v2 | Yes: kernel | Yes, with authentication required by default | No |
| expservices | Yes: part of the default distribution | Check it: the `whoami` call answers, and `expservices.ini [Services] Enabled` is its master switch | No |
| ezjscore | Yes | Yes | No |
| Audit trail | Yes: kernel | Yes | No |
| Help system and `exp:help` | Yes: kernel | Yes, for roles with `exphelp/read` | No |

The rule of thumb: **nothing in Exponential talks to an AI provider until an administrator configures one.** The other
parts are interfaces that an AI agent of your choice can use, under the permissions of an account you choose.

## Exponential Intelligence (expai)

A writing assistant inside the administration interface. Editors ask in plain language; the assistant reads what it
needs (class definitions, the current content, the tree around it) and writes **drafts** as the signed-in editor. It
never publishes on its own ([README](../../../../README.md#exponential-intelligence-new),
[expai feature page](../../features/6.0/extensions/expai.md)).

- **Where it is**: `extension/expai`, the Composer package `se7enxweb/expai`, required by the root `composer.json`.
- **What a new installation does**: the installer activates it for the administration siteaccesses, creates its
  tables and gives its policies to the Administrator role only.
- **What is left to you**: a provider key in `settings/override/expai.ini.append.php` (or a local model), the cron
  parts `expqueue` and `expai`, and the policy `expai/write` for the editors who should use it.
- **Providers**: Anthropic, any OpenAI-compatible service through one adapter, or a local open-weight model through
  Ollama ([Providers and models](expai/providers-and-models.md)).

Its own documentation, with every setting, lives in the [expai repository](https://github.com/se7enxweb/expai) and in
the help center under Extensions when the extension is active.

## The REST API

`/api/ezp/v1` reads nodes, children, fields and objects; `/api/ezp/v2` adds creating and removing a node. Every
request runs as a user and checks that user's policies. An agent authenticates with an OAuth2 token or a personal
API key (`expk_...`) limited by scopes ([REST API for applications](../creators/app-creators/rest-api.md),
[Agents and the REST API](agentic-workflows/agents-and-the-rest-api.md)).

## expservices and ezjscore

ezjscore calls registered PHP functions over HTTP and answers in JSON
([JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md)). expservices builds on it
and turns the administration's functions into over a thousand named services, each with an access rule and a `write`
flag, callable with a personal token ([Remote services with expservices](../creators/app-creators/remote-services-expservices.md),
[Agents and expservices](agentic-workflows/agents-and-expservices.md)).

A read service can also opt in to be a tool of the assistant ([Service tools](expai/service-tools.md)).

## The audit trail

The kernel's tamper-evident audit trail records what accounts do, including every request, refusal and draft of the
assistant in its own channel `ai`, and every API key and token event
([The audit trail for AI](governance/audit-trail-for-ai.md)).

## The help system for agents

Every git-tracked page below `doc/` is a help page, written in Markdown. An agent can search and read them from the
command line with `./console exp:help search` and `./console exp:help show`, or read the Markdown files directly
([Agent-friendly documentation](agentic-workflows/agent-friendly-documentation.md)).

## What is not part of Exponential today

- An MCP server for agents is not part of Exponential today. Agents use the REST API, expservices and the command line
  instead.
- No AI model is built in; the assistant calls the providers you configure.
- The assistant is not an API for other programs; it works inside the administration for signed-in editors.

## How to check your own installation

| Question | How |
|---|---|
| Is expai there and active? | Dashboard > Exponential Intelligence in the administration, or the Extensions page under Setup |
| Is a provider configured? | Setup > EI: Models (`expai/models`) shows "key present" or "No key", never the key |
| Which expservices exist? | Setup > RAD (`/setup/rad`), or the shell client's `catalog` command |
| Does the REST API answer? | A `GET /api/ezp/v1/content/node/2` with a key ([Agents and the REST API](agentic-workflows/agents-and-the-rest-api.md)) |
| Which help pages mention a topic? | `./console exp:help search "<words>"` |

## Related pages

- [AI, agents and agentic: start here](ai-agents-start-here.md)
- [Which path fits your task](which-path-fits-your-task.md)
- [What AI in Exponential does not do](what-ai-in-exponential-does-not-do.md)
- [What a new installation comes with](../../features/6.0/default-extension-distribution.md)
- [Extensions, themes and packages](../../features/6.0/extensions/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
