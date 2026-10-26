---
title: Patterns in lib/ezi18n
audience: developer
category: standards
order: 545
summary: Every class, view, fetch function, operator and plugin point of lib/ezi18n with its design patterns.
keywords: lib/ezi18n, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezi18n

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezi18n` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (22)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expI18nFallbackConfiguredRule` | class implements expI18nFallbackRuleInterface | – | `lib/ezi18n/classes/expi18nfallbackconfiguredrule.php:19` | The configured fallbacks, i18n.ini [TranslationSettings] FallbackLanguages[]: always the first rule the resolver asks, so what is configured wins ove… |
| `expI18nFallbackResolver` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Which translations a locale falls back to, in order: the one source of truth for the translator (eZTSTranslator), the interface language choice (expU… |
| `expI18nFallbackRuleInterface` | interface | [Interface (contract)](interface.md) | `lib/ezi18n/classes/expi18nfallbackruleinterface.php:27` | A translation fallback rule: says which translation a locale falls back to when a translation root (the kernel's share/translations, or an extension'… |
| `expI18nFallbackSameLanguageRule` | class implements expI18nFallbackRuleInterface | – | `lib/ezi18n/classes/expi18nfallbacksamelanguagerule.php:26` | The automatic fallback (i18n.ini [TranslationSettings] AutomaticLanguageFallback): a locale without a translation of its own in the kernel's translat… |
| `eZ1337Translator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ez1337translator.php:30` | – |
| `eZBorkTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ezborktranslator.php:46` | – |
| `eZCharsetInfo` | class | [Utility (static helper)](utility.md), [Value object / struct](value-object.md) | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – |
| `eZCharTransform` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezchartransform.php:21` | – |
| `eZCodeMapper` | class | – | `lib/ezi18n/classes/ezcodemapper.php:19` | – |
| `eZCodePage` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezcodepage.php:19` | – |
| `eZCodePageCodec` | class extends eZTextCodec | – | `lib/ezi18n/classes/ezcodepagecodec.php:19` | – |
| `eZCodePageMapper` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezcodepagemapper.php:18` | – |
| `eZMBStringMapper` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezmbstringmapper.php:25` | – |
| `eZRandomTranslator` | class extends eZTranslatorGroup | – | `lib/ezi18n/classes/ezrandomtranslator.php:19` | – |
| `eZShuffleTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ezshuffletranslator.php:19` | – |
| `eZTextCodec` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/eztextcodec.php:23` | – |
| `eZTranslationCache` | class | [Utility (static helper)](utility.md) | `lib/ezi18n/classes/eztranslationcache.php:18` | – |
| `eZTranslatorGroup` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/eztranslatorgroup.php:19` | – |
| `eZTranslatorHandler` | class | [Strategy / handler](strategy-handler.md) | `lib/ezi18n/classes/eztranslatorhandler.php:19` | – |
| `eZTranslatorManager` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/eztranslatormanager.php:31` | – |
| `eZTSTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/eztstranslator.php:17` | Provides internationalization using XML (.ts) files |
| `eZUTF8Codec` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md), [Utility (static helper)](utility.md) | `lib/ezi18n/classes/ezutf8codec.php:22` | – |
