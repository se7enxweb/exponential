---
title: site.ini [DatabaseSettings]
audience: developer, admin
category: examples
summary: "The settings of [DatabaseSettings] in site.ini: SQLitePragmas, SQLiteTransactionWait, SQLiteCLITransactionWait, SQLiteBusyTimeout, SQLiteCLIBusyTimeout, DatabaseImplementation, Server, Port ..."
keywords: site.ini, DatabaseSettings, INI, setting, SQLitePragmas, SQLiteTransactionWait, SQLiteCLITransactionWait, SQLiteBusyTimeout, SQLiteCLIBusyTimeout, DatabaseImplementation, Server, Port, User, Password, Database, UseSlaveServer, ConnectRetries, Charset, UseBuiltinEncoding, Socket, QueryAnalysisOutput, SQLOutput, SlowQueriesOutput, DebugTransactions, InListChunkSize, InListChunkSizes, ImplementationAlias, MongoSQLFallback, MongoSQLFallbackMaxRows, MongoSQLFallbackLifetime, UsePersistentConnection, Transactions
type: generated
generated: exp:help:examples
---
# site.ini [DatabaseSettings]

The block `[DatabaseSettings]` of `settings/site.ini`, with 28 setting(s).

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
>   busy\_timeout           SQLiteBusyTimeout / SQLiteCLIBusyTimeout below; a  
>                          busy\_timeout=<ms> line here still wins over both  
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

## SQLiteCLITransactionWait

> SQLite only: the same wait on the command line (cronjobs, imports, scripts),  
> in seconds. Nobody waits for a page there, so a cronjob waits for a long  
> import's writes instead of stopping with "A database transaction in  
> Exponential failed".  

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLiteCLITransactionWait=300
*/ ?>
```

## SQLiteBusyTimeout

> SQLite only: how many milliseconds a single statement outside a transaction  
> (an INSERT of a link, an UPDATE of a cronjob) waits for another write to end  
> before it fails with "database is busy: the statement waited N s". It  
> queues on the same <database>.writer-lock as a transaction, and a lock SQLite  
> reports at once is tried again within this time. SQLiteBusyTimeout applies to  
> web requests (PHP-FPM, Velocity), SQLiteCLIBusyTimeout to the command line.  

Default: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLiteBusyTimeout=5000
*/ ?>
```

## SQLiteCLIBusyTimeout

Default: `30000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
SQLiteCLIBusyTimeout=30000
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

## InListChunkSize

> How many values at most go into one IN list when code asks for a set of ids  
> of any size (role assignments, user groups, nodes of a subtree, objects of a  
> removal ...) through expDBInList: a larger set is asked in several  
> statements, one per list, and the rows are put together. One statement with  
> tens of thousands of ids fails or gets slow: Oracle takes at most 1000  
> values per IN list (ORA-01795, ORA-00913), MySQL and MariaDB refuse a  
> statement longer than max\_allowed\_packet, SQLite one longer than  
> SQLITE\_MAX\_SQL\_LENGTH (1 MB by default), and PostgreSQL plans a long list  
> slowly. 1000 ids are about 10 KB of SQL, well inside every limit.  

Default: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
InListChunkSize=1000
*/ ?>
```

## InListChunkSizes

> The same per engine, by the name the driver gives (mysql, postgresql, sqlite,  
> oracle, mongo, pdo\_<driver>); an engine not listed uses InListChunkSize.  
> Oracle never takes more than 1000, whatever is set here. For example  
> InListChunkSizes\[postgresql\]=5000 in an override.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# an empty "InListChunkSizes[]" first replaces the shipped list; without it the items are added
InListChunkSizes[]
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

## MongoSQLFallback

> MongoDB only (expMongoDB): a SELECT the adapter cannot turn into MongoDB operations  
> (joins, GROUP BY with HAVING, UNION, derived tables) runs on an in-memory SQLite copy  
> of the collections it names, so it gives the answer MySQL, PostgreSQL and SQLite give  
> instead of an empty result. Needs PHP's sqlite3 extension; disabled = the empty result  
> and a "MONGO TODO" warning, as before 6.0.15.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
MongoSQLFallback=enabled
*/ ?>
```

## MongoSQLFallbackMaxRows

> A collection with more documents than this is not copied: the statement is refused.  

Default: `200000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
MongoSQLFallbackMaxRows=200000
*/ ?>
```

## MongoSQLFallbackLifetime

> Seconds a copy keeps answering when no write went through the adapter in between.  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
MongoSQLFallbackLifetime=2
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
