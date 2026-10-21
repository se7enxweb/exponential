---
title: Configuring a provider
audience: ai-agents, admin, developer
category: ai-agents
order: 24
summary: A task guide for administrators - put a provider key where it belongs, add an OpenAI-compatible provider and its models, set region, retention and the terms date, choose the site default model, and check the result on Setup > EI: Models without ever showing the key.
keywords: configure provider, API key, settings/override, expai.ini, ProviderSettings, Provider_, Model_, DefaultModel, Region, Retention, TermsCheckedAt, KeyLocations, expai/models, key present
type: help
---
# Configuring a provider

This page is for administrators who connect Exponential Intelligence to an AI provider. It walks through the
settings in the order you need them and says where each value belongs. Every setting is described in full in the
extension's [configuration reference](https://github.com/se7enxweb/expai/blob/main/doc/configuration.md).

## Contents

1. [Before you start](#before-you-start)
2. [Step 1: the key goes into settings/override](#step-1-the-key-goes-into-settingsoverride)
3. [Step 2: another provider and its models](#step-2-another-provider-and-its-models)
4. [Step 3: region, retention and the terms date](#step-3-region-retention-and-the-terms-date)
5. [Step 4: the site default and who may choose](#step-4-the-site-default-and-who-may-choose)
6. [Step 5: check it](#step-5-check-it)
7. [What can go wrong](#what-can-go-wrong)

## Before you start

- The extension is active for your administration siteaccess(es). A new installation does this for you; an older
  one follows the steps in the [README](../../../../../README.md#how-to-enable-it).
- You have a key from the provider, or a local model ([Local models](local-models.md)).
- You have read the provider's terms on retention and training, and know which content may go there
  ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).

## Step 1: the key goes into settings/override

Keys belong in `settings/override/expai.ini.append.php` (mode 0640), never in a committed file:

```ini
[AISettings]
Enabled=true
ApiKey=<your Anthropic API key>
```

Without other provider groups, `[AISettings]` describes the provider `anthropic`. The extension ships `ApiKey=`
empty. A key may also live in an untracked location named in `[ProviderSettings] KeyLocations[]`; Setup > EI: Models
warns about a key in any other file, and about a key in a file git tracks.

The key is read on the server only and sent only to the configured endpoint. It never reaches the browser, a
template, a log or the debug output; a key-like string in a provider's error message is replaced by `[redacted]`.

## Step 2: another provider and its models

An OpenAI-compatible provider, here OpenRouter as in the extension's own example:

```ini
# settings/override/expai.ini.append.php
[ProviderSettings]
Providers[]=openrouter

[Provider_openrouter]
ApiKey=<your key>
TermsCheckedAt=<YYYY-MM-DD>
```

The shipped `[Provider_openrouter]` group already has the type `openai_chat`, its base URL and example models. For a
provider without a shipped group, write the group yourself:

| Setting | Meaning |
|---|---|
| `Type` | `anthropic`, `openai_chat` or `custom` |
| `Flavor` | For `openai_chat`: `openai`, `huggingface`, `openrouter`, `mistral`, `xai`, `ollama`, `llamacpp`, `melious` or `generic` |
| `Label` | The name in the picker and in error texts |
| `BaseURL` | The API base, for example `https://api.mistral.ai/v1` |
| `ApiKey`, `ApiKeyHeader` | The key and how it is sent (`authorization`, `x-api-key`, `api-key`, `x-goog-api-key`, or `none` for a local server) |
| `Region`, `Retention`, `TermsCheckedAt` | For the data policy (step 3) |
| `Timeout` | Seconds for one call; a local CPU model needs more |

A model is a group `[Model_<alias>]` with `Provider`, `Id` (what the API expects), `Label`, `ToolUse`
(`native`, `prompted`, `none`), `WriteTools` and its prices per million tokens (`InputPerMTok`, `OutputPerMTok`, and
the cache prices). Without prices a model of another provider has an unknown cost rather than a guessed one.

## Step 3: region, retention and the terms date

```ini
[Provider_mistral]
Region=eu            # us | eu | local
Retention=30d        # none | zdr | 30d | unknown
TermsCheckedAt=2026-10-11
```

`local` means the data never leaves the server. `zdr` means zero data retention is agreed with the provider; `none`
that it keeps nothing. Setup > EI: Models shows "not checked" until you write the day you checked the terms. These
values drive the data policy and are recorded with every request.

## Step 4: the site default and who may choose

```ini
[ProviderSettings]
DefaultModel=<alias>
ProvidersWithoutPolicy[]
ProvidersWithoutPolicy[]=anthropic
```

- `DefaultModel` is the alias of the site default; empty means the Anthropic model of `[AISettings] Model`.
- Models of providers in `ProvidersWithoutPolicy[]` can be chosen by everyone with `expai/write`; the others need
  `expai/model_use`, which can be limited by provider ([Permissions for AI features](permissions-for-ai-features.md)).
- `[AISettings] AllowUserModelChoice` switches the choice off entirely.

## Step 5: check it

1. Clear the INI cache so the new settings are read.
2. Open **Setup > EI: Models** (`expai/models`, policy `expai/model_manage`). Each provider shows "key present" or
   "No key", its region, retention and terms date, and each model its tool mode and write gate.
3. Send the test prompt of a model from that page.
4. For a model that should write drafts, run the probe ([The write gate and the probe](the-write-gate-and-the-probe.md)).
5. Open **Setup > EI: Summary** and check that it says what you meant.

## What can go wrong

| Message | Usual cause |
|---|---|
| "refused the request (access)" (401/403) | Wrong key, or a key not valid for that endpoint |
| "no credit left" (402) | The provider account has no credit |
| "does not know the configured model" (404) | The model `Id` is wrong for that provider |
| "busy or over its limit" (429/503/529) | Rate limit; the assistant retries a short `retry-after` and otherwise reports it |
| "cost unknown" in the log | The model has no prices and the provider reports none |

The extension's [troubleshooting page](https://github.com/se7enxweb/expai/blob/main/doc/troubleshooting.md) has more.

## Related pages

- [Providers and models](providers-and-models.md)
- [Environment, settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md)
- [Before switching on Exponential Intelligence](../checklists/before-switching-on-exponential-intelligence.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
