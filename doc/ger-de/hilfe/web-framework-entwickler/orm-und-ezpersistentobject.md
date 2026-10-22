---
title: "ORM und Active Record: eZPersistentObject"
audience: web-framework-developer, developer
category: web-frameworks
order: 130
summary: Die Antwort von Exponential auf Eloquent, Doctrine, das Django-ORM und ActiveRecord für Ihre eigenen Tabellen - eZPersistentObject mit einem definition()-Array, fetchObject, fetchObjectList, count, store und remove, Funktionsattribute für Templates, Transaktionen - was es leistet, was nicht, und wann Sie stattdessen Inhaltsklassen verwenden.
keywords: ORM, Active Record, Eloquent, Doctrine, Django-ORM, ActiveRecord, eZPersistentObject, definition, fetchObject, fetchObjectList, store, remove, function_attributes, Relationen, Transaktionen, eZDB, Web-Framework-Entwickler, relations, transactions, web framework developer
type: help
---
# ORM und Active Record: eZPersistentObject

Diese Seite richtet sich an Entwickler, die Daten mit Eloquent, Doctrine, dem Django-ORM oder dem ActiveRecord von
Rails modellieren. Exponential bietet zwei Antworten. Für Daten, die Redakteure anlegen und pflegen, gibt es das
**Inhaltsmodell** (Klassen, Objekte, Versionen), das Sie konfigurieren statt programmieren
([Das Inhaltsmodell für ORM-Nutzer](das-inhaltsmodell-fuer-orm-nutzer.md)). Für die eigenen Tabellen Ihrer Anwendung
gibt es `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`), eine kleine Active-Record-Basisklasse, die der
Kernel selbst für seine Tabellen verwendet (`eZSection`, `eZRole` und viele weitere).

## Inhalt

1. [Inhaltsklasse oder eigene Tabelle?](#inhaltsklasse-oder-eigene-tabelle)
2. [Ein Modell](#ein-modell)
3. [Lesen und Schreiben](#lesen-und-schreiben)
4. [Relationen](#relationen)
5. [Transaktionen](#transaktionen)
6. [Im Vergleich mit vollständigen ORMs](#im-vergleich-mit-vollständigen-orms)

## Inhaltsklasse oder eigene Tabelle?

Aus [Persistente Objekte und eigene Tabellen](../ersteller/anwendungsentwickler/persistente-objekte-und-eigene-tabellen.md):

| Wählen Sie eine Inhaltsklasse, wenn | Wählen Sie eine Tabelle, wenn |
|---|---|
| Redakteure die Daten in der Administrationsoberfläche anlegen und bearbeiten | nur Code sie schreibt |
| Versionen, Übersetzungen und Berechtigungen pro Eintrag nötig sind | nichts davon benötigt wird |
| die Daten als Seiten erscheinen | es sich um interne Daten handelt |
| die Datenmengen moderat sind | es Millionen von Zeilen oder viele Schreibvorgänge pro Sekunde gibt |

## Ein Modell

```php
<?php
class expDemoItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'      => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title'   => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
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

Das `definition()`-Array ist das Mapping (was Doctrine in Attribute oder XML schreibt und Eloquent aus der Tabelle
ableitet): Felder mit Typen und Standardwerten, der Primärschlüssel, der Auto-Increment-Schlüssel, der Tabellenname
und **Funktionsattribute**, also berechnete Eigenschaften, die Templates als `$item.user.login` lesen können.

## Lesen und Schreiben

```php
$item  = eZPersistentObject::fetchObject( expDemoItem::definition(), null, array( 'id' => (int)$id ) );
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null,
                                              array( 'user_id' => (int)$userID ),
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => 0, 'length' => 20 ) );
$count = eZPersistentObject::count( expDemoItem::definition(), array( 'user_id' => (int)$userID ) );

$item = new expDemoItem( array( 'user_id' => $userID, 'title' => $title, 'created' => time() ) );
$item->store();                     // insert; the id is set from increment_key
$item->setAttribute( 'title', $newTitle );
$item->store();                     // update
$item->remove();                    // delete
```

Bedingungen sind Arrays, niemals SQL-Strings, und die Werte werden für Sie maskiert. Die meisten Projekte ergänzen
statische Hilfsmethoden wie `expDemoItem::fetch( $id )` und `expDemoItem::fetchByUser( $userID )`, so wie es die
Klassen des Kernels tun.

## Relationen

Es gibt keine deklarierten Relationen (`hasMany`, `ManyToOne`, `ForeignKey`) und keine Lazy-Loading-Proxys. Schreiben
Sie die Methode selbst, wie `user()` oben, und stellen Sie sie über `function_attributes` bereit, wenn Templates sie
benötigen. Für Many-to-many-Daten modellieren Sie die Verknüpfungstabelle als eigenes persistentes Objekt. Inhalte
haben eigene Relationen (Objektrelationen und den Baum), die Sie über die Inhalts-API lesen.

## Transaktionen

```php
$db = eZDB::instance();
$db->begin();
// several store() and remove() calls
$db->commit();
```

Verschachtelte `begin()`-Aufrufe werden gezählt; nur das äußerste `commit()` schreibt die Transaktion fest
([Datenbankzugriff und SQL](../ersteller/anwendungsentwickler/datenbankzugriff.md)). Halten Sie niemals eine Transaktion um einen
langsamen entfernten Aufruf herum offen.

## Im Vergleich mit vollständigen ORMs

| Funktion | Eloquent / Doctrine / Django-ORM / ActiveRecord | eZPersistentObject |
|---|---|---|
| Mapping | Konventionen, Attribute, Metadaten | das `definition()`-Array |
| Abfragen erstellen | fluente Query Builder, DQL, Querysets | Bedingungs-Arrays, Sortierungen und Limits; für alles Weitere SQL über `eZDB` ([Abfragen](inhalte-und-tabellen-abfragen.md)) |
| Relationen, Eager Loading | deklariert | Methoden, die Sie selbst schreiben |
| Unit of Work, Identity Map | Doctrine | nein: jedes `store()` schreibt sofort |
| Migrationen aus dem Modell | Doctrine, Django, Rails | nein: `.dba`-Schemadateien ([Migrationen](migrationen-und-dba-schemadateien.md)) |
| Datenbank-Portabilität | ja | ja: SQLite, PostgreSQL, MySQL/MariaDB, Oracle sowie MongoDB über den Treiber des Kernels |
| Zugriff aus Templates | über View-Models | eingebaut: Felder und Funktionsattribute sind in Templates lesbar |

Sie können weiterhin eine Composer-Bibliothek in Ihrer Erweiterung verwenden (der Autoloader von Composer wird von
`autoload.php` geladen), aber ein zweites ORM bedeutet eine zweite Verbindung, und Inhalte müssen trotzdem über die
Inhalts-API geschrieben werden, damit Versionen, Caches, Berechtigungen und Suche konsistent bleiben.

## Verwandte Seiten

- [Persistente Objekte und eigene Tabellen](../ersteller/anwendungsentwickler/persistente-objekte-und-eigene-tabellen.md)
- [Das Active-Record-Muster](../../standards/muster/active-record.md)
- [Standard für Datenbanken und SQL](../../standards/datenbanken-und-sql.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
