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

Work that a view, a command and a cronjob part share lives in a **service** class beside them, in
`kernel/private/classes/services/` (`Exponential\Service\...`) or in an existing kernel class; see
[Services](#services-one-implementation-for-the-view-the-command-and-the-cronjob-part).

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
        $script = $this->script( array( 'description' => "Writes the monthly report",
                                        'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $options = $this->startup( '[month:]', '', array( 'month' => 'The month, YYYY-MM' ) );

        $this->output( \myReportService::build( $options['month'] ) );   // the work: a class you can call anywhere

        $this->shutdown( 0 );
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

## Shared option parsing, help and output

What every command does the same way is in `Exponential\Runnable\Command`, once. The helpers hand their
arguments to `eZScript` and `eZCLI` unchanged, so `--help`, `-q`/`--quiet`, `-s`/`--siteaccess`, `-l`/`--login`,
`-d`/`--debug`, `-v`/`--verbose` and the exit codes are exactly what `eZScript::getOptions()` and
`eZScript::shutdown()` make of them.

| Helper | Does |
|---|---|
| `$this->cli()` | the terminal, `eZCLI::instance()` |
| `$this->script( array $settings )` | creates the script, `eZScript::instance( $settings )` (description, `use-session`, `use-modules`, `use-extensions`, `site-access`, ...); without settings it returns the script |
| `$this->options( $config, $argumentConfig, $optionHelp, ... )` | parses the command line, `eZScript::getOptions()`: shows `--help` and exits, applies the standard options |
| `$this->startup( $config, $argumentConfig, $optionHelp, ... )` | the three steps after `script()`: the script's `startup()`, `options()` and the script's `initialize()` (siteaccess, extensions, session, database) |
| `$this->start( $settings, $config, ... )` | `script( $settings )` and `startup()` in one call |
| `$this->output()`, `error()`, `warning()` | write a line, an error, a warning; `output()` writes nothing with `-q` |
| `$this->isQuiet()` | `-q` was given |
| `$this->log( $message, $logName = null )` | `eZLog::write()` to `var/log/<script name>.log` by default |
| `$this->shutdown( $exitCode, $exitText )` | `eZScript::shutdown()`, which exits |

Before and after, in one of the kernel's commands (`bin/php/trashpurge.php`):

```php
// before
$script = eZScript::instance( array( 'description' => "Empty Exponential trash.", 'use-session' => false,
                                     'use-modules' => false, 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( "[iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]", "", $help );
$script->initialize();

// after
$script = $this->script( array( 'description' => "Empty Exponential trash.", 'use-session' => false,
                                'use-modules' => false, 'use-extensions' => true ) );
$options = $this->startup( "[iteration-sleep:][iteration-limit:][memory-monitoring][trashed-days:]", "", $help );
```

A command that does something between the steps (sets a debug option before `initialize()`, picks the
siteaccess itself) keeps calling `$script->startup()`, `$this->options()` and `$script->initialize()` one by one.
The 61 kernel commands that create a script do it with `$this->script()` and parse their options with
`$this->options()` or `$this->startup()` (40 of them); they and `kickstarter.php` get their terminal with
`$this->cli()`. (The autoload generator, the installers and the Velocity warm-up create no script.) Their
`--help` output and exit code are the same as before, byte for byte.

## Services: one implementation for the view, the command and the cronjob part

Where a view and a command or cronjob part do the same work, the work is in one class and all of them call it.

| Work | Service | Called by |
|---|---|---|
| Emptying the trash | `Exponential\Service\Trash` (`kernel/private/classes/services/trash.php`) | `content/trash`, `bin/php/trashpurge.php`, `cronjobs/trashpurge.php` |
| Removing expired sessions | `Exponential\Service\SessionGarbageCollector` (`kernel/private/classes/services/sessiongarbagecollector.php`) | `setup/session`, `bin/php/ezsessiongc.php`, `cronjobs/session_gc.php` |
| Clearing caches | `expCacheManager` (`kernel/classes/expcachemanager.php`) | `setup/cache`, `bin/php/cache.php` (`exp:cache`) |
| The static cache | `expStaticCacheRunner`, `expCacheManager::regenerateStaticCache()` | `setup/cache`, `setup/staticcachestream`, `bin/php/makestaticcache.php` |
| Preloading | `expPreloadRunner`, `expPreloadJob` | `setup/preload`, `setup/preloadjob`, `bin/php/preload.php`, `bin/php/preloadjob.php` |
| Maintenance mode | `expMaintenance` | `setup/maintenance`, `bin/php/maintenance.php` |
| Running cronjobs from the admin | `expCronjobRunner` | `setup/cronjobs`, `setup/cronjobsstream` |

```php
// the trash
if ( \Exponential\Service\Trash::canEmpty( \eZUser::currentUser() ) )   // content/cleantrash
    \Exponential\Service\Trash::purgeObjects( $objectIDs );                // the selected objects
\Exponential\Service\Trash::emptyTrash();                                 // the Empty button: all of it, in batches
\Exponential\Service\Trash::purgeInBatches( 100, 1, $trashedBefore );     // 100 at a time, a transaction each, 1 s between
\Exponential\Service\Trash::purge( $cli, false, false, $script, 100, 1, 30 );  // the command: the same batches with progress,
                                                                          // only what has been in the trash 30 days

// expired sessions
\Exponential\Service\SessionGarbageCollector::collect();          // and the baskets they leave (view, command, cronjob part)
\Exponential\Service\SessionGarbageCollector::collect( false );   // the sessions only
```

The trash view's Empty button and the command purge the same way: `purgeInBatches()`, each batch of 100 in a
transaction of its own, the content cache cleared and a one second pause between batches (`eZScriptTrashPurge`,
behind the command and the cronjob part, runs its batches through the service too). After the batches the
button also purges any archived object left without a trash entry, so the trash is empty afterwards. Removing
timed out sessions on `setup/session` removes the baskets they leave, as the command and the cronjob part do.
The `bin/php/ezcache.php` command keeps `eZCacheHelper`, which writes the progress output it has always
written.

## Extension points: events, the registry in site.ini, re-implementation

### Events around run()

`main()` of every command, cronjob part and view runs `run()` between two events of the kernel's event hub
(`ezpEvent`). `<kind>` is `command`, `cronjob` or `view`:

| Event | Type | Listener gets | Listener returns |
|---|---|---|---|
| `runnable/<kind>/before` | notify | `$runnable, $class, $scope` | nothing |
| `runnable/<kind>/after` | filter | `$result, $runnable, $class, $scope` | the result to use |

`$class` is the class that runs (the re-implementation, if there is one); `$scope` is what the part or view was
handed (`$Params`, `$Module`, ...; empty for a command). A command that ends with `$this->shutdown()` exits inside
`run()`, so only its before event fires. Without listeners, `main()` returns what `run()` returned.

```php
class myViewTimer
{
    private static $start = array();

    public static function before( $runnable, $class, array $scope )
    {
        self::$start[$class] = microtime( true );
    }

    public static function after( $result, $runnable, $class, array $scope )
    {
        \eZDebug::writeNotice( sprintf( '%s took %.1f ms', $class, 1000 * ( microtime( true ) - self::$start[$class] ) ), __METHOD__ );
        return $result;   // a filter: return the result, or the view loses it
    }
}
```

### The registry in site.ini

```ini
# settings/override/site.ini.append.php, or an extension's settings/site.ini.append.php
[RunnableSettings]
# a subclass to run instead of a runnable class
Implementation[Exponential\View\Kernel\Content\History]=myHistoryView
# listeners of the events above
Listeners[]=runnable/view/before@myViewTimer::before
Listeners[]=runnable/view/after@myViewTimer::after
```

`settings/site.ini` documents both, with nothing set. `[RunnableSettings] Listeners[]` is attached for the
runnables in every context, the command line included, once per process; `[Event] Listeners[]` stays attached
to web requests only, as before, so existing listeners do not start running in scripts. A listener can also be
attached in code: `ezpEvent::getInstance()->attach( 'runnable/view/after', $callback )`.

### Re-implementing one from a site or an extension

Every runnable is created through `Runnable::create()`, which looks up `Implementation[<class>]` first. The
replacement must extend the class it replaces (`is_subclass_of`); an entry naming anything else is ignored. So
the replacement overrides one method and keeps the rest:

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

// Implementation[Exponential\Cronjob\Kernel\Trashpurge]=mySundayTrashpurge: empty the trash on Sundays only
class mySundayTrashpurge extends \Exponential\Cronjob\Kernel\Trashpurge
{
    public function run( array $scope )
    {
        if ( date( 'N' ) == 7 )
            return parent::run( $scope );
    }
}
```

No file of the kernel is copied or edited, so the next release updates the original underneath. This works for
all three kinds. One thing to know about commands: a command is created before its script has read the
settings, so for a command `Implementation[]` and `Listeners[]` are read from `settings/site.ini` and
`settings/override/site.ini.append.php` only (`Runnable::settings()` reads them on its own, without creating the
settings instance the script sets up later). Views and cronjob parts see every setting, siteaccess and extension
settings included.

### Seeing them

`setup/rad` and its survey (`setup/radsurvey`, section "Commands, cronjob parts and views as classes") count the
runnables as extension points: each one per kind and owner, which are re-implemented, and every
`Implementation[]` entry that cannot work (it names no runnable class, the replacement does not exist, or it
does not extend the class). The six events are in the survey's event list. The catalogue on `setup/rad` explains
the mechanism under "Command, cronjob part or view re-implemented".

## Calling one from your own code

```php
// a view's work from a script, or a command from a cronjob part
$result = \Exponential\View\Kernel\Content\History::create( 'kernel/content/history.php' )
            ->run( array( 'Params' => $params, 'Module' => $module ) );
```

`create()` honours `Implementation[]`; `run()` called directly does not fire the events (`main()` does).

## How the code was moved

The code moved **unchanged** first (stage 1), so the behaviour is provably the same; four refinement stages
followed: the shared option handling (stage 2), the services (stage 3), the extension points (stage 4) and the
clean-up (stage 5).

- A command's variables were globals, and functions of a script read them with `global $cli`; the moved code
  binds its variables to `$GLOBALS`, so those functions work unchanged. Functions and classes the script
  declared move to the class file, in the global namespace.
- `__FILE__` and `__DIR__` in moved code still mean the original file (`$this->scriptFile()`,
  `$this->scriptDir()`).
- Class names in the moved code are fully qualified (`\eZCLI`), because the class lives in a namespace.
- Three entry points have a form of their own:
  - `bin/php/ezpgenerateautoloads.php` registers the classes, so it cannot rely on them: it requires the base
    classes and its own class file directly before calling `main()`.
  - `bin/php/ezasynchronouspublisher.php` keeps `declare( ticks=1 )` in the entry point, where the signal
    handlers need it.
  - `bin/php/velocity-router.php`, the router of PHP's built-in web server, runs before any autoloader exists and
    must require the front controller at the top level: it requires its class file
    (`Exponential\Command\Kernel\VelocityRouter`, not a runnable) and calls the static `route()`, which answers
    with true, false or the front controller to require.
- Not moved, on purpose: `bin/php/exprepair.php`, because it runs without the Composer libraries.
- The clean-up removed what the moves left behind: the start of 40 commands became `$this->startup()`, the
  session basket hook that two class files declared as the same global function
  (`eZSessionBasketGarbageCollector`, a redeclaration waiting to happen) is now
  `SessionGarbageCollector::cleanupBaskets()`, and variables no code used any more left the global bindings.

Proof, per kind and after every stage:

| Kind | Proved by |
|---|---|
| Commands | `--help` output and exit code identical before and after, for every kernel command; commands run for real where that changes nothing (`maintenance.php status`, `ezsessiongc.php`, `trashpurge.php --trashed-days=100000`, `ezcache.php --list-tags`, ...) |
| Cronjob parts | `runcronjobs.php` runs the default and frequent groups as before; parts in no group are run through `eZRunCronjobs::runScript()` |
| Module views | the pages of every module fetched before and after (status, title, errors, content); the edit-mode test of every content class |

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/runnable/
```

| Test | Covers |
|---|---|
| `RunnableTest` | the base classes: `main()` of each kind, `create()`, the script file, the view result rule |
| `MovedEntryPointsTest` | every moved entry point: the class it calls exists, is mapped by the autoload arrays, parses and extends the right base class; the router form |
| `CommandHelpersTest` | the shared helpers, and that no kernel command creates `eZScript` or `eZCLI` itself |
| `ServicesTest` | the trash and session services and that their callers use them |
| `RunnableExtensionPointsTest` | the events of each kind, re-implementation of each kind through the settings, the listeners of `[RunnableSettings]`, the private settings read of a command |
| `RadSurveyRunnablesTest` | the runnables in the extension point survey and its counts |
