---
title: Patterns in extension/exp_oe_tiptap
audience: developer
category: standards
order: 434
summary: Every class, view, fetch function, operator and plugin point of extension/exp_oe_tiptap with its design patterns.
keywords: extension/exp_oe_tiptap, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/exp_oe_tiptap

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/exp_oe_tiptap` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (14)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expOETiptapAIAnthropicProvider` | class implements expOETiptapAIProvider | [Strategy / handler](strategy-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaianthropicprovider.php:15` | The Anthropic Messages API: POST {Endpoint} (default https://api.anthropic.com/v1/messages) with the headers x-api-key and anthropic-version and { mo… |
| `expOETiptapAIException` | class extends Exception | [Exception hierarchy](exception.md), [Factory and static factory methods](factory.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexception.php:18` | An error of the AI hooks whose message may be shown to the editor (ezjscore sends exception messages to the browser as error_text). |
| `expOETiptapAIExpaiProvider` | class implements expOETiptapAIProvider | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | The AI commands through the extension expai (Exponential Intelligence), [AISettings] Provider=expai: the request goes to the model expai is set up wi… |
| `expOETiptapAIHttpClient` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapaihttpclient.php:16` | JSON POST to an AI provider with curl: https only (plain http only to a loopback host, for a model on the own machine), no redirects, a total timeout… |
| `expOETiptapAIOpenAIProvider` | class implements expOETiptapAIProvider | [Strategy / handler](strategy-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiopenaiprovider.php:16` | Any provider speaking the OpenAI chat completions API: POST {Endpoint} with { model, messages: [ system, user ], max_tokens, temperature }, answer ch… |
| `expOETiptapAIPrompts` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprompts.php:28` | The prompts of the AI commands. |
| `expOETiptapAIProvider` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | A text generation backend of the AI hooks. |
| `expOETiptapAIRequestContext` | class | – | `extension/exp_oe_tiptap/classes/ai/expoetiptapairequestcontext.php:25` | The edited field an AI request names (contentObjectId, version, attributeId, languageCode) and the check that the current user may work on it, the sa… |
| `expOETiptapAIService` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md) | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiservice.php:19` | The AI hooks on the server: checks, limits, prompt, provider call, answer. |
| `expOETiptapEditor` | class | [Utility (static helper)](utility.md) | `extension/exp_oe_tiptap/classes/expoetiptapeditor.php:23` | Tiptap as an engine of ezoe's editor registry, and what the edit template needs to know about it. |
| `expOETiptapEngine` | class extends expOEEditorEngineBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap as an engine of ezoe's editor registry: registered by settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore server functions of the Tiptap editor (ezjscore.ini [ezjscServer_expoetiptap]). |
| `expOETiptapXMLInput` | class extends eZOEXMLInput | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/exp_oe_tiptap/classes/expoetiptapxmlinput.php:20` | The ezxmltext input handler of exp_oe_tiptap: ezoe's eZOEXMLInput, kept for what 0.1.0 put in place. |
| `exp_oe_tiptapInfo` | class | [Value object / struct](value-object.md) | `extension/exp_oe_tiptap/ezinfo.php:7` | – |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/exp_oe_tiptap/switch` | `switch.php` | switch | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:16` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `exp_oe_tiptap/switch` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:26` |
| `exp_oe_tiptap/ai` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:28` |

## INI plugin points (3)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_expoetiptap] Class` | `expOETiptapServerFunctions` | `extension/exp_oe_tiptap/settings/ezjscore.ini.append.php:7` |
| `[EditorSettings] Engines[tiptap]` | `expOETiptapEngine` | `extension/exp_oe_tiptap/settings/ezoe.ini.append.php:8` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `expOETiptapXMLInput` | `extension/exp_oe_tiptap/settings/ezxml.ini.append.php:7` |
