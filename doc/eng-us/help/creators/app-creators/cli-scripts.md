---
title: CLI scripts with eZScript
audience: app-creator, developer
category: app-creation
order: 310
summary: Write command line tools for an application - the class-based Command with script(), startup() and options, the classic eZScript pattern, eZCLI output, siteaccess and user, exit codes, dry runs and safety.
keywords: CLI, command line, eZScript, eZCLI, Command, getOptions, startup, initialize, shutdown, options, dry run, exit code, siteaccess, app creator
type: help
---
# CLI scripts with eZScript

This page is for application creators who write command line tools: imports, maintenance, reports. A script boots the
kernel with `eZScript`, reads options, does its work and exits with a status. New scripts are classes extending
`Exponential\Runnable\Command`; the console runs them as `ext:<ext>:<name>`.

![Setup, MCP server: the read-only tools an assistant can use to inspect the installation](../../images/creators/mcp-dashboard.webp)

## Contents

1. [The class-based command](#the-class-based-command)
2. [The classic pattern](#the-classic-pattern)
3. [Output](#output)
4. [Siteaccess and user](#siteaccess-and-user)
5. [Safety rules](#safety-rules)

## The class-based command

From [Commands as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md#a-command):

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

`php bin/php/console ext:expdemo:report --month=2026-10 --dry-run`.

## The classic pattern

Many scripts use `eZScript` directly, as `extension/expmcp/bin/php/dataset_inspect.php` does:

```php
require_once 'autoload.php';
$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => 'What it does', 'use-session' => false,
                                     'use-modules' => true, 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[file:][dry-run]', '', array( 'file' => 'The input file' ) );
$script->initialize();
// work
$script->shutdown( 0 );
```

`getOptions()` takes a config string (`[name:]` with a value, `[name]` a flag), arguments and help texts; `--help`,
`-s/--siteaccess`, `--allow-root-user` and the debug options are added for you.

## Output

`$cli->output()`, `$cli->notice()`, `$cli->warning()`, `$cli->error()`; colors switch off when output is redirected. End
with a summary line and an exit code: `shutdown( 0 )` success, `shutdown( 1 )` failure.

## Siteaccess and user

- `-s <siteaccess>` selects the settings; extensions active only in that siteaccess need it.
- The user is anonymous. A script that writes content logs in a dedicated user
  (`eZUser::setCurrentlyLoggedInUser( $user, $user->id() )`), never the shared administrator.

## Safety rules

- A `--dry-run` for every command that writes or deletes.
- Read help before running other people's commands: some delete without `--help` (`exp:cleanupversions`).
- Never accept passwords as options (they show in the process list); read them from settings or the environment.
- Run as the web server's user; files written by root break the site later.

## Related pages

- [Console commands](console-commands.md)
- [Writing a cronjob script](writing-a-cronjob-part.md)
- [Command pattern](../../../standards/patterns/command.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
