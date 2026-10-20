---
title: "Lesson 9.2: A command of your own"
audience: trainer, developer
category: training
order: 92
summary: Learners write a command line script in their extension with eZScript, options and help, describe it with @description, run it through the console as ext:mysite_hello:latest, and return exit codes that other scripts can rely on. 50 minutes.
keywords: developers course, command line script, eZScript, eZCLI, getOptions, startup, initialize, shutdown, exit code, @description, ext: namespace, extension bin/php, console, siteaccess option
type: help
---
# Lesson 9.2: A command of your own

Imports, reports, clean-ups and checks belong on the command line, where they can run from cron and in
deployments. In this lesson learners write a command in their extension, with options, help and exit codes, and run
it through the console without any registration: a file in `extension/<ext>/bin/php/` is found automatically.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 9: The console and persistent objects](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 9.1](the-console.md) |

## Learning objectives

After this lesson, learners can:

- write a script with `eZScript::instance()`, `startup()`, `getOptions()`, `initialize()` and `shutdown()`;
- define options with values and a help text;
- describe the script with `@description` so `console list` shows it;
- run it as `ext:<ext>:<name>` and with `-s <siteaccess>`;
- return 0 on success and a non-zero exit code on failure.

## Trainer's notes

### How to present it

Sources: [Exponential Console, extension commands](../../../../bc/6.0/console.md#extension-commands) and
[Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md).

1. **The skeleton** (5 minutes). Walk through the script of the exercise: `require 'autoload.php'` (the script runs
   from the installation root), `eZScript::instance()` with a description, `startup()`, `getOptions()` with the
   option string `[class:][limit:]` (a colon means "takes a value"), `initialize()`, the work, `shutdown( <code> )`.
2. **Standard options** (2 minutes). Every eZScript has `--help`, `-q`, `-s <siteaccess>`, `-d` (debug output) and
   the root check, for free.
3. **Description** (2 minutes). `@description` in the opening docblock is what `console list` shows.
4. **Exit codes** (2 minutes). `shutdown( 0 )` on success, `shutdown( 1 )` (or another non-zero code) on failure, so
   cron and deployment scripts can react.
5. **The class form** (4 minutes). The kernel's own commands are classes extending `Exponential\Runnable\Command`
   with a one-line entry file; extensions may do the same (`extension/<ext>/classes/runnable/commands/`). Start with
   the plain script; move to the class form when the command grows.

### Common questions

**Do I need to register the command?** No. The console scans `extension/*/bin/php/*.php`.

**Can the command use a siteaccess's settings?** Yes, with `-s <siteaccess>`; without it the default siteaccess is
used.

**How do I write a long-running job?** In batches, with progress output and a `--dry-run`; for content changes the
content jobs (`exp:expcontentjob`) show the pattern.

### Pitfalls

- **Output with `echo`.** Use `$cli->output()`, `$cli->warning()` and `$cli->error()`: they respect `-q` and colors.
- **Forgetting `initialize()`.** Without it the database and settings are not ready.
- **Personal data on the terminal.** Do not print e-mail addresses or names of real users in reports that end up
  in logs.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Create `bin/php/latest.php`:

   ```php
   #!/usr/bin/env php
   <?php
   /**
    * Lists the newest nodes of a class: php bin/php/console ext:mysite_hello:latest --class=article --limit=5
    *
    * @description List the newest nodes of a content class
    */
   require 'autoload.php';

   $cli = eZCLI::instance();
   $script = eZScript::instance( array( 'description' => 'List the newest nodes of a content class',
                                        'use-session' => false,
                                        'use-modules' => true,
                                        'use-extensions' => true ) );
   $script->startup();
   $options = $script->getOptions( '[class:][limit:]', '',
                                   array( 'class' => 'Class identifier (default: article)',
                                          'limit' => 'How many nodes (default: 5)' ) );
   $script->initialize();

   $classIdentifier = $options['class'] ? $options['class'] : 'article';
   $limit = $options['limit'] ? max( 1, (int)$options['limit'] ) : 5;

   $nodes = eZContentObjectTreeNode::subTreeByNodeID(
       array( 'ClassFilterType'  => 'include',
              'ClassFilterArray' => array( $classIdentifier ),
              'SortBy'           => array( 'published', false ),
              'Limit'            => $limit ),
       2 );

   if ( !$nodes )
   {
       $cli->warning( 'No nodes of the class ' . $classIdentifier );
       $script->shutdown( 1 );
   }
   foreach ( $nodes as $node )
   {
       $cli->output( $node->attribute( 'node_id' ) . "\t" . $node->attribute( 'name' ) );
   }
   $script->shutdown( 0 );
   ```

2. Make it executable and check it: `chmod +x bin/php/latest.php` and `php -l bin/php/latest.php`.
3. From the installation root, find it: `php bin/php/console list ext:mysite_hello`.
4. Read its help: `php bin/php/console ext:mysite_hello:latest --help`.
5. Run it: `php bin/php/console ext:mysite_hello:latest --class=folder --limit=3`.
6. Run it for a class with no objects and print the exit code:
   `php bin/php/console ext:mysite_hello:latest --class=nothing; echo "exit $?"`.
7. Run it quietly: add `-q` and compare the output.
8. Commit the script.

## Expected result

- Step 3 lists `ext:mysite_hello:latest` with "List the newest nodes of a content class".
- Step 4 prints the usage with the options `--class` and `--limit` and the general options.
- Step 5 prints up to three lines of node ID and name.
- Step 6 prints the warning and `exit 1`.
- The trainer checks one learner's `list ext:mysite_hello`.

## Quiz

1. Where must a script be placed so the console finds it as `ext:mysite_hello:latest`?
2. What does the colon in the option string `[class:]` mean?
   A. The option is required B. The option takes a value C. The option is hidden D. The option repeats
3. Which method must run before the script uses the database?
4. Which tag gives the script its line in `console list`?
5. Why return a non-zero exit code on failure?

<details>
<summary>Answers</summary>

1. `extension/mysite_hello/bin/php/latest.php`.
2. **B.** The option takes a value, as in `--class=article`.
3. `$script->initialize()`.
4. `@description` in the opening docblock.
5. So cron jobs, deployments and other scripts can see that it failed.

</details>

## Further reading

- [Exponential Console](../../../../bc/6.0/console.md).
- [Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md).
- [Content jobs](../../../../features/6.0/content-jobs.md): long operations in batches.

## Related pages

- [Module 9: The console and persistent objects](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
