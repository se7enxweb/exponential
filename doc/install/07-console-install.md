# 7. The console install: `exp:install`

`exp:install` installs Exponential with one command and no file to write first. It turns its options into the same
configuration a `kickstart.ini` would hold, runs the kickstarter's installation steps in the same process, and prints a
summary with the site address, the administration login and the administrator's password. Every option has a default,
so `php bin/php/console exp:install` on its own installs the `sevenx_multisite` site package on SQLite.

This chapter documents every option with its default and its checks, the dry-run and print modes, the exit status,
complete examples for each database system, how the administrator password is chosen, what the command writes and
where, what can go wrong, and the other console commands that matter when setting up an installation.

Use `exp:install` when you want an installation from the command line and do not need to keep a configuration file:
a laptop, a test system, a container, a CI job. When you want the configuration as a file you can review, keep in a
safe place and run again, use the kickstarter ([chapter 6](06-kickstarter.md)); `exp:install` runs the same steps, so
the result is the same.

[Contents](README.md) · Previous: [6. The kickstarter](06-kickstarter.md) · Next: [8. Serving the site](08-serving-the-site.md)

---

## 7.1 The console in brief

`bin/php/console` is Exponential's command console. It does not keep a list of commands; it finds them at run time
from the scripts that are present:

| Script | Command name |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` |
| `bin/shell/<name>.sh` | `shell:<name>` |
| `bin/<name>.sh`, `bin/<name>.php` | `bin:<name>` |
| `extension/<ext>/bin/php/<name>.php` | `ext:<ext>:<name>` |
| `extension/<ext>/bin/shell/<name>.sh` | `ext:<ext>:sh:<name>` |

A script can declare a short alias in its header (`@alias vc` makes `exp:vc` run `bin/php/velocity.php`). A name
without a namespace is tried as `exp:`, then `shell:`, then `bin:`, so `php bin/php/console install` also runs
`exp:install`. An unknown name prints "Command ... was not found", suggests the nearest real one, and exits with 1.

```bash
php bin/php/console list               # every command
php bin/php/console list exp           # the exp: namespace only
php bin/php/console help exp:install   # describes the command, then runs it with --help
php bin/php/console --version          # Exponential's version, build, copyright and license
```

Before it hands over, the console prints one line on standard error naming the command and the script it runs:

```text
  ▶  running exp:install  →  bin/php/install.php
```

That line goes to standard error, so it never ends up in output you pipe into a file or another program.

So `exp:install` is `bin/php/install.php`, whose class is in `kernel/private/classes/commands/install.php`. Both forms
take the same options and behave the same; the console only adds the line above:

```bash
php bin/php/console exp:install --dry-run
php bin/php/install.php --dry-run
```

Many installations add a shortcut in the root, `ln -s bin/php/console console`, after which `./console exp:install`
is the same command. The shortcut is not part of the distributed files; create it yourself if you want it. Run the
console from the installation root, the directory that holds `index.php`. More:
[The console](../bc/6.0/console.md).

## 7.2 What `exp:install` does

The command works in this order. Knowing it explains every message it can print and why a failed run leaves the
directory as it found it.

1. **Parse and check the options.** Every option has the form `--name=value`; flags have no value. Anything that is
   not of the form `--name` or `--name=value` (a bare word, a short option such as `-f`) stops the command with
   "Unknown argument: ... (see --help)". An unknown option name, an option without its value, or a flag given a value
   stops it with "Unknown option or missing value: ... (see --help)". Then every value is checked
   ([7.3](#73-options)). Nothing has been written at this point.
2. **Choose the administrator password** ([7.5](#75-the-administrator-password)).
3. **Build the configuration**: the eleven sections of `kickstart.ini`, each with `Continue=true`, so that every
   step runs without asking ([7.4](#74-what-the-configuration-looks-like)).
4. With `--print`: show it, passwords masked, and stop. This happens before the next check, so `--print` also works
   in a directory that already holds an installation.
5. **Refuse to overwrite**: when `settings/override/site.ini.append.php` contains a `[DatabaseSettings]` section, the
   directory already holds an installation; without `--force` (or `--dry-run`) the command stops here.
6. **Put the configuration in place**: an existing `kickstart.ini` is moved to
   `var/log/kickstart.ini.before-exp-install-<YYYYmmdd-HHMMSS>`, and the generated one is written as `kickstart.ini`
   with mode `0600`, because it holds the passwords in clear text. A generated administrator password is recorded in
   `var/log/initial-admin-password` at this point (not in a dry run).
7. **Run the kickstarter** (`expKickstarter`) with `--force`, or with `--dry-run`. It switches maintenance mode on,
   runs every step from `Welcome` to `Final`, and switches its own maintenance off again
   ([chapter 6](06-kickstarter.md)). A maintenance window that was already open before the run is kept as it is and
   left open afterwards.
8. **Clean up, whatever happened**: the configuration is saved as `var/log/exp-install-<YYYYmmdd-HHMMSS>.ini` with the
   passwords masked, the generated `kickstart.ini` is deleted and the earlier one is put back. This runs when the
   process ends, so it also happens when a step fails.
9. **Print the summary**, but only when the run rewrote `settings/override/site.ini.append.php` with a
   `[DatabaseSettings]` section. That file is the last thing an installation writes, so its presence with a fresh
   modification time is the proof that the installation went through.

## 7.3 Options

Every value is checked before anything is written. The quoted texts are the exact messages; each one ends the command
with exit status 1.

### Database

| Option | Default | Meaning and checks |
|---|---|---|
| `--db=<type>` | `sqlite` | `sqlite`, `mysql`, `pgsql`, `mongodb` or `oracle`. Also accepted: `sqlite3`; `mysqli`, `mariadb`; `postgresql`, `postgres`; `mongo`; `oci8`, `ezoracle`. Anything else: "Unknown database type '...': use sqlite, mysql, pgsql, mongodb or oracle" |
| `--db-host=<host>` | `localhost` | the database server |
| `--db-port=<port>` | `3306` (mysql), `5432` (pgsql), `27017` (mongodb), `1521` (oracle), none (sqlite) | |
| `--db-name=<name>` | `exponential`; `exponential.db` for sqlite; `FREEPDB1` for oracle | the database. SQLite: a file name in `var/storage/sqlite3/`. Oracle: a service name becomes the Easy Connect string `<db-host>:<db-port>/<service>`; a value with `/` or `(` is used as it is; `@alias` names a TNS alias |
| `--db-user=<user>` | `root` (mysql), `postgres` (pgsql), none otherwise | |
| `--db-password=<pass>` | none | when not given, the environment variable `EXP_INSTALL_DB_PASSWORD` is used |
| `--db-socket=<path>` | none | a local MySQL socket instead of host and port |
| `--db-action=<action>` | `remove` | `remove`, `ignore` or `skip`, as `DatabaseAction` in [chapter 6](06-kickstarter.md#65-databaseaction-read-this-before-you-run). Anything else: "--db-action must be remove, ignore or skip". **`remove` empties the database first** |

Prefer `EXP_INSTALL_DB_PASSWORD` to `--db-password`: an option is visible to every user of the machine in the process
list (`ps`) while the installation runs, and it stays in the shell history. The variable is visible to neither.

The defaults `root` and `postgres` are only there so that a test on a laptop works without options. For any site that
matters, create a database user for Exponential alone ([chapter 9](09-databases.md)) and pass it with `--db-user`.

### Site

| Option | Default | Meaning and checks |
|---|---|---|
| `--package=<name>` | `sevenx_multisite` | the site package |
| `--language=<locale>` | `eng-US` | the primary language |
| `--languages=<a,b>` | none | more languages, comma separated, e.g. `ger-DE,fre-FR` |
| `--title=<text>` | `Exponential` | the site name |
| `--organisation-name=<text>` | empty, which means the site name | who sends the optional e-mail (the footer of newsletters and notifications) |
| `--organisation-address=<text>` | empty | the sender's postal address; `\n` separates lines; the e-mail preferences status page warns while it is empty |
| `--url=<url>` | `http://localhost`; with host matching `http://<--host>` | where the site is; `http://` is added when no scheme is given. Set it: every siteaccess's address is built from it |
| `--access=<type>` | `url`; `host` when `--host` or `--admin-host` is given | how siteaccesses are matched: `url` (also `uri`), `host` (also `hostname`) or `port`. Anything else: "--access must be url, host or port" |
| `--site-access=<name>` | `site` | with `url`: the path of the public siteaccess. Lower-case letters, digits and underscores ("--site-access must be lower-case letters, digits and underscores") |
| `--admin-access=<name>` | `admin` | with `url`: the path of the admin siteaccess; same characters; must differ from `--site-access` ("--site-access and --admin-access must differ") |
| `--host=<host>` | none | with `host`: the public host name |
| `--admin-host=<host>` | none | with `host`: the admin host name. `--access=host` needs both ("--access=host needs both --host and --admin-host"), and they must differ ("--host and --admin-host must differ") |
| `--port=<port>` | `8080` | with `port`: the public port |
| `--admin-port=<port>` | `8081` | with `port`: the admin port; must differ from `--port` ("--port and --admin-port must differ") |
| `--editor-access=<name>` | `editor` (`<--site-access>_editor` when that is taken) | with `url`: the path of the editor siteaccess; same characters as `--site-access` ("--editor-access must be lower-case letters, digits and underscores") |
| `--editor-host=<host>` | `edit.<--host without www.>` (`editor.<...>` when that is taken) | with `host`: the editor host name |
| `--editor-port=<port>` | `--port` + 2 (`8082`) | with `port`: the editor port, digits only ("--editor-port must be a port number") |

The editor's value must differ from the public and admin values of the access type ("--editor-port must differ from
--port and --admin-port", and so on); the defaults always do.

Why set `--url` even for a test: the installation writes each siteaccess's address into the settings, and the
addresses in links, in mail and in the summary are built from it. Without it they all point to `http://localhost`,
which is right only when you open the site on the same machine.

As with every install method, the siteaccess directories are `settings/siteaccess/site`, `admin` and `editor`;
`--site-access`, `--admin-access` and `--editor-access` choose the URL paths that lead to them; see
[7.8](#78-the-editor-siteaccess-with-expinstall). When `extension/exp_adminui` is in the installation, `adminui` is
made too, from the admin siteaccess, and reached by the path `/adminui` whatever `--access` says; the summary then
ends its `Siteaccesses` line with `adminui (by uri)`. See [Exponential Admin UI](../features/6.0/exp-adminui.md).

### Administrator (login `admin`)

| Option | Default | Meaning and checks |
|---|---|---|
| `--email=<address>` | `nospam@exponential.earth` | must be a valid address ("--email is not an e-mail address: ..."). Give a real one: it becomes `[MailSettings] AdminEmail` |
| `--password=<pass>` | generated | see [7.5](#75-the-administrator-password). An empty value is refused: "--password cannot be empty (leave it out to have one generated)" |
| `--random-password` | off | always generate the password, even when `--password` is given |
| `--first-name=<name>` | `Administrator` | |
| `--last-name=<name>` | `User` | |

The login name is always `admin`. Change it, or add a personal administrator and disable `admin`, after the install
([chapter 10](10-after-installing.md)).

### Run

| Option | Meaning |
|---|---|
| `--force` | install over an existing installation: its database (with `--db-action=remove`) and its settings are replaced |
| `--dry-run` | check the configuration and the packages, install nothing ([7.6](#76-dry-run-and-print)) |
| `--print` | show the configuration that would be used, passwords masked, and stop |
| `--allow-root-user` | accepted and handed to the installation run, which accepts it too. It is not listed by `--help`. Running as root still leaves root-owned files behind; prefer the web server's user |
| `--help` | all options |

Values may not contain line breaks ("A value for *section*/*key* contains a line break"); use `\n` inside
`--organisation-address`.

Run the command as the user the web server's PHP runs as. The installation creates files in `var/` and
`settings/`; files created by `root` cannot be changed by the web server later, which shows up as errors on the first
page view ([chapter 12](12-troubleshooting.md#123-permissions-and-ownership)). For example:

```bash
sudo -u www-data php bin/php/console exp:install --url=https://www.example.com
```

## 7.4 What the configuration looks like

`--print` shows exactly what the run would put into `kickstart.ini`, with every non-empty password replaced by `***`.
For the defaults:

```bash
php bin/php/console exp:install --print
```

```ini
; Written by exp:install for one installation run; see kickstart.ini-dist.

[email_settings]
Continue=true
Type=mta
Server=
User=
Password=

[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
Server=localhost
Port=
Database=exponential.db
User=
Password=
Socket=

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=Exponential
URL=http://localhost
Access=site
AdminAccess=admin
AccessPort=8080
AdminAccessPort=8081
AccessHostname=
AdminAccessHostname=
Database=exponential.db
DatabaseAction=remove
OrganisationName=
OrganisationAddress=
EditorAccess=editor

[site_admin]
Continue=true
FirstName=Administrator
LastName=User
Email=nospam@exponential.earth
Password=***

[security]
Continue=true

[registration]
Continue=true
Send=false
```

How to read it:

- The last line of `[site_details]` depends on the access type: `EditorAccess` for `url`, `EditorAccessPort` for
  `port`, `EditorAccessHostname` for `host`. With `--access=port` it reads `EditorAccessPort=8082`.
- `--languages=ger-DE,fre-FR` adds one `Languages[]=` line per language under `[language_options]`.
- `AccessPort`, `AdminAccessPort` and the host name keys are always written; the installation uses only the ones that
  belong to the chosen access type.

E-mail is always set up as sendmail/MTA; change `[MailSettings]` after the install for SMTP
([chapter 10](10-after-installing.md)). The registration e-mail (`[registration] Send=false`) is never sent.

The section and key names are documented in [chapter 6](06-kickstarter.md#642-sections-and-keys). To keep this
configuration as a file and run it with the kickstarter instead, redirect `--print` into a file and put the real
passwords in place of `***`:

```bash
php bin/php/console exp:install --print --db=mysql --db-user=exponential > kickstart.ini
chmod 600 kickstart.ini
```

## 7.5 The administrator password

`exp:install` never installs a password that is empty, short or well known:

| You give | Result |
|---|---|
| no `--password` | a generated password |
| `--random-password` | a generated password (also when `--password` is given) |
| `--password=` with at least 10 characters (the shipped `site.ini [UserSettings] MinPasswordLength`) that is not well known | your password, as given |
| `--password=` that is shorter, or one of `publish`, `admin`, `password`, `changeme`, `change-me`, `secret`, `exponential`, `demo`, `123456` (any case) | a generated password; the summary says why |
| `--password=` (empty, or only spaces) | refused, exit status 1 |

Why the replacement instead of a refusal: an unattended installation that stops on a weak password helps nobody, and
one that installs `publish` as the administrator password of a site on the internet is an open door. A replaced
password is reported in the summary and recorded in the file below, so it is never lost.

A generated password has 24 characters from the alphabet `./A-Za-z0-9`, drawn from the operating system's secure
random source (`random_int()`); that is about 143 bits. It is shown once in the summary and, except in a dry run,
written to `var/log/initial-admin-password` (mode `0600`, readable by the owner only):

```text
Exponential administrator login: admin
Password: <the password>
Generated 2026-10-05T10:15:00+02:00 by exp:install: no --password given.
Log in, change it, then delete this file.
```

The reason on the third line is one of "no --password given", "requested with --random-password", or "the password
given is ... and cannot be installed".

The summary always shows the password the installation really set. The installation step has its own check; when it
replaces a password, it writes its replacement to the same file, and the summary reads it from there and says "the
password given is well-known and was refused by the installation".

**After the first login**, change the password in the administration interface and delete the file:

```bash
rm var/log/initial-admin-password
```

A password you gave yourself is stored nowhere in clear text: the summary says "The password is shown here once and
stored nowhere else in clear text", and the saved configuration in `var/log/` has it masked.

## 7.6 Dry run and print

| Mode | Checks the options | Connects to the database | Fetches the packages | Writes |
|---|---|---|---|---|
| `--print` | yes | no | no | nothing; prints the configuration and exits with 0 |
| `--dry-run` | yes | yes | yes, into a temporary repository | `var/log/setup.log`, `var/log/exp-install-<date>.ini`; no database, no settings, no password file, no maintenance mode |

`--dry-run` runs the kickstarter's dry run ([chapter 6](06-kickstarter.md#67-dry-runs)): `DatabaseChoice` to
`SiteDetails` with the generated configuration, stopping before `SiteAdmin`, so no password file is written and no
mail is sent. The temporary package repository (`var/storage/packages/dryrun`) is removed again at the end. A
successful dry run ends with:

```text
Dry-run completed: database and remote packages verified. Stopped after SiteDetails, nothing written.
```

It also runs on a directory that already holds an installation, without `--force`, which makes it the way to test a
configuration before a reinstall. The exit status is 0 when the check passed.

What each mode is good for:

- `--print` answers "what would it do with these options?" in a second, without a database or a network. Use it to
  check option spelling and the derived values (editor siteaccess, URL, database name).
- `--dry-run` answers "would it work?": the database server accepts the user and password, the database exists, the
  site package can be fetched. Use it before every real run on a server, and always before a `--force`.

## 7.7 Exit status

| Status | When |
|---|---|
| `0` | installed; or `--dry-run` passed; or `--print`; or `--help` |
| `1` | an option was unknown, malformed or failed its check; a value contained a line break; the directory already holds an installation and neither `--force` nor `--dry-run` was given; the existing `kickstart.ini` could not be moved aside or the new one not written ("nothing was changed"); or the installation run failed |

A script can therefore rely on `$?`:

```bash
php bin/php/console exp:install --dry-run --db=mysql --db-user=exponential \
  && php bin/php/console exp:install --force --db=mysql --db-user=exponential \
  || echo "installation failed, see var/log/setup.log"
```

On

```text
This directory already holds an installation (settings/override/site.ini.append.php).
exp:install would replace its database and settings. Re-run with --force to do that,
or with --dry-run to check the configuration only.
```

nothing has been touched.

When the installation run fails, the kickstarter prints which step stopped and why, and maintenance mode stays on so
that visitors do not see a half-installed site ("The site stays in maintenance mode. After fixing the cause: run the
kickstarter again, or php bin/php/maintenance.php off"). A window that was open before the run stays open in any case.
See [chapter 6](06-kickstarter.md#68-maintenance-mode-during-a-run). The summary is printed only after a successful
installation.

## 7.8 The editor siteaccess with exp:install

Every installation gets the editor siteaccess: an administration interface for content editing only. Without an
`--editor-*` option its value is derived from the public one, and never equals the public or the admin value, because
two siteaccesses on the same path, host or port would make one of them unreachable:

| `--access` | Editor siteaccess reached at | Option |
|---|---|---|
| `url` | the path `/editor` (`/<site-access>_editor` when `editor` is already the public or admin path) | `--editor-access` |
| `host` | `edit.<--host without www.>`, e.g. `edit.example.com` for `--host=www.example.com`; `editor.<...>` when that is the admin host | `--editor-host` |
| `port` | `--port` + 2, e.g. `8082` for `8080` (the next free port when that is the admin's) | `--editor-port` |

`exp:install` writes the value into the generated `kickstart.ini` (`EditorAccess`, `EditorAccessHostname` or
`EditorAccessPort`), and the summary shows the editor's address under `Editor`. For URL matching, the most common
case, nothing needs to be done. For host matching, the editor host needs a DNS entry and a place in the web server
or certificate configuration like the other two ([chapter 8](08-serving-the-site.md)).

## 7.9 Examples by database

### SQLite (the default)

```bash
php bin/php/console exp:install
```

Installs into `var/storage/sqlite3/exponential.db`. No database server is needed, which makes this the quickest way to
a working site. With an address and an administrator:

```bash
php bin/php/console exp:install \
    --url=https://www.example.com \
    --title="Example" \
    --email=webmaster@example.com \
    --random-password
```

A second SQLite site in the same directory tree is a second installation; one installation uses one database file.
SQLite in production is covered in [chapter 9](09-databases.md).

### MySQL or MariaDB

The database and the user must exist ([chapter 6, MySQL example](06-kickstarter.md#662-mysql-or-mariadb)).

```bash
EXP_INSTALL_DB_PASSWORD='a-long-random-password' \
php bin/php/console exp:install \
    --db=mysql --db-host=127.0.0.1 --db-port=3306 \
    --db-name=exponential --db-user=exponential \
    --url=https://www.example.com --email=webmaster@example.com
```

Through a local socket:

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=mariadb --db-socket=/var/lib/mysql/mysql.sock --db-name=exponential --db-user=exponential
```

`127.0.0.1` and `localhost` are not the same for MySQL: `localhost` makes the client use the local socket, `127.0.0.1`
a TCP connection. A user created as `'exponential'@'localhost'` may be refused over TCP; use the host name the user
was created for.

### PostgreSQL

The database must exist and `pgcrypto` must be available in it ([chapter 6, PostgreSQL example](06-kickstarter.md#663-postgresql)).

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=pgsql --db-host=db.internal --db-name=exponential --db-user=exponential \
    --url=https://intranet.example.com --email=it@example.com
```

### MongoDB

Needs the PHP `mongodb` extension, the `mongodb/mongodb` library and a database user created in the site's database
([chapter 9](09-databases.md#95-mongodb)).

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=mongodb --db-host=127.0.0.1 --db-port=27017 --db-name=exponential --db-user=exponential \
    --url=https://www.example.com
```

`--db-user` has no default for MongoDB, and it is not optional: the adapter always puts a user and a password into
its connection string, and without a user the MongoDB driver refuses the string ("Failed to parse MongoDB URI: ...
'default' authentication mechanism requires a username"), so the run stops at the database step. Create the user first, also on a test server
that does not enforce access control.

### Oracle

Needs the PHP `oci8` extension and the `ezoracle` extension in `extension/`, which the installation activates for the
new site.

```bash
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install \
    --db=oracle --db-host=127.0.0.1 --db-port=1521 --db-name=FREEPDB1 --db-user=EXPONENTIAL
# a TNS alias instead of host, port and service:
EXP_INSTALL_DB_PASSWORD='...' php bin/php/console exp:install --db=oracle --db-name=@EXPDB --db-user=EXPONENTIAL
```

The first form connects to `127.0.0.1:1521/FREEPDB1` (Easy Connect); the second hands `EXPDB` to the Oracle client,
which looks it up in its `tnsnames.ora`.

### Host and port matching

```bash
# site on www.example.com, admin on admin.example.com, editor on edit.example.com
php bin/php/console exp:install --access=host --host=www.example.com --admin-host=admin.example.com \
    --url=https://www.example.com

# site on 8080, admin on 8081, editor on 8082 (7.8)
php bin/php/console exp:install --access=port --port=8080 --admin-port=8081
```

### Reinstall over an existing installation

```bash
php bin/php/console exp:install --dry-run --db=mysql --db-name=exponential --db-user=exponential   # check first
php bin/php/console exp:install --force   --db=mysql --db-name=exponential --db-user=exponential   # replaces it
```

**This destroys the content of the database `exponential`.** Back it up first
([chapter 6](06-kickstarter.md#65-databaseaction-read-this-before-you-run) shows how for each database).

### Regenerate the settings, keep the data

```bash
php bin/php/console exp:install --force --db-action=skip --db=mysql --db-name=exponential --db-user=exponential \
    --url=https://www.example.com
```

`skip` inserts nothing and keeps the existing administrator; the settings in `settings/override/` and
`settings/siteaccess/` are written again from the options. Hand-made changes in those files are replaced, so copy them
aside first and merge them back afterwards.

## 7.10 What you see at the end

The command starts with one line naming what it is about to do, for example:

```text
exp:install: sevenx_multisite on sqlite (exponential.db), http://localhost, access by url, administrator admin / (generated, shown at the end)
```

For a database server the parenthesis names the server too, for example `(exponential at 127.0.0.1:3306)`.

Then the kickstarter prints its steps, and the command ends with:

```text
================================================================
  Exponential is installed
================================================================
  Installed:      2026-10-05 10:17:31 CEST (151s)
  Site:           http://localhost/site/
  Admin login:    http://localhost/admin/user/login
  Editor:         http://localhost/editor/
  Username:       admin
  Password:       <the password>
  Password note:  generated: no --password given; also in var/log/initial-admin-password (owner only), change it and delete that file
  E-mail:         nospam@exponential.earth
  Database:       sqlite exponential.db
  Package:        sevenx_multisite, eng-US
  Siteaccesses:   site, admin, editor (by url)
  Configuration:  var/log/exp-install-20261005-101500.ini (passwords masked)
================================================================
  Copy and paste:

    Admin:    http://localhost/admin/user/login
    Username: admin
    Password: <the password>
    Site:     http://localhost/site/
================================================================
  The password was generated: it is also in var/log/initial-admin-password (owner only).
  Change it after the first login and delete that file.
```

For a database server, the `Database` line reads for example `mysql exponential at 127.0.0.1:3306, user exponential`;
extra languages appear as `eng-US + ger-DE,fre-FR` in the `Package` line. With a password you gave, the `Password
note` line is missing and the last line reads "The password is shown here once and stored nowhere else in clear text."

The addresses are where the site *will* be reachable once a web server serves the installation root; `exp:install`
does not start one. Next: serve the site ([chapter 8](08-serving-the-site.md)), then work through
[chapter 10](10-after-installing.md).

## 7.11 What can go wrong

| Symptom | Cause | What to do |
|---|---|---|
| "This directory already holds an installation ..." | `settings/override/site.ini.append.php` has a `[DatabaseSettings]` section | to replace the installation, back up and add `--force`; to only test, add `--dry-run` |
| "Unknown option or missing value: --db (see --help)" | a value given with a space (`--db mysql`) instead of `=` | write `--db=mysql`; every value needs `=` |
| "Unknown argument: -f (see --help)" | a short option or a bare word | the command knows long options only |
| the run stops in `DatabaseInit` | wrong host, port, user or password, or the database does not exist | run `--dry-run` until it passes; check the user with the database's own client |
| the run stops while fetching packages | no network access to the package server, or a proxy in the way | see [chapter 12](12-troubleshooting.md) |
| the site shows the maintenance page after a failed run | maintenance mode is kept on after a failure, on purpose | fix the cause and run again, or `php bin/php/maintenance.php off` |
| the summary does not appear | the installation did not reach its end (`settings/override/site.ini.append.php` was not rewritten) | read the kickstarter's output above and `var/log/setup.log` |
| pages fail with permission errors after the install | the command ran as `root` | give the files to the web server's user ([chapter 12](12-troubleshooting.md#123-permissions-and-ownership)) |
| your old `kickstart.ini` is missing | the process was killed (`kill -9`, power loss) before its clean-up | it is in `var/log/kickstart.ini.before-exp-install-<date>`; move it back |

The kickstarter's log of every run, successful or not, is `var/log/setup.log` ([chapter 6](06-kickstarter.md#69-logs)).

## 7.12 Other console commands for setting up

These commands are useful around an installation. Each one's own `--help` lists its options. Scripts that use the
standard script options refuse to run as `root` unless `--allow-root-user` is given.

| Command | Use around an install |
|---|---|
| `exp:kickstarter` | `ini` writes `kickstart.ini`, `run` installs from it ([chapter 6](06-kickstarter.md)) |
| `exp:maintenance` | `on`, `off`, `status`: the maintenance page; `on` takes `--message`, `--until`, `--allow-ip`, `--allow-admin` |
| `exp:ini` | read and change settings in every scope: `get`, `set`, `where`, `list` ... ([exp:ini](../bc/6.0/console-exp-ini.md)) |
| `exp:ezcache` | clear caches by tag or id (`--clear-tag=ini`, `--clear-all`) |
| `exp:cache` | clear and inspect all caches, including HTTP, Velocity and OPcache ([cache console](../bc/6.0/cache-console.md)) |
| `exp:ezpgenerateautoloads` | regenerate the class autoload arrays (`-e` extensions, `-k` kernel, `-o` overrides); an unknown option stops it with "The referenced parameter '...' is not registered." and exit status 1 |
| `exp:velocity` (alias `exp:vc`) | Exponential Velocity, the recommended application server ([chapter 8](08-serving-the-site.md)) |
| `exp:webserver` | the web server engines (`php`, `frankenphp`, `qbix`) with the same verbs as `exp:velocity` |
| `exp:frankenphp` (alias `exp:fp`) | the same command pinned to the FrankenPHP engine |
| `exp:resetuserpassword` | reset a user's password, for example the administrator's |
| `exp:checkmanifest` | check the files of the installation against `share/filelist.md5` |
| `exp:checkclasses` | load every declared class and report the ones PHP refuses |
| `exp:ezsqlinsertschema`, `exp:ezsqldumpschema` | insert or dump a database schema from or to a `.dba` file |
| `exp:updatesearchindex` | rebuild the search index |
| `exp:warm`, `exp:preload` | request every published page so the caches are full before visitors arrive |
| `exp:ezwebininstall` | install the `ezwebin` site package into an existing installation |

---

## References

In this repository:

- [Installing Exponential 6.0](../INSTALL.md), section 7
- [Installing Exponential in one command](../features/6.0/install-in-one-command.md)
- [The console](../bc/6.0/console.md), [exp:ini](../bc/6.0/console-exp-ini.md), [cache console](../bc/6.0/cache-console.md)
- [Kickstarter CLI](../bc/6.0/kickstartercli.md)
- [Maintenance mode](../features/6.0/maintenance-mode.md)
- [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md)
- [Database drivers: SQLite and Oracle](../specifications/6.0/database-drivers-sqlite-oracle.md),
  [database drivers, September 2026](../specifications/6.0/database-drivers-2026-09.md),
  [SQLite database](../features/6.0/sqlite-database.md)
- [CLI, cronjob and view abstractions](../bc/6.0/cli_cronjob_view_abstractions.md)
- [Getting started](../guides/getting-started.md)
- Code: `bin/php/install.php` (options, checks, summary), `kernel/private/classes/commands/install.php` (password
  rules, kickstarter call), `kernel/classes/expkickstarter.php`, `kernel/setup/steps/ezstep_site_access.php` (editor
  defaults), `kernel/setup/steps/ezstep_site_admin.php` (well-known passwords), `bin/php/console`

External:

- PHP `random_int()`: <https://www.php.net/manual/en/function.random-int.php>
- PHP `filter_var()`, used with `FILTER_VALIDATE_EMAIL`: <https://www.php.net/manual/en/function.filter-var.php>
- PHP OCI8: <https://www.php.net/manual/en/book.oci8.php>
- MongoDB PHP driver: <https://www.php.net/manual/en/book.mongodb.php>
- Oracle Easy Connect naming: <https://docs.oracle.com/en/database/oracle/oracle-database/19/netag/configuring-naming-methods.html>
- Symfony Console (the model for `bin/php/console`): <https://symfony.com/doc/current/console.html>
- Exponential on GitHub: <https://github.com/se7enxweb/exponential>

[Contents](README.md) · Previous: [6. The kickstarter](06-kickstarter.md) · Next: [8. Serving the site](08-serving-the-site.md)
