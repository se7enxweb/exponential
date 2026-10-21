---
title: Write a cronjob part
audience: php-developer, developer
category: php-development
order: 240
summary: A task guide for scheduled work in an extension - the cronjob class extending Exponential\Runnable\CronjobPart, the entry file in cronjobs/, the [CronjobPart-name] block in cronjob.ini, running it with runcronjobs.php or bin/php/console cron:<part>, quiet output, and rules for safe, repeatable runs.
keywords: cronjob part, cronjob, CronjobPart, Exponential\Cronjob\Extension, runcronjobs.php, cronjob.ini, CronjobPart block, Scripts, cron:frequent, crontab:list, isQuiet, scheduled work, description tag, batches, locks
type: help
---
# Write a cronjob part

This task guide is for PHP developers who need work to run on a schedule: clean up expired records, send a digest,
import a feed. A cronjob part is a named group of scripts in `cronjob.ini`; the system crontab runs
`runcronjobs.php <part>`, which runs each script of the part. The application view of the same topic is
[Cronjobs for applications](../creators/app-creators/cronjobs-for-applications.md).

## Contents

1. [Step 1: the cronjob class](#step-1-the-cronjob-class)
2. [Step 2: the entry file](#step-2-the-entry-file)
3. [Step 3: the part in cronjob.ini](#step-3-the-part-in-cronjobini)
4. [Step 4: run it](#step-4-run-it)
5. [Step 5: schedule it](#step-5-schedule-it)
6. [What the script receives](#what-the-script-receives)
7. [Rules for scheduled code](#rules-for-scheduled-code)

## Step 1: the cronjob class

`extension/acmereport/classes/runnable/cronjobs/cleanup.php`:

```php
<?php
namespace Exponential\Cronjob\Extension\Acmereport;

class Cleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $removed = \acmeReportService::removeExpired( 500 );   // at most 500 per run
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "acmereport: removed $removed expired items" );
    }
}
```

## Step 2: the entry file

`extension/acmereport/cronjobs/cleanup.php`:

```php
<?php
/**
 * @description Remove expired acmereport items
 */
return \Exponential\Cronjob\Extension\Acmereport\Cleanup::main( __FILE__, get_defined_vars() );
```

`list cron` in the console describes each part with the `@description` tags of its scripts.

## Step 3: the part in cronjob.ini

`extension/acmereport/settings/cronjob.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[CronjobSettings]
ExtensionDirectories[]=acmereport

[CronjobPart-acmereport]
Scripts[]=cleanup.php
*/ ?>
```

`ExtensionDirectories[]` makes the runner look in `extension/acmereport/cronjobs/`. You can also add your script to an
existing part (`[CronjobPart-frequent] Scripts[]=cleanup.php`) instead of a part of your own; a part of your own can be
scheduled separately and paused separately.

## Step 4: run it

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
php runcronjobs.php acmereport                # the classic runner
php bin/php/console cron:acmereport           # the same through the console
php bin/php/console list cron                 # every part and its scripts
```

Add `-s <siteaccess>` for a siteaccess's settings, and `--allow-root-user` only if you must run as root.

## Step 5: schedule it

Add the part to the crontab of the site's user, next to the stock parts (`frequent`, `infrequent`, `contentjobs` ...):

```text
*/15 * * * * cd /path/to/exponential && php runcronjobs.php -q acmereport
```

`php bin/php/console crontab:list` prints the crontab, `crontab:edit` opens it. **Setup > Cronjobs** shows the parts and
when they last ran ([Cronjobs console](../../features/6.0/cronjobs-console.md),
[Cronjob part filter](../../features/6.0/cronjob-part-filter.md), [Pausing cronjobs](../../features/6.0/cronjob-pause.md)).

## What the script receives

`get_defined_vars()` passes what `eZRunCronjobs::runScript()` defines:

| In `$scope` | Holds |
|---|---|
| `cli` | `eZCLI`, the terminal |
| `isQuiet` | whether `-q` was given |

The script runs inside `runcronjobs.php`'s process, after the siteaccess, the extensions and the database are set up.
All scripts of a run share that process: a fatal error in one stops the rest, and static state of one is seen by the
next.

## Rules for scheduled code

- **Bounded work per run.** Process at most a fixed number of items, and let the next run continue. A run that takes
  longer than its interval overlaps the next one.
- **Repeatable.** A run that stopped halfway must be safe to start again: mark items done as you go, in short
  transactions.
- **Quiet unless asked.** With `-q`, print nothing on success; cron mails any output to the user.
- **Logs, not output, for what changed.** `eZLog::write( ..., 'acmereport.log' )`.
- **The work in a service.** The cronjob part, a console command and an admin button can all call
  `acmeReportService::removeExpired()` ([Module views as classes](module-views-as-classes.md#services-one-implementation-for-three-entry-points)).
- **Heavy, per-item work** belongs in a queue or a content job rather than a long cronjob
  ([Background work: queues, cronjobs and content jobs](../creators/app-creators/background-work-patterns.md)).

## Related pages

- [Writing a cronjob script (application creators)](../creators/app-creators/writing-a-cronjob-part.md)
- [Cronjob parts (extension developers)](../extension-development/part-3-extension-developer/cronjob-parts.md)
- [Cronjobs guide](../../guides/cronjobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
