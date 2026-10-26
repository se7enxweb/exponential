---
title: Patterns in extension/ezurlmultisite
audience: developer
category: standards
order: 463
summary: Every class, view, fetch function, operator and plugin point of extension/ezurlmultisite with its design patterns.
keywords: extension/ezurlmultisite, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezurlmultisite

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezurlmultisite` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZURLMultisite` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Cross-siteaccess link resolution for the ezurl template operator. |
| `eZURLMultisiteBinaryFileHandler` | class implements eZURLMultisiteDataTypeHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – |
| `eZURLMultisiteDataTypeHandler` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – |
| `ezurlmultisiteInfo` | class | [Value object / struct](value-object.md) | `extension/ezurlmultisite/ezinfo.php:10` | – |
| `eZURLMultisiteObjectRelationHandler` | class implements eZURLMultisiteDataTypeHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – |
| `eZURLMultisiteOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezurlmultisite/classes/ezurlmultisiteoperator.php:3` | – |
| `eZURLMultisiteURLHandler` | class implements eZURLMultisiteDataTypeHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – |
| `eZURLMultisiteURLOperator` | class extends eZURLOperator | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php:24` | The kernel's eZURLOperator with a multisite-aware ezurl. |

## Template operators (2)

| Operators | Class | Registered in |
|---|---|---|
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLMultisiteURLOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:14` |
| ezurl_path, ezurl_siteaccess, ezurl_roots | `eZURLMultisiteOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:21` |

## INI plugin points (4)

| Setting | Class | Set in |
|---|---|---|
| `[DataTypeSettings] HandlerList[ezurl]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:79` |
| `[DataTypeSettings] HandlerList[ezstring]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:80` |
| `[DataTypeSettings] HandlerList[ezbinaryfile]` | `eZURLMultisiteBinaryFileHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:81` |
| `[DataTypeSettings] HandlerList[ezobjectrelation]` | `eZURLMultisiteObjectRelationHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:82` |
