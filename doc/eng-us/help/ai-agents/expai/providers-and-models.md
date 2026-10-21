---
title: Providers and models
audience: ai-agents, admin, developer
category: ai-agents
order: 23
summary: Which AI providers Exponential Intelligence can use - Anthropic, any OpenAI-compatible service through one adapter, a local open-weight model, or a class of your own - what has been proven live and what only against test servers, how the model picker works, and what happens when a chosen model cannot answer.
keywords: providers, models, Anthropic, Claude, OpenAI-compatible, openai_chat, OpenRouter, Hugging Face, Mistral, Melious, Ollama, local model, custom provider, model picker, fallback, provider support, region, retention
type: help
---
# Providers and models

This page is for administrators who choose where the assistant's requests go, and for anyone who asks "which AI does
it use?". The short answer: the one you configure. The assistant speaks to providers through adapters, and the
administrator decides which providers and models are active and who may use them.

## Contents

1. [The adapters](#the-adapters)
2. [What has been proven with which provider](#what-has-been-proven-with-which-provider)
3. [The model picker](#the-model-picker)
4. [When a model cannot answer](#when-a-model-cannot-answer)
5. [Tool modes](#tool-modes)
6. [Choosing a provider](#choosing-a-provider)

## The adapters

| Type | For |
|---|---|
| `anthropic` | The Anthropic Messages API (Claude), and Anthropic-compatible servers |
| `openai_chat` | OpenAI Chat Completions and every compatible server: Hugging Face Inference Providers (router), OpenRouter, Mistral, Melious, xAI, Ollama, llama.cpp `llama-server`, vLLM, LM Studio, Azure OpenAI |
| `custom` | A class of your own implementing the provider interface ([Extending Exponential Intelligence](../../creators/app-creators/extending-exponential-intelligence.md)) |

Inside, the assistant uses one format everywhere (the shape of the Anthropic Messages API); each adapter translates
both ways. The extension ships example provider groups for Hugging Face, OpenRouter, Mistral, Melious and a local
Ollama; they stay inactive until listed in `[ProviderSettings] Providers[]`
([extension documentation: providers](https://github.com/se7enxweb/expai/blob/main/doc/providers.md)).

## What has been proven with which provider

The extension states plainly what has been run and how
([provider support](https://github.com/se7enxweb/expai/blob/main/doc/provider-support.md)):

| Provider | State, as the extension records it |
|---|---|
| Anthropic (Claude) | Proven live: the panel, tools, drafts, field actions, usage and cost, rate limits, Message Batches |
| A local open-weight model (Ollama, through `openai_chat`) | Proven live: the step loop, native tool calls, the probe, a draft, a field action; slow on a shared CPU host (minutes per step) |
| Hugging Face router, OpenRouter | Proven against test servers built from the providers' documentation, plus a live request without a key |
| OpenAI, Gemini, Mistral and other OpenAI-compatible services | The same adapter, proven against test servers only |

Melious (an EU provider of open-weight models) is documented in detail in the extension, including a live probe of
four of its models. Read the provider support page of your installed version before you rely on a provider: a
provider that has only been tested against a test server may still behave differently with a real key.

## The model picker

The dashboard, the panel and the edit form share one picker. It groups models by provider and shows for each one:

- the **region** (`us`, `eu`, `local`);
- the **relative cost** (`free`, `$`, `$$`, `$$$`, by the output price);
- "slow" for a local model, "prompted tools" or "no tools" where it applies, and "read only" for a model that may
  not write drafts;
- its state: a stopped local server or an unreachable provider cannot be chosen.

Which models a user sees: the site default, the models of providers in `[ProviderSettings] ProvidersWithoutPolicy[]`
(by default Anthropic), and the models of providers the policy `expai/model_use` allows
([Permissions for AI features](permissions-for-ai-features.md)). A model that was not offered is refused when posted.

## When a model cannot answer

With `[AISettings] FallbackToDefaultModel=enabled` (the default), a request whose model is stopped, unreachable,
timed out, no longer allowed, removed, or kept away by the data policy goes to the **site default** instead. The
editor sees one line saying so, for example "Claude ... answered instead: Qwen ... (local) is stopped". When the
default may not take the content either, the first allowed model that may answers; when none can, the editor gets a
message naming the model and the reason. The usage log records the model asked for and the reason.

A conversation belongs to its model: after a change of model it goes on from a fresh window with the earlier
exchanges as plain words, after the data policy has checked that the new provider may see what was read.

## Tool modes

| `ToolUse` of a model | What it can do |
|---|---|
| `native` | The API's tool calling; may write drafts once it passed the probe or is trusted |
| `prompted` | Tools described in the instructions, answered as JSON; read tools only, never drafts |
| `none` | No tools; a read-only snapshot of the node in the instructions |

Details: [The write gate and the probe](the-write-gate-and-the-probe.md).

## Choosing a provider

| What matters most | Look at |
|---|---|
| Content must not leave your server | A local model, `Region=local` ([Local models](local-models.md)) |
| Processing in the EU | A provider with `Region=eu`; check its terms and write the day into `TermsCheckedAt` |
| Provider keeps nothing | A provider with zero data retention agreed (`Retention=zdr`); local models keep nothing (`none`) |
| Quality of tool use and drafts | A model that passed the live probe; check Setup > EI: Models |
| Cost | The price per million tokens of each model, and lanes for bulk work ([Cost control](../governance/cost-control.md)) |

The values shipped for the example providers are a starting point, not a legal statement. Read the provider's terms
for your contract.

## Related pages

- [Configuring a provider](configuring-a-provider.md)
- [Local models](local-models.md)
- [Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)
- [Exponential Intelligence (expai) for applications](../../creators/app-creators/exponential-intelligence.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
