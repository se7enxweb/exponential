---
title: eZPersistentObject
audience: developer
category: extension-development
order: 50
summary: Die Referenz von eZPersistentObject - die Schlüssel von definition(), fetchObject(), fetchObjectList() und seine Parameter, count(), store(), remove(), removeObject(), Attributzugriff - mit den Signaturen des Kernels.
keywords: persistentes Objekt, eigene Tabellen, Abrufen, Speichern, Löschen, Attribute, eZPersistentObject, definition, fields, keys, increment_key, function_attributes, fetchObject, fetchObjectList, count, store, remove, removeObject, updateObjectList, attribute
type: help
---
# eZPersistentObject

Teil 8, Referenz 5. `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) bildet eine Tabellenzeile auf ein
Objekt ab. Die eigenen Klassen des Kernels (Inhaltsobjekte, Knoten, Benutzer, Rollen) bauen darauf auf, und es ist der
empfohlene Weg, eigene Tabellen zu führen. Das Tutorial ist
[Persistente Objekte und Ihre eigenen Tabellen](../../../../eng-us/help/extension-development/part-3-extension-developer/persistent-objects-and-tables.md) und
[Ihre eigene Tabelle mit einem persistenten Objekt, von Hand](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/by-hand-own-table-with-persistent-object.md).
Diese Seite ist die Referenz.

## definition()

Eine `public static function definition()` gibt ein Array zurück:

| Schlüssel | Bedeutung |
|---|---|
| `fields` | Spaltenname => `array( 'name' => attribute name, 'datatype' => 'integer'\|'string'\|'float'\|'text', 'default' => ..., 'required' => bool )` |
| `keys` | die Spalten des Primärschlüssels |
| `increment_key` | die Spalte mit automatischer Hochzählung, falls vorhanden |
| `class_name` | die Klasse, zu der Zeilen werden |
| `name` | der Tabellenname |
| `sort` | Standardsortierung, `array( column => 'asc'\|'desc' )` |
| `function_attributes` | Attributname => Methodenname, für berechnete Attribute, die über `attribute()` (und in Templates) lesbar sind |
| `set_functions` | Attributname => Methodenname, aufgerufen von `setAttribute()` |

## Abrufen

```php
public static function fetchObject( $def, $field_filters, $conds, $asObject = true, $grouping = null, $custom_fields = null )

public static function fetchObjectList( $def, $field_filters = null, $conds = [], $sorts = null, $limit = null,
                                        $asObject = true, $grouping = false, $custom_fields = null,
                                        $custom_tables = null, $custom_conds = null )

public static function count( $def, $conds = null, $field = null )
```

| Parameter | Bedeutung |
|---|---|
| `$def` | Ihr `definition()` |
| `$field_filters` | die abzurufenden Spalten; `null` für alle |
| `$conds` | `array( column => value )`; ein Wert kann `array( '<', 10 )` sein (auch `>`, `<=`, `>=`, `<>`, `like`), `array( array( 1, 2, 3 ) )` für IN oder `array( false, array( 200, 500 ) )` für BETWEEN |
| `$sorts` | `array( column => 'asc' )`; `null` verwendet die Sortierung der Definition |
| `$limit` | `array( 'offset' => 0, 'limit' => 20 )` |
| `$asObject` | `false` gibt einfache Arrays zurück |

```php
<?php
class myExtItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'parent_id' => array( 'name' => 'ParentID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'class_name' => 'myExtItem',
            'sort' => array( 'name' => 'asc' ),
            'name' => 'myext_item',
        );
    }

    public static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
    }

    public static function fetchByParent( $parentID, $offset = 0, $limit = 20 )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null, array( 'parent_id' => (int)$parentID ),
                                                    null, array( 'offset' => $offset, 'limit' => $limit ) );
    }

    public static function countByParent( $parentID )
    {
        return eZPersistentObject::count( self::definition(), array( 'parent_id' => (int)$parentID ) );
    }
}
```

`fetchObject()` gibt `null` zurück, wenn nichts passt. Bedingungswerte maskiert und quotiert die Klasse (als
Zeichenketten); wandeln Sie Werte aus dem Request trotzdem in den erwarteten Typ um.

## Schreiben

| Methode | Verwendung |
|---|---|
| `new myExtItem( $row )`, dann `store( $fieldFilters = null )` | einfügen oder, wenn der Schlüssel existiert, aktualisieren; setzt nach einem Einfügen den Hochzählschlüssel |
| `setAttribute( $attr, $val )` | einen Wert ändern (ruft eine `set_functions`-Methode auf, falls definiert) |
| `remove( $conditions = null, $extraConditions = null )` | diese Zeile löschen |
| `removeObject( $def, $conditions = null, $extraConditions = null )` (statisch) | passende Zeilen löschen |
| `updateObjectList( $parameters )` (statisch) | passende Zeilen aktualisieren, ohne sie abzurufen |
| `sync( $fieldFilters = null )` | nur speichern, wenn es Änderungen gibt |

```php
<?php
$item = new myExtItem( array( 'parent_id' => 2, 'name' => 'First' ) );
$item->store();
$id = $item->attribute( 'id' );
```

## Attribute lesen

| Methode | Gibt zurück |
|---|---|
| `attribute( $attr, $noFunction = false )` | den Wert eines Felds oder das Ergebnis einer `function_attributes`-Methode |
| `hasAttribute( $attr )` | ob das Attribut existiert |
| `attributes()` | alle Attributnamen, einschließlich der Funktionsattribute |

Diese drei Methoden machen Objekte in Templates lesbar: `{$item.name}`.

## Auf jeder Engine

`eZPersistentObject` erzeugt das SQL für die konfigurierte Engine, und auf MongoDB-Installationen setzt der Adapter
seine Aufrufe in MongoDB-Abfragen um ([MongoDB-Unterstützung im Kernel](../../../../eng-us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)).
Außerdem hält es Felder, die die Klasse nicht deklariert, in einem internen Array, sodass die Abkündigung dynamischer
Eigenschaften in PHP 8.2 Unterklassen nicht betrifft ([Dynamische Eigenschaften](../../../../eng-us/help/extension-development/part-4-upgrading/dynamic-properties.md)).

## Verwandte Seiten

- Zurück: [eZDB: die Datenbankschicht](ezdb.md)
- Weiter: [eZContentObject](ezcontentobject.md)
- [Persistente Objekte und Ihre eigenen Tabellen](../../../../eng-us/help/extension-development/part-3-extension-developer/persistent-objects-and-tables.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
