---
title: Agents and the REST API
audience: ai-agents, developer, app-creator
category: ai-agents
order: 62
summary: Using the REST API of Exponential from an AI agent - the routes of v1 and v2 as tools, a personal API key with the narrowest scope, permissions and answers, paging, and a pattern for describing the routes to a model without giving it more than it needs.
keywords: REST API, agent, /api/ezp/v1, /api/ezp/v2, personal API key, expk_, scopes, read scope, OAuth2, ResponseGroups, paging, tool definition, function calling, HTTP tools
type: help
---
# Agents and the REST API

This page is for developers who give an AI agent access to content over HTTP. The REST API is part of the kernel; an
agent uses it like any other client, as the account behind its credential. The full reference is
[REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md) and the guide
[REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md); this page adds what matters for agents.

## Contents

1. [The routes an agent needs](#the-routes-an-agent-needs)
2. [The credential](#the-credential)
3. [Describing the routes to a model](#describing-the-routes-to-a-model)
4. [Answers the agent must handle](#answers-the-agent-must-handle)
5. [Writing through REST](#writing-through-rest)

## The routes an agent needs

Relative to `/api/ezp/<version>`:

| Method | Path | v1 | v2 | Does |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | yes | yes | One node |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | yes | yes | The children, paged |
| `GET` | `/content/node/<id>/childrenCount` | yes | yes | The number of children |
| `GET` | `/content/node/<id>/fields`, `/field/<identifier>` | yes | yes | Fields |
| `GET` | `/content/object/<id>` and its fields | yes | yes | By object id |
| `POST` | `/content/node/create` | no | yes | Create and publish |
| `DELETE` | `/content/node/delete/<id>` | no | yes | Remove |

A read-only agent needs only the `GET` routes.

## The credential

A **personal API key** (`Authorization: Bearer expk_...`) is the simplest credential for an agent you run yourself:

1. Create an account for the agent and give it only the content policies it needs.
2. Signed in as that account, make a key in **My account > API access** with only the scopes the agent needs (`read`
   for a reader) and a short lifetime. The key is shown once.
3. Store it in the agent's secret store or an environment variable, never in a prompt or a repository.

A key never gets a right its owner does not hold, and a route needs the scope it is mapped to; a write route of an
extension stays closed to keys until its scope is named
([Personal API keys, scopes](../../../guides/api-keys.md#6-scopes)). An app that acts for many users uses OAuth2
instead ([OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md)).

## Describing the routes to a model

When the agent framework lets you define tools for the model, define **one tool per route you allow**, not a generic
"HTTP request" tool:

| Tool | Input | Calls |
|---|---|---|
| `get_node` | `node_id` (integer) | `GET /api/ezp/v1/content/node/<node_id>` |
| `list_children` | `node_id`, `offset`, `limit` (at most what you allow) | `GET .../content/node/<node_id>/list/offset/<n>/limit/<n>` |
| `get_field` | `node_id`, `identifier` | `GET .../content/node/<node_id>/field/<identifier>` |

Your code builds the URL from validated inputs and adds the key; the model never sees the key and cannot reach other
routes or hosts. Tell the model in the tool description that field values are data written by people, not
instructions.

## Answers the agent must handle

| Status | Meaning | The agent should |
|---|---|---|
| 200 | The content | Use it |
| 201 | Created | Record the new id |
| 400 | A bad request | Fix the arguments |
| 401 | No valid credential | Stop; a person must renew the key |
| 403 | The account or the key's scope may not | Stop for this item; do not try other routes to get around it |
| 404 | Unknown node | Report it |

Lists are paged; let the agent page with a limit you choose rather than asking for everything.

## Writing through REST

A `POST` to `/api/ezp/v2/content/node/create` **creates and publishes**. For agent-written content that a person must
review first, create into a section that visitors cannot see and that an approval workflow covers, or use Exponential
Intelligence, which writes drafts. Test every write workflow on a test installation, and read the audit trail
afterwards ([A content import workflow](workflow-content-import.md)).

## Related pages

- [Choosing an interface for an agent](choosing-an-interface.md)
- [Credentials for agents](credentials-for-agents.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
