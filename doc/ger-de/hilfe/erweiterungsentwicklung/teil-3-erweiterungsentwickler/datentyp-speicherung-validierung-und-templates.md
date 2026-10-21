---
title: "Datentypen: Speicherung, Validierung und Templates"
audience: developer, app-creator
category: extension-development
order: 170
summary: Der vollständige Bewertungs-Datentyp - Spalten zur Speicherung, sichere Validierung gesendeter Eingaben, Werte über Versionen hinweg, Suche und Sortierung sowie die Templates zum Bearbeiten und Anzeigen, alles geprüft.
keywords: Datentyp, Speicherung, Validierung, Bearbeiten-Template, Anzeige-Template, Sortierung, Suche, datatype storage, data_int, data_text, data_float, validateObjectAttributeHTTPInput, fetchObjectAttributeHTTPInput, STATE_ACCEPTED, STATE_INVALID, setValidationError, initializeObjectAttribute, sortKey, metaData, edit template, view template, attribute_base
type: help
---
# Datentypen: Speicherung, Validierung und Templates

Teil 3, Kapitel 17. Das vorige Kapitel hat Datentypen eingeführt. Dieses baut einen vollständigen Datentyp, eine
Bewertung von 1 bis 5, und erklärt jeden Teil. Die Klasse hat `php -l` bestanden, wurde mit `eZDataType::create()`
erzeugt und bei geladenem Kernel an einem Attribut im Speicher ausprobiert; die Templates wurden von der
Template-Engine gerendert.

## Wo Werte gespeichert werden

Jedes Inhaltsattribut ist eine Zeile von `ezcontentobject_attribute` (Klasse `eZContentObjectAttribute`), eine pro
Objekt, Version und Sprache. Ein Datentyp verwendet ihre Spalten:

| Spalte | Enthält |
|---|---|
| `data_int` | eine ganze Zahl |
| `data_float` | eine Dezimalzahl |
| `data_text` | Text, oft serialisierte strukturierte Daten (XML, JSON) |
| `sort_key_int`, `sort_key_string` | aus `sortKey()` gefüllt, zum Sortieren |

Die meisten Datentypen kommen mit diesen Spalten aus. Ein Datentyp mit vielen Werten pro Attribut (eztags, eine Matrix)
kann eine eigene Tabelle führen, deren Schlüssel `id` und `version` des Attributs sind; er muss diese Zeilen dann in
den passenden Methoden selbst kopieren, löschen und exportieren (`initializeObjectAttribute()`,
`deleteStoredObjectAttribute()`, `serializeContentObjectAttribute()`). Siehe
[Persistente Objekte und eigene Tabellen](persistente-objekte-und-tabellen.md).

## Die Klasse

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

## Validierung: zwei Schritte

Wenn ein Redakteur auf **Speichern** oder **Veröffentlichen** drückt, ruft der Kernel zuerst für jedes Attribut
`validateObjectAttributeHTTPInput()` auf und erst dann, wenn alle akzeptiert sind, `fetchObjectAttributeHTTPInput()`,
das den Wert in das Attribut schreibt.

- Geben Sie `eZInputValidator::STATE_ACCEPTED` oder `STATE_INVALID` zurück; setzen Sie bei ungültiger Eingabe mit
  `setValidationError()` eine übersetzte Meldung. Die Bearbeitungsseite zeigt sie neben dem Feld an.
- Beachten Sie `is_required`, und verlangen Sie keinen Wert für ein Attribut, das als **Informationssammler** dient
  (ein Formularfeld, das Besucher ausfüllen); `birthday` folgt derselben Regel.
- **Ein gesendeter Wert kann ein Array sein.** Ein böswilliger Client kann `name[]=x` senden. Seit September 2026
  behandelt jeder Datentyp des Kernels das als ungültig, statt abzustürzen
  ([Härtung der Eingaben von Datentypen](../../../spezifikationen/6.0/datentyp-eingabehaertung.md)); die Prüfungen mit
  `is_string()` oben tun dasselbe.
- Der Feldname enthält `$base` (meist `ContentObjectAttribute`) und die `id` des Attributs, damit mehrere Attribute
  desselben Typs in einem Formular nicht kollidieren.

## Versionen und Übersetzungen

`initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )` wird
ausgeführt, wenn eine neue Version oder Übersetzung angelegt wird. Kopieren Sie den Wert aus dem Original, wie oben,
sonst beginnt der neue Entwurf leer. Mit einer eigenen Tabelle kopieren Sie hier deren Zeilen.

## Suche, Sortierung und Namen

| Methode | Verwendet für |
|---|---|
| `metaData()` | den Text, den die Suchmaschine indiziert; geben Sie `''` zurück, wenn es nichts gibt |
| `isIndexable()` | ob die Suchmaschine das Attribut überhaupt indizieren soll |
| `sortKey()`, `sortKeyType()` (`'int'` oder `'string'`) | Listen nach diesem Attribut sortieren (`sort_by` auf `<class>/<attribute>`) |
| `title()` | den Text, der verwendet wird, wenn das Namensmuster des Objekts dieses Attribut nennt |
| `toString()`, `fromString()` | CSV-Import und -Export sowie Skripte |

## Die Templates

Der Kernel findet sie über den Datentyp-String, über die Designkette:

| Template | Angezeigt |
|---|---|
| `content/datatype/edit/mysiterating.tpl` | im Bearbeitungsformular |
| `content/datatype/view/mysiterating.tpl` | überall, wo `attribute_view_gui` es zeichnet |
| `class/datatype/edit/mysiterating.tpl` | im Klasseneditor, für Optionen auf Klassenebene |
| `class/datatype/view/mysiterating.tpl` | in der Klassenansicht |

Bearbeiten-Template (`design/standard/templates/content/datatype/edit/mysiterating.tpl`):

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

Anzeige-Template (`design/standard/templates/content/datatype/view/mysiterating.tpl`):

```text
{if $attribute.has_content}
<span class="mysite-rating" data-rating="{$attribute.content|wash}">{$attribute.content|wash} / 5</span>
{/if}
```

Mit einem Attribut mit der ID 77 und dem Wert 4 gerendert, erzeugt das Bearbeiten-Template ein `select` mit dem Namen
`ContentObjectAttribute_mysiterating_77`, in dem Option 4 ausgewählt ist, und das Anzeige-Template
`<span class="mysite-rating" data-rating="4">4 / 5</span>`. Die `id` folgt dem eigenen Muster des Kernels
(`ezcoa-<class attribute id>_<identifier>`), das die Beschriftung der Bearbeitungsseite verwendet. Legen Sie das
Aussehen in einem Stylesheet fest, nie in einem `style`-Attribut.

## Prüfen

1. Prüfen Sie die Klasse mit `php -l`; aktivieren Sie die Erweiterung; erzeugen Sie die Autoloads neu; leeren Sie die
   Caches.
2. **Setup > Klassen**: Fügen Sie einer Testklasse ein Attribut vom Typ "Rating (1 to 5)" hinzu.
3. Legen Sie ein Objekt an: Probieren Sie einen leeren Wert (Pflichtfeld und kein Pflichtfeld), `0`, `6`, `abc` und
   eine gültige Bewertung.
4. Zeigen Sie es an; sortieren Sie eine Liste nach dem Attribut.

## Verwandte Seiten

- Zurück: [Datentypen](datentypen.md)
- Weiter: [Workflow-Ereignistypen](workflow-ereignistypen.md)
- [Die API von eZDataType](../teil-8-referenz/ezdatatype-api.md)
- [Von Hand: ein Datentyp](tutorials/von-hand-datentyp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
