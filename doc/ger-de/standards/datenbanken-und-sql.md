---
title: Datenbank- und SQL-Standards
audience: developer, admin
category: standards
order: 130
summary: "Wie Exponential sich zum SQL-Standard (ISO/IEC 9075) und zu den Dialekten von SQLite, PostgreSQL, MySQL, MariaDB, Oracle und MongoDB verhält: die eZDB-Abstraktion, die Behandlung der Dialekte, Schemas, Zeichensätze und die Abdeckung durch die CI."
keywords: Datenbank, SQL, ISO/IEC 9075, SQL-Standard, Dialekt, eZDB, eZDBInterface, escapeString, arrayQuery, Transaktionen, Isolationsstufen, LIMIT, OFFSET, FETCH, Sequenzen, Autoinkrement, Verkettung, BIT_OR, Groß- und Kleinschreibung, Kollation, utf8mb4, AL32UTF8, UTF-8, dba, dbschema, SQLite, PostgreSQL, MySQL, MariaDB, Oracle, ezoracle, MongoDB, CI, database, SQL standard, dialect, transactions, isolation, sequences, auto increment, concatenation, case sensitivity, collation
type: reference
---
# Datenbank- und SQL-Standards

Diese Seite richtet sich an Entwickler, die SQL für Exponential schreiben oder Code von Extensions prüfen, der das tut,
und an Administratoren, die wissen müssen, wie sich die Datenbankschicht zum SQL-Standard verhält und wie gut jede
Datenbank abgedeckt ist. Sie beantwortet drei Fragen: welches SQL Exponential schreibt, wie die Unterschiede zwischen den
unterstützten Datenbanken behandelt werden und welche davon die CI des Projekts testet.

Welche Datenbanken in welcher Reihenfolge, mit welchen Versionen, Einstellungen, Installern und Sicherungen unterstützt
werden, ist Thema von [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md). Lesen Sie zuerst diese Seite; die
vorliegende wiederholt sie nicht.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| SQL, ISO/IEC 9075 | keine Ausgabe angestrebt | **Teilweise erfüllt** | der Kernel schreibt eine portable Teilmenge und fragt den Treiber nach dem, was sich unterscheidet (`lib/ezdb/classes/ezdbinterface.php`); kein Konformitätsanspruch |
| SQLite-3-Dialekt | SQLite 3.33 oder neuer | **Erfüllt** | `eZSQLite3DB` (`lib/ezdb/classes/ezsqlite3db.php`); CI: Unit-Tests des Treibers auf PHP 8.1 bis 8.5 |
| PostgreSQL-Dialekt | PostgreSQL 13 oder neuer | **Erfüllt** | `eZPostgreSQLDB`; CI: Datenbanktests auf `postgres:15` |
| MySQL-Dialekt | 8.0 getestet | **Erfüllt** | `eZMySQLiDB`; CI: Datenbanktests auf `mysql:8.0` |
| MariaDB-Dialekt | 10.5 als funktionierend beobachtet | **Nicht geprüft** | derselbe Treiber wie MySQL; nicht in der CI |
| Oracle-Dialekt | 19c oder neuer | **Nicht geprüft** | `eZOracleDB` in der Extension `ezoracle` 2.3.7; nicht in der CI des Kernels |
| MongoDB (kein SQL) | Server 6.0 oder neuer | **Teilweise erfüllt** | `expMongoDB` übersetzt SQL-förmige Aufrufe; dokumentierte Grenzen; nur Unit-Tests des Adapters |
| Unicode-Speicherung (UTF-8) | je Engine | **Teilweise erfüllt** | `utf8mb4`, `UTF8`, `AL32UTF8`; die Verbindung zu MySQL und MariaDB verwendet das Drei-Byte-`utf8` |

## Der SQL-Standard (ISO/IEC 9075)

### Was er ist

ISO/IEC 9075 ist der internationale Standard für SQL, von ISO und IEC in mehreren Teilen veröffentlicht; die aktuelle
Ausgabe ist SQL:2023. Keine Datenbank implementiert ihn vollständig, und jede Engine fügt ihre eigene Syntax hinzu.

### Wie Exponential ihn erfüllt

Exponential strebt keine bestimmte Ausgabe des Standards an. Sein Kernel-SQL ist in einer Teilmenge geschrieben, die
die unterstützten Engines gemeinsam haben, und alles, was sich unterscheidet, läuft über den Treiber:

- **Eine Schnittstelle.** Jede Datenbank wird über `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`) erreicht;
  `eZDB::instance()` (`lib/ezdb/classes/ezdb.php`) liefert den Treiber, den
  `settings/site.ini [DatabaseSettings] DatabaseImplementation` über `ImplementationAlias[]` nennt.
- **Hilfsfunktionen für das, was sich unterscheidet:** `subString()`, `concatString()`, `md5()`, `bitAnd()`, `bitOr()`,
  `escapeString()`, `lastSerialID()`, `generateSQLINStatement()`, `eZTableList()` und `databaseName()` (`mysql`,
  `postgresql`, `sqlite`, `mongo` oder `oracle`).
- **Zeilenbegrenzungen als Parameter.** `arrayQuery( $sql, array( 'offset' => ..., 'limit' => ... ) )` lässt den
  Treiber seine eigene Syntax anhängen; die Treiber akzeptieren nur numerische Werte (`is_numeric()` in jedem
  `arrayQuery()`).
- **Ein Engine-neutrales Schema.** Das Schema und die Anfangsdaten werden im generischen Format `.dba` gehalten
  (`share/db_schema.dba`, `share/db_data.dba`), das ein Schema-Handler in die DDL der jeweiligen Engine umwandelt
  (siehe [Schemas](#schemas-dba-dateien-und-schema-handler) unten).
- **Portable Bezeichner.** `eZLintSchema` (`lib/ezdbschema/classes/ezlintschema.php`) prüft das Schema gegen
  `settings/dbschema.ini [LintChecker]` (`TableLimit=26`, `FieldLimit=30`, `IndexLimit=30`), Grenzen, die Namen auf
  jeder Engine verwendbar halten.

### Status

**Teilweise erfüllt.** Das Kernel-SQL ist standardkonform, wo die Engines übereinstimmen, und angepasst, wo nicht. Teile
der Codebasis verwenden noch MySQL-Syntax, die der SQLite-Treiber umschreibt (siehe unten), und Code von Extensions ist
nicht abgedeckt.

### Bekannte Lücken

- Exponential erhebt keinen Anspruch auf Konformität mit irgendeinem Teil oder irgendeiner Ausgabe von ISO/IEC 9075.
- Prepared Statements mit gebundenen Parametern werden im SQL des Kernels nicht verwendet; `bindingType()` und
  `bindVariable()` existieren in der Schnittstelle, aber Kernel-Abfragen werden als Zeichenketten mit maskierten Werten
  aufgebaut.
- Handgeschriebenes SQL in Extensions verwendet oft reine MySQL-Syntax (Backticks, `LIMIT offset, count`,
  `SET`-Anweisungen); die [Hinweise für alle Engines](../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg) führen auf, was
  wo scheitert.

## Die eZDB-Regeln für sicheres SQL

Weil Anweisungen als Zeichenketten aufgebaut werden, sind zwei Regeln für jeden Wert verbindlich, der aus einer
Anfrage, einer Einstellung oder der Datenbank stammt:

1. **Maskieren Sie jede Zeichenkette** mit dem Treiber: `$db->escapeString( $value )`, innerhalb einfacher
   Anführungszeichen. Jeder Treiber verwendet die eigene Funktion der Engine (`mysqli_real_escape_string()`,
   `pg_escape_string()`, `SQLite3::escapeString()`; der Oracle-Treiber verdoppelt einfache Anführungszeichen). `null`
   wird zur leeren Zeichenkette.
2. **Casten Sie jede Zahl**, bevor sie in SQL gelangt: `(int)$id`. Ein Cast lässt sich nicht umgehen.

```php
$db = eZDB::instance();
$rows = $db->arrayQuery(
    "SELECT id, name FROM ezsection WHERE identifier = '" . $db->escapeString( $identifier ) . "'"
    . " AND id <> " . (int)$excludeId,
    array( 'offset' => 0, 'limit' => 10 )
);
```

- Bevorzugen Sie für Tabellen mit einer Definition `eZPersistentObject::fetchObjectList()` und die anderen Methoden von
  `eZPersistentObject`; sie maskieren für Sie (`kernel/classes/ezpersistentobject.php`).
- Transaktionen werden durch Zählen verschachtelt: `$db->begin()` und `$db->commit()` dürfen ineinander aufgerufen
  werden, und nur das äußerste Paar sendet `BEGIN` und `COMMIT` (`eZDBInterface::begin()`, `commit()`,
  `TransactionCounter`). `$db->rollback()` rollt die gesamte Transaktion zurück.
- Die Sicherheitskorrekturen von 6.0.13 (SQL-Injections, behoben mit Integer-Casts und `escapeString()`) sind in
  [Sicherheitshärtung von 6.0.13](../bc/6.0/haertung.md) beschrieben.

## Unterschiede der Dialekte, Treiber für Treiber

Was jeder Treiber dort tut, wo sich die Engines unterscheiden. MongoDB ist keine SQL-Engine; ihre Spalte beschreibt,
was der Adapter stattdessen tut („nicht untersucht" markiert eine Zelle, die diese Seite im Code des Adapters nicht
überprüft hat).

| Thema | SQLite 3 (`eZSQLite3DB`) | PostgreSQL (`eZPostgreSQLDB`) | MySQL, MariaDB (`eZMySQLiDB`) | Oracle (`eZOracleDB`, `ezoracle`) | MongoDB (`expMongoDB`) |
|---|---|---|---|---|---|
| Zeilenbegrenzung | `LIMIT offset, count` | `LIMIT count OFFSET offset` | `LIMIT offset, count` | `OFFSET n ROWS FETCH NEXT m ROWS ONLY` (die ISO-Form) | Optionen `limit` und `skip` der Abfrage |
| Neue IDs | `lastInsertRowID()` | Sequenz `<table>_<column>_seq`, `currval()` | `mysqli_insert_id()` (`AUTO_INCREMENT`) | Sequenz `s_<table>` (`se_<table>` für Tabellen von Extensions), `currval` | ein Zählerdokument pro Sequenz in der Collection `ezsequence` |
| Verkettung von Zeichenketten | `\|\|` | `\|\|` | `concat( ... )` | `\|\|` | `concat( ... )`, vom Adapter gelesen |
| Teilzeichenkette | `substr( s, n[, len] )` | `substring( s from n [for len] )` | `substring( s from n [for len] )` | `substr( s, n[, len] )` | nicht untersucht |
| MD5 | `md5()` als benutzerdefinierte Funktion registriert | `encode( digest( s, 'md5' ), 'hex' )`, benötigt `pgcrypto` | `MD5()` | `STANDARD_HASH( s, 'MD5' )`, Hex in Kleinbuchstaben | ein Literal in Anführungszeichen wird in PHP gehasht |
| Bitweises AND, OR | `&`, `\|`; Aggregate `BIT_OR`, `BIT_AND` registriert | `&`, `\|` | `&`, `\|` nach `SIGNED` gecastet | `BITAND()`; OR als `a + b - BITAND( a, b )` | `&`, `\|`, vom Adapter ausgewertet |
| MySQL-`UPDATE ... JOIN` | in eine Form umgeschrieben, die SQLite akzeptiert (`rewriteJoinedUpdate()`, benötigt 3.33) | nicht umgeschrieben | nativ | nicht umgeschrieben | nicht anwendbar |
| MySQL-Session-Anweisungen `SET` | akzeptiert und ignoriert | nur `SET NAMES` | nativ | nicht unterstützt | nicht anwendbar |
| Beginn einer Transaktion | `BEGIN IMMEDIATE`, nach der Schreibsperre | `BEGIN WORK` | `BEGIN WORK` | kein Auto-Commit (`OCI_DEFAULT`) | keiner: jede Operation committet für sich |
| Leere Zeichenkette | gespeichert als `''` | gespeichert als `''` | gespeichert als `''` | gespeichert als NULL; `OracleEmptyStringForNull=enabled` liefert `''` | nicht untersucht |
| Vergleich von Zeichenketten | `=` mit Beachtung der Groß- und Kleinschreibung, `LIKE` ohne für ASCII | mit Beachtung der Groß- und Kleinschreibung | ohne (Standard-Kollationen) | mit; `OracleCaseInsensitive=enabled` für linguistischen Vergleich | mit, sofern keine Kollation gesetzt ist |

Quellen: `lib/ezdb/classes/ezsqlite3db.php`, `ezpostgresqldb.php`, `ezmysqlidb.php`, `expmongodb.php` und
`extension/ezoracle/ezdb/dbms-drivers/ezoracledb.php`; die Hinweise für alle Engines in
[Kapitel 9, Abschnitt 9.8](../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg); die
[Spezifikation des SQLite3-Treibers](../spezifikationen/6.0/sqlite3-datenbanktreiber.md#sql-kompatibilitätsschicht).

**Isolationsstufen.** Kein Treiber setzt eine Isolationsstufe für Transaktionen; eine Suche in `lib/` und `kernel/`
nach `SET TRANSACTION` und `ISOLATION LEVEL` findet nichts. Jede Engine läuft mit ihrem eigenen Standard. SQLite
serialisiert Schreiber selbst: Der Treiber erwirbt eine exklusive Sperre auf `<database>.writer-lock` und führt dann
`BEGIN IMMEDIATE` aus, wobei er bis zu `[DatabaseSettings] SQLiteTransactionWait` Sekunden wartet (Standard 25).

## Schemas: .dba-Dateien und Schema-Handler

- Schema und Daten des Kernels werden als PHP-Arrays in `share/db_schema.dba` und `share/db_data.dba` gehalten.
  Extensions liefern auf dieselbe Weise ihre eigenen `share/db_schema.dba` und `share/db_data.dba` mit.
- `settings/dbschema.ini [SchemaSettings]` ordnet einem Datenbanktyp einen Handler zu: `SchemaHandlerClasses[mysql]` und
  `[mysqli]=eZMysqlSchema`, `[postgresql]=eZPgsqlSchema`, `[sqlite]` und `[sqlite3]=eZSQLiteSchema`,
  `[mongo]=expMongoSchema` (alle in `lib/ezdbschema/classes/`). Der Oracle-Handler `eZOracleSchema` kommt mit
  `ezoracle` (`extension/ezoracle/ezdb/dbms-schema/`).
- Engine-spezifische Übersetzungen stehen in derselben Datei, zum Beispiel `[mysql] IndexTranslation[]`
  (Präfixlängen für Indizes auf langen Textspalten) und `[postgresql] ColumnTypeTranslation[]`.
- Erzeugtes SQL pro Engine: `kernel/sql/mysql/`, `kernel/sql/postgresql/`, `kernel/sql/sqlite/`, dazu
  `kernel/sql/common/`. Aktualisierungen: `update/database/mysql/`, `update/database/postgresql/`,
  `update/database/sqlite/`; die von Oracle liegen in `ezoracle`; MongoDB hat keine.
- Werkzeuge: `bin/php/ezsqldumpschema.php` (Ausgabe nach `.dba`) und `bin/php/ezsqlinsertschema.php` (Laden in eine
  Engine mit `--type`). **Setup > Upgrade-Prüfung** vergleicht die Tabellen einer Website mit dem Schema des Releases
  (`lib/ezdbschema/classes/ezdbschemachecker.php`; [Die Aktualisierungsprüfung](../anleitungen/upgrade-pruefung.md)).

## Zeichensätze

| Datenbank | Speicherung | Hinweise |
|---|---|---|
| SQLite 3 | UTF-8 | nichts zu wählen |
| PostgreSQL | `UTF8` | anlegen mit `--encoding=UTF8 --template=template0` |
| MySQL, MariaDB | `utf8mb4` empfohlen | die Verbindungseinstellung `Charset=utf-8` wird als `utf8` gesendet, das beide Server als das Drei-Byte-`utf8mb3` behandeln |
| Oracle | `AL32UTF8` | der Treiber verbindet sich in AL32UTF8 und warnt, wenn ein Zeichensatz keine Entsprechung in Oracle hat |
| MongoDB | UTF-8 (BSON) | nichts zu wählen |

Die vollständige Tabelle mit Kollationen und der Prüfung des Assistenten steht in
[Datenbankunterstützung, Zeichensätze und Kollationen](../hilfe/datenbankunterstuetzung.md#zeichensätze-und-kollationen).

**Status: Teilweise erfüllt.** Text wird überall als UTF-8 gespeichert, aber auf MySQL und MariaDB können Zeichen
außerhalb der Basic Multilingual Plane (zum Beispiel Emoji) die Verbindung nicht passieren, selbst wenn die Tabellen
`utf8mb4` sind.

## Pro Datenbank

Die folgenden Mindestversionen sind die praktischen Mindestversionen, die in
[Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md#versionen-am-code-überprüft) genannt werden;
die älteren Untergrenzen des Einrichtungsassistenten stehen in `eZSetupDatabaseMap()` (`kernel/setup/ezsetupcommon.php`:
MySQL 4.1.1, PostgreSQL 8.0, SQLite 3.0.1, MongoDB 4.0, Oracle 19.0).

### SQLite 3

- Treiber `eZSQLite3DB`, Schema-Handler `eZSQLiteSchema`, PHP-Extension `sqlite3`. Praktisches Minimum 3.33.
- Ergänzt, was SQLite fehlt: `md5()`, die Aggregate `BIT_OR` und `BIT_AND` (beim Verbindungsaufbau registriert; laut
  Kommentar im Treiber fasst die Content-Engine Sprachmasken mit `BIT_OR` zusammen), das Umschreiben von
  `UPDATE ... JOIN` und eine Schreibwarteschlange.
- Spezifikation: [Der SQLite3-Datenbanktreiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md).
- CI: Der PHPUnit-Job (PHP 8.1 bis 8.5) führt `tests/tests/lib/ezdb/eZSQLite3DBQueryCountTest.php`,
  `eZSQLite3MirrorTest.php` und `eZSQLite3TransactionWaitTest.php` aus. Die Legacy-Datenbanktestsuite läuft in der CI
  nicht auf SQLite.
- **Status: Erfüllt.**

### PostgreSQL

- Treiber `eZPostgreSQLDB`, Schema-Handler `eZPgsqlSchema`, PHP-Extension `pgsql`. Praktisches Minimum 13 (der
  Installer legt die vertrauenswürdige Extension `pgcrypto` an).
- Sequenzen namens `<table>_<column>_seq` (`DEFAULT nextval(...)` in `ezpgsqlschema.php`).
- CI: Job „Database tests (postgresql, PHP 8.3)" in `.github/workflows/phpunit.yml`, Service `postgres:15`,
  `CREATE EXTENSION IF NOT EXISTS pgcrypto`, dann `tests/runtests.php --dsn postgresql://... --db-per-test`.
  Treibertests: `tests/tests/lib/ezdb/eZPostgreSQLDBTest.php`, `eZPostgreSQLDBEscapeStringNullTest.php`.
- **Status: Erfüllt.**

### MySQL

- Treiber `eZMySQLiDB`, Schema-Handler `eZMysqlSchema`, PHP-Extension `mysqli`. InnoDB-Tabellen (Transaktionen
  benötigen sie).
- Die einzige Engine mit eingebauten Lese-Replikaten (`UseSlaveServer`, `SlaveServerArray[]`).
- CI: Job „Database tests (mysql, PHP 8.3)", Service `mysql:8.0`, derselbe Lauf von `tests/runtests.php`.
  Treibertest: `tests/tests/lib/ezdb/eZMySQLiDBFKTest.php`.
- **Status: Erfüllt.**

### MariaDB

- Der MySQL-Treiber und der MySQL-Schema-Handler; alles oben Gesagte gilt.
- Nicht in der CI des Projekts. Der Job für MySQL 8.0 prüft denselben Treiber; MariaDB-spezifisches Verhalten wird nicht
  getestet.
- **Status: Nicht geprüft** (kein CI-Job; diese Seite hat es nicht ausgeführt).

### Oracle

- Treiber `eZOracleDB` und Schema-Handler `eZOracleSchema` in der Extension `ezoracle` (Version 2.3.7, von der
  `composer.json` im Wurzelverzeichnis als `~2.3.7` verlangt; nur für Oracle-Websites aktiviert). PHP-Extension `oci8`.
  Oracle 19c oder neuer in `AL32UTF8`.
- Verwendet die ISO-Syntax für Zeilenbegrenzungen `OFFSET ... ROWS FETCH NEXT ... ROWS ONLY`, `STANDARD_HASH` für MD5
  und `BITAND`.
- Nicht in der CI des Kernel-Repositorys; reine Oracle-Tests des Kernels werden auf anderen Engines übersprungen.
- **Status: Nicht geprüft** (kein CI-Job im Kernel-Repository; diese Seite hat es nicht ausgeführt).

### MongoDB

- Adapter `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) auf der PHP-Extension `mongodb` und der Bibliothek
  `mongodb/mongodb`; Schema-Handler `expMongoSchema`. MongoDB ist keine SQL-Datenbank: Der Adapter übersetzt die
  SQL-förmigen Aufrufe des Kernels in Operationen `find`, `aggregate`, Insert, Upsert und Delete.
- Transaktionen werden nicht verwendet: `beginQuery()`, `commitQuery()` und `rollbackQuery()` tun nichts, sodass eine
  Gruppe von Schreibvorgängen nicht atomar ist und ein Rollback nichts rückgängig macht.
- SQL, das er nicht übersetzen kann, liefert ein leeres Ergebnis und protokolliert eine Warnung; siehe die Grenzen in
  [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md#grenzen-von-mongodb) und
  [MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md).
- CI: die Suite `mongodb` von `phpunit.xml` (`expMongoDBAdapterTest.php`, `expMongoDBDeclaredDefaultTest.php`, kein
  Server). Die Suite `mongodb-live` braucht laufende Server und wird in der CI nicht ausgeführt.
- **Status: Teilweise erfüllt.**

## Bekannte Lücken

- MariaDB und Oracle haben keinen CI-Job im Kernel-Repository.
- Die Legacy-Datenbanksuite läuft nur auf MySQL und PostgreSQL, nur auf PHP 8.3; die anderen PHP-Versionen führen die
  Tests aus, die keinen Datenbankserver brauchen.
- Schreibvorgänge in MongoDB sind nicht transaktional.
- Kernel-SQL wird als Zeichenketten aufgebaut; die Sicherheit hängt davon ab, dass jeder Aufrufer die obigen Regeln zum
  Maskieren und Casten befolgt.
- Es wird keine Isolationsstufe gewählt; Code, der von einer abhängt, ist Engine-abhängig.
- `tests/tests/lib/ezdb/ezmysqlcharsettest.php` und die anderen Dateien ohne das Suffix `Test.php` in diesem Verzeichnis
  werden von den Suites in `phpunit.xml` nicht erfasst.

## Verwandte Seiten

- [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md) und [Kapitel 9, Datenbanken](../installation/09-datenbanken.md)
- [Der SQLite3-Datenbanktreiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md),
  [Datenbanktreiber und Installer, September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md),
  [SQLite- und Oracle-Treiber](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md)
- [MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md), [SQL-Abfrage-Cache](../bc/6.0/sql-abfrage-cache.md)
- [Kontinuierliche Integration](../spezifikationen/6.0/kontinuierliche-integration.md)
- [API- und Protokollstandards](apis-und-protokolle.md)
