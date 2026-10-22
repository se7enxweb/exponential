---
title: SQL mit eZDB, sicher
audience: php-developer, developer
category: php-development
order: 180
summary: SQL in Exponential mit eZDB schreiben - instance(), query() und arrayQuery() mit limit und offset, escapeString() für Strings und (int)-Casts für Zahlen, generateSQLINStatement(), Transaktionen mit begin(), commit() und rollback(), die zu prüfenden Rückgabewerte und wann Sie stattdessen eZPersistentObject verwenden.
keywords: SQL, SQL-Injection, Maskierung, Transaktionen, Rückgabewerte, Prepared Statements, eZDB, SQL, escapeString, int cast, SQL injection, arrayQuery, query, limit, offset, generateSQLINStatement, begin, commit, rollback, transactionCounter, lastSerialID, eZDBInterface, prepared statements
type: help
---
# SQL mit eZDB, sicher

Diese Seite richtet sich an PHP-Entwickler, die SQL schreiben. Exponential erreicht jede Datenbank über `eZDB`
(`lib/ezdb/classes/ezdb.php`), das einen Treiber zurückgibt, der `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`)
implementiert. Die Treiberschnittstelle hat keine gebundenen Parameter für beliebiges SQL, sodass die Sicherheit aus
zwei Gewohnheiten kommt: jeden String maskieren, jede Zahl casten.

## Inhalt

1. [Die Verbindung holen](#die-verbindung-holen)
2. [Lesen](#lesen)
3. [Schreiben](#schreiben)
4. [Maskieren: die zwei Regeln](#maskieren-die-zwei-regeln)
5. [Wertelisten](#wertelisten)
6. [Transaktionen](#transaktionen)
7. [Rückgabewerte](#rückgabewerte)
8. [Wann Sie kein SQL schreiben sollten](#wann-sie-kein-sql-schreiben-sollten)

## Die Verbindung holen

```php
$db = eZDB::instance();
```

`eZDB::instance()` gibt die gemeinsam genutzte Verbindung für die `[DatabaseSettings]` des aktuellen Siteaccess zurück.
Legen Sie mit `forceNewInstance` keine eigene Verbindung an, sofern Sie nicht wirklich eine zweite brauchen; das kostet
eine Verbindung und entzieht sich dem Transaktionszustand der Anfrage.

## Lesen

```php
$userID = (int)$userID;
$rows = $db->arrayQuery(
    "SELECT id, title, created FROM acme_report_item WHERE user_id = $userID ORDER BY created DESC",
    array( 'offset' => 0, 'limit' => 20 )
);
foreach ( $rows as $row )
{
    // $row['id'], $row['title'], $row['created'] are strings (or null)
}

$titles = $db->arrayQuery( "SELECT title FROM acme_report_item WHERE user_id = $userID",
                           array( 'column' => 'title' ) );     // a flat list of one column
```

Übergeben Sie `limit` und `offset` als Parameter, statt `LIMIT` ins SQL zu schreiben: Der Treiber schreibt die Syntax,
die seine Datenbank versteht (Oracle und SQLite unterscheiden sich von MySQL).

## Schreiben

```php
$title = $db->escapeString( $title );
$now = time();
$db->query( "INSERT INTO acme_report_item ( user_id, title, created ) VALUES ( $userID, '$title', $now )" );
$newID = $db->lastSerialID( 'acme_report_item', 'id' );
```

## Maskieren: die zwei Regeln

| Wert | Regel | Beispiel |
|---|---|---|
| String | `$db->escapeString()`, in einfachen Anführungszeichen | `"... WHERE title = '" . $db->escapeString( $title ) . "'"` |
| Ganzzahl | `(int)` | `"... WHERE id = " . (int)$id` |
| Gleitkommazahl | `(float)` | `"... WHERE amount > " . (float)$min` |
| Spalten- oder Tabellenname aus der Eingabe | eine Allow-Liste, niemals die Eingabe selbst | `in_array( $sort, array( 'title', 'created' ), true )` |
| Sortierrichtung | eine feste Auswahl | `$dir = $desc ? 'DESC' : 'ASC';` |

Casten oder maskieren Sie so nah am SQL wie möglich, auch wenn der Wert „bereits geprüft wurde“: Die nächste Änderung am
umgebenden Code kann die Prüfung entfernen. Werte aus `$Params`, `eZHTTPTool`, Einstellungen, Importen und der
Datenbank selbst sind alle Eingaben ([Eingaben validieren und casten](eingaben-validieren-und-casten.md)).

## Wertelisten

```php
$ids = array_map( 'intval', $ids );
$in = $db->generateSQLINStatement( $ids, 'id', false, true, 'int' );   // id IN ( 1, 2, 3 ); drivers may override it
$rows = $db->arrayQuery( "SELECT id, title FROM acme_report_item WHERE $in" );
```

Mit einem Typ (`'int'`) castet `generateSQLINStatement()` jedes Element über `implodeWithTypeCast()`; lassen Sie den
Treiber die Anweisung bauen, statt sie selbst zusammenzusetzen, da ein Treiber sie für seine Datenbank anders schreiben
kann.

## Transaktionen

```php
$db->begin();
$item->store();
$log->store();
$db->commit();
```

- `begin()` und `commit()` lassen sich verschachteln: Ein inneres Paar innerhalb eines äußeren wird nur gezählt
  (`transactionCounter()`), und das äußere `commit()` committet. Jedes `begin()` braucht auf jedem Pfad genau ein
  `commit()` oder `rollback()`, einschließlich vorzeitiger Rückgaben und Exceptions.
- Transaktionen werden nur verwendet, wenn `[DatabaseSettings] Transactions=enabled`.
- Halten Sie Transaktionen kurz; unter SQLite wartet ein Schreiber auf die Schreiber vor ihm
  ([SQLite-Transaktionen](../../bc/6.0/sqlite-transaktionen.md)).

## Rückgabewerte

| Aufruf | Gibt zurück |
|---|---|
| `query()` | `true` oder `false` für die meisten Anweisungen |
| `arrayQuery()` | ein Array von Zeilen (leer, wenn nichts passt) |
| `lastSerialID()` | die neue ID nach einem Insert |

Prüfen Sie das Ergebnis von `query()`, wenn es darauf ankommt. Verlassen Sie sich nicht auf Sitzungsvariablen
`SET @var := ...` über `query()`; für Inserts, die die neue ID brauchen, setzt `eZPersistentObject::store()` sie für
Sie. Ergebnisse unterscheiden sich in Grenzfällen auch zwischen Engines, zum Beispiel ein leeres Ergebnis und eine
fehlgeschlagene Anweisung beim MongoDB-Adapter ([Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md)).

## Wann Sie kein SQL schreiben sollten

| Bedarf | Besser |
|---|---|
| Zeilen Ihrer eigenen Tabelle | Bedingungen von `eZPersistentObject`, für Sie maskiert ([Definitionen persistenter Objekte im Detail](definitionen-persistenter-objekte-im-detail.md)) |
| Inhalte | `eZContentObjectTreeNode::subTreeByNodeID()` und die Content-API ([Die Content-API in PHP](../ersteller/anwendungsentwickler/die-content-api.md)) |
| das Schema | `.dba`-Dateien und der Schema-Handler ([Datenbankschema und .dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)) |

## Verwandte Seiten

- [eZDB-Referenz](../erweiterungsentwicklung/teil-8-referenz/ezdb.md)
- [Datenbankzugriff und SQL](../ersteller/anwendungsentwickler/datenbankzugriff.md)
- [Standard für Datenbanken und SQL](../../standards/datenbanken-und-sql.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
