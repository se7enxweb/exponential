# SQLite for Exponential Platform: no database server needed

This page is for developers who want an Exponential Platform (the Symfony based platform) running in minutes, without
a database server. It is **not** about the legacy kernel's own SQLite driver: for that, see
[SQLite database for Exponential 6](sqlite-database.md). Reference:
[Platform SQLite installer specification](../../specifications/6.0/platform-sqlite-installer.md).

**Repositories:** `core` (Platform v5 kernel), `ezplatform-kernel` (3.x / 4.6 line), `ezpublish-kernel` (2.5 line),
`exponential-platform-legacy`, `exponential-platform-nexus` / `cjw-exponential-platform-nexus`, `legacyBridge`.

## What it is

Between January and April 2026 the platform installers were taught to install into a single SQLite file. You need PHP's `pdo_sqlite` and `sqlite3` extensions and nothing else: no MySQL, no MariaDB, no PostgreSQL server. The result is a complete platform database (content types, content, users, roles, search tables) in `var/data_dev.db`.

## Why it helps

- A new developer has a running administration interface minutes after `composer install`.
- Automated tests and demos run without provisioning a server.
- You can carry a whole site as one file, and convert it to MySQL or PostgreSQL later ([DXP skeleton guide](platform-dxp-skeleton.md), database conversion).

SQLite is for development, testing, demos and air-gapped deployments. Use MySQL, MariaDB or PostgreSQL for production sites with concurrent editors.

## Use it (Platform v5)

Check the extensions:

```bash
php -m | grep -i sqlite        # must list pdo_sqlite and SQLite3
```

Put this in `.env.local`:

```bash
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

`sync://` is needed because the default Doctrine messenger transport wants a second database connection. Remove the `DATABASE_DRIVER`, `DATABASE_HOST`, `DATABASE_PORT`, `DATABASE_NAME`, `DATABASE_USER` and `DATABASE_PASSWORD` lines; they are ignored when a full DSN is set.

Install:

```bash
php bin/console exponential:install exponential-oss
```

You will see `Using SQLite database <path> (file created automatically on first connection).` The installer creates the directory if needed, creates the tables, imports the seed data and indexes the content. Then make the file writable for the web server (SQLite writes to the file on every edit):

```bash
chmod 664 var/data_dev.db
chown $USER:www-data var/data_dev.db     # use your web server group
php bin/console cache:clear
```

The directory that holds the file must also be writable, because SQLite creates journal files next to it.

## Use it (Platform 2.5 / Nexus 1.x style projects)

Set the driver and path in `app/config/parameters.yml`:

```yaml
parameters:
    database_driver: pdo_sqlite
    database_path: '%kernel.project_dir%/var/data_%kernel.environment%.db'
```

(The default of `database_path` is `%kernel.project_dir%/var/data_%kernel.environment%.db`, for example `var/data_dev.db`; the environment variable `DATABASE_PATH` overrides it in `app/config/env/generic.php`.) Then:

```bash
php bin/console ezplatform:install exponential-oss        # platform data
php bin/console ezplatform:install exponential-cjw        # Nexus: the CJW starter content (Nexus 1.0.0.4 and later)
```

On the 2.5 line the command is still `ezplatform:install`. The kernel of the 3.x / 4.6 line registers it as `exponential:install` with the old name as alias and installs the type `ibexa-oss` (`php bin/console exponential:install ibexa-oss`); see [command names](../../specifications/6.0/platform-console-commands.md) and the [installer specification](../../specifications/6.0/platform-sqlite-installer.md) for every install type.

## With the legacy bridge

A platform installation on SQLite can also run the Exponential legacy kernel through the [legacy bridge](legacy-bridge.md) since `v4.0.0.1`: the bridge maps `pdo_sqlite` to the legacy `sqlite3` implementation and hands the file path to the legacy `site.ini`. The legacy kernel's SQLite driver itself is described in [the SQLite 3 database driver specification](../../specifications/6.0/sqlite3-database-driver.md).

## What was fixed along the way (and why you can trust it)

| Problem on SQLite | Fix | Where / when |
|---|---|---|
| `doctrine:database:create` fails with "getListDatabasesSQL is not supported by platform" | the install command detects SQLite, ensures the directory exists and skips the call | `core` 2026-04-29, `ezplatform-kernel` 2026-04-11 |
| The installer chose the SQL file directory from the Doctrine platform name, which does not reliably name the SQLite directory | the DBMS is detected with `instanceof` (MySQL, PostgreSQL, SQLite) | `core` 2026-04-29 |
| Tables with a composite primary key (`id`, `version`) such as content attributes received a single-column autoincrement key, so saving a new draft raised a unique constraint violation | the installer substitutes the Ibexa SQLite platform class, so composite-key tables do not get `AUTOINCREMENT`; the 3.x seed file drops and recreates the three affected tables with the right key | `core` 2026-04-29, `ezplatform-kernel` 2026-04-07 / 04-11 |
| MySQL escape sequences in the seed data (`\'`, `\n`) broke SQLite | seed data converted: `''` for quotes, real whitespace for `\n \r \t` | `ezpublish-kernel` 2026-04-09, `ezplatform-kernel` 2026-04-07 |
| Legacy tables missing for platform plus legacy installs | 73 legacy schema tables appended to the seed data | `ezplatform-kernel` 2026-03-29 |
| A harmless reindex warning aborted a fresh install | the indexing step is wrapped so a warning on an empty install does not stop the command | `ezpublish-kernel` 2026-04-09 |

## Limits

- One writer at a time. Do not use SQLite for a busy editorial team.
- File permissions are the usual installation problem: the web server user must own or write the `.db` file and its directory.
- The seed files carry the content model of the install type you choose; a different install type needs its own seed data.

## Related pages

- [SQLite database for Exponential 6](sqlite-database.md), [SQLite: transactions queue for the write lock](../../bc/6.0/sqlite-transactions.md)
- Platform features: [Nexus starter](platform-nexus-starter.md), [DXP skeleton](platform-dxp-skeleton.md), [platform administration interface](platform-admin-ui-fork.md), [Layouts on the platform](platform-layouts-core-fork.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [site bundles](platform-site-bundles.md), [legacy bridge](legacy-bridge.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md), [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform package map](../../specifications/6.0/platform-package-map.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [core](../../history/ecosystem/core.md), [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md), [ezpublish-kernel](../../history/ecosystem/ezpublish-kernel.md), [exponential-platform-legacy](../../history/ecosystem/exponential-platform-legacy.md), [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md), [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [June 2026, second half](../../history/2026/2026-06b.md)
