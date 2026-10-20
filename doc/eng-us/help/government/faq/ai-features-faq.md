---
title: "FAQ: AI features"
audience: government, project-manager, editor, admin
category: public-sector
order: 210
summary: Answers to the questions public bodies ask about Exponential Intelligence, the optional AI writing assistant - what it does, whether it publishes, which providers it uses, where data goes, who may use it, costs and responsible use.
keywords: AI FAQ, Exponential Intelligence, expai, artificial intelligence, writing assistant, drafts, providers, local model, Ollama, data protection, permissions, responsible use, public sector
type: help
---
# FAQ: AI features

This page answers the questions public bodies ask about Exponential Intelligence (expai), the optional writing assistant
of the administration. The answers come from [Exponential Intelligence](../../../features/6.0/extensions/expai.md) and
the [Exponential Intelligence course](../../trainers/exponential-intelligence/README.md).

> **Note** This page describes features and common practice; it is not legal advice. Rules on AI use in the public
> sector depend on your jurisdiction and organization; check them with your legal counsel.

## Contents

1. [Is AI built into Exponential?](#is-ai-built-into-exponential)
2. [What does it do?](#what-does-it-do)
3. [Can it publish on its own?](#can-it-publish-on-its-own)
4. [Which AI providers does it use?](#which-ai-providers-does-it-use)
5. [Who may use it?](#who-may-use-it)
6. [What does it cost?](#what-does-it-cost)
7. [How do we use it responsibly?](#how-do-we-use-it-responsibly)

## Is AI built into Exponential?

It is an extension, `se7enxweb/expai`, present on installations from 6.0.15 on but **inactive until activated**. A site
that does not activate it sends nothing to any AI provider ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)).

## What does it do?

Editors ask in plain language for new content or changes; the assistant looks up what it needs and writes drafts. It
appears in the Intelligence tab of a node, in the edit form (field actions, presets, an Improve menu, alternative text)
and in Vector threads ([Exponential Intelligence](../../../features/6.0/extensions/expai.md#in-short)).

## Can it publish on its own?

No. It writes drafts, as the signed-in editor and with that editor's permissions, and never publishes on its own; a
person reviews and publishes ([Exponential Intelligence](../../../features/6.0/extensions/expai.md#in-short)). The course
calls these its three promises: drafts only, as the editor, never publishing
([Module 1](../../trainers/exponential-intelligence/module-01-introducing-exponential-intelligence/README.md)).

## Which AI providers does it use?

The one you configure: Anthropic, an OpenAI-compatible service (the feature page names examples, including one in the
EU), or a local open-weight model through Ollama, which keeps requests on your own infrastructure. Your choice decides
where editors' requests and content go; decide it with your data protection officer and record it.

## Who may use it?

Only users whose roles include its policies (for example `expai/write`); administrators set it up
([Module 1](../../trainers/exponential-intelligence/module-01-introducing-exponential-intelligence/README.md),
[Roles and policies](../../../guides/roles-and-policies.md)).

## What does it cost?

The extension is free software. A hosted provider charges for use on its own terms; a local model needs your own
hardware. The administration has a budgets page for limiting use (**Setup > EI: Budgets**).

## How do we use it responsibly?

The course has a module on responsible use ([Module 6: responsible use](../../trainers/exponential-intelligence/module-06-responsible-use/README.md)).
Common practice for public bodies: a written policy on what it may be used for, human review of every draft, no
personal data in prompts unless your assessment allows it, and a record of the provider and model in use.

## Related pages

- [Government FAQ](README.md)
- [Exponential Intelligence](../../../features/6.0/extensions/expai.md)
- [FAQ: privacy and data protection](privacy-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
