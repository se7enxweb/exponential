# The switch to expcomponents: what to check when you upgrade (6.0.15)

Since 6.0.15 the kernel's `composer.json` requires **expcomponents**, Exponential's fork of the Zeta Components, in
place of the upstream `zetacomponents/*` packages: `se7enxweb/expcomponents-mail` instead of `zetacomponents/mail`,
and so on for every library the kernel uses. The classes keep their `ezc` names and APIs, and each fork declares that
it replaces the old package name. For almost every installation the switch is one `composer update` and nothing else.
What the libraries are and why they are forked: [Exponential Components (expcomponents)](../../features/6.0/expcomponents.md).

This page is for administrators who upgrade an installation, developers of extensions and anyone who keeps a
`composer.json` of their own that names Zeta Components packages.

## Contents

1. [Who is affected](#who-is-affected)
2. [What changes in the kernel's composer.json](#what-changes-in-the-kernels-composerjson)
3. [Installations: the upgrade](#installations-the-upgrade)
4. [Projects that required zetacomponents packages themselves](#projects-that-required-zetacomponents-packages-themselves)
5. [Extensions](#extensions)
6. [Custom code](#custom-code)
7. [The replace semantics in detail](#the-replace-semantics-in-detail)
8. [Autoloading is unchanged](#autoloading-is-unchanged)
9. [Verifying the result](#verifying-the-result)
10. [Troubleshooting](#troubleshooting)
11. [Rollback](#rollback)
12. [Related pages](#related-pages)

## Who is affected

| You | What to do |
|---|---|
| An installation that uses the kernel's `composer.json` unchanged | Run the upgrade as usual (`./console exp:upgrade` or `composer update`); nothing else |
| A project whose own `composer.json` lists `zetacomponents/*` packages | Change those lines to the fork names, or leave them: they still resolve ([below](#projects-that-required-zetacomponents-packages-themselves)) |
| An extension whose `composer.json` requires `zetacomponents/*` | Nothing is required; switch to the fork names in your next release ([Extensions](#extensions)) |
| Custom code that uses `ezc*` classes | Nothing; the classes are the same |
| Custom code or scripts that include files under `vendor/zetacomponents/` by path | Change the path to `vendor/se7enxweb/expcomponents-<name>/`, or better, rely on the autoloader |
| Deployment scripts, build images or license reports that grep for `zetacomponents` | Look for `expcomponents` as well |

## What changes in the kernel's composer.json

Before (6.0.14 and the 6.0.15 line until the switch), with the two forks that were already required:

```json
"se7enxweb/expcomponents-archive": "~1.5.2",
"zetacomponents/authentication": "~1.4.1",
"zetacomponents/cache": "~1.6.3",
"zetacomponents/configuration": "~1.4",
"zetacomponents/console-tools": "^1.7.5",
"zetacomponents/database": "~1.5.4",
"zetacomponents/debug": "~1.3",
"zetacomponents/event-log": "~1.5",
"zetacomponents/feed": "~1.4.6",
"zetacomponents/image-conversion": "~1.5.1",
"zetacomponents/mail": "~1.10.1",
"zetacomponents/mvc-tools": "~1.2.4",
"zetacomponents/persistent-object": "~1.8.1",
"zetacomponents/signal-slot": "~1.2",
"zetacomponents/system-information": "~1.1.2",
"se7enxweb/expcomponents-webdav": "~1.2.2",
"zetacomponents/base": "^1.9.5"
```

After (the 6.0.15 release, with every fork released on 2026-11-06): every line names `se7enxweb/expcomponents-<name>`.
The package mapping, one to one, with the constraint the kernel requires:

| Before | After |
|---|---|
| `zetacomponents/archive` (already `se7enxweb/expcomponents-archive ~1.5.2`) | `se7enxweb/expcomponents-archive ~1.5.3` |
| `zetacomponents/authentication` | `se7enxweb/expcomponents-authentication ~1.4.2` |
| `zetacomponents/base` | `se7enxweb/expcomponents-base ^1.9.6` |
| `zetacomponents/cache` | `se7enxweb/expcomponents-cache ~1.6.4` |
| `zetacomponents/configuration` | `se7enxweb/expcomponents-configuration ~1.4.1` |
| `zetacomponents/console-tools` | `se7enxweb/expcomponents-console-tools ^1.7.6` |
| `zetacomponents/database` | `se7enxweb/expcomponents-database ~1.5.5` |
| `zetacomponents/debug` | `se7enxweb/expcomponents-debug ~1.3.1` |
| `zetacomponents/event-log` | `se7enxweb/expcomponents-event-log ~1.5.1` |
| `zetacomponents/feed` | `se7enxweb/expcomponents-feed ~1.4.7` |
| `zetacomponents/image-conversion` | `se7enxweb/expcomponents-image-conversion ~1.5.2` |
| `zetacomponents/mail` | `se7enxweb/expcomponents-mail ~1.10.2` |
| `zetacomponents/mvc-tools` | `se7enxweb/expcomponents-mvc-tools ~1.2.5` |
| `zetacomponents/persistent-object` | `se7enxweb/expcomponents-persistent-object ~1.8.3` |
| `zetacomponents/signal-slot` | `se7enxweb/expcomponents-signal-slot ~1.2.1` |
| `zetacomponents/system-information` | `se7enxweb/expcomponents-system-information ~1.1.3` |
| `zetacomponents/webdav` (already `se7enxweb/expcomponents-webdav ~1.2.2`) | `se7enxweb/expcomponents-webdav ~1.2.3` |

`zetacomponents/image-analysis` was never in the kernel's `require`; it came with `image-conversion` and now comes
with its fork as `se7enxweb/expcomponents-image-analysis` (1.2.1). Every fork is listed with its repository and
release on the [feature page](../../features/6.0/expcomponents.md#the-packages); the kernel requires each one in the
tilde or caret form, never as an exact version.

## Installations: the upgrade

1. Take a backup as for any upgrade (`./console exp:upgrade` does it for you).
2. Update the code to the release, then the libraries. With the console, both in one run:

   ```bash
   ./console exp:upgrade --to=v6.0.15            # dry run: shows the library changes among the rest
   ./console exp:upgrade --apply --to=v6.0.15    # the code, then `composer install` from the release's lock file
   ```

   By hand, depending on how the installation was made ([Updating Exponential 6.0](../../UPDATE.md)):

   ```bash
   # a Git clone of the kernel: after checking out the release tag, install what its composer.lock names
   composer install --no-dev
   # a Composer project that requires se7enxweb/exponential: update the kernel with its new requirements
   composer update se7enxweb/exponential --with-all-dependencies
   ```

   Composer removes the `zetacomponents/*` packages and installs the `se7enxweb/expcomponents-*` packages. Its output
   shows one "Removing" and one "Installing" line per library; that is the expected result.
3. Regenerate the class maps of the kernel and the extensions and clear the caches, as after every library update:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

4. Restart the PHP workers so they load the new files: reload PHP-FPM, or restart Velocity
   (`./console exp:velocity restart`), whichever serves the site.
5. [Verify](#verifying-the-result).

No database change, no INI change and no template change belongs to this switch.

## Projects that required zetacomponents packages themselves

Some projects keep their own `composer.json` (a site project around the kernel, a platform project with the legacy
bridge) that names Zeta Components packages directly, for example because an old guide said to add
`zetacomponents/image-conversion` for ezie.

**Recommended: rename the requirements.** Replace each `zetacomponents/<name>` by `se7enxweb/expcomponents-<name>`,
keep the constraint's meaning, and update:

```bash
composer remove --no-update zetacomponents/mail zetacomponents/feed
composer require --no-update se7enxweb/expcomponents-mail:~1.10.2 se7enxweb/expcomponents-feed:~1.4.7
composer update --with-all-dependencies 'se7enxweb/expcomponents-*' 'zetacomponents/*'
```

These are the constraints of the 6.0.15 `composer.json`; use those of your release.

**Also possible: leave them.** A requirement on `zetacomponents/mail` is satisfied by `se7enxweb/expcomponents-mail`,
because the fork replaces that name. Composer installs the fork (which the kernel requires) and not the upstream
package. Two cases need care:

- **An exact upstream version** (`"zetacomponents/mail": "1.10.1"`) is met only when the installed fork has exactly
  that version. The kernel requires a later fork release (the fork's releases continue after the upstream one), so
  Composer reports that the requirements cannot be resolved. Loosen it to `~1.10.1` or rename it.
- **A constraint above the fork's line** (for example a future `zetacomponents/mail ^2.0`) cannot be met by a 1.x
  fork. Rename it and follow the fork's releases.

## Extensions

No extension shipped with Exponential requires a `zetacomponents/*` package in its `composer.json`. For your own:

- an extension that requires `zetacomponents/<name>` keeps installing: the kernel brings the fork, and the fork
  replaces that name;
- in its next release, require `se7enxweb/expcomponents-<name>` instead, with the constraint you need;
- if the extension must also install with Exponential 6.0.14 or older, which require the upstream packages, keep the
  upstream name for now: on those releases the fork is not installed, and the upstream package is;
- `extension.xml` and `ezinfo.php` do not name libraries and need no change.

An extension that bundles a copy of a Zeta Component inside its own folder (old practice from before Composer) should
drop the copy and require the fork: two copies of the same `ezc` class cannot be loaded together.

## Custom code

- **Classes and methods:** unchanged. `ezcMailComposer`, `ezcArchive::open()`, `ezcFeed`, `ezcWebdavServer`,
  `ezcConsoleInput`, `ezcBaseFile::findRecursive()` work as before.
- **Paths:** code must not include library files by path. If yours does (`require 'vendor/zetacomponents/...'`),
  change it to rely on Composer's autoloader, or to `vendor/se7enxweb/expcomponents-<name>/...`.
- **Tests that check for packages** (`InstalledVersions::isInstalled('zetacomponents/mail')`): Composer's runtime API
  reports replaced names as installed, so this keeps returning `true`; a test that reads `vendor/zetacomponents/`
  from disk does not.
- **Strings in user agents:** the mail library's default `User-Agent` text is part of the library; it is unchanged in
  the fork's 1.10.2 ("Apache Zeta Components"). A site that sets `[MailSettings] UserAgent` is not affected at all.
- **Mail addresses:** `se7enxweb/expcomponents-mail` 1.10.2 refuses an address that contains whitespace or one of
  `< > , ; "` before it writes any header; such an address could end the header or add recipients. Code that built a
  sender or recipient from user input and relied on the library to pass it through now gets an exception; validate
  the address first (`eZMail::validate()`).

## The replace semantics in detail

Each fork's `composer.json` has a `replace` section with the old name and `self.version`:

```json
"replace": {
    "zetacomponents/mail": "self.version"
}
```

- `self.version` means "the same version as this package": `se7enxweb/expcomponents-mail` 1.10.2 stands in for
  `zetacomponents/mail` 1.10.2.
- Composer never installs a package and its replacement together. When the fork is required (by the kernel), every
  requirement on the old name is met by it, and the upstream package is removed if it was installed.
- The forks require each other by their fork names (the archive fork requires `se7enxweb/expcomponents-base ~1.8`).
  A third-party package that still requires `zetacomponents/base` is served by the base fork through its `replace`.
- The WebDAV fork replaces two names: `zetacomponents/webdav` and `se7enxweb/zetacomponents-webdav`, its own name for
  release 1.2.1.
- `replace` is about packages, not classes. There is no `class_alias` and no second class name; the class is simply
  the same.

## Autoloading is unchanged

The libraries are loaded by **Composer's class map**: each fork declares `"autoload": {"classmap": ["src"]}`, as the
upstream package did, and `composer install` or `composer update` writes the map into `vendor/composer/`. The kernel's
`autoload.php` includes `vendor/autoload.php` and then registers its own `ezpAutoloader` for the kernel's and the
extensions' classes; `ezcBase::autoload` is registered only when `EZCBASE_ENABLED` is defined as `true` (an old setup
without Composer), which the kernel does not do. None of this changes. `php bin/php/ezpgenerateautoloads.php` does not
read `vendor/` and does not need to know about the switch; run it as after every upgrade.

Velocity loads `vendor/` classes at its warm-up like the rest; a restart after the update is enough
(`./console exp:velocity restart`).

## Verifying the result

```bash
composer show | grep expcomponents        # one line per fork, with its version
composer show | grep zetacomponents       # should print nothing (only development packages in a dev install)
composer licenses --no-dev | grep expcomponents   # each fork with Apache-2.0
```

- Setup > System information (`/setup/info`) shows the server information, which comes from
  `se7enxweb/expcomponents-system-information`.
- `setup.ini [ezcversion]` still checks for the class `ezcBaseFile`; the setup wizard's system check passes.
- Let the site send a mail (a password reminder, for example), open an RSS export, import a package
  and, if WebDAV is in use, connect a client: each uses one of the forks.
- `composer why zetacomponents/base` prints the packages that still require the old name: none of the kernel's, at
  most a third-party package of yours. That is harmless: the base fork meets it.

In a development installation (`composer install` with dev packages) `zetacomponents/unit-test` and other development
packages of the forks' own test suites may still appear; they are not part of a site.

## Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `Your requirements could not be resolved` naming `zetacomponents/<name>` with an exact version | A `composer.json` of yours pins the upstream version | Use a tilde constraint or the fork's name ([above](#projects-that-required-zetacomponents-packages-themselves)) |
| `se7enxweb/expcomponents-<name> ... could not be found` | The fork's release is not published yet in the version the `composer.json` asks for, or Composer's cache is old | `composer clear-cache`, then try again; check the fork's releases on GitHub |
| A class `ezc...` is not found after the update | `vendor/` was updated but the PHP workers still run the old class map, or `composer install` did not finish | Run `composer install` again, then restart PHP-FPM or Velocity |
| Both `vendor/zetacomponents/<name>` and `vendor/se7enxweb/expcomponents-<name>` exist | A copy left behind by a manual change of `vendor/` | Run `composer install`; it removes the folders of packages it uninstalled. A folder Composer never installed stays: check that `composer show` does not list it, then delete it. Never edit `vendor/` by hand |
| A deprecation notice from a file in `vendor/se7enxweb/expcomponents-*` | A real issue in the fork | Report it to that fork's repository ([Reporting issues](../../features/6.0/expcomponents.md#reporting-issues-and-contributing)) |

## Rollback

The switch can be undone, because the classes are the same. Where depends on how the installation is made:

- **A Git clone of the kernel:** the requirements are in the kernel's own `composer.json`. Put the upstream
  requirements back, with the constraints of your previous release (the "Before" list
  [above](#what-changes-in-the-kernels-composerjson)), in place of the fork names you want to undo; or restore
  `composer.json` and `composer.lock` from your backup.
- **A Composer project that requires `se7enxweb/exponential`:** the kernel package itself requires the forks, and a
  project cannot remove a requirement of a package it depends on. Going back means returning to the kernel release
  you came from (its constraint in your `composer.json`, or your backup of `composer.json` and `composer.lock`).

Then:

1. Make the change above.
2. Update the libraries and restart the workers:

   ```bash
   composer update --no-dev --with-all-dependencies 'se7enxweb/expcomponents-*' 'zetacomponents/*'
   php bin/php/ezcache.php --clear-all
   ```

3. Verify as above, the other way round.

What a rollback loses: the fixes the forks carry. Do **not** roll back `se7enxweb/expcomponents-webdav` on a site that
uses WebDAV: its 1.2.2 release fixes path traversal, symbolic links out of the root, document type declarations in
request bodies, digest comparison and lock tokens, and the upstream `zetacomponents/webdav` 1.2 has none of that. The
archive fork (1.5.2 and later) was required before the rest of the switch and should stay as well. A rollback is meant as a way to
isolate a problem; report the problem so it can be fixed in the fork.

`./console exp:upgrade:rollback` restores the whole installation from the backup of the upgrade, libraries included.

## Related pages

- [Exponential Components (expcomponents)](../../features/6.0/expcomponents.md)
- [Specification: the expcomponents packages](../../specifications/6.0/expcomponents-packages.md)
- [Contributing to expcomponents](../../guides/contributing-to-expcomponents.md)
- [Upgrading with the console](../../guides/upgrading-with-the-console.md)
- [Behaviour changes of October 2026](behaviour-changes-2026-10.md) (the Zeta Components the kernel stopped requiring)
- [The 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
