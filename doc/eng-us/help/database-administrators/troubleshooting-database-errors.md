---
title: Troubleshooting database errors
audience: dba
category: database-administration
order: 43
summary: A symptom-first table of the database problems an Exponential site meets on each engine - the site looks down, missing tables, read-only SQLite files, lock timeouts, charset question marks, pgcrypto, sequences, MongoDB URI and library errors, Oracle quirks, failed update files - with the cause and the fix for each, and the order in which to investigate.
keywords: troubleshooting, database error, database cannot be reached, Database implementation not supported, missing tables, readonly database, database is busy, question marks, digest function, duplicate key, MongoDB URI, MongoDB\Client not found, ORA-00933, CLOB, SET storage_engine, duplicate column name, error.log
type: help
---
# Troubleshooting database errors

This page is for database administrators called in when an Exponential site has a database problem. It answers: where
to look first, and for each common symptom, the cause and the fix. Sources:
[12.10 Databases](../../install/12-troubleshooting.md#1210-databases) and the gotchas of each engine in
[chapter 9](../../install/09-databases.md).

## Contents

1. [Where to look first](#where-to-look-first)
2. [Every engine](#every-engine)
3. [SQLite](#sqlite)
4. [MySQL and MariaDB](#mysql-and-mariadb)
5. [PostgreSQL](#postgresql)
6. [MongoDB](#mongodb)
7. [Oracle](#oracle)
8. [Upgrades](#upgrades)
9. [When to ask for help](#when-to-ask-for-help)

## Where to look first

1. `var/log/error.log`: most database problems are named there ([Log viewer](../hosting/operations/log-viewer-and-rotation.md)).
2. The setting in effect: `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user`.
3. **Setup > System information**, if the administration still loads.
4. The database server's own log.
5. For a detailed view, the debug output for your address only ([Finding slow queries](finding-slow-queries.md)).

## Every engine

| Symptom | Cause | Fix |
|---|---|---|
| Every content page fails; `error.log` says "Database implementation not supported" | a mistyped `DatabaseImplementation`; the kernel falls back to a null database | correct the alias, clear the INI cache, restart Velocity |
| The "database cannot be reached" page | wrong host, user, password or database, or the server is down; the driver raises `eZDBNoConnectionException` | test the login with the engine's client from the web server |
| No page loads after switching engines | `DatabaseImplementation` still names the old engine | set it in `settings/override/site.ini.append.php`, clear the INI cache |
| A change of settings has no effect | INI cache not cleared, Velocity not restarted, or a siteaccess file overrides it | `exp:ini where`, clear, restart |
| Pages show old data after a manual SQL change or a restore | caches, including the SQL query cache | `php bin/php/ezcache.php --clear-all --allow-root-user` and `exp:velocity cache clear` |
| Works in the browser, fails in cron | the command line PHP lacks the database extension | `php -m` ([PHP extensions](php-extensions-per-engine.md)) |

## SQLite

| Symptom | Cause | Fix |
|---|---|---|
| "database is busy: the transaction could not start within N s ..." | another write held the writer lock for the whole `SQLiteTransactionWait` | nothing was written; retry; find the long writer ([Write queue](sqlite-write-queue-and-locking.md)) |
| "attempt to write a readonly database" | a `-shm`, `-wal` or the directory is not writable by this process | common group, `2775`, umask `0002` ([Permissions](sqlite-files-ownership-and-permissions.md)) |
| The site reports missing tables | a wrong `Database` value opened a new, empty file | compare the path in the error with `ls var/storage/sqlite3/` |
| Slow after an install or import | no statistics | `sqlite3 var/storage/sqlite3/exponential.db ANALYZE` |
| "SQLite3 extension was not found" | `sqlite3` not loaded in that PHP | install it |
| The database file can be downloaded | the web server serves `var/` | shipped rewrite rules or Velocity; check with `curl -sI` |

## MySQL and MariaDB

| Symptom | Cause | Fix |
|---|---|---|
| `?` where accented letters were | the connection fell back to the server's default charset | `AlwaysLog[]=warning`, look for "could not set the connection charset" ([utf8mb4](mysql-and-mariadb-charset-and-innodb.md)) |
| The wizard refuses the database's charset | the database is `latin1` or similar | recreate it in `utf8mb4` |
| Emoji are lost | the `utf8` (`utf8mb3`) connection | a known limit of the connection |
| Transactions do not roll back | MyISAM tables | `ezconvertmysqltabletype.php --newtype=InnoDB` |
| A replica shows old content | replication lag | monitor lag ([Read replicas](mysql-read-replicas.md)) |

## PostgreSQL

| Symptom | Cause | Fix |
|---|---|---|
| "The 'digest' function is not available in your database ..." | `pgcrypto` missing | `CREATE EXTENSION IF NOT EXISTS pgcrypto;` as owner or superuser |
| Connection refused | `listen_addresses`, `pg_hba.conf` | allow the web servers ([Setting up PostgreSQL](postgresql-setup.md#allow-the-connection)) |
| Duplicate key on insert after an import | sequences behind their tables | `setval()` ([Operating PostgreSQL](postgresql-operations.md#correcting-sequences)) |
| Permission denied to create a table (PostgreSQL 15+) | the account does not own the database | make it the owner |
| An extension's SQL fails here only | backticks or MySQL-only statements | [Reviewing extension SQL](portable-sql-for-extensions.md) |

## MongoDB

| Symptom | Cause | Fix |
|---|---|---|
| "Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username" | no `User` given | create a user in the site's database ([Setting up MongoDB](mongodb-setup.md#create-the-user)) |
| `Class "MongoDB\Client" not found` | the library `mongodb/mongodb` is missing | `composer require mongodb/mongodb` |
| Subtree and URL alias queries are slow | indexes not created | `bin/mongodb/create_indexes.js` |
| A list or page is empty that is not empty elsewhere | SQL the adapter cannot translate | `grep -rn "MONGO TODO" var/log/` ([Limits](mongodb-limits-and-operations.md)) |
| The setup wizard times out | the configuration step takes minutes | raise `request_terminate_timeout` or install from the console |

## Oracle

| Symptom | Cause | Fix |
|---|---|---|
| The driver is not found | `ezoracle` not active, or `oci8` not loaded | check `extension/ezoracle`, `ActiveExtensions[]`, `php -m` |
| `ORA-00933` in an extension's SQL | `AS` before a table alias | fix the extension |
| An empty string comes back as NULL | Oracle stores `''` as NULL | `OracleEmptyStringForNull=enabled` |
| A login works only in one case | binary comparison | `OracleCaseInsensitive=enabled`, then `ext:ezoracle:ci-indexes --create` |
| CLOB values cut short | an older `ezoracle` or a client that cuts long CLOBs above `LobPrefetch` 2000 | update the extension |
| Settings in your site extension are ignored | it is listed after `ezoracle` | list it before ([Oracle tuning](oracle-connections-and-tuning.md#which-settings-win)) |

## Upgrades

| Symptom | Cause | Fix |
|---|---|---|
| `SET storage_engine=InnoDB;` fails | an update file from before October 2026 | take the current file (`SET default_storage_engine`) |
| "duplicate column name" on SQLite | the 6.0.15 file ran a second time | the database is already updated |
| An update file failed half way | | restore the backup and apply the file again from the start |
| Every insert fails on PostgreSQL after coming from 5.x | old `<table>_s` sequence names | the current 6.0 update file renames them |
| "The audit index classes are not in the autoload array" | autoloads stale | `php bin/php/ezpgenerateautoloads.php -k` |

## When to ask for help

Collect the error lines, the engine and version, and the report from **Setup > System information** (it never contains
passwords), then use the channels in [When something goes wrong](../when-something-goes-wrong.md). Report security
problems privately, never in a public issue.

## Related pages

- [Database error codes and messages](error-codes-and-messages.md)
- [Site down runbook](../hosting/runbooks/site-down.md)
- [Monitoring the database](monitoring-the-database.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
