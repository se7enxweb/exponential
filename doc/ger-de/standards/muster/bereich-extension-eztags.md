---
title: Muster in extension/eztags
audience: developer
category: standards
order: 461
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/eztags mit den Entwurfsmustern.
keywords: extension/eztags, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/eztags

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/eztags` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (32)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Eztags\Convertezkeyword` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/eztags/classes/runnable/commands/php_convertezkeyword.php:10` | Der Code von extension/eztags/bin/php/convertezkeyword.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Add` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/add.php:15` | Der Code von extension/eztags/modules/tags/add.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Addsynonym` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/addsynonym.php:15` | Der Code von extension/eztags/modules/tags/addsynonym.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/dashboard.php:10` | Der Code von extension/eztags/modules/tags/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Delete` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/delete.php:15` | Der Code von extension/eztags/modules/tags/delete.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Deletesynonym` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:15` | Der Code von extension/eztags/modules/tags/deletesynonym.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Deletetags` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/deletetags.php:15` | Der Code von extension/eztags/modules/tags/deletetags.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Edit` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/edit.php:15` | Der Code von extension/eztags/modules/tags/edit.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Editsynonym` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/editsynonym.php:15` | Der Code von extension/eztags/modules/tags/editsynonym.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Id` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/id.php:15` | Der Code von extension/eztags/modules/tags/id.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\ListObjects` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/list_objects.php:15` | Der Code von extension/eztags/modules/tags/list_objects.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Makesynonym` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/makesynonym.php:15` | Der Code von extension/eztags/modules/tags/makesynonym.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Merge` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/merge.php:15` | Der Code von extension/eztags/modules/tags/merge.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Movetags` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/movetags.php:15` | Der Code von extension/eztags/modules/tags/movetags.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Search` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/search.php:10` | Der Code von extension/eztags/modules/tags/search.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Translation` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/translation.php:15` | Der Code von extension/eztags/modules/tags/translation.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\Treemenu` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/treemenu.php:15` | Der Code von extension/eztags/modules/tags/treemenu.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Eztags\Tags\View` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/eztags/classes/runnable/views/tags/view.php:15` | Der Code von extension/eztags/modules/tags/view.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezfSolrDocumentFieldeZTags` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php:7` | Die Klasse ezfSolrDocumentFieldeZTags implementiert einen eigenen Indexierungs-Handler für eZ Find |
| `ezjscTags` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/eztags/classes/ezjsctags.php:6` | Die Klasse ezjscTags implementiert eZ-JS-Core-Serverfunktionen für eztags |
| `ezjscTagsChildren` | class extends ezjscServerFunctions | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/eztags/classes/ezjsctagschildren.php:6` | Die Klasse ezjscTagsChildren implementiert eZ-JS-Core-Serverfunktionen für die Liste der Kinder von eztags |
| `eZTags` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/eztags/datatypes/eztags/eztags.php:6` | Die Klasse eZTags implementiert Funktionen, die der Datentyp eztags verwendet |
| `eZTagsAttributeFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/eztags/classes/eztagsattributefilter.php:6` | Die Klasse eZTagsAttributeFilter implementiert das erweiterte Attribut TagsAttributeFilter |
| `eZTagsAttributeLinkObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagsattributelinkobject.php:7` | Die Klasse eZTagsAttributeLinkObject erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags_attribute_link zugreifen zu können |
| `eZTagsCloud` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/eztags/autoloads/eztagscloud.php:6` | Die Klasse eZTagsCloud implementiert die Methode des Template-Operators eztagscloud |
| `eZTagsFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/eztags/modules/tags/eztagsfunctioncollection.php:9` | Die Klasse eZTagsFunctionCollection implementiert Fetch-Funktionen für eztags |
| `eztagsInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/eztags/ezinfo.php:11` | – |
| `eZTagsKeyword` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagskeyword.php:7` | Die Klasse eZTagsKeyword erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags_keyword zugreifen zu können |
| `eZTagsObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagsobject.php:9` | Die Klasse eZTagsObject erbt von der Klasse eZPersistentObject, um über die API auf die Datenbanktabelle eztags zugreifen zu können |
| `eZTagsTemplateFunctions` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/eztags/autoloads/eztagstemplatefunctions.php:6` | Die Klasse eZTagsTemplateFunctions implementiert die Methoden des Template-Operators eztags |
| `eZTagsTreeAttributeFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/eztags/classes/eztagstreeattributefilter.php:6` | Die Klasse eZTagsTreeAttributeFilter implementiert das erweiterte Attribut TagsTreeAttributeFilter |
| `eZTagsType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/eztags/datatypes/eztags/eztagstype.php:6` | Die Klasse eZTagsType implementiert den Datentyp eztags |

## Modulansichten (17)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/tags/treemenu` | `treemenu.php` | read | `extension/eztags/modules/tags/module.php:7` |
| `/tags/dashboard` | `dashboard.php` | dashboard | `extension/eztags/modules/tags/module.php:13` |
| `/tags/id` | `id.php` | id | `extension/eztags/modules/tags/module.php:20` |
| `/tags/list_objects` | `list_objects.php` | id | `extension/eztags/modules/tags/module.php:27` |
| `/tags/view` | `view.php` | view | `extension/eztags/modules/tags/module.php:34` |
| `/tags/add` | `add.php` | add | `extension/eztags/modules/tags/module.php:41` |
| `/tags/addsynonym` | `addsynonym.php` | addsynonym | `extension/eztags/modules/tags/module.php:47` |
| `/tags/edit` | `edit.php` | edit | `extension/eztags/modules/tags/module.php:53` |
| `/tags/movetags` | `movetags.php` | edit | `extension/eztags/modules/tags/module.php:59` |
| `/tags/translation` | `translation.php` | edit | `extension/eztags/modules/tags/module.php:65` |
| `/tags/editsynonym` | `editsynonym.php` | editsynonym | `extension/eztags/modules/tags/module.php:71` |
| `/tags/delete` | `delete.php` | delete | `extension/eztags/modules/tags/module.php:77` |
| `/tags/deletetags` | `deletetags.php` | delete | `extension/eztags/modules/tags/module.php:83` |
| `/tags/deletesynonym` | `deletesynonym.php` | deletesynonym | `extension/eztags/modules/tags/module.php:89` |
| `/tags/makesynonym` | `makesynonym.php` | makesynonym | `extension/eztags/modules/tags/module.php:95` |
| `/tags/merge` | `merge.php` | merge | `extension/eztags/modules/tags/module.php:101` |
| `/tags/search` | `search.php` | search | `extension/eztags/modules/tags/module.php:107` |

## Richtlinienfunktionen (13)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `tags/read` | – | `extension/eztags/modules/tags/module.php:125` |
| `tags/dashboard` | – | `extension/eztags/modules/tags/module.php:126` |
| `tags/id` | – | `extension/eztags/modules/tags/module.php:127` |
| `tags/view` | – | `extension/eztags/modules/tags/module.php:128` |
| `tags/add` | Tag | `extension/eztags/modules/tags/module.php:129` |
| `tags/addsynonym` | – | `extension/eztags/modules/tags/module.php:130` |
| `tags/edit` | – | `extension/eztags/modules/tags/module.php:131` |
| `tags/editsynonym` | – | `extension/eztags/modules/tags/module.php:132` |
| `tags/delete` | – | `extension/eztags/modules/tags/module.php:133` |
| `tags/deletesynonym` | – | `extension/eztags/modules/tags/module.php:134` |
| `tags/makesynonym` | – | `extension/eztags/modules/tags/module.php:135` |
| `tags/merge` | – | `extension/eztags/modules/tags/module.php:136` |
| `tags/search` | – | `extension/eztags/modules/tags/module.php:137` |

## Fetch-Funktionen (9)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'tags', 'tag' )` | `eZTagsFunctionCollection::fetchTag()` | `extension/eztags/modules/tags/function_definition.php:5` |
| `fetch( 'tags', 'tags_by_keyword' )` | `eZTagsFunctionCollection::fetchTagsByKeyword()` | `extension/eztags/modules/tags/function_definition.php:16` |
| `fetch( 'tags', 'tag_by_remote_id' )` | `eZTagsFunctionCollection::fetchTagByRemoteID()` | `extension/eztags/modules/tags/function_definition.php:27` |
| `fetch( 'tags', 'tag_by_url' )` | `eZTagsFunctionCollection::fetchTagByUrl()` | `extension/eztags/modules/tags/function_definition.php:38` |
| `fetch( 'tags', 'list' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:49` |
| `fetch( 'tags', 'list_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:84` |
| `fetch( 'tags', 'tree' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:107` |
| `fetch( 'tags', 'tree_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:142` |
| `fetch( 'tags', 'latest_tags' )` | `eZTagsFunctionCollection::fetchLatestTags()` | `extension/eztags/modules/tags/function_definition.php:165` |

## Template-Operatoren (2)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| eztags_parent_string, latest_tags, user_limitations, tag_icon | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztemplateautoload.php:10` |
| eztagscloud | `eZTagsCloud` | `extension/eztags/autoloads/eztemplateautoload.php:14` |

## Ereignis-Hooks (13)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `tag/add` | filter | `extension/eztags/classes/runnable/views/tags/add.php:150` |
| `tag/add` | filter | `extension/eztags/classes/runnable/views/tags/addsynonym.php:121` |
| `tag/makesynonym` | filter | `extension/eztags/classes/runnable/views/tags/addsynonym.php:122` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/delete.php:58` |
| `tag/transferobjects` | filter | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:55` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:70` |
| `tag/delete` | filter | `extension/eztags/classes/runnable/views/tags/deletetags.php:66` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/edit.php:179` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/editsynonym.php:141` |
| `tag/makesynonym` | filter | `extension/eztags/classes/runnable/views/tags/makesynonym.php:111` |
| `tag/merge` | filter | `extension/eztags/classes/runnable/views/tags/merge.php:74` |
| `tag/edit` | filter | `extension/eztags/classes/runnable/views/tags/movetags.php:116` |
| `tag/add` | filter | `extension/eztags/datatypes/eztags/eztags.php:623` |

## INI-Plugin-Punkte (7)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[TagsAttributeFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:4` |
| `[TagsAttributeAndFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:8` |
| `[TagsAttributeAndMultipleFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:12` |
| `[TagsTreeAttributeFilter] ClassName` | `eZTagsTreeAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:16` |
| `[SolrFieldMapSettings] CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/settings/ezfind.ini.append.php:5` |
| `[ezjscServer_ezjsctags] Class` | `ezjscTags` | `extension/eztags/settings/ezjscore.ini.append.php:8` |
| `[ezjscServer_ezjsctagschildren] Class` | `ezjscTagsChildren` | `extension/eztags/settings/ezjscore.ini.append.php:11` |
