---
title: Reading and changing database settings
audience: dba
category: database-administration
order: 7
summary: How to read the database settings an Exponential site really uses, find the file each value comes from, change them safely in the override files with exp:ini, and make every process (PHP-FPM, Velocity workers, cronjobs) pick up the change.
keywords: exp:ini, exp:ini get, exp:ini where, exp:ini set, exp:ini list, override, settings/override/site.ini.append.php, siteaccess, INI cache, clear-tag=ini, Velocity restart, secrets masked, change database password, change database host
type: help
---
# Reading and changing database settings

This page is for database administrators who need to read the connection settings of a running site, or change them:
a new password, a new host after a migration, a different engine. It answers: where the values are kept, how to see
the value in effect and the file it comes from, how to write a change, and what to restart so that every process
uses it. Sources: [9.1](../../install/09-databases.md#91-how-exponential-chooses-a-database-driver) and
[exp:ini](../../bc/6.0/console-exp-ini.md).

## Contents

1. [Where the values are kept](#where-the-values-are-kept)
2. [Read the value in effect](#read-the-value-in-effect)
3. [Find the file a value comes from](#find-the-file-a-value-comes-from)
4. [Change a value](#change-a-value)
5. [Make every process read it](#make-every-process-read-it)
6. [Changing the password](#changing-the-password)
7. [Common mistakes](#common-mistakes)

## Where the values are kept

| File | Scope | Who writes it |
|---|---|---|
| `settings/site.ini` | the shipped defaults | the release; never edit it |
| `settings/override/site.ini.append.php` | the whole installation | the installers, you |
| `settings/siteaccess/<name>/site.ini.append.php` | one siteaccess | you, when one siteaccess needs another database |
| `extension/<ext>/settings/site.ini.append.php` | an active extension | the extension (for example the Oracle aliases) |

`settings/override/` wins over every extension. The override files hold the database password: keep them out of
version control ([Securing the database](securing-the-database.md)).

## Read the value in effect

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
php bin/php/console exp:ini get site.ini/DatabaseSettings/Database --allow-root-user
php bin/php/console exp:ini list site.ini/DatabaseSettings global --allow-root-user
```

The console first prints a line naming the script it runs, on the error stream, then the value alone, for example
`sqlite3`. Values whose name looks like a secret (`Password` among them) are shown as `********`; `--show-secrets`
shows them, so use it only where nobody reads over your shoulder.

In the administration, **Setup > System information** shows the engine, server, database name and character set in
the Database card and the Database connection part; the database user and password are never shown there
([System information](../../guides/system-information.md)).

## Find the file a value comes from

```bash
php bin/php/console exp:ini where site.ini/DatabaseSettings/Server --allow-root-user
php bin/php/console exp:ini where site.ini/DatabaseSettings/Server admin --allow-root-user   # for the siteaccess admin
```

`where` lists every file that sets the variable, in the order the settings are loaded, and the value in effect. A
value you changed that still shows the old one means the change went into a file or a block that is not read.

## Change a value

Edit `settings/override/site.ini.append.php` with an editor, or let `exp:ini` write it and record the change:

```bash
php bin/php/console exp:ini set site.ini/DatabaseSettings/Server db2.example.internal global --dry-run --allow-root-user
php bin/php/console exp:ini set site.ini/DatabaseSettings/Server db2.example.internal global --allow-root-user
```

`--dry-run` shows the change without writing. The scope `global` writes `settings/override/`; `siteaccess:<name>`
writes that siteaccess's file. Avoid putting a password on the command line on a shared machine: it is visible in
the process list and the shell history; edit the file instead.

## Make every process read it

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user       # only when Velocity serves the site
```

- Clear the INI cache with `--clear-tag=ini`.
- Reload the PHP-FPM pool that serves the site, gracefully, so that no worker keeps the old connection
  ([Deploying, reload the right PHP-FPM](../../guides/deploying.md#34-after-every-php-change-reload-the-right-php-fpm)).
- Velocity's workers read `site.ini` when they start; restart them.
- Cronjobs and console commands read the settings at every run.

## Changing the password

1. Set the new password on the database server for the site's account, keeping the old one valid if the engine
   allows it (or plan a short maintenance window).
2. Write the new password into `settings/override/site.ini.append.php`.
3. Clear the INI cache, reload PHP-FPM, restart Velocity.
4. Load a page and sign in to the administration.
5. Remove the old password on the server.

If the site uses the DFS cluster, its metadata database has its own login in `file.ini [eZDFSClusteringSettings]`
and in `config.cluster.php` ([Clustering: DFS and its metadata database](clustering-dfs-and-the-database.md)).

## Common mistakes

| Symptom | Cause |
|---|---|
| Every content page fails, `error.log` says "Database implementation not supported" | A mistyped `DatabaseImplementation` alias |
| SQLite: the site reports missing tables | A wrong `Database` value: SQLite creates a new, empty file instead of failing |
| The change shows on the command line but not in the browser | The INI cache was not cleared, or Velocity was not restarted |
| One siteaccess still uses the old server | A value in `settings/siteaccess/<name>/site.ini.append.php` overrides the global one; check with `exp:ini where` |

## Related pages

- [The DatabaseSettings reference](databasesettings-reference.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [Where the connection is stored](../hosting/panels/databases-and-users.md#where-the-connection-is-stored) (for hosting companies)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
