---
title: A read-only API key, step by step
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 65
summary: A task guide for the narrowest credential an AI agent can hold in Exponential - an account for the agent, a role with apikey/create limited to the read scope, a personal API key with a short lifetime, a first test with curl, the answers the agent must handle, and rotation and revocation.
keywords: API key, read scope, personal API key, expk_, apikey/create, Scope limitation, agent account, curl test, Bearer, insufficient_scope, rotate, revoke, least privilege
type: help
---
# A read-only API key, step by step

This page is for administrators and developers who give an AI agent read access to content over the REST API. It
walks through making the narrowest credential: a personal API key with only the `read` scope, owned by an account made
for the agent. The full reference is [Personal API keys](../../../guides/api-keys.md).

## Contents

1. [Step 1: an account for the agent](#step-1-an-account-for-the-agent)
2. [Step 2: a role that allows read keys only](#step-2-a-role-that-allows-read-keys-only)
3. [Step 3: make the key](#step-3-make-the-key)
4. [Step 4: test it](#step-4-test-it)
5. [Step 5: hand it to the agent](#step-5-hand-it-to-the-agent)
6. [Step 6: rotate and revoke](#step-6-rotate-and-revoke)

## Step 1: an account for the agent

Create a user account named after the agent and its task ("Report agent"), in a user group for agents. Give the group
a role with `content/read` for exactly the content the agent should read, limited by subtree or section where possible
([Roles and least privilege](../governance/roles-and-least-privilege.md)).

Check what it can read:

```bash
./console exp:access:check --user=<agent login> --node=<a node it should read>
./console exp:access:check --user=<agent login> --node=<a node it should not read>
```

The first should end with ALLOWED, the second with DENIED and the limitation that refused.

## Step 2: a role that allows read keys only

In a new installation no role may make keys. Add to the agent's role:

1. Setup > Roles and policies > the role > **New policy**.
2. Module **apikey**, function **create**.
3. Limitation **Scope**: `read` only.

A key never gets a right its owner does not hold, and the page offers only the scopes the owner's rights allow.

## Step 3: make the key

1. Sign in as the agent's account and open **My account > API access** (`/apikey/list`).
2. Under **Make a new key**, name it after where it is used ("Report agent, weekly run").
3. Tick **read** only.
4. Choose the shortest lifetime that fits (7, 30, 90 or 365 days by default).
5. Press **Make the key**. The key `expk_...` is shown **once**; copy it straight into your secret store.

## Step 4: test it

```bash
export EXP_API_KEY='expk_...'
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
```

| Answer | Meaning |
|---|---|
| 200 with the content | The key works and the owner may read the node |
| 401 `invalid_token` | No key, a wrong or revoked key, a disabled owner, or the key sent in the address instead of the header |
| 401 `expired_token` | The key passed its end date |
| 403 `access_denied` | The owner may not read this content |
| 403 `insufficient_scope` | The route needs a scope the key lacks, for example a create |
| 429 `rate_limited` | Too many requests this minute; wait for `Retry-After` |

Also test that a write is refused: a `POST` to `/api/ezp/v2/content/node/create` with this key must answer 403
`insufficient_scope`.

## Step 5: hand it to the agent

- Put the key in the agent platform's secret store or an environment variable.
- Let your code add the header; the model never sees the key ([Agents and the REST API](agents-and-the-rest-api.md)).
- Write the key's name, owner, scope and end date into your list of agent credentials.

## Step 6: rotate and revoke

- **Rotate** before the end date: make a second key with the same scope, switch the agent, check that the new key's
  **Last used** fills in, then revoke the old key.
- **Revoke** at once when the key may have been seen by someone else (pasted in a chat, committed, printed in a log,
  shown to a model), or when the agent is retired.
- Administrators see and revoke every key in **Setup > oAuth admin > API keys**. Every make, revoke, first use and
  refusal is in the audit trail as `access.apikey.*`.

## Related pages

- [Credentials for agents](credentials-for-agents.md)
- [A read-only content report](workflow-read-only-content-report.md)
- [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
