---
title: Agents and expservices
audience: ai-agents, developer, app-creator
category: ai-agents
order: 63
summary: Using expservices from an AI agent - named services of the administration over the ezjscore call interface, one answer shape, a personal token instead of a session, the catalog with access rules and write flags as the source of a tool list, and the same declarations reused as tools of the assistant.
keywords: expservices, agent, ezjscore call, services catalog, expsession tokenCreate, expt_ token, Bearer, X-Exp-Token, access rule, write flag, tool list, shell client, Python client, service tools
type: help
---
# Agents and expservices

This page is for developers who want an agent to use administration functions beyond reading content. The extension
expservices, part of the default distribution, turns those functions into over a thousand named services. Its
[guide](../../../guides/remote-services-and-apps.md) walks through calling them; this page adds what matters for an
agent.

## Contents

1. [How a service call looks](#how-a-service-call-looks)
2. [A token for the agent](#a-token-for-the-agent)
3. [The catalog as a tool list](#the-catalog-as-a-tool-list)
4. [Rules every service follows](#rules-every-service-follows)
5. [One declaration, two channels](#one-declaration-two-channels)

## How a service call looks

A service is called by name, `exp<domain>::<method>`, through the ezjscore call interface, and answers JSON:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Inside the ezjscore envelope every answer has one shape: `{"ok":true,"data":...,"meta":{...}}` or
`{"ok":false,"error":{"code":403,"message":"..."}}`. Lists are paged and `meta` carries `total`, `offset`, `limit`,
`count` and `has_more`. One shape makes it easy for an agent's code to tell success from refusal.

## A token for the agent

A session cookie expires and needs a form token for every write. For an agent, use a **personal token**:

1. Sign in as the agent's own account (never a person's).
2. Create a token with the service `expsession::tokenCreate` (a name and a lifetime in days). It is shown once,
   `expt_` followed by 48 hex characters; only its hash is stored.
3. The agent sends it as `Authorization: Bearer <token>`, or `X-Exp-Token: <token>` where a proxy drops the first
   header. Writes still need POST, but no form token.
4. List and revoke tokens with `expsession::tokenList` and `expsession::tokenRevoke`.

An unknown, revoked or expired token, or one of a disabled user, answers 401 on every service, so the agent notices
that its token died. A token cannot create tokens. Create, revoke and refusals are recorded in the audit trail
(`access.expservices.token.*`).

## The catalog as a tool list

The catalog lists every service with its summary, access rule, arguments and an example call:

```bash
cd extension/expservices/bin
./expservices-client.sh catalog node | grep -E '"method"|"summary"'
```

Each entry says `access` (`public`, `user`, or a `[module, function]` policy) and `write` (true for services that
change something). For an agent:

- Build the model's tool list from the catalog entries you choose, **not** from all of them.
- Leave out every entry with `write: true` until the read workflow is proven.
- Give the account only the policies the chosen services need.

Setup > RAD (`/setup/rad`) lists the same catalog in the administration.

## Rules every service follows

From the guide: 401 without login, 403 without the policy; a write needs POST (and a form token with a session) and
records `service.<domain>.<method>` in the audit trail; large operations are routed to content jobs. A failing policy
answers `error 403`, a bad argument `error 422` or `error 400` with the reason.

## One declaration, two channels

A service is declared once in its domain class. That declaration makes it:

| Channel | Name | Who calls it |
|---|---|---|
| An HTTP endpoint | `ezjscore/call/exp<domain>::<method>` | People, apps, agents with a token |
| A tool of Exponential Intelligence (only when the service opts in with `'ai' => true` and is a read service) | a tool name derived from the domain and method | The assistant, as the editor, with `expai/services` ([Service tools](../expai/service-tools.md)) |

Each channel runs with its caller's permissions. See
[Remote services with expservices](../../creators/app-creators/remote-services-expservices.md) for writing a service
domain of your own.

## Related pages

- [Choosing an interface for an agent](choosing-an-interface.md)
- [Credentials for agents](credentials-for-agents.md)
- [Backend services over ezjscore](../../../bc/6.0/backend_ezjscore_services.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
