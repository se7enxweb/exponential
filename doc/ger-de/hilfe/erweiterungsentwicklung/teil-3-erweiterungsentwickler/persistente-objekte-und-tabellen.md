---
title: Persistente Objekte und Ihre eigenen Tabellen
audience: developer, app-creator
category: extension-development
order: 260
summary: Die eigene Tabelle Ihrer Erweiterung über eZPersistentObject - definition(), anlegen, speichern, abrufen, Listen mit Bedingungen und Limits, zählen, entfernen, Template-Attribute, Transaktionen.
keywords: persistentes Objekt, eigene Tabelle, Datenbank, Transaktion, Definition, abrufen, speichern, zählen, eZPersistentObject, definition, fields, keys, increment_key, function_attributes, fetchObject, fetchObjectList, count, store, remove, removeObject, own table, database, transaction, eZDB
type: help
---
# Persistente Objekte und Ihre eigenen Tabellen

Teil 3, Kapitel 26. Inhalte gehören in Inhaltsklassen. Daten, die keine Inhalte sind (Protokolleinträge, Einstellungen
pro Benutzer, eine Warteschlange, Zähler), gehören in eine Tabelle Ihrer Erweiterung, auf die Sie über
`eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) zugreifen, die Klasse, die jede Tabellenklasse des
Kernels erweitert. Sie schreibt sicheres SQL für jede unterstützte Datenbank, sodass Sie selten selbst SQL schreiben.

## Die Klasse

`extension/mysite_hello/classes/mysitehellogreeting.php`:

```php
<?php
/**
 * One greeting, stored in the table mysite_hello_greeting.
 */
class mysiteHelloGreeting extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'      => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name'    => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true )
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'user' => 'user' ),
            'class_name' => 'mysiteHelloGreeting',
            'sort' => array( 'created' => 'desc' ),
            'name' => 'mysite_hello_greeting'
        );
    }

    /** Creates an unsaved greeting for the current user. */
    public static function create( $name )
    {
        return new self( array( 'name' => (string)$name,
                                'user_id' => eZUser::currentUserID(),
                                'created' => time() ) );
    }

    public static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
    }

    /** The newest greetings, newest first. */
    public static function fetchNewest( $limit = 10, $offset = 0 )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null, null,
                                                    array( 'created' => 'desc' ),
                                                    array( 'offset' => (int)$offset, 'length' => (int)$limit ) );
    }

    public static function countByUser( $userID )
    {
        return eZPersistentObject::count( self::definition(), array( 'user_id' => (int)$userID ) );
    }

    /** function attribute "user": the user who greeted */
    public function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }
}
```

Die Klasse hat `php -l` bestanden; mit geladenem Kernel lieferte `create( 'Ada' )` ein Objekt mit dem Namen und dem
aktuellen Benutzer, und dieselben Aufrufe von `fetchObjectList()` und `count()`, nur lesend gegen die Tabellenklasse
`eZSection` des Kernels ausgeführt, gaben wie erwartet Objekte und Anzahlen zurück.

## Die Definition

| Schlüssel | Bedeutung |
|---|---|
| `fields` | Spaltenname => `name` (der Attributname in PHP, CamelCase), `datatype` (`integer`, `string`, `float`), `default`, `required` |
| `keys` | die Spalten des Primärschlüssels |
| `increment_key` | die Auto-Increment-Spalte, nach dem ersten `store()` gefüllt |
| `function_attributes` | Attribute, die eine Methode berechnet: `'user' => 'user'` lässt `$greeting.user` in Templates funktionieren |
| `class_name` | die Klasse, die für abgerufene Zeilen erzeugt wird |
| `sort` | die Standardsortierung |
| `name` | der Tabellenname: Stellen Sie ihm den Namen Ihrer Erweiterung voran |

## Verwendung

```php
<?php
$db = eZDB::instance();
$db->begin();
$greeting = mysiteHelloGreeting::create( 'Ada' );
$greeting->store();                         // INSERT; $greeting->attribute( 'id' ) is now set
$greeting->setAttribute( 'name', 'Ada L.' );
$greeting->store();                         // UPDATE
$db->commit();

$latest = mysiteHelloGreeting::fetchNewest( 5 );
$mine = mysiteHelloGreeting::countByUser( eZUser::currentUserID() );
$greeting->remove();                        // DELETE
```

| Methode | Signatur (aus dem Kernel) |
|---|---|
| `fetchObject()` | `( $def, $field_filters, $conds, $asObject = true, $grouping = null, $custom_fields = null )` |
| `fetchObjectList()` | `( $def, $field_filters = null, $conds = [], $sorts = null, $limit = null, $asObject = true, $grouping = false, $custom_fields = null, $custom_tables = null, $custom_conds = null )` |
| `count()` | `( $def, $conds = null, $field = null )` |
| `store()` | `( $fieldFilters = null )` |
| `remove()` | `( $conditions = null, $extraConditions = null )` |
| `removeObject()` | `( $def, $conditions = null, $extraConditions = null )`, statisch |

Bedingungen sind `array( 'column' => value )`; die Werte werden für Sie maskiert. Weitere Operatoren:
`array( 'created' => array( '>', $since ) )`, `array( 'id' => array( array( 1, 2, 3 ) ) )` für eine IN-Liste. `$limit`
ist `array( 'offset' => ..., 'length' => ... )`.

## In Templates

Geben Sie Templates über eine Fetch-Funktion Zugriff ([Fetch-Funktionen](fetch-funktionen.md)), die diese Objekte
zurückgibt; dann funktionieren `{$greeting.name|wash}` und `{$greeting.user.login}` über `attribute()`.

## Transaktionen

Umschließen Sie mehrere Schreibvorgänge mit `$db->begin()` und `$db->commit()`; bei einem Fehler `$db->rollback()`.
Transaktionen lassen sich verschachteln: Der Kernel zählt sie mit und schreibt auf der äußersten Ebene fest.

## Die Tabelle selbst

Die Tabelle muss existieren, bevor irgendetwas davon funktioniert; wie Sie sie ausliefern und aktualisieren, zeigt das
nächste Kapitel, [Datenbankschema, .dba-Dateien und Upgrades](datenbankschema-und-dba-dateien.md).

## Wann nicht

- Daten, die Redakteure pflegen, versionieren und übersetzen, sind **Inhalte**: Verwenden Sie eine Klasse und
  Attribute.
- Daten pro Inhaltsattribut (viele Tags pro Feld) können in Ihrer eigenen Tabelle liegen, aber der Datentyp muss ihre
  Zeilen mit den Versionen kopieren und löschen ([Speicherung, Validierung und Templates von Datentypen](datentyp-speicherung-validierung-und-templates.md)).

## RAD

**Modul-Assistent** (`/setup/modulewizard`) schreibt ein Modul auf Grundlage vorhandener Tabellen, einschließlich der
Klassen für die persistenten Objekte.

## Verwandte Seiten

- Zurück: [Übersetzungen in einer Erweiterung](uebersetzungen-in-einer-erweiterung.md)
- Weiter: [Datenbankschema, .dba-Dateien und Upgrades](datenbankschema-und-dba-dateien.md)
- [eZPersistentObject](../teil-8-referenz/ezpersistentobject.md), [eZDB: die Datenbankschicht](../teil-8-referenz/ezdb.md)
- [Von Hand: Ihre eigene Tabelle mit eZPersistentObject](tutorials/von-hand-eigene-tabelle-mit-persistentem-objekt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
