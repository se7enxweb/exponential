---
title: Console commands
audience: app-creator, developer, admin
category: app-creation
order: 300
summary: The bin/php/console entry point - command names and namespaces, extension commands found automatically, describing your own commands, options and exit codes, and the commands an application developer uses every day.
keywords: console, bin/php/console, commands, exp:, ext:, cron:, shell:, extension commands, description, exit code, exp:velocity, exp:ini, exp:ezcache, app creator
type: help
---
# Console commands

This page is for application creators who run and add command line tools. `bin/php/console` is one entry point for
every script of Exponential: kernel scripts, shell scripts, extension scripts and cronjob parts
([Exponential Console](../../../bc/6.0/console.md)).

## Contents

1. [Finding commands](#finding-commands)
2. [Command names](#command-names)
3. [Your extension's commands](#your-extensions-commands)
4. [Options and exit codes](#options-and-exit-codes)
5. [Commands you will use daily](#commands-you-will-use-daily)

## Finding commands

```bash
php bin/php/console                   # every command
php bin/php/console list ext:expdemo  # one namespace
php bin/php/console --version
```

## Command names

| Namespace | Runs | Example |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | |
| `bin:<name>` | `bin/<name>.sh` or `.php` | |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` or `bin/<name>.sh` | `ext:expqueue:status` |
| `ext:<ext>:sh:<name>` | `extension/<ext>/bin/shell/<name>.sh` | |
| `cron:<part>` | a cronjob part | `cron:frequent` |

Without a namespace the console tries `exp:`, `shell:`, then `bin:`.

## Your extension's commands

Put the script in `extension/expdemo/bin/php/report.php`; the console finds it as `ext:expdemo:report`, no
registration needed. Give it a `@description` tag in its docblock so `list` describes it. Write the logic as a class
([CLI scripts with eZScript](cli-scripts.md)).

## Options and exit codes

- Everything after the command name goes to the script unchanged; `--quiet`, `--version` and `--help` alone belong to
  the console.
- Scripts refuse to run as root unless `--allow-root-user` is given; run them as the web server's user.
- The script's exit code is returned unchanged: 0 for success, anything else for failure, so commands work in CI and
  shell pipelines. Make your commands follow that.

## Commands you will use daily

| Command | Does |
|---|---|
| `exp:ezpgenerateautoloads -e` | regenerate extension autoloads |
| `exp:ezcache --clear-tag=ini` | clear caches by tag or id |
| `exp:cache status` | every cache layer, PASS or FAIL |
| `exp:ini get/set/where` | read and change settings ([exp:ini](../../../features/6.0/exp-ini-command.md)) |
| `exp:velocity deploy` | autoloads, caches, PHP reload, Velocity restart, in order |
| `exp:velocity status`, `restart`, `cache clear` | the application server |
| `exp:help check`, `exp:help search` | the help's link check and search |
| `exp:benchmark` | measure the site |
| `exp:maintenance on/off` | maintenance mode |
| `cron:<part>` | run a cronjob part |

The generated pages of every command, with their options, are in the examples: Commands.

## Related pages

- [CLI scripts with eZScript](cli-scripts.md)
- [Command pattern](../../../standards/patterns/command.md)
- [Cache console](../../../bc/6.0/cache-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
