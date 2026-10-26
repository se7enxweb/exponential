---
title: Patterns in extension/ezoe
audience: developer
category: standards
order: 451
summary: Every class, view, fetch function, operator and plugin point of extension/ezoe with its design patterns.
keywords: extension/ezoe, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezoe

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezoe` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (26)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `EnchantSpell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/EnchantSpell.php:11` | This class was contributed by Michel Weimerskirch. |
| `expOEAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezoe/classes/expoeauditbranch.php:11` | The audit taxonomy branch of the online editor: the choice of the editor engine. |
| `expOEEditor` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `extension/ezoe/classes/expoeeditor.php:18` | The editor engine registry and the helpers every caller shares: the templates, the ezoe module, the ezjscore functions and the expservices class all… |
| `expOEEditorEngine` | interface | [INI plugin point (extension point)](ini-plugin-point.md), [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `extension/ezoe/classes/expoeeditorengine.php:21` | An editor engine of the online editor: the thing that turns an ezxmltext attribute into an editor in the browser. |
| `expOEEditorEngineBase` | abstract class implements expOEEditorEngine | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Convenience base of editor engines: reads the engine's own ezoe.ini block [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezoe/classes/expoetinymce3engine.php:10` | The default engine: eZ Online Editor on TinyMCE 3.5, rendered by ezxmltext_ezoe.tpl itself. |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezoe/classes/expoetinymce8engine.php:10` | The opt-in engine: eZ Online Editor on TinyMCE 8 (GPL), rendered by ezxmltext_ezoe_tinymce8.tpl. |
| `expOEUrlException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `extension/ezoe/classes/expoeurlexception.php:10` | A reason the online editor refuses to fetch a file from a URL; the message is meant for the user. |
| `expOEUrlFetcher` | class | [Utility (static helper)](utility.md) | `extension/ezoe/classes/expoeurlfetcher.php:23` | Fetches a file from a URL for the upload tab of the online editor ("From a URL"). |
| `Exponential\View\Extension\Ezoe\Ezoe\Dialog` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/dialog.php:10` | The code of extension/ezoe/modules/ezoe/dialog.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\EmbedView` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/embed_view.php:10` | The code of extension/ezoe/modules/ezoe/embed_view.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Load` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/load.php:10` | The code of extension/ezoe/modules/ezoe/load.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Relations` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/relations.php:10` | The code of extension/ezoe/modules/ezoe/relations.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\SpellcheckRpc` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php:58` | Returns an request value by name without magic quoting. |
| `Exponential\View\Extension\Ezoe\Ezoe\Tags` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/tags.php:11` | The code of extension/ezoe/modules/ezoe/tags.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezoe\Ezoe\Upload` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezoe/classes/runnable/views/ezoe/upload.php:10` | The code of extension/ezoe/modules/ezoe/upload.php, moved into a class (#207 stage 1). |
| `eZOEInputParser` | class extends eZXMLInputParser | – | `extension/ezoe/ezxmltext/handlers/input/ezoeinputparser.php:41` | – |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – |
| `eZOETemplateUtils` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezoe/autoloads/ezoetemplateutils.php:37` | – |
| `eZOEXMLInput` | class extends eZXMLInputHandler | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php:40` | – |
| `GoogleSpell` | class extends SpellChecker | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php:9` | – |
| `Moxiecode_JSON` | class | – | `extension/ezoe/modules/ezoe/classes/utils/mcejson.php:377` | This class handles JSON stuff. |
| `Moxiecode_JSONReader` | class | – | `extension/ezoe/modules/ezoe/classes/utils/mcejson.php:10` | – |
| `PSpell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/PSpell.php:9` | – |
| `PSpellShell` | class extends SpellChecker | – | `extension/ezoe/modules/ezoe/classes/PSpellShell.php:9` | – |
| `SpellChecker` | class | – | `extension/ezoe/modules/ezoe/classes/SpellChecker.php:9` | – |

## Module views (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezoe/relations` | `relations.php` | editor | `extension/ezoe/modules/ezoe/module.php:34` |
| `/ezoe/upload` | `upload.php` | editor | `extension/ezoe/modules/ezoe/module.php:41` |
| `/ezoe/tags` | `tags.php` | editor | `extension/ezoe/modules/ezoe/module.php:48` |
| `/ezoe/dialog` | `dialog.php` | editor | `extension/ezoe/modules/ezoe/module.php:55` |
| `/ezoe/embed_view` | `embed_view.php` | editor | `extension/ezoe/modules/ezoe/module.php:62` |
| `/ezoe/load` | `load.php` | editor | `extension/ezoe/modules/ezoe/module.php:68` |
| `/ezoe/spellcheck_rpc` | `spellcheck_rpc.php` | editor | `extension/ezoe/modules/ezoe/module.php:74` |
| `/ezoe/engine` | `engine.php` | editor | `extension/ezoe/modules/ezoe/module.php:146` |

## Policy functions (7)

| Function | Limitations | Defined in |
|---|---|---|
| `ezoe/relations` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:123` |
| `ezoe/editor` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:129` |
| `ezoe/relations` | – | `extension/ezoe/modules/ezoe/module.php:137` |
| `ezoe/editor` | – | `extension/ezoe/modules/ezoe/module.php:138` |
| `ezoe/search` | – | `extension/ezoe/modules/ezoe/module.php:139` |
| `ezoe/browse` | – | `extension/ezoe/modules/ezoe/module.php:140` |
| `ezoe/disable_editor` | – | `extension/ezoe/modules/ezoe/module.php:141` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| ezoe_ini_section | `eZOETemplateUtils` | `extension/ezoe/autoloads/eztemplateautoload.php:13` |

## INI plugin points (7)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[ezoe]` | `expOEAuditBranch` | `extension/ezoe/settings/audit.ini.append.php:6` |
| `[ezjscServer_ezoe] Class` | `ezoeServerFunctions` | `extension/ezoe/settings/ezjscore.ini.append.php:6` |
| `[EditorSettings] Engines[tinymce3]` | `expOETinyMCE3Engine` | `extension/ezoe/settings/ezoe.ini:30` |
| `[EditorSettings] Engines[tinymce8]` | `expOETinyMCE8Engine` | `extension/ezoe/settings/ezoe.ini:31` |
| `[SpellChecker] config[general.engine]` | `GoogleSpell` | `extension/ezoe/settings/ezoe.ini:336` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/settings/ezxml.ini.append.php:11` |
| `[Registry_ezoeengines] Entries[Engines]` | `expOEEditorEngine` | `extension/ezoe/settings/rad.ini.append.php:17` |
