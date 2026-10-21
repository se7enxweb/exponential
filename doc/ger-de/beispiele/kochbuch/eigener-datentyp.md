---
title: Ein eigener Datentyp
audience: developer
category: examples
order: 60
summary: Den Datentyp expcolor schreiben, eine als #rrggbb gespeicherte Farbe mit einem Standardwert je Klasse, als Unterklasse von eZDataType in einer Erweiterung, registriert in content.ini, mit seinen Bearbeitungs- und Ansichts-Templates für Objekte und Klassen.
keywords: Datentyp, eigener Datentyp, Attribut, Klassenattribut, Validierung, Templates, datatype, custom datatype, eZDataType, DATA_TYPE_STRING, register, content.ini, DataTypeSettings, ExtensionDirectories, AvailableDataTypes, data_text, data_text1, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, objectAttributeContent, toString, fromString, eZInputValidator
type: example
---
# Ein eigener Datentyp

Ein Datentyp ist die Art eines Klassenattributs: was ein Redakteur eingibt, wie es geprüft wird, wo es gespeichert
und wie es angezeigt wird. Sie schreiben **expcolor**: eine Farbe als `#rrggbb`, ausgewählt mit dem Farbfeld des
Browsers, gespeichert in der Spalte `data_text` des Objektattributs, mit einer Standardfarbe je Klassenattribut in
`data_text1`.

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert; eine Klasse, der
das Attribut hinzugefügt wird ([Eine Inhaltsklasse anlegen](inhaltsklasse.md)).

## 1. Wo der Kernel sucht

`eZDataType::loadAndRegisterType()` sucht `<directory>/<type>/<type>type.php` in `content.ini [DataTypeSettings]
RepositoryDirectories[]` und in `datatypes/` jeder Erweiterung aus `ExtensionDirectories[]`. Ein Typ wird nur
angeboten, wenn er in `AvailableDataTypes[]` steht. `extension/expexample/settings/content.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
# Look for datatypes/<type>/<type>type.php in this extension
ExtensionDirectories[]=expexample
AvailableDataTypes[]=expcolor

*/ ?>
```

## 2. Die Datentyp-Klasse

`extension/expexample/datatypes/expcolor/expcolortype.php`. Die Datei registriert die Klasse an ihrem Ende mit
`eZDataType::register()`, wie es jeder Datentyp des Kernels tut:

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

Was der Kernel aufruft, und wann:

| Methode | Aufgerufen |
|---|---|
| `initializeObjectAttribute()` | bei einem neuen Objekt oder einer neuen Version (`$currentVersion` gesetzt) |
| `validateObjectAttributeHTTPInput()` | bei *Zur Veröffentlichung schicken* und *Entwurf speichern*; ein `eZInputValidator::STATE_*` |
| `fetchObjectAttributeHTTPInput()` | nach der Validierung, um den gesendeten Wert zu speichern |
| `fetchClassAttributeHTTPInput()` | wenn das Formular der Klassenbearbeitung gespeichert wird |
| `objectAttributeContent()` | `$attribute.content` in Templates |
| `hasObjectAttributeContent()` | `$attribute.has_content` |
| `metaData()`, `isIndexable()` | der Text, den die Suchmaschine indiziert |
| `title()` | das Attribut im Objekt-Namensschema (`<color>`) |
| `sortKey()`, `sortKeyType()` | das Sortieren von Unterknoten nach diesem Attribut |
| `toString()`, `fromString()` | CSV-Import und -Export sowie andere Skripte, die Werte kopieren |

Die Basisklasse implementiert den Rest (Speichern, Löschen, Versionieren der einfachen Spalten). Ein Datentyp mit
einer eigenen Tabelle implementiert außerdem `storeObjectAttribute()`, `deleteStoredObjectAttribute()` und die
Serialisierungsmethoden für Pakete.

## 3. Die Templates

Der Kernel findet die Templates eines Datentyps über dessen Bezeichner. Alle vier gehören in das Design der Erweiterung
(`extension/expexample/design/standard/templates/`).

`content/datatype/edit/expcolor.tpl`, das Feld im Bearbeitungsformular des Objekts; der Name des Eingabefelds muss
der sein, den die Klasse liest (`<base>_expcolor_<attribute id>`):

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

`content/datatype/view/expcolor.tpl`, was `{attribute_view_gui attribute=...}` anzeigt:

```tpl
{* The view of an expcolor attribute: the color as text; a design gives it a swatch with a class *}
{if $attribute.has_content}
<span class="expexample-color" data-color="{$attribute.content|wash}">{$attribute.content|wash}</span>
{/if}
```

Die Farbe wird dem Design als `data-`-Attribut übergeben, nicht als `style=""`: Ein Stylesheet oder ein Skript des
Designs zeichnet das Farbfeld, wodurch die Seite innerhalb einer Content Security Policy bleibt.

`class/datatype/edit/expcolor.tpl`, der Standardwert auf der Bearbeitungsseite der Klasse (der Kernel sendet ihn mit
der Basis `ContentClass`):

```tpl
{* The class edit form of an expcolor attribute: its default color *}
<div class="block">
    <label for="ContentClass_expcolor_default_{$class_attribute.id}">{'Default color'|i18n( 'extension/expexample' )}:</label>
    <input id="ContentClass_expcolor_default_{$class_attribute.id}" type="color"
           name="ContentClass_expcolor_default_{$class_attribute.id}"
           value="{first_set( $class_attribute.data_text1, '#000000' )|wash}" />
</div>
```

`class/datatype/view/expcolor.tpl`, dasselbe auf der Ansichtsseite der Klasse:

```tpl
{* The class view of an expcolor attribute *}
<div class="block">
    <label>{'Default color'|i18n( 'extension/expexample' )}:</label>
    <p>{$class_attribute.data_text1|wash}</p>
</div>
```

## 4. Aktivieren und verwenden

```bash
php -l extension/expexample/datatypes/expcolor/expcolortype.php
php bin/php/ezcache.php --clear-all --allow-root-user
```

Die Datentyp-Datei wird vom Loader eingebunden, nicht über das Autoload-Array; starten Sie Velocity neu (oder
[rollen Sie aus](velocity-deployment.md)), damit seine Worker die neuen Einstellungen laden.

1. **Setup > Klassen**, *Note* bearbeiten, **Attribut hinzufügen**: *Color* steht in der Liste. Nennen Sie es
   `Color`, Bezeichner `color`, wählen Sie einen Standardwert, **OK**.
2. Bearbeiten Sie eine Note: Das Farbfeld ist da; *Zur Veröffentlichung schicken*.
3. In einem Template: `{$node.data_map.color.content}` ist `#3366cc`;
   `{attribute_view_gui attribute=$node.data_map.color}` rendert das Ansichts-Template.

Für diese Seite geprüft (2026-10-18): `php -l` für die Klasse; `bin/php/eztemplatecheck.php` meldet die vier Templates
als gültig (Syntax); jede überschriebene Methode hat die Signatur aus `kernel/classes/ezdatatype.php`. Der Datentyp
wurde für diese Seite nicht gegen eine Datenbank ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Der Typ wird auf der Bearbeitungsseite der Klasse nicht angeboten | nicht in `AvailableDataTypes[]`, oder `ExtensionDirectories[]` fehlt; die Debug-Ausgabe nennt die durchsuchten Verzeichnisse |
| Der Wert wird nie gespeichert | der Name des Eingabefelds im Bearbeitungs-Template weicht von dem ab, den die Klasse liest |
| Ein erforderliches Attribut akzeptiert einen leeren Wert | `validateIsRequired()` wird nicht abgefragt |
| `DATA_TYPE_STRING` nachträglich ändern | jedes bestehende Attribut behält die alte Zeichenkette in `ezcontentclass_attribute.data_type_string`; wählen Sie sie einmal und endgültig |

## Abgeleitet aus

- `kernel/classes/ezdatatype.php` (`loadAndRegisterType()`, `register()`, die Methodenliste)
- `kernel/classes/datatypes/ezboolean/ezbooleantype.php`, `kernel/classes/datatypes/ezstring/ezstringtype.php`
- `design/standard/templates/content/datatype/edit/ezboolean.tpl`, `design/standard/templates/class/datatype/edit/ezstring.tpl`
- `settings/content.ini` `[DataTypeSettings]`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Datentypen](../datentypen/README.md): jeder Datentyp mit dem, was er speichert, und seinen Template-Attributen
- [Eine Inhaltsklasse anlegen](inhaltsklasse.md)
- [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md): `/setup/datatype` schreibt ein Gerüst für einen Datentyp
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
