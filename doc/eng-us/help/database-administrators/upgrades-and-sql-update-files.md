---
title: Upgrades and the SQL update files
audience: dba
category: database-administration
order: 30
summary: How Exponential's database update files are organized per engine and version in update/database, which chain a database walks from 3.10 to 6.0.15, the rules for applying them, the 6.12, 7.2 and 7.3 directories that must not be applied, the cluster files, how to read the version row in ezsite_data, and how to check that the update files are complete.
keywords: upgrade, update files, dbupdate, update/database, mysql, postgresql, sqlite, ezoracle, MongoDB, chain, ezsite_data, ezpublish-version, 6.0.15stable, 6.12, 7.2, 7.3, cluster update, unstable, exp:checkdbfiles, SET default_storage_engine
type: help
---
# Upgrades and the SQL update files

This page is for database administrators who apply the database part of an Exponential upgrade. It answers: where the
update files are, which ones a database needs, the rules for applying them, which directories must never be applied,
and how to tell which version a database is at. The step-by-step chapter is
[chapter 11, Upgrading](../../install/11-upgrading.md); the guide for administrators is
[Upgrading](../../guides/upgrading.md).

## Contents

1. [Which version is the database at?](#which-version-is-the-database-at)
2. [Where the update files are](#where-the-update-files-are)
3. [The chain](#the-chain)
4. [Rules](#rules)
5. [Directories you must not apply](#directories-you-must-not-apply)
6. [Per-engine notes](#per-engine-notes)
7. [Check the update files themselves](#check-the-update-files-themselves)
8. [Checklist](#checklist)

## Which version is the database at?

The code and the database each carry a version; after a correct upgrade they agree.

```bash
php bin/php/console --version
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U USER DATABASE -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
sqlite3 var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

A current site prints `ezpublish-version` `6.0.15stable` and `ezpublish-release` `1`. The row names keep their historic
spelling on purpose: they are identifiers that every update file writes to. **Setup > System information** shows both
versions as well ([11.2](../../install/11-upgrading.md#112-which-version-do-you-run)).

## Where the update files are

| Engine | Directory | Steps |
|---|---|---|
| MySQL, MariaDB | `update/database/mysql/` | 3.10 to 4.0 ... 5.4, then `6.0/dbupdate-5.4.0-6.0.0.sql`, then `6.0/dbupdate-6.0.0-6.0.15.sql` |
| PostgreSQL | `update/database/postgresql/` | the same chain; the 5.4 step is `6.0/dbupdate-5.4-to-6.0.sql`, then `6.0/dbupdate-6.0.0-6.0.15.sql` |
| SQLite | `update/database/sqlite/` | only `6.0/dbupdate-6.0.0-6.0.15.sql` (SQLite support starts with 6.0.1) |
| Oracle | `extension/ezoracle/update/database/` | `ezpublish/` for the kernel chain up to 5.3, `ezoracle/` for the driver's own tables |
| MongoDB | none | the audit collections of 6.0.15 come from `createaudittables.php` |

## The chain

Each file takes a database **from** one version **to** the next. A database walks the chain in order, every file once:

| You run | Path |
|---|---|
| 3.10 up to 5.3 | the old chain to 5.4, then to 6.0.0, then to 6.0.15 |
| 5.4.0, or 5.90 | to 6.0.0, then to 6.0.15 |
| 6.0.0 to 6.0.14 | `dbupdate-6.0.0-6.0.15.sql` |

The full table of the 4.0 to 5.4 files is in [11.3](../../install/11-upgrading.md#113-the-update-files). Apply a file
with the client of the database:

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

## Rules

- **Back up first**, and know how to restore ([Restoring a database](restoring-a-database.md)).
- **In order, each once.** A statement that fails because a column or table already exists means the file was
  applied before: stop and compare with the backup.
- **A file that fails half way**: restore the backup and apply the file again from the start; do not repair by hand.
- **Cluster files**: if the installation uses the DFS cluster, also apply the `dbupdate-cluster-...` file of the same
  step (MySQL: 4.3, 4.7, 5.2, 5.4).
- **`unstable/` directories** hold files of pre-releases; never apply them to a site that ran a final release.
- **Per engine**: a site that moved to another engine uses the update files of the engine it runs on now.

## Directories you must not apply

`update/database/<engine>/6.12/`, `7.2/` and `7.3/` come from the upstream legacy kernel of 2016 to 2018, numbered
after a Symfony kernel version, not after a legacy release. Applied as files, each would replace the version row
with a number that is not an Exponential version. Their changes (the wider `ezuser.password_hash`,
`ezcontentobject_trash.trashed`, and on PostgreSQL the `<table>_<column>_seq` sequence names) are already in the 6.0
files ([11.3](../../install/11-upgrading.md#the-612-72-and-73-directories)).

## Per-engine notes

| Engine | Note |
|---|---|
| MySQL, MariaDB | Files start with `SET default_storage_engine=InnoDB;`. Copies from before October 2026 said `SET storage_engine=InnoDB;`, removed in MySQL 5.7.5 and MariaDB 12.0: take the current files. Tables must be UTF-8 and InnoDB ([MySQL and MariaDB: utf8mb4](mysql-and-mariadb-charset-and-innodb.md)). |
| PostgreSQL | Guarded steps are `DO` blocks (PostgreSQL 9.0+); `pgcrypto`'s `digest()` must exist. |
| SQLite | Each `ALTER TABLE` adds one column and SQLite has no `ADD COLUMN IF NOT EXISTS`: a second run fails with "duplicate column name". Run the file once, after an online backup. |
| Oracle | The extension's own files up to 5.3; the 6.0.15 audit tables from `createaudittables.php`. |
| MongoDB | No SQL; the upgrade check prints the `mongosh` commands for missing collections. |

## Check the update files themselves

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches
```

It verifies the update files against the upgrade path, every directory from 4.0 to 7.3 on every engine, and prints
one line per problem: `!` for a missing file, `?` for a stray one. No output means the files are complete. Without
`--no-verify-branches` it also tries a retired SVN repository and prints a `C` line; ignore it or add the option.

## Checklist

- [ ] Version row read before and after.
- [ ] Backup taken and restore known.
- [ ] Only the files of your engine and your path, in order, each once.
- [ ] No file from `6.12/`, `7.2/`, `7.3/` or `unstable/` applied.
- [ ] Cluster files applied where the cluster is used.
- [ ] `exp:checkdbfiles` prints nothing.

## Related pages

- [Applying the 6.0.15 database update](applying-the-6-0-15-database-update.md)
- [Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md)
- [Upgrading customer sites](../hosting/operations/upgrading-customer-sites.md) (for hosting companies)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
