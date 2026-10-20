---
title: site.ini [FileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FileSettings] in site.ini: RenameBeforeDelete, RenameExpiredCaches, TemporaryDir, TemporaryPermissions, StorageDir, StorageDirPermissions, StorageFilePermissions, LogFilePermissions ..."
keywords: Einstellung, site.ini, FileSettings, INI, setting, RenameBeforeDelete, RenameExpiredCaches, TemporaryDir, TemporaryPermissions, StorageDir, StorageDirPermissions, StorageFilePermissions, LogFilePermissions, LogFileMaxSize, LogFileRotateCount, LogFileCompress, LogFileMaxAge, DirDepth, VarDir, CacheDir, LogDir, UseGlobalLogDir, LogVarDir, CacheVarDir, ExpiryDir, INICacheDir, AllowedDeletionDirs
type: generated
generated: exp:help:examples
---
# site.ini [FileSettings]

Der Block `[FileSettings]` von `settings/site.ini`, mit 22 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RenameBeforeDelete

> Clearing a cache that is deleted from disk -- compiled templates, override,  
> INI and others -- first renames its directory into .cleanup-trash in the cache  
> directory, named after the cache with the time appended, and deletes it only  
> after the last cache of the clear. The rename is  
> instant, so nothing generated while the delete runs is caught by it.  
> disabled deletes file by file, as before. Only for directories on the local  
> file system; a cache kept in a cluster database is not affected.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
RenameBeforeDelete=enabled
*/ ?>
```

## RenameExpiredCaches

> The view cache, cache-block, user info and translation cache are cleared by a  
> timestamp in expiry.php: their files stop being served, but stay on disk until  
> the cache\_cleanup cronjob removes them. enabled moves their directories into  
> .cleanup-trash as well when such a cache is cleared in the administration  
> interface or with bin/php/ezcache.php, and deletes them after the last cache  
> of the clear, so a clear leaves nothing behind. The timestamp is still set  
> first. Needs RenameBeforeDelete: deleting them file by file in the request is  
> what the timestamp is there to avoid, so with it disabled nothing is removed.  
> Publishing content only moves the timestamps, as before.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
RenameExpiredCaches=enabled
*/ ?>
```

## TemporaryDir

> The directory Exponential should use when creating temporary files.  
> e.g when creating an image variation  

Standard: `/tmp/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
TemporaryDir=/tmp/
*/ ?>
```

## TemporaryPermissions

> The permissions below are limited by EZP\_DIR\_MODE\_MAX / EZP\_FILE\_MODE\_MAX in  
> config.php where set (doc/eng-us/bc/6.0/file-modes.md): a mode here is never wider  
> than those.  
> Permission for temporary files, setting it to 0777 means that anyone  
> can read and write the files and can be considered a security risk.  
> It's preferred to use 0770 and make sure the web server has correct  
> user/group access.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
TemporaryPermissions=0777
*/ ?>
```

## StorageDir

> Where to place new files for storage, it's relative to var directory  

Standard: `storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageDir=storage
*/ ?>
```

## StorageDirPermissions

> Permission for storage directories, setting it to 0777 means that anyone  
> can read and write the directories and can be considered a security risk.  
> It's preferred to use 0770 and make sure the web server has correct  
> user/group access.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0777
*/ ?>
```

## StorageFilePermissions

> Permission for storage files, setting it to 0666 means that anyone  
> can read and write the files and can be considered a security risk.  
> It's preferred to use 0660 and make sure the web server has correct  
> user/group access.  

Standard: `0666`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageFilePermissions=0666
*/ ?>
```

## LogFilePermissions

> Permission for log files, setting it to 0666 means that anyone  
> can read and write the files and can be considered a security risk.  
> It's preferred to use 0660 and make sure the web server has correct  
> user/group access.  

Standard: `0666`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFilePermissions=0666
*/ ?>
```

## LogFileMaxSize

> The size in bytes at which a log file is rotated (the debug logs error.log,  
> warning.log, deprecated.log ... and the logs written with eZLog, such as  
> storage.log and the cronjob logs): it is renamed .1, older ones move on.  
> The constants EZPUBLISH\_LOG\_MAX\_FILE\_SIZE and CUSTOM\_LOG\_MAX\_FILE\_SIZE in  
> config.php win over it. 2097152 is 2 MB (was 200 KB).  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileMaxSize=2097152
*/ ?>
```

## LogFileRotateCount

> How many rotated files of each log are kept (.1 to .3). 0 turns the  
> rotation off. The constants EZPUBLISH\_LOG\_ROTATE\_FILES and  
> CUSTOM\_LOG\_ROTATE\_FILES in config.php win over it.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileRotateCount=3
*/ ?>
```

## LogFileCompress

> enabled: the rotated files from .2 on are compressed to .2.gz, .3.gz ...  
> (.1 never is: a request that opened the log before it was rotated may still  
> be writing to it). The log viewer reads compressed rotations too.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileCompress=disabled
*/ ?>
```

## LogFileMaxAge

> The age in days after which "./console exp:logs --prune" (and the log  
> viewer's prune action) removes a rotated file, whatever its number. 0: no  
> age limit, only LogFileRotateCount counts. A rotation never removes by age.  
> doc/eng-us/features/6.0/log-rotation.md  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileMaxAge=0
*/ ?>
```

## DirDepth

> Directory depth for storing files, this means that it will create  
> directories out of the first n characters to make sure not too  
> many files are placed in one directory. For instance a file name  
> newfile.png will be placed in n/e/w/newfile.png.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
DirDepth=3
*/ ?>
```

## VarDir

> The root directory where all log files, cache files  
> and other stored files are created.  
> Only directories relative to the Exponential root are supported. Absolute directories are NOT SUPPORTED  

Standard: `var`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
VarDir=var
*/ ?>
```

## CacheDir

> The name of the cache dir, as a subdir of VarDir  
> Using an absolute dir will be accepted, but won't work with some features and is NOT SUPPORTED:  
> the caches are written there, but the public caches in it (packed scripts and  
> style sheets, text to image) are not served by the web server or Velocity.  
> To keep the cache on another file system, mount that at VarDir/CacheDir, or  
> use CacheVarDir below with a directory inside the installation.  

Standard: `cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
CacheDir=cache
*/ ?>
```

## LogDir

> The name of the log dir, it's a subdir of VarDir. An absolute path puts the  
> logs of the site on another file system (multi-site hosting); the logs are  
> never served, so unlike CacheDir this is supported.  

Standard: `log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogDir=log
*/ ?>
```

## UseGlobalLogDir

> Where the debug logs (error.log, warning.log, ...) and the default logs of  
> eZLog go. enabled: var/log for every site, as before. disabled: the log  
> directory of the site (VarDir/LogDir), from the moment its siteaccess is  
> known; messages written before that still go to var/log.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
UseGlobalLogDir=enabled
*/ ?>
```

## LogVarDir

> A tree of their own for the logs or the caches of every site of a multi-site  
> installation, set once for all of them: the first directory of VarDir is  
> replaced. With LogVarDir=var\_log a site with VarDir=var/example logs to  
> var\_log/example/log, with CacheVarDir=var\_cache its cache is in  
> var\_cache/example/cache (one memory file system for all sites; set ExpiryDir  
> then). LogVarDir may be an absolute path (logs are never served).  
> CacheVarDir is a directory inside the installation: Velocity serves the  
> public caches below it (it reads CacheVarDir of every siteaccess when it  
> starts); for Apache and nginx enable the var\_cache rule in .htaccess\_root or  
> the server configuration. An absolute CacheVarDir is used, but its public  
> caches are not served, as with an absolute CacheDir. Empty: inside VarDir, as  
> before. Audit records stay in VarDir (audit.ini \[AuditSettings\] LogDir).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogVarDir=
*/ ?>
```

## CacheVarDir

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
CacheVarDir=
*/ ?>
```

## ExpiryDir

> Where expiry.php is kept: its timestamps decide whether the caches and the  
> image aliases in the storage are still valid. Empty: in the cache directory,  
> as before. A directory inside VarDir (e.g. expiry) or an absolute path keeps  
> it when the cache directory is on a file system that is emptied at a restart  
> (a memory file system mounted at var/<site>/cache).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
ExpiryDir=
*/ ?>
```

## INICacheDir

> Where the INI files of the site are cached. global: var/cache/ini, shared by  
> every site, as before. site: the cache directory of the site  
> (VarDir/CacheDir/ini), so clearing the INI cache of one site of a multi-site  
> installation leaves the others alone. The INI files read before the  
> siteaccess is known stay in var/cache/ini.  

Standard: `global`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
INICacheDir=global
*/ ?>
```

## AllowedDeletionDirs

> Directories eZDir is allowed to delete from (see eZDir::recursiveDelete()).  
> By default, it is only possible to delete from inside Exponential root directory.  
> Directories listed will be added as authorized directory prefixes.  
> Values must be absolute paths; one that does not exist allows nothing.  
> Inside the cache, log, expiry and INI cache directories of the site, and the  
> cache directory itself, are always allowed, also when CacheDir, LogDir,  
> CacheVarDir, LogVarDir or ExpiryDir put them outside the root (never one  
> that contains the installation).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
# ein leeres "AllowedDeletionDirs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedDeletionDirs[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
