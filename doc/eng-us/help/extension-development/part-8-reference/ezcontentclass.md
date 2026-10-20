---
title: eZContentClass and its attributes
audience: developer
category: extension-development
order: 80
summary: Read content classes and their attributes from PHP - fetchByIdentifier(), classIDByIdentifier(), dataMap(), fetchAttributeByIdentifier(), datatype and content - using identifiers, not IDs.
keywords: eZContentClass, fetch, fetchByIdentifier, classIDByIdentifier, classIdentifierByID, fetchAllClasses, dataMap, fetchAttributeByIdentifier, eZContentClassAttribute, dataType, content, identifier
type: help
---
# eZContentClass and its attributes

Part 8, reference 8. A content class (`kernel/classes/ezcontentclass.php`) describes a type of content: its
identifier (`article`), its name, and its attributes, each an `eZContentClassAttribute`
(`kernel/classes/ezcontentclassattribute.php`) with an identifier, a datatype and settings. Builders create classes in
the administration ([Content classes for builders](../part-2-builder/content-classes-for-builders.md)); extensions read
them.

## Identifiers, not IDs

A class's numeric ID differs between installations; its identifier does not. Write identifiers into code and
settings, and look up the ID when you need it:

| Method (static) | Returns |
|---|---|
| `classIDByIdentifier( $identifier )` | the ID for an identifier |
| `classIdentifierByID( $id )` | the identifier for an ID |
| `fetchByIdentifier( $identifier, $asObject = true, ... )` | the class |
| `fetch( $id, $asObject = true, ... )` | the class by ID |
| `fetchAllClasses( $asObject = true, $includeFilter = true, $groupList = false )` | every class |
| `fetchList( ... )` | classes by conditions |

## Reading a class

| Method or attribute | Returns |
|---|---|
| `attribute( 'identifier' )`, `attribute( 'id' )` | identifier and ID |
| `name( $languageLocale = false )` | the name |
| `dataMap()` | the attributes, keyed by attribute identifier |
| `fetchAttributeByIdentifier( $identifier, $asObject = true )` | one attribute |

For each class attribute:

| Method or attribute | Returns |
|---|---|
| `attribute( 'identifier' )` | e.g. `title` |
| `attribute( 'data_type_string' )` | the datatype, e.g. `ezstring` |
| `dataType()` | the datatype object |
| `content()` | the datatype's class-level settings (what `classAttributeContent()` of the datatype returns) |
| `attribute( 'is_required' )`, `attribute( 'is_searchable' )` | flags |

```php
<?php
$class = eZContentClass::fetchByIdentifier( 'folder' );
if ( $class instanceof eZContentClass )
{
    foreach ( $class->dataMap() as $identifier => $classAttribute )
    {
        echo $identifier, ': ', $classAttribute->attribute( 'data_type_string' ), "\n";
    }
}
```

Run read-only on a demo installation, this listed the seven attributes of the folder class.

The content of a class attribute is the right way to read a datatype's settings. Calling a datatype method such as
`classAttributeContent()` statically is a fatal error on PHP 8; ask the class attribute instead, as
se7enxweb/sevenx_themes_media did in commit f7d93c0
([PHP 8 errors that stop old code](../part-4-upgrading/php-8-errors-that-stop-old-code.md)).

## Creating classes

Do not create classes with raw SQL. Use packages ([Packages (.ezpkg) and the package creator](../part-7-releasing/packages-ezpkg.md)),
the administration, or the **Content extension** wizard of **Setup > RAD**, which writes a class as an install script.

## Related pages

- Previous: [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- Next: [eZUser and access checks](ezuser-and-access.md)
- [Datatypes](../part-3-extension-developer/datatypes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
