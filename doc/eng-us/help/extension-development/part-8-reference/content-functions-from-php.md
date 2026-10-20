---
title: Creating, publishing and changing content from PHP
audience: developer
category: extension-development
order: 220
summary: Create, update, move, hide and remove content the supported way - createAndPublishObject(), updateAndPublishObject() and the node operations - so workflows, caches and search stay in step.
keywords: eZContentFunctions, createAndPublishObject, updateAndPublishObject, fromString, publish, move, removeSubtrees, hideSubTree, eZContentObjectTreeNodeOperations, eZContentOperationCollection, import, content from PHP
type: help
---
# Creating, publishing and changing content from PHP

Part 8, reference 18. Importers, integrations and some cronjobs create or change content. Always do that through the
kernel's functions: they create versions, run the publish operation (with its workflows and triggers), clear the
view caches and update the search index. Writing to content tables directly does none of that and leaves the site
inconsistent.

## Creating: eZContentFunctions::createAndPublishObject()

```php
static function createAndPublishObject( $params, $notify = true )
```

`kernel/classes/ezcontentfunctions.php`. The parameters, from its documentation:

| Key | Meaning |
|---|---|
| `parent_node_id` | where the object goes |
| `class_identifier` | the class, by identifier (the ID is not accepted) |
| `creator_id` | the user object ID of the creator; the current user if not set |
| `attributes` | attribute identifier => value, in the format of the datatype's `fromString()` |
| `remote_id` | optional: your own stable ID for the object |
| `section_id` | optional |
| `storage_dir` | optional: a directory for file attributes given by file name |

`$notify = false` publishes without notification events. It returns the published `eZContentObject`, or `false`.

```php
<?php
$params = array(
    'class_identifier' => 'folder',
    'parent_node_id' => 2,
    'remote_id' => 'myext-import-folder-42',
    'attributes' => array(
        'name' => 'Imported items',
        'short_name' => 'Imported',
    ),
);
$object = eZContentFunctions::createAndPublishObject( $params );
if ( !$object instanceof eZContentObject )
{
    eZDebug::writeError( 'Could not create the folder', __METHOD__ );
}
```

Values are strings in each datatype's `fromString()` format: plain text for `ezstring`, `1`/`0` for `ezboolean`,
a timestamp for dates, a file path for images and files, and the stored XML for `ezxmltext`. Read the datatype's
`fromString()` when unsure ([The eZDataType API](ezdatatype-api.md)).

Use `remote_id` for anything you import: the next run can find the object with `eZContentObject::fetchByRemoteID()`
and update it instead of creating a duplicate.

## Updating: updateAndPublishObject()

```php
public static function updateAndPublishObject( eZContentObject $object, array $params, $notify = true )
```

`$params` takes `attributes` (only the ones to change) and optionally `remote_id`, `section_id` and `language`. It
creates a new version, sets the attributes and publishes it. Returns `true` or `false`.

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'myext-import-folder-42' );
if ( $object instanceof eZContentObject )
{
    eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => array( 'short_name' => 'Imports' ) ) );
}
```

## Moving, hiding, removing

| Task | Call |
|---|---|
| Move a node | `eZContentObjectTreeNodeOperations::move( $nodeID, $newParentNodeID )` |
| Hide or show a subtree | `eZContentObjectTreeNode::hideSubTree( $node )`, `unhideSubTree( $node )` |
| Remove nodes (to the trash by default) | `eZContentObjectTreeNode::removeSubtrees( $deleteIDArray, $moveToTrash = true, $infoOnly = false )` |
| The same through the operation layer (with triggers) | `eZContentOperationCollection::moveNode()`, `removeNodes()`, `deleteObject()`, `changeHideStatus()`, `updateSection()` |

`removeSubtrees( ..., $infoOnly = true )` only reports what would be removed; use it for a dry run.

## As which user

Content is created as the current user. In a CLI script that is the anonymous user unless you set one; the
anonymous user usually may not create content. Run imports as a dedicated user with exactly the rights the import
needs, and make that user current in the script, rather than an administrator:

```php
<?php
$user = eZUser::fetchByName( 'importer' );
if ( $user instanceof eZUser )
{
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}
```

## In transactions and loops

`createAndPublishObject()` manages its own transaction. In a loop over many items:

- clear the in-memory object cache now and then (`eZContentObject::clearCache()`), or memory grows with every object;
- write progress with `eZScript::iterate()` ([eZScript and eZCLI](ezscript-and-ezcli.md));
- offer a `--dry-run` that reports what would be created.

## Related pages

- Previous: [The eZWorkflowType API](ezworkflowtype-api.md)
- Next: [ezjscore server functions reference](ezjscore-server-functions-reference.md)
- [eZContentObject](ezcontentobject.md), [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- [Content jobs](../../../bc/6.0/content-jobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
