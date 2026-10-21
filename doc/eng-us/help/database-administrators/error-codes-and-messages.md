---
title: Database error codes and messages
audience: dba
category: database-administration
order: 44
summary: How Exponential reports database errors - the engine's own error number in errorNumber() and in eZDBException, the codes for a duplicate key and a missing table on each driver, the exception classes, standard versus exception error handling, and a list of the messages the drivers and installers write with where they appear.
keywords: error codes, errorNumber, eZDBException, eZDBNoConnectionException, ERROR_HANDLING_EXCEPTIONS, duplicate key, 1062, 1146, 23505, 42P01, SQLSTATE, SQLite 19, ORA-00001, ORA-00942, 11000, error messages, error.log
type: help
---
# Database error codes and messages

This page is for database administrators and developers who read Exponential's database errors, or write code that
must react to them. It answers: which number a failed statement reports on each engine, how errors reach code, and
what the drivers' own messages mean. Source:
[Operating a site, know your database driver](../../guides/operating-a-site.md#7-know-your-database-driver) and the
drivers in `lib/ezdb/classes/`.

## Contents

1. [The engine's own number](#the-engines-own-number)
2. [Two error handling modes](#two-error-handling-modes)
3. [Exception classes](#exception-classes)
4. [Messages and where they appear](#messages-and-where-they-appear)
5. [Reading an error reference](#reading-an-error-reference)

## The engine's own number

A failed statement's error number is the database's own, in `$db->errorNumber()` and, with exception handling, as
`getCode()` of the `eZDBException` thrown:

| Driver | Duplicate key | Missing table |
|---|---|---|
| MySQL, MariaDB | `1062` | `1146` |
| PostgreSQL (the SQLSTATE, a string) | `'23505'` | `'42P01'` |
| SQLite (the result code) | `19` | `1` |
| Oracle (the ORA number) | `1` | `942` |
| MongoDB (`errorNumber()` only; the driver answers `false` and throws nothing) | `11000` | |

Upgrade note: PostgreSQL's `errorNumber()` was `1` for every error before and is the SQLSTATE now.

## Two error handling modes

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

- **Standard** (the default): a failed statement is logged and the call returns `false`; inside a transaction the
  kernel reports the failure.
- **Exceptions**: `eZDBException` with the engine's code. With it, MySQL on PHP 8.1 and later throws `eZDBException`
  instead of PHP's `mysqli_sql_exception`.
- The SQLite driver throws inside a transaction; outside one it answers `false` with `errorNumber()` set.

Compare codes per engine, never the message text, which differs by engine, version and language.

## Exception classes

| Class | When |
|---|---|
| `eZDBNoConnectionException` | the connection could not be opened (after `ConnectRetries`); the setup wizard and `index.php` report a database that cannot be reached. On SQLite the message carries the full path of the file |
| `eZDBException` | a failed statement with exception handling on |

## Messages and where they appear

| Message | Written by | Where | Page |
|---|---|---|---|
| "Database implementation not supported: ..." | `eZDB::instance()` | `var/log/error.log` | [Reading and changing settings](reading-and-changing-database-settings.md) |
| "No database handler was found for '...'" | the null database `eZNullDB` | the error page | same |
| "SQLite3 extension was not found, the DB handler will not be initialized." | `eZSQLite3DB` | `error.log` | [PHP extensions](php-extensions-per-engine.md) |
| "database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written" | `eZSQLite3DB` | `error.log`, the error page | [Write queue](sqlite-write-queue-and-locking.md) |
| "the transaction could not start: ...; nothing was written" | `eZSQLite3DB` | same | same |
| "attempt to write a readonly database" | SQLite | `error.log` | [Permissions](sqlite-files-ownership-and-permissions.md) |
| "Connection warning: could not set the connection charset to 'utf8'; it is '...'" | `eZMySQLiDB` | `warning.log` with `AlwaysLog[]=warning` | [utf8mb4](mysql-and-mariadb-charset-and-innodb.md) |
| "The 'digest' function is not available in your database, and Exponential cannot run without it." | the setup wizard | the wizard | [PostgreSQL](postgresql-setup.md#pgcrypto) |
| "Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username" | the MongoDB driver | `error.log` | [MongoDB](mongodb-setup.md#create-the-user) |
| `MONGO TODO arrayQuery: tables=[...] caller=...` | `expMongoDB` | the logs | [MongoDB limits](mongodb-limits-and-operations.md#finding-untranslated-sql) |
| "The database schema could not be read." | the upgrade check | the page | [Checking the schema](checking-the-schema-after-an-upgrade.md) |
| "The audit index classes are not in the autoload array" | `createaudittables.php` | the shell | [6.0.15 update](applying-the-6-0-15-database-update.md) |

## Reading an error reference

An error page shows a short reference. `exp:logs` finds the error by that reference and shows the lines of its
request, with personal data masked by default ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).

## Related pages

- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [Reviewing extension SQL for every engine](portable-sql-for-extensions.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
