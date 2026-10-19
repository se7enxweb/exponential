---
title: Datatype storage, validation and templates
audience: developer, app-creator
category: extension-development
order: 170
summary: The complete rating datatype: storage columns, validating posted input safely, values across versions, search and sorting, and the edit and view templates, all verified.
keywords: datatype storage, data_int, data_text, data_float, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, STATE_ACCEPTED, STATE_INVALID, setValidationError, initializeObjectAttribute, sortKey, metaData, edit template, view template, attribute_base
type: help
---
# Datatype storage, validation and templates

Part 3, chapter 17. The previous chapter introduced datatypes. This one builds a complete one, a rating from 1 to 5,
and explains each part. The class passed `php -l`, was created with `eZDataType::create()` and exercised on an
in-memory attribute with the kernel loaded; the templates were rendered by the template engine.

## Where values are stored

Every content attribute is a row of `ezcontentobject_attribute` (class `eZContentObjectAttribute`), one per object,
version and language. A datatype uses its columns:

| Column | Holds |
|---|---|
| `data_int` | an integer |
| `data_float` | a decimal number |
| `data_text` | text, often serialized structured data (XML, JSON) |
| `sort_key_int`, `sort_key_string` | filled from `sortKey()` for sorting |

Most datatypes fit these columns. A datatype with many values per attribute (eztags, a matrix) can keep its own table,
keyed by the attribute's `id` and `version`; it must then copy, delete and export those rows itself in the matching
methods (`initializeObjectAttribute()`, `deleteStoredObjectAttribute()`, `serializeContentObjectAttribute()`). See
[Persistent objects and your own tables](persistent-objects-and-tables.md).

## The class

`extension/mysite_hello/datatypes/mysiterating/mysiteratingtype.php`:

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

    /** The name of the edit field for one attribute. */
    protected static function postName( $base, $contentObjectAttribute )
    {
        return $base . '_mysiterating_' . $contentObjectAttribute->attribute( 'id' );
    }

    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $name = self::postName( $base, $contentObjectAttribute );
        $raw = $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : '';
        // a posted array is not a rating: treat it as invalid input, never as text
        $value = is_string( $raw ) ? trim( $raw ) : 'invalid';
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $value === '' )
        {
            if ( $classAttribute->attribute( 'is_required' ) && !$classAttribute->attribute( 'is_information_collector' ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'extension/mysite_hello', 'Choose a rating.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            return eZInputValidator::STATE_ACCEPTED;
        }
        if ( !ctype_digit( $value ) || (int)$value < 1 || (int)$value > 5 )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'extension/mysite_hello', 'A rating is a number from 1 to 5.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $name = self::postName( $base, $contentObjectAttribute );
        if ( !$http->hasPostVariable( $name ) )
            return false;
        $raw = $http->postVariable( $name );
        $value = is_string( $raw ) ? trim( $raw ) : '';
        $contentObjectAttribute->setAttribute( 'data_int', $value === '' ? 0 : (int)$value );
        return true;
    }

    public function objectAttributeContent( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' );
    }

    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' ) > 0;
    }

    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion !== false )
        {
            $contentObjectAttribute->setAttribute( 'data_int', $originalContentObjectAttribute->attribute( 'data_int' ) );
        }
    }

    public function metaData( $contentObjectAttribute )
    {
        return (string)$contentObjectAttribute->attribute( 'data_int' );
    }

    public function title( $contentObjectAttribute, $name = null )
    {
        return (string)$contentObjectAttribute->attribute( 'data_int' );
    }

    public function toString( $contentObjectAttribute )
    {
        return (string)$contentObjectAttribute->attribute( 'data_int' );
    }

    public function fromString( $contentObjectAttribute, $string )
    {
        $contentObjectAttribute->setAttribute( 'data_int', (int)$string );
        return true;
    }

    public function isIndexable()
    {
        return true;
    }

    public function sortKey( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' );
    }

    public function sortKeyType()
    {
        return 'int';
    }
}

eZDataType::register( mysiteRatingType::DATA_TYPE_STRING, 'mysiteRatingType' );
```

## Validation: two steps

When an editor presses **Store** or **Publish**, the kernel first calls `validateObjectAttributeHTTPInput()` for every
attribute, and only when all are accepted, `fetchObjectAttributeHTTPInput()`, which writes the value into the attribute.

- Return `eZInputValidator::STATE_ACCEPTED` or `STATE_INVALID`; for invalid input set a translated message with
  `setValidationError()`. The edit page shows it next to the field.
- Respect `is_required`, and do not require a value for an **information collector** attribute (a form field filled by
  visitors); `birthday` follows the same rule.
- **A posted value can be an array.** A hostile client can send `name[]=x`. Since September 2026 every kernel datatype
  treats that as invalid instead of crashing ([Datatype input hardening](../../../specifications/6.0/datatype-input-hardening.md));
  the `is_string()` checks above do the same.
- The field name contains `$base` (usually `ContentObjectAttribute`) and the attribute's `id`, so several attributes of
  the same type on one form do not collide.

## Versions and translations

`initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )` runs when a new
version or translation is created. Copy the value from the original, as above, or the new draft starts empty. With
your own table, copy its rows here.

## Search, sorting and names

| Method | Used for |
|---|---|
| `metaData()` | the text the search engine indexes; return `''` for nothing |
| `isIndexable()` | whether the search engine should index it at all |
| `sortKey()`, `sortKeyType()` (`'int'` or `'string'`) | sorting lists by this attribute (`sort_by` on `<class>/<attribute>`) |
| `title()` | the text used when the object name pattern names this attribute |
| `toString()`, `fromString()` | CSV import and export, and scripts |

## The templates

The kernel finds them by the datatype string, through the design chain:

| Template | Shown |
|---|---|
| `content/datatype/edit/mysiterating.tpl` | in the edit form |
| `content/datatype/view/mysiterating.tpl` | wherever `attribute_view_gui` draws it |
| `class/datatype/edit/mysiterating.tpl` | in the class editor, for class-level options |
| `class/datatype/view/mysiterating.tpl` | in the class view |

Edit template (`design/standard/templates/content/datatype/edit/mysiterating.tpl`):

```text
{default attribute_base='ContentObjectAttribute'}
<select id="ezcoa-{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"
        name="{$attribute_base}_mysiterating_{$attribute.id}">
    <option value="">{'No rating'|i18n( 'extension/mysite_hello' )}</option>
    {foreach array( 1, 2, 3, 4, 5 ) as $value}
        <option value="{$value}"{if eq( $attribute.data_int, $value )} selected="selected"{/if}>{$value}</option>
    {/foreach}
</select>
{/default}
```

View template (`design/standard/templates/content/datatype/view/mysiterating.tpl`):

```text
{if $attribute.has_content}
<span class="mysite-rating" data-rating="{$attribute.content|wash}">{$attribute.content|wash} / 5</span>
{/if}
```

Rendered with an attribute of ID 77 and the value 4, the edit template produces a `select` named
`ContentObjectAttribute_mysiterating_77` with option 4 selected, and the view template
`<span class="mysite-rating" data-rating="4">4 / 5</span>`. The `id` follows the kernel's own pattern
(`ezcoa-<class attribute id>_<identifier>`), which the edit page's label uses. Put the look in a stylesheet, never in a
`style` attribute.

## Check it

1. `php -l` the class; activate; regenerate autoloads; clear caches.
2. **Setup > Classes**: add an attribute of type "Rating (1 to 5)" to a test class.
3. Create an object: try an empty value (required and not), `0`, `6`, `abc`, and a valid rating.
4. View it; sort a list by the attribute.

## Related pages

- Previous: [Datatypes](datatypes.md)
- Next: [Workflow event types](workflow-event-types.md)
- The eZDataType API
- [By hand: a datatype](tutorials/by-hand-datatype.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
