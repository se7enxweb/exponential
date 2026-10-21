---
title: The DatabaseSettings reference
audience: dba
category: database-administration
order: 6
summary: Every key of site.ini [DatabaseSettings] with its shipped default, the engines that read it and what it does - connection, character set, retries, persistent connections, replicas, transactions, debug output and the SQLite keys - plus the per-engine blocks to copy into settings/override.
keywords: DatabaseSettings, site.ini, DatabaseImplementation, Server, Port, Socket, User, Password, Database, Charset, UseBuiltinEncoding, ConnectRetries, UsePersistentConnection, UseSlaveServer, SlaveServerArray, Transactions, SQLOutput, SlowQueriesOutput, QueryAnalysisOutput, DebugTransactions, SQLitePragmas, SQLiteTransactionWait, ImplementationAlias
type: help
---
# The DatabaseSettings reference

This page is for database administrators who read or write the connection settings of an Exponential site. It lists
every key of `settings/site.ini`, block `[DatabaseSettings]`, as shipped in 6.0.x, with its default and the engines
that use it, and gives a minimal block per engine. Sources: `settings/site.ini` and
[9.1 How Exponential chooses a database driver](../../install/09-databases.md#91-how-exponential-chooses-a-database-driver).

> **Note**
> Never edit `settings/site.ini` itself: an upgrade replaces it. Put your values in
> `settings/override/site.ini.append.php` (the whole installation) or `settings/siteaccess/<name>/site.ini.append.php`
> (one siteaccess). See [Reading and changing database settings](reading-and-changing-database-settings.md).

## Contents

1. [Connection keys](#connection-keys)
2. [Behavior keys](#behavior-keys)
3. [Replica keys](#replica-keys)
4. [Debug keys](#debug-keys)
5. [SQLite keys](#sqlite-keys)
6. [Driver aliases](#driver-aliases)
7. [Keys outside this block](#keys-outside-this-block)
8. [Minimal blocks per engine](#minimal-blocks-per-engine)

## Connection keys

| Key | Shipped default | Used by | Meaning |
|---|---|---|---|
| `DatabaseImplementation` | `ezmysqli` | all | The driver alias ([Driver aliases](#driver-aliases)). The installers write the value for your engine. |
| `Server` | `localhost` | MySQL, PostgreSQL, MongoDB | Host name or address. Not used by Oracle. |
| `Port` | empty | MySQL, PostgreSQL, MongoDB | Empty means the driver's default port (3306, 5432, 27017). Not used by Oracle. |
| `Socket` | `disabled` | MySQL, MariaDB | A Unix socket path instead of host and port. |
| `User`, `Password` | `root`, empty | all but SQLite | The login. Never keep the shipped `root` in production. MongoDB requires a user even without access control. |
| `Database` | `nextgen` | all | The database name; for SQLite the file; for Oracle the connect string. |
| `Charset` | `utf-8` | MySQL, Oracle | The connection character set ([Character sets and collations](character-sets-and-collations.md)). |
| `UseBuiltinEncoding` | `true` | | "Use charset conversion routines in DB if possible" (comment in `site.ini`). |

## Behavior keys

| Key | Shipped default | Used by | Meaning |
|---|---|---|---|
| `ConnectRetries` | `0` | MySQL, PostgreSQL, SQLite | How often to try again when the first connection fails. MySQL and PostgreSQL wait between attempts; SQLite tries again at once. Oracle has its own `RetryCount` and falls back to this value. |
| `UsePersistentConnection` | `disabled` | MySQL, PostgreSQL, Oracle | Reuse a connection across requests of the same PHP process (`p:` host prefix for mysqli, `pg_pconnect` for PostgreSQL). |
| `Transactions` | `enabled` | all | Run grouped writes in transactions. Leave it on. |

## Replica keys

| Key | Shipped default | Meaning |
|---|---|---|
| `UseSlaveServer` | `disabled` | Send reads to a replica chosen at random per request; writes go to `Server`. MySQL and MariaDB only. |
| `SlaveServerArray[]`, `SlaveServerPort[]` | commented out | Replica hosts and ports. |
| `SlaverServerUser[]`, `SlaverServerPassword[]`, `SlaverServerDatabase[]` | commented out | Replica logins and database names. The keys really are spelled `Slaver...`. |

See [MySQL and MariaDB read replicas](mysql-read-replicas.md).

## Debug keys

| Key | Shipped default | Used by | Meaning |
|---|---|---|---|
| `SQLOutput` | `disabled` | all | Show statements in the debug output. |
| `SlowQueriesOutput` | `0` | all | With a value above 0, only statements slower than this many milliseconds. |
| `QueryAnalysisOutput` | `disabled` | MySQL, MariaDB | With `SQLOutput=enabled`, MySQL's `EXPLAIN` of each statement. |
| `DebugTransactions` | `disabled` | all | Record a stack trace for every begin and commit, to find an unbalanced transaction. |

These only show when the debug output is on for your address. See [Finding slow queries](finding-slow-queries.md).

## SQLite keys

| Key | Shipped default | Meaning |
|---|---|---|
| `SQLitePragmas[]` | empty | PRAGMAs over the driver's defaults, one `name=value` per entry. |
| `SQLiteTransactionWait` | `25` | Seconds a transaction waits for the writers ahead of it. Keep it below the web server's request timeout. |

Details: [SQLite: the write queue and locking](sqlite-write-queue-and-locking.md) and
[SQLite maintenance](sqlite-maintenance.md).

## Driver aliases

`ImplementationAlias[]` maps the value of `DatabaseImplementation` to a class. Shipped:
`mysql`, `mysqli`, `ezmysql`, `ezmysqli` to `eZMySQLiDB`; `postgresql`, `ezpostgresql`, `pgsql` to `eZPostgreSQLDB`;
`sqlite3` to `eZSQLite3DB`; `mongodb` to `expMongoDB`. The `ezoracle` extension adds `ezoracle` and `oracle`. An
extension can add more aliases from its own `site.ini.append.php`.

## Keys outside this block

| Setting | Where | Meaning |
|---|---|---|
| Oracle-only keys `OracleEmptyStringForNull`, `OracleCaseInsensitive`, `OracleCaseInsensitiveSort` | `site.ini [DatabaseSettings]`, read by `ezoracle` | See [Setting up Oracle](oracle-setup.md) |
| Connection pooling, retries, tracing for Oracle | `ezoracle.ini` | See [Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md) |
| The SQL query cache | `querycache.ini [QueryCacheSettings]` | See [The SQL query cache](sql-query-cache.md) |
| The DFS cluster metadata database | `file.ini [eZDFSClusteringSettings]` | See [Clustering: DFS and its metadata database](clustering-dfs-and-the-database.md) |
| Sessions in the database | `site.ini [Session] Handler` | See [Sessions in the database](sessions-in-the-database.md) |

## Minimal blocks per engine

```ini
# SQLite 3
[DatabaseSettings]
DatabaseImplementation=sqlite3
Database=exponential.db

# PostgreSQL
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential

# MariaDB and MySQL
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
User=exponential
Password=<the password>
Database=exponential
Charset=utf-8
```

MongoDB and Oracle blocks are on [Setting up MongoDB](mongodb-setup.md) and [Setting up Oracle](oracle-setup.md).
The placeholder values are examples; use your own and never commit the override file.

## Related pages

- [Reading and changing database settings](reading-and-changing-database-settings.md)
- [The database layer in one page](the-database-layer-in-one-page.md)
- [Database support, the settings](../database-support.md#the-settings-databasesettings-in-siteini)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
