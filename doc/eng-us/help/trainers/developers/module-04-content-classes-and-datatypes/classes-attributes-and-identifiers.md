---
title: "Lesson 4.1: Classes, attributes and identifiers"
audience: trainer, developer
category: training
order: 41
summary: Learners read the content model as a developer, create a small class in Setup > Classes, and list a class's attributes, datatypes and required fields from PHP with eZContentClass. 50 minutes.
keywords: developers course, content class, class identifier, attribute identifier, datatype, class group, eZContentClass, fetchByIdentifier, dataMap, object name pattern, Setup Classes
type: help
---
# Lesson 4.1: Classes, attributes and identifiers

Templates and code never name a class by its title but by its **identifier**: `article`, `folder`, `title`, `body`.
In this lesson learners see the content model from a developer's side: classes and their attributes, the datatype of
each attribute, and the identifiers that templates, fetches and PHP code use. They create a small class of their own
and read it back from PHP.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 4: Content classes and datatypes](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Module 2](../module-02-the-template-language/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- explain class, class group, attribute, datatype, object and node in one sentence each;
- create a class with two attributes in **Setup** > **Classes**;
- find a class's identifier and its attributes' identifiers and datatypes;
- list them from PHP with `eZContentClass::fetchByIdentifier()` and `dataMap()`;
- explain why identifiers, not names or IDs, belong in templates and code.

## Trainer's notes

### How to present it

Source: [The content model and editing content, parts 1 to 3](../../../../guides/content-model-and-editing.md#1-how-the-content-model-fits-together).

1. **The model** (5 minutes). Project the table:

   | Term | Meaning |
   |---|---|
   | Content class | A type of content: a name, an identifier and a list of attributes |
   | Class group | A folder of classes in the class editor (Content, Users, Media, Setup) |
   | Datatype | The kind of one attribute: `ezstring`, `eztext`, `ezxmltext`, `ezimage`, `ezobjectrelationlist` ... |
   | Content object | One piece of content, an instance of a class, with versions and languages |
   | Node | A place of an object in the content tree; one object can have several |

2. **Identifiers** (3 minutes). IDs differ between installations; identifiers travel with packages and code. A
   template writes `$node.data_map.title`, an override `Match[class_identifier]=article`.
3. **The class editor** (4 minutes). **Setup** > **Classes** lists the class groups; open **Content** and show
   **New class**, **Edit**, **Copy** and **Remove selected**. Open the **Folder** class and read its attributes.
4. **From PHP** (3 minutes). Show the script of the exercise; `dataMap()` returns the class attributes by
   identifier.

### Common questions

**Can I rename an identifier later?** You can, but every template, override, fetch and script that uses it must
change too. Choose identifiers carefully at the start.

**How many datatypes are there?** The shipped `settings/content.ini [DataTypeSettings] AvailableDataTypes[]` lists
35; extensions add their own (lesson 4.4).

**Where do the classes of a site package come from?** From the package it installs. The Multisite and site
packages course covers packages in depth; [Packages](../../../../guides/packages.md) is the reference.

### Pitfalls

- **Editing a class with many objects.** Adding an attribute to a class with thousands of objects updates every
  object. On a training installation it is quick; on a live site plan it.
- **Changing a datatype.** An attribute's datatype cannot simply be changed; add a new attribute and move the data.
- **Learners sharing an installation.** Each learner names their class with their own initials, for example
  `note_ab`, so classes do not collide.

## Hands-on exercise

On the training installation, signed in to the administration as an administrator.

1. Open **Setup** > **Classes** and the group **Content**.
2. Open **Folder** and write down the identifiers and datatypes of its attributes.
3. Back in the group, press **New class**. Name it `Note`, identifier `note` (on a shared installation add your
   initials). Set the **Object name pattern** to `<title>`.
4. Add an attribute of the datatype **Text line**, name `Title`, identifier `title`, and tick **Required**.
5. Add an attribute of the datatype **Text block**, name `Body`, identifier `body`.
6. Press **OK**. The class appears in the list.
7. Create `extension/mysite_hello/scripts/classinfo.php`:

   ```php
   <?php
   // List the attributes of a class: php bin/php/ezexec.php extension/mysite_hello/scripts/classinfo.php
   $cli = eZCLI::instance();
   $class = eZContentClass::fetchByIdentifier( 'note' );
   if ( !$class instanceof eZContentClass )
   {
       $cli->error( 'No class with the identifier note' );
       return;
   }
   $cli->output( $class->attribute( 'name' ) . ' (' . $class->attribute( 'identifier' ) . '), ID ' . $class->attribute( 'id' ) );
   foreach ( $class->dataMap() as $identifier => $classAttribute )
   {
       $cli->output( sprintf( '  %-20s %-22s %s',
           $identifier,
           $classAttribute->attribute( 'data_type_string' ),
           $classAttribute->attribute( 'is_required' ) ? 'required' : '' ) );
   }
   $cli->output( 'Objects of this class: ' . $class->objectCount() );
   ```

8. Check and run it: `php -l` on the file, then `php bin/php/ezexec.php extension/mysite_hello/scripts/classinfo.php`.
9. Change `note` to `article` in the script and run it again.
10. Create one **Note** in your course folder in the administration and run the script once more.
11. Commit the script.

## Expected result

- Step 8 prints `Note (note)`, its ID, `title ezstring required` and `body eztext`, and `Objects of this class: 0`.
- Step 9 lists the article's attributes, for example `title`, `short_title`, `author`, `intro`, `body`, with their
  datatypes.
- Step 10: the object count is 1.
- The trainer checks the class in one learner's **Setup** > **Classes** list.

## Quiz

1. Why do templates use `class_identifier` and not the class ID?
2. Which method returns a class's attributes by identifier?
   A. `attribute( 'name' )` B. `dataMap()` C. `objectCount()` D. `fetchByIdentifier()`
3. What is the difference between a content object and a node?
4. Where is the list of datatypes that can be chosen for new attributes?
5. True or false: changing an attribute's identifier is harmless once templates use it.

<details>
<summary>Answers</summary>

1. IDs differ between installations; identifiers stay the same in packages, templates and code.
2. **B.** `eZContentClass::dataMap()`.
3. The object is the content; a node is one of its places in the tree.
4. `settings/content.ini`, `[DataTypeSettings] AvailableDataTypes[]`, plus what extensions add.
5. **False.** Every template, override and script that uses it must change too.

</details>

## Further reading

- [The content model and editing content](../../../../guides/content-model-and-editing.md): classes, objects,
  versions and the editor.
- [Class groups](../../../../guides/class-groups.md): organizing classes.
- [Bundling content class packages](../../../designers/extensions/bundling-content-class-packages.md): shipping
  classes with an extension.

## Related pages

- [Module 4: Content classes and datatypes](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
