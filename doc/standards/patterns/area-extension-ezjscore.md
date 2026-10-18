---
title: Patterns in extension/ezjscore
audience: developer
category: standards
order: 443
summary: Every class, view, fetch function, operator and plugin point of extension/ezjscore with its design patterns.
keywords: extension/ezjscore, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezjscore

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezjscore` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (21)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezjscore\Ezjscore\Call` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezjscore/classes/runnable/views/ezjscore/call.php:89` | The code of extension/ezjscore/modules/ezjscore/call.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Hello` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezjscore/classes/runnable/views/ezjscore/hello.php:43` | The code of extension/ezjscore/modules/ezjscore/hello.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Run` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:79` | The code of extension/ezjscore/modules/ezjscore/run.php, moved into a class (#207 stage 1). |
| `ezjscAccessTemplateFunctions` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php:40` | Custom has access to call that also lets you check that user has access to provided limitation(s) |
| `ezjscAjaxContent` | class | – | `extension/ezjscore/classes/ezjscajaxcontent.php:36` | – |
| `ezjscCacheManager` | class | [Facade / service](facade.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – |
| `ezjscCssOptimizer` | class | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezjscore/classes/ezjsccssoptimizer.php:32` | – |
| `ezjscEncodingTemplateFunctions` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php:44` | ezjscAjaxContent related template operators |
| `ezjscJavascriptOptimizer` | class | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezjscore/classes/ezjscjavascriptoptimizer.php:32` | – |
| `ezjscoreInfo` | class | [Value object / struct](value-object.md) | `extension/ezjscore/ezinfo.php:7` | – |
| `ezjscPacker` | class | [Factory and static factory methods](factory.md) | `extension/ezjscore/classes/ezjscpacker.php:53` | – |
| `ezjscPackerTemplateFunctions` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php:86` | ezjsPacker related template operators For merging and packing javascript or stylesheet files together to reduce size and number of files(as in reduce… |
| `ezjscServerFunctions` | class | – | `extension/ezjscore/classes/ezjscserverfunctions.php:36` | – |
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | This class handles AJAX calls for the ajax uploader component |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | ezjscServerFunctionsNode class definition that provide node fetch functions |
| `ezjscServerFunctionsPublishingQueue` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php:18` | This class handles AJAX calls for the publishing queue system |
| `ezjscServerRouter` | class | [Chain of Responsibility](chain.md), [Singleton / instance()](singleton.md) | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – |
| `ezpAjaxUploaderHandlerInterface` | interface | [Interface (contract)](interface.md) | `extension/ezjscore/classes/ajaxuploader/ezpajaxuploaderhandlerinterface.php:19` | Interface of the AJAX uploader handler |
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelation attributes |
| `ezpRelationListAjaxUploader` | class implements ezpAjaxUploaderHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelationList attributes |

## Module views (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezjscore/hello` | `hello.php` | – | `extension/ezjscore/modules/ezjscore/module.php:35` |
| `/ezjscore/call` | `call.php` | call | `extension/ezjscore/modules/ezjscore/module.php:40` |
| `/ezjscore/run` | `run.php` | run | `extension/ezjscore/modules/ezjscore/module.php:46` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `ezjscore/run` | – | `extension/ezjscore/modules/ezjscore/module.php:70` |
| `ezjscore/call` | FunctionList | `extension/ezjscore/modules/ezjscore/module.php:71` |

## Template operators (3)

| Operators | Class | Registered in |
|---|---|---|
| ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:18` |
| json_encode, xml_encode, node_encode | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:29` |
| has_access_to_limitation | `ezjscAccessTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:36` |

## Event hooks (1)

| Event | Kind | Called in |
|---|---|---|
| `response/output` | filter | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:149` |

## INI plugin points (11)

| Setting | Class | Set in |
|---|---|---|
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/ezjscore/settings/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/ezjscore/settings/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/ezjscore/settings/ezjscore.ini:116` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/ezjscore/settings/ezjscore.ini:122` |
| `[ezjscServer_expsubitems] Class` | `expSubitemsServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:149` |
| `[ezjscServer_expdebugbar] Class` | `expDebugBarServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:167` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/ezjscore/settings/ezjscore.ini:178` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:181` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:190` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:191` |
| `[Cache_ezjscore] class` | `ezjscCacheManager` | `extension/ezjscore/settings/site.ini.append.php:41` |
