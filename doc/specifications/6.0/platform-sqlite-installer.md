# Specification: the Exponential Platform SQLite installer

This page is the reference for how the installers of Exponential Platform (the Symfony based platform) put a
complete database into one SQLite file: the install types, how the install command behaves on SQLite, how the
schema and seed data are built, and the settings that select SQLite. Read it if you maintain a platform
installer or need to know why an install on SQLite behaves the way it does. For step-by-step instructions, use
[SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md). For the legacy kernel's own
driver, use [SQLite 3 database driver](sqlite3-database-driver.md).

## In short

- An install type is a named installer class; `install <type>` runs its `importSchema()`, `importData()` and
  `importBinaries()`.
- Seed files are looked up per database engine: `<baseDataDir>/<dbms>/<relative>`, with `<dbms>` one of
  `mysql`, `postgresql`, `sqlite`.
- On SQLite the install command creates no database; the file is created on the first connection.
- Composite primary keys (`id`, `version`) are kept, so drafts can be edited without a unique constraint error.

## Install types

An install type is registered with a service tag. The command `install <type>` runs `importSchema()`,
`importData()` and `importBinaries()` of the class behind the name.

| Type | Class | Tag | Line | What it installs |
|---|---|---|---|---|
| `exponential-oss` | `ExponentialOssInstaller` (extends `CoreInstaller`) | `ezplatform.installer` (2.5 line, `ezpublish-kernel`, 2026-04-09); `ibexa.installer` (Platform v5: the Nexus starter and `exponential-platform-dxp-core` each register it) | 2.5, v5 | Clean platform content from `data/<dbms>/cleandata.sql`; on SQLite `data/sqlite/cleandata.sql` |
| `ibexa-oss` (legacy alias `clean`) | `CoreInstaller` | `ezplatform.installer` (3.x / 4.6 line); `ibexa.installer` (v5) | 3.x, 4.6, v5 | Upstream seed data. The 3.x kernel and v5 `core` carry `data/sqlite/cleandata.sql` (v5: 322 lines, 2026-04-29), so this type also runs on SQLite |
| `exponential-media` | `ExponentialMediaInstaller` (package `se7enxweb/exponential-platform-dxp-core`) | `ibexa.installer` | v5 | The Nexus demo: `media_schema.sql` and `media_data.sql` of the engine in use, from `data/{mysql,sqlite,postgresql}/`. Replaces the MySQL-only `netgen-media` type |
| `exponential-cjw` | `ExponentialCjwInstaller` (`src/AppBundle/Installer/`) | `ezplatform.installer` | Nexus 1.x | CJW starter content. On SQLite: `sqlite/schema.sql` (164 tables, composite keys kept) and `sqlite/data.sql` (41,568 inserts with named columns) |

### Seed file lookup

`DbBasedInstaller::getKernelSQLFileForDBMS( $relative )` returns `<baseDataDir>/<dbms>/<relative>`. `<dbms>` is
`mysql`, `postgresql` or `sqlite`, chosen by an `instanceof` test on the Doctrine platform (since 2026-04-29).
A missing or unreadable file stops the install with an error that names the path.

## The install command on SQLite

`InstallPlatformCommand::checkCreateDatabase()` does three things when the connection platform is SQLite:

1. It skips `doctrine:database:create --if-not-exists`. That command fails on SQLite with
   "getListDatabasesSQL is not supported by platform".
2. For a file database (not `:memory:`) it creates the parent directory with mode `0755` when it is missing.
   If that fails, the command ends with the general database error exit code.
3. It prints `Using SQLite database <path> (file created automatically on first connection).` and returns.

Other engines keep the subprocess path. On the 2.5 line the indexing step is wrapped in a try/catch, so a
non-fatal reindex warning on an empty install does not stop the install.

## Schema generation

`CoreInstaller::importSchema()` builds the schema from the schema builder events and generates the DDL for the
connection's platform. On SQLite, when the connection reports the bare `SqlitePlatform`, the platform object is
replaced by `Ibexa\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform`.

Why: tables with a composite primary key (`id`, `version`), such as the content field table, must not carry
`AUTOINCREMENT` on `id`. SQLite does not support that combination. The generic platform produced a single-column
key, so editing a draft raised a unique constraint violation: the same field id appears in several versions.

The 3.x / 4.6 kernel predates that class, so the defect is corrected in the seed file instead.
`data/sqlite/cleandata.sql` starts, after the pragmas, with `DROP TABLE IF EXISTS`, `CREATE TABLE` (composite
key) and `CREATE INDEX` blocks for `ezcontentclass`, `ezcontentclass_attribute` and `ezcontentobject_attribute`
(96 lines) before any insert. An `AUTOINCREMENT` was also removed from `ezcontentobject_attribute.id` for the
same reason.

## Seed data conventions

- Seed files are SQL with one statement group per table: `INSERT INTO "<table>" (...) VALUES (...)`.
- MySQL escape sequences are converted: `\'` becomes `''`, `\"` becomes `"`, `\\` becomes `\`.
- In the platform seed, `\n`, `\r` and `\t` become real whitespace. The Nexus 1.x `data.sql` keeps them as
  two-character literals, so statements can be split safely, and converts them after splitting in
  `runQueriesFromFile()`. XML field values then parse correctly.
- The 3.x kernel `ibexa-oss` seed data has 73 legacy schema tables appended, so the legacy kernel can share the
  file (2026-03-29).

## Settings

| Line | File | Setting | Default | Scope |
|---|---|---|---|---|
| Platform v5 | `.env.local` | `DATABASE_URL` | `sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db` (documented value) | per environment |
| Platform v5 | `.env.local` | `MESSENGER_TRANSPORT_DSN` | `sync://` (documented value; needed with SQLite) | per environment |
| 2.5 line | `app/config/parameters.yml` | `database_driver` | `pdo_sqlite` selects SQLite | project |
| 2.5 line | `app/config/default_parameters.yml` | `database_path` | `%kernel.project_dir%/var/data_%kernel.environment%.db` | project |
| 2.5 line | environment variable | `DATABASE_PATH` | unset; overrides `database_path` through `app/config/env/generic.php` | process |
| 2.5 line | `app/config/config.yml` | `doctrine.dbal.default_connection.path` | `'%database_path%'` | project |
| Nexus 1.x | `app/config/default_parameters.yml` | `ngsite.default.locations.tree_root.id` | `168` in the CJW seed data | project |

## The legacy kernel on the same file

`LegacyMapper\Configuration` in the legacy bridge keeps a map from Doctrine drivers to legacy drivers. Since
`v4.0.0.1` it contains `pdo_sqlite => sqlite3`. For that driver it writes Doctrine's `path` parameter to the
legacy `site.ini` `[DatabaseSettings] Database` value, because Doctrine stores the SQLite file under `path`, not
`dbname`. Before this, the legacy kernel received no database name.

## Verify an installation

Run these in the project root (read-only):

```bash
php -m | grep -i sqlite                                    # expect pdo_sqlite and sqlite3
sqlite3 var/data_dev.db ".tables" | tr -s ' ' '\n' | wc -l # number of tables
sqlite3 var/data_dev.db "SELECT COUNT(*) FROM ezcontentobject;"
```

`ezcontentobject` is the content table on the 3.x kernel; on v5 query `ibexa_content` instead.

## Related pages

- Specifications: [Platform package map](platform-package-map.md), [Platform console command names](platform-console-commands.md), [Legacy bridge bundle](legacy-bridge-bundle.md), [SQLite 3 database driver](sqlite3-database-driver.md)
- Features: [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md), [SQLite database support](../../features/6.0/sqlite-database.md), [Platform administration interface](../../features/6.0/platform-admin-ui-fork.md), [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md), [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md), [Nexus starter](../../features/6.0/platform-nexus-starter.md), [PHP 8.5 framework forks](../../features/6.0/platform-php85-framework-forks.md), [Site bundles](../../features/6.0/platform-site-bundles.md), [Legacy bridge](../../features/6.0/legacy-bridge.md), [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- Upgrade notes: [Package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md), [SQLite: transactions queue for the write lock](../../bc/6.0/sqlite-transactions.md)
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md); repositories [core](../../history/ecosystem/core.md), [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md), [ezpublish-kernel](../../history/ecosystem/ezpublish-kernel.md), [exponential-platform-legacy](../../history/ecosystem/exponential-platform-legacy.md), [exponential-platform-nexus](../../history/ecosystem/exponential-platform-nexus.md), [legacyBridge](../../history/ecosystem/legacyBridge.md); months [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [June 2026, second half](../../history/2026/2026-06b.md)
