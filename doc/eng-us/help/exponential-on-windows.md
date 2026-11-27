---
title: Exponential on Windows
audience: admin, developer
category: install-upgrade
order: 12
summary: Exponential 6.0.x runs on Windows - on Windows Server and on Windows 10 and 11 - with the official PHP for Windows behind IIS, Apache or Exponential Velocity, on SQLite, Microsoft SQL Server, PostgreSQL, MySQL, MariaDB, Oracle or MongoDB. This page covers the PHP to install, the web servers, Velocity's portable worker pool for Windows, the databases, what the project's CI runs on Windows and what differs from Linux.
keywords: Windows, Windows Server, Windows 11, Windows 10, IIS, FastCGI, URL Rewrite, web.config, Apache on Windows, PHP for Windows, php.exe, php-cgi, Velocity on Windows, vc, vcc, qbixserver, portable pool, pool mode, proc_open, SQL Server, LocalDB, sqlsrv, pdo_sqlsrv, Azure SQL, WSL2, PowerShell, smoke test
featured: true
context: setup/info
type: help
---
# Exponential on Windows

**Exponential runs on Windows.** It is plain PHP and a database, so the official PHP for Windows runs it the same
way it runs on Linux and macOS: behind IIS, behind Apache, or on Exponential Velocity, the application server that
ships with Exponential, which has a worker pool of its own for Windows. The database can be SQLite (no server at all),
Microsoft SQL Server and Azure SQL, PostgreSQL, MySQL, MariaDB, Oracle or MongoDB.

This page is for administrators who host Exponential on Windows Server and for developers who build sites on a
Windows desktop. The other systems are on [Operating system support](os-support.md).

## Contents

1. [What you need](#what-you-need)
2. [Installing](#installing)
3. [Web servers on Windows](#web-servers-on-windows)
4. [Exponential Velocity on Windows](#exponential-velocity-on-windows)
5. [Databases on Windows](#databases-on-windows)
6. [What the project's CI runs on Windows](#what-the-projects-ci-runs-on-windows)
7. [What differs from Linux](#what-differs-from-linux)
8. [Related pages](#related-pages)

## What you need

| Need | On Windows |
|---|---|
| Windows | a Windows release Microsoft still supports: Windows Server 2022 or 2025, Windows 10 or 11 |
| PHP | PHP 8.0 to 8.5 from [windows.php.net](https://windows.php.net/download/), x64 (x86 works for Velocity too). The non-thread-safe (NTS) build for IIS and FastCGI. PHP 7.4 is usable without support, as everywhere ([why](why-exponential-supports-php-7-4.md)) |
| PHP extensions | the official builds carry them all as DLLs; switch them on in `php.ini`: `intl`, `mbstring`, `curl`, `gd`, `fileinfo`, `openssl`, `sockets`, and the database's extension (`sqlite3`, `sqlsrv` or `pdo_sqlsrv`, `pgsql`, `mysqli`, `oci8`, `mongodb`) |
| Composer | the [Composer setup for Windows](https://getcomposer.org/download/) |
| Web server | IIS, Apache for Windows, or Exponential Velocity (comes with Exponential) |
| Database | none for SQLite; otherwise the server of your choice, on the same machine or elsewhere |

## Installing

Composer installs Exponential on Windows exactly as on any other system, from a PowerShell or command prompt:

```bat
composer create-project se7enxweb/exponential C:\sites\exponential
cd C:\sites\exponential
```

Then open the site in a browser and follow the setup wizard, or use the kickstarter; both are described in the
[installation guide](../INSTALL.md). The console installer works the same way, for example on SQLite:

```bat
php bin\php\console exp:install --apply --random-password --url=http://localhost:8080
```

Microsoft SQL Server sites are installed with `--db=sqlserver`
([SQL Server driver](../features/6.0/sqlserver-database-driver.md#installing-a-site-on-sql-server)).

Give the account the web server runs as (the IIS application pool identity, the Apache service account or the account
that starts Velocity) write access to `var\`, `settings\override\` and `settings\siteaccess\` while installing, and to
`var\` afterwards.

## Web servers on Windows

| Server | How PHP runs | Configuration |
|---|---|---|
| **IIS** (Windows Server, Windows 10 and 11) | FastCGI (`php-cgi.exe`, NTS build), the URL Rewrite module for the rewrite rules | [IIS example](../examples/web-servers/iis.md) with a ready [`web.config`](../examples/web-servers/web.config); hosting notes: [IIS](hosting/servers/iis.md) |
| **Apache for Windows** (for example the Apache Lounge builds) | FastCGI or PHP's Apache module | the same virtual host as on Linux: [Apache virtual host](../examples/web-servers/apache-virtual-host.md) |
| **Exponential Velocity** | its own pool of PHP workers | [below](#exponential-velocity-on-windows) |
| PHP's built-in server | one process | for development, from the installation folder: `php -S localhost:8080 -t . bin\php\velocity-router.php` (the router applies the rewrite rules) |

The rewrite rules are the same on every server: the IIS example translates the rules of `.htaccess_root` into URL
Rewrite rules, rule for rule, so the same URLs reach `index.php`, the image and design files are served directly and the
installation's private folders are refused.

## Exponential Velocity on Windows

Exponential Velocity (`vc`) is the application server that ships with Exponential (the root `composer.json` requires
`se7enxweb/exponential-velocity`). It loads the application once and keeps it in memory between requests, which is
where its speed comes from.

**Windows support.** From Velocity 0.0.4.63 on, Velocity has a **portable worker pool**: where there is no `fork()`,
as on Windows, every PHP worker is a process of its own, started with `proc_open()`, that boots the application once
and then answers request after request. Workers connect back to the server over loopback TCP and prove themselves with
a token; a worker that dies is replaced; CTRL+C in the console stops the server and every worker gracefully. The pool
mode is chosen with `--pool-mode` (`auto`, `fork`, `portable`); `auto` means `portable` on Windows and `fork` on Linux,
the BSDs and macOS. Settings and internals: the engine's
[Windows page](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/windows.md).

**Start and stop.** Velocity's server script is in Exponential's `vendor\` folder:

```bat
cd C:\sites\exponential
C:\php\php.exe vendor\se7enxweb\exponential-velocity\sbin\qbixserver.php --root=. --preset=exponential --port=8080 --pid=C:\sites\run\vc.pid
rem in another console:
C:\php\php.exe vendor\se7enxweb\exponential-velocity\sbin\qbixserver.php --stop --pid=C:\sites\run\vc.pid
```

| Action | On Windows |
|---|---|
| Stop | `--stop --pid=FILE`, or CTRL+C in the server's console |
| Reload changed code | `--reload --pid=FILE`: every worker is replaced in turn |
| Workers | `--workers=N`; the default is one per core, 2 to 8 |
| Pool | `--pool-mode=portable` (the default on Windows) |
| Smoke test | `powershell -ExecutionPolicy Bypass -File vendor\se7enxweb\exponential-velocity\tests\windows\smoke.ps1 -Php C:\php\php.exe` |

**Binaries.** Every Velocity release also publishes single-file Windows executables (`qbixserver-windows-x64*.exe`,
one per PHP version and variant) on the
[release page](https://github.com/se7enxweb/exponential-velocity/releases). They carry their own PHP, built without
`intl`, which Exponential needs; for an Exponential site, run Velocity with the official PHP for Windows as shown
above.

## Databases on Windows

| Database | On Windows |
|---|---|
| SQLite 3 | built into the official PHP (`sqlite3`); the database is one file under `var\`. No server |
| Microsoft SQL Server 2017 to 2025, Azure SQL | the kernel's own driver `expSQLServerDB` over Microsoft's `sqlsrv` or `pdo_sqlsrv` extension (or `pdo_odbc`); SQL Server Express and LocalDB work for development ([driver](../features/6.0/sqlserver-database-driver.md)) |
| PostgreSQL 13 to 19 | `pgsql` (in the official PHP); PostgreSQL's Windows installer for the server |
| MySQL, MariaDB | `mysqli` (in the official PHP) |
| Oracle 19c and later | `oci8` with Oracle Instant Client for Windows |
| MongoDB 6.0 and later | the `mongodb` extension DLL from PECL, PHP 8.2 or later |
| Any database through ODBC (6.0.15) | `expODBCDB` over `pdo_odbc` or `odbc` (in the official PHP), for data table connections and imports; tested against LocalDB in CI ([ODBC driver](../features/6.0/odbc-database-driver.md)) |
| MS Access files (6.0.15) | `exp:access:import` and the small driver `expMSAccessDB` through the Access Database Engine 2016 x64 and `odbc`/`pdo_odbc`; Access 2000 and later files (Access 97 files: import them with MDBTools on Linux); tested on windows-2022 and windows-2025 ([MS Access](../features/6.0/ms-access-support.md)) |
| Firebird, DuckDB, the MySQL and PostgreSQL protocol databases (6.0.15) | their PHP extensions exist for Windows (`pdo_firebird`, `ffi` with `duckdb.dll`, `mysqli`, `pgsql`), but none of them has been tried on Windows |

Every engine, its versions and its installers: [Database support](database-support.md) and
[Choosing a database](choosing-a-database.md#windows).

## What the project's CI runs on Windows

- **Exponential** (`.github/workflows/phpunit.yml`, job "SQL Server LocalDB smoke"): on GitHub's `windows-latest`
  runner with PHP 8.4, `sqlsrv` and SQL Server LocalDB, the SQL Server driver's test suites run against a live
  LocalDB instance on every push.
- **Exponential Velocity** (the engine's `windows` workflow, on every push): Windows Server 2022 and 2025 with PHP
  8.1, 8.3 and 8.5 (x64), and Windows Server 2022 with PHP 8.3 (x86, 32-bit), run the portable pool's unit test
  (81 checks) and the Windows smoke test (15 checks: requests answered by persistent workers, worker reuse, a killed
  worker replaced, a PHP fatal error answered with 500, a graceful stop with the port free afterwards).
- **Exponential** (`.github/workflows/windows.yml`, on every push): Windows Server 2022 and 2025 with PHP 8.1 and 8.5
  run the DB-less PHPUnit suites, install a site on SQLite with `exp:install` and request its pages; the job "ODBC on
  Windows" runs the ODBC driver against LocalDB on both layers; the job "Upgrade 6.0.14 -> main on Windows" runs the
  upgrade tool xne's SQLite scenario (plan, dry run, upgrade, rollback). `phpunit.yml`'s job `msaccess-windows`
  tests MS Access on windows-2022 and windows-2025 with the Access Database Engine.
- The kernel's whole PHPUnit suite runs on Linux on PHP 8.1 to 8.5; the code it tests is the same PHP that runs on
  Windows.

## What differs from Linux

- **No `pcntl` and no `posix` on Windows.** Asynchronous publishing (`bin/php/ezasynchronouspublisher.php`) forks and
  is not available; publish synchronously, which is the default. Velocity uses its portable pool, in which every
  worker holds its own copy of the application (no copy-on-write), so keep the worker count modest.
- **Velocity features that need `pcntl`** (its watchdog, hot-reload re-exec, shared instances, scheduled tasks and
  WebSocket rooms) are off or run in-process on Windows. Start Velocity from a console or the Task Scheduler.
- **Paths.** The commands in this help are written with `/`; on Windows use `\` in the shell, and `php` is wherever
  you unpacked PHP (for example `C:\php\php.exe`).
- **WSL2** runs the Linux setup unchanged and is a good choice for development on a Windows desktop; keep the site in
  the Linux file system rather than under `/mnt/c`.
- **Static publishing on Windows** has its own notes: [Static publishing on Windows and in
  clusters](../guides/static-publishing-on-windows-and-clusters.md).

## Related pages

- [Operating system support](os-support.md)
- [Database support](database-support.md)
- [Moving off an old database](moving-off-an-old-database.md)
- [Velocity is part of Exponential 6.0.15](velocity-is-part-of-exponential-6-0-15.md)
- [IIS hosting](hosting/servers/iis.md)
- [Microsoft SQL Server driver](../features/6.0/sqlserver-database-driver.md)
