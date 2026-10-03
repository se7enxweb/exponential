# Platform SQLite installer

**Scope:** how the installers of Exponential Platform put a complete database into one SQLite file. For the legacy kernel's own SQLite driver see [SQLite 3 database driver](sqlite3-database-driver.md); for the task-oriented guide see [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md).
**History:** [core](../../history/ecosystem/core.md), [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md), [ezpublish-kernel](../../history/ecosystem/ezpublish-kernel.md), [exponential-platform-legacy](../../history/ecosystem/exponential-platform-legacy.md), [exponential-platform-nexus](../../history/ecosystem/exponential-platform-nexus.md), [legacyBridge](../../history/ecosystem/legacyBridge.md).

## Install types

An install type is a named installer registered with a service tag. `install <type>` runs `importSchema()`, `importData()` and `importBinaries()` of the class behind the name.

| Type | Class | Tag | Line | What it installs |
|---|---|---|---|---|
| `exponential-oss` | `ExponentialOssInstaller` (extends `CoreInstaller`) | `ezplatform.installer` (2.5 line, `ezpublish-kernel`, 2026-04-09), `ibexa.installer` (Platform v5: the Nexus starter and `exponential-platform-dxp-core` each register it) | 2.5, v5 | clean platform content from `data/<dbms>/cleandata.sql`; on SQLite `data/sqlite/cleandata.sql` |
| `ibexa-oss` (and legacy alias `clean`) | `CoreInstaller` | `ezplatform.installer` (3.x / 4.6 line), `ibexa.installer` (v5) | 3.x, 4.6, v5 | upstream seed data; the 3.x kernel and v5 `core` carry `data/sqlite/cleandata.sql` (v5: 322 lines, 2026-04-29), so this type also runs on SQLite |
| `exponential-media` | `ExponentialMediaInstaller` (package `se7enxweb/exponential-platform-dxp-core`) | `ibexa.installer` | v5 | the Nexus demo: `media_schema.sql` and `media_data.sql` of the DBMS in use, from `data/{mysql,sqlite,postgresql}/`; replaces the MySQL-only `netgen-media` type |
| `exponential-cjw` | `ExponentialCjwInstaller` (`src/AppBundle/Installer/`) | `ezplatform.installer` | Nexus 1.x | CJW starter content; on SQLite `sqlite/schema.sql` (164 tables, composite keys preserved) and `sqlite/data.sql` (41,568 named-column inserts) |

Seed file lookup: `DbBasedInstaller::getKernelSQLFileForDBMS($relative)` returns `<baseDataDir>/<dbms>/<relative>` where `<dbms>` is `mysql`, `postgresql` or `sqlite`, chosen by `instanceof` on the Doctrine platform (since 2026-04-29). A missing or unreadable file stops the install with an error naming the path.

## Install command behaviour on SQLite

`InstallPlatformCommand::checkCreateDatabase()`:

1. If the connection platform is SQLite, the command does not call `doctrine:database:create --if-not-exists` (it fails with "getListDatabasesSQL is not supported by platform").
2. For a file database (not `:memory:`) it creates the parent directory with mode `0755` when missing; failure ends the command with the general database error exit code.
3. It prints `Using SQLite database <path> (file created automatically on first connection).` and returns.

Other engines keep the subprocess path. On the 2.5 line the indexing step is wrapped in a try/catch, so a non-fatal reindex warning on an empty install does not abort it.

## Schema generation

`CoreInstaller::importSchema()` builds the schema from the schema builder events and generates DDL for the connection's platform. On SQLite the platform object is replaced by `Ibexa\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform` when the connection reports the bare `SqlitePlatform`. Reason: tables with a composite primary key (`id`, `version`) such as the content field table must not carry `AUTOINCREMENT` on `id`; SQLite does not support that, and the generic platform produced a single-column key, which made editing a draft raise a unique constraint violation because the same field id appears in several versions.

On the 3.x / 4.6 kernel (which predates that class) the same defect is corrected in the seed file: `data/sqlite/cleandata.sql` starts, after the pragmas, with `DROP TABLE IF EXISTS`, `CREATE TABLE` (composite key) and `CREATE INDEX` blocks for `ezcontentclass`, `ezcontentclass_attribute` and `ezcontentobject_attribute`, 96 lines, before any insert. An `AUTOINCREMENT` was also removed from `ezcontentobject_attribute.id` for the same reason.

## Seed data conventions

- Seed files are SQL with one statement group per table, `INSERT INTO "<table>" (...) VALUES (...)`.
- MySQL escape sequences are converted: `\'` becomes `''`, `\"` becomes `"`, `\\` becomes `\`; `\n`, `\r`, `\t` become real whitespace in the platform seed (the Nexus 1.x `data.sql` keeps them as two-character literals so statements can be split safely and converts them after splitting in `runQueriesFromFile()`, so XML field values parse correctly).
- On the 3.x kernel `ibexa-oss` seed data, 73 legacy schema tables are appended so the legacy kernel can share the file (2026-03-29).

## Configuration

| Line | File | Setting | Default | Scope |
|---|---|---|---|---|
| Platform v5 | `.env.local` | `DATABASE_URL` | `sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db` (documented) | per environment |
| Platform v5 | `.env.local` | `MESSENGER_TRANSPORT_DSN` | `sync://` (documented; needed with SQLite) | per environment |
| 2.5 line | `app/config/parameters.yml` | `database_driver` | `pdo_sqlite` to select SQLite | project |
| 2.5 line | `app/config/default_parameters.yml` | `database_path` | `%kernel.project_dir%/var/data_%kernel.environment%.db` | project |
| 2.5 line | environment | `DATABASE_PATH` | unset; overrides `database_path` via `app/config/env/generic.php` | process |
| 2.5 line | `app/config/config.yml` | `doctrine.dbal.default_connection.path` | `'%database_path%'` | project |
| Nexus 1.x | `app/config/default_parameters.yml` | `ngsite.default.locations.tree_root.id` | 168 in the CJW seed data | project |

## Legacy kernel on the same file

`LegacyMapper\Configuration` of the legacy bridge keeps a driver map from Doctrine to legacy implementations. Since `v4.0.0.1` it contains `pdo_sqlite => sqlite3`, and for that driver it writes Doctrine's `path` parameter to the legacy `site.ini` `[DatabaseSettings] Database` value (Doctrine stores the SQLite file under `path`, not `dbname`). Without it the legacy kernel received no database name.

## Verify an installation

```bash
php -m | grep -i sqlite                                   # pdo_sqlite and SQLite3
sqlite3 var/data_dev.db ".tables" | tr -s ' ' '\n' | wc -l # number of tables
sqlite3 var/data_dev.db "SELECT COUNT(*) FROM ezcontentobject;"
```

(`ezcontentobject` is the content table name on the 3.x kernel; v5 uses `ibexa_content`.)

## Related

[Package map](platform-package-map.md) · [Console commands](platform-console-commands.md) · [Legacy bridge specification](legacy-bridge-bundle.md)
