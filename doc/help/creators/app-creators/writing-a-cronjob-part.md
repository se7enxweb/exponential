---
title: Writing a cronjob script
audience: app-creator, developer
category: app-creation
order: 290
summary: Write the script a cronjob part runs - the class-based form with CronjobPart, the variables it receives, output and quiet mode, a description for the console, batches, idempotence, users and errors.
keywords: cronjob script, CronjobPart, runcronjobs, cli, isQuiet, description tag, batch, idempotent, cronjob part, Exponential Runnable, app creator
type: help
---
# Writing a cronjob script

This page is for application creators who write the code a cronjob part runs. New scripts are classes extending
`Exponential\Runnable\CronjobPart`, with a one-line entry file; the plain-file form of older scripts still works
([A cronjob part](../../../bc/6.0/cli_cronjob_view_abstractions.md#a-cronjob-part)).

## Contents

1. [The class and the entry file](#the-class-and-the-entry-file)
2. [What the script receives](#what-the-script-receives)
3. [Output](#output)
4. [Doing the work well](#doing-the-work-well)
5. [Testing](#testing)

## The class and the entry file

```php
<?php
// extension/expdemo/classes/runnable/cronjobs/expdemoimport.php
namespace Exponential\Cronjob\Extension\Expdemo;

class ExpdemoImport extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $result = \expDemoImportService::importNewItems( 100 );   // the work lives in a service class
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "Imported {$result['created']} items, updated {$result['updated']}" );
    }
}
```

```php
<?php
/**
 * @description Import new items from the partner feed
 */
// extension/expdemo/cronjobs/expdemo_import.php
return \Exponential\Cronjob\Extension\Expdemo\ExpdemoImport::main( __FILE__, get_defined_vars() );
```

Regenerate the autoloads, add the script to a part ([Cronjobs for applications](cronjobs-for-applications.md)) and run
it with `php runcronjobs.php -s <siteaccess> --script=expdemo_import.php`.

## What the script receives

`get_defined_vars()` passes what `eZRunCronjobs::runScript()` defines: `$cli` (an `eZCLI` for output) and `$isQuiet`.
The kernel is initialized for the siteaccess given with `-s`; the current user is anonymous.

## Output

- One line per meaningful result; no output in quiet mode except errors.
- `$cli->error( ... )` for errors, so they stand out in logs.
- The `@description` tag is what `php bin/php/console list cron` and **Setup > Cronjobs** show
  ([Describe your own script](../../../bc/6.0/console.md#describe-your-own-script)).

## Doing the work well

| Rule | Why |
|---|---|
| Work in batches with a limit per run | a run must end before the next one starts |
| Make it idempotent (remote IDs, "processed" markers) | a crashed run is simply repeated |
| Log in a dedicated user when you write content | permissions and the audit name a real account, not the administrator |
| Catch errors per item, continue, report at the end | one bad record should not stop the rest |
| Clear in-memory caches between batches | long runs otherwise run out of memory |
| Hand slow single tasks to a queue | the part stays short (Queues with expqueue) |

## Testing

Keep the logic in a service class and test it with PHPUnit; run the script by hand with `--script` on a development
copy; check the output in **Setup > Cronjobs** (Testing applications).

## Related pages

- [Cronjobs for applications](cronjobs-for-applications.md)
- [CLI scripts with eZScript](cli-scripts.md)
- Cronjob cookbook
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
