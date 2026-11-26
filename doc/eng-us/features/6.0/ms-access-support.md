# MS Access support

This page is for administrators who have data in Microsoft Access files (`.accdb`, `.mdb`) and for developers who
work on the Access driver. From Exponential 6.0.15 the kernel has two pieces for Access:

1. **An import tool**, `exp:access:import`, that copies the tables of an Access file into the site's database or into
   a data table connection, on Linux with MDBTools (no Access needed) and on Windows with Microsoft's Access ODBC
   driver.
2. **A small Access driver**, `expMSAccessDB` (`databaseName()` is `MSAccess`), over ODBC: Windows with the Access
   Database Engine (ACE, x64) reads and writes; on Linux only the import reads Access files (MDBTools' ODBC driver
   is not usable from PHP).

**Recommendation: import, do not run on Access.** Access is a file database for one desktop. Use the import for
anything real, and keep the driver for reading an Access file in place or for a small read-mostly data table on
Windows. A site on Access is not supported; how far one gets is measured below.

## Contents

1. [Importing an Access file](#importing-an-access-file)
2. [The Access driver](#the-access-driver)
3. [What works and what does not](#what-works-and-what-does-not)
4. [A minimal site on Access](#a-minimal-site-on-access)
5. [Installing the drivers](#installing-the-drivers)
6. [Tests](#tests)

## Importing an Access file

```bash
./console exp:access:import shop.accdb --list                       # the tables and their columns
./console exp:access:import shop.accdb --prefix=shop_               # the plan: tables to make, nothing written
./console exp:access:import shop.accdb --prefix=shop_ --apply       # make the tables, copy the rows
./console exp:access:import shop.mdb --connection=archive --tables=Customers,Orders --mode=replace --apply --format=json
```

| Option | Meaning |
|---|---|
| `--connection` | `site` (the default) or the identifier of a data table connection (any engine with a schema: MySQL/MariaDB, PostgreSQL, SQLite, SQL Server, Oracle, ODBC, Access) |
| `--tables` | only these Access tables (comma list, names as in the file) |
| `--prefix` | in front of every table name, e.g. `shop_` |
| `--mode` | `create` (new tables; a table with rows already is left alone), `append`, `replace` (delete the rows first) |
| `--reader` | `auto`, `mdbtools` (Linux, macOS, BSD) or `odbc` (Windows, ACE) |
| `--apply` | write; without it the command shows the plan only |

Exit code 0 when every table and row arrived, 1 when one failed (the failed rows are listed, the others are written),
2 for wrong options or a file that cannot be read.

**Names** become lower case ASCII with `_` (`Order Details` -> `order_details`, `Umsätze` -> `umsaetze`, `Größe` ->
`groesse`); an SQL keyword gets a trailing `_` (`Order` -> `order_`). **Types**: AutoNumber -> `auto_increment` (the
key of the table; an Access key on other columns stays a unique index), Byte/Integer/Long Integer/Yes-No -> `int`,
Large Number -> `bigint`, Single/Double -> `double`, Currency/Decimal -> `decimal`, Date/Time -> `varchar(19)`
`Y-m-d H:i:s` (Access keeps no time zone), Short Text -> `varchar(n)`, Long Text -> `longtext`, OLE object and binary
-> `longtext` in base64, Replication ID -> `char(38)`. A key column is `NOT NULL`. Ids are copied as they are (SQL
Server: `IDENTITY_INSERT`; PostgreSQL: the sequences are moved past the highest id). Rows go in transactions of 500;
a row the target refuses is reported and its batch is written row by row.

Measured on 2026-11-25 with MDBTools 1.0.0 on Linux, importing MDBTools' test files:

| File | Format | Tables | Rows | SQLite (PDO) | SQL Server, PostgreSQL, MariaDB, SQLite (ODBC) |
|---|---|---|---|---|---|
| `nwind.mdb` | JET3 (Access 97) | 9, one named `Umsätze` | 4,032 | every row | every row |
| `DateTestDatabase.mdb` | JET4 (Access 2000-2003) | 1 | 6 | every row | every row |
| `ASampleDatabase.accdb` | ACE12 (Access 2007 and later) | 1 (`Asset Items`, a text key) | 65 | every row | every row |

Checked values: names and dates of an employee, a price, a Yes/No column, photos as base64. Found and fixed on the
way: MDBTools refuses a table name such as `Umsätze` in the C locale (the importer runs it in `C.UTF-8`); SQL Server
refuses a key on a nullable column and MariaDB a `NOT NULL` column with `DEFAULT NULL` (key columns are now `NOT NULL`
with a default). On Windows the job `msaccess-windows` imports the same files through the ODBC reader.

## The Access driver

```ini
[DatabaseSettings]
DatabaseImplementation=msaccess
Database=C:\data\shop.accdb

[MSAccessSettings]
Driver=Microsoft Access Driver (*.mdb, *.accdb)
ExtendedAnsiSQL=enabled
```

`ImplementationAlias[msaccess]` and `[access]` name `expMSAccessDB`. `[MSAccessSettings]`: `Driver` (empty: ACE on
Windows, `MDBTools` elsewhere), `ExtendedAnsiSQL` (ANSI-92 mode: `DEFAULT` and `DECIMAL` in `CREATE TABLE`,
`BAND`/`BOR`; enabled), `ReadOnly`, `Exclusive`, `ConnectionString`, `Layer` (`auto` takes PHP's odbc extension
first, the only layer that can list Access's tables). As a data table connection: `Engine=msaccess`, the file in one
of `datatable.ini [ConnectionSettings] SQLiteDirectories`.

The Access dialect (`expMSAccessDialect`):

| Kernel word | Access |
|---|---|
| `LIMIT n` | `TOP n` after `SELECT [DISTINCT]`; a `UNION` or a statement with its own `TOP` is wrapped |
| `OFFSET m` | none: `arrayQuery()` reads `TOP m+n` and drops the first `m` rows in PHP; `sqlLimit()` with an offset throws `expDBUnsupportedException` |
| `AUTO_INCREMENT` | `COUNTER` (AutoNumber); the new id from `SELECT @@IDENTITY` |
| `CONCAT()`, `\|\|` | `&` |
| `CASE WHEN` | `IIf()` |
| `SUBSTRING()`, `LENGTH()` | `Mid()`, `Len()` |
| date literals | `#yyyy-mm-dd hh:nn:ss#` |
| `` `name` `` | `[name]` (a name cannot hold `]`, at most 64 characters) |
| several statements | one per call: `query()` cuts a script at its semicolons and runs them in turn (not atomic unless in a transaction) |
| comments | removed (Access takes none) |
| `UNIX_TIMESTAMP()` | the date's days as a Double times 86400 (DateDiff in seconds overflows after 2038); `Now()` is the machine's local time |
| `&`, `\|` (bits) | `BAND`/`BOR` in ANSI-92 mode; without it exact integer arithmetic for a constant mask |
| upsert, `GROUP_CONCAT`, `MD5()` | none: `expDBUnsupportedException` (do it in PHP) |
| `FOR UPDATE` | left out: the engine locks pages itself |

## What works and what does not

Windows: measured by the GitHub job `msaccess-windows` (windows-2022 and windows-2025, Access Database Engine 2016 x64,
PHP 8.4). Linux: measured on 2026-11-25 with MDBTools 1.0.0 (Ubuntu 24.04).

| What | Windows, ACE x64 (`msaccess-windows`) | Linux, MDBTools |
|---|---|---|
| import (`exp:access:import`) | **works** through the ODBC reader for Access 2000 and later files (JET4 `.mdb`, ACE `.accdb`: DateTestDatabase.mdb, ASampleDatabase.accdb); **not** Access 97 files (JET3: ACE 2016 refuses nwind.mdb, "created with a previous version"): import those with MDBTools | **works**: MDBTools' programs, JET3, JET4 and ACE files (above) |
| `expMSAccessDB` connect, list tables (`SQLTables`), describe columns | works (AL-01) | **does not**: the MDBTools ODBC driver connects through PDO_ODBC, but cannot fetch rows ("Driver does not support this function", SQLFetchScroll) and its SQL parser refuses `TOP`, `COUNT(*)`, `JOIN` and `INSERT`; with PHP's odbc extension the process ended without a message. Use the import |
| DDL from `.dba` (COUNTER, TEXT(n), LONGTEXT, DECIMAL, DEFAULT, indexes) | works (AL-02) | - |
| insert and `@@IDENTITY` | works (AL-03); DECIMAL(19,0) keeps 2^53 + 1 | - |
| paging (TOP, offset dropped in PHP; TOP's ties) | works (AL-04); a bare `TOP 3` returned 4 rows (ties), `arrayQuery()` 3 | - |
| `&`, `IIf`, `Mid`, `Len`, dates, time stamps past 2038, BAND/BOR and the emulation | works (AL-05); past 2038 since the days-as-Double form (DateDiff/DateAdd in seconds failed: "Numeric value out of range") | - |
| transactions | works (AL-06) | - |
| Unicode, 1 MB long text | works (AL-07) | - |
| scripts of several statements, comments | works (AL-08) | - |
| refused: `LIMIT`, `OFFSET FETCH`, `FOR UPDATE`, `MERGE`, upsert, `GROUP_CONCAT`, `MD5`, `TEXT(300)`; a second writer on a locked row | all refused (AL-09): "Syntax error in FROM clause" (LIMIT, FOR UPDATE), "Reserved error (-1001)" (OFFSET FETCH), "Invalid SQL statement" (MERGE), "Size of field is too long" (TEXT(300)), "Could not update; currently locked" (second writer) | - |
| data table connection (engine `msaccess`) | not measured: the CI file is outside `SQLiteDirectories` (AL-10 skips) | - |

Measured on 2026-11-26 by `expMSAccessLiveTest` in the job `msaccess-windows` on windows-2022 and windows-2025 (the
same result on both); the job runs with every push to main, and its log lists what Access refused.

**Limits of Access itself** (Microsoft's specifications, not measured here): 2 GB per file including system objects;
255 columns per table; 255 characters in a Short Text; 32 indexes per table; 255 concurrent users in theory, far fewer
in practice; one lock file (`.laccdb`) next to the database, so the directory must be writable by every user; no
server process, every client reads the pages over the file system (over a network share a dropped connection can
corrupt the file).

## A minimal site on Access

The test `expMSAccessKernelSchemaTest` makes the kernel's schema (`share/db_schema.dba`) in an empty `.accdb`, writes
the kernel's data (`share/db_data.dba`), then runs kernel reads with Access as the site's database. Its report is the
artifact `msaccess-kernel-probe-<os>` of the job.

Not measured yet: the probe runs for the first time in the job `msaccess-windows`. What the dialect already tells:
the kernel reads language masks with `bitAnd()` (BAND in ANSI-92 mode, else only a constant mask), pages with
`LIMIT`/`OFFSET` through `arrayQuery()` (TOP and PHP), and writes with `sqlUpsert()` and `GROUP_CONCAT` in places
(refused on Access); every kernel upgrade, cache and cluster path assumes a server. A site on Access is therefore not
a target.

## Installing the drivers

- **Linux, import only**: `apt install mdbtools` (Debian, Ubuntu) or `dnf install mdbtools` (EPEL); the importer
  needs `mdb-tables`, `mdb-schema` and `mdb-json` (MDBTools 0.9 or later). The package `odbc-mdbtools` (driver
  `MDBTools`) is not usable from PHP (above).
- **Windows**: the *Microsoft Access Database Engine 2016 Redistributable*, x64 for a 64-bit PHP
  (`accessdatabaseengine_X64.exe /quiet`); PHP's `odbc` and `pdo_odbc` extensions. A new empty file is made with
  Access, or in PowerShell: `(New-Object -ComObject ADOX.Catalog).Create("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=C:\data\new.accdb")`.
  A 32-bit Office on the same machine blocks the x64 engine's installer (`/passive` forces it, at your own risk).

## Tests

| File | Where | What |
|---|---|---|
| `tests/tests/lib/ezdb/odbc/expMSAccessDialectTest.php` | DB-less | tokens, names, literals, TOP, splitting, functions, bit emulation against PHP, DDL, the helpers |
| `tests/tests/lib/ezdb/odbc/expODBCDBTest.php` | DB-less | connection strings with ACE's switches, MDBTools without, the dialect's expressions |
| `tests/tests/kernel/classes/msaccess/expAccessImportTest.php` | DB-less | names, types, schema, the MDBTools schema parser, literals, an import into SQLite with a bad row, the modes |
| `tests/tests/kernel/classes/msaccess/live/expAccessImportLiveTest.php` | `EXP_TEST_ACCESS_FIXTURES` | MDBTools' test files into SQLite and every ODBC target |
| `tests/tests/lib/ezdb/odbc/live/expMSAccessLiveTest.php` | Windows, `EXP_TEST_MSACCESS_FILE` | the driver on ACE, and what Access refuses |
| `tests/tests/lib/ezdb/odbc/live/expMSAccessKernelSchemaTest.php` | Windows, `EXP_TEST_MSACCESS_KERNEL_FILE` | the minimal site probe |
