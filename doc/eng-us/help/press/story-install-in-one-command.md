---
title: "Story: install in one command"
audience: press
category: press-media
order: 80
summary: A feature story about installing Exponential with one console command on SQLite, without a database server or a configuration file - what the command does, the database choices, the Kickstarter and the package manager in the console - with sources and the claims to avoid.
keywords: story, installation, install, one command, exp:install, SQLite, Kickstarter, ezpm, setup wizard, getting started, quick start
type: help
---
# Story: install in one command

This page is for journalists and creators who want to show how quickly Exponential can be tried. Since July 2026 a
complete site installs with one command.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

"This page is for anyone who wants a working Exponential site in a few minutes, without writing a `kickstart.ini` and
without a database server" ([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)).
`./console exp:install` installs the multisite package on SQLite in American English, with a site and an
administration, and prints the login addresses and a summary. Options choose MySQL, PostgreSQL, MongoDB or Oracle
instead.

## What is new, and when

| What | When | Source |
|---|---|---|
| `ezpm` in the console, a one-command installer and the Kickstarter | July 2026 | [July 2026](../../history/2026/2026-07.md) |
| SQLite as a database that needs no server | 7 January 2024 | [January 2024](../../history/2024/2024-01a.md) |

## Facts you can print

| Fact | Source |
|---|---|
| One command: `./console exp:install`, the same as `php bin/php/install.php` | [Installing Exponential in one command](../../features/6.0/install-in-one-command.md) |
| Default: SQLite, `eng-US`, siteaccesses `site` and `admin` | same |
| Database options: `sqlite` (default), `mysql`/`mariadb`, `pgsql`, `mongodb`, `oracle` | [Choose a database](../../features/6.0/install-in-one-command.md#choose-a-database) |
| The Kickstarter installs from a configuration file for automated setups | [Kickstarter CLI](../../features/6.0/kickstarter-cli.md) |
| `ezpm`, a package manager in the console | [ezpm](../../features/6.0/ezpm-package-manager-cli.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Installs on any shared hosting in one click" | One console command on a machine with PHP and the code |
| "No requirements" | PHP 8 and the documented extensions are needed ([Getting the code](../../install/03-getting-the-code.md)) |
| "Production-ready in one command" | A working site to try; production needs the hardening guide ([Security hardening](../security-hardening.md)) |

## Angles and headline ideas

- "From zero to a CMS in one command, no database server."
- Video: [Demo script: install from zero](demo-script-install-from-zero.md).

## Related pages

- [Story: SQLite by default](story-sqlite-by-default.md)
- [Setting up a demo installation](setting-up-a-demo-installation.md)
- [Getting started](../getting-started.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
