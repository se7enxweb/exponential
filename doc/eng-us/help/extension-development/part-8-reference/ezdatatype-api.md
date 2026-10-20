---
title: The eZDataType API
audience: developer
category: extension-development
order: 200
summary: Every eZDataType method a datatype overrides, grouped by purpose - registration, editing, storage, class settings, collection, search and sorting, import, packages - with kernel signatures.
keywords: eZDataType, datatype API, register, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, storeObjectAttribute, objectAttributeContent, hasObjectAttributeContent, classAttributeContent, metaData, sortKey, toString, fromString, onPublish
type: help
---
# The eZDataType API

Part 8, reference 16. A datatype is a class that extends `eZDataType` (`kernel/classes/ezdatatype.php`) and overrides
the methods it needs. This page lists them by purpose, with the signatures of the base class (copy them exactly: PHP
8 refuses an override whose signature differs). How to build one is in [Datatypes](../part-3-extension-developer/datatypes.md),
[Datatype storage, validation and templates](../part-3-extension-developer/datatype-storage-validation-and-templates.md)
and the tutorials [A datatype, by hand](../part-3-extension-developer/tutorials/by-hand-datatype.md) and
[With RAD: a datatype](../part-3-extension-developer/tutorials/rad-datatype.md).

## Registration

| Method | Notes |
|---|---|
| `public function __construct( $dataTypeString, $name, $properties = array() )` | call it from your constructor with your datatype string and translated name |
| `static function register( $dataTypeString, $className )` | call once at the end of the file: `eZDataType::register( 'mydatatype', 'myDataTypeType' );` |

Plus `content.ini [DataTypeSettings] ExtensionDirectories[]` and `AvailableDataTypes[]`.

## Editing an object's attribute

| Method | Called when |
|---|---|
| `initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )` | a new version is created: copy or set defaults |
| `validateObjectAttributeHTTPInput( $http, $base, $objectAttribute )` | the edit form is posted; return `eZInputValidator::STATE_ACCEPTED` or `STATE_INVALID` |
| `fetchObjectAttributeHTTPInput( $http, $base, $objectAttribute )` | after validation: read the posted value into the attribute; return `true` if something was read |
| `customObjectAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )` | a custom button of your edit template was pressed |

The posted field names follow the kernel's convention of base, datatype string, field and attribute ID:
`ezstring` reads `$base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' )`. Your edit template
must post exactly the names your class reads.

## Storage and content

| Method | Use |
|---|---|
| `storeObjectAttribute( $objectAttribute )` | before the attribute row is stored |
| `objectAttributeContent( $objectAttribute )` | what `$attribute.content` is in templates and `attribute( 'content' )` in PHP |
| `hasObjectAttributeContent( $contentObjectAttribute )` | whether the attribute is filled |
| `onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )` | when the version is published |
| `deleteStoredObjectAttribute( $objectAttribute, $version = null )` | before the stored data is removed (delete files you stored) |

## Class settings

| Method | Use |
|---|---|
| `validateClassAttributeHTTPInput( $http, $base, $classAttribute )` | the class edit form is posted |
| `fetchClassAttributeHTTPInput( $http, $base, $classAttribute )` | read the class-level settings |
| `storeClassAttribute( $classAttribute, $version )` | before the class attribute is stored |
| `classAttributeContent( $classAttribute )` | what `$class_attribute.content` is |

## Information collection

| Method | Use |
|---|---|
| `isInformationCollector()` | return `true` if the attribute can collect input from visitors |
| `validateCollectionAttributeHTTPInput( $http, $base, $objectAttribute )` | validate a visitor's input |
| `fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $objectAttribute )` | store it |

## Search, sorting, naming

| Method | Use |
|---|---|
| `isIndexable()` | whether the search engine indexes it |
| `metaData( $contentObjectAttribute )` | the text to index |
| `sortKey( $objectAttribute )`, `sortKeyType()` | the value and type (`'int'` or `'string'`) for sorting by this attribute |
| `title( $objectAttribute, $name = null )` | the text used in object names |

## Import, export, packages

| Method | Use |
|---|---|
| `toString( $objectAttribute )`, `fromString( $objectAttribute, $string )` | the attribute as a string, for imports, exports and some tools |
| `serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )`, `unserializeContentClassAttribute( ... )` | the class settings in a package |

## Templates

A datatype draws nothing without its templates, found through the design chain:
`content/datatype/view/<datatypestring>.tpl` and `content/datatype/edit/<datatypestring>.tpl`, plus
`class/datatype/view/` and `class/datatype/edit/` for class settings. The extension needs
`design.ini [ExtensionSettings] DesignExtensions[]` so they are found.

## Related pages

- Previous: [eZFile, eZDir and cluster file handlers](ezfile-ezdir-and-cluster-files.md)
- Next: [The eZWorkflowType API](ezworkflowtype-api.md)
- [Datatypes](../part-3-extension-developer/datatypes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
