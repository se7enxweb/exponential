---
title: "Objection: \"What about the AI features?\""
audience: advocate
category: advocacy
order: 36
summary: How to answer both kinds of AI objection - "AI will ruin our content or leak our data" and "we need more AI" - with the documented facts about Exponential Intelligence, the controls administrators have, and what an advocate must not promise.
keywords: objection, AI, artificial intelligence, Exponential Intelligence, expai, data protection, privacy, drafts, master switch, providers, local model, costs, hallucination, editorial control
type: help
---
# Objection: "What about the AI features?"

This page is for advocates who meet either side of the AI debate. Some people object **because** Exponential has a
writing assistant; others object that it does **not do enough** with AI. Both deserve the same thing: the precise facts
about [Exponential Intelligence](talking-points-exponential-intelligence.md) and the controls that exist.

## Contents

1. [Objections from AI skeptics](#objections-from-ai-skeptics)
2. [Objections from AI enthusiasts](#objections-from-ai-enthusiasts)
3. [The controls administrators have](#the-controls-administrators-have)
4. [Reply templates](#reply-templates)
5. [Do not say](#do-not-say)

## Objections from AI skeptics

| They say | Answer | Source |
|---|---|---|
| "AI will publish wrong content." | It writes drafts only and never publishes on its own; a person publishes, and workflows still apply | [expai, In short](../../features/6.0/extensions/expai.md#in-short), [For editors](../../features/6.0/extensions/expai.md#for-editors) |
| "It will leak our data." | It sends nothing until a provider key is added; you choose the region (US, EU) or a local model | [Exponential Intelligence](../who-is-the-exponential-foundation.md#exponential-intelligence), [For administrators](../../features/6.0/extensions/expai.md#for-administrators) |
| "We do not want AI at all." | There is a master switch, also per siteaccess | [For administrators](../../features/6.0/extensions/expai.md#for-administrators) |
| "Costs will run away." | Usage is logged, with limits per user, day, month, site and provider, and budgets | same |
| "Nobody will know what the AI did." | Every request, draft and draft state change is an event in the audit trail; prompts, answers and keys are never recorded | same |
| "Our data protection officer needs details." | **EI: Summary** is one printable page with every provider's region, retention and terms date | same |

## Objections from AI enthusiasts

| They say | Answer | Source |
|---|---|---|
| "Can it write whole pages?" | It can fill fields from a brief and draft content; an editor reviews and publishes | [For editors](../../features/6.0/extensions/expai.md#for-editors) |
| "Can it translate?" | Yes, as an action per field and as chains through the job queue | same |
| "Can it help with accessibility?" | It suggests alternative text and reports images that miss it | same |
| "Can I use my own model?" | A local open-weight model through Ollama is supported | [expai, In short](../../features/6.0/extensions/expai.md#in-short) |
| "Why does it not publish automatically?" | By design: drafts only, a human decides | same |

## The controls administrators have

From [expai, For administrators](../../features/6.0/extensions/expai.md#for-administrators):

- providers and models, with a probe so only tested models may write drafts;
- a data policy: region and retention per provider, rules per section and class;
- usage, cost, limits and budgets;
- a master switch and a veto hook;
- audit events;
- a summary page for data protection officers.

## Reply templates

```text
TO A SKEPTIC
Fair worry. Exponential Intelligence only writes drafts - it never publishes - and it works with the
editor's own permissions. Nothing is sent anywhere until an admin adds a provider key, you can pick a
region or run a local model, and there's a master switch to turn it off. Details: (link to expai page)

TO AN ENTHUSIAST
It does quite a lot: fill fields from a brief, rewrite and translate per field, alt text, chains through
the job queue. It always stops at a draft so a human publishes. (link)
```

## Do not say

- "It is safe for personal data" or "GDPR compliant". That depends on the provider, the region and the
  organization's own assessment.
- "It is free to use." Hosted providers may charge.
- "The AI is always right."
- "Exponential AI": the name is Exponential Intelligence ([Naming rules quick card](naming-rules-quick-card.md)).

## Related pages

- [Talking points: Exponential Intelligence](talking-points-exponential-intelligence.md)
- [Handling objections, Exponential Intelligence and data protection](../business/objection-handling.md#exponential-intelligence-and-data-protection)
- [Answering objections as an advocate](answering-objections-as-an-advocate.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
