---
title: Muster in extension/ezjscore
audience: developer
category: standards
order: 443
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezjscore mit den Entwurfsmustern.
keywords: extension/ezjscore, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezjscore

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezjscore` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (21)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Call` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezjscore/classes/runnable/views/ezjscore/call.php:89` | Der Code von extension/ezjscore/modules/ezjscore/call.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Hello` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezjscore/classes/runnable/views/ezjscore/hello.php:43` | Der Code von extension/ezjscore/modules/ezjscore/hello.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Run` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:79` | Der Code von extension/ezjscore/modules/ezjscore/run.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezjscAccessTemplateFunctions` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php:40` | Eigener Has-Access-Aufruf, mit dem Sie auch prüfen können, ob der Benutzer Zugriff auf die angegebene(n) Einschränkung(en) hat |
| `ezjscAjaxContent` | class | – | `extension/ezjscore/classes/ezjscajaxcontent.php:36` | – |
| `ezjscCacheManager` | class | [Fassade / Dienst](fassade.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – |
| `ezjscCssOptimizer` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezjscore/classes/ezjsccssoptimizer.php:32` | – |
| `ezjscEncodingTemplateFunctions` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php:44` | Template-Operatoren rund um ezjscAjaxContent |
| `ezjscJavascriptOptimizer` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezjscore/classes/ezjscjavascriptoptimizer.php:32` | – |
| `ezjscoreInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezjscore/ezinfo.php:7` | – |
| `ezjscPacker` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezjscore/classes/ezjscpacker.php:53` | – |
| `ezjscPackerTemplateFunctions` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php:86` | Template-Operatoren rund um ezjsPacker zum Zusammenführen und Packen von JavaScript- oder Stylesheet-Dateien, um Größe und Anzahl der Dateien zu verringern (wie in reduce… |
| `ezjscServerFunctions` | class | – | `extension/ezjscore/classes/ezjscserverfunctions.php:36` | – |
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | Diese Klasse verarbeitet AJAX-Aufrufe für die Ajax-Uploader-Komponente |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | Die Klasse ezjscServerFunctionsNode definiert Fetch-Funktionen für Knoten |
| `ezjscServerFunctionsPublishingQueue` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php:18` | Diese Klasse verarbeitet AJAX-Aufrufe für das System der Veröffentlichungswarteschlange |
| `ezjscServerRouter` | class | [Zuständigkeitskette (Chain of Responsibility)](kette.md), [Singleton / instance()](singleton.md) | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – |
| `ezpAjaxUploaderHandlerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezjscore/classes/ajaxuploader/ezpajaxuploaderhandlerinterface.php:19` | Schnittstelle des AJAX-Uploader-Handlers |
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelation-Attribute |
| `ezpRelationListAjaxUploader` | class implements ezpAjaxUploaderHandlerInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | Diese Klasse verarbeitet AJAX-Uploads für eZObjectRelationList-Attribute |

## Modulansichten (3)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezjscore/hello` | `hello.php` | – | `extension/ezjscore/modules/ezjscore/module.php:35` |
| `/ezjscore/call` | `call.php` | call | `extension/ezjscore/modules/ezjscore/module.php:40` |
| `/ezjscore/run` | `run.php` | run | `extension/ezjscore/modules/ezjscore/module.php:46` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezjscore/run` | – | `extension/ezjscore/modules/ezjscore/module.php:70` |
| `ezjscore/call` | FunctionList | `extension/ezjscore/modules/ezjscore/module.php:71` |

## Template-Operatoren (3)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:18` |
| json_encode, xml_encode, node_encode | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:29` |
| has_access_to_limitation | `ezjscAccessTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:36` |

## Ereignis-Hooks (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `response/output` | filter | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:149` |

## INI-Plugin-Punkte (11)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
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
