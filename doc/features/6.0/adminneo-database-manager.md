# AdminNeo: the database manager behind the Database Source Editor

**Repository:** `se7enxweb/adminneo` (a fork of the AdminNeo project, itself based on Adminer). Version 5.2.1 (2025-12-07) is the newest tag; the fork's main branch carries 5.3 development.
**History:** [adminneo](../../history/ecosystem/adminneo.md): 1,121 changes, all upstream history carried by the fork (the se7enxweb fork contains no changes of its own in the ledger).
**In Exponential:** the extension `sevenx_dse` embeds AdminNeo in the legacy administration interface, behind role-based permissions. See [sevenx_dse](extensions/sevenx_dse.md) for how to install and use it.

## What it is

AdminNeo is a complete database management tool written in PHP. It consists of a single file you can upload to a server. **EditorNeo** is the companion that offers data editing to end users without structure or SQL access. In Exponential you reach AdminNeo through `sevenx_dse`, so a site administrator can look at tables and run SQL from the same administration interface as everything else, without installing a separate tool next to the site.

Supported databases (README): MySQL, MariaDB, PostgreSQL, MS SQL, SQLite, Oracle, MongoDB, SimpleDB, Elasticsearch (beta) and ClickHouse (alpha). Requirements: PHP 5.4 or newer with sessions to run the compiled file, PHP 7.1 or newer to run it from source; the composer package states PHP 7.1 to 8.5. The OpenSSL extension is recommended so stored login information is encrypted.

## What it gives an Exponential administrator

- Browse tables, filter, sort, page through rows and edit values in place.
- Run SQL commands with syntax highlighting and (from 5.1.0) autocompletion.
- Create, alter and drop tables, columns and indexes.
- Export and import: SQL dumps, CSV, TSV; exports can be zipped (Bz2/Zip output plugins) or written as JSON or XML (plugins).
- A clean responsive interface with dark mode and colour variants (from 5.0.0).
- Plugins and customisations: the fork ships plugins for logins (one-time password, IP, table based, external), foreign key editing, JSON preview, slug generation, translation, a Tiny MCE editor, SQL logging and an assistant plugin that generates SQL from a prompt.

## Release highlights since December 2023

| Release | Date | Highlights (from the project changelog) |
|---|---|---|
| 5.0.0 | 2025-05-29 | First release of AdminNeo and EditorNeo as standalone products: new responsive theme with dark mode and colour variants, easy configuration, reviewed plugins and customisations. Version 5 breaks backward compatibility with older Adminer based customisations; read the upgrade guide of the project before upgrading. |
| 5.1.0 | 2025-08-31 | New Settings page with basic UI options; autocompletion in the SQL command editor; `defaultServer` and `defaultDatabase` configuration parameters; generated columns (MySQL, SQLite); computed columns (MS SQL); CockroachDB support; indexes on materialized views (PostgreSQL); the editor displays database views; install through Composer. Many bug fixes, for example the MongoDB driver and ordering by `COUNT(*)`. |
| 5.1.1 | 2025-09-01 | Compiler fix: autocompletion script missing from a single-driver file. |
| 5.2.0 | 2025-11-02 | `sqlAutocompletion` and `relationLinks` configuration parameters; links to referencing tables; relation links for PostgreSQL and MS SQL; PostgreSQL procedures (11+); URL parameter `?ext=pdo` to force PDO; credential escaping fix for web based drivers; a plugin that generates SQL from an AI prompt (needs an API key you provide). |
| 5.2.1 | 2025-12-07 | PostgreSQL: connect through the default socket when no server is given; JSON dump format fix; CSV export opens in the browser window again; cookie escaping fix. |
| 5.3 (main, unreleased) | 2026-03 | Declares PHP 8.5 compatibility; more secure randomness and 256 bits of entropy in all random strings; support for new system tables in the foreign keys plugin; fix for paging the selection table (issue #181). |

The month by month list of all 1,121 changes, including the smaller UI and driver fixes, is in the [repository history](../../history/ecosystem/adminneo.md#upstream-history-carried-by-the-fork-by-month).

## Use it standalone (optional)

Without the extension you can still use AdminNeo directly: build the single file and upload it.

```bash
git clone git@github.com:se7enxweb/adminneo.git
cd adminneo
php bin/compile.php            # writes the single-file version (see the Makefile for targets)
```

Upload the compiled file to a protected location (HTTP authentication or a network restriction in front of it) and open it in a browser. Never leave a database manager reachable by anonymous visitors.

The `examples` directory of the repository contains customisation and plugin examples; the `plugins` directory lists the plugins named above.

## Limits

- A database manager gives full access to the data it can log in to. Restrict it by role, network and backup discipline; the extension adds a safety gate that asks for a backup acknowledgement.
- The AI SQL plugin sends your prompt to a third party service when you enable it. It is off unless you configure it.
- Features of the unreleased 5.3 line may change before a tag.

## Related

[Platform ecosystem overview](../../history/ecosystem.md) · [Package map](../../specifications/6.0/platform-package-map.md)
