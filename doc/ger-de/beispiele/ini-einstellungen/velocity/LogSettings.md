---
title: velocity.ini [LogSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LogSettings] in velocity.ini: Enabled, Dir, AccessName, ErrorName, Format, FileMode, DirMode"
keywords: Einstellung, velocity.ini, LogSettings, INI, setting, Enabled, Dir, AccessName, ErrorName, Format, FileMode, DirMode
type: generated
generated: exp:help:examples
---
# velocity.ini [LogSettings]

Der Block `[LogSettings]` von `settings/velocity.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> The server's own access and error log, one line per request and one per  
> fault. LogFile above is only what the process prints to the console.  
>
> On, because without it the server writes nothing at all: no access log,  
> no error log, no record of a single request.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Enabled=enabled
*/ ?>
```

## Dir

> The qbix engine's log directory; relative paths resolve against the  
> installation root. Everything Velocity writes for one server is kept  
> together below var/vc/<engine>/: log/ (the frankenphp engine's is  
> \[FrankenPHPSettings\] LogDir, the php engine's \[PHPServerSettings\] LogFile),  
> run/ (pid file, console output, generated configuration, admin socket), and  
> for qbix the configuration tree etc/ and lib/, for frankenphp bin/, tls/ and  
> caddy/. Until 2026-09 this was var/log/qbix; an installation that sets Dir  
> keeps its own.  

Standard: `var/vc/qbix/log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Dir=var/vc/qbix/log
*/ ?>
```

## AccessName

> The file names. Empty names them after the default siteaccess  
> (\[SiteSettings\] DefaultAccess in site.ini): site-access.log and  
> site-error.log for a siteaccess called "site", which keeps several  
> installations apart in one directory. Rotation puts the date in front of  
> the extension, so site-access.log becomes site-access.2026-09-24.log.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
AccessName=
*/ ?>
```

## ErrorName

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
ErrorName=
*/ ?>
```

## Format

> The access line.  
>
>   combined   what Apache and nginx write, for tools that read those. The  
>              default here.  
>   qbix       combined, plus the time the response took. The server's own  
>              default.  
>   common     combined without referrer and user agent.  
>
> Anything else is taken as a format string, with %{Host}i and the like.  

Standard: `combined`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
Format=combined
*/ ?>
```

## FileMode

> Permissions of the log files, octal. Empty leaves them to the umask. An  
> access log names the people who visited, so it is not for every account on  
> the machine to read.  

Standard: `0640`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
FileMode=0640
*/ ?>
```

## DirMode

> Permissions of the log directory, octal, applied to one that already exists  
> as well. Empty leaves it to the umask, which in practice is 0755 -- anyone  
> could list the files, and 0640 on each of them hides only their contents.  
> 0750 goes with 0640. For a group that shares the logs, 2770 with a FileMode  
> of 0660: the setgid bit passes the group on to new files, it does not give  
> the group write access. chmod needs ownership, so a directory another user  
> created keeps its mode, and the server says so on start.  

Standard: `0750`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LogSettings]
DirMode=0750
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
