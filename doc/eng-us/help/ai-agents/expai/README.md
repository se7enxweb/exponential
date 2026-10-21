---
title: Exponential Intelligence for AI users
audience: ai-agents, editor, admin, developer
category: ai-agents
order: 20
summary: The part of the AI, agents and agentic section about Exponential Intelligence (expai), the writing assistant of the administration interface - what it does, which providers it supports, how drafts and review work, translation and alt text help, Vector and chains, permissions and the master switch - with every page in one table.
keywords: Exponential Intelligence, expai, writing assistant, AI drafts, providers, local model, translation, alt text, Vector, chains, permissions, master switch, veto
type: help
---
# Exponential Intelligence for AI users

This part is for editors, site managers and administrators who use, or are deciding whether to use, Exponential
Intelligence: the writing assistant built into the administration interface. It explains what the assistant does
with your content and your money, in plain language, and links to the extension's own documentation for every
setting.

## Contents

1. [What it is](#what-it-is)
2. [Every page in this part](#every-page-in-this-part)
3. [Suggested reading order](#suggested-reading-order)
4. [Where the full documentation is](#where-the-full-documentation-is)

## What it is

Exponential Intelligence is the extension `expai`. Editors ask in plain language for new content or for changes;
the assistant looks up what it needs and writes **drafts**, as the signed-in editor and with that editor's own
permissions. Nothing is published on its own ([README](../../../../../README.md#exponential-intelligence-new),
[expai feature page](../../../features/6.0/extensions/expai.md)).

It appears in these places of the administration:

| Place | What it offers |
|---|---|
| The **Intelligence** tab of a node's full view | A conversation per user and node; lookups and drafts shown as steps; links to edit, view and discard each draft |
| The edit form | "Fill the fields" from a brief, actions per field (Write, Rewrite, Shorten, Translate, Suggest), presets, the **Improve** menu, alt text for images |
| **Vector** (`expai/vector`) | Threads with a scope, plan and write modes, staged review of every change |
| Dashboard > Exponential Intelligence | Estimated cost, tokens, requests, drafts, limits, and "Start writing" |
| Setup > EI: Summary | One printable page for a data protection officer |

## Every page in this part

| Page | What it answers |
|---|---|
| [What Exponential Intelligence does](what-exponential-intelligence-does.md) | The features for editors, step by step, and what is sent to the model |
| [Drafts and human review](drafts-and-human-review.md) | Why nothing is published on its own, what "Send for approval" does, and the label on AI-written versions |
| [Providers and models](providers-and-models.md) | Anthropic, OpenAI-compatible services and local models; what has been proven with which |
| [Configuring a provider](configuring-a-provider.md) | The settings of a provider and a model, where keys go, and how to check them |
| [Local models](local-models.md) | Running a model on your own server so that nothing leaves it |
| [The write gate and the probe](the-write-gate-and-the-probe.md) | Which models may write drafts, and how a model earns it |
| [Translation help](translation-help.md) | The Translate field action, translating in the panel, and the translation sync |
| [Alt text for images](alt-text-for-images.md) | Suggestions from a vision model, and the decorative choice that stays the editor's |
| [Vector and staged review](vector-and-staged-review.md) | Bulk work with a plan, a cost estimate and review per field before anything is written |
| [Chains and queued work](chains-and-queued-work.md) | Multi-step writing with a human checkpoint, through the job queue |
| [Permissions for AI features](permissions-for-ai-features.md) | The `expai/*` policies and how they combine with content permissions |
| [The master switch and the veto hook](master-switch-and-veto.md) | Turning the assistant off everywhere or per siteaccess, and refusing requests by rule |
| [Web and history tools](web-and-history-tools.md) | Fetching allowlisted pages, a web search and the project's history, with their safeguards |
| [Service tools](service-tools.md) | Read services of expservices that the assistant may call, as the editor |
| [The dashboard and the log](dashboard-and-log.md) | Usage, cost, drafts and limits; every request in EI: Log |
| [Extending the assistant](extending-the-assistant.md) | Field actions, tools, providers and panel sections an extension can add |

## Suggested reading order

| You are | Read |
|---|---|
| An editor | What it does, Drafts and human review, Translation help, Alt text, then the [review checklist](../checklists/reviewing-ai-drafts.md) |
| An administrator | Providers and models, Configuring a provider, The write gate, Permissions, The master switch, then the [checklist before switching it on](../checklists/before-switching-on-exponential-intelligence.md) |
| A data protection officer | Providers and models, then [Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md) and [The audit trail for AI](../governance/audit-trail-for-ai.md) |
| A developer | Service tools, Extending the assistant, then [Extending Exponential Intelligence](../../creators/app-creators/extending-exponential-intelligence.md) |

## Where the full documentation is

- **In the help center**: when expai is active, Dashboard > EI: Help opens its documentation, including its own user
  manual, among the extension documentation (needs `exphelp/read`).
- **On GitHub**: the [expai repository](https://github.com/se7enxweb/expai), with
  [INSTALL.md](https://github.com/se7enxweb/expai/blob/main/INSTALL.md),
  [USAGE.md](https://github.com/se7enxweb/expai/blob/main/USAGE.md) and the
  [documentation index](https://github.com/se7enxweb/expai/blob/main/doc/index.md).
- **For trainers**: the [Exponential Intelligence course](../../trainers/exponential-intelligence/README.md).

## Related pages

- [AI, agents and agentic: start here](../ai-agents-start-here.md)
- [Exponential Intelligence (expai) for applications](../../creators/app-creators/exponential-intelligence.md)
- [Talking points: Exponential Intelligence](../../advocates/talking-points-exponential-intelligence.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
