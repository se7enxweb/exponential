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

> enabled | disabled. Mit disabled antworten setup/logs und sein Feed mit 404.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
LogViewer=enabled
*/ ?>
```

## Directories

> Die Verzeichnisse, deren Logdateien angezeigt werden, relativ zum Wurzelverzeichnis der Installation (oder absolut, aber darin).  
> Ein \* passt auf einen Verzeichnisnamen (var/\*/log ist das Log-Verzeichnis jeder Website). Nur die Dateien direkt in einem  
> Verzeichnis werden aufgeführt, nie seine Unterverzeichnisse (der Audit-Trail in var/log/audit hat seine eigene Konsole), und ein  
> Verzeichnis oder eine Datei, die (per realpath) außerhalb des Wurzelverzeichnisses der Installation liegt, wird nie angezeigt, sodass ein symbolischer Link  
> nirgendwo anders hinführen kann. {log\_dir} ist das Log-Verzeichnis, in das eZDebug schreibt (var/log, oder var/<site>/log mit  
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

> Die angezeigten Dateinamen (Shell-Muster): die Logdateien, ihre Rotationen (.1 .. .N) und komprimierten Rotationen  
> (.gz, nur lesen: angesehen und durchsucht, nie verfolgt).  

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

> Dateinamen, die nie angezeigt werden (Shell-Muster), egal was die Listen oben sagen.  

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

> Die Gruppen des Navigators. Eine Datei, deren Name auf KernelLogs\[\] passt, steht unter „Kernel“, eine Datei im Log-Verzeichnis  
> einer anderen Website (var/<site>/log) unter „Website“, jede andere unter „Erweiterungen“; eine Rotation (.1 .. .N, .gz) steht unter „Rotierte  
> Archive“. Shell-Muster auf den Namen der aktuellen Datei.  
> Die Anfrage-Logs (doc/eng-us/features/6.0/request-logs.md): die nicht gefundenen Anfragen (404, 410) und die Anfragen, die der  
> Anfrage-Klassifizierer für einen Angriff hielt (jeder Status), eine Zeile pro Anfrage.  

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

> Das Log, das geöffnet wird, wenn die Seite ohne eines geöffnet wird: ein Schlüssel (siehe unten), sonst die Datei DefaultFile (relativ zum  
> Wurzelverzeichnis). Wenn sich keines öffnen lässt: das erste Kernel-Log.  

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

> Jedes Log hat einen Schlüssel, verwendet in seinem Permalink /setup/logs/view/<key> (doc/eng-us/features/6.0/log-viewer.md, "Permalinks"):  
>   var/log/<name>.log         <name>              z. B. /setup/logs/view/error, /setup/logs/view/debug  
>   var/<site>/log/<name>.log  <site>-<name>       z. B. /setup/logs/view/site-storage  
> und die hier per Schlüssel konfigurierten Logs, etwa die des Webservers. Ein Schlüssel nennt nur diese: nie einen Pfad.  
> LogList\[\] listet die konfigurierten Schlüssel auf, LogPath\[<key>\] die Datei (absolut oder relativ zum Wurzelverzeichnis; sie darf außerhalb  
> der Installation liegen, wie die Logs des Webservers; ihre Rotationen <name>.N und <name>.N.gz daneben werden ebenfalls aufgeführt),  
> LogLabel\[<key>\] den Namen, den der Navigator zeigt, LogGroup\[<key>\] ihre Gruppe (kernel, site, extensions, webserver; der  
> Standard ist webserver). Ein Schlüssel ohne LogPath wird nicht angezeigt: Die Logs des Webservers müssen eingeschaltet werden, sie werden nie erraten. Der  
> Benutzer des Webservers muss die Datei lesen können (Plesk: /var/www/vhosts/<domain>/logs/access\_ssl\_log und error\_log).  
> Die Aktionen Rotieren, Leeren und Entfernen werden für die Gruppe webserver nie angeboten: Diese Dateien gehören dem Webserver.  
> LogLabel\[<key>\] benennt auch ein Log der Log-Verzeichnisse über seinen Schlüssel (var/log/404.log hat den Schlüssel 404); die Beschriftung wird  
> übersetzt (Kontext kernel/logviewer), wenn die Übersetzung sie hat.  

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

> Die Stufe einer Datei, deren Zeilen keine nennen: <file name pattern>;<level> (error, warning, notice, debug).  
> Das erste passende Muster gewinnt; eine Zeile, die ihre Stufe nennt ("Error:", "\[warning\]"), gewinnt immer.  
> Die Anfrage-Logs nennen keine Stufe: Eine nicht gefundene Anfrage ist eine notice (sie ist eine Antwort, kein Fehler), eine Anfrage, die der  
> Klassifizierer für einen Angriff hielt, eine warning (einen Blick wert, standardmäßig in der Fehlerübersicht aufgeführt).  

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
