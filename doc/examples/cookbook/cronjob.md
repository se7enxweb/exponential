---
title: A cronjob
audience: developer, admin
category: examples
order: 80
summary: Write a cronjob script in an extension, give it a cronjob part of its own in cronjob.ini, run it with runcronjobs.php and the console, and schedule it in the crontab; with the class form of a cronjob part.
keywords: cronjob, cronjob part, runcronjobs.php, cronjob.ini, CronjobSettings, ExtensionDirectories, CronjobPart, Scripts, crontab, cron:, console, CronjobPart class, isQuiet, cli, MaxScriptExecutionTime
type: example
---
# A cronjob

A cronjob script is a PHP file `runcronjobs.php` includes with the kernel loaded for one siteaccess; a **part**
groups scripts that run together on one schedule. You write `expexample_report.php`, which counts the objects
published in the last 24 hours and writes the number to `var/log/expexample.log`, give it the part `expexample`, run
it by hand and schedule it.

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on; a shell in the
installation root; the name of a siteaccess of the site (here `site`).

## 1. Register the directory and the part

`extension/expexample/settings/cronjob.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
# Look for cronjobs/<script>.php in this extension
ExtensionDirectories[]=expexample

# A part of its own: php runcronjobs.php -s <siteaccess> expexample
[CronjobPart-expexample]
Scripts[]=expexample_report.php

*/ ?>
```

`runcronjobs.php` adds `cronjobs/` of every extension in `[CronjobSettings] ExtensionDirectories[]` to the directories
it searches. A script listed in `[CronjobSettings] Scripts[]` instead runs in the default part, the one
`runcronjobs.php` runs without a part name; a part of its own keeps your schedule apart from the kernel's.

## 2. The script

`extension/expexample/cronjobs/expexample_report.php`:

```php
<?php
/**
 * @description Example: count the objects published in the last 24 hours and write the number to the log
 *
 * A cronjob script of the expexample extension, run by runcronjobs.php inside the part
 * [CronjobPart-expexample] (cronjob.ini.append.php). runcronjobs.php has set up the siteaccess, the database and
 * the variables $cli (eZCLI) and $isQuiet before it includes this file.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$since = time() - 86400;
$db = eZDB::instance();
$rows = $db->arrayQuery( 'SELECT COUNT(*) AS count FROM ezcontentobject WHERE status = ' . (int)eZContentObject::STATUS_PUBLISHED .
                         ' AND published >= ' . (int)$since );
$count = $rows ? (int)$rows[0]['count'] : 0;

eZLog::write( "objects published in the last 24 hours: $count", 'expexample.log' );

if ( !$isQuiet )
    $cli->output( "expexample_report: $count objects published in the last 24 hours" );
```

- The `@description` tag is what `./console list cron` and **Setup > Cronjobs** show for the script.
- Every value put into SQL is cast to an integer first; strings go through `$db->escapeString()`.
- Each script runs under a lock of its own: a second run while the first holds it is skipped. A run that takes longer
  than `[CronjobSettings] MaxScriptExecutionTime` (12 hours) loses the lock to the next one; set
  `MaxScriptExecutionTime` in `[CronjobPart-expexample]` for a part that needs more or less.

### The same as a class

From 6.0.15 the kernel's own parts are classes; the plain script above keeps working. The class form, which can be
tested and extended through events:

```php
<?php
// extension/expexample/classes/runnable/cronjobs/expexamplereport.php
namespace Exponential\Cronjob\Extension\Expexample;

class Expexamplereport extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $count = \expExampleReport::publishedSince( time() - 86400 );
        if ( empty( $scope['isQuiet'] ) )
            $scope['cli']->output( "expexample_report: $count objects published in the last 24 hours" );
    }
}
```

```php
<?php
// extension/expexample/cronjobs/expexample_report.php
return \Exponential\Cronjob\Extension\Expexample\Expexamplereport::main( __FILE__, get_defined_vars() );
```

`expExampleReport::publishedSince()` stands for a class of your own that holds the query, so a view or a command can
call it too. Regenerate the autoloads after adding the classes.

## 3. Run it by hand

```bash
php -l extension/expexample/cronjobs/expexample_report.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php runcronjobs.php --list --allow-root-user                                   # CronjobPart-expexample is listed
php runcronjobs.php -s site expexample --allow-root-user                       # the part
php runcronjobs.php -s site --script=expexample_report.php --allow-root-user   # the one script
./console cron:expexample -s site --allow-root-user                            # the same through the console
```

Expected: `expexample_report: <n> objects published in the last 24 hours`, and the same number in
`var/log/expexample.log`. Better than `--allow-root-user`: run as the site's user, so the files a job writes stay
writable for the web server.

**Setup > Cronjobs** shows the part as a card with its script and description, and runs it from the browser.

## 4. Schedule it

One crontab line per part, for the user the site runs as:

```cron
30 2 * * * cd /var/www/example.com && php runcronjobs.php -q -s site expexample >/dev/null 2>&1
```

**Setup > Cronjobs** proposes the lines that are missing under *Suggested entries*, with this installation's root and
PHP binary; the schedule it proposes for a part comes from `cronjob.ini [AdminSettings] CrontabSchedule_<part>`:

```ini
# settings/override/cronjob.ini.append.php
[AdminSettings]
CrontabSchedule_expexample=30 2 * * *
```

Checked for this page (2026-10-18): `php -l` on the script; the options of `runcronjobs.php` are those of
`kernel/private/classes/commands/runcronjobs.php`; the extension directory lookup is its
`eZExtension::expandedPathList( $extensionDirectories, 'cronjobs' )`. The class form is the one of
`doc/bc/6.0/cli_cronjob_view_abstractions.md`; it was not run for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| `--list` does not show the part | the extension is inactive, the INI cache, or the block name lacks `CronjobPart-` |
| *Cronjob part locked by other process* | a previous run still holds the lock; it is taken over after `MaxScriptExecutionTime` |
| Files written by the job cannot be changed by the site | the job ran as root; run it as the site's user |
| Works by hand, not from cron | cron's `PATH` and working directory: `cd` to the root and name the PHP binary in full |

## Derived from

- `kernel/private/classes/commands/runcronjobs.php`, `kernel/private/classes/runnable/cronjobpart.php`
- `cronjobs/unlock.php` and `kernel/private/classes/cronjobs/unlock.php` (a kernel part in both forms)
- `settings/cronjob.ini`; `doc/guides/cronjobs.md`, sections 1, 12 and 13

## Related pages

- [Cookbook](README.md)
- [Cronjobs: running, scheduling and following them](../../guides/cronjobs.md)
- [Cronjob parts](../cronjobs/README.md): every part and script of the kernel
- [Commands](../commands/README.md): `runcronjobs.php` and the console
- [Leaving out a cronjob part](../../features/6.0/cronjob-part-filter.md),
  [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
