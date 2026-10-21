---
title: Extending the assistant
audience: ai-agents, developer, app-creator
category: ai-agents
order: 37
summary: An overview of what an extension can add to Exponential Intelligence without changing it - field actions by settings only, tools the model can call, read services that opt in, providers, panel and edit form sections and JavaScript hooks - with the rules every addition must keep (run as the user, write drafts only) and how to check additions in Setup > RAD.
keywords: extending expai, field action, FieldActions, tool, expAiTool, provider, expAiProvider, service tool, panel section, JavaScript hook, RAD survey, extension points
type: help
---
# Extending the assistant

This page is for developers who want Exponential Intelligence to do something specific for their site. Everything is
added from your own extension, by settings and small classes; the assistant itself stays unchanged. The task guide
with code is [Extending Exponential Intelligence](../../creators/app-creators/extending-exponential-intelligence.md);
this page is the overview.

## Contents

1. [What you can add](#what-you-can-add)
2. [Rules every addition keeps](#rules-every-addition-keeps)
3. [Choosing the smallest addition](#choosing-the-smallest-addition)
4. [Checking your additions](#checking-your-additions)

## What you can add

| Addition | How | Example |
|---|---|---|
| A field action | Settings only: `[EditViewSettings] FieldActions[]` and a `[FieldAction_<id>]` group with label, datatypes, input and instruction | A "Headline" button on text line fields |
| A tool the model can call | A class extending `expAiTool`, listed in `[ToolSettings] Tools[]` | Counting children of a node |
| A read service as a tool | `'ai' => true` in an expservices declaration, no class | [Service tools](service-tools.md) |
| A provider | A `[Provider_<id>]` group for an OpenAI- or Anthropic-compatible server, or a class implementing `expAiProvider` | A company's own model gateway |
| Panel and edit form sections | Templates your extension adds to expai's lists | A section with house-style hints |
| JavaScript hooks | Script files in your design's backend script list, without inline scripts | Reacting when a draft is written |

## Rules every addition keeps

From the expai extension documentation:

- Read and write through the context's reader and writer, which run **as the current user** with the kernel's checks.
- A writing tool records its drafts and **never publishes**.
- Errors the model should hear are thrown as the assistant's own exception type, so the model can correct its call.
- New tools are offered in new conversations.
- Admin templates and scripts keep the admin rules: no inline scripts or styles, so the Content Security Policy holds.

## Choosing the smallest addition

1. Can a **field action** by settings do it? Then no code is needed.
2. Does a read **service** already return what the model needs? Opt it in.
3. Only then write a **tool** class.
4. Only for an incompatible API write a **provider** class; compatible servers need settings only.

The smaller the addition, the less there is to review, and the fewer ways a model can misuse it.

## Checking your additions

expai declares its registries for the RAD tools: **Setup > RAD > Extension point survey** counts your providers, tools
and field actions and shows any whose class, template or block is missing
([RAD extension surface](../../../bc/6.0/rad-extension-surface.md)). Then activate the extension, regenerate the
autoloads, clear the caches, and try the addition with a test editor.

## Related pages

- [Extending Exponential Intelligence](../../creators/app-creators/extending-exponential-intelligence.md)
- [Exponential Intelligence (expai) for applications](../../creators/app-creators/exponential-intelligence.md)
- [The write gate and the probe](the-write-gate-and-the-probe.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
