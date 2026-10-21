---
title: Cronjobs for integrations
audience: integrator, developer, admin
category: integration
order: 160
summary: Scheduling integration work with Exponential's cronjob parts - a part of your own, the script as a class, the crontab line per siteaccess, locking against overlapping runs, pausing a part during maintenance, and following runs on Setup > Cronjobs.
keywords: cronjob, cronjob part, runcronjobs.php, cronjob.ini, CronjobPart, ExtensionDirectories, crontab, schedule, lock, overlap, pause, Setup > Cronjobs, cron:, scheduled import, scheduled export
type: help
---
# Cronjobs for integrations

This page is for integration developers and operators who schedule imports, exports and queue processing. Exponential
runs scheduled work through **cronjob parts**: named groups of scripts that the system crontab starts with
`runcronjobs.php`. Give your integration a part of its own, so it can be scheduled, paused and debugged without
touching anything else. The operator's view is [Cronjobs](../../guides/cronjobs.md); the developer's chapter is
[Cronjob parts](../extension-development/part-3-extension-developer/cronjob-parts.md).

## Contents

1. [A part of your own](#a-part-of-your-own)
2. [The script](#the-script)
3. [The crontab line](#the-crontab-line)
4. [Overlapping runs and time limits](#overlapping-runs-and-time-limits)
5. [Pausing during maintenance](#pausing-during-maintenance)
6. [Following the runs](#following-the-runs)
7. [The kernel's parts an integration depends on](#the-kernels-parts-an-integration-depends-on)

## A part of your own

`extension/myintegration/settings/cronjob.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
ExtensionDirectories[]=myintegration

[CronjobPart-myintegration_import]
Scripts[]=importproducts.php

[CronjobPart-myintegration_outbox]
Scripts[]=sendoutbox.php

*/ ?>
```

`ExtensionDirectories[]` makes `runcronjobs.php` look in `extension/myintegration/cronjobs/`. Two parts let operators
run the nightly import and the frequent outbox at different rhythms.

## The script

The recommended form is a class with a one-line file in `cronjobs/`:

```php
<?php
// extension/myintegration/classes/runnable/cronjobs/importproducts.php
namespace Exponential\Cronjob\Extension\Myintegration;

class ImportProducts extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $file = \eZINI::instance( 'myintegration.ini' )->variable( 'ImportSettings', 'File' );
        $importer = new \myIntegrationProductImport( $cli, false );
        $ok = $importer->run( $file );
        \eZLog::write( $ok ? 'Import finished' : 'Import failed', 'myintegration.log' );
        if ( empty( $scope['isQuiet'] ) )
        {
            $cli->output( $ok ? 'Import finished' : 'Import failed' );
        }
    }
}
```

```php
<?php
// extension/myintegration/cronjobs/importproducts.php
return \Exponential\Cronjob\Extension\Myintegration\ImportProducts::main( __FILE__, get_defined_vars() );
```

`myintegration.ini` is your extension's own settings file; `File` is an example setting you define there.

## The crontab line

Run as the web server's user, from the installation root, per siteaccess:

```cron
# nightly product import at 02:15
15 2 * * *  cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> myintegration_import >> var/log/cronjob-myintegration.log 2>&1
# outbox every 5 minutes
*/5 * * * * cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> myintegration_outbox >> var/log/cronjob-myintegration.log 2>&1
```

Test by hand first:

```bash
php runcronjobs.php --list | grep -A2 myintegration
php runcronjobs.php -s <siteaccess> --script=importproducts.php
php bin/php/console cron:myintegration_import -s <siteaccess>
```

Every part is also a console command `cron:<part>`. **Setup > Cronjobs** shows the exact command per part and proposes
crontab lines.

## Overlapping runs and time limits

A slow run can overlap the next one. Protect each part:

| Measure | How |
|---|---|
| A lock | Take a lock file under `var/` at the start (for example with PHP's `flock()` on a file of your extension) and stop when it is held |
| A time budget | Stop after a number of minutes and continue from a saved position next time |
| Batches | Work in batches of a few hundred records and clear the in-memory object cache between them (`eZContentObject::clearCache()`) |
| A global limit | `cronjob.ini [CronjobSettings] MaxScriptExecutionTime` (43200 seconds by default) bounds every script |

## Pausing during maintenance

**Setup > Cronjobs** can pause one part (**Pause** on its card) or every part, with a note; `runcronjobs.php` then
skips them until someone resumes ([Pausing cronjobs](../../features/6.0/cronjob-pause.md)). Pause your integration's
parts before a migration, an upgrade or a restore, and resume them after the check. `cronjob.ini [PauseSettings]
Pause=disabled` switches the check off.

## Following the runs

- **Setup > Cronjobs** shows each part's last runs, how issues are counted and the output while a job runs.
- With `--logfiles`, `runcronjobs.php` writes log files; `cronjob.ini [AdminSettings] LogFile` and `ErrorFile` name
  the files the page keeps.
- The audit taxonomy has the event `system.cronjob.run` (recorded by default); the kernel's notification part emits
  it. Your own part can record its runs the same way through the audit API ([Audit, developer API](../../bc/6.0/audit.md#35-the-developer-api)).
- Write your own summary line per run with `eZLog::write()` and alert on its absence
  ([Monitoring integrations](monitoring-integrations.md)).

## The kernel's parts an integration depends on

| Part | Scripts | Why it matters |
|---|---|---|
| global (no name) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | RSS imports and delayed search indexing run here |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | Waiting workflow processes, notifications, content jobs and audit sinks (the webhook) are processed here |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | Link checks of imported URLs |

If an integration uses workflows that defer to the cronjob, or the audit webhook, the `frequent` part must run
(every 5 minutes is the usual schedule).

## Related pages

- [Cronjobs](../../guides/cronjobs.md)
- [Cronjob parts](../extension-development/part-3-extension-developer/cronjob-parts.md)
- [Writing a cronjob part](../creators/app-creators/writing-a-cronjob-part.md)
- [Cron jobs for hosts](../hosting/operations/cron-jobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
