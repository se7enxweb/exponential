# Exponential Components (expcomponents): the kernel's libraries, forked from Zeta Components

Since 6.0.15 the PHP libraries that the Exponential kernel is built on are **Exponential Components**, short
**expcomponents**: Exponential's own fork of the Zeta Components, published as the Composer packages
`se7enxweb/expcomponents-<name>`, licensed under the Apache License 2.0 like the code they come from, and maintained
by 7x and the Exponential Foundation. The kernel's `composer.json` requires the forks in place of the upstream
`zetacomponents/*` packages. The classes keep their `ezc` names and their APIs, so code that uses them does not change.

This page is for everybody who wants to know what these libraries are, where they came from, why Exponential forks
them, what stays the same and where to find each one. What an upgrade means for an installation, an extension or
custom code is in the [upgrade notes](../../bc/6.0/expcomponents.md); what each package does in the kernel, class by
class, is in the [package reference](../../specifications/6.0/expcomponents-packages.md); how to work on a fork is in
the guide [Contributing to expcomponents](../../guides/contributing-to-expcomponents.md).

## Contents

1. [In short](#in-short)
2. [What the components are](#what-the-components-are)
3. [History: eZ Components, Zeta Components, expcomponents](#history-ez-components-zeta-components-expcomponents)
4. [Why Exponential forks them](#why-exponential-forks-them)
5. [What stays the same](#what-stays-the-same)
6. [What changes](#what-changes)
7. [The packages](#the-packages)
8. [How the old package names keep working: Composer "replace"](#how-the-old-package-names-keep-working-composer-replace)
9. [The license](#the-license)
10. [Where the repositories and packages are](#where-the-repositories-and-packages-are)
11. [Versions and releases](#versions-and-releases)
12. [Reporting issues and contributing](#reporting-issues-and-contributing)
13. [Modernization: the intent](#modernization-the-intent)
14. [Questions and answers](#questions-and-answers)
15. [Related pages](#related-pages)

## In short

| Question | Answer |
|---|---|
| What is it? | Exponential's fork of the Zeta Components PHP libraries (archive, mail, WebDAV, feeds, MVC tools, the database layer and more) |
| Package names | `se7enxweb/expcomponents-<name>`, for example `se7enxweb/expcomponents-mail` for `zetacomponents/mail` |
| Class names | Unchanged: `ezcMail`, `ezcArchive`, `ezcWebdavServer`, `ezcBaseFile` ... |
| License | Apache License 2.0 (`Apache-2.0`), as upstream; the upstream copyright and notice texts are kept |
| Who maintains it | 7x and the Exponential Foundation, in the open, on GitHub under `se7enxweb` |
| What an installation must do | Nothing beyond the usual `composer update` of an upgrade; see the [upgrade notes](../../bc/6.0/expcomponents.md) |
| Third-party code that requires `zetacomponents/<name>` | Keeps working: each fork declares that it replaces the old name |
| Since | 6.0.15. All forks were released on 2026-11-06, and the kernel requires them since then; archive and webdav had been required as forks a few days before |

## What the components are

The components are a set of independent PHP libraries, one Composer package each, that each do one job well: read and
write tar and zip archives, build and send e-mail, serve WebDAV, write RSS and Atom feeds, route and dispatch HTTP
requests, cache values, talk to a database through PDO, parse command line options, and so on. Each package has its
own version, its own tests and its own documentation; all of them share one base package (`base`) that provides the
exception classes, the option classes, file helpers and the original autoloader.

Every class of every component starts with `ezc` ("eZ Components", the original name) followed by the component:
`ezcArchive`, `ezcMailSmtpTransport`, `ezcWebdavServer`, `ezcFeed`, `ezcMvcController`, `ezcCacheManager`,
`ezcDbFactory`, `ezcConsoleInput`, `ezcBaseFile`. The libraries use no PHP namespaces; that is a property of their age,
not a defect, and it is why the class names are the interface that must not change.

The kernel uses them in many places that are easy to overlook: package import and export (archive), every mail the
site sends (mail), the WebDAV server (`index_webdav.php`), RSS export (feed), the REST API and the GraphQL, help and
migration endpoints built on it (MVC tools, authentication, cache, debug, event log, persistent object, database),
console scripts (console tools), Setup > System information (system information) and the content publishing queue
(signal slot). The [package reference](../../specifications/6.0/expcomponents-packages.md) names the classes and
files for each.

## History: eZ Components, Zeta Components, expcomponents

The libraries are older than most of the PHP ecosystem around them, and they changed hands twice before this fork.

| Period | Name | Who | License | Where |
|---|---|---|---|---|
| From 2005 | **eZ Components** | eZ Systems AS, the Norwegian company behind eZ Publish, the product Exponential grew from | New BSD license | ez.no, installed beside the code (for example as `ezcomponents/trunk`) |
| From 2010 | **Apache Zeta Components** | The Apache Software Foundation's incubator, after eZ Systems contributed the code | Apache License 2.0 | incubator.apache.org |
| After the incubator | **Zeta Components** | The Zeta Components development group, as an independent project | Apache License 2.0 | github.com/zetacomponents, Packagist `zetacomponents/*` |
| Since 6.0.15 | **Exponential Components (expcomponents)** | 7x and the Exponential Foundation | Apache License 2.0 | github.com/se7enxweb, Packagist `se7enxweb/expcomponents-*` |

The license file of every package still tells this story in its own words: "Zeta Components, Copyright 2011-2015 The
Zeta Components development group", "Apache Zeta Components, Copyright 2010-2011 The Apache Software Foundation",
"Originally developed by eZ Systems as eZ Components, Copyright 2005-2010 eZ Systems AS". These lines stay in the forks.

What this meant for the product line:

- **eZ Publish 4.x**, the discontinued product Exponential grew from, needed eZ Components installed next to the code,
  at a minimum version per release (2008.2 for 4.1, 2009.1 for 4.2, 2009.2.1 for 4.3 and 4.4, later the special
  builds `ezcomponents-ezp45` to `ezcomponents-ezp47`). The setup wizard checked it with the test `ezcversion`.
- **Exponential 6** installed the continuation, the Zeta Components, through Composer: `zetacomponents/*` in the root
  `composer.json`, loaded by Composer's class map. 6.0.11 (December 2025) raised them to the releases that support
  PHP 8.5.
- **Exponential 6.0.15** forked two of them first, because the kernel needed fixes that upstream had not released:
  `se7enxweb/expcomponents-webdav` 1.2.2, with security fixes to the WebDAV server (the fork had been published as
  `se7enxweb/zetacomponents-webdav` 1.2.1 before its rename), and `se7enxweb/expcomponents-archive` 1.5.2, which
  extracts GNU tar entries with long names again and prints no deprecations on PHP 8.4 and 8.5. Then the project
  made it official: every Zeta Component the kernel needs is forked as expcomponents, all 21 forks were released on
  2026-11-06, and the 6.0.15 line requires them in place of the upstream packages.

## Why Exponential forks them

- **Upstream activity is uneven.** Some packages are maintained and released (`base` and `database` in 2026), others
  have had no release for many years: `configuration`, `debug`, `event-log`, `image-analysis` and `signal-slot`
  have their last upstream tag from 2015. A kernel that promises a support window cannot wait for releases that may
  not come.
- **PHP 8.x.** Exponential supports PHP 8.0 to 8.5 and runs on PHP 7.4. Every new PHP version deprecates something
  these libraries do (implicitly nullable parameters, missing tentative return types, `null` given to string
  functions). A deprecation in a library becomes a warning in every log of every site. The forks fix them where the
  code is, and their tests run on every PHP version the kernel supports.
- **Security.** Fixes the kernel needs must be releasable when they are ready. The WebDAV fork is the example: its
  1.2.2 release keeps request paths and the destination of COPY and MOVE inside the backend's root, does not follow
  symbolic links out of it, refuses request bodies with a document type declaration, compares digest authentication
  in constant time and creates random lock tokens.
- **One maintainer for the whole stack.** The kernel, its extensions and its libraries are tested, released and
  documented together, under the same rules (permanent tags, one changelog entry per change, tests with each fix).
- **Modernization.** The libraries will be modernized internally; see [the intent](#modernization-the-intent).

Forking is not a break with upstream. Fixes that are useful to everyone are offered to the Zeta Components project as
pull requests, as 7x has done before (the se7enxweb profile reports 14 security pull requests to Zeta Components in
April 2026), and upstream fixes are merged into the forks ([syncing with upstream](../../guides/contributing-to-expcomponents.md#syncing-with-upstream)).

## What stays the same

- **The class names and the APIs.** Every class keeps its `ezc` name, every public method its signature and
  meaning. Code that calls `new ezcMailComposer()` or extends `ezcWebdavSimpleBackend` works unchanged. A change that
  would break a caller is not made in a fork's 1.x line.
- **The files on disk are found the same way.** Each package is loaded by Composer's class map
  (`"autoload": {"classmap": ["src"]}`), exactly as the upstream package was. Nothing in `autoload.php`, `config.php`
  or the kernel's class maps changes.
- **The license.** Apache License 2.0. The upstream `LICENSE.txt` with its notice lines, `CREDITS`, `ChangeLog` and
  the copyright headers in the source files stay as they are.
- **The version line.** A fork continues the upstream version numbers: the first fork release of a package follows
  the upstream release it starts from (archive went from 1.5.1 to 1.5.2, webdav from 1.2 to 1.2.2), so a constraint
  such as `~1.10` keeps its meaning.
- **The old package names resolve.** Through Composer's `replace` (below), a requirement on `zetacomponents/mail`
  is satisfied by `se7enxweb/expcomponents-mail`.
- **Settings.** No INI setting changes. `setup.ini [ezcversion]` still tests for the class `ezcBaseFile`, which the
  fork of `base` provides.

## What changes

- **The package names** in `composer.json`, `composer.lock`, `composer show` and `composer licenses`:
  `se7enxweb/expcomponents-<name>` instead of `zetacomponents/<name>`.
- **The directories in `vendor/`**: `vendor/se7enxweb/expcomponents-<name>/` instead of
  `vendor/zetacomponents/<name>/`. Code that includes a library file by its path (it should not) must follow; code
  that uses the classes does not notice.
- **Where issues go**: to the fork's repository under `se7enxweb`, not to the Zeta Components project
  ([below](#reporting-issues-and-contributing)).
- **Where releases come from**: the forks are released by 7x on the Exponential schedule, with their own release
  notes on GitHub.

## The packages

Every Zeta Component the kernel needs has a fork, and every fork was released on 2026-11-06 and is on Packagist. The
table lists them all: the upstream package, its fork, the repository, the upstream release the fork starts from, the
current fork release and the constraint in the kernel's `composer.json` (the tilde or caret form, never an exact
version). The repositories marked *new* were created when the fork became official; the others existed before.

| Upstream package | Fork package | Repository | Upstream release | Fork release | Kernel requires |
|---|---|---|---|---|---|
| `zetacomponents/base` | `se7enxweb/expcomponents-base` | [expcomponents-base](https://github.com/se7enxweb/expcomponents-base) | 1.9.5 | 1.9.6 | `^1.9.6` |
| `zetacomponents/archive` | `se7enxweb/expcomponents-archive` | [expcomponents-archive](https://github.com/se7enxweb/expcomponents-archive) | 1.5.1 | 1.5.3 (1.5.2 on 2026-11-03) | `~1.5.3` |
| `zetacomponents/authentication` | `se7enxweb/expcomponents-authentication` | [expcomponents-authentication](https://github.com/se7enxweb/expcomponents-authentication) | 1.4.1 | 1.4.2 | `~1.4.2` |
| `zetacomponents/cache` | `se7enxweb/expcomponents-cache` | [expcomponents-cache](https://github.com/se7enxweb/expcomponents-cache) | 1.6.3 | 1.6.4 | `~1.6.4` |
| `zetacomponents/configuration` | `se7enxweb/expcomponents-configuration` | [expcomponents-configuration](https://github.com/se7enxweb/expcomponents-configuration) (new) | 1.4 | 1.4.1 | `~1.4.1` |
| `zetacomponents/console-tools` | `se7enxweb/expcomponents-console-tools` | [expcomponents-console-tools](https://github.com/se7enxweb/expcomponents-console-tools) (new) | 1.7.5 | 1.7.6 | `^1.7.6` |
| `zetacomponents/database` | `se7enxweb/expcomponents-database` | [expcomponents-database](https://github.com/se7enxweb/expcomponents-database) | 1.5.4 | 1.5.5 | `~1.5.5` |
| `zetacomponents/debug` | `se7enxweb/expcomponents-debug` | [expcomponents-debug](https://github.com/se7enxweb/expcomponents-debug) (new) | 1.3 | 1.3.1 | `~1.3.1` |
| `zetacomponents/event-log` | `se7enxweb/expcomponents-event-log` | [expcomponents-event-log](https://github.com/se7enxweb/expcomponents-event-log) (new) | 1.5 | 1.5.1 | `~1.5.1` |
| `zetacomponents/feed` | `se7enxweb/expcomponents-feed` | [expcomponents-feed](https://github.com/se7enxweb/expcomponents-feed) (new) | 1.4.6 | 1.4.7 | `~1.4.7` |
| `zetacomponents/image-analysis` | `se7enxweb/expcomponents-image-analysis` | [expcomponents-image-analysis](https://github.com/se7enxweb/expcomponents-image-analysis) (new) | 1.2 | 1.2.1 | through image-conversion |
| `zetacomponents/image-conversion` | `se7enxweb/expcomponents-image-conversion` | [expcomponents-image-conversion](https://github.com/se7enxweb/expcomponents-image-conversion) | 1.5.1 | 1.5.2 | `~1.5.2` |
| `zetacomponents/mail` | `se7enxweb/expcomponents-mail` | [expcomponents-mail](https://github.com/se7enxweb/expcomponents-mail) (new) | 1.10.1 | 1.10.2 | `~1.10.2` |
| `zetacomponents/mvc-tools` | `se7enxweb/expcomponents-mvc-tools` | [expcomponents-mvc-tools](https://github.com/se7enxweb/expcomponents-mvc-tools) (new) | 1.2.4 | 1.2.5 | `~1.2.5` |
| `zetacomponents/persistent-object` | `se7enxweb/expcomponents-persistent-object` | [expcomponents-persistent-object](https://github.com/se7enxweb/expcomponents-persistent-object) | 1.8.2 | 1.8.3 | `~1.8.3` |
| `zetacomponents/signal-slot` | `se7enxweb/expcomponents-signal-slot` | [expcomponents-signal-slot](https://github.com/se7enxweb/expcomponents-signal-slot) (new) | 1.2 | 1.2.1 | `~1.2.1` |
| `zetacomponents/system-information` | `se7enxweb/expcomponents-system-information` | [expcomponents-system-information](https://github.com/se7enxweb/expcomponents-system-information) (new) | 1.1.2 | 1.1.3 | `~1.1.3` |
| `zetacomponents/webdav` | `se7enxweb/expcomponents-webdav` | [expcomponents-webdav](https://github.com/se7enxweb/expcomponents-webdav) | 1.2 | 1.2.3 (1.2.2 on 2026-11-01) | `~1.2.3` |

The image-conversion fork also carries a fix that was offered upstream as
[zetacomponents/ImageConversion pull request #9](https://github.com/zetacomponents/ImageConversion/pull/9): since
upstream runs ImageMagick through an argument list instead of a shell, scaling and cropping failed with
"unrecognized option `-resize '", and the fork trims the option names. Its 1.5.2 release notes list the rest.

The mail fork 1.10.2 includes the changes upstream made after its 1.10.1 release, among them protection against
header injection: it refuses an address that contains whitespace or one of `< > , ; "` before it writes any header,
because such an address could end the header or add recipients
([upgrade notes](../../bc/6.0/expcomponents.md#custom-code)).

`image-analysis` is not used by the kernel directly: `image-conversion` requires it, and the image editor ezie uses
both. `configuration` stays required while 7x SQLI Import 2.0.x throws one of its exceptions.

Forks that exist but that the kernel does not require:

| Upstream package | Fork package | Why it is not required |
|---|---|---|
| `zetacomponents/authentication-database-tiein` | `se7enxweb/expcomponents-authentication-database-tiein` (1.2.1, released 2026-11-06) | No code of the kernel or of the packages it requires uses it; the kernel stopped requiring it in October 2026 ([behaviour changes of October 2026](../../bc/6.0/behaviour-changes-2026-10.md)). |
| `zetacomponents/graph` | `se7enxweb/expcomponents-graph` (1.7.3, released 2026-11-06) | Neither the kernel nor any shipped extension draws charts with it; the fork is there for extensions that do. |
| `zetacomponents/workflow` | `se7enxweb/expcomponents-workflow` (1.5.2, released 2026-11-06) | The kernel has its own workflow engine (`eZWorkflow`); the fork is there for extensions that use the component. |

Upstream packages that are not forked:

- `zetacomponents/mvc-authentication-tiein` and `zetacomponents/php-generator`: the kernel stopped requiring them in
  October 2026; nothing uses them.
- `zetacomponents/unit-test`, `zetacomponents/database-schema` and `zetacomponents/coding-standard`: development
  dependencies of the forks' own test suites; they stay upstream.

No `composer.json` of a shipped extension requires a `zetacomponents/*` package, and the metapackage
`se7enxweb/exponential-php-floor` requires none either; the switch happens in the kernel's `composer.json` alone.

## How the old package names keep working: Composer "replace"

Each fork's `composer.json` says which package it replaces. The archive fork, as published:

```json
{
    "name": "se7enxweb/expcomponents-archive",
    "license": "Apache-2.0",
    "require": {
        "php": ">=7.1",
        "se7enxweb/expcomponents-base": "~1.8"
    },
    "replace": {
        "zetacomponents/archive": "self.version"
    }
}
```

What `replace` means to Composer:

1. **A requirement on the old name is met by the fork.** A package that requires `zetacomponents/archive ~1.5` is
   satisfied by `se7enxweb/expcomponents-archive` 1.5.3, because `self.version` makes the fork stand in for the same
   version of the old name.
2. **The old package is not installed next to it.** A replaced package and its replacement are never installed
   together; there is one copy of each class.
3. **The forks require each other by their fork names.** The archive fork above requires
   `se7enxweb/expcomponents-base`, not `zetacomponents/base`; a third-party package that still requires
   `zetacomponents/base` gets the base fork through its `replace`, and the upstream `base` is not installed.
4. **The WebDAV fork replaces two names**: `zetacomponents/webdav` and `se7enxweb/zetacomponents-webdav`, the name it
   was published under for 1.2.1.

`replace` does not rename a class and does not alias one; it only tells Composer which packages are interchangeable.
That is enough here, because the classes are the same.

## The license

The forks are licensed under the **Apache License, Version 2.0** (`Apache-2.0` in `composer.json`), the license of the
Zeta Components they come from. Apache 2.0 allows anyone to use, change and redistribute the code, also in a fork, on
these conditions, which the forks follow:

- each package keeps the license text and the notice lines it came with (`LICENSE.txt`, with the notices of the
  Zeta Components development group, the Apache Software Foundation and eZ Systems AS), and the copyright headers in
  its source files;
- Apache 2.0 (section 4(b)) asks that modified files carry prominent notices stating that they were changed; every
  change of a fork is a commit with its own message and an entry in the package's `ChangeLog`, and the release notes
  list them;
- every fork ships a `NOTICE` file (Apache 2.0, section 4(d)) that keeps the upstream copyright notices and names the
  fork; the notice lines at the top of `LICENSE.txt` stay as well.

The Exponential kernel itself is licensed under the **GNU GPL version 2 or (at your option) any later version**
(`GPL-2.0-or-later`). How the two fit together, briefly and without overstating it: the Free Software Foundation
considers the Apache License 2.0 compatible with GPL version 3, and incompatible with GPL version 2 *only*. Because
the kernel's license includes "or any later version", the kernel combined with Apache 2.0 libraries can be
distributed under GPL version 3. This is the same situation as with the upstream Zeta Components before the fork;
the fork changes nothing about it. An extension licensed GPL version 2 *only* that bundles Apache 2.0 code is the case
to look at carefully ([License compatibility](../../help/extension-development/part-5-extend-fork-contribute/licenses-and-compatibility.md)).
This is not legal advice; ask counsel about your own distribution.

## Where the repositories and packages are

- **Repositories:** `https://github.com/se7enxweb/expcomponents-<name>`, for example
  [github.com/se7enxweb/expcomponents-mail](https://github.com/se7enxweb/expcomponents-mail). Each one is a GitHub
  fork of the matching repository under [github.com/zetacomponents](https://github.com/zetacomponents) (for example
  `zetacomponents/Mail`), so the upstream history is kept and visible.
- **Packages:** on Packagist as `se7enxweb/expcomponents-<name>`, for example
  [packagist.org/packages/se7enxweb/expcomponents-archive](https://packagist.org/packages/se7enxweb/expcomponents-archive).
  Every fork is there since its release on 2026-11-06.
- **Documentation:** each repository's `docs/` folder and `README`. The API is the same as upstream, so the
  [Zeta Components documentation](https://zetacomponents.org/documentation/overview.html) still describes it.
- **Installed copies:** `vendor/se7enxweb/expcomponents-<name>/` in an installation; `composer show` lists them.

## Versions and releases

- A fork's version continues the upstream line and raises the last position for each release: 1.5.1 upstream, then
  1.5.2, 1.5.3 ... in the fork. After 1.5.9 comes 1.5.10; reaching nine never moves a higher position.
- Tags carry no `v` prefix, as upstream (`1.5.2`, not `v1.5.2`).
- **A published tag is permanent.** It is never moved, deleted or re-cut; a mistake in a release is corrected by the
  next release, whose notes say what was wrong.
- The kernel requires each fork with a tilde constraint (`~1.5.2`), which accepts every later release of the same
  line (1.5.3, 1.5.10) and no new minor line.
- Releases are approved by the maintainers. The
  release notes of each fork on GitHub are the record of what was released.

## Reporting issues and contributing

- **A problem in a library** (an error in `ezcMail...`, a WebDAV client that fails, a deprecation from a file in
  `vendor/se7enxweb/expcomponents-*`): open an issue in that fork's repository. Name the package and its version
  (`composer show se7enxweb/expcomponents-mail`), the PHP version and the smallest code that shows it.
- **A security problem:** do not open a public issue. Report it as the kernel's `SECURITY.md` says (security@se7enx.com).
- **Not sure whether it is the kernel or the library:** open it against the kernel, `se7enxweb/exponential`; it will
  be moved.
- **Code:** a pull request to the fork, with a test. How to clone, run the tests on several PHP versions and write the
  commit message: [Contributing to expcomponents](../../guides/contributing-to-expcomponents.md).

## Modernization: the intent

The owner's words: the project is "officially forking zeta components under apache 2.0 license and modernizing its
features internally". What that means, and what it does not:

- The modernization is **internal**: the code inside the libraries is brought up to current PHP, while the class
  names and the public API stay as they are, so the kernel and every extension keep working.
- It happens **package by package, release by release**, each change with tests that run on every supported PHP
  version, and each release with notes that say what changed.
- Anything that would change behavior a caller can see is documented in the fork's release notes and in the kernel's
  changelog before it is required.

There is no list of new features to announce here. When a fork gains one, its release notes and this page will say so.

## Questions and answers

**Do I have to change my code?**
No, unless it includes a library file by its path under `vendor/zetacomponents/`. Classes, methods and settings are
unchanged.

**My extension requires `zetacomponents/mail`. Does it still install?**
Yes. `se7enxweb/expcomponents-mail` replaces `zetacomponents/mail`, so Composer counts the requirement as met. You
may change the requirement to the fork's name when convenient ([upgrade notes](../../bc/6.0/expcomponents.md)).

**Can I go back to the upstream packages?**
Yes: the [upgrade notes](../../bc/6.0/expcomponents.md#rollback) show how (in a Git clone, in
the kernel's `composer.json`; in a Composer project, by staying on the previous kernel release) and
what you lose (the fixes of the forks, notably the WebDAV security fixes).

**Is this still open source?**
Yes. Apache License 2.0, public repositories, public issues.

**Will the forks be renamed to use namespaces?**
Not in a way that breaks callers. The `ezc` class names are the compatibility promise.

**Why "expcomponents" and not "zetacomponents"?**
A fork carries the name of the project that maintains it, so that nobody confuses its releases with the upstream
project's. The prefix `exp` is the one Exponential uses for its own code.

## Related pages

- [Upgrade notes: the switch to expcomponents](../../bc/6.0/expcomponents.md)
- [Specification: the expcomponents packages and what the kernel uses them for](../../specifications/6.0/expcomponents-packages.md)
- [Contributing to expcomponents](../../guides/contributing-to-expcomponents.md)
- [Upstream projects and forks](../../help/ecosystem/php-stack/upstream-projects-and-forks.md)
- [Third-party components and their licenses](../../help/bundlers/third-party-components-and-their-licenses.md)
- [Copyright and copyleft](../../help/copyright-and-copyleft.md)
- [The 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
