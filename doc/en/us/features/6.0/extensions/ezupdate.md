# ezupdate: updates and packages in the admin

This page is for administrators who update an installation or add packages to it. `ezupdate` ("eZ Update") is the
package manager of an Exponential installation, in the admin and on the command line.

An installation is a few dozen Composer packages, a few dozen extension directories and a few settings that decide
which of them run. ezupdate puts all of that on one screen, shows where the pieces disagree, and lets you update and
install packages with the same guard rails you would use in a shell: a check that changes nothing, a dry run, a backup
confirmation, and Composer's own output as it runs.

It first shipped on 2 November 2024 (1.0.1) as a Composer update screen. Releases 1.1.3 to 1.1.10 (28 September to
2 October 2026) made it a full package manager. Open it at **Setup > Updates and packages** (`/update/dashboard`). The
extension's own guides (user guide, installed packages, configuration, command line, security, architecture, FAQ) are
in its `doc/` folder; this page is the overview.

## What you can do

| Tab or feature | What it does | Since |
|---|---|---|
| **Overview** | Where Composer is and which PHP runs it, whether updating and installing are allowed, a check for updates (`composer outdated`) that changes nothing, an update dry run, a guarded update, recent runs, and cards counting installed packages, extensions and what needs attention | 1.0.1, redesigned 1.1.8 |
| **Installed packages** (`/update/installed`) | `composer.json`, `composer.lock`, `vendor/composer/installed.json` and the extension directories side by side, with the extensions `ActiveExtensions` and each siteaccess's `ActiveAccessExtensions` switch on. Names what does not agree. Filter, search, sort, row details, shareable address (`#issues`), JSON and CSV downloads. Only reads files | 1.1.8 |
| **Find packages** | Search packagist.org (Exponential extensions by default) or every server in `composer.json`. A package page shows description, versions, requirements, license, downloads, git URL and commit of each version, and, when installed, how it was fetched and the branch and commit of its git working copy. Install from there, dry run first | 1.1.3 |
| **dist or source** | Install from release archives (fast) or as git clones with history, or let Composer decide, per run | 1.1.3 |
| **Package servers** | The Composer `repositories` of `composer.json` (packagist.org can be switched off) and the Exponential `.ezpkg` package servers the setup wizard reads; fetch a `.ezpkg` into the local package repository | 1.1.3 |
| **Live runs** | Updates and installs run in the background, one at a time, with a lock; the run page follows Composer's output as it is written, in colour, with **Follow**, **Wrap lines** and **Copy** | 1.1.3 |
| **Run again / Run it for real** | Every run can be run again; a dry run can be run for real, under the same switches and confirmations | 1.1.4 |
| **Funding** | Who the installed packages ask to be funded by, grouped as `composer fund` groups it, from package metadata and each package's `.github/FUNDING.yml`; as a page, JSON and text | 1.1.6 |
| **Command line** | Everything above from `php extension/ezupdate/bin/php/ezupdate.php` | 1.1.3 |

## Set it up

```bash
composer require se7enxweb/ezupdate
# settings/override/site.ini.append.php:
#   [ExtensionSettings]
#   ActiveExtensions[]=ezupdate
php bin/php/ezpgenerateautoloads.php --extension
php bin/php/ezcache.php --clear-all
```

Open **Setup > Updates and packages**. Administrators see it at once. For other roles, grant
`update/ezupdate` (look, check, dry run) and, where they may change the installation,
`update/manage` (update, install, fetch, change servers).

**Updating and installing are off until you switch them on**, in
`settings/override/ezupdate.ini.append.php`:

```ini
[UpdateSettings]
AllowUpdate=enabled
AllowInstall=enabled
```

Composer rewrites `vendor/` and every package it manages, so switch these on only where that
is wanted, with a backup (see [git_manager](git_manager.md) for backups from the admin).

## How an update goes

1. **Check for updates** (`composer outdated`): changes nothing.
2. **Preview (dry run)** of the update: shows what Composer would change. The Kind column is
   decided by Composer itself (one `composer update --dry-run` over the outdated packages):
   *Can be installed*, or *Blocked by composer.json (constraint)* (composer outdated's
   "semver-safe-update" does not look at composer.json). Only installable packages have a tick box.
3. Confirm that a **backup exists**, then **Install updates** (the ticked packages; disabled, with
   an explanation, while `AllowUpdate` is `disabled`; **Switch on updates** / **Switch off** write
   `settings/override/ezupdate.ini.append.php` through the kernel INI editor). It runs in the background; the page
   follows the output. Afterwards the commands in `[JobSettings] AfterRunCommands[]` regenerate
   the autoloads and clear the ini, template and content caches.
4. When a run ends the page shows a notice at the top: *The installation is up to date!
   Update again soon to remain secure.* if there was nothing to install, update or remove
   (Composer's "Nothing to modify in lock file" / "Nothing to install, update or remove", or
   zero outdated packages after *Check for updates*), a short summary of what changed
   otherwise, and the exit code if the run failed. It also appears while you watch the run.
5. If a run cannot be followed the page says why: signed out (sign in again and reload; the run
   continues on the server), refused with HTTP 403 (the policy is missing), or a server error
   (it retries, waiting longer each time, and stops after ten failures in a row).

## Settings

All keys are in `ezupdate.ini`; put your values in `settings/override/ezupdate.ini.append.php`.

| Block | Key | Default | Meaning |
|---|---|---|---|
| `ComposerSettings` | `Path`, `Binary` | empty | Directory and file name of Composer; empty tries `SearchPath[]` times `BinaryNames[]` |
| `ComposerSettings` | `SearchPath[]` | `var/ezupdate/`, `bin/`, `./`, `vendor/bin/`, then `/usr/local/bin/`, `/usr/bin/`, `/opt/cpanel/composer/bin/` | Where to look; relative entries are below the installation root and are searched first, because `open_basedir` (default Plesk) hides the system folders. A path set in `Path`/`Binary`/`PHPBinary` is used unchecked under `open_basedir`. The Overview's *Get Composer* downloads a SHA-256 verified `composer.phar` into `var/ezupdate/` |
| `ComposerSettings` | `BinaryNames[]` | `composer`, `composer.phar` | Names to try |
| `ComposerSettings` | `PHPBinary` | empty | PHP CLI that runs a `composer.phar` and the background jobs; empty uses the php next to the running one |
| `ComposerSettings` | `Timeout` | `900` | Seconds a Composer run may take |
| `ComposerSettings` | `ComposerHome` | `var/ezupdate/composer` | `COMPOSER_HOME` when the web server passes none; one folder per system account below it |
| `UpdateSettings` | `AllowUpdate` | `disabled` | Permit `composer update` from the admin |
| `UpdateSettings` | `AllowInstall` | `disabled` | Permit `composer require` from the admin |
| `UpdateSettings` | `PreferredInstall` | `auto` | `dist`, `source` or `auto` (source for dev versions, dist for releases) |
| `UpdateSettings` | `UpdateArguments[]`, `RequireArguments[]` | `--ansi`, `--no-progress` | Arguments always passed |
| `JobSettings` | `AfterRunCommands[]` | `bin/php/ezpgenerateautoloads.php --extension`, `bin/php/ezcache.php --clear-tag=ini,template,content` | Run after a successful update or install |
| `JobSettings` | `KeepJobs` | `20` | Finished jobs kept (log and status) |
| `PackagistSettings` | `URL` | `https://packagist.org` | Package index searched |
| `PackagistSettings` | `PerPage` | `25` | Search results per page |
| `PackagistSettings` | `CacheTime` | `900` | Seconds an answer is reused |
| `PackagistSettings` | `Types[]` | extension, kernel, library, any | Package types offered in the search form |
| `PackageServerSettings` | `Servers[<name>]` | none | `.ezpkg` servers added in the admin are written here as `https://` URLs; the server of `package.ini [RepositorySettings]` is always first |

## Command line

```bash
php extension/ezupdate/bin/php/ezupdate.php status
php extension/ezupdate/bin/php/ezupdate.php installed --issues
php extension/ezupdate/bin/php/ezupdate.php outdated
php extension/ezupdate/bin/php/ezupdate.php search seven
php extension/ezupdate/bin/php/ezupdate.php fund --direct --json
```

The commands are `status`, `installed [--issues] [--json]`, `outdated`, `search`, `show`,
`servers`, `server-add`, `server-remove`, `packagist on|off`, `packages`, `fetch`, `require`,
`update`, `jobs` and `fund [--direct] [--json]`. Runs started on one front end show up on the
other.

## Safety

Composer is started without a shell (`proc_open` with an argument list), in the installation
root, with a time limit, its own `COMPOSER_HOME`, no questions asked, and only pipes handed to
it (under Velocity none of the server's sockets). Server addresses must be `https://`; every
form carries the form token; Composer's output is escaped before its colours become HTML, so a
package description that contains markup stays text. Nothing is kept in statics, so a persistent
worker always reads current settings, and the views load their classes when the autoload array
does not know them yet.

## Behaviour changes

* 1.1.3 removed the `dump` and `show` views, the commit template and the *Dump assets* link.
  They belonged to `git_manager` and could not work here (one read `git_manager.ini`, one called
  a `GitManager` class this extension does not have, and the template posted to
  `git_manager/dashboard`). The Setup menu link to `git_manager/dump` went with them;
  `git_manager` carries its own.
* 1.1.12 shows a result notice after every finished run (up to date, what changed, failed).
* 1.1.13 adds Install updates (tick boxes per package, disabled with an explanation while updating is off, Switch on / Switch off) and labels packages by what Composer would install.
* The license texts are `LICENSE.md` and `doc/LICENSE.md` since 1.1.8.

## Languages

The extension carries translation files in `translations/<locale>/translation.ts`: eng-US and ger-DE. The German file
holds 280 messages (count `<message` in `translations/ger-DE/translation.ts`). The texts are looked up in the contexts
`extension/ezupdate`, `kernel/navigationpart` and `design/admin/parts/setup/menu`.

After editing a translation file, refresh the compiled translation cache with `./console exp:ezgeneratetranslationcache`
and clear the template and content caches. `./console exp:ezchecktranslation ger-DE` prints statistics of the kernel's
`share/translations/ger-DE/translation.ts`, not of this extension's file.

## Related pages

- [git_manager](git_manager.md): backups before updating
- [Package licenses and versions](../package-licenses-and-versions.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/ezupdate.md) and [release notes](../../../changelogs/extensions/ezupdate.md)
- [Change ledger](../../../history/ledger/ezupdate.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-11](../../../history/extensions/months/2024-11.md), [2026-06](../../../history/extensions/months/2026-06.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
