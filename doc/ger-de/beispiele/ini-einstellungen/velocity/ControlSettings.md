---
title: velocity.ini [ControlSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ControlSettings] in velocity.ini: StopTimeout, ShutdownTimeout, DetachedRestart, ExtraOptions"
keywords: Einstellung, velocity.ini, ControlSettings, INI, setting, StopTimeout, ShutdownTimeout, DetachedRestart, ExtraOptions
type: generated
generated: exp:help:examples
---
# velocity.ini [ControlSettings]

Der Block `[ControlSettings]` von `settings/velocity.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StopTimeout

> How long stop and restart wait for a graceful stop. The qbix engine's  
> processes still there after it -- every process of this installation, found  
> by the script it runs and the log it writes to, not only the pid files --  
> are then killed (SIGKILL), and restart reports success only once a new  
> server answers an HTTP request. The frankenphp and php engines report that  
> the server did not stop.  

Standard: `15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
StopTimeout=15
*/ ?>
```

## ShutdownTimeout

> The qbix server's own bound on a graceful stop (Q.webserver.shutdownTimeout):  
> after this many seconds it is killed with every process it started, even  
> when it is blocked where no signal but SIGKILL reaches it. Keep it below  
> StopTimeout. 0 leaves the stop unbounded.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
ShutdownTimeout=12
*/ ?>
```

## DetachedRestart

> restart (and the restart step of deploy) runs as one detached unit: its own  
> session (setsid) and, as root under systemd, a transient scope  
> vc-restart-<date>-<token>, so a caller killed between the stop and the start  
> no longer leaves the server down. The caller waits and prints the outcome as  
> before. While it runs, restart-in-progress.json lies next to the pid file.  
> disabled: restart in the calling process, as before.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
DetachedRestart=enabled
*/ ?>
```

## ExtraOptions

> Extra options appended to the command line verbatim. The qbix engine only;  
> the frankenphp engine has \[FrankenPHPSettings\] ExtraOptions\[\].  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
# ein leeres "ExtraOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtraOptions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
