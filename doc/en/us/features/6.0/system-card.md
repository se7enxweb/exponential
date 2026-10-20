---
title: "The system card: exp:expfetch, exp:info and exp:about"
audience: admin, developer, hosting, partner, supporter, trainer, advocate, press
category: administration
summary: Three console commands that draw the Exponential "e" next to the facts of an installation, its engine and its machine, with health hints. exp:expfetch is the card, exp:info adds an inventory, exp:about adds the project. Text, plain text or JSON; themes, color depths, a live --watch view, and an extension API for providers, themes and logos. Never prints a secret.
keywords: exp:expfetch, exp:info, exp:about, expfetch, system card, syscard, syscard.ini, expSysCardProvider, expSysCardBaseProvider, expSysCardRegistry, expSysCardRenderer, system information, health hints, neofetch, fastfetch, --json, --plain, --watch, --animate, NO_COLOR, truecolor, 256 colors, themes, logo, console, support request, monitoring
type: feature
---
# The system card: exp:expfetch, exp:info and exp:about

This page is for administrators, hosting staff and developers who want to see at a glance what an Exponential
installation is and how it is doing, and for anyone who has to paste that into a support request or show it in a
demo. It covers the three commands, every option, the output formats, the hints, the settings and how an extension
adds its own facts. Added 2026-10-20 in 6.0.15.

## Contents

1. [In short](#in-short)
2. [exp:expfetch: the card](#expexpfetch-the-card)
3. [exp:info: the card and an inventory](#expinfo-the-card-and-an-inventory)
4. [exp:about: the card and the project](#expabout-the-card-and-the-project)
5. [Options](#options)
6. [Output formats](#output-formats)
7. [Layout, colors and themes](#layout-colors-and-themes)
8. [Health hints](#health-hints)
9. [Providers, time budgets and isolation](#providers-time-budgets-and-isolation)
10. [No secrets, ever](#no-secrets-ever)
11. [Settings: syscard.ini](#settings-syscardini)
12. [Adding facts, a theme or a logo from an extension](#adding-facts-a-theme-or-a-logo-from-an-extension)
13. [How the aliases work](#how-the-aliases-work)
14. [Shell completion](#shell-completion)
15. [Why the internals are called "system card"](#why-the-internals-are-called-system-card)
16. [Related pages](#related-pages)

## In short

| Command | Shows | Use it when |
|---|---|---|
| `./console exp:expfetch` | Exponential, the machine, PHP, the engine (Velocity, Apache, PHP-FPM), the database and the site, with hints | you log in to a server, after an install or upgrade, in a demo |
| `./console exp:info` | the same, plus every extension with its version, the siteaccesses, key settings, the cronjob parts, the queue, the cache sizes and the help coverage per language | you need an inventory: a support request, an audit, a handover |
| `./console exp:about` | the same as exp:expfetch, plus what Exponential is, the Foundation and 7x, the license, links, how to contribute, the partner program and the credits | someone asks "what is this?" |

All three are read-only. They print to the terminal in color only when the output is a terminal, never with
`NO_COLOR` set, and never print a password, key, token or full database address. As root, add
`--allow-root-user`, like every console command.

## exp:expfetch: the card

**What it shows.** The Exponential "e" in orange block art, and next to it the facts, grouped in sections:

- **Exponential**: the version, its state and line; for a git checkout the branch and the commit.
- **System**: the operating system, the kernel, the host name, the uptime, the load, the CPU (model and cores), the
  memory, the disk of the installation, the shell and the terminal.
- **PHP**: the version and SAPI, OPcache (with its memory), the JIT, the extensions that matter to Exponential and
  `memory_limit`.
- **Engine**: Exponential Velocity (version, running or stopped, live and configured workers, ports and TLS, the
  number of variables in its envvars file, demo mode) and the other web servers on the machine (Apache, nginx,
  Caddy, FrankenPHP, PHP-FPM masters and pool workers).
- **Database**: its type, the server's version and the host (only host and port).
- **Site**: the siteaccesses and the default one, the active extensions, demo mode, debug output, the mail
  transport (its type only), the caches, the help (pages, languages, whether its index is current), the last
  cronjob run and Composer.
- **Hints**: one line for each fact that needs attention ([Health hints](#health-hints)).
- **Color bars**: the terminal's 16 colors and a bar of the theme's gradient.

**When to use it.** The first thing after you log in to a server, after an install or an upgrade, before and after a
deploy, and in a demo.

```bash
./console exp:expfetch
./console exp:expfetch --plain
./console exp:expfetch --section=php,engine
```

Sample output (`--plain`, values of an example machine):

```text
Exponential 6.0.15 @ web.example.org
====================================

[Exponential]
Version: Exponential 6.0.15 (stable, 6.0 line)
Git: main @ 9b945ab6bd

[System]
OS: AlmaLinux 9.8 (Olive Jaguar)
Kernel: Linux 5.14.0-687.29.1.el9_8.x86_64 x86_64
Host: web.example.org
Uptime: 12d 4h
Load: 1.52 1.31 1.20
CPU: AMD EPYC (12 cores)
Memory: 25.9 GiB / 46.8 GiB (55%)
Disk: 1.4 TiB / 2.0 TiB (72%)
Shell: bash
Terminal: xterm-256color (120x40)

[PHP]
PHP: 8.5.11 (cli)
OPcache: on, also for the CLI, 256 MiB
JIT: off
Extensions: intl mbstring curl gd xsl sodium apcu mysqli pdo_mysql sqlite3 pdo_sqlite imagick pcntl posix zip
memory_limit: 512M

[Engine]
Velocity: v0.0.4.54, running, up 3h 12m
Workers: 52 live / 48 configured
Ports: 8088 (HTTP), 8080 (HTTPS, TLS)
Envvars: 6 variables
Demo mode: off (Velocity)
Apache: running (13 processes)
PHP-FPM: 1 master (8.5), 16 pool workers

[Database]
Database: MySQL/MariaDB 10.11.14-MariaDB
Host: localhost

[Site]
Siteaccesses: 4, default site
Extensions: 38 active
Demo mode: off
Debug: off
Mail: sendmail
Caches: view, templates on; static off
Help: 7,083 pages, en/us de
Cronjobs: last run 4 min ago, 14 parts
Composer: 105 packages, all consistent
```

The live workers count every process below the Velocity server (workers and their helpers), so it can be a few more
than the configured number.

## exp:info: the card and an inventory

**What it shows.** Everything exp:expfetch shows, with a little more detail in some facts (PHP's support date, the
number of loaded PHP extensions, every local disk, the names of the Velocity envvars, the instances and worker model,
the FPM pool names, the database charset and read replica), plus these sections:

| Section | What it lists | Read from |
|---|---|---|
| Extensions | every active extension and its version | `extension.xml` `<metadata>`, `ezinfo.php`, then `composer.json` |
| Siteaccesses | the URL mode (MatchOrder) and, per siteaccess, its design, language and SiteURL host | `site.ini` of each siteaccess |
| Settings | the settings of an allowlist | `syscard.ini [Inventory] Settings[]` |
| Cronjobs | each cronjob part and its scripts | `cronjob.ini` |
| Queue | the expqueue service, live workers, last action | `expQueueService::statusSummary()` (when expqueue is active) |
| Caches | the size of the cache directory and of its ten largest caches | one walk of `var/<site>/cache`, stopped at the time budget |
| Help | the pages of each documentation language against the original | the help index |

**When to use it.** For a support request, an audit, the handover of a site, or to compare two servers (the
`--json` output diffs well).

```bash
./console exp:info
./console exp:info --json --no-logo > info.json
./console exp:info --section=extensions,settings --plain
```

Sample of the sections it adds (`--plain`):

```text
[Caches]
Cache size: 1.2 GiB in 48,210 files
content: 812 MiB
template-block: 201 MiB
exphelp: 96 MiB
ini: 1.4 MiB

[Help]
Help en/us: 7,083 / 7,083 pages (100%)
Help de: 785 / 7,083 pages (11%)

[Cronjobs]
default: unpublish, rssimport, indexcontent, hide, internal_drafts_cleanup
frequent: notification, workflow, contentjobs, audit, mailbounces
infrequent: basket_cleanup, linkcheck, mailpreferences

[Extensions]
ezjscore: 1.4.0
ezoe: 6.0.15
explayouts: 1.2.3

[Siteaccesses]
URL mode: uri, host, port
site: design site, eng-US, www.example.org
admin: design admin4, eng-US, www.example.org/admin

[Settings]
SiteSettings/SiteName: Example site
ContentSettings/ViewCaching: enabled
MailSettings/Transport: sendmail
ServerSettings/Workers: 48

[Queue]
Service: exp-queue.service, installed
Workers: 2 alive
```

## exp:about: the card and the project

**What it shows.** Everything exp:expfetch shows, then the project: what Exponential is, the Exponential Foundation,
7x, Exponential Velocity as part of 6.0.15, the license (code GPL 2.0 or later, documentation GFDL 1.3 or later),
links (website, documentation, GitHub, Packagist, issues, forums), how to contribute (CONTRIBUTING.md and the CLA),
the "Become an Exponential Partner" call to action and the credits. The text is `share/syscard/about.txt`, written
from the documentation (each block names its source); no statement is made up for the card.

**When to use it.** In a demo, a talk or a screenshot, or when someone opens a terminal on a server and asks what
runs there.

```bash
./console exp:about
./console exp:about --section=about,links --plain
```

Sample of the project sections (`--plain`):

```text
[About]
What Exponential is:
Exponential is a professional PHP application framework with advanced CMS
(content management system) functionality. As a CMS its most notable feature is
its fully customizable and extendable content model. ...

[Project]
Code: GNU General Public License v2.0 (or any later version)
Documentation: GNU Free Documentation License 1.3 (or any later version)
Copyright: 1998 - 2026 7x & Exponential Foundation; 1999 - 2014 eZ Systems AS

[Links]
Website: https://exponential.earth
Documentation: https://exponential.doc.exponential.earth/Exponential
GitHub: https://github.com/se7enxweb/exponential
Packagist: https://packagist.org/packages/se7enxweb/exponential
Issues: https://issues.exponential.earth
Forums: https://share.exponential.earth/forums

[Contribute]
...
Become an Exponential Partner:
Join the network of official partners: companies that build, host or sell around
Exponential and support the project in return. Apply at
https://exponential.earth/partner/apply
```

## Options

The same options work for all three commands.

| Option | What it does | Example |
|---|---|---|
| `--json` | one JSON document: sections, hints, how each provider ran; no logo, no colors | `./console exp:info --json --no-logo` |
| `--plain` | plain text: no logo, no colors, ASCII marks, one `Label: value` per line; `[warn]`/`[error]` after a value that needs attention | `./console exp:expfetch --plain` |
| `--no-logo` | the facts without the logo | `./console exp:expfetch --no-logo` |
| `--section=<list>` | only these sections, comma separated: `system`, `php`, `exponential`, `engine`, `database`, `site`; exp:info also `extensions`, `siteaccesses`, `settings`, `cron`, `queue`, `caches`, `help`; exp:about also `about`, `project`, `links`, `contribute`, `credits` | `./console exp:expfetch --section=php,engine` |
| `--no-color` | no colors (like `NO_COLOR=1`) | `./console exp:expfetch --no-color` |
| `--color=<depth>` | `auto` (default), `truecolor`, `256`, `16` or `none`; `NO_COLOR` still wins | `./console exp:expfetch --color=256` |
| `--theme=<name>` | `rainbow` (default), `orange`, `mono`, `high-contrast`, `light`, or one an extension adds | `./console exp:expfetch --theme=light` |
| `--width=<columns>` | the width (default: `COLUMNS`, else `stty size`, else `[Display] DefaultWidth`) | `./console exp:expfetch --width=100` |
| `--ascii` | ASCII art and marks instead of Unicode block art | `./console exp:expfetch --ascii` |
| `--no-hints` | leave out the Hints block | `./console exp:expfetch --no-hints` |
| `--no-bars` | leave out the color bars | `./console exp:expfetch --no-bars` |
| `--watch[=<seconds>]` | refresh in place every N seconds (default 2), like `top`; Ctrl-C stops | `./console exp:expfetch --watch=5` |
| `--count=<n>` | with `--watch`: stop after n refreshes | `./console exp:expfetch --watch=1 --count=10` |
| `--animate` | a reveal of the logo and its gradient, under a second; only on a color terminal | `./console exp:expfetch --animate` |
| `--mode=<card>` | `card`, `info` or `about`: which card, when the command is run as `php bin/php/expfetch.php` | `php bin/php/expfetch.php --mode=info` |
| `--list-providers` | the providers: id, class, cards, priority, budget, on or off | `./console exp:expfetch --list-providers` |
| `--list-themes` | the themes of `syscard.ini` | `./console exp:expfetch --list-themes` |
| `--completion=<shell>` | a completion script for `bash` or `zsh` | `./console exp:expfetch --completion=bash >> ~/.bashrc` |

The standard options of every console command work too (`--siteaccess`, `--quiet`, `--allow-root-user` ...). The
exit code is 0, or 2 for a wrong option. Generated reference: [exp:expfetch](../../examples/commands/expfetch.md).

### --watch

`--watch` clears the screen and draws the card again every N seconds. Only the providers that change from second to
second run again (the machine: load, memory, disk; Velocity: its workers); the others keep their first facts, so a
refresh costs almost nothing. The footer shows the interval and the time. Ctrl-C (or SIGTERM) stops it and shows
the cursor again. When the output is not a terminal, each refresh is printed under the previous one instead.

## Output formats

**Text** (the default) draws the logo and the facts side by side. **Plain** (`--plain`) is meant for pasting into an
issue, a mail or a log. **JSON** (`--json`) is meant for programs, monitoring and diffs:

```json
{
    "card": "info",
    "generated": "2026-10-20T08:00:00+00:00",
    "title": "Exponential 6.0.15",
    "host": "web.example.org",
    "sections": {
        "php": [
            { "key": "php.version", "label": "PHP", "value": "8.5.11 (cli)", "severity": "ok", "kind": "row" },
            { "key": "php.opcache", "label": "OPcache", "value": "off", "severity": "warn", "kind": "row",
              "hint": "OPcache is off (opcache.enable=0): every request compiles the PHP code again. Turn it on in php.ini." }
        ]
    },
    "hints": [
        { "key": "php.opcache", "severity": "warn", "hint": "OPcache is off (opcache.enable=0): ..." }
    ],
    "providers": [
        { "id": "php", "class": "expSysCardPhpProvider", "status": "ok", "seconds": 0.004, "priority": 80, "budget": 0.5 }
    ]
}
```

- `card` is `card`, `info` or `about`; `generated` is ISO 8601; `host` is the host name.
- `sections` maps a section id to its facts in order. A fact has a stable `key` (`<provider>.<name>`), a `label`, a
  `value` (always a string), a `severity` (`ok`, `info`, `warn`, `error`), a `kind` (`row`, or `text` for a
  paragraph of exp:about) and, when it needs attention, a `hint`.
- `hints` lists the hints, worst first.
- `providers` says how each provider ran: `status` is `ok`, `slow` (it took longer than its budget, without
  isolation), `timeout`, `error` or `skipped` (the total budget was spent).

A monitoring check can read one value, for example `jq -r '.sections.php[] | select(.key=="php.opcache") | .severity'`,
or count the errors: `jq '[.hints[] | select(.severity=="error")] | length'`.

## Layout, colors and themes

### Layout by width

The card follows the width of the terminal: the logo and a gap of three columns next to at least 36 columns of facts.

| Width | Layout |
|---|---|
| 82 columns or more | the big "e" with the wordmark EXPONENTIAL under it, then the facts |
| 60 to 81 | the big "e" |
| 50 to 59 | the small "e" |
| 41 to 49 | the mini "e" |
| 40 or less | stacked: the mini "e" on top, the facts under it |

Values that do not fit are cut with an ellipsis; the paragraphs of exp:about are wrapped. With a UTF-8 locale
(`LC_ALL`, `LC_CTYPE`, `LANG`) the logo is block art and the marks are dots and signs; otherwise, or with `--ascii`, the
ASCII drawings and `+ - ! x` are used.

At 120 columns, without colors (the first lines):

```text
      ▄▄██████▄▄                              Exponential 6.0.15 @ web.example.org
   ▄███▀▀    ▀▀███▄                           ────────────────────────────────────
  ███            ███
 ███▄▄▄▄▄▄▄▄▄▄▄▄▄▄███                         Exponential
 ███▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀                         · Version      Exponential 6.0.15 (stable, 6.0 line)
 ███                                          · Git          main @ 9b945ab6bd
  ███▄           ▄▄▄
   ▀███▄▄    ▄▄███▀                           System
      ▀▀██████▀▀                              · OS           AlmaLinux 9.8 (Olive Jaguar)
                                              · Kernel       Linux 5.14.0-687.29.1.el9_8.x86_64 x86_64
█▀▀ ▀▄▀ █▀█ █▀█ █▄ █ █▀▀ █▄ █ ▀█▀ █ ▄▀█ █     · Host         web.example.org
██▄ █ █ █▀▀ █▄█ █ ▀█ ██▄ █ ▀█  █  █ █▀█ █▄▄   · Uptime       12d 4h
                                              · Load         1.52 1.31 1.20

                                              PHP
                                              ● PHP          8.5.11 (cli)
                                              ▲ OPcache      off
```

The marks: `●` ok (green), `·` info (dim), `▲` warn (yellow), `✖` error (red, bold).

### Color depth

| Depth | When | How a color is sent |
|---|---|---|
| truecolor | `COLORTERM=truecolor` or `24bit` | `ESC[38;2;R;G;Bm`, the exact color |
| 256 | `TERM` contains `256` | `ESC[38;5;Nm`, the nearest color of the 6x6x6 cube or the gray ramp |
| 16 | any other `TERM` | `ESC[30m`-`ESC[37m` and `ESC[90m`-`ESC[97m`, the nearest basic color (or the theme's `<Role>16`) |
| none | `NO_COLOR` set (<https://no-color.org>), `TERM=dumb`, output not a terminal, `--no-color`, `--plain`, `--json` | no escape codes at all |

`--color=` forces a depth (for a screenshot or a terminal that does not say what it can), except that `NO_COLOR`
always wins. Other codes: `ESC[1m` bold (labels, headings, the logo), `ESC[2m` dim (when a theme has no Dim color),
`ESC[0m` reset after each colored piece. The palette rows are background colors `ESC[40m`-`ESC[47m` and
`ESC[100m`-`ESC[107m`, so they show the terminal's own palette. `--watch` clears with `ESC[H ESC[2J`, hides the cursor
with `ESC[?25l` and shows it again with `ESC[?25h`; `--animate` draws the logo line by line, moves back up with
`ESC[nA` and clears each line with `ESC[K`.

### Themes

| Theme | For |
|---|---|
| `rainbow` (default) | the orange "e" with a rainbow gradient on the wordmark and the bar |
| `orange` | the Exponential orange only |
| `mono` | no colors, only bold |
| `high-contrast` | bright colors, no dim text |
| `light` | a light terminal background: darker colors |

A theme is a block `[Theme-<name>]` in `syscard.ini` with a `#RRGGBB` color per role (`Logo`, `LogoShade`, `Accent`,
`Label`, `Value`, `Dim`, `Ok`, `Info`, `Warn`, `Error`), `Gradient[]` stops, `GradientDirection=columns|lines` and
`Bold`. An empty color is the terminal's own. `<Role>16=` names the basic color for a 16-color terminal (`yellow`,
`bright-yellow` ...), so the orange "e" turns yellow there and not red. The "e" is shaded from `Logo` at the top to
`LogoShade` at the bottom.

## Health hints

A fact that needs attention is marked and adds a line to the Hints block (and to `hints` in the JSON):

| Provider | Hint when |
|---|---|
| host | the load is more than twice the number of cores; memory is 90% used (error at 95%); a disk is 90% full (error at 97%) |
| php | the PHP branch has no security support any more (error) or loses it within `[Php] WarnDays` (warn); OPcache not loaded or off; a recommended extension is missing (intl, mbstring, curl, gd or imagick, xsl, sodium, apcu); `memory_limit` below 256M |
| velocity | Velocity runs in demo mode (`EXP_DEMO_MODE=1` in its envvars) |
| database | the database does not answer (error) |
| site | demo mode is on; debug output is on for every visitor |
| caches | the view cache is off |
| help | the help index is older than the pages or missing |
| cron | no cronjob run was found, or none for two days |
| composer | `vendor/` is missing (error); packages do not agree between composer.json, composer.lock and vendor/ (ezupdate's inventory) |
| queue | the expqueue service is installed but no worker runs |

The PHP support dates are `syscard.ini [Php] SecuritySupportEnds[]` (from php.net). The Composer count uses the
ezupdate extension's inventory, which reads only local files; it is kept for `[Updates] CacheTime` seconds, and only
when the command runs as the owner of the cache directory (a file written by root there would block the web server's
user from clearing the caches).

The facts are those of the command-line PHP and of the machine. The web server's PHP (PHP-FPM, Velocity) can have its
own php.ini: [Setup > System information](../../guides/system-information.md) shows that one, and
`./console exp:system:info` has the full health checks.

## Providers, time budgets and isolation

The facts come from providers, one class per group of facts, listed in `syscard.ini [Providers] ProviderClasses[]`
in the order their facts are shown:

| id | Class | Card | Section(s) |
|---|---|---|---|
| exponential | `expSysCardExponentialProvider` | all | exponential |
| host | `expSysCardHostProvider` | all | system |
| php | `expSysCardPhpProvider` | all | php |
| velocity | `expSysCardVelocityProvider` | all | engine |
| webserver | `expSysCardWebServerProvider` | all | engine |
| database | `expSysCardDatabaseProvider` | all | database |
| site | `expSysCardSiteProvider` | all | site |
| caches | `expSysCardCacheProvider` | all | site (exp:info: caches) |
| help | `expSysCardHelpProvider` | all | site (exp:info: help) |
| cron | `expSysCardCronProvider` | all | site (exp:info: cron) |
| composer | `expSysCardComposerProvider` | all | site |
| extensions | `expSysCardExtensionsProvider` | info | extensions |
| siteaccess | `expSysCardSiteAccessProvider` | info | siteaccesses |
| settings | `expSysCardSettingsProvider` | info | settings |
| queue | `expSysCardQueueProvider` | info | queue |
| about | `expSysCardAboutProvider` | about | about, project, links, contribute, credits |

Each provider has a **priority** (the higher, the earlier it runs) and a **budget** (seconds). All of them together
get `[Runtime] TotalBudget` seconds; when that is spent, the providers left show `n/a (time)`, which is why the
priority matters. With `[Runtime] Isolation=enabled` (and the pcntl and posix extensions) each provider runs in a
child process that is killed when its budget is spent: a database that does not answer or a stalled disk shows
`n/a (timeout)` and never hangs the card. The child ends with SIGKILL, so it never closes a connection or removes a
file that belongs to the command. Without isolation, providers keep their budget themselves (a program they run and
a directory they walk stop at the deadline), and one that went over is reported as `slow` in the JSON.

`./console exp:expfetch --list-providers` shows the list, the cards, the priorities, the budgets and whether each is
on. Nothing runs a shell: programs are started without one, and nothing is asked over the network.

## No secrets, ever

The card is meant to be pasted into public places, so it is safe by construction:

- Providers show only what they need: the database's type, version and host (never the user, password, database
  name or a full DSN); the mail transport's type (never its server or login); the names of the Velocity envvars
  (never their values); the host of a SiteURL (never credentials in it).
- `exp:info` shows only the settings of `[Inventory] Settings[]`, and never one whose name looks like a secret
  (password, key, token, secret, salt, user, login, auth, session, dsn ...), even when the list names it.
- Every fact passes `expSysCardRegistry::safe()` before it is shown, whatever a provider (also an extension's)
  returns: a fact whose name matches `expSysCardRegistry::SECRET_NAME` (password, secret, token, api key, private key,
  credential, salt, cookie, session id, dsn ...) is dropped, and `mask()` replaces the password of
  `scheme://user:password@host`, the value of `password=...`, `token: ...`, `api_key=...` and a `Bearer`/`Basic`
  credential with `***`, then applies the masking of the system report (long key-like strings).
- The installation path is not printed (the disk of the installation is labeled "Disk").

## Settings: syscard.ini

| Block | Settings |
|---|---|
| `[Providers]` | `ProviderClasses[]` (order), `Enabled[<id>]=disabled`, `Priority[<id>]=<n>`, `Budget[<id>]=<seconds>` |
| `[Runtime]` | `TotalBudget=6`, `Isolation=enabled` |
| `[Display]` | `DefaultWidth=80`, `Wordmark`, `ColorBars`, `Hints` (enabled/disabled), `Charset=auto|unicode|ascii` |
| `[Sections]` | `Titles[<id>]=<title>` |
| `[Logo]` | `Logo=e`, `Directories[]` (the last listed is looked in first) |
| `[Theme]` | `Default=rainbow`, `Themes[]` |
| `[Theme-<name>]` | the colors of a theme |
| `[Php]` | `SecuritySupportEnds[<branch>]=<date>`, `WarnDays=180` |
| `[Database]` | `AskVersion=enabled`: connect once and ask the server for its version |
| `[Updates]` | `AskUpdateInventory=enabled`, `CacheTime=3600` |
| `[Inventory]` | `Settings[]=<file>/<block>/<name>`: what exp:info shows |
| `[About]` | `TextFile=share/syscard/about.txt` |

Every setting: [syscard.ini](../../examples/ini-settings/syscard/README.md).

## Adding facts, a theme or a logo from an extension

An extension adds to the card with its own `settings/syscard.ini.append.php`; nothing in the kernel changes.

### A provider

A provider implements `expSysCardProvider` (`id()`, `modes()`, `priority()`, `budget()`, `watched()`,
`collect( expSysCardContext $context )`); most extend `expSysCardBaseProvider`, which has defaults and helpers
(`fact()`, `text()`, `size()`, `bytes()`, `duration()`, `ago()`). Read everything through the context
(`$context->ini()`, `->file()`, `->env()`, `->glob()`, `->command()`, `->get( $key, $closure )`), so a test can give
the provider made-up values with `expSysCardContext::fake()`. A full example:

```php
<?php
// extension/myshop/classes/myshopsyscardprovider.php

/**
 * The shop on the system card: the open orders, and a hint when the payment gateway is in test mode.
 */
class myShopSysCardProvider extends expSysCardBaseProvider
{
    protected $id = 'myshop';          // Enabled[myshop], Priority[myshop], Budget[myshop]
    protected $section = 'shop';       // a section of its own (or 'site')
    protected $modes = array( expSysCardContext::MODE_CARD );   // MODE_INFO: only on exp:info
    protected $priority = 30;
    protected $budget = 1.0;

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $open = $context->get( 'myshop.open_orders', function () {
            $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS n FROM myshop_order WHERE status = 1' );
            return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : null;
        } );
        $facts[] = $this->fact( 'orders', 'Open orders', $open === null ? 'n/a' : $open );

        $test = $context->iniEnabled( 'myshop.ini', 'Gateway', 'TestMode', 'disabled' );
        $facts[] = $this->fact( 'gateway', 'Payments', $test ? 'test mode' : 'live',
                                $test ? expSysCardFact::WARN : expSysCardFact::OK,
                                $test ? 'The payment gateway is in test mode (myshop.ini [Gateway] TestMode): no payment is taken.' : '' );
        return $facts;
    }
}
```

```ini
# extension/myshop/settings/syscard.ini.append.php
[Providers]
ProviderClasses[]=myShopSysCardProvider

[Sections]
Titles[shop]=Shop
```

Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`), then check it with
`./console exp:expfetch --list-providers` and `./console exp:expfetch --section=shop`. A test with a fake context:

```php
$context = expSysCardContext::fake( array(
    'myshop.open_orders' => 7,
    'ini' => array( 'myshop.ini' => array( 'Gateway' => array( 'TestMode' => 'enabled' ) ) ),
) );
$facts = ( new myShopSysCardProvider() )->collect( $context );
```

Never return a secret: the registry drops and masks what it recognizes, but a provider should not ask for one.

### A theme

```ini
# extension/myshop/settings/syscard.ini.append.php
[Theme]
Themes[]=myshop

[Theme-myshop]
Logo=#2563EB
LogoShade=#1E3A8A
Logo16=bright-blue
Accent=#2563EB
Label=#60A5FA
Dim=#8A8A8A
Gradient[]
Gradient[]=#60A5FA
Gradient[]=#2563EB
GradientDirection=lines
```

`./console exp:expfetch --theme=myshop`, or `[Theme] Default=myshop` to make it the default.

### A logo

Put the drawings in `extension/<ext>/share/syscard/logos/`: `<name>-big.txt`, `<name>-small.txt`, `<name>-mini.txt`
(Unicode) and the same with `.ascii.txt`, plus `wordmark.txt` and `wordmark.ascii.txt` if you replace the wordmark.

```ini
[Logo]
Logo=myshop
Directories[]=extension/myshop/share/syscard/logos
```

The last directory listed is looked in first, and a drawing not found there is taken from the next, so a logo of the
same name ("e") replaces the kernel's.

## How the aliases work

`exp:info` and `exp:about` are aliases of `exp:expfetch`, declared with `@alias` in `bin/php/expfetch.php`. The
console (`bin/php/console`) passes the name it was called by to the command in the environment variable
`EXP_CONSOLE_COMMAND`, and the command chooses the card from it. Run directly with PHP, the command has no alias to
read: use `--mode`.

```bash
php bin/php/expfetch.php --mode=info --allow-root-user
```

## Shell completion

```bash
./console exp:expfetch --completion=bash >> ~/.bashrc     # or: eval "$(./console exp:expfetch --completion=bash)"
./console exp:expfetch --completion=zsh  >> ~/.zshrc
```

It completes the three commands after `./console`, their options, and the values of `--theme=`, `--color=`,
`--mode=`, `--section=` and `--completion=`.

## Why the internals are called "system card"

Only the command name says "fetch", after the tools it is modeled on. Everything inside is the **system card**
(`expSysCard*`, `kernel/classes/syscard/`, `syscard.ini`, `share/syscard/`), because "fetch" already means something
in Exponential: the template `fetch()` functions, the fetch definitions of modules, function collections and the
`fetch*()` methods of persistent objects. A test keeps the new code free of the word outside the command name.

## Related pages

- [exp:expfetch (generated reference)](../../examples/commands/expfetch.md)
- [syscard.ini (generated reference)](../../examples/ini-settings/syscard/README.md)
- [Health checks and uptime](../../help/hosting/operations/health-checks-and-uptime.md)
- [System information](../../guides/system-information.md)
- [The help system](help-system.md)
- [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
