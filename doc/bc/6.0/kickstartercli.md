# Kickstarter CLI

Read this page if you install Exponential from the command line, script installations, or reinstall test sites.
`bin/php/kickstarter.php` (console: `php bin/php/console exp:kickstarter`, new in Exponential 6.0.15) runs the setup
wizard without a browser: it reads every wizard answer from `kickstart.ini`.

## In short

| | |
|---|---|
| What changed | New command with two subcommands: `ini` writes `kickstart.ini` from `kickstart.ini-dist`, `run` executes the wizard steps. `run` refuses the destructive `CreateSites` step without `--force`; `run --dry-run` tests the database connection and the package download without writing anything. In kickstart mode a site package that is already in `var/storage/packages/` is used as it is; only a missing one is downloaded from the remote repository. `eZPackage::import()` gains an optional fifth argument `$skipExisting`. |
| Who is affected | Anyone who installs sites. Existing callers of `eZPackage::import()` are unchanged (the new argument defaults to `false`). A `kickstart.ini` left in the project root is also read by the web setup wizard. |
| How to check | `php bin/php/kickstarter.php run --list-steps` (changes nothing) |
| How to fix | Nothing to fix. Remove or move `kickstart.ini` after a CLI install if the web wizard must not use it. Before an install onto another database engine, set `DatabaseImplementation` in `settings/override/site.ini.append.php` (see [`database_choice`](#database_choice)). |

## Prerequisites

Run all commands from the project root (the directory that contains `index.php`, `autoload.php`, `bin/`, `kickstart.ini`, etc.).

```bash
# Confirm you are in the project root
ls index.php autoload.php kickstart.ini-dist
```

You need:

- **PHP 8.1 or later** on your `$PATH`.
- A database server already running and reachable (MySQL, PostgreSQL, SQLite 3, or MongoDB).
- The `var/cache/ini/` directory must be writable because `kickstart.ini` is cached there.


## Quick start

```bash
# Generate an interactive kickstart.ini
php bin/php/console exp:kickstarter ini

# Test the remote packages and configuration (safe, no --force)
php bin/php/console exp:kickstarter run --dry-run

# Install the site (requires --force)
php bin/php/console exp:kickstarter run --force
```

For an unattended install with sensible defaults:

```bash
php bin/php/console exp:kickstarter ini --yes
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```


## Command reference

### `exp:kickstarter ini` — generate `kickstart.ini`

Builds the `kickstart.ini` file that `exp:kickstarter run` will consume. It reads `kickstart.ini-dist` (all commented-out examples) and writes a filled `kickstart.ini` in the project root.

```bash
php bin/php/console exp:kickstarter ini
php bin/php/kickstarter.php ini
```

#### Options

| Option | Short | Description |
|--------|-------|-------------|
| `--defaults` | `-d` | Copy `kickstart.ini-dist` values verbatim into `kickstart.ini` and exit. |
| `--yes` | `-y` | Accept the built-in sensible defaults and write `kickstart.ini` without prompting. |
| `--from-installed` | | Take the database server, name and user (never the password) of an installed siteaccess as defaults. Off by default. |
| `--help` | `-h` | Show help. |

The `--yes` defaults are loaded from `kernel/classes/expkickstarterini.php`. They take nothing from the settings of an installed site: those name a live database, and a `kickstart.ini` written from them would install over it. With `--from-installed` the database server, name and user of the first installed siteaccess that names a database become the defaults; its password is never copied. The default `Site_package` is `sevenx_multisite`, the package `exp:install` installs.


### `exp:kickstarter run` — run the setup wizard

Executes the setup wizard steps from `welcome` through `final` using the values in `kickstart.ini`. A configured site package that is already in `var/storage/packages/` is used as it is; only a missing package (or a missing dependency) is downloaded from the remote repository. Delete the local copy first if you want a fresh download.

```bash
php bin/php/console exp:kickstarter run
php bin/php/kickstarter.php run
```

#### Options

| Option | Description |
|--------|-------------|
| `--start-step=<step>` | First step to run (default: `welcome`). A later start is refused when a step in the run needs results of steps before it (see [Resuming](#resuming-after-a-failure)). |
| `--stop-step=<step>` | Last step to run (default: `final`). |
| `--dry-run` | Validate `kickstart.ini`, run `DatabaseChoice` through `SiteDetails` (database connection, package download), then stop. Writes no password, sends no mail, installs nothing. |
| `--list-steps` | List setup steps and exit. |
| `--force` | Required when the step range includes `CreateSites`, because `CreateSites` modifies the database and site settings. |
| `--help` | Show help. |

The standard script options (`--allow-root-user`, `--no-colors`, `--quiet`, `--debug`, `--verbose`, `--siteaccess`, `--logfiles`) are accepted and ignored; the kickstarter always runs on the plain siteaccess.

#### `CreateSites` and `--force`

`CreateSites` is the step that drops / inserts the database schema, installs the site package and required packages, and writes the `settings/siteaccess/` files. Because it is destructive, `exp:kickstarter run` will abort when this step is included and `--force` is not provided:

```
The CreateSites step will modify the database and site settings.
Re-run with --force to confirm you want to install the site package.
```

To run the configuration and package steps and stop before anything is installed, stop at the step before `CreateSites` (no `--force` needed):

```bash
php bin/php/console exp:kickstarter run --stop-step=Registration
```

`--stop-step=CreateSites` does the opposite: it includes `CreateSites`, so it installs (and needs `--force`), and only leaves out `Final`.

#### Resuming after a failure

A run cannot pick up where an earlier one stopped. The steps keep what they found out (the database type and connection, the chosen package and its languages, the site access values, the system check's ImageMagick result, the administrator) in the running process only; `kickstart.ini` holds the answers, not those results. So a later `--start-step` is refused before anything runs when a step in the range needs an earlier one, and the message names the earliest step that works:

```
--start-step=SiteDetails cannot work: the steps SiteDetails..Final need what Welcome, EmailSettings, DatabaseChoice, DatabaseInit, LanguageOptions, SiteTypes, PackageLanguageOptions, SiteAccess found out earlier in the same run (...). Start at Welcome or earlier: --start-step=Welcome.
```

Any range that includes `CreateSites` must start at `Welcome`. After a failure, fix the cause in `kickstart.ini` and run the whole installation again:

```bash
php bin/php/console exp:kickstarter run --force
```

### Remote package handling

`exp:kickstarter run` is designed to install the site package from the remote package repository configured in `package.ini` (e.g. `https://exponential.packages.exponential.earth/exponential/6.0/6.0.14/index.xml`). The remote `index.xml` lists packages with a `version` attribute, but that attribute does **not** match the version reported by the package's own `package.xml`.

For example, `sevenx_site` in the remote index has `version="1.0.0-0"`, while the downloaded `.ezpkg` contains:

```xml
<version>6.0.10-stable</version>
<version>
  <number>6.0</number>
  <release>10-stable</release>
</version>
```

So `eZPackage::getVersion()` returns `6.0-10-stable`. A naive `version_compare('1.0.0-0', '6.0-10-stable')` would decide the local copy is newer and skip the download. This is an unfortunate API data convention that the kickstart workflow has to accept.

When `kickstart.ini` has a `[site_types]` section, `eZStepSiteTypes::createSitePackagesList()` sees `hasKickstartData()` is true and keeps the remote entry (with its URL) even if a local copy exists, so the version comparison above cannot hide the package. `eZStepSiteTypes::init()` then calls `downloadAndImportPackage()` with `forceDownload=false`: a site package that is already in `var/storage/packages/` is used as it is, and only a missing one is downloaded. `downloadDependantPackages()` checks each required dependency against its `min-version` and downloads only missing or older packages, using the remote `index.xml` to obtain the URLs. To install a newer copy of a package that is already there, remove the local copy first.

### Dry-run mode

`--dry-run` is not just a static validator any more. It:

1. Validates `kickstart.ini` and lists the wizard steps.
2. Runs the wizard steps from `DatabaseChoice` through `SiteDetails`.
3. During `SiteTypes`, imports the site package and its dependencies into a temporary `var/storage/packages/dryrun/` repository.
4. Cleans up the `dryrun/` repository as soon as the `SiteTypes` step finishes.
5. Stops after `SiteDetails` (index 10): before `SiteAdmin`, which can write a generated password to `var/log/initial-admin-password`, before `Registration`, which can send mail, and before `CreateSites` (index 14).

The dry-run path uses the same `downloadAndImportPackage()` and `downloadDependantPackages()` helpers as a real install, but with `repositoryID='dryrun'` and `skipExisting=true`. `eZPackage::import()` supports the `skipExisting` flag so it can import into the temp directory without returning `STATUS_ALREADY_EXISTS` even when `7x/sevenx_site` already exists locally. This makes the dry-run a true test of the remote download and dependency resolution without overwriting the local package cache or the `settings/siteaccess/` files.

```bash
php bin/php/console exp:kickstarter run --dry-run
```

Because the stop step is `SiteDetails`, the `--force` guard is never triggered and `CreateSites` is never reached, so the database and site files are left untouched, and no password file is written and no mail sent.


## Setup steps

The wizard steps are defined in `kernel/setup/steps/ezstep_data.php`.

| # | Step | Notes |
|---|------|-------|
| 0 | `Welcome` | Always sets `eZSetupWizardLanguage` to `eng-GB` and continues. |
| 1 | `SystemCheck` | Verifies PHP extensions, permissions, and file system. |
| 2 | `SystemFinetune` | Applies system fine-tuning. |
| 3 | `EmailSettings` | Configures the email handler. |
| 4 | `DatabaseChoice` | Selects the database type. |
| 5 | `DatabaseInit` | Tests the database connection and stores the DSN. |
| 6 | `LanguageOptions` | Sets the primary language and additional languages. |
| 7 | `SiteTypes` | Selects the site package and downloads it from the remote repository when kickstart mode is active. |
| 8 | `PackageLanguageOptions` | Maps any package languages that are not part of the site language list to the primary language. |
| 9 | `SiteAccess` | Selects the access matching method (`url`, `port`, `hostname`). |
| 10 | `SiteDetails` | Sets site title, URL, access names, and `DatabaseAction`. |
| 11 | `SiteAdmin` | Creates the admin user. |
| 12 | `Security` | Security options. |
| 13 | `Registration` | Registration report. Sends nothing unless `Send=true` and setup.ini `[RegistrationSettings] Receiver` is set. |
| 14 | `CreateSites` | Destructive step: installs schema, data, packages, and siteaccess files. Not counted in progress. |
| 15 | `Final` | Final step. |


## `kickstart.ini` reference

`kickstart.ini` is a standard INI file in the project root. Each section corresponds to a wizard step. The `Continue=true` flag in a section means the wizard should skip the UI for that step and use the values from the file.

### `email_settings`

```ini
[email_settings]
Continue=true
Type=mta
Server=
User=
Password=
```

| Field | Description |
|-------|-------------|
| `Continue` | `true` skips the wizard screen; `false` pre-fills it but still shows it. |
| `Type` | `mta` for Sendmail/MTA, `smtp` for an SMTP server. |
| `Server` | SMTP server hostname. |
| `User` | SMTP username. |
| `Password` | SMTP password. |

### `database_choice`

```ini
[database_choice]
Continue=true
Type=sqlite3
```

| Field | Description |
|-------|-------------|
| `Type` | Database driver: `sqlite3` (the default, needs no database server), `mysqli`, `pgsql`, `mongodb`. |

**Important:** `Type` chooses the driver the *installer* connects with. It does
not decide the `DatabaseImplementation` written into
`settings/override/site.ini.append.php` for the finished site. That value is
taken from the live `site.ini`, and it is deliberately the operator's to set.

The reason is that a driver has more than one name. `mysql`, `mysqli`,
`ezmysql` and `ezmysqli` all resolve to `eZMySQLiDB` through the
`ImplementationAlias` block in `settings/site.ini`, and a site may register
its own alias there for a driver of its own. Deriving the name from whichever
driver happened to connect would overwrite that choice on every install, so
the installer keeps it.

The consequence is worth knowing before an install that changes engine. The
live `site.ini` still holds the name of the engine you are leaving, so the new
installation is written with the previous engine's implementation beside the
new host, database and credentials. `settings/override/` outranks every
siteaccess, so the whole site then tries to speak the old engine's protocol to
the new server and no page will load.

Set it to match the target before running, for example when moving to MySQL:

```bash
sed -i '/^\[DatabaseSettings\]/,/^\[/ s/^DatabaseImplementation=.*/DatabaseImplementation=mysqli/' \
  settings/override/site.ini.append.php
```

Then run the installer as usual. Afterwards the value in
`settings/override/site.ini.append.php` and the one in each siteaccess should
both name the engine you have just installed onto; if they disagree, that is
what to correct.

### `database_init`

```ini
[database_init]
Continue=true
Server=localhost
Port=
Database=ezp
User=root
Password=
Socket=
```

| Field | Description |
|-------|-------------|
| `Server` | Database host. |
| `Port` | Database port (leave empty for default). |
| `Database` | Database name. |
| `User` | Database user. |
| `Password` | Database password. |
| `Socket` | Path to a Unix socket, or empty. |

### `language_options`

```ini
[language_options]
Continue=true
Primary=eng-GB
Languages[]=eng-US
```

| Field | Description |
|-------|-------------|
| `Primary` | Primary language locale for the site. |
| `Languages[]` | Additional languages. The primary language is always added automatically if it is not listed. |

**Important:** every locale used by the selected site package (for example `eng-US` in `sevenx_site`) must be present in `Languages[]` or mapped to the primary language. Otherwise content objects will be imported with a language that is not set as a prioritized site language, and tree-node lookups for those objects will fail.

If `Languages[]` is left empty, the `eZStepPackageLanguageOptions` step treats it as an empty array and maps any package-only locales to the primary language, so the install can continue without a fatal `array_diff()` error.

### `site_types`

```ini
[site_types]
Continue=true
Site_package=sevenx_site
```

| Field | Description |
|-------|-------------|
| `Continue` | `true` to use the values in this section and skip the UI. |
| `Site_package` | Identifier of the site package to install. When `exp:kickstarter run` is used, the remote package list from the configured repository is preferred over the local `var/storage/packages/` copy. The package index uses a different version format than the package files, so the remote entry is always chosen when a `kickstart.ini` `[site_types]` section is active. |

### `site_access`

```ini
[site_access]
Continue=true
Access=url
```

| Field | Description |
|-------|-------------|
| `Access` | `url`, `port`, or `hostname`. |

### `site_details`

```ini
[site_details]
Continue=true
Title=My Exponential Site
URL=
Access=sevenx_site_user
AdminAccess=sevenx_site_admin
AccessPort=8080
AdminAccessPort=8081
AccessHostname=sevenx-site.test.com
AdminAccessHostname=sevenx-site-admin.test.com
EditorAccess=editor
EditorAccessPort=8082
EditorAccessHostname=edit.sevenx-site.test.com
Database=ezp
DatabaseAction=skip
```

| Field | Description |
|-------|-------------|
| `Title` | Site title. |
| `URL` | Site URL; if empty, it is derived from the current request. |
| `Access` | User siteaccess name. |
| `AdminAccess` | Admin siteaccess name. |
| `AccessPort` | Port for user access when `Access=port`. |
| `AdminAccessPort` | Port for admin access when `Access=port`. |
| `AccessHostname` | Hostname for user access when `Access=hostname`. |
| `AdminAccessHostname` | Hostname for admin access when `Access=hostname`. |
| `EditorAccess` | Editor siteaccess name (default `editor`): the administration interface for content editing only, without setup, design and developer tools. Every installation has one. |
| `EditorAccessPort` | Port for editor access when `Access=port` (default 8082). |
| `EditorAccessHostname` | Hostname for editor access when `Access=hostname`; by convention `edit.<the site's domain>`. |
| `Database` | Database name for this site. |
| `DatabaseAction` | What to do when the database already contains data. See below. |

#### `DatabaseAction` values

| Value | Constant | Behaviour |
|-------|----------|-----------|
| `ignore` | `DB_DATA_APPEND` (1) | Try to add entries without cleaning up. |
| `remove` | `DB_DATA_REMOVE` (2) | Clean up existing entries and install schema + data + packages. |
| `skip` | `DB_DATA_KEEP` (3) | Do not insert schema and data. Use this to only regenerate siteaccess files against an existing database. |

### `site_admin`

```ini
[site_admin]
Continue=true
FirstName=Admin
LastName=User
Email=<e-mail address of the first administrator>
Password=<choose a password>
```

| Field | Description |
|-------|-------------|
| `FirstName` | Admin first name. |
| `LastName` | Admin last name. |
| `Email` | Admin email. |
| `Password` | Admin password. |

### `security`

```ini
[security]
Continue=true
```

### `registration`

```ini
[registration]
Continue=true
Send=false
```

| Field | Description |
|-------|-------------|
| `Send` | `true` to send the registration report; anything else, or no `Send=` at all, sends nothing (the default). The report goes only to `settings/setup.ini` `[RegistrationSettings] Receiver`, which is empty by default, so even `Send=true` sends nothing until a receiver is set. The old upstream registration address is never used. |
| `UserData[...]` | The registration form's fields (`UserData[first_name]`, `UserData[last_name]`, `UserData[email]`, `UserData[country]`, `UserData[company]`), used in the report when it is sent. |

A `Comments=` key, written by older versions of `exp:kickstarter ini`, is not read.


## Installation workflow

1. **Generate the configuration file**:

   ```bash
   php bin/php/console exp:kickstarter ini --yes
   ```

2. **Review and edit `kickstart.ini`**. At minimum confirm:

   - `database_init` credentials.
   - `site_types` `Site_package`.
   - `language_options` `Primary` and `Languages[]` include every locale used by the site package.
   - `site_details` `DatabaseAction` is set to `remove` for a clean install, or `skip` to regenerate siteaccess.

3. **Verify the remote packages can be downloaded** without modifying the site:

   ```bash
   php bin/php/console exp:kickstarter run --dry-run
   ```

   This runs `DatabaseChoice` through `SiteDetails`, imports the site package and dependencies into `var/storage/packages/dryrun/` (which is removed after the `SiteTypes` step), and stops before `SiteAdmin`: nothing is installed, no password file is written and no mail is sent.

4. **Run the installer**:

   ```bash
   php bin/php/console exp:kickstarter run --force
   ```

5. **Verify the site is accessible** at the URL written in the summary.


## Examples

### Clean install

```bash
php bin/php/console exp:kickstarter ini --yes
sed -i 's/^DatabaseAction=skip$/DatabaseAction=remove/' kickstart.ini
php bin/php/console exp:kickstarter run --force
```

### Regenerate only siteaccess and INI files

```bash
# Adjust the relevant fields and keep DatabaseAction=skip
php bin/php/console exp:kickstarter run --force --stop-step=CreateSites
```

### After a failed run

A run cannot resume from a later step (see [Resuming after a failure](#resuming-after-a-failure)); `--start-step=SiteDetails` is refused with the step to start at. Fix `kickstart.ini` and run the installation again:

```bash
php bin/php/console exp:kickstarter run --force
```

### Dry-run to validate `kickstart.ini` and remote packages

```bash
php bin/php/console exp:kickstarter run --dry-run
```

This validates the INI sections, lists the wizard steps, and then runs the wizard steps from `DatabaseChoice` through `SiteDetails` to verify the database connection and that the configured site package and all of its remote dependencies can be downloaded. It stops before `SiteAdmin`, so no database or siteaccess files are modified, no password file is written and no mail is sent. The downloaded packages are imported into a temporary `var/storage/packages/dryrun/` repository that is removed when the `SiteTypes` step finishes.

### List the wizard steps

```bash
php bin/php/console exp:kickstarter run --list-steps
```


## FAQ: short answers for common needs

### What is the simplest way to get started?

```bash
php bin/php/console exp:kickstarter ini --yes
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```

The first line creates the config. The second line tests remote packages without changing anything. The third line installs.

### Do I need `--start-step` or `--stop-step` for normal use?

No. They are for partial runs, and a later `--start-step` cannot resume an earlier run: it is refused when the steps before it are needed, which is always the case for a range that installs. The normal commands are `run --dry-run` and `run --force`.

### Why does `run` need `--force`?

`CreateSites` installs the database schema, data, and siteaccess files. `--force` confirms you want that to happen. Without it, the command stops safely.

### What does `--dry-run` actually do?

It validates `kickstart.ini`, lists the steps, then runs `DatabaseChoice` through `SiteDetails` to test the database connection and the package download. It stops before `SiteAdmin` and cleans up the temporary `dryrun/` repository.

### What does `--dry-run` change on disk?

Nothing permanent. It downloads packages into `var/storage/packages/dryrun/` and removes that directory when `SiteTypes` finishes. It does not touch the database or `settings/siteaccess/`, writes no `var/log/initial-admin-password` and sends no mail.

### What does `run --force` change?

It installs the site package and dependencies into `var/storage/packages/`, writes `settings/siteaccess/`, creates the database schema, and inserts the site data. Use `--dry-run` first to test.

### Can I run `run --dry-run` without a working database?

No. `DatabaseChoice` and `DatabaseInit` still test the connection. But no data is written. The database only needs to be reachable.

### Why does the remote package index use a different version?

The remote `index.xml` uses a simple version like `1.0.0-0`, but the actual `.ezpkg` file contains `6.0.10-stable`. `eZPackage::getVersion()` returns `6.0-10-stable`. A plain version comparison would think the local copy is newer, so kickstart mode always keeps the remote entry.

### Does this break the normal web setup wizard?

No. The web setup wizard uses `eZStepSiteTypes::display()` with the old version-comparison logic. The remote-preference only happens inside `eZStepSiteTypes::init()`, which is triggered by `kickstart.ini` `[site_types]` with `Continue=true`. If you do not leave `kickstart.ini` in the web root, the web wizard is unchanged.

### Does `eZPackage::import()` change break other tools?

No. The signature is `eZPackage::import( $archiveName, &$packageName, $dbAvailable = true, $repositoryID = false, $skipExisting = false )`. The new 5th argument is optional and defaults to `false`. All existing callers (`ezpm.php`, `upload.php`, `bin/php/ezwebincommon.php`, tests) still work.

### Is it safe to leave `kickstart.ini` in the project root?

`kickstart.ini` is read by the CLI tool and, if present, can also be read by the web setup wizard. If you do not want the web wizard to use it, remove or move it after the CLI install.

### What if `Languages[]` is empty in `language_options`?

The installer now casts the language list to an array, so an empty `Languages[]` no longer causes a fatal `array_diff()` error. The package languages are mapped to the primary language.

### What is the `dryrun/` directory?

A temporary package repository created only during `--dry-run`. `eZPackage::import()` supports a `skipExisting` flag so the dry-run can import the remote package there without colliding with the local `7x/sevenx_site` copy. `expKickstarter` removes `dryrun/` after `SiteTypes` and also removes any leftover `dryrun/` at CLI startup.

### What if the remote package server is unavailable?

`eZStepSiteTypes` falls back to a local `index.xml` if the remote `index.xml` cannot be downloaded. If neither is available, the step stalls and shows the package list so you can upload a package manually. The normal web wizard is unaffected because it does not use kickstart mode unless `kickstart.ini` exists.

### What if the dry-run is interrupted and `dryrun/` is left behind?

`expKickstarter` removes `dryrun/` after `SiteTypes` and also removes any leftover `dryrun/` at the start of every CLI run. The directory is a temporary cache and contains no database or site settings.

### Is it safe for long-term use?

Yes. The changes are additive. The `--dry-run` path is separate from the install path. The install path (`run --force`) still requires the `--force` guard. The `eZPackage` and `eZStepSiteTypes` APIs keep their old behavior unless the new optional flags are used.

## Troubleshooting

### `kickstart.ini not found`

Generate it first:

```bash
php bin/php/console exp:kickstarter ini
```

### `The CreateSites step will modify the database and site settings`

Add `--force` to confirm the database will be modified:

```bash
php bin/php/console exp:kickstarter run --force
```

### `eZINI` caches stale values

Both `exp:kickstarter ini` and `exp:kickstarter run` delete `var/cache/ini/kickstart-*.php` automatically. If you edit `kickstart.ini` manually, clear that directory:

```bash
rm -f var/cache/ini/kickstart-*.php
```

### Content pages render but sub-items (images, galleries) are missing

This usually means the package languages were not aligned with the site languages. Make sure `kickstart.ini` `language_options` `Languages[]` lists every locale used by the site package. If the package uses `eng-US`, add:

```ini
[language_options]
Primary=eng-GB
Languages[]=eng-US
```

Then set `DatabaseAction=remove` and run the installer again:

```bash
php bin/php/console exp:kickstarter run --force
```

### `kickstart.ini` fields are not read

- Ensure no leading whitespace before section headers or keys.
- Ensure `Continue=true` is set for the section you want to skip.
- Ensure the file is saved as `kickstart.ini` in the project root.

### The site package is not downloaded from the remote server

- Confirm `package.ini` points to a reachable `index.xml` URL.
- Confirm `site_types` `Site_package` matches the `name` attribute in the remote `index.xml`.
- A package that is already in `var/storage/packages/` is not downloaded again; remove the local copy to force a download.
- Remember that `hasKickstartData()` must be true for the remote-entry preference. The `[site_types]` section must have `Continue=true` and `Site_package` set.
- Run `--dry-run` to see exactly where the download fails.


## Files

| File | Purpose |
|------|---------|
| `bin/php/kickstarter.php` | Entry point for `ini` and `run` subcommands. |
| `kernel/classes/expkickstarter.php` | `expKickstarter` class that drives the setup wizard steps, including `--dry-run` orchestration and cleanup. |
| `kernel/classes/expkickstarterini.php` | `expKickstarterIni` class that generates `kickstart.ini` from `kickstart.ini-dist`. |
| `kernel/classes/ezpackage.php` | `eZPackage::import()` now supports `skipExisting` and a target repository for dry-run imports. |
| `kernel/setup/steps/ezstep_site_types.php` | `eZStepSiteTypes` selects the remote site package in kickstart mode and downloads it, including dependency resolution. |
| `kernel/setup/steps/ezstep_package_language_options.php` | `eZStepPackageLanguageOptions` handles empty `Languages[]` by casting to array. |
| `kernel/setup/steps/ezstep_data.php` | Wizard step table. |
| `kickstart.ini-dist` | Commented template describing every section and field. |
| `kickstart.ini` | Generated/active configuration. |

## Implementation notes

### `eZPackage::import()` changes

`eZPackage::import( $archiveName, &$packageName, $dbAvailable = true, $repositoryID = false, $skipExisting = false )` now accepts a fifth argument. When `$skipExisting` is `true`, the function skips the `eZPackage::fetch()` check that would normally return `STATUS_ALREADY_EXISTS`. This allows the dry-run path to import a package into a non-default repository (e.g. `var/storage/packages/dryrun/`) while a copy with the same name still exists in `var/storage/packages/7x/`.

### `eZStepSiteTypes` changes

- `downloadAndImportPackage( $packageName, $packageUrl, $forceDownload = false, $repositoryID = false, $skipExisting = false )` now forwards the optional repository and skip-existing flags to `eZPackage::import()`.
- `downloadDependantPackages( $sitePackage, $repositoryID = false, $skipExisting = false )` now forwards the same flags to `downloadAndImportPackage()`.
- In `init()`, when the `eZSetupKickstartDryRun` post variable is set, the site package is imported into the `dryrun` repository with `skipExisting=true`. In normal mode it is imported into the package's own vendor directory (`7x`) with `forceDownload=false`: a package already there is used as it is.
- `createSitePackagesList()` now calls `$this->hasKickstartData()` before the version comparison. If a kickstart configuration is active, the local package entry is skipped and the remote entry is kept, so the remote URL is preserved; the package is downloaded only when it is not in the local repository.

### `eZStepPackageLanguageOptions` changes

The `siteLanguageLocaleList` is now cast with `(array)` in both `processPostData()` and `init()`, so an empty `Languages[]` value in `kickstart.ini` no longer triggers a fatal `array_diff()` error in `eZStepPackageLanguageOptions::init()`.


## The summary ends with a finish time and duration

After the summary the kickstarter prints a rule, then `Finished:` with the date, time and zone and `Elapsed:` as `hh:mm:ss (n seconds)`. Two runs of the same `kickstart.ini` otherwise print identical text, so the finish time tells a fresh run from scrollback and the elapsed time lets you compare them (`showFinishTime()` in `kernel/classes/expkickstarter.php`).

Every run is also logged to `var/log/kickstart.log` with passwords masked; earlier runs are kept as `kickstart.log.1` (the last) to `kickstart.log.9`, and `EXP_KICKSTART_LOG=0` turns the log off (see `php bin/php/kickstarter.php --help`). List the steps without changing anything: `php bin/php/kickstarter.php run --list-steps`.


## Related pages

- [Kickstarter CLI (feature page)](../../features/6.0/kickstarter-cli.md) and [Install in one command](../../features/6.0/install-in-one-command.md)
- [Getting started guide](../../guides/getting-started.md)
- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [Exponential Console](console.md)
- [September 2026, first half: 13 September](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- `kickstart.ini-dist` in the project root: every section and field, commented.
