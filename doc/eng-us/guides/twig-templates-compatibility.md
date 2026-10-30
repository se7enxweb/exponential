# Twig templates: versions, upgrades and compatibility

This page is for operators and site builders who install, upgrade or move an installation that uses `.twig`
templates. It says which Twig 3 version runs on which PHP version, what Composer installs, what happens when Twig is
missing, what to watch when Twig or PHP is upgraded, and what is special under Exponential Velocity. Everything was
checked on 29 October 2026 against Packagist's package data, Composer, and a development installation; where
something was not run, the page says so.

## Which Twig for which PHP

The kernel's `composer.json` requires `twig/twig` `^3`: any Twig 3 release. Which one Composer picks depends on the
PHP version, because each Twig line states the PHP it needs (Packagist's data for `twig/twig`, read 29 October 2026):

| Twig line | Needs PHP | Newest release of the line |
|---|---|---|
| 3.0 to 3.11 | 7.2.5 or later | 3.11.3 (7 November 2024) |
| 3.12 to 3.19 | 8.0.2 or later | 3.19.0 (29 January 2025) |
| 3.20 to 3.30 | 8.1 or later | 3.30.0 (25 September 2026) |

Put against the PHP versions Exponential 6.0.15 runs on:

| PHP | Exponential | Newest Twig that runs there | Installed by Composer with security blocking on (the default) |
|---|---|---|---|
| 8.1 to 8.5 | supported | 3.30.0 | yes, the newest 3.x (3.30.0 on 29 October 2026) |
| 8.0 | supported | 3.19 line | **no**, see below |
| 7.4 | usable without support | 3.11 line | **no**, see below |

**Why not on PHP 8.0 and 7.4.** Composer refuses package versions with known security advisories while it resolves
dependencies, and the project keeps that on, on every PHP version. Every `twig/twig` release that runs on PHP 8.0 or
7.4 is affected by advisories, so Composer finds nothing it may install there:

```
Problem 1
  - Root composer.json requires twig/twig ^3 -> satisfiable by twig/twig[v3.27.0, ..., v3.30.0].
  - twig/twig[v3.27.0, ..., v3.30.0] require php >=8.1.0 -> your php version (8.0.30) does not satisfy that requirement.
```

The project's fork `se7enxweb/twig` has releases for those lines (3.19.1 of 25 October 2026 for PHP 8.0, 3.11.4 of
29 October 2026 for PHP 7.4). Installed on its own, it installs on PHP 8.0 and 7.4. Required next to `twig/twig`,
it stands in for `twig/twig` and inherits that package's advisories, so it does not resolve the problem above.

**What this means in practice:**

- **PHP 8.1 or later**: nothing to do. `composer update` installs Twig with the rest; `.twig` templates work.
- **PHP 8.0 or 7.4**: `.tpl` and `.stpl` templates are not affected. To use `.twig`, install Twig in a directory of its
  own and point `[TwigSettings] LibraryPath` at it (next section). Read the advisories Composer names (on Packagist's
  security advisories page) and decide whether they matter for your templates before you do: a site whose `.twig`
  files are written only by its own developers is exposed differently from one where others can change them. Or move
  to PHP 8.1 or later, which removes the question.

What was run for this page: rendering `.twig` on PHP 8.5 with Twig 3.30.0 (every example of these guides); `php -l`
of every file of the Twig bridge (`lib/twig/classes/`, `lib/templatebridge/classes/`) on PHP 7.4.33 and 8.0.30, which
passed; installing the fork on both. Rendering on PHP 8.0 and 7.4 was not run on the development installation, whose
Composer set-up is for PHP 8.4 and later.

The bridge compiles templates in Twig's `use_yield` mode and marks its nodes as yield-ready, which Twig offers from
3.9 on, so it needs Twig 3.9 or later; the newest release of each line in the tables above is later than that.

## Installing Twig in its own directory

When the installation's Composer set-up has no Twig (or to try another Twig version), install it in a directory of its
own and tell the bridge where it is:

```bash
mkdir -p var/lib/twig-vendor && cd var/lib/twig-vendor
composer require twig/twig:^3
```

```ini
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=var/lib/twig-vendor
```

`LibraryPath` may name a Composer directory (with `vendor/autoload.php`) or the `twig/twig` package itself (with
`src/Environment.php`). It is read only when the installation's own Composer autoload has no Twig. The tests read the
environment variable `TWIG_LIBRARY_PATH` the same way.

Check the result:

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
             endings .html.twig .twig; settings settings/twig.ini [TwigSettings]; security policy on; compiled var/site/cache/twig/compile-secure
PASS 3 engines available
```

## When Twig is not installed

Nothing breaks that does not use Twig:

- `.tpl` and `.stpl` templates render as before; the template system does not load Twig until a `.twig` is rendered.
- A `.twig` that a page reaches prints nothing in its place, and the error `Twig 3 not found: install twig/twig with
  Composer or set [TwigSettings] LibraryPath in twig.ini` is written to the debug output and `var/log/error.log`; with
  the debug output on, the page carries `<!-- twig: the Twig library is not available (...) -->` where the template
  would have been.
- `./console exp:template:engines` lists `twig` as `UNAVAILABLE ... library not found`, prints `FAIL an engine is not
  available` and exits with 3.
- `./console exp:template:check` reports `UNAVAILABLE twig` and every `.twig` as `SKIP`, never as a pass, and exits
  with 3 unless `--allow-missing-engine` is given.
- Operators and template functions written for the template system have no Twig dependency: an extension's operator
  works in `.tpl` with or without Twig.

So a design can ship `.twig` templates and an installation can switch Twig off by removing it: the `.twig` parts go
blank and say why in the log. Keep `.tpl` versions of anything that must never go blank (the converter writes them:
`exp:template:convert:twig-to-tpl`).

## Upgrading Twig

Twig 3 keeps backward compatibility within the major version, and deprecations are announced before removal. When
Composer moves to a newer Twig 3:

1. Clear the compiled templates: `./console exp:ezcache --clear-id=twig` (also part of `--clear-tag=template`). The
   compiled class names include Twig's version, so stale classes are never loaded, but they stay on disk until
   cleared.
2. Check every design for deprecations the new version reports:
   `./console exp:template:check --design-dir=<dir> --engine=twig --deprecations`
   (rule `TWIG-DEPRECATION-001`).
3. Under Velocity, restart the workers (`./console exp:velocity restart`), which keep the old Twig classes loaded.

Upgrading PHP works the same way: the compiled class names include the PHP major and minor version, so a template is
compiled again for the new PHP on its first request.

## Upgrading Exponential

The Twig bridge is part of the kernel library (`lib/twig/`, `lib/templatebridge/`, `settings/twig.ini`); it is
upgraded with the kernel. After an upgrade:

- clear the template caches (`./console exp:ezcache --clear-tag=template`): the compiled stubs, the generated
  template-function files in `<cache directory>/twig/functions/` and the compiled `.twig` classes;
- keep your settings in `twig.ini.append.php` files; never edit `settings/twig.ini`;
- read the 6.0.x changelog for changed defaults of `[TwigSettings]`, in particular the sandbox lists.

## Velocity

Exponential Velocity runs PHP in persistent workers; Twig benefits from that and needs to know three things about it.

- **One environment per worker.** The Twig environment is built once per worker and per design chain, and under
  Velocity every operator and template function is registered as a Twig filter or function when it is built. Under
  PHP-FPM, which starts every request afresh, only the names a template uses are resolved.
- **Compiled templates stay in memory.** A compiled `.twig` is a PHP class. The class name holds the file's
  modification time, the siteaccess and the locale (constant translations are compiled into the class), the Twig
  version and the PHP version. A changed file gets a new class on the next request, in every worker, without a
  restart. Old classes stay loaded until the worker ends; that is a little memory, not a correctness issue.
- **Cache permissions.** Velocity's workers usually run as root, PHP-FPM as the site's user. Compiled templates are
  written with the umask the installation uses for its other cache files (`eZFile::creationUmask()`, from
  `[FileSettings] StorageDirPermissions` and `StorageFilePermissions`), so a directory a Velocity worker creates in
  `<cache directory>/twig/` stays writable for PHP-FPM. Before this was so (it is part of 6.0.15), a template compiled
  first by a root worker could leave a directory PHP-FPM could not write, and the next template compiled into it
  failed with `Unable to write in the cache directory` (the page printed nothing in its place). If you see that
  message after an upgrade, clear the compiled templates once: `./console exp:ezcache --clear-id=twig`.

After changing `twig.ini` (the sandbox lists, `Autoescape`, `Security`), restart Velocity: the workers built their
environment with the old settings. A deploy with `./console exp:velocity deploy` does that.

## Compatibility of templates

- **`.twig` and `.html.twig`** are both rendered (`[TwigSettings] TemplateSuffixList[]`); `.html.twig` exists for
  templates shared with Symfony-style projects. The longer ending is matched first, so `a.html.twig` is always a Twig
  template named `a.html.twig`.
- **Templates from Symfony or Platform projects** use Twig functions those frameworks add (`path()`, `asset()`,
  the platforms' field-rendering functions, `form_*`); Exponential does not have them. Replace them with the template system's
  operators and functions (`ezurl`, `ezdesign`, `attribute_view_gui`); see the
  [side-by-side page](../help/template-language/tpl-twig-smarty-side-by-side.md).
- **The sandbox** refuses some of Twig's own tags and filters by default ([Security](twig-templates-security.md));
  templates from elsewhere often use `filter`, `map` or `spaceless`.
- **Converted templates** are written for `Autoescape=disabled` and the sandbox on, the defaults.

## Related

- [Twig templates](../features/6.0/twig-templates.md) (settings and installing)
- [twig.ini [TwigSettings]](../examples/ini-settings/twig/TwigSettings.md): every setting with its default, and [the stub operator exp_twig_render](../examples/template-operators/exp_twig_render.md)
- [Debugging Twig templates](twig-templates-debugging.md)
- [Velocity engines](../bc/6.0/velocity-engines.md)
- [Why Exponential supports PHP 7.4](../help/why-exponential-supports-php-7-4.md)
- [PHP 8.0 support](../bc/6.0/php-8.0-support.md)
