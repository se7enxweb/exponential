---
title: Persistente Objekte und eigene Tabellen
audience: app-creator, developer
category: app-creation
order: 210
summary: Anwendungsdaten mit eZPersistentObject in eigenen Tabellen speichern - das Array definition(), fetchObject und fetchObjectList, store und remove, Funktionsattribute für Templates und wann Tabellen statt Inhaltsklassen passen.
keywords: eigene Tabellen, persistente Objekte, Datenbank, Active Record, Anwendungsentwickler, eZPersistentObject, custom tables, definition, fetchObject, fetchObjectList, store, remove, increment_key, function_attributes, active record, database, app creator
type: help
---
# Persistente Objekte und eigene Tabellen

Diese Seite richtet sich an Anwendungsentwickler, die Daten speichern, die keine Inhalte sind: Logs, Einstellungen je
Benutzer, importierte Datensätze, Zähler. `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) bildet eine
Tabelle auf eine Klasse ab, funktioniert mit jeder Datenbank, die Exponential unterstützt, und macht Zeilen in
Templates lesbar.

## Inhalt

1. [Inhaltsklasse oder eigene Tabelle?](#inhaltsklasse-oder-eigene-tabelle)
2. [Die Klasse](#die-klasse)
3. [Lesen](#lesen)
4. [Schreiben](#schreiben)
5. [In Templates](#in-templates)
6. [Die Tabelle anlegen](#die-tabelle-anlegen)

## Inhaltsklasse oder eigene Tabelle?

| Wählen Sie eine Inhaltsklasse, wenn | Wählen Sie eine Tabelle, wenn |
|---|---|
| die Redaktion die Daten in der Administration anlegt und bearbeitet | nur Code sie schreibt |
| sie Versionen, Übersetzungen und Rechte je Element braucht | nichts davon nötig ist |
| sie als Seiten erscheinen | es sich um interne Daten handelt |
| die Mengen moderat sind | es Millionen Zeilen oder viele Schreibvorgänge pro Sekunde gibt |

## Die Klasse

```php
<?php
class expDemoItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'         => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'user_id'    => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title'      => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'created'    => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'user' => 'user' ),
            'class_name' => 'expDemoItem',
            'sort' => array( 'created' => 'desc' ),
            'name' => 'expdemo_item' );
    }

    public function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }
}
```

Die Klassen des Kernels sehen genauso aus (`eZSection::definition()` in `kernel/classes/ezsection.php`).

## Lesen

```php
$item  = eZPersistentObject::fetchObject( expDemoItem::definition(), null, array( 'id' => (int)$id ) );
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null,
                                              array( 'user_id' => (int)$userID ),
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => 0, 'length' => 20 ) );
$count = eZPersistentObject::count( expDemoItem::definition(), array( 'user_id' => (int)$userID ) );
```

Bedingungen sind Arrays, niemals SQL-Zeichenketten: Die Werte werden für Sie maskiert.

## Schreiben

```php
$item = new expDemoItem( array( 'user_id' => $userID, 'title' => $title, 'created' => time() ) );
$item->store();                     // insert; the id is set from increment_key
$item->setAttribute( 'title', $newTitle );
$item->store();                     // update
$item->remove();                    // delete
eZPersistentObject::removeObject( expDemoItem::definition(), array( 'user_id' => (int)$userID ) );
```

Fassen Sie zusammengehörige Schreibvorgänge in eine Transaktion (`$db = eZDB::instance(); $db->begin(); ...
$db->commit();`) ([Datenbankzugriff](datenbankzugriff.md)).

## In Templates

Felder und `function_attributes` sind als `$item.title` und `$item.user.login` lesbar. Geben Sie persistente Objekte aus
Fetch-Funktionen zurück, um Templates Ihre Daten bereitzustellen
([Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)).

## Die Tabelle anlegen

Liefern Sie die Tabelle als Schemadatei der Erweiterung und einen Installationsbefehl aus; halten Sie eine einzige
Definition für jede Datenbank vor ([Schema und .dba-Dateien](schema-und-dba-dateien.md)).

## Verwandte Seiten

- [Datenbankzugriff](datenbankzugriff.md)
- [Active-Record-Muster](../../../../eng-us/standards/patterns/active-record.md)
- [Standard für Datenbanken und SQL](../../../../eng-us/standards/databases-and-sql.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
