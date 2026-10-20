---
title: The content API in PHP
audience: app-creator, developer
category: app-creation
order: 190
summary: Read content from PHP - eZContentObject, eZContentObjectTreeNode and eZContentObjectAttribute, fetching by id, remote id and URL alias, subtree queries with filters, data maps, versions and languages, and permission-aware reads.
keywords: content API, eZContentObject, eZContentObjectTreeNode, eZContentObjectAttribute, data map, subTreeByNodeID, fetchByRemoteID, fetchByURLPath, versions, languages, PHP, app creator
type: help
---
# The content API in PHP

This page is for application creators who read content in PHP: in views, services, commands and cronjobs. The classes
are the kernel's `eZContentObject`, `eZContentObjectTreeNode` and `eZContentObjectAttribute` in `kernel/classes/`.

## Contents

1. [Objects, nodes and attributes](#objects-nodes-and-attributes)
2. [Fetching one item](#fetching-one-item)
3. [Fetching lists](#fetching-lists)
4. [Reading attributes](#reading-attributes)
5. [Versions and languages](#versions-and-languages)
6. [Permissions and context](#permissions-and-context)

## Objects, nodes and attributes

| Class | Is | Key attributes |
|---|---|---|
| `eZContentObject` | a piece of content, all its versions and translations | `id`, `remote_id`, `name`, `current_version`, `main_node_id`, `class_identifier`, `section_id` |
| `eZContentObjectTreeNode` | one location of an object | `node_id`, `parent_node_id`, `path_string`, `url_alias`, `depth`, `object` |
| `eZContentObjectAttribute` | one field of one version and language | `data_type_string`, `content`, `has_content`, `contentclass_attribute_identifier` |

## Fetching one item

```php
$object = eZContentObject::fetch( 57 );
$object = eZContentObject::fetchByRemoteID( 'expdemo-settings-page' );
$node   = eZContentObjectTreeNode::fetch( 2 );
$node   = eZContentObjectTreeNode::fetchByURLPath( 'about_us' );
```

Prefer **remote IDs** for content your application depends on: they are stable across installations, IDs are not.

## Fetching lists

```php
$nodes = eZContentObjectTreeNode::subTreeByNodeID( array(
    'ClassFilterType'  => 'include',
    'ClassFilterArray' => array( 'event' ),
    'AttributeFilter'  => array( array( 'event/start_date', '>=', time() ) ),
    'SortBy'           => array( array( 'attribute', true, 'event/start_date' ) ),
    'Depth'            => 3,
    'Limit'            => 20,
    'Offset'           => 0 ), $parentNodeID );

$count = eZContentObjectTreeNode::subTreeCountByNodeID( array(
    'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'event' ) ), $parentNodeID );
```

These are the PHP side of the template fetches ([Fetch functions](fetch-functions.md)). Always set a `Limit`.

## Reading attributes

```php
$dataMap = $object->dataMap();                     // identifier => eZContentObjectAttribute
if ( isset( $dataMap['title'] ) && $dataMap['title']->hasContent() )
    $title = $dataMap['title']->content();         // a string for ezstring
$image = $dataMap['image']->content();             // an eZImageAliasHandler for ezimage
$alias = $image->attribute( 'medium' );            // url, width, height, alternative_text
$text  = $dataMap['title']->toString();            // a plain string form for every datatype
```

`content()` returns what the datatype stores (a string, a number, an object); `toString()` a string you can store and
pass to `fromString()` later ([The datatype life cycle](datatype-lifecycle.md)).

## Versions and languages

```php
$version = $object->version( $object->attribute( 'current_version' ) );
$germanMap = $object->fetchDataMap( false, 'ger-DE' );
$languages = $object->availableLanguages();
```

The current version is the published one; drafts are other versions with their own status. Which language a page shows
follows the siteaccess's `SiteLanguageList` ([Content languages](../../../guides/content-languages.md)).

## Permissions and context

- `subTreeByNodeID()` applies the current user's `content/read` unless you pass `'Limitation' => array()`; never pass it
  in code that shows results to users.
- `$node->canRead()`, `$object->canEdit()` check one item ([Policy functions and limitations](policy-functions-and-limitations.md)).
- In command line scripts the current user is anonymous until you log one in; scripts that need more must log in a
  dedicated user (`eZUser::setCurrentlyLoggedInUser()`), not the administrator.
- Large lists: fetch in pages and clear in-memory caches between them (`eZContentObject::clearCache()`) so a long
  script does not run out of memory.

## Related pages

- [Creating and updating content in code](creating-content-in-code.md)
- [Persistent objects and custom tables](persistent-objects-and-custom-tables.md)
- [Active record pattern](../../../standards/patterns/active-record.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
