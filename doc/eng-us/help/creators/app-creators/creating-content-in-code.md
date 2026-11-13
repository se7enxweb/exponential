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

Since Exponential 6.0.15 there is a second way that is often simpler: `expContentWriter` takes rich text as HTML and
files as public web addresses, and tells you whether the content was published, is waiting for a workflow or was
refused and why ([From plain values: expContentWriter](#from-plain-values-expcontentwriter)).

## Contents

1. [Creating and publishing](#creating-and-publishing)
2. [Updating](#updating)
3. [Values for each datatype](#values-for-each-datatype)
4. [Idempotent imports with remote IDs](#idempotent-imports-with-remote-ids)
5. [Moving, hiding, removing](#moving-hiding-removing)
6. [Large operations](#large-operations)
7. [From plain values: expContentWriter](#from-plain-values-expcontentwriter)

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

## From plain values: expContentWriter

```php
<?php
$result = expContentWriter::create( array(
    'parentNodeID'    => $parentNodeID,
    'classIdentifier' => 'article',
    'languageLocale'  => 'eng-US',
    'remoteID'        => 'myapp-article-' . $externalID,
    'attributes'      => array(
        'title' => $title,
        'intro' => '<p>' . htmlspecialchars( $teaser ) . '</p>',            // rich text as HTML
        'image' => 'https://cdn.example.org/a/' . $externalID . '.jpg|' . $title, // downloaded, with its alternative text
    ),
) );
if ( $result['status'] === 'invalid' || $result['status'] === 'failed' )
    eZDebug::writeError( $result['message'], 'myapp import' );

if ( $result['object'] instanceof eZContentObject )
    $result = expContentWriter::update( $result['object'], array( 'title' => $newTitle ), 'eng-US' ); // a new version
```

`update()` changes only the attributes given and publishes a new version; a language the object does not have yet
adds a translation. `remove( $object, true )` moves the object to the trash. The writer does not check permissions; check
them first when the code acts for a user. Everything about it: [Writing content from PHP with expContentWriter](../../../guides/writing-content-from-php.md).
The REST API uses the same writer, so an app can update content over HTTP
([REST API v1 and v2 for applications](rest-api.md)).

## Related pages

- [The content API in PHP](the-content-api.md)
- [Content from plain values: expContentWriter](../../../features/6.0/content-writer.md)
- [CLI scripts with eZScript](cli-scripts.md)
- [Workflows, triggers and events](workflows-and-events.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
