---
title: The audit trail for AI
audience: ai-agents, admin, businesses, government
category: ai-agents
order: 83
summary: How AI use is recorded in Exponential's tamper-evident audit trail - the channel ai of Exponential Intelligence with its events for requests, refusals, drafts, chains, models and budgets (actions and metadata, never prompts, answers or keys), the records of what agent accounts do through REST and expservices, API key and token events, and how to give an auditor exactly the AI channel.
keywords: audit trail, AI audit, channel ai, system.expai.request, content.expai_draft, service events, access.apikey, access.expservices.token, exp:audit, auditor role, Channel limitation, hash chain, SIEM, export CSV
type: help
---
# The audit trail for AI

This page is for administrators, auditors and data protection officers who must show what AI and agents did on an
installation. Exponential has a tamper-evident audit trail: canonical JSON lines with a hash chain, which can be
verified, archived, exported and forwarded ([The audit trail](../../../features/6.0/audit-trail.md)). AI use is
recorded there, as actions and metadata.

## Contents

1. [What is never recorded](#what-is-never-recorded)
2. [Exponential Intelligence: the channel ai](#exponential-intelligence-the-channel-ai)
3. [What agent accounts do](#what-agent-accounts-do)
4. [Keys and tokens of agents](#keys-and-tokens-of-agents)
5. [Reading it](#reading-it)
6. [An auditor for AI only](#an-auditor-for-ai-only)

## What is never recorded

The audit trail records who did what, when, with what result. For the assistant it **never** holds a prompt, an
answer, a draft's text, a provider's error text or a key. Personal data beyond the user id follows the kernel's audit
privacy settings.

## Exponential Intelligence: the channel ai

From the extension's [audit page](https://github.com/se7enxweb/expai/blob/main/doc/audit-trail.md), written into the
channel `ai` (`var/log/audit/ai-<day>.jsonl`):

| Event | When |
|---|---|
| `system.expai.request` | Every request sent to a model: provider, model, region, retention, purpose, lane, token counts, latency, status, estimated cost |
| `system.expai.request.failed` | The request failed or the model refused |
| `system.expai.request.refused` | The veto refused it, or the master switch is off |
| `content.expai_draft.write` | A version written by the assistant, from any source |
| `content.expai_draft.keep`, `.submit`, `.publish`, `.discard`, `.reopen` | The draft's state changed |
| `content.expai_chain.start`, `.approve`, `.changes`, `.stop`, `.resume` | An editor acted on a chain |
| `system.expai.model.action` | A local model loaded or unloaded, a model test queued |
| `system.expai.budget.hold`, `.resume` | A queued job held by a budget, or resumed |

Changes of the assistant's settings (the master switch, the data policy) written through `exp:ini` or the settings
views are recorded by the kernel as `system.setting.write`; role changes as `access.role.*`.

The volume is about one line of 1 KB per request and per draft. To record only failures, refusals and drafts, disable
`system.expai.request` in your audit settings.

## What agent accounts do

An agent that works through the REST API or expservices acts as its own account, so its actions are recorded like any
user's:

- every expservices write records `service.<domain>.<method>` in the audit trail
  ([Remote services and apps](../../../guides/remote-services-and-apps.md));
- content changes made through the kernel are recorded in the content channel under the account that made them.

This is why every agent should have an account of its own: the audit trail then answers "what did the agent do?"
directly ([Roles and least privilege](roles-and-least-privilege.md)).

## Keys and tokens of agents

| Event | Records |
|---|---|
| `access.apikey.*` | Making, revoking, first use and refusals of personal API keys |
| `access.expservices.token.create`, `.revoke`, `.failed` | expservices tokens |

## Reading it

```bash
./console exp:audit tail --channel=ai --allow-root-user
./console exp:audit search --name=system.expai.request.failed --allow-root-user
./console exp:audit verify --channel=ai --allow-root-user
./console exp:audit tail --channel=access --name='access.expservices.*' --lines=5 --allow-root-user
```

In the administration, with the assistant active, the audit console has an **Exponential Intelligence** source: a
dashboard panel (requests, failures, refusals, drafts, estimated cost of 7 days, top users and models), a chart per
day, a card per event with links to the request in EI: Log, and an alert rule for many failed requests to one model in
a short time. The CSV export of the channel `ai` adds columns such as provider, model, region, purpose, tokens and cost.

## An auditor for AI only

The `Channel` limitation of `audit/read` offers "Exponential Intelligence" (`ai`). A role with `audit/read` limited
to that channel sees the AI events and nothing of the content, access or system channels. Add `expai/log` if the
auditor should also read the usage log.

## Related pages

- [Security and audit](../../../guides/security-and-audit.md)
- [Cost control](cost-control.md)
- [Incident response for AI](incident-response-for-ai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
