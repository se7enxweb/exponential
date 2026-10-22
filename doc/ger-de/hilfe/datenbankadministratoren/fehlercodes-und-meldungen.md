---
title: Fehlercodes und Meldungen der Datenbank
audience: dba
category: database-administration
order: 44
summary: Wie Exponential Datenbankfehler meldet - die eigene Fehlernummer der Engine in errorNumber() und in eZDBException, die Codes für einen doppelten Schlüssel und eine fehlende Tabelle bei jedem Treiber, die Exception-Klassen, Standard- gegenüber Exception-Fehlerbehandlung und eine Liste der Meldungen, die Treiber und Installer schreiben, mit dem Ort, an dem sie erscheinen.
keywords: Fehlercodes, errorNumber, eZDBException, eZDBNoConnectionException, ERROR_HANDLING_EXCEPTIONS, doppelter Schlüssel, 1062, 1146, 23505, 42P01, SQLSTATE, SQLite 19, ORA-00001, ORA-00942, 11000, Fehlermeldungen, error.log, error codes, duplicate key, error messages
type: help
---
# Fehlercodes und Meldungen der Datenbank

Diese Seite richtet sich an Datenbankadministratoren und Entwickler, die die Datenbankfehler von Exponential lesen oder Code
schreiben, der darauf reagieren muss. Sie beantwortet: welche Nummer eine gescheiterte Anweisung auf jeder Engine meldet, wie
Fehler den Code erreichen und was die eigenen Meldungen der Treiber bedeuten. Quelle:
[Eine Website betreiben, kennen Sie Ihren Datenbanktreiber](../../anleitungen/betrieb-einer-website.md#7-den-eigenen-datenbanktreiber-kennen) und die
Treiber in `lib/ezdb/classes/`.

## Inhalt

1. [Die eigene Nummer der Engine](#die-eigene-nummer-der-engine)
2. [Zwei Arten der Fehlerbehandlung](#zwei-arten-der-fehlerbehandlung)
3. [Exception-Klassen](#exception-klassen)
4. [Meldungen und wo sie erscheinen](#meldungen-und-wo-sie-erscheinen)
5. [Eine Fehlerreferenz lesen](#eine-fehlerreferenz-lesen)

## Die eigene Nummer der Engine

Die Fehlernummer einer gescheiterten Anweisung ist die eigene der Datenbank, in `$db->errorNumber()` und, bei
Exception-Behandlung, als `getCode()` der geworfenen `eZDBException`:

| Treiber | Doppelter Schlüssel | Fehlende Tabelle |
|---|---|---|
| MySQL, MariaDB | `1062` | `1146` |
| PostgreSQL (der SQLSTATE, eine Zeichenkette) | `'23505'` | `'42P01'` |
| SQLite (der Ergebniscode) | `19` | `1` |
| Oracle (die ORA-Nummer) | `1` | `942` |
| MongoDB (nur `errorNumber()`; der Treiber antwortet mit `false` und wirft nichts) | `11000` | |

Hinweis zum Upgrade: `errorNumber()` von PostgreSQL war früher für jeden Fehler `1` und ist jetzt der SQLSTATE.

## Zwei Arten der Fehlerbehandlung

```php
$db = eZDB::instance();
$db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
try
{
    $db->query( "INSERT INTO my_table ( id ) VALUES ( 1 )" );
}
catch ( eZDBException $e )
{
    if ( $e->getCode() === 1062 || $e->getCode() === '23505' )   // MySQL, PostgreSQL
        { /* the row is there already */ }
}
$db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
```

- **Standard** (der Standard): Eine gescheiterte Anweisung wird protokolliert, und der Aufruf gibt `false` zurück; innerhalb
  einer Transaktion meldet der Kernel das Scheitern.
- **Exceptions**: `eZDBException` mit dem Code der Engine. Damit wirft MySQL unter PHP 8.1 und neuer `eZDBException` statt PHPs
  `mysqli_sql_exception`.
- Der SQLite-Treiber wirft innerhalb einer Transaktion; außerhalb antwortet er mit `false` und gesetztem `errorNumber()`.

Vergleichen Sie Codes je Engine, nie den Text der Meldung, der sich nach Engine, Version und Sprache unterscheidet.

## Exception-Klassen

| Klasse | Wann |
|---|---|
| `eZDBNoConnectionException` | die Verbindung ließ sich (nach `ConnectRetries`) nicht öffnen; der Einrichtungsassistent und `index.php` melden eine nicht erreichbare Datenbank. Auf SQLite enthält die Meldung den vollständigen Pfad der Datei |
| `eZDBException` | eine gescheiterte Anweisung bei eingeschalteter Exception-Behandlung |

## Meldungen und wo sie erscheinen

| Meldung | Geschrieben von | Wo | Seite |
|---|---|---|---|
| "Database implementation not supported: ..." | `eZDB::instance()` | `var/log/error.log` | [Einstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md) |
| "No database handler was found for '...'" | die Null-Datenbank `eZNullDB` | die Fehlerseite | dieselbe |
| "SQLite3 extension was not found, the DB handler will not be initialized." | `eZSQLite3DB` | `error.log` | [PHP-Erweiterungen](php-erweiterungen-je-engine.md) |
| "database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written" | `eZSQLite3DB` | `error.log`, die Fehlerseite | [Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md) |
| "the transaction could not start: ...; nothing was written" | `eZSQLite3DB` | dasselbe | dieselbe |
| "attempt to write a readonly database" | SQLite | `error.log` | [Rechte](sqlite-dateien-eigentuemer-und-rechte.md) |
| "Connection warning: could not set the connection charset to 'utf8'; it is '...'" | `eZMySQLiDB` | `warning.log` mit `AlwaysLog[]=warning` | [utf8mb4](mysql-und-mariadb-zeichensatz-und-innodb.md) |
| "The 'digest' function is not available in your database, and Exponential cannot run without it." | der Einrichtungsassistent | der Assistent | [PostgreSQL](postgresql-einrichten.md#pgcrypto) |
| "Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username" | der MongoDB-Treiber | `error.log` | [MongoDB](mongodb-einrichten.md#den-benutzer-anlegen) |
| `MONGO TODO arrayQuery: tables=[...] caller=...` | `expMongoDB` | die Logs | [Grenzen von MongoDB](mongodb-grenzen-und-betrieb.md#unübersetztes-sql-finden) |
| "The database schema could not be read." | die Upgrade-Prüfung | die Seite | [Das Schema prüfen](das-schema-nach-einem-upgrade-pruefen.md) |
| "The audit index classes are not in the autoload array" | `createaudittables.php` | die Shell | [Update auf 6.0.15](das-datenbank-update-6-0-15-anwenden.md) |

## Eine Fehlerreferenz lesen

Eine Fehlerseite zeigt eine kurze Referenz. `exp:logs` findet den Fehler anhand dieser Referenz und zeigt die Zeilen seiner
Anfrage, mit standardmäßig maskierten personenbezogenen Daten
([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Verwandte Seiten

- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
