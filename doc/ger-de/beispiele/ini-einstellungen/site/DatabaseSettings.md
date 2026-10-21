---
title: site.ini [DatabaseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DatabaseSettings] in site.ini: SQLitePragmas, SQLiteTransactionWait, DatabaseImplementation, Server, Port, User, Password, Database ..."
keywords: Einstellung, site.ini, DatabaseSettings, INI, setting, SQLitePragmas, SQLiteTransactionWait, DatabaseImplementation, Server, Port, User, Password, Database, UseSlaveServer, ConnectRetries, Charset, UseBuiltinEncoding, Socket, QueryAnalysisOutput, SQLOutput, SlowQueriesOutput, DebugTransactions, ImplementationAlias, UsePersistentConnection, Transactions
type: generated
generated: exp:help:examples
---
# site.ini [DatabaseSettings]

Der Block `[DatabaseSettings]` von `settings/site.ini`, mit 20 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SQLitePragmas

> Nur SQLite (DatabaseImplementation=sqlite3): die PRAGMAs der Verbindung, eines  
> pro Zeile als name=value, über den Standards des Treibers:  
>   synchronous=NORMAL     mit WAL weiterhin absturzsicher; FULL synchronisierte jeden Commit  
>   cache\_size=-65536      64 MB Seiten-Cache (Standard von SQLite ist 2 MB)  
>   mmap\_size=268435456    die Datei über 256 MB eingeblendeten Speicher lesen  
>   temp\_store=MEMORY      Sortierungen und temporäre Tabellen im Speicher  
>   busy\_timeout=5000      bis zu 5 s auf eine Sperre warten, statt sofort zu scheitern  
> Führen Sie nach einer Installation php bin/php/ezexec.php mit einem ANALYZE-Skript (oder sqlite3 <file> ANALYZE) aus,  
> damit der Abfrageplaner Statistiken hat.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# ein leeres "SQLitePragmas[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SQLitePragmas[]
*/ ?>
```

## SQLiteTransactionWait

> Nur SQLite: wie viele Sekunden eine Transaktion (ein Veröffentlichen, ein Speichern) zu ihrem  
> Beginn auf die anderen Schreiber vor ihr wartet, eingereiht in <database>.writer-lock  
> neben der Datenbankdatei, bevor sie aufgibt. Dort zu warten ist sicher: Die  
> Transaktion hat noch nichts geschrieben, und sobald sie begonnen hat, kann sie nicht mehr  
> an einer Sperre scheitern. Halten Sie den Wert unter dem Anfrage-Timeout des Webservers, sonst wird die Anfrage  
> beendet, bevor der Treiber "database is busy" melden kann: Velocity beendet eine  
> Anfrage nach seiner Engine-Einstellung Q.webserver.requestTimeout, 30 s, sofern nicht  
> geändert (der Client erhält 504), daher hier 25; unter PHP-FPM ist die Grenze  
> request\_terminate\_timeout des Pools. Erhöhen Sie beide gemeinsam.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLiteTransactionWait=25
*/ ?>
```

## DatabaseImplementation

Standard: `ezmysqli`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezmysqli
*/ ?>
```

## Server

Standard: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Server=localhost
*/ ?>
```

## Port

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Port=
*/ ?>
```

## User

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
User=root
*/ ?>
```

## Password

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Password=
*/ ?>
```

## Database

Standard: `nextgen`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Database=nextgen
*/ ?>
```

## UseSlaveServer

> Slave-Server aktivieren  
> Die Slave-Server werden nur für lesende Abfragen verwendet  
> Nützlich in lastverteilten Umgebungen  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
UseSlaveServer=disabled
*/ ?>
```

## ConnectRetries

> SlaveServerArray\[\]=localhost  
> SlaveServerPort\[\]=  
> SlaverServerUser\[\]=nextgen  
> SlaverServerPassword\[\]=nextgen  
> SlaverServerDatabase\[\]=nextgen  
> Wie oft neu verbunden wird, wenn die erste Verbindung scheitert  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
ConnectRetries=0
*/ ?>
```

## Charset

> Der Zeichensatz für die Datenbankverbindung  
> Wenn leer, wird der in i18n.ini gesetzte Zeichensatz verwendet,  
> was oft die vernünftigste Einstellung ist.  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Charset=utf-8
*/ ?>
```

## UseBuiltinEncoding

> Wenn möglich die Routinen der Datenbank für die Zeichensatzumwandlung verwenden  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
UseBuiltinEncoding=true
*/ ?>
```

## Socket

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Socket=disabled
*/ ?>
```

## QueryAnalysisOutput

> Steuert, ob die Abfragen für die Debug-Ausgabe  
> analysiert werden (erfordert SQLOutput=enabled)  
> HINWEIS: Funktioniert derzeit nur mit MySQL.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
QueryAnalysisOutput=disabled
*/ ?>
```

## SQLOutput

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLOutput=disabled
*/ ?>
```

## SlowQueriesOutput

> Ist diese Einstellung größer als 0, werden Abfragen nur dann in der  
> SQLOutput angezeigt, wenn ihre Ausführung länger als <setting> Millisekunden dauerte.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SlowQueriesOutput=0
*/ ?>
```

## DebugTransactions

> Wenn aktiviert, speichert das System Stacktraces für alle begin/commit im  
> System. Werden unausgeglichene verschachtelte Transaktionen erkannt, gibt es  
> alle begin/commit mit der Stelle, an der sie auftraten, als tiefen Baum aus.  
> Mit diesem Baum lässt sich der Code debuggen und das fehlende commit finden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
DebugTransactions=disabled
*/ ?>
```

## ImplementationAlias

> Alias für Implementierungen  

Standard: ein Hash mit 9: `mysql=eZMySQLiDB`, `mysqli=eZMySQLiDB`, `postgresql=eZPostgreSQLDB`, `ezmysql=eZMySQLiDB`, `ezmysqli=eZMySQLiDB`, `ezpostgresql=eZPostgreSQLDB`, `pgsql=eZPostgreSQLDB`, `sqlite3=eZSQLite3DB`, `mongodb=expMongoDB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# ein leeres "ImplementationAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImplementationAlias[]
ImplementationAlias[mysql]=eZMySQLiDB
ImplementationAlias[mysqli]=eZMySQLiDB
ImplementationAlias[postgresql]=eZPostgreSQLDB
ImplementationAlias[ezmysql]=eZMySQLiDB
ImplementationAlias[ezmysqli]=eZMySQLiDB
ImplementationAlias[ezpostgresql]=eZPostgreSQLDB
ImplementationAlias[pgsql]=eZPostgreSQLDB
ImplementationAlias[sqlite3]=eZSQLite3DB
ImplementationAlias[mongodb]=expMongoDB
*/ ?>
```

## UsePersistentConnection

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
UsePersistentConnection=disabled
*/ ?>
```

## Transactions

> Transaktionen verwenden, um die Integrität der Datenbank zu wahren.  
> MySQL braucht transaktionssichere Tabellen wie den Tabellen-Handler InnoDB.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Transactions=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
