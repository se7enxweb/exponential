# PostgreSQL database support

This page is for administrators and database administrators who run an Exponential site on PostgreSQL. It is the
one page for PostgreSQL that every other database has: the support level, what the server and PHP need, the
settings, an install, the limits and the versions that were tested. The deeper chapters are linked where they say
more.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15:
> [Choosing a database](../../help/choosing-a-database.md)). Details: [Database
> support](../../help/database-support.md#support-levels-at-a-glance).

PostgreSQL is the recommended database server: strict SQL, row-level locking, replication and point-in-time recovery
of its own, and it is tested in CI on every push.

## Contents

1. [Support level](#support-level)
2. [Requirements](#requirements)
3. [PHP extension](#php-extension)
4. [Settings](#settings)
5. [Installing a site](#installing-a-site)
6. [Limits](#limits)
7. [Tested versions](#tested-versions)
8. [Databases that speak PostgreSQL's protocol](#databases-that-speak-postgresqls-protocol)
9. [Related pages](#related-pages)

## Support level

| Data tables | Full site | DFS | Multi-server cluster | Setup wizard | `exp:install` / kickstarter | xne upgrade tested |
|---|---|---|---|---|---|---|
| yes (`Engine=postgresql`) | yes | yes, in the kernel (`expDFSFileHandlerPostgresqlBackend`) | yes, with read replicas | yes, with `pgsql` | `--db=pgsql` | yes: 6.0.14 to 6.0.15 and an exact rollback, also of a failed step |

## Requirements

- **PostgreSQL 13 or later.** The installer creates `pgcrypto`, a trusted extension from PostgreSQL 13 on, so the
  database owner may create it; the driver computes MD5 with its `digest()`. If the server lacks it, install the
  distribution's PostgreSQL contrib package.
- **A database owned by the site's user**, in `UTF8`. From PostgreSQL 15 on, other users may no longer create tables
  in the `public` schema of a database they do not own:

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

The step-by-step server preparation (`pg_hba.conf`, `listen_addresses`, the grants) is in
[PostgreSQL setup](../../help/database-administrators/postgresql-setup.md).

## PHP extension

`pgsql`, bundled with PHP (`php-pgsql`, `php<version>-pgsql`). The setup wizard offers PostgreSQL, and the databases
of its protocol, whenever the PHP running it has `pgsql`. A data table connection or an import source over PDO uses
`pdo_pgsql`.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
UsePersistentConnection=disabled
```

`ezpostgresql`, `postgresql` and `pgsql` all name the driver `eZPostgreSQLDB`. Put the block in
`settings/override/site.ini.append.php` (the installers write it for you), then clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini`). Read replicas: `UseSlaveServer=enabled` with `SlaveServerArray[]`; only
plain SELECTs of anonymous GET and HEAD requests go to a replica, and only to one that is current
([Read replicas](../../bc/6.0/postgresql-read-replica.md)).

## Installing a site

```bash
export EXP_INSTALL_DB_PASSWORD='<the password>'
php bin/php/console exp:install --db=pgsql --db-host=localhost --db-user=exponential --db-name=exponential
```

In the browser, choose PostgreSQL on the setup wizard's database page. The kickstarter takes `Type=pgsql` in
`[database_choice]` ([console install](../../install/07-console-install.md)).

## Limits

- None specific to Exponential's own code. Extension code written for MySQL (backtick quoting, MySQL-only `SET`
  statements) fails here; write portable SQL ([cross-engine notes](../../install/09-databases.md#98-cross-engine-notes)).
- Auto-increment columns are sequences named `<table>_<column>_seq`. After loading rows with explicit ids, set the
  sequence to the highest id; the schema installer does this itself.
- PostgreSQL sorts NULL after every value in ascending order, where MySQL and SQLite sort it first.

## Tested versions

PostgreSQL 13, 15, 18 and 19 in CI for the whole database suite on every push, 13 to 19 for the DFS cluster backend;
18.7 and 19.0 also for the install, the upgrade backup, the upgrade's database stage, the MySQL to PostgreSQL
conversion and the read replica ([PostgreSQL 18 and 19](postgresql-18-and-19.md)). xne's integration suite upgrades a
real 6.0.14 site on PostgreSQL to the current code and rolls it back exactly.

## Databases that speak PostgreSQL's protocol

CockroachDB, YugabyteDB, Neon, Supabase and Amazon Aurora PostgreSQL install a whole site on the same extension with
a profile each, and Google Cloud Spanner (through PGAdapter) holds data tables:
[Databases of the PostgreSQL wire protocol](postgresql-wire-databases.md). Managed PostgreSQL services such as Amazon
RDS for PostgreSQL and Google Cloud SQL are plain PostgreSQL to the driver.

## Related pages

- [Database support](../../help/database-support.md) and [Choosing a database](../../help/choosing-a-database.md).
- [9.4 PostgreSQL](../../install/09-databases.md#94-postgresql), the operator's chapter.
- [PostgreSQL setup](../../help/database-administrators/postgresql-setup.md) and
  [PostgreSQL operations](../../help/database-administrators/postgresql-operations.md).
- [Clustering on PostgreSQL](postgresql-dfs-cluster.md) and [Clustering in Exponential 6.0](clustering-overview.md).
