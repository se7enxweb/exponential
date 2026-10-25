---
title: Chain of Responsibility
audience: developer
category: standards
order: 332
summary: A request is passed along a list of handlers until one of them takes it: login handlers, URL matchers, router routes. 19 instances in Exponential.
keywords: design pattern, chain, behavior, pattern catalogue
type: catalogue
---
# Chain of Responsibility

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A request is passed along a list of handlers until one of them takes it: login handlers, URL matchers, router routes.

| | |
|---|---|
| Group | Behavior |
| Instances | 19 |
| Recognized by | Class name contains LoginHandler, Chain, Pipeline, Middleware, Router or Matcher. |
| Extension point | site.ini [UserSettings] LoginHandler[] (tried in order), siteaccess MatchOrder. |

## Classes by area

### extension/expai (12)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiBatchOpenRouter` | class extends expAiBatchApi | `extension/expai/classes/lanes/expaibatchopenrouter.php:25` | The OpenRouter Batch API (https://openrouter.ai/docs/batch-quickstart and https://openrouter.ai/docs/api/api-reference/batch/get-a-batch, read 2026-1… | – |
| `expAiChain` | class | `extension/expai/classes/chain/expaichain.php:23` | The definition of an AI chain (doc/chains.md): an ordered list of steps, each one structured call to a model, a human checkpoint or a hand-off, read… | – |
| `expAiChainApplier` | class | `extension/expai/classes/chain/expaichainapplier.php:14` | The applier "chain" of queued requests ([QueueSettings] Appliers[chain]): hands the stored answer of a chain step to expAiChainEngine::onAnswer(), wh… | extension/expai/settings/expai.ini [QueueSettings] Appliers[chain] |
| `expAiChainCriteria` | class | `extension/expai/classes/chain/expaichaincriteria.php:23` | The pass criteria of a chain step ([ChainStep_.] PassCriteria[]): each entry is one comparison " ", all of them must hold. | – |
| `expAiChainEngine` | class | `extension/expai/classes/chain/expaichainengine.php:24` | Runs AI chains (doc/chains.md) through the job queue. | – |
| `expAiChainFunctionCollection` | class | `extension/expai/classes/chain/expaichainfunctioncollection.php:18` | Template fetch functions of AI chains: | – |
| `expAiChainPolicy` | class | `extension/expai/classes/chain/expaichainpolicy.php:20` | Access to AI chains: the policy expai/chain (limitation Chain) starts runs and acts on them at the checkpoint. | – |
| `expAiChainPrompt` | class | `extension/expai/classes/chain/expaichainprompt.php:21` | The prompts of chain steps. | – |
| `expAiChainRun` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainrun.php:20` | One run of an AI chain (table expai_chain_run). | – |
| `expAiChainSchema` | class | `extension/expai/classes/chain/expaichainschema.php:19` | The tables of AI chains, added to expAiSchema::definition() (bin/php/expaischema.php --install creates and upgrades them on every engine like the oth… | – |
| `expAiChainStep` | class extends eZPersistentObject | `extension/expai/classes/chain/expaichainstep.php:19` | One step of a chain run (table expai_chain_step): each call to a model is one row with its queue job, model, lane, tokens, cost and the usage row; a… | – |
| `expAiChainTranslateSync` | class | `extension/expai/classes/chain/expaichaintranslatesync.php:20` | The shipped translation hand-off of chains ([ChainSettings] TranslateHandler=expAiChainTranslateSync): the translate step hands the run's object to t… | extension/expai/settings/expai.ini [ChainSettings] TranslateHandler |

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerRouter` | class | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hcaptchaInfo` | class | `extension/hcaptcha/ezinfo.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison | – |

### extension/recaptcha (1)

[All patterns of extension/recaptcha](area-extension-recaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `recaptchaInfo` | class | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison | – |

### kernel/classes/datatypes (1)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |

### kernel/classes/ini (1)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSettingsChain` | class | `kernel/classes/ini/expsettingschain.php:32` | The override chain of one INI file, built once from the files eZINI reads for it, in eZINI's own order. | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestRouter` | class extends ezcMvcRouter | `kernel/private/rest/classes/router.php:11` | – | – |

### kernel/private/classes/commands (1)

[All patterns of kernel/private/classes/commands](area-kernel-private-classes-commands.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Kernel\VelocityRouter` | class | `kernel/private/classes/commands/velocity-router.php:46` | The code of bin/php/velocity-router.php, moved into a class (#207). | – |
