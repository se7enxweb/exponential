# 12. Troubleshooting

This chapter is a reference for problems during and after an installation. It starts with the general method (read
the log, turn on debug output, change one thing at a time), explains where every log lives and how to turn on the
debug output safely, covers permissions and ownership, and then lists symptoms by area: getting the code, the system
check, the setup wizard, the kickstarter and the console install, pages at run time, databases, Velocity and the web
server, caches, and signing in. Every message quoted here is copied from the code that prints it, with the file named,
so you can search for the exact text you see. Each row gives the cause and the fix.

[Contents](README.md) · Previous: [11. Upgrading](11-upgrading.md) · Next: [13. Security hardening](13-security-hardening.md)

## 12.1 Start here

1. **Read the message exactly.** Most errors in this chapter are quoted verbatim; search this page for a few words of
   yours.
2. **Read the log.** `var/log/error.log` first; the files are described in [12.2](#122-where-the-logs-are). A visitor's
   error page shows a reference such as `ERR-1A2B3C4D5E`; the same reference is in `error.log` beside the details.
3. **Turn on debug output for yourself only** ([12.4](#124-debug-output)) and reload the page.
4. **Change one thing, then clear the smallest cache that covers it** ([12.12](#1212-caches-and-changes-that-do-not-show)).
   Under Velocity, a changed class or setting also needs `exp:velocity restart` or `exp:velocity deploy`.
5. **On the command line, run as the installation's owner.** A script run as `root` creates files the web server
   cannot change later ([12.3](#123-permissions-and-ownership)).

## 12.2 Where the logs are

All paths are relative to the installation root. A log file is rotated when it passes 200 KB
(`eZLog::MAX_LOGFILE_SIZE = 204800`), and three old copies are kept (`.1` to `.3`, `eZLog::MAX_LOGROTATE_FILES`);
`config.php` can change both with `CUSTOM_LOG_MAX_FILE_SIZE` and `CUSTOM_LOG_ROTATE_FILES` (commented examples in
`config.php-RECOMMENDED`).

| File | Written by | What is in it |
|---|---|---|
| `var/log/error.log` | `eZDebug` (errors are always logged: `[DebugSettings] AlwaysLog[]=error`), the uncaught error handler | errors; "Unexpected error ERR-..., the message was : ..." with file and line for every error page a visitor saw |
| `var/log/warning.log`, `notice.log`, `debug.log`, `strict.log` | `eZDebug` | warnings, notices, debug and strict messages, when their type is in `AlwaysLog[]` or debug output is on |
| `var/log/setup.log` | the setup wizard, the kickstarter, `exp:install` | every step of an installation, with database connection failures |
| `var/log/kickstart.log` | the kickstarter (`kickstart.log.1` to `.9` keep earlier runs) | each kickstarter run, passwords masked; `EXP_KICKSTART_LOG=0` turns it off |
| `var/log/exp-install-<date>.ini` | `exp:install` | the configuration a console install used, passwords masked |
| `var/log/initial-admin-password` | the installers | the generated administrator password, readable by the owner only; delete it after the first login |
| `var/log/storage.log` | file storage | files written to `var/storage` (under `[FileSettings] VarDir`/`LogDir`) |
| `var/log/async.log` | asynchronous publishing | the publishing queue |
| `var/log/requestrules.log` | request rules | refused and logged requests |
| `var/log/audit/` | the audit trail (`settings/audit.ini`, `[AuditSettings] LogDir=log/audit`, relative to `[FileSettings] VarDir`) | hash-chained audit files; see [Audit](../bc/6.0/audit.md) |
| `var/vc/qbix/log/` | Velocity (`settings/velocity.ini`, `[LogSettings] Dir`) | `<siteaccess>-access.log` and `<siteaccess>-error.log`, named after `[SiteSettings] DefaultAccess`; dated when rotated |
| `var/vc/qbix/run/console.log` | Velocity (`[ServerSettings] LogFile`) | what the server process prints: start-up, warm-up and failures to start |
| `var/vc/frankenphp/log/` | the FrankenPHP engine | its `access.log` and `error.log` |
| the web server's and PHP-FPM's own logs | Apache, nginx, PHP-FPM | PHP fatal errors that happen before Exponential can log them |

Before September 2026 Velocity logged to `var/log/qbix/`; an installation whose `[LogSettings] Dir` says so keeps
that place.

A shell script's errors go to the terminal. Command line scripts add `-d` (or `--debug`) to print the debug output at
the end of the run (`-d all`, or one of `accumulator`, `include`, `timing`, `error`, `warning`, `debug`, `notice`,
`strict`) and `-v` for more information.

## 12.3 Permissions and ownership

The user the web server or Velocity's workers run as must be able to write `design`, `extension`, `settings`,
`settings/override`, `settings/siteaccess` and `var` with everything below it. The setup wizard tests the list in
`settings/setup.ini`, `[directory_permissions] CheckList` and, when it fails, shows "Insufficient directory
permissions" with commands for your machine, introduced as "These shell commands will give proper permission to the
web server." They have this form:

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

| Symptom | Cause | Fix |
|---|---|---|
| The system check fails on directory permissions | The web server user cannot write one of the directories | Run the commands the page prints, then press **Next**. |
| Pages worked, then errors appear after a cronjob or a console command | A script run as `root` (with `--allow-root-user`) created cache or storage files that belong to root | `chown -R <web user>:<web group> var`; run scripts as the installation's owner. |
| Apache/PHP-FPM, Velocity workers and cronjobs run as different users and each breaks the others' files | Files get the creating process's umask | Give them one group that can read and write `var/` (and `settings/override/` if settings are changed in the admin). |
| The script stops with "Running scripts as root may be dangerous." | `eZScript` refuses `root` (`kernel/classes/ezscript.php`) | Run as the owner, or append `--allow-root-user`. |
| SQLite: "The directory %directory cannot be written by the web server (user %user). SQLite needs to create the database file there, and the -wal and -shm files it keeps next to it." | The database directory is not writable | Give that user write access to `var/storage/sqlite3/` (create it first if needed). |
| SQLite: "The database file %file exists but cannot be written by the web server (user %user)." | The file belongs to another user | Fix the owner or mode of the file, or choose another file name. |
| Velocity workers cannot write | Workers run as `[ServerSettings] User` and `Group` of `settings/velocity.ini`; when empty, `VC_RUN_USER`/`VC_RUN_GROUP` or the owner of the document root | Set `User`/`Group`, or fix the ownership. Workers never run as root unless `AllowRootWorkers=enabled`. |

The `%directory`, `%file` and `%user` placeholders are filled in on the page (`kernel/setup/steps/ezstep_installer.php`).

## 12.4 Debug output

Debug output shows warnings, errors, timings, SQL and the templates used, at the bottom of each page (since
2 October 2026 as the pinned [Exp Debug bar](../features/6.0/exp-debug-bar.md)). It is off by default
(`settings/site.ini`, `[DebugSettings] DebugOutput=disabled`). Turn it on **for your own address only**, in
`settings/override/site.ini.append.php`:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Then clear the INI cache and reload the page:

```bash
php bin/php/ezcache.php --clear-tag=ini
```

Under Velocity, restart it as well (`php bin/php/console exp:velocity restart`), because the workers hold the settings.
The same can be done with [exp:ini](../features/6.0/exp-ini-command.md), which writes the override and records it:

```bash
php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
```

The switches (`settings/site.ini`, `[DebugSettings]` unless noted):

| Setting | Default | Effect |
|---|---|---|
| `DebugOutput` | `disabled` | the master switch |
| `DebugByIP`, `DebugIPList[]` | `disabled` | show debug only to these addresses or networks (`192.0.0.0/27`) |
| `DebugByUser`, `DebugUserIDList[]` | `disabled` | show debug only to these user ids |
| `Debug` | `inline` | `inline` in the page, or `popup` |
| `DebugRedirection` | `disabled` | stop at redirects with a page that shows the debug output |
| `DisplayDebugWarnings` | `disabled` | show warnings and errors in the page, not only in the debug output |
| `AlwaysLog[]` | `error` | which types are written to `var/log/` even with debug output off |
| `ScriptDebugOutput` | `disabled` | debug output for command line scripts (with `DebugOutput=enabled`) |
| `DebugToolbar` | `enabled` | the developer toolbar (clear caches, quick settings) for administrators |
| `[TemplateSettings] Debug`, `ShowUsedTemplates`, `ShowXHTMLCode` | | template debug, the list of templates used, template names inline in the HTML |
| `[DatabaseSettings] SQLOutput` | `disabled` | every SQL query in the debug output |

**Never leave debug output on for everyone on a public site.** It shows paths, queries and settings. With several
machines, put the debug settings in the development machine's settings only; see
[Settings per environment (EXP_ENV)](../features/6.0/environment-settings.md).

## 12.5 Getting the code and Composer

| Symptom | Cause | Fix |
|---|---|---|
| Composer: PHP version does not satisfy `phpunit/phpunit` | `require-dev` asks for PHPUnit 13 (`^13.4`), which needs PHP 8.4.1; without a lock file Composer resolves it even with `--no-dev` | On PHP 8.0 to 8.3: `composer remove --dev --no-update --no-interaction phpunit/phpunit zetacomponents/php-generator`, then `composer install --no-dev` ([chapter 3](03-getting-the-code.md#33-installing-on-php-80-to-83)). |
| Composer: the root package requires PHP 8.1 on PHP 8.0 | Releases `v6.0.8` to `v6.0.14` require PHP 8.1 | Use PHP 8.1 or later, or the 6.0.15 line (`dev-main`), which accepts 8.0. |
| Composer: `se7enxweb/exponential-velocity` requires PHP 8.1 | Velocity needs PHP 8.1 | Leave it out on PHP 8.0 (it is only suggested), or use a newer PHP. |
| Composer stops on a missing `ext-mongodb` | A package asks for the MongoDB extension | Install it, or, when you do not use MongoDB, add `--ignore-platform-req=ext-mongodb` (as the project's PHP 8.0 check does). |
| Composer asks whether to trust `se7enxweb/exponential-legacy-installer`, or refuses to run it | Composer 2.2+ runs only allowed plugins; your own `composer.json` (way C of chapter 3) does not list it | Answer yes, or `composer config allow-plugins.se7enxweb/exponential-legacy-installer true`. |
| After `composer require se7enxweb/exponential` the kernel files are in the current directory | That is how the installer plugin installs a package of type `ezpublish-legacy`: into the project root | Expected; see [chapter 3](03-getting-the-code.md#32-three-ways-to-get-the-code). |
| `Class ... not found` after Composer added an extension | The class maps were not regenerated (Composer runs only the root package's scripts) | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches. |
| An extension is in `extension/` but has no effect | Being on disk does not activate it | Add it to `[ExtensionSettings] ActiveExtensions[]` (or **Setup > Extensions**), then `php bin/php/ezcache.php --clear-tag=ini`. |

## 12.6 The setup wizard's system check

The headings below are those of `design/standard/templates/setup/tests/<test>_error.tpl`; the tests are described in
[chapter 2](02-requirements.md#24-the-setup-wizards-system-check).

| Message | Cause | Fix |
|---|---|---|
| "Insufficient directory permissions" | the web server cannot write a directory of `CheckList` | the commands on the page ([12.3](#123-permissions-and-ownership)) |
| "Missing database handler" ("No supported database handlers were found. ...") | none of `sqlite3`, `mysqli`, `pgsql`, `mongodb`, `oci8` is loaded | install one; `sqlite3` needs no server |
| "Missing image conversion support" | neither GD's `imagegd2()` nor ImageMagick's `convert` | install `gd` or ImageMagick |
| "Insufficient memory allocated to install Exponential" ("Exponential will not work correctly with a memory limit of %1.") | `memory_limit` below `64M` | raise it in the `php.ini` the web server reads, then reload PHP |
| "Insufficient execution time allowed to install Exponential" | `max_execution_time` below 30 | raise it, then reload PHP |
| "allow_url_fopen ini setting is disabled" | `allow_url_fopen=Off` | turn it on |
| "File uploading is disabled" | `file_uploads=Off`, or the upload directory is missing or not writable | turn uploads on; check `upload_tmp_dir` (empty means `TMPDIR` or `/tmp`) |
| "Missing Session Extension" | no `session` | install it |
| "Missing zlib extension", "Missing DOM extension", "Missing iconv extension", "Missing MBString extension", "Missing intl extension", "Missing xsl extension" | the extension is not loaded in the web server's PHP | install it for that PHP (see [chapter 2](02-requirements.md#29-operating-system-notes)) and reload PHP |
| "Time zone configuration" ("You are using the default time zone, UTC. ...") | `php.ini` has no `date.timezone`, so `index.php` fell back to UTC | set `date.timezone`; to keep UTC on purpose, set it to `UTC` or tick **Ignore this test** |
| "Wrong eZ Components version detected" or "Missing eZ Components dependancy" | the Zeta Components are missing (`ezcBaseFile` not found) | `composer install` |
| "Unstable PHP version" | a PHP listed in `[phpversion] UnstableVersions` | use another PHP |
| "Missing cURL extension", "Missing imagegd2 extension", "Missing ImageMagick program", "Missing text creation functions", "Missing database handlers" | optional tests on **System finetuning** | install them, or press **Next** |
| "PHP does not register environment variables" | `variables_order` lacks `E` | set it to `EGPCS` |
| the `open_basedir` warning | `open_basedir` is set | leave it empty if you meet problems |

The wizard runs in the **web server's** PHP. A test that fails in the browser while `php -m` on the command line looks
fine means the two PHPs differ: install the extension for the web PHP (on Plesk, the domain's PHP version) and reload
it.

## 12.7 The setup wizard

| Symptom or message | Cause | Fix |
|---|---|---|
| Every address shows the setup wizard | `[SiteAccessSettings] CheckValidity` is still `true`: no install has finished | Finish the wizard, or install with the kickstarter or `exp:install`. The last step writes `CheckValidity=false` into `settings/override/site.ini.append.php`. |
| "The site is being set up" (HTTP 503) | A wizard is running in another browser; only the browser holding the cookie `exp_setup_wizard` gets through. Or a kickstarter run is rebuilding the database | Wait: the hold ends with the wizard's last page, or 30 minutes after its last request (`expMaintenance::WIZARD_LEASE = 1800`). If nothing runs: `php bin/php/maintenance.php status`, then `php bin/php/maintenance.php off`. |
| The wizard skips pages, or shows values you did not enter | A `kickstart.ini` is in the installation root; sections with `Continue=true` are skipped, the others pre-filled | Move `kickstart.ini` away to answer every page yourself. |
| "The database would not accept the connection, please review your settings and try again." | MySQL/MariaDB refused: wrong host, port, user, password, or the server is not running | Check the values; test them with the `mysql` client; see `var/log/setup.log`. |
| "Could not connect to the PostgreSQL database. ..." | PostgreSQL refused the connection | Check server, port, user, password and that the database exists; allow the host in `listen_addresses` (`postgresql.conf`) and `pg_hba.conf`. |
| "The 'digest' function is not available in your database, and Exponential cannot run without it. ..." | PostgreSQL lacks `pgcrypto` and the wizard could not create it | Install the server's contrib package if needed; the database owner or a superuser runs `CREATE EXTENSION pgcrypto;`; press **Next**. |
| "Your database version %version does not fit the minimum requirement which is %req_version." | The server is older than the minimum in `kernel/setup/ezsetupcommon.php` | Use a newer database server. |
| "The database [...] cannot be used, the setup wizard wants to create the site in [...] but the database has been created using character set [...]." | The MySQL database is not UTF-8 | Create the database with a UTF-8 character set, or convert it. |
| "The selected database was not empty, please choose from the alternatives below." | The database holds tables | Choose to remove the data, keep it, or choose another database. Removing deletes everything in it. |
| "The selected user has not got access to any databases. Change user or create a database for the user." | The user may not see any database | Create the database and grant the user access. |
| "The database file name is not valid. Give a plain file name ending in .db, .db3, .sqlite or .sqlite3, ..." | SQLite file name with a path or wrong ending | A plain name such as `exponential.db`; the file lives in `var/storage/sqlite3/`. |
| "The file %file exists and is not a SQLite database. Choose another file name; the setup does not overwrite it." | Another file has that name | Choose another name. |
| "The SQLite database file %file could not be opened. See var/log/setup.log and var/log/error.log for the reason." | Permissions, a damaged file or a missing `sqlite3` extension | Read the two logs; see [12.3](#123-permissions-and-ownership). |
| "Password entries did not match." | The two password fields differ | Type them again. |
| "The primary language %1 is not a language this installation has a locale for (share/locale)." | A locale code without a file in `share/locale/` | Choose a listed language. |
| "%1 is the primary language and cannot also be an additional language. ..." | The same language twice | Uncheck it among the additional languages. |
| "Could not fetch site package: '...'" or "Failed to initialize site package '...'" | The site package could not be downloaded or read | Check the network access to the package repository configured in `settings/package.ini`; read `var/log/setup.log`. |
| "The editor siteaccess could not be made from settings/siteaccess/<admin>" | The admin siteaccess files were not written | Check write access to `settings/siteaccess/`; see `var/log/setup.log`. |
| "Failed loading database schema file share/db_schema.dba" or "Failed loading database data file share/db_data.dba" | The installation's `share/` is incomplete | Get the code again (chapter 3). |
| Pages render but images or sub-items are missing after the install | The site package uses a locale that is not among the installed languages | Add it to the languages and install again. |

All messages of this table are in `kernel/setup/steps/ezstep_installer.php`, `ezstep_create_sites.php` and
`kernel/setup/ezsetupcommon.php`.

## 12.8 The kickstarter and the console install

| Symptom or message | Cause | Fix |
|---|---|---|
| "The CreateSites step will modify the database and site settings." / "Re-run with --force to confirm you want to install the site package." | The kickstarter writes the database only with `--force` (`kernel/classes/expkickstarter.php`) | `php bin/php/console exp:kickstarter run --force`, after checking `DatabaseAction`. |
| "Unknown start step: ..." or "Unknown stop step: ..." | A step name that does not exist | `php bin/php/console exp:kickstarter run --list-steps` lists them. |
| "This directory already holds an installation (settings/override/site.ini.append.php)." | `exp:install` found a configured database (`bin/php/install.php`) | Add `--force` only to replace that installation; `--dry-run` checks without changing anything. |
| `kickstart.ini` values are ignored | Leading whitespace before a section or key (`kickstart.ini-dist`: "Remove all leading whitespaces"), a missing `Continue=true`, or stale cached values | Remove the whitespace; set `Continue=true`; delete `var/cache/ini/kickstart-*.php` (the `ini` and `run` subcommands do it themselves). |
| The install went into the wrong database engine | `DatabaseImplementation` in `settings/override/site.ini.append.php` still names the old engine; the installer keeps the value it finds there | Set it to the new engine before installing again. |
| Everything in the database is gone after an install | `DatabaseAction=remove` (the default of `exp:install`, `--db-action=remove`) empties the named database first | Restore from backup. Point installs only at databases that hold nothing you need. |
| The password you gave was replaced | Passwords shorter than `[UserSettings] MinPasswordLength` or well-known ones are replaced with a generated one | Read it from the summary or `var/log/initial-admin-password`. |

## 12.9 Pages and errors at run time

| What the visitor sees | Cause | Fix |
|---|---|---|
| "The site is missing the software libraries it needs" (HTTP 503) | `vendor/` is missing, moved or renamed (`lib/ezutils/classes/ezexecution.php`, `errorCase()`) | Put `vendor/` back, or run `composer install` (`--no-dev` on production); then `php bin/php/ezcache.php --clear-all` and, under Velocity, restart it. See [Repairing an installation](../bc/6.0/repair.md). |
| "Something went wrong on our side" (HTTP 500) with a reference `ERR-...` | An uncaught error | Find the reference in `var/log/error.log`; the line names the message, file and line. With debug output on, the page shows the detail itself. |
| "We'll be right back" (HTTP 503, `Retry-After: 30`) | The database could not be reached: refused, unknown host, wrong password, too many connections, gone away (`eZExecution::isDatabaseUnavailable()`) | Check that the database server runs and the credentials in `settings/override/site.ini.append.php`; the log line holds the driver's message, for PostgreSQL "Unable to connect to the database server '<host>'". |
| "Down for maintenance" (HTTP 503) | Maintenance mode is on | `php bin/php/maintenance.php status`; `php bin/php/maintenance.php off` when the work is done. |
| "This form has expired" (HTTP 403) | The form's token was missing or wrong: the page was open a long time, the session ended, or the form came from another page | Reload the form and send it again. Nothing was saved. See [Form expired page](../features/6.0/form-expired-page.md). |
| A blank page, or 500 with nothing in `error.log` | PHP failed before Exponential could log: a syntax error, a missing PHP extension, memory exhausted | Read the web server's or PHP-FPM's error log; run `php -l` on a changed file; check `php -v` and `vendor/`. |
| 404 for every page under Apache | `.htaccess` is not read (`AllowOverride All` missing) or `mod_rewrite` is off | `cp .htaccess_root .htaccess`; allow overrides; enable `mod_rewrite`. |
| An old page appears every few reloads | The browser's service worker replays a page it cached (the site's worker is `/index.js`, loaded by `/sw.js`) | Keep `/sw.js` in place; a reload after the new worker activates (cache version `exp-nav-v4`) fixes it, or clear the site data in the browser. See [Behaviour changes of 16 to 30 September 2026](../bc/6.0/behaviour-changes-2026-09b.md). |
| The command line prints "An unexpected error has occurred. Please contact the webmaster." | An uncaught error in a script | Run it again with `-d all`; read `var/log/error.log`. |

## 12.10 Databases

| Symptom or message | Cause | Fix |
|---|---|---|
| SQLite: "database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written" | Another write held the writer lock for the whole `[DatabaseSettings] SQLiteTransactionWait` (60 s shipped) | Nothing was written; retry. Raise the setting (below the web server's request timeout), or move a busy editorial site to MySQL, MariaDB or PostgreSQL. |
| SQLite: the site is slow after an install or a large import | The query planner has no statistics | `sqlite3 var/storage/sqlite3/exponential.db ANALYZE` once. |
| SQLite: the database file can be downloaded | The web server serves `var/` | Check with `curl -sI https://www.example.com/var/storage/sqlite3/exponential.db` (anything but 200 is right); use the shipped `.htaccess` rules or Velocity. |
| After switching the database engine no page loads | `DatabaseImplementation` still names the old engine | Set it in `settings/override/site.ini.append.php`, then `php bin/php/ezcache.php --clear-tag=ini`. |
| MySQL: `SET storage_engine=InnoDB;` fails in an update file | An update file from before October 2026: that spelling was removed in MySQL 5.7.5 and MariaDB 12.0 | Take the current file, which says `SET default_storage_engine=InnoDB;`; see [chapter 11](11-upgrading.md#113-the-update-files). |
| PostgreSQL: errors about `digest` | `pgcrypto` is missing | `CREATE EXTENSION pgcrypto;` as owner or superuser. |
| MongoDB: subtree and URL alias queries are slow | The indexes were not created | `mongosh "mongodb://<user>:<password>@localhost:27017/<database>" --file bin/mongodb/create_indexes.js`; see [MongoDB](../features/6.0/mongodb-database-support.md). |
| MongoDB: `Class "MongoDB\Client" not found` | The PHP library `mongodb/mongodb` is not installed | `composer require mongodb/mongodb`; the `mongodb` PHP extension must be loaded too. |
| Oracle: the driver is not found | The `ezoracle` extension is not in `extension/` or not active | It is required by `composer.json`; check `extension/ezoracle` and that `oci8` is loaded. |

## 12.11 Velocity and the web server

| Symptom or message | Cause | Fix |
|---|---|---|
| `velocity: no server script at ...` | The `qbix` engine needs the Velocity package, which is not installed | `composer require se7enxweb/exponential-velocity:~0.0.4.42` (PHP 8.1+), or use `--engine=php` for development. |
| `velocity: already running` | A server of that engine runs already | `php bin/php/console exp:velocity status`; `restart` to apply changes. |
| `velocity: port ... is already in use by another process (another engine? exp:velocity stop --engine=qbix\|frankenphp)` | Another program or engine holds the port (php and frankenphp engines) | Stop the other engine, or change `Port`/`HTTPSPort` with `exp:velocity config set`. |
| `velocity: did not start; see <log>` | The server failed at start-up | Read the named log (`var/vc/qbix/run/console.log` for qbix). |
| `velocity deploy: FAIL after Ns; the steps after the failed one were not run` | One step of `exp:velocity deploy` failed | The output names the step; fix it and run `deploy` again (`--dry-run` lists the steps). |
| A PHP, template or settings change does not show under Velocity | Workers keep the application in memory | `exp:velocity restart` (or `deploy`), then `exp:velocity cache clear`. |
| Ports 80 and 443 cannot be bound | Ports below 1024 need root | Start Velocity as root (the workers drop to `[ServerSettings] User`), or use ports above 1024 behind a proxy. |
| HTTPS shows a self-signed certificate | The configured certificate is missing, expired or does not match its key; Velocity falls back to a self-signed one | `php bin/php/console exp:velocity ssl show`; fix `[HTTPSSettings] Certificate` and `Key`. See [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md). |
| A PHP change does not show under Apache or nginx | The PHP-FPM that serves the site was not reloaded; on servers with several PHP versions it is not always `php-fpm` (Plesk: `plesk-php<XY>-fpm`) | Reload the right service; `exp:velocity deploy` finds it with `[DeploySettings] PhpFpmService=auto`. |
| Under Velocity alone, `deploy` tries to reload PHP-FPM | `PhpFpmService=auto` | Set `[DeploySettings] PhpFpmService=disabled`. |

## 12.12 Caches and changes that do not show

Clear the smallest cache that covers the change (`php bin/php/ezcache.php --list-tags` and `--list-ids` list them):

| You changed | Command |
|---|---|
| a setting (`.ini`) | `php bin/php/ezcache.php --clear-tag=ini` (not `--clear-id=ini`, which leaves the global INI cache) |
| a template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template` |
| added a template file, an override or an extension | `php bin/php/ezcache.php --clear-id=template-override` |
| a stylesheet or script packed by `ezjscore` | `php bin/php/ezcache.php --clear-id=ezjscore-packer` and `--clear-id=template-block` |
| content looks stale | `php bin/php/ezcache.php --clear-tag=content` |
| a PHP class, under Velocity | `php bin/php/console exp:velocity deploy` (or `restart`) |
| unsure | `php bin/php/ezcache.php --clear-all` |

Under Velocity also clear its response cache, **after** the restart: `php bin/php/console exp:velocity cache clear`.
A page rendered by the old code between a cache clear and the restart is cached again and served as if the change had
not worked. More in [Cache console](../bc/6.0/cache-console.md) and [Operating a site](../guides/operating-a-site.md).

| Symptom | Cause | Fix |
|---|---|---|
| A new template file renders an empty area (HTTP 200) | The template override cache predates the file | `--clear-id=template-override`. |
| `Class ... not found` after adding or renaming a class | The class maps are stale | `php bin/php/ezpgenerateautoloads.php -e` (`-k` for the kernel), then clear the caches; under Velocity restart it. |
| "The audit index classes are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first." | `createaudittables.php` ran before the kernel class map was regenerated | Run `php bin/php/ezpgenerateautoloads.php -k`, then the script again. |

## 12.13 Signing in

| Symptom | Cause | Fix |
|---|---|---|
| The administrator password is lost | | `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` as root (the root bypass), or with an administrator's login (`-a <login> -ap <password>`); `-p <password>` sets a given one. See [Reset a user password](../features/6.0/reset-user-password.md). |
| The generated password is unknown | It was printed once at the end of the install | `var/log/initial-admin-password` (readable by the owner only); delete the file after the first login. |
| An account made by SQL or a migration cannot sign in | Since 16 August 2026 a user without an `ezuser_setting` row counts as disabled | Add the row; the queries are in the [August 2026 security specification](../specifications/6.0/security-hardening-2026-08.md#f-06-find-accounts-that-cannot-sign-in). |
| Signed in, but the next page is signed out (behind a proxy or on mixed HTTP and HTTPS) | Session cookie attributes: `[Session] CookieSecure=auto` marks the cookie `Secure` when the request came over HTTPS; `CookieSameSite=Lax` | Serve the whole site over one scheme; when the admin is framed or posted to from another site, set `CookieSameSite=None` together with `CookieSecure=true`. See [Security defaults](../specifications/6.0/security-defaults-2026-09.md). |

## 12.14 Getting help

- Search this documentation: the [guides](../guides/README.md), the [feature pages](../features/6.0/),
  the [behaviour change notes](../bc/6.0/) and the [glossary](../glossary.md).
- The online manual: [doc.exponential.earth](https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html).
- Report a bug: the [Exponential issue tracker](https://issues.exponential.earth), or the
  [community issue tracker](https://github.com/se7enxweb/exponential-community/issues).
- Report a security issue privately, as [SECURITY.md](../../SECURITY.md) describes, never in a public issue.
- Forums: [share.exponential.earth/forums](https://share.exponential.earth/forums).

When you report a problem, include the version (`php bin/php/console --version`), `php -v`, the database and its
version, the engine that serves the site, the exact message and the matching lines of `var/log/error.log`. Remove
passwords and personal data from logs before you share them.

## References

In this repository:

- [Installation overview](../INSTALL.md), section 12.
- [Repairing an installation](../bc/6.0/repair.md) and [Repair from the browser](../features/6.0/repair-from-the-browser.md).
- [Maintenance mode](../features/6.0/maintenance-mode.md), [Form expired page](../features/6.0/form-expired-page.md),
  [Reset a user password](../features/6.0/reset-user-password.md).
- [The Exp Debug bar](../features/6.0/exp-debug-bar.md), [debug bar reference](../bc/6.0/debug-bar.md),
  [Settings per environment](../features/6.0/environment-settings.md), [exp:ini](../features/6.0/exp-ini-command.md).
- [Cache console](../bc/6.0/cache-console.md), [Operating a site](../guides/operating-a-site.md),
  [Deploying](../guides/deploying.md).
- [SQLite database support](../features/6.0/sqlite-database.md), [SQLite transactions](../bc/6.0/sqlite-transactions.md),
  [MongoDB](../features/6.0/mongodb-database-support.md).
- [Velocity engines](../bc/6.0/velocity-engines.md), [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md),
  [Velocity on-disk layout](../bc/6.0/velocity-ondisk-layout.md).
- [Audit](../bc/6.0/audit.md), [Security defaults](../specifications/6.0/security-defaults-2026-09.md),
  [Security and audit guide](../guides/security-and-audit.md).
- [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md).
- Code: `kernel/setup/ezsetuptests.php`, `kernel/setup/steps/ezstep_installer.php`,
  `kernel/setup/steps/ezstep_create_sites.php`, `kernel/classes/expkickstarter.php`, `bin/php/install.php`,
  `lib/ezutils/classes/ezexecution.php`, `lib/ezutils/classes/ezdebug.php`, `lib/ezfile/classes/ezlog.php`,
  `kernel/classes/expmaintenance.php`, `kernel/classes/expvelocity.php`, `design/standard/templates/setup/tests/`.

External:

- PHP: [error handling and logging](https://www.php.net/manual/en/errorfunc.configuration.php),
  [php.ini directives](https://www.php.net/manual/en/ini.list.php),
  [List of Supported Timezones](https://www.php.net/manual/en/timezones.php),
  [PHP-FPM configuration](https://www.php.net/manual/en/install.fpm.configuration.php).
- Composer: [troubleshooting](https://getcomposer.org/doc/articles/troubleshooting.md),
  [platform requirements](https://getcomposer.org/doc/03-cli.md#check-platform-reqs).
- Apache: [mod_rewrite](https://httpd.apache.org/docs/current/mod/mod_rewrite.html),
  [AllowOverride](https://httpd.apache.org/docs/current/mod/core.html#allowoverride).
- Databases: [PostgreSQL client authentication](https://www.postgresql.org/docs/current/client-authentication.html),
  [pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html),
  [SQLite command line shell](https://sqlite.org/cli.html), [SQLite WAL](https://sqlite.org/wal.html).
- [Exponential issue tracker](https://issues.exponential.earth),
  [community issues](https://github.com/se7enxweb/exponential-community/issues).

[Contents](README.md) · Previous: [11. Upgrading](11-upgrading.md) · Next: [13. Security hardening](13-security-hardening.md)
