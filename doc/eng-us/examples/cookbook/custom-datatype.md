---
title: A custom datatype
audience: developer
category: examples
order: 60
summary: Write the datatype expcolor, a color stored as #rrggbb with a class default, as an eZDataType subclass in an extension, registered in content.ini, with its edit and view templates for objects and classes.
keywords: datatype, custom datatype, eZDataType, DATA_TYPE_STRING, register, content.ini, DataTypeSettings, ExtensionDirectories, AvailableDataTypes, data_text, data_text1, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, objectAttributeContent, toString, fromString, eZInputValidator
type: example
---
# A custom datatype

A datatype is the kind of a class attribute: what an editor types, how it is checked, where it is stored and how it
is shown. You write **expcolor**: a color as `#rrggbb`, chosen with the browser's color field, stored in the object
attribute's `data_text` column, with a default color per class attribute in `data_text1`.

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on; a class to add the
attribute to ([Create a content class](content-class.md)).

## 1. Where the kernel looks

`eZDataType::loadAndRegisterType()` looks for `<directory>/<type>/<type>type.php` in `content.ini [DataTypeSettings]
RepositoryDirectories[]` and in `datatypes/` of every extension in `ExtensionDirectories[]`. A type is offered only
when it is in `AvailableDataTypes[]`. `extension/expexample/settings/content.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
# Look for datatypes/<type>/<type>type.php in this extension
ExtensionDirectories[]=expexample
AvailableDataTypes[]=expcolor

*/ ?>
```

## 2. The datatype class

`extension/expexample/datatypes/expcolor/expcolortype.php`. The file registers the class with `eZDataType::register()`
at its end, as every kernel datatype does:

```php
<?php
/**
 * The datatype expcolor: a color as #rrggbb, stored in data_text of the object attribute.
 * The class attribute keeps its default color in data_text1.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expColorType extends eZDataType
{
    const DATA_TYPE_STRING = 'expcolor';
    const DEFAULT_FIELD = 'data_text1';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'extension/expexample', 'Color', 'Datatype name' ),
                             array( 'serialize_supported' => true,
                                    'object_serialize_map' => array( 'data_text' => 'color' ) ) );
    }

    /** #rrggbb in lower case, or '' when $value is not a color */
    public static function normalize( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        return preg_match( '/^#[0-9a-f]{6}$/', $value ) ? $value : '';
    }

    /** A new attribute gets the class default; a new version copies the previous one */
    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $contentObjectAttribute->setAttribute( 'data_text', $originalContentObjectAttribute->attribute( 'data_text' ) );
            return;
        }
        $default = $contentObjectAttribute->contentClassAttribute()->attribute( self::DEFAULT_FIELD );
        $contentObjectAttribute->setAttribute( 'data_text', self::normalize( $default ) );
    }

    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $name = $base . '_expcolor_' . $contentObjectAttribute->attribute( 'id' );
        $value = $http->hasPostVariable( $name ) ? trim( $http->postVariable( $name ) ) : '';
        if ( $value === '' )
        {
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'extension/expexample', 'Input required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            return eZInputValidator::STATE_ACCEPTED;
        }
        if ( self::normalize( $value ) === '' )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'extension/expexample', 'Write the color as #rrggbb.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $name = $base . '_expcolor_' . $contentObjectAttribute->attribute( 'id' );
        if ( !$http->hasPostVariable( $name ) )
            return false;
        $contentObjectAttribute->setAttribute( 'data_text', self::normalize( $http->postVariable( $name ) ) );
        return true;
    }

    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $name = $base . '_expcolor_default_' . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $name ) )
            $classAttribute->setAttribute( self::DEFAULT_FIELD, self::normalize( $http->postVariable( $name ) ) );
        return true;
    }

    /** {$attribute.content} in templates */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' ) !== '';
    }

    public function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function isIndexable()
    {
        return true;
    }

    public function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function sortKeyType()
    {
        return 'string';
    }

    /** CSV import and export (ezcsvexport.php, ezcsvimport.php) */
    public function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function fromString( $contentObjectAttribute, $string )
    {
        $contentObjectAttribute->setAttribute( 'data_text', self::normalize( $string ) );
        return true;
    }
}

eZDataType::register( expColorType::DATA_TYPE_STRING, 'expColorType' );
```

What the kernel calls, and when:

| Method | Called |
|---|---|
| `initializeObjectAttribute()` | a new object, or a new version (`$currentVersion` set) |
| `validateObjectAttributeHTTPInput()` | on *Send for publishing* and *Store draft*; an `eZInputValidator::STATE_*` |
| `fetchObjectAttributeHTTPInput()` | after validation, to store the posted value |
| `fetchClassAttributeHTTPInput()` | when the class edit form is stored |
| `objectAttributeContent()` | `$attribute.content` in templates |
| `hasObjectAttributeContent()` | `$attribute.has_content` |
| `metaData()`, `isIndexable()` | the text the search engine indexes |
| `title()` | the attribute in the object name pattern (`<color>`) |
| `sortKey()`, `sortKeyType()` | sorting children by this attribute |
| `toString()`, `fromString()` | CSV import and export, and other scripts that copy values |

The base class implements the rest (storing, deleting, versioning of the plain columns). A datatype with a table of
its own also implements `storeObjectAttribute()`, `deleteStoredObjectAttribute()` and the serialize methods for
packages.

## 3. The templates

The kernel finds a datatype's templates by its identifier. All four go into the extension's design
(`extension/expexample/design/standard/templates/`).

`content/datatype/edit/expcolor.tpl`, the field in the object edit form; the input name must be the one the class reads
(`<base>_expcolor_<attribute id>`):

```tpl
{* The edit field of an expcolor attribute *}
{default attribute_base='ContentObjectAttribute'}
<input id="ezcoa-{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"
       class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}"
       type="color"
       name="{$attribute_base}_expcolor_{$attribute.id}"
       value="{first_set( $attribute.content, '#000000' )|wash}" />
{/default}
```

`content/datatype/view/expcolor.tpl`, what `{attribute_view_gui attribute=...}` shows:

```tpl
{* The view of an expcolor attribute: the color as text; a design gives it a swatch with a class *}
{if $attribute.has_content}
<span class="expexample-color" data-color="{$attribute.content|wash}">{$attribute.content|wash}</span>
{/if}
```

The color is handed to the design as a `data-` attribute, not as `style=""`: a style sheet or a script of the design
draws the swatch, which keeps the page within a Content Security Policy.

`class/datatype/edit/expcolor.tpl`, the default on the class edit page (the kernel posts it with the base
`ContentClass`):

```tpl
{* The class edit form of an expcolor attribute: its default color *}
<div class="block">
    <label for="ContentClass_expcolor_default_{$class_attribute.id}">{'Default color'|i18n( 'extension/expexample' )}:</label>
    <input id="ContentClass_expcolor_default_{$class_attribute.id}" type="color"
           name="ContentClass_expcolor_default_{$class_attribute.id}"
           value="{first_set( $class_attribute.data_text1, '#000000' )|wash}" />
</div>
```

`class/datatype/view/expcolor.tpl`, the same on the class view page:

```tpl
{* The class view of an expcolor attribute *}
<div class="block">
    <label>{'Default color'|i18n( 'extension/expexample' )}:</label>
    <p>{$class_attribute.data_text1|wash}</p>
</div>
```

## 4. Switch it on and use it

```bash
php -l extension/expexample/datatypes/expcolor/expcolortype.php
php bin/php/ezcache.php --clear-all --allow-root-user
```

The datatype file is included by the loader, not by the autoload array; restart Velocity (or
[deploy](velocity-deploy.md)) so that its workers load the new settings.

1. **Setup > Classes**, edit *Note*, **Add attribute**: *Color* is in the list. Name it `Color`, identifier `color`,
   choose a default, **OK**.
2. Edit a Note: the color field is there; *Send for publishing*.
3. In a template: `{$node.data_map.color.content}` is `#3366cc`; `{attribute_view_gui attribute=$node.data_map.color}`
   renders the view template.

Checked for this page (2026-10-18): `php -l` on the class; `bin/php/eztemplatecheck.php` reports the four templates
valid (syntax); every overridden method has the signature of `kernel/classes/ezdatatype.php`. The datatype was not run
against a database for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| The type is not offered on the class edit page | not in `AvailableDataTypes[]`, or `ExtensionDirectories[]` missing; the debug output names the directories searched |
| The value is never stored | the input name in the edit template differs from the one the class reads |
| A required attribute accepts an empty value | `validateIsRequired()` is not asked |
| Changing `DATA_TYPE_STRING` later | every existing attribute keeps the old string in `ezcontentclass_attribute.data_type_string`; choose it once |

## Derived from

- `kernel/classes/ezdatatype.php` (`loadAndRegisterType()`, `register()`, the method list)
- `kernel/classes/datatypes/ezboolean/ezbooleantype.php`, `kernel/classes/datatypes/ezstring/ezstringtype.php`
- `design/standard/templates/content/datatype/edit/ezboolean.tpl`, `design/standard/templates/class/datatype/edit/ezstring.tpl`
- `settings/content.ini` `[DataTypeSettings]`

## Related pages

- [Cookbook](README.md)
- [Datatypes](../datatypes/README.md): every datatype with what it stores and its template attributes
- [Create a content class](content-class.md)
- [RAD tools](../../features/6.0/rad-tools.md): `/setup/datatype` writes a datatype skeleton
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
