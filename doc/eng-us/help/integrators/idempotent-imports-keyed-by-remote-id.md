---
title: Idempotent imports keyed by remote_id
audience: integrator, developer
category: integration
order: 280
summary: Making imports safe to run any number of times - the remote_id of objects and nodes, a naming scheme for keys, the create-or-update function with change detection, handling records removed at the source, and what to do when keys collide.
keywords: idempotent, idempotency, remote_id, remote id, fetchByRemoteID, eZContentObject, eZContentObjectTreeNode, createAndPublishObject, updateAndPublishObject, upsert, change detection, duplicate, re-run, sync, key scheme
type: help
---
# Idempotent imports keyed by remote_id

This page is for integration developers. An import is **idempotent** when running it twice gives the same result as
running it once: no duplicates, no empty versions, no lost changes. Exponential gives every content object (and every
node) a **remote id**, a string you may set yourself, and that is the key that makes imports idempotent.

## Contents

1. [The remote id](#the-remote-id)
2. [A key scheme](#a-key-scheme)
3. [Create or update, with change detection](#create-or-update-with-change-detection)
4. [Records removed at the source](#records-removed-at-the-source)
5. [Collisions and repairs](#collisions-and-repairs)
6. [Rules](#rules)

## The remote id

| | Object | Node (location) |
|---|---|---|
| Column | `ezcontentobject.remote_id`, up to 100 characters | `ezcontentobject_tree.remote_id`, up to 100 characters |
| Unique | Yes (unique index on MySQL and PostgreSQL) | Each node gets its own; key your imports on the object's remote id |
| Set when | Generated when empty (`eZRemoteIdUtility::generate()`), or given by you | Generated when the node is made |
| Find by it | `eZContentObject::fetchByRemoteID( $remoteID )`, returns the object or `null` | `eZContentObjectTreeNode::fetchByRemoteID( $remoteID )` |
| Set it | `createAndPublishObject( array( 'remote_id' => ... ) )`, `updateAndPublishObject( $object, array( 'remote_id' => ... ) )`, `expobject::setRemoteId` | |

The object id is a number that differs between installations; the remote id travels: packages carry it, and an
object restored or installed elsewhere keeps it.

## A key scheme

Choose a scheme per integration and write it into the interface specification:

```text
<system>-<record type>-<id at the source>
erp-product-10442
pim-category-shoes
crm-event-2027-0312
```

- Prefix by source system, so two integrations can never collide (the RSS import uses `RSSImport_<import id>_...`).
- Use the source's **stable** id, never a name or a title that can change.
- Stay within letters, digits and `_ . : -`, and within 100 characters (the `expservices` service `expobject::setRemoteId`
  accepts exactly that alphabet).
- For languages, keep one object per record and import each language as a translation, not one object per language.

## Create or update, with change detection

```php
<?php
/**
 * Creates or updates the object for one source record. Returns 'created', 'updated', 'unchanged' or 'failed'.
 * $attributes: attribute identifier => value in the datatype's fromString() format.
 */
function myIntegrationUpsert( $remoteID, $parentNodeID, $classIdentifier, array $attributes, $language = false, $dryRun = false )
{
    $object = eZContentObject::fetchByRemoteID( $remoteID );

    if ( !$object instanceof eZContentObject )
    {
        if ( $dryRun )
            return 'created';
        $params = array( 'class_identifier' => $classIdentifier,
                         'parent_node_id' => $parentNodeID,
                         'remote_id' => $remoteID,
                         'attributes' => $attributes );
        if ( $language )
            $params['language'] = $language;
        $object = eZContentFunctions::createAndPublishObject( $params, false );   // no notifications
        return $object instanceof eZContentObject ? 'created' : 'failed';
    }

    // only the attributes whose value differs, so an unchanged record makes no new version
    $changed = array();
    $dataMap = $object->dataMap();
    foreach ( $attributes as $identifier => $value )
    {
        if ( !isset( $dataMap[$identifier] ) || $dataMap[$identifier]->toString() !== (string)$value )
            $changed[$identifier] = $value;
    }
    if ( !$changed )
        return 'unchanged';
    if ( $dryRun )
        return 'updated';

    $params = array( 'attributes' => $changed );
    if ( $language )
        $params['language'] = $language;
    return eZContentFunctions::updateAndPublishObject( $object, $params, false ) ? 'updated' : 'failed';
}
```

Notes:

- `toString()` and `fromString()` are each datatype's own text format; compare in that format. For rich text
  (`ezxmltext`), images and files the comparison is on the stored representation; compare a checksum of the source
  value that you store in a hidden attribute or your own table when the stored form differs from what you send.
- `createAndPublishObject()` manages its own transaction and runs the publish operation, so workflows, caches, the
  search index and the audit trail stay in step. Write to content tables directly never.
- With a dry run the function reports what it would do; count the results and print them at the end.

## Records removed at the source

Decide per class, with the customer:

| Policy | How | Consequence |
|---|---|---|
| Remove to the trash | `eZContentObjectTreeNode::removeSubtrees( array( $nodeID ), true )` | Restorable for a while; URLs stop answering |
| Hide | `eZContentObjectTreeNode::hideSubTree( $node )` | URL stays for editors; visitors do not see it |
| Mark with an object state | Assign a state such as "withdrawn" | Visible to templates and filters; nothing disappears |
| Keep | Do nothing | The site shows outdated records |

Find removed records in a full run: every object with your prefix whose source id was not in the file. Never remove
when the source file is empty or much smaller than usual; stop and alert instead.

## Collisions and repairs

- **Two objects for one record** (created before remote ids were used): pick the one to keep, give it the remote id
  with `expobject::setRemoteId` or `updateAndPublishObject( $object, array( 'remote_id' => ..., 'attributes' => ... ) )`,
  and remove the other.
- **A remote id already in use by another object**: the create fails on MySQL and PostgreSQL, and
  `expobject::create` answers `409`. Log it with both ids and stop for that record; never overwrite the other object.
- **Changing the key scheme**: write a one-off migration that sets the new remote id on every existing object, with a
  dry run, before the first import with the new scheme.

## Rules

- [ ] Every imported object carries a remote id from the scheme.
- [ ] Imports find before they create.
- [ ] Unchanged records make no new version.
- [ ] Imports publish without notification unless notifications are wanted.
- [ ] Removal policy decided per class; a safety stop for suspiciously small sources.
- [ ] A dry run exists and is used before the first real run on each environment.

## Related pages

- [Creating, publishing and changing content from PHP](../extension-development/part-8-reference/content-functions-from-php.md)
- [Writing an import script](writing-an-import-script.md)
- [Mapping content classes to external data](mapping-content-classes-to-external-data.md)
- [eZContentObject](../extension-development/part-8-reference/ezcontentobject.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
