---
title: Database accounts and privileges
audience: dba
category: database-administration
order: 24
summary: The database account an Exponential site needs on each engine and the least privileges that work - MySQL and MariaDB grants, PostgreSQL ownership, MongoDB roles in the site's database, Oracle grants from ora-grant.php, SQLite file permissions - plus the separate accounts for replicas, the DFS cluster and backups.
keywords: database user, account, privileges, least privilege, GRANT, LOCK TABLES, CREATE TEMPORARY TABLES, owner, PostgreSQL 15 public schema, readWrite, dbAdmin, ora-grant, SELECT_CATALOG_ROLE, root, superuser, backup account, read-only, replica account, DFS DBUser
type: help
---
# Database accounts and privileges

This page is for database administrators who create the accounts an Exponential site connects with and who must
justify every privilege. It answers: which account the site needs on each engine, which privileges are required and
why, and which other accounts (replicas, cluster, backups) a setup may need. The shipped defaults are not safe for
production: `site.ini [DatabaseSettings] User=root` and `file.ini [eZDFSClusteringSettings] DBUser=root` are
placeholders to replace.

## Contents

1. [One account per site](#one-account-per-site)
2. [MySQL and MariaDB](#mysql-and-mariadb)
3. [PostgreSQL](#postgresql)
4. [MongoDB](#mongodb)
5. [Oracle](#oracle)
6. [SQLite](#sqlite)
7. [Other accounts](#other-accounts)
8. [What the project does not document](#what-the-project-does-not-document)
9. [Checklist](#checklist)

## One account per site

Exponential connects with one account, named in `site.ini [DatabaseSettings] User` (or for SQLite, through the file
system). The same account runs page views, publishing, cronjobs and the update files of an upgrade, so it needs the
right to change the schema of its own database, and nothing outside it. Never use an administrative account
(`root`, a superuser, `SYS`). Give each site its own account and its own database or schema.

## MySQL and MariaDB

```sql
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

| Privilege | Why |
|---|---|
| `SELECT`, `INSERT`, `UPDATE`, `DELETE` | normal work |
| `CREATE`, `DROP`, `INDEX`, `ALTER` | the installer and update files |
| `LOCK TABLES` | the driver's `lock()` uses `LOCK TABLES` |
| `CREATE TEMPORARY TABLES` | the kernel creates temporary tables for some operations |

No global privilege, no `SUPER`, no right to list databases. Restrict the host part to the web servers. Source:
[9.3](../../install/09-databases.md#create-the-database-and-the-user).

## PostgreSQL

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

Make the account the **owner** of the database: from PostgreSQL 15 on, other users may no longer create tables in its
`public` schema. As owner it may also create `pgcrypto`, a trusted extension from PostgreSQL 13 on. It needs no
superuser rights, `CREATEDB` or `CREATEROLE`. Restrict it in `pg_hba.conf` to the web servers and this database.

## MongoDB

Create the user in the site's own database, because the adapter uses that database as the authentication database:
roles `readWrite` and `dbAdmin` on that database only ([Setting up MongoDB](mongodb-setup.md#create-the-user)). A user
is required even on a server without access control.

## Oracle

`php extension/ezoracle/bin/php/ora-grant.php` prints the grants for a DBA to run: the account may create sessions,
tables, views, triggers, sequences and procedures. Add `SELECT_CATALOG_ROLE` if you use the `health` and `report`
commands; for `datapump`, READ and WRITE on the directory object ([Setting up Oracle](oracle-setup.md#create-the-account)).

## SQLite

There are no database accounts. The file system is the access control: every process that uses the site must be
able to write the directory and its files, through one common group; nobody else should be able to read them
([SQLite files, ownership and permissions](sqlite-files-ownership-and-permissions.md)).

## Other accounts

| Account | Where it is configured | Needs |
|---|---|---|
| Read replicas (MySQL, MariaDB) | `SlaverServerUser[]`, `SlaverServerPassword[]` | read access; `CREATE TEMPORARY TABLES` for some read paths ([Read replicas](mysql-read-replicas.md)) |
| DFS cluster metadata | `file.ini [eZDFSClusteringSettings] DBUser`, `DBPassword`, and `config.cluster.php` | read and write on the cluster table(s) in their own database ([Clustering](clustering-dfs-and-the-database.md)) |
| Backups | your backup tool | read access and, for consistent MySQL dumps, what `mysqldump --single-transaction` needs on your release; use the tools' option files, not command-line passwords |
| Schema tools (`ezsqldumpschema.php`, `ezsqlinsertschema.php`) | `--user`, `--password` on the command line | as the site account; the password is visible in the process list, so run them on a machine you control or with a temporary password |

## What the project does not document

The project documents and tests one account per site that both runs the site and applies schema changes. Splitting
it into a run-time account without DDL rights and a migration account is not described or tested by the project. If
your policy requires it, grant the migration account the DDL rights, keep `CREATE TEMPORARY TABLES` and
`LOCK TABLES` on the run-time account, and test every update file and the setup on staging first.

## Checklist

- [ ] No site connects as `root`, a superuser or `SYS`.
- [ ] Each site has its own account, limited to its own database or schema.
- [ ] Host or network restrictions allow only the web servers (and cron hosts).
- [ ] The DFS cluster's `DBUser` is not the shipped `root`.
- [ ] Backup accounts read only what they back up.
- [ ] Passwords live in `settings/override/` and the cluster file only ([Securing the database](securing-the-database.md)).

## Related pages

- [Securing the database](securing-the-database.md)
- [Databases and users in hosting panels](../hosting/panels/databases-and-users.md)
- [Per-customer users and pools](../hosting/security/per-customer-users-and-pools.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
