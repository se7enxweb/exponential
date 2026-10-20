---
title: PostgreSQL for hosts
audience: hosting, admin
category: hosting
order: 80
summary: PostgreSQL, the recommended database server for Exponential, from a host's point of view: versions, the database owner rule, the pgcrypto extension, settings, installing with exp:install, sequences, backups and the usual problems.
keywords: PostgreSQL, Postgres, pgsql, ezpostgresql, pgcrypto, digest, database owner, PostgreSQL 13, PostgreSQL 15, createdb, createuser, template0, UTF8, pg_dump, pg_restore, sequences, setval, pg_hba.conf, listen_addresses, hosting
type: help
---
# PostgreSQL for hosts

This page is for system administrators at a hosting company who provide PostgreSQL databases for Exponential sites.
It answers: which PostgreSQL versions to run, how to create each site's database and user so the installer succeeds,
what the `pgcrypto` extension has to do with it, and how to back up and troubleshoot. PostgreSQL is second in the
official order and the recommended database server. Sources: [Database support](../../database-support.md#2-postgresql)
and [chapter 9.4](../../../install/09-databases.md#94-postgresql).

## Contents

1. [Why PostgreSQL](#why-postgresql)
2. [Versions and extension](#versions-and-extension)
3. [Creating a database per site](#creating-a-database-per-site)
4. [pgcrypto](#pgcrypto)
5. [Settings and install](#settings-and-install)
6. [Sequences](#sequences)
7. [Backups](#backups)
8. [Problems and their causes](#problems-and-their-causes)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## Why PostgreSQL

The project calls PostgreSQL the recommended database server: strict SQL, row-level locking, replication and
point-in-time recovery of its own, and a database test job in the project's CI on every push (PostgreSQL 15). It is
the first choice when a site needs a database server, for example when several web servers share one database
([Database support](../../database-support.md#the-supported-databases-in-order)).

## Versions and extension

| Item | Value | Source |
|---|---|---|
| Wizard floor | 8.0 | `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php` |
| Practical minimum | **13** | the installer creates `pgcrypto`, a trusted extension from PostgreSQL 13 on |
| From 15 on | the site's user must own the database | PostgreSQL no longer lets other users create tables in `public` |
| Seen working | 13.23 on the development server; 15 in CI | [versions](../../database-support.md#versions-checked-against-the-code) |
| PHP extension | `pgsql` (`php-pgsql`, `php<version>-pgsql`) | |

Run a release your vendor still supports with security fixes. The wizard floor is not a recommendation.

## Creating a database per site

The installers do not create databases or users. Create both before the install, and make the site's user the
**owner** of its database:

```bash
sudo -u postgres createuser --pwprompt <customer>
sudo -u postgres createdb --owner=<customer> --encoding=UTF8 --template=template0 <customer>
```

`template0` lets you choose the encoding regardless of the server's default. A panel that creates PostgreSQL
databases may set a different owner or encoding; check your panel's documentation and compare with the commands
above ([Databases and users in control panels](../panels/databases-and-users.md)).

> **Tip**
> One database and one user per site, the user owning the database, is the simplest model for isolation between
> customers and for moving a site later.

## pgcrypto

The driver computes MD5 with `digest()` from `pgcrypto`. The installer runs `CREATE EXTENSION IF NOT EXISTS pgcrypto`;
as a trusted extension (PostgreSQL 13 and later) the database owner may create it. If the server lacks it, install
your distribution's PostgreSQL contrib package, then as the owner or a superuser:

```bash
sudo -u postgres psql -d <customer> -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

When it is missing, the setup wizard says: "The 'digest' function is not available in your database, and Exponential
cannot run without it."

> **Note**
> Install the contrib package on every PostgreSQL server you offer to Exponential customers. It is the one server-side
> prerequisite the site's user cannot fix alone.

## Settings and install

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=<customer>
Password=<the password>
Database=<customer>
UsePersistentConnection=disabled
```

The installers write this to `settings/override/site.ini.append.php`. A console install, with the password from the
environment so it stays out of the shell history:

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=pgsql \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

`--db=postgresql` and `--db=postgres` are accepted too; the kickstarter takes `[database_choice] Type=pgsql`. The
setup wizard prefills port 5432 and user `postgres` (`settings/setup.ini` `DefaultPort_pgsql`, `DefaultUser_pgsql`).
`UsePersistentConnection=enabled` uses `pg_pconnect` instead of `pg_connect`; under Velocity each persistent worker
keeps its connection anyway.

## Sequences

Auto-increment columns use sequences named `<table>_<column>_seq`. After loading data with explicit ids (an import, a
restore of selected tables), correct them, or the next insert fails with a duplicate key:

```sql
SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));
```

The schema installer does this itself after it loads data.

## Backups

```bash
pg_dump -U <customer> -Fc <customer> > <customer>.dump
pg_restore -U <customer> -d <customer> --clean <customer>.dump
```

A complete backup also contains `var/storage`, `settings/override` and `settings/siteaccess`. Switch on maintenance
mode for a restore and clear every cache afterwards
([Backup and restore](../../database-support.md#backup-and-restore)). Point-in-time recovery and replication are
PostgreSQL's own features; configure them with the PostgreSQL documentation.

## Problems and their causes

| Symptom | Cause |
|---|---|
| "The 'digest' function is not available ..." in the wizard | `pgcrypto` could not be created; install the contrib package and create the extension |
| Connection refused | `listen_addresses` in `postgresql.conf`, or a missing line in `pg_hba.conf` |
| Permission denied creating tables | the site's user does not own the database (PostgreSQL 15 and later) |
| Duplicate key on insert after an import | sequences behind their tables; correct them as above |
| An extension's SQL fails on PostgreSQL only | MySQL syntax such as backtick quoting ([cross-engine notes](../../../install/09-databases.md#98-cross-engine-notes)) |
| No page loads, `error.log` says "Database implementation not supported" | a mistyped `DatabaseImplementation` |

## Checklist

- [ ] A supported PostgreSQL release, 13 or later, with the contrib package installed.
- [ ] One database per site, encoding `UTF8`, owned by the site's user.
- [ ] `pgsql` in the web and the command line PHP.
- [ ] `pg_hba.conf` allows the site's user from the web servers only.
- [ ] `pg_dump` backups scheduled and a restore tested.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md)
- [Database support: PostgreSQL](../../database-support.md#2-postgresql)
- [Chapter 9.4: PostgreSQL](../../../install/09-databases.md#94-postgresql)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Several web nodes](../operations/several-web-nodes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
