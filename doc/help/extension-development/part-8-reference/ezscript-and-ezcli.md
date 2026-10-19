---
title: eZScript and eZCLI
audience: developer
category: extension-development
order: 140
summary: The reference of eZScript (instance settings, startup, getOptions, initialize, iterate, shutdown) and eZCLI (output, notice, warning, error, stylize) for command line scripts and console commands of an extension.
keywords: eZScript, eZCLI, instance, startup, getOptions, initialize, shutdown, iterate, resetIteration, isQuiet, output, notice, warning, error, stylize, command line
type: help
---
# eZScript and eZCLI

Part 8, reference 14. `eZScript` (`kernel/classes/ezscript.php`) sets up the kernel for a command line script;
`eZCLI` (`lib/ezutils/classes/ezcli.php`) writes to the terminal. A complete script and the console's way of finding
it are in [CLI scripts and console commands](../part-3-extension-developer/cli-scripts-and-console-commands.md).

## eZScript

| Method | Use |
|---|---|
| `eZScript::instance( $settings = array() )` (static) | the script object |
| `startup()` | basic initialization; call first |
| `getOptions( $config = '', $argumentConfig = '', $optionHelp = false, ... )` | parse options and arguments; handles `--help` |
| `initialize()` | siteaccess, database, extensions; call after `getOptions()` |
| `isQuiet()` | whether `-q` was given |
| `resetIteration( $iterationMax = false, $startIndex = 0 )`, `iterate( $cli, $status, $text = false )` | a progress display for loops |
| `setUseDebugOutput( $useDebug )` | debug output at the end |
| `shutdown( $exitCode = false, $exitText = false )` | clean exit with an exit code |

`instance()` settings used most:

| Key | Meaning |
|---|---|
| `description` | the text `--help` shows |
| `use-session` | start a session (rarely needed) |
| `use-modules` | load the module system |
| `use-extensions` | read active extensions' settings (almost always `true`) |

`getOptions()` option syntax: `[name:]` takes a value (`--name=Ada`), `[flag]` does not, `[n:]` is a short option.
The return value is an array keyed by option name, plus `arguments` for the positional arguments.

## A loop with progress

```php
<?php
$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Re-index items.\n", 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[limit:]', '', array( 'limit' => 'How many items (default: all)' ) );
$script->initialize();

$items = array( 1, 2, 3 );
$script->resetIteration( count( $items ) );
foreach ( $items as $item )
{
    $ok = true; // the work for $item
    $script->iterate( $cli, $ok, "Item $item" );
}
$script->shutdown( 0 );
```

`iterate()` prints a `.` for success and a `~` for failure per item, wrapping lines with a running count.

## eZCLI

| Method | Writes |
|---|---|
| `eZCLI::instance()` (static) | the terminal object |
| `output( $string = false, $addEOL = true )` | normal output (suppressed with `-q`) |
| `notice( ... )`, `warning( ... )`, `error( ... )` | messages of that kind |
| `stylize( $styleName, $text )` | text with a style, e.g. `$cli->stylize( 'success', 'Done' )` |
| `setUseStyles( $useStyles )` | colors on or off |

## Exit codes and safety

- Exit with `0` on success and non-zero on failure, so cronjobs and deployments can react.
- Scripts refuse to run as root unless `--allow-root-user` is given; run them as the web server's user so the files
  they write have the right owner.
- A script that changes data should offer a dry run (`[dry-run]`) and say what it would do.

## Related pages

- Previous: [eZModule and module results](ezmodule.md)
- Next: [eZFile, eZDir and cluster file handlers](ezfile-ezdir-and-cluster-files.md)
- [Console commands for developers](console-commands-for-developers.md)
- [Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
