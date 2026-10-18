---
title: Patterns in lib/eztemplate
audience: developer
category: standards
order: 539
summary: Every class, view, fetch function, operator and plugin point of lib/eztemplate with its design patterns.
keywords: lib/eztemplate, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/eztemplate

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/eztemplate` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (56)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZTemplate` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplate.php:200` | – |
| `eZTemplateArithmeticOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatearithmeticoperator.php:40` | – |
| `eZTemplateArrayOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatearrayoperator.php:28` | – |
| `eZTemplateAttributeOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateattributeoperator.php:56` | – |
| `eZTemplateBlockFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateblockfunction.php:50` | – |
| `eZTemplateCacheBlock` | class | [Utility (static helper)](utility.md) | `lib/eztemplate/classes/eztemplatecacheblock.php:18` | – |
| `eZTemplateCacheFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatecachefunction.php:19` | – |
| `eZTemplateCompiledLoop` | class | – | `lib/eztemplate/classes/eztemplatecompiledloop.php:17` | – |
| `eZTemplateCompiler` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – |
| `eZTemplateControlOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatecontroloperator.php:26` | – |
| `eZTemplateDebugFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatedebugfunction.php:63` | – |
| `eZTemplateDefFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatedeffunction.php:38` | – |
| `eZTemplateDelimitFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatedelimitfunction.php:37` | – |
| `eZTemplateDigestOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatedigestoperator.php:21` | – |
| `eZTemplateDoFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatedofunction.php:35` | – |
| `eZTemplateElementParser` | class | [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplateelementparser.php:18` | – |
| `eZTemplateExecuteOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateexecuteoperator.php:18` | – |
| `eZTemplateExpInfoOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateexpinfooperator.php:36` | – |
| `eZTemplateFailedExecutingCompiledTemplate` | class extends Exception | [Exception hierarchy](exception.md) | `lib/eztemplate/classes/exceptions/failedexecutingcompiledtemplate.php:17` | Exception occuring when the execution of a compiled template fails |
| `eZTemplateFileResource` | class | – | `lib/eztemplate/classes/eztemplatefileresource.php:20` | – |
| `eZTemplateForeachFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateforeachfunction.php:46` | – |
| `eZTemplateForFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateforfunction.php:39` | – |
| `eZTemplateFunctionElement` | class | – | `lib/eztemplate/classes/eztemplatefunctionelement.php:26` | – |
| `eZTemplateIfFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateiffunction.php:38` | – |
| `eZTemplateImageOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateimageoperator.php:24` | – |
| `eZTemplateIncludeFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateincludefunction.php:33` | – |
| `eZTemplateLocaleOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatelocaleoperator.php:30` | – |
| `eZTemplateLogicOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatelogicoperator.php:69` | – |
| `eZTemplateLoop` | class | – | `lib/eztemplate/classes/eztemplateloop.php:49` | – |
| `eZTemplateLoopSequence` | class | – | `lib/eztemplate/classes/eztemplateloop.php:13` | – |
| `eZTemplateMenuFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatemenufunction.php:18` | – |
| `eZTemplateMultiPassParser` | class extends eZTemplateParser | [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplatemultipassparser.php:18` | – |
| `eZTemplateNl2BrOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatenl2broperator.php:21` | – |
| `eZTemplateNodeTool` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – |
| `eZTemplateOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateoperator.php:20` | – |
| `eZTemplateOperatorElement` | class | – | `lib/eztemplate/classes/eztemplateoperatorelement.php:24` | – |
| `eZTemplateOptimizer` | class | [Utility (static helper)](utility.md) | `lib/eztemplate/classes/eztemplateoptimizer.php:18` | – |
| `eZTemplateParser` | class | – | `lib/eztemplate/classes/eztemplateparser.php:18` | – |
| `eZTemplatePHPOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatephpoperator.php:30` | – |
| `eZTemplateRoleOperator` | class | [Factory and static factory methods](factory.md), [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | The eZTemplateRoleOperator class provides template operators for checking user role membership |
| `eZTemplateRoot` | class | – | `lib/eztemplate/classes/eztemplateroot.php:26` | – |
| `eZTemplateSectionFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatesectionfunction.php:58` | – |
| `eZTemplateSectionIterator` | class | – | `lib/eztemplate/classes/eztemplatesectioniterator.php:28` | – |
| `eZTemplateSequenceFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatesequencefunction.php:34` | – |
| `eZTemplateSetFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatesetfunction.php:37` | – |
| `eZTemplateStringOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatestringoperator.php:22` | – |
| `eZTemplateStringsOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatestringsoperator.php:80` | – |
| `eZTemplateSwitchFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplateswitchfunction.php:43` | – |
| `eZTemplateTextElement` | class | – | `lib/eztemplate/classes/eztemplatetextelement.php:20` | – |
| `eZTemplateTextOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatetextoperator.php:18` | – |
| `eZTemplateToolbarFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatetoolbarfunction.php:18` | – |
| `eZTemplateTreeCache` | class | [Utility (static helper)](utility.md) | `lib/eztemplate/classes/eztemplatetreecache.php:18` | – |
| `eZTemplateTypeOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatetypeoperator.php:36` | – |
| `eZTemplateUnitOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateunitoperator.php:39` | – |
| `eZTemplateVariableElement` | class | – | `lib/eztemplate/classes/eztemplatevariableelement.php:20` | – |
| `eZTemplateWhileFunction` | class | [Template function (Interpreter plugin)](template-function.md) | `lib/eztemplate/classes/eztemplatewhilefunction.php:35` | – |

## Template operators (16)

| Operators | Class | Registered in |
|---|---|---|
| array, hash, array_prepend, prepend, array_append, append, array_merge, merge, contains, compare, extract, extract_left, extract_right, begins_with, ends_with, implode, explode, repeat, reverse, insert, remove, replace, unique, array_sum | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php:13` |
| fetch, fetch_alias | `eZTemplateExecuteOperator` | `lib/eztemplate/classes/eztemplateautoload.php:42` |
| l10n, locale, datetime, currentdate, maketime, makedate, gettime | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:44` |
| attribute, dump | `eZTemplateAttributeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:46` |
| nl2br | `eZTemplateNl2BrOperator` | `lib/eztemplate/classes/eztemplateautoload.php:48` |
| concat, indent | `eZTemplateTextOperator` | `lib/eztemplate/classes/eztemplateautoload.php:50` |
| si | `eZTemplateUnitOperator` | `lib/eztemplate/classes/eztemplateautoload.php:52` |
| lt, gt, le, ge, eq, ne, null, not, true, false, or, and, choose | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php:54` |
| is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_class, is_null, is_set, is_unset, get_type, get_class | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:59` |
| cond, first_set | `eZTemplateControlOperator` | `lib/eztemplate/classes/eztemplateautoload.php:64` |
| sum, sub, inc, dec, div, mod, mul, max, min, abs, ceil, floor, round, int, float, count, roman, rand | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php:67` |
| texttoimage, image, imagefile | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php:77` |
| upcase, downcase, count_words, count_chars, trim, break, wrap, upfirst, upword, simplify, trim, wash, chr, ord, shorten, pad | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php:83` |
| crc32, md5, rot13 | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php:101` |
| expinfo | `eZTemplateExpInfoOperator` | `lib/eztemplate/classes/eztemplateautoload.php:106` |
| – | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php:109` |

## Template functions (17)

| Functions | Class | Registered in |
|---|---|---|
| section | `eZTemplateSectionFunction` | `lib/eztemplate/classes/eztemplateautoload.php:181` |
| ldelim, rdelim | `eZTemplateDelimitFunction` | `lib/eztemplate/classes/eztemplateautoload.php:187` |
| include | `eZTemplateIncludeFunction` | `lib/eztemplate/classes/eztemplateautoload.php:189` |
| switch | `eZTemplateSwitchFunction` | `lib/eztemplate/classes/eztemplateautoload.php:191` |
| sequence | `eZTemplateSequenceFunction` | `lib/eztemplate/classes/eztemplateautoload.php:194` |
| set, let, default | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php:196` |
| set-block, append-block, run-once | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php:198` |
| debug-timing-point, debug-accumulator, debug-log, debug-trace | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php:201` |
| cache-block | `eZTemplateCacheFunction` | `lib/eztemplate/classes/eztemplateautoload.php:206` |
| tool_bar | `eZTemplateToolbarFunction` | `lib/eztemplate/classes/eztemplateautoload.php:209` |
| menu | `eZTemplateMenuFunction` | `lib/eztemplate/classes/eztemplateautoload.php:212` |
| if | `eZTemplateIfFunction` | `lib/eztemplate/classes/eztemplateautoload.php:216` |
| while | `eZTemplateWhileFunction` | `lib/eztemplate/classes/eztemplateautoload.php:220` |
| for | `eZTemplateForFunction` | `lib/eztemplate/classes/eztemplateautoload.php:226` |
| foreach | `eZTemplateForeachFunction` | `lib/eztemplate/classes/eztemplateautoload.php:233` |
| do | `eZTemplateDoFunction` | `lib/eztemplate/classes/eztemplateautoload.php:239` |
| def, undef | `eZTemplateDefFunction` | `lib/eztemplate/classes/eztemplateautoload.php:246` |
