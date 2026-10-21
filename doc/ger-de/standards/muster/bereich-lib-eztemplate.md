---
title: Muster in lib/eztemplate
audience: developer
category: standards
order: 539
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/eztemplate mit den Entwurfsmustern.
keywords: lib/eztemplate, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/eztemplate

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/eztemplate` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (56)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZTemplate` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplate.php:200` | – |
| `eZTemplateArithmeticOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatearithmeticoperator.php:40` | – |
| `eZTemplateArrayOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatearrayoperator.php:28` | – |
| `eZTemplateAttributeOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateattributeoperator.php:56` | – |
| `eZTemplateBlockFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateblockfunction.php:50` | – |
| `eZTemplateCacheBlock` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/eztemplate/classes/eztemplatecacheblock.php:18` | – |
| `eZTemplateCacheFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatecachefunction.php:19` | – |
| `eZTemplateCompiledLoop` | class | – | `lib/eztemplate/classes/eztemplatecompiledloop.php:17` | – |
| `eZTemplateCompiler` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – |
| `eZTemplateControlOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatecontroloperator.php:26` | – |
| `eZTemplateDebugFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatedebugfunction.php:63` | – |
| `eZTemplateDefFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatedeffunction.php:38` | – |
| `eZTemplateDelimitFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatedelimitfunction.php:37` | – |
| `eZTemplateDigestOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatedigestoperator.php:21` | – |
| `eZTemplateDoFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatedofunction.php:35` | – |
| `eZTemplateElementParser` | class | [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplateelementparser.php:18` | – |
| `eZTemplateExecuteOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateexecuteoperator.php:18` | – |
| `eZTemplateExpInfoOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateexpinfooperator.php:36` | – |
| `eZTemplateFailedExecutingCompiledTemplate` | class extends Exception | [Ausnahmehierarchie](ausnahme.md) | `lib/eztemplate/classes/exceptions/failedexecutingcompiledtemplate.php:17` | Ausnahme, die auftritt, wenn die Ausführung eines kompilierten Templates fehlschlägt |
| `eZTemplateFileResource` | class | – | `lib/eztemplate/classes/eztemplatefileresource.php:20` | – |
| `eZTemplateForeachFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateforeachfunction.php:46` | – |
| `eZTemplateForFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateforfunction.php:39` | – |
| `eZTemplateFunctionElement` | class | – | `lib/eztemplate/classes/eztemplatefunctionelement.php:26` | – |
| `eZTemplateIfFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateiffunction.php:38` | – |
| `eZTemplateImageOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateimageoperator.php:24` | – |
| `eZTemplateIncludeFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateincludefunction.php:33` | – |
| `eZTemplateLocaleOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatelocaleoperator.php:30` | – |
| `eZTemplateLogicOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatelogicoperator.php:69` | – |
| `eZTemplateLoop` | class | – | `lib/eztemplate/classes/eztemplateloop.php:49` | – |
| `eZTemplateLoopSequence` | class | – | `lib/eztemplate/classes/eztemplateloop.php:13` | – |
| `eZTemplateMenuFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatemenufunction.php:18` | – |
| `eZTemplateMultiPassParser` | class extends eZTemplateParser | [Singleton / instance()](singleton.md) | `lib/eztemplate/classes/eztemplatemultipassparser.php:18` | – |
| `eZTemplateNl2BrOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatenl2broperator.php:21` | – |
| `eZTemplateNodeTool` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – |
| `eZTemplateOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateoperator.php:20` | – |
| `eZTemplateOperatorElement` | class | – | `lib/eztemplate/classes/eztemplateoperatorelement.php:24` | – |
| `eZTemplateOptimizer` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/eztemplate/classes/eztemplateoptimizer.php:18` | – |
| `eZTemplateParser` | class | – | `lib/eztemplate/classes/eztemplateparser.php:18` | – |
| `eZTemplatePHPOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatephpoperator.php:30` | – |
| `eZTemplateRoleOperator` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | Die Klasse eZTemplateRoleOperator stellt Template-Operatoren zur Prüfung der Rollenzugehörigkeit von Benutzern bereit |
| `eZTemplateRoot` | class | – | `lib/eztemplate/classes/eztemplateroot.php:26` | – |
| `eZTemplateSectionFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatesectionfunction.php:58` | – |
| `eZTemplateSectionIterator` | class | – | `lib/eztemplate/classes/eztemplatesectioniterator.php:28` | – |
| `eZTemplateSequenceFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatesequencefunction.php:34` | – |
| `eZTemplateSetFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatesetfunction.php:37` | – |
| `eZTemplateStringOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatestringoperator.php:22` | – |
| `eZTemplateStringsOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatestringsoperator.php:80` | – |
| `eZTemplateSwitchFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplateswitchfunction.php:43` | – |
| `eZTemplateTextElement` | class | – | `lib/eztemplate/classes/eztemplatetextelement.php:20` | – |
| `eZTemplateTextOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatetextoperator.php:18` | – |
| `eZTemplateToolbarFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatetoolbarfunction.php:18` | – |
| `eZTemplateTreeCache` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/eztemplate/classes/eztemplatetreecache.php:18` | – |
| `eZTemplateTypeOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplatetypeoperator.php:36` | – |
| `eZTemplateUnitOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `lib/eztemplate/classes/eztemplateunitoperator.php:39` | – |
| `eZTemplateVariableElement` | class | – | `lib/eztemplate/classes/eztemplatevariableelement.php:20` | – |
| `eZTemplateWhileFunction` | class | [Template-Funktion (Interpreter-Plugin)](template-funktion.md) | `lib/eztemplate/classes/eztemplatewhilefunction.php:35` | – |

## Template-Operatoren (17)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
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
| has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user | `eZTemplateRoleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:110` |
| – | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php:115` |

## Template-Funktionen (17)

| Funktionen | Klasse | Registriert in |
| --- | --- | --- |
| section | `eZTemplateSectionFunction` | `lib/eztemplate/classes/eztemplateautoload.php:187` |
| ldelim, rdelim | `eZTemplateDelimitFunction` | `lib/eztemplate/classes/eztemplateautoload.php:193` |
| include | `eZTemplateIncludeFunction` | `lib/eztemplate/classes/eztemplateautoload.php:195` |
| switch | `eZTemplateSwitchFunction` | `lib/eztemplate/classes/eztemplateautoload.php:197` |
| sequence | `eZTemplateSequenceFunction` | `lib/eztemplate/classes/eztemplateautoload.php:200` |
| set, let, default | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php:202` |
| set-block, append-block, run-once | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php:204` |
| debug-timing-point, debug-accumulator, debug-log, debug-trace | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php:207` |
| cache-block | `eZTemplateCacheFunction` | `lib/eztemplate/classes/eztemplateautoload.php:212` |
| tool_bar | `eZTemplateToolbarFunction` | `lib/eztemplate/classes/eztemplateautoload.php:215` |
| menu | `eZTemplateMenuFunction` | `lib/eztemplate/classes/eztemplateautoload.php:218` |
| if | `eZTemplateIfFunction` | `lib/eztemplate/classes/eztemplateautoload.php:222` |
| while | `eZTemplateWhileFunction` | `lib/eztemplate/classes/eztemplateautoload.php:226` |
| for | `eZTemplateForFunction` | `lib/eztemplate/classes/eztemplateautoload.php:232` |
| foreach | `eZTemplateForeachFunction` | `lib/eztemplate/classes/eztemplateautoload.php:239` |
| do | `eZTemplateDoFunction` | `lib/eztemplate/classes/eztemplateautoload.php:245` |
| def, undef | `eZTemplateDefFunction` | `lib/eztemplate/classes/eztemplateautoload.php:252` |
