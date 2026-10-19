---
title: The datatype life cycle
audience: app-creator, developer
category: app-creation
order: 250
summary: When the kernel calls each eZDataType method - class attribute editing, object creation, edit and validation, store and publish, copy and translation, trash and removal, search, sorting, string and package serialization, information collection.
keywords: datatype life cycle, eZDataType methods, initializeObjectAttribute, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, storeObjectAttribute, onPublish, deleteStoredObjectAttribute, toString, fromString, metaData, sortKey, serialize, app creator
type: help
---
# The datatype life cycle

This page is for application creators who write or maintain a datatype. It lists the methods of `eZDataType`
(`kernel/classes/ezdatatype.php`) by the moment the kernel calls them, so you know which ones your datatype must
implement ([Writing a custom datatype](custom-datatypes.md)).

## Contents

1. [Class attribute: the field's settings](#class-attribute-the-fields-settings)
2. [Object attribute: create and edit](#object-attribute-create-and-edit)
3. [Store and publish](#store-and-publish)
4. [Copies, versions and translations](#copies-versions-and-translations)
5. [Trash and removal](#trash-and-removal)
6. [Search, sorting and names](#search-sorting-and-names)
7. [Strings and packages](#strings-and-packages)
8. [Information collection and the shop](#information-collection-and-the-shop)

## Class attribute: the field's settings

| Method | Called when |
|---|---|
| `initializeClassAttribute()` | an attribute of the type is added to a class |
| `validateClassAttributeHTTPInput()`, `fetchClassAttributeHTTPInput()` | the class edit form is posted |
| `customClassAttributeHTTPAction()` | a custom button of the class edit form is pressed |
| `preStoreClassAttribute()`, `storeClassAttribute()`, `storeDefinedClassAttribute()` | the class is stored and applied |
| `classAttributeContent()` | `$class_attribute.content` is read |
| `isClassAttributeRemovable()` | before the attribute is removed from the class |
| `deleteStoredClassAttribute()` | the attribute is removed from the class |

## Object attribute: create and edit

| Method | Called when |
|---|---|
| `initializeObjectAttribute()` | a new object or version gets the attribute (copy values from the previous version here) |
| `postInitializeObjectAttribute()` | after initialization |
| `validateObjectAttributeHTTPInput()` | the edit form is posted: return `eZInputValidator::STATE_ACCEPTED` or `STATE_INVALID` with `setValidationError()` |
| `fixupObjectAttributeHTTPInput()` | to correct input before fetching |
| `fetchObjectAttributeHTTPInput()` | to put the posted value into the attribute; return `true` when it changed |
| `customObjectAttributeHTTPAction()` | a custom button of the edit form (upload, remove image ...) |
| `objectAttributeContent()`, `hasObjectAttributeContent()` | templates read `.content`, `.has_content` |

## Store and publish

| Method | Called when |
|---|---|
| `storeObjectAttribute()` | the attribute is stored (every save of the draft) |
| `postStore()` | after storing |
| `onPublish()` | the version is published: the moment for work that must only happen for published data |

## Copies, versions and translations

`initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )` is also called for a new
version, a copy and a new translation: copy what the original holds, including data in your own tables.
`isTranslatable()` (a datatype property) decides whether each language has its own value.

## Trash and removal

| Method | Called when |
|---|---|
| `trashStoredObjectAttribute()` | the object goes to the trash |
| `restoreTrashedObjectAttribute()` | it is restored |
| `deleteStoredObjectAttribute( $objectAttribute, $version = null )` | a version or the whole object is removed: delete your own rows and files here |

## Search, sorting and names

| Method | Purpose |
|---|---|
| `isIndexable()`, `metaData()` | whether and what text goes into the search index |
| `sortKey()`, `sortKeyType()` | the value and type (`int` or `string`) used to sort by the attribute |
| `title()` | the attribute's part of the object name pattern |
| `isRelationType()`, `removeRelatedObjectItem()`, `fixRelatedObjectItem()` | relation datatypes |
| `diff()` | the version comparison view |

## Strings and packages

| Method | Purpose |
|---|---|
| `toString()`, `fromString()` | a plain string form, used by imports, CSV, REST and `eZContentFunctions` |
| `serializeContentClassAttribute()`, `unserializeContentClassAttribute()` | the attribute's settings in packages |
| `serializeContentObjectAttribute()`, `unserializeContentObjectAttribute()`, `postUnserializeContentObjectAttribute()` | values in content packages |
| `isSimpleStringInsertionSupported()`, `insertSimpleString()`, `insertHTTPFile()`, `insertRegularFile()` | uploads and WebDAV |

Make `fromString( toString( x ) )` give `x` back; test it.

## Information collection and the shop

| Method | Purpose |
|---|---|
| `isInformationCollector()`, `hasInformationCollection()` | the attribute can be filled in by visitors |
| `validateCollectionAttributeHTTPInput()`, `fetchCollectionAttributeHTTPInput()` | the visitor's form value |
| `isAddToBasketValidationRequired()`, `validateAddToBasket()`, `productOptionInformation()` | product options |

## Related pages

- [Writing a custom datatype](custom-datatypes.md)
- [Datatype pattern](../../../standards/patterns/datatype.md)
- [Creating and updating content in code](creating-content-in-code.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
