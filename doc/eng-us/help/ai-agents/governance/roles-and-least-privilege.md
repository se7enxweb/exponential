---
title: Roles and least privilege
audience: ai-agents, admin, developer
category: ai-agents
order: 84
summary: How to give AI assistants and agents exactly the rights they need in Exponential - who each part runs as (the editor, the owner of a key or token), dedicated accounts and roles for agents, limitations by subtree, section, class and language, narrow credentials, and how to check effective rights with exp:access:check before anything runs.
keywords: least privilege, roles, policies, limitations, dedicated account, agent account, API key scopes, expservices token, subtree limitation, section limitation, exp:access:check, role audit
type: help
---
# Roles and least privilege

This page is for administrators who decide what an assistant or an agent may do. In Exponential, every AI part acts
as an account, and the account's roles and policies are the only permission model. Least privilege therefore means
one thing: the right account, with the smallest role that does the job.

## Contents

1. [Who each part runs as](#who-each-part-runs-as)
2. [A dedicated account per agent](#a-dedicated-account-per-agent)
3. [Narrowing a role](#narrowing-a-role)
4. [Narrowing the credential](#narrowing-the-credential)
5. [Checking effective rights](#checking-effective-rights)
6. [Reviewing regularly](#reviewing-regularly)

## Who each part runs as

| Part | Runs as | Extra limits on top |
|---|---|---|
| Exponential Intelligence | The signed-in editor; queued work as the user who queued it | The `expai/*` policies, the data policy, the write gate, the write scope of the panel or thread |
| Service tools of the assistant | The signed-in editor | `expai/services`, `ezjscore/call`, each service's own access rule ([Service tools](../expai/service-tools.md)) |
| REST API | The owner of the key or OAuth token | The key's scopes |
| expservices | The token's user | Each service's access rule |
| A coding agent on the server | The system user you start it as | Whatever that user may read and run; keep it off production ([Coding agents on an Exponential project](../agentic-workflows/coding-agents-on-an-exponential-project.md)) |

No part switches users or has a service account of its own.

## A dedicated account per agent

- Create one account per agent or per automated workflow, in a user group for agents.
- Give it a role made for that job; never the Administrator role, never a person's account.
- Disable the account when the agent is retired; every key and token of a disabled owner stops working at once.

## Narrowing a role

| Limit by | Example |
|---|---|
| Subtree | `content/create` only below "Partner events" |
| Section | `content/read` only in the public section |
| Class | `content/create` only for `event` |
| Language | `content/edit` only in the languages a translator handles |
| Function | `content/read` without `content/remove` |

For the assistant, `expai/vector` adds its own limitations (Subtree, Section, Scope, Review), and `expai/model_use`,
`expai/web` and `expai/services` can be limited by provider, tool and domain
([Permissions for AI features](../expai/permissions-for-ai-features.md)).

## Narrowing the credential

- **REST**: a personal API key with only the scopes needed (often only `read`) and a short lifetime
  ([A read-only API key, step by step](../agentic-workflows/a-read-only-api-key-step-by-step.md)).
- **expservices**: a token with a short lifetime, and only the services you chose from the catalog offered to the
  agent's model ([Agents and expservices](../agentic-workflows/agents-and-expservices.md)).
- **The assistant**: give `expai/web` and `expai/services` only where needed.

A scope that is not granted and a service that is not offered cannot be misused.

## Checking effective rights

Before an agent runs:

```bash
./console exp:access:check --user=<agent login> --node=<node id>
./console exp:access:check --user=<agent login> --node=<node id> --function=edit
./console exp:access:check --user=anonymous --node=2 --json
```

The command says what a user may do with a node or object and which limitation refuses it. Also check the account's
role assignments in the administration, and sign in as that account once to try what it should and should not do.

General background: [Roles and policies](../../../guides/roles-and-policies.md),
[Policy functions and limitations](../../creators/app-creators/policy-functions-and-limitations.md).

## Reviewing regularly

- EI: Summary lists every role with an `expai/*` policy.
- The audit trail records role changes (`access.role.*`) and every key and token event.
- Once a quarter: list agent accounts, their roles, their keys and tokens, and remove what is no longer used.

## Related pages

- [Credentials for agents](../agentic-workflows/credentials-for-agents.md)
- [Access control in code](../../creators/app-creators/access-control-in-code.md)
- [Incident response for AI](incident-response-for-ai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
