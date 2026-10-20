---
title: "Story: Exponential Intelligence"
audience: press
category: press-media
order: 54
summary: A feature story about Exponential Intelligence (expai), the writing assistant in the Exponential administration - what it does, providers in the US, the EU or on your own server, data policy, costs and audit - with printable facts, sources and the claims to avoid.
keywords: story, Exponential Intelligence, expai, AI, writing assistant, providers, data policy, local model, audit, costs, press
type: help
---
# Story: Exponential Intelligence

This page is for journalists and creators who cover AI in content management. Exponential Intelligence is easy to
describe wrongly; this page gives the exact wording of the sources, the dates and the claims you must not make.
The next story, [an assistant that writes drafts only](story-ai-that-writes-drafts-only.md), takes one angle further.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

"Exponential Intelligence is a writing assistant built into the administration interface. Editors ask in plain
language for new content or for changes", and the assistant "writes **drafts**, as the signed-in editor and with that
editor's own permissions" ([README, Exponential Intelligence](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-intelligence-new)). It
works with hosted providers in the US or the EU, or with a local open-weight model on the same server; it records every
request in the audit trail without the prompt or the answer; and it shows the model, region and cost of each click.
It is the extension `se7enxweb/expai`, required by the kernel from the 6.0.15 development line on
([expai feature page](../../features/6.0/extensions/expai.md)).

## What is new, and when

| What | When | Source |
|---|---|---|
| The repository `expai` is created | 11 October 2026 | [Who is 7x?, timeline](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| Read services as tools of the assistant | 11 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#11-october-2026-read-services-as-tools-of-the-assistant) |
| `composer.json` requires `se7enxweb/expai`; the job queue `expqueue` too | 12 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution) |
| First release of the extension, 0.0.1.0 | the sources give no date | [expai, In short](../../features/6.0/extensions/expai.md#in-short) |

The 6.0.15 line is in development and not released.

## Facts you can print

| Fact | Source |
|---|---|
| It writes drafts and never publishes on its own. | [expai, In short](../../features/6.0/extensions/expai.md#in-short) |
| It works as the signed-in editor, with that editor's permissions. | same |
| Providers: Anthropic, any OpenAI-compatible service (Melious.ai in the EU, Hugging Face, OpenRouter, Mistral and others), or a local model through Ollama. | same |
| Each provider states a region (`us`, `eu`, `local`) and a retention; rules per section and class decide which content may go where. | [expai, For administrators](../../features/6.0/extensions/expai.md#for-administrators) |
| Prompts, answers and keys are never recorded in the audit trail. | same |
| Usage limits per user, day, month, site and provider, and budgets for queued work. | same |
| "EI: Summary" is one printable page for a data protection officer. | same |
| It can suggest alternative text for images and report images that lack it. | [expai, For editors](../../features/6.0/extensions/expai.md#for-editors) |
| On a new installation it is active for the administration, but "Without a key it stays idle and sends nothing." | [README, How to enable it](https://github.com/se7enxweb/exponential/blob/main/README.md#how-to-enable-it) |
| On an upgraded installation it arrives inactive until an administrator activates it. | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution) |

## What not to claim

- "The AI publishes content" or "AI writes your website". It writes drafts; people publish.
- "It is GDPR compliant" or any legal compliance. That depends on the provider, the region and the organization.
- "It is free." The software is free; a hosted provider may charge for use.
- "Exponential AI" or "ExpAI". The name is **Exponential Intelligence**; `expai` in technical text
  ([Naming rules quick card](../advocates/naming-rules-quick-card.md)).
- Which provider is "best", or anything about model quality: the sources do not compare them.
- Adoption figures: the sources do not say how many sites use it.

## Angles and headline ideas

- Choice of jurisdiction: the US, the EU or your own server, decided per provider.
- Accountability: every request audited, but never the text itself.
- Accessibility: alternative text suggestions for images.

```text
"An AI assistant that asks where your data may go"
"Exponential Intelligence: drafts from the US, the EU or your own server"
"Every request logged, no prompt stored: how Exponential audits its AI"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Design decisions, data policy, a demonstration | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| The documentation of the extension | its repository | <https://github.com/se7enxweb/expai> |
| Provider prices and terms | the provider itself | its own website |

## Related pages

- [Talking points: Exponential Intelligence](../advocates/talking-points-exponential-intelligence.md)
- [Objection: "What about the AI features?"](../advocates/objection-ai-features.md)
- [Story: an assistant that writes drafts only](story-ai-that-writes-drafts-only.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
