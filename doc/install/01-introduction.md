# 1. Introduction: what you are installing

This chapter explains what Exponential is and what an installation consists of before you install one: the kernel,
the extensions, the siteaccesses `site`, `admin` and `editor`, the designs and the site package. It then explains how
this book is organised, the conventions every chapter follows (where commands are run, how settings are written, how
to read the tables), and ends with a short glossary of the words the installation chapters use. Read it once; the
later chapters assume the vocabulary introduced here.

[Contents](README.md) · Next: [2. Requirements](02-requirements.md)

## 1.1 What Exponential is

Exponential is an open source content management system and application framework written in PHP. Its most notable
feature is a content model you define yourself: you describe kinds of content (content classes such as Article or
Product) as lists of typed fields (attributes), and every piece of content is an object of such a class, kept with
its versions and translations and placed in a content tree. Standard CMS functionality, such as news publishing,
e-commerce and forums, is built in, together with user management, a permission system of roles and policies,
multi-language content and a template language for the output.

Exponential 6 continues a long-lived code base (its copyright lines start in 1998). It runs on PHP 8.0 to 8.5,
installs with Composer and can store its content in SQLite, MySQL or MariaDB, PostgreSQL, MongoDB or Oracle. Since
6.0.15 it also ships its own application server, **Exponential Velocity**, which is the recommended way to serve it
at every stage, from a developer's laptop to production. Apache or nginx with PHP-FPM remain fully supported.

The product is maintained by [7x](https://se7enx.com). The source code is on GitHub at
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential), and every part of it is a Composer package
on [Packagist](https://packagist.org/packages/se7enxweb/exponential). The licence is the GNU General Public License,
version 2 or later (`composer.json`: `GPL-2.0-or-later`).

> **A note on names.** Exponential grew out of a product with an older name. Some identifiers keep that history on
> purpose: kernel classes start with `eZ` (`eZINI`, `eZDB`, `eZContentObject`), many extensions start with `ez`
> (`ezjscore`, `ezoe`, `eztags`), the Composer package type is `ezpublish-legacy` and the database rows that hold the
> version are called `ezpublish-version` and `ezpublish-release`. They are names of code, not of the product, and this
> book writes them exactly as the code does. The product itself is always called Exponential.
> [Rebranding to Exponential](../features/6.0/rebranding-to-exponential.md) tells the story.

## 1.2 The parts of an installation

An installation is one directory on disk, the **installation root**: the directory that holds `index.php`,
`autoload.php`, `bin/`, `kernel/` and `settings/`. Everything Exponential needs lives below it, and the web server's
document root is that directory as well. The parts:

```
installation root
├── kernel/, lib/          the kernel: the code every installation runs
├── autoload/              the kernel's class map (autoload/ezp_kernel.php)
├── extension/             extensions: kernel-shipped ones and those Composer installs
├── design/                designs: templates, stylesheets, images
├── settings/              shipped settings; siteaccess/ and override/ hold yours
├── var/                   everything written at run time: cache, logs, storage, the SQLite database
├── vendor/                the libraries Composer installs (Zeta Components and others)
├── bin/                   command line tools; bin/php/console runs all of them
├── share/                 the database schema and data, locales, translations, the file manifest
├── update/                database update files and scripts for upgrades
└── index.php              the front controller every page request goes through
```

[Chapter 3](03-getting-the-code.md#36-a-tour-of-the-installation-root) tours every top-level entry in detail.

### The kernel

The kernel is the code in `kernel/` and `lib/`. `lib/` holds the general libraries (database drivers in `lib/ezdb/`,
the template engine in `lib/eztemplate/`, files, locales, images, sessions); `kernel/` holds the content model, the
modules that answer URLs (`content/view`, `user/login`, `setup/...`), the setup wizard (`kernel/setup/`) and the
datatypes. The version of the kernel is defined in `lib/version.php` (`VERSION_MAJOR`, `VERSION_MINOR`,
`VERSION_RELEASE`, `VERSION_STATE`); the command console prints it:

```bash
php bin/php/console --version
```

The first line names the version, for example `console (Exponential) 6.0.15stable`.

The kernel depends on the **Zeta Components** (`zetacomponents/*` in `composer.json`), a set of PHP libraries for
caching, configuration, mail, archives, image conversion and more. They are why Composer is required: without
`vendor/` the kernel cannot start (see [Troubleshooting](12-troubleshooting.md)).

### Extensions

An extension is a directory under `extension/` that adds settings, classes, modules, templates, designs or
command line scripts, without changing the kernel. An extension is only used when it is active: it is listed in
`[ExtensionSettings] ActiveExtensions[]` of `site.ini` (for every siteaccess) or `ActiveAccessExtensions[]` (for one
siteaccess).

Extensions arrive in two ways:

| Way | Which | Where they come from |
|---|---|---|
| Shipped inside the kernel repository | `ezjscore` (scripts, styles, jQuery, server functions), `ezoe` (the online editor), `ezformtoken` (form protection), `expservices` (remote services) | the `extension/` directory of the repository itself |
| Installed by Composer | everything else `composer.json` requires, for example `eztags`, `ezflow`, `ezwebin`, `ezoracle`, `cjw_newsletter`, the `explayouts*` and `expsite*` packages, `sevenx_dse`, `sevenx_themes_simple`, `sevenx-themes-media` | Packagist; each is a Composer package of type `ezpublish-legacy-extension` |

The Composer plugin `se7enxweb/exponential-legacy-installer` places every package of type `ezpublish-legacy-extension`
in `extension/<name>`, where `<name>` is the package's `extra.ezpublish-legacy-extension-name` or, when that is not
set, the part of the package name after the slash. That is why `se7enxweb/explayouts-ui` lands in
`extension/explayouts_ui`. [Chapter 3](03-getting-the-code.md#35-extensions-are-composer-packages) explains this in
detail. The extensions and what each one does are listed in [Extensions](../features/6.0/extensions/README.md);
building your own is covered by the [Extensions guide](../guides/extensions.md).

### Siteaccesses

A **siteaccess** is one way of reaching the installation, with its own settings and its own design. Every request is
matched to exactly one siteaccess, by the first element of the URL path, by the host name or by the port
(`[SiteAccessSettings] MatchOrder=uri;host;port` in `settings/site.ini`). The settings of a siteaccess live in
`settings/siteaccess/<name>/`.

An installation made by any of the three install methods has three siteaccesses:

| Siteaccess | Default name | For | Design |
|---|---|---|---|
| public site | `site` (the setup wizard takes the name from the site package) | visitors | the site package's design |
| administration | `admin` | administrators | `admin4`, or the admin design chosen |
| editor | `editor` | editors: the administration for content only | `editor`, which falls back to `admin4`, `admin3`, `admin2`, `admin` |

The editor siteaccess is made from the admin one. It shows only the content tabs (Dashboard, Content structure,
Media, Users, Store, Tags, Newsletter), has no developer toolbar, and the modules behind the hidden tabs (`setup`,
`visual`, `explayouts_ui`, `explayouts_ui_api`, `git_manager`, `xrowextract`, `bccie`) answer 404 there. See
[The setup wizard and the editor siteaccess](../features/6.0/setup-wizard-and-editor-siteaccess.md).

How siteaccesses are told apart is chosen during the install:

| Access type | Example: the site | Example: the admin | Example: the editor |
|---|---|---|---|
| URL path (`url`, the default) | `https://example.com/site/` | `https://example.com/admin/` | `https://example.com/editor/` |
| Host name (`hostname`; `exp:install --access=host`) | `www.example.com` | `admin.example.com` | `edit.example.com` |
| Port (`port`) | `https://example.com:8080/` | `https://example.com:8081/` | `https://example.com:8082/` |

The names are yours to choose. The defaults differ per method: `exp:install` uses `site` and `admin` (ports 8080 and
8081); the setup wizard proposes names made from the site package's identifier (`<identifier>` and
`<identifier>_admin` for URL paths, `<identifier>.<host>` and `<identifier>-admin.<host>` for host names, ports 8080
and 8081) and, for the editor, `editor`, `edit.<host>` or port 8082 (`kernel/setup/steps/ezstep_site_access.php`).

### Designs

A **design** is a set of templates (`.tpl`), stylesheets, scripts and images under `design/<name>/` (or
`extension/<ext>/design/<name>/`). A siteaccess names its own design in `[DesignSettings] SiteDesign` and
falls back through `AdditionalSiteDesignList[]` to `StandardDesign` (`standard`). When a template is needed, the
designs are searched in that order and the first one that has it wins, so a site design only needs the templates that
differ from the standard ones.

The kernel repository ships the designs `standard` and `base` (the fallbacks), the administration designs `admin`,
`admin3`, `admin4` and `admin4l`, the `editor` design and the example designs `mysite` and `plain`. The public site's
design comes with the site package or with a theme extension. How to find and override a template is in
[Templates and design](../guides/templates-and-design.md).

### Settings files

Settings are INI files (`site.ini`, `content.ini`, `image.ini`, ...) made of blocks (`[DatabaseSettings]`) of
`Key=Value` lines; a key ending in `[]` is a list. The shipped defaults are in `settings/` and are never edited. The
values of your installation are written as overrides, which are read after the defaults and win:

1. `settings/<file>.ini`: the shipped defaults.
2. The `settings/` directory of every active extension.
3. `settings/siteaccess/<siteaccess>/<file>.ini.append.php`: the values of one siteaccess.
4. `settings/override/<file>.ini.append.php`: the values of the whole installation, read last.

The installers write both `settings/siteaccess/` and `settings/override/`. Settings are cached in `var/cache/ini/`;
after editing a file by hand, clear that cache with `php bin/php/ezcache.php --clear-tag=ini`. The placement rules
are specified in [INI override placements](../specifications/6.0/ini-override-placements.md); the command that reads
and writes settings in every scope is [exp:ini](../features/6.0/exp-ini-command.md).

### The site package and the database

The content, the content classes, the users and roles, and often the design of a new site arrive as a **site
package**: an archive of content, classes and files installed at the end of the install. The default is
`sevenx_multisite`, the Exponential multisite package. Before the package is installed, the database is created from
the schema and data in `share/db_schema.dba` and `share/db_data.dba`, which the installer reads with its schema
handler and writes in the dialect of the database you chose.

### Velocity

Exponential Velocity is the application server developed together with Exponential (package
`se7enxweb/exponential-velocity`, on PHP 8.1 or later). It loads the application once, keeps it in persistent workers,
has its own response cache and serves HTTP and HTTPS itself, with its own certificates. It is driven by the console
command `exp:velocity`, which also runs two other engines: PHP's built-in server (`php`, for development) and
FrankenPHP (`frankenphp`, a production-ready alternative). Velocity is optional to install but recommended to use;
the site runs without it behind Apache or nginx. See [Velocity engines](../bc/6.0/velocity-engines.md) and the
project [README](../../README.md#exponential-velocity--the-fast-option).

## 1.3 The three install methods in one paragraph each

All three methods run the same setup steps (`kernel/setup/steps/`) and produce the same kind of installation. They
differ in where the answers come from.

- **The setup wizard** runs in the browser. A fresh installation has `[SiteAccessSettings] CheckValidity=true` in
  `settings/site.ini`, which sends every request to the wizard until the last step writes `CheckValidity=false`. You
  answer one page per step. Best for a first install.
- **The kickstarter** (`php bin/php/console exp:kickstarter run --force`) runs the same steps on the command line and
  reads every answer from `kickstart.ini` in the installation root. Best for repeatable and unattended installs.
- **The console install** (`php bin/php/console exp:install`) builds the kickstart configuration from command line
  options, every one of which has a default. Best for the fastest install and for scripts and CI.

[Chapter 4](04-choosing-an-install-method.md) compares them and helps you choose.

## 1.4 How to read this book

The book follows the order in which you install a site:

| Chapter | Read it when |
|---|---|
| 1. Introduction (this chapter) | you are new to Exponential |
| [2. Requirements](02-requirements.md) | you prepare a machine |
| [3. Getting the code](03-getting-the-code.md) | you download Exponential |
| [4. Choosing an install method](04-choosing-an-install-method.md) | you decide between wizard, kickstarter and console install |
| [5. The setup wizard](05-setup-wizard.md), [6. The kickstarter](06-kickstarter.md), [7. The console install](07-console-install.md) | you install with the method you chose |
| [8. Serving the site](08-serving-the-site.md) | you set up Velocity, Apache or nginx, HTTPS and permissions |
| [9. Databases](09-databases.md) | you choose or tune SQLite, MySQL or MariaDB, PostgreSQL, MongoDB or Oracle |
| [10. After installing](10-after-installing.md) | the site is installed and you set up cronjobs, caches, backups |
| [11. Upgrading](11-upgrading.md) | you already run an older version |
| [12. Troubleshooting](12-troubleshooting.md) | something does not work |
| [13. Security hardening](13-security-hardening.md) | before the site goes public |

The [contents](README.md) link every chapter. If you only want the shortest path, the
[installation overview](../INSTALL.md) has the whole process on one page; this book explains each step in depth,
with the reasons, the alternatives and the references.

Every chapter starts with a summary, ends with a **References** section that links the documents of this repository
and the external manuals the chapter relies on, and links its neighbours at the top.

## 1.5 Conventions

**Where commands run.** Every command is run from the installation root, unless the text says otherwise:

```bash
cd /var/www/example.com
php bin/php/ezcache.php --clear-tag=ini
```

**The console.** `bin/php/console` finds and runs every command of an installation. A script `bin/php/<name>.php`
is the console command `exp:<name>`, so these two lines run the same program:

```bash
php bin/php/console exp:kickstarter run --dry-run
php bin/php/kickstarter.php run --dry-run
```

Many installations add a shortcut in the root (`ln -s bin/php/console console`), after which `./console` is the same
program. `php bin/php/console list` lists every command; `php bin/php/console help <command>` explains one. See
[Console](../bc/6.0/console.md).

**Running as root.** Command line scripts refuse to run as the operating system user `root`. They print:

```
Running scripts as root may be dangerous.
If you think you know what you are doing, you can run this script with the
root account by appending the parameter --allow-root-user.
```

Run them as the user that owns the installation, or add `--allow-root-user` when you must run them as root. Files a
root process creates belong to root, which the web server then cannot change (see
[Troubleshooting](12-troubleshooting.md#123-permissions-and-ownership)).

**Settings in the text.** A setting is written as `[Block] Key` with the file it lives in, for example
`settings/site.ini`, `[SiteAccessSettings] CheckValidity`. To change it, write the block and the key into the override
file named in the text, never into the shipped file:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
```

**Placeholders.** Text in angle brackets is yours to replace: `<siteaccess>`, `<web user>`, `<version>`.
`example.com` stands for your domain.

**Tables.** Most reference material is in tables; a row whose first column is a symptom, a setting or an option is
meant to be found by searching the page.

**Versions.** The book describes the 6.0 line as of release 6.0.15 (in development; the newest published release tag
is `v6.0.14`). Where an earlier 6.0.x behaves differently, the text says so; the full history is in the
[changelogs](../changelogs/6.0/6.0.15.md).

## 1.6 Glossary

The terms below are the ones the installation chapters use. The [full glossary](../glossary.md) has every term of the
documentation.

| Term | Meaning |
|---|---|
| installation root | The directory that holds `index.php`, `autoload.php`, `bin/` and `settings/`; the document root of the web server. |
| kernel | The core code in `kernel/` and `lib/`. |
| extension | A directory under `extension/` that adds functionality; used when listed in `ActiveExtensions[]`. |
| siteaccess | One way of reaching the installation (by URL, host or port) with its own settings and design: `site`, `admin`, `editor`. |
| design | A set of templates, styles and images; a siteaccess chooses which designs are searched, in order. |
| site package | The archive of content, classes and design installed at setup; `sevenx_multisite` by default. |
| INI file | A settings file of `[Block]` and `Key=Value` lines; read in order: defaults, extensions, siteaccess, override. |
| override (settings) | A file in `settings/override/` or `settings/siteaccess/<name>/` whose values replace the defaults. |
| setup wizard | The browser installer, active while `CheckValidity=true`. |
| kickstarter | The command line installer that answers the wizard's steps from `kickstart.ini`. |
| `exp:install` | The one-command console installer. |
| console | `bin/php/console`, which runs every command (`exp:<name>` for `bin/php/<name>.php`). |
| Velocity | Exponential's application server, run with `exp:velocity`; the `qbix` engine. |
| engine | The program that serves the site: `qbix` (Velocity), `php` (PHP's built-in server) or `frankenphp`. |
| PHP-FPM | PHP's process manager, which serves PHP behind Apache or nginx. |
| Composer | The PHP package manager that installs the libraries and extensions `composer.json` lists. |
| Packagist | The package repository Composer reads by default. |
| Zeta Components | The PHP libraries (`zetacomponents/*`) the kernel is built on. |
| autoload | The generated class maps (`autoload/ezp_kernel.php`, `var/autoload/ezp_extension.php`) that tell PHP where each class is. |
| cache | Stored results that make pages faster (INI, template, content view, Velocity response cache); cleared with `ezcache.php`. |
| maintenance mode | The site answers every page with a notice (HTTP 503) while it is installed or maintained. |
| `var/` | The directory for everything Exponential writes: caches, logs, uploaded files, the SQLite database. |

## References

In this repository:

- [Installation overview](../INSTALL.md): the whole install on one page.
- [Project README](../../README.md): what Exponential is and how it is served.
- [Glossary](../glossary.md): every term of the documentation.
- [Getting started](../guides/getting-started.md): the first hour after an install.
- [The setup wizard and the editor siteaccess](../features/6.0/setup-wizard-and-editor-siteaccess.md).
- [Clean install defaults](../features/6.0/clean-install-defaults.md).
- [Extensions](../features/6.0/extensions/README.md) and the [Extensions guide](../guides/extensions.md).
- [Templates and design](../guides/templates-and-design.md).
- [INI override placements](../specifications/6.0/ini-override-placements.md) and
  [exp:ini](../features/6.0/exp-ini-command.md).
- [Console](../bc/6.0/console.md).
- [Velocity engines](../bc/6.0/velocity-engines.md).
- [Rebranding to Exponential](../features/6.0/rebranding-to-exponential.md).
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md).

External:

- Source code: [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential).
- Composer package: [packagist.org/packages/se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential).
- Online manual: [doc.exponential.earth](https://doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html).
- PHP manual: [php.net/manual](https://www.php.net/manual/en/).
- Composer documentation: [getcomposer.org/doc](https://getcomposer.org/doc/).
- 7x: [se7enx.com](https://se7enx.com).

[Contents](README.md) · Next: [2. Requirements](02-requirements.md)
