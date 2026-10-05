# 3. Getting the code

This chapter puts the Exponential code on your machine. There are three ways: `composer create-project` (the
quickest for a new site), a `git clone` followed by `composer install` (when you want the history and to follow
development) and `composer require` into an empty directory. All three end with the same files. The chapter explains
which version each way gives you and how to choose one, how the extensions arrive as Composer packages, how to add
the optional Exponential Velocity package, what to do on PHP 8.0 to 8.3, and closes with a tour of every top-level
directory of the installation root.

[Contents](README.md) · Previous: [2. Requirements](02-requirements.md) · Next: [4. Choosing an install method](04-choosing-an-install-method.md)

## 3.1 Versions, tags and branches

Exponential is published as the Composer package
[`se7enxweb/exponential`](https://packagist.org/packages/se7enxweb/exponential) from the Git repository
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential).

| What | Name | Gives you |
|---|---|---|
| Release tags | `v6.0.0` to `v6.0.14` (the 6.0 line); older lines use tags without `v`, such as `4.4.0` | an exact, permanent release |
| Default branch | `main` | the current development of the 6.0 line, today 6.0.15 |
| Release branches | `6.0.7`, `6.0.8`, ... `6.0.14` | the state of each release line |

The newest release tag at the time of writing is `v6.0.14`; the `main` branch identifies itself as `6.0.15stable`
(`lib/version.php`) and carries the changes described in [Changelog 6.0.15](../changelogs/6.0/6.0.15.md). To see what
is published now:

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
```

or, inside a clone, after `git fetch --tags`:

```bash
git tag -l 'v*' --sort=version:refname | tail -5
```

Always sort by version (`--sort=version:refname` or `sort -V`), never by name: by name `v6.0.10` sorts before
`v6.0.8`. The project's releases are listed with their notes on
[GitHub Releases](https://github.com/se7enxweb/exponential/releases).

**A release tag never changes.** Once published, a tag and the package version Packagist built from it are
permanent; a fix always arrives as the next version. You can pin a tag and rely on getting the same files every time.

**Which version needs which PHP.** `v6.0.8` to `v6.0.14` require PHP 8.1 or later in their `composer.json`; PHP 8.0
support arrives with 6.0.15 (`"php": "^8.0 || ..."`), which until its tag is published is the `main` branch. On
PHP 8.0, take `main` (`dev-main` for Composer) until a tag of the 6.0.15 line exists.

## 3.2 Three ways to get the code

### A. composer create-project

The quickest way to a new site. Composer downloads the package, makes the directory your project and installs every
dependency:

```bash
cd /var/www
composer create-project se7enxweb/exponential exponential
cd exponential
```

Without a version, Composer takes the newest stable release. To pin one, or to follow the development branch:

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential
composer create-project se7enxweb/exponential:dev-main exponential
```

`create-project` treats the downloaded `composer.json` as the root package, so its `config.allow-plugins` (which
allows `se7enxweb/exponential-legacy-installer`) and its `post-install-cmd` script apply: the class maps are generated
at the end (`php bin/php/ezpgenerateautoloads.php`).

### B. git clone and composer install

When you want the history, want to follow `main` or switch between tags:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git checkout v6.0.14        # optional: a release instead of main
composer install
```

The clone holds the kernel, the kernel-shipped extensions (`ezjscore`, `ezoe`, `ezformtoken`, `expservices`) and the
designs; `composer install` adds `vendor/` and the extensions `composer.json` requires. Those extensions are not part
of the repository, so `git status` lists them as untracked; that is expected. To update later:

```bash
git pull
composer install
```

or, to move to a release, `git checkout v6.0.<n>` followed by `composer install`.

### C. composer require into an empty directory

When the installation should be a project of your own, with your own `composer.json`:

```bash
mkdir exponential
cd exponential
composer require se7enxweb/exponential
```

The package has the type `ezpublish-legacy`. The installer plugin (`LegacyKernelInstaller`) installs a package of that
type into the project root (`.`), so the kernel files land in the directory you are in, not in `vendor/`. In a
Symfony project (one whose `composer.json` sets `extra.symfony-app-dir`) it goes to `ezpublish_legacy/` instead; a
project can name another directory with `extra.ezpublish-legacy-dir`.

Two things differ from ways A and B:

- Your new `composer.json` does not yet allow the installer plugin. Composer 2.2 and later asks whether to trust
  `se7enxweb/exponential-legacy-installer`; answer yes, or add it beforehand:

  ```bash
  composer config allow-plugins.se7enxweb/exponential-legacy-installer true
  ```

- Composer runs only the scripts of the root package, which is now yours. Generate the class maps yourself after each
  install or update:

  ```bash
  php bin/php/ezpgenerateautoloads.php
  ```

### Check the result

Whichever way you took:

```bash
php bin/php/console --version
ls vendor/autoload.php
```

The first line of the console's output names the version, for example `console (Exponential) 6.0.15stable`. If
`vendor/autoload.php` is missing, Composer did not finish; run `composer install` again and read its output.

## 3.3 Installing on PHP 8.0 to 8.3

`composer.json` has a development requirement, `"phpunit/phpunit": "^13.4"`, and PHPUnit 13 needs PHP 8.4
(`vendor/phpunit/phpunit/composer.json`: `"php": ">=8.4.1"`). The repository ships no `composer.lock`, and without a
lock file Composer resolves `require-dev` as well, even when it is told `--no-dev` not to install it. On PHP 8.0 to
8.3 the resolution therefore fails on PHPUnit.

The project's own PHP 8.0 check (the job `php80` of `.github/workflows/quality.yml`) installs like this, and so can
you:

```bash
composer remove --dev --no-update --no-interaction phpunit/phpunit zetacomponents/php-generator
composer install --no-dev --prefer-dist
```

The first command removes the two development requirements from `composer.json` without installing anything; the
second installs what a site needs. With `create-project`, stop before the install and do the same:

```bash
composer create-project --no-install se7enxweb/exponential:dev-main exponential
cd exponential
composer remove --dev --no-update --no-interaction phpunit/phpunit zetacomponents/php-generator
composer install --no-dev --prefer-dist
```

The CI job also passes `--ignore-platform-req=ext-mongodb`, so that a machine without the `mongodb` PHP extension
installs. If Composer stops on a missing `ext-mongodb` and you do not use MongoDB, add that option.

On a production server, `--no-dev` is right on every PHP version: the test tools are not needed to run a site.

## 3.4 The optional Velocity package

Exponential Velocity, the recommended application server, is a separate package,
[`se7enxweb/exponential-velocity`](https://packagist.org/packages/se7enxweb/exponential-velocity). `composer.json`
suggests it rather than requires it, because it needs PHP 8.1 (`"php": ">=8.1"`) and the kernel runs on 8.0. On
PHP 8.1 or later, add it with the version the suggestion names:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.42
```

`~0.0.4.42` accepts every later `0.0.4.x` release (`>=0.0.4.42 <0.0.5.0`). The last position of a Velocity version
counts on past 9 (`0.0.4.9`, `0.0.4.10`, ...), so a version sort, not a name sort, finds the newest.

The package lands in `vendor/se7enxweb/exponential-velocity/` (its type is `project`, so it is not moved into
`extension/`). The console finds it there: `php bin/php/console exp:velocity status` reports the engine, and
`exp:velocity start` starts it. Before the package is installed, the `qbix` engine cannot start
(`velocity: no server script at ...`); the `php` engine (PHP's built-in server) works without it:

```bash
php bin/php/console exp:velocity start --engine=php
```

Velocity is also published as operating system packages (`.deb`, `.rpm`) and as a standalone binary; see
[Velocity packages and binaries](../features/6.0/velocity-packages-and-binaries.md). Before you upgrade it, read
[Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md).

## 3.5 Extensions are Composer packages

Every extension except the four kernel-shipped ones is a Composer package of type `ezpublish-legacy-extension`. The
installer plugin `se7enxweb/exponential-legacy-installer` (required by `composer.json`) places such a package in
`extension/<name>`:

| The package says | It is installed in |
|---|---|
| `"extra": { "ezpublish-legacy-extension-name": "eztags" }` (package `se7enxweb/eztags`) | `extension/eztags` |
| `"extra": { "ezpublish-legacy-extension-name": "explayouts_ui" }` (package `se7enxweb/explayouts-ui`) | `extension/explayouts_ui` |
| no `ezpublish-legacy-extension-name` (package `vendor/name`) | `extension/name` |

So `composer.json` decides which extensions an installation has, and their versions:

```json
"se7enxweb/eztags": "~2.4.11",
"se7enxweb/cjw_newsletter": "~4.2.0",
"se7enxweb/explayouts": "~1.4.15",
```

The tilde constraint floats the last position given: `~2.4.11` means `>=2.4.11 <2.5.0`. To add an extension, require
its package, then activate it and regenerate the class maps:

```bash
composer require se7enxweb/ezfind
php bin/php/ezpgenerateautoloads.php -e
```

Being on disk does not make an extension active: it must be listed in `[ExtensionSettings] ActiveExtensions[]`. The
installers activate the extensions of the site package; others you activate in the admin (**Setup > Extensions**) or
in `settings/override/site.ini.append.php`. Then clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`).

`composer.json`'s `suggest` block lists further extensions with a sentence each: `ezsi`, `ezscriptmonitor`, `ezfind`,
`ezauthorize`, `bcurlaliaswithdash`, `sevenx_valkey`, `sevenx_valkey_cache`, `ezownerchange`, `adminaid`, `ezpm` and
`hcaptcha` among them. Every extension has its own repository under
[github.com/se7enxweb](https://github.com/se7enxweb) and carries its version in `ezinfo.php` and `extension.xml`; the
admin's extension list shows it.

> **Do not run `composer update` casually on a live site.** It moves every package to the newest version its
> constraint allows. When an extension directory is a Git working copy of its own (common on development machines),
> Composer replaces it. Update one package deliberately (`composer update se7enxweb/<package>`) and read its release
> notes first.

More: [Extensions guide](../guides/extensions.md), [Extension list and downloads](../features/6.0/extension-list-and-downloads.md),
[Default extension distribution](../features/6.0/default-extension-distribution.md),
[Extension loading order](../features/6.0/extension-loading-order.md),
[Additional extension directories](../features/6.0/additional-extension-directories.md).

## 3.6 A tour of the installation root

The entries a fresh installation has, in alphabetical order. "Tracked" means the file or directory is part of the
Git repository; the others are created by Composer or at run time.

### Directories

| Directory | Tracked | What it holds |
|---|---|---|
| `autoload/` | yes | `ezp_kernel.php`, the generated class map of the kernel. Regenerated with `php bin/php/ezpgenerateautoloads.php -k`. |
| `benchmarks/` | yes | Micro-benchmarks of kernel parts (templates, hashing). |
| `bin/` | yes | Command line tools: `bin/php/` (the PHP scripts, each also a console command `exp:<name>`, and the console itself, `bin/php/console`), `bin/shell/` (shell scripts, `shell:<name>`), `bin/mongodb/` (MongoDB index scripts), `bin/startup/`, `bin/linux/`, `bin/phpcs/`, `bin/awk/`. |
| `cronjobs/` | yes | The cronjob parts `runcronjobs.php` runs (`basket_cleanup.php`, `linkcheck.php`, `audit.php`, ...). |
| `design/` | yes | The designs: `standard` and `base` (fallbacks), `admin`, `admin3`, `admin4`, `admin4l`, `editor`, and the examples `mysite` and `plain`. Theme extensions add more. |
| `doc/` | yes | The documentation, this book included. |
| `extension/` | partly | Extensions. The repository ships `ezjscore`, `ezoe`, `ezformtoken` and `expservices`; Composer adds the others. |
| `kernel/` | yes | The kernel: content model, modules, datatypes, the setup wizard (`kernel/setup/`), the console commands' classes (`kernel/private/classes/commands/`). |
| `lib/` | yes | The general libraries: database drivers (`lib/ezdb/`), templates (`lib/eztemplate/`), files, locales, images, sessions; `lib/version.php` holds the version. |
| `schemas/` | yes | XML schemas (the translation file schema). |
| `settings/` | yes | The shipped `.ini` files. `settings/siteaccess/<name>/` and `settings/override/` hold your installation's settings and are written by the installers. |
| `share/` | yes | `db_schema.dba` and `db_data.dba` (the database the installer creates), `locale/` and `translations/`, `codepages/` and `transformations/` (character sets), `icons/`, `maintenance.html`, and `filelist.md5`, the file manifest the admin's upgrade check compares against. |
| `support/` | yes | The sources of `ezlupdate`, the translation extraction tool. |
| `templates/` | yes | Code templates for the class generator (`.ctpl`). |
| `tests/` | yes | The PHPUnit test suite (`tests/runtests.php`, `phpunit.xml`). |
| `update/` | yes | Database update files per engine and version (`update/database/<engine>/`) and data repair scripts (`update/common/scripts/`); see [chapter 11](11-upgrading.md). |
| `var/` | no (only `var/webdav/`) | Everything written at run time: `var/cache/`, `var/log/`, `var/storage/` (uploaded files, packages, the SQLite database in `var/storage/sqlite3/`), `var/autoload/` (the extensions' class maps), `var/vc/` (Velocity's run files and logs). Must be writable by the web server. |
| `vendor/` | no | The Composer libraries: Zeta Components, the installer plugin, Velocity when installed. |

### Files

| File | What it is |
|---|---|
| `index.php` | The front controller: every page request goes through it. |
| `index_rest.php` | The front controller of the REST API (`/api/`). |
| `index_cluster.php` | Serves files from the database cluster. |
| `index_treemenu.php` | The fast endpoint of the admin's content tree menu. |
| `soap.php`, `webdav.php` | The SOAP and WebDAV entry points. |
| `runcronjobs.php` | Runs cronjob parts: `php runcronjobs.php --list`. |
| `autoload.php` | Loads Composer's autoloader and the kernel's; every entry point starts with it. |
| `ezpm.php` | The package manager on the command line. |
| `composer.json` | The requirements: PHP, extensions, Zeta Components. `composer.dev.json` holds the development tools of `make devtools` (installed into `.devtools/`); `composer.json.dist` is an older upstream manifest kept for reference. |
| `.htaccess_root` | Apache rewrite rules; copy to `.htaccess` (`cp .htaccess_root .htaccess`). `.htaccess_root_static` is the same with the rules that serve the static cache ahead of the application. |
| `kickstart.ini-dist` | The commented template of `kickstart.ini`; `kickstart.ini--example` is an example. |
| `config.php-RECOMMENDED` | A documented list of the settings `config.php` can make; rename to `config.php` and uncomment what you need. |
| `config.env.php.example` | Copy to `config.env.php` to name the machine's environment (`EXP_ENV`); see [Settings per environment](../features/6.0/environment-settings.md). |
| `exponential.cron`, `ezpublish.cron` | Example crontabs for the cronjobs (`crontab exponential.cron` after setting its two variables). |
| `sw.js`, `index.js` | The site's service worker. |
| `robots.txt`, `favicon.ico` | As usual for a web site. |
| `phpunit.xml`, `phpcs.xml.dist`, `phpstan.neon.dist`, `phpstan-baseline.neon`, `Makefile` | Development and quality tools. |
| `LICENSE`, `COPYRIGHT.md`, `README.md`, `CONTRIBUTING.md`, `SECURITY.md` | Licence, copyright, the project README, how to contribute, how to report a vulnerability. |

Files that appear after an install and are yours, never to be committed or shared: `settings/override/*` (database
credentials), `kickstart.ini` (the database password), `config.env.php`, and `var/log/initial-admin-password`.

## 3.7 Permissions after getting the code

The user the web server or Velocity's workers run as must be able to write `design`, `extension`, `settings`,
`settings/override`, `settings/siteaccess` and `var` with everything below it (the list the setup wizard checks is
`[directory_permissions] CheckList` in `settings/setup.ini`). If you ran Composer as a different user, hand the files
over before the install, in this form (the setup wizard prints the exact commands for your machine):

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

Serving and permissions are covered in depth in [chapter 8](08-serving-the-site.md).

## References

In this repository:

- [Installation overview](../INSTALL.md), section 3.
- [Upgrading](../guides/upgrading.md): moving an existing site to new code.
- [PHP 8.0 support](../bc/6.0/php-8.0-support.md): installing with Composer on PHP 8.0.
- [Continuous integration](../specifications/6.0/continuous-integration.md) and [Quality checks](../features/6.0/quality-checks.md).
- [Velocity engines](../bc/6.0/velocity-engines.md), [Velocity packages and binaries](../features/6.0/velocity-packages-and-binaries.md),
  [Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md).
- [Extensions guide](../guides/extensions.md), [Extensions](../features/6.0/extensions/README.md),
  [Extension metadata](../specifications/6.0/extension-metadata.md).
- [Repairing an installation](../bc/6.0/repair.md): when `vendor/` is missing.
- [Console](../bc/6.0/console.md).
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md) and the [extensions' release notes](../changelogs/extensions/README.md).

External:

- Composer: [create-project](https://getcomposer.org/doc/03-cli.md#create-project),
  [install](https://getcomposer.org/doc/03-cli.md#install-i), [require](https://getcomposer.org/doc/03-cli.md#require-r),
  [remove](https://getcomposer.org/doc/03-cli.md#remove-rm),
  [version constraints](https://getcomposer.org/doc/articles/versions.md),
  [allow-plugins](https://getcomposer.org/doc/06-config.md#allow-plugins),
  [scripts](https://getcomposer.org/doc/articles/scripts.md).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential),
  [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity),
  [all se7enxweb packages](https://packagist.org/packages/se7enxweb/).
- GitHub: [se7enxweb/exponential](https://github.com/se7enxweb/exponential),
  [releases](https://github.com/se7enxweb/exponential/releases),
  [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity),
  [all se7enxweb repositories](https://github.com/se7enxweb).
- Git: [git clone](https://git-scm.com/docs/git-clone), [git tag](https://git-scm.com/docs/git-tag).

[Contents](README.md) · Previous: [2. Requirements](02-requirements.md) · Next: [4. Choosing an install method](04-choosing-an-install-method.md)
