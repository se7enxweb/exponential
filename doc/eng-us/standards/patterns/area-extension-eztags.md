---
title: Patterns in extension/eztags
audience: developer
category: standards
order: 456
summary: Every class, view, fetch function, operator and plugin point of extension/eztags with its design patterns.
keywords: extension/eztags, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/eztags

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/eztags` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (32)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Command\Extension\Eztags\Convertezkeyword` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/eztags/classes/runnable/commands/php_convertezkeyword.php:10` | The code of extension/eztags/bin/php/convertezkeyword.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Add` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/add.php:15` | The code of extension/eztags/modules/tags/add.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Addsynonym` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/addsynonym.php:15` | The code of extension/eztags/modules/tags/addsynonym.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/dashboard.php:10` | The code of extension/eztags/modules/tags/dashboard.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Delete` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/delete.php:15` | The code of extension/eztags/modules/tags/delete.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Deletesynonym` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:15` | The code of extension/eztags/modules/tags/deletesynonym.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Deletetags` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/deletetags.php:15` | The code of extension/eztags/modules/tags/deletetags.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Edit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/edit.php:15` | The code of extension/eztags/modules/tags/edit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Editsynonym` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/editsynonym.php:15` | The code of extension/eztags/modules/tags/editsynonym.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Id` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/id.php:15` | The code of extension/eztags/modules/tags/id.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\ListObjects` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/list_objects.php:15` | The code of extension/eztags/modules/tags/list_objects.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Makesynonym` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/makesynonym.php:15` | The code of extension/eztags/modules/tags/makesynonym.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Merge` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/merge.php:15` | The code of extension/eztags/modules/tags/merge.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Movetags` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/movetags.php:15` | The code of extension/eztags/modules/tags/movetags.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Search` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/search.php:10` | The code of extension/eztags/modules/tags/search.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Translation` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/translation.php:15` | The code of extension/eztags/modules/tags/translation.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\Treemenu` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/treemenu.php:15` | The code of extension/eztags/modules/tags/treemenu.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Eztags\Tags\View` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/eztags/classes/runnable/views/tags/view.php:15` | The code of extension/eztags/modules/tags/view.php, moved into a class (#207 stage 1). |
| `ezfSolrDocumentFieldeZTags` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php:7` | ezfSolrDocumentFieldeZTags class implements custom indexing handler for eZ Find |
| `ezjscTags` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/eztags/classes/ezjsctags.php:6` | ezjscTags class implements eZ JS Core server functions for eztags |
| `ezjscTagsChildren` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/eztags/classes/ezjsctagschildren.php:6` | ezjscTagsChildren class implements eZ JS Core server functions for eztags children list |
| `eZTags` | class | [Factory and static factory methods](factory.md) | `extension/eztags/datatypes/eztags/eztags.php:6` | eZTags class implements functions used by eztags datatype |
| `eZTagsAttributeFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/eztags/classes/eztagsattributefilter.php:6` | eZTagsAttributeFilter class implements TagsAttributeFilter extended attribute |
| `eZTagsAttributeLinkObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagsattributelinkobject.php:7` | eZTagsAttributeLinkObject class inherits eZPersistentObject class to be able to access eztags_attribute_link database table through API |
| `eZTagsCloud` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/eztags/autoloads/eztagscloud.php:6` | eZTagsCloud class implements eztagscloud template operator method |
| `eZTagsFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `extension/eztags/modules/tags/eztagsfunctioncollection.php:9` | eZTagsFunctionCollection class implements fetch functions for eztags |
| `eztagsInfo` | class | [Value object / struct](value-object.md) | `extension/eztags/ezinfo.php:11` | – |
| `eZTagsKeyword` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagskeyword.php:7` | eZTagsKeyword class inherits eZPersistentObject class to be able to access eztags_keyword database table through API |
| `eZTagsObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/eztags/classes/eztagsobject.php:9` | eZTagsObject class inherits eZPersistentObject class to be able to access eztags database table through API |
| `eZTagsTemplateFunctions` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/eztags/autoloads/eztagstemplatefunctions.php:6` | eZTagsTemplateFunctions class implements eztags template operator methods |
| `eZTagsTreeAttributeFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/eztags/classes/eztagstreeattributefilter.php:6` | eZTagsTreeAttributeFilter class implements TagsTreeAttributeFilter extended attribute |
| `eZTagsType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/eztags/datatypes/eztags/eztagstype.php:6` | eZTagsType class implements the eztags datatype |

## Module views (17)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
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

## Policy functions (13)

| Function | Limitations | Defined in |
|---|---|---|
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

## Fetch functions (9)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'tags', 'tag' )` | `eZTagsFunctionCollection::fetchTag()` | `extension/eztags/modules/tags/function_definition.php:5` |
| `fetch( 'tags', 'tags_by_keyword' )` | `eZTagsFunctionCollection::fetchTagsByKeyword()` | `extension/eztags/modules/tags/function_definition.php:16` |
| `fetch( 'tags', 'tag_by_remote_id' )` | `eZTagsFunctionCollection::fetchTagByRemoteID()` | `extension/eztags/modules/tags/function_definition.php:27` |
| `fetch( 'tags', 'tag_by_url' )` | `eZTagsFunctionCollection::fetchTagByUrl()` | `extension/eztags/modules/tags/function_definition.php:38` |
| `fetch( 'tags', 'list' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:49` |
| `fetch( 'tags', 'list_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:84` |
| `fetch( 'tags', 'tree' )` | `eZTagsFunctionCollection::fetchTagTree()` | `extension/eztags/modules/tags/function_definition.php:107` |
| `fetch( 'tags', 'tree_count' )` | `eZTagsFunctionCollection::fetchTagTreeCount()` | `extension/eztags/modules/tags/function_definition.php:142` |
| `fetch( 'tags', 'latest_tags' )` | `eZTagsFunctionCollection::fetchLatestTags()` | `extension/eztags/modules/tags/function_definition.php:165` |

## Template operators (2)

| Operators | Class | Registered in |
|---|---|---|
| eztags_parent_string, latest_tags, user_limitations, tag_icon | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztemplateautoload.php:10` |
| eztagscloud | `eZTagsCloud` | `extension/eztags/autoloads/eztemplateautoload.php:14` |

## Event hooks (13)

| Event | Kind | Called in |
|---|---|---|
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

## INI plugin points (7)

| Setting | Class | Set in |
|---|---|---|
| `[TagsAttributeFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:4` |
| `[TagsAttributeAndFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:8` |
| `[TagsAttributeAndMultipleFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:12` |
| `[TagsTreeAttributeFilter] ClassName` | `eZTagsTreeAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:16` |
| `[SolrFieldMapSettings] CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/settings/ezfind.ini.append.php:5` |
| `[ezjscServer_ezjsctags] Class` | `ezjscTags` | `extension/eztags/settings/ezjscore.ini.append.php:8` |
| `[ezjscServer_ezjsctagschildren] Class` | `ezjscTagsChildren` | `extension/eztags/settings/ezjscore.ini.append.php:11` |
