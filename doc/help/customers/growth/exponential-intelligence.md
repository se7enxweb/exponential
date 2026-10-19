---
title: Exponential Intelligence
audience: customer, editor, admin
category: customer-success
order: 12
summary: The owner's view of Exponential Intelligence, the writing assistant built into the administration - what it does for editors, the drafts-only rule, providers and where text goes, costs and limits, who gets access, and the decisions to take before switching it on.
keywords: Exponential Intelligence, expai, writing assistant, AI, drafts, providers, data policy, costs, limits, Vector, alt text, translation sync
type: help
---
# Exponential Intelligence

This page is for the owner who decides whether the team uses the writing assistant, and how. Exponential Intelligence
is built into the administration: editors ask in plain language for new content or changes, and the assistant looks
up what it needs and writes **drafts**, as the signed-in editor and with that editor's permissions (`README.md`,
"Exponential Intelligence"). How to use it well is on [Using AI drafts responsibly](using-ai-drafts-responsibly.md).

## Contents

1. [What it does for editors](#what-it-does-for-editors)
2. [The rule that protects you](#the-rule-that-protects-you)
3. [Where text goes](#where-text-goes)
4. [Costs and limits](#costs-and-limits)
5. [Who gets access](#who-gets-access)
6. [Decisions before switching it on](#decisions-before-switching-it-on)

## What it does for editors

From `README.md` and [expai: Exponential Intelligence](../../../features/6.0/extensions/expai.md):

| Where | What |
|---|---|
| The **Intelligence** tab of an item | a conversation per user and item; each lookup and draft is a step; the drafts it wrote are listed with links to edit, view and discard |
| The edit form | "Fill the fields" from a brief; Write, Rewrite, Shorten, Translate and Suggest per field; presets for title, summary, meta description, keywords; an **Improve** menu; values go into the form for review |
| Images | alternative text suggestions from a vision model, and a "Missing alt text" report for a subtree |
| **Vector** | a workspace for larger jobs: a plan with a cost estimate, review of every change per field before anything is written, **What changed** with revert |
| Chains and translation sync | write, review, revise and translate in steps with a human checkpoint; translations brought up to date after the source changed |
| A dashboard | cost, tokens, requests, drafts and limits |

## The rule that protects you

- **Drafts only.** It never publishes on its own. "Send for approval" runs the normal publish operation as the
  editor, so an approval workflow holds the version for the approver (`README.md`).
- **The editor's own permissions.** Every lookup and draft runs with the editor's content permissions.
- **Labeled.** Every version it wrote carries the label "Exponential Intelligence" in My drafts, version views, edit
  forms and approval items.
- **Audited.** Every request, draft and state change is an event of the audit channel `ai`; prompts, answers and keys
  are never recorded.

## Where text goes

| Provider | Region |
|---|---|
| Anthropic (Claude) | as stated by the provider |
| An OpenAI-compatible service (Melious.ai in the EU region, Hugging Face, OpenRouter, Mistral and others) | as stated by the provider |
| A local open-weight model through Ollama | on your own server; the data never leaves it |

Each provider states its region (`us`, `eu`, `local`) and retention; rules per section and content class decide which
content may go where, and every request records where it went. **EI: Summary** is one printable page for a data
protection officer (`README.md`). Only models that passed a live tool-calling probe, or are trusted by
configuration, may write drafts.

## Costs and limits

A usage log keeps the estimated cost of every request; limits can be set per user, day, month, site and provider, and
budgets for queued work (`README.md`). Ask your administrator to set limits **before** the team starts, and look at the
dashboard monthly. Provider prices are set by the providers, not by Exponential.

## Who gets access

The assistant's features are separate policies (`expai/write`, `expai/vector`, ...). On a new installation only the
Administrator role holds them (`README.md`). Give `expai/write` (and `ezjscore/call`) to the editors who should use it;
keep Vector and the administration of providers with fewer people. The switch `[AISettings] Enabled` turns it off
everywhere at once, or per siteaccess.

> **Note**
> A new installation activates the extension for the administration siteaccesses, but it **sends nothing anywhere
> until a provider key is added** (`README.md`). The key belongs in an override settings file, never in a committed
> file. Cron parts `expqueue` and `expai` run its queued work.

## Decisions before switching it on

- [ ] Which provider, in which region, with which retention: decided with your data protection officer. This is not
      legal advice.
- [ ] Which sections or classes must never be sent to an outside provider.
- [ ] Who gets `expai/write`, who gets more.
- [ ] Monthly and per-user limits.
- [ ] That approvals stay in place where they were, for AI drafts too ([Approvals and review](../day-to-day/approvals-and-review.md)).
- [ ] The team's rules: [Using AI drafts responsibly](using-ai-drafts-responsibly.md).

## Related pages

- [Using AI drafts responsibly](using-ai-drafts-responsibly.md)
- [Assisted translation with Exponential Intelligence](../../businesses/multilingual/assisted-translation-with-exponential-intelligence.md)
- [expai: Exponential Intelligence](../../../features/6.0/extensions/expai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
