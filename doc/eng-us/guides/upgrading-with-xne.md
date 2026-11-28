# Upgrading with xne (Exponential Network)

This guide teaches **xne**, the upgrade tool of Exponential (product name Exponential Network): one command that looks
at an installation, plans its upgrade, makes a verified backup, brings files, libraries and database to the new release,
checks the result, and rolls everything back by itself when any step fails. It is for administrators who upgrade
installations, one or many, on Linux, macOS or Windows.

xne does not replace the kernel's own tools; it drives them. The database and settings steps are still the kernel's
[`exp:upgrade`](upgrading-with-the-console.md) (or, on releases that do not have it, the SQL files of
`update/database/<engine>/`), the autoloads are still `ezpgenerateautoloads.php`, the caches still `ezcache.php`. What
xne adds is everything around them: the plan, the verified backup, the file changes, the health check and the way back.

The facts on this page are those of **xne 0.3.0.10**, released on 26 November 2026, the day xne became public.

[Guides](README.md) · Related: [Upgrading](upgrading.md), [Upgrading with the console](upgrading-with-the-console.md),
[11. Upgrading](../install/11-upgrading.md), [The upgrade check](upgrade-check.md),
[Exponential on Windows](../help/exponential-on-windows.md), [Choosing a database](../help/choosing-a-database.md)

## In short

```bash
xne detect   --root=/var/www/example.com     # what the installation is: version, type, PHP, database, owner
xne traps    --root=/var/www/example.com     # known upgrade traps, each with its fix
xne plan     --root=/var/www/example.com     # what would change; nothing is touched
xne upgrade  --root=/var/www/example.com --dry-run
xne upgrade  --root=/var/www/example.com     # verified backup, upgrade, health check, automatic rollback on failure
xne backups  --root=/var/www/example.com     # the backups, newest first
xne rollback --root=/var/www/example.com     # files and database back to the newest backup
```

Every command answers `PASS`, `FAIL` or `REFUSED` with a fixed exit code (0 PASS, 1 FAIL, 2 usage error, 3 refused)
and prints one JSON object with `--json`.

## Contents

1. [When to use xne, and when exp:upgrade alone](#when-to-use-xne-and-when-expupgrade-alone)
2. [Requirements](#requirements)
3. [Installing xne](#installing-xne)
4. [A first upgrade, step by step](#a-first-upgrade-step-by-step)
5. [What happens during an upgrade](#what-happens-during-an-upgrade)
6. [Channels and targets](#channels-and-targets)
7. [Databases](#databases)
8. [Traps](#traps)
9. [Rolling back](#rolling-back)
10. [The backup folder](#the-backup-folder)
11. [Several installations](#several-installations)
12. [Windows](#windows)
13. [Checks and notifications](#checks-and-notifications)
14. [Commands and options](#commands-and-options)
15. [What xne never does](#what-xne-never-does)
16. [Limits](#limits)

## When to use xne, and when exp:upgrade alone

| You want to | Use |
|---|---|
| Bring a 6.0.x installation to a newer release: code, libraries, database and settings, with a way back | `xne upgrade` |
| Apply the database and settings steps after you put the new code in place yourself (a deployment pipeline, a git pull) | `./console exp:upgrade` ([guide](upgrading-with-the-console.md)) |
| See what an upgrade would do, without changing anything | `xne plan`, or `./console exp:upgrade:status` for the database steps only |
| Undo an upgrade, files and database | `xne rollback` |

xne calls `exp:upgrade` itself when the installation has it, so the two never conflict: `exp:upgrade` records what it
applied, and xne reads that record (`exp:upgrade:status`) in its health check.

## Requirements

- **PHP 7.4 to 8.5** on the command line, with `zlib` (archives and backups) and `json`; `curl` or `openssl` for
  HTTPS downloads.
- **The PDO driver of the installation's database** in the PHP that runs xne: `pdo_mysql`, `pdo_pgsql` or
  `pdo_sqlite`; for MongoDB the `mongodb` extension, for Oracle `oci8`. When the PHP on `PATH` lacks it, name one that
  has it with `--php=/path/to/php` or the environment variable `XNE_PHP`.
- **git**, only for installations that are git checkouts (the git method).
- **Write access** to the installation, as its owner, or as root or Administrator: xne then runs the installation's
  own tools as the owner of `index.php`.

## Installing xne

xne is a folder of PHP files under GPL-2.0-or-later; nothing is compiled. Releases are tagged `vX.Y.Z.N` on
[github.com/se7enxweb/xne](https://github.com/se7enxweb/xne) and published as the Composer package `se7enxweb/xne`.
Take the newest tag, sorted by version and never by name (`v0.3.0.10` comes after `v0.3.0.9`).

**Linux and BSD:**

```bash
sudo git clone https://github.com/se7enxweb/xne.git /opt/xne
cd /opt/xne && sudo git checkout "$(git tag -l 'v*' --sort=version:refname | tail -1)"
sudo ./install.sh            # shows what it would do
sudo ./install.sh --apply    # links /usr/local/bin/xne, creates /etc/xne, /var/lib/xne (0700), /var/cache/xne
xne version
```

`install.sh` never removes anything; run it again after every update (`git fetch --tags`, check out the new tag,
`sudo ./install.sh --apply`). As root xne keeps its state in `/var/lib/xne` and its release cache in `/var/cache/xne`;
as another user in `~/.xne/lib` and `~/.xne/cache`.

**macOS:** clone it to `~/xne`, link `~/xne/bin/xne` into `/usr/local/bin` (or put `~/xne/bin` on `PATH`), and name the
PHP that runs the site with `--php=` or `XNE_PHP` (Homebrew's `php`, or MAMP's). State and cache: `~/.xne/`.

**Windows:** see [Windows](#windows) below.

## A first upgrade, step by step

Point `--root` at the installation: the folder with `lib/version.php`, `kernel/` and `settings/` (or run xne from
inside it). The first four steps change nothing.

**1. What is the installation?**

```bash
xne detect --root=/var/www/example.com
```

Check the version, the installation type (`git` installations are fast-forwarded, `composer` and plain `files`
installations are file-patched), the database line (it must be reachable) and the kernel tools found.

**2. The traps.**

```bash
xne traps --root=/var/www/example.com
```

A `block` line refuses the upgrade until it is fixed, or until the person responsible accepts that trap by its id
(`--accept=<id>`). A `warn` line asks for an action after the upgrade. See [Traps](#traps).

**3. The plan.**

```bash
xne plan --root=/var/www/example.com                    # the newest stable release of the same major
xne plan --root=/var/www/example.com --to=v6.0.15       # an exact release
```

Read what it lists:

- **files**: how many are added, updated and retired.
- **local edits**: files changed on this server. Each is copied into the backup's `kept/` folder and then replaced with
  the release's version; plan the merge of each (a template change, a hot fix) before or right after the upgrade.
- **protected**: files the release has that xne never writes: `settings/override`, `settings/siteaccess`, `var`,
  `config.php`, `.htaccess` and the like. Compare them by hand when a release note asks for it.
- **Composer's files**: in an installation whose libraries Composer manages (`vendor/composer/installed.json`), the
  files in `vendor/`, in every package's install path and in the extension folders `composer.lock` names are never
  written or retired by the file patch.
- **composer.json**: it follows the release's file, merged: the release's requirements, plus the site's own extension
  packages the release no longer requires, at `>=` their locked version, so a release that drops an extension never
  uninstalls one the site uses.
- **database**: `kernel` means the kernel's `exp:upgrade` runs (its stages are listed); `sql` lists the
  `update/database/<engine>/` files that run, in order. When `composer.json` changes, the `libraries` stage runs first.
- **PHP extensions**: since 0.3.0.10 the plan checks the PHP extensions the target release requires (`ext-*` of its
  `composer.json`, its `composer.lock` platform and every package the lock installs) against the PHP its tools and
  Composer run with, and blocks with the trap `php-extension-missing` before anything changes, naming each extension,
  who requires it, the `php.ini` that is loaded and the line to add (for example `extension=sockets`: Exponential's
  main branch requires `ext-sockets`).
- **blocked**: anything here refuses the upgrade.

**4. The dry run.**

```bash
xne upgrade --root=/var/www/example.com --dry-run
```

**5. The upgrade.**

```bash
xne upgrade --root=/var/www/example.com
```

It prints each step as it goes (`.. backup: archiving`, `.. files: writing`, `.. kernel: exp:upgrade`, ...) and ends
with one of:

- `PASS: upgraded 6.0.14 -> 6.0.15 (backup 20261124-031502-a1b2)`: done. Note the backup id.
- `FAIL: <reason>; rolled back to backup <id>`: nothing of the upgrade is left, the installation runs as before. Fix
  the cause and plan again.
- `REFUSED: ...`: a safety check stopped it before anything changed.

**6. After the upgrade.**

1. Open the site and the administration, sign in, look at a few pages.
2. Merge the local edits listed as kept aside (copies in `<backup>/kept/`).
3. Act on every `warn` trap (for example flushing a Redis or Valkey view cache).
4. Reload the PHP processes that keep the old classes: PHP-FPM, LiteSpeed, or Velocity with
   `./console exp:velocity deploy` (an installation that sets `finish=velocity` has xne run it).
5. **Setup > Upgrade check** in the administration should show no modified kernel file: the release's
   `share/filelist.md5` arrived with it.

**Visitors** keep being served during the upgrade by default. With `maintenance=page` (a server setting, or
`Maintenance=page` for one installation) they get the kernel's [maintenance page](../features/6.0/maintenance-mode.md)
(503 with `Retry-After`) while administrators can still sign in; that needs `bin/php/maintenance.php`, which
Exponential has from 6.0.15 on, in the installation or in the target.

## What happens during an upgrade

```
 detect -> plan -> lock -> backup (verified) -> maintenance on? (optional)
                                                       |
                         files: file patch | git fast-forward
                                                       |
            kernel exp:upgrade: [libraries,] database, scripts   (else the SQL update files)
                                                       |
                         finish: autoloads + cache clears -> health check
                                                       |
             any failure -> automatic rollback: files, libraries, database, caches (exit 1, rolled_back: true)
             PASS        -> maintenance off -> backup marked applied -> old backups rotated
```

- **Two methods, chosen per installation.** A verified **file patch** (the difference against the release's
  `share/filelist.md5`, written file by file and checked against the release) or a **git fast-forward** for git
  checkouts. A git checkout that cannot fast-forward is refused, never forced. `--method=filepatch` or `--method=git`
  overrides the choice.
- **The kernel's libraries stage.** When the kernel reports its `libraries` stage pending (`exp:upgrade:status
  --stages=libraries`), xne runs `libraries, database, scripts` in that order, with `vendor/`, `composer.json`,
  `composer.lock` and the Composer extension folders in the verified backup.
- **The health check** after every upgrade: the written files equal the release, the release's own
  `share/filelist.md5` matches the files, the installation starts from the command line, `exp:upgrade:status` shows
  nothing failed or pending, and every configured `health_url` answers 200 to 399.
- **One run per installation.** A lock refuses a second run at the same time (exit 3).

## Channels and targets

| Channel | Target |
|---|---|
| `stable` (the default) | the newest stable release of the same major version |
| `security` | only the newest release of the same x.y line |
| `beta` | the newest release, pre-releases included |
| `main` | the main branch (nightly); the plan warns, and a backup is made as always |

`--to=v6.0.15` names an exact release instead. Releases come from ranked sources (GitLab, GitHub, Packagist and a local
source, in the order the setting `sources=` gives) and are downloaded once into the release cache. `xne sources` shows
the order and the target of each channel.

## Databases

xne backs up, upgrades and rolls back the database through the installation's own `[DatabaseSettings]`; the password is
never shown. Every backup is read back and verified before anything changes, and a rollback is exact: the tests compare
a fingerprint of every table or collection before the backup and after the restore.

| Engine | `DatabaseImplementation` | PHP extension xne needs | Backup | Rollback |
|---|---|---|---|---|
| MariaDB / MySQL | `ezmysqli`, `ezmysql` | `pdo_mysql` | `db.sql.gz`, xne's framed SQL dump with row counts | every table dropped and recreated from the dump |
| PostgreSQL | `ezpostgresql` | `pdo_pgsql` | `db.sql.gz`, DDL from the catalog, sequences at their positions | in one transaction |
| SQLite | `sqlite3`, `ezsqlite3` | `pdo_sqlite` | `db.sqlite`, a consistent copy, integrity-checked | through SQLite's backup API |
| MongoDB | `expmongodb` | `mongodb` | `db.mongo.jsonl.gz`, indexes and documents as canonical extended JSON | collections dropped, indexes and documents recreated |
| Oracle | `ezoracle` | `oci8` | `db.oracle.jsonl.gz`, DDL from `DBMS_METADATA`, rows with CLOB and BLOB whole | tables purged, recreated, rows bound back, then indexes and triggers |

**What is proven.** MariaDB / MySQL, PostgreSQL and SQLite: upgrades from 6.0.13 to 6.0.14 and from 6.0.14 to 6.0.15,
by file patch and by git, with exact rollbacks. MongoDB and Oracle: a full upgrade of a kickstarted installation and an
exact `xne rollback`, one proof run each.

**Sites over ODBC** (`DatabaseImplementation=odbc`, 6.0.15): xne reads `[ODBCSettings] Dialect` and treats the
installation as that engine. `postgresql` and `mysql` are backed up and rolled back through the engine's own PDO driver
against the same server; `sqlserver`, `db2` and `msaccess` over ODBC are refused.

**Not yet:** the databases added in 6.0.15 on their own drivers (SQL Server, Firebird, DuckDB, Db2, Informix, SAP HANA,
libSQL, MonetDB, the MySQL and PostgreSQL protocol families) have no verified xne backup yet, so xne refuses to upgrade
them; upgrade those with `exp:upgrade` and a backup of your own. An installation whose siteaccesses use more than one
database is refused (`several-databases`). See [Choosing a database](../help/choosing-a-database.md) for the upgrade
column per database.

## Traps

`xne traps` reports the known traps of an installation as `info`, `warn` or `block`, each with its fix. Among them:

- a stale copy of `lib/` above the document root;
- a Composer `files` autoload entry outside the installation;
- extension folders shared between installations;
- PHP handlers set by `.htaccess` on LiteSpeed;
- kernel class overrides that a new release could leave behind;
- an unknown `DatabaseImplementation`;
- a PHP extension the target release requires that the PHP running its tools lacks (`php-extension-missing`).

A `block` trap is never worked around unless the person responsible accepts that trap by id: `--accept=<id>`.

## Rolling back

There are four ways back; all use the backup xne made before the upgrade, and none deletes a file.

1. **Automatically.** Any failure after the backup (files, `exp:upgrade`, an SQL file, the cache clears, the health
   check) rolls back at once, and the command exits 1 with `rolled_back: true` and the reason. If the rollback itself
   fails (`rollback_error`, state `rollback-failed`), the backup is still there and verified: fix the cause (a full disk,
   a database that went away) and run `xne rollback <id>`.
2. **`xne rollback`.**

   ```bash
   xne backups  --root=...                      # the backups, newest first
   xne rollback --root=... --dry-run            # what the newest backup would restore
   xne rollback --root=... [BACKUP-ID]          # files and database
   xne rollback --root=... [BACKUP-ID] --no-db  # files only
   ```

   The backup is verified; the files the upgrade added move to `aside-rollback/`; the archived files are written back,
   each checked against its md5; files added to `vendor/` and the Composer extension folders move aside; a git checkout
   is reset to its commit (`git reset --keep`, refused over local changes); the database is restored and its row counts
   compared; then the autoloads and the caches.

   A database rollback brings back the data **of the moment of the backup**: content created after the upgrade is
   lost. Use `--no-db` when the database step was fine and only files need to go back, and tell the site's owner.
3. **A git checkout, by tag:** `xne rollback --root=... --git-tag=v6.0.14` moves the code to the tag; the database is
   not touched.
4. **By hand**, only when xne cannot run at all: `files.tar.gz` is a normal archive to extract over the root;
   `db.sqlite` is copied over the database file while the site is stopped; `db.sql.gz` is SQL (each statement preceded
   by a `-- xne-stmt <bytes>` comment) to feed to `mysql` or `psql` after dropping the tables; `backup.json` lists the
   files the upgrade added. Then clear the caches.

## The backup folder

`<state>/installs/<install>/backups/<id>/` (mode 0700; `<state>` is `/var/lib/xne` as root). The newest `backup_keep`
backups (5 by default) are kept.

| File | Holds |
|---|---|
| `.xne-backup` | the marker; rotation removes only folders that carry it |
| `backup.json` | versions, target, method, git commit, the file list with the md5 of each file, the database meta (tables, row counts, checksum), the state and the rollback result |
| `files.tar.gz` | every file the upgrade changes or retires, `settings/override` and `settings/siteaccess` whole, and `vendor/`, `composer.json`, `composer.lock` and the Composer extension folders when Composer runs |
| `listing.json` | the file listing of those folders |
| `db.sql.gz`, `db.sqlite`, `db.mongo.jsonl.gz`, `db.oracle.jsonl.gz` | the database, per engine |
| `kept/` | a plain copy of every local edit the upgrade replaced |
| `aside/`, `aside-rollback/` | the files the upgrade retired; the files a rollback took back |

`xne verify-backup [id]` reads a backup back (the archive's checksum, every file's md5, the dump and its row counts);
`xne backup` makes a verified backup without upgrading.

## Several installations

Plan all of them first (`xne plan --json` for each), then upgrade one at a time. Each installation has its own lock and
its own backups; per-installation settings (`Maintenance=page`, `finish=velocity`, `health_url`) go in xne's
configuration. Installations that share extension folders are reported as a trap.

## Windows

1. Install PHP 7.4 or later for the command line, the same PHP the site uses (XAMPP's `C:\xampp\php\php.exe`,
   WampServer's, or a zip from windows.php.net), with `extension=openssl`, `extension=curl` and the database's `pdo_*`
   enabled in its `php.ini`.
2. Get xne: `git clone https://github.com/se7enxweb/xne.git C:\xne`, or unpack the repository archive there.
3. Put `C:\xne\bin` on `PATH`, or set `XNE_PHP` to the full path of `php.exe`:

   ```bat
   setx XNE_PHP "C:\xampp\php\php.exe"
   xne version
   xne detect --root="C:\xampp\htdocs\exponential"
   ```

In PowerShell the same commands run through `xne.ps1` (when scripts are blocked:
`powershell -ExecutionPolicy Bypass -File C:\xne\bin\xne.ps1 version`). State and cache: `%ProgramData%\xne\lib` and
`%ProgramData%\xne\cache`.

**Proven on Windows:** upgrades from 6.0.13 to 6.0.14 and from 6.0.14 to 6.0.15 run in CI on Windows Server 2022 and
2025, each with an exact rollback, in xne's own CI and in Exponential's Windows CI
([run 45867228580](https://github.com/se7enxweb/exponential/actions/runs/45867228580)). The Windows launchers are
simple stubs; an installer (MSI), the IIS, XAMPP and WampServer specifics and a scheduled task are planned. See also
[Exponential on Windows](../help/exponential-on-windows.md).

## Checks and notifications

- `xne check` asks `https://network.se7enx.com/api/v1` what each channel offers and which advisories affect the
  installation (an anonymous check sends only the version), and runs the notify hooks.
- **Notify hooks** (since 0.3.0.8): `notify_problem_cmd`, `notify_ok_cmd` and `notify_event_cmd` are command lines with
  placeholders and `XNE_NOTIFY_*` environment variables, started without a shell, so any alerting, chat or ticket tool
  can open and resolve a problem or receive an event.
- **Install keys** (`xne key:set`, read from standard input, stored 0600; `xne key:status`) sign the installation's
  report (`xne report`), trimmed to the key's level: never content, users or settings values.
- A daily check ships as a systemd timer (`xne-check.timer`), not installed by default.

The public API: `GET /status`, `GET /releases`, `GET /advisories?version=6.0.14`, `POST /check`, and the signed
`POST /report` (HMAC-SHA256 with a timestamp; a signature more than 300 seconds off is refused).

## Commands and options

Global options: `--root=DIR`, `--json`, `--dry-run`, `--help`, `--php=PATH`, `--set key=value` (any `xne.conf` key).

| Command | Does |
|---|---|
| `xne detect` | version, installation type (`git`, `composer`, `files`), PHP, owner, databases, extensions, siteaccesses, kernel tools |
| `xne traps` | the known traps, each with its level and fix |
| `xne plan [--channel=stable\|beta\|main\|security] [--to=REF] [--method=auto\|filepatch\|git]` | target, file counts, local edits, database steps, traps, warnings, `blocked`; changes nothing |
| `xne upgrade [plan options] [--dry-run] [--accept=<id>]` | plan, lock, verified backup, files, libraries, database and scripts, finish, health check; automatic rollback on failure |
| `xne rollback [BACKUP-ID] [--no-db] [--git-tag=TAG] [--dry-run]` | files and database back to a backup |
| `xne backup`, `xne backups`, `xne verify-backup [BACKUP-ID]` | a verified backup now; the list; a backup read back |
| `xne sources [--refresh]` | the release sources in rank order and the target of each channel |
| `xne config` | every effective setting and where it came from (secrets never shown) |
| `xne db-fingerprint` | rows and an md5 per table, plus one fingerprint of all of them |
| `xne check [--no-notify]`, `xne report [--dry-run]` | channels and advisories; the signed report |
| `xne key:set`, `xne key:status` | the install key |
| `xne version`, `xne help [command]` | the version; the manual of one command |

## What xne never does

- It never changes an installation without a verified backup; if the backup fails, nothing is changed.
- It never leaves a half-upgraded installation: any failure after the backup rolls files and database back.
- It never deletes a file: retired files and files a rollback takes back are moved into the backup folder.
- It never overwrites a local edit silently: each one is kept in the backup and listed.
- It never writes `settings/override`, `settings/siteaccess`, `var`, `config.php`, `.htaccess` and the like.
- It never runs twice on one installation at the same time, and never forces git.
- It never works around a blocking trap unless it is accepted by id.
- It never prints a database password, token or key secret.
- It never tags or publishes an Exponential release; it installs them.

## Limits

- **6.x only, for now.** xne upgrades within 6.x. It detects 4.x and 5.x installations and chains their SQL update
  files in a plan, but those chains are not yet completed and tested: plan only, and follow
  [11.4 From 3.10 to 5.3](../install/11-upgrading.md#114-from-310-to-53-the-old-chain) and
  [11.5 From 5.4 to 6.0.0](../install/11-upgrading.md#115-from-54-or-590-to-600).
- **Databases:** see [Databases](#databases); the drivers added in 6.0.15 are not upgraded by xne yet.
- **Planned, no date:** Setup > Network in the administration and `exp:network:*` commands inside an installation (the
  extension `exp_network` in xne's repository), a Composer method, the 4.x and 5.x chains, nightly and security
  channels applied automatically, and the Windows installer.
