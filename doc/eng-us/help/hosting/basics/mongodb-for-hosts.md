---
title: MongoDB for hosts
audience: hosting, admin
category: hosting
order: 90
summary: MongoDB as the database of an Exponential site, from a host's point of view: the PHP, server and extension versions it needs, the Composer library, the database user, installing, the indexes, the documented limits, backups and sizing figures.
keywords: MongoDB, mongodb, expMongoDB, MongoDB Client, mongodb/mongodb, PECL mongodb, PHP 8.2, MongoDB 6.0, mongosh, createUser, readWrite, dbAdmin, create_indexes.js, mongodump, mongorestore, limits, MONGO TODO, eztags, hosting
type: help
---
# MongoDB for hosts

This page is for system administrators at a hosting company whose customers want to run Exponential on MongoDB. It
answers: what MongoDB needs from the server and from PHP, how to create the site's user so the adapter can connect,
which steps the install needs beyond the usual ones, which limits to tell a customer before they choose it, and how
to back it up. MongoDB is third in the official order. Sources:
[Database support](../../database-support.md#3-mongodb), [chapter 9.5](../../../install/09-databases.md#95-mongodb)
and [MongoDB as the database](../../../features/6.0/mongodb-database-support.md).

## Contents

1. [What it is](#what-it-is)
2. [Requirements](#requirements)
3. [Creating the user](#creating-the-user)
4. [Settings and install](#settings-and-install)
5. [Limits to tell the customer](#limits-to-tell-the-customer)
6. [Backups](#backups)
7. [Sizing figures](#sizing-figures)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## What it is

The adapter `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) translates the kernel's SQL-shaped calls into MongoDB
`find`, `aggregate`, insert, upsert and delete operations. It arrived with 6.0.14. It is the choice for
organizations standardized on MongoDB; for everyone else the project recommends SQLite or PostgreSQL first.

## Requirements

| | Minimum | Tested |
|---|---|---|
| PHP | **8.2** | 8.5 |
| MongoDB server | **6.0** (the wizard checks 4.0) | 8.3 |
| PHP `mongodb` extension (PECL) | **1.18** | current |
| Composer library | `mongodb/mongodb` | CI installs `^1.21 \|\| ^2.0` |

Two things are easy to miss:

- **PHP 8.2.** The rest of Exponential runs on 8.0; a MongoDB site needs 8.2 or later.
- **The library.** `composer.json` only suggests `mongodb/mongodb`; add it to the site with
  `composer require mongodb/mongodb`. The PECL extension alone is not enough.

## Creating the user

The adapter always connects with `mongodb://<user>:<password>@<server>:<port>/<database>`, and MongoDB uses the
database in the URI as the authentication database. Create the user **in the site's own database**:

```bash
mongosh --eval '
  db.getSiblingDB("<customer>").createUser({
    user: "<customer>",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "<customer>" }, { role: "dbAdmin", db: "<customer>" } ]
  })'
```

The database itself is created by the first write. A user is required even on a server without access control: with
an empty `User` the driver refuses the URI ("'default' authentication mechanism requires a username").

## Settings and install

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
Port=27017
User=<customer>
Password=<the password>
Database=<customer>
```

1. **Install from the console**, or give the wizard time: its configuration step on MongoDB takes one to three
   minutes, so raise PHP-FPM's `request_terminate_timeout` first.

   ```bash
   EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=mongodb \
       --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
   ```

2. **Create the indexes** the kernel's queries rely on; without them subtree queries and URL alias lookups scan whole
   collections:

   ```bash
   mongosh "mongodb://<customer>@localhost:27017/<customer>" --file bin/mongodb/create_indexes.js
   ```

3. **Run the cronjobs once** to build the search index: `php runcronjobs.php --allow-root-user`.

> **Tip**
> Put the index step into your provisioning script right after the install. A MongoDB site without its indexes works,
> but slowly, and the cause is not obvious from the outside.

## Limits to tell the customer

From [MongoDB limits](../../database-support.md#mongodb-limits):

- The adapter never runs real SQL. Hand-written SQL it cannot translate returns an empty result and logs a warning
  (search the logs for `MONGO TODO`). Extensions with SQL of their own may need a MongoDB path.
- Deep URL alias regeneration after moving subtrees is incomplete; role and policy list pages can be empty although
  permissions are enforced; some cronjobs are not ported; `eztags` is not usable; the shop checkout and `ezflow`
  scheduling are untested end to end.
- The SQL query cache does not apply.
- There are no SQL update files: the upgrade check compares collections and prints the `mongosh` commands for missing
  ones.
- In the project's CI, MongoDB is covered by adapter unit tests; the live tests need running servers and are not part
  of CI.

> **Warning**
> Make these limits part of the plan's description or the order confirmation. A customer who relies on `eztags` or on
> an extension with its own SQL should choose another database.

## Backups

```bash
mongodump --uri="mongodb://<customer>@localhost:27017/<customer>" --out=/backup/<customer>
mongorestore --uri="mongodb://<customer>@localhost:27017/<customer>" --drop /backup/<customer>
```

A complete backup also contains `var/storage`, `settings/override` and `settings/siteaccess`
([Backup and restore](../../database-support.md#backup-and-restore)).

## Sizing figures

The only figures the repository gives for a MongoDB installation are in section 24.1 of the
[MongoDB porting reference](../../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md): RAM 512 MB minimum, 2 GB
recommended; disk 2 GB minimum, 10 GB recommended. Performance follows the number of statements per page; measure a
real site with `php bin/php/console exp:benchmark` ([Sizing memory, CPU and disk](sizing-memory-cpu-and-disk.md)).

## Checklist

- [ ] PHP 8.2 or later for the site, web and command line.
- [ ] PECL `mongodb` 1.18 or later, and `mongodb/mongodb` required in the site's Composer project.
- [ ] MongoDB server 6.0 or later.
- [ ] A user with `readWrite` and `dbAdmin` in the site's own database.
- [ ] `bin/mongodb/create_indexes.js` run after the install.
- [ ] The limits communicated to the customer.
- [ ] `mongodump` backups scheduled and a restore tested.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md)
- [Database support: MongoDB](../../database-support.md#3-mongodb)
- [Chapter 9.5: MongoDB](../../../install/09-databases.md#95-mongodb)
- [MongoDB as the database](../../../features/6.0/mongodb-database-support.md)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
