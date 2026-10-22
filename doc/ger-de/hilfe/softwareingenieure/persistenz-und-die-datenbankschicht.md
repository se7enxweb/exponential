---
title: Persistenz und die Datenbankschicht
audience: software-engineer, developer
category: software-engineering
order: 160
summary: Wie Exponential mit seiner Datenbank spricht - die Fabrik eZDB und die Treiber, eZPersistentObject als Tabellen-Mapper, Transaktionen, die durch Zählen verschachtelt werden, der SQL-Abfrage-Cache, Isolationsstufen und die Regeln, die jede Abfrage einhalten muss - mit den Unterschieden, die ein Ingenieur mit ORM-Hintergrund erwarten sollte.
keywords: Persistenz, Datenbankschicht, eZDB, eZDBInterface, eZPersistentObject, definition, fetchObjectList, Transaktionen, begin, commit, rollback, Isolationsstufe, Abfrage-Cache, ORM, Active Record, Softwareingenieur, persistence, database layer, transactions, isolation level, query cache, active record
type: help
---
# Persistenz und die Datenbankschicht

Diese Seite richtet sich an Ingenieure, die Code schreiben oder prüfen, der die Datenbank liest und schreibt. Exponential
hat kein ORM im Sinne von Doctrine oder Hibernate: Es hat eine Datenbankfabrik mit einem Treiber je Engine und einen
Mapper im Stil von Active Record, `eZPersistentObject`, mit einer PHP-Klasse je Tabelle. Die maßgeblichen Regeln stehen
in [Standards für Datenbanken und SQL](../../standards/datenbanken-und-sql.md).

## Inhalt

1. [Die Teile](#die-teile)
2. [eZPersistentObject](#ezpersistentobject)
3. [Rohe Abfragen](#rohe-abfragen)
4. [Transaktionen](#transaktionen)
5. [Der SQL-Abfrage-Cache](#der-sql-abfrage-cache)
6. [Wenn Sie von einem ORM kommen](#wenn-sie-von-einem-orm-kommen)

## Die Teile

| Teil | Wo | Rolle |
|---|---|---|
| `eZDB` | `lib/ezdb/classes/` | Fabrik: `eZDB::instance()` gibt die Verbindung des aktuellen Siteaccess zurück |
| `eZDBInterface` | `lib/ezdb/classes/` | die gemeinsame Schnittstelle jedes Treibers |
| Treiber | `eZSQLite3DB`, `eZPostgreSQLDB`, `eZMySQLiDB`, `expMongoDB`, `eZNullDB`; `eZOracleDB` aus `ezoracle` | einer je Engine ([Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md)) |
| `eZPersistentObject` | `kernel/classes/ezpersistentobject.php` | bildet eine Klasse auf eine Tabelle ab |
| `eZDBQueryCache` | `lib/ezdb/classes/ezdbquerycache.php` | optionaler Cache für `SELECT`-Ergebnisse |
| Schema-Handler | `lib/ezdbschema/classes/` | lesen und schreiben `.dba`-Schemadateien je Engine |

Der Treiber wird durch `site.ini [DatabaseSettings] DatabaseImplementation` gewählt, aufgelöst über
`ImplementationAlias[]`.

## eZPersistentObject

Eine Unterklasse gibt ihre Tabellenbeschreibung aus `static function definition()` zurück: `fields`, `keys`,
`increment_key`, `class_name`, `name` (die Tabelle) und `function_attributes`. Lesen erfolgt über
`eZPersistentObject::fetchObject()` und `fetchObjectList()`; Schreiben über `store()` und `remove()`. Diese Methoden
maskieren Werte für Sie.

Neue Tabellen sollten eine Persistenzklasse erhalten statt handgeschriebenem SQL, und ihr Schema muss
`share/db_schema.dba`, die Update-Dateien jedes Treibers und jeden unterstützten Treiber erreichen
([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)). Durchgerechnete Beispiele:
[Persistente Objekte und eigene Tabellen](../ersteller/anwendungsentwickler/persistente-objekte-und-eigene-tabellen.md) und die
[Referenz zu eZPersistentObject](../erweiterungsentwicklung/teil-8-referenz/ezpersistentobject.md). Das Muster ist im
Katalog als [Active Record](../../standards/muster/active-record.md) beschrieben.

## Rohe Abfragen

Anweisungen werden als Zeichenketten gebaut; für jeden Wert aus einer Anfrage, einer Einstellung oder der Datenbank sind
daher zwei Regeln verpflichtend:

1. **Maskieren Sie jede Zeichenkette** mit `$db->escapeString( $value )`, innerhalb einfacher Anführungszeichen.
2. **Casten Sie jede Zahl** mit `(int)`, bevor sie ins SQL geht.

```php
$db = eZDB::instance();
$rows = $db->arrayQuery(
    "SELECT id, name FROM ezsection WHERE identifier = '" . $db->escapeString( $identifier ) . "'"
    . " AND id <> " . (int)$excludeId,
    array( 'offset' => 0, 'limit' => 10 )
);
```

Übergeben Sie Offset und Limit wie oben als Parameter, damit jeder Treiber seine eigene `LIMIT`-Syntax schreibt. Die
Unterschiede der Dialekte stehen unter [Portables SQL schreiben](portables-sql-schreiben.md).

## Transaktionen

- `$db->begin()` und `$db->commit()` werden durch Zählen verschachtelt: Nur das äußerste Paar sendet `BEGIN` und
  `COMMIT`. `$db->rollback()` rollt die ganze Transaktion zurück.
- Schreibvorgänge aus mehreren Anweisungen im Kernel-Code müssen in `begin()` und `commit()` eingeschlossen sein.
- **Kein Treiber setzt eine Isolationsstufe**; jede Engine läuft mit ihrem eigenen Standard. Code, der von einer abhängt,
  ist engine-abhängig.
- SQLite serialisiert Schreibende: Der Treiber nimmt eine Sperrdatei und führt `BEGIN IMMEDIATE` aus und wartet bis zu
  `[DatabaseSettings] SQLiteTransactionWait` Sekunden ([SQLite-Transaktionen](../../bc/6.0/sqlite-transaktionen.md)).
- Schreibvorgänge in MongoDB sind nicht transaktional: Jede Operation wird für sich festgeschrieben.

## Der SQL-Abfrage-Cache

`querycache.ini` kann `SELECT`-Ergebnisse im Speicher halten, bis ein Schreibvorgang in eine der gelesenen Tabellen sie
veralten lässt. Er ist standardmäßig aus (`Mode=off`), und der MongoDB-Treiber verwendet ihn nicht. Ist er an, muss alles,
was von außerhalb von Exponential in die Datenbank schreibt (ein SQL-Client, eine Wiederherstellung, ein anderes
Programm), ihn danach leeren:

```bash
php bin/php/ezcache.php --clear-id=querycache --allow-root-user
```

Siehe [Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md).

## Wenn Sie von einem ORM kommen

| Sie erwarten vielleicht | In Exponential |
|---|---|
| Entities mit Relationen und Lazy Loading | ein Definitions-Array je Tabelle; verwandte Objekte werden über ausdrückliche Aufrufe oder `function_attributes` geladen |
| Eine Unit of Work, die Änderungen schreibt | `store()` schreibt sofort |
| Prepared Statements mit gebundenen Parametern | Zeichenketten bauen mit `escapeString()` und Casts |
| Erzeugte Migrationen | `.dba`-Schemadateien und SQL-Update-Dateien je Treiber, von Hand geschrieben und angewendet |
| Ein Repository je Aggregat | statische Fetch-Methoden an der Persistenzklasse und Fetch-Funktionen für Templates |

## Verwandte Seiten

- [Datenbankzugriff](../ersteller/anwendungsentwickler/datenbankzugriff.md)
- [Referenz zu eZDB](../erweiterungsentwicklung/teil-8-referenz/ezdb.md)
- [Portables SQL schreiben](portables-sql-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
