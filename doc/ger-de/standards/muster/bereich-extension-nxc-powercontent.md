---
title: Muster in extension/nxc_powercontent
audience: developer
category: standards
order: 464
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/nxc_powercontent mit den Entwurfsmustern.
keywords: extension/nxc_powercontent, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/nxc_powercontent

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/nxc_powercontent` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (6)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZContentFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php:20` | – |
| `eZContentOperationCollection` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md) | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:20` | – |
| `ezpContentPublishingBehaviour` | class extends ezcBaseOptions | – | `extension/nxc_powercontent/modules/content/ezcontentpublishingbehaviour.php:19` | Diese Klasse ermöglicht die Anpassung von |
| `nxcPowerContent` | class | – | `extension/nxc_powercontent/classes/powercontent.php:8` | – |
| `nxc_powercontentInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/nxc_powercontent/ezinfo.php:8` | – |
| `nxc_powercontentSettings` | class extends nxcExtensionSettings | – | `extension/nxc_powercontent/settings.php:8` | – |

## Modulansichten (40)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/content/edit` | `edit.php` | bearbeiten oder anlegen | `extension/nxc_powercontent/modules/content/module.php:14` |
| `/content/removenode` | `removenode.php` | edit | `extension/nxc_powercontent/modules/content/module.php:49` |
| `/content/removeassignment` | `removeassignment.php` | edit | `extension/nxc_powercontent/modules/content/module.php:58` |
| `/content/pdf` | `pdf.php` | pdf | `extension/nxc_powercontent/modules/content/module.php:68` |
| `/content/view` | `view.php` | read | `extension/nxc_powercontent/modules/content/module.php:80` |
| `/content/copy` | `copy.php` | read | `extension/nxc_powercontent/modules/content/module.php:92` |
| `/content/copysubtree` | `copysubtree.php` | create | `extension/nxc_powercontent/modules/content/module.php:103` |
| `/content/versionview` | `versionview.php` | versionread | `extension/nxc_powercontent/modules/content/module.php:117` |
| `/content/restore` | `restore.php` | restore | `extension/nxc_powercontent/modules/content/module.php:134` |
| `/content/search` | `search.php` | read | `extension/nxc_powercontent/modules/content/module.php:145` |
| `/content/urlalias` | `urlalias.php` | edit | `extension/nxc_powercontent/modules/content/module.php:152` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:166` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:186` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `extension/nxc_powercontent/modules/content/module.php:203` |
| `/content/browse` | `browse.php` | read | `extension/nxc_powercontent/modules/content/module.php:210` |
| `/content/upload` | `upload.php` | create | `extension/nxc_powercontent/modules/content/module.php:218` |
| `/content/removeobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:228` |
| `/content/removeuserobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:234` |
| `/content/removemediaobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:240` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `extension/nxc_powercontent/modules/content/module.php:246` |
| `/content/download` | `download.php` | read | `extension/nxc_powercontent/modules/content/module.php:253` |
| `/content/action` | `action.php` | read | `extension/nxc_powercontent/modules/content/module.php:260` |
| `/content/collectinformation` | `collectinformation.php` | read | `extension/nxc_powercontent/modules/content/module.php:328` |
| `/content/draft` | `draft.php` | edit | `extension/nxc_powercontent/modules/content/module.php:338` |
| `/content/history` | `history.php` | read, edit | `extension/nxc_powercontent/modules/content/module.php:345` |
| `/content/trash` | `trash.php` | restore | `extension/nxc_powercontent/modules/content/module.php:360` |
| `/content/translations` | `translations.php` | translations | `extension/nxc_powercontent/modules/content/module.php:367` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `extension/nxc_powercontent/modules/content/module.php:383` |
| `/content/keyword` | `keyword.php` | read | `extension/nxc_powercontent/modules/content/module.php:389` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `extension/nxc_powercontent/modules/content/module.php:396` |
| `/content/bookmark` | `bookmark.php` | bookmark | `extension/nxc_powercontent/modules/content/module.php:402` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `extension/nxc_powercontent/modules/content/module.php:413` |
| `/content/new` | `newcontent.php` | read | `extension/nxc_powercontent/modules/content/module.php:420` |
| `/content/hide` | `hide.php` | hide | `extension/nxc_powercontent/modules/content/module.php:426` |
| `/content/move` | `move.php` | edit | `extension/nxc_powercontent/modules/content/module.php:432` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `extension/nxc_powercontent/modules/content/module.php:438` |
| `/content/translation` | `translation.php` | read | `extension/nxc_powercontent/modules/content/module.php:445` |
| `/content/treemenu` | `treemenu.php` | read | `extension/nxc_powercontent/modules/content/module.php:474` |
| `/content/dashboard` | `dashboard.php` | dashboard | `extension/nxc_powercontent/modules/content/module.php:481` |
| `/content/queued` | `queued.php` | edit | `extension/nxc_powercontent/modules/content/module.php:488` |

## Richtlinienfunktionen (23)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `content/bookmark` | – | `extension/nxc_powercontent/modules/content/module.php:605` |
| `content/move` | – | `extension/nxc_powercontent/modules/content/module.php:607` |
| `content/read` | Class, Section, Owner, Group, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:609` |
| `content/diff` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:616` |
| `content/view_embed` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:621` |
| `content/create` | Class, Section, ParentOwner, ParentGroup, ParentClass, ParentDepth, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:626` |
| `content/edit` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:636` |
| `content/publish` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:645` |
| `content/manage_locations` | Class, Section, Owner, Subtree | `extension/nxc_powercontent/modules/content/module.php:654` |
| `content/hide` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:660` |
| `content/reverserelatedlist` | – | `extension/nxc_powercontent/modules/content/module.php:668` |
| `content/translate` | Class, Section, Owner, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:670` |
| `content/remove` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:676` |
| `content/versionread` | Class, Section, Owner, Status, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:684` |
| `content/versionremove` | Class, Section, Owner, Status, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:691` |
| `content/pdf` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:698` |
| `content/translations` | – | `extension/nxc_powercontent/modules/content/module.php:704` |
| `content/urltranslator` | – | `extension/nxc_powercontent/modules/content/module.php:705` |
| `content/pendinglist` | – | `extension/nxc_powercontent/modules/content/module.php:706` |
| `content/restore` | – | `extension/nxc_powercontent/modules/content/module.php:707` |
| `content/cleantrash` | – | `extension/nxc_powercontent/modules/content/module.php:708` |
| `content/tipafriend` | – | `extension/nxc_powercontent/modules/content/module.php:709` |
| `content/dashboard` | – | `extension/nxc_powercontent/modules/content/module.php:710` |

## Fetch-Funktionen (56)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'content', 'object' )` | `eZContentFunctionCollection::fetchContentObject()` | `extension/nxc_powercontent/modules/content/function_definition.php:12` |
| `fetch( 'content', 'version' )` | `eZContentFunctionCollection::fetchContentVersion()` | `extension/nxc_powercontent/modules/content/function_definition.php:25` |
| `fetch( 'content', 'node' )` | `eZContentFunctionCollection::fetchContentNode()` | `extension/nxc_powercontent/modules/content/function_definition.php:37` |
| `fetch( 'content', 'locale_list' )` | `eZContentFunctionCollection::fetchLocaleList()` | `extension/nxc_powercontent/modules/content/function_definition.php:58` |
| `fetch( 'content', 'locale' )` | `eZContentFunctionCollection::fetchLocale()` | `extension/nxc_powercontent/modules/content/function_definition.php:67` |
| `fetch( 'content', 'prioritized_languages' )` | `eZContentFunctionCollection::fetchPrioritizedLanguages()` | `extension/nxc_powercontent/modules/content/function_definition.php:77` |
| `fetch( 'content', 'prioritized_language_codes' )` | `eZContentFunctionCollection::fetchPrioritizedLanguageCodes()` | `extension/nxc_powercontent/modules/content/function_definition.php:83` |
| `fetch( 'content', 'translation_list' )` | `eZContentFunctionCollection::fetchTranslationList()` | `extension/nxc_powercontent/modules/content/function_definition.php:89` |
| `fetch( 'content', 'non_translation_list' )` | `eZContentFunctionCollection::fetchNonTranslationList()` | `extension/nxc_powercontent/modules/content/function_definition.php:95` |
| `fetch( 'content', 'class' )` | `eZContentFunctionCollection::fetchClass()` | `extension/nxc_powercontent/modules/content/function_definition.php:106` |
| `fetch( 'content', 'class_attribute_list' )` | `eZContentFunctionCollection::fetchClassAttributeList()` | `extension/nxc_powercontent/modules/content/function_definition.php:114` |
| `fetch( 'content', 'class_attribute' )` | `eZContentFunctionCollection::fetchClassAttribute()` | `extension/nxc_powercontent/modules/content/function_definition.php:126` |
| `fetch( 'content', 'calendar' )` | `eZContentFunctionCollection::calendar()` | `extension/nxc_powercontent/modules/content/function_definition.php:138` |
| `fetch( 'content', 'list' )` | `eZContentFunctionCollection::fetchObjectTree()` | `extension/nxc_powercontent/modules/content/function_definition.php:199` |
| `fetch( 'content', 'list_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:283` |
| `fetch( 'content', 'tree' )` | `eZContentFunctionCollection::fetchObjectTree()` | `extension/nxc_powercontent/modules/content/function_definition.php:339` |
| `fetch( 'content', 'tree_count' )` | `eZContentFunctionCollection::fetchObjectTreeCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:425` |
| `fetch( 'content', 'search' )` | `eZContentFunctionCollection::fetchContentSearch()` | `extension/nxc_powercontent/modules/content/function_definition.php:482` |
| `fetch( 'content', 'trash_count' )` | `eZContentFunctionCollection::fetchTrashObjectCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:535` |
| `fetch( 'content', 'trash_object_list' )` | `eZContentFunctionCollection::fetchTrashObjectList()` | `extension/nxc_powercontent/modules/content/function_definition.php:549` |
| `fetch( 'content', 'draft_count' )` | `eZContentFunctionCollection::fetchDraftVersionCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:579` |
| `fetch( 'content', 'draft_version_list' )` | `eZContentFunctionCollection::fetchDraftVersionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:586` |
| `fetch( 'content', 'pending_count' )` | `eZContentFunctionCollection::fetchPendingCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:600` |
| `fetch( 'content', 'pending_list' )` | `eZContentFunctionCollection::fetchPendingList()` | `extension/nxc_powercontent/modules/content/function_definition.php:607` |
| `fetch( 'content', 'version_count' )` | `eZContentFunctionCollection::fetchVersionCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:621` |
| `fetch( 'content', 'version_list' )` | `eZContentFunctionCollection::fetchVersionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:630` |
| `fetch( 'content', 'can_instantiate_class_list' )` | `eZContentFunctionCollection::canInstantiateClassList()` | `extension/nxc_powercontent/modules/content/function_definition.php:653` |
| `fetch( 'content', 'class_list' )` | `eZContentFunctionCollection::fetchClassList()` | `extension/nxc_powercontent/modules/content/function_definition.php:683` |
| `fetch( 'content', 'can_instantiate_classes' )` | `eZContentFunctionCollection::canInstantiateClasses()` | `extension/nxc_powercontent/modules/content/function_definition.php:705` |
| `fetch( 'content', 'contentobject_attributes' )` | `eZContentFunctionCollection::contentobjectAttributes()` | `extension/nxc_powercontent/modules/content/function_definition.php:714` |
| `fetch( 'content', 'bookmarks' )` | `eZContentFunctionCollection::fetchBookmarks()` | `extension/nxc_powercontent/modules/content/function_definition.php:728` |
| `fetch( 'content', 'recent' )` | `eZContentFunctionCollection::fetchRecent()` | `extension/nxc_powercontent/modules/content/function_definition.php:742` |
| `fetch( 'content', 'section_list' )` | `eZContentFunctionCollection::fetchSectionList()` | `extension/nxc_powercontent/modules/content/function_definition.php:749` |
| `fetch( 'content', 'tipafriend_top_list' )` | `eZContentFunctionCollection::fetchTipafriendTopList()` | `extension/nxc_powercontent/modules/content/function_definition.php:756` |
| `fetch( 'content', 'view_top_list' )` | `eZContentFunctionCollection::fetchMostViewedTopList()` | `extension/nxc_powercontent/modules/content/function_definition.php:790` |
| `fetch( 'content', 'collected_info_count' )` | `eZContentFunctionCollection::fetchCollectedInfoCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:812` |
| `fetch( 'content', 'collected_info_count_list' )` | `eZContentFunctionCollection::fetchCollectedInfoCountList()` | `extension/nxc_powercontent/modules/content/function_definition.php:838` |
| `fetch( 'content', 'collected_info_collection' )` | `eZContentFunctionCollection::fetchCollectedInfoCollection()` | `extension/nxc_powercontent/modules/content/function_definition.php:849` |
| `fetch( 'content', 'collected_info_list' )` | `eZContentFunctionCollection::fetchCollectionsList()` | `extension/nxc_powercontent/modules/content/function_definition.php:863` |
| `fetch( 'content', 'object_by_attribute' )` | `eZContentFunctionCollection::fetchObjectByAttribute()` | `extension/nxc_powercontent/modules/content/function_definition.php:893` |
| `fetch( 'content', 'object_count_by_user_id' )` | `eZContentFunctionCollection::fetchObjectCountByUserID()` | `extension/nxc_powercontent/modules/content/function_definition.php:903` |
| `fetch( 'content', 'same_classattribute_node' )` | `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` | `extension/nxc_powercontent/modules/content/function_definition.php:921` |
| `fetch( 'content', 'keyword' )` | `eZContentFunctionCollection::fetchKeyword()` | `extension/nxc_powercontent/modules/content/function_definition.php:936` |
| `fetch( 'content', 'keyword_count' )` | `eZContentFunctionCollection::fetchKeywordCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:982` |
| `fetch( 'content', 'access' )` | `eZContentFunctionCollection::checkAccess()` | `extension/nxc_powercontent/modules/content/function_definition.php:1015` |
| `fetch( 'content', 'navigation_parts' )` | `eZContentFunctionCollection::fetchNavigationParts()` | `extension/nxc_powercontent/modules/content/function_definition.php:1040` |
| `fetch( 'content', 'navigation_part' )` | `eZContentFunctionCollection::fetchNavigationPart()` | `extension/nxc_powercontent/modules/content/function_definition.php:1048` |
| `fetch( 'content', 'related_objects' )` | `eZContentFunctionCollection::fetchRelatedObjects()` | `extension/nxc_powercontent/modules/content/function_definition.php:1058` |
| `fetch( 'content', 'related_objects_count' )` | `eZContentFunctionCollection::fetchRelatedObjectsCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:1107` |
| `fetch( 'content', 'reverse_related_objects' )` | `eZContentFunctionCollection::fetchReverseRelatedObjects()` | `extension/nxc_powercontent/modules/content/function_definition.php:1124` |
| `fetch( 'content', 'reverse_related_objects_count' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` | `extension/nxc_powercontent/modules/content/function_definition.php:1168` |
| `fetch( 'content', 'available_sort_fields' )` | `eZContentFunctionCollection::fetchAvailableSortFieldList()` | `extension/nxc_powercontent/modules/content/function_definition.php:1188` |
| `fetch( 'content', 'country_list' )` | `eZContentFunctionCollection::fetchCountryList()` | `extension/nxc_powercontent/modules/content/function_definition.php:1195` |
| `fetch( 'content', 'related_objects_ids' )` | `eZContentFunctionCollection::fetchRelatedObjectsID()` | `extension/nxc_powercontent/modules/content/function_definition.php:1207` |
| `fetch( 'content', 'reverse_related_objects_ids' )` | `eZContentFunctionCollection::fetchReverseRelatedObjectsID()` | `extension/nxc_powercontent/modules/content/function_definition.php:1224` |
| `fetch( 'content', 'content_tree_menu_expiry' )` | `eZContentFunctionCollection::fetchContentTreeMenuExpiry()` | `extension/nxc_powercontent/modules/content/function_definition.php:1241` |

## Ereignis-Hooks (11)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache/version` | notify | `extension/nxc_powercontent/modules/content/attribute_edit.php:325` |
| `content/download` | notify | `extension/nxc_powercontent/modules/content/download.php:74` |
| `content/cache` | filter | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:960` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:1075` |
| `content/state/assign` | notify | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:1467` |
| `content/translations/cache` | notify | `extension/nxc_powercontent/modules/content/translations.php:76` |
| `content/translations/cache` | notify | `extension/nxc_powercontent/modules/content/translations.php:104` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:55` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:75` |
| `content/cache` | notify | `extension/nxc_powercontent/modules/content/urlalias.php:157` |
| `content/view` | filter | `extension/nxc_powercontent/modules/content/view.php:52` |
