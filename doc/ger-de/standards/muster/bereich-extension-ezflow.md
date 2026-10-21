---
title: Muster in extension/ezflow
audience: developer
category: standards
order: 439
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezflow mit den Entwurfsmustern.
keywords: extension/ezflow, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezflow

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezflow` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (28)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZFeedReader` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezflow/autoloads/ezfeedreader.php:32` | Die Klasse eZFeedReader implementiert die Methoden des Template-Operators feedreader |
| `eZFlowAjaxContent` | class | [Singleton / instance()](singleton.md) | `extension/ezflow/classes/ezflowajaxcontent.php:35` | – |
| `eZFlowBlock` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezflow/classes/ezflowblock.php:28` | – |
| `eZFlowFetchInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezflow/classes/ezflowfetchinterface.php:28` | – |
| `eZFlowFetchParameters` | class | – | `extension/ezflow/classes/ezflowfetchparameters.php:28` | – |
| `eZFlowFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `extension/ezflow/modules/ezflow/functioncollection.php:30` | – |
| `ezflowInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezflow/ezinfo.php:12` | – |
| `eZFlowKeywordsFetch` | class implements eZFlowFetchInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php:28` | – |
| `eZFlowLatestContent` | class implements eZFlowFetchInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezflow/classes/fetches/ezflowlatestcontent.php:28` | – |
| `eZFlowLatestObjects` | class implements eZFlowFetchInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezflow/classes/fetches/ezflowlatestobjects.php:28` | – |
| `eZFlowMCFetch` | class implements eZFlowFetchInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezflow/classes/fetches/ezflowmcfetch.php:3` | – |
| `eZFlowOperations` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezflow/classes/ezflowoperations.php:28` | – |
| `eZFlowPool` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezflow/classes/ezflowpool.php:28` | – |
| `eZFlowPoolItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezflow/classes/ezflowpoolitem.php:28` | – |
| `eZFlowServerCallFunctions` | class | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implementiert Methoden, die per XHR-Aufruf aus der Ferne aufgerufen werden |
| `eZHTTPCacheManager` | class | [Fassade / Dienst](fassade.md) | `extension/ezflow/classes/ezhttpcachemanager.php:33` | Die Klasse eZHTTPCacheManager implementiert Methoden, die zum Laden benutzerdefinierter HTTP-Cache-Handler nötig sind |
| `eZHTTPCacheManagerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/ezflow/classes/ezhttpcachemanagerinterface.php:32` | Schnittstellendeklaration |
| `eZJSON` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezflow/autoloads/ezjson.php:32` | Die Klasse eZJSON implementiert die Methoden des Template-Operators json |
| `eZPage` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezflow/classes/ezpage.php:28` | – |
| `eZPageBlock` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezflow/classes/ezpageblock.php:28` | – |
| `eZPageBlockItem` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezflow/classes/ezpageblockitem.php:28` | – |
| `eZPageLink` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezflow/autoloads/ezpagelink.php:32` | Die Klasse eZPageLink implementiert die Methoden des Template-Operators pagelink |
| `eZPageSwapType` | class extends eZWorkflowEventType | [Workflow-Ereignistyp (Befehl, Plugin)](workflow-ereignis.md) | `extension/ezflow/eventtypes/event/ezpageswap/ezpageswaptype.php:28` | – |
| `eZPageType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/ezflow/datatypes/ezpage/ezpagetype.php:28` | – |
| `eZPageZone` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezflow/classes/ezpagezone.php:28` | – |
| `eZRed5StreamListOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezflow/autoloads/ezred5streamlist.php:32` | Die Klasse eZRed5StreamListOperator implementiert die Methoden des Template-Operators red5list |
| `eZSquidCacheManager` | class implements eZHTTPCacheManagerInterface | [Fassade / Dienst](fassade.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – |
| `eZUnserialize` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezflow/autoloads/ezunserialize.php:32` | Die Klasse eZUnserialize implementiert die Methoden des Template-Operators unserialize |

## Modulansichten (8)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezflow/get` | `get.php` | edit | `extension/ezflow/modules/ezflow/module.php:32` |
| `/ezflow/timeline` | `timeline.php` | timeline | `extension/ezflow/modules/ezflow/module.php:35` |
| `/ezflow/preview` | `preview.php` | timeline | `extension/ezflow/modules/ezflow/module.php:39` |
| `/ezflow/zone` | `zone.php` | edit | `extension/ezflow/modules/ezflow/module.php:43` |
| `/ezflow/request` | `request.php` | edit | `extension/ezflow/modules/ezflow/module.php:47` |
| `/ezflow/push` | `push.php` | edit | `extension/ezflow/modules/ezflow/module.php:52` |
| `/ezflow/block` | `block.php` | call | `extension/ezflow/modules/ezflow/module.php:58` |
| `/flash/embed` | `embed.php` | – | `extension/ezflow/modules/flash/module.php:31` |

## Richtlinienfunktionen (4)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezflow/timeline` | – | `extension/ezflow/modules/ezflow/module.php:63` |
| `ezflow/edit` | – | `extension/ezflow/modules/ezflow/module.php:64` |
| `ezflow/call` | – | `extension/ezflow/modules/ezflow/module.php:65` |
| `ezflow/changelayout` | – | `extension/ezflow/modules/ezflow/module.php:66` |

## Fetch-Funktionen (6)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'ezflow', 'waiting' )` | `eZFlowFunctionCollection::fetchWaiting()` | `extension/ezflow/modules/ezflow/function_definition.php:30` |
| `fetch( 'ezflow', 'valid' )` | `eZFlowFunctionCollection::fetchValid()` | `extension/ezflow/modules/ezflow/function_definition.php:42` |
| `fetch( 'ezflow', 'archived' )` | `eZFlowFunctionCollection::fetchArchived()` | `extension/ezflow/modules/ezflow/function_definition.php:54` |
| `fetch( 'ezflow', 'valid_nodes' )` | `eZFlowFunctionCollection::fetchValidNodes()` | `extension/ezflow/modules/ezflow/function_definition.php:66` |
| `fetch( 'ezflow', 'block' )` | `eZFlowFunctionCollection::fetchBlock()` | `extension/ezflow/modules/ezflow/function_definition.php:78` |
| `fetch( 'ezflow', 'allowed_zones' )` | `eZFlowFunctionCollection::fetchAllowedZones()` | `extension/ezflow/modules/ezflow/function_definition.php:90` |

## Template-Operatoren (5)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| unserialize | `eZUnserialize` | `extension/ezflow/autoloads/eztemplateautoload.php:29` |
| json | `eZJSON` | `extension/ezflow/autoloads/eztemplateautoload.php:32` |
| feedreader | `eZFeedReader` | `extension/ezflow/autoloads/eztemplateautoload.php:35` |
| red5list | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/eztemplateautoload.php:38` |
| pagelink | `eZPageLink` | `extension/ezflow/autoloads/eztemplateautoload.php:41` |

## Cronjob-Teile (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezflowcleanup` | `extension/ezflow/cronjobs/ezflowcleanup.php` | – |
| `ezflowupdate` | `extension/ezflow/cronjobs/ezflowupdate.php` | – |

## INI-Plugin-Punkte (6)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[Dynamic3Items] FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/settings/block.ini:151` |
| `[Keyword] FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/settings/block.ini:246` |
| `[MultimediaCarousel] FetchClass` | `eZFlowMCFetch` | `extension/ezflow/settings/block.ini:275` |
| `[LatestContent] FetchClass` | `eZFlowLatestContent` | `extension/ezflow/settings/block.ini:302` |
| `[ezjscServer_ezflow] Class` | `eZFlowServerCallFunctions` | `extension/ezflow/settings/ezjscore.ini.append.php:4` |
| `[HTTPCacheHandlers] Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/settings/http.ini:5` |
