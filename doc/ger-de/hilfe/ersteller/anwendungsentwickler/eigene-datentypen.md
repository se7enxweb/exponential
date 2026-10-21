---
title: Einen eigenen Datentyp schreiben
audience: app-creator, developer
category: app-creation
order: 240
summary: Exponential um einen neuen Feldtyp ergänzen - die Unterklasse von eZDataType, die Registrierung in content.ini, die Speicherung in den Attributspalten, Eingabe und Validierung, Ansichts- und Bearbeitungs-Templates und der Datentyp-Assistent von RAD.
keywords: Datentyp, eigener Datentyp, Registrierung, Bearbeitungs-Template, Ansichts-Template, Validierung, Anwendungsentwickler, datatype, custom datatype, eZDataType, DATA_TYPE_STRING, register, AvailableDataTypes, ExtensionDirectories, data_text, data_int, edit template, view template, app creator
type: help
---
# Einen eigenen Datentyp schreiben

Diese Seite ist für Anwendungsentwickler, die einen Feldtyp brauchen, den die mitgelieferten Datentypen nicht bieten:
eine Farbe, eine Bewertung, eine strukturierte Adresse. Ein Datentyp ist eine Klasse, die `eZDataType`
(`kernel/classes/ezdatatype.php`) erweitert, plus ein Ansichts- und ein Bearbeitungs-Template.
**Setup > RAD > Datentyp-Assistent** (`/setup/datatype`) schreibt ein lauffähiges Gerüst ([RAD-Werkzeuge](rad-werkzeuge.md)).

![Setup, RAD, der Datentyp-Assistent: Name und Klasse eines neuen Datentyps, um sein Gerüst zu erzeugen](../../../../eng-us/help/images/creators/rad-datatype-wizard.webp)

## Inhalt

1. [Zuerst prüfen](#zuerst-prüfen)
2. [Dateien und Registrierung](#dateien-und-registrierung)
3. [Die Klasse](#die-klasse)
4. [Templates](#templates)
5. [Speicherung](#speicherung)
6. [Testen](#testen)

## Zuerst prüfen

Viele Anforderungen erfüllt ein mitgelieferter Datentyp oder eine Erweiterung ([Datentypen wählen](../website-ersteller/datentypen-waehlen.md)).
Ein Datentyp ist eine langfristige Verpflichtung: Inhalte, die damit gespeichert werden, hängen für immer von Ihrer
Erweiterung ab.

## Dateien und Registrierung

```text
extension/expdemo/datatypes/expcolor/expcolortype.php
extension/expdemo/design/standard/templates/content/datatype/view/expcolor.tpl
extension/expdemo/design/standard/templates/content/datatype/edit/expcolor.tpl
extension/expdemo/settings/content.ini.append.php
```

```ini
<?php /* #?ini charset="utf-8"?
[DataTypeSettings]
ExtensionDirectories[]=expdemo
AvailableDataTypes[]=expcolor
*/ ?>
```

So registriert das mitgelieferte `ezstarrating` seinen Datentyp (`extension/ezstarrating/settings/content.ini.append.php`).

## Die Klasse

```php
<?php
class expColorType extends eZDataType
{
    const DATA_TYPE_STRING = 'expcolor';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'extension/expdemo', 'Color' ),
                             array( 'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        $name = $base . '_expcolor_' . $objectAttribute->attribute( 'id' );
        $value = $http->hasPostVariable( $name ) ? trim( (string)$http->postVariable( $name ) ) : '';
        if ( $value === '' && $objectAttribute->validateIsRequired() )
        {
            $objectAttribute->setValidationError( ezpI18n::tr( 'extension/expdemo', 'Choose a color.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $value !== '' && !preg_match( '/^#[0-9a-f]{6}$/i', $value ) )
        {
            $objectAttribute->setValidationError( ezpI18n::tr( 'extension/expdemo', 'Write the color as #rrggbb.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        $name = $base . '_expcolor_' . $objectAttribute->attribute( 'id' );
        if ( !$http->hasPostVariable( $name ) )
            return false;
        $objectAttribute->setAttribute( 'data_text', strtolower( trim( (string)$http->postVariable( $name ) ) ) );
        return true;
    }

    function objectAttributeContent( $objectAttribute )
    {
        return $objectAttribute->attribute( 'data_text' );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' ) !== '';
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function toString( $objectAttribute )
    {
        return (string)$objectAttribute->attribute( 'data_text' );
    }

    function fromString( $objectAttribute, $string )
    {
        $objectAttribute->setAttribute( 'data_text', (string)$string );
        return true;
    }

    function title( $objectAttribute, $name = null )
    {
        return $objectAttribute->attribute( 'data_text' );
    }

    function isIndexable()
    {
        return true;
    }
}

eZDataType::register( expColorType::DATA_TYPE_STRING, 'expColorType' );
```

Die Methodennamen und Signaturen sind die von `eZDataType`; mit der Registrierungszeile am Ende registriert sich jeder
mitgelieferte Datentyp selbst (zum Beispiel `kernel/classes/datatypes/ezboolean/ezbooleantype.php`).

## Templates

`content/datatype/edit/expcolor.tpl`:

```text
<input type="color" id="ezcoa-{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"
       name="{$attribute_base}_expcolor_{$attribute.id}" value="{$attribute.content|wash}">
```

`content/datatype/view/expcolor.tpl`:

```text
<span class="expdemo-color" data-color="{$attribute.content|wash}">{$attribute.content|wash}</span>
```

Kein `style=""` in Templates: Ein Stylesheet oder Skript liest `data-color`
([Content Security Policy für Ihren Code](content-security-policy.md)).

## Speicherung

Jede Attributversion hat die Spalten `data_text`, `data_int`, `data_float` (und das Klassenattribut hat `data_text1`...,
`data_int1`... für Einstellungen). Speichern Sie einfache Werte dort; komplexe in einer eigenen Tabelle, deren Schlüssel
Attribut-ID und Version sind und die in `deleteStoredObjectAttribute()` aufgeräumt wird
([Der Lebenszyklus eines Datentyps](lebenszyklus-eines-datentyps.md)).

## Testen

1. Autoloads neu erzeugen, Caches leeren.
2. Einer Testklasse ein Attribut des neuen Typs hinzufügen; Objekte anlegen, bearbeiten, übersetzen, kopieren und
   entfernen.
3. Den Hin- und Rückweg über `toString()`/`fromString()` in einem PHPUnit-Test prüfen ([PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)).
4. Paketexport und -import erhalten den Wert ([Inhaltspakete](pakete-und-ezpkg.md)).

## Verwandte Seiten

- [Der Lebenszyklus eines Datentyps](lebenszyklus-eines-datentyps.md)
- [Muster Datentyp](../../../../eng-us/standards/patterns/datatype.md)
- [Erweiterungspunkte: Datentyp](../../../bc/6.0/rad-erweiterungspunkte.md#datentyp)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
