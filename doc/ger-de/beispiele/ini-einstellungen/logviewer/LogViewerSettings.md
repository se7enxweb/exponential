---
title: logviewer.ini [LogViewerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LogViewerSettings] in logviewer.ini: LogViewer, Directories, FilePatterns, ExcludePatterns, KernelLogs, DefaultKey, DefaultFile, LogList ..."
keywords: Einstellung, logviewer.ini, LogViewerSettings, INI, setting, LogViewer, Directories, FilePatterns, ExcludePatterns, KernelLogs, DefaultKey, DefaultFile, LogList, LogPath, LogLabel, LogGroup, FileLevels
type: generated
generated: exp:help:examples
---
# logviewer.ini [LogViewerSettings]

Der Block `[LogViewerSettings]` von `settings/logviewer.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LogViewer

> enabled | disabled. With disabled, setup/logs and its feed answer 404.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
LogViewer=enabled
*/ ?>
```

## Directories

> The directories whose log files are shown, relative to the installation root (or absolute, still inside it).  
> A \* matches one directory name (var/\*/log is the log directory of every site). Only the files directly in a  
> directory are listed, never its subdirectories (the audit trail in var/log/audit has its own console), and a  
> directory or file that resolves (realpath) outside the installation root is never shown, so a symbolic link  
> cannot lead anywhere else. {log\_dir} is the log directory eZDebug writes to (var/log, or var/<site>/log with  
> site.ini \[FileSettings\] UseGlobalLogDir=disabled).  

Standard: eine Liste mit 3: `var/log`, `{log_dir}`, `var/*/log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "Directories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Directories[]
Directories[]=var/log
Directories[]={log_dir}
Directories[]=var/*/log
*/ ?>
```

## FilePatterns

> The file names shown (shell patterns): the log files, their rotations (.1 .. .N) and compressed rotations  
> (.gz, read only: viewed and searched, never followed).  

Standard: eine Liste mit 6: `*.log`, `*.log.[0-9]`, `*.log.[0-9][0-9]`, `*.log.[0-9].gz`, `*.log.[0-9][0-9].gz`, `*.log.gz`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "FilePatterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FilePatterns[]
FilePatterns[]=*.log
FilePatterns[]=*.log.[0-9]
FilePatterns[]=*.log.[0-9][0-9]
FilePatterns[]=*.log.[0-9].gz
FilePatterns[]=*.log.[0-9][0-9].gz
FilePatterns[]=*.log.gz
*/ ?>
```

## ExcludePatterns

> File names never shown (shell patterns), whatever the lists above say.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "ExcludePatterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludePatterns[]
*/ ?>
```

## KernelLogs

> The groups of the navigator. A file whose name matches KernelLogs\[\] is in "Kernel", a file in another site's  
> log directory (var/<site>/log) in "Site", any other in "Extensions"; a rotation (.1 .. .N, .gz) is in "Rotated  
> archives". Shell patterns of the current file's name.  
> The request logs (doc/eng-us/features/6.0/request-logs.md): the not found requests (404, 410) and the requests the  
> request classifier took for an attack (any status), one line per request.  

Standard: eine Liste mit 15: `error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log`, `cronjob*.log`, `storage.log`, `setup.log`, `mail.log`, `trashpurge.log` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "KernelLogs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
KernelLogs[]
KernelLogs[]=error.log
KernelLogs[]=warning.log
KernelLogs[]=notice.log
KernelLogs[]=debug.log
KernelLogs[]=strict.log
KernelLogs[]=deprecated.log
KernelLogs[]=auth.log
KernelLogs[]=cronjob*.log
KernelLogs[]=storage.log
KernelLogs[]=setup.log
KernelLogs[]=mail.log
KernelLogs[]=trashpurge.log
KernelLogs[]=404.log
KernelLogs[]=security.log
KernelLogs[]=csp.log
*/ ?>
```

## DefaultKey

> The log opened when the page is opened without one: a key (see below), else the file DefaultFile (relative to the  
> root). When neither can be opened: the first kernel log.  

Standard: `error`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
DefaultKey=error
*/ ?>
```

## DefaultFile

Standard: `var/log/error.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
DefaultFile=var/log/error.log
*/ ?>
```

## LogList

> Every log has a key, used in its permalink /setup/logs/view/<key> (doc/eng-us/features/6.0/log-viewer.md, "Permalinks"):  
>   var/log/<name>.log         <name>              e.g. /setup/logs/view/error, /setup/logs/view/debug  
>   var/<site>/log/<name>.log  <site>-<name>       e.g. /setup/logs/view/site-storage  
> and the logs configured here by key, such as the web server's. A key names only these: never a path.  
> LogList\[\] lists the configured keys, LogPath\[<key>\] the file (absolute, or relative to the root; it may be outside  
> the installation, as the web server's logs are; its rotations <name>.N and <name>.N.gz beside it are listed too),  
> LogLabel\[<key>\] the name the navigator shows, LogGroup\[<key>\] its group (kernel, site, extensions, webserver; the  
> default webserver). A key without a LogPath is not shown: the web server's logs are opt-in, never guessed. The web  
> server's user must be able to read the file (Plesk: /var/www/vhosts/<domain>/logs/access\_ssl\_log and error\_log).  
> The actions rotate, empty and remove are never offered for the webserver group: the web server owns those files.  
> LogLabel\[<key>\] also names a log of the log directories by its key (var/log/404.log is the key 404); the label is  
> translated (context kernel/logviewer) when the translation has it.  

Standard: eine Liste mit 2: `webserver-access`, `webserver-error`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "LogList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogList[]
LogList[]=webserver-access
LogList[]=webserver-error
*/ ?>
```

## LogPath

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "LogPath[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogPath[]
*/ ?>
```

## LogLabel

Standard: ein Hash mit 6: `webserver-access=Web server access log`, `webserver-error=Web server error log`, `404=Not found requests (404)`, `security=Security (request classifier)`, `csp=Content Security Policy reports`, `velocity-watchdog=Velocity watchdog`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "LogLabel[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogLabel[]
LogLabel[webserver-access]=Web server access log
LogLabel[webserver-error]=Web server error log
LogLabel[404]=Not found requests (404)
LogLabel[security]=Security (request classifier)
LogLabel[csp]=Content Security Policy reports
LogLabel[velocity-watchdog]=Velocity watchdog
*/ ?>
```

## LogGroup

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "LogGroup[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogGroup[]
*/ ?>
```

## FileLevels

> The level of a file whose lines do not name one: <file name pattern>;<level> (error, warning, notice, debug).  
> The first pattern that matches wins; a line that names its level ("Error:", "\[warning\]") always wins.  
> The request logs name no level: a not found request is a notice (it is an answer, not a fault), a request the  
> classifier took for an attack a warning (worth a look, listed by the error digest by default).  

Standard: eine Liste mit 10: `error.log*;error`, `*error*.log*;error`, `warning.log*;warning`, `strict.log*;warning`, `deprecated.log*;notice`, `notice.log*;notice`, `debug.log*;debug`, `404.log*;notice`, `security.log*;warning`, `csp.log*;warning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
# ein leeres "FileLevels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FileLevels[]
FileLevels[]=error.log*;error
FileLevels[]=*error*.log*;error
FileLevels[]=warning.log*;warning
FileLevels[]=strict.log*;warning
FileLevels[]=deprecated.log*;notice
FileLevels[]=notice.log*;notice
FileLevels[]=debug.log*;debug
FileLevels[]=404.log*;notice
FileLevels[]=security.log*;warning
FileLevels[]=csp.log*;warning
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
