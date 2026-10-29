---
title: Supported engines and versions
audience: dba
category: database-administration
order: 3
summary: The six databases Exponential 6.0.x supports, in the project's official order, with the three version numbers that matter for each (the setup wizard's floor, the practical minimum of the current drivers and the versions seen working), the PHP range, and what the continuous integration really tests.
keywords: supported databases, versions, minimum version, SQLite 3.33, PostgreSQL 13, PostgreSQL 15, MongoDB 6.0, MariaDB, MySQL 8.0, Oracle 19c, AL32UTF8, wizard floor, CI, test coverage, PHP 8.0, PHP 8.5
type: help
---
# Supported engines and versions

This page is for database administrators who must state, in a design document or a hosting offer, which database
server versions an Exponential 6.0.x site may run on. It answers: which engines are supported, in which order the
project recommends them, which version numbers are real requirements and which are only historical floors, and how
well each engine is tested. The figures come from [Database support](../database-support.md), which was checked
against the code and the continuous integration of the 6.0.15 line.

## Contents

1. [The official order](#the-official-order)
2. [Three numbers per engine](#three-numbers-per-engine)
3. [The version table](#the-version-table)
4. [PHP versions](#php-versions)
5. [What the project tests](#what-the-project-tests)
6. [Engines that are not supported](#engines-that-are-not-supported)
7. [How to state support in your own documents](#how-to-state-support-in-your-own-documents)

## The official order

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time.

All six have a driver in the kernel or in a shipped extension, an entry in the setup wizard and a path in the
command-line installers. The order is a recommendation for new sites; it is not a ranking of quality. MySQL is last
because the project recommends the others first, not because it works less well
([Database support](../database-support.md#the-supported-databases-in-order)).

## Three numbers per engine

- **Wizard floor**: the oldest server version the setup wizard accepts, from `eZSetupDatabaseMap()` in
  `kernel/setup/ezsetupcommon.php`. It is kept from earlier releases so the wizard does not refuse an old server
  outright. It is not a recommendation.
- **Practical minimum**: the oldest version the current drivers really need, because of a feature they use.
- **Seen working**: the versions the project runs on its development server and in CI.

Plan with the practical minimum and a release your vendor still supports with security fixes.

## The version table

| Engine | Wizard floor | Practical minimum | Why | Seen working |
|---|---|---|---|---|
| SQLite 3 | 3.0.1 | **3.33** | The driver rewrites `UPDATE ... JOIN` into `UPDATE ... FROM` (`eZSQLite3DB::rewriteJoinedUpdate()`) | library 3.34.1 in PHP 8.5 |
| PostgreSQL | 8.0 | **13** | The installer creates `pgcrypto`, a trusted extension from 13 on; from 15 the site user must own the database | 13.23; 15 in CI |
| MongoDB | 4.0 | **6.0** server, PHP **8.2**, extension `mongodb` **1.18** | Adapter requirements ([MongoDB support](../../features/6.0/mongodb-database-support.md)) | server 8.3.13, extension 2.3.3, PHP 8.5 |
| MariaDB | 4.1.1 (the MySQL number) | a supported release with `utf8mb4` and InnoDB | | 10.5.29 |
| Oracle | 19.0 | **19c** | The driver uses `STANDARD_HASH` and `OFFSET`/`FETCH`; 11g and older are not supported | 26ai Free (23.26), oci8 3.4.1 |
| MySQL | 4.1.1 | a supported release with `utf8mb4` and InnoDB | | 8.0 in CI |

Check what your servers run before an install:

```bash
php --ri sqlite3 | grep -i "library"      # SQLite library inside PHP
psql --version                             # PostgreSQL client; SELECT version(); on the server
mysql --version                            # MySQL or MariaDB client; SELECT VERSION(); on the server
mongosh --eval 'db.version()'              # MongoDB server
```

On a running site, **Setup > System information**, card **Database**, shows the engine and the server version
([System information](../../guides/system-information.md)).

## PHP versions

Exponential 6.0.x runs on PHP 7.4 to 8.5 (`composer.json`). The database drivers follow that range with one
exception: the MongoDB adapter needs PHP 8.2 or later. Velocity, the recommended web server, needs PHP 8.1. For Oracle,
PECL oci8 3.4 serves PHP 8.4 and 8.5 and oci8 3.2 serves PHP 8.1 to 8.3
([PHP extensions per engine](php-extensions-per-engine.md)).

## What the project tests

| Engine | In CI | How |
|---|---|---|
| SQLite 3 | driver unit tests | PHPUnit on PHP 8.1 to 8.5, tests in `tests/tests/lib/ezdb/` |
| PostgreSQL | database tests | job "Database tests (postgresql, PHP 8.3)" with a `postgres:15` service and `pgcrypto` |
| MongoDB | adapter unit tests | the `mongodb` suite without a server; the `mongodb-live` group is excluded from CI |
| MariaDB | no | covered indirectly by the MySQL job (same driver) |
| Oracle | no | Oracle-specific kernel tests skip on other engines |
| MySQL | database tests | job "Database tests (mysql, PHP 8.3)" with a `mysql:8.0` service |

Source: `.github/workflows/` as described in [Database support](../database-support.md#test-coverage-ci). If your
production engine is not tested in CI (MariaDB, Oracle), run the upgrade on a staging copy first and keep the
restore tested ([Restoring a database](restoring-a-database.md)).

## Engines that are not supported

- **Microsoft SQL Server**: no driver, no schema handler, no installer entry in 6.0.x. It is known to be possible
  with additional work ([Microsoft SQL Server: status for DBAs](microsoft-sql-server-status.md)).
- **Any other engine** (for example a cloud database that speaks neither of these protocols): not supported. Managed
  services that run one of the six engines (a managed PostgreSQL or MySQL) are the same engine to Exponential; check
  that they allow the privileges listed on [Database accounts and privileges](database-accounts-and-privileges.md),
  and for PostgreSQL that `pgcrypto` can be created.

## How to state support in your own documents

1. Name the engine and a version at or above the practical minimum.
2. Name the PHP extension and its version (`pgsql`, `mysqli`, `sqlite3`, `mongodb` 1.18+, `oci8` 3.2/3.4).
3. Name the character set (`UTF8`, `utf8mb4`, `AL32UTF8`; see [Character sets and collations](character-sets-and-collations.md)).
4. Say whether the engine is in the project's CI, and if not, what you test yourself.
5. Link [Database support](../database-support.md) as the project's own statement, and give the date you checked it.

## Related pages

- [Choosing an engine](choosing-an-engine.md)
- [PHP extensions per engine](php-extensions-per-engine.md)
- [Databases in the official order](../hosting/basics/databases-in-the-official-order.md) (for hosting companies)
- [Chapter 2, Requirements](../../install/02-requirements.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
