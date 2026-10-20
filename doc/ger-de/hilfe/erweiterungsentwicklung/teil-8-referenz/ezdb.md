---
title: "eZDB: die Datenbankschicht"
audience: developer
category: extension-development
order: 40
summary: Die eZDB-Methoden, die Erweiterungen verwenden - arrayQuery() mit offset und limit, query(), escapeString(), generateSQLINStatement(), Transaktionen, lastSerialID(), portable SQL-Hilfen - und sichere Werte.
keywords: Datenbank, Datenbankschicht, SQL, Transaktionen, sichere Werte, Escaping, eZDB, eZDBInterface, instance, arrayQuery, query, escapeString, generateSQLINStatement, begin, commit, rollback, lastSerialID, databaseName, concatString, subString, bitAnd
type: help
---
# eZDB: die Datenbankschicht

Teil 8, Referenz 4. `eZDB::instance()` gibt die Datenbankverbindung der Website zurück, ein Objekt einer Klasse, die
`eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`) erweitert: `eZMySQLiDB`, `eZPostgreSQLDB`, `eZSQLite3DB`,
`expMongoDB` oder ein Handler aus einer Erweiterung. Programmieren Sie gegen die Schnittstelle, dann läuft Ihr Code auf
jeder Engine mit SQL. Für Ihre eigenen Tabellen bevorzugen Sie `eZPersistentObject`
([eZPersistentObject](ezpersistentobject.md)), das auch mit MongoDB funktioniert. Wie Sie alten Datenbankcode
aktualisieren, steht in [Datenbankcode für PHP 8 und jede Engine](../../../../eng-us/help/extension-development/part-4-upgrading/database-code-for-php-8.md).

## Lesen und Schreiben

| Methode | Verwendung |
|---|---|
| `arrayQuery( $sql, $params = array(), $server = false )` | führt ein SELECT aus und gibt alle Zeilen als Arrays zurück; `$params` nimmt `offset` und `limit` auf, außerdem `column`, um eine Spalte als einfache Liste zurückzugeben |
| `query( $sql, $server = false )` | führt jede andere Anweisung aus; gibt ein Ergebnis-Handle oder `false` zurück |
| `lastSerialID( $table = false, $column = false )` | die ID der gerade eingefügten Zeile |
| `escapeString( $str )` | maskiert eine Zeichenkette zur Verwendung in einfachen Anführungszeichen |
| `generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )` | `column IN ( ... )` für eine Liste, wobei jeder Wert in `$type` umgewandelt wird (zum Beispiel `'int'`) |
| `errorMessage()` | der Text des letzten Fehlers |
| `databaseName()` | der Name der Engine (`mysql`, `postgresql`, `sqlite`, `mongo` ...) |

```php
<?php
$db = eZDB::instance();
$parentID = (int)$parentNodeID;
$rows = $db->arrayQuery( "SELECT id, name FROM myext_item WHERE parent_id = $parentID ORDER BY name",
                         array( 'offset' => 0, 'limit' => 20 ) );
$ids = $db->arrayQuery( 'SELECT id FROM myext_item', array( 'column' => 'id' ) );
```

## Immer sichere Werte

| Wert | Vorgehen |
|---|---|
| Ganzzahl | `(int)$value` |
| Zeichenkette | `"'" . $db->escapeString( (string)$value ) . "'"` |
| Liste von Ganzzahlen | `$db->generateSQLINStatement( $ids, 'id', false, true, 'int' )` |
| ein Spalten- oder Tabellenname aus einer Eingabe | nie; wählen Sie aus einer festen Liste in Ihrem Code |

## Transaktionen

```php
<?php
$db = eZDB::instance();
$db->begin();
$db->query( 'DELETE FROM myext_item_tag WHERE item_id = ' . (int)$itemID );
$db->query( 'INSERT INTO myext_item_tag ( item_id, tag_id ) VALUES ( ' . (int)$itemID . ', ' . (int)$tagID . ' )' );
$db->commit();
```

`begin()` und `commit()` lassen sich verschachteln: Der Kernel zählt sie mit, und nur das äußerste `commit()` schreibt
die Transaktion fest. `rollback()` macht die ganze Transaktion rückgängig. Jedes `begin()` braucht auf jedem Weg,
auch auf Fehlerwegen, genau ein `commit()` oder `rollback()`, sonst bleibt die Transaktion bis zum Ende des Requests
offen.

## Portable SQL-Hilfen

| Methode | Statt |
|---|---|
| `concatString( $strings = array() )` | `CONCAT()` oder `\|\|` |
| `subString( $string, $from, $len = null )` | Varianten von `SUBSTRING()` |
| `bitAnd( $arg1, $arg2 )`, `bitOr( $arg1, $arg2 )` | von Hand geschriebenem `&` und `\|` |

## Nicht unter MongoDB

Auf einer Installation, die ihre Inhalte in MongoDB speichert, führt der Adapter `expMongoDB` kein SQL aus:
`arrayQuery()` gibt keine Zeilen zurück und protokolliert eine Warnung
([MongoDB-Unterstützung im Kernel](../../../../eng-us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)). Code, der dort funktionieren
muss, verwendet `eZPersistentObject`.

## Verwandte Seiten

- Zurück: [eZDebug und Protokollierung](ezdebug-und-protokollierung.md)
- Weiter: [eZPersistentObject](ezpersistentobject.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Datenbankschema und .dba-Dateien](../teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
