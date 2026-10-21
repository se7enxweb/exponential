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

> Wie lange stop und restart auf ein sauberes Beenden warten. Die Prozesse der Engine qbix,  
> die danach noch da sind (jeder Prozess dieser Installation, gefunden  
> über das Skript, das er ausführt, und das Log, in das er schreibt, nicht nur über die PID-Dateien),  
> werden dann beendet (SIGKILL), und restart meldet Erfolg erst, wenn ein neuer  
> Server eine HTTP-Anfrage beantwortet. Die Engines frankenphp und php melden, dass  
> der Server nicht angehalten hat.  

Standard: `15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
StopTimeout=15
*/ ?>
```

## ShutdownTimeout

> Die eigene Grenze des qbix-Servers für ein sauberes Beenden (Q.webserver.shutdownTimeout):  
> Nach so vielen Sekunden wird er mit jedem von ihm gestarteten Prozess beendet, auch  
> wenn er dort blockiert ist, wo ihn kein Signal außer SIGKILL erreicht. Halten Sie den Wert unter  
> StopTimeout. 0 lässt das Beenden unbegrenzt.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
ShutdownTimeout=12
*/ ?>
```

## DetachedRestart

> restart (und der Neustart-Schritt von deploy) läuft als eine abgekoppelte Einheit: eigene  
> Sitzung (setsid) und, als root unter systemd, ein flüchtiger Scope  
> vc-restart-<date>-<token>, sodass ein Aufrufer, der zwischen Stopp und Start beendet wird,  
> den Server nicht mehr unten lässt. Der Aufrufer wartet und gibt das Ergebnis wie  
> bisher aus. Solange es läuft, liegt restart-in-progress.json neben der PID-Datei.  
> disabled: Neustart im aufrufenden Prozess, wie bisher.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ControlSettings]
DetachedRestart=enabled
*/ ?>
```

## ExtraOptions

> Zusätzliche Optionen, die unverändert an die Befehlszeile angehängt werden. Nur die Engine qbix;  
> die Engine frankenphp hat \[FrankenPHPSettings\] ExtraOptions\[\].  

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
