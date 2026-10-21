---
title: Setting up PostgreSQL
audience: dba
category: database-administration
order: 12
summary: How to prepare PostgreSQL for an Exponential site - the user and a UTF8 database it owns, the pgcrypto extension the driver needs for MD5, the settings block, the installers, and the server-side settings (listen_addresses, pg_hba.conf) that most often stop the first connection.
keywords: PostgreSQL, Postgres, createuser, createdb, owner, UTF8, template0, pgcrypto, digest, CREATE EXTENSION, pg_hba.conf, listen_addresses, ezpostgresql, pgsql, exp:install --db=pgsql, PostgreSQL 15 public schema
type: help
---
# Setting up PostgreSQL

This page is for database administrators who prepare PostgreSQL for an Exponential site, the project's recommended
database server. It answers: how to create the account and the database, why the account must own the database,
why `pgcrypto` is needed and how to provide it, what the settings look like and how to run the installer. Source:
[9.4 PostgreSQL](../../install/09-databases.md#94-postgresql); driver `lib/ezdb/classes/ezpostgresqldb.php`.

## Contents

1. [Requirements](#requirements)
2. [Create the user and the database](#create-the-user-and-the-database)
3. [pgcrypto](#pgcrypto)
4. [Allow the connection](#allow-the-connection)
5. [The settings](#the-settings)
6. [Install](#install)
7. [First checks](#first-checks)
8. [Checklist](#checklist)

## Requirements

- PostgreSQL **13 or later** (the wizard accepts 8.0, but `pgcrypto` is a trusted extension only from 13 on, and the
  installer relies on that). The project's CI runs PostgreSQL 15; the development server runs 13.
- PHP extension `pgsql` in every PHP that uses the site ([PHP extensions per engine](php-extensions-per-engine.md)).
- Your distribution's PostgreSQL contrib package, which carries `pgcrypto`, if the server does not have it yet.

## Create the user and the database

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

- **The owner matters.** From PostgreSQL 15 on, ordinary users may no longer create tables in the `public` schema of
  a database they do not own. Making the site's account the owner gives it what the installer and every later
  update file need, and nothing more.
- **`--template=template0`** lets you choose the encoding regardless of the server's default.
- **`--pwprompt`** asks for the password instead of taking it on the command line.

None of the installers creates the database or the user; create them first.

## pgcrypto

The driver computes MD5 with `encode(digest(..., 'md5'), 'hex')`, and `digest()` comes from `pgcrypto`. The installer
runs `CREATE EXTENSION IF NOT EXISTS pgcrypto` when the server ships the extension; as a trusted extension the
database owner may create it. If the server lacks it, install the contrib package, then, as the owner or a
superuser:

```bash
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

When it is missing, the setup wizard says: "The 'digest' function is not available in your database, and Exponential
cannot run without it." Create the extension and click Next again. The update files also need `digest()`
([11.3 per-database notes](../../install/11-upgrading.md#per-database-notes)).

## Allow the connection

"Connection refused" or an authentication error at the first connection is usually the server's own configuration:

- `listen_addresses` in `postgresql.conf` must include the address the web servers connect to (for a database on
  the same machine `localhost` is enough);
- `pg_hba.conf` needs a line that allows the site's user, from the web servers' addresses, to the site's database,
  with a password method.

Reload PostgreSQL after a change. The authoritative reference is PostgreSQL's
[client authentication](https://www.postgresql.org/docs/current/client-authentication.html) chapter.

## The settings

In `settings/override/site.ini.append.php` (the installers write it):

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
# 5432 is the server's default
Port=5432
User=exponential
Password=<the password>
Database=exponential
# enabled: pg_pconnect instead of pg_connect
UsePersistentConnection=disabled
```

The aliases `postgresql` and `pgsql` name the same driver.

## Install

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=pgsql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`exp:install` takes the password from `EXP_INSTALL_DB_PASSWORD`, so it stays out of the process list. `--db=postgresql`
and `--db=postgres` are accepted too. The setup wizard prefills port 5432 and user `postgres`
(`settings/setup.ini DefaultPort_pgsql`, `DefaultUser_pgsql`); replace the user with the site's account. The
kickstarter takes `[database_choice] Type=pgsql`.

## First checks

```bash
psql -U exponential -d exponential -c '\dx'                       # pgcrypto is listed
psql -U exponential -d exponential -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U exponential -d exponential -c '\dt' | wc -l               # the kernel's tables are there
```

Then open **Setup > System information**: the Database card shows PostgreSQL, the server version and `UTF8`.

## Checklist

- [ ] PostgreSQL 13 or later, a release with security support.
- [ ] The site's account owns the database; the database is `UTF8`.
- [ ] `pgcrypto` exists in the site's database.
- [ ] `pg_hba.conf` allows only the web servers, with a password method.
- [ ] The account is not a superuser.
- [ ] Backups with `pg_dump -Fc` are scheduled ([Backups per engine](backups-per-engine.md)).

## Related pages

- [Operating PostgreSQL: sequences, case and gotchas](postgresql-operations.md)
- [Database accounts and privileges](database-accounts-and-privileges.md)
- [PostgreSQL for hosts](../hosting/basics/postgresql-for-hosts.md)
- [Databases and users in hosting panels](../hosting/panels/databases-and-users.md#2-postgresql)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
