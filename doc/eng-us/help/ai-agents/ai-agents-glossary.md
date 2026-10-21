---
title: AI, agents and agentic glossary
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 4
summary: The words of the AI, agents and agentic section explained in one place - agent, agentic workflow, data policy, draft, expai, expservices, lane, model, probe, prompt injection, provider, scope, service tool, staged review, token, tool, Vector, write gate and more - each with the page that explains it.
keywords: glossary, terms, definitions, AI words, agent, agentic, tool, provider, model, probe, write gate, data policy, prompt injection, lane, Vector, expai, expservices, API key, scope
type: help
---
# AI, agents and agentic glossary

This page is for readers who meet an unfamiliar term in this section. The general glossary of Exponential is
[Glossary](../../glossary.md).

## Contents

1. [A to F](#a-to-f)
2. [G to O](#g-to-o)
3. [P to Z](#p-to-z)

## A to F

- **Agent**: a program in which an AI model decides which action to take next, usually by calling tools.
  [Agentic workflows](agentic-workflows/README.md).
- **Agentic workflow**: a task carried out by an agent over several steps, such as a report or an import.
  [A content import workflow](agentic-workflows/workflow-content-import.md).
- **Audit trail**: the kernel's tamper-evident record of who did what; the assistant writes to the channel `ai`.
  [The audit trail for AI](governance/audit-trail-for-ai.md).
- **Chain (AI chain)**: multi-step writing through the job queue with a human checkpoint.
  [Chains and queued work](expai/chains-and-queued-work.md).
- **Data policy**: rules that decide which content may go to which provider, by region and retention.
  [Privacy and data leaving the site](governance/privacy-and-data-leaving-the-site.md).
- **Draft**: an unpublished version; everything Exponential Intelligence writes is one.
  [Drafts and human review](expai/drafts-and-human-review.md).
- **EI: Summary**: the printable page for a data protection officer (`expai/summary`).
- **expai**: the extension name of Exponential Intelligence. [Exponential Intelligence for AI users](expai/README.md).
- **expservices**: the extension that offers administration functions as named services.
  [Agents and expservices](agentic-workflows/agents-and-expservices.md).
- **Exponential Intelligence**: the writing assistant of the administration interface.
- **Fallback**: when a chosen model cannot answer, the site default answers instead.
  [Providers and models](expai/providers-and-models.md).

## G to O

- **Label**: the mark "Exponential Intelligence" on every version the assistant wrote.
- **Lane**: a way of sending a request (standard, flex, batch, fast) with its own price and speed.
  [Cost control](governance/cost-control.md#lanes-and-batches).
- **Local model**: an open-weight model running on your own server; region `local`. [Local models](expai/local-models.md).
- **Model**: the AI system that writes text; reached through a provider.
- **OAuth2 token**: a REST credential for an application that acts for users who sign in themselves.

## P to Z

- **Personal API key**: a REST credential `expk_...` with scopes, acting as its owner.
  [A read-only API key, step by step](agentic-workflows/a-read-only-api-key-step-by-step.md).
- **Personal token (expservices)**: a credential `expt_...` for expservices, acting as its user.
- **Plan mode**: Vector's mode in which the model proposes a plan with a cost estimate before bulk work.
  [Vector and staged review](expai/vector-and-staged-review.md).
- **Probe**: 15 scripted tasks a model must pass before it may write drafts.
  [The write gate and the probe](expai/the-write-gate-and-the-probe.md).
- **Prompt injection**: text in content written to steer a model.
  [Untrusted data and prompt injection](agentic-workflows/untrusted-data-and-prompt-injection.md).
- **Provider**: the service (or local server) that runs a model. [Providers and models](expai/providers-and-models.md).
- **Region / retention**: where a provider processes requests (`us`, `eu`, `local`) and how long it keeps them
  (`none`, `zdr`, `30d`, `unknown`).
- **Scope (API key)**: what a key may be used for (`read`, `publish`, `edit`, `remove`), each standing for a policy of
  its owner.
- **Scope (Vector)**: the nodes, subtrees, classes and languages a thread may touch.
- **Service tool**: a read service of expservices that the assistant may call. [Service tools](expai/service-tools.md).
- **Staged review**: proposals reviewed per field and part before they are written into drafts.
- **Token (billing)**: the unit providers count and bill. **Token (credential)**: a secret that authenticates.
- **Tool**: one named ability a model can call, with a description and an input schema.
- **Vector**: the workspace of threads of Exponential Intelligence for larger jobs.
- **Veto hook**: rules or a listener that refuse single AI requests with a reason.
  [The master switch and the veto hook](expai/master-switch-and-veto.md).
- **Web tools**: `web_fetch`, `web_search` and `project_history` of the assistant.
  [Web and history tools](expai/web-and-history-tools.md).
- **Write gate**: the rule that decides which models may write drafts.

## Related pages

- [AI, agents and agentic: FAQ](ai-agents-faq.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
