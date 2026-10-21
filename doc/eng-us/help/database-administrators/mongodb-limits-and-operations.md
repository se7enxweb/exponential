---
title: "MongoDB: limits and operations"
audience: dba
category: database-administration
order: 18
summary: What the MongoDB adapter of Exponential cannot do yet, how to find untranslated SQL in the logs, how upgrades work without SQL update files, backups with mongodump, the migration scripts in bin/mongodb, and what to monitor on a MongoDB site.
keywords: MongoDB, limits, MONGO TODO, adapter, untranslated SQL, eztags, ezflow, shop, URL alias, upgrade check, collections, createaudittables, mongodump, mongorestore, export_mysql.sh, mysql2ndjson.py, import_all.sh, validate_ndjson.sh, fixup_ndjson.py
type: help
---
# MongoDB: limits and operations

This page is for database administrators who decide whether MongoDB fits a site, or who already run one. It answers:
what the adapter does not support, how to recognize a feature that silently returns nothing, how upgrades and schema
checks work without SQL, how to back up, and how a MySQL site is moved to MongoDB. Sources:
[MongoDB as the database](../../features/6.0/mongodb-database-support.md),
[9.5 MongoDB](../../install/09-databases.md#95-mongodb) and section 26 of the
[porting reference](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md).

## Contents

1. [How the adapter works](#how-the-adapter-works)
2. [Known limits](#known-limits)
3. [Finding untranslated SQL](#finding-untranslated-sql)
4. [Upgrades without SQL files](#upgrades-without-sql-files)
5. [Backups](#backups)
6. [Moving a MySQL site to MongoDB](#moving-a-mysql-site-to-mongodb)
7. [Performance](#performance)
8. [Checklist](#checklist)

## How the adapter works

`expMongoDB` never runs real SQL. It parses the kernel's SQL-shaped calls and turns them into `find`, `aggregate`,
insert, upsert and delete operations on collections named like the kernel's tables (`ezcontentobject`,
`ezcontentobject_tree` and so on). Its schema handler is `expMongoSchema` (`settings/dbschema.ini`, type `mongo`).
`eZDB::instance()->databaseName()` returns `mongo`.

## Known limits

- Hand-written SQL that the adapter cannot translate returns an **empty result** and logs a warning. Extensions with
  SQL of their own may need a MongoDB path.
- Deep URL alias regeneration after moving subtrees is incomplete.
- Role and policy list pages can be empty although permissions are enforced.
- Some cronjobs are not ported.
- `eztags` is not usable.
- The shop checkout and `ezflow` scheduling are untested end to end.
- The SQL query cache does not apply.
- There are no SQL update files.
- Errors: `errorNumber()` reports MongoDB's codes (`11000` for a duplicate key); the driver answers `false` and throws
  nothing ([Database error codes and messages](error-codes-and-messages.md)).

Test every extension and every workflow your site uses on a staging copy before you go live.

## Finding untranslated SQL

Search the logs for the marker the adapter writes:

```bash
grep -rn "MONGO TODO" var/log/ | tail -20
```

Each hit names a statement that returned an empty result instead of data. A page that shows "nothing found" where
other engines show content usually has a hit here. Report kernel statements on the
[software tracker](https://github.com/se7enxweb/exponential/issues); fix your own extensions' SQL
([Reviewing extension SQL for every engine](portable-sql-for-extensions.md)).

## Upgrades without SQL files

MongoDB has no `update/database/` directory. What replaces it:

- **Setup > Upgrade check**, "Check database consistency", compares collections instead of tables: missing
  collections grouped by feature, collections not in the schema, and the `mongosh` commands that create the missing
  ones ([The upgrade check](../../guides/upgrade-check.md#6-run-the-database-consistency-check)).
- `php update/common/scripts/6.0/createaudittables.php` creates the audit index collections of 6.0.15 through the
  schema handler (`--dry-run` first).
- The trash documents get `trashed_by` and `trashed_via` when written; older ones read as their defaults, so
  `movetrashrecords.php` runs as on the other engines.
- Run `bin/mongodb/create_indexes.js` again after an upgrade.

See [11.3 The update files](../../install/11-upgrading.md#113-the-update-files).

## Backups

```bash
mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/$(date +%F)
mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/2026-10-05
```

The tools ask for the password when the URI has none. Replica sets, oplog-based point-in-time backups and snapshots
are MongoDB's own features and work as with any client. Back up `var/storage` and `settings/override` with the
database ([Backups per engine](backups-per-engine.md)).

## Moving a MySQL site to MongoDB

The scripts in `bin/mongodb/` move an existing MySQL site:

| Script | Role |
|---|---|
| `export_mysql.sh` | dumps every Exponential table from MySQL to NDJSON (the password comes from `MYSQL_PASSWORD`) |
| `mysql2ndjson.py` | the converter `export_mysql.sh` calls |
| `fixup_ndjson.py` | post-processes the files: integer `_id`s, `NULL` strings dropped, known integer fields cast |
| `validate_ndjson.sh` | checks that each file is valid JSON, one object per line; exit 1 on errors |
| `import_all.sh` | imports the files into MongoDB in dependency order |

The procedure is section 18 of the [porting reference](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md). Plan a
maintenance window, then create the indexes and run the cronjobs ([Moving a site to another engine](moving-between-engines.md)).

## Performance

Performance follows the number of statements per page, because each kernel call becomes one or more MongoDB
operations. The indexes from `create_indexes.js` are the first lever; the caches in front of the database are the
second ([Performance and scaling](performance-and-scaling.md)).

## Checklist

- [ ] Every extension and workflow tested on staging; no `MONGO TODO` in the logs after a full click-through.
- [ ] `eztags`, shop checkout and `ezflow` not needed, or tested.
- [ ] Indexes created after install and after each upgrade.
- [ ] Upgrade check run after each upgrade; missing collections created.
- [ ] `mongodump` backups scheduled and a restore tested.

## Related pages

- [Setting up MongoDB](mongodb-setup.md)
- [Choosing an engine](choosing-an-engine.md)
- [MongoDB for hosts](../hosting/basics/mongodb-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
