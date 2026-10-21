---
title: Credentials for agents
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 64
summary: Which credential to give an AI agent and how to keep it - a dedicated account, a scoped personal API key or an expservices token with a short lifetime, OAuth2 for applications, AI provider keys in settings/override only - plus storage, rotation, revocation and what never goes into a prompt.
keywords: credentials, secrets, API key, expk_, expt_ token, OAuth2, provider key, settings/override, rotation, revocation, least privilege, secret store, prompt
type: help
---
# Credentials for agents

This page is for developers and administrators who hand a credential to an AI agent. An agent can be wrong, can be
misled by text it reads, and leaves transcripts behind. So it should hold the narrowest credential that does the job,
for the shortest time, and never see the credential in its own context.

## Contents

1. [One account per agent](#one-account-per-agent)
2. [Which credential](#which-credential)
3. [Where credentials live](#where-credentials-live)
4. [Rotation and revocation](#rotation-and-revocation)
5. [What never goes into a prompt](#what-never-goes-into-a-prompt)

## One account per agent

Give each agent its own account, in its own user group, with a role made for it. Then:

- its permissions are exactly what you granted, not what a person happens to have;
- every action it takes is recorded under its own name in the audit trail;
- disabling the account stops it at once, everywhere.

Never give an agent the administrator account or a person's account. Details:
[Roles and least privilege](../governance/roles-and-least-privilege.md).

## Which credential

| Interface | Credential | Narrowing | Read |
|---|---|---|---|
| REST API | Personal API key `expk_...` | Scopes (`read`, `publish`, `edit`, `remove`), a lifetime, never beyond the owner's policies | [A read-only API key, step by step](a-read-only-api-key-step-by-step.md) |
| REST API for many users | OAuth2 token | The user's own policies, `oauth/authorize` | [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md) |
| expservices | Personal token `expt_...` | The token's user's policies, a lifetime | [Agents and expservices](agents-and-expservices.md) |
| Exponential Intelligence | The provider key | Lives on the server; editors never see it | [Configuring a provider](../expai/configuring-a-provider.md) |

## Where credentials live

- **AI provider keys**: in `settings/override` only (mode 0640), never in a committed file. Setup > EI: Models warns
  about a provider key anywhere else.
- **Keys and tokens an external agent uses**: in the agent platform's secret store or an environment variable,
  never in source code, a URL, a prompt or a shared configuration file.

General rules for secrets in Exponential: [Environment, settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md).

## Rotation and revocation

| Credential | Revoke | Rotate |
|---|---|---|
| Personal API key | My account > API access, or an administrator in Setup > oAuth admin > API keys | Make the new key before the old one ends, switch, revoke the old |
| expservices token | `expsession::tokenRevoke` | Create a new token, switch, revoke |
| OAuth2 | Setup > oAuth admin | Tokens expire after `TokenTTL`; refresh tokens renew them |
| Any of them | Disabling the owner's account ends every key and token at once | |

Making, revoking, first use and refusals of API keys are in the audit trail as `access.apikey.*`; expservices tokens
as `access.expservices.token.*` ([The audit trail for AI](../governance/audit-trail-for-ai.md)).

## What never goes into a prompt

- A key, token, password or session cookie, even "just for testing".
- The contents of `settings/override`.
- Personal data the task does not need.

If a credential reached a model's context or a transcript, treat it as disclosed: revoke it and make a new one
([Incident response for AI](../governance/incident-response-for-ai.md)).

## Related pages

- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Personal API keys](../../../guides/api-keys.md)
- [Before connecting an agent](../checklists/before-connecting-an-agent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
