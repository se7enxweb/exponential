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

> SQLite (DatabaseImplementation=sqlite3) only: the connection's PRAGMAs, one  
> per line as name=value, over the driver's defaults:  
>   synchronous=NORMAL     with WAL still crash-safe; FULL synced every commit  
>   cache\_size=-65536      64 MB page cache (SQLite's default is 2 MB)  
>   mmap\_size=268435456    read the file through 256 MB of mapped memory  
>   temp\_store=MEMORY      sorts and temporary tables in memory  
>   busy\_timeout=5000      wait up to 5 s for a lock instead of failing at once  
> Run php bin/php/ezexec.php on an ANALYZE script (or sqlite3 <file> ANALYZE)  
> after an install, so the query planner has statistics.  

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

> SQLite only: how many seconds a transaction (a publish, a save) waits at its  
> start for the other writers ahead of it, queued on <database>.writer-lock  
> next to the database file, before it gives up. Waiting there is safe: the  
> transaction has written nothing yet, and once it has started it cannot fail  
> for a lock. Keep it below the web server's request timeout, or the request  
> is killed before the driver can report "database is busy": Velocity ends a  
> request after its engine setting Q.webserver.requestTimeout, 30 s unless  
> changed (the client gets a 504), hence 25 here; under PHP-FPM the limit is  
> the pool's request\_terminate\_timeout. Raise both together.  

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

> Enable slave servers  
> The slave servers will only be used for read queries  
> Useful for load balanced environments  

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
> The number of times to reconnect if the first fails  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
ConnectRetries=0
*/ ?>
```

## Charset

> The charset to use for the database connection  
> If left empty the charset set in i18n.ini will be used,  
> which is often the most reasonable setting.  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Charset=utf-8
*/ ?>
```

## UseBuiltinEncoding

> Use charset conversion routines in DB if possible  

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

> This controls if the queries should have an analysis done  
> for the debug output (Requires SQLOutput=enabled)  
> NOTE: Currently this only works for MySQL.  

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

> When this setting is larger than 0 the Queries would only be shown in the  
> SQLOutput if the execution time took more than <setting> miliseconds.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SlowQueriesOutput=0
*/ ?>
```

## DebugTransactions

> If enabled the system will store stack traces for all begin/commits in the  
> system. When an unbalanced nested transactions are detected it will output  
> all begin/commits with information on where it occured as a deep tree.  
> This tree can be used to debug the code and find the missing commit.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
DebugTransactions=disabled
*/ ?>
```

## ImplementationAlias

> Alias for implementations  

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

> Use transactions to ensure that integrity of a database is maintained.  
> MySQL needs transaction safe-tables like the InnoDB table handler.  

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
