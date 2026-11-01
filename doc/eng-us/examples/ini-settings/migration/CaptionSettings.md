---
title: migration.ini [CaptionSettings]
audience: developer, admin
category: examples
summary: "The settings of [CaptionSettings] in migration.ini: Before, After, ReuseUnchanged, ReuseIgnoreTableList, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory"
keywords: migration.ini, CaptionSettings, INI, setting, Before, After, ReuseUnchanged, ReuseIgnoreTableList, RestoreOnFailure, RestoreParts, CodeTreeList, BackupDirectory
type: generated
generated: exp:help:examples
---
# migration.ini [CaptionSettings]

The block `[CaptionSettings]` of `settings/migration.ini`, with 8 setting(s).

Never edit `settings/migration.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/migration.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in an extension (read when the extension is active).

## Before

> Captions are exp:upgrade's (./console exp:upgrade:captions lists them all): git\_manager's full caption, or the  
> database dump of exp:upgrade without git\_manager, with settings/override and the commits of the code.  
>
> enabled: a caption (kind migration-pre) is taken before a run that applies migrations. When none can be taken  
> (an engine without a dump, a full disk) the run goes on with a warning: there is then no automatic restore.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
Before=enabled
*/ ?>
```

## After

> enabled: a caption (kind migration-post, labelled with the migrations that ran) is taken after a run that  
> succeeded: the next run reuses it while nothing has changed since.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
After=enabled
*/ ?>
```

## ReuseUnchanged

> enabled: the newest migration caption is reused instead of a new one while the database, var/ storage,  
> settings/override and the commit of the installation are as they were when it was taken. The check is exact on  
> PostgreSQL (the change counters of every table), MySQL/MariaDB (CHECKSUM TABLE of every table) and SQLite (the  
> rows of every table); MongoDB and Oracle always take a new caption.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
ReuseUnchanged=enabled
*/ ?>
```

## ReuseIgnoreTableList

> The tables the reuse check leaves out: the record of the runs and captions, sessions and the audit index (each  
> caption writes an audit event). A name ending in \* stands for every table that starts with it.  

Default: a list of 4: `exp_migration`, `exp_migration_run`, `ezsession`, `expaudit_*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# an empty "ReuseIgnoreTableList[]" first replaces the shipped list; without it the items are added
ReuseIgnoreTableList[]
ReuseIgnoreTableList[]=exp_migration
ReuseIgnoreTableList[]=exp_migration_run
ReuseIgnoreTableList[]=ezsession
ReuseIgnoreTableList[]=expaudit_*
*/ ?>
```

## RestoreOnFailure

> enabled: a run that fails is put back to its pre caption at once (after a caption of the failed state, kind  
> migration-failure). Oracle has no built-in restore: the run stops and prints the steps to restore by hand.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
RestoreOnFailure=enabled
*/ ?>
```

## RestoreParts

> The parts of a caption a failed run is put back to (exp:upgrade:rollback --parts): database, storage, settings.  

Default: a list of 3: `database`, `storage`, `settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# an empty "RestoreParts[]" first replaces the shipped list; without it the items are added
RestoreParts[]
RestoreParts[]=database
RestoreParts[]=storage
RestoreParts[]=settings
*/ ?>
```

## CodeTreeList

> The code trees of the installation a run checks for changes that are not committed: a run with --apply is refused  
> while one has some, unless it is given --include-uncommitted (the changes then go into the caption as a patch and  
> a copy of the new files).  

Default: a list of 3: `settings`, `extension`, `design`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
# an empty "CodeTreeList[]" first replaces the shipped list; without it the items are added
CodeTreeList[]
CodeTreeList[]=settings
CodeTreeList[]=extension
CodeTreeList[]=design
*/ ?>
```

## BackupDirectory

> The folder of the captions made without git\_manager. Empty: var/backup/upgrade, as exp:upgrade.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[CaptionSettings]
BackupDirectory=
*/ ?>
```

## Related

- [All blocks of migration.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/migration.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
