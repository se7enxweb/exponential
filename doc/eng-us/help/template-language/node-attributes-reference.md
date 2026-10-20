---
title: Node attributes reference
audience: developer, designer, app-creator
category: development
order: 210
summary: Every attribute a template can read from a node (eZContentObjectTreeNode) - the 16 database fields and the 35 function attributes of its definition, the PHP method behind each, and the value each returned for a real article on alpha.
keywords: node attributes, eZContentObjectTreeNode, definition, function_attributes, name, data_map, children, path, url_alias, can_edit, class_identifier, reference
type: help
---
# Node attributes reference

`$node` in a node template is an `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`). What a
template can read from it is exactly what `eZContentObjectTreeNode::definition()` lists: the database `fields` of the
`ezcontentobject_tree` table and the `function_attributes`, which call a PHP method each time they are read (see
[The object model in templates](object-model.md) for the protocol).

## Contents

1. [How this table was made](#how-this-table-was-made)
2. [Database fields](#database-fields)
3. [Function attributes](#function-attributes)
4. [A sample on alpha](#a-sample-on-alpha)
5. [Notes](#notes)
6. [Related pages](#related-pages)

## How this table was made

A read-only CLI script on alpha (run with `bin/php/ezexec.php -s site` as the web server's user) called
`eZContentObjectTreeNode::definition()`, listed `fields` and `function_attributes`, and read each name with
`$node->attribute( $name )` on node 96, an `ng_article`. The "Value on alpha" column is the PHP type and, for short
values, the value it returned. The script runs as the anonymous user, so the `can_*` checks show what an anonymous
visitor may do. The core of it:

```php
$node = eZContentObjectTreeNode::fetch( 96 );
$def = $node->definition();
foreach ( $def['fields'] as $name => $field )
    echo $name, ' ', $field['name'], ' ', $field['datatype'], "\n";
foreach ( $def['function_attributes'] as $name => $method )
    echo $name, ' ', $method, ' ', gettype( $node->attribute( $name ) ), "\n";
```

## Database fields

| Attribute | Database column / member | Datatype | Value on alpha (node 96) |
|---|---|---|---|
| `node_id` | `NodeID` | integer | integer 96 |
| `parent_node_id` | `ParentNodeID` | integer | integer 92 |
| `main_node_id` | `MainNodeID` | integer | integer 96 |
| `contentobject_id` | `ContentObjectID` | integer | integer 94 |
| `contentobject_version` | `ContentObjectVersion` | integer | integer 1 |
| `contentobject_is_published` | `ContentObjectIsPublished` | integer | integer 1 |
| `depth` | `Depth` | integer | integer 4 |
| `sort_field` | `SortField` | integer | integer 2 |
| `sort_order` | `SortOrder` | integer | integer 0 |
| `priority` | `Priority` | integer | integer 0 |
| `modified_subnode` | `ModifiedSubNode` | integer | integer 1790970543 |
| `path_string` | `PathString` | string | string "/1/2/89/92/96/" |
| `path_identification_string` | `PathIdentificationString` | text | string (59 bytes) |
| `remote_id` | `RemoteID` | string | string "media-n-729" |
| `is_hidden` | `IsHidden` | integer | integer 0 |
| `is_invisible` | `IsInvisible` | integer | integer 0 |

## Function attributes

`remote_id` is in both lists; the function attribute (`remoteID()`) is the one a template reads.

| Function attribute | PHP method | Value on alpha (node 96) |
|---|---|---|
| `name` | `eZContentObjectTreeNode::getName()` | string "Why Should You Choose Strength Training?" |
| `data_map` | `eZContentObjectTreeNode::dataMap()` | array(18) of eZContentObjectAttribute |
| `remote_id` | `eZContentObjectTreeNode::remoteID()` | string "media-n-729" |
| `object` | `eZContentObjectTreeNode::object()` | object eZContentObject |
| `subtree` | `eZContentObjectTreeNode::subTree()` | array(0) |
| `children` | `eZContentObjectTreeNode::children()` | array(0) |
| `children_count` | `eZContentObjectTreeNode::childrenCount()` | integer 0 |
| `view_count` | `eZContentObjectTreeNode::viewCount()` | integer 0 |
| `contentobject_version_object` | `eZContentObjectTreeNode::contentObjectVersionObject()` | object eZContentObjectVersion |
| `sort_array` | `eZContentObjectTreeNode::sortArray()` | array(1) of array |
| `can_read` | `eZContentObjectTreeNode::canRead()` | boolean true |
| `can_pdf` | `eZContentObjectTreeNode::canPdf()` | boolean true |
| `can_create` | `eZContentObjectTreeNode::canCreate()` | boolean true |
| `can_edit` | `eZContentObjectTreeNode::canEdit()` | boolean false |
| `can_hide` | `eZContentObjectTreeNode::canHide()` | boolean false |
| `can_remove` | `eZContentObjectTreeNode::canRemove()` | boolean false |
| `can_move` | `eZContentObjectTreeNode::canMoveFrom()` | boolean false |
| `can_move_from` | `eZContentObjectTreeNode::canMoveFrom()` | boolean false |
| `can_add_location` | `eZContentObjectTreeNode::canAddLocation()` | boolean false |
| `can_remove_location` | `eZContentObjectTreeNode::canRemoveLocation()` | boolean false |
| `can_view_embed` | `eZContentObjectTreeNode::canViewEmbed()` | boolean false |
| `is_main` | `eZContentObjectTreeNode::isMain()` | boolean true |
| `creator` | `eZContentObjectTreeNode::creator()` | object eZContentObject |
| `path_with_names` | `eZContentObjectTreeNode::pathWithNames()` | string (59 bytes) |
| `path` | `eZContentObjectTreeNode::fetchPath()` | array(3) of eZContentObjectTreeNode |
| `path_array` | `eZContentObjectTreeNode::pathArray()` | array(5) of integer |
| `parent` | `eZContentObjectTreeNode::fetchParent()` | object eZContentObjectTreeNode |
| `url` | `eZContentObjectTreeNode::url()` | string (47 bytes) |
| `url_alias` | `eZContentObjectTreeNode::urlAlias()` | string (47 bytes) |
| `class_identifier` | `eZContentObjectTreeNode::classIdentifier()` | string "ng_article" |
| `class_name` | `eZContentObjectTreeNode::className()` | string "NG Article" |
| `hidden_invisible_string` | `eZContentObjectTreeNode::hiddenInvisibleString()` | string "-/-" |
| `hidden_status_string` | `eZContentObjectTreeNode::hiddenStatusString()` | string "Visible" |
| `classes_js_array` | `eZContentObjectTreeNode::availableClassesJsArray()` | string (3218 bytes) |
| `is_container` | `eZContentObjectTreeNode::classIsContainer()` | integer 1 |

## A sample on alpha

A few of these attributes on node 89, the "Fit & Healthy" front page:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
name:            {$node.name|wash}
class:           {$node.class_identifier} ({$node.class_name|wash})
url_alias:       {$node.url_alias}
path_string:     {$node.path_string}
depth:           {$node.depth}
children_count:  {$node.children_count}
sort_array:      {$node.sort_array.0|implode( ', ' )}
is_main:         {cond( $node.is_main, 'yes', 'no' )}
hidden status:   {$node.hidden_status_string}
can_read:        {cond( $node.can_read, 'yes', 'no' )}, can_edit: {cond( $node.can_edit, 'yes', 'no' )}
current user:    {fetch( 'user', 'current_user' ).login}
```

Output on alpha:

```text
name:            Fit &amp; Healthy
class:           ng_frontpage (NG Frontpage)
url_alias:       
path_string:     /1/2/89/
depth:           2
children_count:  16
sort_array:      published, 1
is_main:         yes
hidden status:   Visible
can_read:        yes, can_edit: no
current user:    anonymous
```

## Notes

- `url_alias` of node 89 is empty. The siteaccess `site` has a path prefix (`[SiteAccessSettings] PathPrefix`), and
  `urlAlias()` removes that prefix from the start of the alias; node 89 is the node at the prefix itself.
  `path_with_names` is the full alias path without that step, and `url` returns `url_alias` while
  `[URLTranslator] Translation` is enabled, else `content/view/full/<node id>`:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
PathPrefix:      {ezini( 'SiteAccessSettings', 'PathPrefix' )}
path_with_names: {$node.path_with_names}
url_alias:       {$node.url_alias}
url:             {$node.url}
```

Output on alpha:

```text
PathPrefix:      fit-healthy
path_with_names: fit-healthy/fitness/why-should-you-choose-strength-training
url_alias:       fitness/why-should-you-choose-strength-training
url:             fitness/why-should-you-choose-strength-training
```

- `sort_array` is a list of `array( sort field, ascending )` pairs, the node's child sort order as `fetch( 'content',
  'list' )` understands it (here: by publishing date, ascending is `1`).
- `children`, `subtree` and `children_count` run a query each time they are read. In a list with many items use
  `fetch( 'content', 'list', ... )` with `limit` instead (see [Fetch performance](../designers/templates/fetch-performance.md)).
- `can_read`, `can_edit`, `can_hide`, `can_remove`, `can_move`, `can_create` check the current user's policies on this
  location; their results depend on who views the page, which is why such checks belong outside view-cached output or
  in templates whose cache varies by role (the content view cache keys include the user's role IDs).
- `classes_js_array` returns a JavaScript array string for the admin's "create here" menu; site templates do not need it.
- `creator` returns the content object of the user who created the version stored on this node.

## Related pages

- [The object model in templates](object-model.md)
- [Content object attributes reference](object-attributes-reference.md)
- [Content attribute and version reference](content-attribute-reference.md)
- [Nodes, objects and attributes](../designers/templates/nodes-objects-attributes.md)
- [Fetching nodes and lists](../designers/templates/fetching-nodes-and-lists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
