---
title: "Abfragen: Inhalts-Fetches, eZDB und SQL"
audience: web-framework-developer, developer
category: web-frameworks
order: 150
summary: Wie Sie in Exponential Daten abfragen, wenn Sie an einen Query Builder gewöhnt sind - Inhalts-Fetches mit Klassen-, Attribut- und Tiefenfiltern für Inhalte, Bedingungs-Arrays für persistente Objekte, eZDB arrayQuery und Maskierung für Ihr eigenes SQL, Transaktionen, Portabilität über SQLite, PostgreSQL, MySQL/MariaDB, Oracle und MongoDB sowie der Query-Cache.
keywords: Abfrage, Query Builder, SQL, eZDB, arrayQuery, escapeString, subTreeByNodeID, AttributeFilter, ClassFilterArray, SortBy, Limit, fetchObjectList, Transaktionen, Portabilität, SQLite, MongoDB, Web-Framework-Entwickler, query, transactions, portability, web framework developer
type: help
---
# Abfragen: Inhalts-Fetches, eZDB und SQL

Diese Seite richtet sich an Entwickler, die zu einem Query Builder greifen (`$qb->where()`, `Model::where()`,
Querysets, Arel). Der Kernel hat keinen allgemeinen Query Builder. Sie fragen Daten je nach Art auf eine von drei
Weisen ab.

## Inhalt

1. [Welcher Weg für welche Daten](#welcher-weg-für-welche-daten)
2. [Inhalte: Fetch-Parameter](#inhalte-fetch-parameter)
3. [Ihre Tabellen: Bedingungs-Arrays](#ihre-tabellen-bedingungs-arrays)
4. [Ihre Tabellen: SQL über eZDB](#ihre-tabellen-sql-über-ezdb)
5. [Portabilität](#portabilität)
6. [Performance](#performance)

## Welcher Weg für welche Daten

| Daten | Weg | Ergebnis |
|---|---|---|
| Inhalte (Knoten, Objekte) | `eZContentObjectTreeNode::subTreeByNodeID()` und im Template `fetch( 'content', 'list', ... )` | angewendete Berechtigungen, Sprachbehandlung, Sortierung nach Attribut |
| Ihre persistenten Objekte | `eZPersistentObject::fetchObjectList()` mit Bedingungen | Objekte, Maskierung, jede Datenbank-Engine |
| Berichte, Joins, Aggregate auf Ihren Tabellen | `eZDB::instance()->arrayQuery()` | Zeilen als Arrays |
| Volltextsuche über Inhalte | die Suchmaschine (eingebaut oder Solr über `ezfind`) | Relevanz, Facetten mit Solr ([Website-Suche](../ersteller/website-ersteller/suche-auf-der-website.md)) |

## Inhalte: Fetch-Parameter

```php
$nodes = eZContentObjectTreeNode::subTreeByNodeID( array(
    'ClassFilterType'  => 'include',
    'ClassFilterArray' => array( 'event' ),
    'AttributeFilter'  => array( array( 'event/start_date', '>=', time() ) ),
    'SortBy'           => array( array( 'attribute', true, 'event/start_date' ) ),
    'Depth'            => 3,
    'Limit'            => 20,
    'Offset'           => 0 ), $parentNodeID );
```

Das Parameter-Array ist der Query Builder für Inhalte: Klassenfilter, Attributfilter, Tiefe, Sortierung, Limit und
Offset. Die `content/read`-Richtlinie des aktuellen Benutzers wird angewendet, es sei denn, Sie übergeben
`'Limitation' => array()` – was Sie in Code, der Benutzern Ergebnisse anzeigt, niemals tun dürfen
([Die Inhalts-API](../ersteller/anwendungsentwickler/die-content-api.md#listen-abrufen)). Templates verwenden dieselben Parameter
in Kleinschreibung über `fetch( 'content', 'list', hash( ... ) )`
([Fetch-Funktionen](../ersteller/anwendungsentwickler/fetch-funktionen.md)).

## Ihre Tabellen: Bedingungs-Arrays

```php
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null,
                                              array( 'user_id' => (int)$userID ),
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => 0, 'length' => 20 ) );
```

Bedingungen sind Arrays; die Werte werden für Sie maskiert ([ORM: eZPersistentObject](orm-und-ezpersistentobject.md)).

## Ihre Tabellen: SQL über eZDB

Aus [Datenbankzugriff und SQL](../ersteller/anwendungsentwickler/datenbankzugriff.md):

```php
$db = eZDB::instance();
$rows = $db->arrayQuery( 'SELECT id, title FROM expdemo_item WHERE user_id = ' . (int)$userID . ' ORDER BY created DESC',
                         array( 'offset' => 0, 'limit' => 20 ) );
$db->query( "UPDATE expdemo_item SET title = '" . $db->escapeString( $title ) . "' WHERE id = " . (int)$id );
```

`arrayQuery()` und `query()` erwarten einen fertigen SQL-String, kein Statement mit Platzhaltern wie bei PDO: Casten
Sie Ganzzahlen, umschließen Sie Strings mit `escapeString()` und wählen Sie Bezeichner aus einer Allow-List.
`query()` gibt bei datenändernden Statements `true` oder `false` zurück.

## Portabilität

Der Treiber wird über `site.ini [DatabaseSettings] DatabaseImplementation` gewählt; der Kernel liefert Treiber für
MySQL/MariaDB (`eZMySQLiDB`), PostgreSQL, SQLite (`eZSQLite3DB`) und MongoDB (`expMongoDB`) mit, Oracle kommt über
die Erweiterung `ezoracle` ([Datenbankunterstützung](../datenbankunterstuetzung.md)). Damit Ihr SQL auf allen funktioniert:

- Verwenden Sie Offsets und Limits von `arrayQuery()`, nicht `LIMIT` im String.
- Vermeiden Sie Engine-spezifische Funktionen wie `IFNULL`, `GROUP_CONCAT` oder `SET @var := ...`.
- MongoDB kennt keine Joins: Der Treiber übersetzt gängige Statements; für Ihre eigenen Tabellen sollten Sie `eZPersistentObject` bevorzugen.
- Testen Sie auf SQLite (Standard für neue Installationen) und auf der Produktions-Engine.

## Performance

- Begrenzen Sie Listen immer; zählen Sie mit `subTreeCountByNodeID()` oder `eZPersistentObject::count()`.
- Holen Sie Daten in langen Skripten seitenweise und leeren Sie dazwischen die In-Memory-Caches (`eZContentObject::clearCache()`).
- Der SQL-Query-Cache kann Leseergebnisse vorhalten (`querycache.ini`) ([SQL-Query-Cache](../../bc/6.0/sql-abfrage-cache.md)).
- Die Debug-Leiste zeigt die Abfragen einer Seite und ihre Dauer ([Debugging](debugging-logging-und-fehler.md)).

## Verwandte Seiten

- [Datenbankzugriff und SQL](../ersteller/anwendungsentwickler/datenbankzugriff.md)
- [Datenbanken](../../installation/09-datenbanken.md)
- [Profiling und Performance](../ersteller/anwendungsentwickler/profiling-und-leistung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
