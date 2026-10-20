---
title: eZContentObject
audience: developer
category: extension-development
order: 60
summary: The eZContentObject methods extensions use - fetch(), fetchByRemoteID(), dataMap(), mainNode(), assignedNodes(), versions, access checks - with signatures and a tested example.
keywords: eZContentObject, fetch, fetchByRemoteID, fetchByNodeID, dataMap, mainNode, mainNodeID, assignedNodes, currentVersion, contentClassIdentifier, canRead, canEdit, checkAccess, remoteID
type: help
---
# eZContentObject

Part 8, reference 6. A content object (`kernel/classes/ezcontentobject.php`) is one piece of content: an article, an
image, a user. It has versions and translations; each published version has attributes, one per attribute of its
content class. It appears in the content tree through one or more nodes ([eZContentObjectTreeNode](ezcontentobjecttreenode.md)).
Creating and publishing objects is in [Content functions from PHP](content-functions-from-php.md).

## Fetching

| Method (static) | Returns |
|---|---|
| `fetch( $id, $asObject = true )` | the object, or `null` (and an error in the debug output) if there is none |
| `fetchByRemoteID( $remoteID, $asObject = true )` | the object with that remote ID |
| `fetchByNodeID( $nodeID, $asObject = true )` | the object of a node |
| `fetchIDArray( $idArray, $asObject = true, $lang = false )` | several objects at once |
| `fetchList( $asObject = true, $conditions = null, $offset = false, $limit = false )` | objects matching conditions |

Prefer the remote ID over the numeric ID for objects your extension refers to in settings or code: remote IDs survive
an export and import to another installation, numeric IDs do not.

## Reading

| Method or attribute | Returns |
|---|---|
| `attribute( 'name' )`, `name( $version = false, $lang = false )` | the object's name |
| `attribute( 'id' )`, `remoteID()` | the IDs |
| `contentClassIdentifier()`, `contentClass()` | the class's identifier, or the class object |
| `dataMap()` | the attributes of the current version, keyed by attribute identifier |
| `fetchDataMap( $version = false, $language = false )` | the same for another version or language |
| `mainNodeID()`, `mainNode()` | the main location |
| `assignedNodes( $asObject = true, ... )` | every location |
| `currentVersion( $asObject = true )`, `version( $version, $asObject = true )` | versions |
| `relatedObjects( ... )`, `reverseRelatedObjectList( ... )` | relations |

Each entry of `dataMap()` is an `eZContentObjectAttribute`: its value is `attribute( 'content' )` (the datatype
decides what that is), `hasContent()` says whether it is filled, `attribute( 'data_type_string' )` names the datatype.

## A tested example

```php
<?php
$object = eZContentObject::fetchByNodeID( 2 );
if ( $object instanceof eZContentObject && $object->canRead() )
{
    $dataMap = $object->dataMap();
    echo $object->contentClassIdentifier(), ': ', $object->attribute( 'name' ), "\n";
    if ( isset( $dataMap['short_description'] ) && $dataMap['short_description']->hasContent() )
    {
        echo 'has a short description', "\n";
    }
    echo 'main node ', $object->mainNodeID(), "\n";
}
```

Run read-only with the kernel loaded on a demo installation, this printed the class `folder`, the name, and main node
2.

## Access

| Method | Checks |
|---|---|
| `canRead()` | whether the current user may read it |
| `canEdit( ... )` | whether the current user may edit it |
| `checkAccess( $functionName, ... )` | any content policy function, with limitations |

Check access in your own views before showing or changing an object; fetching does not check it. See
[eZUser and access checks](ezuser-and-access.md).

## In templates

The same object is `$node.object` in node templates; its attributes are `$node.data_map.<identifier>` and are
displayed with `attribute_view_gui`. See [Built-in template functions](template-functions-reference.md).

## Related pages

- Previous: [eZPersistentObject](ezpersistentobject.md)
- Next: [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- [eZContentClass and its attributes](ezcontentclass.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
