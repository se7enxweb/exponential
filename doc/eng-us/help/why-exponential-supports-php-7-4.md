---
title: Why Exponential supports PHP 7.4
audience: admin, developer, user, press, business
category: getting-started
order: 3
summary: Supported - PHP 8.0 to 8.5; usable without support - PHP 7.4 (Exponential 6.0.15 and later). Who this is for, what works on PHP 7.4 and what needs PHP 8 (a table per feature), how one Composer metapackage picks the right packages for each PHP version, how security is kept, the honest trade-offs, and the end-of-life stance - and why bridging old PHP versions is one of the project's values.
keywords: PHP 7.4, PHP 8.0, PHP 8.5, supported PHP versions, legacy PHP, old hosting, locked-down hosting, se7enxweb/exponential-php-floor, metapackage, Composer, polyfills, symfony/polyfill-php80, symfony/polyfill-php81, end of life, security, Velocity, Layouts, upgrade path, 6.0.15
type: help
---
# Why Exponential supports PHP 7.4

This page is for site owners, administrators, hosting companies and developers who run, or must run, Exponential on
PHP 7.4, and for anyone who wonders why a project that supports PHP 8.5 on the day it appears also goes back to a
PHP version that its makers stopped supporting in 2022.

> **Supported: PHP 8.0 to 8.5. Usable without support: PHP 7.4 (Exponential 6.0.15 and later).**
>
> "Without support" means: the software is designed to be usable on PHP 7.4, but the project gives no bug-fix or security commitment specific to PHP 7.4, you use it at your own risk, and the features that need PHP 8 are not available there.

The supported versions stay PHP 8.0 to 8.5, and the newest PHP stays fully supported; usability on PHP 7.4 is added
below them, not instead of anything.

## Contents

1. [In short](#in-short)
2. [Who it is for](#who-it-is-for)
3. [What works on PHP 7.4 and what needs PHP 8](#what-works-on-php-74-and-what-needs-php-8)
4. [How it works](#how-it-works)
5. [A short history of PHP support in Exponential](#a-short-history-of-php-support-in-exponential)
6. [Why the project does this](#why-the-project-does-this)
7. [How security is kept](#how-security-is-kept)
8. [The honest trade-offs](#the-honest-trade-offs)
9. [The end-of-life stance](#the-end-of-life-stance)
10. [Installing on PHP 7.4](#installing-on-php-74)
11. [Related pages](#related-pages)

## In short

| Question | Answer |
|---|---|
| Which PHP versions? | Supported: 8.0 to 8.5. Usable without support: 7.4. `composer.json` declares `"php": "^7.4 \|\| ^8.0 \|\| ... \|\| ^8.8"`; versions after 8.5 do not exist yet and are not tested. |
| What is usable on 7.4, without support? | The whole kernel: content, the admin and editor interfaces, templates, the REST API, SOAP, WebDAV, cronjobs, search, the setup wizard and `exp:install`, and the extensions bundled with the kernel. |
| What needs PHP 8? | Exponential Velocity, Exponential Layouts and the media site (8.0), and a handful of extensions whose releases need 8.0. The [table](#what-works-on-php-74-and-what-needs-php-8) lists each one. |
| Do I have to choose packages by hand? | No. The kernel requires the metapackage `se7enxweb/exponential-php-floor`; Composer installs the version that fits your PHP and with it the right set of packages. |
| Is PHP 7.4 safe? | PHP 7.4 has had no security fixes from the PHP project since 28 November 2022. It is safe only where your operating system vendor or host still patches it. See [How security is kept](#how-security-is-kept). |
| What does "without support" mean? | No bug-fix or security commitment specific to PHP 7.4; a problem that occurs only on 7.4 may stay unfixed. You use it at your own risk. |
| How long? | Usability on PHP 7.4 is a bridge, not a destination. See [The end-of-life stance](#the-end-of-life-stance). |

## Who it is for

Using Exponential on PHP 7.4, without support, is for organizations that want a current Exponential but **cannot change the PHP version of the
machine it runs on yet**. In our experience they are rarely careless; they are usually bound by something outside
their control:

- **Hosting contracts and managed platforms.** A shared or managed host decides the PHP versions it offers, and a
  contract may run for years.
- **Long-term operating system releases.** Distributions with long support periods shipped PHP 7.4 as their
  standard PHP: Debian 11, Ubuntu 20.04 LTS, and the `php:7.4` stream of Red Hat Enterprise Linux 8 and its rebuilds.
  Organizations with extended support contracts for these systems receive patched PHP packages from their vendor.
- **Certified and audited environments.** In the public sector, health care, finance and industry, a server image
  may be certified as a whole. Changing PHP means a new certification, planned months ahead.
- **Remote, offline and low-bandwidth sites.** An installation in a remote office, on a ship, in a research station
  or on a network without Internet access is updated when someone can get there.
- **Other applications on the same server.** A server that also runs software which only works on PHP 7.4 cannot
  move until that software can.

For all of them the choice used to be: stay on an old Exponential with known bugs, or wait. Now they can run the
current release, with its fixes and features, and move to PHP 8 when their circumstances allow, without moving
Exponential again at that moment.

## What works on PHP 7.4 and what needs PHP 8

The **kernel** (everything in `kernel/` and `lib/`) and the **extensions shipped with it** (`ezjscore`, `ezoe`,
`ezformtoken`, `expservices`, `expimageeditor`, `exphelpsite`) run on PHP 7.4. For the extension packages and
servers, the table shows the oldest PHP each one needs. "7.4 (older release)" means Composer installs, on PHP 7.4,
the last release of the same line that still runs there; on PHP 8 you get the newest one.

| Feature or package | PHP 7.4 (without support) | PHP 8.0 | PHP 8.1 to 8.3 | PHP 8.4 and 8.5 |
|---|---|---|---|---|
| Kernel: content, admin, editor, templates, REST v1 and v2, SOAP, WebDAV, cronjobs, search | yes | yes | yes | yes |
| Setup wizard, kickstarter, `exp:install` | yes | yes | yes | yes |
| Apache or nginx with PHP-FPM or mod_php | yes | yes | yes | yes |
| PHP's built-in server (`exp:velocity start --engine=php`) | yes | yes | yes | yes |
| Exponential Velocity, the application server (`--engine=qbix`) | no: `exp:velocity start` refuses with a message | yes | yes | yes |
| FrankenPHP engine | brings its own PHP 8 binary | yes | yes | yes |
| Exponential Layouts (`explayouts` and its packages), the expsite packages, the media site theme | no | no | yes | yes |
| Two-factor authentication (`sevenx_authentication_2fa`) | no | yes | yes | yes |
| `exp_adminui`, `expai`, `expqueue`, `exp_enhanced_link`, `expchangeclass`, `cjw_themes_jumper`, `cjw_tmv` | no | yes | yes | yes |
| `sqliimport` 2.x, `ezodf` 7.x, `ezownerchange` 1.4 | no | yes | yes | yes |
| Tags (`eztags`), CJW Newsletter, Syndication, `bccie`, `git_manager` | 7.4 (older release) | yes | yes | yes |
| `xrowextract`, `sevenx_dse` | no (their code needs PHP 8) | yes | yes | yes |
| reCAPTCHA library (`google/recaptcha`) | 1.2.4 | 1.3 | 1.3 | 2.x |
| All other extensions the kernel installs (ezflow, ezwebin, ezdemo, recaptcha, hcaptcha, ezmultiupload, ...) | yes | yes | yes | yes |
| AVIF image output | no (WebP is the best format) | no | yes | yes |
| File times inside the log viewer's ZIP download | the time of the download | the original times | the original times | the original times |

Nothing in this table fails silently with a white page. A package that cannot run on your PHP is simply not
installed by Composer, and the one server that would need PHP 8, Velocity, says so:

```text
$ php bin/php/console exp:velocity start
the Velocity engine (qbix) needs PHP 8.0 or later, this is PHP 7.4. Exponential runs on PHP 7.4 with Apache or
nginx and PHP-FPM, or with the php engine (exp:velocity start --engine=php)
```

## How it works

Three pieces make one code base run from PHP 7.4 to PHP 8.5.

**1. The kernel is written for the oldest supported PHP.** Every PHP file must be valid PHP 7.4: no union types,
no nullsafe operator, no `match`, no named arguments, no constructor property promotion, no enums, no `readonly`.
Typed properties and arrow functions, which PHP 7.4 introduced, are fine. A newer function is used only behind
`function_exists()` or through a polyfill. The rule is checked by `make lint74-all` and by a PHP 7.4 job in
continuous integration, which lints every file, installs the production dependencies with Composer on PHP 7.4 and
loads every kernel and extension class.

For 6.0.15 this meant: the namespace `Exponential\View\Kernel\Class` became `Exponential\View\Kernel\ContentClass`
(`class` is a reserved word before PHP 8.0; the old name still works through `class_alias`), a few `Type|null`
parameters became `?Type`, three nullsafe operators became plain checks, `mixed` disappeared from two magic methods,
and a few runtime differences got a fallback: `WeakMap` (8.0) falls back to `SplObjectStorage`, a GD image is a
resource on 7.4 and an object on 8, a cURL handle must stay open until its last use, and `ZipArchive::setMtimeName()`
exists only from 8.0.

**2. Polyfills fill in the functions.** `symfony/polyfill-php80` and `symfony/polyfill-php81` define
`str_contains()`, `str_starts_with()`, `get_debug_type()`, `array_is_list()` and the other small functions added in
PHP 8.0 and 8.1, on the PHP versions that lack them; on newer PHP they do nothing. `lib/phpcompat.php` covers the few
the kernel needs beyond that.

**3. One metapackage per PHP range.** The kernel's `composer.json` requires `se7enxweb/exponential-php-floor` at
`^1.0`. Each version of that metapackage belongs to one PHP range and requires the packages that run there:

| Metapackage version | PHP | What it adds |
|---|---|---|
| 1.0.1 | 7.4 (usable without support) | the last PHP 7.4 releases of the lines that moved to PHP 8 (eztags ~2.4.23, cjw_newsletter ~4.2.14, syndication ~1.3.10, bccie ~1.1.18, git_manager ~2.0.22) and google/recaptcha ~1.2.4; 1.0.0 also required xrowextract and sevenx_dse, whose code does not run on 7.4 |
| 1.1.0 | 8.0 | the extensions that need PHP 8.0, Velocity included, and google/recaptcha ~1.3.1 |
| 1.2.0 | 8.1 to 8.3 | all of 1.1.0, plus Exponential Layouts, the expsite packages and the media site theme; google/recaptcha ~1.3.1 |
| 1.3.0 | 8.4 and later | the same set with the current google/recaptcha 2.x |
| 1.1.4 | 8.0 | all of 1.1.0 plus Exponential Layouts, the expsite packages and the media site theme, which run on PHP 8.0; 1.1.0 to 1.1.3 left them out |

Composer always installs the highest version whose PHP requirement the running PHP meets. The result: the same
`composer require se7enxweb/exponential` (or `composer create-project` of a skeleton) gives a complete, consistent
installation on every supported PHP, and nobody has to know which extension needs which PHP. The source of the
metapackage is [se7enxweb/exponential-php-floor](https://github.com/se7enxweb/exponential-php-floor); its README lists
each version.

The technique is not new to the project: the Exponential Platform DXP 4.6 line uses the same kind of metapackage,
`se7enxweb/exponential-platform-dxp-php-floor`, to select maintained forks of its libraries on older PHP versions.

## A short history of PHP support in Exponential

| Release | PHP |
|---|---|
| eZ Publish 4.x and 5.x (historical, discontinued) | PHP 5, later PHP 7 for the legacy kernel |
| up to 6.0.7 | PHP 7.4 and 8.1 |
| 6.0.8 (April 2025) to 6.0.14 | PHP 8.1 and later; PHP 8.4 support began in 6.0.8, PHP 8.5 support in 6.0.11 |
| 6.0.15 | Supported: PHP 8.0 again (the stock PHP of Red Hat Enterprise Linux 9) to 8.5; usable without support: PHP 7.4 |

The step to PHP 8.1 in 6.0.8 was right at the time: it let the project clean up years of PHP 5 and PHP 7 code
quickly. Once that work was done, the same review showed that very little of the kernel actually needed PHP 8. Only a
handful of language features stood between the code and PHP 7.4, so going back cost days, not months. That is why
the supported floor came down to 8.0 in 6.0.15, and the software was made usable, without support, on 7.4.

## Why the project does this

Exponential grew out of a content management system that organizations have run for more than two decades. Many of
them still do. The engineers at 7x, who maintain Exponential, have spent most of that time helping such
organizations, and they have seen the same pattern many times: a site is not held back by a lack of will, but by a
contract, a certification, a platform or a budget cycle that nobody on the project can change this year.

The project's values follow from that experience:

- **Meet people where they are.** Software that only runs on the newest platform helps the organizations that need
  help least. Keeping the software usable on an older PHP version, even without support, lets the others take part, get the current fixes and features, and plan
  their own move.
- **Separate the application upgrade from the platform upgrade.** Changing Exponential and changing PHP at the same
  time doubles the risk. With PHP 7.4 support, a site first moves to the current Exponential on the PHP it has, and
  later moves to PHP 8 with an application that already runs there. Each step can be tested and rolled back on its
  own.
- **Never trade the future for the past.** Supporting PHP 7.4 does not hold back PHP 8.5: the newest PHP is
  supported from its first release, and every feature that needs PHP 8 is still built. The old version is carried
  by a small set of rules and tools, not by giving anything up.
- **Be honest about the cost.** Older PHP has real drawbacks. This page names them instead of hiding them.

## How security is kept

Exponential on an old PHP is only as safe as the whole stack, and on PHP 7.4 that stack is unsupported. This is how each layer is kept safe.

**PHP itself.** The PHP project ended security support for PHP 7.4 on 28 November 2022. Run PHP 7.4 only where
someone still fixes it: the PHP packages of an operating system with long-term support (for example Debian LTS or
extended support, Ubuntu Pro, or the PHP 7.4 stream of Red Hat Enterprise Linux 8 and its rebuilds within their
support periods), or a host that backports security fixes. An unpatched PHP 7.4 downloaded from somewhere is a risk
no application can remove.

**Exponential's own code.** The kernel is one code base for every PHP version. Security fixes go into it once and
reach PHP 7.4 and PHP 8.5 alike, although the project makes no commitment specific to 7.4; there is no separate, older branch that could fall behind.

**Dependencies.** Composer's security blocking stays on: Composer refuses package versions with known security
advisories during resolution, on every PHP version. The metapackage only selects versions that Composer accepts.
Where an upstream library dropped an old PHP version before fixing a vulnerability, the project maintains patched
forks of the last compatible line (the approach already used for the DXP 4.6 line, for example for Twig 3.11 and
graphql-php 14.11) instead of pinning a vulnerable release.

**Extensions.** On PHP 7.4 a few extensions are installed in the last release of their line that still runs there.
These releases are days or weeks older than the newest ones, not years; when a security fix lands in such an
extension, the project publishes a release of that line for PHP 7.4 too, or a new version of the metapackage that
requires it.

**Polyfills.** The polyfills come from the Symfony project, are widely used and are maintained independently of
Exponential. They only add functions; they change nothing on PHP versions that already have them.

## The honest trade-offs

- **Fewer features.** Velocity, Layouts, two-factor authentication and the other packages in the table are not
  available on PHP 7.4. For many small and medium sites that is acceptable; for others it is the reason to move to
  PHP 8 first.
- **Older extension releases.** Some extensions are one or a few releases behind on PHP 7.4.
- **Less testing.** The full test suite runs on PHP 8.1 to 8.5. On PHP 7.4 the continuous integration checks the
  syntax of every file, a production install and the loading of every class. The project's own test runs on PHP 7.4
  use PHPUnit 9.6, the last PHPUnit for PHP 7.4, which does not read the PHP 8 attributes some tests use.
- **Slower PHP.** PHP 8 is faster than PHP 7.4, often by a wide margin with OPcache and the JIT. A busy site gains
  speed from the move alone.
- **Security depends on your vendor.** See above: an unpatched PHP 7.4 is a risk no application can remove.

## The end-of-life stance

Usability on PHP 7.4 is a **bridge**, without support. It exists so that organizations can run a current Exponential while they prepare
their move to PHP 8. The project's commitments:

- Usability on PHP 7.4 is not removed in a patch release. When the time comes, it is announced in the changelog and in
  this documentation at least one release ahead, and the release that removes it says so in its notes.
- The deciding factor is security: as long as maintained, patched PHP 7.4 packages are widely available from
  operating system vendors and the dependencies can be kept free of known vulnerabilities on PHP 7.4, the bridge
  stays.
- The newest PHP is never dropped or delayed to keep an old one.
- Every site on PHP 7.4 should have a plan to move. The [PHP upgrade runbook](hosting/runbooks/php-upgrade.md) shows
  the steps; with Exponential already current, the move is a change of PHP and a `composer update`, nothing more.

## Installing on PHP 7.4

1. Check the machine with [2. Requirements](../install/02-requirements.md): PHP 7.4 with the required extensions
   for the web server **and** the command line (the same version and extensions in both).
2. Install the 6.0.15 line, as on any PHP version ([chapter 3](../install/03-getting-the-code.md)). Run Composer
   with the PHP 7.4 that will serve the site, so it selects the PHP 7.4 packages.
3. Serve the site with Apache or nginx and PHP-FPM, or with `exp:velocity start --engine=php` for a quick test.
4. When the machine moves to PHP 8, run `composer update` with the new PHP: Composer moves the metapackage to the
   version for that PHP and installs the packages that were left out.

## Related pages

- [2. Requirements](../install/02-requirements.md): every PHP version, extension and setting
- [Operating system support](os-support.md)
- [PHP 8.0 support](../bc/6.0/php-8.0-support.md): the fallbacks for PHP 8.0, which PHP 7.4 shares
- [Supported PHP versions](php-developers/supported-php-versions.md): the rules for code and extensions
- [PHP upgrade runbook](hosting/runbooks/php-upgrade.md)
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
