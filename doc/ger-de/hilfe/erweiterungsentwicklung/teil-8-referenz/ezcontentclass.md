---
title: eZContentClass und ihre Attribute
audience: developer
category: extension-development
order: 80
summary: Inhaltsklassen und ihre Attribute aus PHP lesen - fetchByIdentifier(), classIDByIdentifier(), dataMap(), fetchAttributeByIdentifier(), Datentyp und Inhalt - über Bezeichner, nicht über IDs.
keywords: Inhaltsklasse, Klassenattribute, Bezeichner, Datentyp, eZContentClass, fetch, fetchByIdentifier, classIDByIdentifier, classIdentifierByID, fetchAllClasses, dataMap, fetchAttributeByIdentifier, eZContentClassAttribute, dataType, content, identifier
type: help
---
# eZContentClass und ihre Attribute

Teil 8, Referenz 8. Eine Inhaltsklasse (`kernel/classes/ezcontentclass.php`) beschreibt eine Art von Inhalt: ihren
Bezeichner (`article`), ihren Namen und ihre Attribute, jedes ein `eZContentClassAttribute`
(`kernel/classes/ezcontentclassattribute.php`) mit einem Bezeichner, einem Datentyp und Einstellungen. Builder legen
Klassen in der Administration an ([Inhaltsklassen für Builder](../teil-2-builder/inhaltsklassen-fuer-builder.md));
Erweiterungen lesen sie.

## Bezeichner, nicht IDs

Die numerische ID einer Klasse unterscheidet sich zwischen Installationen, ihr Bezeichner nicht. Schreiben Sie
Bezeichner in Code und Einstellungen, und schlagen Sie die ID nach, wenn Sie sie brauchen:

| Methode (statisch) | Gibt zurück |
|---|---|
| `classIDByIdentifier( $identifier )` | die ID zu einem Bezeichner |
| `classIdentifierByID( $id )` | den Bezeichner zu einer ID |
| `fetchByIdentifier( $identifier, $asObject = true, ... )` | die Klasse |
| `fetch( $id, $asObject = true, ... )` | die Klasse über ihre ID |
| `fetchAllClasses( $asObject = true, $includeFilter = true, $groupList = false )` | alle Klassen |
| `fetchList( ... )` | Klassen nach Bedingungen |

## Eine Klasse lesen

| Methode oder Attribut | Gibt zurück |
|---|---|
| `attribute( 'identifier' )`, `attribute( 'id' )` | Bezeichner und ID |
| `name( $languageLocale = false )` | den Namen |
| `dataMap()` | die Attribute, nach Attributbezeichner geordnet |
| `fetchAttributeByIdentifier( $identifier, $asObject = true )` | ein Attribut |

Für jedes Klassenattribut:

| Methode oder Attribut | Gibt zurück |
|---|---|
| `attribute( 'identifier' )` | z. B. `title` |
| `attribute( 'data_type_string' )` | den Datentyp, z. B. `ezstring` |
| `dataType()` | das Datentyp-Objekt |
| `content()` | die Einstellungen des Datentyps auf Klassenebene (was `classAttributeContent()` des Datentyps zurückgibt) |
| `attribute( 'is_required' )`, `attribute( 'is_searchable' )` | Kennzeichen |

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

Nur lesend auf einer Demo-Installation ausgeführt, listete dies die sieben Attribute der Klasse folder auf.

Der Inhalt eines Klassenattributs ist der richtige Weg, die Einstellungen eines Datentyps zu lesen. Eine
Datentyp-Methode wie `classAttributeContent()` statisch aufzurufen, ist unter PHP 8 ein fataler Fehler; fragen Sie
stattdessen das Klassenattribut, wie es se7enxweb/sevenx_themes_media im Commit f7d93c0 getan hat
([PHP-8-Fehler, die alten Code stoppen](../teil-4-aktualisieren/php-8-fehler-die-alten-code-stoppen.md)).

## Klassen anlegen

Legen Sie Klassen nicht mit rohem SQL an. Verwenden Sie Pakete
([Pakete (.ezpkg) und der Paketersteller](../teil-7-veroeffentlichen/pakete-ezpkg.md)), die Administration oder den
Assistenten **Content extension** unter **Setup > RAD**, der eine Klasse als Installationsskript schreibt.

## Verwandte Seiten

- Zurück: [eZContentObjectTreeNode](ezcontentobjecttreenode.md)
- Weiter: [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md)
- [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
