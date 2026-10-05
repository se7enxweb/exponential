# 6. The kickstarter

The kickstarter installs Exponential on the command line from one file, `kickstart.ini` in the installation root. It
runs the setup wizard's own step classes in a single PHP process, takes every answer from the file instead of a form,
holds the site in maintenance mode while it works, and records the run in `var/log/kickstart.log` and
`var/log/setup.log`. This chapter is the complete reference: the two subcommands `ini` and `run` with every option and
exit status, every section and key of `kickstart.ini` with its type, its default and its effect as the code applies
them, annotated example files for SQLite, MySQL and PostgreSQL, the database action and its dangers, dry runs,
resuming and re-running, and the use of the kickstarter in CI and containers.

[Previous: 5. The setup wizard](05-setup-wizard.md) | [Next: 7. The console install](07-console-install.md) |
[Contents](README.md)

---

## 6.1 How it works

```text
php bin/php/console exp:kickstarter run --force
        |
        |  bin/php/kickstarter.php  ->  \Exponential\Command\Kernel\Kickstarter
        |  (kernel/private/classes/commands/kickstarter.php)
        |
        |  1. rotates var/log/kickstart.log -> kickstart.log.1 ... .9
        |  2. starts itself again as a child process (same PHP binary, same memory_limit,
        |     date.timezone and max_execution_time), copies the child's output to the
        |     terminal and to var/log/kickstart.log, masking the passwords of kickstart.ini
        v
expKickstarter (kernel/classes/expkickstarter.php), in the child
        |  - boots a CLI script with the siteaccess "plain", HTTP_HOST "localhost" when unset
        |  - deletes the cached copies var/cache/ini/kickstart-*.php
        |  - switches maintenance mode on (not in a dry run)
        |  - runs the steps Welcome .. Final from kernel/setup/steps/ezstep_data.php,
        |    each one reading its section of kickstart.ini
        |  - switches maintenance mode off and prints a summary
        v
    a site, or "Setup failed on step: <Step>" and exit status 1
```

The command has two names: `php bin/php/console exp:kickstarter` and `php bin/php/kickstarter.php`. They take the same
subcommands and options. Many installations add a `console` shortcut in the root (`./console exp:kickstarter`).

## 6.2 The commands

```text
Usage: ./bin/php/console exp:kickstarter <command> [options]
       ./bin/php/kickstarter.php <command> [options]
```

| Command | Effect |
|---|---|
| `ini` | write or edit `kickstart.ini` |
| `run` | install from `kickstart.ini` |
| `help`, `--help`, `-h`, or no command | show the help |

Any other word prints "Unknown command: *word*" and the help, with exit status 1. (The help text calls `ini` the
default; in this version a call without a command shows the help.)

### 6.2.1 `ini`: write kickstart.ini

```bash
php bin/php/console exp:kickstarter ini              # interactive editor
php bin/php/console exp:kickstarter ini --yes        # built-in defaults, no questions
php bin/php/console exp:kickstarter ini --defaults   # the values of kickstart.ini-dist
```

| Option | Short | Effect |
|---|---|---|
| `--defaults` | `-d` | write `kickstart.ini` from the values in `kickstart.ini-dist` and exit |
| `--yes` | `-y` | apply the built-in defaults (table below) and write `kickstart.ini` without asking |
| `--help` | `-h` | show the help of `ini` |

How the file is built (`kernel/classes/expkickstarterini.php`):

1. `kickstart.ini-dist` is read. Every commented section (`#[name]`) and key (`#Key=value`) becomes a field; a
   `## ...` comment line above a key becomes its description.
2. When a `kickstart.ini` exists already, its values are merged over those, so `ini` edits the file you have.
3. With `--yes`, the built-in defaults are applied over both (only for the keys that have one), and every empty
   `Continue` becomes `true`.
4. The file is written with a three-line header, one section per wizard step, and the cached copies
   `var/cache/ini/kickstart-*.php` are deleted.

Without `--yes` or `--defaults` the command needs a terminal; otherwise it stops with "No TTY detected. Run with
--defaults or --yes for non-interactive mode." and exit status 1. It also stops when `kickstart.ini-dist` is missing
("kickstart.ini-dist not found in project root.").

**The `--yes` defaults:**

| Section | Key | Default |
|---|---|---|
| `database_choice` | `Type` | `sqlite3` |
| `database_init` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket` | `localhost`, empty, `ezp`, `root`, empty, empty |
| `language_options` | `Primary`, `Languages[]` | `eng-US`, none |
| `site_types` | `Site_package` | `sevenx_site` |
| `site_access` | `Access` | `url` |
| `site_details` | `Title` | `My Exponential Site` |
| `site_details` | `URL` | empty |
| `site_details` | `Access`, `AdminAccess`, `EditorAccess` | `sevenx_site_user`, `sevenx_site_admin`, `editor` |
| `site_details` | `AccessPort`, `AdminAccessPort`, `EditorAccessPort` | `8080`, `8081`, `8082` |
| `site_details` | `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname` | `sevenx-site.test.com`, `sevenx-site-admin.test.com`, `edit.sevenx-site.test.com` |
| `site_details` | `Database`, `DatabaseAction` | `ezp`, `skip` |
| `site_admin` | `FirstName`, `LastName`, `Email`, `Password` | `Admin`, `User`, `admin@example.com`, empty (a password is generated at install time) |
| `registration` | `Comments`, `Send` | empty, `false` |

> **Careful with `--yes` on a machine that already has an installation.** Before the defaults are applied, the
> generator looks through `settings/siteaccess/*/site.ini` and takes the `Server`, `Database`, `User` and `Password`
> of the first siteaccess that names a database and a user, for `[database_init]` and `[site_details] Database`. The
> resulting file points at that installation's database. Read the file before you run it, and check
> `DatabaseAction`.

**The interactive editor.** `ini` without options shows a menu of the sections with a summary of each:

| Key | In the main menu | In a section |
|---|---|---|
| a number | edit that section | edit that field |
| `n` / `p` | next / previous section | next / previous section |
| `m` | | back to the main menu |
| `w` | wizard: walk through every field of every section, then save | |
| `v` | preview the file (this writes it) | |
| `s` | save and stay | save and stay |
| `q` | save and quit | save and quit |
| `?` | help | help |

Fields are edited according to their type: `Continue` and `Send` as yes/no, `Type`, `Access` and `DatabaseAction` as a
numbered choice, ports as numbers, `Password` without echo, `Languages[]` as a comma-separated list (`+value`
appends). There is no "quit without saving": interrupt with Ctrl-C to leave the file as it was.

### 6.2.2 `run`: install from kickstart.ini

```bash
php bin/php/console exp:kickstarter run --list-steps    # show the steps, change nothing
php bin/php/console exp:kickstarter run --dry-run       # check, stop before CreateSites
php bin/php/console exp:kickstarter run --force         # install
```

| Option | Default | Effect |
|---|---|---|
| `--force` | off | required whenever the range of steps includes `CreateSites`. Without it `run` stops with "The CreateSites step will modify the database and site settings." and "Re-run with --force to confirm you want to install the site package.", exit status 1 |
| `--dry-run` | off | lists the sections found and the steps, then runs `DatabaseChoice` to `Registration` (it sets the start and stop step itself), imports the site package into a temporary repository, and stops before `CreateSites`. No `--force` needed. See [6.7](#67-dry-runs) |
| `--list-steps` | off | prints the step table and exits with status 0 |
| `--start-step=<Step>` | `welcome` | first step to run |
| `--stop-step=<Step>` | `final` | last step to run |
| `--help`, `-h` | | the help of `run` |

Step names are the class names of the step table and are matched without regard to case (`SiteDetails`,
`sitedetails`). An unknown name stops the run with "Unknown start step: ..." or "Unknown stop step: ...".

`--list-steps` prints:

```text
Setup steps:
  [ 0] Welcome                  (counted)
  [ 1] SystemCheck              (counted)
  [ 2] SystemFinetune           (counted)
  [ 3] EmailSettings            (counted)
  [ 4] DatabaseChoice           (counted)
  [ 5] DatabaseInit             (counted)
  [ 6] LanguageOptions          (counted)
  [ 7] SiteTypes                (counted)
  [ 8] PackageLanguageOptions   (counted)
  [ 9] SiteAccess               (counted)
  [10] SiteDetails              (counted)
  [11] SiteAdmin                (counted)
  [12] Security                 (counted)
  [13] Registration             (counted)
  [14] CreateSites              (hidden)
  [15] Final                    (counted)
```

The environment variables of `run`:

| Variable | Effect |
|---|---|
| `EXP_KICKSTART_LOG=0` | no `var/log/kickstart.log`, and no child process |
| `EXP_SETUP_LOG_DIR=<dir>` | write `setup.log` to another directory (used by tests so a real log is not rotated) |

### 6.2.3 Exit status

| Status | When |
|---|---|
| `0` | the installation finished; or `--list-steps`; or a dry run that completed; or `ini` wrote the file; or help |
| `1` | `kickstart.ini` not found ("kickstart.ini not found. Generate it first with: ./bin/php/console exp:kickstarter ini"); an unknown command, option or step name; `--force` missing; `kickstart.ini` has no sections (dry run); any step failed; `ini` could not run or write |

When the run log is on, the parent process exits with the child's status, so scripts see the same values.

### 6.2.4 What a run prints

A successful run prints the progress of each step, then:

```text
Maintenance mode off: the site answers again.

Setup summary
--------------------------------------------------
Site package: sevenx_multisite
Title:        My Exponential site
Database:     exponential
Access type:  url
Site URL:     https://www.example.com/site
Admin URL:    https://www.example.com/admin
--------------------------------------------------
Finished:     2026-10-05 10:15:42 CEST
Elapsed:      00:02:31 (151.3 seconds)
```

A failed step prints `Step <Step> failed:` followed by one line per problem, for example a failed system test with its
message, the database message of [chapter 5](05-setup-wizard.md#536-database-initialization), or the coded errors of
`CreateSites` ([section 5.3.15](05-setup-wizard.md#5315-creating-sites)), and then `Setup failed on step: <Step>`.
When a password had to be generated, the run prints it once (see `[site_admin]` below).

## 6.3 How each step behaves on the command line

On the command line there is nobody to show a page to, so a step that would show one fails, with these exceptions:

| Step | Section | Without a section, or with `Continue=false` |
|---|---|---|
| `Welcome` | none needed | sets the wizard language to `eng-GB` and continues |
| `SystemCheck` | none | runs the critical tests; a failed test fails the run and is listed with its message |
| `SystemFinetune` | none | continues whatever the optional tests say |
| `EmailSettings` | `[email_settings]` | **fails** |
| `DatabaseChoice` | `[database_choice]` | fails, unless exactly one database extension is loaded |
| `DatabaseInit` | `[database_init]` | **fails** |
| `LanguageOptions` | `[language_options]` | **fails** |
| `SiteTypes` | `[site_types]` | **fails** |
| `PackageLanguageOptions` | none | creates every package language you did not choose as itself (the wizard's default) and continues |
| `SiteAccess` | `[site_access]` | **fails** |
| `SiteDetails` | `[site_details]` | **fails** |
| `SiteAdmin` | `[site_admin]` | **fails** (skipped when `DatabaseAction=skip`) |
| `Security` | `[security]` | continues |
| `Registration` | `[registration]` | continues without sending anything |
| `CreateSites` | none | installs |
| `Final` | none | ends the run |

A step that fails because its section is missing or says `Continue=false` reports "Unknown failure": the message
does not name the section, so check the file first when you see it.

So for the command line: **give every section from `[email_settings]` to `[site_admin]`, each with
`Continue=true`.** The same file also drives the browser wizard ([chapter 5](05-setup-wizard.md#56-how-kickstartini-pre-fills-and-skips-pages)).

The system check runs as the user that runs the kickstarter. Run it as the user the web server or Velocity runs as,
or fix the ownership of `settings/`, `var/` and `design/` afterwards; the health checks at the end of
`var/log/setup.log` include file ownership.

## 6.4 kickstart.ini reference

### 6.4.1 Syntax

`kickstart.ini` is read by `eZINI` like any Exponential settings file:

- one `[section]` per step, named after the step's identifier;
- `Key=value`, one per line; `Key[]=value` lines build an array;
- **no whitespace before a section or a key**: an indented line is not read;
- `#` and `;` start comments;
- values are text; booleans are the words `true` and `false`.

Keep the file at mode `0600`: it holds passwords in clear text. Never commit it.

### 6.4.2 Sections and keys

Types: *bool* is `true`/`false`, *choice* one of the listed words, *int* a number, *text* free text, *secret* text
masked in the logs.

**`[welcome]`** (not in `kickstart.ini-dist`)

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | | with `true` the browser wizard skips its Welcome page. The command line skips it anyway |

**`[email_settings]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | `true`: use the values, do not ask |
| `Type` | choice: `mta`, `smtp` | `mta` | `smtp` writes `Transport=SMTP`; any other value means sendmail/MTA. On Windows `smtp` is forced |
| `Server` | text | | SMTP host (`TransportServer`), used with `smtp` |
| `User` | text | | SMTP user (`TransportUser`) |
| `Password` | secret | | SMTP password (`TransportPassword`) |

**`[database_choice]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Type` | choice | `setup.ini [DatabaseSettings] DefaultType` (`sqlite3`) | `sqlite3` (alias `sqlite`), `mysqli` (alias `mysql`), `pgsql` (alias `postgresql`), `mongodb`, `oci8` (aliases `oracle`, `ezoracle`; needs `extension/ezoracle`). An unknown value is ignored and the step falls back to asking |

**`[database_init]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Server` | text | empty | database host |
| `Port` | int | empty (the driver's default) | |
| `Database` | text | **required** | the database. SQLite: a file name in `var/storage/sqlite3/` matching `^[A-Za-z0-9][A-Za-z0-9_.-]{0,99}\.(db\|db3\|sqlite\|sqlite3)$`, or an absolute path without `/../` (allowed only from `kickstart.ini`). Oracle: the connect string, e.g. `127.0.0.1:1521/FREEPDB1`, or a TNS alias |
| `User` | text | empty | |
| `Password` | secret | empty | |
| `Socket` | text | empty | path of a local MySQL socket; empty writes `Socket=disabled` |

This step connects to the database to check it. The checks and messages are those of the wizard
([section 5.3.6](05-setup-wizard.md#536-database-initialization)).

**`[language_options]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Primary` | text (locale) | **required** | the primary language, e.g. `eng-US` |
| `Languages[]` | text list | none | additional languages. The primary may be listed here too; it is dropped from the list |

Every language must have a locale in `share/locale`. Otherwise the step stops the run. The terminal shows only
"Step LanguageOptions failed:" and "Unknown failure"; the reason is in `var/log/error.log` and `var/log/setup.log`
("kickstart.ini [language_options]: The primary language ... is not a language this installation has a locale for
(share/locale)."). The site is always installed as UTF-8.

**`[site_types]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Site_package` | text | **required** | the identifier of the site package, e.g. `sevenx_multisite` |

How the package is found: the remote index of `settings/package.ini [RepositorySettings] RemotePackagesIndexURL` is
read and its entry for the package is preferred. A package that is **already imported** under
`var/storage/packages/` is used as it is; one that is not is downloaded and imported, followed by its required
packages that are missing or older than their `min-version`. When the index cannot be read, a package already in the
local repository is used if all its requirements are imported (an offline install). To force a fresh copy of a
package, remove its imported directory first.

**`[site_access]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Access` | choice: `url`, `port`, `hostname` | | how requests are matched to the siteaccesses; written as `MatchOrder=uri`, `port` or `host` |

**`[site_details]`**

| Key | Type | Default when missing | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Title` | text | the site package's summary | `[SiteSettings] SiteName` |
| `URL` | text | `http://<host><path>`; on the command line `http://localhost` | the site's address; `SiteURL` is written without the scheme. Always set it |
| `OrganisationName` | text | empty (the site name is used) | sender of the optional e-mail (`mailpreferences.ini [FooterSettings]`) |
| `OrganisationAddress` | text | empty | the sender's postal address; `\n` separates lines |
| `Access` | text | the package identifier | with `Access=url`: the URL path of the siteaccess `site` |
| `AdminAccess` | text | *identifier*`_admin` | with `url`: the path of `admin` |
| `EditorAccess` | text | `editor` | with `url`: the path of `editor` |
| `AccessPort` | int | `8080` | with `port`: the port of `site` |
| `AdminAccessPort` | int | the next free of `8080`, `8081`, ... | with `port`: the port of `admin` |
| `EditorAccessPort` | int | the next free of `8080`, `8081`, `8082` | with `port`: the port of `editor` |
| `AccessHostname` | text | *identifier*`.`*host* | with `hostname`: the host of `site` |
| `AdminAccessHostname` | text | *identifier*`-admin.`*host* | with `hostname`: the host of `admin` |
| `EditorAccessHostname` | text | `edit.`*host*; on the command line `edit.localhost` | with `hostname`: the host of `editor`. Always set it for host matching |
| `Database` | text | **required** | the database the site uses (SQLite: the file). Give the same value as `[database_init] Database` |
| `DatabaseAction` | choice: `remove`, `ignore`, `skip` | `ignore` behaviour | what to do with existing data, [6.5](#65-databaseaction-read-this-before-you-run) |

Only the keys of the chosen access type are used. The port defaults count up from 8080 only for the ports you leave
out: with `AccessPort=9000` and nothing else, the admin gets 8080 and the editor 8081. Give all three.

The siteaccess **directories** are always `site`, `admin` and `editor`. `Access`, `AdminAccess` and `EditorAccess`
are the values that select them, not their names. Unlike the browser wizard, the command line does not refuse `admin`
as a path, so `AdminAccess=admin` gives the familiar `/admin`.

**`[site_admin]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `FirstName` | text | `Administrator` | |
| `LastName` | text | `User` | |
| `Email` | text | none | the administrator's e-mail and `[MailSettings] AdminEmail` |
| `Password` | secret | generated | the password of the user `admin` |

**An empty or well-known password is never installed.** When `Password` is empty or missing, or one of `publish`,
`admin`, `password`, `changeme`, `change-me`, `secret`, `exponential`, `demo`, `123456` (in any case), the step
generates a random 20-character password, prints it once:

```text
The administrator password was not set or is a well-known one; generated a random one.
  admin password: <the password>
  (also written to var/log/initial-admin-password - change the password after the first login and delete that file)
```

and writes `var/log/initial-admin-password` with mode `0600`. Log in, change the password, delete the file. A password
you give is not checked against `MinPasswordLength` here (`exp:install` does check it, [chapter 7](07-console-install.md)).

**`[security]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | the step has no values; `true` skips its advice page in the browser wizard |

**`[registration]`**

| Key | Type | Default | Effect |
|---|---|---|---|
| `Continue` | bool | `false` | |
| `Send` | bool | **`true` when the section exists** | `true` sends a registration e-mail about the installation (system, PHP and database details) to an external address through the configured mail transport. **Write `Send=false`** |
| `UserData[...]` | text | empty | the details sent with that e-mail |
| `Comments` | text | | listed in `kickstart.ini-dist`, not read by this version |

### 6.4.3 The template: kickstart.ini-dist

`kickstart.ini-dist` in the installation root documents every section with commented examples (`#[section]`,
`#Key=value`) and a `## ...` description above each key; it is what `ini` reads. `kickstart.ini--example` is a
complete, generated file for a host-matched installation, useful to compare with your own. Neither is read by `run`.

## 6.5 DatabaseAction: read this before you run

> **Warning: `DatabaseAction=remove` destroys data.** It empties the database named in `[site_details] Database`
> and installs into it. On MySQL, PostgreSQL and the other servers every kernel table is dropped first; on SQLite the
> whole file is emptied. **Everything in that database is lost, and nothing asks you again**: `--force` is the only
> confirmation. Point `Database` at a database that holds nothing you need, check it twice, and take a backup first.
> `exp:kickstarter ini --yes` may fill `Database` from an existing installation's settings
> ([6.2.1](#621-ini-write-kickstartini)).

| Value | Effect on the database | Effect on the settings | Use it for |
|---|---|---|---|
| `remove` | existing tables dropped (SQLite: file emptied), then schema, base data and packages installed | written | a clean install, a reinstall |
| `ignore` | schema, data and packages added to whatever is there | written | rarely: an empty database you cannot drop tables in |
| `skip` | nothing inserted; the administrator is not touched | written | regenerating `settings/siteaccess/` and `settings/override/` against a database that is already complete |

A value that is missing or misspelled behaves like `ignore`: the data is added to what is there, which on a database
that is not empty fails with duplicate tables or rows. Write the value explicitly.

Before a `remove`, back up:

```bash
# MySQL / MariaDB
mysqldump --single-transaction --routines exponential > var/backup-exponential-$(date +%Y%m%d-%H%M).sql
# PostgreSQL
pg_dump --format=custom --file=var/backup-exponential-$(date +%Y%m%d-%H%M).dump exponential
# SQLite (consistent copy while nothing writes)
sqlite3 var/storage/sqlite3/exponential.db ".backup 'var/backup-exponential.db'"
```

## 6.6 Annotated example files

Each example is a complete `kickstart.ini` for `run --force`. Lines starting with `;` are comments and can stay in
the file.

### 6.6.1 SQLite

```ini
; kickstart.ini - SQLite, URL matching (/site, /admin, /editor)

[email_settings]
Continue=true
Type=mta                         ; local sendmail/MTA

[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
Server=                          ; a file needs no server, port, user or password
Port=
Database=exponential.db          ; var/storage/sqlite3/exponential.db
User=
Password=
Socket=

[language_options]
Continue=true
Primary=eng-US                   ; the language of the bundled content
Languages[]=ger-DE               ; optional additional language

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=My Exponential site
URL=https://www.example.com
Access=site                      ; https://www.example.com/site   -> siteaccess site
AdminAccess=admin                ; https://www.example.com/admin  -> siteaccess admin
EditorAccess=editor              ; https://www.example.com/editor -> siteaccess editor
Database=exponential.db          ; same as [database_init] Database
DatabaseAction=remove            ; empties the file if it exists

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=webmaster@example.com
Password=                        ; empty: generated, printed once, var/log/initial-admin-password

[security]
Continue=true

[registration]
Continue=true
Send=false                       ; always
```

The directory `var/storage/sqlite3/` must be writable by the user that runs the kickstarter and by the web server; the
database keeps `-wal` and `-shm` files beside it. SQLite in production is covered in [chapter 9](09-databases.md).

### 6.6.2 MySQL or MariaDB

Create the database and its user first, with UTF-8:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY 'a-long-random-password';
GRANT ALL PRIVILEGES ON exponential.* TO 'exponential'@'localhost';
```

```ini
; kickstart.ini - MySQL/MariaDB, host name matching

[email_settings]
Continue=true
Type=smtp
Server=smtp.example.com
User=mailer@example.com
Password=CHANGE_ME_SMTP          ; masked in kickstart.log

[database_choice]
Continue=true
Type=mysqli                      ; "mysql" is accepted as well

[database_init]
Continue=true
Server=127.0.0.1
Port=3306
Database=exponential             ; must exist; no SHOW DATABASES privilege needed
User=exponential
Password=CHANGE_ME_DB
Socket=                          ; or /var/lib/mysql/mysql.sock instead of host and port

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=hostname

[site_details]
Continue=true
Title=Example
URL=https://www.example.com
OrganisationName=Example Ltd
OrganisationAddress=1 Example Street\n12345 Example City
AccessHostname=www.example.com
AdminAccessHostname=admin.example.com
EditorAccessHostname=edit.example.com   ; set it: the default would be edit.localhost
Database=exponential
DatabaseAction=remove            ; DROPS every kernel table in `exponential`

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=webmaster@example.com
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

When the server supports InnoDB, the tables are created as InnoDB. The connection charset written to the siteaccess
settings is `utf-8`.

### 6.6.3 PostgreSQL

The database must exist, and the `pgcrypto` extension must be available in it (it provides `digest()`):

```sql
CREATE ROLE exponential LOGIN PASSWORD 'a-long-random-password';
CREATE DATABASE exponential OWNER exponential ENCODING 'UTF8';
\c exponential
CREATE EXTENSION IF NOT EXISTS pgcrypto;
```

```ini
; kickstart.ini - PostgreSQL, port matching (8080 site, 8081 admin, 8082 editor)

[email_settings]
Continue=true
Type=mta

[database_choice]
Continue=true
Type=pgsql                       ; "postgresql" is accepted as well

[database_init]
Continue=true
Server=db.internal
Port=5432
Database=exponential             ; the login is tested against this database
User=exponential
Password=CHANGE_ME_DB
Socket=

[language_options]
Continue=true
Primary=eng-GB
Languages[]=eng-US               ; dropped if it is the primary; eng-US content is kept anyway

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=port

[site_details]
Continue=true
Title=Intranet
URL=http://intranet.example.com
AccessPort=8080
AdminAccessPort=8081
EditorAccessPort=8082            ; give all three ports
Database=exponential
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=it@example.com
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

When the installer cannot create `pgcrypto` itself, `DatabaseInit` fails with the message about the `digest` function
quoted in [section 5.3.6](05-setup-wizard.md#536-database-initialization). MongoDB and Oracle use the same sections
with `Type=mongodb` or `Type=oci8`; their specifics are in [chapter 9](09-databases.md).

## 6.7 Dry runs

```bash
php bin/php/console exp:kickstarter run --dry-run
```

A dry run:

1. prints "Dry-run: validating kickstart.ini", the sections found and the step table; a file without sections stops
   here with "No groups found in kickstart.ini" and status 1;
2. runs `DatabaseChoice` to `Registration` with the values of the file, so the database must be reachable;
3. in `SiteTypes` downloads the site package and its requirements into a temporary repository
   `var/storage/packages/dryrun/`, which is removed as soon as the step is done (and at the start of every run);
4. stops before `CreateSites` and prints "Dry-run completed: remote packages verified. Stopped before CreateSites."

It does not switch maintenance mode on, and it writes neither the database nor the settings. It is not entirely
without effects, though: `SiteAdmin` runs, so an empty or well-known `Password` produces a generated password and
`var/log/initial-admin-password`; `Registration` runs, so `Send=true` would send the registration e-mail; and the run
is logged to `var/log/setup.log` and `var/log/kickstart.log`.

## 6.8 Maintenance mode during a run

While a real run works, the site answers every web request with the maintenance page (HTTP 503, title "The site is
being set up"), so no visitor meets the half-built database ([chapter 4](04-choosing-an-install-method.md#44-what-an-installation-writes)).
The run prints "Maintenance mode on: the site shows the maintenance page until the installation is done." at the start
and "Maintenance mode off: the site answers again." at the end.

After a failed step the site **stays** in maintenance mode, and the run says: "The site stays in maintenance mode.
After fixing the cause: run the kickstarter again, or php bin/php/maintenance.php off". A run replaces any marker that
was there with its own and removes it at the end, so do not start an installation inside a maintenance window you
opened with `exp:maintenance on` and expect the window to survive it.

```bash
php bin/php/maintenance.php status       # or: php bin/php/console exp:maintenance status
php bin/php/maintenance.php off
```

## 6.9 Logs

| File | Content |
|---|---|
| `var/log/kickstart.log` | everything the run printed on stdout and stderr, with the passwords of `kickstart.ini` masked as `********`, a header with the date, the directory and the command, and the exit status at the end. Earlier runs: `kickstart.log.1` (the last) to `kickstart.log.9`. `EXP_KICKSTART_LOG=0` turns it off |
| `var/log/setup.log` | the readable record of the run: environment, every step with its duration, errors and warnings (with a hint beside known problems), the health checks after an installation and a RESULT/NEXT line. Earlier runs rotate to `setup.log.1` ... |
| `var/log/error.log` | `BEGIN` and `END` entries around the run; entries written during the run end with the run and step they came from |
| `var/log/initial-admin-password` | only when a password was generated |

```bash
tail -n 40 var/log/setup.log
grep -n "FAIL\|ERROR" var/log/setup.log
```

## 6.10 Re-running and resuming

**Run again for an identical install.** The same `kickstart.ini` with `DatabaseAction=remove` and `run --force`
replaces the database and the settings again. Use it to reset a test or training site.

**After a failure, run the whole sequence again.** Fix the cause (the failing step and its message are in the output
and in `var/log/setup.log`), then:

```bash
php bin/php/console exp:kickstarter run --dry-run     # optional: check up to Registration
php bin/php/console exp:kickstarter run --force
```

The steps hand their results to the following ones inside one process: `DatabaseChoice` sets the database type that
`DatabaseInit` and `SiteDetails` use, `SiteTypes` the package that `SiteAccess` and `SiteDetails` work on, and
`SystemCheck` what `CreateSites` learns about ImageMagick. A run started with `--start-step` at a later step does not
have those results, and steps such as `SiteAccess` or `SiteDetails` then fail or work with empty values. Use
`--start-step` for diagnosis of the early steps (for example `--start-step=DatabaseChoice --stop-step=DatabaseInit`
to test only the database connection); for an installation, start at `Welcome`, the default. With
`DatabaseAction=remove` a complete second run replaces whatever the failed one left behind.

**Stop early.** `--stop-step=<Step>` ends after that step. With a stop step before `CreateSites` (for example
`--stop-step=SiteDetails`) nothing is installed and `--force` is not needed: the run checks the configuration up to
that point and the summary says "stopped after SiteDetails (nothing installed)" in the setup log. Unlike a dry run,
such a run switches maintenance mode on for as long as it works.

**Regenerate the settings only.** `DatabaseAction=skip` and `run --force` rewrite `settings/siteaccess/` and
`settings/override/` from the file and leave the database alone.

**Edited the file and see old values?** `ini` and `run` delete `var/cache/ini/kickstart-*.php` themselves, because
`config.php` switches off the INI modification-time check. The browser wizard does not; delete those files after
editing `kickstart.ini` by hand when the wizard is to read it.

**Changing database engine.** The settings in `settings/override/` win over a siteaccess's. When you install onto a
different engine than the one the directory held before, set `[DatabaseSettings] DatabaseImplementation` in
`settings/override/site.ini.append.php` to the new driver first (or remove the old value), or the site keeps talking
the old engine's protocol.

**After the install.** Move `kickstart.ini` out of the installation root, or delete it: it holds passwords, and a
browser wizard started later would read it.

## 6.11 In CI and containers

The kickstarter is a good fit for automation when the configuration is best kept as a reviewed file:

1. Keep a template, e.g. `deploy/kickstart.ini.template`, under version control with placeholders for secrets.
2. Render it at deploy time into the installation root with mode `0600`.
3. Run a dry run, then the install, and fail the job on a non-zero status:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

Points to remember:

- `run` needs no terminal; `ini` needs one unless `--yes` or `--defaults` is given.
- The run takes a few minutes; give the job enough time. The child process keeps the parent's `memory_limit` and
  `max_execution_time`.
- `var/log/kickstart.log` and `var/log/setup.log` are the artefacts to keep from a failed job.
- In a container, run the install once (for example when `settings/override/site.ini.append.php` has no
  `[DatabaseSettings]`), not at every start.
- For installs without a file, `exp:install` ([chapter 7](07-console-install.md)) builds the same configuration from
  options.

## 6.12 Notes on the other kickstarter documents

The earlier documents [Kickstarter CLI](../bc/6.0/kickstartercli.md) and
[Kickstarter: install a whole site from one file](../features/6.0/kickstarter-cli.md) remain useful background. Where
they differ from this chapter, this chapter follows the code of this version:

- **Package download.** [Kickstarter CLI](../bc/6.0/kickstartercli.md) says the site package is always downloaded
  from the remote repository, replacing a local copy. In this version the kickstarter prefers the remote index entry
  but uses a package that is already imported, and downloads only what is missing ([6.4.2](#642-sections-and-keys),
  `[site_types]`). A dry run always downloads, into the temporary `dryrun` repository.
- **`--stop-step=CreateSites`.** It does not stop before the database is written: `CreateSites` is the step that writes
  it. Stop at `Registration` or earlier to leave the database untouched.
- **`[registration] Comments`.** Documented as the comment of the registration e-mail; this version does not read it.
  `Send` defaults to `true` when the section exists.
- **Resuming with `--start-step=SiteDetails`.** Both earlier documents (and [Installing Exponential 6.0](../INSTALL.md))
  suggest resuming a failed run there. The steps before it set values that `SiteDetails` and `CreateSites` read in the
  same process, so this chapter recommends running the whole sequence again ([6.10](#610-re-running-and-resuming)).

---

## References

In this repository:

- [Installing Exponential 6.0](../INSTALL.md), section 6
- [Kickstarter CLI](../bc/6.0/kickstartercli.md) and [Kickstarter: install a whole site from one file](../features/6.0/kickstarter-cli.md)
- [Installing Exponential in one command](../features/6.0/install-in-one-command.md)
- [Maintenance mode](../features/6.0/maintenance-mode.md)
- [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md)
- [Clean install defaults](../features/6.0/clean-install-defaults.md)
- [SQLite database](../features/6.0/sqlite-database.md), [SQLite installer](../specifications/6.0/platform-sqlite-installer.md),
  [database drivers, September 2026](../specifications/6.0/database-drivers-2026-09.md)
- [The console](../bc/6.0/console.md), and [CLI, cronjob and view abstractions](../bc/6.0/cli_cronjob_view_abstractions.md)
  (why `bin/php/kickstarter.php` is one call into `kernel/private/classes/commands/kickstarter.php`)
- [Deploying](../guides/deploying.md)
- Code: `kernel/private/classes/commands/kickstarter.php`, `kernel/classes/expkickstarter.php`,
  `kernel/classes/expkickstarterini.php`, `kernel/setup/steps/`, `kickstart.ini-dist`, `kickstart.ini--example`

External:

- PHP `proc_open()`: <https://www.php.net/manual/en/function.proc-open.php>
- PHP `posix_isatty()`: <https://www.php.net/manual/en/function.posix-isatty.php>
- MySQL `mysqldump`: <https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html>
- PostgreSQL `pg_dump`: <https://www.postgresql.org/docs/current/app-pgdump.html>
- PostgreSQL pgcrypto: <https://www.postgresql.org/docs/current/pgcrypto.html>
- SQLite online backup: <https://www.sqlite.org/backup.html>
- GNU `envsubst`: <https://www.gnu.org/software/gettext/manual/html_node/envsubst-Invocation.html>
- Exponential on GitHub: <https://github.com/se7enxweb/exponential>
