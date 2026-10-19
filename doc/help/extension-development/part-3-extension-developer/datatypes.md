---
title: Datatypes
audience: developer, app-creator
category: extension-development
order: 160
summary: A new kind of field for content classes: when to write one, how the kernel finds and registers it, the eZDataType life cycle, a verified rating datatype, and real ones to learn from.
keywords: datatype, eZDataType, AvailableDataTypes, ExtensionDirectories, DataTypeSettings, register, DATA_TYPE_STRING, content class attribute, field type, data_int, data_text, birthday
type: help
---
# Datatypes

Part 3, chapter 16. A **datatype** defines a kind of field editors can add to a content class: its edit form, its
validation, how it is stored, how it is shown, searched and sorted. The kernel ships dozens (`ezstring`, `ezimage`,
`ezxmltext` ...); extensions add more (`eztags`, `birthday`, `ngclasslist`). This chapter explains the contract with
a small rating datatype that was checked with `php -l` and exercised with the kernel loaded; the next chapter covers
storage, validation and templates in depth.

## Do you need one?

Write a datatype when editors must enter a kind of value no existing datatype handles **and** the value belongs to the
content (versioned, translated, searchable). Before you write one, check the list of available datatypes when you add
an attribute to a class, and the [catalogue](../part-1-from-user-to-developer/the-se7enxweb-extension-catalogue.md).
A selection, a number with limits, a relation to other content: all exist already.

## How the kernel finds it

In `extension/mysite_hello/settings/content.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
ExtensionDirectories[]=mysite_hello
AvailableDataTypes[]=mysiterating

*/ ?>
```

- `ExtensionDirectories[]` makes the kernel look in `extension/mysite_hello/datatypes/`.
- `AvailableDataTypes[]` offers the datatype in the class editor.
- The file must be `datatypes/mysiterating/mysiteratingtype.php`: `eZDataType::loadAndRegisterType()`
  (`kernel/classes/ezdatatype.php`) includes `<directory>/<type>/<type>type.php`.
- Templates live in your design, so the extension also needs `DesignExtensions[]` in `design.ini.append.php`.

The real `birthday` extension does exactly this (its `settings/content.ini.append` names `ExtensionDirectories[]=birthday`
and `AvailableDataTypes[]=ezbirthday`).

## The class

```php
<?php
/**
 * A rating from 1 to 5, stored in data_int of the object attribute.
 */
class mysiteRatingType extends eZDataType
{
    const DATA_TYPE_STRING = 'mysiterating';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING,
                             ezpI18n::tr( 'extension/mysite_hello', 'Rating (1 to 5)', 'Datatype name' ),
                             array( 'serialize_supported' => true ) );
    }

    // validateObjectAttributeHTTPInput(), fetchObjectAttributeHTTPInput(), objectAttributeContent() ...
    // the complete class is in the next chapter
}

eZDataType::register( mysiteRatingType::DATA_TYPE_STRING, 'mysiteRatingType' );
```

| Part | Meaning |
|---|---|
| `DATA_TYPE_STRING` | the identifier stored in the database for every attribute of this type; never change it once used |
| `parent::__construct( $dataTypeString, $name, $properties )` | registers the identifier, the name shown in the class editor (translated), and properties |
| `'serialize_supported' => true` | the type can be exported in packages |
| `eZDataType::register()` at the end of the file | tells the kernel which class implements the string |

With the kernel loaded, `eZDataType::create( 'mysiterating' )` returned an object of the class with the name
"Rating (1 to 5)".

## The life cycle

| When | Methods the kernel calls |
|---|---|
| An editor adds the attribute to a class | `initializeClassAttribute()`, `fetchClassAttributeHTTPInput()`, `validateClassAttributeHTTPInput()`, `storeClassAttribute()` |
| An editor creates or edits content | `initializeObjectAttribute()`, `validateObjectAttributeHTTPInput()`, `fetchObjectAttributeHTTPInput()`, `storeObjectAttribute()` |
| The content is published | `onPublish()` |
| A template shows it | `objectAttributeContent()` (the `.content` in templates), `hasObjectAttributeContent()` (`.has_content`) |
| Search, sorting, names | `metaData()`, `isIndexable()`, `sortKey()`, `sortKeyType()`, `title()` |
| Import and export | `toString()`, `fromString()`, `serializeContentObjectAttribute()`, `unserializeContentObjectAttribute()` |
| Removal | `deleteStoredObjectAttribute()`, `deleteStoredClassAttribute()` |

Every method has a default in `eZDataType`; override only what your type needs. The full list with signatures is
The eZDataType API.

## Learn from real ones

| Datatype | Shows |
|---|---|
| `extension/birthday` (`ezbirthday`) | a date stored in `data_text`, three edit fields, validation, information collection |
| `kernel/classes/datatypes/ezinteger/` | the simplest number type in the kernel |
| `extension/ngclasslist` | a list stored as text |
| `extension/eztags` | a datatype with its own tables and modules |

## RAD

**Datatype extension** (`/setup/datatype`) writes the class, the settings and the four templates; see
[With RAD: a datatype](tutorials/rad-datatype.md). The hand-written version is
[By hand: a datatype](tutorials/by-hand-datatype.md).

## Related pages

- Previous: [Template functions](template-functions.md)
- Next: [Datatype storage, validation and templates](datatype-storage-validation-and-templates.md)
- [Datatype input hardening (specification)](../../../specifications/6.0/datatype-input-hardening.md)
- [eZpedia: Datatype](https://ezpedia.exponential.earth/en/learning/datatype)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
