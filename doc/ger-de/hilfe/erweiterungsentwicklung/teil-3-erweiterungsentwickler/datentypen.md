---
title: Datentypen
audience: developer, app-creator
category: extension-development
order: 160
summary: Eine neue Art von Feld für Inhaltsklassen - wann Sie einen Datentyp schreiben, wie der Kernel ihn findet und registriert, der Lebenszyklus von eZDataType, ein geprüfter Bewertungs-Datentyp und echte Beispiele zum Lernen.
keywords: Datentyp, Feldtyp, Inhaltsklasse, Klassenattribut, Lebenszyklus, datatype, eZDataType, AvailableDataTypes, ExtensionDirectories, DataTypeSettings, register, DATA_TYPE_STRING, content class attribute, field type, data_int, data_text, birthday
type: help
---
# Datentypen

Teil 3, Kapitel 16. Ein **Datentyp** definiert eine Art von Feld, die Redakteure einer Inhaltsklasse hinzufügen
können: sein Bearbeitungsformular, seine Validierung, wie er gespeichert, angezeigt, durchsucht und sortiert wird. Der
Kernel bringt Dutzende mit (`ezstring`, `ezimage`, `ezxmltext` ...); Erweiterungen fügen weitere hinzu (`eztags`,
`birthday`, `ngclasslist`). Dieses Kapitel erklärt den Vertrag anhand eines kleinen Bewertungs-Datentyps, der mit
`php -l` geprüft und bei geladenem Kernel ausprobiert wurde; das nächste Kapitel behandelt Speicherung, Validierung und
Templates im Detail.

## Brauchen Sie einen?

Schreiben Sie einen Datentyp, wenn Redakteure eine Art von Wert eingeben müssen, die kein vorhandener Datentyp
abdeckt, **und** der Wert zum Inhalt gehört (versioniert, übersetzt, durchsuchbar). Bevor Sie einen schreiben, prüfen
Sie die Liste der verfügbaren Datentypen, wenn Sie einer Klasse ein Attribut hinzufügen, und den
[Katalog](../teil-1-vom-benutzer-zum-entwickler/der-se7enxweb-erweiterungskatalog.md).
Eine Auswahl, eine Zahl mit Grenzen, eine Beziehung zu anderen Inhalten: All das gibt es bereits.

## Wie der Kernel ihn findet

In `extension/mysite_hello/settings/content.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
ExtensionDirectories[]=mysite_hello
AvailableDataTypes[]=mysiterating

*/ ?>
```

- `ExtensionDirectories[]` sorgt dafür, dass der Kernel in `extension/mysite_hello/datatypes/` sucht.
- `AvailableDataTypes[]` bietet den Datentyp im Klasseneditor an.
- Die Datei muss `datatypes/mysiterating/mysiteratingtype.php` heißen: `eZDataType::loadAndRegisterType()`
  (`kernel/classes/ezdatatype.php`) bindet `<directory>/<type>/<type>type.php` ein.
- Templates liegen in Ihrem Design, daher braucht die Erweiterung außerdem `DesignExtensions[]` in
  `design.ini.append.php`.

Die echte Erweiterung `birthday` macht genau das (ihre `settings/content.ini.append` nennt
`ExtensionDirectories[]=birthday` und `AvailableDataTypes[]=ezbirthday`).

## Die Klasse

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

| Teil | Bedeutung |
|---|---|
| `DATA_TYPE_STRING` | der Bezeichner, der für jedes Attribut dieses Typs in der Datenbank gespeichert wird; ändern Sie ihn nie, sobald er verwendet wird |
| `parent::__construct( $dataTypeString, $name, $properties )` | registriert den Bezeichner, den im Klasseneditor angezeigten Namen (übersetzt) und Eigenschaften |
| `'serialize_supported' => true` | der Typ kann in Paketen exportiert werden |
| `eZDataType::register()` am Ende der Datei | sagt dem Kernel, welche Klasse den String implementiert |

Bei geladenem Kernel gab `eZDataType::create( 'mysiterating' )` ein Objekt der Klasse mit dem Namen
"Rating (1 to 5)" zurück.

## Der Lebenszyklus

| Wann | Methoden, die der Kernel aufruft |
|---|---|
| Ein Redakteur fügt das Attribut einer Klasse hinzu | `initializeClassAttribute()`, `fetchClassAttributeHTTPInput()`, `validateClassAttributeHTTPInput()`, `storeClassAttribute()` |
| Ein Redakteur legt Inhalte an oder bearbeitet sie | `initializeObjectAttribute()`, `validateObjectAttributeHTTPInput()`, `fetchObjectAttributeHTTPInput()`, `storeObjectAttribute()` |
| Der Inhalt wird veröffentlicht | `onPublish()` |
| Ein Template zeigt ihn an | `objectAttributeContent()` (das `.content` in Templates), `hasObjectAttributeContent()` (`.has_content`) |
| Suche, Sortierung, Namen | `metaData()`, `isIndexable()`, `sortKey()`, `sortKeyType()`, `title()` |
| Import und Export | `toString()`, `fromString()`, `serializeContentObjectAttribute()`, `unserializeContentObjectAttribute()` |
| Entfernen | `deleteStoredObjectAttribute()`, `deleteStoredClassAttribute()` |

Jede Methode hat in `eZDataType` eine Vorgabe; überschreiben Sie nur, was Ihr Typ braucht. Die vollständige Liste mit
Signaturen steht in
[Die API von eZDataType](../teil-8-referenz/ezdatatype-api.md).

## Von echten Datentypen lernen

| Datentyp | Zeigt |
|---|---|
| `extension/birthday` (`ezbirthday`) | ein Datum, gespeichert in `data_text`, drei Bearbeitungsfelder, Validierung, Informationssammlung |
| `kernel/classes/datatypes/ezinteger/` | den einfachsten Zahlentyp im Kernel |
| `extension/ngclasslist` | eine Liste, gespeichert als Text |
| `extension/eztags` | einen Datentyp mit eigenen Tabellen und Modulen |

## RAD

**Datatype extension** (`/setup/datatype`) schreibt die Klasse, die Einstellungen und die vier Templates; siehe
[Mit RAD: ein Datentyp](tutorials/rad-datentyp.md).
Die von Hand geschriebene Fassung ist
[Von Hand: ein Datentyp](tutorials/von-hand-datentyp.md).

## Verwandte Seiten

- Zurück: [Template-Funktionen](template-funktionen.md)
- Weiter: [Datentypen: Speicherung, Validierung und Templates](datentyp-speicherung-validierung-und-templates.md)
- [Härtung der Eingaben von Datentypen (Spezifikation)](../../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [eZpedia: Datatype](https://ezpedia.exponential.earth/en/learning/datatype)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
