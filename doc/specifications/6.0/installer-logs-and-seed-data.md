# Installer logs and seed data

Reference for the data a new Exponential installation starts with and for the
files the installers write. Use it when you build your own installer, check an
installation, or script around the kickstarter. For the how-to see
[Installing in one command](../../features/6.0/install-in-one-command.md).

## Where the seed data lives

| Source | Used by |
|---|---|
| `share/db_data.dba` (and `share/db_schema.dba`) | The setup wizard and the kickstarter on every engine: MySQL, PostgreSQL, SQLite, Oracle, MongoDB (through `eZDbSchema`). |
| `kernel/sql/common/cleandata.sql` | SQL-dump based installs on MySQL and PostgreSQL. Quotes are standard SQL (`''` and `""`) since 30 September, so PostgreSQL loads it too. |
| `kernel/sql/sqlite/cleandata.sql` | SQLite SQL install. |
| `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` | Upgrade of an existing database. |

All of them carry the same rows. When you change seed data change every file,
then load each into a throwaway database and check the tree. (Review a `.dba`
change by comparing rows, not by its diff: rows are a numerically keyed array and
removing one renumbers everything after it.)

Changes of September 2026:

- SQLite loads the kernel's own schema and clean data again, and the SQL clean
  data matches the base data installations get. The unused SQLite demo data files
  were removed.
- SQL dumps made from the schema and data files escape quotes correctly for MySQL,
  SQLite and PostgreSQL. A PostgreSQL dump made on a site running another database
  no longer queries PostgreSQL catalogues.
- The ISBN group names carry their apostrophe again.
- Sixteen links of the former product's own website (forum, documentation, sales,
  training, videos, tutorials and two conference slides) were removed from the
  `ezurl` table (Setup > Link management) of the base data; no content used them.
  XML namespace addresses in stored rich text are identifiers, not links, and stay.
- The built-in users' e-mail address and the host name in the search index use
  the project's own `exponential.earth` domain.

## The content tree of a new installation

Node 1 is the virtual top node. Directly below it:

| Folder | Object | Node | Section | URL alias | Sorted |
|---|---|---|---|---|---|
| Websites (the content root, formerly named after the old product; title "Welcome to Exponential") | 1 | 2 | | (none, it is the front page) | by priority |
| Configuration (new) | 2 | 3 | Setup | `x-configuration` | by name, ascending |
| Archives (new) | 3 | 4 | Standard | `x-archives` | by published date, newest first |
| Users, Media, Setup, Design | existing | existing | | Setup is `x-setup` | |

(Sort values read from the node rows of `kernel/sql/common/cleandata.sql`:
Configuration `sort_field` 9, `sort_order` 1; Archives `sort_field` 2,
`sort_order` 0.)

Configuration and Archives are published, visible and always available in the
base data's language, with a name, version, node assignment, object state (not
locked), search index entry and URL alias of their own. Their ids are the lowest
free in every seed and in the running installation, so an installation made from
the seeds and an existing one carry the same ids. The node paths
(`path_identification_string`) use underscores, `x_setup`, `x_archives` and
`x_configuration`; the URL aliases use dashes:

| Folder | Alias | Why |
|---|---|---|
| Setup | `x-setup` (was `setup2`) | The kernel never lets a top-level address equal a module name, and `setup` is the setup module. |
| Archives | `x-archives` | Beside it at the top level. |
| Configuration | `x-configuration` | Same convention. |

Other seed changes:

- A content class group **Configuration** (group 5, after Setup) exists and is empty.
- Version 1 of the Users object was dated 1970; it carries the date the object was
  published (`1033917596`). The top node's `modified_subnode` moves to the time the
  two folders were published below it.
- The root folder is named "Websites" and titled "Welcome to Exponential" in both
  clean data files, matching `share/db_data.dba`.
- **Order statuses.** Fourteen lifecycle statuses are seeded in addition to
  Pending, Processing and Delivered; see
  [Store dashboard and order statuses](../../features/6.0/store-dashboard.md).

## Logs written by the installers

| File | Writer | Retention | Controls |
|---|---|---|---|
| `var/log/setup.log` | `expSetupLog`, one file per run of the kickstarter or the web wizard (the wizard's requests continue one run; a wizard started again closes the abandoned one) | Earlier runs rotate to `setup.log.1`, `.2` ... | `EXP_SETUP_LOG_DIR` |
| `var/log/kickstart.log` | The kickstarter (child process whose stdout and stderr pass through) | `kickstart.log.1` to `.9` | `EXP_KICKSTART_LOG=0` |
| `var/log/initial-admin-password` | Setup when no strong administrator password was given | Delete it after the first login | mode owner-only |
| configuration used by `exp:install` in `var/log` | `exp:install` | passwords masked | |
| `var/log/error.log` | `eZDebug` | normal | `BEGIN` / `END` entries with run id and result while a run goes on; entries carry `(setup <id>, step N "...")` |

`setup.log` structure: introduction, environment, one section per step with
duration and errors and warnings (read from `error.log`, `warning.log` and from
PHP; repeats grouped with count, first and last time; a hint beside known
problems), health checks (database, declared tables, content classes, objects,
the content root, name lists, settings, siteaccesses, file ownership) and a framed
`RESULT` and `NEXT` line. Passwords are masked. Entries from the run carry its
context line; others are listed as "(not this run)" and summed up in a `NOTE`.
The log records the site languages and the package language map the wizard
settled on. A post-install that stops reports the step, its number and the number
of steps (`eZSiteInstaller::abortedStep()`).

Request context in every log line (since 30 September): `eZLog::requestContext()`
gives the siteaccess and the full address (scheme, host, port, path) of the
request for every web request, also under Velocity, where workers run under the
command-line SAPI and were logged as the server's own command line. `isWebRequest()`
tells a web request from a shell script under any SAPI.

## Installer parameters a site package receives

`all_language_codes` (every site language, the primary first) stays as it was.
New: `primary_language`, `extra_language_codes` and `language_map` (what the
package language step answered), so a package no longer relies on the position of
the primary in a list. `eZStepInstaller::CLEAN_DATA_LANGUAGE` names the clean
data's language (`eng-US`) in one place.

## Database drivers and the installers

- **Oracle** (`ezoracle`): `eZSetupDatabaseMap()` has an `oci8` entry (driver
  `ezoracle`, 19c or later); `eZSetupActivateDatabaseExtension()` loads that
  extension's settings for the run. `DatabaseInit` takes the connect string as the
  named database, as for MySQL and PostgreSQL. Table aliases are written without
  `AS`, which Oracle does not accept.
- **PostgreSQL**: the installer creates the `pgcrypto` extension it needs
  instead of refusing; the schema handler creates the small integer columns and
  index names extension schemas use; the setup wizard installs into the database
  named on the database page (not the server's first database) and prefills port
  5432 and user `postgres`; its messages name `pgcrypto` and today's causes.
- **SQLite**: `removeDatabase()` is implemented (drops every non-SQLite object in
  the order trigger, view, index, table and vacuums), session `SET` statements are
  accepted and ignored, `BIT_OR` and `BIT_AND` are registered and the
  language-mask `UPDATE ... JOIN` is rewritten to `UPDATE ... FROM`. The driver
  sets its connection for a web site and waits for a lock instead of failing, and
  escapes `null` as an empty string as the MySQL driver does.
- **MySQL** under PHP 8.1 and later reports a refused login as a failed
  connection.
- **MongoDB**: see [MongoDB kernel support](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)
  and [database drivers](database-drivers-sqlite-oracle.md).

## Related pages

- [Maintenance mode](../../features/6.0/maintenance-mode.md)
- [Kickstarter on the command line](../../bc/6.0/kickstartercli.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Database drivers and installers, September 2026](database-drivers-2026-09.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
