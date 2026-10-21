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

> exp:velocity watchdog: ein Sicherheitsnetz, das die Standard-Engine erneut startet,  
> wenn sie unten ist und niemand das wollte (ein Neustart, dessen Aufrufer  
> zwischen Stopp und Start beendet wurde, ein Absturz, ein Beenden wegen Speichermangels). Aus, bis  
> \`exp:velocity watchdog install\` (root, systemd) <UnitName>.service  
> und .timer und conf-available/<name>.conf im Konfigurationsbaum schreibt,  
> diese Datei aktiviert (conf-enabled) und den Timer startet. Werte in der aktivierten Datei gewinnen  
> gegen diese. \`exp:velocity conf disable watchdog\` pausiert ihn; \`watchdog remove\`  
> nimmt ihn heraus; \`watchdog status\` und var/log/velocity-watchdog.log sagen, was er  
> getan hat. Er startet die Engine nur, wenn kein Prozess von ihr läuft, niemand sie angehalten hat  
> (exp:velocity stop/kill hinterlassen stopped.json neben der PID-Datei) und kein  
> Neustart im Gange ist (restart-in-progress.json); einen laufenden Server fasst er nie an,  
> ob er antwortet oder nicht.  
>
> Sekunden zwischen den Prüfungen (15 bis 3600).  

Standard: `45`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
Interval=45
*/ ?>
```

## RestartTimeout

> Sekunden, die ein Neustart dauern darf, bevor seine Markierung den Watchdog nicht mehr  
> zurückhält (eine Markierung, deren Prozess nicht mehr da ist, tut das nie). Auch, wie lange eine gehaltene  
> LockFile einen Server, der unten ist, vom Start abhalten darf.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
RestartTimeout=300
*/ ?>
```

## Backoff

> Nach drei gescheiterten Starts in Folge Sekunden Wartezeit vor dem nächsten.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
Backoff=600
*/ ?>
```

## LockFile

> Eine Sperrdatei (flock), die, solange sie gehalten wird, „es wird gerade ausgerollt, warten“ bedeutet:  
> eine Deployment-Warteschlange der Installation. Leer: keine.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
LockFile=
*/ ?>
```

## UnitName

> Der Name der systemd-Unit; geben Sie jeder Installation auf einem Host ihren eigenen.  

Standard: `vc-watchdog`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[WatchdogSettings]
UnitName=vc-watchdog
*/ ?>
```

## UnitDir

> Wohin die Unit-Dateien geschrieben werden.  

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
