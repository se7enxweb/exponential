---
title: velocity.ini [WatchdogSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [WatchdogSettings] in velocity.ini: Interval, RestartTimeout, Backoff, LockFile, UnitName, UnitDir"
keywords: Einstellung, velocity.ini, WatchdogSettings, INI, setting, Interval, RestartTimeout, Backoff, LockFile, UnitName, UnitDir
type: generated
generated: exp:help:examples
---
# velocity.ini [WatchdogSettings]

Der Block `[WatchdogSettings]` von `settings/velocity.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Interval

> exp:velocity watchdog: a safety net that starts the default engine again  
> when it is down and nobody meant it to be (a restart whose caller was killed  
> between the stop and the start, a crash, an out-of-memory kill). Off until  
> \`exp:velocity watchdog install\` (root, systemd) writes <UnitName>.service  
> and .timer and conf-available/<name>.conf in the configuration tree, enables  
> that file (conf-enabled) and starts the timer. Values in the enabled file win  
> over these. \`exp:velocity conf disable watchdog\` pauses it; \`watchdog remove\`  
> takes it out; \`watchdog status\` and var/log/velocity-watchdog.log say what it  
> did. It starts the engine only when no process of it runs, nobody stopped it  
> (exp:velocity stop/kill leave stopped.json beside the pid file) and no  
> restart is under way (restart-in-progress.json); it never touches a server  
> that runs, whether it answers or not.  
>
> Seconds between checks (15 to 3600).  

Standard: `45`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
Interval=45
*/ ?>
```

## RestartTimeout

> Seconds a restart may take before its marker no longer holds the watchdog  
> back (a marker whose process is gone never does). Also how long a held  
> LockFile may keep a server that is down from being started.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
RestartTimeout=300
*/ ?>
```

## Backoff

> After three starts in a row that failed, seconds to wait before the next.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
Backoff=600
*/ ?>
```

## LockFile

> A lock file (flock) that, while held, means "something is deploying, wait":  
> a deploy queue of the installation. Empty: none.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
LockFile=
*/ ?>
```

## UnitName

> The systemd unit's name; give each installation on a host its own.  

Standard: `vc-watchdog`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
UnitName=vc-watchdog
*/ ?>
```

## UnitDir

> Where the unit files are written.  

Standard: `/etc/systemd/system`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
UnitDir=/etc/systemd/system
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
