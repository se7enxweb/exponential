# Installing Exponential in one command

You can have a working Exponential site in a few minutes, without writing a
`kickstart.ini` and without a database server.

```bash
./console exp:install
```

That installs the multisite package on **SQLite**, in `eng-US`, with the
siteaccesses `site` and `admin` reached by URL, and prints a summary with the
site and administration login addresses, the user name, the e-mail, database,
package and siteaccesses, plus a short block you can copy and paste. `exp:install`
is the same as `php bin/php/install.php`.

## Choose a database

| `--db=` | Aliases | Default port | Default database name |
|---|---|---|---|
| `sqlite` (default) | `sqlite3` | none | `exponential.db` |
| `mysql` | `mysqli`, `mariadb` | 3306 | `exponential` (user `root`) |
| `pgsql` | `postgresql`, `postgres` | 5432 | `exponential` (user `postgres`) |
| `mongodb` | `mongo` | 27017 | `exponential` |
| `oracle` | `oci8`, `ezoracle` | 1521 | service `FREEPDB1` |

For Oracle the connect string is built as `host:port/service`; a full connect
string or an `@alias` (TNS) is used as given. The Oracle driver lives in the
`ezoracle` extension; the installer loads that extension's settings for the run,
puts it first in the new site's `ActiveExtensions`, and talks UTF-8 to Oracle
during the checks.

```bash
./console exp:install --db=mysql --db-host=127.0.0.1 --db-name=mysite \
                      --db-user=root --db-action=remove
EXP_INSTALL_DB_PASSWORD='...' ./console exp:install --db=pgsql
```

Give the database password with `--db-password` or, better, with the
environment variable `EXP_INSTALL_DB_PASSWORD`, which keeps it out of the
process list and shell history.

## All options

| Group | Options |
|---|---|
| Database | `--db`, `--db-host`, `--db-port`, `--db-name`, `--db-user`, `--db-password`, `--db-socket`, `--db-action` |
| Site | `--package`, `--language`, `--languages`, `--title`, `--url`, `--access` (`url`, `host` or `port`), `--site-access`, `--admin-access`, `--host`, `--admin-host`, `--port`, `--admin-port` |
| Administrator | `--email`, `--password`, `--random-password`, `--first-name`, `--last-name` |
| Control | `--force`, `--dry-run`, `--print` |

- Without options the administrator is `admin` with the password `publish` and
  the e-mail `nospam@exponential.earth` (from `./console exp:install --help`).
- `--random-password` generates a 24-character password and prints it once in
  the summary. Use it; the default password exists only so a first try works.
- It refuses to run over an existing installation unless you add `--force`.
- `--dry-run` checks the configuration and the packages and installs nothing.
- `--print` shows the configuration it would use, passwords masked.
- A `kickstart.ini` already present is moved aside for the run and put back
  afterwards. The configuration used is kept in `var/log` with passwords
  masked.

## Safer first login

A kickstart installation no longer ends with a guessable administrator
password. When the kickstart file sets no password, or one of the well-known
defaults, setup generates a random 20-character password, prints it once on the
console and writes it to `var/log/initial-admin-password` (readable by the owner
only). Change it after the first login and delete the file. The password is
applied even when no administrator e-mail is given; the shipped example and
dist kickstart files no longer carry a password.

## What the logs tell you

| File | Content | Settings |
|---|---|---|
| `var/log/setup.log` | One file per run of the kickstarter or web wizard: introduction, environment, every step with its duration and the errors and warnings it caused (repeats grouped with count, first and last time, a hint beside known problems), health checks after the install (database, declared tables, content classes, objects, content root, name lists, settings, siteaccesses, file ownership) and a framed RESULT and NEXT line. Earlier runs rotate to `setup.log.1`, `.2` ... | `EXP_SETUP_LOG_DIR` moves it elsewhere (tests use this so a real log is not rotated away). |
| `var/log/kickstart.log` | Everything the kickstarter printed (stdout and stderr), with `kickstart.ini` passwords masked and the start time, command and exit status around it. Nine earlier runs are kept as `kickstart.log.1` to `.9`. | `EXP_KICKSTART_LOG=0` turns it off. |
| `var/log/error.log` | While a run goes on it shows `BEGIN` and `END` entries with the run id and result; every entry ends with the run and step it came from, and an entry written several times in a row is written once with its count. | |

`setup.log` counts only what the installation itself logged. An error another
process wrote meanwhile (for example a visitor's request that reached the site
while its database was being built) is listed as "(not this run)", left out of
the counts and summed up in a NOTE line.

A failed query is now logged once, with the database's reason first and the
statement on one line. A site package's post-install reports the step it
stopped on (`eZSiteInstaller::abortedStep()`).

## Defaults that changed

- **SQLite first.** The web setup wizard lists SQLite first and preselects it,
  and the interactive kickstarter offers it first and uses it as the default
  `Type` of `[database_choice]`. The wizard checks the SQLite database file and
  asks for nothing a server needs. A kickstart `database_init` block without
  server, port, user, password or socket raises no warnings.
- **The bundled site is preselected**, so pressing Next on every step installs
  the default site. Before, a person who accepted every default got "No site
  package chosen".
- **Primary language `eng-US`** in both the wizard and the kickstart editor,
  the language the bundled data is in. Other primary languages install cleanly:
  the package language step no longer offers the clean data's own language for
  mapping, creating the language is the default for other package languages
  (mapping relabels content without translating it), and the site package
  receives `primary_language`, `extra_language_codes` and `language_map` by
  name. Siteaccesses made for two locales of one language get different names
  (`eng` and `eng_gb`). See [Translations and languages](translations-and-languages.md).
- **`DatabaseImplementation` is not derived from `Type`.** The kickstart
  `Type` chooses the driver the installer connects with; the
  `DatabaseImplementation` written to `settings/override/site.ini.append.php`
  comes from the live `site.ini`, because a driver has several names and a site
  may register its own alias. When you install onto a different engine, set the
  implementation first or the new site speaks the old engine's protocol to the
  new server.
- **GD first for images.** `settings/image.ini [ImageConverterSettings] ImageConverters[]` lists GD before
  ImageMagick; ImageMagick stays the fallback for formats and filters GD lacks
  (PSD, TIFF, PDF, WebP, flatten, swirl, noise) and when GD is missing.
- **No links to the former product's website** in a new installation's link
  list, and `ActiveExtensions` lists each extension once with the site
  package's `SiteURL` kept.
- **Maintenance mode** wraps every run: see [Maintenance mode](maintenance-mode.md).

## Related pages

- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [Kickstarter on the command line](../../bc/6.0/kickstartercli.md)
- [SQLite database](sqlite-database.md) and [database drivers](../../specifications/6.0/database-drivers-sqlite-oracle.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Database drivers and installers, September 2026](../../specifications/6.0/database-drivers-2026-09.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

## See also

- [Kickstarter: install a whole site from one file](kickstarter-cli.md) (the file-driven variant of the same installer)

## Related pages

- [The setup wizard's new look, and the editor siteaccess](setup-wizard-and-editor-siteaccess.md)
- [January 2024, second half (16 to 31 January)](../../history/2024/2024-01b.md)
