---
title: Choosing an interface for an agent
audience: ai-agents, developer, app-creator
category: ai-agents
order: 61
summary: The REST API, expservices, ezjscore functions and the console on the server compared for AI agents - what each one can read and write, how it authenticates, where the agent can run, whether writes are drafts or publications, and which one fits which kind of agent.
keywords: choose interface, REST API, expservices, ezjscore, console, exp:help, agent integration, authentication, reads, writes, drafts, publish, comparison
type: help
---
# Choosing an interface for an agent

This page is for developers who connect an AI agent to Exponential and must pick an interface. All of them act as an
account and respect that account's policies; they differ in what they reach, how they authenticate and where the agent
can run. An agent that speaks the Model Context Protocol can use Exponential Tron, the MCP server every installation
has in `extension/tron` (not active until an administrator activates it); the others are below.

## Contents

1. [Side by side](#side-by-side)
2. [When to use the REST API](#when-to-use-the-rest-api)
3. [When to use expservices](#when-to-use-expservices)
4. [When to use ezjscore functions of your own](#when-to-use-ezjscore-functions-of-your-own)
5. [When to use the console on the server](#when-to-use-the-console-on-the-server)
6. [When to use MCP (Exponential Tron)](#when-to-use-mcp-exponential-tron)
7. [Writes: drafts or publications](#writes-drafts-or-publications)

## Side by side

| | REST API | expservices | ezjscore function of your own | Console on the server | MCP (Exponential Tron) |
|---|---|---|---|---|---|
| Ships with the root repository | Yes (kernel) | Yes (default distribution) | Yes (ezjscore) | Yes (kernel) | Yes (required package `se7enxweb/tron`; activate it first) |
| Reaches | Nodes, children, fields, objects; create, update and remove in v2 | Over a thousand named services of the administration | What you write | Settings with their source files, access checks, logs, the audit trail, the help | The tools of its programs: search, content, classes, settings, templates and more, and your own programs |
| Authenticates | OAuth2 token or personal API key with scopes | Session, or a personal token `expt_...` | Session; your own policy check | The system user that runs the command | A Tron token `tron_...` of a dedicated user, hashed, expiring, scoped to programs |
| Writes | As the key's owner, limited by scope | As the token's user; POST | As you define | Avoid: give a coding agent read commands only | As the token's user; audited before the write runs |
| Runs where | Anywhere over HTTPS | Anywhere over HTTPS | Usually the browser | On the server, or a development copy | stdio on the server or over SSH, or HTTPS at `/tron/mcp` |
| Read next | [Agents and the REST API](agents-and-the-rest-api.md) | [Agents and expservices](agents-and-expservices.md) | [JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md) | [A diagnosis workflow with the console](workflow-diagnosis-with-the-console.md) | [Exponential Tron](../../../features/6.0/exponential-tron-mcp-server.md) |

## When to use the REST API

- The agent reads published content for another system, or creates content in a known place.
- It runs elsewhere and talks HTTPS.
- You want the narrowest credential: a personal API key with only the `read` scope
  ([A read-only API key, step by step](a-read-only-api-key-step-by-step.md)).

## When to use expservices

- The agent needs administration functions beyond content: users, caches, cronjobs, shop, feeds, tags, the audit.
- You want a catalog of services with access rules and a `write` flag per service, which maps naturally onto a tool
  list for a model.

## When to use ezjscore functions of your own

- Your own page in the administration or on the site calls the agent, and the agent needs one small, specific answer
  that no existing interface gives.
- You write the policy check yourself, as the guide shows. Prefer expservices if the function might be useful to
  others ([JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md#ezjscore-expservices-or-rest)).

## When to use the console on the server

- A coding agent works on a project and needs runtime facts: which file sets a setting (`./console exp:ini where`),
  what an account may do (`./console exp:access:check`), what the logs say (`./console exp:logs`), and what the
  documentation says (`./console exp:help search`).
- Use it on a development or staging copy. A command runs as the system user that starts it, with access to the whole
  installation, so it is not an interface for agents on production.

## When to use MCP (Exponential Tron)

- The agent is an MCP client (Claude Code, Claude Desktop, Cursor, VS Code) and should discover its tools itself.
- You want one dedicated user per agent, tokens that expire and can be revoked in one step, and an audit of every call
  in **Setup > tron**.
- You want to offer your own data as tools: a program in your extension (`tron/program.json`).
- Activate it first (`ActiveExtensions[]=tron`, `tron.php install`); the HTTP endpoint stays off until
  `[HTTPSettings] Enabled=true` ([Exponential Tron](../../../features/6.0/exponential-tron-mcp-server.md)).

## Writes: drafts or publications

Know what a write produces before an agent makes one:

| Interface | A content write produces |
|---|---|
| Exponential Intelligence | A **draft** of the editor, for review |
| REST API v2 create | A published object ("create and publish") |
| expservices content writes | What the service declares; check its catalog entry |

If people must review agent output before it goes public, read [Keeping a person in the loop](keeping-a-person-in-the-loop.md).

## Related pages

- [Which path fits your task](../which-path-fits-your-task.md)
- [Credentials for agents](credentials-for-agents.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
