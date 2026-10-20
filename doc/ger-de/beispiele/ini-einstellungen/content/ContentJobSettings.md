---
title: content.ini [ContentJobSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentJobSettings] in content.ini: SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays ..."
keywords: Einstellung, content.ini, ContentJobSettings, INI, setting, SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays, PhpBinary, JobTypes
type: generated
generated: exp:help:examples
---
# content.ini [ContentJobSettings]

Der Block `[ContentJobSettings]` von `settings/content.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

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

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
SynchronousLimit=50
*/ ?>
```

## NowLimit

> "Now" is refused above this number of nodes, with a message to run it as a job. 0 = no ceiling.  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
NowLimit=1000
*/ ?>
```

## BatchSize

> Nodes per batch (one transaction each).  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
BatchSize=50
*/ ?>
```

## BatchPause

> Pause between batches in milliseconds (0 = none), to leave room for other writers.  

Standard: `0`.

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

Standard: `60`.

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

Standard: `600`.

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

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
MaxAttempts=5
*/ ?>
```

## KeepDays

> Finished (done or cancelled) jobs are removed by the cronjob part after this many days. 0 = never.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
KeepDays=30
*/ ?>
```

## PhpBinary

> The PHP command line binary for the worker; empty = the PHP running the request (PHP\_BINDIR/php).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
PhpBinary=
*/ ?>
```

## JobTypes

> The job types: JobTypes\[<name>\]=<class implementing expContentJobType>.  
> A package install in the background (Setup > Packages > Install, "Install in the background"), doc/eng-us/bc/6.0/package-json-format.md  
> An export of the audit console in the background (Audit > Export, "Export in the background"), doc/eng-us/bc/6.0/audit.md  

Standard: ein Hash mit 11: `remove=expContentJobRemoveSubtree`, `copy=expContentJobCopySubtree`, `move=expContentJobMoveSubtree`, `hide=expContentJobHideSubtree`, `reveal=expContentJobRevealSubtree`, `section=expContentJobSectionSubtree`, `state=expContentJobStateSubtree`, `addlocation=expContentJobAddLocation`, `removelocation=expContentJobRemoveLocation`, `packageinstall=expContentJobPackageInstall`, `auditexport=expContentJobAuditExport`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
# ein leeres "JobTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
