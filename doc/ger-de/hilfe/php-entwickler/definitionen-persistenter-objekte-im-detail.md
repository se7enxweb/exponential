---
title: Definitionen persistenter Objekte im Detail
audience: php-developer, developer
category: php-development
order: 200
summary: eZPersistentObject für PHP-Entwickler - jeder Schlüssel von definition(), die Feldformen, die Bedingungssyntax von fetchObjectList() mit Operatoren, Bereichen und IN-Listen, Sortierung, Grenzen und eigene Felder, store() und sync(), removeObject(), Attribute in Templates und die häufigen Fehler.
keywords: persistentes Objekt, Definition, Felder, Schlüssel, Bedingungen, eigene Tabellen, eZPersistentObject, definition, fields, keys, increment_key, function_attributes, set_functions, class_name, sort, name, fetchObject, fetchObjectList, count, store, sync, remove, removeObject, conditions, BETWEEN, IN, custom_fields, active record
type: help
---
# Definitionen persistenter Objekte im Detail

Diese Seite richtet sich an PHP-Entwickler, die Daten in eigenen Tabellen speichern. `eZPersistentObject`
(`kernel/classes/ezpersistentobject.php`) bildet eine Tabelle über ein statisches Array `definition()` auf eine Klasse ab
und baut das SQL für jede unterstützte Datenbank. Die ersten Schritte finden Sie unter
[Persistente Objekte und eigene Tabellen](../ersteller/anwendungsentwickler/persistente-objekte-und-eigene-tabellen.md); diese Seite
ist die Referenz dahinter.

## Inhalt

1. [Die Schlüssel der Definition](#die-schlüssel-der-definition)
2. [Felddefinitionen](#felddefinitionen)
3. [Abrufen: die Parameter](#abrufen-die-parameter)
4. [Die Bedingungssyntax](#die-bedingungssyntax)
5. [Schreiben](#schreiben)
6. [Attribute und Templates](#attribute-und-templates)
7. [Häufige Fehler](#häufige-fehler)

## Die Schlüssel der Definition

| Schlüssel | Bedeutung |
|---|---|
| `fields` | die Spalten der Tabelle: Spaltenname => Felddefinition |
| `keys` | die Spalten, die eine Zeile identifizieren (der Primärschlüssel) |
| `increment_key` | die Spalte, die die Datenbank beim Insert füllt; nach `store()` am Objekt gesetzt |
| `function_attributes` | zusätzliche Attributnamen, die auf Methoden abgebildet werden, gelesen mit `attribute()` und in Templates |
| `set_functions` | Attributnamen, die auf von `setAttribute()` verwendete Methoden abgebildet werden |
| `class_name` | die Klasse, die für abgerufene Zeilen instanziiert wird |
| `sort` | Standardsortierung: Spalte => `asc` oder `desc` |
| `name` | der Tabellenname |
| `grouping` | Standardgruppierung für Listenabrufe |

```php
class acmeReportItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'      => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'status'  => array( 'name' => 'Status', 'datatype' => 'string', 'default' => 'open', 'required' => true ),
                'amount'  => array( 'name' => 'Amount', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'user' => 'user' ),
            'class_name' => 'acmeReportItem',
            'sort' => array( 'created' => 'desc' ),
            'name' => 'acme_report_item' );
    }

    public function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }
}
```

Die eigenen Klassen des Kernels haben dieselbe Form, zum Beispiel `eZSection::definition()` in
`kernel/classes/ezsection.php`.

## Felddefinitionen

Ein Feld ist entweder die Kurzform, Spalte => Name der Member-Variable (`'id' => 'ID'`), oder ein Array:

| Schlüssel | Bedeutung |
|---|---|
| `name` | der Name der Member-Variable |
| `datatype` | `integer`, `float`, `string`, verwendet beim Schreiben von Werten |
| `default` | der Wert eines neuen Objekts |
| `required` | ob das Feld gesetzt sein muss |

## Abrufen: die Parameter

```php
eZPersistentObject::fetchObject( $def, $fieldFilters, $conds, $asObject = true, $grouping = null, $customFields = null );
eZPersistentObject::fetchObjectList( $def, $fieldFilters = null, $conds = array(), $sorts = null, $limit = null,
                                     $asObject = true, $grouping = false, $customFields = null,
                                     $customTables = null, $customConds = null );
eZPersistentObject::count( $def, $conds = null, $field = null );
```

| Parameter | Bedeutung |
|---|---|
| `$fieldFilters` | `null` für alle Spalten oder eine Liste von Spaltennamen |
| `$conds` | die Bedingungen, siehe unten |
| `$sorts` | `null` verwendet das `sort` der Definition, `false` keine, oder Spalte => `asc`/`desc` |
| `$limit` | `array( 'offset' => 0, 'length' => 20 )` |
| `$asObject` | `true` für Objekte von `class_name`, `false` für einfache Zeilen |
| `$customFields`, `$customTables`, `$customConds` | zusätzliche Spalten, Tabellen und ein roher `WHERE`-Teil für Joins; maskieren Sie alles, was Sie in `$customConds` schreiben, selbst |

`fetchObjectList()` gibt ein Array zurück oder bei einem Datenbankfehler `null`: Sichern Sie vor `foreach` ab.

## Die Bedingungssyntax

| Bedingung | SQL |
|---|---|
| `array( 'id' => 5 )` | `id = 5` |
| `array( 'amount' => array( '>', 5 ) )` | `amount > 5` |
| `array( 'created' => array( false, array( $from, $to ) ) )` | `created BETWEEN $from AND $to` |
| `array( 'id' => array( array( 1, 5, 7 ) ) )` | `id IN ( 1, 5, 7 )` |
| mehrere Schlüssel | mit `AND` verbunden |

Werte in Bedingungen werden für Sie maskiert; das ist der Hauptgrund, persistente Objekte handgeschriebenem SQL
vorzuziehen ([SQL mit eZDB, sicher](sql-mit-ezdb.md)). Casten Sie IDs trotzdem (`(int)$id`), damit nie ein falscher
Typ die Abfrage erreicht.

## Schreiben

```php
$item = new acmeReportItem( array( 'user_id' => $userID, 'status' => 'open', 'amount' => 2, 'created' => time() ) );
$item->store();                                   // insert; 'id' is set from increment_key
$item->setAttribute( 'amount', 3 );
$item->sync();                                    // store only when an attribute changed (dirty data)
$item->remove();                                  // delete this row
eZPersistentObject::removeObject( acmeReportItem::definition(), array( 'user_id' => (int)$userID ) );
```

`store()`, `sync()` und `remove()` sind nach den eigenen Worten des Kernels „transaction unsafe“: Fassen Sie
zusammengehörige Schreibvorgänge in eine Transaktion (`eZDB::instance()->begin()` und `commit()`).

## Attribute und Templates

`attribute( $name )` liest ein Feld oder ruft das Funktionsattribut auf; `hasAttribute()` sagt, ob ein Name existiert.
Diese Schnittstelle verwenden Templates, sodass ein persistentes Objekt, das eine Fetch-Funktion zurückgibt, als
`$item.status` und `$item.user.login` lesbar ist ([Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md)).

## Häufige Fehler

| Fehler | Wirkung | Lösung |
|---|---|---|
| ein SQL-String in `$conds` | nicht unterstützt; verwenden Sie die Array-Syntax | `array( 'amount' => array( '>', 5 ) )` |
| unbegrenztes `fetchObjectList()` | Speicher und Zeit wachsen mit der Tabelle | übergeben Sie immer `$limit` |
| `foreach` über das Ergebnis ohne Prüfung | Warnung, wenn der Abruf fehlgeschlagen ist (`null`) | `if ( is_array( $rows ) )` |
| nicht deklarierte Member-Eigenschaften | Deprecation-Hinweise von PHP 8.2 in alten Klassen | deklarieren Sie die Member |
| mehrere Schreibvorgänge ohne Transaktion | halb geschriebene Daten nach einem Fehler | `begin()` und `commit()` darum herum |

## Verwandte Seiten

- [eZPersistentObject-Referenz](../erweiterungsentwicklung/teil-8-referenz/ezpersistentobject.md)
- [Das Muster Active Record](../../standards/muster/active-record.md)
- [Persistente Objekte und Tabellen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
