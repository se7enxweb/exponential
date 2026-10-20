---
title: site.ini [DatabaseSettings]
audience: developer, admin
category: examples
summary: "The settings of [DatabaseSettings] in site.ini: SQLitePragmas, SQLiteTransactionWait, DatabaseImplementation, Server, Port, User, Password, Database ..."
keywords: site.ini, DatabaseSettings, INI, setting, SQLitePragmas, SQLiteTransactionWait, DatabaseImplementation, Server, Port, User, Password, Database, UseSlaveServer, ConnectRetries, Charset, UseBuiltinEncoding, Socket, QueryAnalysisOutput, SQLOutput, SlowQueriesOutput, DebugTransactions, ImplementationAlias, UsePersistentConnection, Transactions
type: generated
generated: exp:help:examples
---
# site.ini [DatabaseSettings]

The block `[DatabaseSettings]` of `settings/site.ini`, with 20 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

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

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# an empty "SQLitePragmas[]" first replaces the shipped list; without it the items are added
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

Default: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLiteTransactionWait=25
*/ ?>
```

## DatabaseImplementation

Default: `ezmysqli`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezmysqli
*/ ?>
```

## Server

Default: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Server=localhost
*/ ?>
```

## Port

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Port=
*/ ?>
```

## User

Default: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
User=root
*/ ?>
```

## Password

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Password=
*/ ?>
```

## Database

Default: `nextgen`.

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

Default: `disabled`.

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

Default: `0`.

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

Default: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Charset=utf-8
*/ ?>
```

## UseBuiltinEncoding

> Use charset conversion routines in DB if possible  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
UseBuiltinEncoding=true
*/ ?>
```

## Socket

Default: `disabled`.

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

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
QueryAnalysisOutput=disabled
*/ ?>
```

## SQLOutput

Default: `disabled`.

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

Default: `0`.

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

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
DebugTransactions=disabled
*/ ?>
```

## ImplementationAlias

> Alias for implementations  

Default: a hash of 9: `mysql=eZMySQLiDB`, `mysqli=eZMySQLiDB`, `postgresql=eZPostgreSQLDB`, `ezmysql=eZMySQLiDB`, `ezmysqli=eZMySQLiDB`, `ezpostgresql=eZPostgreSQLDB`, `pgsql=eZPostgreSQLDB`, `sqlite3=eZSQLite3DB`, `mongodb=expMongoDB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# an empty "ImplementationAlias[]" first replaces the shipped list; without it the items are added
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

Default: `disabled`.

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

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
Transactions=enabled
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
