---
title: Datenbankzugriff und SQL
audience: app-creator, developer
category: app-creation
order: 220
summary: Sicher und portabel mit der Datenbank arbeiten - eZDB, arrayQuery und query, Escaping und Typumwandlung, Transaktionen, die sechs unterstützten Datenbanksysteme und ihre Unterschiede und wann stattdessen eZPersistentObject die bessere Wahl ist.
keywords: Datenbank, SQL, Escaping, Transaktionen, Portabilität, Anwendungsentwickler, database, eZDB, arrayQuery, query, escapeString, transactions, begin, commit, rollback, SQLite, PostgreSQL, MySQL, MariaDB, MongoDB, Oracle, portability, app creator
type: help
---
# Datenbankzugriff und SQL

Diese Seite ist für Anwendungsentwickler, die SQL brauchen. Exponential läuft auf SQLite 3, PostgreSQL, MongoDB,
MariaDB, Oracle und MySQL ([Datenbankunterstützung](../../datenbankunterstuetzung.md)), daher zählt portabler Code mehr als bei
einem Produkt für nur ein Datenbanksystem. Bevorzugen Sie `eZPersistentObject`
([Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)); verwenden Sie SQL für Berichte und
Massenarbeit.

## Inhalt

1. [Die Verbindung](#die-verbindung)
2. [Lesen und schreiben](#lesen-und-schreiben)
3. [Escaping](#escaping)
4. [Transaktionen](#transaktionen)
5. [Portables SQL schreiben](#portables-sql-schreiben)

## Die Verbindung

```php
$db = eZDB::instance();
```

Die Instanz verwendet die `[DatabaseSettings]` des Siteaccess; eine Verbindung pro Anfrage (unter Velocity pro Worker).

## Lesen und schreiben

```php
$rows = $db->arrayQuery( 'SELECT id, title FROM expdemo_item WHERE user_id = ' . (int)$userID . ' ORDER BY created DESC',
                         array( 'offset' => 0, 'limit' => 20 ) );

$db->query( "UPDATE expdemo_item SET title = '" . $db->escapeString( $title ) . "' WHERE id = " . (int)$id );
```

`arrayQuery()` gibt ein Array von Zeilen (assoziativen Arrays) zurück; das zweite Argument nimmt `offset` und `limit`
entgegen, die der Treiber in die Syntax des jeweiligen Systems übersetzt. `query()` gibt bei Anweisungen, die Daten
ändern, `true` oder `false` zurück.

## Escaping

| Wert | Vorgehen |
|---|---|
| Ganzzahl | umwandeln: `(int)$id` |
| Zeichenkette | `$db->escapeString( $value )` innerhalb von Anführungszeichen |
| Liste von Ganzzahlen | `implode( ',', array_map( 'intval', $ids ) )` |
| Bezeichner (Tabelle, Spalte) | nie aus Eingaben; aus einer Positivliste wählen |

Bauen Sie SQL niemals ohne diese Maßnahmen aus Eingaben der Anfrage.

## Transaktionen

```php
$db->begin();
try
{
    // several writes
    $db->commit();
}
catch ( Exception $e )
{
    $db->rollback();
    throw $e;
}
```

Transaktionen lassen sich verschachteln: Nur das äußerste `commit()` schreibt. Unter SQLite warten schreibende Prozesse
in einer Schlange auf die Schreibsperre; halten Sie Transaktionen kurz ([SQLite-Transaktionen](../../../bc/6.0/sqlite-transaktionen.md)).

## Portables SQL schreiben

- Verwenden Sie Offset und Limit von `arrayQuery()`, nicht `LIMIT` in der Zeichenkette.
- Vermeiden Sie systemspezifische Funktionen (`IFNULL`, `GROUP_CONCAT`, `MOD()`): Die Korrekturen von eztags für SQLite
  haben `MOD()` durch `%` ersetzt ([eztags](../../../funktionen/6.0/erweiterungen/eztags.md)).
- Verlassen Sie sich nicht auf `SET @var := ...` oder Zeichenketten mit mehreren Anweisungen.
- MongoDB kennt keine Joins: Der Treiber des Kernels übersetzt gängige Anweisungen; für Ihre eigenen Tabellen sollten
  Sie `eZPersistentObject` bevorzugen, das jedes System unterstützt ([MongoDB-Unterstützung](../../../funktionen/6.0/mongodb-datenbankunterstuetzung.md)).
- Testen Sie auf SQLite und auf dem System der Produktivumgebung.
- Der SQL-Abfrage-Cache kann Leseergebnisse cachen (`querycache.ini`) ([SQL-Abfrage-Cache](../../../bc/6.0/sql-abfrage-cache.md)).

## Verwandte Seiten

- [Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)
- [Datenbankschema und .dba-Dateien](schema-und-dba-dateien.md)
- [Standard für Datenbanken und SQL](../../../standards/datenbanken-und-sql.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
