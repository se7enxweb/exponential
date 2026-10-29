---
title: Template-Funktion (Interpreter-Plugin)
audience: developer
category: standards
order: 314
summary: Eine Klasse, die der Template-Sprache einen Block oder eine Funktion hinzufügt ({def}, {foreach}, {cache-block} ...). 45 Instanzen in Exponential.
keywords: Entwurfsmuster, Template-Funktion, Plugins, Musterkatalog, design pattern, template-function, pattern catalogue
type: catalogue
---
# Template-Funktion (Interpreter-Plugin)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Klasse, die der Template-Sprache einen Block oder eine Funktion hinzufügt ({def}, {foreach}, {cache-block} ...).

| | |
| --- | --- |
| Gruppe | Plugins |
| Instanzen | 45 |
| Erkannt an | Registriert in einer eztemplateautoload.php ($eZTemplateFunctionArray[]) oder hat functionList() und process(). |
| Erweiterungspunkt | autoloads/eztemplateautoload.php einer Extension. |

## Klassen nach Bereich

### extension/explayouts (1)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ExplBlockFunction` | class | `extension/explayouts/classes/explblockfunction.php:9` | Template-Funktion {explblock name=... | – |

### kernel/common (1)

[Alle Muster von kernel/common](bereich-kernel-common.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZObjectForwarder` | class | `kernel/common/ezobjectforwarder.php:18` | – | – |

### lib/eztemplate (17)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplateBlockFunction` | class | `lib/eztemplate/classes/eztemplateblockfunction.php:50` | – | – |
| `eZTemplateCacheFunction` | class | `lib/eztemplate/classes/eztemplatecachefunction.php:19` | – | – |
| `eZTemplateDebugFunction` | class | `lib/eztemplate/classes/eztemplatedebugfunction.php:63` | – | – |
| `eZTemplateDefFunction` | class | `lib/eztemplate/classes/eztemplatedeffunction.php:38` | – | – |
| `eZTemplateDelimitFunction` | class | `lib/eztemplate/classes/eztemplatedelimitfunction.php:37` | – | – |
| `eZTemplateDoFunction` | class | `lib/eztemplate/classes/eztemplatedofunction.php:35` | – | – |
| `eZTemplateForeachFunction` | class | `lib/eztemplate/classes/eztemplateforeachfunction.php:46` | – | – |
| `eZTemplateForFunction` | class | `lib/eztemplate/classes/eztemplateforfunction.php:39` | – | – |
| `eZTemplateIfFunction` | class | `lib/eztemplate/classes/eztemplateiffunction.php:38` | – | – |
| `eZTemplateIncludeFunction` | class | `lib/eztemplate/classes/eztemplateincludefunction.php:33` | – | – |
| `eZTemplateMenuFunction` | class | `lib/eztemplate/classes/eztemplatemenufunction.php:18` | – | – |
| `eZTemplateSectionFunction` | class | `lib/eztemplate/classes/eztemplatesectionfunction.php:58` | – | – |
| `eZTemplateSequenceFunction` | class | `lib/eztemplate/classes/eztemplatesequencefunction.php:34` | – | – |
| `eZTemplateSetFunction` | class | `lib/eztemplate/classes/eztemplatesetfunction.php:37` | – | – |
| `eZTemplateSwitchFunction` | class | `lib/eztemplate/classes/eztemplateswitchfunction.php:43` | – | – |
| `eZTemplateToolbarFunction` | class | `lib/eztemplate/classes/eztemplatetoolbarfunction.php:18` | – | – |
| `eZTemplateWhileFunction` | class | `lib/eztemplate/classes/eztemplatewhilefunction.php:35` | – | – |

## Template-Funktionen (17)

### lib/eztemplate (17)

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
