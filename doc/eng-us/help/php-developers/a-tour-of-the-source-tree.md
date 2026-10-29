---
title: A tour of the source tree
audience: php-developer, developer
category: php-development
order: 20
summary: Every top-level directory and entry point of an Exponential checkout from a PHP developer's point of view - where the kernel, the libraries, the commands, the settings, the tests and the generated files live, and which of them you may change.
keywords: source tree, directories, kernel, lib, extension, settings, var, autoload, bin/php, cronjobs, tests, entry points, index.php, index_rest.php, config.php, config.env.php
type: help
---
# A tour of the source tree

This page is for PHP developers who open an Exponential checkout for the first time. It walks through the directories
and entry points at the root, says what lives where, and which places are generated or belong to the installation
rather than to the code.

## Contents

1. [The entry points](#the-entry-points)
2. [Code directories](#code-directories)
3. [Inside kernel/](#inside-kernel)
4. [Inside lib/](#inside-lib)
5. [Configuration and generated files](#configuration-and-generated-files)
6. [Quality and tests](#quality-and-tests)
7. [What you change and what you leave alone](#what-you-change-and-what-you-leave-alone)

## The entry points

| File | Runs |
|---|---|
| `index.php` | every web page: maintenance check, `autoload.php`, then `new ezpKernel( new ezpKernelWeb() )` and `run()` |
| `index_rest.php` | the REST API |
| `index_treemenu.php` | the admin's content tree menu |
| `index_cluster.php` | serving clustered binary files |
| `bin/php/*.php` | command line scripts; one dispatcher, `bin/php/console`, runs them all as `exp:<name>` |
| `runcronjobs.php` | cronjob parts (`php runcronjobs.php frequent`) |
| `soap.php`, `index_webdav.php` | the SOAP and WebDAV servers (`webdav.php` only forwards to `index_webdav.php`, [until a later major version](../../bc/6.0/webdav-entry-script-index-webdav.md)) |

Every PHP entry point starts with `autoload.php`. It defines `EXP_ROOT_DIR` (the installation root, an absolute path),
loads `lib/phpcompat.php` on PHP older than 8.1, decides about the engine archive and registers the class map
autoloader ([How a request runs in PHP](how-a-request-runs-in-php.md)).

## Code directories

| Directory | Holds | Namespaces and prefixes |
|---|---|---|
| `kernel/` | the CMS: modules, content model, users, roles, workflows, setup, the request kernel | `eZ*`, `ezp*`, `exp*` classes; `Exponential\...` for runnables |
| `lib/` | general libraries the kernel builds on | `eZ*`, `ezp*`, `exp*` |
| `extension/` | shipped extensions and your own | each extension's own prefix |
| `cronjobs/` | the kernel's cronjob part files (`cronjobs/workflow.php` ...) | thin files calling `Exponential\Cronjob\Kernel\...` |
| `design/` | the kernel's designs: `standard`, `base`, `admin`, `admin3`, `admin4`, `admin4l`, `editor`, `plain` ... | templates, stylesheets, images |
| `update/` | database update scripts and SQL per engine (`update/database/sqlite/`, `postgresql/`, `mysql/`) | |
| `share/` | `share/filelist.md5` and other shipped data | |
| `schemas/`, `templates/`, `support/` | XML schemas, shipped templates for packages, support files | |

## Inside kernel/

Most top-level directories of `kernel/` are **modules**: `kernel/content/`, `kernel/user/`, `kernel/setup/`,
`kernel/role/`, `kernel/workflow/`, `kernel/shop/` and more. Each holds a `module.php`, its views and often a
`function_definition.php` for fetch functions. Two directories are not modules:

| Directory | Holds |
|---|---|
| `kernel/classes/` | the content model and services: `eZContentObject`, `eZContentObjectTreeNode`, `eZPersistentObject`, `eZDataType` and the datatypes in `kernel/classes/datatypes/`, workflow types, the help system (`kernel/classes/help/`), the debug bar (`kernel/classes/debugbar/`) |
| `kernel/private/classes/` | the request kernel (`ezpkernel.php`, `ezpkernelweb.php`), `ezpEvent`, and the runnable classes: `commands/`, `cronjobs/`, `views/`, `services/`, `runnable/` |
| `kernel/common/` | shared helpers, for example `ezpI18n` and template operators used everywhere |
| `kernel/sql/` | the kernel's database schema per engine |

## Inside lib/

| Library | Main classes |
|---|---|
| `lib/ezutils/` | `eZINI`, `eZDebug`, `eZDebugSetting`, `eZSys`, `eZHTTPTool`, `eZExecution`, `eZCLI`, `eZURI`, `eZExtension`, the debug bar report |
| `lib/ezdb/` | `eZDB` and the drivers (`eZSQLite3DB`, `eZPostgreSQLDB`, `eZMySQLiDB`, `expMongoDB`), the SQL query cache |
| `lib/eztemplate/` | the template engine (`eZTemplate`), its compiler and the built-in operators |
| `lib/ezfile/` | files, directories, `eZLog` |
| `lib/ezi18n/`, `lib/ezlocale/` | translation and locale |
| `lib/ezdbschema/` | schema handling and `.dba` files |
| `lib/ezsession/` | sessions |
| `lib/phpcompat.php` | functions newer than PHP 8.0, defined only when PHP lacks them |
| `lib/version.php` | the release number |

## Configuration and generated files

| Path | What it is | In git |
|---|---|---|
| `settings/*.ini` | the kernel's default settings | yes |
| `settings/override/`, `settings/siteaccess/` | the installation's own settings; may hold secrets | no (installation) |
| `config.php` | constants for this installation (from `config.php-RECOMMENDED`) | no |
| `config.env.php` | `EXP_ENV`, the environment name of this machine (from `config.env.php.example`) | no |
| `autoload/ezp_kernel.php` | the kernel class map | yes |
| `var/autoload/ezp_extension.php`, `ezp_override.php`, `ezp_tests.php` | generated class maps | no |
| `var/cache/`, `var/log/`, `var/<site>/` | caches, logs, storage | no |
| `vendor/` | Composer packages | no |
| `dist/engine.phar` | the optional engine archive | no (`dist/` is ignored) |

## Quality and tests

| Path | Purpose |
|---|---|
| `phpunit.xml`, `tests/bootstrap.php`, `tests/tests/` | PHPUnit configuration, bootstrap and the tests |
| `tests/test-map.json` | which tests cover which source files |
| `tests/runtests.php` | the legacy runner for database tests (`--dsn ... --db-per-test`) |
| `tests/bin/bootsmoke.php` | the boot smoke test CI runs on PHP 8.0 |
| `phpcs.xml.dist`, `bin/phpcs/` | the code style and the project's own sniffs |
| `phpstan.neon.dist`, `phpstan-baseline.neon` | static analysis at level 1 with its baseline |
| `Makefile`, `.githooks/`, `composer.dev.json` | the make targets, the git hooks and the development tools |
| `.github/workflows/` | CI: Quality, PHPUnit, Performance, CI health |

## What you change and what you leave alone

- **Your work goes into an extension** in nearly every case. The kernel is changed only for kernel fixes and features
  you contribute back ([Contributing PHP changes](contributing-php-changes.md)).
- **Never edit generated files** (`var/autoload/*`, caches); regenerate them.
- **Never commit installation files**: `settings/override/`, `config.php`, `config.env.php`, `var/`.
- **Never edit `vendor/`**; extensions installed by Composer can be live git checkouts in some installations, and
  Composer would replace them.

## Related pages

- [Exponential for PHP developers](exponential-for-php-developers.md)
- [Project files](../project-files.md)
- [Extension directory layout](../extension-development/part-3-extension-developer/extension-directory-layout.md)
- [Directories under var/](../extension-development/part-8-reference/directories-under-var.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
