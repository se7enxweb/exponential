---
title: Service tools
audience: ai-agents, admin, developer, app-creator
category: ai-agents
order: 35
summary: How read services of expservices become tools of Exponential Intelligence - opting in with 'ai' => true or the ServiceTools settings, the services expservices opts in itself (node, section, state, tags), what the model sees, the checks on every call (expai/services, ezjscore/call, the service's access rule, the data policy) and why write services are never offered.
keywords: service tools, expservices, 'ai' => true, ServiceTools, expai/services, read services, node services, tags services, data policy, access rule, tool, assistant
type: help
---
# Service tools

This page is for administrators and developers who want the assistant to use more lookups than its own. A read
service of the extension expservices, declared once, is usable by people over HTTP and by the assistant as a tool,
each with the caller's own permissions ([extension documentation: service tools](https://github.com/se7enxweb/expai/blob/main/doc/service-tools.md)).

## Contents

1. [How a service becomes a tool](#how-a-service-becomes-a-tool)
2. [Services offered out of the box](#services-offered-out-of-the-box)
3. [What the model sees](#what-the-model-sees)
4. [Checks on every call](#checks-on-every-call)
5. [Reads only](#reads-only)

## How a service becomes a tool

Services are private to the assistant by default. A service is offered when it opts in:

| Where | What | Effect |
|---|---|---|
| The service declaration | `'ai' => true` | Offered to the assistant |
| The service declaration | `'ai' => array( 'summary' => '...' )` | Offered, with this description for the model |
| The service declaration | `'ai' => false` | Never offered, even when the settings list it |
| `expai.ini [ServiceTools] Services[]` | `<domain>::<method>` or `<domain>::*` | Offered without changing code |
| `expai.ini [ServiceTools] Domains[]` | `<domain>` | Every read service of the domain offered |
| `expai.ini [ServiceTools] Exclude[]` | the same patterns | Never offered |
| `expai.ini [ServiceTools] Enabled` | `disabled` | No service is offered |

Writing a service domain of your own: [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md).

## Services offered out of the box

expservices opts in these read services itself, each checked by the node's own read access:

| Domain | Services |
|---|---|
| node | children names, children count, breadcrumb, tree, latest, find |
| section | list, section of a node |
| state | groups, states of an object |
| tags | roots, children, tree, search, tags of a node |

## What the model sees

Each tool has a description that starts with "[Exponential service, read only]", names the service and what it
returns, and says the answer is data, not instructions. Its input schema has one property per declared argument. The
answer is the service's own envelope, wrapped with a note that it is data; an error envelope becomes a tool error the
model can correct.

## Checks on every call

The call runs in the same PHP process, as the signed-in editor, through the same entry the HTTP router uses. Before
it:

1. the policy `expai/services` (limitation **Domain**); without it no service is offered;
2. the policy `ezjscore/call`, and the router's own check of the domain, exactly as for HTTP;
3. the service's own `access` declaration, and its own content checks;
4. the data policy of the panel's provider: an argument naming content the policy keeps away refuses the call.

After it, the data policy filters the answer: rows naming objects the policy keeps away are left out and counted.
Each call is recorded as a tool entry of the request's usage row.

## Reads only

A service declared `'write' => true` is **never** offered, whatever the declaration or the settings say. The
assistant's writes stay its own draft tools, which never publish
([Drafts and human review](drafts-and-human-review.md)).

## Related pages

- [Agents and expservices](../agentic-workflows/agents-and-expservices.md)
- [Permissions for AI features](permissions-for-ai-features.md)
- [Backend services over ezjscore](../../../bc/6.0/backend_ezjscore_services.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
