---
title: Exponential Intelligence (expai) for applications
audience: app-creator, developer, admin
category: app-creation
order: 390
summary: What the expai extension offers an application - drafts written as the editor, providers and data policy, the job queue, usage and budgets, audit - how to switch it on, and how an application plugs into it.
keywords: Exponential Intelligence, expai, AI, assistant, drafts, providers, Anthropic, OpenAI-compatible, Ollama, data policy, budgets, audit, expqueue, app creator
type: help
---
# Exponential Intelligence (expai) for applications

This page is for application creators who want AI assistance in their application or want to know how it fits.
Exponential Intelligence is the `expai` extension: a writing assistant in the administration that looks up what it
needs and writes **drafts** as the signed-in editor, never publishing on its own
([expai](../../../features/6.0/extensions/expai.md)). Its full documentation is in its repository and in
`extension/expai/doc/`.

## Contents

1. [What it does](#what-it-does)
2. [Requirements and switching it on](#requirements-and-switching-it-on)
3. [Data policy, usage and audit](#data-policy-usage-and-audit)
4. [How applications plug in](#how-applications-plug-in)
5. [Design rules for AI features](#design-rules-for-ai-features)

## What it does

- **The Intelligence tab** of a node: a conversation per user and node; each lookup and draft is a step.
- **The edit form**: fill fields from a brief, field actions (write, rewrite, shorten, translate, suggest), presets per
  class, an Improve menu, alternative texts for images from a vision model.
- **Vector**: threads with plan and write modes, staged review, checkpoints, revert.
- **Chains** and translation sync run through the job queue.

## Requirements and switching it on

From the feature page: Exponential 6.0.14 or later, PHP 8.0+ with `curl`, `dom`, `json`, `mbstring`; ezjscore and
ezformtoken; `expqueue` for queued work; a provider key or a local model (Anthropic, OpenAI-compatible services, or
Ollama).

1. Activate it in each admin siteaccess (`ActiveAccessExtensions[]=expai`) or everywhere.
2. Put keys only in `settings/override/expai.ini.append.php` (mode 0640, never committed).
3. Activate and run `expqueue` for queued work ([Queues with expqueue](queues-with-expqueue.md)).

## Data policy, usage and audit

- A master switch (`[AISettings] Enabled`) and a veto hook.
- Per provider: a region (`us`, `eu`, `local`) and retention; rules per section and class.
- Every request in a usage log (user IDs only), estimated cost, limits and budgets.
- Every request, draft and draft state change in the audit trail channel `ai`; prompts, answers and keys are never
  recorded. **EI: Summary** gives a data protection officer one printable page.

## How applications plug in

| Need | Mechanism |
|---|---|
| A field action for your class | settings only (`[EditViewSettings] FieldActions[]`) |
| The assistant reading your data | a tool class, or a read service of expservices with `'ai' => true` |
| Another AI service | a provider class or a provider group in settings |
| UI in the panel | a panel section template and JavaScript hooks |

Details and worked examples: [Extending Exponential Intelligence](extending-exponential-intelligence.md).

## Design rules for AI features

- AI writes drafts; humans publish. Keep it that way in your own features.
- Run as the user, with the user's permissions.
- Keep secrets in settings, never in prompts or payloads.
- Make cost visible and limited.

## Related pages

- [Extending Exponential Intelligence](extending-exponential-intelligence.md)
- [Queues with expqueue](queues-with-expqueue.md)
- [Privacy standard](../../../standards/privacy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
