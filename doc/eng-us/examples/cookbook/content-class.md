---
title: Create a content class
audience: developer, admin
category: examples
order: 20
summary: Create the content class Note with a title and a body, once by hand in the administration and once by a re-runnable PHP script using eZContentClass and eZContentClassAttribute.
keywords: content class, class, attribute, datatype, ezstring, eztext, eZContentClass, eZContentClassAttribute, eZContentClassClassGroup, ezexec, class group, identifier
type: example
---
# Create a content class

A content class is the type of a content object: its fields (attributes), each of a datatype, and how its objects
are named. You create the class **Note** (identifier `note`) with a required **Title** (Text line, `ezstring`) and a
**Body** (Text block, `eztext`): first by hand in the administration, then with a script you can keep in an extension
and run on every installation that needs the class.

Prerequisites: an administrator login with the `class` policies; for the script, a shell in the installation root.

## 1. By hand in the administration

1. Open **Setup > Classes** (`/class/grouplist`). The page lists the class groups.
2. Open the group **Content** and press **New class**.
3. **Name** `Note`, **Identifier** `note`, **Object name pattern** `<title>`.
4. Under **Add attribute** choose *Text line* and press **Add**: name `Title`, identifier `title`, tick
   **Required**. Add a *Text block*: name `Body`, identifier `body`.
5. Press **OK**. The class appears in the group; `/class/view/<id>` shows it with two attributes.

The identifiers are what templates and code use (`$node.data_map.title`), so choose them once: lower case, letters,
digits and underscores.

## 2. By a script

Keep the script in your extension, for example `extension/expexample/scripts/create_note_class.php`. It looks the class
up by its identifier first, so running it twice changes nothing:

```php
<?php
/**
 * Creates the content class "Note" (identifier note) with a title and a body, in the class group "Content".
 * Run it from the installation root with the kernel loaded:
 *
 *   php bin/php/ezexec.php extension/expexample/scripts/create_note_class.php
 *
 * Running it a second time changes nothing: the class is looked up by its identifier first.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$identifier = 'note';
$language   = 'eng-US';

if ( eZContentClass::fetchByIdentifier( $identifier ) instanceof eZContentClass )
{
    echo "The class $identifier exists already, nothing to do\n";
    return;
}

$group = eZContentClassGroup::fetchByName( 'Content' );
if ( !$group instanceof eZContentClassGroup )
{
    echo "The class group Content does not exist\n";
    return;
}

// The administrator (user id 14 in a new installation) is the creator, as the command line has no signed-in user
$creatorID = 14;

$db = eZDB::instance();
$db->begin();

$class = eZContentClass::create( $creatorID, array(
    'version'            => eZContentClass::VERSION_STATUS_DEFINED,
    'identifier'         => $identifier,
    'remote_id'          => 'expexample-class-note',
    'contentobject_name' => '<title>',
    'is_container'       => 0,
    'always_available'   => 0,
    'name'               => 'Note',
    'description'        => 'A short note with a title and a body',
), $language );
$class->store();

$attributes = array(
    // identifier => array( datatype, name, required, searchable, translatable, extra columns )
    'title' => array( 'ezstring', 'Title', 1, 1, 1, array( 'data_int1' => 255 ) ),   // data_int1: maximum length
    'body'  => array( 'eztext',   'Body',  0, 1, 1, array( 'data_int1' => 10 ) ),    // data_int1: rows of the field
);
$placement = 0;
foreach ( $attributes as $attributeIdentifier => $definition )
{
    list( $dataTypeString, $name, $required, $searchable, $translatable, $extra ) = $definition;
    $attribute = eZContentClassAttribute::create( (int)$class->attribute( 'id' ), $dataTypeString, array_merge( array(
        'version'                  => eZContentClass::VERSION_STATUS_DEFINED,
        'identifier'               => $attributeIdentifier,
        'name'                     => $name,
        'placement'                => ++$placement,
        'is_required'              => $required,
        'is_searchable'            => $searchable,
        'can_translate'            => $translatable,
        'is_information_collector' => 0,
    ), $extra ), $language );
    $attribute->dataType()->initializeClassAttribute( $attribute );
    $attribute->store();
}

eZContentClassClassGroup::create( $class->attribute( 'id' ), eZContentClass::VERSION_STATUS_DEFINED,
                                  $group->attribute( 'id' ), $group->attribute( 'name' ) )->store();
$db->commit();

eZContentClass::expireCache();
eZContentClassAttribute::expireCache();

echo "Created the class $identifier (id " . $class->attribute( 'id' ) . ")\n";
```

What each call does:

| Call | Does |
|---|---|
| `eZContentClass::fetchByIdentifier( $identifier )` | the defined (published) class with that identifier, or `null` |
| `eZContentClass::create( $userID, $values, $locale )` | a new class object, not stored yet; `name` and `description` are stored for `$locale` |
| `eZContentClassAttribute::create( $classID, $datatype, $values, $locale )` | a new attribute; `data_int1` ... `data_text5` are the datatype's own class settings |
| `$attribute->dataType()->initializeClassAttribute( $attribute )` | lets the datatype set its defaults |
| `eZContentClassClassGroup::create( ... )->store()` | puts the class in a class group |
| `eZContentClass::expireCache()` | the class caches of every process see the new class |

Writing `version` as `VERSION_STATUS_DEFINED` stores the class directly as published. A class that already has
objects is changed through the class edit (a temporary version, then *Apply*), never by writing its defined version
underneath them: adding an attribute to such a class also needs `$attribute->initializeObjectAttributes()`.

Run it:

```bash
php -l extension/expexample/scripts/create_note_class.php
php bin/php/ezexec.php extension/expexample/scripts/create_note_class.php --allow-root-user
```

Expected: `Created the class note (id <n>)`; a second run prints `The class note exists already, nothing to do`.

## 3. Check it

- **Setup > Classes > Content** lists *Note*; its page shows the two attributes in order.
- The object name pattern works: create a Note with the title *Shopping list*; it is named *Shopping list*.
- In a template: `{$node.data_map.title.content|wash}` and `{attribute_view_gui attribute=$node.data_map.body}`.

Checked for this page (2026-10-18): `php -l` on the script; every method it calls exists with this signature in
`kernel/classes/ezcontentclass.php`, `ezcontentclassattribute.php`, `ezcontentclassgroup.php`,
`ezcontentclassclassgroup.php` and `ezdatatype.php`. The script was not run against a database for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| The class shows no name in another language | `name` was stored for `$locale` only; add translations on the class edit page |
| `fetchByName( 'Content' )` finds nothing | the group was renamed; use its id with `eZContentClassGroup::fetch( <id> )` |
| The new class does not appear in the *Create here* menu | the role of the user lacks `content/create` for the class, or the parent's class limits the classes allowed below it |
| A changed attribute is not seen at once | the class caches: `eZContentClass::expireCache()`, or `php bin/php/ezcache.php --clear-id=classid --allow-root-user` |

## Derived from

- `kernel/classes/ezcontentclass.php` (`create()`, `fetchByIdentifier()`, `expireCache()`)
- `kernel/classes/ezcontentclassattribute.php` (`create()`, `dataType()`, `initializeObjectAttributes()`)
- `kernel/classes/help/content/exphelpcontentinstaller.php` (`ensureClass()`, `createAttribute()`: the kernel's own
  re-runnable class creation, which this script follows)
- `doc/eng-us/guides/content-model-and-editing.md`, section 2

## Related pages

- [Cookbook](README.md)
- [Content model and editing](../../guides/content-model-and-editing.md) and [Class groups](../../guides/class-groups.md)
- [Datatypes](../datatypes/README.md): every datatype and its template attributes
- [A custom datatype](custom-datatype.md) and [A template override](template-override.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
