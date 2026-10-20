---
title: "Lesson 4.3: Reading and writing content from PHP"
audience: trainer, developer
category: training
order: 43
summary: Learners read objects, nodes and attributes from PHP, use toString and fromString, and create and update a content object with eZContentFunctions on their training installation. 55 minutes.
keywords: developers course, eZContentObject, eZContentObjectTreeNode, dataMap, data_map, toString, fromString, eZContentFunctions, createAndPublishObject, updateAndPublishObject, remote_id, creator_id, import script
type: help
---
# Lesson 4.3: Reading and writing content from PHP

Imports, migrations and scheduled jobs read and write content from PHP. The kernel has a small, safe API for it:
fetch an object or node, read its attributes by identifier, and create or update an object through the same publish
operation the edit form uses. In this lesson learners write a script that reads a note, creates a new one and
updates it.

> **Note**
> This script writes content. Run it only on the training installation.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 4: Content classes and datatypes](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 4.1](classes-attributes-and-identifiers.md): the **Note** class |

## Learning objectives

After this lesson, learners can:

- fetch a node with `eZContentObjectTreeNode::fetch()` and its object with `object()`;
- read attributes by identifier with `dataMap()` and `toString()`;
- create and publish an object with `eZContentFunctions::createAndPublishObject()`;
- update an object with `eZContentFunctions::updateAndPublishObject()`;
- use a `remote_id` so a script can find its own objects again.

## Trainer's notes

### How to present it

Source: the documentation comments of `kernel/classes/ezcontentfunctions.php`.

1. **Reading** (3 minutes). A node gives its object with `$node->object()`; `$object->dataMap()` returns the
   attributes by identifier, like `data_map` in a template. `toString()` returns a datatype's value as a string.
2. **`createAndPublishObject`** (5 minutes). Its parameters are `parent_node_id`, `class_identifier`, `creator_id`,
   `attributes` (identifier to value), and optionally `remote_id`, `section_id` and `storage_dir`. Values are strings
   in the format of each datatype's `fromString()`; read that method when in doubt. It returns the object or `false`.
3. **`updateAndPublishObject`** (3 minutes). Takes an object and `attributes` (and optionally `language`), makes a
   new version and publishes it. A missing or empty attribute list is refused before anything changes.
4. **Notifications** (2 minutes). Both methods take a last argument `$notify`; `false` publishes without
   notification events, like "Publish without notification" in the edit form.
5. **`remote_id`** (2 minutes). A unique string you choose. `eZContentObject::fetchByRemoteID()` finds the object
   again, so an import can update instead of duplicating.

### Common questions

**Which user publishes?** The `creator_id` you give (the content object ID of a user), otherwise the current user.
In a script the current user is anonymous; give an editor's or administrator's ID.

**How do I write rich text?** As the XML the rich text datatype stores; the example in the comment of
`updateAndPublishObject()` shows a `<section>` with `<paragraph>`. Plain text attributes (`ezstring`, `eztext`)
take plain strings.

**Are permissions checked?** The API publishes what you give it; the script is trusted code. Check permissions
yourself when a script acts for a user.

### Pitfalls

- **Running the script twice.** Without a `remote_id` check, the second run creates a second object. Fetch by
  remote ID first.
- **Wrong parent.** `parent_node_id` is a node ID, not an object ID.
- **Huge loops.** Thousands of objects in one run need memory care (clearing the in-memory object cache between
  batches) and are better done as content jobs or in batches; start small.

## Hands-on exercise

On the training installation, with the **Note** class and your course folder. `<folder node ID>` is the node ID of
your course folder (shown in the administration).

1. Create `extension/mysite_hello/scripts/notes.php`:

   ```php
   <?php
   // Create or update a note: php bin/php/ezexec.php extension/mysite_hello/scripts/notes.php
   $cli = eZCLI::instance();
   $parentNodeID = 0; // the node ID of your course folder
   $remoteID = 'mysite_hello_note_1';

   $admin = eZUser::fetchByName( 'admin' );
   $creatorID = $admin instanceof eZUser ? $admin->attribute( 'contentobject_id' ) : false;

   $object = eZContentObject::fetchByRemoteID( $remoteID );
   if ( !$object instanceof eZContentObject )
   {
       $object = eZContentFunctions::createAndPublishObject( array(
           'parent_node_id'   => $parentNodeID,
           'class_identifier' => 'note',
           'creator_id'       => $creatorID,
           'remote_id'        => $remoteID,
           'attributes'       => array( 'title' => 'Created by a script',
                                        'body'  => 'First version.' ) ) );
       $cli->output( $object ? 'Created object ' . $object->attribute( 'id' ) : 'Create failed' );
   }
   else
   {
       $ok = eZContentFunctions::updateAndPublishObject( $object, array(
           'attributes' => array( 'body' => 'Updated at ' . date( 'H:i:s' ) ) ) );
       $cli->output( $ok ? 'Updated object ' . $object->attribute( 'id' ) : 'Update failed' );
   }

   $object = eZContentObject::fetchByRemoteID( $remoteID );
   if ( $object instanceof eZContentObject )
   {
       $dataMap = $object->dataMap();
       $cli->output( 'Title: ' . $dataMap['title']->toString() );
       $cli->output( 'Body: ' . $dataMap['body']->toString() );
       $cli->output( 'Version: ' . $object->attribute( 'current_version' ) );
   }
   ```

2. Set `$parentNodeID` to your course folder's node ID.
3. Check it with `php -l`, then run it: `php bin/php/ezexec.php extension/mysite_hello/scripts/notes.php`.
4. Find the new note in your course folder in the administration.
5. Run the script again.
6. Open the note's **Manage versions** page (or its version list) and count the versions.
7. Open the note's page in the administration and check **Owner** or the version list: the creator is the
   administrator, not the anonymous user.
8. Commit the script.

## Expected result

- Step 3 prints `Created object <ID>`, the title, `First version.` and `Version: 1`.
- Step 5 prints `Updated object <ID>`, the new body and `Version: 2`.
- Step 6: the note has two versions.
- The trainer checks one learner's note in the administration.

## Quiz

1. Which method returns an object's attributes by identifier?
2. Which parameter lets a script find its own object again?
   A. `section_id` B. `remote_id` C. `storage_dir` D. `creator_id`
3. What does `createAndPublishObject` return when it fails?
4. What happens when `updateAndPublishObject` is called with an empty attribute list?
5. Is `parent_node_id` a node ID or an object ID?

<details>
<summary>Answers</summary>

1. `$object->dataMap()`.
2. **B.** `remote_id`, found with `eZContentObject::fetchByRemoteID()`.
3. `false`.
4. It is refused before anything changes.
5. A node ID.

</details>

## Further reading

- [The content model and editing content](../../../../guides/content-model-and-editing.md): versions and drafts.
- [Content jobs](../../../../features/6.0/content-jobs.md): large operations in batches.
- [Publish without notification](../../../../features/6.0/publish-without-notification.md): the `$notify` argument.

## Related pages

- [Module 4: Content classes and datatypes](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
