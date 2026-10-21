---
title: Moving a site to another engine
audience: dba
category: database-administration
order: 33
summary: How to move an existing Exponential site from one database engine to another - planning the maintenance window, the .dba dump and insert between SQL engines, the ezoracle transfer commands into Oracle, the bin/mongodb scripts into MongoDB, the settings to change, the checks before reopening and what changes for later upgrades.
keywords: migrate database, switch engine, change database, MySQL to PostgreSQL, MySQL to SQLite, SQLite to PostgreSQL, into Oracle, mysql2oracle, into MongoDB, ezsqldumpschema, ezsqlinsertschema, dba, maintenance window, row counts, sequences, DatabaseImplementation
type: help
---
# Moving a site to another engine

This page is for database administrators who move an existing Exponential site to another database engine, for
example from SQLite to PostgreSQL when the site grows onto several servers, or from MySQL to PostgreSQL by
organization policy. The content stays the same; only where it is stored changes. Sources:
[9.9 Switching an existing site to another engine](../../install/09-databases.md#99-switching-an-existing-site-to-another-engine)
and [Database support](../database-support.md#moving-a-site-to-another-database).

## Contents

1. [Plan](#plan)
2. [Between SQL engines](#between-sql-engines)
3. [Into Oracle](#into-oracle)
4. [Into MongoDB](#into-mongodb)
5. [Switch the settings](#switch-the-settings)
6. [Check before you reopen](#check-before-you-reopen)
7. [After the move](#after-the-move)
8. [Checklist](#checklist)

## Plan

- **A maintenance window**: no editor may publish between the dump and the moment the site runs on the new database,
  or that work is lost.
- **A rehearsal**: run the whole move on a copy first and time it.
- **The target prepared**: database, account and character set as on the engine's setup page
  ([PostgreSQL](postgresql-setup.md), [MySQL and MariaDB](mysql-and-mariadb-setup.md), [MongoDB](mongodb-setup.md),
  [Oracle](oracle-setup.md)).
- **A backup** of the source in its native format.
- **Extensions reviewed** for the target engine ([Reviewing extension SQL](portable-sql-for-extensions.md)).

## Between SQL engines

SQLite, PostgreSQL, MariaDB, MySQL, and Oracle with `ezoracle`, through the engine-neutral `.dba` format. `--type`
takes a schema handler from `settings/dbschema.ini`: `mysql`, `postgresql`, `sqlite3`, and with `ezoracle` `oracle`.
For SQLite the database argument is the file name, as in `Database`.

```bash
php bin/php/maintenance.php on --message="Moving the database" --allow-root-user
# dump schema and data of the current database (MySQL in this example)
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
# load them into the new one
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

`--password=` is visible in the process list: run the move on a machine you control, or use a temporary password and
change it afterwards. Run both scripts with `--help` first ([Schema tools](schema-tools-dump-insert-diff.md)).

## Into Oracle

The extension has its own transfer commands, `ext:ezoracle:mysql2oracle-schema` and `ext:ezoracle:mysql2oracle-data`
(scripts in `extension/ezoracle/bin/php/`). Read their `--help`, then run `ext:ezoracle:sequence-sync` and
`ext:ezoracle:gather-stats` afterwards ([Oracle maintenance commands](oracle-maintenance-commands.md)).

## Into MongoDB

The scripts in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `validate_ndjson.sh`, `fixup_ndjson.py`,
`import_all.sh`) and section 18 of the [porting reference](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md). Then
`create_indexes.js` and a cronjob run ([MongoDB: limits and operations](mongodb-limits-and-operations.md#moving-a-mysql-site-to-mongodb)).

## Switch the settings

In `settings/override/site.ini.append.php`, set `DatabaseImplementation` and the connection keys to the new engine
(and `ActiveExtensions[]=ezoracle` for Oracle). Then:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user     # when Velocity serves the site
```

and reload PHP-FPM. A forgotten `DatabaseImplementation` is the usual reason no page loads after a switch.

## Check before you reopen

1. Count the rows of large tables on both sides; the numbers must match:

   ```sql
   SELECT COUNT(*) FROM ezcontentobject;
   SELECT COUNT(*) FROM ezcontentobject_tree;
   SELECT COUNT(*) FROM ezcontentobject_attribute;
   ```

2. On PostgreSQL, correct the sequences if `ezsqlinsertschema.php` reported a problem with them
   ([Operating PostgreSQL](postgresql-operations.md#correcting-sequences)).
3. Run the database consistency check ([Checking the schema](checking-the-schema-after-an-upgrade.md)).
4. Sign in to the administration, open a few pages and publish a test object.
5. Rebuild the search index if results look wrong: `php bin/php/updatesearchindex.php --clean --allow-root-user`.
6. Maintenance off.

## After the move

- **Later upgrades** use the update files of the engine the site runs on **now**, not the one it was installed on.
- **Backups** change tool: update the backup jobs and test a restore on the new engine.
- **Monitoring** changes: point your checks at the new server ([Monitoring the database](monitoring-the-database.md)).
- Keep the old database read-only for a while, then remove it with its credentials.

## Checklist

- [ ] Maintenance window agreed; rehearsal done.
- [ ] Target database, account and character set prepared.
- [ ] Dump and insert completed without errors.
- [ ] Settings switched; caches cleared; workers restarted.
- [ ] Row counts match; consistency check clean; test publish done.
- [ ] Backups, monitoring and upgrade notes updated for the new engine.

## Related pages

- [Changing the database engine](../hosting/migrations/changing-the-database-engine.md) (for hosting companies)
- [Choosing an engine](choosing-an-engine.md)
- [Moving a site between hosts](../hosting/migrations/moving-a-site-between-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
