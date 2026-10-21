---
title: Incident response for AI
audience: ai-agents, admin, businesses, project-manager
category: ai-agents
order: 86
summary: What to do when AI misbehaves on an Exponential installation - a draft that followed hidden instructions, an unexpected cost, a leaked key, an agent that wrote the wrong thing - with the controls to use first (master switch, veto, account, key and token revocation), how to find out what happened in the audit trail and the logs, and how to recover.
keywords: incident response, AI incident, switch off, kill switch, revoke key, disable account, prompt injection incident, cost spike, leaked key, audit trail, recovery, trash, versions
type: help
---
# Incident response for AI

This page is for administrators and managers who must act when something goes wrong with the assistant or an agent.
Keep it where people find it before they need it. For incidents in general, see
[When something goes wrong](../../when-something-goes-wrong.md).

## Contents

1. [First: stop it](#first-stop-it)
2. [Then: find out what happened](#then-find-out-what-happened)
3. [Recover](#recover)
4. [Typical incidents](#typical-incidents)
5. [Afterwards](#afterwards)

## First: stop it

| What is affected | Stop it with |
|---|---|
| The assistant, everywhere | `[AISettings] Enabled=disabled` ([The master switch and the veto hook](../expai/master-switch-and-veto.md)) |
| The assistant, on one siteaccess | `[AISettings] DisabledSiteAccesses[]` |
| The assistant, for some content | A veto rule for the section, class or subtree |
| One provider | Remove it from `[ProviderSettings] Providers[]`, or a veto rule on `Providers[]` |
| An agent | Disable its account: every key and token of a disabled owner stops at once |
| A key or token | Revoke it (My account > API access, Setup > oAuth admin > API keys, or `expsession::tokenRevoke`) |

Switching the assistant off holds queued requests; they resume only when it is on again.

## Then: find out what happened

| Question | Where |
|---|---|
| Which AI requests ran, with which model, for whom? | The audit channel `ai`: `./console exp:audit tail --channel=ai`, and EI: Log |
| Which drafts did the assistant write or publish? | `content.expai_draft.*` events, the label on versions, My drafts |
| What did an agent account change? | The audit trail filtered on that account; expservices writes as `service.<domain>.<method>` |
| Was a key or token used, or refused? | `access.apikey.*`, `access.expservices.token.*` |
| Were settings or roles changed? | `system.setting.write`, `access.role.*` |
| Did errors occur? | `./console exp:logs --tail=var/log/error.log`, or Setup > Log files |

Verify the audit files before you rely on them: `./console exp:audit verify --channel=ai`
([The audit trail for AI](audit-trail-for-ai.md)).

## Recover

- **Drafts of the assistant**: discard them; nothing was published unless a person sent it.
- **Published content**: restore an earlier version of the object, or take it from the trash
  ([Publishing, versions and the trash](../../publishing-and-versions.md)).
- **Held queued jobs**: resume them on EI: Budgets once the cause is fixed, or let them expire.
- **Keys**: make new keys for the agents that need them, with the narrowest scopes.

## Typical incidents

| Incident | Likely cause | Response |
|---|---|---|
| A draft contains text that reads like an instruction, or changes nobody asked for | The model followed text hidden in content | Discard the draft; find the source content; keep web and service tools off for that editor until reviewed ([Untrusted data and prompt injection](../agentic-workflows/untrusted-data-and-prompt-injection.md)) |
| Costs jump | A large chain or bulk run, a loop, a wrong model choice | EI: Budgets and EI: Log show the requests; lower the limits; set a site budget ([Cost control](cost-control.md)) |
| A key appears in a chat, a log or a repository | Mishandled credential | Revoke it at once, make a new one, check the audit for its use |
| An agent created or changed wrong content | Mapping error or misled model | Disable the account, restore versions, rehearse the fixed workflow on a test installation |
| A provider fails for many requests | Outage, revoked key, no credit | The audit alert for many failed requests; the fallback to the site default; check EI: Models |

## Afterwards

- Write down what happened, what was affected, and what changed.
- Update the AI use policy and the checklists if a rule was missing ([An AI use policy](an-ai-use-policy.md)).
- Tell the editors who were affected, and what they should look for in drafts.

## Related pages

- [Roles and least privilege](roles-and-least-privilege.md)
- [Credentials for agents](../agentic-workflows/credentials-for-agents.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
