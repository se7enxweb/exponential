# 4. Choosing an install method

Exponential 6.0 can be installed in three ways that all end in the same kind of installation: the **setup wizard**
in a browser, the **kickstarter** on the command line driven by a `kickstart.ini` file, and the one-command
**console install** `exp:install`, which builds that file from options. Under all three runs one engine: the step
classes in `kernel/setup/steps/`, in the order of the step table in `kernel/setup/steps/ezstep_data.php`, and a
**site package** whose install scripts shape the new site. This chapter explains that common engine, compares the
three methods, lists exactly what an installation writes to disk and to the database, and recommends a method for
each situation, including unattended installs in CI pipelines and containers. If you already know which method you
want, skip to its chapter; if you will install more than once, or automate the install, read 4.4 and 4.6 first,
because they describe what a reinstall destroys.

[Contents](README.md) · Previous: [3. Getting the code](03-getting-the-code.md) · Next: [5. The setup wizard](05-setup-wizard.md)

## 4.1 One installer, three front ends

Every install method runs the same sequence of steps. Each step is a class `eZStep<Name>` in
`kernel/setup/steps/ezstep_<file>.php`, and the order is fixed by `eZStepData::$StepTable`:

| # | Step class | File | Asks for |
|---|---|---|---|
| 0 | `Welcome` | `ezstep_welcome.php` | the language of the wizard itself |
| 1 | `SystemCheck` | `ezstep_system_check.php` | nothing: runs the critical system tests |
| 2 | `SystemFinetune` | `ezstep_system_finetune.php` | nothing: runs the optional tests on request |
| 3 | `EmailSettings` | `ezstep_email_settings.php` | sendmail/MTA or SMTP |
| 4 | `DatabaseChoice` | `ezstep_database_choice.php` | the database system |
| 5 | `DatabaseInit` | `ezstep_database_init.php` | server, port, database, user, password, socket |
| 6 | `LanguageOptions` | `ezstep_language_options.php` | the primary and additional languages |
| 7 | `SiteTypes` | `ezstep_site_types.php` | the site package |
| 8 | `PackageLanguageOptions` | `ezstep_package_language_options.php` | what to do with package languages you did not choose |
| 9 | `SiteAccess` | `ezstep_site_access.php` | how siteaccesses are matched: URL, port or host name |
| 10 | `SiteDetails` | `ezstep_site_details.php` | title, URL, match values, database, what to do with existing data |
| 11 | `SiteAdmin` | `ezstep_site_admin.php` | the administrator's name, e-mail and password |
| 12 | `Security` | `ezstep_security.php` | nothing: advice when the site is not in virtual host mode |
| 13 | `Registration` | `ezstep_registration.php` | nothing in the browser; an optional registration e-mail from `kickstart.ini` |
| 14 | `CreateSites` | `ezstep_create_sites.php` | nothing: **writes the database, the packages and the settings** |
| 15 | `Final` | `ezstep_final.php` | nothing: shows the addresses of the new site |

`CreateSites` is marked `count_step => false`: it is never shown as a page and does not count in the wizard's
progress bar. It is the only step that installs the database, the site package and the siteaccess settings; the steps
before it collect and check answers (`DatabaseInit` opens the database to test it, which creates an empty SQLite file
if none exists, and in the browser `LanguageOptions` writes the character set to `settings/override/i18n.ini.append.php`), and keep
what they found in memory for the steps after them. That is why an install cannot be resumed half-way in a new
process: the kickstarter refuses a `--start-step` that would need those results
([chapter 6](06-kickstarter.md#610-re-running-and-resuming)).

The three front ends differ only in **where the answers come from** and **how the steps are driven**:

```text
                 browser forms             kickstart.ini               command-line options
                 (one page per step)       (one section per step)      (exp:install --db=... --url=...)
                        |                          |                                 |
                        |                          |                  bin/php/install.php writes a
                        |                          |                  temporary kickstart.ini
                        v                          v                                 v
   kernel/setup/ezsetup.php           expKickstarter (kernel/classes/expkickstarter.php)
   (module setup, view init)          runs the same step classes in one CLI process
                        \                          |                                 /
                         \_________________________|________________________________/
                                                   v
                       eZStep* classes in kernel/setup/steps/  ->  CreateSites
                                                   v
                         site package install scripts (eZSitePreInstall, eZSitePostInstall, ...)
```

Two consequences follow:

- **A fix or default in a step applies everywhere.** The SQLite file checks, the language validation and the
  generated administrator password behave the same in the browser and on the command line (with the differences
  this book points out where they exist).
- **`kickstart.ini` is read by the browser wizard too.** The constructor of every step (`eZStepInstaller`) reads
  `kickstart.ini` in the installation root. When the wizard finds a section for a step, it pre-fills that page, or
  skips it when the section says `Continue=true`. See [chapter 5](05-setup-wizard.md#56-how-kickstartini-pre-fills-and-skips-pages).

## 4.2 The three methods at a glance

| | Setup wizard | Kickstarter | Console install |
|---|---|---|---|
| Entry point | any page of the site while `[SiteAccessSettings] CheckValidity=true` | `php bin/php/console exp:kickstarter run --force` (also `php bin/php/kickstarter.php run --force`) | `php bin/php/console exp:install` (also `php bin/php/install.php`) |
| Answers come from | the forms, one page per step | `kickstart.ini` in the installation root, one section per step | command-line options, every one with a default |
| Needs a web server during the install | yes | no | no |
| Needs a file written first | no (`kickstart.ini` optional) | yes: `kickstart.ini` (`exp:kickstarter ini` writes it) | no |
| Interactive | yes | only `exp:kickstarter ini` asks questions; `run` does not | no |
| Dry run | no | `run --dry-run` | `--dry-run`, and `--print` to see the configuration |
| Partial runs | Back and Next in the browser | `run --start-step=<Step>` and `--stop-step=<Step>`, for diagnosis; an install runs from `Welcome`, and a later start that needs the results of earlier steps is refused | no; run again with the same options |
| Guard against overwriting | starts only while `CheckValidity=true` | `--force` required whenever the steps include `CreateSites` | refuses when `settings/override/site.ini.append.php` holds `[DatabaseSettings]`, unless `--force` |
| Maintenance page during the run | yes, every visitor but the wizard's browser | yes | yes (it runs the kickstarter) |
| Run log | `var/log/setup.log` | `var/log/setup.log` and `var/log/kickstart.log` | `var/log/setup.log` and `var/log/exp-install-<date>.ini` (it prints to the terminal only; there is no `kickstart.log`) |
| Administrator password | typed in the form | from `kickstart.ini`; generated when empty or well known | generated unless `--password` is given and acceptable |
| Best for | a first install, evaluating Exponential, hosts without shell access | repeatable installs from a reviewed file, staging copies, offline package sets | the fastest install; scripts, CI, containers, demos |
| Chapter | [5](05-setup-wizard.md) | [6](06-kickstarter.md) | [7](07-console-install.md) |

### The setup wizard

The wizard starts by itself. `settings/site.ini` ships with:

```ini
[SiteAccessSettings]
CheckValidity=true
```

While that is `true`, the kernel (`ezpKernelWeb`) sends every request to the module `setup`, view `init`, with the
page layout `[SetupSettings] PageLayout=setup_pagelayout.tpl` and without a session or a database. `CreateSites`
writes `CheckValidity=false` to `settings/override/site.ini.append.php`, and from the next request on the site is
served. The wizard needs a working web server first ([chapter 8](08-serving-the-site.md)), and it is the only method
that shows each decision on a page with an explanation. It is described page by page in [chapter 5](05-setup-wizard.md).

### The kickstarter

The kickstarter (`bin/php/kickstarter.php`, console name `exp:kickstarter`) runs the step classes in one PHP process
on the command line and reads every answer from `kickstart.ini`. It has two subcommands: `ini` writes the file
(interactively, or from defaults with `--yes` or `--defaults`), and `run` installs from it. It is the method to use
when the same installation has to be made more than once, when the answers should be reviewed as a file before they
are applied, or when the site package comes from a local package repository. It is described in
[chapter 6](06-kickstarter.md).

### The console install

`exp:install` (`bin/php/install.php`) is a front end to the kickstarter. It turns its options into a temporary
`kickstart.ini`, runs `expKickstarter` in the same process, removes the temporary file and puts back any
`kickstart.ini` that was there before. Every option has a default, so

```bash
php bin/php/console exp:install
```

alone installs the `sevenx_multisite` site package on SQLite (`var/storage/sqlite3/exponential.db`) with the
administrator `admin` and a generated password, shown once at the end. It is described in
[chapter 7](07-console-install.md).

## 4.3 Site packages: what is installed

The steps install a **site package**: an Exponential package of type `site` (`package.xml` with
`<type>site</type>`), plus the packages it requires. Packages live in the package repository below
`var/storage/packages/`; the vendor directory is set by `settings/package.ini [RepositorySettings] Vendor` (`7x`), so
an imported package lands in `var/storage/packages/7x/<name>/`.

The package index of this version, `[RepositorySettings] RemotePackagesIndexURL`
(`https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`, whose `index.xml` lists the packages),
offers eleven site packages. The ones you are most likely to choose:

| Site package | Summary in the index | Notes |
|---|---|---|
| `sevenx_multisite` | 7x MultiSite Default Installation | the default of `exp:install --package` and of `exp:kickstarter ini --yes`; requires `sevenx_classes` (the content classes) and `sevenx_multisite_democontent` (the demo content), each `min-version 5.1` |
| `sevenx_multisite_clean` | 7x MultiSite Default Installation (without demo content) | the same site, empty |
| `sevenx_site`, `sevenx_site_clean` | 7x Simple Website Interface | a simpler site, with or without demo content |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (each also as `..._clean`), `plain_site` | Website Interface, eZ Flow, Exponential Demo Site, Plain site | the older site packages, kept for sites built on them |

A `_clean` package installs the same structure without the demo articles and images: choose it for a real site you
will fill yourself, and the full one to evaluate Exponential.

Where packages come from:

- **The remote package index.** `SiteTypes` downloads the index from `[RepositorySettings] RemotePackagesIndexURL`
  in `settings/package.ini` (when it is empty: `RemotePackagesIndexURLBase` plus the Exponential version), and offers
  the site packages listed there next to the ones already imported. A package that is listed remotely and not yet
  imported is downloaded and imported, followed by the packages it requires.
- **The local repository.** A package already imported under `var/storage/packages/` is used as it is. When the
  index cannot be downloaded, `SiteTypes` looks for an `index.xml` in the system repository instead, which is how an
  offline installation works.
- **An upload.** The wizard's Site package page has an **Upload package** field for an `.ezpkg` file.

### Package install scripts: the "multi-site installer"

A site package can carry PHP install scripts as `<settings-file>` entries in its `package.xml`. `CreateSites` calls
the functions they define, when they exist, at fixed points:

| Function | Called by `CreateSites` | Purpose |
|---|---|---|
| `eZSitePreInstall( $siteType )` | before the package is installed | prepare, for example create classes |
| `eZSiteINISettings( $parameters )` | while the settings are built | settings of the public siteaccess |
| `eZSiteAdminINISettings( $parameters )` | while the settings are built | settings of the admin siteaccess |
| `eZSiteCommonINISettings( $parameters )` | while the settings are built | settings written to `settings/override/` |
| `eZSiteRoles( $parameters )` | after the content is in | roles and policies |
| `eZSitePreferences( $parameters )` | after the roles | user preferences |
| `eZSitePostInstall( $parameters )` | at the end | anything else |
| `eZSiteFinalText( $parameters )` | for the last page | text shown on the wizard's Finished page |

`sevenx_multisite` lists `ini-site.php`, `ini-admin.php`, `ini-common.php`, `sevenx-multi-site-installer.php`,
`install-scripts.php`, `roles.php` and `preferences.php`. Its `install-scripts.php` defines `eZSitePreInstall()` and
`eZSitePostInstall()`, each of which creates a `sevenxMultiSiteInstaller` (a subclass of the kernel's
`eZSiteInstaller` in `kernel/classes/ezsiteinstaller.php`) and calls `preInstall()` or `postInstall()`. This
"multi-site installer" is therefore not a fourth install method: it is the site package's part of every install,
whichever front end started it. If its post-install stops part-way, `CreateSites` reports error `EZSW-080`
("The post-install of site package '...' stopped at step N of M (...): the steps after it did not run").

### Other installer scripts you may meet

| Script | What it does | Use it to install a new site? |
|---|---|---|
| `exp:ezwebininstall` (`bin/php/ezwebininstall.php`) | installs the `ezwebin` site package into a configured installation; options `--repository`, `--package`, `--package-dir`, `--url`, `--admin-siteaccess`, `--user-siteaccess`, `--auto-mode` | no: it expects an installation that already exists |
| `extension/expsite_installer/bin/php/install.php` (extension `expsite_installer`, when present) | activates the `explayouts*`/`expsite*` extensions in `settings/override/site.ini.append.php` and runs `expSiteInstaller::runFullInstall()` with the data in `extension/expsite_data_media/data` | no: it layers layout data onto an existing installation, and that data set is deprecated; reinstall from the site packages instead |

## 4.4 What an installation writes

All three methods end in `CreateSites`, so they write the same things. Knowing them makes it clear what a
reinstall replaces and what has to be backed up.

### Settings

| File | Written by | What is in it |
|---|---|---|
| `settings/override/site.ini.append.php` | `CreateSites` | `[SiteAccessSettings] CheckValidity=false`, `MatchOrder` (`uri`, `host` or `port`), `HostMatchMapItems[]`, `AvailableSiteAccessList[]`; `[PortAccessSettings]` for port matching; `[SiteSettings] SiteList[]` and `DefaultAccess`; `[MailSettings] AdminEmail`, `Transport` (`sendmail` or `SMTP`) with `TransportServer`, `TransportUser`, `TransportPassword` for SMTP; `[DesignSettings] DesignLocationCache=enabled`; and the global `site.ini` settings the site package returns (for example `[ExtensionSettings] ActiveExtensions[]`) |
| `settings/override/i18n.ini.append.php` | `LanguageOptions` (browser) and `CreateSites` | `[CharacterSettings] Charset` (always `utf-8` for a new site) |
| `settings/override/image.ini.append.php` | `CreateSites` | `[ImageMagick] IsEnabled`, and `ExecutablePath`/`Executable` when the system check found ImageMagick |
| `settings/override/mailpreferences.ini.append.php` | `CreateSites` | the e-mail preferences' site secret, and the organisation name and postal address from Site details |
| `settings/override/<file>.ini.append.php` | `CreateSites` | any other INI file the package's `eZSiteCommonINISettings()` returns |
| `settings/siteaccess/site/` | `CreateSites` | the public siteaccess: `site.ini.append.php` with `[DatabaseSettings]` (`DatabaseImplementation`, `Server`, `Port`, `Database`, `User`, `Password`, `Socket`, `Charset`), `[SiteSettings] SiteName` and `SiteURL`, `[RegionalSettings]`, `[FileSettings] VarDir`, `[DesignSettings] SiteDesign`, plus the package's siteaccess settings |
| `settings/siteaccess/admin/` | `CreateSites` | the admin siteaccess, with the same `[DatabaseSettings]` |
| `settings/siteaccess/editor/` | `CreateSites` (`createEditorSiteAccess()`) | a copy of the admin siteaccess's `*.ini.append.php` files with `SiteDesign=editor`, `AdditionalSiteDesignList[]` `admin4l`, `admin4`, `admin3`, `admin2`, `admin`, `ExtensionSettingsSiteAccess=admin`, `SiteName=Editor`, its own `SiteURL`, and `[SiteAccessRules]` that switch off the modules `setup`, `visual`, `explayouts_ui`, `explayouts_ui_api`, `git_manager`, `xrowextract` and `bccie`; plus `menu.ini`, `toolbar.ini` and `admininterface.ini` appends |
| `settings/siteaccess/adminui/` | `CreateSites` (`createAdminUISiteAccess()`), or the multisite package's post-install with the same function; only when `extension/exp_adminui` is there | a copy of the admin siteaccess's `*.ini.append.php` files with `SiteDesign=adminui`, `AdditionalSiteDesignList[]` `admin4l`, `admin4`, `admin3`, `admin2`, `admin`, `ActiveAccessExtensions[]=exp_adminui`, `SiteName=Admin UI`, `SiteURL` the admin's address with the path `/adminui`, and an `icon.ini` and `ezoe.ini` without settings. Listed in `AvailableSiteAccessList[]` and `SiteList[]`, matched by URI only (no host match line, no port; `uri` is added to `MatchOrder`). See [Exponential Admin UI](../features/6.0/exp-adminui.md) |

The siteaccess **directory names are always `site`, `admin` and `editor`**, plus `adminui` when the `exp_adminui`
extension is in the installation. What you choose in the wizard, in
`kickstart.ini` or with `exp:install --site-access=` is the **match value**: the URL path, the port or the host name
that leads to each siteaccess. `Access=www` in URL mode therefore means "the path `/www` serves the siteaccess
`site`", not a siteaccess called `www`.

The database password ends up in `settings/siteaccess/site/site.ini.append.php` and
`settings/siteaccess/admin/site.ini.append.php` (and the editor and adminui copies), and an SMTP password in
`settings/override/site.ini.append.php`. Keep `settings/override/` and `settings/siteaccess/` out of version control
and readable only by the web server user; [chapter 13](13-security-hardening.md) covers this.

### Database

What happens to the database is decided by the **database action**: the `DatabaseAction` key in `kickstart.ini`,
`--db-action` for `exp:install`, or the **Action** drop-down the wizard shows when the database already holds
tables.

| Action | Wizard label | Effect in `CreateSites` |
|---|---|---|
| `remove` | Remove existing data | `eZDBTool::cleanup()` drops the kernel's tables; on SQLite the whole file is emptied. Then the schema (`share/db_schema.dba`), the base data (`share/db_data.dba`) and the packages are installed. **Everything that was in the database is lost.** |
| `ignore` | Leave the data and add new | the schema, data and packages are added without cleaning up first |
| `skip` | Leave the data and do nothing | no schema and no data are inserted; the settings are still written. `SiteAdmin` is skipped as well, so the existing administrator stays. |

The schema load creates every table the kernel declares, then imports each datatype's own `.dba` data (on engines
other than SQLite) and, where the database supports it, the full-text part of the audit index. After that the site
package and its required packages are installed into the database. The database engines themselves are the subject
of [chapter 9](09-databases.md).

### Files and logs

| Path | Written by | Notes |
|---|---|---|
| `var/storage/sqlite3/<file>` | `DatabaseInit` / `CreateSites` | the SQLite database, with `-wal` and `-shm` files beside it |
| `var/storage/packages/` | `SiteTypes` | downloaded and imported packages |
| `design/<package>/override/templates/` | `CreateSites` | empty override directories for the site design named after the package |
| `var/<package>/` or `var/site/` | the site | `CreateSites` writes `[FileSettings] VarDir=var/<package identifier>` to the siteaccess settings, and a site package can set its own: `sevenx_multisite` sets `var/site` for the public siteaccess. Uploaded files and the content caches live below it |
| `var/maintenance.json` | all methods | exists only while the installation runs (see below) |
| `var/log/setup.log` | all methods | one readable record per run; earlier runs rotate to `setup.log.1`, `.2` ... |
| `var/log/setup-run.state` | the wizard | lets each wizard request resume the same run |
| `var/log/kickstart.log` | `exp:kickstarter run` | everything the run printed, passwords masked; nine earlier runs kept as `kickstart.log.1` to `.9` |
| `var/log/exp-install-<date>.ini` | `exp:install` | the configuration used, passwords masked |
| `var/log/initial-admin-password` | kickstarter, `exp:install` | only when a password was generated; mode `0600`. Read it, log in, change the password, delete the file |

### Maintenance mode during the install

Every method switches **maintenance mode** on while it works, so that no visitor meets a half-built database:
`var/maintenance.json` exists, and the front controllers (`index.php`, `index_rest.php`, `index_treemenu.php`)
answer every request with `share/maintenance.html` (or the active theme's `errors/maintenance.html`), HTTP 503 and a
`Retry-After` header, before the settings or the database are touched.

- The **kickstarter** (and so `exp:install`) switches it on when the run begins and off when the installation is
  done. After a failed step it stays on: the run says so, and `php bin/php/maintenance.php off` ends it.
- The **wizard** switches it on at its first request, for every browser but its own (the cookie
  `exp_setup_wizard`), and off on its Finished page; a wizard that is left releases the site after 30 minutes
  ([chapter 5](05-setup-wizard.md#52-the-browser-hold-and-maintenance-mode)).
- A dry run does not switch it on.

## 4.5 Which method for which situation

| Situation | Recommended | Why |
|---|---|---|
| First look at Exponential on a laptop | `exp:install` | one command, SQLite, nothing to answer |
| First production install, done by hand | the wizard, or `exp:install` with explicit options | the wizard explains each decision; `exp:install` leaves a masked record of what was used |
| Shared hosting without shell access | the wizard | it needs only a browser and FTP access |
| The same site built again and again (staging, test, training) | the kickstarter | the reviewed `kickstart.ini` is the specification of the install |
| CI pipeline, container image, automated test environment | `exp:install` | options or environment variables, exit status, no file to template |
| Several environments that differ only in host names and credentials | the kickstarter with one `kickstart.ini` per environment, or `exp:install` with per-environment options | both are deterministic |
| No internet access on the server | the kickstarter or the wizard with the packages already in `var/storage/packages/` | the site package is taken from the local repository when the index cannot be reached |
| Regenerate the settings for an existing database | the kickstarter with `DatabaseAction=skip`, or `exp:install --db-action=skip --force` | `skip` writes the settings and leaves the data alone |
| Reinstall over an existing installation | `exp:kickstarter run --force` or `exp:install --force`, after a backup | both say plainly that they replace the database |

### Decision guide

1. **Do you have shell access?** No: use the wizard. Yes: go on.
2. **Must a person review every answer before anything happens?** Yes: write `kickstart.ini` with
   `exp:kickstarter ini`, review it, then `exp:kickstarter run --dry-run` and `run --force`.
3. **Is the install part of a script, a pipeline or a container start-up?** Yes: use `exp:install` with explicit
   options, the password in `EXP_INSTALL_DB_PASSWORD`, and check the exit status.
4. **Otherwise** `exp:install` is the shortest path, and the wizard the most explained one.

## 4.6 Unattended installs: CI and containers

The command-line methods were built for unattended use. These rules keep such installs safe and repeatable.

**Prefer `exp:install` in automation.** It needs no file, validates its options before anything is written, and
returns a non-zero exit status on every failure ([chapter 7](07-console-install.md#77-exit-status)). The
kickstarter is just as scriptable when the configuration is better kept as a reviewed file.

**Keep secrets out of the command line.** `exp:install` reads the database password from the environment variable
`EXP_INSTALL_DB_PASSWORD` when `--db-password` is not given; that keeps it out of the process list and the shell
history. `kickstart.ini` holds passwords in clear text: create it with mode `0600`, never commit it, and remove it
after the install (the wizard would read it on a later run).

**Choose the administrator password deliberately.** Without `--password`, `exp:install` generates a 24-character
password and writes it to `var/log/initial-admin-password` (mode `0600`). In a pipeline, either pass a password from
your secret store with `--password=` (at least `[UserSettings] MinPasswordLength`, which is 10, and not a well-known
one), or read the generated one from that file and rotate it.

**Run as the web server's user.** The installer creates files in `settings/`, `var/` and `design/`. Run it as the
user the web server or Velocity runs as, or fix the ownership afterwards; the system check tests the directory
permissions as the user it runs as. Neither the kickstarter nor `exp:install` refuses to run as `root` (both accept
`--allow-root-user` and ignore it), but files created by `root` may later be unwritable for the web server. Most
other commands, `exp:velocity` among them, do refuse `root` unless `--allow-root-user` is given.

**Make the database action explicit.** Both `exp:install` (default) and the kickstarter files most people write
use `remove`. That is what a fresh container wants, and exactly what must never point at a database that holds data
you need.

**Expect the run to be idempotent only with `remove`.** Running the same configuration again with
`DatabaseAction=remove` produces the same installation again. With `ignore` a second run adds a second copy of the
data.

**A minimal container entry point** (installs once, then starts the server and stays in the foreground while it
runs):

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
if ! grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null; then
    php bin/php/console exp:install \
        --db=mysql --db-host="${DB_HOST}" --db-name="${DB_NAME}" --db-user="${DB_USER}" \
        --url="${SITE_URL}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASSWORD}"
fi
php bin/php/console exp:velocity start
# exp:velocity start runs the server in the background and returns; status exits 0 while it runs
while php bin/php/console exp:velocity status >/dev/null 2>&1; do sleep 30; done
echo "Velocity stopped" >&2
exit 1
```

The test `grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php` is the same test `exp:install`
itself uses to decide that a directory already holds an installation. Pass `EXP_INSTALL_DB_PASSWORD` in the
container's environment. The loop at the end matters: `exp:velocity start` detaches the server and returns as soon as
it answers, so an entry point that ended with it would end the container's main process, and with it the container.
The loop keeps the entry point alive while `exp:velocity status` reports the server as running (exit status 0) and
ends the container when it stops, so the container runtime can restart it. If the entry point runs as `root`, add
`--allow-root-user` to the two `exp:velocity` commands. Starting and configuring Velocity is the subject of
[chapter 8](08-serving-the-site.md).

**A CI smoke test** that installs on SQLite and fails the job on any error:

```bash
php bin/php/console exp:install --dry-run          # configuration and packages, nothing written
php bin/php/console exp:install --force --title="CI build ${CI_PIPELINE_ID:-local}"
test -f settings/siteaccess/site/site.ini.append.php
tail -n 20 var/log/setup.log
```

## 4.7 Before you start, whatever the method

- The code is in place and the dependencies are installed ([chapter 3](03-getting-the-code.md)), and PHP 8.0 or
  later with the extensions of [chapter 2](02-requirements.md) is available.
- For a database server: the database and its user exist, and the user may create tables
  ([chapter 9](09-databases.md)). SQLite needs only a writable `var/storage/sqlite3/`.
- `settings/`, `var/` and `design/` are writable by the user the install runs as.
- For the wizard: the site is served ([chapter 8](08-serving-the-site.md)); Exponential Velocity is the recommended
  engine for every stage.
- You have a backup of any database the install might touch.

---

## References

In this repository:

- [Installing Exponential 6.0](../INSTALL.md): the one-page installation guide this book expands
- [Kickstarter CLI](../bc/6.0/kickstartercli.md) and [Kickstarter: install a whole site from one file](../features/6.0/kickstarter-cli.md)
- [Installing Exponential in one command](../features/6.0/install-in-one-command.md)
- [The setup wizard's new look, and the editor siteaccess](../features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Clean install defaults](../features/6.0/clean-install-defaults.md)
- [Maintenance mode](../features/6.0/maintenance-mode.md)
- [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md)
- [Package installer batching](../features/6.0/package-installer-batching.md)
- [Multi-site INI overrides](../features/6.0/multi-site-ini-overrides.md)
- [Exponential console](../bc/6.0/console.md)
- [Velocity engines](../bc/6.0/velocity-engines.md)
- [Audit log](../bc/6.0/audit.md) (the event `system.install.run` records every installation)
- [Getting started](../guides/getting-started.md) and [Deploying](../guides/deploying.md)
- Code: `kernel/setup/steps/ezstep_data.php`, `kernel/setup/steps/ezstep_create_sites.php`,
  `kernel/classes/expkickstarter.php`, `bin/php/install.php`, `kernel/classes/expmaintenance.php`,
  `kernel/classes/ezsiteinstaller.php`

External:

- Exponential on GitHub: <https://github.com/se7enxweb/exponential>
- Exponential Velocity: <https://github.com/se7enxweb/exponential-velocity>
- Composer: <https://getcomposer.org/doc/>
- PHP command line usage: <https://www.php.net/manual/en/features.commandline.php>
- PHP `proc_open()` (used by the kickstarter's run log): <https://www.php.net/manual/en/function.proc-open.php>
- Package index of this version: <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml>

[Contents](README.md) · Previous: [3. Getting the code](03-getting-the-code.md) · Next: [5. The setup wizard](05-setup-wizard.md)
