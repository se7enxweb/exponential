---
title: "Command line: the console and eZScript"
audience: web-framework-developer, developer
category: web-frameworks
order: 310
summary: The Exponential counterpart of bin/console, artisan, manage.py, rails commands and Spring Boot runners - bin/php/console with its exp:, ext:, shell: and cron: namespaces, writing a command as an Exponential\Runnable\Command class or a classic eZScript script, options, siteaccess and user, exit codes, and the commands you use every day.
keywords: console, CLI, command line, bin/php/console, artisan, manage.py, bin/console, exp:, ext:, cron:, eZScript, eZCLI, Exponential\Runnable\Command, getOptions, startup, shutdown, allow-root-user, siteaccess, exit codes, web framework developer
type: help
---
# Command line: the console and eZScript

This page is for developers who live in `bin/console`, `php artisan`, `manage.py` or `bin/rails`. Exponential has one
entry point for every command, `bin/php/console` (installations usually add a `./console` shortcut in the root). It
finds commands at run time from their file location, so an extension's command needs no registration
([Exponential Console](../../bc/6.0/console.md), [Console commands](../creators/app-creators/console-commands.md)).

## Contents

1. [Finding commands](#finding-commands)
2. [Command names](#command-names)
3. [Writing a command](#writing-a-command)
4. [Siteaccess, user and root](#siteaccess-user-and-root)
5. [Commands you will use daily](#commands-you-will-use-daily)
6. [Compared with other frameworks](#compared-with-other-frameworks)

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
| `cron:<part>` | a cronjob part | `cron:frequent` |

A script can declare extra names with an `@alias` tag in its docblock (`bin/php/exphelp.php` declares `@alias
exp:help`), and a `@description` tag describes it in `list`.

## Writing a command

New commands are classes extending `Exponential\Runnable\Command`, called by a one-line script
([CLI scripts with eZScript](../creators/app-creators/cli-scripts.md)):

```php
<?php
// extension/expdemo/classes/runnable/commands/report.php
namespace Exponential\Command\Extension\Expdemo;

class Report extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Writes the monthly report",
                              'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $options = $this->startup( '[month:][dry-run]', '', array(
            'month'   => 'The month, YYYY-MM',
            'dry-run' => 'Show what would be written, write nothing' ) );
        $this->output( \expDemoReportService::build( $options['month'], !empty( $options['dry-run'] ) ) );
        $this->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
// extension/expdemo/bin/php/report.php
require_once 'autoload.php';
\Exponential\Command\Extension\Expdemo\Report::main( __FILE__ );
```

Run it: `php bin/php/console ext:expdemo:report --month=2026-10 --dry-run`. The classic form, used by many scripts,
calls `eZScript::instance()`, `startup()`, `getOptions()`, `initialize()` and `shutdown()` directly; output goes through
`eZCLI` (`output()`, `notice()`, `warning()`, `error()`). Option strings use `[name:]` for a value and `[name]` for a
flag; `--help`, `-s/--siteaccess`, `--allow-root-user` and the debug options are added for you.

## Siteaccess, user and root

- `-s <siteaccess>` selects the settings; extensions active only in that siteaccess need it.
- The user is anonymous. A command that writes content signs in a dedicated user
  (`eZUser::setCurrentlyLoggedInUser( $user, $user->id() )`), never the shared administrator.
- Scripts refuse to run as root unless `--allow-root-user` is given; run them as the web server's user, because files a
  root process writes into `var/` break the site later.
- The exit code is returned unchanged: `shutdown( 0 )` for success, anything else for failure.
- Offer `--dry-run` for every command that writes or deletes, and never accept passwords as options.

## Commands you will use daily

| Command | Does |
|---|---|
| `exp:ezpgenerateautoloads -e` | regenerate extension autoloads after adding classes |
| `exp:ezcache --clear-tag=ini` | clear caches by tag or id |
| `exp:cache status` | every cache layer, PASS or FAIL |
| `exp:ini get/set/where` | read and change settings |
| `exp:velocity deploy` | autoloads, caches, PHP reload, Velocity restart, in order |
| `exp:install` | a new installation in one command ([Installing in one command](../../features/6.0/install-in-one-command.md)) |
| `exp:help check`, `exp:help search` | the help's link check and search |
| `exp:maintenance on/off` | maintenance mode |
| `cron:<part>` | run a cronjob part |

## Compared with other frameworks

| Framework | Concept | Exponential |
|---|---|---|
| Symfony | commands registered as services, `#[AsCommand]` | files found by location, `Exponential\Runnable\Command` |
| Laravel | Artisan commands, `$signature` | the option string of `startup()` / `getOptions()` |
| Django | management commands in `management/commands/` | scripts in `extension/<ext>/bin/php/` |
| Rails | rake tasks, `bin/rails` commands | the same |
| Spring | `CommandLineRunner` | a command class |

## Related pages

- [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Command pattern](../../standards/patterns/command.md)
- [Scheduled tasks: cronjobs](scheduled-tasks-and-cronjobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
