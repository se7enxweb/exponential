---
title: Assisted translation with Exponential Intelligence
audience: businesses, editor, project-manager
category: business-solutions
order: 60
summary: How Exponential Intelligence helps a business translate - translation drafts from a language model that a person reviews, the translation sync that keeps translations in step with changed source texts, providers and data regions, costs you can cap, and the rule that nothing is published by the assistant.
keywords: Exponential Intelligence, expai, machine translation, assisted translation, translation drafts, translation sync, review, approval, data policy, providers, EU, local model, costs, business
type: help
---
# Assisted translation with Exponential Intelligence

This page is for managers and language owners who want help from a language model to translate content, without
giving up control over what is published. Exponential Intelligence, the writing assistant of the administration,
can prepare translations as **drafts** that a person reviews. This page explains what it does, what it never does,
what you decide about data and costs, and how it fits a translation workflow. It is not legal advice about data
protection; involve your data protection officer before switching it on.

## Contents

1. [The rule: drafts only](#the-rule-drafts-only)
2. [Ways it helps with translation](#ways-it-helps-with-translation)
3. [The translation sync](#the-translation-sync)
4. [Where your text goes](#where-your-text-goes)
5. [Costs and limits](#costs-and-limits)
6. [Switching it on](#switching-it-on)
7. [A review routine](#a-review-routine)

## The rule: drafts only

From the project README and the [expai feature page](../../../features/6.0/extensions/expai.md):

- The assistant writes **drafts**, as the signed-in editor and with that editor's own permissions. "It never
  publishes on its own."
- **Send for approval** runs the normal publish operation as the editor, so an approval workflow holds the version
  for the approver ([Translation workflow](translation-workflow.md#approval-per-language)).
- Every version it wrote carries the label "Exponential Intelligence" in My drafts, version views, edit forms and
  approval items, so reviewers know what they are looking at.

For a business this means machine-prepared text cannot reach customers unless a person with publishing rights lets
it through.

## Ways it helps with translation

| Where | What it does | Source |
|---|---|---|
| The edit form | A **Translate** action per field, next to Write, Rewrite, Shorten and Suggest; values go into the form for review | [expai](../../../features/6.0/extensions/expai.md#for-editors) |
| Vector, the workspace for larger jobs | Plans with a cost estimate, staged review of every change per field before anything is written, and "What changed" with revert | same |
| AI chains | Write, review, revise and translate in steps, with a human checkpoint | same |
| Translation sync | Keeps existing translations in step when the source text changes | below |

## The translation sync

When the source language of an object changes, its translations are out of date. The translation sync, documented in
the extension's own [translation sync guide](https://github.com/se7enxweb/expai/blob/main/doc/translation-sync.md):

1. finds what changed in the source since the translation was made;
2. decides per translation whether nothing needs doing, a **patch** of the changed passages is enough, or a **full**
   translation is needed;
3. queues small, cheap patches at once, and lets an editor approve the others;
4. writes the result as a **draft** of the translation, labeled "Exponential Intelligence". "Nothing is published."

In a patch, unchanged passages keep the existing translation exactly, and links, emphasis and embedded objects travel
as placeholders so they stay as they are. The guide lists what makes the result refused rather than written (for
example an answer that drops an embed or changes numbers).

It can be started after every publish by a workflow event, from the **Translations** section of the node's
Intelligence tab (buttons Check translations, Approve, Translate in full and Skip), or from the command line. The
buttons need the policy `expai/translation_sync` and the right to edit the object in that language.

> **Tip**
> Patches keep human corrections in unchanged passages. That makes the sync a good fit for a business that has its
> key pages translated by people and only wants help with later edits.

## Where your text goes

Providers listed in the README: Anthropic; any OpenAI-compatible service (Melious.ai in the EU region, Hugging Face,
OpenRouter, Mistral and others); and local open-weight models through Ollama, where "the data never leaves the
server". Safeguards from the same source and the [feature page](../../../features/6.0/extensions/expai.md#for-administrators):

- Each provider states its **region** (`us`, `eu`, `local`) and retention; rules per section and content class decide
  which content may go where, and every request records where it went.
- **EI: Summary** is one printable page for a data protection officer with every provider's region, retention, terms
  date and key source (never the key).
- Every request, draft and draft state change is an event of the **audit trail**; prompts, answers and keys are never
  recorded.
- Only models that passed a live tool-calling probe, or are trusted by configuration, may write drafts.

Decide with your data protection officer which content may be sent to which provider, before anyone uses it
([Security](../security/README.md)).

## Costs and limits

Every request is in a usage log with its estimated cost from a price table; limits can be set per user, day, month,
site and provider, and budgets cap queued work
([expai](../../../features/6.0/extensions/expai.md#for-administrators)). The translation sync estimates the cost of
each job before it is queued, and a job the monthly budget cannot take is held.

The project publishes no price list for providers, and this page gives none: costs depend on your provider, model and
volume. Use the dashboard's figures from a pilot to budget.

## Switching it on

The README describes two cases:

- **A new installation**: Exponential Intelligence is already active for the administration siteaccesses, its tables
  exist and the Administrator role holds its policies. It "stays idle and sends nothing" until a provider key is
  added, and the cron parts for the job queue and the assistant must be scheduled.
- **An earlier installation**: activate the extension, add the key, create its tables, give editors the policy
  `expai/write`, and add the cron parts.

The [feature page](../../../features/6.0/extensions/expai.md#switch-it-on) has the steps. The master switch
`[AISettings] Enabled` turns everything off at once, or per siteaccess.

## A review routine

- [ ] Only people who read the target language review and publish its drafts.
- [ ] Reviewers check names, numbers, prices, legal wording and tone, not just grammar.
- [ ] Machine-prepared legal texts (terms, privacy notices) are always reviewed by someone qualified.
- [ ] The approval workflow covers the languages that use drafts from the assistant.
- [ ] The usage dashboard is checked monthly against your budget.
- [ ] Your terminology list (product names, brand words that must not be translated) is given to reviewers.

## Related pages

- [Translation workflow](translation-workflow.md)
- [Multilingual checklist](multilingual-checklist.md)
- [expai: Exponential Intelligence](../../../features/6.0/extensions/expai.md)
- [Exponential Intelligence for applications](../../creators/app-creators/exponential-intelligence.md)
- [Content languages](../../../guides/content-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
