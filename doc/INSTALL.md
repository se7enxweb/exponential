# Installing Exponential 6.0

This guide installs Exponential 6.0 from nothing to a working site: what you need, how to get the code, the three
ways to install (the browser setup wizard, the kickstarter, the one-command console install), how to serve the
site and what to do afterwards. For what Exponential is and how it is served, read the project
[README](../README.md) first. For a guided first hour after the install, read
[Getting started](guides/getting-started.md).

Every command is run from the installation root: the directory that holds `index.php`, `autoload.php`, `bin/`
and `settings/`. The command console is `bin/php/console`. Many installations add a shortcut to it in the root
(`ln -s bin/php/console console`), so `./console` and `php bin/php/console` are the same program. When you run
a script as `root`, add `--allow-root-user`; the script says so when it is needed.

## Contents

1. [What you get, and the quick start](#1-what-you-get-and-the-quick-start)
2. [Requirements](#2-requirements)
3. [Get the code](#3-get-the-code)
4. [Choose an install method](#4-choose-an-install-method)
5. [Install with the setup wizard](#5-install-with-the-setup-wizard)
6. [Install with the kickstarter](#6-install-with-the-kickstarter)
7. [Install with one console command](#7-install-with-one-console-command)
8. [Serve the site: web server, HTTPS and permissions](#8-serve-the-site-web-server-https-and-permissions)
9. [SQLite in production](#9-sqlite-in-production)
10. [After installing](#10-after-installing)
11. [Upgrading from an older version](#11-upgrading-from-an-older-version)
12. [Troubleshooting](#12-troubleshooting)

## 1. What you get, and the quick start

An installation is the Exponential kernel, the extensions listed in `composer.json`, a site package with its
content and design, and three siteaccesses:

| Siteaccess | Default name | For |
|---|---|---|
| public site | `site` (wizard: from the package) | visitors |
| administration | `admin` | administrators |
| editor | `editor` | editors: the administration for content only, without setup, design and developer tools |

The shortest path that works needs only PHP and Composer. It installs on SQLite (no database server) and serves the
site with the PHP server that Exponential can start for you:

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console exp:install --random-password --url=http://localhost:8087
php bin/php/console exp:velocity start --engine=php
```

Open `http://localhost:8087/site/` for the site and `http://localhost:8087/admin/user/login` for the
administration. The user is `admin`; the password is printed once at the end of the install and written to
`var/log/initial-admin-password` (readable by the owner only). Change it after the first login and delete that
file. Stop the server with `php bin/php/console exp:velocity stop --engine=php`.

The `php` engine is PHP's own server, for development. For a real site use Exponential Velocity's `qbix` engine or
Apache/nginx with PHP-FPM ([section 8](#8-serve-the-site-web-server-https-and-permissions)).

## 2. Requirements

### PHP

| | |
|---|---|
| Version | **PHP 8.0 to 8.5**. `composer.json` declares `^8.0` (and accepts later 8.x). PHP 8.0 is the oldest supported version, so the stock PHP of Red Hat Enterprise Linux 9 (and AlmaLinux 9, Rocky Linux 9) runs Exponential under Apache or PHP-FPM. Use the newest PHP your system offers. See [PHP 8.0 support](bc/6.0/php-8.0-support.md). |
| Exponential Velocity | needs **PHP 8.1** or later. |
| 64-bit PHP | needed for more than 30 languages (`composer.json`, `suggest`). |
| `memory_limit` | at least `64M` (setup check `memory_limit`). Restart the web server or PHP-FPM after editing `php.ini`. |
| `max_execution_time` | at least `30` seconds (setup check `execution_time`). |
| `date.timezone` | must be set in `php.ini` (or in `.htaccess`); see the [list of timezones](https://www.php.net/manual/en/timezones.php). |
| File uploads | `file_uploads` on (setup check `file_upload`). |

PHP extensions:

| Needed by | Extensions |
|---|---|
| `composer.json` (`require`) | `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl`, `simplexml` |
| the setup wizard's critical checks (`settings/site.ini [SetupSettings] CriticalTests`) | also `zlib`, `intl`, `xsl`; one database extension (`sqlite3`, `mysqli`, `pgsql`, `mongodb` or `oci8`); `gd` or the ImageMagick `convert` program for images |
| recommended (`composer.json`, `suggest`) | `curl` (talking to other servers, packages over SSL), `gd`, `openssl` (secure random bytes), `pcntl` (asynchronous publishing) |

The setup wizard's system check tests all of these and tells you what is missing. On a running installation,
`php bin/php/console exp:velocity ext check` lists every extension the installation wants, marks what is missing
and prints the install command for your system.

### Composer

Composer 2.x. It installs the Zeta Components and the Exponential extensions that `composer.json` lists.

### Database

| Database | Driver (`DatabaseImplementation`) | PHP extension | Notes |
|---|---|---|---|
| **SQLite 3** | `sqlite3` | `sqlite3` | The default. No database server: the database is one file under `var/storage/sqlite3/`. A full production database, see [section 9](#9-sqlite-in-production). |
| MySQL or MariaDB | `mysqli` (also `ezmysqli`, `mysql`) | `mysqli` | UTF-8 is required. |
| PostgreSQL | `pgsql` (also `ezpostgresql`, `postgresql`) | `pgsql` | |
| MongoDB | `mongodb` | `mongodb` | |
| Oracle 19c or later | through the `ezoracle` extension | `oci8` | `ezoracle` must be in `extension/`; it is a dependency in `composer.json`. |

The names are the aliases in `settings/site.ini`, block `[DatabaseSettings]`, `ImplementationAlias[]`. The minimum
server versions the wizard checks are in `kernel/setup/ezsetupcommon.php` (MySQL 4.1.1, PostgreSQL 8.0, SQLite
3.0.1, MongoDB 4.0, Oracle 19).

### Web server

One of these, chosen in [section 8](#8-serve-the-site-web-server-https-and-permissions):

- **Exponential Velocity**, the recommended application server: it serves HTTP and HTTPS itself, with its own
  certificates, and needs no other web server.
- **Apache** with `mod_rewrite` and PHP-FPM (or mod_php). The rewrite rules ship in `.htaccess_root`.
- Any other server that can rewrite a URL to `index.php` (nginx, LiteSpeed, Caddy and others, listed in the
  [README](../README.md#the-traditional-options)). This guide gives no configuration for them.

## 3. Get the code

Pick one. All give the same installation.

**Composer project** (quickest for a new site):

```bash
cd www_root_directory
composer create-project se7enxweb/exponential exponential
cd exponential
```

Add a version to pin a release, for example `composer create-project se7enxweb/exponential:v6.0.14 exponential`.

**Git clone** (when you want the history and to follow changes):

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
composer install
```

**Composer require into an empty directory** (the package from
[Packagist](https://packagist.org/packages/se7enxweb/exponential)):

```bash
mkdir exponential
cd exponential
composer require se7enxweb/exponential
```

Then check that the console runs:

```bash
php bin/php/console --version
```

The first line names the version, for example `console (Exponential) 6.0.15stable`.

**Optional: Exponential Velocity.** It is suggested, not installed by default, because it needs PHP 8.1. On PHP 8.1
or later add it with:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.42
```

## 4. Choose an install method

All three methods run the same setup steps (`kernel/setup/steps/`) and produce the same kind of installation.

| | Setup wizard | Kickstarter | Console install |
|---|---|---|---|
| Started by | opening the site in a browser | `php bin/php/console exp:kickstarter run --force` | `php bin/php/console exp:install` |
| Answers come from | the forms, one page per step | `kickstart.ini` in the installation root | command-line options, every one with a default |
| Best for | a first install, trying Exponential | repeatable, scripted or unattended installs | the fastest install; scripts and CI |
| Needs a web server during install | yes | no | no |
| Dry run | no | `run --dry-run` | `--dry-run`, `--print` |
| Protects an existing installation | no (it only starts while `CheckValidity=true`) | needs `--force` for the step that writes the database | refuses unless `--force` |

How the site is served is chosen separately ([section 8](#8-serve-the-site-web-server-https-and-permissions)):

| | Exponential Velocity (`qbix`) | Apache or nginx with PHP-FPM | FrankenPHP (through Velocity) |
|---|---|---|---|
| Other web server needed | no | yes | no |
| HTTPS | built in: an existing certificate, Let's Encrypt or any ACME CA, self-signed fallback | the web server's, certificate from your own tool | built in, self-signed when no certificate is set |
| PHP | 8.1+ | 8.0+ | the binary's own |
| Role | **recommended**, every stage from development to production | the classic setup, shared hosting | production-ready alternative |

## 5. Install with the setup wizard

### How it starts

`settings/site.ini` ships with `[SiteAccessSettings] CheckValidity=true`. While it is `true`, every request is sent
to the setup wizard (module `setup`, function `init`) instead of the site. The last step writes
`CheckValidity=false` into `settings/override/site.ini.append.php`, and from then on the site is served.

1. Serve the installation with a web server ([section 8](#8-serve-the-site-web-server-https-and-permissions)), for
   example `php bin/php/console exp:velocity start --engine=php`.
2. Open the site address in a browser. The wizard's first page appears.

While the wizard runs, only the browser that started it gets through (it carries the cookie
`exp_setup_wizard`); every other visitor sees a "The site is being set up" page (HTTP 503). The hold ends with the
wizard's last page, or 30 minutes after the wizard's last request (`expMaintenance::WIZARD_LEASE`).

If a `kickstart.ini` is in the installation root, the wizard reads it: steps whose section has `Continue=true` are
skipped, the others are pre-filled ([section 6](#6-install-with-the-kickstarter)). Remove or move `kickstart.ini`
if you want to answer every page yourself.

### The steps

The order is the step table in `kernel/setup/steps/ezstep_data.php`.

| # | Page | What to enter |
|---|---|---|
| 1 | Welcome | Nothing; continue. |
| 2 | System check | The critical checks: directory permissions, PHP version, a database extension, image conversion, memory limit, execution time, `allow_url_fopen`, sessions, file uploads, `zlib`, `dom`, `iconv`, `mbstring`, `intl`, `xsl`, the timezone and the Zeta Components. Each problem comes with the fix, for permissions the exact `chmod`/`chown` commands. Fix them and press **Next** to run the check again. A test can be ignored with its checkbox (not recommended). |
| 3 | System finetuning | Optional checks (`curl`, GD, ImageMagick, the other database extensions, TrueType text functions, `open_basedir`). Fix and press **Finetune**, or press **Next** to go on. |
| 4 | Outgoing Email | Sendmail/MTA, or SMTP with server, user and password. |
| 5 | Choose database system | SQLite is listed first and preselected (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`). If the `sqlite3` extension is missing, the first available system is chosen and the page says so. |
| 6 | Database initialization | Server, port, database name, user, password, socket. For SQLite only the file name; the Database field lists the SQLite files already in `var/storage/sqlite3/`. |
| 7 | Language support | The primary language and any additional languages. |
| 8 | Site package | The site package to install; the bundled one is preselected. |
| 9 | Package language options | Shown when the package has languages you did not choose: map them to one of yours. |
| 10 | Site access configuration | How the siteaccesses are told apart: by URL path (`/site`, `/admin`), by port, or by host name. |
| 11 | Site details | Title, URL, the names of the public, admin and editor siteaccesses (or their ports, or host names; the editor is prefilled `editor`, `8082` or `edit.<host>`), and what to do with a database that already holds data. |
| 12 | Site administrator | First name, last name, e-mail and password of the `admin` user. An empty or well-known password (such as `publish`) is replaced with a generated one, written once to `var/log/initial-admin-password`. |
| 13 | Site security | Advice when the site does not run in virtual host mode: copy the rules with `cp .htaccess_root .htaccess`. |
| 14 | Registration | Whether to send the optional registration e-mail. |
| — | Create sites | Not a page: the database schema and data, the packages and the `settings/siteaccess/` files are written here. |
| 15 | Finished | The addresses of the public, admin and editor sites. |

The wizard logs its progress to `var/log/setup.log`. More: [setup wizard and editor siteaccess](features/6.0/setup-wizard-and-editor-siteaccess.md),
[clean install defaults](features/6.0/clean-install-defaults.md),
[installer logs and seed data](specifications/6.0/installer-logs-and-seed-data.md).

## 6. Install with the kickstarter

The kickstarter (`bin/php/kickstarter.php`, console `exp:kickstarter`) runs the setup wizard's steps without a
browser and reads every answer from `kickstart.ini` in the installation root. The commented template is
`kickstart.ini-dist`.

### The commands

```bash
php bin/php/console exp:kickstarter ini            # write kickstart.ini, asking a few questions
php bin/php/console exp:kickstarter ini --yes      # write it with the built-in defaults, no questions
php bin/php/console exp:kickstarter run --dry-run  # check kickstart.ini and the packages, write nothing
php bin/php/console exp:kickstarter run --force    # install
```

`php bin/php/kickstarter.php` takes the same subcommands and options.

| Subcommand and option | Meaning |
|---|---|
| `ini` | Write `kickstart.ini` from `kickstart.ini-dist`, asking for the values. |
| `ini --yes`, `-y` | Accept the defaults and write without asking. |
| `ini --defaults`, `-d` | Copy the values of `kickstart.ini-dist` verbatim. |
| `run` | Run the steps from `welcome` to `final`. |
| `run --force` | Required whenever the steps include `CreateSites`, the step that writes the database and settings. Without it `run` stops with "Re-run with --force". |
| `run --dry-run` | Validate `kickstart.ini`, run `DatabaseChoice` to `Registration`, download the site package and its dependencies into a temporary `var/storage/packages/dryrun/` (removed again), and stop before `CreateSites`. The database must be reachable; nothing is written to it. |
| `run --list-steps` | List the steps and exit. Changes nothing. |
| `run --start-step=<step>`, `--stop-step=<step>` | Run part of the steps, for example to resume after a failure: `run --force --start-step=SiteDetails`. |

While `run --force` rebuilds the database, the site answers every visitor with the maintenance page. Every run is
logged to `var/log/kickstart.log` with passwords masked (earlier runs are kept as `kickstart.log.1` to `.9`;
`EXP_KICKSTART_LOG=0` turns the log off). In kickstart mode the site package is always downloaded from the remote
package repository configured in `package.ini`.

### kickstart.ini

One section per wizard step. Leave no whitespace before a section or a key. `Continue=true` skips the step and uses
the values; `Continue=false` (or no `Continue`) shows the step pre-filled. A complete example for MySQL:

```ini
[email_settings]
Continue=true
Type=mta

[database_choice]
Continue=true
Type=mysqli

[database_init]
Continue=true
Server=localhost
Port=
Database=exponential
User=exponential
Password=CHANGE_ME
Socket=

[language_options]
Continue=true
Primary=eng-GB
Languages[]=eng-US

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
Access=site
AdminAccess=admin
EditorAccess=editor
Database=exponential
DatabaseAction=remove

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

| Section | Keys |
|---|---|
| `email_settings` | `Type` (`mta` or `smtp`), `Server`, `User`, `Password` |
| `database_choice` | `Type`: `sqlite3` (default), `mysqli` (or `mysql`), `pgsql` (or `postgresql`), `mongodb`, `oci8` (or `oracle`; needs `extension/ezoracle`) |
| `database_init` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket`. For Oracle, `Database` is the connect string (for example `127.0.0.1:1521/FREEPDB1`) or a TNS alias. |
| `language_options` | `Primary`, `Languages[]` |
| `site_types` | `Site_package` |
| `site_access` | `Access`: `url`, `port` or `hostname` |
| `site_details` | `Title`, `URL`, `OrganisationName`, `OrganisationAddress`, `Access`, `AdminAccess`, `EditorAccess`, `AccessPort`, `AdminAccessPort`, `EditorAccessPort`, `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname`, `Database`, `DatabaseAction` |
| `site_admin` | `FirstName`, `LastName`, `Email`, `Password` (empty: one is generated and written to `var/log/initial-admin-password`) |
| `security` | `Continue` only |
| `registration` | `Comments`, `Send` |

Every locale the site package uses must be in `Languages[]` (or be mapped to the primary language); otherwise
objects in that language are imported but cannot be found in the tree.

### DatabaseAction: read this before you run

> **Warning.** `DatabaseAction=remove` **empties the named database** and installs into it. Everything in that
> database is lost. Point `Database` at a database that holds nothing you need, and take a backup first.

| Value | What happens to the database |
|---|---|
| `remove` | Existing entries are removed, then the schema, data and packages are installed. A clean install. |
| `ignore` | Entries are added without cleaning up. |
| `skip` | No schema or data is inserted. Use it to regenerate the siteaccess and INI files against an existing database. |

### Re-running

- Run again with the same `kickstart.ini` for an identical install. With `DatabaseAction=remove` this replaces the
  database again.
- Resume after a failure with `--start-step=<step>`; see the steps with `run --list-steps`.
- `ini` and `run` delete the cached `var/cache/ini/kickstart-*.php` themselves. If you edit `kickstart.ini` by hand
  and see old values, delete those files.
- When you install onto a different database engine than the one the installation used before, first set
  `DatabaseImplementation` in `settings/override/site.ini.append.php` to the new engine: the installer keeps the
  value it finds there.
- `kickstart.ini` holds the database password. Keep it out of version control, and remove or move it after the
  install if the web wizard must not read it.

Full reference: [Kickstarter CLI](bc/6.0/kickstartercli.md), [Kickstarter](features/6.0/kickstarter-cli.md).

## 7. Install with one console command

`exp:install` (`bin/php/install.php`) builds the kickstart configuration from its options, runs the same steps in
the same process, and puts back any `kickstart.ini` that was there. Everything has a default, so this alone
installs the `sevenx_multisite` package on SQLite with the user `admin` and a generated password:

```bash
php bin/php/console exp:install
```

Other databases (keep the password out of the shell history with `EXP_INSTALL_DB_PASSWORD`):

```bash
EXP_INSTALL_DB_PASSWORD='secret' php bin/php/console exp:install --db=mysql --db-host=127.0.0.1 \
    --db-name=mysite --db-user=mysite --url=https://www.example.com --random-password
```

| Option | Default | Meaning |
|---|---|---|
| `--db=` | `sqlite` | `sqlite`, `mysql` (also `mariadb`), `pgsql`, `mongodb`, `oracle` |
| `--db-host=`, `--db-port=`, `--db-socket=` | `localhost`; 3306, 5432, 27017, 1521 | where the database server is |
| `--db-name=` | `exponential` (`exponential.db` for SQLite, `FREEPDB1` for Oracle) | the database |
| `--db-user=`, `--db-password=` | `root` (mysql), `postgres` (pgsql), none otherwise | credentials; or `EXP_INSTALL_DB_PASSWORD` |
| `--db-action=` | `remove` | `remove`, `ignore` or `skip`, as `DatabaseAction` above. **`remove` empties the database first.** |
| `--package=` | `sevenx_multisite` | site package |
| `--language=`, `--languages=` | `eng-US` | primary language; more as a comma list |
| `--title=` | `Exponential` | site name |
| `--url=` | `http://localhost` | where the site is |
| `--access=` | `url` | `url`, `host` or `port` |
| `--site-access=`, `--admin-access=` | `site`, `admin` | siteaccess names |
| `--host=`, `--admin-host=` | — | host names (imply `--access=host`) |
| `--port=`, `--admin-port=` | `8080`, `8081` | ports for `--access=port` |
| `--email=`, `--first-name=`, `--last-name=` | | the administrator |
| `--password=` | generated | kept only when it has at least 10 characters (`site.ini [UserSettings] MinPasswordLength`) and is not a well-known one; otherwise replaced by a generated one |
| `--random-password` | | generate a 24-character password, shown once and written to `var/log/initial-admin-password` |
| `--force` | | install over an existing installation (replaces its database and settings) |
| `--dry-run` | | check the configuration and packages, install nothing |
| `--print` | | show the configuration that would be used, and stop |
| `--help` | | all options |

The summary at the end lists the site address, the admin login address, the user and the password. The
configuration used is kept in `var/log/exp-install-<date>.ini` with passwords masked. More:
[Install in one command](features/6.0/install-in-one-command.md).

## 8. Serve the site: web server, HTTPS and permissions

The document root is always the installation root.

### Option A: Exponential Velocity (recommended)

Velocity is an application server that ships for Exponential. It loads the application once, keeps it in
persistent workers and has its own response cache. It serves HTTP and HTTPS itself, with its own certificates, so
no Apache, nginx or PHP-FPM is needed. Install it with Composer ([section 3](#3-get-the-code)), then:

```bash
php bin/php/console exp:velocity config set ServerSettings Engine qbix
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
```

`config set` writes `settings/override/velocity.ini.append.php`; the shipped defaults stay in
`settings/velocity.ini`. The shipped default engine is `php` (development); set `qbix` for every real site.
The `qbix` engine serves plain HTTP on `[ServerSettings] Port` (8088) and HTTPS on `HTTPSPort` (8080), bound to
`Host` (`127.0.0.1`). To serve the network:

```bash
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0
php bin/php/console exp:velocity config set ServerSettings Port 80
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443
php bin/php/console exp:velocity restart
```

Ports below 1024 need root. Started as root, the server process keeps root to bind the ports and read the
certificate, and the workers run as `[ServerSettings] User` and `Group` (when empty: `VC_RUN_USER` and
`VC_RUN_GROUP` from the environment or `/etc/vc/envvars`, else the owner of the document root). Workers never run as
root unless `AllowRootWorkers=enabled`.

**HTTPS with a certificate you have** (one PEM file may hold both):

```bash
php bin/php/console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem
php bin/php/console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem
php bin/php/console exp:velocity config set HTTPSSettings Enabled true
php bin/php/console exp:velocity restart
php bin/php/console exp:velocity ssl show
```

HTTPS is on when `Enabled` is `true` and both files exist. The certificate is checked before use (parses, not
expired, key matches); a self-signed certificate stands in while the real one is unusable (`fallback`, default
`self-signed`), and a renewed certificate is swapped in without a restart. The engine's certificate subsystem also
obtains and renews Let's Encrypt (or any ACME CA) certificates and reads archives and PKCS#12 bundles; it is
configured under `Q.web.https` and described in [Velocity HTTPS and certificates](features/6.0/velocity-https-certificates.md).
`[HTTPSSettings] HSTSMaxAge` (300 seconds shipped) sets Strict-Transport-Security; raise it only when every name of
the site is on HTTPS for good.

**Configuration layout.** Velocity's configuration follows Debian's `/etc/apache2`: `/etc/vc/vc.conf`,
`ports.conf`, `envvars`, and `sites-available`/`sites-enabled`, `conf-available`/`conf-enabled`,
`mods-available`/`mods-enabled` (an overlay on the engine's `/etc/qbix`, which is read too). The site file
`sites-available/<site>.conf` is generated from `velocity.ini` on every `start` and `restart`, or with
`exp:velocity layout migrate`. Switch sites, snippets and modules like `a2ensite`:

```bash
php bin/php/console exp:velocity layout                 # show every file the server uses
php bin/php/console exp:velocity site enable <name>
php bin/php/console exp:velocity site disable <name>
```

**Day to day:**

| Command | Does |
|---|---|
| `exp:velocity start`, `stop`, `status`, `restart` | as named; `status --json` for monitoring |
| `exp:velocity graceful` | re-executes the server, keeping the listening socket: no dropped requests |
| `exp:velocity cache clear` | clears the response cache |
| `exp:velocity deploy` | after a PHP, INI or class change: autoloads, caches, PHP-FPM reload, Velocity restart, in the right order (`--dry-run` shows the steps) |

Workers keep the application in memory: after changing a class or a setting, restart (or `deploy`); clearing
caches alone does not reach them. Velocity has no command to install a boot service; the engine package ships an
example systemd unit in `vendor/se7enxweb/exponential-velocity/service/`. When Velocity serves the site alone, set
`[DeploySettings] PhpFpmService` to `disabled`.

More: [Velocity engines](bc/6.0/velocity-engines.md), [on-disk layout](bc/6.0/velocity-ondisk-layout.md),
[persistent workers](features/6.0/velocity-persistent-worker-server.md), [Deploying](guides/deploying.md).

**FrankenPHP** is the production-ready alternative engine driven by the same commands: `exp:velocity config set
ServerSettings Engine frankenphp`, `exp:velocity install` (downloads and checks the binary), `exp:velocity start`.
It serves HTTPS by default (`[FrankenPHPSettings] HTTPS=enabled`, port 8444), with your `[HTTPSSettings]
Certificate` and `Key`, or a self-signed certificate it makes and renews itself.

### Option B: Apache with PHP-FPM

Enable the shipped rewrite rules:

```bash
cp .htaccess_root .htaccess
```

They turn on `.htaccess` based virtual host mode: dot files, `.git` and scripts below the front controllers answer
404, `/api/` goes to `index_rest.php`, static design files are served directly and everything else goes to
`index.php`. Do not remove security rules from it to make something work. A minimal virtual host:

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/example.com
    DirectoryIndex index.php

    <Directory /var/www/example.com>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

`mod_rewrite`, `mod_proxy` and `mod_proxy_fcgi` must be loaded. Check with `apachectl configtest` and reload
Apache. For HTTPS add a `*:443` virtual host with `SSLEngine on`, `SSLCertificateFile` and
`SSLCertificateKeyFile`, and a certificate from your usual tool. After a PHP change reload the PHP-FPM that serves
the site; on servers with several PHP versions that is not always the one called `php-fpm`
(`[DeploySettings] PhpFpmService=auto` lets `exp:velocity deploy` find it). Details: [Deploying](guides/deploying.md).

### Permissions

The user the web server or Velocity workers run as must be able to write these directories (the setup check
`directory_permissions` in `settings/setup.ini`): `design`, `extension`, `settings`, `settings/override`,
`settings/siteaccess`, `var` and everything below it that Exponential writes (`var/cache`, `var/log`,
`var/storage`, `var/autoload`). The system check prints the commands for your machine, of this form:

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

When command-line scripts and cronjobs run as a different user from the web server, give them a common group
that can write the same files.

## 9. SQLite in production

SQLite is not only for trying Exponential. The kernel driver (`lib/ezdb/classes/ezsqlite3db.php`, class
`eZSQLite3DB`) is built for a site under load.

**What the driver does:**

| | |
|---|---|
| WAL | The driver switches the database to `journal_mode=WAL` (set once, stored in the file). Readers do not block the writer and the writer does not block readers, so reads run concurrently. |
| Connection tuning | `synchronous=NORMAL` (crash-safe with WAL), `cache_size=-65536` (64 MB), `mmap_size=268435456` (256 MB), `temp_store=MEMORY`, `busy_timeout=5000` (5 s per statement). |
| Queued writes | SQLite has one writer at a time. Each transaction starts with `BEGIN IMMEDIATE` after taking an exclusive lock on `<database file>.writer-lock`, next to the database. Writers wait their turn there for up to `SQLiteTransactionWait` seconds instead of failing with "database is locked". Waiting is safe: nothing has been written yet, and a started transaction cannot fail for a lock. The lock is released at commit or rollback, and by the operating system if the process dies. |
| Timeout | Only a transaction that waits longer than `SQLiteTransactionWait` fails, with "database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written". |

**Settings** (`settings/site.ini`, block `[DatabaseSettings]`; override in `settings/override/site.ini.append.php`):

```ini
[DatabaseSettings]
DatabaseImplementation=sqlite3
# a name under var/storage/sqlite3/, or an absolute path
Database=exponential.db
# seconds a transaction waits for the writers ahead of it (default 60);
# keep it below the web server's request timeout
SQLiteTransactionWait=60
# PRAGMAs over the driver's defaults, one per line as name=value
SQLitePragmas[]
#SQLitePragmas[]=cache_size=-131072
```

Clear the INI cache after a change: `php bin/php/ezcache.php --clear-tag=ini`.

**What carries the load.** Most requests never reach the database: the content view cache, the template-block
cache and, with Velocity, the response cache, which answers a repeat request without starting the application.
Under Velocity's persistent workers every worker keeps the application loaded and shares the one database file;
reads run side by side and writes queue on the writer lock (the driver reopens the lock in each forked worker).
In load tests of this driver, concurrent readers scaled with the number of processes, short writes stayed
error-free at 16 concurrent writers at the serial rate, and long transactions that failed with the earlier
driver's 5-second limit completed without errors, only waiting longer. A team where many editors publish large
changes at the same moment is still better served by MySQL, MariaDB or PostgreSQL.

**Ownership and permissions.** SQLite needs to write the database file, the `-wal` and `-shm` files beside it and
the directory that holds them; the driver creates `var/storage/sqlite3/` with mode `0775` but does not set the
mode or owner of the files, so they get the creating process's umask. When Apache/PHP-FPM, Velocity workers and
cronjobs run as different users, give them one group that can read and write `var/storage/sqlite3/` and every file
in it. A `.writer-lock` created by another user still works: the driver opens it read-only when it cannot open it
for writing. Never serve the database file over HTTP; check with
`curl -sI https://www.example.com/var/storage/sqlite3/exponential.db` (anything but 200 is right).

**Backups.** Use SQLite's online backup, never a plain copy of a busy file:

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup backup.db"
```

Restore by copying `backup.db` back with the site in maintenance mode (`php bin/php/maintenance.php on`, then
`off`). Run `ANALYZE` once after an install or a big import so the query planner has statistics
(`sqlite3 var/storage/sqlite3/exponential.db ANALYZE`).

More: [SQLite database support](features/6.0/sqlite-database.md),
[SQLite 3 driver](specifications/6.0/sqlite3-database-driver.md),
[SQLite transactions](bc/6.0/sqlite-transactions.md).

## 10. After installing

1. **Log in** at the admin address the installer printed (for example `/admin/user/login`) as `admin`. Change the
   password (**Change password** in the user menu) and delete `var/log/initial-admin-password`. A lost password is
   reset with `php bin/php/resetuserpassword.php -u admin -g`.
2. **Check the front page and the logs.** Open the site address. On an error read `var/log/error.log` and
   [Repairing an installation](bc/6.0/repair.md).
3. **Set up the cronjobs.** They publish scheduled content, send notifications and newsletters and clean up.
   List the parts, then add them to the crontab of the site user:

   ```bash
   php runcronjobs.php --list
   ```

   ```
   */5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent >> var/log/cron-frequent.log 2>&1
   17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent >> var/log/cron-infrequent.log 2>&1
   ```

   Use your public siteaccess name. The same parts are in the admin under **Setup > Cronjobs**, and Velocity's
   [scheduler](features/6.0/velocity-scheduler.md) can run them without the system cron.
4. **Know the cache commands.** Clear the smallest thing that fixes the problem:

   | You changed | Command |
   |---|---|
   | a setting (`.ini`) | `php bin/php/ezcache.php --clear-tag=ini` |
   | a template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template` |
   | content looks stale | `php bin/php/ezcache.php --clear-tag=content` |
   | added a template or an extension | `php bin/php/ezcache.php --clear-id=template-override` |
   | unsure | `php bin/php/ezcache.php --clear-all` |

   With Velocity, also `php bin/php/console exp:velocity cache clear`; after a class or INI change, `exp:velocity
   deploy` does all of it in order.
5. **Regenerate autoloads** after adding or renaming a class in an extension:
   `php bin/php/ezpgenerateautoloads.php -e` (`-k` for the kernel).
6. **Back up** the database and `var/storage`, `settings/override`, `settings/siteaccess`
   ([Operating a site](guides/operating-a-site.md#6-back-up-and-restore)).

Then continue with the guides:

- [Getting started](guides/getting-started.md): first login, first content, first template change.
- [The content model and editing content](guides/content-model-and-editing.md): classes, objects, the online editor, the trash and content jobs.
- [Templates and design](guides/templates-and-design.md): find the template that wrote a page, override it, switch the admin design.
- [Extensions](guides/extensions.md): find, install, configure, build and release an extension.
- [Remote services and apps](guides/remote-services-and-apps.md): call Exponential from the shell, from Python and with an API token.
- [Deploying](guides/deploying.md): serve the site with Apache and PHP-FPM, Velocity or FrankenPHP, over HTTPS.
- [Operating a site](guides/operating-a-site.md): caches, cronjobs, backups, logs and repairs.
- [Security and audit](guides/security-and-audit.md): check the hardening, roles and policies, the audit trail.
- Every guide: [the learning path](guides/README.md).

## 11. Upgrading from an older version

Do not run the installer over an existing site. Follow [Upgrading](guides/upgrading.md): from 4.x, 5.x or an
earlier 6.0.x to the current line, with the database update and the checklists per release.

The online manual has further installation notes:
https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html

## 12. Troubleshooting

| Symptom | Cause and fix |
|---|---|
| Every address shows the setup wizard | `CheckValidity` is still `true`: the install did not finish. Finish the wizard, or run the kickstarter or `exp:install`. |
| "The site is being set up" (503) | A wizard is running in another browser (the hold ends with its last page or 30 minutes after its last request), or a kickstarter run is rebuilding the database. If nothing is running: `php bin/php/maintenance.php status`, then `off`. |
| The wizard skips pages or shows values you did not enter | A `kickstart.ini` is in the installation root. Move it away. |
| The system check fails on directory permissions | Run the `chmod`/`chown` commands the page prints ([Permissions](#permissions)), then **Next**. |
| `The CreateSites step will modify the database and site settings` | The kickstarter needs `--force` to install. |
| `exp:install`: "This directory already holds an installation" | Add `--force` only if you mean to replace it; `--dry-run` checks without changing anything. |
| `kickstart.ini` values are ignored | Leading whitespace before sections or keys, a missing `Continue=true`, or a stale `var/cache/ini/kickstart-*.php`. |
| After switching database engine no page loads | `DatabaseImplementation` in `settings/override/site.ini.append.php` still names the old engine. Set it to the new one. |
| Pages render but images or sub-items are missing | The site package uses a locale that is not in `Languages[]`. Add it and install again with `DatabaseAction=remove`. |
| SQLite: "database is busy: the transaction could not start within N s" | Another write held the lock for the whole `SQLiteTransactionWait`. Nothing was written; retry, or raise the setting (below the request timeout). |
| 404 for every page under Apache | `.htaccess` is not read (`AllowOverride All`) or `mod_rewrite` is off; did you `cp .htaccess_root .htaccess`? |
| A white page or 500 | Read `var/log/error.log`; check `php -v` and that `vendor/` exists (`composer install`). |
| A change does not show under Velocity | Workers keep the old code: `exp:velocity restart` (or `deploy`), then `exp:velocity cache clear`. |
| The administrator password is lost | `php bin/php/resetuserpassword.php -u admin -g`, see [Reset a user password](features/6.0/reset-user-password.md). |

More: [Repairing an installation](bc/6.0/repair.md), [Maintenance mode](features/6.0/maintenance-mode.md).
