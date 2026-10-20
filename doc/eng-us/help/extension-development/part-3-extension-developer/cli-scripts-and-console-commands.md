---
title: CLI scripts and console commands
audience: developer, app-creator
category: extension-development
order: 210
summary: Command line tools in an extension: bin/php scripts the console runs as ext:<extension>:<name>, eZScript and eZCLI, exit codes, @description, and the Command class form.
keywords: cli, command line, console, bin/php, ext:, eZScript, getOptions, startup, initialize, shutdown, eZCLI, output, Command class, @description, allow-root-user, exit code
type: help
---
# CLI scripts and console commands

Part 3, chapter 21. Administrators and deployments need commands: import this, rebuild that, report on the other. An
extension ships commands as scripts in `bin/php/`, and the console finds them automatically. The example on this page
was run.

## Where the console finds them

The console (`bin/php/console`, usually linked as `./console`) does not keep a list; it finds scripts at run time:

| Script | Command |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` |
| `extension/<ext>/bin/php/<name>.php` | `ext:<ext>:<name>` |
| `extension/<ext>/bin/shell/<name>.sh` | `ext:<ext>:sh:<name>` |

So `extension/mysite_hello/bin/php/greet.php` is `./console ext:mysite_hello:greet`. `./console list ext` lists every
extension command with a one-line description, which the console reads from an `@description` tag in the script's
first docblock (or, failing that, from the `description` given to `eZScript`).

## A plain script

`extension/mysite_hello/bin/php/greet.php`:

```php
#!/usr/bin/env php
<?php
/**
 * @description Greet someone from the command line (an example command of mysite_hello)
 */
require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Greet someone from the command line.\n",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[name:][greeting:]', '',
                                array( 'name' => 'Who to greet (default: world)',
                                       'greeting' => 'The greeting word (default: Hello)' ) );
$script->initialize();

$name = $options['name'] ? $options['name'] : 'world';
$greeting = $options['greeting'] ? $options['greeting'] : 'Hello';
$cli->output( $greeting . ', ' . $name . '!' );

$script->shutdown( 0 );
```

Run from the installation root:

```bash
./console ext:mysite_hello:greet --name=Ada
php extension/mysite_hello/bin/php/greet.php --help
```

The first prints `Hello, Ada!`. `--help` prints the standard options (`-q`, `-s` for the siteaccess, `-d` for debug
output, `-v`, `--allow-root-user` ...) followed by yours:

```text
Options:
  --name=VALUE      Who to greet (default: world)
  --greeting=VALUE  The greeting word (default: Hello)
```

## The steps

| Call | Does |
|---|---|
| `eZScript::instance( $settings )` | the script object; `use-extensions` loads active extensions' settings, `use-modules` the module system, `use-session` a session |
| `$script->startup()` | basic initialization |
| `$script->getOptions( $config, $arguments, $help )` | parses `--name=value` options (`[name:]` takes a value, `[flag]` does not), shows `--help` and exits |
| `$script->initialize()` | siteaccess, database, extensions |
| `$cli->output()`, `$cli->error()`, `$cli->warning()` | write to the terminal (nothing with `-q` for `output()`) |
| `$script->shutdown( $exitCode, $text )` | clean exit with the code; `0` is success |

Use a non-zero exit code for failure, so scripts and deployments can rely on it.

## As a class

Since 6.0.15 the kernel's own commands are classes (`\Exponential\Runnable\Command`); the script is two lines. A
class can be tested and shares code with views and cronjob parts:

```php
<?php
// extension/mysite_hello/classes/runnable/commands/greet.php
namespace Exponential\Command\Extension\MysiteHello;

class Greet extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Greet someone from the command line.",
                              'use-session' => false, 'use-modules' => false, 'use-extensions' => true ) );
        $options = $this->startup( '[name:]', '', array( 'name' => 'Who to greet' ) );
        $this->output( 'Hello, ' . ( $options['name'] ? $options['name'] : 'world' ) . '!' );
        $this->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
require_once 'autoload.php';
\Exponential\Command\Extension\MysiteHello\Greet::main( __FILE__ );
```

The helpers (`script()`, `startup()`, `output()`, `error()`, `isQuiet()`, `log()`, `shutdown()`) are listed in
[Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md).

## Rules for commands

- Start every script with `#!/usr/bin/env php` and make it executable; library files get no such line.
- Accept `--dry-run` for anything that changes data, and show what would change.
- Never print passwords or keys; mask them.
- Work in batches with a progress line for large jobs; respect `-q`.
- Run as the site's user; a file created as root may not be writable by the web server afterwards.

## Related pages

- Previous: [Cronjob parts](cronjob-parts.md)
- Next: [A design inside an extension](design-in-an-extension.md)
- [The console](../../../bc/6.0/console.md)
- [eZScript and eZCLI](../part-8-reference/ezscript-and-ezcli.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
