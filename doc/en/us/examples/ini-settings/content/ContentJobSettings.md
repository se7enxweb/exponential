---
title: content.ini [ContentJobSettings]
audience: developer, admin
category: examples
summary: "The settings of [ContentJobSettings] in content.ini: SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays ..."
keywords: content.ini, ContentJobSettings, INI, setting, SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays, PhpBinary, JobTypes
type: generated
generated: exp:help:examples
---
# content.ini [ContentJobSettings]

The block `[ContentJobSettings]` of `settings/content.ini`, with 10 setting(s).

Never edit `settings/content.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/content.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in an extension (read when the extension is active).

## SynchronousLimit

> Content jobs: large subtree removes (content/removeobject, content/removenode) and subtree copies  
> (content/copysubtree) run in batches in a background worker instead of in the web request, so they  
> never fail from a request time limit, a memory limit or one huge transaction. Each batch is a  
> transaction of its own, followed by a checkpoint: a job resumes where it stopped after any  
> interruption (kill -9 included). Classes in kernel/classes/contentjob/; the command  
> bin/php/expcontentjob.php (./console exp:expcontentjob list|show|run|resume|cancel|remove|copy).  
> Jobs live in var/<site var dir>/jobs/content/ (a cluster needs a shared var directory for them).  
>
> The user chooses per operation whether it runs now (in the request) or as a background job; this  
> sets the choice offered first: below this number of nodes "now", from it on "job". 0 = "job" always.  

Default: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
SynchronousLimit=50
*/ ?>
```

## NowLimit

> "Now" is refused above this number of nodes, with a message to run it as a job. 0 = no ceiling.  

Default: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
NowLimit=1000
*/ ?>
```

## BatchSize

> Nodes per batch (one transaction each).  

Default: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
BatchSize=50
*/ ?>
```

## BatchPause

> Pause between batches in milliseconds (0 = none), to leave room for other writers.  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
BatchPause=0
*/ ?>
```

## QueuedGrace

> A queued job whose worker has not started within this many seconds is started by the cronjob  
> part 'contentjobs' (frequent group); a running job whose worker died is started at once.  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
QueuedGrace=60
*/ ?>
```

## QueuedTimeout

> A queued job that no worker started within this many seconds (no cronjob runs, the start was lost) fails  
> and gives back its locks if it has done nothing yet, so it never blocks a subtree; resume it to try again.  
> A worker that cannot be started at all fails the job at once, with the reason. 0 = never expire.  

Default: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
QueuedTimeout=600
*/ ?>
```

## MaxAttempts

> A job whose worker is started this many times without finishing one batch is failed (with the  
> last error) instead of being retried for ever. Resume it once the cause is fixed.  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
MaxAttempts=5
*/ ?>
```

## KeepDays

> Finished (done or cancelled) jobs are removed by the cronjob part after this many days. 0 = never.  

Default: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
KeepDays=30
*/ ?>
```

## PhpBinary

> The PHP command line binary for the worker; empty = the PHP running the request (PHP\_BINDIR/php).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
PhpBinary=
*/ ?>
```

## JobTypes

> The job types: JobTypes\[<name>\]=<class implementing expContentJobType>.  
> A package install in the background (Setup > Packages > Install, "Install in the background"), doc/bc/6.0/package-json-format.md  
> An export of the audit console in the background (Audit > Export, "Export in the background"), doc/bc/6.0/audit.md  

Default: a hash of 11: `remove=expContentJobRemoveSubtree`, `copy=expContentJobCopySubtree`, `move=expContentJobMoveSubtree`, `hide=expContentJobHideSubtree`, `reveal=expContentJobRevealSubtree`, `section=expContentJobSectionSubtree`, `state=expContentJobStateSubtree`, `addlocation=expContentJobAddLocation`, `removelocation=expContentJobRemoveLocation`, `packageinstall=expContentJobPackageInstall`, `auditexport=expContentJobAuditExport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
# an empty "JobTypes[]" first replaces the shipped list; without it the items are added
JobTypes[]
JobTypes[remove]=expContentJobRemoveSubtree
JobTypes[copy]=expContentJobCopySubtree
JobTypes[move]=expContentJobMoveSubtree
JobTypes[hide]=expContentJobHideSubtree
JobTypes[reveal]=expContentJobRevealSubtree
JobTypes[section]=expContentJobSectionSubtree
JobTypes[state]=expContentJobStateSubtree
JobTypes[addlocation]=expContentJobAddLocation
JobTypes[removelocation]=expContentJobRemoveLocation
JobTypes[packageinstall]=expContentJobPackageInstall
JobTypes[auditexport]=expContentJobAuditExport
*/ ?>
```

## Related

- [All blocks of content.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/content.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
