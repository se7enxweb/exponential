---
title: SQLite for development
audience: dxp-developer
category: dxp-development
order: 11
summary: Run a DXP project on SQLite with no database server - the DSN, the synchronous messenger transport, the install, file permissions, the two typical errors and the limits that keep SQLite out of busy production sites.
keywords: SQLite, development, DATABASE_URL, data_dev.db, MESSENGER_TRANSPORT_DSN, sync, readonly database, no such table, pdo_sqlite, laptop, demo, air-gapped
type: help
---
# SQLite for development

This page is for developers who want a DXP project running on a laptop, in a test pipeline or on a demo server
without installing a database server. SQLite support is one of the things the DXP adds over the upstream line
([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-dxp)).

## Contents

1. [Five steps](#five-steps)
2. [Two errors you will meet](#two-errors-you-will-meet)
3. [Limits](#limits)
4. [Moving to a server database later](#moving-to-a-server-database-later)

## Five steps

1. **Check the extensions.**

   ```bash
   php -m | grep -i sqlite      # expect SQLite3 and pdo_sqlite
   ```

2. **Set the DSN** in `.env.local`, and remove the `DATABASE_DRIVER`, `DATABASE_HOST` and other parts:

   ```bash
   DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
   MESSENGER_TRANSPORT_DSN=sync://
   ```

   The messenger line matters: the default Doctrine transport needs a second database connection, which SQLite may
   not give it; `sync://` handles messages in the same request ([Messenger and notifications](messenger-and-notifications.md)).

3. **Install.** The command creates `var/data_dev.db` (or `var/data_prod.db`), imports the seed content and creates the
   Doctrine tables.

   ```bash
   php bin/console exponential:install exponential-oss
   ```

4. **Let the web server write the file.** SQLite writes to the file on every edit:

   ```bash
   chmod 664 var/data_dev.db
   chown "$USER":www-data var/data_dev.db     # your web server's group
   ```

5. **Clear the cache:** `php bin/console cache:clear`.

## Two errors you will meet

| Error | Cause | Fix |
|---|---|---|
| `no such table: ibexa_section` | The database file was never initialized | Run the install command; if an empty file exists, remove it first and install again |
| `attempt to write a readonly database` | The install ran as another user than the web server | Fix the owner and mode as in step 4 |

## Limits

From the skeleton's guide:

| Limit | Impact |
|---|---|
| No concurrent write transactions | Writes queue up under load and can time out |
| No replication or clustering | One server only |
| No FULLTEXT index | Full-text search falls back to `LIKE` queries |
| File-level locking | The file must be on a local file system, not NFS or CIFS |

SQLite is right for development, automated tests, demos and air-gapped installations. Use MySQL 8.0, MariaDB 10.6 or
PostgreSQL 14 or newer for any public or multi-user site.

## Moving to a server database later

The skeleton's guide converts a SQLite project to MySQL with `sqlite3-to-mysql` or to PostgreSQL with `pgloader`.
Afterwards remove the SQLite DSN and the `sync://` transport from `.env.local`
([Databases, Converting between engines](databases-mysql-postgresql-sqlite.md#converting-between-engines)).

## Related pages

- [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md)
- [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- [Database support in Exponential 6](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
