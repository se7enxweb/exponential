---
title: Creating and updating content in code
audience: app-creator, developer
category: app-creation
order: 200
summary: Create, update, move and remove content from PHP - eZContentFunctions::createAndPublishObject and updateAndPublishObject, fromString values, rich text, images, remote ids for idempotent imports, workflows and transactions, and large jobs.
keywords: create content, update content, eZContentFunctions, createAndPublishObject, updateAndPublishObject, fromString, import, remote_id, publish, content jobs, PHP, app creator
type: help
---
# Creating and updating content in code

This page is for application creators who write content from code: imports, synchronizations, forms that create
objects, migrations. The kernel's `eZContentFunctions` (`kernel/classes/ezcontentfunctions.php`) creates and publishes in
one call, with the same operation (and triggers) as the editor.

## Contents

1. [Creating and publishing](#creating-and-publishing)
2. [Updating](#updating)
3. [Values for each datatype](#values-for-each-datatype)
4. [Idempotent imports with remote IDs](#idempotent-imports-with-remote-ids)
5. [Moving, hiding, removing](#moving-hiding-removing)
6. [Large operations](#large-operations)

## Creating and publishing

```php
$object = eZContentFunctions::createAndPublishObject( array(
    'creator_id'       => $userID,
    'class_identifier' => 'article',
    'parent_node_id'   => $parentNodeID,
    'remote_id'        => 'feed-item-' . $externalID,
    'attributes'       => array(
        'title' => 'A new article',
        'intro' => $introXml,
        'body'  => $bodyXml ) ) );

if ( !$object instanceof eZContentObject )
    throw new RuntimeException( 'Could not create the article' );
```

Attribute values are given in their `fromString()` form. Publishing runs the `content/publish` operation, so workflows
on its triggers run too, and caches and the search index are updated.

## Updating

```php
eZContentFunctions::updateAndPublishObject( $object, array(
    'attributes' => array( 'title' => 'A better title' ) ) );
```

A new version is created and published; the previous one becomes archived, like an editor's edit.

## Values for each datatype

| Datatype | String form |
|---|---|
| `ezstring`, `eztext` | the text |
| `ezinteger`, `ezfloat`, `ezboolean` | `42`, `3.5`, `1` |
| `ezdate`, `ezdatetime` | a Unix timestamp |
| `ezxmltext` | the internal XML of rich text (the `<section>` document) |
| `ezobjectrelationlist` | object IDs separated by `-` |
| `ezselection` | the selected option IDs |
| `ezimage`, `ezbinaryfile` | a file path (and for images an alternative text after a separator) |

The exact format of each is in its datatype class's `fromString()` in `kernel/classes/datatypes/<name>/`; read it before
importing a datatype for the first time.

## Idempotent imports with remote IDs

Give imported objects a remote ID built from the source's ID. On the next run, update instead of create:

```php
$remoteID = 'feed-item-' . $externalID;
$existing = eZContentObject::fetchByRemoteID( $remoteID );
if ( $existing )
    eZContentFunctions::updateAndPublishObject( $existing, array( 'attributes' => $attributes ) );
else
    eZContentFunctions::createAndPublishObject( array( 'remote_id' => $remoteID ) + $params );
```

Running an import twice must not create duplicates.

## Moving, hiding, removing

Use the kernel operations rather than SQL: `eZContentObjectTreeNodeOperations::move()`, `eZContentOperationCollection`
for hide and reveal, `eZContentObjectTreeNode::removeSubtrees()` for removal (to the trash or not). They keep URL aliases,
caches and the search index right.

## Large operations

Thousands of objects belong in a background process: a command run from cron, a queue job
([Queues with expqueue](queues-with-expqueue.md)), or the kernel's content jobs for removes, copies and moves of large
subtrees ([Content jobs](content-jobs.md)). Commit in batches, clear `eZContentObject::clearCache()` between batches,
and log progress.

## Related pages

- [The content API in PHP](the-content-api.md)
- [CLI scripts with eZScript](cli-scripts.md)
- [Workflows, triggers and events](workflows-and-events.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
