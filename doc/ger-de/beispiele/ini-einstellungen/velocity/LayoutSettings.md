---
title: velocity.ini [LayoutSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LayoutSettings] in velocity.ini: ConfDir, StateDir, SiteName"
keywords: Einstellung, velocity.ini, LayoutSettings, INI, setting, ConfDir, StateDir, SiteName
type: generated
generated: exp:help:examples
---
# velocity.ini [LayoutSettings]

Der Block `[LayoutSettings]` von `settings/velocity.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConfDir

> Wo die Konfiguration des Servers liegt, aufgebaut wie /etc/apache2 bei Debian  
> (siehe doc/eng-us/bc/6.0/velocity-ondisk-layout.md):  
>   vc.conf, ports.conf, envvars  
>   mods-available/  -> mods-enabled/   Engine-Module  
>   conf-available/  -> conf-enabled/   gemeinsame Schnipsel  
>   sites-available/ -> sites-enabled/  eine Datei pro Installation  
> Bei jedem Start aus diesen Einstellungen geschrieben und verwendet, wenn sie, so zusammengeführt,  
> wie der Server zusammenführt, genau die erzeugte Konfiguration ist; sonst wird wie bisher  
> die einzelne Datei var/tmp/velocity-server.json verwendet.  
>
> auto      /etc/vc, wenn es existiert oder angelegt werden kann, sonst ein vorhandenes  
>           /etc/qbix, sonst var/vc/qbix/etc in dieser Installation (Standard)  
> disabled  nur die einzelne Datei  
> <path>    dieses Verzeichnis  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
ConfDir=auto
*/ ?>
```

## StateDir

> Erweiterte Informationen über jede Website (Wurzel, Pfade, wann sie migriert wurde):  
> /var/lib/vc, wenn die Konfiguration in /etc/vc liegt, sonst var/vc/qbix/lib.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
StateDir=auto
*/ ?>
```

## SiteName

> Der Name dieser Installation in sites-available; Standard: der Host aus  
> site.ini \[SiteSettings\] SiteURL.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
SiteName=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
