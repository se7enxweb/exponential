# Specification: commands, cronjob parts and module views as classes

On 2026-10-02 the code of every kernel command, cronjob part and module view moved, unchanged, into classes.
The old files stay as thin entry points with the same paths, so nothing that names a script or a view changes.
The full guide, with before/after examples and how to write one, is
[doc/bc/6.0/cli_cronjob_view_abstractions.md](../../bc/6.0/cli_cronjob_view_abstractions.md).

## Hierarchy

```
Exponential\Runnable\Runnable
 +- Exponential\Runnable\Command        run()                  command line script
 +- Exponential\Runnable\CronjobPart    run( array $scope )    cronjob part
 +- Exponential\Runnable\ModuleView     run( array $scope )    module view
```

| Kind | Entry point (unchanged path) | Class | Class file |
|---|---|---|---|
| Command | `bin/php/<name>.php` | `Exponential\Command\Kernel\<Name>` | `kernel/private/classes/commands/<name>.php` |
| Cronjob part | `cronjobs/<name>.php` | `Exponential\Cronjob\Kernel\<Name>` | `kernel/private/classes/cronjobs/<name>.php` |
| Module view | `kernel/<module>/<view>.php` | `Exponential\View\Kernel\<Module>\<View>` | `kernel/private/classes/views/<module>/<view>.php` |
| Extension command, part, view | the extension's file | `Exponential\<Kind>\Extension\<Ext>\...` | `extension/<ext>/classes/runnable/{commands,cronjobs,views}/` |

An entry point keeps its path, shebang and header, and then makes one call: `<Name>::main( __FILE__ )` for a
command, `return <Name>::main( __FILE__, get_defined_vars() )` for a cronjob part (so the part reads the variables
the runner sets), the view file for a view. `module.php` `ViewList` is untouched. All 198 views of the 22 kernel
modules moved (counts on 2026-10-02; at HEAD `kernel/private/classes/views/` holds 210 files in 24 module directories, `commands/` 70 and `cronjobs/` 22 files, because later work added some: count with `find kernel/private/classes/views -name "*.php" | wc -l`) (a view named after a reserved word gets a `View` suffix, `Section\ListView`; functions a view
declared behind `function_exists()` went to the global namespace so string callbacks keep working). On this
installation the extension point survey counted 469 re-implementation points when this was written (re-count in Setup > RAD at `/setup/rad`) (66 kernel commands, 20 cronjob
parts, 199 views and the extensions' own). The built-in web server's router `bin/php/velocity-router.php` calls
`Exponential\Command\Kernel\VelocityRouter::route()`; the class decides (answered, file sent by the server, or the
front controller to run) and the file acts on the answer.

## Shared helpers

`Command` provides `cli()`, `script( $settings )`, `options( $config, $argumentConfig, $help )`, `startup()` (the
script's startup, option parsing and initialize in one call), `start( $settings, ... )`, `output()`, `error()`,
`warning()`, `isQuiet()`, `log()` and `shutdown()`; they hand arguments to `eZScript` and `eZCLI` unchanged, so
`--help`, `-q`, `--siteaccess` and the other standard options behave as before. Work shared by a view, a command
and a cronjob part lives in services in `kernel/private/classes/services/`: `Exponential\Service\Trash`
(`canEmpty()`, `purgeObjects()`, `emptyArchived()`, `purgeInBatches()`), `SessionGarbageCollector::collect()`,
`DraftsCleanup::cleanup()` (used by `old_drafts_cleanup` and `internal_drafts_cleanup`), `TrashRecord`, `TrashList`.

## Extension points

| Point | Definition |
|---|---|
| Events | `Runnable::main()` runs `run()` between two `ezpEvent` events: `runnable/<kind>/before` (notified with the runnable, the class and its variables) and `runnable/<kind>/after` (filters the result, so a listener can change what a view returns); `<kind>` is `command`, `cronjob` or `view`. Without listeners the result is exactly what `run()` returns |
| Re-implementation | `site.ini [RunnableSettings] Implementation[<runnable class>]=<subclass>`; the subclass must extend the class it replaces, anything else is ignored |
| Listeners | `site.ini [RunnableSettings] Listeners[]=<event>@<callback>` |
| Survey | `setup/rad` and `setup/radsurvey` list every runnable as an extension point |

A command reads only `settings/site.ini` and `settings/override` for `RunnableSettings`.

## Console descriptions

`bin/php/console list` and `help` follow an entry point to the class that holds the code and read an
`@description` tag there; before, 42 commands listed "(no description)". A cronjob part bound to several scripts
shows one line. `runcronjobs.php`, `ezpm.php`, `ezpgenerateautoloads.php` and `ezasynchronouspublisher.php` are
commands too (the autoload generator loads its class itself, since it runs before the autoloader exists).

## Copyright and headers

Every copyright notice names "1998 - 2026 7x & Exponential Foundation" first, above the notice of the original
holder, which stays as the GPL requires. Entry point files carry a header of 7x and the Exponential Foundation
with the description kept; the original header moved into the class file.

## Compatibility

Behaviour is unchanged. Code that `include`s a view file, calls a command by path or names a view in `module.php`
keeps working; code that grepped the old files for their logic must look in `kernel/private/classes/`. Regenerate
the kernel autoloads after adding a class (`php bin/php/ezpgenerateautoloads.php -k` with an `--exclude` for any worktree directory, or
`./console exp:velocity deploy --kernel`). Tests: unit tests for the base classes and every moved entry point.

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [content jobs](../../features/6.0/content-jobs.md), [Exp Debug bar](../../features/6.0/exp-debug-bar.md), [audit event model](audit-event-model.md).

See also: [RAD extension points](../../bc/6.0/rad-extension-points.md), [Cronjobs console](../../features/6.0/cronjobs-console.md), [October 2026 chronicle](../../history/2026/2026-10.md).
