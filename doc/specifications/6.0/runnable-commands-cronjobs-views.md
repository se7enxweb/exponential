# Specification: commands, cronjob parts and module views as classes

This page is the reference for the runnable classes: since 2026-10-02 the code of every kernel command, cronjob
part and module view lives in a class, and the old files remain as thin entry points with the same paths. Read it
if you look for the code behind a script or view, want to replace or listen to one without editing the kernel,
or write your own. Nothing that names a script or a view changes. The full guide, with before and after examples
and how to write one, is [CLI, cronjob and view abstractions](../../bc/6.0/cli_cronjob_view_abstractions.md).

## In short

- `bin/php/<name>.php`, `cronjobs/<name>.php` and `kernel/<module>/<view>.php` keep their paths; the code is in
  `kernel/private/classes/{commands,cronjobs,views}/`.
- Replace a runnable with `site.ini [RunnableSettings] Implementation[<class>]=<subclass>`.
- Listen to one with `site.ini [RunnableSettings] Listeners[]=<event>@<callback>`.
- After adding a class, regenerate the autoloads.

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

### Entry points

An entry point keeps its path, shebang and header, and then makes one call:

| Kind | Call |
|---|---|
| Command | `<Name>::main( __FILE__ )` |
| Cronjob part | `return <Name>::main( __FILE__, get_defined_vars() )`, so the part reads the variables the runner sets |
| Module view | `return <View>::main( __FILE__, get_defined_vars() )`, so the view reads the variables the module runner sets (such as `$Params`) |

`module.php` `ViewList` is untouched.

### Counts and special cases

- All 198 views of the 22 kernel modules moved (counts on 2026-10-02). Later work added more: at HEAD
  `kernel/private/classes/views/` holds 210 files in 24 module directories, `commands/` 70 and `cronjobs/` 22.
  Count them yourself:

  ```bash
  find kernel/private/classes/views -name "*.php" | wc -l
  ```

- A view named after a PHP reserved word gets a `View` suffix, for example `Section\ListView`.
- Functions a view declared behind `function_exists()` went to the global namespace, so string callbacks keep
  working.
- When this was written, the extension point survey counted 469 re-implementation points on this installation
  (66 kernel commands, 20 cronjob parts, 199 views and the extensions' own). Re-count in Setup > RAD (`/setup/rad`).
- The built-in web server's router `bin/php/velocity-router.php` calls
  `Exponential\Command\Kernel\VelocityRouter::route()`. The class decides (answered, file sent by the server, or
  run the front controller) and the file acts on the answer.

## Shared helpers

`Command` provides `cli()`, `script( $settings )`, `options( $config, $argumentConfig, $help )`, `startup()` (the
script's start-up, option parsing and initialisation in one call), `start( $settings, ... )`, `output()`,
`error()`, `warning()`, `isQuiet()`, `log()` and `shutdown()`. They hand arguments to `eZScript` and `eZCLI`
unchanged, so `--help`, `-q`, `--siteaccess` and the other standard options behave as before.

Work shared by a view, a command and a cronjob part lives in services in `kernel/private/classes/services/`:

| Service | Methods |
|---|---|
| `Exponential\Service\Trash` | `canEmpty()`, `purgeObjects()`, `emptyArchived()`, `purgeInBatches()` |
| `SessionGarbageCollector` | `collect()` |
| `DraftsCleanup` | `cleanup()` (used by `old_drafts_cleanup` and `internal_drafts_cleanup`) |
| `TrashRecord`, `TrashList` | trash records and lists |

## Extension points

| Point | Definition |
|---|---|
| Events | `Runnable::main()` runs `run()` between two `ezpEvent` events. `runnable/<kind>/before` is notified with the runnable, the class and its variables. `runnable/<kind>/after` filters the result, so a listener can change what a view returns. `<kind>` is `command`, `cronjob` or `view`. Without listeners the result is exactly what `run()` returns |
| Re-implementation | `site.ini [RunnableSettings] Implementation[<runnable class>]=<subclass>`. The subclass must extend the class it replaces; anything else is ignored |
| Listeners | `site.ini [RunnableSettings] Listeners[]=<event>@<callback>` |
| Survey | `setup/rad` and `setup/radsurvey` list every runnable as an extension point |

A command reads `RunnableSettings` only from `settings/site.ini` and `settings/override`.

### Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `site.ini` | `RunnableSettings` | `Implementation[<runnable class>]` | none | installation (`settings/override` for commands) |
| `site.ini` | `RunnableSettings` | `Listeners[]` | none | installation (`settings/override` for commands) |

## Console descriptions

`bin/php/console list` and `help` follow an entry point to the class that holds the code and read the
`@description` tag there; before, 42 commands listed "(no description)". A cronjob part bound to several scripts
shows one line. `runcronjobs.php`, `ezpm.php`, `ezpgenerateautoloads.php` and `ezasynchronouspublisher.php` are
commands too; the autoload generator loads its class itself, since it runs before the autoloader exists.

## Copyright and headers

Every copyright notice names "1998 - 2026 7x & Exponential Foundation" first, above the notice of the original
holder, which stays as the GPL requires. Entry point files carry a header of 7x and the Exponential Foundation
with the description kept; the original header moved into the class file.

## Compatibility

Behaviour is unchanged. Code that `include`s a view file, calls a command by path or names a view in
`module.php` keeps working. Code that searched the old files for their logic must look in
`kernel/private/classes/` now.

After adding a class, regenerate the kernel autoloads, excluding any worktree directory:

```bash
php bin/php/ezpgenerateautoloads.php -k --exclude='<worktree directory pattern>'
```

or use `./console exp:velocity deploy --kernel`, which does the same as part of a deploy.

Tests: unit tests for the base classes and every moved entry point.

## Related pages

- [CLI, cronjob and view abstractions](../../bc/6.0/cli_cronjob_view_abstractions.md), [RAD extension points](../../bc/6.0/rad-extension-points.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [Cronjobs console](../../features/6.0/cronjobs-console.md), [content jobs](../../features/6.0/content-jobs.md), [Exp Debug bar](../../features/6.0/exp-debug-bar.md)
- [Audit event model](audit-event-model.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [October 2026 chronicle](../../history/2026/2026-10.md)
