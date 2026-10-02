# Commands, cronjob parts and module views as classes

From Exponential 6.0.15 the code of every command line script, cronjob part and module view lives in a
**class**. The file you know (`bin/php/ezcache.php`, `cronjobs/workflow.php`, `kernel/content/view.php`)
keeps its path and does one thing: it calls that class.

That makes the work callable from inside the system (a view, a command and a cronjob can share one
implementation), testable, and re-implementable by sites and extensions without copying files.

> **The old way still works.** A script, cronjob part or view written as a plain PHP file runs exactly as
> before; nothing in the kernel requires the new form. It is no longer what the product uses or documents,
> because a plain file cannot be extended, called or tested on its own. Write new code as classes.

## The class hierarchy

```
Exponential\Runnable\Runnable            kernel/private/classes/runnable/runnable.php
├── Exponential\Runnable\Command          a command line script      run()
├── Exponential\Runnable\CronjobPart      a cronjob part             run( array $scope )
└── Exponential\Runnable\ModuleView       a module view              run( array $scope )
```

| Kind | The file | The class |
|---|---|---|
| Command | `bin/php/<name>.php` | `Exponential\Command\Kernel\<Name>` in `kernel/private/classes/commands/<name>.php` |
| Cronjob part | `cronjobs/<name>.php` | `Exponential\Cronjob\Kernel\<Name>` in `kernel/private/classes/cronjobs/<name>.php` |
| Module view | `kernel/<module>/<view>.php` | `Exponential\View\Kernel\<Module>\<View>` in `kernel/private/classes/views/<module>/<view>.php` |
| Extension command | `extension/<ext>/bin/.../<name>.php` | `Exponential\Command\Extension\<Ext>\<Name>` in `extension/<ext>/classes/runnable/commands/` |
| Extension cronjob part | `extension/<ext>/cronjobs/<name>.php` | `Exponential\Cronjob\Extension\<Ext>\<Name>` in `extension/<ext>/classes/runnable/cronjobs/` |
| Extension module view | `extension/<ext>/modules/<module>/<view>.php` | `Exponential\View\Extension\<Ext>\<Module>\<View>` in `extension/<ext>/classes/runnable/views/<module>/` |

## What the files look like now

A command (`bin/php/ezcache.php`):

```php
#!/usr/bin/env php
<?php
/** ...the script's header... */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezcache.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezcache::main( __FILE__ );
```

A cronjob part (`cronjobs/workflow.php`) and a module view (`kernel/content/history.php`):

```php
<?php
return \Exponential\Cronjob\Kernel\Workflow::main( __FILE__, get_defined_vars() );
```

```php
<?php
return \Exponential\View\Kernel\Content\History::main( __FILE__, get_defined_vars() );
```

`get_defined_vars()` hands the class the variables the kernel gives a part or a view: `$cli`, `$isQuiet` for a
cronjob part (from `eZRunCronjobs::runScript()`), `$Params`, `$Module` and the view parameters for a view
(from `eZProcess::runFile()`). A view's class returns what the kernel used to take from the file: its
`$Result`, or what it returned (`ModuleView::viewResult()`).

## Writing a new one

### A command

```php
<?php
// extension/myext/classes/runnable/commands/report.php
namespace Exponential\Command\Extension\Myext;

class Report extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => "Writes the monthly report", 'use-session' => false,
                                              'use-modules' => true, 'use-extensions' => true ) );
        $script->startup();
        $options = $script->getOptions( '[month:]', '', array( 'month' => 'The month, YYYY-MM' ) );
        $script->initialize();

        $cli->output( \myReportService::build( $options['month'] ) );   // the work: a class you can call anywhere

        $script->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
// extension/myext/bin/php/report.php
require_once 'autoload.php';
\Exponential\Command\Extension\Myext\Report::main( __FILE__ );
```

Then `php bin/php/ezpgenerateautoloads.php -e` and `php extension/myext/bin/php/report.php --help`.

### A cronjob part

```php
<?php
// extension/myext/classes/runnable/cronjobs/cleanup.php
namespace Exponential\Cronjob\Extension\Myext;

class Cleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $removed = \myCleanupService::removeExpired();
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "Removed $removed expired items" );
    }
}
```

```php
<?php
// extension/myext/cronjobs/cleanup.php
return \Exponential\Cronjob\Extension\Myext\Cleanup::main( __FILE__, get_defined_vars() );
```

Register the part in `cronjob.ini` as before (`[CronjobPart-cleanup] Scripts[]=cleanup.php`).

### A module view

```php
<?php
// extension/myext/classes/runnable/views/report/show.php
namespace Exponential\View\Extension\Myext\Report;

class Show extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Module'];
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', \myReportService::build( $scope['Params']['Month'] ) );
        return array( 'content' => $tpl->fetch( 'design:report/show.tpl' ),
                      'path' => array( array( 'text' => 'Report', 'url' => false ) ) );
    }
}
```

```php
<?php
// extension/myext/modules/report/show.php (named in module.php ViewList as before)
return \Exponential\View\Extension\Myext\Report\Show::main( __FILE__, get_defined_vars() );
```

## Re-implementing one from a site or an extension

Every runnable is created through `Runnable::create()`, which looks up a replacement first:

```ini
# settings/override/site.ini.append.php
[RunnableSettings]
Implementation[Exponential\View\Kernel\Content\History]=myHistoryView
Implementation[Exponential\Command\Kernel\Ezcache]=myCacheCommand
```

The replacement must extend the class it replaces (`is_subclass_of`), so it can override one method and keep
the rest:

```php
class myHistoryView extends \Exponential\View\Kernel\Content\History
{
    public function run( array $scope )
    {
        $result = parent::run( $scope );
        $result['content'] .= '<p class="note">Versions older than a year are archived.</p>';
        return $result;
    }
}
```

No file of the kernel is copied or edited, so the next release updates the original underneath.

## Calling one from your own code

```php
// a view's work from a script, or a command from a cronjob part
$result = \Exponential\View\Kernel\Content\History::create( 'kernel/content/history.php' )
            ->run( array( 'Params' => $params, 'Module' => $module ) );
```

## How the code was moved (stage 1)

The code moved **unchanged** first, so the behaviour is provably the same; four later refinement stages turn
the moved code into smaller, reusable classes (shared option handling, services the views, commands and
cronjob parts share, extension points, clean-up).

- A command's variables were globals, and functions of a script read them with `global $cli`; the moved code
  binds its variables to `$GLOBALS`, so those functions work unchanged. Functions and classes the script
  declared move to the class file, in the global namespace.
- `__FILE__` and `__DIR__` in moved code still mean the original file (`$this->scriptFile()`,
  `$this->scriptDir()`).
- Class names in the moved code are fully qualified (`\eZCLI`), because the class lives in a namespace.
- Not moved, on purpose: `bin/php/ezpgenerateautoloads.php` (it registers the classes),
  `bin/php/exprepair.php` (it runs without the Composer libraries), `bin/php/velocity-router.php` and
  `bin/php/ezasynchronouspublisher.php` (it uses `declare(ticks)`); files the mover could not move faithfully
  (code using `$this`, inline HTML) are listed in the release notes.

Proof, per kind:

| Kind | Proved by |
|---|---|
| Commands | `--help` output and exit code identical before and after, for every script |
| Cronjob parts | `runcronjobs.php` runs the default, frequent and infrequent groups as before |
| Module views | the pages of every module fetched before and after (status, title, errors, content); the edit-mode test of every content class |

## Tests

`tests/tests/kernel/classes/runnable/` covers the base classes and checks every moved entry point: the class it
calls exists, is mapped by the autoload arrays, parses, and extends the right base class.

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/runnable/
```
