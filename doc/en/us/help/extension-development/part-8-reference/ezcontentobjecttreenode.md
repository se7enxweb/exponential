---
title: eZContentObjectTreeNode
audience: developer
category: extension-development
order: 70
summary: The node methods extensions use - fetch(), fetchByURLPath(), subTreeByNodeID() and its parameters, subTreeCountByNodeID(), object(), path, URL alias, access - with a tested example.
keywords: eZContentObjectTreeNode, node, fetch, subTreeByNodeID, subTreeCountByNodeID, ClassFilterType, ClassFilterArray, AttributeFilter, SortBy, Depth, Limit, url_alias, pathArray, children, canRead
type: help
---
# eZContentObjectTreeNode

Part 8, reference 7. A node (`kernel/classes/ezcontentobjecttreenode.php`) is a location of a content object in the
content tree. Lists, menus and paths are made of nodes. The node with ID 2 is usually the root of the site's content.

## Fetching one node

| Method (static) | Returns |
|---|---|
| `fetch( $nodeID = false, $lang = false, $asObject = true, $conditions = false )` | the node, or `null` |
| `fetchByURLPath( $pathString, $asObject = true )` | the node at a URL path |
| `fetchByContentObjectID( $contentObjectID, $asObject = true, $contentObjectVersion = false )` | every node of an object |
| `fetchNode( $contentObjectID, $parentNodeID )` | the node of an object under a given parent |

## Fetching lists: subTreeByNodeID()

```php
static function subTreeByNodeID( $params = false, $nodeID = 0 )
static function subTreeCountByNodeID( $params, $nodeID )
```

The parameters, in an array:

| Key | Meaning |
|---|---|
| `Depth`, `DepthOperator` | how deep: `1` with `eq` for direct children; `lt`, `le` and others for ranges |
| `ClassFilterType`, `ClassFilterArray` | `include` or `exclude`, with class identifiers |
| `AttributeFilter` | conditions on attributes, for example `array( array( 'published', '>', $since ) )` |
| `SortBy` | `array( 'published', false )` (false = descending), or a list of such pairs |
| `Offset`, `Limit` | paging |
| `Language`, `OnlyTranslated` | translations |
| `IgnoreVisibility` | include hidden nodes |
| `MainNodeOnly` | only main locations |
| `LoadDataMap` | load the attributes at once |
| `Limitation` | `array()` to skip the access check (only when you check access yourself) |
| `AsObject` | `false` for plain arrays |

Access policies and visibility apply unless you switch them off; that is what you want for anything shown to users.
Always use the matching `subTreeCountByNodeID()` for paging, with the same filters.

```php
<?php
$params = array( 'Depth' => 1, 'DepthOperator' => 'eq',
                 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'article', 'folder' ),
                 'SortBy' => array( 'published', false ),
                 'Offset' => 0, 'Limit' => 10 );
$children = eZContentObjectTreeNode::subTreeByNodeID( $params, 2 );
$total = eZContentObjectTreeNode::subTreeCountByNodeID( $params, 2 );
foreach ( $children as $child )
{
    echo $child->attribute( 'node_id' ), ' ', $child->getName(), ' /', $child->attribute( 'url_alias' ), "\n";
}
```

## Reading a node

| Method or attribute | Returns |
|---|---|
| `object()` | the content object |
| `dataMap()` | the object's attributes |
| `getName( $language = false )` | the name |
| `attribute( 'url_alias' )`, `urlAlias()` | the URL path without the leading slash |
| `fetchParent()` | the parent node |
| `pathArray()` | the node IDs from the root to this node |
| `children()`, `childrenCount( $checkPolicies = true )` | direct children |
| `isMain()` | whether this is the object's main node |
| `canRead()`, `canEdit()`, `canCreate()`, `canRemove()` | access of the current user |

## In templates

The same lists come from the fetch function `fetch( 'content', 'list', hash( ... ) )` with the same parameters in
lower case (`parent_node_id`, `class_filter_type`, `sort_by`, `limit`); see
[Built-in fetch functions](fetch-functions-reference.md).

## Related pages

- Previous: [eZContentObject](ezcontentobject.md)
- Next: [eZContentClass and its attributes](ezcontentclass.md)
- [Fetch for builders](../part-2-builder/fetch-for-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
